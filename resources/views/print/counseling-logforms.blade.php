<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Counseling Logforms</title>
    <style>
        *, *::before, *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: 210mm 297mm portrait;
            margin: 12mm 10mm 12mm 10mm;
        }

        html {
            width: 210mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            width: 210mm;
        }

        @media screen {
            html, body {
                width: auto;
                background: #d1d5db;
                padding: 20px 0;
            }
            .page {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
                padding: 12mm 10mm 12mm 10mm;
                box-shadow: 0 6px 32px rgba(0,0,0,0.22);
            }
        }

        @media print {
            html, body { width: 100%; background: #fff; }
            .page { padding: 0; margin: 0; width: 100%; }
            .no-print { display: none !important; }
            table { page-break-inside: auto; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }

        .no-print {
            text-align: center;
            padding: 10px;
            background: #f3f4f6;
            margin-bottom: 16px;
            border-radius: 6px;
        }
        .no-print button {
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 9px 22px;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 8px;
        }
        .no-print button:hover { background: #15803d; }
        .no-print a { color: #6b7280; font-size: 12px; text-decoration: none; }

        /* ─────────────────────────────────────────
           HEADER TABLE — logo (spans 3 rows) |
           row 1: school name + address
           row 2: document title
           row 3: Semester A.Y. ___
        ───────────────────────────────────────── */
        .doc-header {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 12px;
        }

        .doc-header td {
            vertical-align: middle;
        }

        .doc-header .logo-cell {
            width: 90px;
            text-align: center;
            padding: 8px 10px;
            border-right: 1px solid #000;
        }

        .doc-header .logo-cell img {
            width: 62px;
            height: 62px;
            object-fit: contain;
        }

        .doc-header .name-row td {
            padding: 8px 12px 6px;
            text-align: center;
            border-bottom: 1px solid #000;
        }

        .doc-header .school-name {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: block;
        }

        .doc-header .school-address {
            font-size: 8.5pt;
            font-style: italic;
            color: #333;
            display: block;
            margin-top: 1px;
        }

        .doc-header .title-row td {
            padding: 7px 12px;
            text-align: center; 
        }

        .doc-header .doc-title {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .doc-header .term-row td {
            padding: 7px 12px;
            text-align: center;
        }

        .doc-header .term-line {
            font-size: 9.5pt;
        }

        .doc-header .term-line .blank {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 160px;
            margin-left: 4px;
        }

        /* ── Content box below header ── */
        .content-box {
            padding: 10px 0 0;
        }

        /* ── Data table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            table-layout: fixed;
        }

        col.col-no      { width: 5%; }
        col.col-name    { width: 20%; }
        col.col-course  { width: 13%; }
        col.col-contact { width: 15%; }
        col.col-concern { width: 23%; }
        col.col-remarks { width: 24%; }

        .data-table thead tr {
            background-color: #1a6b3a !important;
        }
        .data-table thead th {
            padding: 7px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #155e32;
            background-color: #1a6b3a !important;
            color: #fff !important;
            white-space: nowrap;
            overflow: hidden;
        }
        .data-table th.col-no { text-align: center; }

        .data-table tbody tr:nth-child(even) td {
            background-color: #f0fdf4 !important;
        }
        .data-table tbody tr:nth-child(odd) td {
            background-color: #ffffff !important;
        }
        .data-table tbody td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            font-size: 9.5pt;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .data-table td.col-no { text-align: center; }

        .summary {
            margin-top: 5px;
            font-size: 8.5pt;
            color: #555;
            text-align: right;
            padding: 0 8px 8px;
        }

        .footer {
            margin-top: 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer .sig-block {
            text-align: center;
            width: 50mm;
        }
        .footer .sig-block .sig-line {
            border-top: 1.5px solid #000;
            margin-top: 40px;
            padding-top: 4px;
            font-weight: bold;
            font-size: 10pt;
        }
        .footer .sig-block .sig-title {
            font-size: 8.5pt;
            color: #444;
            margin-top: 2px;
        }
        .footer .printed-meta {
            font-size: 8pt;
            color: #555;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
        <a href="javascript:history.back()">← Go Back</a>
    </div>

    <div class="page">

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{--  Header: logo | name+address / title / Semester A.Y. ___    --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <table class="doc-header">
            <tr class="name-row">
                <td class="logo-cell" rowspan="3">
                    <img src="{{ asset('images/logo.png') }}" alt="GVCF Logo">
                </td>
                <td>
                    <span class="school-name">Green Valley College Foundation, Inc.</span>
                    <span class="school-address">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</span>
                </td>
            </tr>
            <tr class="title-row">
                <td>
                    <span class="doc-title">LOG FOR COUNSELING SERVICES</span>
                </td>
            </tr>
            <tr class="term-row">
    <td>
        <span class="term-line">
            <span class="blank">&nbsp;{{ $semesterOnly ?? '' }}</span>
            Semester A.Y.
            <span class="blank">&nbsp;{{ $schoolYearOnly ?? '' }}</span>
        </span>
    </td>
</tr>
        </table>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{--  Content box (large bordered area below header)             --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="content-box">

            <table class="data-table">
                <colgroup>
                    <col class="col-no">
                    <col class="col-name">
                    <col class="col-course">
                    <col class="col-contact">
                    <col class="col-concern">
                    <col class="col-remarks">
                </colgroup>
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th class="col-name">Name</th>
                        <th class="col-course">Course &amp; Year</th>
                        <th class="col-contact">Contact No.</th>
                        <th class="col-concern">Concern</th>
                        <th class="col-remarks">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logforms as $index => $log)
                        <tr>
                            <td class="col-no">{{ $index + 1 }}</td>
                            <td>{{ $log->appointment?->full_name ?? '—' }}</td>
                            <td>{{ $log->appointment?->course_and_year ?? '—' }}</td>
                            <td>{{ $log->appointment?->contact_no ?? '—' }}</td>
                            <td>{{ $log->concern ?? '—' }}</td>
                            <td>{{ $log->remarks ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:18px; color:#888;">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="summary">
                Showing {{ $logforms->count() }} record(s)
            </div>

        </div>{{-- /.content-box --}}

        <div class="footer">
            <div class="sig-block">
                <div class="sig-line">Guidance Counselor</div>
                <div class="sig-title">Signature over Printed Name</div>
            </div>

            <div class="printed-meta">
                Generated by the Guidance Management System<br>
                {{ now()->format('F d, Y') }}
            </div>

            <div class="sig-block">
                <div class="sig-line">Noted by</div>
                <div class="sig-title">Signature over Printed Name</div>
            </div>
        </div>

    </div>

</body>
</html>