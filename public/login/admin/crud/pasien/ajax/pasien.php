<?php
use LDAP\Result;

require '../../../../../php/functions.php';


$keyword = $_GET["keyword"] ?? '';
$page = $_GET["halaman"] ?? 1;
$limit = 8;
$offset = ($page - 1) * $limit;

// Query data pasien dengan join ke kunjungan
$query = "SELECT 
    pasien.*, 
    antrian.no_antrian,
    kunjungan.tanggal_kunjungan, 
    kunjungan.poli_tujuan, 
    kunjungan.keluhan 
FROM pasien
LEFT JOIN kunjungan ON kunjungan.id = pasien.id
LEFT JOIN antrian ON antrian.pasien_id = pasien.id
WHERE 
    pasien.nama LIKE '%$keyword%' OR
    pasien.nik LIKE '%$keyword%' OR
    pasien.no_hp LIKE '%$keyword%'
GROUP BY pasien.id
ORDER BY pasien.id DESC
LIMIT $limit OFFSET $offset";


// Query total data
$total_query = "SELECT COUNT(DISTINCT pasien.id) as total 
FROM pasien
LEFT JOIN kunjungan ON kunjungan.id = pasien.id
WHERE 
    pasien.nama LIKE '%$keyword%' OR
    pasien.nik LIKE '%$keyword%' OR
    pasien.no_hp LIKE '%$keyword%'";

// Eksekusi query total
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

// Eksekusi query data pasien
$pasien = query($query);

// Baru bisa didefinisikan ini:
$HalamanAktif = $page;
$JumlahHalaman = $total_pages;

?>

<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
            <th class="border p-2">Nama</th>
            <th class="border p-2">NIK</th>
            <th class="border p-2">Jenis Kelamin</th>
            <th class="border p-2">No HP</th>
            <th class="border p-2">Tempat Lahir</th>
            <th class="border p-2">Tanggal Lahir</th>
            <th class="border p-2">Alamat</th>
            <th class="border p-2">NIK / No. BPJS</th>
            <th class="border p-2">Action</th>
        </tr>
    </thead>
    <tbody class="text-xs">

        <?php $i = 1; ?>
        <?php foreach ($pasien as $row): ?>

            <tr>
                <td class="border p-2 md"><?= $i; ?></td>
                <td class="border p-2 truncate w-20 md"><?= $row["nama"]; ?></td>
                <td class="border p-2 truncate w-20 md">
                    <?= strlen($row['nik']) > 13 ? substr($row['nik'], 0, 13) . '...' : $row["nik"]; ?>
                </td>
                <td class="border p-2 md"><?= $row["jenis_kelamin"]; ?></td>
                <td class="border p-2 truncate w-20 md"><?= $row["no_hp"]; ?></td>
                <td class="border p-2"><?= $row["tempat_lahir"]; ?></td>
                <td class="border p-2"><?= $row["tanggal_lahir"]; ?></td>
                <td class="border p-2 truncate w-20 md"><?= $row["alamat"]; ?></td>
                <td class="border p-2"><?= $row["nik_bpjs"]; ?></td>
                <td class="border p-2">
                    <div class="flex justify-end space-x-1">
                        <a href="create_kunjungan.php?id=<?= $row['id']; ?>"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs inline-block">
                            Create.K
                        </a>
                        <a href="update.php?id=<?= $row['id']; ?>"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs inline-block">
                            Update
                        </a>
                        <a href="delete.php?id=<?= $row['id']; ?>" id="delete-link"
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