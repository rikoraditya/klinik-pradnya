<?php
require '../../../../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // keamanan tambahan
    $result = query("
        SELECT 
            kunjungan.id AS id_kunjungan,
            kunjungan.no_rm,
            pasien.nama,
            pasien.jenis_kelamin,
            pasien.no_hp,
            pasien.nik,
            pasien.tanggal_lahir,
            pasien.tempat_lahir,
            pasien.alamat,
            pasien.nik_bpjs,
            kunjungan.tanggal_kunjungan,
            kunjungan.poli_tujuan,
            kunjungan.keluhan,
            kunjungan.diagnosa,
            kunjungan.denyut_nadi,
            kunjungan.laju_pernapasan,
            dokter.nama AS nama_dokter,
            kunjungan.jenis_pasien,
            GROUP_CONCAT(CONCAT(obat.nama_obat, ' (', kunjungan_obat.jumlah, ') (', kunjungan_obat.dosis, ')') SEPARATOR ', ') AS detail_obat
        FROM kunjungan
        LEFT JOIN rekam_medis ON kunjungan.no_rm = rekam_medis.no_rm
        LEFT JOIN dokter ON kunjungan.dokter_id = dokter.id
        LEFT JOIN pasien ON rekam_medis.nik = pasien.nik
        LEFT JOIN kunjungan_obat ON kunjungan.id = kunjungan_obat.id_kunjungan
        LEFT JOIN obat ON kunjungan_obat.kode_obat = obat.kode_obat
        WHERE kunjungan.id = $id
        GROUP BY kunjungan.id
    ");

    if ($result && count($result) > 0) {
        $data = $result[0];

        // Ambil daftar obat lebih detail
        $id_kunjungan = $data['id_kunjungan'];
        $obat_result = query("
            SELECT 
                obat.nama_obat, 
                kunjungan_obat.dosis, 
                kunjungan_obat.jumlah
            FROM kunjungan_obat
            JOIN obat ON kunjungan_obat.kode_obat = obat.kode_obat
            WHERE kunjungan_obat.id_kunjungan = $id_kunjungan
        ");

        $data['obat_list'] = $obat_result;

        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
} else {
    echo json_encode(["error" => "ID tidak valid atau tidak diberikan"]);
}

?>