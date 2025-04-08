<?php
require '../../../../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = query("SELECT * FROM rekam_medis WHERE id = '$id'");

    if ($result && count($result) > 0) {
        echo json_encode($result[0]);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
}
?>