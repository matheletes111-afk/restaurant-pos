<?php

namespace App\Services;

use App\Models\CashDrawerTransaction;
use App\Models\OrderToPayment;
use App\Models\OrderManage;
use App\Models\SupplierDeposit;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashDrawerService
{
    /**
     * Check if opening cash has been set for this restaurant.
     */
    public function hasOpeningCash(int $restaurantId): bool
    {
        return CashDrawerTransaction::where('restaurant_id', $restaurantId)
            ->where('transaction_type', CashDrawerTransaction::TYPE_OPENING)
            ->exists();
    }

    /**
     * Get the initial opening cash record for this restaurant.
     */
    public function getOpeningCash(int $restaurantId): ?CashDrawerTransaction
    {
        return CashDrawerTransaction::where('restaurant_id', $restaurantId)
            ->where('transaction_type', CashDrawerTransaction::TYPE_OPENING)
            ->first();
    }

    /**
     * Set initial opening cash for the restaurant (Only allowed once).
     */
    public function setOpeningCash(int $restaurantId, float $amount, string $date, ?string $remarks = null, ?int $userId = null): CashDrawerTransaction
    {
        if ($this->hasOpeningCash($restaurantId)) {
            throw new \Exception('Initial opening cash has already been configured for this restaurant.');
        }

        return CashDrawerTransaction::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $userId,
            'transaction_type' => CashDrawerTransaction::TYPE_OPENING,
            'entry_type' => CashDrawerTransaction::ENTRY_CREDIT,
            'amount' => $amount,
            'entry_date' => Carbon::parse($date)->format('Y-m-d'),
            'entry_time' => Carbon::now()->format('H:i:s'),
            'remarks' => $remarks ?: 'Initial Opening Cash',
        ]);
    }

    /**
     * Manual Cash In (Increment / Add Cash to Drawer).
     */
    public function addCash(int $restaurantId, float $amount, string $date, ?string $remarks = null, ?int $userId = null): CashDrawerTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        return CashDrawerTransaction::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $userId,
            'transaction_type' => CashDrawerTransaction::TYPE_CASH_IN,
            'entry_type' => CashDrawerTransaction::ENTRY_CREDIT,
            'amount' => $amount,
            'entry_date' => Carbon::parse($date)->format('Y-m-d'),
            'entry_time' => Carbon::now()->format('H:i:s'),
            'remarks' => $remarks ?: 'Cash Added to Drawer',
        ]);
    }

    /**
     * Manual Cash Out (Decrement / Spend Cash from Drawer).
     */
    public function spendCash(int $restaurantId, float $amount, string $date, ?string $remarks = null, ?int $userId = null): CashDrawerTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        return CashDrawerTransaction::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $userId,
            'transaction_type' => CashDrawerTransaction::TYPE_CASH_OUT,
            'entry_type' => CashDrawerTransaction::ENTRY_DEBIT,
            'amount' => $amount,
            'entry_date' => Carbon::parse($date)->format('Y-m-d'),
            'entry_time' => Carbon::now()->format('H:i:s'),
            'remarks' => $remarks ?: 'Cash Spent / Withdrawal',
        ]);
    }

    /**
     * Automatically record cash received from an order invoice payment.
     */
    public function recordOrderPayment(OrderToPayment $payment, ?OrderManage $order = null): ?CashDrawerTransaction
    {
        if (strtoupper($payment->payment_method ?? '') !== 'CASH') {
            return null;
        }

        // Avoid duplicate entry for the same payment
        $existing = CashDrawerTransaction::where('reference_id', $payment->id)
            ->where(function ($q) {
                $q->where('reference_type', OrderToPayment::class)
                  ->orWhere('reference_type', 'OrderToPayment');
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        if (!$order) {
            $order = OrderManage::find($payment->order_id);
        }

        $orderLabel = $order ? ($order->order_id ?? $order->id) : $payment->order_id;
        $customerName = $order && !empty($order->customer_name) ? $order->customer_name : 'Customer';
        
        $remarks = "Order #{$orderLabel} Cash Payment ({$customerName})";
        if (!empty($payment->remarks)) {
            $remarks .= " - " . $payment->remarks;
        }

        $entryDate = $payment->payment_date ? Carbon::parse($payment->payment_date) : Carbon::now();

        return CashDrawerTransaction::create([
            'restaurant_id' => $payment->restaurant_id,
            'user_id' => $payment->created_by ?: auth()->id(),
            'transaction_type' => CashDrawerTransaction::TYPE_ORDER_PAYMENT,
            'entry_type' => CashDrawerTransaction::ENTRY_CREDIT,
            'amount' => $payment->amount,
            'entry_date' => $entryDate->format('Y-m-d'),
            'entry_time' => $entryDate->format('H:i:s'),
            'reference_id' => $payment->id,
            'reference_type' => OrderToPayment::class,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Remove cash drawer entry when an order cash payment is deleted.
     */
    public function removeOrderPayment(int $paymentId): bool
    {
        return (bool) CashDrawerTransaction::where('reference_id', $paymentId)
            ->where(function ($q) {
                $q->where('reference_type', OrderToPayment::class)
                  ->orWhere('reference_type', 'OrderToPayment');
            })
            ->delete();
    }

    /**
     * Automatically record cash paid as a supplier deposit/ledger payment.
     */
    public function recordSupplierDeposit(SupplierDeposit $deposit, ?Supplier $supplier = null): ?CashDrawerTransaction
    {
        if (strtoupper($deposit->payment_mode ?? '') !== 'CASH') {
            return null;
        }

        // Avoid duplicate entry for the same deposit
        $existing = CashDrawerTransaction::where('reference_id', $deposit->id)
            ->where(function ($q) {
                $q->where('reference_type', SupplierDeposit::class)
                  ->orWhere('reference_type', 'SupplierDeposit');
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        if (!$supplier) {
            $supplier = Supplier::find($deposit->supplier_id);
        }

        $supplierName = $supplier ? $supplier->supplier_name : 'Supplier';
        $remarks = "Supplier Deposit to {$supplierName}";
        if (!empty($deposit->transaction_no)) {
            $remarks .= " (Txn: {$deposit->transaction_no})";
        }
        if (!empty($deposit->remarks)) {
            $remarks .= " - {$deposit->remarks}";
        }

        $entryDate = $deposit->deposit_date ? Carbon::parse($deposit->deposit_date) : Carbon::now();

        return CashDrawerTransaction::create([
            'restaurant_id' => $deposit->restaurant_id,
            'user_id' => $deposit->user_id ?: auth()->id(),
            'transaction_type' => CashDrawerTransaction::TYPE_SUPPLIER_PAYMENT,
            'entry_type' => CashDrawerTransaction::ENTRY_DEBIT,
            'amount' => $deposit->amount,
            'entry_date' => $entryDate->format('Y-m-d'),
            'entry_time' => Carbon::now()->format('H:i:s'),
            'reference_id' => $deposit->id,
            'reference_type' => SupplierDeposit::class,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Remove cash drawer entry when a supplier deposit is deleted.
     */
    public function removeSupplierDeposit(int $depositId): bool
    {
        return (bool) CashDrawerTransaction::where('reference_id', $depositId)
            ->where(function ($q) {
                $q->where('reference_type', SupplierDeposit::class)
                  ->orWhere('reference_type', 'SupplierDeposit');
            })
            ->delete();
    }

    /**
     * Automatically record cash paid for an expense.
     */
    public function recordExpense(\App\Models\Expense $expense): ?CashDrawerTransaction
    {
        if (strtoupper($expense->payment_method ?? '') !== 'CASH') {
            return null;
        }

        // Avoid duplicate entry for the same expense
        $existing = CashDrawerTransaction::where('reference_id', $expense->id)
            ->where(function ($q) {
                $q->where('reference_type', \App\Models\Expense::class)
                  ->orWhere('reference_type', 'Expense');
            })
            ->first();

        if ($existing) {
            $existing->amount = $expense->amount;
            $existing->entry_date = $expense->expense_date ? Carbon::parse($expense->expense_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $existing->remarks = "Expense: " . ($expense->description ?: $expense->title);
            $existing->save();
            return $existing;
        }

        $remarks = "Expense: " . ($expense->description ?: $expense->title);
        $entryDate = $expense->expense_date ? Carbon::parse($expense->expense_date) : Carbon::now();

        $restaurantId = $expense->restaurant_id;
        if (empty($restaurantId)) {
            $restaurantId = auth()->user()->restaurant_id ?? session('restaurant_id') ?? 1;
        }

        return CashDrawerTransaction::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $expense->created_by ?: auth()->id(),
            'transaction_type' => CashDrawerTransaction::TYPE_EXPENSE_PAYMENT,
            'entry_type' => CashDrawerTransaction::ENTRY_DEBIT,
            'amount' => $expense->amount,
            'entry_date' => $entryDate->format('Y-m-d'),
            'entry_time' => Carbon::now()->format('H:i:s'),
            'reference_id' => $expense->id,
            'reference_type' => \App\Models\Expense::class,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Update or sync cash drawer entry when an expense is updated.
     */
    public function updateExpense(\App\Models\Expense $expense): ?CashDrawerTransaction
    {
        if (strtoupper($expense->payment_method ?? '') === 'CASH') {
            return $this->recordExpense($expense);
        } else {
            $this->removeExpense($expense->id);
            return null;
        }
    }

    /**
     * Remove cash drawer entry when an expense is deleted.
     */
    public function removeExpense(int $expenseId): bool
    {
        return (bool) CashDrawerTransaction::where('reference_id', $expenseId)
            ->where(function ($q) {
                $q->where('reference_type', \App\Models\Expense::class)
                  ->orWhere('reference_type', 'Expense');
            })
            ->delete();
    }

    /**
     * Get live current cash balance in drawer (All Credits - All Debits).
     */
    public function getCurrentBalance(int $restaurantId): float
    {
        $totalCredits = CashDrawerTransaction::where('restaurant_id', $restaurantId)
            ->where('entry_type', CashDrawerTransaction::ENTRY_CREDIT)
            ->sum('amount');

        $totalDebits = CashDrawerTransaction::where('restaurant_id', $restaurantId)
            ->where('entry_type', CashDrawerTransaction::ENTRY_DEBIT)
            ->sum('amount');

        return (float) ($totalCredits - $totalDebits);
    }

    /**
     * Get complete Cash Drawer ledger with running balance calculations.
     */
    public function getLedgerData(
        int $restaurantId,
        string $startDate,
        string $endDate,
        ?string $filterType = null,
        ?string $search = null
    ): array {
        $hasOpening = $this->hasOpeningCash($restaurantId);
        $openingRecord = $this->getOpeningCash($restaurantId);
        $currentLiveBalance = $this->getCurrentBalance($restaurantId);

        // Calculate opening balance before the filtered start date
        $creditsBefore = CashDrawerTransaction::where('restaurant_id', $restaurantId)
            ->where('entry_type', CashDrawerTransaction::ENTRY_CREDIT)
            ->where('entry_date', '<', $startDate)
            ->sum('amount');

        $debitsBefore = CashDrawerTransaction::where('restaurant_id', $restaurantId)
            ->where('entry_type', CashDrawerTransaction::ENTRY_DEBIT)
            ->where('entry_date', '<', $startDate)
            ->sum('amount');

        $openingBalanceForPeriod = (float) ($creditsBefore - $debitsBefore);

        // Fetch transactions for the period ordered chronologically
        $query = CashDrawerTransaction::with(['user'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('entry_date', [$startDate, $endDate]);

        if ($filterType && $filterType !== 'ALL') {
            $query->where('transaction_type', $filterType);
        }

        if ($search && trim($search) !== '') {
            $searchTerm = trim($search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('remarks', 'like', "%{$searchTerm}%")
                  ->orWhere('amount', 'like', "%{$searchTerm}%");
            });
        }

        $transactions = $query->orderBy('entry_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate sequential running balance for each transaction
        $runningBalance = $openingBalanceForPeriod;
        $totalPeriodCredit = 0;
        $totalPeriodDebit = 0;

        foreach ($transactions as $txn) {
            if ($txn->entry_type === CashDrawerTransaction::ENTRY_CREDIT) {
                $runningBalance += (float) $txn->amount;
                $totalPeriodCredit += (float) $txn->amount;
            } else {
                $runningBalance -= (float) $txn->amount;
                $totalPeriodDebit += (float) $txn->amount;
            }
            $txn->running_balance = $runningBalance;
        }

        $closingBalanceForPeriod = $runningBalance;

        // Return latest transactions on top for table display
        $displayTransactions = $transactions->reverse()->values();

        return [
            'has_opening_cash' => $hasOpening,
            'opening_record' => $openingRecord,
            'live_balance' => $currentLiveBalance,
            'opening_balance_period' => $openingBalanceForPeriod,
            'closing_balance_period' => $closingBalanceForPeriod,
            'total_period_credit' => $totalPeriodCredit,
            'total_period_debit' => $totalPeriodDebit,
            'transactions' => $displayTransactions,
        ];
    }
}
