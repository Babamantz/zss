<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll List</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 2px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f4f4f4; font-size: 10px; text-transform: uppercase; }
        td { font-size: 11px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background: #f9f9f9; }
        .footer-note { margin-top: 20px; font-size: 9px; color: #777; text-align: center; }
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
                <th style="width:20%;">Account No.</th>
                <th style="width:20%;" class="text-end">Net Pay (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['opf_number'] }}</td>
                    <td>{{ $row['account_no'] }}</td>
                    <td class="text-end">{{ number_format($row['net_pay'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No payroll entries found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end">Total</td>
                <td class="text-end">{{ number_format($totalNet, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="footer-note">
        This is a computer-generated payroll list and does not require a signature.
    </p>

</body>
</html>