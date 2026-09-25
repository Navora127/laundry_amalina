<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan LaundryKu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 11px; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        p { margin: 0 0 12px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 7px; border: 1px solid #cbd5e1; text-align: left; }
        th { background: #e2e8f0; }
        .summary td { border: 0; padding: 4px 12px 4px 0; }
        .summary strong { float: right; }
    </style>
</head>
<body>
    <h1>Laporan LaundryKu</h1>
    <p>{{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</p>
    <table class="summary">
        <tr><td>Total pesanan</td><td><strong>{{ $summary['total_orders'] }}</strong></td></tr>
        <tr><td>Pesanan selesai</td><td><strong>{{ $summary['completed_orders'] }}</strong></td></tr>
        <tr><td>Pesanan dibatalkan</td><td><strong>{{ $summary['cancelled_orders'] }}</strong></td></tr>
        <tr><td>Pendapatan terverifikasi</td><td><strong>Rp{{ number_format($summary['revenue'], 0, ',', '.') }}</strong></td></tr>
        <tr><td>Pelanggan aktif</td><td><strong>{{ $summary['active_customers'] }}</strong></td></tr>
    </table>
    <table>
        <thead><tr><th>Kode</th><th>Pelanggan</th><th>Status</th><th>Total</th><th>Tanggal</th></tr></thead>
        <tbody>@foreach ($orders as $order)<tr><td>{{ $order->code }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->status->label() }}</td><td>Rp{{ number_format($order->final_total, 0, ',', '.') }}</td><td>{{ $order->created_at->format('d M Y H:i') }}</td></tr>@endforeach</tbody>
    </table>
</body>
</html>
