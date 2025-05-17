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
    $_POST['nik'],
    $_POST['tanggal_kunjungan'],
    $_POST['keluhan'],
    $_POST['poli_tujuan'],
    $_POST['jenis_pasien'],
    $_POST['dokter'],
    $_POST['nik_bpjs'],
    $_POST['denyut_nadi'],
    $_POST['laju_pernapasan'],
    $_POST['diagnosa'],
    $_POST['kode_obat'],
    $_POST['dosis'],
    $_POST['jumlah']
)
) {
    $nik = htmlspecialchars($_POST['nik']);
    $tanggal_kunjungan = htmlspecialchars($_POST['tanggal_kunjungan']);
    $keluhan = htmlspecialchars($_POST['keluhan']);
    $poli_tujuan = htmlspecialchars($_POST['poli_tujuan']);
    $jenis_pasien = htmlspecialchars($_POST['jenis_pasien']);
    $dokter = htmlspecialchars($_POST['dokter']);
    $nik_bpjs = htmlspecialchars($_POST['nik_bpjs']);
    $denyut_nadi = htmlspecialchars($_POST['denyut_nadi']);
    $laju_pernapasan = htmlspecialchars($_POST['laju_pernapasan']);
    $diagnosa = htmlspecialchars($_POST['diagnosa']);

    // Data obat multi
    $kode_obat = $_POST['kode_obat']; // array
    $dosis_obat = $_POST['dosis'];    // array
    $jumlah_obat = $_POST['jumlah'];  // array

    // 1. Cek apakah pasien sudah punya no_rm
    $rm_query = "SELECT no_rm FROM rekam_medis WHERE nik = ?";
    $stmt_rm = $conn->prepare($rm_query);
    $stmt_rm->bind_param("s", $nik);
    $stmt_rm->execute();
    $result_rm = $stmt_rm->get_result();

    if ($result_rm->num_rows > 0) {
        $row_rm = $result_rm->fetch_assoc();
        $no_rm = $row_rm['no_rm'];
    } else {
        // Generate no_rm baru yang benar-benar unik
        $query_last_rm = "SELECT MAX(CAST(SUBSTRING(no_rm, 3) AS UNSIGNED)) AS max_rm FROM rekam_medis";
        $result_last_rm = mysqli_query($conn, $query_last_rm);
        $last_rm = mysqli_fetch_assoc($result_last_rm);
        $last_number = ($last_rm && $last_rm['max_rm']) ? intval($last_rm['max_rm']) : 0;
        $new_number = $last_number + 1;
        $no_rm = "RM" . str_pad($new_number, 5, "0", STR_PAD_LEFT);

        // Insert ke rekam_medis (hanya kolom yang ada: no_rm, nik)
        $insert_rm = $conn->prepare("INSERT INTO rekam_medis (no_rm, nik) VALUES (?, ?)");
        if (!$insert_rm) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuat Rekam Medis!',
                    text: 'Query error: " . addslashes($conn->error) . "',
                    confirmButtonText: 'Kembali'
                }).then(() => {
                    window.history.back();
                });
            </script>";
            $conn->close();
            exit;
        }
        $insert_rm->bind_param("ss", $no_rm, $nik);
        if (!$insert_rm->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuat Rekam Medis!',
                    text: '" . addslashes($insert_rm->error) . "',
                    confirmButtonText: 'Kembali'
                }).then(() => {
                    window.history.back();
                });
            </script>";
            $insert_rm->close();
            $conn->close();
            exit;
        }
        $insert_rm->close();
    }
    $stmt_rm->close();

    // Cek apakah sudah ada kunjungan dengan no_rm dan tanggal_kunjungan yang sama
    $cek_kunjungan = $conn->prepare("SELECT COUNT(*) as total FROM kunjungan WHERE no_rm = ? AND tanggal_kunjungan = ?");
    $cek_kunjungan->bind_param("ss", $no_rm, $tanggal_kunjungan);
    $cek_kunjungan->execute();
    $result_cek = $cek_kunjungan->get_result();
    $cek_data = $result_cek->fetch_assoc();
    $jumlah = $cek_data['total'];
    $cek_kunjungan->close();

    if ($jumlah > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Data Sudah Ada!',
                text: 'Kunjungan dengan No RM ini sudah ada di tanggal tersebut.',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.history.back();
            });
        </script>";
    } else {
        // Insert ke tabel kunjungan (TANPA kolom obat)
        $insert_kunjungan = $conn->prepare("INSERT INTO kunjungan 
            (no_rm, tanggal_kunjungan, keluhan, poli_tujuan, jenis_pasien, dokter, nik_bpjs, denyut_nadi, laju_pernapasan, diagnosa)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_kunjungan->bind_param(
            "ssssssssss",
            $no_rm,
            $tanggal_kunjungan,
            $keluhan,
            $poli_tujuan,
            $jenis_pasien,
            $dokter,
            $nik_bpjs,
            $denyut_nadi,
            $laju_pernapasan,
            $diagnosa
        );

        if ($insert_kunjungan->execute()) {
            // Dapatkan id_kunjungan yang baru saja diinsert
            $id_kunjungan = $conn->insert_id;

            // Insert ke tabel kunjungan_obat untuk setiap obat yang dipilih
            for ($i = 0; $i < count($kode_obat); $i++) {
                $kode = htmlspecialchars($kode_obat[$i]);
                $dosis = htmlspecialchars($dosis_obat[$i]);
                $jumlah_o = intval($jumlah_obat[$i]);
                if ($kode && $dosis && $jumlah_o > 0) {
                    // Perhatikan urutan dan tipe data: id_kunjungan (int), kode_obat (string), jumlah (int), dosis (string)
                    $insert_kunjungan_obat = $conn->prepare("INSERT INTO kunjungan_obat (id_kunjungan, kode_obat, jumlah, dosis) VALUES (?, ?, ?, ?)");
                    if (!$insert_kunjungan_obat) {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Query Error!',
                                text: 'Query error: " . addslashes($conn->error) . "',
                                confirmButtonText: 'Kembali'
                            }).then(() => {
                                window.history.back();
                            });
                        </script>";
                        $conn->close();
                        exit;
                    }
                    $insert_kunjungan_obat->bind_param("isis", $id_kunjungan, $kode, $jumlah_o, $dosis);
                    if (!$insert_kunjungan_obat->execute()) {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan Data Obat!',
                                text: '" . addslashes($insert_kunjungan_obat->error) . "',
                                confirmButtonText: 'Kembali'
                            }).then(() => {
                                window.history.back();
                            });
                        </script>";
                        $insert_kunjungan_obat->close();
                        $conn->close();
                        exit;
                    }
                    $insert_kunjungan_obat->close();
                }
            }

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Data Kunjungan Pasien Dibuat',
                    confirmButtonText: 'Kembali'
                }).then(() => {
                    window.location.href = 'tambah.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuat Data Kunjungan!',
                    text: '" . addslashes($insert_kunjungan->error) . "',
                    confirmButtonText: 'Kembali'
                }).then(() => {
                    window.history.back();
                });
            </script>";
        }
        $insert_kunjungan->close();
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