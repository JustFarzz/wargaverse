<?php

namespace App\Http\Controllers;

use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = FinanceTransaction::with('user')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $summary = $this->getSummary();
        $currentBalance = $this->getCurrentBalance();

        return view('kas.index', compact('transactions', 'summary', 'currentBalance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentBalance = $this->getCurrentBalance();

        return view('kas.create', compact('currentBalance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('kas-attachments', $filename, 'public');
                $attachments[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        // Create transaction
        $transaction = FinanceTransaction::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'category' => $request->category,
            'transaction_date' => $request->transaction_date,
            'payment_method' => $request->payment_method ?? 'cash',
            'is_recurring' => $request->boolean('is_recurring'),
            'notes' => $request->notes,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        return redirect()->route('kas.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FinanceTransaction $transaction)
    {
        $transaction->load('user');

        return view('kas.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinanceTransaction $transaction)
    {
        // Only allow editing if user is the creator
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengedit transaksi ini.');
        }

        $currentBalance = $this->getCurrentBalance();

        return view('kas.edit', compact('transaction', 'currentBalance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinanceTransaction $transaction)
    {
        // Only allow editing if user is the creator
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengedit transaksi ini.');
        }

        // Handle file uploads
        $attachments = $transaction->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('kas-attachments', $filename, 'public');
                $attachments[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        // Update transaction
        $transaction->update([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'category' => $request->category,
            'transaction_date' => $request->transaction_date,
            'payment_method' => $request->payment_method ?? 'cash',
            'is_recurring' => $request->boolean('is_recurring'),
            'notes' => $request->notes,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        return redirect()->route('kas.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinanceTransaction $transaction)
    {
        // Only allow deletion if user is the creator
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak dapat menghapus transaksi ini.');
        }

        // Delete attachments
        if ($transaction->attachments) {
            foreach ($transaction->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $transaction->delete();

        return redirect()->route('kas.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Get financial summary
     */
    public function getSummary()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyIncome = FinanceTransaction::income()
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $monthlyExpense = FinanceTransaction::expense()
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $yearlyIncome = FinanceTransaction::income()
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $yearlyExpense = FinanceTransaction::expense()
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        return [
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'monthly_balance' => $monthlyIncome - $monthlyExpense,
            'yearly_income' => $yearlyIncome,
            'yearly_expense' => $yearlyExpense,
            'yearly_balance' => $yearlyIncome - $yearlyExpense,
        ];
    }

    /**
     * Get current balance
     */
    public function getCurrentBalance()
    {
        $totalIncome = FinanceTransaction::income()->sum('amount');
        $totalExpense = FinanceTransaction::expense()->sum('amount');

        return $totalIncome - $totalExpense;
    }

    /**
     * Get monthly report
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $transactions = FinanceTransaction::whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->with('user')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $summary = [
            'income' => $transactions->where('type', 'income')->sum('amount'),
            'expense' => $transactions->where('type', 'expense')->sum('amount'),
        ];
        $summary['balance'] = $summary['income'] - $summary['expense'];

        return view('kas.monthly-report', compact('transactions', 'summary', 'month', 'year'));
    }

    /**
     * Get yearly report
     */
    public function yearlyReport(Request $request)
    {
        $year = $request->get('year', now()->year);

        $transactions = FinanceTransaction::whereYear('transaction_date', $year)
            ->with('user')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTransactions = $transactions->filter(function ($transaction) use ($month) {
                return $transaction->transaction_date->month == $month;
            });

            $monthlyData[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'income' => $monthlyTransactions->where('type', 'income')->sum('amount'),
                'expense' => $monthlyTransactions->where('type', 'expense')->sum('amount'),
            ];
        }

        $summary = [
            'income' => $transactions->where('type', 'income')->sum('amount'),
            'expense' => $transactions->where('type', 'expense')->sum('amount'),
        ];
        $summary['balance'] = $summary['income'] - $summary['expense'];

        return view('kas.yearly-report', compact('transactions', 'monthlyData', 'summary', 'year'));
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        // Implementation for Excel export
        // You can use Laravel Excel package for this
        return response()->json(['message' => 'Export Excel feature coming soon']);
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        // Implementation for PDF export
        // You can use DomPDF or similar package for this
        return response()->json(['message' => 'Export PDF feature coming soon']);
    }
}