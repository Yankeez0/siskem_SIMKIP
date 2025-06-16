<?php
session_start();
include 'php/session_check.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: login-mahasiswa.php');
    exit;
}

$mahasiswa = [
    'nim' => $_SESSION['username'] ?? '',
    'nama' => $_SESSION['nama'] ?? 'Mahasiswa',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ajukan Perubahan Data - SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar" id="mahasiswaSidebar">
        <h3>SIM - KIP</h3>
        <a href="dashboard-mahasiswa.php">Dashboard Saya</a>
        <a href="ajukan-perubahan-data.php" class="active">Ajukan Perubahan Data</a>
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
            <h2>Ajukan Perubahan Data</h2>
            <p>Formulir ini digunakan untuk mengajukan perubahan data pribadi Anda kepada administrator.</p>
        </div>
        <div class="section-container">
            <div class="profile-card">
                <h3>Formulir Perubahan Data</h3>
                <form id="requestDataChangeForm" class="profile-form" method="post" action="php/proses_perubahan_data.php">
                    <div class="form-group-profile">
                        <label for="currentNIM">NIM (Tidak Dapat Diubah)</label>
                        <input type="text" id="currentNIM" name="currentNIM" value="<?php echo htmlspecialchars($mahasiswa['nim']); ?>" readonly>
                    </div>
                    <div class="form-group-profile">
                        <label for="currentNama">Nama Lengkap (Saat Ini)</label>
                        <input type="text" id="currentNama" name="currentNama" value="<?php echo htmlspecialchars($mahasiswa['nama']); ?>" readonly>
                    </div>
                    <h4>Data yang Diajukan untuk Perubahan</h4>
                    <div class="form-group-profile">
                        <label for="requestNama">Nama Lengkap Baru</label>
                        <input type="text" id="requestNama" name="requestNama" placeholder="Masukkan nama lengkap baru (jika ada)">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestJenisKelamin">Jenis Kelamin Baru</label>
                        <select id="requestJenisKelamin" name="requestJenisKelamin">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group-profile">
                        <label for="requestTempatTglLahir">Tempat, Tanggal Lahir Baru</label>
                        <input type="text" id="requestTempatTglLahir" name="requestTempatTglLahir" placeholder="Contoh: Jakarta, 01-01-2000">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestAgama">Agama Baru</label>
                        <input type="text" id="requestAgama" name="requestAgama" placeholder="Masukkan agama baru">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestNamaIbu">Nama Ibu Kandung Baru</label>
                        <input type="text" id="requestNamaIbu" name="requestNamaIbu" placeholder="Masukkan nama ibu kandung baru">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestNoHpOrtu">No. HP Orang Tua Baru</label>
                        <input type="text" id="requestNoHpOrtu" name="requestNoHpOrtu" placeholder="Masukkan nomor HP orang tua baru">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestEmailOrtu">Email Orang Tua Baru</label>
                        <input type="email" id="requestEmailOrtu" name="requestEmailOrtu" placeholder="Masukkan email orang tua baru">
                    </div>
                    <div class="form-group-profile">
                        <label for="alasanPerubahan">Alasan Perubahan</label>
                        <textarea id="alasanPerubahan" name="alasanPerubahan" rows="4" placeholder="Jelaskan mengapa Anda mengajukan perubahan data ini (misal: kesalahan data, perubahan status, dll.)" required></textarea>
                    </div>
                    <button type="submit" class="button-primary">Ajukan Perubahan</button>
                </form>
            </div>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
