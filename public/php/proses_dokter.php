<?php
// Konfigurasi database
$conn = mysqli_connect("localhost", "root", "", "klinik");

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Pastikan tombol submit ditekan
if (isset($_POST["submit"])) {

    // Ambil ID dokter terakhir
    $query = "SELECT id_dokter FROM dokter ORDER BY id_dokter DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Generate ID dokter baru
    if ($row) {
        $last_id = intval(substr($row["id_dokter"], 3)) + 1;
        $new_id = "DR-" . str_pad($last_id, 3, "0", STR_PAD_LEFT);
    } else {
        $new_id = "DR-001";
    }

    // Ambil data dari form
    $nama = htmlspecialchars($_POST["nama"]);
    $poliklinik = htmlspecialchars($_POST["poliklinik"]);

    // Cek apakah folder uploads ada, jika tidak buat foldernya
    $upload_dir = __DIR__ . "/uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Upload profile picture jika ada
    $profile_picture = null;
    if (!empty($_FILES["profile_picture"]["name"])) {
        $filename = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $upload_path = $upload_dir . $filename;

        // Cek apakah file berhasil diunggah
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $upload_path)) {
            $profile_picture = $filename;
        } else {
            echo "<script>alert('Gagal mengunggah gambar. Pastikan folder uploads memiliki izin yang cukup.');</script>";
            exit;
        }
    }

    // Gunakan prepared statement untuk mencegah SQL Injection
    $sql = "INSERT INTO dokter (id_dokter, nama, poliklinik, profile_picture) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $new_id, $nama, $poliklinik, $profile_picture);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>
                alert('Data Berhasil Ditambah!');
                window.location.href = '../login/admin/crud/dokter/tambah.html'; // Redirect ke halaman dokter
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
} else {
    echo "<script>alert('Semua data harus diisi!');</script>";
}

$conn->close();
?>