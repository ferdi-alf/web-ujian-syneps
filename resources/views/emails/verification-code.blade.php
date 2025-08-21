<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode Verifikasi</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Kode Verifikasi Email Anda</h2>

        <p>Halo {{ $user->name }},</p>

        <p>Terima kasih telah membuat akun. Untuk menyelesaikan proses registrasi, silakan masukkan kode verifikasi
            berikut:</p>

        <div style="text-align: center; margin: 30px 0;">
            <div
                style="background-color: #F3F4F6; padding: 20px; border-radius: 5px; font-size: 24px; font-weight: bold; letter-spacing: 5px;">
                {{ $code }}
            </div>
        </div>

        <p><strong>Penting:</strong></p>
        <ul>
            <li>Kode ini berlaku selama 10 menit</li>
            <li>Jangan bagikan kode ini kepada siapapun</li>
            <li>Jika Anda tidak meminta kode ini, abaikan email ini</li>
        </ul>

        <p>Terima kasih,<br>Tim {{ config('app.name') }}</p>
    </div>
</body>

</html>
