<?php
session_start();
require_once __DIR__ . '/config.php';

// Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['login_error'] = 'Metode request tidak valid';
    header('Location: ../login.php');
    exit;
}

// Ambil input
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi input kosong
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password harus diisi';
    header('Location: ../login.php');
    exit;
}

// Koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

// Cek username admin
$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Verifikasi password
    if (password_verify($password, $admin['password'])) {
        $_SESSION['id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = $admin['role'];
        $_SESSION['nama'] = $admin['nama'];

        // Redirect ke dashboard admin
        header('Location: ../admin/index.php');
        $stmt->close();
        $conn->close();
        exit;
    }
}

// Jika login gagal
$_SESSION['login_error'] = 'Username atau password salah';
$stmt->close();
$conn->close();
header('Location: ../login.php');
exit;
?>
