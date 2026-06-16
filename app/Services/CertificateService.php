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
        $certificateLocation = $setting->certificate_location ?: '';

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

        $html = $this->renderCertificateHTML($schoolName, $certificateTitle, $certificateSubtitle, $studentName, $projectText, $certificateFooter, $principalName, $principalNip, $logoUrl, $certificateNumber, $certificateLocation, $templateUrl);

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $tempFile = tempnam(sys_get_temp_dir(), 'certificate_') . '.pdf';
        $pdf->save($tempFile);

        return $tempFile;
    }

    private function renderCertificateHTML(string $schoolName, string $title, string $subtitle, string $studentName, string $projectText, string $footer, string $principalName, string $principalNip, string $logoUrl, string $certificateNumber = '', string $certificateLocation = '', string $templateUrl = ''): string
    {
        $date = date('d F Y');
        $logoImg = $logoUrl ? "<img src=\"$logoUrl\" class=\"logo\" />" : '';
        $displayLocation = $certificateLocation ?: 'Bandung';
        $templateBg = $templateUrl ? "background-image: url('$templateUrl'); background-size: cover; background-position: center;" : '';
        $numberDisplay = $certificateNumber ? "<div class=\"certificate-number\">$certificateNumber</div>" : '';

        return "<!DOCTYPE html>
<html lang=\"id\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>$title</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', serif;
            width: 100%;
            padding: 0;
            margin: 0;
            background: white;
        }
        .page {
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 40px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            $templateBg
        }
        .header-decoration {
            display: flex;
            gap: 0;
            margin-bottom: 30px;
        }
        .header-decoration .box {
            height: 15px;
            flex: 1;
        }
        .yellow { background: #F1C40F; }
        .red { background: #E74C3C; }
        .teal { background: #1ABC9C; }
        .blue { background: #2E86C1; }

        .certificate-content {
            border: 3px solid #CCCCCC;
            padding: 40px;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
        }

        .certificate-number {
            font-size: 11px;
            color: #666;
            text-align: right;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .title {
            font-size: 48px;
            font-weight: bold;
            color: #1F4E79;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 14px;
            font-style: italic;
            color: #666;
            margin-bottom: 30px;
        }

        .label {
            font-size: 13px;
            color: #555;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .student-name {
            font-size: 50px;
            font-family: 'Brush Script MT', 'Lucida Handwriting', cursive, serif;
            color: #1F4E79;
            font-style: italic;
            margin: 15px 0;
            font-weight: normal;
        }

        .project-name {
            font-size: 15px;
            font-weight: bold;
            color: #333;
            margin: 15px 0;
        }

        .signature-area {
            display: flex;
            justify-content: flex-end;
            gap: 60px;
            margin-top: 40px;
        }

        .signature-box {
            width: 180px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 40px 0 5px 0;
            height: 1px;
        }

        .signature-name {
            font-size: 12px;
            font-weight: bold;
        }

        .signature-nip {
            font-size: 11px;
            color: #666;
        }

        .date-location {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 20px auto 0;
        }

        .footer-text {
            font-size: 10px;
            color: #999;
            margin-top: 20px;
            font-style: italic;
        }

        .footer-decoration {
            display: flex;
            gap: 0;
            margin-top: 30px;
        }
        .footer-decoration .box {
            height: 12px;
            flex: 1;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class=\"page\">
        <div class=\"header-decoration\">
            <div class=\"box yellow\" style=\"flex: 0.45;\"></div>
            <div class=\"box red\" style=\"flex: 0.55;\"></div>
        </div>

        <div class=\"certificate-content\">
            $numberDisplay
            <div class=\"school-name\">$schoolName</div>
            <div class=\"title\">$title</div>
            <div class=\"subtitle\">$subtitle</div>

            <div class=\"label\">Diberikan kepada:</div>
            <div class=\"student-name\">$studentName</div>

            <div class=\"label\">Untuk:</div>
            <div class=\"project-name\">$projectText</div>

            <div class=\"date-location\">$displayLocation, $date</div>

            <div class=\"signature-area\">
                <div class=\"signature-box\">
                    <div class=\"signature-line\"></div>
                    <div class=\"signature-name\">$principalName</div>
                    <div class=\"signature-nip\">$principalNip</div>
                </div>
            </div>

            $logoImg

            <div class=\"footer-text\">$footer</div>
        </div>

        <div class=\"footer-decoration\">
            <div class=\"box teal\" style=\"flex: 0.55;\"></div>
            <div class=\"box blue\" style=\"flex: 0.45;\"></div>
        </div>
    </div>
</body>
</html>";
    }
}
