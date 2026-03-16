<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TimelineController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //view timeline
        return view('timeline.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function data(Request $request)
    {
        //get project by id
       
        $project = Project::whereHas('designBrief', function($query) {
            $query->where('is_approved', 1);
        })->get();
        return DataTables::of($project)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">Edit</a> ';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
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
