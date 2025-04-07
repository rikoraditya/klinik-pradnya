<?php
session_start();
require '../../public/php/functions.php'; // Ganti sesuai file koneksi ke database kamu

if (isset($_POST['no_hp'])) {
    $no_hp = $_POST['no_hp'];
    $otp = rand(100000, 999999); // kode 6 digit

    // Simpan ke database
    $query = "INSERT INTO otp_login (no_hp, kode_otp, waktu_kirim, is_verified)
              VALUES ('$no_hp', '$otp', NOW(), 0)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Kirim OTP ke nomor HP
        $message = "Kode verifikasi Anda adalah: $otp";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://kirim.pesan.id/api/send-message');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
            'phone' => $no_hp,
            'message' => $message,
            'token' => '25e41f23595d72ec7853222e69e8f43503ff22da', // ganti dengan token aslimu
        ]));
        $response = curl_exec($curl);
        curl_close($curl);

        // Debug response
        echo "<pre>";
        print_r($response);
        echo "</pre>";

        // Arahkan ke form input kode OTP
        $_SESSION['no_hp_temp'] = $no_hp; // simpan sementara
        header("Location: input_otp.php");
        exit;
    } else {
        echo "Gagal menyimpan OTP.";
    }
} else {
    echo "Nomor HP tidak ditemukan.";
}
?>