<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Institutional Scholars</title>
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

        .title-cell {
            text-align: center;
            vertical-align: middle;
            padding: 6px 12px;
        }

        /* KEY FIX: reduced font size + nowrap so school name stays on one line */
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
            font-size: 12.5pt;
            font-weight: bold;
            margin-top: 7px;
            text-decoration: underline;
        }

        .doc-subtitle {
            font-size: 11pt;
            margin-top: 5px;
            font-style: italic;
        }

        .doc-subtitle sup {
            font-size: 7pt;
        }

        .doc-info-cell {
            width: 205px;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .doc-info-row {
            display: block;
            padding: 5px 8px;
            border-bottom: 1px solid #000;
        }

        .doc-info-row:last-child {
            border-bottom: none;
        }

        /* ── SCHOLARS TABLE ── */
        .scholars-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 9pt;
        }

        .scholars-table th {
            border: 1.5px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
            background-color: #fff;
            vertical-align: bottom;
            white-space: nowrap;
        }

        .scholars-table td {
            border: 1px solid #000;
            padding: 2px 6px;
            vertical-align: middle;
            line-height: 1.15;
            height: 18px;
        }

        .scholars-table th.seq-col,
        .scholars-table td.seq-col {
            width: 36px;
            text-align: center;
        }

        .scholars-table th.name-col   { width: 30%; }
        .scholars-table th.course-col { width: 100px; }
        .scholars-table th.schol-col  { width: 22%; }
        .scholars-table th.disc-col   { width: auto; }

        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
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

    @php
    $activeTerm = \App\Models\Term::where('is_active', true)->first();
    $semLabel   = $activeTerm?->semester ?? '—';
    $ayLabel    = $activeTerm?->school_year ?? '—';

    $semOrdinal = '';
    if (preg_match('/(\d+)/u', $semLabel, $m)) {
        $n = (int) $m[1];
        $semOrdinal = match(true) {
            $n === 1 => '1<sup>ST</sup>',
            $n === 2 => '2<sup>ND</sup>',
            $n === 3 => '3<sup>RD</sup>',
            default  => $n . '<sup>TH</sup>',
        };
    }
@endphp
    <!-- OFFICIAL HEADER -->
    <table class="doc-header">
  <tr>
    <td class="logo-cell" rowspan="2">
      <img src="{{ asset('images/logo.png') }}" alt="School Logo" onerror="this.style.display='none'">
      <div class="iso">ISO 21001:2018</div>
    </td>
    <td style="text-align:center; vertical-align:middle; padding:5px 12px; border-right:1.5px solid #000; border-bottom:1px solid #000;">
      <div class="school-name">GREEN VALLEY COLLEGE FOUNDATION, INC.</div>
      <div class="school-address">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</div>
    </td>
    <td style="font-size:8.5pt; padding:5px 8px; vertical-align:middle; border-bottom:1px solid #000;">
      Document Code: <strong>FM-GUI-016</strong>
    </td>
  </tr>
  <tr>
    <td style="text-align:center; vertical-align:middle; padding:8px 12px; border-right:1.5px solid #000;">
      <div class="doc-title">LIST OF INSTITUTIONAL SCHOLARS</div>
      <div class="doc-subtitle">{!! $semOrdinal !!} Semester &nbsp; A.Y. {{ $ayLabel }}</div>
    </td>
    <td style="font-size:8.5pt; padding:0; vertical-align:top;">
      <span style="display:block; padding:5px 8px; border-bottom:1px solid #000;">Revision No. <strong>01</strong></span>
      <span style="display:block; padding:5px 8px; border-bottom:1px solid #000;">Effectivity Date: <strong>July 10, 2023</strong></span>
      <span style="display:block; padding:5px 8px;">Page No. &nbsp; <strong>1 of {{ max(1, ceil($scholars->count() / 30)) }}</strong></span>
    </td>
  </tr>
