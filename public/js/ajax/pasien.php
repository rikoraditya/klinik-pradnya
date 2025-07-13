<?php
require '../../php/functions.php';

$keyword = $_GET["keyword"] ?? '';
$tanggal = $_GET["tanggal"] ?? '';
$page = isset($_GET["halaman"]) ? (int) $_GET["halaman"] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Membuat kondisi WHERE dinamis
$where = [];

if (!empty($keyword)) {
    $escaped_keyword = mysqli_real_escape_string($conn, $keyword);
    $where[] = "(
        antrian.no_antrian LIKE '%$escaped_keyword%' OR
        pasien.nama LIKE '%$escaped_keyword%' OR
        pasien.nik LIKE '%$escaped_keyword%' OR
        pasien.no_hp LIKE '%$escaped_keyword%' OR
        antrian.poli_tujuan LIKE '%$escaped_keyword%' OR
        antrian.status_antrian LIKE '%$escaped_keyword%'
    )";
}

if (!empty($tanggal)) {
    $escaped_tanggal = mysqli_real_escape_string($conn, $tanggal);
    $where[] = "DATE(antrian.tanggal_antrian) = '$escaped_tanggal'";
}

// Gabungkan semua kondisi
$where_sql = '';
if (!empty($where)) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

// Query utama dengan kondisi
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
    $where_sql
    ORDER BY antrian.id DESC
    LIMIT $limit OFFSET $offset
");

// Query total data
$total_query = "
    SELECT COUNT(*) AS total
    FROM antrian
    INNER JOIN pasien ON antrian.pasien_id = pasien.id
    $where_sql
";
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

$HalamanAktif = $page;
$JumlahHalaman = $total_pages;
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
        <?php $i = 1 + $offset; ?>
        <?php foreach ($antrian as $row): ?>
            <?php
            $statusClass = '';
            if ($row['status_antrian'] === 'dipanggil') {
                $statusClass = 'text-blue-600';
            } elseif ($row['status_antrian'] === 'selesai') {
                $statusClass = 'text-green-600';
            }
            ?>
            <tr>
                <td class="border p-2"><?= $i++; ?></td>
                <td class="border p-2"><?= htmlspecialchars($row["no_antrian"]); ?></td>
                <td class="border p-2"><?= htmlspecialchars($row["nama"]); ?></td>
                <td class="border p-2">
                    <?= strlen($row['nik']) > 13 ? htmlspecialchars(substr($row['nik'], 0, 13)) . '...' : htmlspecialchars($row["nik"]); ?>
                </td>
                <td class="border p-2"><?= $row["jenis_kelamin"] === 'Laki-laki' ? 'Laki-laki' : 'Perempuan'; ?></td>
                <td class="border p-2"><?= htmlspecialchars($row["no_hp"]); ?></td>
                <td class="border p-2"><?= htmlspecialchars($row["poli_tujuan"]); ?></td>
                <td class="border p-2"><?= htmlspecialchars($row["tanggal_antrian"]); ?></td>
                <td id="status-antrian-<?= $row['id']; ?>" class="border p-2 <?= $statusClass ?>">
                    <?= htmlspecialchars($row["status_antrian"]); ?>
                </td>
                <td class="border p-2">
                    <div class="flex justify-end space-x-1">
                        <button onclick="lihatPasien('<?= $row['id']; ?>')"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-poppins px-2 py-1 rounded text-xs">
                            <i class="fas fa-eye mr-1"></i><span class="">View</span>
                        </button>


                        <button
                            onclick="panggilPasien('<?= $row['id']; ?>', '<?= $row['no_antrian']; ?>', '<?= $row['poli_tujuan']; ?>')"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">Panggil</button>
                        <button onclick="selesaikanPasien('<?= $row['id']; ?>')"
                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">Selesai</button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination text-xs font-poppins mt-2 ml-1 text-gray-500">
    <?php if ($page > 1): ?>
        <button class="px-2" data-page="<?= $page - 1 ?>">&laquo;</button>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <button class="px-2 <?= $i == $page ? 'font-bold text-green-950' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <button class="px-2" data-page="<?= $page + 1 ?>">&raquo;</button>
    <?php endif; ?>
</div>