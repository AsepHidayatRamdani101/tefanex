<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\Siswa;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function downloadAdminCertificate()
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
            ]
        );

        $filePath = $this->certificateService->generateCertificateDocument(
            'Contoh Peserta',
            [
                [
                    'project_name' => 'Seluruh Modul',
                    'material_title' => 'Seluruh Modul',
                    'pretest_score' => null,
                    'posttest_score' => null,
                    'task_score' => null,
                    'average_score' => null,
                ],
            ],
            $setting
        );

        return response()->download($filePath, 'sertifikat-contoh.pdf', [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    public function downloadStudentCertificate()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

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
            ]
        );

        if (!$setting->certificate_enabled) {
            abort(403, 'Sertifikat belum diaktifkan oleh administrator.');
        }

        if (! $this->certificateService->isStudentCompleted($user)) {
            abort(403, 'Anda belum menyelesaikan seluruh alur modul.');
        }

        $moduleGrades = $this->certificateService->getStudentModuleGrades($user);
        $overallScore = $this->certificateService->calculateOverallScore($user);

        $filePath = $this->certificateService->generateCertificateDocument(
            $siswa->nama,
            $moduleGrades,
            $setting,
            $overallScore
        );

        $fileName = sprintf('sertifikat-%s-%s.pdf', str()->slug($siswa->nama), date('Ymd'));

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
