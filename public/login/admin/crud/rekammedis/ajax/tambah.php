<?php
use LDAP\Result;

require '../../../../../php/functions.php';

$keyword = $_GET["keyword"] ?? '';
$page = $_GET["page"] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Query utama untuk mengambil data dari pasien + antrian
$antrian = query("SELECT 
    a.id AS antrian_id,
    a.no_antrian,
    a.poli_tujuan,
    a.tanggal_antrian,
    a.status_antrian,
    p.id AS pasien_id,
    p.nama,
    p.nik,
    p.no_hp,
    p.jenis_kelamin,
    p.tanggal_lahir
  FROM antrian a
  JOIN pasien p ON a.pasien_id = p.id
  WHERE 
    a.no_antrian LIKE '%$keyword%' OR
    p.nama LIKE '%$keyword%' OR
    p.nik LIKE '%$keyword%' OR
    p.no_hp LIKE '%$keyword%'
  ORDER BY a.tanggal_antrian DESC
  LIMIT $limit OFFSET $offset");



?>

<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
        <tr class="text-xs">
            <th class="border p-2">No</th>
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
        <?php foreach ($antrian as $row)
        : ?>

            <tr>
                <td class="border p-2 md"><?= $i; ?></td>
                <td class="border p-2 truncate md"><?= $row["nama"]; ?></td>
                <td class="border p-2 truncate md"><?= $row["nik"]; ?></td>
                <td class="border p-2 md"><?= $row["jenis_kelamin"]; ?></td>
                <td class="border p-2 md"><?= $row["tanggal_lahir"]; ?></td>
                <td class="border p-2 md"><?= $row["tanggal_antrian"]; ?></td>
                <td class="border p-2 md"><?= $row["no_hp"]; ?></td>
                <td class="border p-2 md"><?= $row["status_antrian"]; ?></td>
                <td class="border p-2 space-x-1 w-56">

                    <a href="rm.php?id=<?= $row['antrian_id']; ?>"
                        class="bg-green-700 hover:bg-green-800 text-white px-2 py-1 rounded text-xs flex items-center gap-2">
                        <i class="fas fa-book"></i>Tambah Rekam Medis</a>

                </td>
            </tr>

            <?php $i++; ?>
        <?php endforeach; ?>

    </tbody>
</table>