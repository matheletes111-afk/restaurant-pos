<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportTicketComment;
use App\Models\RestaurantMaster;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Generate ticket number
     */
    private function generateTicketNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastTicket = SupportTicket::orderBy('id', 'desc')->first();
        $lastNumber = $lastTicket ? intval(substr($lastTicket->ticket_no, -4)) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return "TKT-{$year}{$month}-{$newNumber}";
    }
    
    /**
     * Restaurant - Create Ticket Form
     */
    public function createTicket()
    {
        return view('support.restaurant.create');
    }
    
/**
 * Restaurant - Store Ticket
 */
public function storeTicket(Request $request)
{
    $request->validate([
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:10',
        'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT'
    ]);
    
    try {
        $ticket = new SupportTicket();
        $ticket->ticket_no = $this->generateTicketNumber();
        $ticket->restaurant_id = auth()->user()->restaurant_id;
        $ticket->subject = $request->subject;
        $ticket->message = $request->message;
        $ticket->priority = $request->priority;
        $ticket->status = SupportTicket::STATUS_NEW;
        $ticket->created_by = auth()->id();
        $ticket->save();
        
        // Get restaurant details and user
        $restaurant = \App\Models\RestaurantMaster::find(auth()->user()->restaurant_id);
        $user = auth()->user();
        
        // Send email to admin
        try {
            $adminEmail = 'developersayan2001@gmail.com';
            \Mail::to($adminEmail)->send(new \App\Mail\NewSupportTicketMail($ticket, $restaurant, $user));
            \Log::info('Admin notification email sent for ticket: ' . $ticket->ticket_no);
        } catch (\Exception $e) {
            \Log::error('Failed to send admin notification email: ' . $e->getMessage());
            // Continue execution - don't throw exception
        }
        
        return redirect()->route('restaurant.support.tickets')->with('success', 'Ticket #' . $ticket->ticket_no . ' created successfully');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
    
    /**
     * Restaurant - List Tickets
     */
    public function restaurantTickets()
    {
        $tickets = SupportTicket::where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('support.restaurant.tickets', compact('tickets'));
    }
    
    /**
     * Restaurant - View Single Ticket with Comments
     */
    public function viewTicket($id)
    {
        $ticket = SupportTicket::with(['comments.user', 'restaurant'])
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        return view('support.restaurant.ticket_detail', compact('ticket'));
    }
    
    /**
     * Restaurant - Add Comment
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|min:2'
        ]);
        
        try {
            $ticket = SupportTicket::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();
            
            // If ticket is resolved, can't add comment
            if ($ticket->status == SupportTicket::STATUS_RESOLVED) {
                return redirect()->back()->with('error', 'This ticket is already resolved. Cannot add comment.');
            }
            
            $comment = new SupportTicketComment();
            $comment->ticket_id = $id;
            $comment->user_id = auth()->id();
            $comment->user_type = 'RESTAURANT';
            $comment->comment = $request->comment;
            
            // Handle attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/tickets'), $filename);
                $comment->attachment = 'uploads/tickets/' . $filename;
            }
            
            $comment->save();
            
            // If status is NEW, change to IN_PROGRESS when restaurant replies
            if ($ticket->status == SupportTicket::STATUS_NEW) {
                $ticket->status = SupportTicket::STATUS_IN_PROGRESS;
                $ticket->save();
            }
            
            return redirect()->back()->with('success', 'Comment added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Restaurant - Mark Ticket as Resolved
     */
    public function markResolved($id)
    {
        try {
            $ticket = SupportTicket::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();
            
            $ticket->status = SupportTicket::STATUS_RESOLVED;
            $ticket->save();
            
            return redirect()->back()->with('success', 'Ticket marked as resolved');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Admin - List All Tickets
     */
    public function adminTickets(Request $request)
    {
        $query = SupportTicket::with(['restaurant', 'creator']);
        
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $statusCounts = [
            'new' => SupportTicket::where('status', SupportTicket::STATUS_NEW)->count(),
            'in_progress' => SupportTicket::where('status', SupportTicket::STATUS_IN_PROGRESS)->count(),
            'resolved' => SupportTicket::where('status', SupportTicket::STATUS_RESOLVED)->count(),
        ];
        
        return view('support.admin.tickets', compact('tickets', 'statusCounts'));
    }
    
    /**
     * Admin - View Ticket
     */
    public function adminViewTicket($id)
    {
        $ticket = SupportTicket::with(['comments.user', 'restaurant', 'creator'])->findOrFail($id);
        
        $admins = User::where('role_type', 'ADMIN')->where('status', 'A')->get();
        
        return view('support.admin.ticket_detail', compact('ticket', 'admins'));
    }
    
    /**
     * Admin - Add Comment
     */
    public function adminAddComment(Request $request, $id)
    {

        $request->validate([
            'comment' => 'required|string|min:2'
        ]);
        
        try {
            $ticket = SupportTicket::findOrFail($id);
            // return $ticket;
            $comment = new SupportTicketComment();
            $comment->ticket_id = $id;
            $comment->user_id = auth()->id();
            $comment->user_type = 'ADMIN';
            $comment->comment = $request->comment;
            
            // Handle attachment
            // if ($request->hasFile('attachment')) {
            //     $file = $request->file('attachment');
            //     $filename = time() . '_' . $file->getClientOriginalName();
            //     $file->move(public_path('uploads/tickets'), $filename);
            //     $comment->attachment = 'uploads/tickets/' . $filename;
            // }
            
            $comment->save();
            
            // Update ticket status to IN_PROGRESS if it's NEW
            if ($ticket->status == SupportTicket::STATUS_NEW) {
                $ticket->status = SupportTicket::STATUS_IN_PROGRESS;
                $ticket->save();
            }
            
            return redirect()->back()->with('success', 'Reply added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Admin - Change Ticket Status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:NEW,IN_PROGRESS,RESOLVED'
        ]);
        
        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->status = $request->status;
            $ticket->save();
            
            return redirect()->back()->with('success', 'Status updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Admin - Assign Ticket to Admin
     */
    public function assignTicket(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);
        
        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->assigned_to = $request->assigned_to;
            $ticket->save();
            
            return redirect()->back()->with('success', 'Ticket assigned successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}