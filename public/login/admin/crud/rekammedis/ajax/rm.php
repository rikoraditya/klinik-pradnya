<?php
use LDAP\Result;

require '../../../../../php/functions.php';


$keyword = $_GET["keyword"] ?? '';
$page = $_GET["page"] ?? 1;
$limit = 5; // jumlah data per halaman
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM rekam_medis 
    WHERE 
    no_rm LIKE '%$keyword%' OR
    nama LIKE '%$keyword%' OR
    nik LIKE '%$keyword%' OR
    no_hp LIKE '%$keyword%' 
    LIMIT $limit OFFSET $offset";


$total_query = "SELECT COUNT(*) as total FROM rekam_medis
    WHERE 
    no_rm LIKE '%$keyword%' OR
    nama LIKE '%$keyword%' OR
    nik LIKE '%$keyword%' OR
    no_hp LIKE '%$keyword%'";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);
$rekam_medis = query($query);


?>

<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
            <th class="border p-2">No RM</th>
            <th class="border p-2">Nama</th>
            <th class="border p-2">Jenis Kelamin</th>
            <th class="border p-2">Tanggal Lahir</th>
            <th class="border p-2">Tanggal Kunjungan</th>
            <th class="border p-2">No. HP</th>
            <th class="border p-2">Obat</th>
            <th class="border p-2">Dokter</th>

            <th class="border p-2">Action</th>
        </tr>
    </thead>
    <tbody class="text-xs">

        <?php $i = 1; ?>
        <?php foreach ($rekam_medis as $row)
        : ?>

            <tr>
                <td class="border p-2 md"><?= $i; ?></td>
                <td class="border p-2 md"><?= $row["no_rm"]; ?></td>
                <td class="border p-2 truncate md"><?= $row["nama"]; ?></td>
                <td class="border p-2 md"><?= $row["jenis_kelamin"]; ?></td>
                <td class="border p-2 md"><?= $row["tanggal_lahir"]; ?></td>
                <td class="border p-2"><?= $row["tanggal_kunjungan"]; ?></td>
                <td class="border p-2 md"><?= $row["no_hp"]; ?></td>
                <td class="border p-2 md"><?= $row["obat"]; ?></td>
                <td class="border p-2 md">
                    <?= strlen($row['dokter']) > 18 ? substr($row['dokter'], 0, 18) . '...' : $row["dokter"]; ?>
                </td>

                <td class="border p-2">
                    <div class="flex justify-end space-x-1">
                        <button onclick="lihatPasien('<?= $row['id']; ?>')"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                            View
                        </button>
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