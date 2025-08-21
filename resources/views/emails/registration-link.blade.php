<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link Registrasi</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Selamat! Pendaftaran Anda Telah Disetujui</h2>

        <p>Halo {{ $peserta->nama_lengkap }},</p>

        <p>Kami dengan senang hati memberitahukan bahwa pendaftaran Anda untuk kelas
            <strong>{{ $peserta->kelas->nama ?? 'Program' }}</strong> batch
            <strong>{{ $peserta->batches->nama ?? 'Default' }}</strong> telah disetujui oleh admin.</p>

        <p>Langkah selanjutnya, silakan klik tombol di bawah ini untuk membuat akun Anda:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $registrationUrl }}"
                style="background-color: #3B82F6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Buat Akun Sekarang
            </a>
        </div>

        <p><strong>Penting:</strong></p>
        <ul>
            <li>Link ini berlaku selama 24 jam</li>
            <li>Setelah membuat akun, Anda akan menerima kode verifikasi</li>
            <li>Pastikan menggunakan email yang sama saat pendaftaran</li>
        </ul>

        <p>Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.</p>

        <p>Terima kasih,<br>Tim {{ config('app.name') }}</p>
    </div>
</body>

</html>
