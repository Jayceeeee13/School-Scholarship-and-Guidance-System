<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Endorsement - {{ $record->full_name ?? $record->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            color: #000;
            background: #fff;
            padding: 60px 80px;
            max-width: 850px;
            margin: 0 auto;
        }

        /* ── School Header ── */
        /*
         * TOP SECTION (small):
         *   +----------+----------------------------------+
         *   |          |  School Name + Address (top row) |
         *   |  Logo    +----------------------------------+
         *   |          |  Document Title (bottom row)     |
         *   +----------+----------------------------------+
         *
         * CONTENT SECTION (large bordered box below header):
         *   +------------------------------------------------+
         *   |  Date, fields, issue, signatures, etc.         |
         *   |                                                |
         *   +------------------------------------------------+
         */
        .school-header {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 0; /* flush against content box */
        }

        .school-header td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .school-header .logo-cell {
            width: 90px;
            text-align: center;
            padding: 8px 10px;
            vertical-align: middle;
        }

        .school-header .logo-cell img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }

        .school-header .name-cell {
            padding: 8px 12px 6px;
            text-align: center;
            border-bottom: 1px solid #000;
            vertical-align: middle;
        }

        .school-header .name-cell .school-name {
            font-family: 'Arial', sans-serif;
            font-size: 15px;
            font-weight: bold;
            color: #2d5a30;
            letter-spacing: 0.5px;
            display: block;
        }

        .school-header .name-cell .school-address {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #444;
            display: block;
            margin-top: 2px;
            font-style: italic;
        }

        .school-header .title-cell {
            padding: 10px 12px;
            text-align: center;
            vertical-align: middle;
        }

        .school-header .title-cell .doc-title {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            letter-spacing: 1px;
        }

        /* ── Large content box below header ── */
        .content-box {
            border: 1.5px solid #000;
            border-top: none; /* shares border with header bottom */
            padding: 30px 40px 40px;
            min-height: 700px;
        }

        /* ── Date ── */
        .date-row {
            text-align: right;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .date-row .date-value {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 180px;
            padding-bottom: 2px;
            margin-left: 4px;
            font-weight: normal;
            text-align: center;
        }

        .info-table {
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px 0;
            vertical-align: bottom;
        }

        .info-table td.label {
            white-space: nowrap;
            width: 110px;
        }

        .info-table td.colon {
            width: 16px;
            padding: 5px 6px 5px 0;
        }

        .underline-value {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 280px;
            padding-bottom: 2px;
        }

        .issue-section {
            margin-bottom: 40px;
        }

        .issue-label {
            margin-bottom: 6px;
        }

        .issue-line {
            border-bottom: 1px solid #000;
            min-height: 26px;
            padding: 4px 4px 2px 4px;
            margin-bottom: 6px;
            width: 100%;
        }

        .issue-line.empty {
            padding: 0;
            height: 26px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 60px;
            margin-bottom: 60px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: bottom;
            padding: 0;
        }

        .signature-table td:first-child {
            padding-right: 40px;
        }

        .signature-table td:last-child {
            padding-left: 40px;
        }

        .sig-label {
            font-weight: bold;
            display: block;
            margin-bottom: 50px;
        }

        .sig-name {
            text-align: center;
            font-size: 13px;
            margin-bottom: 4px;
            display: block;
        }

        .sig-line {
            border-top: 1px solid #000;
            display: block;
            width: 100%;
        }

        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
        }

        .bottom-section .title {
            font-weight: bold;
        }

        .bottom-section .date-block {
            font-weight: bold;
        }

        .bottom-section .date-block .date-value {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 160px;
            margin-left: 4px;
            font-weight: normal;
            text-align: center;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .print-btn:hover { background: #15803d; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 30px 50px; }
        }
    </style>
</head>
<body>

    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  School Header                                              --}}
    {{--  Layout (matches uploaded image):                           --}}
    {{--    Logo (rowspan=2) | School Name + Address  (row 1)        --}}
    {{--                     | Document Title         (row 2)        --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <table class="school-header">
        {{-- Row 1: logo + school name/address --}}
        <tr>
            <td class="logo-cell" rowspan="2">
                <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation Logo">
            </td>
            <td class="name-cell">
                <span class="school-name">GREEN VALLEY COLLEGE FOUNDATION, INC.</span>
                <span class="school-address">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</span>
            </td>
        </tr>
        {{-- Row 2: document title --}}
        <tr>
            <td class="title-cell">
                <span class="doc-title">ENDORSEMENT</span>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Large content box (everything below the header)            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="content-box">

    {{-- Date top-right --}}
    <div class="date-row">
        Date: <span class="date-value">
            {{ $record->endorsement?->date
                ? \Carbon\Carbon::parse($record->endorsement->date)->format('F d, Y')
                : '' }}
        </span>
    </div>

    {{-- To / From / Name / Course/Year --}}
    @php
        $studentName = $record->full_name ?? $record->name;
    @endphp

    <table class="info-table">
        <tr>
            <td class="label">To</td>
            <td class="colon">:</td>
            <td>
                <span class="underline-value">{{ $record->endorsement?->to_where ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="label">From</td>
            <td class="colon">:</td>
            <td>
                <span class="underline-value">{{ $record->endorsement?->from_where ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="label">Name</td>
            <td class="colon">:</td>
            <td>
                <span class="underline-value">{{ $record->endorsement?->name ?? $studentName }}</span>
            </td>
        </tr>
        <tr>
            <td class="label">Course/Year:</td>
            <td class="colon"></td>
            <td>
                <span class="underline-value">{{ $record->endorsement?->course_and_year ?? $record->course_and_year }}</span>
            </td>
        </tr>
    </table>

    {{-- Issue --}}
    <div class="issue-section">
        <div class="issue-label">Issue &nbsp;&nbsp; :</div>

        @php
            $issueText = $record->endorsement?->issue ?? '';
            $chunks = [];
            if ($issueText) {
                $words = explode(' ', $issueText);
                $line  = '';
                foreach ($words as $word) {
                    if (strlen($line . ' ' . $word) > 100) {
                        $chunks[] = trim($line);
                        $line     = $word;
                    } else {
                        $line .= ' ' . $word;
                    }
                }
                if ($line) $chunks[] = trim($line);
            }
            while (count($chunks) < 3) $chunks[] = '';
        @endphp

        @foreach($chunks as $chunk)
            <div class="issue-line {{ $chunk ? '' : 'empty' }}">{{ $chunk }}</div>
        @endforeach
    </div>

    {{-- Endorsed by / Received by --}}
    <table class="signature-table">
        <tr>
            <td>
                <span class="sig-label">Endorsed by:</span>
                <span class="sig-name">
                    {{ $record->endorsement?->personnel
                        ? trim("{$record->endorsement->personnel->first_name} {$record->endorsement->personnel->middle_name} {$record->endorsement->personnel->last_name}")
                        : '' }}
                </span>
                <span class="sig-line"></span>
            </td>
            <td>
                <span class="sig-label">Received by:</span>
                <span class="sig-name">
                    {{ $record->endorsement?->received_by ?? '' }}
                </span>
                <span class="sig-line"></span>
            </td>
        </tr>
    </table>

    {{-- Guidance Advocate / Receive Date bottom --}}
    <div class="bottom-section">
        <div class="title">Guidance Advocate</div>
        <div class="date-block">
            Date: <span class="date-value">
                {{ $record->endorsement?->receive_date
                    ? \Carbon\Carbon::parse($record->endorsement->receive_date)->format('F d, Y')
                    : '' }}
            </span>
        </div>
    </div>
    </div>{{-- /.content-box --}}

</body>
</html>