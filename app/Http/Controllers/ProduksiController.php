<?php

namespace App\Http\Controllers;

use App\Models\Production;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('produksi.index');
    }

    public function data()
    {
        $produksis = Production::all();
        return DataTables::of($produksis)
            ->addColumn('aksi', function ($produksi) {
                return '<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="editProduksi('.$produksi->id.')">Edit</a>';
            })
            ->addColumn('status', function ($produksi) {
                return $produksi->status ? 'Aktif' : 'Non Aktif';
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
