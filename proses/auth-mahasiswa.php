<?php
session_start();
require_once __DIR__ . '/config.php';

// Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['login_error'] = 'Metode request tidak valid';
    header('Location: ../loginmahasiswa.php');
    exit;
}

// Ambil input
$nim = trim($_POST['nim'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi input kosong
if (empty($nim) || empty($password)) {
    $_SESSION['login_error'] = 'NIM dan password harus diisi';
    header('Location: ../loginmahasiswa.php');
    exit;
}

// Koneksi database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

// Cek data mahasiswa berdasarkan NIM dan password
$stmt = $conn->prepare("SELECT id, nim, nama FROM mahasiswa WHERE nim = ? AND password = ?");
$stmt->bind_param("ss", $nim, $password);
$stmt->execute();
$result = $stmt->get_result();
$mahasiswa = $result->fetch_assoc();
$stmt->close();
$conn->close();

if ($mahasiswa) {
    $_SESSION['id'] = $mahasiswa['id'];
    $_SESSION['username'] = $mahasiswa['nama'];
    $_SESSION['role'] = 'mahasiswa';

    header("Location: ../mahasiswa/dashboard-mahasiswa.php");
    exit;
} else {
    $_SESSION['login_error'] = "Login gagal. NIM atau password salah.";
    header("Location: ../loginmahasiswa.php");
    exit;
}
