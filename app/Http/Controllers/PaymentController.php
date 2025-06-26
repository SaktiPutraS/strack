<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['project.client']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $sortBy = $request->get('sort', 'payment_date');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $payments = $query->paginate(15)->withQueryString();

        $totalPayments = Payment::sum('amount');
        $monthlyIncome = Payment::whereYear('payment_date', Carbon::now()->year)
            ->whereMonth('payment_date', Carbon::now()->month)
            ->sum('amount');
        $paymentCount = Payment::count();

        return view('payments.index', compact(
            'payments',
            'totalPayments',
            'monthlyIncome',
            'paymentCount'
        ));
    }

    public function create(): View
    {
        $projects = Project::with('client')
            ->whereIn('status', ['WAITING', 'PROGRESS'])
            ->get();

        return view('payments.create', compact('projects'));
    }

    public function createForProject(Project $project): View
    {
        $projects = collect([$project]);
        return view('payments.create', compact('projects', 'project'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|in:DP,INSTALLMENT,FULL,FINAL',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $project = Project::findOrFail($validated['project_id']);

        if ($validated['amount'] > $project->remaining_amount) {
            return back()->withErrors([
                'amount' => 'Jumlah pembayaran tidak boleh melebihi sisa tagihan.'
            ])->withInput();
        }

        $payment = Payment::create($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil ditambahkan!');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['project.client']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        $projects = Project::with('client')->get();
        return view('payments.edit', compact('payment', 'projects'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|in:DP,INSTALLMENT,FULL,FINAL',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $currentProjectPaid = $project->payments()->where('id', '!=', $payment->id)->sum('amount');
        $newTotal = $currentProjectPaid + $validated['amount'];

        if ($newTotal > $project->total_value) {
            return back()->withErrors([
                'amount' => 'Total pembayaran tidak boleh melebihi nilai proyek.'
            ])->withInput();
        }

        $payment->update($validated);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Pembayaran berhasil diperbarui!');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil dihapus!');
    }

    public function getRecentPayments(): JsonResponse
    {
        $payments = Payment::with(['project.client'])
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'payment_type' => $payment->payment_type,
                    'payment_date' => $payment->payment_date->format('Y-m-d'),
                    'payment_date_formatted' => $payment->payment_date->format('d M Y'),
                    'project_title' => $payment->project->title,
                    'client_name' => $payment->project->client->name,
                ];
            });

        return response()->json($payments);
    }

    public function getMonthlyPayments(): JsonResponse
    {
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $total = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'total' => $total,
                'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.')
            ];
        }

        return response()->json($monthlyData);
    }
}
