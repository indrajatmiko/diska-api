<!DOCTYPE html>
<html>
<head>
    <title>Penghapusan Akun Dikonfirmasi</title>
</head>
<body>
    <h1>Akun Anda Telah Dihapus</h1>
    <p>Halo {{ $userName }},</p>
    <p>Sesuai dengan permintaan Anda, akun Anda yang terdaftar dengan email <strong>{{ $userEmail }}</strong> beserta semua data terkait telah berhasil dihapus secara permanen dari sistem kami.</p>
    <p>Terima kasih telah menggunakan layanan kami. Jika Anda memiliki pertanyaan, silakan hubungi customer service.</p>
    <p>Hormat kami,<br>Tim {{ config('app.name') }}</p>
</body>
</html>