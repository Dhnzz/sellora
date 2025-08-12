@php
    // Guard & helper
    $cards = $cards ?? [];
    $trend = $trend ?? [];
    $methods = $methods ?? [];
    $top_products = $top_products ?? [];
    $top_customers = $top_customers ?? [];
    $invoices = $invoices ?? [];

    $from = request('from');
    $to = request('to');

    function idr($n)
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }
    function nf($n)
    {
        return number_format((float) $n, 0, ',', '.');
    }

    // Date label
    $dateLabel =
        $from && $to
            ? \Carbon\Carbon::parse($from)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($to)->format('d M Y')
            : 'Semua Tanggal';

    // Company (opsional)
    $companyName = config('app.name', 'Perusahaan XYZ');
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        /* Reset ringan */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            margin: 0 0 6px;
        }

        .muted {
            color: #666;
        }

        .small {
            font-size: 11px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        /* Header + Footer */
        .header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header .title {
            font-size: 18px;
            font-weight: 700;
        }

        .header .meta {
            font-size: 11px;
            color: #555;
        }

        /* Grid cards */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -6px;
        }

        .col {
            padding: 0 6px;
        }

        .col-3 {
            width: 25%;
        }

        .col-4 {
            width: 33.3333%;
        }

        .col-6 {
            width: 50%;
        }

        .col-12 {
            width: 100%;
        }

        .card {
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 10px;
        }

        .card .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 2px;
        }

        .card .value {
            font-size: 16px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 10px;
        }

        th,
        td {
            border: 1px solid #e5e5e5;
            padding: 6px 8px;
        }

        th {
            background: #f6f6f6;
            font-weight: 700;
        }

        tbody tr:nth-child(even) {
            background: #fbfbfb;
        }

        /* Page breaks */
        .page-break {
            page-break-after: always;
        }

        @page {
            margin: 20px 24px;
        }

        .section-title {
            font-weight: 700;
            margin: 10px 0 6px;
        }

        /* Badge status */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }

        .bg-success {
            background: #d1fae5;
            color: #065f46;
        }

        .bg-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .bg-secondary {
            background: #e5e7eb;
            color: #374151;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <table style="width:100%; border:0">
            <tr>
                <td style="border:0">
                    <div class="title">{{ $companyName }}</div>
                    <div class="meta">Laporan Penjualan • {{ $dateLabel }}</div>
                </td>
                <td class="right muted" style="border:0">
                    Dicetak: {{ now()->format('d M Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- KPI CARDS --}}
    <div class="row">
        <div class="col col-3">
            <div class="card">
                <div class="label">Total Penjualan (Gross)</div>
                <div class="value">{{ idr($cards['gross'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col col-3">
            <div class="card">
                <div class="label">Diskon</div>
                <div class="value">{{ idr($cards['discount'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col col-3">
            <div class="card">
                <div class="label">Pajak</div>
                <div class="value">{{ idr($cards['tax'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col col-3">
            <div class="card">
                <div class="label">Net Sales</div>
                <div class="value">{{ idr($cards['net_sales'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col col-3">
            <div class="card">
                <div class="label">Retur</div>
                <div class="value">{{ idr($cards['return_total'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col col-3">
            <div class="card">
                <div class="label">Biaya Lain</div>
                <div class="value">{{ idr($cards['other_expense'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col col-3">
            <div class="card">
                <div class="label">Jumlah Transaksi</div>
                <div class="value">{{ nf($cards['trx_count'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col col-3">
            <div class="card">
                <div class="label">AOV</div>
                <div class="value">{{ idr($cards['aov'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    {{-- TREN PENJUALAN (TABLE) --}}
    <div class="section-title">Tren Penjualan per Tanggal</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30%">Tanggal</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trend as $t)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($t->d ?? ($t['d'] ?? ''))->format('d M Y') }}</td>
                    <td class="right">{{ idr($t->total ?? ($t['total'] ?? 0)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="center muted">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- BREAKDOWN METODE PEMBAYARAN --}}
    <div class="section-title">Metode Pembayaran</div>
    <table>
        <thead>
            <tr>
                <th>Metode</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($methods as $m)
                <tr>
                    <td>{{ strtoupper($m->method ?? ($m['method'] ?? '-')) }}</td>
                    <td class="right">{{ idr($m->total ?? ($m['total'] ?? 0)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="center muted">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- TOP PRODUK & TOP CUSTOMER --}}
    <div class="row">
        <div class="col col-6">
            <div class="section-title">Top Produk</div>
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="right">Qty</th>
                        <th class="right">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($top_products as $p)
                        <tr>
                            <td>{{ $p->name ?? ($p['name'] ?? '-') }}</td>
                            <td class="right">{{ nf($p->qty ?? ($p['qty'] ?? 0)) }}</td>
                            <td class="right">{{ idr($p->omzet ?? ($p['omzet'] ?? 0)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="center muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="col col-6">
            <div class="section-title">Top Pelanggan</div>
            <table>
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th class="right">Transaksi</th>
                        <th class="right">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($top_customers as $c)
                        <tr>
                            <td>{{ $c->customer ?? ($c['customer'] ?? '-') }}</td>
                            <td class="right">{{ nf($c->trx_count ?? ($c['trx_count'] ?? 0)) }}</td>
                            <td class="right">{{ idr($c->omzet ?? ($c['omzet'] ?? 0)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="center muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DETAIL TRANSAKSI --}}
    <div class="section-title">Detail Transaksi</div>
    <table>
        <thead>
            <tr>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 12%">No. Invoice</th>
                <th>Pelanggan</th>
                <th>Sales</th>
                <th class="right">Subtotal</th>
                <th class="right">Diskon</th>
                <th class="right">Pajak</th>
                <th class="right">Retur</th>
                <th class="right">Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $r)
                @php
                    $status = strtolower($r->status ?? ($r['status'] ?? ''));
                    $badgeClass =
                        $status === 'paid' ? 'bg-success' : ($status === 'partial' ? 'bg-warning' : 'bg-secondary');
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($r->date ?? ($r['date'] ?? ''))->format('d M Y') }}</td>
                    <td>{{ $r->id ?? ($r['id'] ?? ($r->invoice_no ?? ($r['invoice_no'] ?? '-'))) }}</td>
                    <td>{{ $r->customer ?? ($r['customer'] ?? '-') }}</td>
                    <td>{{ $r->sales ?? ($r['sales'] ?? '-') }}</td>
                    <td class="right">{{ idr($r->subtotal ?? ($r['subtotal'] ?? 0)) }}</td>
                    <td class="right">{{ idr($r->discount ?? ($r['discount'] ?? 0)) }}</td>
                    <td class="right">{{ idr($r->tax ?? ($r['tax'] ?? 0)) }}</td>
                    <td class="right">{{ idr($r->return_total ?? ($r['return_total'] ?? 0)) }}</td>
                    <td class="right">{{ idr($r->total ?? ($r['total'] ?? 0)) }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ strtoupper($status ?: '-') }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center muted">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER KECIL --}}
    <div class="small muted" style="margin-top:6px;">
        *Laporan dihasilkan oleh sistem pada {{ now()->format('d M Y H:i') }}. Angka dibulatkan ke rupiah terdekat.
    </div>
</body>

</html>
