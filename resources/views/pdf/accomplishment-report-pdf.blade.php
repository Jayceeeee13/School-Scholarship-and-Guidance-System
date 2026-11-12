<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accomplishment Report</title>
    <style>
        @page { margin: 36px 40px; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1a1a1a;
            margin: 0;
        }

        table { border-collapse: collapse; width: 100%; }

        /* ── Form header (same layout as the Guidance log form) ── */
        .hdr td { border: 1px solid #bdbdbd; color: #555; }
        .hdr .logo-cell { width: 13%; text-align: center; vertical-align: middle; padding: 8px; }
        .hdr .logo-cell img { width: 62px; height: 62px; }
        .hdr .org-cell { text-align: center; padding: 6px 8px 5px; }
        .hdr .org { font-size: 13px; font-weight: bold; letter-spacing: .3px; }
        .hdr .addr { font-size: 7.5px; margin-top: 1px; }
        .hdr .title-cell { text-align: center; padding: 7px 8px 8px; }
        .hdr .title { font-size: 11px; font-weight: bold; }
        .hdr .term { font-size: 10px; margin-top: 4px; }

        /* ── Scholar info lines ── */
        .info { margin: 16px 0 14px 28px; width: 82%; }
        .info td { padding: 2px 0; font-size: 11px; vertical-align: bottom; }
        .info .label { width: 92px; white-space: nowrap; }
        .info .colon { width: 10px; }
        .info .value { border-bottom: 1px solid #111; padding-left: 4px; }

        /* ── Activities table ── */
        .acts th, .acts td { border: 1px solid #111; padding: 6px 8px; vertical-align: top; }
        .acts th { font-size: 11px; text-align: center; padding: 10px 7px 7px; }
        .acts tbody td { font-size: 16px; line-height: 1.3; height: 44px; }
        .acts .seq { text-align: left; }
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
            <td class="logo-cell" rowspan="2">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" alt="GVC">
                @endif
            </td>
            <td class="org-cell">
                <div class="org">GREEN VALLEY COLLEGE FOUNDATION, INC.</div>
                <div class="addr">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</div>
            </td>
        </tr>
        <tr>
            <td class="title-cell">
                <div class="title">ACCOMPLISHMENT REPORT OF INSTITUTIONAL SCHOLARS</div>
                <div class="term">{{ $term?->semester }} A.Y. {{ $term?->school_year }}</div>
            </td>
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
                    {{-- Inline font-size is a safeguard in case DomPDF ignores the CSS rule --}}
                    <td class="seq" style="font-size: 16px;">{{ $i + 1 }}</td>
                    <td style="font-size: 16px;">{{ $a?->activity_date?->format('M d, Y') }}</td>
                    <td style="font-size: 16px;">{{ $a?->venue }}</td>
                    <td style="font-size: 16px;">{{ $a?->activity }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

</body>
</html>