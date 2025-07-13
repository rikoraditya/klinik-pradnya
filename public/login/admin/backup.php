<?php
session_start();
require '../../php/functions.php';

if (!isset($_SESSION["login"])) {
    header("location:../admin_login.php");
    exit;
}

// Konfigurasi database
$host = "localhost";
$user = "root";        // ganti jika username MySQL bukan root
$pass = "";            // ganti jika MySQL ada password
$db_name = "klinik_pradnya";  // ganti dengan nama database kamu

// Nama file backup
$backup_file = "backup_" . date("Ymd_His") . ".sql";

// Jalankan mysqldump
$command = "\"C:\\xampp\\mysql\\bin\\mysqldump.exe\" --user=$user --password=$pass --host=$host $db_name";


// Jalankan command
$output = null;
$return_var = null;
exec($command, $output, $return_var);

// Jika gagal
if ($return_var !== 0) {
    die("Gagal melakukan backup. Pastikan mysqldump tersedia dan kredensial benar.");
}

// Gabungkan array output
$sql_dump = implode("\n", $output);

// Header download
header("Content-Type: application/sql");
header("Content-Disposition: attachment; filename=\"$backup_file\"");
echo $sql_dump;
exit;
?>