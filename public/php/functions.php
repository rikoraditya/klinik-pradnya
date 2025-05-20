<?php
// Koneksi Database
$conn = mysqli_connect("localhost", "root", "", "klinik_pradnya");

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        // Jika query gagal, hentikan eksekusi dan tampilkan error
        die("Query Error: " . mysqli_error($conn) . "<br>Query: " . $query);
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// =============================
// FUNGSI ANTRIAN
// =============================
function generateNoAntrian($tanggal)
{
    global $conn;
    $query = "SELECT COUNT(*) AS jumlah FROM antrian WHERE tanggal_antrian = '$tanggal'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    $jumlah = $data['jumlah'] + 1;
    return str_pad($jumlah, 3, '0', STR_PAD_LEFT);
}

function tambahAntrian($pasien_id)
{
    global $conn;
    $tanggal = date('Y-m-d');
    $no_antrian = generateNoAntrian($tanggal);
    $query = "INSERT INTO antrian (pasien_id, no_antrian, tanggal_antrian, status_antrian) VALUES ('$pasien_id', '$no_antrian', '$tanggal', 'menunggu')";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}



function getAntrianHariIni()
{
    global $conn;
    $tanggal = date('Y-m-d');
    $query = "
        SELECT a.*, p.nama, p.nik, p.no_hp
        FROM antrian a
        JOIN pasien p ON a.pasien_id = p.id
        WHERE a.tanggal_antrian = '$tanggal'
        ORDER BY a.no_antrian ASC
    ";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// =============================
// FUNGSI UMUM
// =============================
function hapusData($tabel, $id_field, $id)
{
    global $conn;
    $query = "DELETE FROM $tabel WHERE $id_field = '$id'";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

// =============================
// PASIEN
// =============================
function updatePasien($data)
{
    global $conn;

    $id = $data['id'];
    $nama = $data['nama'];
    $nik = $data['nik'];
    $jenis_kelamin = $data['jenis_kelamin'];
    $no_hp = $data['no_hp'];
    $tempat_lahir = $data['tempat_lahir'];
    $tanggal_lahir = $data['tanggal_lahir'];
    $alamat = $data['alamat'];


    // Query update pasien
    $query = "UPDATE pasien SET 
        nama = '$nama', 
        nik = '$nik', 
        jenis_kelamin = '$jenis_kelamin', 
        no_hp = '$no_hp', 
        tempat_lahir = '$tempat_lahir', 
        tanggal_lahir = '$tanggal_lahir', 
        alamat = '$alamat'
        WHERE id = $id";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}



// =============================
// DOKTER
// =============================
function updateDokter($data)
{
    global $conn;
    $id = $data['id'];
    $nama = $data['nama'];
    $poliklinik = $data['poliklinik'];
    $profile_picture = $data['profile_picture'];

    $query = "UPDATE dokter SET 
        nama = '$nama', poliklinik = '$poliklinik', profile_picture = '$profile_picture'
        WHERE id = $id";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

// =============================
// OBAT
// =============================
function updateObat($data)
{
    global $conn;
    $id = $data['id'];
    $nama_obat = $data['nama_obat'];
    $jenis_obat = $data['jenis_obat'];
    $dosis = $data['dosis'];
    $keterangan = $data['keterangan'];

    $query = "UPDATE obat SET 
        nama_obat = '$nama_obat', jenis_obat = '$jenis_obat', 
        dosis = '$dosis', keterangan = '$keterangan'
        WHERE id = $id";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

// =============================
// REKAM MEDIS (hanya update RM & NIK)
// =============================

function updateRM($data)
{
    global $conn;

    $id = intval($data['id']);
    $laju_pernapasan = $data['laju_pernapasan'];
    $denyut_nadi = $data['denyut_nadi'];
    $diagnosa = $data['diagnosa'];

    $stmt = $conn->prepare("UPDATE kunjungan SET laju_pernapasan = ?, denyut_nadi = ?, diagnosa = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssi", $laju_pernapasan, $denyut_nadi, $diagnosa, $id);
    $stmt->execute();
    return $stmt->affected_rows;
}



// =============================
// CARI
// =============================
function cari($tabel, $kolom, $keyword)
{
    $query = "SELECT * FROM $tabel WHERE $kolom LIKE '%$keyword%'";
    return query($query);
}

function deleteDokter($id)
{
    global $conn;
    $query = "DELETE FROM dokter WHERE id = $id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function deleteObat($id)
{
    global $conn;
    $query = "DELETE FROM obat WHERE id = $id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function deletePasien($id)
{
    global $conn;
    $query = "DELETE FROM pasien WHERE id = $id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function deleteRM($id)
{
    global $conn;
    $query = "DELETE FROM kunjungan WHERE id = $id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}