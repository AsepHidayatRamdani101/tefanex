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
                'certificate_enabled' => true,
                'certificate_title' => 'Sertifikat Penyelesaian Modul',
                'certificate_subtitle' => 'Telah menyelesaikan seluruh alur pembelajaran dan tugas modul.',
                'certificate_footer' => 'Sertifikat ini berlaku sebagai bukti penyelesaian modul.',
                'certificate_number' => 'No. -',
                'certificate_place' => 'Bandung',
                'certificate_template' => null,
            ]
        );

        return view('setting-sekolah.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = SchoolSetting::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'school_name' => ['nullable', 'string', 'max:255'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_nip' => ['nullable', 'string', 'max:255'],
            'school_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'certificate_enabled' => ['nullable', 'boolean'],
            'certificate_title' => ['nullable', 'string'],
            'certificate_subtitle' => ['nullable', 'string'],
            'certificate_footer' => ['nullable', 'string'],
            'certificate_number' => ['nullable', 'string', 'max:255'],
            'certificate_place' => ['nullable', 'string', 'max:255'],
            'certificate_template' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:4096'],
        ]);

        $validated['certificate_enabled'] = $request->boolean('certificate_enabled');

        if ($request->hasFile('school_logo')) {
            if ($setting->school_logo && Storage::disk('public')->exists($setting->school_logo)) {
                Storage::disk('public')->delete($setting->school_logo);
            }

            $validated['school_logo'] = $request->file('school_logo')->store('school-logos', 'public');
        }

        if ($request->hasFile('certificate_template')) {
            if ($setting->certificate_template && Storage::disk('public')->exists($setting->certificate_template)) {
                Storage::disk('public')->delete($setting->certificate_template);
            }

            $validated['certificate_template'] = $request->file('certificate_template')->store('certificate-templates', 'public');
        }

        $setting->update($validated);

        return redirect()
            ->route('school-settings.index')
            ->with('success', 'Setting sekolah berhasil diperbarui.');
    }
}
