<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_users");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_input = $_POST['otp'] ?? '';

    if (!empty($otp_input) && isset($_SESSION['otp'])) {
        if ($otp_input == $_SESSION['otp']) {
            echo "success"; // Login berhasil
            unset($_SESSION['otp']);
        } else {
            echo "Kode OTP salah!";
        }
    } else {
        echo "Kode OTP tidak valid.";
    }
}
?>