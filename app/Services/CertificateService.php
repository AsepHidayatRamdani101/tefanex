<?php

namespace App\Services;

use App\Models\Project_Member;
use App\Models\SchoolSetting;
use App\Models\Test_Result;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    public function isStudentCompleted($user): bool
    {
        $members = Project_Member::where('user_id', $user->id)
            ->with(['project.designBrief', 'project.mockups', 'project.productions', 'project.qualityControls'])
            ->get();

        if ($members->isEmpty()) {
            return false;
        }

        foreach ($members as $member) {
            $project = $member->project;
            if (!$project) {
                return false;
            }

            $role = strtolower($member->role_in_project);
            if ($this->isRoleRequiringDesignBrief($role)) {
                if (!$project->designBrief || $project->designBrief->approval_status !== 'approved') {
                    return false;
                }
            }

            if ($this->isRoleRequiringMockup($role)) {
                if ($project->mockups->isEmpty() || $project->mockups->contains(fn($mockup) => $mockup->status !== 'approved')) {
                    return false;
                }
            }

            if ($this->isRoleRequiringProduction($role)) {
                if ($project->productions->isEmpty() || $project->productions->contains(fn($production) => $production->status !== 'selesai')) {
                    return false;
                }
            }

            if ($this->isRoleRequiringQualityControl($role)) {
                if ($project->qualityControls->isEmpty() || $project->qualityControls->contains(fn($quality) => $quality->status !== 'lulus')) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isRoleRequiringDesignBrief(string $role): bool
    {
        return in_array($role, ['marketing', 'marketing (pemasaran)']);
    }

    private function isRoleRequiringMockup(string $role): bool
    {
        return in_array($role, ['designer', 'desain']);
    }

    private function isRoleRequiringProduction(string $role): bool
    {
        return in_array($role, ['operator produksi', 'operator_produksi', 'produksi']);
    }

    private function isRoleRequiringQualityControl(string $role): bool
    {
        return in_array($role, ['qc', 'quality control', 'quality_control', 'kontrol kualitas']);
    }

    public function getStudentModuleGrades($user): array
    {
        $results = Test_Result::join('tests', 'test_results.test_id', '=', 'tests.id')
            ->join('materials', 'tests.material_id', '=', 'materials.id')
            ->leftJoin('projects', 'materials.project_id', '=', 'projects.id')
            ->where('test_results.user_id', $user->id)
            ->select(
                'test_results.*',
                'tests.type as test_type_raw',
                'projects.judul as project_name',
                'materials.title as material_title',
                'materials.id as material_id',
                'projects.id as project_id'
            )
            ->get()
            ->groupBy(function ($result) {
                return $result->project_id . ':' . $result->material_id;
            })
            ->map(function ($group) {
                $first = $group->first();
                $pretest = $group->firstWhere('test_type_raw', 'pretest');
                $posttest = $group->firstWhere('test_type_raw', 'posttest');
                $taskScore = $group->pluck('task_score')->filter(fn($value) => $value !== null && $value !== '')->last();

                $pretestScore = $pretest?->manual_score ?? $pretest?->score;
                $posttestScore = $posttest?->manual_score ?? $posttest?->score;

                return [
                    'project_name' => $first->project_name ?: 'Tidak Diketahui',
                    'material_title' => $first->material_title ?: 'Tidak Diketahui',
                    'pretest_score' => $pretestScore,
                    'posttest_score' => $posttestScore,
                    'task_score' => $taskScore,
                    'average_score' => $this->calculateAverageScore([
                        $pretestScore,
                        $posttestScore,
                        $taskScore,
                    ]),
                ];
            })
            ->values()
            ->toArray();

        return $results;
    }

    private function calculateAverageScore(array $scores): ?float
    {
        $filtered = collect($scores)->filter(fn($score) => $score !== null && $score !== '');

        if ($filtered->isEmpty()) {
            return null;
        }

        return round($filtered->avg(), 2);
    }

    public function calculateOverallScore($user): ?float
    {
        $moduleGrades = collect($this->getStudentModuleGrades($user));
        $averageScores = $moduleGrades->pluck('average_score')->filter(fn($value) => $value !== null && $value !== '')->map(fn($value) => (float) $value);

        if ($averageScores->isEmpty()) {
            return null;
        }

        return round($averageScores->avg(), 2);
    }

    public function generateCertificateDocument(string $studentName, array $moduleGrades, SchoolSetting $setting, ?float $overallScore = null): string
    {
        $schoolName = $setting->school_name ?: 'Nama Sekolah';
        $principalName = $setting->principal_name ?: 'Nama Kepala Sekolah';
        $principalNip = $setting->principal_nip ?: 'NIP Kepala Sekolah';
        $certificateTitle = $setting->certificate_title ?: 'Sertifikat Penyelesaian Modul';
        $certificateSubtitle = $setting->certificate_subtitle ?: 'Telah menyelesaikan seluruh alur pembelajaran dan tugas modul.';
        $certificateFooter = $setting->certificate_footer ?: 'Sertifikat ini berlaku sebagai bukti penyelesaian modul.';
        $certificateNumber = $setting->certificate_number ?: '';
        $certificateLocation = $setting->certificate_place ?: '';

        $projectText = count($moduleGrades) > 0
            ? implode(', ', array_unique(array_map(fn($grade) => $grade['project_name'], $moduleGrades)))
            : 'Seluruh Modul';

        $logoUrl = '';
        if ($setting->school_logo && Storage::disk('public')->exists($setting->school_logo)) {
            $logoUrl = asset('storage/' . $setting->school_logo);
        }

        $templateUrl = '';
        if ($setting->certificate_template && Storage::disk('public')->exists($setting->certificate_template)) {
            $templateUrl = asset('storage/' . $setting->certificate_template);
        }

        $html = $this->renderCertificateHTML(
            $schoolName,
            $certificateTitle,
            $certificateSubtitle,
            $studentName,
            $projectText,
            $certificateFooter,
            $principalName,
            $principalNip,
            $logoUrl,
            $certificateNumber,
            $certificateLocation,
            $templateUrl
        );

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $tempFile = tempnam(sys_get_temp_dir(), 'certificate_') . '.pdf';
        $pdf->save($tempFile);

        return $tempFile;
    }

    private function renderCertificateHTML(string $schoolName, string $title, string $subtitle, string $studentName, string $projectText, string $footer, string $principalName, string $principalNip, string $logoUrl, string $certificateNumber = '', string $certificateLocation = '', string $templateUrl = ''): string
    {
        $data = [
            'schoolName' => $schoolName,
            'title' => $title,
            'subtitle' => $subtitle,
            'studentName' => $studentName,
            'projectText' => $projectText,
            'footer' => $footer,
            'principalName' => $principalName,
            'principalNip' => $principalNip,
            'logoUrl' => $logoUrl,
            'certificateNumber' => $certificateNumber,
            'certificateLocation' => $certificateLocation,
            'templateUrl' => $templateUrl,
            'date' => date('d F Y'),
        ];

        return view('certificates.template', $data)->render();
    }
}
