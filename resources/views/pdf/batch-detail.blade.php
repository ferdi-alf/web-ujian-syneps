<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Detail Batch - {{ $batch['nama'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .stat-box {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        h3 {
            color: #333;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DETAIL BATCH</h1>
        <h2>{{ $batch['nama'] }} - {{ $batch['kelas'] }}</h2>
        <p>Periode: {{ $batch['periode'] }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>{{ $batch['total_siswa'] }}</h3>
            <p>Siswa</p>
        </div>
        <div class="stat-box">
            <h3>{{ $batch['total_materi'] }}</h3>
            <p>Materi</p>
        </div>
        <div class="stat-box">
            <h3>{{ $batch['total_ujian'] }}</h3>
            <p>Ujian</p>
        </div>
        <div class="stat-box">
            <h3>{{ $batch['total_pembayaran'] }}</h3>
            <p>Pembayaran</p>
        </div>
    </div>

    @if (count($batch['siswa']) > 0)
        <h3>Daftar Siswa</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Rata-rata</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($batch['siswa'] as $index => $siswa)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $siswa['nama'] }}</td>
                        <td>{{ $siswa['email'] }}</td>
                        <td>{{ $siswa['rata_rata'] }}</td>
                        <td>{{ $siswa['status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (count($batch['materi']) > 0)
        <h3>Daftar Materi</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Ditambahkan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($batch['materi'] as $index => $materi)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $materi['judul'] }}</td>
                        <td>{{ $materi['created_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (count($batch['ujian']) > 0)
        <h3>Daftar Ujian</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Soal</th>
                    <th>Siswa</th>
                    <th>Rata-rata</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($batch['ujian'] as $index => $ujian)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $ujian['judul'] }}</td>
                        <td>{{ $ujian['total_soal'] }}</td>
                        <td>{{ $ujian['total_siswa'] }}</td>
                        <td>{{ $ujian['rata_rata'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="margin-top: 40px; text-align: center; color: #666;">
        <p>Dokumen dibuat pada {{ date('d F Y H:i:s') }}</p>
    </div>
</body>

</html>
