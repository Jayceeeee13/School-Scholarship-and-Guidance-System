<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Counseling Logforms</title>
    <style>
        @page {
            size: legal landscape;
            margin: 0.4in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            font-size: 13px;
        }

        .print-btn-wrap {
            text-align: center;
            padding: 20px;
            background: #f3f4f6;
        }

        .print-btn {
            background: #059669;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .back-link {
            margin-left: 12px;
            color: #059669;
            text-decoration: none;
            font-size: 14px;
        }

        .sheet {
            width: 100%;
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 12px;
            vertical-align: top;
        }

        th {
            background-color: #065f46;
            color: white;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #ecfdf5;
        }

        @media print {
            .print-btn-wrap {
                display: none;
            }
            .sheet {
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-wrap">
        <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
        <a href="javascript:history.back()" class="back-link">← Go Back</a>
    </div>

    <div class="sheet">
        <div style="display: flex; align-items: center; border: 2px solid #000; padding: 10px;">
            <div style="flex: 0 0 90px; text-align: center;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
            </div>
            <div style="flex: 1; text-align: center; padding: 10px;">
                <h1 style="font-size: 20px; margin: 0; letter-spacing: 1px;">GREEN VALLEY COLLEGE FOUNDATION, INC.</h1>
                <p style="font-style: italic; font-size: 13px; margin-top: 4px;">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</p>
            </div>
        </div>

        <div style="border: 2px solid #000; border-top: none; padding: 10px; text-align: center;">
            <h2 style="font-size: 16px; margin: 0; letter-spacing: 1px;">LOG FOR COUNSELING SERVICES</h2>
            <p style="font-size: 15px; margin-top: 2px;">{{ $semesterLabel }} Semester, A.Y. {{ $schoolYearLabel }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Name</th>
                    <th>Course & Year</th>
                    <th>Contact No.</th>
                    <th>Concern</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logforms as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->name }}</td>
                    <td>{{ $log->course_and_year }}</td>
                    <td>{{ $log->contact_no }}</td>
                    <td>{{ $log->concern }}</td>
                    <td>{{ $log->remarks }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No logform records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>