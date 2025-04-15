<?php
use LDAP\Result;

require '../../../../../php/functions.php';


$keyword = $_GET["keyword"] ?? '';
$page = $_GET["page"] ?? 1;
$limit = 5; // jumlah data per halaman
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM pasien 
    WHERE 
    nama LIKE '%$keyword%' OR
    nik LIKE '%$keyword%' OR
    no_hp LIKE '%$keyword%' 
    LIMIT $limit OFFSET $offset";


$total_query = "SELECT COUNT(*) as total FROM pasien 
    WHERE 
    nama LIKE '%$keyword%' OR
    nik LIKE '%$keyword%' OR
    no_hp LIKE '%$keyword%'";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);
$pasien = query($query);


?>

<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
            <th class="border p-2">No Antrian</th>
            <th class="border p-2">Nama</th>
            <th class="border p-2">NIK</th>
            <th class="border p-2">Jenis Kelamin</th>
            <th class="border p-2">No HP</th>
            <th class="border p-2">Keluhan</th>
            <th class="border p-2">Poli Tujuan</th>
            <th class="border p-2">Tanggal Kunjungan</th>
            <th class="border p-2">Action</th>
        </tr>
    </thead>
    <tbody class="text-xs">

        <?php $i = 1; ?>
        <?php foreach ($pasien as $row)
        : ?>

            <tr>
                <td class="border p-2 md"><?= $i; ?></td>
                <td class="border p-2 md"><?= $row["no_antrian"]; ?></td>
                <td class="border p-2 truncate w-20 md">
                    <?= $row["nama"]; ?>
                </td>
                <td class="border p-2 truncate w-20 md">
                    <?= strlen($row['nik']) > 10 ? substr($row['nik'], 0, 10) . '...' : $row["nik"]; ?>
                </td>
                <td class="border p-2 md"> <?= $row["jenis_kelamin"]; ?></td>
                <td class="border p-2 truncate w-20 md"> <?= $row["no_hp"]; ?></td>
                <td class="border p-2 truncate w-20 md">
                    <?= strlen($row['keluhan']) > 15 ? substr($row['keluhan'], 0, 15) . '...' : $row["keluhan"]; ?>
                </td>
                <td class="border p-2 truncate w-20 md"> <?= $row["poli_tujuan"]; ?></td>
                <td class="border p-2 md"> <?= $row["tanggal_kunjungan"]; ?></td>
                <td class="border p-2 space-x-1">
                    <button onclick="lihatPasien('<?= $row['id']; ?>')"
                        class="bg-gray-500 text-white px-2 py-1 rounded text-xs">
                        View
                    </button>

                    <a href="update.php?id=<?= $row['id']; ?>"
                        class="bg-blue-500 text-white px-2 py-1 rounded text-xs inline-block">
                        Update
                    </a>

                    <a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin hapus?')"
                        class="bg-red-700 text-white px-2 py-1 rounded text-xs inline-block">
                        Delete
                    </a>
                </td>
            </tr>

            <?php $i++; ?>
        <?php endforeach; ?>

    </tbody>
</table>