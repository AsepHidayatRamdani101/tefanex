<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Exports\RekapAbsensiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswas = Siswa::with('user', 'kelas')
            ->whereNotNull('user_id')
            ->orderBy('nama')
            ->get();
        $kelasList = Kelas::orderBy('name')->get();

        return view('attendances.index', compact('siswas', 'kelasList'));
    }

    public function data()
    {
        $kelasId = request('kelas_id');
        $date = request('date');

        $siswas = Siswa::with(['user', 'kelas'])
            ->whereNotNull('user_id')
            ->when($kelasId, function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) use ($date) {
                $attendance = null;
                if ($date) {
                    $attendance = Attendance::where('user_id', $siswa->user_id)
                        ->whereDate('date', $date)
                        ->first();
                }
                
                return [
                    'id' => $attendance?->id ?? 'new_' . $siswa->id,
                    'user_id' => $siswa->user_id,
                    'siswa_id' => $siswa->id,
                    'user' => $siswa->nama,
                    'kelas' => $siswa->kelas?->name ?? '-',
                    'status' => $attendance?->status ?? '',
                ];
            });

        return DataTables::of(collect($siswas))
            ->addColumn('checkbox', function ($data) {
                return '<input type="checkbox" class="row-checkbox" value="' . $data['id'] . '" data-user-id="' . $data['user_id'] . '">';
            })
            ->addColumn('user', function ($data) {
                return $data['user'];
            })
            ->addColumn('kelas', function ($data) {
                return $data['kelas'];
            })
            ->addColumn('status_dropdown', function ($data) {
                $selected = $data['status'];
                return '<select class="form-control form-control-sm status-dropdown" data-attendance-id="' . $data['id'] . '" data-user-id="' . $data['user_id'] . '">' .
                    '<option value="">-- Pilih Status --</option>' .
                    '<option value="hadir" ' . ($selected === 'hadir' ? 'selected' : '') . '>Hadir</option>' .
                    '<option value="sakit" ' . ($selected === 'sakit' ? 'selected' : '') . '>Sakit</option>' .
                    '<option value="izin" ' . ($selected === 'izin' ? 'selected' : '') . '>Izin</option>' .
                    '<option value="alpha" ' . ($selected === 'alpha' ? 'selected' : '') . '>Alpa</option>' .
                    '</select>';
            })
            ->rawColumns(['checkbox', 'status_dropdown'])
            ->make(true);
    }

    /**
     * Display attendance recap report.
     */
    public function rekap(Request $request)
    {
        $kelasList = Kelas::orderBy('name')->get();

        $students = Siswa::with(['user', 'kelas'])
            ->whereNotNull('user_id')
            ->when($request->filled('kelas_id'), function ($query) use ($request) {
                $query->where('kelas_id', $request->kelas_id);
            })
            ->orderBy('nama')
            ->get();

        $userIds = $students->pluck('user_id')->filter()->values();

        $attendanceGrouped = Attendance::query()
            ->when($userIds->isNotEmpty(), function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            });

        // Date filtering: if both from and to provided, use inclusive whereBetween
        $from = !empty($request->date_from) ? Carbon::createFromFormat('Y-m-d', $request->date_from)->toDateString() : null;
        $to = !empty($request->date_to) ? Carbon::createFromFormat('Y-m-d', $request->date_to)->toDateString() : null;

        if ($from && $to) {
            $attendanceGrouped = $attendanceGrouped->whereBetween('date', [$from, $to]);
        } elseif ($from) {
            $attendanceGrouped = $attendanceGrouped->whereDate('date', '>=', $from);
        } elseif ($to) {
            $attendanceGrouped = $attendanceGrouped->whereDate('date', '<=', $to);
        }

        $attendanceGrouped = $attendanceGrouped
            ->selectRaw('user_id, status, COUNT(*) as total')
            ->groupBy('user_id', 'status')
            ->get()
            ->groupBy('user_id');

        $rekapRows = $students->map(function ($student) use ($attendanceGrouped) {
            $userStats = $attendanceGrouped->get($student->user_id, collect());

            $hadir = (int) optional($userStats->firstWhere('status', 'hadir'))->total;
            $sakit = (int) optional($userStats->firstWhere('status', 'sakit'))->total;
            $izin = (int) optional($userStats->firstWhere('status', 'izin'))->total;
            $alpha = (int) optional($userStats->firstWhere('status', 'alpha'))->total;

            return [
                'nama' => $student->nama,
                'kelas' => $student->kelas?->name ?? '-',
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'total' => $hadir + $sakit + $izin + $alpha,
            ];
        });

        return view('attendances.rekap', [
            'kelasList' => $kelasList,
            'rekapRows' => $rekapRows,
            'filters' => [
                'kelas_id' => $request->kelas_id ?? '',
                'date_from' => $request->date_from ?? '',
                'date_to' => $request->date_to ?? '',
            ],
        ]);
    }

    /**
     * AJAX endpoint for attendance recap used by DataTables.
     */
    public function rekapData(Request $request)
    {
        $students = Siswa::with(['user', 'kelas'])
            ->whereNotNull('user_id')
            ->when($request->filled('kelas_id'), function ($query) use ($request) {
                $query->where('kelas_id', $request->kelas_id);
            })
            ->orderBy('nama')
            ->get();

        $userIds = $students->pluck('user_id')->filter()->values();

        $attendanceGrouped = Attendance::query()
            ->when($userIds->isNotEmpty(), function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            });

        $from = !empty($request->date_from) ? Carbon::createFromFormat('Y-m-d', $request->date_from)->toDateString() : null;
        $to = !empty($request->date_to) ? Carbon::createFromFormat('Y-m-d', $request->date_to)->toDateString() : null;

        if ($from && $to) {
            $attendanceGrouped = $attendanceGrouped->whereBetween('date', [$from, $to]);
        } elseif ($from) {
            $attendanceGrouped = $attendanceGrouped->whereDate('date', '>=', $from);
        } elseif ($to) {
            $attendanceGrouped = $attendanceGrouped->whereDate('date', '<=', $to);
        }

        $attendanceGrouped = $attendanceGrouped
            ->selectRaw('user_id, status, COUNT(*) as total')
            ->groupBy('user_id', 'status')
            ->get()
            ->groupBy('user_id');

        $rows = $students->map(function ($student) use ($attendanceGrouped) {
            $userStats = $attendanceGrouped->get($student->user_id, collect());

            $hadir = (int) optional($userStats->firstWhere('status', 'hadir'))->total;
            $sakit = (int) optional($userStats->firstWhere('status', 'sakit'))->total;
            $izin = (int) optional($userStats->firstWhere('status', 'izin'))->total;
            $alpha = (int) optional($userStats->firstWhere('status', 'alpha'))->total;

            return [
                'nama' => $student->nama,
                'kelas' => $student->kelas?->name ?? '-',
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'total' => $hadir + $sakit + $izin + $alpha,
            ];
        });

        // write simple debug log with incoming filters and result count
        try {
            $log = '[' . now() . '] rekapData filters: ' . json_encode([
                'kelas_id' => $request->kelas_id ?? null,
                'date_from' => $request->date_from ?? null,
                'date_to' => $request->date_to ?? null,
            ]) . ' => rows: ' . count($rows) . "\n";
            file_put_contents(storage_path('logs/rekap_debug.log'), $log, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // ignore logging errors
        }

        return DataTables::of($rows)
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        Attendance::create($validated);

        return response()->json(['message' => 'Attendance berhasil dibuat']);
    }

    /**
     * Store bulk attendance for multiple users.
     */
    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        foreach ($validated['user_ids'] as $userId) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $validated['date'],
                'status' => $validated['status'],
            ]);
        }

        return response()->json(['message' => 'Bulk attendance berhasil dibuat']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Attendance::with('user')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Attendance::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        $attendance->update($validated);

        return response()->json(['message' => 'Attendance berhasil diperbarui']);
    }

    /**
     * Export attendance recap.
     */
    public function exportRekap(Request $request)
    {
        try {
            $students = Siswa::with(['user', 'kelas'])
                ->whereNotNull('user_id')
                ->when($request->filled('kelas_id'), function ($query) use ($request) {
                    $query->where('kelas_id', $request->kelas_id);
                })
                ->orderBy('nama')
                ->get();

            $userIds = $students->pluck('user_id')->filter()->values();

            $attendanceGrouped = Attendance::query()
                ->when($userIds->isNotEmpty(), function ($query) use ($userIds) {
                    $query->whereIn('user_id', $userIds);
                }, function ($query) {
                    $query->whereRaw('1 = 0');
                });

            $from = !empty($request->date_from) ? Carbon::createFromFormat('Y-m-d', $request->date_from)->toDateString() : null;
            $to = !empty($request->date_to) ? Carbon::createFromFormat('Y-m-d', $request->date_to)->toDateString() : null;

            if ($from && $to) {
                $attendanceGrouped = $attendanceGrouped->whereBetween('date', [$from, $to]);
            } elseif ($from) {
                $attendanceGrouped = $attendanceGrouped->whereDate('date', '>=', $from);
            } elseif ($to) {
                $attendanceGrouped = $attendanceGrouped->whereDate('date', '<=', $to);
            }

            $attendanceGrouped = $attendanceGrouped
                ->selectRaw('user_id, status, COUNT(*) as total')
                ->groupBy('user_id', 'status')
                ->get()
                ->groupBy('user_id');

            $rekapRows = $students->map(function ($student) use ($attendanceGrouped) {
                $userStats = $attendanceGrouped->get($student->user_id, collect());

                $hadir = (int) optional($userStats->firstWhere('status', 'hadir'))->total;
                $sakit = (int) optional($userStats->firstWhere('status', 'sakit'))->total;
                $izin = (int) optional($userStats->firstWhere('status', 'izin'))->total;
                $alpha = (int) optional($userStats->firstWhere('status', 'alpha'))->total;

                return [
                    'nama' => $student->nama,
                    'kelas' => $student->kelas?->name ?? '-',
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpha' => $alpha,
                    'total' => $hadir + $sakit + $izin + $alpha,
                ];
            })->toArray();

            $filename = 'Rekap_Absensi_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new RekapAbsensiExport($rekapRows), $filename);
        } catch (\Exception $e) {
            return redirect()->route('attendances.rekap')->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance berhasil dihapus']);
    }
}
