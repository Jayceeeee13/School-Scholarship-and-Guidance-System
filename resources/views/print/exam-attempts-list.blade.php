<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examinees List</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }

        * { box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 16px;
        }

        .letterhead {
            width: 100%;
            border: 1px solid #9ca3af;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .letterhead td { border: 0; padding: 0; }
        .letterhead .logo-cell {
            width: 110px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid #9ca3af;
            padding: 6px;
        }
        .letterhead .logo-cell img { width: 78px; height: auto; display: block; margin: 0 auto; }
        .letterhead .school {
            text-align: center;
            padding: 8px 10px 6px;
            border-bottom: 1px solid #9ca3af;
        }
        .letterhead .school .name { font-size: 15px; font-weight: 700; color: #4b5563; letter-spacing: .2px; }
        .letterhead .school .address { font-size: 8.5px; font-style: italic; color: #6b7280; margin-top: 1px; }
        .letterhead .title-row { text-align: center; padding: 8px 10px; }
        .letterhead .title-row .title { font-size: 12px; font-weight: 700; color: #4b5563; }
        .letterhead .title-row .sub { font-size: 11px; color: #4b5563; margin-top: 3px; }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: #374151;
        }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
        }

        thead th {
            background: #047857;
            color: #fff;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        tbody tr:nth-child(even) td { background: #f9fafb; }

        .center { text-align: center; }
        .passed { color: #047857; font-weight: 700; }
        .failed { color: #dc2626; font-weight: 700; }

        .actions { text-align: right; margin-bottom: 10px; }
        .actions button {
            background: #059669; color: #fff; border: 0; border-radius: 6px;
            padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer;
        }

        @media print { .actions { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <table class="letterhead">
        <tr>
            <td class="logo-cell" rowspan="2">
                {{-- Put your logo at public/images/gvc-logo.png (or change this path) --}}
                <img src="{{ asset('images/logo.png') }}" alt="GVCFI Logo">
            </td>
            <td class="school">
                <div class="name">GREEN VALLEY COLLEGE FOUNDATION, INC.</div>
                <div class="address">Km. 2, Bo. 2, Gensan Dr., Koronadal City, South Cotabato</div>
            </td>
        </tr>
        <tr>
            <td class="title-row">
                <div class="title">EXAMINEES LIST</div>
                <div class="sub">Admission and Scholarship Test Results</div>
            </td>
        </tr>
    </table>

    <div class="meta">
        <span>Total: <strong>{{ $attempts->count() }}</strong> &nbsp;|&nbsp; Passed: <strong>{{ $passedCount }}</strong> &nbsp;|&nbsp; Failed: <strong>{{ $attempts->count() - $passedCount }}</strong></span>
        <span>Printed {{ $printedAt->format('M d, Y h:i A') }} by {{ $printedBy }}</span>
    </div>

    @php
        $show = fn (string $key): bool => in_array($key, $columns, true);
        $colCount = 1 + count(array_intersect($columns, [
            'user.name', 'user.email', 'exam.title', 'score', 'percentage',
            'status', 'scholarship_discount', 'violation_count', 'completed_at',
        ]));
    @endphp

    <table>
        <thead>
            <tr>
                <th class="center" style="width:28px">#</th>
                @if ($show('user.name'))            <th>Name</th> @endif
                @if ($show('user.email'))           <th>Email</th> @endif
                @if ($show('exam.title'))           <th>Exam</th> @endif
                @if ($show('score'))                <th class="center">Score</th> @endif
                @if ($show('percentage'))           <th class="center">%</th> @endif
                @if ($show('status'))               <th class="center">Result</th> @endif
                @if ($show('scholarship_discount')) <th>Scholarship</th> @endif
                @if ($show('violation_count'))      <th class="center">Violations</th> @endif
                @if ($show('completed_at'))         <th>Completed</th> @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($attempts as $attempt)
                @php $passed = $attempt->percentage >= 75; @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    @if ($show('user.name'))            <td>{{ $attempt->user?->name ?? '—' }}</td> @endif
                    @if ($show('user.email'))           <td>{{ $attempt->user?->email ?? '—' }}</td> @endif
                    @if ($show('exam.title'))           <td>{{ $attempt->exam?->title ?? '—' }}</td> @endif
                    @if ($show('score'))                <td class="center">{{ $attempt->score }} / {{ $attempt->total_points }}</td> @endif
                    @if ($show('percentage'))           <td class="center">{{ $attempt->percentage }}%</td> @endif
                    @if ($show('status'))               <td class="center {{ $passed ? 'passed' : 'failed' }}">{{ $passed ? 'Passed' : 'Failed' }}</td> @endif
                    @if ($show('scholarship_discount')) <td>{{ $attempt->print_scholarship }}</td> @endif
                    @if ($show('violation_count'))      <td class="center">{{ $attempt->print_violations === 0 ? 'Clean' : $attempt->print_violations }}</td> @endif
                    @if ($show('completed_at'))         <td>{{ $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('M d, Y h:i A') : '—' }}</td> @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $colCount }}" class="center" style="padding:20px;color:#6b7280">No examinee records to print.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 300));</script>
</body>
</html>