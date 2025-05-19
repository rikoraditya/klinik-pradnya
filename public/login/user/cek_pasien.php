<?php
session_start();
use LDAP\Result;

require '../../php/functions.php';


if (!isset($_SESSION["login_user"])) {
    header("location:../user_login.php");
    exit;
}


echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

// Cek apakah user sudah login
if (!isset($_SESSION['no_hp'])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak!',
            text: 'Silakan login terlebih dahulu.',
            confirmButtonText: 'Login'
        }).then(() => {
            window.location.href = '../user_login.php';
        });
    </script>";
    exit;
}

// Ambil NIK dari input user
if (isset($_POST['no_rm_cari'])) {
    $no_rm = htmlspecialchars($_POST['no_rm_cari']);
    $no_hp_login = $_SESSION['no_hp']; // Tambahan: ambil dari session

    // Query untuk mencocokkan no_rm dan no_hp login
    $query = $conn->prepare("SELECT pasien.* 
                             FROM rekam_medis 
                             JOIN pasien ON rekam_medis.nik = pasien.nik 
                             WHERE rekam_medis.no_rm = ? AND pasien.no_hp = ?");
    $query->bind_param("ss", $no_rm, $no_hp_login);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['pasien_lama'] = $data;
        header("Location: pasien_lama.php");
    } else {
        echo "<script> 
            Swal.fire({
                icon: 'error',
                title: 'No. RM Tidak Ditemukan!',
                html: 'No. RM tidak sesuai dengan akun anda, Silakan melakukan pendaftaran sebagai <strong>Pasien Baru</strong>.',
                confirmButtonText: 'Lanjutkan'
            }).then(() => {
                window.location.href = 'buat_kunjungan.php';
            });
        </script>";
    }

    $query->close();
}

echo "</body></html>";
?>