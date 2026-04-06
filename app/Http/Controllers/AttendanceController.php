<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('attendances.index', compact('users'));
    }

    public function data()
    {
        $attendances = Attendance::with('user')->select('attendances.*');

        return DataTables::of($attendances)
            ->addColumn('user', function (Attendance $attendance) {
                return $attendance->user?->name ?? '-';
            })
            ->editColumn('date', function (Attendance $attendance) {
                return is_string($attendance->date) ? $attendance->date : $attendance->date?->format('Y-m-d');
            })
            ->editColumn('status', function (Attendance $attendance) {
                $badge = match($attendance->status) {
                    'hadir' => 'success',
                    'izin' => 'warning',
                    'alpha' => 'danger',
                    default => 'secondary'
                };
                return '<span class="badge badge-' . $badge . '">' . ucfirst($attendance->status) . '</span>';
            })
            ->editColumn('created_at', function (Attendance $attendance) {
                return $attendance->created_at?->format('Y-m-d');
            })
            ->addColumn('action', function (Attendance $attendance) {
                return '<button class="btn btn-sm btn-primary editBtn" data-id="' . $attendance->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deleteBtn" data-id="' . $attendance->id . '">Hapus</button>';
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
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,izin,alpha',
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
            'status' => 'required|in:hadir,izin,alpha',
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
            'status' => 'required|in:hadir,izin,alpha',
        ]);

        $attendance->update($validated);

        return response()->json(['message' => 'Attendance berhasil diperbarui']);
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
