<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: login-mahasiswa.php');
    exit;
}
$mahasiswa = [
    'nim' => $_SESSION['username'] ?? '',
    'nama' => $_SESSION['nama'] ?? 'Mahasiswa',
    'jenis_kelamin' => $_SESSION['jenis_kelamin'] ?? '-',
    'tempat_tgl_lahir' => $_SESSION['tempat_tgl_lahir'] ?? '-',
    'agama' => $_SESSION['agama'] ?? '-',
    'nama_ibu' => $_SESSION['nama_ibu'] ?? '-',
    'no_hp_ortu' => $_SESSION['no_hp_ortu'] ?? '-',
    'email_ortu' => $_SESSION['email_ortu'] ?? '-',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengaturan Profil Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar" id="mahasiswaSidebar">
        <h3>SIM - KIP</h3>
        <a href="dashboard-mahasiswa.php">Dashboard Saya</a>
        <a href="ajukan-perubahan-data.php">Ajukan Perubahan Data</a>
        <a href="profile-settings-mahasiswa.php" class="active">Pengaturan Profil</a>
        <a href="login.php" id="mahasiswaLogout">Logout</a>
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
                    <span id="loggedInUserName"><?php echo htmlspecialchars($mahasiswa['nama']); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu" id="userProfileDropdown">
                    <a href="profile-settings-mahasiswa.php" id="profileSettingsLinkDropdown">Pengaturan Profil</a>
                    <a href="help.php">Bantuan</a>
                    <a href="login.php">Logout</a>
                </div>
            </div>
        </div>
        <div class="page-header">
            <h2>Pengaturan Profil Mahasiswa</h2>
            <p>Kelola informasi akun dan kata sandi Anda.</p>
        </div>
        <div class="section-container">
            <div class="profile-card">
                <h3>Informasi Profil</h3>
                <div class="profile-info-display">
                    <div class="info-group">
                        <label>NIM:</label>
                        <span id="displayNIM"><?php echo htmlspecialchars($mahasiswa['nim']); ?></span>
                    </div>
                    <div class="info-group">
                        <label>Nama Lengkap:</label>
                        <span id="displayNama"><?php echo htmlspecialchars($mahasiswa['nama']); ?></span>
                    </div>
                    <div class="info-group">
                        <label>Jenis Kelamin:</label>
                        <span id="displayJenisKelamin"><?php echo htmlspecialchars($mahasiswa['jenis_kelamin']); ?></span>
                    </div>
                    <div class="info-group">
                        <label>Tempat, Tanggal Lahir:</label>
                        <span id="displayTempatTglLahir"><?php echo htmlspecialchars($mahasiswa['tempat_tgl_lahir']); ?></span>
                    </div>
                    <div class="info-group">
                        <label>Agama:</label>
                        <span id="displayAgama"><?php echo htmlspecialchars($mahasiswa['agama']); ?></span>
                    </div>
                    <div class="info-group">
                        <label>Nama Ibu Kandung:</label>
                        <span id="displayNamaIbu"><?php echo htmlspecialchars($mahasiswa['nama_ibu']); ?></span>
                    </div>
                    <div class="info-group">
                        <label>No. HP Orang Tua:</label>
                        <span id="displayNoHpOrtu"><?php echo htmlspecialchars($mahasiswa['no_hp_ortu']); ?></span>
                    </div>
                    <div class="info-group">
                        <label>Email Orang Tua:</label>
                        <span id="displayEmailOrtu"><?php echo htmlspecialchars($mahasiswa['email_ortu']); ?></span>
                    </div>
                </div>
                <!-- Form edit profil dan ubah password bisa dibuat dinamis dengan PHP jika diinginkan -->
            </div>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
