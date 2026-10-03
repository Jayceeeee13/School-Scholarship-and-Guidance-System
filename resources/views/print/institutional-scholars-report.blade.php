<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scholarship Comparative Analysis Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 210mm;
            margin: 0 auto;
            background: #e0e0e0;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto;
            padding: 15mm 15mm 15mm 15mm;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
        }

        /* ── HEADER ── */
        .doc-header {
            width: 100%;
            border: 1.5px solid #000;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .doc-header td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .logo-cell {
            width: 90px;
            text-align: center;
            vertical-align: middle;
            padding: 6px;
            border-right: 1.5px solid #000;
        }

        .logo-cell img {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .logo-cell .iso {
            font-size: 7pt;
            font-weight: bold;
            margin-top: 3px;
        }

        .school-name {
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .school-address {
            font-size: 8pt;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 7px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .doc-subtitle {
            font-size: 10pt;
            margin-top: 4px;
            font-style: italic;
        }

        /* ── REPORT TABLE ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 9pt;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .report-table th {
            font-weight: bold;
            text-align: center;
        }

        .title-row th {
            background: #1f2937;
            color: #ffffff;
            font-size: 11pt;
            padding: 8px 12px;
            letter-spacing: .08em;
        }

        .academic-year-row th {
            background: #facc15;
            color: #000000;
            text-align: left;
            font-size: 10pt;
            padding: 6px 10px;
        }

        .col-header-row th {
            background: #f3f4f6;
            color: #111827;
            font-size: 9pt;
        }

        .data-row td {
            font-size: 9pt;
            color: #111827;
        }

        .data-row td:first-child {
            text-align: left;
        }

        .data-row td:not(:first-child) {
            text-align: center;
        }

        .total-row td {
            background: #facc15;
            color: #000000;
            font-weight: bold;
            font-size: 9pt;
        }

        .total-row td:first-child {
            text-align: left;
        }

        .total-row td:not(:first-child) {
            text-align: center;
        }

        /* ── SIGNATURE BLOCK ── */
        .signature-block {
            width: 100%;
            border-collapse: collapse;
            margin-top: auto;
            padding-top: 40px;
            border: 1.5px solid #000;
            font-size: 8.5pt;
        }

        .signature-block td {
            border: 1px solid #000;
            padding: 5px 10px 10px 10px;
            vertical-align: top;
            width: 25%;
        }

        .sig-role {
            font-size: 8pt;
            color: #000;
            margin-bottom: 0;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            margin: 32px 8px 5px 8px;
        }

        .sig-title {
            font-size: 8pt;
            text-align: center;
        }

        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        @media print {
            html, body { width: 210mm; background: #fff; }
            .page { padding: 10mm 12mm 12mm 12mm; min-height: 297mm; }
            .no-print { display: none !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>
<div class="page">

    <div class="no-print" style="margin-bottom:10px; text-align:right;">
        <button onclick="window.print()"
            style="padding:7px 18px; background:#1e3a5f; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:9.5pt;">
            🖨 Print
        </button>
    </div>

    <!-- OFFICIAL HEADER -->
    <table class="doc-header">
        <tr>
            <td class="logo-cell">
                <img src="{{ asset('images/logo.png') }}" alt="School Logo" onerror="this.style.display='none'">
                <div class="iso">ISO 21001:2018</div>
            </td>
            <td style="text-align:center; vertical-align:middle; padding:0;">
                <div style="padding: 6px 12px; border-bottom: 1px solid #000;">
                    <div class="school-name">GREEN VALLEY COLLEGE FOUNDATION, INC.</div>
                    <div class="school-address">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</div>
                </div>
                <div style="padding: 8px 12px;">
                    <div class="doc-title">Scholarship Comparative Analysis Report</div>
                    <div class="doc-subtitle">Academic Year: {{ $school_year ?? '—' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- REPORT TABLE -->
    <div class="content-area">
        <table class="report-table">
            <thead>
                <tr class="academic-year-row">
                    <th colspan="3">ACADEMIC YEAR: {{ $school_year ?? '—' }}</th>
                </tr>
                <tr class="col-header-row">
                    <th style="width:50%; text-align:left; padding-left:10px;">Type of Scholarships</th>
                    <th>{{ $term1 ? $term1->semester : '1st Semester' }}</th>
                    <th>{{ $term2 ? $term2->semester : '2nd Semester' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="data-row">
                        <td>{{ $row['type'] }}</td>
                        <td>{{ $row['t1_count'] ?: '' }}</td>
                        <td>{{ $row['t2_count'] ?: '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center; padding:14px; color:#888;">
                            No scholarship types found.
                        </td>
                    </tr>
                @endforelse

                <tr class="total-row">
                    <td>GRAND TOTAL OF SCHOLARS</td>
                    <td>{{ $grand_t1 ?: '0' }}</td>
                    <td>{{ $grand_t2 ?: '0' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SIGNATURE BLOCK -->
        <table class="signature-block">
            <tr>
                <td>
                    <div class="sig-role">Prepared by:</div>
                    <div class="sig-line"></div>
                    <div class="sig-title">Guidance and Scholarship Director</div>
                </td>
                <td>
                    <div class="sig-role">Reviewed by:</div>
                    <div class="sig-line"></div>
                    <div class="sig-title">Dean for Support Services</div>
                </td>
                <td>
                    <div class="sig-role">Recommending Approval:</div>
                    <div class="sig-line"></div>
                    <div class="sig-title">Vice President for Admin and Finance</div>
                </td>
                <td>
                    <div class="sig-role">Approved by:</div>
                    <div class="sig-line"></div>
                    <div class="sig-title">President</div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>