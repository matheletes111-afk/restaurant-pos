<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Services\CashDrawerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    protected CashDrawerService $cashDrawerService;

    public function __construct(CashDrawerService $cashDrawerService)
    {
        $this->cashDrawerService = $cashDrawerService;
    }

    protected function getActiveRestaurantId(): int
    {
        $user = auth()->user();
        if ($user && !empty($user->restaurant_id)) {
            return (int) $user->restaurant_id;
        }
        if (session('active_restaurant_id')) {
            return (int) session('active_restaurant_id');
        }
        if (session('restaurant_id')) {
            return (int) session('restaurant_id');
        }
        // Fallback for Super Admin / unassigned user to first active restaurant
        $firstRest = \App\Models\RestaurantMaster::first();
        return $firstRest ? (int) $firstRest->id : 1;
    }

    // Display expenses with filters
    public function index(Request $request)
    {
        $restaurantId = $this->getActiveRestaurantId();
        
        // Get filter parameters
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        // Base query
        $query = Expense::with('user')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('expense_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
            
        // Apply search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate(50);
        
        // Summary statistics
        $summary = [
            'total_expenses' => (float) Expense::where('restaurant_id', $restaurantId)
                ->whereBetween('expense_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                ->sum('amount'),
            'average_expense' => (float) (Expense::where('restaurant_id', $restaurantId)
                ->whereBetween('expense_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                ->avg('amount') ?? 0.0),
            'total_count' => $expenses->total(),
            'date_range' => $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y')
        ];
        
        // Payment methods for dropdown matching deposit & invoice
        $paymentMethods = Expense::PAYMENT_MODES;
        
        return view('expense.index', compact('expenses', 'summary', 'fromDate', 'toDate', 'paymentMethods'));
    }

    // Store new expense
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'remarks' => 'nullable|string|max:500',
            'title' => 'nullable|string|max:255'
        ]);

        $restaurantId = $this->getActiveRestaurantId();
        $paymentMethod = strtoupper(trim($request->payment_method));
        $remarks = $request->remarks ?: $request->title ?: 'Expense';

        DB::beginTransaction();
        try {
            $expense = new Expense();
            $expense->title = $request->title ?: $remarks;
            $expense->amount = $request->amount;
            $expense->description = $remarks;
            $expense->expense_date = $request->expense_date;
            $expense->payment_method = $paymentMethod;
            $expense->restaurant_id = $restaurantId;
            $expense->created_by = auth()->id();
            $expense->save();

            // If payment mode is CASH, sync with Cash Drawer
            if ($paymentMethod === 'CASH') {
                $this->cashDrawerService->recordExpense($expense);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Expense added successfully!' . ($paymentMethod === 'CASH' ? ' Cash debited from Cash Drawer.' : '')
                ]);
            }

            return redirect()->route('expense.index')
                ->with('success', 'Expense added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add expense: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add expense: ' . $e->getMessage());
        }
    }

    // Update expense
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'remarks' => 'nullable|string|max:500',
            'title' => 'nullable|string|max:255'
        ]);

        $expense = Expense::findOrFail($id);
        $activeRestId = $this->getActiveRestaurantId();
        
        // Check if user has permission to update
        if ($expense->restaurant_id && $expense->restaurant_id != $activeRestId && auth()->user()->role !== 'SA') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action!'
            ], 403);
        }

        $paymentMethod = strtoupper(trim($request->payment_method));
        $remarks = $request->remarks ?: $request->title ?: 'Expense';

        DB::beginTransaction();
        try {
            $expense->title = $request->title ?: $remarks;
            $expense->amount = $request->amount;
            $expense->description = $remarks;
            $expense->expense_date = $request->expense_date;
            $expense->payment_method = $paymentMethod;
            $expense->save();

            // Sync with Cash Drawer
            $this->cashDrawerService->updateExpense($expense);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Expense updated successfully!'
                ]);
            }

            return redirect()->route('expense.index')
                ->with('success', 'Expense updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update expense: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update expense: ' . $e->getMessage());
        }
    }

    // Delete expense
    public function destroy(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $activeRestId = $this->getActiveRestaurantId();
        
        // Check if user has permission to delete
        if ($expense->restaurant_id && $expense->restaurant_id != $activeRestId && auth()->user()->role !== 'SA') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action!'
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized action!');
        }

        DB::beginTransaction();
        try {
            // Remove linked Cash Drawer transaction if CASH payment
            if (strtoupper($expense->payment_method) === 'CASH') {
                $this->cashDrawerService->removeExpense($expense->id);
            }

            $expense->delete();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Expense deleted successfully!'
                ]);
            }

            return redirect()->route('expense.index')
                ->with('success', 'Expense deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete expense: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete expense: ' . $e->getMessage());
        }
    }

    // Get expense details for edit
    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        $activeRestId = $this->getActiveRestaurantId();
        
        if ($expense->restaurant_id && $expense->restaurant_id != $activeRestId && auth()->user()->role !== 'SA') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access!'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'expense' => $expense
        ]);
    }

    // Export expenses to CSV / Excel
    public function export(Request $request)
    {
        $restaurantId = $this->getActiveRestaurantId();
        
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        $expenses = Expense::with('user')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('expense_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        $filename = "expenses_" . date('Y_m_d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['S.No', 'Date', 'Amount (Rs)', 'Payment Mode', 'Remarks / Description', 'Created By', 'Created At'];

        $callback = function () use ($expenses, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $index = 1;
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $index++,
                    $expense->expense_date ? Carbon::parse($expense->expense_date)->format('d M Y') : '-',
                    $expense->amount,
                    $expense->payment_method ?: '-',
                    $expense->description ?: $expense->title ?: '-',
                    $expense->user ? $expense->user->name : 'System',
                    $expense->created_at ? $expense->created_at->format('d M Y h:i A') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}