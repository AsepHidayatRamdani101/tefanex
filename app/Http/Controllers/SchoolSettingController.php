<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function index()
    {
        $setting = SchoolSetting::firstOrCreate(
            ['id' => 1],
            [
                'school_name' => '',
                'principal_name' => '',
                'principal_nip' => '',
                'school_logo' => null,
            ]
        );

        return view('settings.school', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = SchoolSetting::firstOrCreate(
            ['id' => 1],
            [
                'school_name' => '',
                'principal_name' => '',
                'principal_nip' => '',
                'school_logo' => null,
            ]
        );

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'principal_nip' => 'required|string|max:100',
            'school_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('school_logo')) {
            if ($setting->school_logo && Storage::disk('public')->exists($setting->school_logo)) {
                Storage::disk('public')->delete($setting->school_logo);
            }

            $validated['school_logo'] = $request->file('school_logo')->store('school_logos', 'public');
        }

        $setting->update($validated);

        return back()->with('success', 'Setting sekolah berhasil disimpan.');
    }
}
