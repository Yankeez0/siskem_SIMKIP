<?php
session_start();
// Halaman bantuan bisa diakses semua user, atau tambahkan pengecekan session jika ingin membatasi
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bantuan & FAQ - SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php">Dashboard</a>
        <a href="rekapitulasi.php">Rekapitulasi</a>
        <a href="data-mahasiswa.php">Data Mahasiswa</a>
        <a href="keuangan.php">Keuangan</a>
        <a href="profile-settings.php">Pengaturan Profil</a>
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
                    <span class="username">SIM-KIP</span>
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
            <h2>Bantuan & FAQ</h2>
            <p>Temukan jawaban atas pertanyaan Anda atau hubungi kami.</p>
        </div>
        <div class="help-container">
            <div class="help-section">
                <h3>Pertanyaan yang Sering Diajukan (FAQ)</h3>
                <div class="faq-accordion">
                    <div class="accordion-item">
                        <button class="accordion-header">
                            Bagaimana cara menambahkan data mahasiswa baru?
                            <i class="fas fa-chevron-down accordion-icon"></i>
                        </button>
                        <div class="accordion-content">
                            <p>Untuk menambahkan data mahasiswa baru, navigasikan ke menu "Data Mahasiswa" di sidebar. Klik tombol "Tambah Mahasiswa" dan lengkapi formulir yang disediakan. Pastikan semua informasi yang diperlukan telah diisi dengan benar.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <button class="accordion-header">
                            Apakah saya bisa mengedit data mahasiswa yang sudah ada?
                            <i class="fas fa-chevron-down accordion-icon"></i>
                        </button>
                        <div class="accordion-content">
                            <p>Ya, Anda bisa mengedit data mahasiswa. Pada halaman "Data Mahasiswa", temukan mahasiswa yang ingin diedit, lalu klik ikon pensil (edit) di samping namanya. Anda akan diarahkan ke formulir pengeditan.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <button class="accordion-header">
                            Bagaimana cara melihat rekapitulasi data KIP?
                            <i class="fas fa-chevron-down accordion-icon"></i>
                        </button>
                        <div class="accordion-content">
                            <p>Data rekapitulasi KIP dapat dilihat di menu "Rekapitulasi". Di sana, Anda akan menemukan ringkasan statistik dan laporan terkait penerima KIP.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <button class="accordion-header">
                            Apa yang harus saya lakukan jika lupa password?
                            <i class="fas fa-chevron-down accordion-icon"></i>
                        </button>
                        <div class="accordion-content">
                            <p>Jika Anda lupa password, silakan klik tautan "Lupa Password?" di halaman login. Ikuti instruksi untuk mengatur ulang password Anda. Jika masalah berlanjut, hubungi administrator sistem.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="help-section contact-section">
                <h3>Hubungi Kami</h3>
                <p>Jika Anda memiliki pertanyaan lain atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi tim dukungan kami.</p>
                <div class="contact-info">
                    <p><i class="fas fa-envelope"></i> Email: <a href="mailto:support@simkip.com">support@simkip.com</a></p>
                    <p><i class="fas fa-phone"></i> Telepon: (021) 123-4567</p>
                </div>
            </div>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