</table>

    <!-- SCHOLARS TABLE -->
    <div class="content-area">
    <table class="scholars-table">
        <thead>
            <tr>
                <th class="seq-col">SEQ</th>
                <th class="name-col">FULL NAME</th>
                <th class="course-col">Course &amp; Year</th>
                <th class="schol-col">TYPE OF SCHOLARSHIP</th>
                <th class="disc-col">DISCOUNT(S)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($scholars as $index => $scholar)
            @php
                $lastName  = strtoupper(trim($scholar->last_name));
                $firstName = strtoupper(trim($scholar->first_name));
                $ext       = $scholar->extension_name ? ', ' . strtoupper(trim($scholar->extension_name)) : '';
                $mi        = $scholar->middle_name
                                ? ' ' . strtoupper(mb_substr(trim($scholar->middle_name), 0, 1)) . '.'
                                : '';
                $fullName  = $lastName . $ext . ', ' . $firstName . $mi;

                $rawProgram = trim($scholar->program);
                $programKey = preg_replace('/\s+/', ' ', strtolower($rawProgram));

                $programMap = [
                    'bachelor of elementary education'                                                             => 'BEEd',
                    'bachelor of early childhood education'                                                        => 'BECEd',
                    'bachelor of secondary education major in english'                                             => 'BSEd-Eng',
                    'bachelor of secondary education major in mathematics'                                         => 'BSEd-Math',
                    'bachelor of technology and livelihood education major in home economics'                      => 'BTLEd-HE',
                    'bachelor of technology and livelihood education major in industrial arts'                     => 'BTLEd-IA',
                    'bachelor of technology and livelihood education major in information communication technology' => 'BTLEd-ICT',
                    'btled-he'  => 'BTLEd-HE',
                    'btled-ia'  => 'BTLEd-IA',
                    'btled-ict' => 'BTLEd-ICT',
                    'bachelor of science in mechanical engineering'                                                => 'BSME',
                    'bachelor of science in information technology'                                                => 'BSIT',
                    'associate in computer technology'                                                             => 'ACT',
                    'bachelor of science in criminology'                                                           => 'BSCrim',
                    'bachelor of science in industrial security management'                                        => 'BSISM',
                    'bachelor of science in business administration major in financial management'                  => 'BSBA-FM',
                    'bachelor of science in business administration major in marketing management'                  => 'BSBA-MM',
                    'bachelor of science in business administration major in operations management'                 => 'BSBA-OM',
                    'bachelor of science in tourism management'                                                    => 'BSTM',
                    'bachelor of science in hospitality management'                                                => 'BSHM',
                ];

                if (isset($programMap[$programKey])) {
                    $courseAbbr = $programMap[$programKey];
                } elseif (strlen($rawProgram) <= 8 && !str_contains($rawProgram, ' ')) {
                    $courseAbbr = strtoupper($rawProgram);
                } else {
                    $stopWords = ['OF','IN','AND','THE','FOR','A','AN','WITH','TO','AT','BY'];
                    $words     = preg_split('/\s+/', strtoupper($rawProgram));
                    $acronym   = '';
                    foreach ($words as $word) {
                        $word = trim($word);
                        if ($word !== '' && !in_array($word, $stopWords)) {
                            $acronym .= $word[0];
                        }
                    }
                    $courseAbbr = $acronym ?: strtoupper($rawProgram);
                }

                $yearLevel = (string) $scholar->year_level;
            @endphp
            <tr>
                <td class="seq-col">{{ $index + 1 }}</td>
                <td>{{ $fullName }}</td>
                <td>{{ $courseAbbr }} {{ $yearLevel }}</td>
                <td>{{ strtoupper($scholar->type_of_scholarship) }}</td>
                <td>{{ $scholar->benefit ? number_format((float)$scholar->benefit, 0) . '% IN TUITION FEE' : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:14px; color:#888;">
                    No institutional scholars found.
                </td>
            </tr>
            @endforelse
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