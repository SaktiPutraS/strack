<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\Payment;
use App\Models\BankBalance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BankTransferController extends Controller
{
    public function index(Request $request): View
    {
        $query = BankTransfer::with(['payment.project.client']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('transfer_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transfer_date', '<=', $request->date_to);
        }

        $transfers = $query->orderBy('transfer_date', 'desc')->paginate(15)->withQueryString();

        // Untransferred payments
        $untransferredPayments = Payment::with(['project.client'])
            ->where('is_transferred', false)
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalTransferred = BankTransfer::sum('transfer_amount');
        $totalUntransferred = $untransferredPayments->sum('amount');

        return view('bank-transfers.index', compact(
            'transfers',
            'untransferredPayments',
            'totalTransferred',
            'totalUntransferred'
        ));
    }

    public function create(Request $request): View
    {
        $payment = null;
        if ($request->filled('payment_id')) {
            $payment = Payment::with(['project.client'])->findOrFail($request->payment_id);
        }

        $untransferredPayments = Payment::with(['project.client'])
            ->where('is_transferred', false)
            ->orderBy('payment_date', 'desc')
            ->get();

        return view('bank-transfers.create', compact('payment', 'untransferredPayments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'transfer_date' => 'required|date|before_or_equal:today',
            'transfer_amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if payment is already transferred
        $payment = Payment::findOrFail($validated['payment_id']);
        if ($payment->is_transferred) {
            return back()->withErrors(['payment_id' => 'Pembayaran ini sudah ditransfer!'])->withInput();
        }

        BankTransfer::create($validated);

        // Update bank balance
        BankBalance::updateBalance();

        return redirect()->route('bank-transfers.index')
            ->with('success', 'Transfer berhasil dicatat!');
    }

    public function batchTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:payments,id',
            'transfer_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $payments = Payment::whereIn('id', $validated['payment_ids'])
            ->where('is_transferred', false)
            ->get();

        if ($payments->isEmpty()) {
            return back()->withErrors(['payment_ids' => 'Tidak ada pembayaran yang valid untuk ditransfer!']);
        }

        foreach ($payments as $payment) {
            BankTransfer::create([
                'payment_id' => $payment->id,
                'transfer_date' => $validated['transfer_date'],
                'transfer_amount' => $payment->amount,
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);
        }

        // Update bank balance
        BankBalance::updateBalance();

        return redirect()->route('bank-transfers.index')
            ->with('success', "Berhasil mentransfer {$payments->count()} pembayaran!");
    }

    public function destroy(BankTransfer $bankTransfer): RedirectResponse
    {
        $bankTransfer->delete();

        // Update bank balance
        BankBalance::updateBalance();

        return redirect()->route('bank-transfers.index')
            ->with('success', 'Transfer berhasil dihapus!');
    }
}
