<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_users"); // Ganti dengan kredensial database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_input = $_POST['otp'] ?? '';

    if (!empty($otp_input) && isset($_SESSION['otp'])) {
        if ($otp_input == $_SESSION['otp']) {
            // Simpan ke database
            $phone = $_SESSION['phone'];
            $sql = "INSERT INTO users (phone) VALUES ('$phone')";
            $conn->query($sql);
            echo "success";

            // Hapus sesi OTP
            unset($_SESSION['otp']);
        } else {
            echo "Kode OTP salah!";
        }
    } else {
        echo "Kode OTP tidak valid.";
    }
}
?>