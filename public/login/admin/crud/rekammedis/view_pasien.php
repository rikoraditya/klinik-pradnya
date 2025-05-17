<?php
require '../../../../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data rekam medis + pasien + kunjungan
    $query = "SELECT 
                rekam_medis.*, 
                pasien.nama, 
                pasien.alamat, 
                pasien.jenis_kelamin, 
                pasien.no_hp, 
                pasien.tempat_lahir, 
                pasien.tanggal_lahir,
                kunjungan.id AS id_kunjungan,
                kunjungan.tanggal_kunjungan,
                kunjungan.keluhan,
                kunjungan.poli_tujuan,
                kunjungan.jenis_pasien,
                kunjungan.dokter,
                kunjungan.nik_bpjs,
                kunjungan.denyut_nadi,
                kunjungan.laju_pernapasan,
                kunjungan.diagnosa
              FROM rekam_medis 
              JOIN pasien ON rekam_medis.nik = pasien.nik
              JOIN kunjungan ON rekam_medis.no_rm = kunjungan.no_rm
              WHERE rekam_medis.id = $id
              LIMIT 1";

    $result = query($query);

    if ($result && count($result) > 0) {
        $data = $result[0];

        // Ambil daftar obat untuk kunjungan tersebut
        $id_kunjungan = $data['id_kunjungan'];
        $query_obat = "SELECT 
                          obat.nama_obat, 
                          kunjungan_obat.dosis, 
                          kunjungan_obat.jumlah
                       FROM kunjungan_obat
                       JOIN obat ON kunjungan_obat.kode_obat = obat.kode_obat
                       WHERE kunjungan_obat.id_kunjungan = $id_kunjungan";

        $obat_result = query($query_obat);
        $data['obat_list'] = $obat_result;

        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
} else {
    echo json_encode(["error" => "ID tidak valid atau tidak diberikan"]);
}
?>