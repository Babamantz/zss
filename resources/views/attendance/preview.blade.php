{{-- resources/views/attendance/preview.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .heading-content {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .signature-cell {
            height: 40px;
        }

        .print-btn {
            margin: 20px 0;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .lang-selector {
            margin: 10px 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">Print Form</button>

        @if (count($attendance->languages) > 1)
            <div class="lang-selector">
                <strong>Language:</strong>
                @foreach ($attendance->languages as $availableLang)
                    <a href="{{ route('attendance.preview', ['id' => $attendance->id, 'lang' => $availableLang]) }}"
                        style="margin: 0 10px; {{ $lang === $availableLang ? 'font-weight: bold; text-decoration: underline;' : '' }}">
                        {{ strtoupper($availableLang) }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    <div class="heading-content">
        {!! $heading !!}
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= $rows; $i++)
                <tr>
                    <td style="width: 5%; text-align: center;">{{ $i }}</td>
                    <td style="width: 25%;"></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 25%;"></td>
                    <td style="width: 25%;" class="signature-cell"></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <script>
        // Auto-adjust table for printing
        window.onbeforeprint = function() {
            document.body.style.margin = '10mm';
        };
    </script>
</body>

</html>
