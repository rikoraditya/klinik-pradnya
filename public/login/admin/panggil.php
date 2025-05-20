<?php
require '../../php/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $status = '';

    if (isset($_POST['panggil'])) {
        $status = 'dipanggil';
    } elseif (isset($_POST['selesai'])) {
        $status = 'selesai';
    } else {
        echo 'invalid';
        exit;
    }

    $stmt = $conn->prepare("UPDATE antrian SET status_antrian = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "failed";
    }

    $stmt->close();
    exit;
}
?>