<?php
require_once 'config/auth.php';

// Check if user is logged in
if (!$auth->isAdmin()) {
    header("Location: login.php");
    exit;
}

// Get statistics from database
try {
    $totalMahasiswa = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
    $kipAktif = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE `No KIP` IS NOT NULL")->fetchColumn();
    $dokumenBaru = 15; // This should be replaced with actual query
} catch (PDOException $e) {
    error_log("Error fetching statistics: " . $e->getMessage());
    $totalMahasiswa = $kipAktif = $dokumenBaru = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Dashboard</h2>
            <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Mahasiswa</h3>
                <p class="card-value"><?php echo number_format($totalMahasiswa); ?></p>
                <i class="fas fa-users card-icon"></i>
            </div>
            <div class="card">
                <h3>Mahasiswa Aktif KIP</h3>
                <p class="card-value"><?php echo number_format($kipAktif); ?></p>
                <i class="fas fa-user-check card-icon"></i>
            </div>
            <div class="card">
                <h3>Total Mahasiswa Tidak Aktif</h3>
                <p class="card-value"><?php echo number_format($totalMahasiswa - $kipAktif); ?></p>
                <i class="fas fa-dollar-sign card-icon"></i>
            </div>
            <div class="card">
                <h3>Dokumen Baru</h3>
                <p class="card-value"><?php echo number_format($dokumenBaru); ?></p>
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
