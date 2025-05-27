<?php
require '../../../../../php/functions.php';

$keyword = $_GET["keyword"] ?? '';
$page = $_GET["halaman"] ?? 1;
$limit = 8;
$offset = ($page - 1) * $limit;

// Ambil data dari tabel kunjungan dan relasi lainnya
$query = "SELECT 
    k.id,
    k.no_rm,
    k.tanggal_kunjungan,
    k.keluhan,
    k.poli_tujuan,
    d.nama AS nama_dokter,
    p.nik,
    p.nama AS nama_pasien,
    p.tanggal_lahir
  FROM kunjungan k
  LEFT JOIN rekam_medis rm ON k.no_rm = rm.no_rm
  LEFT JOIN pasien p ON rm.nik = p.nik
  LEFT JOIN dokter d ON k.dokter_id = d.id
  WHERE 
    k.no_rm LIKE '%$keyword%' OR
    p.nama LIKE '%$keyword%' OR
    p.nik LIKE '%$keyword%' OR
    k.keluhan LIKE '%$keyword%' OR
    k.poli_tujuan LIKE '%$keyword%'
  ORDER BY CAST(SUBSTRING(k.no_rm, 3) AS UNSIGNED) DESC
  LIMIT $limit OFFSET $offset";


// Total data
$total_query = "SELECT COUNT(*) as total
  FROM kunjungan k
  LEFT JOIN rekam_medis rm ON k.no_rm = rm.no_rm
  LEFT JOIN pasien p ON rm.nik = p.nik
  WHERE 
    k.no_rm LIKE '%$keyword%' OR
    p.nama LIKE '%$keyword%' OR
    p.nik LIKE '%$keyword%' OR
    k.keluhan LIKE '%$keyword%' OR
    k.poli_tujuan LIKE '%$keyword%'";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

$kunjungan = query($query);

// Baru bisa didefinisikan ini:
$HalamanAktif = $page;
$JumlahHalaman = $total_pages;
?>


<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
            <th class="border p-2">No RM</th>
            <th class="border p-2">NIK</th>
            <th class="border p-2">Nama</th>
            <th class="border p-2">Tanggal Kunjungan</th>
            <th class="border p-2">Tanggal Lahir</th>
            <th class="border p-2">Keluhan</th>
            <th class="border p-2">Poli Tujuan</th>
            <th class="border p-2">Action</th>
        </tr>
    </thead>
    <tbody class="text-xs">
        <?php $i = 1; ?>
        <?php foreach ($kunjungan as $row): ?>
            <tr>
                <td class="border p-2"><?= $i; ?></td>
                <td class="border p-2"><?= $row["no_rm"]; ?></td>
                <td class="border p-2"><?= $row["nik"]; ?></td>
                <td class="border p-2"><?= $row["nama_pasien"]; ?></td>
                <td class="border p-2"><?= $row["tanggal_kunjungan"]; ?></td>
                <td class="border p-2"><?= $row["tanggal_lahir"]; ?></td>
                <td class="border p-2">
                    <?= strlen($row["keluhan"]) > 20 ? substr($row["keluhan"], 0, 20) . '...' : $row["keluhan"]; ?>
                </td>
                <td class="border p-2"><?= $row["poli_tujuan"]; ?></td>

                <td class="border p-2">
                    <div class="flex justify-end space-x-1">
                        <button onclick="lihatPasien('<?= $row['id']; ?>')"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-poppins px-2 py-1 rounded text-xs">
                            <i class="fas fa-eye mr-1"></i><span class="">View</span>
                        </button>
                        <a href="update.php?id=<?= $row['id']; ?>"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs inline-block">Update</a>
                        <a href="#" onclick="konfirmasiHapus(<?= $row['id']; ?>)"
                            class="delete-link bg-red-700 hover:bg-red-900 text-white px-2 py-1 rounded text-xs inline-block">
                            Delete
                        </a>

                    </div>
                </td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
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