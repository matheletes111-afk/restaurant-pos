<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RestaurantMaster;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\RestaurantRegistrationMail;
use App\Models\Plan;
use App\Models\RestaurantToCustomPlan;
use App\Models\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;
class RestaurantController extends Controller
{
    /**
     * Display list of restaurants
     */
    public function index(Request $request)
    {
        $query = RestaurantMaster::where('status', '!=', 'D')
            ->with(['owner', 'active_subscription.plan', 'active_subscription.payments', 'latest_subscription.plan', 'latest_subscription.payments'])
            ->orderBy('id', 'desc');

        // Apply filters
        // 1. Keyword search (restaurant ID, unique code, restaurant name, owner name, owner email, owner phone, address, pincode, gstin, fssai)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanSearch = ltrim($search, '#');
            $query->where(function($q) use ($search, $cleanSearch) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id', $cleanSearch)
                  ->orWhere('id', 'like', "%{$cleanSearch}%")
                  ->orWhere('restaurant_id_unique', 'like', "%{$search}%")
                  ->orWhere('restaurant_id_unique', 'like', "%{$cleanSearch}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('pincode', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('fssai_number', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($uq) use ($search, $cleanSearch) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Status filter
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 3. Plan filter
        if ($request->filled('plan_id') && $request->plan_id != 'all') {
            $planId = $request->plan_id;
            if ($planId == 'none') {
                $query->whereDoesntHave('active_subscription');
            } else {
                $query->whereHas('active_subscription', function($q) use ($planId) {
                    $q->where('plan_id', $planId);
                });
            }
        }

        // 4. Date range filter by created_at
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // 5. Subscription date range filter by plan start_date
        if ($request->filled('sub_from_date') || $request->filled('sub_to_date')) {
            $query->whereHas('subscriptions', function($q) use ($request) {
                if ($request->filled('sub_from_date')) {
                    $q->whereDate('start_date', '>=', $request->sub_from_date);
                }
                if ($request->filled('sub_to_date')) {
                    $q->whereDate('start_date', '<=', $request->sub_to_date);
                }
            });
        }

        // Excel Export action
        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportExcel($query->get());
        }

        $data['restaurants'] = $query->get();
        
        // Fetch plans that have at least one subscription for filter dropdown
        $data['plans'] = Plan::whereHas('subscriptions')->orderBy('name', 'asc')->get();

        return view('restaurant.index', $data);
    }

    /**
     * Export restaurants as CSV
     */
    private function exportExcel($restaurants)
    {
        $filename = 'restaurants_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Restaurant ID',
            'Restaurant Name', 
            'Address', 
            'Pincode', 
            'GSTIN', 
            'FSSAI Number', 
            'Owner Name', 
            'Owner Email', 
            'Owner Phone', 
            'Active Plan', 
            'Plan Start Date',
            'Plan End Date',
            'Status', 
            'Created At'
        ];

        $callback = function() use($restaurants, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($restaurants as $rest) {
                $sub = $rest->active_subscription ?? $rest->latest_subscription;
                $planName = $sub && $sub->plan ? $sub->plan->name : 'No Plan';
                $planStartDate = $sub && $sub->start_date ? \Carbon\Carbon::parse($sub->start_date)->format('Y-m-d') : '';
                $planEndDate = $sub && $sub->end_date ? \Carbon\Carbon::parse($sub->end_date)->format('Y-m-d') : '';
                $statusText = $rest->status == 'A' ? 'Active' : 'Inactive';

                fputcsv($file, [
                    $rest->restaurant_id_unique ?? ('BILL-BITE-' . str_pad($rest->id, 3, '0', STR_PAD_LEFT)),
                    $rest->name,
                    $rest->address,
                    $rest->pincode,
                    $rest->gstin,
                    $rest->fssai_number,
                    $rest->owner->name ?? '',
                    $rest->owner->email ?? '',
                    $rest->owner->phone ?? '',
                    $planName,
                    $planStartDate,
                    $planEndDate,
                    $statusText,
                    $rest->created_at ? $rest->created_at->format('Y-m-d H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store new restaurant with owner
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            // Restaurant Information
            'restaurant_name' => 'required|string|max:255',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'gstin' => 'nullable|string|max:50',
            'fssai_number' => 'nullable|string|max:50',
            
            // Owner Information
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6',
        ], [
            'restaurant_name.required' => 'Restaurant name is required',
            'address.required' => 'Address is required',
            'pincode.required' => 'Pincode is required',
            'name.required' => 'Owner name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'phone.required' => 'Phone number is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        DB::beginTransaction();
        
        try {
            // Store plain password for email
            $plainPassword = $request->password;
            
            // 1. Create Owner User
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->role = 'RES'; // Restaurant role
            $user->role_type = 'ADMIN';
            $user->status = 'A';
            $user->created_by = auth()->id();
            $user->save();

            // 2. Create Restaurant
            $restaurant = new RestaurantMaster();
            $restaurant->name = $request->restaurant_name;
            $restaurant->address = $request->address;
            $restaurant->pincode = $request->pincode;
            $restaurant->gstin = $request->gstin;
            $restaurant->fssai_number = $request->fssai_number;
            $restaurant->owner_id = $user->id;
            $restaurant->status = 'A';
            $restaurant->created_by = auth()->id();
            $restaurant->save();

            // 3. Update user with restaurant_id
            $user->restaurant_id = $restaurant->id;
            $user->save();

            // Update DemoLead status / register_done if registered from a lead
            if ($request->filled('lead_id')) {
                $lead = \App\Models\DemoLead::find($request->lead_id);
                if ($lead) {
                    $lead->register_done = 'y';
                    $lead->save();
                }
            }

            // 4. Send Welcome Email with credentials
            try {
                $adminEmail = config('mail.admin_email') ?? env('ADMIN_EMAIL', 'sayansrvtechnology@gmail.com');
                Mail::to($user->email)
                    ->cc($adminEmail)
                    ->send(new RestaurantRegistrationMail($user, $plainPassword, $restaurant));
            } catch (\Exception $mailError) {
                // Log email error but don't rollback the transaction
                \Log::error('Failed to send registration email: ' . $mailError->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Restaurant added successfully. Login credentials have been sent to ' . $user->email);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error adding restaurant: ' . $e->getMessage());
        }
    }

    /**
     * Update restaurant and owner details
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:restaurant_master,id',
            'owner_id' => 'required|exists:users,id',
            'restaurant_name' => 'required|string|max:255',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'gstin' => 'nullable|string|max:50',
            'fssai_number' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->owner_id,
            'phone' => 'required|string|max:15',
        ]);

        DB::beginTransaction();
        
        try {
            // Update Restaurant
            $restaurant = RestaurantMaster::find($request->id);
            if (!$restaurant) {
                return back()->with('error', 'Restaurant not found.');
            }
            
            $restaurant->name = $request->restaurant_name;
            $restaurant->address = $request->address;
            $restaurant->pincode = $request->pincode;
            $restaurant->gstin = $request->gstin;
            $restaurant->fssai_number = $request->fssai_number;
            $restaurant->updated_by = auth()->id();
            $restaurant->save();

            // Update Owner User
            $user = User::find($restaurant->owner_id);
            if ($user) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Restaurant updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating restaurant: ' . $e->getMessage());
        }
    }

    /**
     * Change restaurant status (Active/Inactive)
     */
    public function status($owner_id)
    {
        $user = User::find($owner_id);

        if ($user) {
            DB::beginTransaction();
            try {
                $newStatus = $user->status === 'A' ? 'I' : 'A';
                $user->status = $newStatus;
                $user->save();

                // Update restaurant status also
                RestaurantMaster::where('owner_id', $owner_id)->update([
                    'status' => $newStatus,
                    'updated_by' => auth()->id()
                ]);

                DB::commit();
                return back()->with('success', 'Status updated successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error updating status.');
            }
        }

        return back()->with('error', 'Record not found.');
    }

    /**
     * Delete restaurant (Soft delete - status 'D')
     */
    public function delete($id)
    {
        $restaurant = RestaurantMaster::find($id);
        
        if (!$restaurant) {
            return back()->with('error', 'Restaurant not found.');
        }

        DB::beginTransaction();
        
        try {
            // Update restaurant status to 'D' for soft delete
            $restaurant->status = 'D';
            $restaurant->updated_by = auth()->id();
            $restaurant->save();

            // Update owner status to 'D'
            if ($restaurant->owner_id) {
                User::where('id', $restaurant->owner_id)->update([
                    'status' => 'D'
                ]);
            }

            DB::commit();
            return back()->with('success', 'Restaurant deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting restaurant: ' . $e->getMessage());
        }
    }

    /**
     * Restaurant Analytics/Dashboard
     */
    public function analytics($id)
    {
        $restaurant = RestaurantMaster::with('owner')->find($id);
        
        if (!$restaurant) {
            return redirect()->back()->with('error', 'Restaurant not found.');
        }
        
        // You can add analytics data here
        $data = [
            'restaurant' => $restaurant,
            'total_orders' => $restaurant->orders()->count(),
            'total_revenue' => $restaurant->orders()->sum('grand_total'),
            'total_customers' => $restaurant->orders()->distinct('customer_name')->count('customer_name'),
        ];
        
        return view('restaurant.analytics', $data);
    }

        /**
     * Show plan assignment page for specific restaurant
     */
    public function showPlans($id)
    {
        // Get restaurant details
        $restaurant = RestaurantMaster::with('owner')->findOrFail($id);
        
        // Get all plans where plan_status = 'A' and is_default_plan = 'N'
        $plans = Plan::where('is_delete', 'N')
            ->where('plan_status', 'A')
            ->where('is_default_plan', 'N')
            ->orderBy('name', 'asc')
            ->get();
        
        // Get already assigned plan IDs for this restaurant
        $assignedPlanIds = RestaurantToCustomPlan::where('restaurant_id', $id)
            ->pluck('plan_id')
            ->toArray();
        
        return view('restaurant.assign-plans', compact('restaurant', 'plans', 'assignedPlanIds'));
    }
    
    /**
     * Save assigned plans for restaurant
     */
    public function savePlans(Request $request)
    {
      
        
        try {
            DB::beginTransaction();
            
            // Delete all existing assignments
            RestaurantToCustomPlan::where('restaurant_id', $request->restaurant_id)->delete();
            
            // Add new assignments
            if ($request->has('plan_ids') && is_array($request->plan_ids)) {
                foreach ($request->plan_ids as $planId) {
                    RestaurantToCustomPlan::create([
                        'restaurant_id' => $request->restaurant_id,
                        'plan_id' => $planId,
                        'created_by' => auth()->id()
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('manage.restaurant.show.plans', $request->restaurant_id)
                ->with('success', 'Plans assigned successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Download subscription invoice as A4 PDF
     */
    public function downloadInvoice($subscription_id)
    {
        $subscription = Subscription::with(['plan', 'payments', 'restaurant_details.owner'])
            ->findOrFail($subscription_id);

        $restaurant = $subscription->restaurant_details;
        $payment = $subscription->payments->first();
        $plan = $subscription->plan;

        $data = [
            'subscription' => $subscription,
            'restaurant' => $restaurant,
            'payment' => $payment,
            'plan' => $plan,
            'invoice_no' => 'INV-' . str_pad($subscription->id, 6, '0', STR_PAD_LEFT),
            'invoice_date' => $payment ? $payment->created_at->format('Y-m-d') : $subscription->created_at->format('Y-m-d'),
        ];

        $pdf = Pdf::loadView('emails.subscription_invoice_pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice_' . $data['invoice_no'] . '.pdf');
    }
}