<?php
// File: create_admin.php (Hanya untuk development)
require_once 'config.php';
require_once 'scrypt.php';

$username = 'admin1';
$password = 'password_kuat@123';
$hashed_password = scrypt_hash($password);

$conn = getDbConnection();
$stmt = $conn->prepare("INSERT INTO admin (username, password, role, nama_lengkap) VALUES (?, ?, 'admin', 'Admin Utama')");
$stmt->bind_param('ss', $username, $hashed_password);

if ($stmt->execute()) {
    echo "Admin berhasil dibuat!";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();