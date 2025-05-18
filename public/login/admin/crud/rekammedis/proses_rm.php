<?php
$conn = mysqli_connect("localhost", "root", "", "klinik_pradnya");
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
    $_POST['diagnosa']
)
) {
    // Ambil data dari form
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
    $dokter_nama = htmlspecialchars($_POST['dokter']);
    $nik_bpjs = htmlspecialchars($_POST['nik_bpjs']);
    $denyut_nadi = htmlspecialchars($_POST['denyut_nadi']);
    $laju_pernapasan = htmlspecialchars($_POST['laju_pernapasan']);
    $diagnosa = htmlspecialchars($_POST['diagnosa']);

    // Cek apakah pasien ada
    $cek_pasien = mysqli_query($conn, "SELECT nik FROM pasien WHERE nik = '$nik'");
    if (mysqli_num_rows($cek_pasien) == 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Pasien Tidak Terdaftar!',
                text: 'Silakan daftarkan pasien terlebih dahulu.',
                confirmButtonText: 'Kembali'
            }).then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // Cek apakah sudah punya no_rm
    $rm_query = "SELECT no_rm FROM rekam_medis WHERE nik = '$nik' LIMIT 1";
    $rm_result = mysqli_query($conn, $rm_query);

    if (mysqli_num_rows($rm_result) > 0) {
        $row_rm = mysqli_fetch_assoc($rm_result);
        $no_rm = $row_rm['no_rm'];
    } else {
        // Buat no_rm baru
        $query_rm = mysqli_query($conn, "SELECT no_rm FROM rekam_medis ORDER BY id DESC LIMIT 1");
        $last_rm = mysqli_fetch_assoc($query_rm);
        $last_number = ($last_rm) ? intval(substr($last_rm['no_rm'], 2)) : 0;
        $new_number = $last_number + 1;
        $no_rm = "RM" . str_pad($new_number, 3, "0", STR_PAD_LEFT);

        // Simpan ke rekam_medis
        $stmt_rm = $conn->prepare("INSERT INTO rekam_medis (no_rm, nik) VALUES (?, ?)");
        if (!$stmt_rm) {
            die("Prepare failed (rekam_medis): " . $conn->error);
        }
        $stmt_rm->bind_param("ss", $no_rm, $nik);
        $stmt_rm->execute();
        $stmt_rm->close();
    }

    // Ambil dokter_id dari nama
    $dokter_result = mysqli_query($conn, "SELECT id FROM dokter WHERE nama = '$dokter_nama' LIMIT 1");
    if (!$dokter_result || mysqli_num_rows($dokter_result) == 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Dokter tidak ditemukan!',
                text: 'Pastikan dokter terdaftar.',
                confirmButtonText: 'Kembali'
            }).then(() => { window.history.back(); });
        </script>";
        exit;
    }
    $dokter_row = mysqli_fetch_assoc($dokter_result);
    $dokter_id = $dokter_row['id'];

    // Cek duplikat kunjungan
    $cek_sql = "SELECT COUNT(*) as total FROM kunjungan WHERE no_rm = '$no_rm' AND tanggal_kunjungan = '$tanggal_kunjungan'";
    $cek_result = mysqli_query($conn, $cek_sql);
    $jumlah = mysqli_fetch_assoc($cek_result)['total'];

    if ($jumlah > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Data Sudah Ada!',
                text: 'Pasien sudah memiliki kunjungan di tanggal tersebut.',
                confirmButtonText: 'Kembali'
            }).then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // Simpan data kunjungan
    $sql = "INSERT INTO kunjungan (
        no_rm, tanggal_kunjungan, keluhan, poli_tujuan,
        jenis_pasien, dokter_id, nik_bpjs, denyut_nadi,
        laju_pernapasan, diagnosa
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed (kunjungan): " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssss",
        $no_rm,
        $tanggal_kunjungan,
        $keluhan,
        $poli_tujuan,
        $jenis_pasien,
        $dokter_id,
        $nik_bpjs,
        $denyut_nadi,
        $laju_pernapasan,
        $diagnosa
    );

    if ($stmt->execute()) {
        $kunjungan_id = $stmt->insert_id;

        // Simpan detail obat
        if (!empty($_POST['obat']) && is_array($_POST['obat'])) {
            foreach ($_POST['obat'] as $i => $kode_obat) {
                $jumlah = $_POST['jumlah'][$i];
                $dosis = $_POST['dosis'][$i];

                $obat_stmt = $conn->prepare("INSERT INTO kunjungan_obat (id_kunjungan, kode_obat, jumlah, dosis) VALUES (?, ?, ?, ?)");
                if (!$obat_stmt) {
                    die("Prepare failed (obat): " . $conn->error);
                }
                $obat_stmt->bind_param("isis", $kunjungan_id, $kode_obat, $jumlah, $dosis);
                $obat_stmt->execute();
                $obat_stmt->close();
            }
        }

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Data kunjungan tersimpan!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'tambah.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal menyimpan kunjungan!',
                text: '" . addslashes($stmt->error) . "',
                confirmButtonText: 'Kembali'
            }).then(() => { window.history.back(); });
        </script>";
    }

    $stmt->close();
} else {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Form tidak lengkap!',
            text: 'Pastikan semua input sudah diisi.',
            confirmButtonText: 'Kembali'
        }).then(() => { window.history.back(); });
    </script>";
}

$conn->close();
echo "</body></html>";
?>