<?php
use LDAP\Result;

require '../../../../../php/functions.php';


$keyword = $_GET["keyword"] ?? '';
$page = $_GET["page"] ?? 1;
$limit = 5; // jumlah data per halaman
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM pasien 
    WHERE 
    no_antrian LIKE '%$keyword%' OR
    nama LIKE '%$keyword%' OR
    nik LIKE '%$keyword%' OR
    no_hp LIKE '%$keyword%' 
    LIMIT $limit OFFSET $offset";


$total_query = "SELECT COUNT(*) as total FROM pasien 
    WHERE 
    no_antrian LIKE '%$keyword%' OR
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
            <th class="border p-2">Tanggal Lahir</th>
            <th class="border p-2">Tanggal Kunjungan</th>
            <th class="border p-2">No HP</th>
            <th class="border p-2">Status Antrian</th>
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
                <td class="border p-2 truncate md"><?= $row["nama"]; ?></td>
                <td class="border p-2 truncate md"><?= $row["nik"]; ?></td>
                <td class="border p-2 md"><?= $row["jenis_kelamin"]; ?></td>
                <td class="border p-2 md"><?= $row["tanggal_lahir"]; ?></td>
                <td class="border p-2 md"><?= $row["tanggal_kunjungan"]; ?></td>
                <td class="border p-2 md"><?= $row["no_hp"]; ?></td>
                <td class="border p-2 md"><?= $row["status_antrian"]; ?></td>
                <td class="border p-2 space-x-1 w-56">

                    <a href="rm.php?id=<?= $row['id']; ?>"
                        class="bg-green-700 hover:bg-green-800 text-white px-2 py-1 rounded text-xs flex items-center gap-2">
                        <i class="fas fa-book"></i>Tambah Rekam Medis</a>

                </td>
            </tr>

            <?php $i++; ?>
        <?php endforeach; ?>

    </tbody>
</table>