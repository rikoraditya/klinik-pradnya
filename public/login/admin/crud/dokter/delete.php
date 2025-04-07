<?php

require '../../../../php/functions.php';

$id = $_GET["id"];

// HTML dan JS untuk SweetAlert
echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";


if (data_dokter($id) > 0) {
    // Berhasil Delete
    echo "<script>
   Swal.fire({
       icon: 'success',
       title: 'Data Berhasil Dihapus',
       confirmButtonText: 'Kembali'
   }).then(() => {
       window.location.href = 'manage.php';
   });
</script>";

} else {
    // Gagal Delete
    echo "<script>
   Swal.fire({
       icon: 'error',
       title: 'Data Galal Dihapus',
       confirmButtonText: 'Kembali'
   }).then(() => {
       window.location.href = 'manage.php';
   });
</script>";

}


echo "</body></html>";
?>