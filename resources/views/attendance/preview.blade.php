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

        .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-container img {
            max-height: 60px;
            width: auto;
            display: inline-block;
        }

        .header {
            text-align: center;
            margin-bottom: 0;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header .meeting-date {
            margin: 4px 0 0 0;
            font-size: 13px;
            font-weight: bold;
        }

        .heading-content {
            margin: 0 0 20px 0;
            padding: 12px;
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

    {{-- Logo Section --}}
    <div class="logo-container">
        <img src="{{ public_path('assets/img/zhc_logo.png') }}" alt="zhc_logo">
    </div>

    {{-- Organization Header --}}
    <div class="header">

        @if (!$isSwahili)
            <h1>ZANZIBAR HOUSING CORPORATION</h1>
        @else
            <h1>SHIRIKA LA NYUMBA ZANZIBAR</h1>
        @endif

        @if ($meeting_date)
            <p class="meeting-date">
                {{ $isSwahili ? 'Tarehe' : 'Date' }}: {{ $meeting_date }}
            </p>
        @endif

    </div>

    {{-- Heading --}}
    <div class="heading-content">
        {!! $heading !!}
    </div>

    {{-- Attendance Table --}}
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

                    @foreach ($columns as $index => $column)

                        @if ($index === 0)

                            <td style="width: 5%; text-align: center;">
                                {{ $i }}
                            </td>

                        @elseif ($index === count($columns) - 1)

                            <td style="width: {{ 95 / max(count($columns) - 1, 1) }}%;" class="signature-cell"></td>

                        @else

                            <td style="width: {{ 95 / max(count($columns) - 1, 1) }}%;"></td>

                        @endif

                    @endforeach

                </tr>

            @endfor

        </tbody>

    </table>

</body>

</html>