<?php
// Memulai session
session_start();

// Memeriksa apakah user sudah login dan memiliki role mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit();
}

// Include file konfigurasi database
require_once 'config.php';

// Ambil data mahasiswa yang sedang login
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM mahasiswa WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$mahasiswa = $result->fetch_assoc();

// Jika data mahasiswa tidak ditemukan, redirect ke login
if (!$mahasiswa) {
    session_destroy();
    header("Location: login.php?error=user_not_found");
    exit();
}

// Hitung status KIP
$status_kip = $mahasiswa['status_kip'] === 'Penerima' ? 'Aktif' : 'Tidak Aktif';
$status_class = $mahasiswa['status_kip'] === 'Penerima' ? 'status-active' : 'status-inactive';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="sidebar" id="mahasiswaSidebar">
        <h3>SIM - KIP</h3>
        <a href="dashboard-mahasiswa.php" class="active">Dashboard Saya</a>
        <a href="ajukan-perubahan-data.php">Ajukan Perubahan Data</a>
        <a href="profile-settings-mahasiswa.php">Pengaturan Profil</a>
        <a href="logout.php" id="mahasiswaLogout">Logout</a>
    </div>

    <main class="main-content">
        <div class="header-main">
            <div class="header-left">
                <span class="user-role-label">Mahasiswa</span>
            </div>
            <div class="header-right">
                <div class="header-icons">
                    <i class="fas fa-search"></i>
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile-toggle" id="userProfileToggle">
                    <img src="https://via.placeholder.com/35" alt="User Avatar" />
                    <span id="loggedInUserName"><?php echo htmlspecialchars($mahasiswa['nama_lengkap']); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu" id="userProfileDropdown">
                    <a href="profile-settings-mahasiswa.php" id="profileSettingsLinkDropdown">Pengaturan Profil</a>
                    <a href="help.php">Bantuan</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="page-header">
            <h2>Dashboard Saya</h2>
            <p>Selamat datang kembali, <span id="welcomeUserName"><?php echo htmlspecialchars($mahasiswa['nama_lengkap']); ?>!</span></p>
        </div>

        <div class="dashboard-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="info-content">
                    <h3>NIM</h3>
                    <p><?php echo htmlspecialchars($mahasiswa['nim']); ?></p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="info-content">
                    <h3>Angkatan</h3>
                    <p><?php echo htmlspecialchars($mahasiswa['angkatan'] ?? '-'); ?></p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="info-content">
                    <h3>Status KIP</h3>
                    <p class="<?php echo $status_class; ?>"><?php echo $status_kip; ?></p>
                </div>
            </div>
        </div>

        <div class="section-container">
            <h3>Informasi Pribadi</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nama Lengkap:</span>
                    <span class="info-value"><?php echo htmlspecialchars($mahasiswa['nama_lengkap']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jenis Kelamin:</span>
                    <span class="info-value"><?php echo htmlspecialchars($mahasiswa['jenis_kelamin']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tempat, Tgl Lahir:</span>
                    <span class="info-value"><?php echo htmlspecialchars($mahasiswa['tempat_tgl_lahir']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Agama:</span>
                    <span class="info-value"><?php echo htmlspecialchars($mahasiswa['agama']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Ibu Kandung:</span>
                    <span class="info-value"><?php echo htmlspecialchars($mahasiswa['nama_ibu']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">No. HP Orang Tua:</span>
                    <span class="info-value"><?php echo htmlspecialchars($mahasiswa['no_hp_ortu']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email Orang Tua:</span>
                    <span class="info-value"><?php echo htmlspecialchars($mahasiswa['email_ortu']); ?></span>
                </div>
            </div>
        </div>

        <div class="section-container">
            <h3>Status Pendaftaran KIP</h3>
            <div class="status-card">
                <div class="status-header">
                    <h4>Kartu Indonesia Pintar</h4>
                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_kip; ?></span>
                </div>
                <div class="status-content">
                    <?php if ($mahasiswa['status_kip'] === 'Penerima'): ?>
                        <p>Selamat! Anda terdaftar sebagai penerima KIP.</p>
                        <p>Periode aktif: <?php echo htmlspecialchars($mahasiswa['periode_aktif'] ?? '2024/2025'); ?></p>
                    <?php else: ?>
                        <p>Anda belum terdaftar sebagai penerima KIP.</p>
                        <p>Silakan hubungi admin untuk informasi lebih lanjut.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi dropdown menu
            const userProfileToggle = document.getElementById('userProfileToggle');
            const userProfileDropdown = document.getElementById('userProfileDropdown');

            userProfileToggle.addEventListener('click', function() {
                userProfileDropdown.classList.toggle('show');
            });

            // Tutup dropdown saat klik di luar
            window.addEventListener('click', function(event) {
                if (!event.target.matches('.user-profile-toggle') && !event.target.closest('.user-profile-toggle')) {
                    if (userProfileDropdown.classList.contains('show')) {
                        userProfileDropdown.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>
</html>
