<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    // Display expenses with filters
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        
        // Get filter parameters
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        // Base query
        $query = Expense::where('restaurant_id', $restaurantId)
            ->whereBetween('expense_date', [$fromDate, $toDate]);
            
        // Apply search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')
                         ->paginate(50);
        
        // Summary statistics
        $summary = [
            'total_expenses' => $expenses->sum('amount'),
            'average_expense' => $expenses->avg('amount'),
            'total_count' => $expenses->total(),
            'date_range' => $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y')
        ];
        
        // Payment methods for dropdown
        $paymentMethods = ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Online Payment'];
        
        return view('expense.index', compact('expenses', 'summary', 'fromDate', 'toDate', 'paymentMethods'));
    }

    // Store new expense
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100'
        ]);

        $expense = new Expense();
        $expense->title = $request->title;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->expense_date = $request->expense_date;
        $expense->payment_method = $request->payment_method;
        $expense->restaurant_id = auth()->user()->restaurant_id;
        $expense->created_by = auth()->user()->id;
        $expense->save();

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully!'
        ]);
    }

    // Update expense
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100'
        ]);

        $expense = Expense::findOrFail($id);
        
        // Check if user has permission to update
        if ($expense->restaurant_id != auth()->user()->restaurant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action!'
            ], 403);
        }

        $expense->title = $request->title;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->expense_date = $request->expense_date;
        $expense->payment_method = $request->payment_method;
        $expense->save();

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully!'
        ]);
    }

    // Delete expense
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        
        // Check if user has permission to delete
        if ($expense->restaurant_id != auth()->user()->restaurant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action!'
            ], 403);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully!'
        ]);
    }

    // Get expense details for edit
    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        
        if ($expense->restaurant_id != auth()->user()->restaurant_id) {
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

    // Export expenses to Excel
    public function export(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        $expenses = Expense::where('restaurant_id', $restaurantId)
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        $data = [];
        $data[] = ['Title', 'Amount', 'Description', 'Date', 'Payment Method', 'Created At'];
        
        foreach ($expenses as $expense) {
            $data[] = [
                $expense->title,
                $expense->amount,
                $expense->description,
                $expense->expense_date->format('d M Y'),
                $expense->payment_method,
                $expense->created_at->format('d M Y H:i')
            ];
        }
        
        $total = $expenses->sum('amount');
        $data[] = ['', '', '', '', 'TOTAL:', $total];
        
        return response()->json($data);
    }
}