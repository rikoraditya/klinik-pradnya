<?php
$conn = mysqli_connect("localhost", "root", "", "klinik_pradnya");
echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

if (!$conn) {
    die("<script>
        Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Tidak bisa terhubung ke database!' });
    </script>");
}

if (isset($_POST['nik'], $_POST['tanggal_antrian'], $_POST['poli_tujuan'])) {
    $nik = $_POST['nik'];
    $tanggal_antrian = $_POST['tanggal_antrian'];
    $poli_tujuan = htmlspecialchars($_POST['poli_tujuan']);
    $status_antrian = "menunggu";

    // ✅ Cek apakah pasien sudah terdaftar
    $q = $conn->prepare("SELECT id FROM pasien WHERE nik = ?");
    $q->bind_param("s", $nik);
    $q->execute();
    $result = $q->get_result();

    if ($result->num_rows === 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Pasien Tidak Terdaftar',
                text: 'Silakan daftarkan pasien terlebih dahulu.',
                confirmButtonText: 'OK'
            }).then(() => { window.history.back(); });
        </script>";
        $q->close();
        $conn->close();
        exit;
    }

    // ✅ Ambil pasien_id
    $row = $result->fetch_assoc();
    $pasien_id = $row['id'];
    $q->close();

    // ✅ Cek apakah pasien sudah antri di tanggal yang sama
    $cek = $conn->prepare("SELECT id FROM antrian WHERE pasien_id = ? AND tanggal_antrian = ?");
    $cek->bind_param("is", $pasien_id, $tanggal_antrian);
    $cek->execute();
    $cek_result = $cek->get_result();
    if ($cek_result->num_rows > 0) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Sudah Terdaftar',
                text: 'Pasien ini sudah memiliki antrian di tanggal tersebut.',
                confirmButtonText: 'OK'
            }).then(() => { window.history.back(); });
        </script>";
        $cek->close();
        $conn->close();
        exit;
    }
    $cek->close();

    // ✅ Buat no_antrian baru
    $stmtNo = $conn->prepare("SELECT MAX(no_antrian) AS max_no FROM antrian WHERE tanggal_antrian = ?");
    $stmtNo->bind_param("s", $tanggal_antrian);
    $stmtNo->execute();
    $max = $stmtNo->get_result()->fetch_assoc();
    $last_no = $max['max_no'] ?? 'A-000';
    $angka = intval(substr($last_no, 2)) + 1;
    $no_antrian_baru = 'A-' . str_pad($angka, 3, '0', STR_PAD_LEFT);
    $stmtNo->close();

    // ✅ Insert ke antrian
    $stmt = $conn->prepare("INSERT INTO antrian (pasien_id, no_antrian, tanggal_antrian, poli_tujuan, status_antrian) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $pasien_id, $no_antrian_baru, $tanggal_antrian, $poli_tujuan, $status_antrian);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Antrian Berhasil',
                text: 'No Antrian Anda: $no_antrian_baru',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '../login/admin/crud/pasien/manage.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
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
            title: 'Data Tidak Lengkap',
            text: 'Mohon isi semua field yang dibutuhkan.',
            confirmButtonText: 'Kembali'
        }).then(() => {
            window.history.back();
        });
    </script>";
}

$conn->close();
echo "</body></html>";
?>