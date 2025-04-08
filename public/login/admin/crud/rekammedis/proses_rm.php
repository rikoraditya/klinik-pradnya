<?php
$conn = mysqli_connect("localhost", "root", "", "klinik");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

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
    $_POST['dokter'],
    $_POST['nik_bpjs'],
    $_POST['denyut_nadi'],
    $_POST['laju_pernapasan'],
    $_POST['obat'],
    $_POST['diagnosa']
)
) {
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
    $dokter = htmlspecialchars($_POST['dokter']);
    $nik_bpjs = htmlspecialchars($_POST['nik_bpjs']);
    $denyut_nadi = htmlspecialchars($_POST['denyut_nadi']);
    $laju_pernapasan = htmlspecialchars($_POST['laju_pernapasan']);
    $obat = htmlspecialchars($_POST['obat']);
    $diagnosa = htmlspecialchars($_POST['diagnosa']);

    // Cek apakah data dengan NIK dan tanggal_kunjungan yang sama sudah ada
    $cek_sql = "SELECT COUNT(*) as total FROM rekam_medis WHERE nik = '$nik' AND tanggal_kunjungan = '$tanggal_kunjungan'";
    $cek_result = mysqli_query($conn, $cek_sql);
    $cek_data = mysqli_fetch_assoc($cek_result);
    $jumlah = $cek_data['total'];

    if ($jumlah > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Data Sudah Ada!',
                text: 'Pasien dengan NIK ini sudah memiliki rekam medis di tanggal tersebut.',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.history.back();
            });
        </script>";
    } else {
        // Cek apakah pasien ini sudah punya no_rm
        $rm_query = "SELECT no_rm FROM rekam_medis WHERE nik = '$nik' LIMIT 1";
        $rm_result = mysqli_query($conn, $rm_query);
        if (mysqli_num_rows($rm_result) > 0) {
            $row_rm = mysqli_fetch_assoc($rm_result);
            $no_rm = $row_rm['no_rm'];
        } else {
            // Generate no_rm baru
            $query_rm = mysqli_query($conn, "SELECT no_rm FROM rekam_medis ORDER BY id DESC LIMIT 1");
            $last_rm = mysqli_fetch_assoc($query_rm);
            $last_number = ($last_rm) ? intval(substr($last_rm['no_rm'], 2)) : 0;
            $new_number = $last_number + 1;
            $no_rm = "RM" . str_pad($new_number, 5, "0", STR_PAD_LEFT);
        }

        // Insert data baru
        $sql = "INSERT INTO rekam_medis (nama, nik, jenis_kelamin, no_hp, tempat_lahir, tanggal_lahir, alamat, tanggal_kunjungan, keluhan, poli_tujuan, jenis_pasien, dokter, nik_bpjs, denyut_nadi, laju_pernapasan, obat, diagnosa, no_rm) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssssssss", $nama, $nik, $jenis_kelamin, $no_hp, $tempat_lahir, $tanggal_lahir, $alamat, $tanggal_kunjungan, $keluhan, $poli_tujuan, $jenis_pasien, $dokter, $nik_bpjs, $denyut_nadi, $laju_pernapasan, $obat, $diagnosa, $no_rm);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Data Rekam Medis Pasien Dibuat',
                    confirmButtonText: 'Kembali'
                }).then(() => {
                    window.location.href = 'tambah.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuat Rekam Medis Pasien!',
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
            title: 'Data Tidak Lengkap!',
            text: 'Silakan isi semua form terlebih dahulu.',
            confirmButtonText: 'Kembali'
        }).then(() => {
            window.history.back();
        });
    </script>";
}

$conn->close();
echo "</body></html>";
?>