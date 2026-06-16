<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
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
            background: #ffffff;
        }

        .page {
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 32px 36px;
            position: relative;
            overflow: hidden;
            background: #ffffff;
        }

        .template-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .overlay {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header-decoration,
        .footer-decoration {
            display: flex;
            gap: 0;
        }

        .header-decoration {
            margin-bottom: 22px;
        }

        .footer-decoration {
            margin-top: 22px;
        }

        .header-decoration .box,
        .footer-decoration .box {
            height: 14px;
            flex: 1;
        }

        .yellow {
            background: #F1C40F;
        }

        .red {
            background: #E74C3C;
        }

        .teal {
            background: #1ABC9C;
        }

        .blue {
            background: #2E86C1;
        }

        .certificate-content {
            border: 3px solid rgba(170, 170, 170, 0.9);
            padding: 34px 40px 28px;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.92);
        }

        .certificate-number {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            text-align: center;
            margin-bottom: 12px;
            letter-spacing: 0.4px;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .title {
            font-size: 44px;
            font-weight: bold;
            color: #1F4E79;
            margin-bottom: 10px;
            letter-spacing: 1.5px;
        }

        .subtitle {
            font-size: 13px;
            font-style: italic;
            color: #666;
            margin-bottom: 22px;
        }

        .label {
            font-size: 13px;
            color: #555;
            margin-top: 12px;
            margin-bottom: 8px;
        }

        .student-name {
            font-size: 46px;
            font-family: 'Brush Script MT', 'Lucida Handwriting', cursive, serif;
            color: #1F4E79;
            font-style: italic;
            font-weight: normal;
            line-height: 1.05;
            margin: 8px 0 10px;
        }

        .project-name {
            font-size: 15px;
            font-weight: bold;
            color: #333;
            margin: 10px 0 14px;
        }

        .date-location {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }

        .signature-area {
            display: flex;
            justify-content: flex-end;
            margin-top: 28px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 42px 0 6px 0;
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

        .logo {
            width: 58px;
            height: 58px;
            margin: 12px auto 0;
            object-fit: contain;
        }

        .footer-text {
            font-size: 10px;
            color: #999;
            margin-top: 16px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="page">
        @if (!empty($templateUrl))
            <img src="{{ $templateUrl }}" class="template-bg" alt="Template Sertifikat">
        @endif

        <div class="overlay">
            <div class="header-decoration">
                <div class="box yellow" style="flex: 0.45;"></div>
                <div class="box red" style="flex: 0.55;"></div>
            </div>

            <div class="certificate-content">


                <div class="school-name">{{ $schoolName }}</div>
                @if (!empty($certificateNumber))
                    <div class="certificate-number">{{ $certificateNumber }}</div>
                @endif
                <div class="title">{{ $title }}</div>


                <div class="label">Diberikan kepada:</div>
                <div class="student-name">{{ $studentName }}</div>

                <div class="label">sebagai:</div>
                <div class="subtitle">{{ $subtitle }}</div>
                <div class="project-name">{{ $projectText }}</div>

                <div class="date-location">{{ $certificateLocation ?: 'Bandung' }}, {{ $date }}</div>

                <div class="signature-area">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $principalName }}</div>
                        <div class="signature-nip">{{ $principalNip }}</div>
                    </div>
                </div>

                @if (!empty($logoUrl))
                    <img src="{{ $logoUrl }}" class="logo" alt="Logo Sekolah">
                @endif

                <div class="footer-text">{{ $footer }}</div>
            </div>

            <div class="footer-decoration">
                <div class="box teal" style="flex: 0.55;"></div>
                <div class="box blue" style="flex: 0.45;"></div>
            </div>
        </div>
    </div>
</body>

</html>
