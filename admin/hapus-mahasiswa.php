<?php
session_start();
require_once '../proses/config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $conn = getDbConnection();
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM mahasiswa WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: data-mahasiswa.php");
        exit;
    } else {
        echo "Gagal menghapus data.";
    }
    $stmt->close();
} else {
    header("Location: data-mahasiswa.php");
    exit;
}
