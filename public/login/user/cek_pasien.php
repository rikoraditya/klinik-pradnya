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
if (isset($_POST['nik_cari'])) {
    $nik_input = $_POST['nik_cari'];
    $no_hp_login = $_SESSION['no_hp'];

    // Ambil NIK dari database berdasarkan no_hp akun yang sedang login
    $stmt = $conn->prepare("SELECT nik FROM rekam_medis WHERE no_hp = ?");
    $stmt->bind_param("s", $no_hp_login);
    $stmt->execute();
    $result_nik = $stmt->get_result();

    if ($result_nik->num_rows > 0) {
        $row = $result_nik->fetch_assoc();
        $nik_asli = $row['nik'];

        if ($nik_input !== $nik_asli) {
            echo "<script> 
            Swal.fire({
                icon: 'error',
                title: 'NIK Tidak Sesuai!',
                text: 'NIK yang Anda masukkan tidak cocok dengan akun Anda.',
                confirmButtonText: 'Coba Lagi'
            }).then(() => {
                window.location.href = 'buat_kunjungan.php';
            });
            </script>";
            exit;
        }

        // Jika nik_input sama dengan nik akun login, lanjut cari data rekam medis
        $query = $conn->prepare("SELECT * FROM rekam_medis WHERE nik = ?");
        $query->bind_param("s", $nik_input);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $_SESSION['pasien_lama'] = $data;
            header("Location: pasien_lama.php");
            exit;
        } else {
            echo "<script> 
            Swal.fire({
                icon: 'error',
                title: 'Rekam Medis Tidak Ditemukan!',
                html: 'Silakan melakukan pendaftaran sebagai <strong>Pasien Baru</strong>.',
                confirmButtonText: 'Lanjutkan'
            }).then(() => {
                window.location.href = 'buat_kunjungan.php';
            });
            </script>";
        }

    } else {
        echo "<script> 
        Swal.fire({
            icon: 'error',
            title: 'Data Akun Tidak Valid!',
            text: 'Data NIK tidak ditemukan pada Rekam Medis.',
             html:  'Jika anda belum pernah berobat, anda tidak akan bisa mendaftar sebagai <strong>Pasien Lama</strong>.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'buat_kunjungan.php';
        });
        </script>";
    }
}

echo "</body></html>";
?>