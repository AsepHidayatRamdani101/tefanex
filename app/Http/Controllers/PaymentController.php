<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payments.index');
    }

    public function data(Request $request)
    {
        // Choose an existing column to order by: prefer `date`, otherwise `created_at`
        $orderColumn = Schema::hasColumn('payments', 'date') ? 'date' : (Schema::hasColumn('payments', 'created_at') ? 'created_at' : null);
        $query = Payment::query();
        if ($orderColumn) {
            $query = $query->orderByDesc($orderColumn);
        }

        $dt = DataTables::of($query)
            ->editColumn('date', function (Payment $p) {
                // prefer explicit date column, fallback to created_at
                $d = null;
                if (isset($p->date) && $p->date) $d = $p->date;
                elseif (isset($p->created_at) && $p->created_at) $d = $p->created_at;
                return $d ? (is_string($d) ? date('Y-m-d', strtotime($d)) : $d->format('Y-m-d')) : null;
            })
            ->editColumn('amount', function (Payment $p) {
                return isset($p->amount) ? number_format((float) $p->amount, 2, ',', '.') : '-';
            })
            ->addColumn('action', function (Payment $p) {
                return '<button class="btn btn-sm btn-primary editPayment" data-id="' . $p->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deletePayment" data-id="' . $p->id . '">Hapus</button>';
            })
            ->rawColumns(['action'])
        ;

        // Map DataTables ordering on 'date' column to an actual DB column
        $dt = $dt->orderColumn('date', function ($query, $order) use ($orderColumn) {
            $col = $orderColumn ?? 'id';
            $query->orderBy($col, $order);
        });

        return $dt->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric',
        ]);

        $validated['created_by'] = auth()->id();

        Payment::create($validated);

        return response()->json(['message' => 'Pengeluaran tersimpan']);
    }

    public function edit($id)
    {
        return Payment::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric',
        ]);

        $payment->update($validated);

        return response()->json(['message' => 'Pengeluaran diperbarui']);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(['message' => 'Pengeluaran dihapus']);
    }
}
