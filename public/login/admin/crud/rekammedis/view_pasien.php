<?php
require '../../../../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT 
                rekam_medis.*, 
                pasien.nama, 
                pasien.alamat, 
                pasien.jenis_kelamin, 
                pasien.no_hp, 
                pasien.tempat_lahir, 
                pasien.tanggal_lahir 
              FROM rekam_medis 
              JOIN pasien ON rekam_medis.nik = pasien.nik 
              WHERE rekam_medis.id = $id
              LIMIT 1";

    $result = query($query);

    if ($result && count($result) > 0) {
        echo json_encode($result[0]);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
} else {
    echo json_encode(["error" => "ID tidak valid atau tidak diberikan"]);
}
?>