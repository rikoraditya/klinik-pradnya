<?php
session_start();
require '../../public/php/functions.php'; // koneksi DB

// Normalisasi nomor HP
$no_hp = preg_replace('/^0/', '62', $_POST['no_hp']);
$no_hp = mysqli_real_escape_string($conn, $no_hp);

$otp_input = trim($_POST['otp']); // hilangkan spasi
$otp_input = mysqli_real_escape_string($conn, $otp_input);

// Ambil data OTP dari database
$query = "SELECT * FROM otp_login 
          WHERE no_hp = '$no_hp' 
          AND kode_otp = '$otp_input' 
          AND is_verified = 0 
          ORDER BY waktu_kirim DESC 
          LIMIT 1";
$result = mysqli_query($conn, $query);

// HTML dan JS untuk SweetAlert
echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

if ($data = mysqli_fetch_assoc($result)) {
    date_default_timezone_set('Asia/Jakarta');
    $waktu_kirim = strtotime($data['waktu_kirim']);
    $now = time();

    if ($now - $waktu_kirim <= 300) {
        // OTP valid
        mysqli_query($conn, "UPDATE otp_login SET is_verified = 1 WHERE id = " . $data['id']);
        $_SESSION['no_hp_verified'] = $no_hp;

        // Alert sukses + redirect
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Verifikasi Berhasil!',
                text: 'Silakan lanjutkan pendaftaran.',
                confirmButtonText: 'Lanjut'
            }).then(() => {
                window.location.href = 'user/buat_kunjungan.php';
            });
        </script>";
    } else {
        // OTP kadaluarsa
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Kode OTP Kadaluarsa',
                text: 'Silakan kirim ulang OTP.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'input_otp.php';
            });
        </script>";
    }
} else {
    // OTP salah
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Kode OTP Salah',
            text: 'Periksa kembali kode OTP kamu!',
            confirmButtonText: 'Coba Lagi'
        }).then(() => {
            window.location.href = 'input_otp.php';
        });
    </script>";
}

echo "</body></html>";
?>