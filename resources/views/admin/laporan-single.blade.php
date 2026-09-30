<!DOCTYPE html>
<html>
<head>
    <title>Laporan Patroli Diskominfo</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .table th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DISKOMINFO - LAPORAN HASIL PATROLI</h2>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i') }} WIB</p>
    </div>
    <table class="table">
        <tr>
            <th>Judul Laporan</th>
            <td>{{ $laporan->judul ?? '-' }}</td>
        </tr>
        <tr>
            <th>Petugas Pelapor</th>
            <td>{{ $laporan->user->name ?? 'Petugas' }}</td>
        </tr>
        <tr>
            <th>Status Validasi</th>
            <td>{{ ucfirst($laporan->status ?? 'Pending') }}</td>
        </tr>
        <tr>
            <th>Deskripsi / Catatan</th>
            <td>{{ $laporan->deskripsi ?? $laporan->isi ?? '-' }}</td>
        </tr>
    </table>
</body>
</html>