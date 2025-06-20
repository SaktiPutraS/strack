<?php
// app/Http/Controllers/SavingController.php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\Models\BankBalance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SavingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Saving::with(['payment.project.client']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort', 'transaction_date');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $savings = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $totalSavings = Saving::getTotalSavings();
        $pendingSavings = Saving::getPendingSavings();
        $transferredSavings = Saving::getTransferredSavings();
        $currentBankBalance = Saving::getCurrentBankBalance();
        $isBalanced = Saving::isSavingsBalanced();
        $difference = Saving::getSavingsDifference();

        $monthlySavings = Saving::whereYear('transaction_date', Carbon::now()->year)
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->sum('amount');

        return view('savings.index', compact(
            'savings',
            'totalSavings',
            'pendingSavings',
            'transferredSavings',
            'currentBankBalance',
            'isBalanced',
            'difference',
            'monthlySavings'
        ));
    }

    public function show(Saving $saving): View
    {
        $saving->load(['payment.project.client']);
        return view('savings.show', compact('saving'));
    }

    public function edit(Saving $saving): View
    {
        return view('savings.edit', compact('saving'));
    }

    public function update(Request $request, Saving $saving): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $saving->update($validated);

        return redirect()->route('savings.show', $saving)
            ->with('success', 'Data tabungan berhasil diperbarui!');
    }

    public function destroy(Saving $saving): RedirectResponse
    {
        $saving->delete();

        return redirect()->route('savings.index')
            ->with('success', 'Data tabungan berhasil dihapus!');
    }

    /**
     * Get pending savings for transfer selection
     */
    public function getPendingSavings(): JsonResponse
    {
        try {
            $pendingSavings = Saving::with(['payment.project.client'])
                ->where('status', 'PENDING')
                ->orderBy('transaction_date', 'asc')
                ->get()
                ->map(function ($saving) {
                    return [
                        'id' => $saving->id,
                        'amount' => $saving->amount,
                        'formatted_amount' => $saving->formatted_amount,
                        'transaction_date' => $saving->transaction_date->format('d M Y'),
                        'project_title' => $saving->payment->project->title,
                        'client_name' => $saving->payment->project->client->name,
                        'payment_type' => $saving->payment->payment_type,
                        'payment_amount' => $saving->payment->formatted_amount,
                    ];
                });

            $totalPending = $pendingSavings->sum('amount');

            return response()->json([
                'success' => true,
                'pending_savings' => $pendingSavings,
                'total_pending' => $totalPending,
                'formatted_total_pending' => 'Rp ' . number_format($totalPending, 0, ',', '.'),
                'count' => $pendingSavings->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting pending savings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading pending savings: ' . $e->getMessage(),
                'pending_savings' => [],
                'total_pending' => 0,
                'formatted_total_pending' => 'Rp 0',
                'count' => 0
            ], 500);
        }
    }

    /**
     * Transfer selected savings to bank
     */
    public function transferToBank(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'saving_ids' => 'required|array|min:1',
                'saving_ids.*' => 'exists:savings,id',
                'transfer_date' => 'required|date|before_or_equal:today',
                'transfer_method' => 'required|string|max:255',
                'transfer_reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

            // Get selected savings
            $selectedSavings = Saving::whereIn('id', $validated['saving_ids'])
                ->where('status', 'PENDING')
                ->get();

            if ($selectedSavings->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tabungan pending yang dipilih'
                ], 400);
            }

            $totalAmount = $selectedSavings->sum('amount');

            // Update savings status to TRANSFERRED
            Saving::whereIn('id', $validated['saving_ids'])->update([
                'status' => 'TRANSFERRED',
                'transfer_date' => $validated['transfer_date'],
                'transfer_method' => $validated['transfer_method'],
                'transfer_reference' => $validated['transfer_reference'],
                'notes' => ($validated['notes'] ?? '') . " (Batch transfer)",
            ]);

            // Update or create bank balance record
            $currentBalance = BankBalance::getLatestBalance($validated['transfer_method']);
            $newBalance = $currentBalance + $totalAmount;

            BankBalance::recordBalance(
                $newBalance,
                $validated['transfer_method'],
                "Transfer batch {$selectedSavings->count()} tabungan - Total: Rp " . number_format($totalAmount, 0, ',', '.')
            );

            return response()->json([
                'success' => true,
                'message' => "Berhasil transfer {$selectedSavings->count()} tabungan sebesar Rp " . number_format($totalAmount, 0, ',', '.'),
                'transferred_count' => $selectedSavings->count(),
                'total_amount' => $totalAmount,
                'formatted_amount' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
                'new_bank_balance' => $newBalance
            ]);
        } catch (\Exception $e) {
            Log::error('Error transferring savings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error saat transfer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bank balance manually
     */
    public function updateBankBalance(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'balance' => 'required|numeric|min:0',
                'bank_name' => 'required|string|in:Bank Octo,BCA,Mandiri,Other',
                'balance_date' => 'required|date|before_or_equal:today',
                'notes' => 'nullable|string',
            ]);

            BankBalance::create([
                'balance' => $validated['balance'],
                'balance_date' => $validated['balance_date'],
                'bank_name' => $validated['bank_name'],
                'notes' => $validated['notes'] ?: 'Manual bank balance update',
                'is_verified' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Saldo bank berhasil diperbarui!',
                'new_balance' => $validated['balance'],
                'formatted_balance' => 'Rp ' . number_format($validated['balance'], 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating bank balance: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating bank balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check balance status
     */
    public function checkBalance(): JsonResponse
    {
        try {
            $totalSavings = Saving::getTotalSavings();
            $pendingSavings = Saving::getPendingSavings();
            $transferredSavings = Saving::getTransferredSavings();
            $currentBankBalance = Saving::getCurrentBankBalance();
            $isBalanced = Saving::isSavingsBalanced();
            $difference = Saving::getSavingsDifference();

            return response()->json([
                'success' => true,
                'total_savings' => $totalSavings,
                'pending_savings' => $pendingSavings,
                'transferred_savings' => $transferredSavings,
                'current_bank_balance' => $currentBankBalance,
                'is_balanced' => $isBalanced,
                'difference' => $difference,
                'formatted_total_savings' => 'Rp ' . number_format($totalSavings, 0, ',', '.'),
                'formatted_pending_savings' => 'Rp ' . number_format($pendingSavings, 0, ',', '.'),
                'formatted_transferred_savings' => 'Rp ' . number_format($transferredSavings, 0, ',', '.'),
                'formatted_bank_balance' => 'Rp ' . number_format($currentBankBalance, 0, ',', '.'),
                'formatted_difference' => 'Rp ' . number_format(abs($difference), 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking balance: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error checking balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transfer history
     */
    public function getTransferHistory(): JsonResponse
    {
        try {
            $transfers = Saving::with(['payment.project.client'])
                ->where('status', 'TRANSFERRED')
                ->orderBy('transfer_date', 'desc')
                ->get()
                ->groupBy('transfer_date')
                ->map(function ($group, $date) {
                    return [
                        'transfer_date' => Carbon::parse($date)->format('d M Y'),
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                        'formatted_amount' => 'Rp ' . number_format($group->sum('amount'), 0, ',', '.'),
                        'transfer_method' => $group->first()->transfer_method,
                        'reference' => $group->first()->transfer_reference,
                        'savings' => $group->map(function ($saving) {
                            return [
                                'amount' => $saving->formatted_amount,
                                'project' => $saving->payment->project->title,
                                'client' => $saving->payment->project->client->name,
                            ];
                        })
                    ];
                })->values();

            return response()->json([
                'success' => true,
                'transfers' => $transfers
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting transfer history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error getting transfer history: ' . $e->getMessage(),
                'transfers' => []
            ], 500);
        }
    }
}
