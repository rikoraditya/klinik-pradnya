<?php
use LDAP\Result;

require '../../../../../php/functions.php';


$keyword = $_GET["keyword"] ?? '';
$page = $_GET["page"] ?? 1;
$limit = 5; // jumlah data per halaman
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
            <th class="border p-2">Action</th>
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
                <td class="border p-2">
                    <div class="flex justify-end space-x-1">
                        <a href="update.php?id=<?= $row['id']; ?>"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs inline-block">
                            Update
                        </a>

                        <a href="delete.php?id=<?= $row['id']; ?>"
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