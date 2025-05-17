<?php

//Konkesi ke Database
$conn = mysqli_connect("localhost", "root", "", "klinik");
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


$obat = mysqli_query($conn, "SELECT * FROM obat");

// Ambil jumlah pasien untuk setiap poli
$query_poli_umum = "SELECT COUNT(*) AS total FROM pasien WHERE poli_tujuan = 'Poli Umum'";
$query_poli_gigi = "SELECT COUNT(*) AS total FROM pasien WHERE poli_tujuan = 'Poli Gigi'";

// Eksekusi query
$result_poli_umum = mysqli_query($conn, $query_poli_umum);
$result_poli_gigi = mysqli_query($conn, $query_poli_gigi);

// Ambil hasil jumlah pasien
$poli_umum = mysqli_fetch_assoc($result_poli_umum)['total'];
$poli_gigi = mysqli_fetch_assoc($result_poli_gigi)['total'];

// Jika tombol "Panggil" ditekan
if (isset($_POST['panggil'])) {
    $id = $_POST['id'];
    $query = "UPDATE pasien SET status_antrian = 'Dipanggil' WHERE id = $id";
    mysqli_query($conn, $query);
    header("Location: ../login/admin/dashboard.php"); // Refresh halaman
    exit();
}

// Jika tombol "Selesai" ditekan
if (isset($_POST['selesai'])) {
    $id = $_POST['id'];
    $query = "UPDATE pasien SET status_antrian = 'Selesai' WHERE id = $id";
    mysqli_query($conn, $query);
    header("Location: ../login/admin/dashboard.php"); // Refresh halaman
    exit();
}

// Ambil data pasien dari database
$result = mysqli_query($conn, "SELECT * FROM pasien ORDER BY no_antrian ASC");
$pasien = mysqli_fetch_all($result, MYSQLI_ASSOC);


function data_pasien($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM pasien WHERE id = $id");
    return mysqli_affected_rows($conn);
}

function data_dokter($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM dokter WHERE id_nomor = $id");
    return mysqli_affected_rows($conn);
}

function data_obat($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM obat WHERE id = $id");
    return mysqli_affected_rows($conn);
}


function data_rm($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM rekam_medis WHERE id = $id");
    return mysqli_affected_rows($conn);
}

function update_pasien($data)
{

    global $conn;

    $id = $data["id"];

    $nama = $data['nama'];
    $nik = $data['nik'];
    $jenis_kelamin = $data['jenis_kelamin'];
    $no_hp = $data['no_hp'];
    $tempat_lahir = $data['tempat_lahir'];
    $tanggal_lahir = $data['tanggal_lahir'];
    $alamat = $data['alamat'];
    $tanggal_kunjungan = $data['tanggal_kunjungan'];
    $keluhan = $data['keluhan'];
    $poli_tujuan = $data['poli_tujuan'];
    $jenis_pasien = $data['jenis_pasien'];
    $nik_bpjs = $data['nik_bpjs'];

    $query = "UPDATE pasien SET
    
    nama = '$nama',
    nik = '$nik',
    jenis_kelamin = '$jenis_kelamin',
    no_hp = '$no_hp',
    tempat_lahir = '$tempat_lahir',
    tanggal_lahir = '$tanggal_lahir',
    alamat = '$alamat',
    tanggal_kunjungan = '$tanggal_kunjungan',
    keluhan = '$keluhan',
    poli_tujuan = '$poli_tujuan',
    jenis_pasien= '$jenis_pasien',
    nik_bpjs = '$nik_bpjs'

    WHERE id = $id
    ";


    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}


function update_dokter($data)
{

    global $conn;

    $id = $data["id_nomor"];
    $id_dokter = $data['id_dokter'];
    $nama = $data['nama'];
    $poliklinik = $data['poliklinik'];
    $profile_picture = $data['profile_picture'];


    $query = "UPDATE dokter SET
    
    nama = '$nama',
    poliklinik = '$poliklinik',
    profile_picture = '$profile_picture'
  

    WHERE id_nomor = $id
    ";


    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function update_obat($data)
{

    global $conn;

    $id = $data["id"];
    $kode_obat = $data['kode_obat'];
    $nama_obat = $data['nama_obat'];
    $jenis_obat = $data['jenis_obat'];
    $dosis = $data['dosis'];
    $keterangan = $data['keterangan'];


    $query = "UPDATE obat SET
    
    nama_obat = '$nama_obat',
    jenis_obat = '$jenis_obat',
    dosis = '$dosis',
    keterangan = '$keterangan'
  
    WHERE id = $id
    ";


    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function update_rm($data)
{

    global $conn;

    $id = $data["id"];
    $no_rm = $data['no_rm'];
    $nama = $data['nama'];
    $nik = $data['nik'];
    $jenis_kelamin = $data['jenis_kelamin'];
    $no_hp = $data['no_hp'];
    $tempat_lahir = $data['tempat_lahir'];
    $tanggal_lahir = $data['tanggal_lahir'];
    $alamat = $data['alamat'];
    $tanggal_kunjungan = $data['tanggal_kunjungan'];
    $keluhan = $data['keluhan'];
    $poli_tujuan = $data['poli_tujuan'];
    $jenis_pasien = $data['jenis_pasien'];
    $dokter = $data['dokter'];
    $nik_bpjs = $data['nik_bpjs'];
    $denyut_nadi = $data['denyut_nadi'];
    $laju_pernapasan = $data['laju_pernapasan'];
    $obat = $data['obat'];
    $diagnosa = $data['diagnosa'];

    $query = "UPDATE rekam_medis SET
    
    
    nama = '$nama',
    nik = '$nik',
    jenis_kelamin = '$jenis_kelamin',
    no_hp = '$no_hp',
    tempat_lahir = '$tempat_lahir',
    tanggal_lahir = '$tanggal_lahir',
    alamat = '$alamat',
    tanggal_kunjungan = '$tanggal_kunjungan',
    keluhan = '$keluhan',
    poli_tujuan = '$poli_tujuan',
    jenis_pasien= '$jenis_pasien',
    dokter = '$dokter',
    nik_bpjs = '$nik_bpjs',
    denyut_nadi = '$denyut_nadi',
    laju_pernapasan = '$laju_pernapasan',
    obat = '$obat',
    diagnosa = '$diagnosa'

    WHERE id = $id
    ";


    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function cari($keyword)
{
    $query = "SELECT * FROM pasien 
    WHERE 
    nama LIKE '%$keyword%' OR
    nik LIKE '%$keyword%' OR
    no_hp LIKE '%$keyword%'
    ";
    return query($query);
}

function cari_dokter($keyword)
{
    $query = "SELECT * FROM dokter
    WHERE 
    nama LIKE '%$keyword%' OR
    poliklinik LIKE '%$keyword%'
    ";
    return query($query);
}

function cari_obat($keyword)
{
    $query = "SELECT * FROM obat
    WHERE 
    nama_obat LIKE '%$keyword%' OR
    jenis_obat LIKE '%$keyword%'
    ";
    return query($query);
}

function cari_rm($keyword)
{
    $query = "SELECT * FROM rekam_medis 
    WHERE 
    nama LIKE '%$keyword%' OR
    nik LIKE '%$keyword%' OR
    no_hp LIKE '%$keyword%' OR
    no_rm LIKE '%$keyword%' OR
    tanggal_lahir LIKE '%$keyword%' OR
    tanggal_kunjungan LIKE '%$keyword%'
    ";
    return query($query);
}
?>