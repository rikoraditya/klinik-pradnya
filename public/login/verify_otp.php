<?php
session_start();

if (isset($_SESSION["login_user"])) {
    header("location: user/buat_kunjungan.php");
    exit;
}

require '../../public/php/functions.php'; // koneksi DB

// Fungsi untuk menormalkan nomor HP
function normalize_hp($no_hp)
{
    $no_hp = preg_replace('/[^0-9]/', '', $no_hp); // Hanya angka
    if (substr($no_hp, 0, 1) === '0') {
        $no_hp = '62' . substr($no_hp, 1); // Ubah 08xx menjadi 628xx
    }
    return $no_hp;
}

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Validasi CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die("<script>
        alert('Permintaan tidak valid. Token CSRF tidak cocok.');
        window.location.href = 'input_otp.php';
    </script>");
}

// Ambil nomor HP dari session dan normalisasi
$no_hp_raw = $_SESSION['no_hp_temp'] ?? '';
$no_hp = normalize_hp($no_hp_raw);
$no_hp = mysqli_real_escape_string($conn, $no_hp);

// Validasi OTP
$otp_input = preg_replace('/[^0-9]/', '', $_POST['otp']);
$otp_input = mysqli_real_escape_string($conn, $otp_input);

if (strlen($otp_input) !== 6) {
    echo "<script>
        alert('Kode OTP harus 6 digit angka.');
        window.location.href = 'input_otp.php';
    </script>";
    exit;
}

// Cek OTP di database
$query = "SELECT * FROM otp_login 
          WHERE no_hp = '$no_hp' 
          AND kode_otp = '$otp_input' 
          AND is_verified = 0 
          ORDER BY waktu_kirim DESC 
          LIMIT 1";
$result = mysqli_query($conn, $query);

// SweetAlert
echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0 && $data = mysqli_fetch_assoc($result)) {
    date_default_timezone_set('Asia/Makassar');
    $waktu_kirim = strtotime($data['waktu_kirim']);
    $now = time();

    if ($now - $waktu_kirim <= 300) { // valid 5 menit
        // Tandai OTP sebagai terverifikasi
        mysqli_query($conn, "UPDATE otp_login SET is_verified = 1 WHERE id = " . $data['id']);

        // Simpan nomor HP yang sudah dinormalisasi ke session login
        $_SESSION['no_hp_verified'] = $no_hp;
        $_SESSION['no_hp'] = $no_hp;

        $_SESSION["login_user"] = true;

        header("Location: user/buat_kunjungan.php");
        exit;


    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Kode OTP Kadaluarsa',
                text: 'Silakan kirim ulang OTP.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'user_login.php';
            });
        </script>";
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Kode OTP Salah',
            text: 'Pastikan kode dan nomor HP sesuai',
            confirmButtonText: 'Coba Lagi'
        }).then(() => {
            window.location.href = 'input_otp.php';
        });
    </script>";
}

echo "</body></html>";
