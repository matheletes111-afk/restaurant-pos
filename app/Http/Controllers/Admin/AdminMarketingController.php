<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingNotification;
use App\Models\MarketingNotificationRecipient;
use App\Models\RestaurantMaster;
use App\Models\User;
use App\Jobs\SendMarketingEmailJob;
use App\Mail\MarketingNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class AdminMarketingController extends Controller
{
    /**
     * Display a listing of marketing notification campaigns (1 row per broadcast).
     */
    public function index(Request $request)
    {
        $query = MarketingNotification::with('creator')->latest('id');

        // Filter by search keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $notifications = $query->paginate(15)->withQueryString();

        // High-level statistics
        $stats = [
            'total_campaigns' => MarketingNotification::count(),
            'total_sent' => MarketingNotification::sum('sent_count'),
            'total_scheduled' => MarketingNotification::where('status', 'scheduled')->count(),
            'total_failed' => MarketingNotification::sum('failed_count'),
        ];

        return view('admin.marketing.index', compact('notifications', 'stats'));
    }

    /**
     * Show the form for creating a new marketing notification broadcast.
     */
    public function create()
    {
        // Fetch all active restaurants with their owners
        $restaurants = RestaurantMaster::where('status', '!=', 'D')
            ->with('owner')
            ->orderBy('name', 'asc')
            ->get();

        // Count how many have valid emails
        $restaurantsWithEmailCount = $restaurants->filter(function ($r) {
            return !empty($r->owner?->email) && filter_var($r->owner?->email, FILTER_VALIDATE_EMAIL);
        })->count();

        return view('admin.marketing.create', compact('restaurants', 'restaurantsWithEmailCount'));
    }

    /**
     * Store a newly created marketing notification broadcast and dispatch background queue jobs.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:5',
            'notification_date' => 'required|date',
            'target_type' => 'required|in:all,selected',
            'restaurant_ids' => 'required_if:target_type,selected|array',
            'restaurant_ids.*' => 'exists:restaurant_master,id',
        ], [
            'title.required' => 'Please provide an email notification title/subject.',
            'description.required' => 'Please enter the email description body.',
            'notification_date.required' => 'Please choose a notification date and time.',
            'restaurant_ids.required_if' => 'Please select at least one restaurant when targeting specific restaurants.',
        ]);

        // Resolve recipients
        $restaurantQuery = RestaurantMaster::where('status', '!=', 'D')->with('owner');

        if ($request->target_type === 'selected') {
            $restaurantQuery->whereIn('id', $request->restaurant_ids);
        }

        $selectedRestaurants = $restaurantQuery->get();

        $recipientsData = [];
        $seenEmails = [];

        foreach ($selectedRestaurants as $restaurant) {
            $email = trim($restaurant->owner?->email ?? '');
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // If same owner email appears multiple times across restaurants, we can attach with restaurant info
                $recipientsData[] = [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'owner_id' => $restaurant->owner_id,
                    'owner_name' => $restaurant->owner?->name ?? 'Valued Partner',
                    'email' => $email,
                    'status' => 'pending',
                    'error_message' => null,
                    'sent_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (empty($recipientsData)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No valid restaurant owner email addresses found for the selected targets.');
        }

        // Check scheduling time
        $scheduledAt = Carbon::parse($request->notification_date);
        $isImmediate = $scheduledAt->lessThanOrEqualTo(now()->addMinutes(1));

        $status = $isImmediate ? 'processing' : 'scheduled';

        DB::beginTransaction();
        try {
            // 1. Create single notification campaign record
            $notification = MarketingNotification::create([
                'title' => $request->title,
                'description' => $request->description,
                'scheduled_at' => $scheduledAt,
                'status' => $status,
                'target_type' => $request->target_type,
                'total_recipients' => count($recipientsData),
                'sent_count' => 0,
                'failed_count' => 0,
                'created_by' => Auth::id(),
                'sent_at' => null,
            ]);

            // 2. Attach notification_id to each recipient and bulk insert
            foreach ($recipientsData as &$item) {
                $item['notification_id'] = $notification->id;
            }
            unset($item);

            // Insert in chunks of 100 for maximum MySQL efficiency
            foreach (array_chunk($recipientsData, 100) as $chunk) {
                MarketingNotificationRecipient::insert($chunk);
            }

            DB::commit();

            // 3. If immediate, dispatch background queue jobs for every recipient
            if ($isImmediate) {
                $recipientRecords = MarketingNotificationRecipient::where('notification_id', $notification->id)->get();
                foreach ($recipientRecords as $rec) {
                    SendMarketingEmailJob::dispatch($rec->id);
                }
            }

            $msg = $isImmediate
                ? "Notification campaign created! Background jobs started for " . count($recipientsData) . " restaurant(s)."
                : "Notification campaign scheduled successfully for {$scheduledAt->format('d M Y, h:i A')} (" . count($recipientsData) . " recipients).";

            return redirect()->route('admin.marketing.index')->with('success', $msg);

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create marketing notification: ' . $e->getMessage());
        }
    }

    /**
     * Show the detailed status of a single marketing broadcast (recipients list & delivery logs).
     */
    public function show(Request $request, $id)
    {
        $notification = MarketingNotification::with('creator')->findOrFail($id);

        $recipientsQuery = MarketingNotificationRecipient::where('notification_id', $id)->latest('id');

        // Filter by recipient status (sent, failed, pending)
        if ($request->filled('status') && $request->status !== 'all') {
            $recipientsQuery->where('status', $request->status);
        }

        // Filter by recipient search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $recipientsQuery->where(function ($q) use ($search) {
                $q->where('restaurant_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $recipients = $recipientsQuery->paginate(25)->withQueryString();

        // Breakdown stats
        $breakdown = [
            'total' => $notification->total_recipients,
            'sent' => $notification->sent_count,
            'failed' => $notification->failed_count,
            'pending' => MarketingNotificationRecipient::where('notification_id', $id)->where('status', 'pending')->count(),
        ];

        return view('admin.marketing.show', compact('notification', 'recipients', 'breakdown'));
    }

    /**
     * AJAX endpoint to fetch campaign recipient summary and delivery status for quick modal view.
     */
    public function recipientStatusAjax($id)
    {
        $notification = MarketingNotification::with('creator')->findOrFail($id);
        
        $recipients = MarketingNotificationRecipient::where('notification_id', $id)
            ->orderBy('id', 'asc')
            ->get(['id', 'restaurant_name', 'owner_name', 'email', 'status', 'error_message', 'sent_at']);

        $pendingCount = $recipients->where('status', 'pending')->count();
        $sentCount = $recipients->where('status', 'sent')->count();
        $failedCount = $recipients->where('status', 'failed')->count();

        return response()->json([
            'success' => true,
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'status' => $notification->status,
                'status_badge' => $notification->status_badge,
                'scheduled_at' => $notification->scheduled_at ? $notification->scheduled_at->format('d M Y, h:i A') : 'Immediate',
                'sent_at' => $notification->sent_at ? $notification->sent_at->format('d M Y, h:i A') : '-',
                'total_recipients' => $notification->total_recipients,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'pending_count' => $pendingCount,
                'progress_percentage' => $notification->progress_percentage,
            ],
            'recipients' => $recipients,
        ]);
    }

    /**
     * Send a single live test email preview to an admin email address.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:5',
        ]);

        try {
            $dummyRecipient = [
                'owner_name' => 'Admin (Test Preview)',
                'restaurant_name' => 'Demo Restaurant POS',
                'email' => $request->test_email,
                'restaurant_id' => 'BILL-BITE-TEST',
            ];

            $mailable = new MarketingNotificationMail([
                'title' => '[TEST] ' . $request->title,
                'description' => $request->description,
            ], $dummyRecipient);

            Mail::to($request->test_email)->send($mailable);

            return response()->json([
                'success' => true,
                'message' => "Test email successfully sent to {$request->test_email}!",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Retry sending failed emails for a specific campaign.
     */
    public function retryFailed($id)
    {
        $notification = MarketingNotification::findOrFail($id);

        $failedRecipients = MarketingNotificationRecipient::where('notification_id', $id)
            ->where('status', 'failed')
            ->get();

        if ($failedRecipients->isEmpty()) {
            return redirect()->back()->with('info', 'No failed recipients found to retry.');
        }

        $count = $failedRecipients->count();

        // Reset failed recipients to pending
        MarketingNotificationRecipient::where('notification_id', $id)
            ->where('status', 'failed')
            ->update([
                'status' => 'pending',
                'error_message' => null,
            ]);

        // Update parent notification status
        $notification->update([
            'status' => 'processing',
            'failed_count' => max(0, $notification->failed_count - $count),
        ]);

        // Dispatch jobs
        foreach ($failedRecipients as $rec) {
            SendMarketingEmailJob::dispatch($rec->id);
        }

        return redirect()->back()->with('success', "Dispatched retry jobs for {$count} failed recipient(s).");
    }

    /**
     * Delete a marketing notification campaign and its recipient logs.
     */
    public function destroy($id)
    {
        $notification = MarketingNotification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.marketing.index')
            ->with('success', 'Marketing notification campaign deleted successfully.');
    }
}
