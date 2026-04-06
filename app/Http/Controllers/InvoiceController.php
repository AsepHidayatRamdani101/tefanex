<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderBy('judul')->get();
        return view('invoices.index', compact('projects'));
    }

    public function data()
    {
        $invoices = Invoice::with('project')->select('invoices.*');

        return DataTables::of($invoices)
            ->addColumn('project', function (Invoice $invoice) {
                return $invoice->project?->judul ?? '-';
            })
            ->editColumn('amount', function (Invoice $invoice) {
                return number_format($invoice->amount, 2, ',', '.');
            })
            ->editColumn('status', function (Invoice $invoice) {
                $badge = $invoice->status === 'paid' ? 'success' : 'secondary';
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:belum bayar,DP,lunas',
        ]);

        Invoice::create($validated);

        return response()->json(['message' => 'Invoice berhasil dibuat']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Invoice::with('project')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Invoice::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:belum bayar,DP,lunas',
        ]);

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
