<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin');
    }
    public function index(Request $request)
    {
        
        return view('roles.index');
    }

    public function data()
    {
        $roles = Role::select('id', 'name')->latest()->get();

        return DataTables::of($roles)
            ->addIndexColumn('DT_RowIndex')
            ->addColumn('action', function ($role) {
                return '
                <button class="btn btn-sm btn-warning editBtn" data-id="' . $role->id . '">Edit</button>
                <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $role->id . '">Delete</button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create(['name' => $request->name]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id
        ]);

        $role->update(['name' => $request->name]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
