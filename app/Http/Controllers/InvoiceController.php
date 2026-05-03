<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('designBrief')->orderBy('judul')->get();
        return view('invoices.index', compact('projects'));
    }

    public function data()
    {
        $invoices = Invoice::with(['project.designBrief'])->select('invoices.*');

        return DataTables::of($invoices)
            ->addColumn('project', function (Invoice $invoice) {
                return $invoice->project?->judul ?? '-';
            })
            ->editColumn('amount', function (Invoice $invoice) {
                $totalBudget = $invoice->project?->designBrief?->budget ?? $invoice->amount;

                return $totalBudget !== null ? number_format((float) $totalBudget, 2, ',', '.') : '-';
            })
            ->editColumn('payment_amount', function (Invoice $invoice) {
                return $invoice->payment_amount !== null ? number_format((float) $invoice->payment_amount, 2, ',', '.') : '-';
            })
            ->addColumn('remaining_amount', function (Invoice $invoice) {
                $totalBudget = (float) ($invoice->project?->designBrief?->budget ?? $invoice->amount ?? 0);
                $paymentAmount = (float) ($invoice->payment_amount ?? 0);

                return number_format($totalBudget - $paymentAmount, 2, ',', '.');
            })
            ->editColumn('status', function (Invoice $invoice) {
                $badge = match ($invoice->status) {
                    'lunas' => 'success',
                    'DP' => 'warning',
                    default => 'secondary',
                };
                return '<span class="badge badge-' . $badge . '">' . ucfirst($invoice->status) . '</span>';
            })
            ->editColumn('created_at', function (Invoice $invoice) {
                return $invoice->created_at?->format('Y-m-d');
            })
            ->addColumn('action', function (Invoice $invoice) {
                return '<button class="btn btn-sm btn-primary editBtn" data-id="' . $invoice->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deleteBtn" data-id="' . $invoice->id . '">Hapus</button>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show recap page for all payment history (income and expenses)
     */
    public function rekap()
    {
        return view('invoices.rekap');
    }

    /**
     * Return combined invoice/payment history for DataTables
     */
    public function rekapData(Request $request)
    {
        $rows = [];

        // Invoices -> treat payment_amount as incoming payment
        $invoices = Invoice::with('project')->orderByDesc('created_at')->get();
        foreach ($invoices as $inv) {
            $rows[] = [
                'date' => $inv->created_at?->format('Y-m-d') ?: null,
                'type' => 'Pemasukan',
                'reference' => $inv->invoice_number,
                'description' => $inv->project?->judul ?? '-',
                'amount' => (float) ($inv->payment_amount ?? 0),
            ];
        }

        // Payments (expenses) if table exists
        try {
            if (Schema::hasTable('payments')) {
                $payments = \App\Models\Payment::orderByDesc('created_at')->get();
                foreach ($payments as $p) {
                    $amt = $p->amount ?? ($p->nominal ?? ($p->value ?? 0));
                    $rows[] = [
                        'date' => $p->created_at?->format('Y-m-d') ?: null,
                        'type' => 'Pengeluaran',
                        'reference' => $p->id,
                        'description' => $p->description ?? $p->notes ?? 'Pengeluaran',
                        'amount' => -1 * (float) $amt,
                    ];
                }
            }
        } catch (\Exception $e) {
            // ignore if payments table missing
        }

        // Apply optional date filter and search
        $collection = collect($rows);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search.value');

        if ($startDate || $endDate) {
            $collection = $collection->filter(function ($r) use ($startDate, $endDate) {
                $d = $r['date'] ?? null;
                if (!$d) return false;
                if ($startDate && $d < $startDate) return false;
                if ($endDate && $d > $endDate) return false;
                return true;
            });
        }

        if ($search) {
            $search = strtolower($search);
            $collection = $collection->filter(function ($r) use ($search) {
                return str_contains(strtolower($r['reference'] ?? ''), $search)
                    || str_contains(strtolower($r['description'] ?? ''), $search)
                    || str_contains(strtolower($r['type'] ?? ''), $search);
            });
        }

        // Compute running cumulative balance in chronological order
        $collection = $collection->sortBy(function ($r) {
            return $r['date'] ?? '';
        })->values();

        $running = 0.0;
        $collection = $collection->map(function ($r) use (&$running) {
            $running += (float) ($r['amount'] ?? 0);
            $r['balance'] = $running;
            return $r;
        })->values();

        // Reverse to newest-first for display
        $collection = $collection->sortByDesc(function ($r) {
            return $r['date'] ?? null;
        })->values();

        return DataTables::of($collection)
            ->addColumn('date', function ($row) {
                return $row['date'];
            })
            ->addColumn('type', function ($row) {
                return $row['type'];
            })
            ->addColumn('reference', function ($row) {
                return $row['reference'];
            })
            ->addColumn('description', function ($row) {
                return $row['description'];
            })
            ->addColumn('amount', function ($row) {
                return number_format((float) $row['amount'], 2, ',', '.');
            })
            ->make(true);
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $lastNumber = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $sequence = 1;

        if ($lastNumber) {
            $lastSequence = (int) Str::afterLast($lastNumber, '-');
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'payment_amount' => 'required|numeric|min:0',
            'status' => 'required|in:belum bayar,DP,lunas',
        ]);

        $project = Project::with('designBrief')->findOrFail($validated['project_id']);
        $budget = (float) ($project->designBrief?->budget ?? 0);
        $paymentAmount = (float) $validated['payment_amount'];

        $validated['invoice_number'] = $this->generateInvoiceNumber();
        $validated['amount'] = $budget;

        Invoice::create($validated);

        return response()->json(['message' => 'Invoice berhasil dibuat']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Invoice::with(['project.designBrief'])->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::with(['project.designBrief'])->findOrFail($id);
        $budget = (float) ($invoice->project?->designBrief?->budget ?? $invoice->amount ?? 0);
        $paymentAmount = (float) ($invoice->payment_amount ?? 0);

        $invoice->remaining_amount = $budget - $paymentAmount;

        return $invoice;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'payment_amount' => 'required|numeric|min:0',
            'status' => 'required|in:belum bayar,DP,lunas',
        ]);

        $project = Project::with('designBrief')->findOrFail($validated['project_id']);
        $budget = (float) ($project->designBrief?->budget ?? 0);
        $paymentAmount = (float) $validated['payment_amount'];

        $validated['amount'] = $budget;
        $validated['invoice_number'] = $invoice->invoice_number;

        $invoice->update($validated);

        return response()->json(['message' => 'Invoice berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json(['message' => 'Invoice berhasil dihapus']);
    }
}
