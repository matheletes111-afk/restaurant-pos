<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashDrawerTransaction;
use App\Services\CashDrawerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashDrawerController extends Controller
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

    /**
     * Display Cash Drawer Management & Ledger.
     */
    public function index(Request $request)
    {
        $restaurantId = $this->getActiveRestaurantId();

        // Default date range (current month)
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $filterType = $request->get('type', 'ALL');
        $search = $request->get('search', '');

        // Fetch ledger data and calculations
        $ledgerData = $this->cashDrawerService->getLedgerData(
            $restaurantId,
            $startDate,
            $endDate,
            $filterType,
            $search
        );

        $types = [
            'ALL' => 'All Transactions',
            CashDrawerTransaction::TYPE_OPENING => 'Initial Opening Cash',
            CashDrawerTransaction::TYPE_CASH_IN => 'Cash In (Added)',
            CashDrawerTransaction::TYPE_CASH_OUT => 'Cash Out (Spent)',
            CashDrawerTransaction::TYPE_ORDER_PAYMENT => 'Order Payments (Cash)',
            CashDrawerTransaction::TYPE_SUPPLIER_PAYMENT => 'Supplier Deposits (Cash)',
            CashDrawerTransaction::TYPE_EXPENSE_PAYMENT => 'Expenses (Cash)',
        ];

        return view('cash_drawer.index', compact(
            'ledgerData',
            'startDate',
            'endDate',
            'filterType',
            'search',
            'types'
        ));
    }

    /**
     * Store Initial Opening Cash (Only First Time).
     */
    public function storeOpening(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $restaurantId = $this->getActiveRestaurantId();

        try {
            $this->cashDrawerService->setOpeningCash(
                $restaurantId,
                (float) $request->amount,
                $request->entry_date,
                $request->remarks,
                auth()->id()
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Initial opening cash recorded successfully!',
                ]);
            }

            return redirect()->route('cash.drawer.index')
                ->with('success', 'Initial opening cash recorded successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Store Cash In (Increment / Add Cash).
     */
    public function storeCashIn(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'remarks' => 'required|string|max:500',
        ]);

        $restaurantId = $this->getActiveRestaurantId();

        try {
            $this->cashDrawerService->addCash(
                $restaurantId,
                (float) $request->amount,
                $request->entry_date,
                $request->remarks,
                auth()->id()
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cash added to drawer successfully!',
                ]);
            }

            return redirect()->route('cash.drawer.index')
                ->with('success', 'Cash added to drawer successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Store Cash Out (Decrement / Spend Cash).
     */
    public function storeCashOut(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'remarks' => 'required|string|max:500',
        ]);

        $restaurantId = $this->getActiveRestaurantId();

        try {
            $this->cashDrawerService->spendCash(
                $restaurantId,
                (float) $request->amount,
                $request->entry_date,
                $request->remarks,
                auth()->id()
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cash spend recorded successfully!',
                ]);
            }

            return redirect()->route('cash.drawer.index')
                ->with('success', 'Cash spend recorded successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a manual cash drawer transaction.
     */
    public function destroy(Request $request, $id)
    {
        $restaurantId = $this->getActiveRestaurantId();

        $query = CashDrawerTransaction::where('id', $id);
        if (auth()->user()->role !== 'SA') {
            $query->where('restaurant_id', $restaurantId);
        }
        $transaction = $query->firstOrFail();

        // Restrict deletion to current date only
        $entryDate = Carbon::parse($transaction->entry_date)->format('Y-m-d');
        $today = Carbon::today()->format('Y-m-d');

        if ($entryDate !== $today) {
            $msg = 'Only current date (' . Carbon::today()->format('d M Y') . ') cash drawer transactions can be deleted. Past date transactions are locked.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Prevent direct deletion of auto-synced entries
        if (in_array($transaction->transaction_type, [
            CashDrawerTransaction::TYPE_OPENING,
            CashDrawerTransaction::TYPE_ORDER_PAYMENT,
            CashDrawerTransaction::TYPE_SUPPLIER_PAYMENT,
            CashDrawerTransaction::TYPE_EXPENSE_PAYMENT
        ])) {
            $msg = match($transaction->transaction_type) {
                CashDrawerTransaction::TYPE_OPENING => 'Initial opening cash cannot be deleted.',
                CashDrawerTransaction::TYPE_ORDER_PAYMENT => 'This entry is linked to an order invoice payment. Please manage it directly from the order invoice page.',
                CashDrawerTransaction::TYPE_SUPPLIER_PAYMENT => 'This entry is linked to a supplier deposit. Please manage it directly from the supplier ledger page.',
                CashDrawerTransaction::TYPE_EXPENSE_PAYMENT => 'This entry is linked to an expense record. Please manage it directly from the Expense Management page.',
                default => 'This transaction cannot be deleted directly.'
            };

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $transaction->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully!',
            ]);
        }

        return redirect()->route('cash.drawer.index')
            ->with('success', 'Transaction deleted successfully!');
    }

    /**
     * Export Cash Drawer Ledger to Excel / CSV.
     */
    public function export(Request $request)
    {
        $restaurantId = $this->getActiveRestaurantId();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $filterType = $request->get('type', 'ALL');
        $search = $request->get('search', '');

        $ledgerData = $this->cashDrawerService->getLedgerData(
            $restaurantId,
            $startDate,
            $endDate,
            $filterType,
            $search
        );

        $filename = "cash_drawer_report_" . date('Y_m_d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'S.No',
            'Date',
            'Time',
            'Transaction Type',
            'Remarks / Reference',
            'Debit (Cash Out) Rs',
            'Credit (Cash In) Rs',
            'Recorded By'
        ];

        $callback = function () use ($ledgerData, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            $index = 1;
            foreach ($ledgerData['transactions'] as $txn) {
                $debit = $txn->entry_type === CashDrawerTransaction::ENTRY_DEBIT ? $txn->amount : '0.00';
                $credit = $txn->entry_type === CashDrawerTransaction::ENTRY_CREDIT ? $txn->amount : '0.00';

                fputcsv($file, [
                    $index++,
                    $txn->entry_date ? Carbon::parse($txn->entry_date)->format('d M Y') : '-',
                    $txn->entry_time ?: '-',
                    $txn->getTypeBadge()['label'],
                    $txn->remarks ?: '-',
                    $debit,
                    $credit,
                    $txn->user ? $txn->user->name : 'System'
                ]);
            }

            // Total Sum row at the end
            fputcsv($file, []);
            fputcsv($file, [
                '',
                '',
                '',
                '',
                'TOTAL PERIOD SUM:',
                number_format($ledgerData['total_period_debit'], 2, '.', ''),
                number_format($ledgerData['total_period_credit'], 2, '.', ''),
                ''
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
