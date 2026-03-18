<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bondo - {{ $summary['month'] ?? $summary['period'] }}</title>
    <style>
        @page { 
            margin: 1.2cm; 
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #334155; 
            line-height: 1.6; 
            margin: 0;
        }

        /* Header & Logo Area */
        .header-table { width: 100%; border-bottom: 2px solid #1e40af; padding-bottom: 15px; margin-bottom: 25px; }
        .brand-section { vertical-align: middle; }
        .brand-name { font-size: 24px; font-weight: bold; color: #1e40af; margin: 0; letter-spacing: -0.5px; }
        .report-title { font-size: 12px; color: #64748b; margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        
        .logo-box {
            width: 45px;
            height: 45px;
            background-color: #1e40af;
            color: white;
            text-align: center;
            line-height: 45px;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px;
            float: right;
        }

        /* Summary Cards */
        .summary-wrapper { margin-bottom: 30px; width: 100%; border-spacing: 10px 0; margin-left: -10px; }
        .card { 
            border: 1px solid #e2e8f0; 
            border-radius: 10px; 
            padding: 15px; 
            text-align: center;
            background-color: #f8fafc;
        }
        .card-label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .card-value { font-size: 15px; font-weight: bold; }
        
        .text-income { color: #059669; }
        .text-expense { color: #dc2626; }
        .text-royal { color: #1e40af; }

        /* Main Table */
        table.main-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.main-table th { 
            background-color: #f1f5f9; 
            color: #475569; 
            text-align: left; 
            padding: 12px 10px; 
            font-size: 9px;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }
        table.main-table td { 
            padding: 10px; 
            border-bottom: 1px solid #f1f5f9; 
        }
        table.main-table tr:nth-child(even) { background-color: #f8fafc; }

        /* Footer */
        .footer { 
            position: fixed; 
            bottom: -30px; 
            left: 0;
            right: 0;
            height: 50px;
            text-align: center; 
            font-size: 10px; 
            color: #94a3b8; 
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        .page-number:before { content: "Halaman " counter(page); }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="brand-section">
                <h1 class="brand-name">BONDO</h1>
                <p class="report-title">Keuangan Keluarga</p>
            </td>
            <td align="right">
                <div class="logo-box">B</div>
                <div style="clear: both; padding-top: 5px;">
                    <strong style="color: #1e293b;">Periode:</strong> {{ $summary['period'] }}<br>
                    <span style="font-size: 10px;">{{ $summary['date_range'] }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="summary-wrapper">
        <tr>
            <td width="33%">
                <div class="card">
                    <div class="card-label">Total Pemasukan</div>
                    <div class="card-value text-income">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="33%">
                <div class="card">
                    <div class="card-label">Total Pengeluaran</div>
                    <div class="card-value text-expense">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="34%">
                <div class="card" style="background-color: #eff6ff; border-color: #bfdbfe;">
                    <div class="card-label text-royal">Selisih Bersih</div>
                    <div class="card-value text-royal">Rp {{ number_format($summary['total_income'] - $summary['total_expense'], 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="margin: 20px 0 10px 0; color: #1e293b; font-size: 13px;">Rincian Arus Kas</h3>
    <table class="main-table">
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="18%">Kategori</th>
                <th width="35%">Keterangan</th>
                <th width="15%">Dompet</th>
                <th width="20%" align="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr>
                <td>{{ $trx->date->format('d/m/Y') }}</td>
                <td>{{ $trx->category->name ?? 'Lainnya' }}</td>
                <td>{{ $trx->notes ?: '-' }}</td>
                <td>{{ $trx->wallet->name ?? '-' }}</td>
                <td align="right" style="font-weight: bold;" class="{{ $trx->type == 'Income' ? 'text-income' : 'text-expense' }}">
                    {{ $trx->type == 'Income' ? '+' : '-' }} {{ number_format($trx->amount, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" align="center" style="padding: 30px; color: #94a3b8;">Belum ada data transaksi tersimpan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table width="100%">
            <tr>
                <td align="left" width="40%">Bondo - Keuangan Keluarga</td>
                <td align="center" width="20%" class="page-number"></td>
                <td align="right" width="40%">Powered by <strong>reditech.id</strong></td>
            </tr>
        </table>
    </div>

</body>
</html>