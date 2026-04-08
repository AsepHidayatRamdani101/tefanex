<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Exports\KelasExport;
use App\Exports\KelasTemplateExport;
use App\Imports\KelasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kelas.index');
    }

    /**
     * Get data for DataTables
     */
    public function data()
    {
        $kelas = Kelas::select('*');

        return DataTables::of($kelas)
            ->editColumn('created_at', function (Kelas $kelas) {
                return $kelas->created_at?->format('Y-m-d H:i');
            })
            ->addColumn('action', function (Kelas $kelas) {
                return '<button class="btn btn-sm btn-primary editBtn" data-id="' . $kelas->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deleteBtn" data-id="' . $kelas->id . '">Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:kelas,code',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1|max:200',
        ]);

        try {
            Kelas::create($validated);
            return response()->json(['message' => 'Kelas berhasil ditambahkan'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kelas = Kelas::findOrFail($id);
        return response()->json($kelas);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('kelas.edit', compact('kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:kelas,code,' . $id,
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1|max:200',
        ]);

        try {
            $kelas->update($validated);
            return response()->json(['message' => 'Kelas berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kelas = Kelas::findOrFail($id);

        try {
            $kelas->delete();
            return response()->json(['message' => 'Kelas berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export data kelas to Excel
     */
    public function export()
    {
        return Excel::download(new KelasExport(), 'kelas_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Download template for kelas import
     */
    public function downloadTemplate()
    {
        return Excel::download(new KelasTemplateExport(), 'template_kelas.xlsx');
    }

    /**
     * Import kelas from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new KelasImport();
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $errors = $import->getErrors();

            if ($imported > 0) {
                $message = "Berhasil mengimport {$imported} kelas";
                if (!empty($errors)) {
                    $message .= ". Beberapa error: " . implode('; ', array_slice($errors, 0, 3));
                }
                return response()->json(['message' => $message, 'imported' => $imported, 'errors' => $errors]);
            } else {
                return response()->json(['message' => 'Tidak ada kelas yang berhasil diimport. Errors: ' . implode('; ', array_slice($errors, 0, 3))], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error importing file: ' . $e->getMessage()], 500);
        }
    }
}
