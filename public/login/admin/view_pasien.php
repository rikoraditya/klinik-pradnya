<?php
require '../../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $result = query("
        SELECT 
            a.id,
            a.no_antrian,
            a.tanggal_antrian,
            a.poli_tujuan,
            a.status_antrian,

            p.nama,
            p.jenis_kelamin,
            p.no_hp,
            p.nik,
            p.tanggal_lahir,
            p.tempat_lahir,
            p.alamat,

            k.tanggal_kunjungan,
            k.keluhan,
            k.jenis_pasien,
            k.nik_bpjs

        FROM antrian a
        INNER JOIN pasien p ON a.pasien_id = p.id
        LEFT JOIN rekam_medis rm ON p.nik = rm.nik
        LEFT JOIN kunjungan k ON k.no_rm = rm.no_rm
        WHERE a.id = $id
        ORDER BY k.tanggal_kunjungan DESC
        LIMIT 1
    ");

    if ($result && count($result) > 0) {
        echo json_encode($result[0]);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
}

?>