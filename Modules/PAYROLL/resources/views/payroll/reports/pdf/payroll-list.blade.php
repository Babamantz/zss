<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payroll List</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 2px 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f4f4f4;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            font-size: 11px;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        tfoot td {
            font-weight: bold;
            background: #f9f9f9;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 9px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Payroll List</h2>
        <p>
            Pay Period: {{ $period->start_date->format('d M Y') }}
            &ndash;
            {{ $period->end_date->format('d M Y') }}
        </p>
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:35%;">Employee Name</th>
                <th style="width:20%;">Payroll No.</th>
                <th style="width:20%;">Bank</th>
                <th style="width:20%;">Account No.</th>
                <th style="width:20%;" class="text-end">Net Pay (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @php $globalIndex = 1; @endphp

            @forelse ($entries as $bankSlug => $bankGroup)
                <!-- Bank Header Row -->
                <tr class="bg-light fw-bold">
                    <td colspan="6" style="background-color: #f8f9fa; padding: 8px; font-weight: bold;">
                        Bank: {{ strtoupper(str_replace('-', ' ', $bankSlug)) }}
                        <span style="font-weight: normal; font-size: 0.9em; color: #6c757d;">
                            ({{ $bankGroup->count() }} {{ Str::plural('entry', $bankGroup->count()) }})
                        </span>
                    </td>
                </tr>

                <!-- Employee Rows for this specific Bank -->
                @foreach ($bankGroup as $row)
                    <tr>
                        <td class="text-center">{{ $globalIndex++ }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['opf_number'] }}</td>
                        <td>{{ $row['bank'] }}</td>
                        <td>{{ $row['account_no'] }}</td>
                        <td class="text-end">{{ number_format($row['net_pay'], 2) }}</td>
                    </tr>
                @endforeach

                <!-- Optional: Subtotal for this Bank -->
                <tr class="fw-semibold" style="border-bottom: 2px solid #dee2e6;">
                    <td colspan="5" class="text-end"><strong>Subtotal ({{ strtoupper($bankSlug) }}):</strong></td>
                    <td class="text-end"><strong>{{ number_format(collect($bankGroup)->sum('net_pay'), 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No payroll entries found.</td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr style="font-weight: bold; background-color: #e9ecef; border-top: 2px solid #000;">
                <td colspan="5" class="text-end">Grand Total</td>
                <td class="text-end">
                    {{ number_format($entries->flatten(1)->sum('net_pay'), 2) }}
                </td>
            </tr>
        </tfoot>





    </table>

    <p class="footer-note">
        This is a computer-generated payroll list and does not require a signature.
    </p>

</body>

</html>