<?php
// Memulai session
session_start();

// Memeriksa apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include file konfigurasi database
require_once 'config.php';

// Ambil statistik dari database
$stats = [
    'total_mahasiswa' => 0,
    'mahasiswa_aktif' => 0,
    'mahasiswa_non_aktif' => 0,
    'dokumen_baru' => 0
];

// Hitung total mahasiswa
$query = "SELECT COUNT(*) as total FROM mahasiswa";
$result = $conn->query($query);
if ($result) {
    $stats['total_mahasiswa'] = $result->fetch_assoc()['total'];
}

// Hitung mahasiswa aktif KIP
$query = "SELECT COUNT(*) as total FROM mahasiswa WHERE status_kip = 'Penerima'";
$result = $conn->query($query);
if ($result) {
    $stats['mahasiswa_aktif'] = $result->fetch_assoc()['total'];
}

// Hitung mahasiswa non-aktif
$query = "SELECT COUNT(*) as total FROM mahasiswa WHERE status_kip != 'Penermi' OR status_kip IS NULL";
$result = $conn->query($query);
if ($result) {
    $stats['mahasiswa_non_aktif'] = $result->fetch_assoc()['total'];
}

// Hitung dokumen baru (contoh: pengajuan perubahan dalam 7 hari terakhir)
$query = "SELECT COUNT(*) as total FROM pengajuan_perubahan WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$result = $conn->query($query);
if ($result) {
    $stats['dokumen_baru'] = $result->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php" class="active">Dashboard</a>
        <a href="rekapitulasi.php">Rekapitulasi</a>
        <a href="data-mahasiswa.php">Data Mahasiswa</a>
        <a href="keuangan.php">Keuangan</a>
        <a href="logout.php">Logout</a>
    </div>

    <main class="main-content">
        <div class="header-main">
            <div class="header-left">
                <span class="admin-label">Admin</span>
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
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="page-header">
            <h2>Dashboard</h2>
            <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Mahasiswa</h3>
                <p class="card-value"><?php echo number_format($stats['total_mahasiswa']); ?></p>
                <i class="fas fa-users card-icon"></i>
            </div>
            <div class="card">
                <h3>Mahasiswa Aktif KIP</h3>
                <p class="card-value"><?php echo number_format($stats['mahasiswa_aktif']); ?></p>
                <i class="fas fa-user-check card-icon"></i>
            </div>
            <div class="card">
                <h3>Mahasiswa Non-Aktif</h3>
                <p class="card-value"><?php echo number_format($stats['mahasiswa_non_aktif']); ?></p>
                <i class="fas fa-user-times card-icon"></i>
            </div>
            <div class="card">
                <h3>Dokumen Baru</h3>
                <p class="card-value"><?php echo number_format($stats['dokumen_baru']); ?></p>
                <i class="fas fa-file-alt card-icon"></i>
            </div>
        </div>

        <div class="chart-section">
            <h3>Statistik KIP</h3>
            <div class="chart-placeholder">
                <p>Grafik Mahasiswa KIP Tahun Ini</p>
                <!-- Di sini bisa ditambahkan grafik menggunakan Chart.js atau library lainnya -->
                <canvas id="kipChart" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="recent-activity">
            <h3>Aktivitas Terbaru</h3>
            <div class="activity-list">
                <?php
                // Ambil 5 aktivitas terbaru
                $query = "SELECT * FROM pengajuan_perubahan 
                         ORDER BY created_at DESC 
                         LIMIT 5";
                $result = $conn->query($query);
                
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="activity-item">';
                        echo '<div class="activity-icon"><i class="fas fa-edit"></i></div>';
                        echo '<div class="activity-details">';
                        echo '<p>Pengajuan perubahan data baru</p>';
                        echo '<span class="activity-time">' . date('d M Y H:i', strtotime($row['created_at'])) . '</span>';
                        echo '</div></div>';
                    }
                } else {
                    echo '<p>Tidak ada aktivitas terbaru</p>';
                }
                ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
    <script>
        // Inisialisasi grafik
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('kipChart').getContext('2d');
            const kipChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Jumlah Penerima KIP',
                        data: [12, 19, 3, 5, 2, 3, 7, 10, 15, 20, 25, 30],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Toggle dropdown profil
            const profileToggle = document.getElementById('userProfileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (profileToggle && profileDropdown) {
                profileToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('show');
                });

                // Tutup dropdown saat klik di luar
                document.addEventListener('click', function() {
                    if (profileDropdown.classList.contains('show')) {
                        profileDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>
