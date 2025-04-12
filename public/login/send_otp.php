<?php
session_start();
require '../../public/php/functions.php'; // koneksi DB

// Token device Fonnte kamu
$token = "kuN8SnSFXNd3VRx4GMvZ"; // Ganti dengan token device kamu

// Fungsi untuk kirim pesan via Fonnte
function Kirimfonnte($token, $data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'target' => $data["target"],
            'message' => $data["message"],
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // Simpan log respon ke file

}

if (isset($_POST['no_hp'])) {
    $no_hp = preg_replace('/[^0-9]/', '', $_POST['no_hp']);
    if (substr($no_hp, 0, 1) === '0') {
        $no_hp = '62' . substr($no_hp, 1);
    }

    $_SESSION['no_hp_temp'] = $no_hp;

    // Hapus OTP lama
    mysqli_query($conn, "DELETE FROM otp_login WHERE no_hp = '$no_hp' AND is_verified = 0");

    // Buat OTP baru
    $otp = rand(100000, 999999);

    // Simpan ke database
    $query = "INSERT INTO otp_login (no_hp, kode_otp, waktu_kirim, is_verified)
              VALUES ('$no_hp', '$otp', NOW(), 0)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Siapkan data pesan
        $data = [
            "target" => $no_hp,
            "message" => "_*Om Swastyastu*_. Terimakasih sudah memilih layanan kami. Berikut kode verifikasi anda: *$otp*\nBerlaku selama 5 menit.\n\n*Klinik Pradnya Usadha* berkomitmen untuk menghadirkan inovasi layanan terbaik. Didukung oleh Dokter, Perawat, dan Staff yang ramah melayani pasien. Kami yakin akan selalu menjadi pilihan anda dan keluarga. Terimakasih\n\n*#We Care With Cencerity*\n*#Klinik Pradnya Usadha Klungkung*"
        ];

        // Kirim ke WhatsApp
        Kirimfonnte($token, $data);

        // Redirect
        header("Location: input_otp.php");
        exit;
    } else {
        echo "Gagal menyimpan OTP.";
    }
} else {
    echo "Nomor HP tidak ditemukan.";
}
