<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemoLead;
use App\Models\DemoLeadInteraction;
use App\Models\DemoLeadTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

        // Filter by Date Range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Excel Export action
        if ($request->has('export') && $request->export == 'excel') {
            $exportStatus = $request->input('export_status');
            if ($exportStatus) {
                $query->where('status', $exportStatus);
                $filename = 'leads_' . strtolower($exportStatus) . '_' . date('Y-m-d') . '.csv';
            } else {
                $filename = 'all_leads_' . date('Y-m-d') . '.csv';
            }
            return $this->exportLeadsExcel($query->get(), $filename);
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
     * Store a newly created lead
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'restaurant_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email_address' => 'required|email|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:Contacted,Qualified,Nurturing,Converted,Lost',
        ]);

        DemoLead::create([
            'full_name' => $request->full_name,
            'restaurant_name' => $request->restaurant_name,
            'phone_number' => $request->phone_number,
            'email_address' => $request->email_address,
            'source' => $request->source,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Lead created successfully.');
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

    /**
     * Download sample Excel file template for CRM Leads Bulk Upload
     */
    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads Import');

        // Column Headers
        $headers = [
            'A1' => 'Full Name *',
            'B1' => 'Email Address *',
            'C1' => 'Phone Number',
            'D1' => 'Restaurant Name',
            'E1' => 'Source',
            'F1' => 'Followup Notes'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'] // Indigo branding
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Data Rows (same structure as leads from frontend)
        $sampleData = [
            [
                'John Doe',
                'john.doe@example.com',
                '+91 9876543210',
                'Spice Garden Bistro',
                'Social Media',
                'Interested in POS & kitchen display system demo'
            ],
            [
                'Sarah Smith',
                'sarah.smith@example.com',
                '+91 9123456780',
                'The Coffee Beanery',
                'Search Engine',
                'Requested callback on weekend'
            ],
            [
                'Rajesh Kumar',
                'rajesh.kumar@example.com',
                '+91 9988776655',
                'Tandoori Nights',
                'Friend/Colleague',
                'Opening a new restaurant next month'
            ]
        ];

        $rowNum = 2;
        foreach ($sampleData as $row) {
            $sheet->setCellValue('A' . $rowNum, $row[0]);
            $sheet->setCellValue('B' . $rowNum, $row[1]);
            $sheet->setCellValue('C' . $rowNum, $row[2]);
            $sheet->setCellValue('D' . $rowNum, $row[3]);
            $sheet->setCellValue('E' . $rowNum, $row[4]);
            $sheet->setCellValue('F' . $rowNum, $row[5]);
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
        }

        // Auto-fit column widths
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'crm_leads_import_sample.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, must-revalidate',
        ]);
    }

    /**
     * Handle Bulk Upload of CRM Leads via Excel/CSV file
     * If ANY error occurs or if an email already exists in the database/file,
     * NO data is saved to the database, and detailed errors are returned.
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file'     => 'required|mimes:xlsx,xls,csv,txt|max:5120',
            'default_source' => 'nullable|string|max:255'
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) <= 1) {
                return redirect()->back()->with('error', 'The uploaded file is empty or only contains headers.');
            }

            // Detect column indices dynamically based on header row (row index 0)
            $headerRow = array_map(function($h) {
                return strtolower(trim((string)$h));
            }, $rows[0]);

            $nameIdx = 0;
            $emailIdx = 1;
            $phoneIdx = 2;
            $restaurantIdx = 3;
            $sourceIdx = 4;
            $notesIdx = 5;

            foreach ($headerRow as $idx => $headerText) {
                if (str_contains($headerText, 'name') && !str_contains($headerText, 'restaurant')) {
                    $nameIdx = $idx;
                } elseif (str_contains($headerText, 'email')) {
                    $emailIdx = $idx;
                } elseif (str_contains($headerText, 'phone') || str_contains($headerText, 'contact') || str_contains($headerText, 'mobile')) {
                    $phoneIdx = $idx;
                } elseif (str_contains($headerText, 'restaurant') || str_contains($headerText, 'company') || str_contains($headerText, 'business') || str_contains($headerText, 'outlet')) {
                    $restaurantIdx = $idx;
                } elseif (str_contains($headerText, 'source')) {
                    $sourceIdx = $idx;
                } elseif (str_contains($headerText, 'note') || str_contains($headerText, 'comment') || str_contains($headerText, 'remark') || str_contains($headerText, 'message')) {
                    $notesIdx = $idx;
                }
            }

            $errors = [];
            $leadsToInsert = [];
            $seenEmailsInFile = [];

            // Pass 1: Parse and validate all rows in memory
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Skip blank row
                $nonEmpty = array_filter($row, fn($v) => !is_null($v) && trim((string)$v) !== '');
                if (empty($nonEmpty)) {
                    continue;
                }

                $rowNum = $i + 1; // 1-indexed for Excel users

                $fullName       = isset($row[$nameIdx]) ? trim((string)$row[$nameIdx]) : '';
                $email          = isset($row[$emailIdx]) ? trim((string)$row[$emailIdx]) : '';
                $phone          = isset($row[$phoneIdx]) ? trim((string)$row[$phoneIdx]) : '';
                $restaurantName = isset($row[$restaurantIdx]) ? trim((string)$row[$restaurantIdx]) : '';
                $source         = isset($row[$sourceIdx]) ? trim((string)$row[$sourceIdx]) : '';
                $notes          = isset($row[$notesIdx]) ? trim((string)$row[$notesIdx]) : '';

                if (empty($source)) {
                    $source = $request->input('default_source') ?: 'Bulk Upload';
                }

                // 1. Validation: Full Name is required
                if (empty($fullName)) {
                    $errors[] = "Row {$rowNum}: Full name is missing.";
                }

                // 2. Validation: Email is required and valid
                if (empty($email)) {
                    $errors[] = "Row {$rowNum}: Email address is required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNum}: Invalid email format '{$email}'.";
                } else {
                    $normalizedEmail = strtolower($email);

                    // 3. Validation: Duplicate check within the uploaded file
                    if (isset($seenEmailsInFile[$normalizedEmail])) {
                        $firstSeenRow = $seenEmailsInFile[$normalizedEmail];
                        $errors[] = "Row {$rowNum}: Duplicate email '{$email}' (already appears in Row {$firstSeenRow}).";
                    } else {
                        $seenEmailsInFile[$normalizedEmail] = $rowNum;
                    }

                    // 4. Validation: Check if email already exists in database
                    if (DemoLead::where('email_address', $email)->exists()) {
                        $errors[] = "Row {$rowNum}: Email '{$email}' already exists in the system.";
                    }
                }

                $leadsToInsert[] = [
                    'full_name'       => $fullName,
                    'email_address'   => $email,
                    'phone_number'    => $phone ?: null,
                    'restaurant_name' => $restaurantName ?: null,
                    'source'          => $source,
                    'status'          => 'Contacted',
                    'followup_notes'  => $notes ?: null,
                ];
            }

            // If there are ANY errors, do not save anything and return all errors
            if (count($errors) > 0) {
                return redirect()->back()
                    ->with('error', 'Bulk upload cancelled: ' . count($errors) . ' validation error(s) found. No leads were saved into the database.')
                    ->with('import_errors', $errors);
            }

            // If no data rows found
            if (empty($leadsToInsert)) {
                return redirect()->back()->with('error', 'The uploaded file does not contain any valid lead data.');
            }

            // Pass 2: Save all leads atomically in a transaction
            DB::beginTransaction();

            $authId = Auth::id();
            foreach ($leadsToInsert as $leadData) {
                $notes = $leadData['followup_notes'];
                $lead = DemoLead::create($leadData);

                // Create initial interaction in timeline
                DemoLeadInteraction::create([
                    'demo_lead_id' => $lead->id,
                    'user_id'      => $authId,
                    'notes'        => 'Lead imported via Excel Bulk Upload into "Contacted" pipeline stage.' . ($notes ? " Note: {$notes}" : '')
                ]);
            }

            DB::commit();

            $count = count($leadsToInsert);
            return redirect()->route('admin.crm.index')
                ->with('success', "Successfully uploaded and added {$count} lead(s) into the 'Contacted' stage.");

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return redirect()->back()
                ->with('error', 'Bulk upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Export CRM leads as CSV
     */
    private function exportLeadsExcel($leads, $filename)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Lead ID',
            'Full Name',
            'Email Address',
            'Phone Number',
            'Restaurant Name',
            'Source',
            'Status',
            'Followup Date',
            'Followup Notes',
            'Created At'
        ];

        $callback = function() use($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->full_name,
                    $lead->email_address,
                    $lead->phone_number,
                    $lead->restaurant_name,
                    $lead->source,
                    $lead->status,
                    $lead->followup_date ? \Carbon\Carbon::parse($lead->followup_date)->format('Y-m-d H:i') : '',
                    $lead->followup_notes,
                    $lead->created_at ? $lead->created_at->format('Y-m-d H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
