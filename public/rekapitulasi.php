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
    <title>Rekapitulasi SIM-KIP</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php">Dashboard</a>
        <a href="rekapitulasi.php" class="active">Rekapitulasi</a>
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
            <h2>Rekapitulasi Data</h2>
            <p>Ringkasan dan laporan data penting.</p>
        </div>
        <div class="section-container">
            <h3>Rekapitulasi Pembayaran KIP</h3>
            <div class="filter-controls">
                <select>
                    <option>Filter Berdasarkan Angkatan</option>
                    <option>2024</option>
                    <option>2023</option>
                </select>
                <input type="text" placeholder="Cari Mahasiswa...">
                <button class="button-primary"><i class="fas fa-download"></i> Export Data</button>
            </div>
            <div class="table-container">
                <table border="1" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Status KIP</th>
                            <th>Periode Aktif</th>
                            <th>Pembayaran Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Contoh: ambil data rekap dari database
                        // $result = $conn->query('SELECT ... FROM mahasiswa ...');
                        // $no = 1;
                        // while ($row = $result->fetch_assoc()) {
                        //     echo '<tr>';
                        //     echo '<td>' . $no++ . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['nim']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['status_kip']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['periode_aktif']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['pembayaran_terakhir']) . '</td>';
                        //     echo '</tr>';
                        // }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="JS/script.js"></script>
</body>
</html>
