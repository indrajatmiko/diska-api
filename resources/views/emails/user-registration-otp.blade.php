<?php
// Contoh di controller sebelum return view()
$waPhone = preg_replace('/^0/', '+62', $user->phone_number); // Ganti 0 di depan dengan +62
$waPhone = preg_replace('/^62/', '+62', $waPhone); // Ganti 62 di depan dengan +62 jika belum ada +
$waPhone = preg_replace('/^\+?62/', '+62', $waPhone); // Pastikan hanya satu +62 di depan
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Pengguna Baru</title>
</head>
<body>
    <h1>Pengguna Baru Telah Mendaftar</h1>
    <p>Berikut adalah detail pengguna yang baru saja mendaftar dan memerlukan verifikasi:</p>
    <ul>
        <li><strong>Nama:</strong> {{ $user->name }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>No. Handphone:</strong> {{ $user->phone_number }}</li>
    </ul>
    <p>
        <strong>KODE VERIFIKASI (OTP):</strong>
        <strong style="font-size: 20px; color: #007BFF;">{{ $otpCode }} > <a href="https://wa.me/{{ $waPhone }}?text=Kode%20OTP%20Kauniyah%20Store%20Anda%20adalah%20{{ $otpCode }}.%20Simpan%20Nomor%20ini.">WA Pengguna</a></strong>
    </p>
    <p>Kode ini akan kedaluwarsa dalam 30 menit.</p>
</body>
</html>