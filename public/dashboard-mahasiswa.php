<?php
session_start();
include 'php/session_check.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: login-mahasiswa.php');
    exit;
}

// Contoh: data biodata mahasiswa dari session/database
$default_biodata = [
    'nim' => $_SESSION['username'] ?? '',
    'nama' => $_SESSION['nama'] ?? 'Mahasiswa',
    'jenis_kelamin' => $_SESSION['jenis_kelamin'] ?? '-',
    'tempat_tgl_lahir' => $_SESSION['tempat_tgl_lahir'] ?? '-',
    'agama' => $_SESSION['agama'] ?? '-',
    'nama_ibu' => $_SESSION['nama_ibu'] ?? '-',
    'no_hp_ortu' => $_SESSION['no_hp_ortu'] ?? '-',
    'email_ortu' => $_SESSION['email_ortu'] ?? '-',
    'status_kip' => $_SESSION['status_kip'] ?? '-',
    'periode_aktif' => $_SESSION['periode_aktif'] ?? '-',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar" id="mahasiswaSidebar">
        <h3>SIM - KIP</h3>
        <a href="dashboard-mahasiswa.php" class="active">Dashboard Saya</a>
        <a href="ajukan-perubahan-data.php">Ajukan Perubahan Data</a>
        <a href="profile-settings-mahasiswa.php">Pengaturan Profil</a>
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
                    <span id="loggedInUserName"><?php echo htmlspecialchars($default_biodata['nama']); ?></span>
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
            <h2>Dashboard Saya</h2>
            <p>Selamat datang kembali, <span id="welcomeUserName"><?php echo htmlspecialchars($default_biodata['nama']); ?>!</span></p>
        </div>
        <div class="section-container">
            <h3>Informasi Pribadi</h3>
            <div class="profile-info-display">
                <div class="info-group">
                    <label>NIM:</label>
                    <span id="displayNIM"><?php echo htmlspecialchars($default_biodata['nim']); ?></span>
                </div>
                <div class="info-group">
                    <label>Nama Lengkap:</label>
                    <span id="displayNama"><?php echo htmlspecialchars($default_biodata['nama']); ?></span>
                </div>
                <div class="info-group">
                    <label>Jenis Kelamin:</label>
                    <span id="displayJenisKelamin"><?php echo htmlspecialchars($default_biodata['jenis_kelamin']); ?></span>
                </div>
                <div class="info-group">
                    <label>Tempat, Tanggal Lahir:</label>
                    <span id="displayTempatTglLahir"><?php echo htmlspecialchars($default_biodata['tempat_tgl_lahir']); ?></span>
                </div>
                <div class="info-group">
                    <label>Agama:</label>
                    <span id="displayAgama"><?php echo htmlspecialchars($default_biodata['agama']); ?></span>
                </div>
                <div class="info-group">
                    <label>Nama Ibu Kandung:</label>
                    <span id="displayNamaIbu"><?php echo htmlspecialchars($default_biodata['nama_ibu']); ?></span>
                </div>
                <div class="info-group">
                    <label>No. HP Orang Tua:</label>
                    <span id="displayNoHpOrtu"><?php echo htmlspecialchars($default_biodata['no_hp_ortu']); ?></span>
                </div>
                <div class="info-group">
                    <label>Email Orang Tua:</label>
                    <span id="displayEmailOrtu"><?php echo htmlspecialchars($default_biodata['email_ortu']); ?></span>
                </div>
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <button class="button-primary" onclick="location.href='profile-settings-mahasiswa.php'">
                    <i class="fas fa-edit"></i> Edit Data
                </button>
            </div>
        </div>
        <div class="section-container">
            <h3>Status KIP Anda</h3>
            <div class="status-display">
                <div class="info-group">
                    <label>Status KIP:</label>
                    <span id="displayStatusKIP" class="status-badge"><?php echo htmlspecialchars($default_biodata['status_kip']); ?></span>
                </div>
                <div class="info-group">
                    <label>Periode Aktif:</label>
                    <span id="displayPeriodeAktif"><?php echo htmlspecialchars($default_biodata['periode_aktif']); ?></span>
                </div>
                <p class="status-note" id="statusKIPNote">
                    <?php
                    if ($default_biodata['status_kip'] === 'Aktif') {
                        echo 'Selamat! Status KIP Anda aktif untuk periode yang disebutkan.';
                    } elseif ($default_biodata['status_kip'] === 'Pending') {
                        echo 'Status KIP Anda masih dalam proses peninjauan.';
                    } elseif ($default_biodata['status_kip'] === 'Tidak Aktif') {
                        echo 'Status KIP Anda tidak aktif. Hubungi admin.';
                    } else {
                        echo 'Informasi status KIP tidak tersedia.';
                    }
                    ?>
                </p>
            </div>
        </div>
    </main>
    <script src="JS/script.js"></script>
</body>
</html>
