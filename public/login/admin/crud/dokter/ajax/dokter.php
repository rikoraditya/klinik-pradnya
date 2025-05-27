<?php
use LDAP\Result;

require '../../../../../php/functions.php';

$keyword = $_GET["keyword"] ?? '';
$page = $_GET["halaman"] ?? 1;
$limit = 8;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM dokter 
    WHERE 
    nama LIKE '%$keyword%' OR
    poliklinik LIKE '%$keyword%' 
    LIMIT $limit OFFSET $offset";


$total_query = "SELECT COUNT(*) as total FROM dokter 
    WHERE 
    nama LIKE '%$keyword%' OR
    poliklinik LIKE '%$keyword%'";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);
$dokter = query($query);

$HalamanAktif = $page;
$JumlahHalaman = $total_pages;
?>

<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
            <th class="border p-2">ID Dokter</th>
            <th class="border p-2">Nama Dokter</th>
            <th class="border p-2">Poliklinik</th>
            <th class="border p-2">Action</th>
        </tr>
    </thead>
    <tbody class="text-xs">

        <?php $i = 1; ?>
        <?php foreach ($dokter as $row)
        : ?>

            <tr>
                <td class="border p-2 md w-10"><?= $i; ?></td>
                <td class="border p-2 w-60 md"><?= $row["id_dokter"]; ?></td>
                <td class="border p-2 truncate w-96 md">
                    <?= $row["nama"]; ?>
                </td>
                <td class="border p-2 truncate w-96 md"><?= $row["poliklinik"]; ?></td>
                <td class="border p-2">
                    <div class="flex justify-end space-x-1">
                        <a href="update.php?id=<?= $row['id']; ?>"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs inline-block">
                            Update
                        </a>

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