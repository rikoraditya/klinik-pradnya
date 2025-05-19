<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "klinik_pradnya");

echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

if (!$conn) {
    die("<script>
        Swal.fire({
            icon: 'error',
            title: 'Koneksi Gagal',
            text: 'Tidak bisa terhubung ke database!',
            confirmButtonText: 'OK'
        }).then(() => { window.history.back(); });
    </script>");
}

// Cek jika user belum login
if (!isset($_SESSION["login_user"]) || !isset($_SESSION["no_hp"])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak',
            text: 'Silakan login terlebih dahulu.',
            confirmButtonText: 'Login'
        }).then(() => {
            window.location.href = '../user_login.php';
        });
    </script>";
    exit;
}

// Proses jika form dikirim
if (
    isset($_POST['nama'], $_POST['nik'], $_POST['jenis_kelamin'], $_POST['no_hp'], $_POST['tempat_lahir'], $_POST['tanggal_lahir'], $_POST['alamat'])
) {
    $nama = htmlspecialchars($_POST['nama']);
    $nik = htmlspecialchars($_POST['nik']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);

    // Format dan normalisasi nomor HP dari form
    $no_hp_form = preg_replace('/[^0-9]/', '', $_POST['no_hp']);
    if (substr($no_hp_form, 0, 1) === '0') {
        $no_hp_form = '62' . substr($no_hp_form, 1);
    }

    // Ambil no_hp dari session login
    $no_hp_login = $_SESSION['no_hp'];

    // Validasi no_hp login harus sama dengan yang diisi di form
    if ($no_hp_form !== $no_hp_login) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'No. HP Tidak Cocok!',
                text: 'Gunakan nomor HP yang sama dengan saat login.',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    $tempat_lahir = htmlspecialchars($_POST['tempat_lahir']);
    $tanggal_lahir = htmlspecialchars($_POST['tanggal_lahir']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $nik_bpjs = isset($_POST['nik_bpjs']) ? htmlspecialchars($_POST['nik_bpjs']) : null;

    // Cek apakah pasien sudah terdaftar berdasarkan NIK
    $result = mysqli_query($conn, "SELECT id FROM pasien WHERE nik = '$nik'");
    if (mysqli_num_rows($result) > 0) {
        $pasien = mysqli_fetch_assoc($result);
        $pasien_id = $pasien['id'];

        echo "<script>
            Swal.fire({
                icon: 'info',
                title: 'Pasien Sudah Terdaftar',
                text: 'Pasien dengan NIK tersebut sudah ada, silakan daftar sebagai pasien lama!',
                 confirmButtonText: 'Kembali'
                }).then(() => {
                    window.history.back();
            });
        </script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO pasien (nama, nik, jenis_kelamin, no_hp, tempat_lahir, tanggal_lahir, alamat, nik_bpjs) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nama, $nik, $jenis_kelamin, $no_hp_form, $tempat_lahir, $tanggal_lahir, $alamat, $nik_bpjs);

        if ($stmt->execute()) {
            $pasien_id = $stmt->insert_id;
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil',
                    text: 'Silakan Menuju Klinik untuk Konfirmasi, karena anda merupakan Pasien Baru!',
                    confirmButtonText: 'Kembali'
                }).then(() => {
                    window.location.href = '../login/user/buat_kunjungan.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Melakukan Pendaftaran',
                    text: '" . addslashes($stmt->error) . "',
                    confirmButtonText: 'Kembali'
                }).then(() => {
                    window.history.back();
                });
            </script>";
        }
        $stmt->close();
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Data Tidak Lengkap',
            text: 'Mohon isi semua form dengan benar.',
            confirmButtonText: 'Kembali'
        }).then(() => {
            window.history.back();
        });
    </script>";
}

$conn->close();
echo "</body></html>";
?>