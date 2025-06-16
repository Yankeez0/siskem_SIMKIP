<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Keuangan - Admin SIM-KIP</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php">Dashboard</a>
        <a href="rekapitulasi.php">Rekapitulasi</a>
        <a href="data-mahasiswa.php">Data Mahasiswa</a>
        <a href="keuangan.php" class="active">Keuangan</a>
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
                    <span class="username"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
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
            <h2>Manajemen Keuangan</h2>
            <p>Kelola pemasukan dan pengeluaran.</p>
        </div>
        <div class="dashboard-grid">
            <div class="card">
                <h3>Dana Anggaran</h3>
                <p class="card-value">Rp 150.000.000</p>
                <i class="fas fa-money-bill-wave card-icon"></i>
            </div>
            <div class="card">
                <h3>Total Pembayaran</h3>
                <p class="card-value">Rp 25.000.000</p>
                <i class="fas fa-hand-holding-dollar card-icon"></i>
            </div>
            <div class="card">
                <h3>Pending</h3>
                <p class="card-value">Rp 5.000.000</p>
                <i class="fas fa-clock card-icon"></i>
            </div>
        </div>
        <!-- Tambahkan tabel atau detail keuangan lain di sini jika diperlukan -->
    </main>
    <script src="JS/script.js"></script>
</body>
</html>
