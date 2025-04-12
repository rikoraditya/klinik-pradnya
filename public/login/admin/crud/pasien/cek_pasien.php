<?php
require '../../../../php/functions.php';


echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";

if (isset($_POST['nik_cari'])) {
    $cari = $_POST['nik_cari'];

    $query = $conn->prepare("SELECT * FROM rekam_medis WHERE nik = ?");
    $query->bind_param("s", $cari);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        // Simpan ke session atau tampilkan form isian otomatis
        session_start();
        $_SESSION['pasien_lama'] = $data;
        header("Location: isi_rm.php");
    } else {
        echo "<script> 
        Swal.fire({
            icon: 'error',
            title: 'No. RM Tidak Ditemukan!',
            html: 'Silahkan melakukan pendaftaran sebagai <strong>Pasien Baru</strong>',
            confirmButtonText: 'Lanjutkan'
        }).then(() => {
            window.location.href = 'registrasi.php';
        });
    </script>";
    }
}

echo "</body></html>";
?>