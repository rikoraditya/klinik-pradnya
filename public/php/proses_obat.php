<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "klinik");

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel obat
$query = "SELECT * FROM obat";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if ($result) {
    $obat = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $obat = []; // Jika query gagal, set array kosong
}

// Pastikan tombol submit ditekan
if (isset($_POST["submit"])) {

    // Ambil ID obat terakhir
    $query = "SELECT kode_obat FROM obat ORDER BY kode_obat DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Generate kode obat baru dengan format OBT-XXX
    if ($row && preg_match('/\d+/', $row["kode_obat"], $matches)) {
        $last_id = intval($matches[0]) + 1;
        $new_id = "OBT-" . str_pad($last_id, 3, "0", STR_PAD_LEFT);
    } else {
        $new_id = "OBT-001"; // Jika tidak ada data sebelumnya
    }

    // Ambil data dari form dan pastikan tidak kosong
    $nama_obat = htmlspecialchars($_POST["nama_obat"] ?? '');
    $jenis_obat = htmlspecialchars($_POST["jenis_obat"] ?? '');
    $dosis = htmlspecialchars($_POST["dosis"] ?? '');
    $keterangan = htmlspecialchars($_POST["keterangan"] ?? '');

    if (empty($nama_obat) || empty($jenis_obat) || empty($dosis) || empty($keterangan)) {
        echo "<script>alert('Semua data harus diisi!'); window.history.back();</script>";
        exit;
    }

    // Query menggunakan prepared statements untuk mencegah SQL Injection
    $sql = "INSERT INTO obat (kode_obat, nama_obat, jenis_obat, dosis, keterangan) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<script>alert('Terjadi kesalahan dalam query: " . $conn->error . "');</script>";
        exit;
    }

    $stmt->bind_param("sssss", $new_id, $nama_obat, $jenis_obat, $dosis, $keterangan);

    // Eksekusi query dan tangani error
    if ($stmt->execute()) {
        echo "<script>
                alert('Data Berhasil Ditambahkan!');
                window.location.href = '../login/admin/crud/obat/tambah.html';
              </script>";
        exit;
    } else {
        echo "<script>alert('Error saat menyimpan data: " . $stmt->error . "');</script>";
    }

    // Tutup statement dan koneksi
    $stmt->close();
}

// Tutup koneksi setelah semua proses selesai
$conn->close();
?>