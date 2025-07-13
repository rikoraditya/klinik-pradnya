<?php
require '../../../../../../php/functions.php';

$keyword = $_GET["keyword"] ?? '';
$page = $_GET["halaman"] ?? 1;
$limit = 8;
$offset = ($page - 1) * $limit;

// Query total data (hitung pasien yang masuk ke antrian dan cocok dengan pencarian)
$total_query = "
  SELECT COUNT(*) AS total FROM (
    SELECT a.id
    FROM antrian a
    INNER JOIN (
      SELECT pasien_id, MAX(tanggal_antrian) AS max_tanggal
      FROM antrian
      GROUP BY pasien_id
    ) latest ON latest.pasien_id = a.pasien_id AND latest.max_tanggal = a.tanggal_antrian
    JOIN pasien p ON a.pasien_id = p.id
    WHERE 
      a.no_antrian LIKE '%$keyword%' OR
      p.nama LIKE '%$keyword%' OR
      p.nik LIKE '%$keyword%' OR
      p.no_hp LIKE '%$keyword%'
  ) AS filtered
";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

// Query utama data antrian + pasien
$query = "
  SELECT 
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
  INNER JOIN (
    SELECT pasien_id, MAX(tanggal_antrian) AS max_tanggal
    FROM antrian
    GROUP BY pasien_id
  ) latest ON latest.pasien_id = a.pasien_id AND latest.max_tanggal = a.tanggal_antrian
  WHERE 
    a.no_antrian LIKE '%$keyword%' OR
    p.nama LIKE '%$keyword%' OR
    p.nik LIKE '%$keyword%' OR
    p.no_hp LIKE '%$keyword%'
  ORDER BY a.tanggal_antrian DESC
  LIMIT $limit OFFSET $offset
";

$antrian = query($query);

// Definisikan variabel untuk pagination
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
            <?php
            $statusClass = '';
            if ($row['status_antrian'] === 'dipanggil') {
                $statusClass = 'text-blue-600';
            } elseif ($row['status_antrian'] === 'selesai') {
                $statusClass = 'text-green-600';
            }
            ?>
            <tr>
                <td class="border p-2 md"><?= $i; ?></td>
                <td class="border p-2 truncate md"><?= $row["nama"]; ?></td>
                <td class="border p-2 truncate md"><?= $row["nik"]; ?></td>
                <td class="border p-2 md"><?= $row["jenis_kelamin"]; ?></td>
                <td class="border p-2 md"><?= $row["tanggal_lahir"]; ?></td>
                <td class="border p-2 md"><?= $row["tanggal_antrian"]; ?></td>
                <td class="border p-2 md"><?= $row["no_hp"]; ?></td>
                <td id="status-antrian-<?= $row['antrian_id']; ?>" class="border p-2 <?= $statusClass ?>">
                    <?= htmlspecialchars($row["status_antrian"]); ?>
                </td>
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