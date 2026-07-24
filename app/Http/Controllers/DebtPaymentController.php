<?php

namespace App\Http\Controllers;

use App\Models\DebtPayment;
use App\Models\DebtRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DebtPaymentController extends Controller
{
    public function store(Request $request, DebtRecord $debt): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        // Tolak pembayaran yang melebihi sisa.
        if ((float) $validated['amount'] > $debt->remaining_amount) {
            return back()
                ->withErrors(['amount' => 'Nominal melebihi sisa (' . $debt->formatted_remaining . ').'])
                ->withInput();
        }

        $debt->payments()->create($validated);

        $sisa = $debt->fresh()->formatted_remaining;

        return redirect()->route('debts.show', $debt)
            ->with('success', "Pembayaran dicatat. Sisa sekarang {$sisa}.");
    }

    public function destroy(DebtPayment $debtPayment): RedirectResponse
    {
        $debt = $debtPayment->debtRecord;
        $debtPayment->delete();

        return redirect()->route('debts.show', $debt)
            ->with('success', 'Pembayaran dihapus.');
    }
}
