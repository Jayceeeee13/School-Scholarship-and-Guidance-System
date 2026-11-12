<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accomplishment Report</title>
    <style>
        @page { margin: 36px 40px; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            margin: 0;
        }

        table { border-collapse: collapse; width: 100%; }

        /* ── Form header (matches the official form) ── */
        .hdr td { border: 1px solid #9a9a9a; vertical-align: middle; color: #6b6b6b; }
        .hdr .logo-cell { width: 17%; text-align: center; padding: 8px 4px 4px; }
        .hdr .logo-cell img { width: 80px; height: 80px; }
        .hdr .iso { font-size: 9px; font-weight: bold; margin-top: 8px; }
        .hdr .org-cell { text-align: center; padding: 6px 8px; }
        .hdr .org { font-size: 13px; font-weight: bold; letter-spacing: .2px; }
        .hdr .addr { font-size: 8.5px; margin-top: 2px; }
        .hdr .title-cell { text-align: center; padding: 10px 12px; }
        .hdr .title { font-size: 14px; font-weight: bold; line-height: 1.35; }
        .hdr .term { font-size: 13px; margin-top: 18px; }
        .hdr .meta { width: 29%; padding: 5px 8px; font-size: 10px; }
        .hdr .meta b { color: #333; }
        .pagenum:before { content: counter(page); }

        /* ── Scholar info lines ── */
        .info { margin: 16px 0 14px 34px; width: 80%; }
        .info td { padding: 2px 0; font-size: 12px; vertical-align: bottom; }
        .info .label { width: 105px; white-space: nowrap; }
        .info .colon { width: 10px; }
        .info .value { border-bottom: 1px solid #111; padding-left: 4px; }

        /* ── Activities table ── */
        .acts th, .acts td { border: 1px solid #111; padding: 6px 8px; vertical-align: top; }
        .acts th { font-size: 13px; text-align: center; padding: 14px 8px 8px; }
        .acts td { font-size: 11px; height: 38px; }
        .acts .seq { text-align: left; width: 8%; }
    </style>
</head>

@php
    // Embed the logo so DomPDF never has to fetch it over HTTP.
    $logoFile = public_path('images/logo.png');
    $logoSrc  = is_file($logoFile) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile)) : null;

    $activities = $report->activities->sortBy('activity_date')->values();
    $minRows    = 5;
    $rowCount   = max($minRows, $activities->count());
@endphp

<body>

    {{-- ── Header block ── --}}
    <table class="hdr">
        <tr>
            <td class="logo-cell" rowspan="4">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" alt="GVC">
                @endif
                <div class="iso">ISO 21001:2018</div>
            </td>
            <td class="org-cell">
                <div class="org">GREEN VALLEY COLLEGE FOUNDATION, INC.</div>
                <div class="addr">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</div>
            </td>
            <td class="meta">Document Code: <b>FM-GUI-027</b></td>
        </tr>
        <tr>
            <td class="title-cell" rowspan="3">
                <div class="title">ACCOMPLISHMENT REPORT OF INSTITUTIONAL SCHOLARS</div>
                <div class="term">{{ $term?->semester }} A.Y. {{ $term?->school_year }}</div>
            </td>
            <td class="meta">Revision No.01</td>
        </tr>
        <tr>
            <td class="meta">Effectivity Date: July 10, 2023</td>
        </tr>
        <tr>
            <td class="meta">Page No. <span class="pagenum"></span></td>
        </tr>
    </table>

    {{-- ── Scholar details ── --}}
    <table class="info">
        <tr>
            <td class="label">Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $scholar->full_name }}</td>
        </tr>
        <tr>
            <td class="label">Course &amp; Year</td>
            <td class="colon">:</td>
            <td class="value">{{ $scholar->program }}@if ($scholar->year_level) &mdash; Year {{ $scholar->year_level }}@endif</td>
        </tr>
        <tr>
            <td class="label">Scholarship</td>
            <td class="colon">:</td>
            <td class="value">{{ $scholar->type_of_scholarship }}</td>
        </tr>
    </table>

    {{-- ── Activities ── --}}
    <table class="acts">
        <thead>
            <tr>
                <th style="width: 8%;">SEQ.</th>
                <th style="width: 20%;">DATE</th>
                <th style="width: 26%;">VENUE</th>
                <th>ACTIVITY</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $rowCount; $i++)
                @php $a = $activities->get($i); @endphp
                <tr>
                    <td class="seq">{{ $i + 1 }}</td>
                    <td>{{ $a?->activity_date?->format('M d, Y') }}</td>
                    <td>{{ $a?->venue }}</td>
                    <td>{{ $a?->activity }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

</body>
</html>