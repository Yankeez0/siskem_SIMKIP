<?php
require_once 'config.php';
require_once 'aes_gcm.php';
require_once 'hkdf.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = getDbConnection();
$result = $conn->query('SELECT id, nim, nama_encrypted, biodata_encrypted FROM mahasiswa');
$mahasiswa = [];
$key = hkdf(ENCRYPTION_MASTER_KEY, 32, 'simkip_salt', 'mahasiswa_data');
while ($row = $result->fetch_assoc()) {
    $row['nama'] = aes_gcm_decrypt($row['nama_encrypted'], $key);
    $row['biodata'] = aes_gcm_decrypt($row['biodata_encrypted'], $key);
    unset($row['nama_encrypted'], $row['biodata_encrypted']);
    $mahasiswa[] = $row;
}
$conn->close();
echo json_encode($mahasiswa);
