
{{-- resources/views/attendance/preview.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 22px;
        }

        .heading-content {
            margin: 15px 0 20px 0;
            padding: 12px;
            background: #f9f9f9;
            border-left: 4px solid #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .signature-cell {
            height: 35px;
        }
    </style>
</head>

<body>
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
</body>

</html>