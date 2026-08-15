<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Grantees Report</title>
    <style>
        *, *::before, *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
            box-sizing: border-box;
            margin: 0; padding: 0;
        }

        @page { size: 420mm 297mm landscape; margin: 8mm 7mm; }

        html, body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.5pt;
            color: #000;
            background: #fff;
        }

        @media screen {
            body { background: #e5e7eb; padding: 20px 0; }
            .page {
                width: 420mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
                padding: 8mm 7mm;
                box-shadow: 0 6px 32px rgba(0,0,0,.2);
            }
        }

        @media print {
            .no-print { display: none !important; }
            .page { padding: 0; width: 100%; }
            .report-block { page-break-inside: avoid; }
        }

        .no-print {
            text-align: center; padding: 10px;
            background: #f3f4f6; margin-bottom: 16px;
        }
        .no-print button {
            background: #16a34a; color: #fff; border: none;
            padding: 8px 22px; font-size: 13px; border-radius: 6px;
            cursor: pointer; margin-right: 8px;
        }
        .no-print button:hover { background: #15803d; }
        .no-print a { color: #6b7280; font-size: 12px; text-decoration: none; }

        /* ─────────────────────────────────────────
           HEADER TABLE
           +--------+-----------------------------+
           |        | School Name + Office  (r1)  |
           |  Logo  +-----------------------------|
           |        | SY + Generated meta   (r2)  |
           +--------+-----------------------------+
        ───────────────────────────────────────── */
        .doc-header {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 12px;
        }

        .doc-header td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .doc-header .logo-cell {
            width: 70px;
            text-align: center;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .doc-header .logo-cell img {
            width: 54px;
            height: 54px;
            object-fit: contain;
        }

        .doc-header .name-cell {
            padding: 6px 12px 5px;
            text-align: center;
            border-bottom: 1px solid #000;
            vertical-align: middle;
        }

        .doc-header .name-cell .school-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
        }

        .doc-header .name-cell .office-name {
            font-size: 9pt;
            font-style: italic;
            color: #444;
            display: block;
            margin-top: 2px;
        }

        .doc-header .title-cell {
            padding: 6px 12px;
            text-align: center;
            vertical-align: middle;
        }

        .doc-header .title-cell .sy-label {
            font-size: 9.5pt;
            font-weight: bold;
            display: block;
        }

        .doc-header .title-cell .doc-meta {
            font-size: 7.5pt;
            color: #444;
            display: block;
            margin-top: 2px;
        }

        /* ── Report blocks ── */
        .report-block { margin-bottom: 10px; }

        /* ── Data table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7.5pt;
        }
        th, td {
            border: 1px solid #000;
            text-align: center;
            padding: 3px 2px;
            vertical-align: middle;
            word-wrap: break-word;
            background: #fff;
            color: #000;
        }

        .row-title th {
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: .5px;
            padding: 5px;
            border-bottom: 2px solid #000;
        }
        .col-cat   { width: 46px; font-weight: bold; }
        .th-term   { font-weight: bold; font-size: 8pt; border-bottom: 1px solid #000; }
        .th-sub    { font-weight: bold; font-size: 7.5pt; }
        .th-mf     { font-weight: bold; font-size: 7pt; }
        .th-total  { font-weight: bold; font-size: 7.5pt; }

        .row-total td { font-weight: bold; font-size: 8pt; border-top: 1.5px solid #000; }

        .sem-divider { border-left: 2px solid #000 !important; }

        /* ── Footer ── */
        .footer {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .sig-block { text-align: center; width: 45mm; }
        .sig-block .sig-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 3px;
            font-weight: bold;
            font-size: 8.5pt;
        }
        .sig-block .sig-title { font-size: 7.5pt; color: #444; }
        .printed-meta { font-size: 7pt; color: #555; text-align: center; line-height: 1.5; }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨️ Print / Save as PDF</button>
    <a href="javascript:history.back()">← Go Back</a>
</div>

<div class="page">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Header: logo | school name+office / sy+meta               --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <table class="doc-header">
        <tr>
            <td class="logo-cell" rowspan="2">
                <img src="{{ asset('images/logo.png') }}" alt="GVCF Logo">
            </td>
            <td class="name-cell">
                <span class="school-name">Green Valley College Foundation, Inc.</span>
                <span class="office-name">Scholarship Office</span>
            </td>
        </tr>
        <tr>
            <td class="title-cell">
                <span class="sy-label">School Year: {{ $schoolYear ?? '—' }}</span>
                <span class="doc-meta">Generated: {{ now()->format('F d, Y h:i A') }}</span>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  3 separate report tables, one per category                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @foreach ($categories as $cat)
        @php
            $t1 = $rows[$cat]['term1'];
            $t2 = $rows[$cat]['term2'];
        @endphp

        <div class="report-block">
            <table>
                <colgroup>
                    <col style="width:46px">
                    @for ($i = 0; $i <= 10; $i++)<col>@endfor
                    @for ($i = 0; $i <= 10; $i++)<col>@endfor
                </colgroup>

                <thead>
                    <tr class="row-title">
                        <th colspan="23">
                            TOTAL {{ $cat }} GRANTEES REPORT &mdash; S.Y. {{ $schoolYear ?? '—' }}
                        </th>
                    </tr>

                    <tr>
                        <th rowspan="3" class="col-cat">{{ $cat }}<br>CAT.</th>

                        <th colspan="11" class="th-term">
                            1ST SEMESTER
                            @if ($term1)
                                ({{ $term1->semester }})
                            @endif
                        </th>

                        <th colspan="11" class="th-term sem-divider">
                            2ND SEMESTER
                            @if ($term2)
                                ({{ $term2->semester }})
                            @endif
                        </th>
                    </tr>

                    <tr>
                        @foreach ($subGroups as $g)
                            <th colspan="2" class="th-sub">{{ $g }}</th>
                        @endforeach
                        <th class="th-total" rowspan="2">TOTAL</th>

                        @foreach ($subGroups as $idx => $g)
                            <th colspan="2" class="th-sub {{ $idx === 0 ? 'sem-divider' : '' }}">{{ $g }}</th>
                        @endforeach
                        <th class="th-total" rowspan="2">TOTAL</th>
                    </tr>

                    <tr>
                        @for ($i = 0; $i < 5; $i++)
                            <th class="th-mf">MALE</th>
                            <th class="th-mf">FEMALE</th>
                        @endfor

                        @for ($i = 0; $i < 5; $i++)
                            <th class="th-mf {{ $i === 0 ? 'sem-divider' : '' }}">MALE</th>
                            <th class="th-mf">FEMALE</th>
                        @endfor
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="col-cat">{{ $cat }}</td>

                        <td>{{ $t1['total_male'] ?: '' }}</td>
                        <td>{{ $t1['total_female'] ?: '' }}</td>
                        <td>{{ $t1['pwd_male'] ?: '' }}</td>
                        <td>{{ $t1['pwd_female'] ?: '' }}</td>
                        <td>{{ $t1['ip_male'] ?: '' }}</td>
                        <td>{{ $t1['ip_female'] ?: '' }}</td>
                        <td>{{ $t1['none_board_male'] ?: '' }}</td>
                        <td>{{ $t1['none_board_female'] ?: '' }}</td>
                        <td>{{ $t1['with_board_male'] ?: '' }}</td>
                        <td>{{ $t1['with_board_female'] ?: '' }}</td>
                        <td><strong>{{ $t1['total'] ?: '' }}</strong></td>

                        <td class="sem-divider">{{ $t2['total_male'] ?: '' }}</td>
                        <td>{{ $t2['total_female'] ?: '' }}</td>
                        <td>{{ $t2['pwd_male'] ?: '' }}</td>
                        <td>{{ $t2['pwd_female'] ?: '' }}</td>
                        <td>{{ $t2['ip_male'] ?: '' }}</td>
                        <td>{{ $t2['ip_female'] ?: '' }}</td>
                        <td>{{ $t2['none_board_male'] ?: '' }}</td>
                        <td>{{ $t2['none_board_female'] ?: '' }}</td>
                        <td>{{ $t2['with_board_male'] ?: '' }}</td>
                        <td>{{ $t2['with_board_female'] ?: '' }}</td>
                        <td><strong>{{ $t2['total'] ?: '' }}</strong></td>
                    </tr>

                    <tr class="row-total">
                        <td>TOTAL</td>

                        <td colspan="2">{{ ($t1['total_male'] + $t1['total_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t1['pwd_male'] + $t1['pwd_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t1['ip_male'] + $t1['ip_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t1['none_board_male'] + $t1['none_board_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t1['with_board_male'] + $t1['with_board_female']) ?: '' }}</td>
                        <td>{{ $t1['total'] }}</td>

                        <td colspan="2" class="sem-divider">{{ ($t2['total_male'] + $t2['total_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t2['pwd_male'] + $t2['pwd_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t2['ip_male'] + $t2['ip_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t2['none_board_male'] + $t2['none_board_female']) ?: '' }}</td>
                        <td colspan="2">{{ ($t2['with_board_male'] + $t2['with_board_female']) ?: '' }}</td>
                        <td>{{ $t2['total'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Footer --}}
    <div class="footer">
        <div class="sig-block">
            <div class="sig-line">Prepared by</div>
            <div class="sig-title">Scholarship Officer</div>
        </div>
        <div class="printed-meta">
            Generated by the Scholarship Management System<br>
            {{ now()->format('F d, Y h:i A') }}
        </div>
        <div class="sig-block">
            <div class="sig-line">Noted by</div>
            <div class="sig-title">Signature over Printed Name</div>
        </div>
    </div>

</div>
</body>
</html>