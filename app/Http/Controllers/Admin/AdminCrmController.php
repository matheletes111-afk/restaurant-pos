<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemoLead;
use App\Models\DemoLeadInteraction;
use App\Models\DemoLeadTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminCrmController extends Controller
{
    /**
     * Display the CRM Kanban board
     */
    public function index(Request $request)
    {
        $query = DemoLead::query();

        // Filter by Search Query (Name or Email or Restaurant Name)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email_address', 'like', "%{$search}%")
                  ->orWhere('restaurant_name', 'like', "%{$search}%");
            });
        }

        // Filter by Source (corresponds to role filter buttons in screenshot style)
        if ($request->filled('source') && $request->input('source') !== 'all') {
            $query->where('source', $request->input('source'));
        }

        // Get filtered leads
        $leads = $query->orderBy('created_at', 'desc')->get();

        // Group leads by status
        $leadsByStatus = [
            'Contacted' => $leads->where('status', 'Contacted'),
            'Qualified' => $leads->where('status', 'Qualified'),
            'Nurturing' => $leads->where('status', 'Nurturing'),
            'Converted' => $leads->where('status', 'Converted'),
            'Lost'      => $leads->where('status', 'Lost'),
        ];

        // Stats calculation
        $statistics = [
            'total'     => DemoLead::count(),
            'contacted' => DemoLead::where('status', 'Contacted')->count(),
            'qualified' => DemoLead::where('status', 'Qualified')->count(),
            'nurturing' => DemoLead::where('status', 'Nurturing')->count(),
            'converted' => DemoLead::where('status', 'Converted')->count(),
            'lost'      => DemoLead::where('status', 'Lost')->count(),
        ];

        // Sources list for filters (unique sources present in the system, plus standard ones)
        $predefinedSources = ['Social Media', 'Search Engine', 'Friend/Colleague'];
        $dbSources = DemoLead::whereNotNull('source')->distinct()->pluck('source')->toArray();
        $sources = array_unique(array_merge($predefinedSources, $dbSources));

        return view('admin.crm.index', compact('leadsByStatus', 'statistics', 'sources'));
    }

    /**
     * Update lead status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Contacted,Qualified,Nurturing,Converted,Lost',
        ]);

        try {
            $lead = DemoLead::findOrFail($id);
            $oldStatus = $lead->status;
            
            if ($oldStatus !== $request->status) {
                $lead->status = $request->status;
                $lead->save();

                // Automatically log status change interaction in timeline
                DemoLeadInteraction::create([
                    'demo_lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'notes' => 'Lead pipeline stage updated to ' . strtoupper($request->status) . '.'
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead status updated to ' . $request->status . ' successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Lead status updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update lead status.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update lead status.');
        }
    }

    /**
     * Update lead follow-up date and notes
     */
    public function updateFollowup(Request $request, $id)
    {
        $request->validate([
            'followup_date' => 'nullable|date',
            'followup_notes' => 'nullable|string',
        ]);

        try {
            $lead = DemoLead::findOrFail($id);
            $lead->followup_date = $request->followup_date;
            $lead->followup_notes = $request->followup_notes;
            $lead->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Follow-up details updated successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Follow-up details updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update follow-up details.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update follow-up details.');
        }
    }

    /**
     * Show Lead Details profile view
     */
    public function show($id)
    {
        $lead = DemoLead::with([
            'interactions' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'interactions.user',
            'tasks' => function($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        return view('admin.crm.show', compact('lead'));
    }

    /**
     * Add log interaction note to timeline
     */
    public function logNote(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|min:2',
        ]);

        try {
            DemoLeadInteraction::create([
                'demo_lead_id' => $id,
                'user_id' => Auth::id(),
                'notes' => $request->notes,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Interaction note logged successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Interaction note logged successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to log note.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to log note.');
        }
    }

    /**
     * Add a follow-up reminder task
     */
    public function addTask(Request $request, $id)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        try {
            DemoLeadTask::create([
                'demo_lead_id' => $id,
                'task_title' => $request->task_title,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'is_completed' => false,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Follow-up task added successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Follow-up task added successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add task.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to add task.');
        }
    }

    /**
     * Toggle a task completion status
     */
    public function toggleTask(Request $request, $taskId)
    {
        try {
            $task = DemoLeadTask::findOrFail($taskId);
            $task->is_completed = !$task->is_completed;
            $task->save();

            return response()->json([
                'success' => true,
                'is_completed' => $task->is_completed,
                'message' => 'Task marked as ' . ($task->is_completed ? 'completed' : 'pending') . '.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle task.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
