<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Exports\SiswaExport;
use App\Exports\SiswaTemplateExport;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::all();
        return view('siswa.index', compact('kelas'));
    }

    /**
     * Get data for DataTables
     */
    public function data()
    {
        $siswa = Siswa::with(['kelas', 'user'])->select('siswas.*');

        return DataTables::of($siswa)
            ->addColumn('kelas_name', function (Siswa $siswa) {
                return $siswa->kelas?->name ?? '-';
            })
            ->addColumn('username', function (Siswa $siswa) {
                return $siswa->user?->email ?? '-';
            })
            ->editColumn('created_at', function (Siswa $siswa) {
                return $siswa->created_at?->format('Y-m-d H:i');
            })
            ->addColumn('action', function (Siswa $siswa) {
                return '<button class="btn btn-sm btn-primary editBtn" data-id="' . $siswa->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deleteBtn" data-id="' . $siswa->id . '">Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|unique:siswas,nim',
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'no_telepon' => 'nullable|string|max:15',
            'kelas_id' => 'nullable|exists:kelas,id',
            'alamat' => 'nullable|string',
            'username' => 'nullable|string|max:255|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        try {
            $siswaData = [
                'nim' => $validated['nim'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'no_telepon' => $validated['no_telepon'],
                'kelas_id' => $validated['kelas_id'],
                'alamat' => $validated['alamat'],
            ];

            // Create user if username and password are provided
            if (!empty($validated['username']) && !empty($validated['password'])) {
                $user = User::create([
                    'email' => $validated['username'],
                    'name' => $validated['nama'],
                    'password' => Hash::make($validated['password']),
                ]);
                $user->assignRole('siswa');
                $siswaData['user_id'] = $user->id;
            }

            Siswa::create($siswaData);
            return response()->json(['message' => 'Siswa berhasil ditambahkan'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        return response()->json($siswa);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nim' => 'required|string|unique:siswas,nim,' . $id,
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . ($siswa->user_id ?? 'NULL'),
            'no_telepon' => 'nullable|string|max:15',
            'kelas_id' => 'nullable|exists:kelas,id',
            'alamat' => 'nullable|string',
            'username' => 'nullable|string|max:255|unique:users,email,' . ($siswa->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:6',
        ]);

        try {
            $siswaData = [
                'nim' => $validated['nim'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'no_telepon' => $validated['no_telepon'],
                'kelas_id' => $validated['kelas_id'],
                'alamat' => $validated['alamat'],
            ];

            // Create or update user
            if (!empty($validated['username']) && !empty($validated['password'])) {
                if ($siswa->user_id) {
                    // Update existing user
                    $siswa->user->update([
                        'email' => $validated['username'],
                        'name' => $validated['nama'],
                        'password' => Hash::make($validated['password']),
                    ]);
                } else {
                    // Create new user
                    $user = User::create([
                        'email' => $validated['username'],
                        'name' => $validated['nama'],
                        'password' => Hash::make($validated['password']),
                    ]);
                    $user->assignRole('siswa');
                    $siswaData['user_id'] = $user->id;
                }
            } elseif (!empty($validated['username']) && empty($validated['password'])) {
                // Update email only if username is provided
                if ($siswa->user_id) {
                    $siswa->user->update([
                        'email' => $validated['username'],
                        'name' => $validated['nama'],
                    ]);
                } else {
                    $user = User::create([
                        'email' => $validated['username'],
                        'name' => $validated['nama'],
                        'password' => Hash::make('password'),
                    ]);
                    $user->assignRole('siswa');
                    $siswaData['user_id'] = $user->id;
                }
            }

            $siswa->update($siswaData);
            return response()->json(['message' => 'Siswa berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);

        try {
            // Delete associated user if exists
            if ($siswa->user_id) {
                $siswa->user->delete();
            }
            $siswa->delete();
            return response()->json(['message' => 'Siswa berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export siswa data to Excel
     */
    public function export()
    {
        return Excel::download(new SiswaExport(), 'siswa_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Download template for siswa import
     */
    public function downloadTemplate()
    {
        return Excel::download(new SiswaTemplateExport(), 'template_siswa.xlsx');
    }

    /**
     * Import siswa from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new SiswaImport();
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $errors = $import->getErrors();

            if ($imported > 0) {
                $message = "Berhasil mengimport {$imported} siswa";
                if (!empty($errors)) {
                    $message .= ". Beberapa error: " . implode('; ', array_slice($errors, 0, 3));
                }
                return response()->json(['message' => $message, 'imported' => $imported, 'errors' => $errors]);
            } else {
                return response()->json(['message' => 'Tidak ada siswa yang berhasil diimport. Errors: ' . implode('; ', array_slice($errors, 0, 3))], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error importing file: ' . $e->getMessage()], 500);
        }
    }
}

