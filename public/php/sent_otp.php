<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'] ?? '';

    if (!empty($phone)) {
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Simpan OTP di sesi
        $_SESSION['otp'] = $otp;
        $_SESSION['phone'] = $phone;

        // Simulasi pengiriman OTP (Gantilah dengan API SMS jika perlu)
        echo "Kode OTP telah dikirim: $otp";
    } else {
        echo "Nomor HP tidak valid.";
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>