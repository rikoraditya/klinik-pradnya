<?php
require '../../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = query("SELECT 
    antrian.id,
    antrian.no_antrian,
    pasien.nama,
    pasien.jenis_kelamin,
    pasien.no_hp,
    pasien.nik,
    pasien.tanggal_lahir,
    pasien.tempat_lahir,
    pasien.alamat,
    kunjungan.tanggal_kunjungan,
    kunjungan.keluhan,
    kunjungan.poli_tujuan,
    kunjungan.jenis_pasien,
    kunjungan.nik_bpjs,
    antrian.tanggal_antrian,
    antrian.status_antrian
  FROM antrian
  INNER JOIN pasien ON antrian.pasien_id = pasien.id
  INNER JOIN kunjungan ON kunjungan.id = pasien.id
  ");

    if ($result && count($result) > 0) {
        echo json_encode($result[0]);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
}
?>