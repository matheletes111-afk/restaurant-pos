<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportTicketComment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SupportTicketApiController extends Controller
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
     * Get all tickets for the authenticated restaurant
     */
    public function index(Request $request)
    {
        try {
            $query = SupportTicket::with(['comments' => function($q) {
                    $q->orderBy('created_at', 'asc');
                }, 'creator'])
                ->where('restaurant_id', $request->user()->restaurant_id);
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }
            
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            
            $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
            
            // Add statistics
            $statistics = [
                'total' => SupportTicket::where('restaurant_id', $request->user()->restaurant_id)->count(),
                'new' => SupportTicket::where('restaurant_id', $request->user()->restaurant_id)
                    ->where('status', SupportTicket::STATUS_NEW)->count(),
                'in_progress' => SupportTicket::where('restaurant_id', $request->user()->restaurant_id)
                    ->where('status', SupportTicket::STATUS_IN_PROGRESS)->count(),
                'resolved' => SupportTicket::where('restaurant_id', $request->user()->restaurant_id)
                    ->where('status', SupportTicket::STATUS_RESOLVED)->count(),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Tickets retrieved successfully',
                'data' => $tickets,
                'statistics' => $statistics
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Create a new support ticket
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10',
                'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $ticket = new SupportTicket();
            $ticket->ticket_no = $this->generateTicketNumber();
            $ticket->restaurant_id = $request->user()->restaurant_id;
            $ticket->subject = $request->subject;
            $ticket->message = $request->message;
            $ticket->priority = $request->priority;
            $ticket->status = SupportTicket::STATUS_NEW;
            $ticket->created_by = $request->user()->id;
            $ticket->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'data' => [
                    'id' => $ticket->id,
                    'ticket_no' => $ticket->ticket_no,
                    'subject' => $ticket->subject,
                    'message' => $ticket->message,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at
                ]
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Get single ticket details with comments
     */
    public function show($id)
    {
        try {
            $ticket = SupportTicket::with(['comments' => function($q) {
                    $q->orderBy('created_at', 'asc');
                }, 'comments.user', 'creator'])
                ->where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Format comments
            $comments = [];
            foreach ($ticket->comments as $comment) {
                $comments[] = [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user_type' => $comment->user_type,
                    'user_name' => $comment->user->name ?? 'Unknown',
                    'attachment' => $comment->attachment ? url($comment->attachment) : null,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $comment->created_at->format('d M Y, h:i A')
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket details retrieved successfully',
                'data' => [
                    'id' => $ticket->id,
                    'ticket_no' => $ticket->ticket_no,
                    'subject' => $ticket->subject,
                    'message' => $ticket->message,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                    'created_by' => $ticket->creator->name ?? 'Unknown',
                    'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $ticket->created_at->format('d M Y, h:i A'),
                    'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                    'comments' => $comments
                ]
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string|min:2'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $ticket = SupportTicket::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // If ticket is resolved, can't add comment
            if ($ticket->status == SupportTicket::STATUS_RESOLVED) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ticket is already resolved. Cannot add comment.'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            $comment = new SupportTicketComment();
            $comment->ticket_id = $id;
            $comment->user_id = $request->user()->id;
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
            
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user_type' => $comment->user_type,
                    'attachment' => $comment->attachment ? url($comment->attachment) : null,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $comment->created_at->format('d M Y, h:i A')
                ]
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Mark ticket as resolved
     */
    public function markResolved($id)
    {
        try {
            $ticket = SupportTicket::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            $ticket->status = SupportTicket::STATUS_RESOLVED;
            $ticket->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket marked as resolved successfully',
                'data' => [
                    'id' => $ticket->id,
                    'ticket_no' => $ticket->ticket_no,
                    'status' => $ticket->status
                ]
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Delete ticket
     */
    public function destroy($id)
    {
        try {
            $ticket = SupportTicket::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Delete associated comments
            $ticket->comments()->delete();
            
            // Delete the ticket
            $ticket->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully'
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Get ticket statistics
     */
    public function getStatistics()
    {
        try {
            $statistics = [
                'total' => SupportTicket::where('restaurant_id', auth()->user()->restaurant_id)->count(),
                'new' => SupportTicket::where('restaurant_id', auth()->user()->restaurant_id)
                    ->where('status', SupportTicket::STATUS_NEW)->count(),
                'in_progress' => SupportTicket::where('restaurant_id', auth()->user()->restaurant_id)
                    ->where('status', SupportTicket::STATUS_IN_PROGRESS)->count(),
                'resolved' => SupportTicket::where('restaurant_id', auth()->user()->restaurant_id)
                    ->where('status', SupportTicket::STATUS_RESOLVED)->count(),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $statistics
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}