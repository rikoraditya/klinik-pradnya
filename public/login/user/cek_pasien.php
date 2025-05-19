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


    // Query untuk ambil data pasien dari no_rm
    $query = $conn->prepare("SELECT pasien.* 
                             FROM rekam_medis 
                             JOIN pasien ON rekam_medis.nik = pasien.nik 
                             WHERE rekam_medis.no_rm = ?");
    $query->bind_param("s", $no_rm);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Simpan ke session
        $_SESSION['pasien_lama'] = $data;

        // Redirect ke form isian RM
        header("Location: pasien_lama.php");
    } else {
        echo "<script> 
            Swal.fire({
                icon: 'error',
                title: 'No. RM Tidak Ditemukan!',
                html: 'Silakan melakukan pendaftaran sebagai <strong>Pasien Baru</strong>.',
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