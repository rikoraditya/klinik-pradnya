<?php
// Koneksi ke Database
$conn = mysqli_connect("localhost", "root", "", "klinik");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// HTML & SweetAlert di awal
echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

if (isset($_POST['no_hp'])) {
    $no_hp = preg_replace('/[^0-9]/', '', $_POST['no_hp']);
    if (substr($no_hp, 0, 1) === '0') {
        $no_hp = '62' . substr($no_hp, 1);
    }
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
    // Ambil dan bersihkan data
    $nama = htmlspecialchars($_POST['nama']);
    $nik = htmlspecialchars($_POST['nik']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $no_hp = htmlspecialchars($no_hp);
    $tempat_lahir = htmlspecialchars($_POST['tempat_lahir']);
    $tanggal_lahir = htmlspecialchars($_POST['tanggal_lahir']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $tanggal_kunjungan = htmlspecialchars($_POST['tanggal_kunjungan']);
    $keluhan = htmlspecialchars($_POST['keluhan']);
    $poli_tujuan = htmlspecialchars($_POST['poli_tujuan']);
    $jenis_pasien = htmlspecialchars($_POST['jenis_pasien']);
    $nik_bpjs = htmlspecialchars($_POST['nik_bpjs']);

    // Cek apakah sudah daftar hari ini
    $cek_pasien = $conn->prepare("SELECT COUNT(*) FROM pasien WHERE nik = ? AND tanggal_kunjungan = ?");
    $cek_pasien->bind_param("ss", $nik, $tanggal_kunjungan);
    $cek_pasien->execute();
    $cek_pasien->bind_result($jumlah);
    $cek_pasien->fetch();
    $cek_pasien->close();

    if ($jumlah > 0) {
        // Sudah pernah daftar hari ini
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Sudah Terdaftar!',
                text: 'Pasien dengan NIK ini sudah melakukan pendaftaran pada tanggal tersebut.',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit; // Hentikan proses insert
    }

    // Insert data baru
    $sql = "INSERT INTO pasien (nama, nik, jenis_kelamin, no_hp, tempat_lahir, tanggal_lahir, alamat, tanggal_kunjungan, keluhan, poli_tujuan, jenis_pasien, nik_bpjs) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $nama, $nik, $jenis_kelamin, $no_hp, $tempat_lahir, $tanggal_lahir, $alamat, $tanggal_kunjungan, $keluhan, $poli_tujuan, $jenis_pasien, $nik_bpjs);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Pendaftaran Berhasil',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '../login/user/kunjungan.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mendaftar!',
                text: '" . addslashes($stmt->error) . "',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $stmt->close();
} else {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Data Tidak Lengkap!',
            text: 'Mohon isi semua form sebelum mengirim.',
            confirmButtonText: 'Kembali'
        }).then(() => {
            window.history.back();
        });
    </script>";
}

$conn->close();
echo "</body></html>";
?>