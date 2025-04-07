<?php
// Konfigurasi database
//Konkesi ke Database
$conn = mysqli_connect("localhost", "root", "", "klinik");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan data dari form tidak kosong
if (
    isset(
    $_POST['nama'],
    $_POST['nik'],
    $_POST['jenis_kelamin'],
    $_POST['no_hp'],
    $_POST['tempat_lahir'],
    $_POST['tanggal_lahir'],
    $_POST['alamat'],
    $_POST['tanggal_kunjungan'],
    $_POST['keluhan'],
    $_POST['poli_tujuan'],
    $_POST['jenis_pasien'],
    $_POST['nik_bpjs']
)
) {

    // Ambil data dari form dan bersihkan input
    $nama = htmlspecialchars($_POST['nama']);
    $nik = htmlspecialchars($_POST['nik']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $tempat_lahir = htmlspecialchars($_POST['tempat_lahir']);
    $tanggal_lahir = htmlspecialchars($_POST['tanggal_lahir']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $tanggal_kunjungan = htmlspecialchars($_POST['tanggal_kunjungan']);
    $keluhan = htmlspecialchars($_POST['keluhan']);
    $poli_tujuan = htmlspecialchars($_POST['poli_tujuan']);
    $jenis_pasien = htmlspecialchars($_POST['jenis_pasien']);
    $nik_bpjs = htmlspecialchars($_POST['nik_bpjs']);

    // Query menggunakan prepared statements untuk mencegah SQL Injection
    $sql = "INSERT INTO pasien (nama, nik, jenis_kelamin, no_hp, tempat_lahir, tanggal_lahir, alamat, tanggal_kunjungan, keluhan, poli_tujuan, jenis_pasien, nik_bpjs) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $nama, $nik, $jenis_kelamin, $no_hp, $tempat_lahir, $tanggal_lahir, $alamat, $tanggal_kunjungan, $keluhan, $poli_tujuan, $jenis_pasien, $nik_bpjs);


    // HTML dan JS untuk SweetAlert
    echo "<!DOCTYPE html><html><head>
 <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
 </head><body>";

    // Eksekusi query
    if ($stmt->execute()) {
        // Data Berhasil Ditambah

        echo "<script>
 Swal.fire({
     icon: 'success',
     title: 'Data Berhasil Ditambah!',
     confirmButtonText: 'Kembali'
 }).then(() => {
     window.location.href = '../login/user/buat_kunjungan.php';
 });
</script>";

    } else {
        echo "Error: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
} else {
    echo "Semua data harus diisi!";
}

$conn->close();

echo "</body></html>";
?>