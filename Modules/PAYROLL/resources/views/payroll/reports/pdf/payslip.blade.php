<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #222;
            padding: 20px;
        }

        /* ── Header ────────────────────────────────────────────── */
        .header {
            background: #1a237e;
            color: white;
            padding: 16px 20px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 0;
        }
        .header h2 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }
        .header .period {
            font-size: 10px;
            color: rgba(255,255,255,0.7);
        }
        .header .period strong {
            color: white;
        }
        .header-right {
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .status-locked    { background: #4caf50; color: white; }
        .status-processing{ background: #ff9800; color: white; }
        .status-default   { background: #9e9e9e; color: white; }

        /* ── Employee section ──────────────────────────────────── */
        .section-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #888;
            margin-bottom: 6px;
        }
        .employee-block {
            background: #f8f9ff;
            border: 1px solid #e0e0e0;
            padding: 12px 16px;
            border-radius: 0 0 6px 6px;
            margin-bottom: 16px;
        }
        .employee-name {
            font-size: 14px;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 2px;
        }
        .employee-email {
            font-size: 9px;
            color: #666;
        }
        .info-grid {
            width: 100%;
        }
        .info-grid td {
            padding: 2px 0;
            font-size: 9px;
            width: 50%;
        }
        .info-label { color: #888; }
        .info-value { font-weight: 600; color: #333; }

        /* ── Two-column earnings/deductions ───────────────────── */
        .split-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .split-table td {
            vertical-align: top;
            width: 50%;
            padding: 0 6px;
        }
        .split-table td:first-child { padding-left: 0; }
        .split-table td:last-child  { padding-right: 0; }

        .panel {
            border-radius: 4px;
            overflow: hidden;
        }
        .panel-header {
            padding: 6px 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .panel-earning  { background: #e8f5e9; }
        .panel-deduction{ background: #ffebee; }
        .panel-header-earning  { background: #2e7d32; color: white; }
        .panel-header-deduction{ background: #c62828; color: white; }

        .line-table {
            width: 100%;
            border-collapse: collapse;
        }
        .line-table th {
            font-size: 8px;
            color: #666;
            padding: 4px 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .line-table th.text-right { text-align: right; }
        .line-table td {
            font-size: 9px;
            padding: 4px 8px;
            border-bottom: 1px solid #f0f0f0;
        }
        .line-table td.text-right { text-align: right; }
        .line-table tfoot td {
            font-weight: 700;
            font-size: 9px;
            border-top: 1.5px solid #ccc;
            padding: 5px 8px;
        }
        .panel-earning   .line-table tfoot td { color: #2e7d32; }
        .panel-deduction .line-table tfoot td { color: #c62828; }

        /* ── Net pay banner ────────────────────────────────────── */
        .net-banner {
            background: #1b5e20;
            color: white;
            text-align: center;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .net-banner .label {
            font-size: 8px;
            color: rgba(255,255,255,0.65);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .net-banner .amount {
            font-size: 22px;
            font-weight: 700;
        }
        .net-banner .sub {
            font-size: 8px;
            color: rgba(255,255,255,0.6);
            margin-top: 3px;
        }

        /* ── Summary row ───────────────────────────────────────── */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            width: 33.33%;
            text-align: center;
            padding: 10px 6px;
            border-radius: 4px;
        }
        .summary-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 3px;
        }
        .summary-value {
            font-size: 12px;
            font-weight: 700;
        }
        .bg-gross      { background: #e3f2fd; }
        .color-gross   { color: #1565c0; }
        .bg-deductions { background: #ffebee; }
        .color-ded     { color: #c62828; }
        .bg-net        { background: #e8f5e9; }
        .color-net     { color: #2e7d32; }

        /* ── Signature section ─────────────────────────────────── */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            padding: 0 10px;
            vertical-align: bottom;
        }
        .sig-line {
            border-top: 1px solid #333;
            margin: 0 auto;
            width: 80%;
            margin-bottom: 4px;
        }
        .sig-label {
            font-size: 8px;
            color: #555;
        }

        /* ── Footer ────────────────────────────────────────────── */
        .doc-footer {
            text-align: center;
            font-size: 8px;
            color: #aaa;
            margin-top: 16px;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .divider { margin: 12px 0; border: none; border-top: 1px solid #eee; }

        /* DomPDF doesn't support flexbox/grid — use tables for layout */
        .layout-table { width: 100%; border-collapse: collapse; }
        .layout-table td { vertical-align: top; padding: 0; }
    </style>
</head>
<body>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <table class="layout-table" style="background:#1a237e;
        border-radius:6px 6px 0 0;padding:14px 18px;
        color:white;margin-bottom:0;">
        <tr>
            <td style="width:60%;padding:14px 18px;">
                <div style="font-size:18px;font-weight:700;letter-spacing:2px;
                    color:white;margin-bottom:2px;">
                    PAYSLIP
                </div>
                <div style="font-size:9px;color:rgba(255,255,255,0.65);">
                    Pay Period:
                    <strong style="color:white;">
                        {{ $entry->payPeriod->start_date->format('d M Y') }}
                        &ndash;
                        {{ $entry->payPeriod->end_date->format('d M Y') }}
                    </strong>
                </div>
                <div style="font-size:9px;color:rgba(255,255,255,0.5);margin-top:2px;">
                    Generated: {{ now()->format('d M Y H:i') }}
                </div>
            </td>
            <td style="width:40%;padding:14px 18px;text-align:right;
                vertical-align:middle;">
                @php
                    $sc = match($entry->payPeriod->status) {
                        'Locked_Completed' => '#4caf50',
                        'Processing'       => '#ff9800',
                        default            => '#9e9e9e',
                    };
                @endphp
                <span style="background:{{ $sc }};color:white;padding:3px 10px;
                    border-radius:4px;font-size:9px;font-weight:700;">
                    {{ str_replace('_', ' ', $entry->payPeriod->status) }}
                </span>
                <div style="font-size:9px;color:rgba(255,255,255,0.55);margin-top:4px;">
                    Processed:
                    {{Carbon\Carbon::parse($entry->processed_at)?->format('d M Y H:i') ?? 'Pending' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Employee Block ───────────────────────────────────────────────────── --}}
    <table class="layout-table"
        style="background:#f8f9ff;border:1px solid #e0e0e0;
               border-top:none;border-radius:0 0 6px 6px;margin-bottom:14px;">
        <tr>
            {{-- Left: Name --}}
            <td style="padding:12px 18px;width:55%;border-right:1px solid #e8e8e8;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;
                    letter-spacing:0.8px;color:#888;margin-bottom:6px;">
                    Employee Details
                </div>
                <div style="font-size:14px;font-weight:700;color:#1a237e;margin-bottom:2px;">
                    {{ $entry->employee->user->first_name }}
                    {{ $entry->employee->user->middle_name }}
                    {{ $entry->employee->user->last_name }}
                </div>
                <div style="font-size:9px;color:#666;">
                    {{ $entry->employee->user->email }}
                </div>
            </td>
            {{-- Right: Employment info --}}
            <td style="padding:12px 18px;width:45%;vertical-align:top;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;
                    letter-spacing:0.8px;color:#888;margin-bottom:6px;">
                    Employment Info
                </div>
                <table style="width:100%;border-collapse:collapse;">
                    @foreach ([
                        ['OPF No.',    $entry->employee->opf_number  ?? '—'],
                        ['Department', $entry->employee->department?->name ?? '—'],
                        ['Unit',       $entry->employee->unit?->name ?? '—'],
                    ] as [$lbl, $val])
                        <tr>
                            <td style="font-size:9px;color:#888;padding:1px 0;width:45%;">
                                {{ $lbl }}
                            </td>
                            <td style="font-size:9px;font-weight:600;color:#333;padding:1px 0;">
                                {{ $val }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- ── Earnings & Deductions ────────────────────────────────────────────── --}}
    <table style="width:100%;border-collapse:collapse;margin-bottom:14px;">
        <tr>

            {{-- Earnings panel --}}
            <td style="width:50%;vertical-align:top;padding-right:6px;">
                <table style="width:100%;border-collapse:collapse;border-radius:4px;
                    overflow:hidden;">
                    <thead>
                        <tr>
                            <td colspan="2"
                                style="background:#2e7d32;color:white;padding:6px 10px;
                                       font-size:9px;font-weight:700;text-transform:uppercase;
                                       letter-spacing:0.5px;">
                                ▲ Earnings
                            </td>
                        </tr>
                        <tr style="background:#e8f5e9;">
                            <th style="font-size:8px;color:#555;padding:4px 8px;
                                text-align:left;font-weight:600;border-bottom:1px solid #ccc;">
                                Component
                            </th>
                            <th style="font-size:8px;color:#555;padding:4px 8px;
                                text-align:right;font-weight:600;border-bottom:1px solid #ccc;">
                                Amount (TZS)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($earnings as $item)
                            <tr style="background:{{ $loop->even ? '#f1f8e9' : '#ffffff' }};">
                                <td style="font-size:9px;padding:4px 8px;
                                    border-bottom:1px solid #f0f0f0;">
                                    {{ $item->component_name_snapshot }}
                                </td>
                                <td style="font-size:9px;padding:4px 8px;text-align:right;
                                    border-bottom:1px solid #f0f0f0;font-weight:600;">
                                    {{ number_format($item->finalized_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2"
                                    style="font-size:9px;color:#999;padding:8px;
                                    text-align:center;">
                                    No earnings recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background:#e8f5e9;">
                            <td style="font-size:9px;font-weight:700;padding:5px 8px;
                                border-top:1.5px solid #a5d6a7;color:#2e7d32;">
                                Total Earnings
                            </td>
                            <td style="font-size:9px;font-weight:700;padding:5px 8px;
                                text-align:right;border-top:1.5px solid #a5d6a7;
                                color:#2e7d32;">
                                {{ number_format($entry->total_gross, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>

            {{-- Deductions panel --}}
            <td style="width:50%;vertical-align:top;padding-left:6px;">
                <table style="width:100%;border-collapse:collapse;border-radius:4px;">
                    <thead>
                        <tr>
                            <td colspan="2"
                                style="background:#c62828;color:white;padding:6px 10px;
                                       font-size:9px;font-weight:700;text-transform:uppercase;
                                       letter-spacing:0.5px;">
                                ▼ Deductions
                            </td>
                        </tr>
                        <tr style="background:#ffebee;">
                            <th style="font-size:8px;color:#555;padding:4px 8px;
                                text-align:left;font-weight:600;border-bottom:1px solid #ccc;">
                                Component
                            </th>
                            <th style="font-size:8px;color:#555;padding:4px 8px;
                                text-align:right;font-weight:600;border-bottom:1px solid #ccc;">
                                Amount (TZS)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deductions as $item)
                            <tr style="background:{{ $loop->even ? '#fff3f3' : '#ffffff' }};">
                                <td style="font-size:9px;padding:4px 8px;
                                    border-bottom:1px solid #f0f0f0;">
                                    {{ $item->component_name_snapshot }}
                                </td>
                                <td style="font-size:9px;padding:4px 8px;text-align:right;
                                    border-bottom:1px solid #f0f0f0;font-weight:600;
                                    color:#c62828;">
                                    {{ number_format($item->finalized_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2"
                                    style="font-size:9px;color:#999;padding:8px;
                                    text-align:center;">
                                    No deductions recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background:#ffebee;">
                            <td style="font-size:9px;font-weight:700;padding:5px 8px;
                                border-top:1.5px solid #ef9a9a;color:#c62828;">
                                Total Deductions
                            </td>
                            <td style="font-size:9px;font-weight:700;padding:5px 8px;
                                text-align:right;border-top:1.5px solid #ef9a9a;
                                color:#c62828;">
                                {{ number_format($entry->total_deductions, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>

    {{-- ── Net Pay Banner ───────────────────────────────────────────────────── --}}
    <table style="width:100%;border-collapse:collapse;margin-bottom:14px;">
        <tr>
            <td style="background:#1b5e20;text-align:center;padding:16px;
                border-radius:6px;">
                <div style="font-size:8px;color:rgba(255,255,255,0.6);
                    text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">
                    NET PAY
                </div>
                <div style="font-size:24px;font-weight:700;color:white;">
                    TZS {{ number_format($entry->net_pay, 2) }}
                </div>
                <div style="font-size:8px;color:rgba(255,255,255,0.55);margin-top:3px;">
                    After all deductions for the period ending
                    {{ $entry->payPeriod->end_date->format('d M Y') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Summary Row ──────────────────────────────────────────────────────── --}}
    <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
        <tr>
            <td style="width:33.33%;text-align:center;padding:10px 4px;">
                <div style="background:#e3f2fd;border-radius:4px;padding:10px;">
                    <div style="font-size:8px;color:#777;text-transform:uppercase;
                        letter-spacing:0.5px;margin-bottom:3px;">
                        Total Gross
                    </div>
                    <div style="font-size:13px;font-weight:700;color:#1565c0;">
                        TZS {{ number_format($entry->total_gross, 2) }}
                    </div>
                </div>
            </td>
            <td style="width:33.33%;text-align:center;padding:10px 4px;">
                <div style="background:#ffebee;border-radius:4px;padding:10px;">
                    <div style="font-size:8px;color:#777;text-transform:uppercase;
                        letter-spacing:0.5px;margin-bottom:3px;">
                        Total Deductions
                    </div>
                    <div style="font-size:13px;font-weight:700;color:#c62828;">
                        TZS {{ number_format($entry->total_deductions, 2) }}
                    </div>
                </div>
            </td>
            <td style="width:33.33%;text-align:center;padding:10px 4px;">
                <div style="background:#e8f5e9;border-radius:4px;padding:10px;">
                    <div style="font-size:8px;color:#777;text-transform:uppercase;
                        letter-spacing:0.5px;margin-bottom:3px;">
                        Net Pay
                    </div>
                    <div style="font-size:13px;font-weight:700;color:#2e7d32;">
                        TZS {{ number_format($entry->net_pay, 2) }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Signature Block ──────────────────────────────────────────────────── --}}
    <table style="width:100%;border-collapse:collapse;margin-top:20px;">
        <tr>
            <td style="width:33%;text-align:center;padding:0 10px;">
                <div style="border-top:1px solid #333;width:80%;
                    margin:0 auto 4px auto;padding-top:4px;">
                </div>
                <div style="font-size:8px;color:#555;">Employee Signature</div>
            </td>
            <td style="width:33%;text-align:center;padding:0 10px;">
                <div style="border-top:1px solid #333;width:80%;
                    margin:0 auto 4px auto;padding-top:4px;">
                </div>
                <div style="font-size:8px;color:#555;">HR Officer</div>
            </td>
            <td style="width:33%;text-align:center;padding:0 10px;">
                <div style="border-top:1px solid #333;width:80%;
                    margin:0 auto 4px auto;padding-top:4px;">
                </div>
                <div style="font-size:8px;color:#555;">Finance Officer</div>
            </td>
        </tr>
    </table>

    {{-- ── Document Footer ──────────────────────────────────────────────────── --}}
    <div style="text-align:center;font-size:8px;color:#aaa;margin-top:14px;
        border-top:1px solid #eee;padding-top:8px;">
        This is a computer-generated payslip and does not require a physical signature.
        Generated on {{ now()->format('d M Y H:i') }}.
    </div>

</body>
</html>