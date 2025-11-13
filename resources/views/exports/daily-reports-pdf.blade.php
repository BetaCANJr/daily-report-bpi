<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Harian - {{ now()->format('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .user-info { text-align: left; margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f5f5f5; font-weight: bold; }
        .status-selesai { color: #28a745; }
        .status-progress { color: #ffc107; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Harian - LPK BPI</h1>
        <p>Periode: 
            @if(request('start_date') && request('end_date'))
                {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
            @else
                Semua Data
            @endif
        </p>
    </div>

    <div class="user-info">
        <strong>User:</strong> {{ auth()->user()->name }}<br>
        <strong>Jabatan:</strong> {{ auth()->user()->jabatan }}<br>
        <strong>Email:</strong> {{ auth()->user()->email }}<br>
        <strong>Total Laporan:</strong> {{ $reports->count() }}<br>
        <strong>Dicetak pada:</strong> {{ now()->format('d F Y H:i') }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Laporan</th>
                <th>Tugas Harian</th>
                <th>Deskripsi</th>
                <th>Kendala</th>
                <th>Status</th>
                <th>Catatan Tambahan</th>
                <th>Dibuat Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $report->report_date->format('d/m/Y') }}</td>
                <td>{{ $report->tugas_harian }}</td>
                <td>{{ $report->deskripsi }}</td>
                <td>{{ $report->kendala ?: '-' }}</td>
                <td>
                    <span class="status-{{ strtolower(str_replace(' ', '-', $report->status)) }}">
                        {{ $report->status }}
                    </span>
                </td>
                <td>{{ $report->catatan_tambahan ?: '-' }}</td>
                <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Daily Report LPK BPI</p>
        <p>User: {{ auth()->user()->name }} | Halaman 1</p>
    </div>
</body>
</html>