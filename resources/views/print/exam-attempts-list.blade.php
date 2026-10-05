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

        .header { text-align: center; margin-bottom: 14px; }
        .header h1 { font-size: 16px; margin: 0 0 2px; color: #047857; }
        .header h2 { font-size: 13px; margin: 0 0 4px; }
        .header p  { margin: 0; color: #6b7280; }

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

    <div class="header">
        <h1>Green Valley College Foundation Inc.</h1>
        <h2>Examinees List</h2>
        <p>Admission and Scholarship Test Results</p>
    </div>

    <div class="meta">
        <span>Total: <strong>{{ $attempts->count() }}</strong> &nbsp;|&nbsp; Passed: <strong>{{ $passedCount }}</strong> &nbsp;|&nbsp; Failed: <strong>{{ $attempts->count() - $passedCount }}</strong></span>
        <span>Printed {{ $printedAt->format('M d, Y h:i A') }} by {{ $printedBy }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="center" style="width:28px">#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Exam</th>
                <th class="center">Score</th>
                <th class="center">%</th>
                <th class="center">Result</th>
                <th>Scholarship</th>
                <th class="center">Violations</th>
                <th>Completed</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attempts as $attempt)
                @php $passed = $attempt->percentage >= 75; @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $attempt->user?->name ?? '—' }}</td>
                    <td>{{ $attempt->user?->email ?? '—' }}</td>
                    <td>{{ $attempt->exam?->title ?? '—' }}</td>
                    <td class="center">{{ $attempt->score }} / {{ $attempt->total_points }}</td>
                    <td class="center">{{ $attempt->percentage }}%</td>
                    <td class="center {{ $passed ? 'passed' : 'failed' }}">{{ $passed ? 'Passed' : 'Failed' }}</td>
                    <td>{{ $attempt->print_scholarship }}</td>
                    <td class="center">{{ $attempt->print_violations === 0 ? 'Clean' : $attempt->print_violations }}</td>
                    <td>{{ $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('M d, Y h:i A') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center" style="padding:20px;color:#6b7280">No examinee records to print.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 300));</script>
</body>
</html>