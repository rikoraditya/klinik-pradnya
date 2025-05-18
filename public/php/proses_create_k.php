<?php
$conn = mysqli_connect("localhost", "root", "", "klinik_pradnya");
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

echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

if (
    isset($_POST['nama'], $_POST['nik'], $_POST['jenis_kelamin'], $_POST['no_hp'], $_POST['tempat_lahir'], $_POST['tanggal_lahir'], $_POST['alamat'])
) {
    $nama = htmlspecialchars($_POST['nama']);
    $nik = htmlspecialchars($_POST['nik']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $no_hp = preg_replace('/[^0-9]/', '', $_POST['no_hp']);
    if (substr($no_hp, 0, 1) === '0') {
        $no_hp = '62' . substr($no_hp, 1);
    }
    $tempat_lahir = htmlspecialchars($_POST['tempat_lahir']);
    $tanggal_lahir = htmlspecialchars($_POST['tanggal_lahir']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $nik_bpjs = isset($_POST['nik_bpjs']) ? htmlspecialchars($_POST['nik_bpjs']) : null;

    $result = mysqli_query($conn, "SELECT id FROM pasien WHERE nik = '$nik'");
    if (mysqli_num_rows($result) > 0) {
        $pasien = mysqli_fetch_assoc($result);
        $pasien_id = $pasien['id'];

        echo "<script>
            Swal.fire({
                icon: 'info',
                title: 'Pasien Sudah Terdaftar',
                text: 'Pasien dengan NIK tersebut sudah ada.',
                confirmButtonText: 'Lanjut Buat Antrian'
            }).then(() => {
                window.location.href = 'form_antrian.php?pasien_id=$pasien_id';
            });
        </script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO pasien (nama, nik, jenis_kelamin, no_hp, tempat_lahir, tanggal_lahir, alamat, nik_bpjs) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nama, $nik, $jenis_kelamin, $no_hp, $tempat_lahir, $tanggal_lahir, $alamat, $nik_bpjs);

        if ($stmt->execute()) {
            $pasien_id = $stmt->insert_id;
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Mendaftarkan Pasien',
                    confirmButtonText: 'Buat Antrian'
                }).then(() => {
                    window.location.href = '../login/admin/crud/pasien/manage.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mendaftar Pasien',
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