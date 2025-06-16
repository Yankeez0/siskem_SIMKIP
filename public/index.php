<?php
session_start();
include 'php/session_check.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Contoh data dashboard, bisa diambil dari database
$total_mahasiswa = 1234;
$mahasiswa_aktif = 1100;
$mahasiswa_tidak_aktif = 134;
$dokumen_baru = 15;
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard SIM-KIP</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php" class="active">Dashboard</a>
        <a href="rekapitulasi.php">Rekapitulasi</a>
        <a href="data-mahasiswa.php">Data Mahasiswa</a>
        <a href="keuangan.php">Keuangan</a>
        <a href="login.php">Logout</a>
    </div>
    <main class="main-content">
        <div class="header-main">
            <div class="header-left">
                <span class="admin-label">admin</span>
            </div>
            <div class="header-right">
                <div class="header-icons">
                    <i class="fas fa-search"></i>
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile-toggle" id="userProfileToggle">
                    <img src="https://via.placeholder.com/35" alt="Avatar" class="user-avatar">
                    <span class="username"><?php echo htmlspecialchars($username); ?></span>
                    <i class="fas fa-caret-down caret-icon"></i>
                </div>
                <div id="profileDropdown" class="profile-dropdown">
                    <a href="profile-settings.php">Pengaturan Profil</a>
                    <a href="help.php">Bantuan</a>
                    <a href="login.php">Logout</a>
                </div>
            </div>
        </div>
        <div class="page-header">
            <h2>Dashboard</h2>
            <p>Selamat datang, <?php echo htmlspecialchars($username); ?>!</p>
        </div>
        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Mahasiswa </h3>
                <p class="card-value"><?php echo $total_mahasiswa; ?></p>
                <i class="fas fa-users card-icon"></i>
            </div>
            <div class="card">
                <h3>Mahasiswa Aktif KIP</h3>
                <p class="card-value"><?php echo $mahasiswa_aktif; ?></p>
                <i class="fas fa-user-check card-icon"></i>
            </div>
            <div class="card">
                <h3>Total Mahasiswa Tidak Aktif</h3>
                <p class="card-value"><?php echo $mahasiswa_tidak_aktif; ?></p>
                <i class="fas fa-dollar-sign card-icon"></i>
            </div>
            <div class="card">
                <h3>Dokumen Baru</h3>
                <p class="card-value"><?php echo $dokumen_baru; ?></p>
                <i class="fas fa-file-alt card-icon"></i>
            </div>
        </div>
        <div class="chart-section">
            <h3>Statistik KIP</h3>
            <div class="chart-placeholder">
                <p>Grafik Mahasiswa KIP Tahun Ini</p>
            </div>
        </div>
    </main>
    <script src="JS/script.js"></script>
</body>
</html>
