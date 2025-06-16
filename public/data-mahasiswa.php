<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
// Data mahasiswa akan diambil dari database dan ditampilkan di tabel
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Mahasiswa SIM-KIP</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php">Dashboard</a>
        <a href="rekapitulasi.php">Rekapitulasi</a>
        <a href="data-mahasiswa.php" class="active">Data Mahasiswa</a>
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
            <h2>Data Mahasiswa</h2>
            <p>Kelola data mahasiswa KIP.</p>
        </div>
        <div class="section-container">
            <h3>Daftar Mahasiswa Terdaftar</h3>
            <div class="table-container">
                <table border="1" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat, Tgl Lahir</th>
                            <th>Agama</th>
                            <th>Nama Ibu</th>
                            <th>No. HP Ortu</th>
                            <th>Email Ortu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Contoh: ambil data dari database
                        // $result = $conn->query('SELECT * FROM mahasiswa');
                        // $no = 1;
                        // while ($row = $result->fetch_assoc()) {
                        //     echo '<tr>';
                        //     echo '<td>' . $no++ . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['nim']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['jenis_kelamin']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['tempat_tgl_lahir']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['agama']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['nama_ibu']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['no_hp_ortu']) . '</td>';
                        //     echo '<td>' . htmlspecialchars($row['email_ortu']) . '</td>';
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
