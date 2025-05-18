<?php
require '../../php/functions.php';

$keyword = $_GET["keyword"] ?? '';
$page = $_GET["page"] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Query utama dengan pencarian keyword
$antrian = query("
  SELECT 
    antrian.id,
    antrian.no_antrian,
    pasien.nama,
    pasien.jenis_kelamin,
    pasien.no_hp,
    pasien.nik,
    antrian.poli_tujuan,
    antrian.tanggal_antrian,
    antrian.status_antrian
  FROM antrian
  INNER JOIN pasien ON antrian.pasien_id = pasien.id
  WHERE 
    antrian.no_antrian LIKE '%$keyword%' OR
    pasien.nama LIKE '%$keyword%' OR
    pasien.nik LIKE '%$keyword%' OR
    pasien.no_hp LIKE '%$keyword%' OR
    antrian.poli_tujuan LIKE '%$keyword%' OR
    antrian.status_antrian LIKE '%$keyword%'
  ORDER BY antrian.id DESC
  LIMIT $limit OFFSET $offset
");

// Query total untuk pagination
$total_query = "
  SELECT COUNT(*) AS total
  FROM antrian
  INNER JOIN pasien ON antrian.pasien_id = pasien.id
  WHERE 
    antrian.no_antrian LIKE '%$keyword%' OR
    pasien.nama LIKE '%$keyword%' OR
    pasien.nik LIKE '%$keyword%' OR
    pasien.no_hp LIKE '%$keyword%' OR
    antrian.poli_tujuan LIKE '%$keyword%' OR
    antrian.status_antrian LIKE '%$keyword%'
";

$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

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
            <th class="border p-2">Poli Tujuan</th>
            <th class="border p-2">Tanggal Antrian</th>
            <th class="border p-2">Status Antrian</th>
            <th class="border p-2">Action</th>
        </tr>
    </thead>
    <tbody class="text-xs">
        <?php $i = 1; ?>
        <?php foreach ($antrian as $row): ?>
            <tr>
                <td class="border p-2 md"><?= $i; ?></td>
                <td class="border p-2 w-8 md"><?= htmlspecialchars($row["no_antrian"]); ?></td>
                <td class="border p-2 truncate w-52 md"><?= htmlspecialchars($row["nama"]); ?></td>
                <td class="border p-2 truncate w-20 md">
                    <?= strlen($row['nik']) > 13 ? htmlspecialchars(substr($row['nik'], 0, 13)) . '...' : htmlspecialchars($row["nik"]); ?>
                </td>
                <td class="border p-2 w-28 md">
                    <?= $row["jenis_kelamin"] == 'Laki-laki' ? 'Laki-laki' : ($row["jenis_kelamin"] == 'Perempuan' ? 'Perempuan' : '-') ?>
                </td>
                <td class="border p-2 w-8 md"><?= htmlspecialchars($row["no_hp"]); ?></td>
                <td class="border p-2 truncate w-36 md"><?= htmlspecialchars($row["poli_tujuan"]); ?></td>
                <td class="border p-2 md w-28"><?= htmlspecialchars($row["tanggal_antrian"]); ?></td>
                <td class="border p-2 w-20 md"><?= htmlspecialchars($row["status_antrian"]); ?></td>
                <td class="border p-2 w-fit">
                    <div class="flex justify-end space-x-1">
                        <button onclick="lihatPasien('<?= $row['id']; ?>')"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                            View
                        </button>
                        <form action="../../php/functions.php" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs"
                                onclick="panggilPasien('<?= $row['id'] ?>', '<?= $row['no_antrian'] ?>', '<?= $row['poli_tujuan'] ?>')">
                                Panggil
                            </button>
                        </form>
                        <form action="../../php/functions.php" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button type="submit" name="selesai"
                                class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                Selesai
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
    </tbody>
</table>