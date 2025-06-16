<?php
require_once '../proses/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    $periode = $_POST['periode_aktif'] ?? '';

    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE mahasiswa SET status = ?, periode_aktif = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $periode, $id);

    if ($stmt->execute()) {
        header("Location: data-mahasiswa.php?success=1");
    } else {
        header("Location: data-mahasiswa.php?error=1");
    }
    $stmt->close();
}
?>
