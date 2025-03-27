<?php

//Konkesi ke Database
$conn = mysqli_connect("localhost", "root", "", "klinik");
function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Ambil jumlah pasien untuk setiap poli
$query_poli_umum = "SELECT COUNT(*) AS total FROM pasien WHERE poli_tujuan = 'Poli Umum'";
$query_poli_gigi = "SELECT COUNT(*) AS total FROM pasien WHERE poli_tujuan = 'Poli Gigi'";

// Eksekusi query
$result_poli_umum = mysqli_query($conn, $query_poli_umum);
$result_poli_gigi = mysqli_query($conn, $query_poli_gigi);

// Ambil hasil jumlah pasien
$poli_umum = mysqli_fetch_assoc($result_poli_umum)['total'];
$poli_gigi = mysqli_fetch_assoc($result_poli_gigi)['total'];

// Jika tombol "Panggil" ditekan
if (isset($_POST['panggil'])) {
    $id = $_POST['id'];
    $query = "UPDATE pasien SET status_antrian = 'Dipanggil' WHERE id = $id";
    mysqli_query($conn, $query);
    header("Location: ../login/admin/dashboard.php"); // Refresh halaman
    exit();
}

// Jika tombol "Selesai" ditekan
if (isset($_POST['selesai'])) {
    $id = $_POST['id'];
    $query = "UPDATE pasien SET status_antrian = 'Selesai' WHERE id = $id";
    mysqli_query($conn, $query);
    header("Location: ../login/admin/dashboard.php"); // Refresh halaman
    exit();
}

// Ambil data pasien dari database
$result = mysqli_query($conn, "SELECT * FROM pasien ORDER BY no_antrian ASC");
$pasien = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>