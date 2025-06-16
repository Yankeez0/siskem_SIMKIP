<?php
require_once 'config.php';
require_once 'aes_gcm.php';
require_once 'hkdf.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = getDbConnection();
$stmt = $conn->prepare('SELECT nim, nama_encrypted, biodata_encrypted FROM mahasiswa WHERE nim = ?');
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$key = hkdf(ENCRYPTION_MASTER_KEY, 32, 'simkip_salt', 'mahasiswa_data');
if ($row) {
    $row['nama'] = aes_gcm_decrypt($row['nama_encrypted'], $key);
    $row['biodata'] = aes_gcm_decrypt($row['biodata_encrypted'], $key);
    unset($row['nama_encrypted'], $row['biodata_encrypted']);
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Data tidak ditemukan']);
}
$stmt->close();
$conn->close();
