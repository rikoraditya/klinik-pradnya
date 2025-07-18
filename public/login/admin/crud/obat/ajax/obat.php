<?php
use LDAP\Result;

require '../../../../../php/functions.php';


$keyword = $_GET["keyword"] ?? '';
$page = $_GET["halaman"] ?? 1;
$limit = 8;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM obat
    WHERE 
    nama_obat LIKE '%$keyword%' OR
    jenis_obat LIKE '%$keyword%'
    LIMIT $limit OFFSET $offset";


$total_query = "SELECT COUNT(*) as total FROM obat 
    WHERE 
    nama_obat LIKE '%$keyword%' OR
    jenis_obat LIKE '%$keyword%'";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);
$obat = query($query);

$HalamanAktif = $page;
$JumlahHalaman = $total_pages;
?>

<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
            <th class="border p-2">Kode Obat</th>
            <th class="border p-2">Nama Obat</th>
            <th class="border p-2">Jenis Obat</th>
            <th class="border p-2">Dosis</th>
            <th class="border p-2">Keterangan</th>

        </tr>
    </thead>
    <tbody class="text-xs">

        <?php $i = 1; ?>
        <?php foreach ($obat as $row)
        : ?>

            <tr>
                <td class="border p-2 md w-10"><?= $i; ?></td>
                <td class="border p-2 md w-40"><?= $row["kode_obat"]; ?></td>
                <td class="border p-2 truncate md w-60"><?= $row["nama_obat"]; ?></td>
                <td class="border p-2 truncate md w-40"><?= $row["jenis_obat"]; ?></td>
                <td class="border p-2 truncate md w-80"><?= $row["dosis"]; ?></td>
                <td class="border p-2 truncate md w-80"><?= $row["keterangan"]; ?></td>

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