<?php
session_start();
require '../../../php/functions.php';

if (!isset($_SESSION["login_user"])) {
    header("location:../user_login.php");
    exit;
}

$no_hp_login = $_SESSION['no_hp'];
$pasien_data = query("SELECT nik FROM pasien WHERE no_hp = '$no_hp_login' LIMIT 1");
if (!$pasien_data) {
    die("Data pasien tidak ditemukan.");
}
$nik_login = $pasien_data[0]['nik'];


$keyword = $_GET["keyword"] ?? '';
$page = $_GET["halaman"] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;



// Query utama untuk data kunjungan
$query = "
    SELECT 
        k.id,
        k.no_rm,
        k.tanggal_kunjungan,
        k.keluhan,
        k.poli_tujuan,
        d.nama AS nama_dokter,
        p.nik,
        p.nama AS nama_pasien,
        p.tanggal_lahir,
        p.jenis_kelamin,
        p.no_hp,
        p.tempat_lahir,
        p.alamat,
        p.nik_bpjs
    FROM kunjungan k
    LEFT JOIN rekam_medis rm ON k.no_rm = rm.no_rm
    LEFT JOIN pasien p ON rm.nik = p.nik
    LEFT JOIN dokter d ON k.dokter_id = d.id
    WHERE 
        p.no_hp = '$no_hp_login' AND
        p.nik = '$nik_login' AND (
            k.no_rm LIKE '%$keyword%' OR
            p.nama LIKE '%$keyword%' OR
            p.nik LIKE '%$keyword%' OR
            k.keluhan LIKE '%$keyword%' OR
            k.poli_tujuan LIKE '%$keyword%'
        )
    ORDER BY k.tanggal_kunjungan DESC
    LIMIT $limit OFFSET $offset";

// Total query untuk pagination
$total_query = "
    SELECT COUNT(*) as total
    FROM kunjungan k
    LEFT JOIN rekam_medis rm ON k.no_rm = rm.no_rm
    LEFT JOIN pasien p ON rm.nik = p.nik
    WHERE 
        p.no_hp = '$no_hp_login' AND
        p.nik = '$nik_login' AND (
            k.no_rm LIKE '%$keyword%' OR
            p.nama LIKE '%$keyword%' OR
            p.nik LIKE '%$keyword%'
        )
";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

$kunjungan = query($query);


$HalamanAktif = $page;
$JumlahHalaman = $total_pages;

// Ambil semua ID kunjungan yang muncul
$id_kunjungan_list = array_column($kunjungan, 'id');
$obat_per_kunjungan = [];

if (!empty($id_kunjungan_list)) {
    $id_kunjungan_in = implode(',', array_map('intval', $id_kunjungan_list));

    $obat_rows = query("
        SELECT ko.id_kunjungan, o.nama_obat, ko.dosis, ko.jumlah
        FROM kunjungan_obat ko
        JOIN obat o ON ko.kode_obat = o.kode_obat
        WHERE ko.id_kunjungan IN ($id_kunjungan_in)
        ORDER BY ko.id_kunjungan, o.nama_obat
    ");

    foreach ($obat_rows as $obat_row) {
        $idk = $obat_row['id_kunjungan'];
        if (!isset($obat_per_kunjungan[$idk]))
            $obat_per_kunjungan[$idk] = [];
        $obat_per_kunjungan[$idk][] = $obat_row;
    }
}

// Tombol cari berdasarkan no_rm
if (isset($_POST["cari_rm"])) {
    $keyword = htmlspecialchars($_POST["keyword"]);
    $kunjungan = query("
        SELECT 
            k.id,
            k.no_rm,
            k.tanggal_kunjungan,
            k.keluhan,
            k.poli_tujuan,
            d.nama AS nama_dokter,
            p.nik,
            p.nama AS nama_pasien,
            p.tanggal_lahir,
            p.jenis_kelamin,
            p.no_hp,
            p.tempat_lahir,
            p.alamat,
            p.nik_bpjs
        FROM kunjungan k
        LEFT JOIN rekam_medis rm ON k.no_rm = rm.no_rm
        LEFT JOIN pasien p ON rm.nik = p.nik
        LEFT JOIN dokter d ON k.dokter_id = d.id
        WHERE k.no_rm LIKE '%$keyword%' 
          AND p.no_hp = '$no_hp_login' 
          AND p.nik = '$nik_login'
        ORDER BY k.tanggal_kunjungan DESC
    ");

    $id_kunjungan_list = array_column($kunjungan, 'id');
    $obat_per_kunjungan = [];

    if (!empty($id_kunjungan_list)) {
        $id_kunjungan_in = implode(',', array_map('intval', $id_kunjungan_list));

        $obat_rows = query("
            SELECT ko.id_kunjungan, o.nama_obat, ko.dosis, ko.jumlah
            FROM kunjungan_obat ko
            JOIN obat o ON ko.kode_obat = o.kode_obat
            WHERE ko.id_kunjungan IN ($id_kunjungan_in)
            ORDER BY ko.id_kunjungan, o.nama_obat
        ");

        foreach ($obat_rows as $obat_row) {
            $idk = $obat_row['id_kunjungan'];
            if (!isset($obat_per_kunjungan[$idk]))
                $obat_per_kunjungan[$idk] = [];
            $obat_per_kunjungan[$idk][] = $obat_row;
        }
    }
}
?>



<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
            <th class="border p-2">No RM</th>

            <th class="border p-2">Tanggal Kunjungan</th>
            <th class="border p-2">Action</th>
        </tr>
    </thead>
    <tbody class="text-xs">
        <?php if (empty($kunjungan)): ?>
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500 text-sm">
                    Anda belum memiliki rekam medis<br>
                    Silakan datang ke klinik untuk berkonsultasi jika anda sudah melakukan pendaftaran.
                </td>
            </tr>
        <?php else: ?>
            <?php $i = 1; ?>
            <?php foreach ($kunjungan as $row): ?>
                <tr>
                    <td class="border p-2"><?= $i; ?></td>
                    <td class="border p-2"><?= $row["no_rm"]; ?></td>

                    <td class="border p-2 w-24"><?= $row["tanggal_kunjungan"]; ?></td>
                    <td class="border p-2">
                        <div class="flex justify-end">
                            <button onclick="lihatPasien('<?= $row['id']; ?>')"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-poppins px-2 py-1 rounded text-xs">
                                <i class="fas fa-eye mr-1"></i><span class="">View</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- PAGINATION AJAX -->
<div class="pagination text-xs font-poppins mt-2 ml-1 text-gray-500">
    <?php if ($page > 1): ?>
        <button class="px-2" data-page="<?= $page - 1 ?>">&laquo;</button>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <button class="px-2 <?= $i == $page ? 'font-bold text-green-950' : '' ?>" data-page="<?= $i ?>">
            <?= $i ?>
        </button>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <button class="px-2" data-page="<?= $page + 1 ?>">&raquo;</button>
    <?php endif; ?>
</div>