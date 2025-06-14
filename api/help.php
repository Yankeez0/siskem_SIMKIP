<?php
require_once 'config/auth.php';

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bantuan - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Bantuan</h2>
            <p>Panduan penggunaan sistem SIM-KIP.</p>
        </div>

        <div class="help-section">
            <?php if ($auth->isAdmin()): ?>
            <div class="section-container">
                <h3>Panduan Admin</h3>
                
                <div class="accordion-item">
                    <button class="accordion-header">
                        Bagaimana cara menambah data mahasiswa?
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Untuk menambah data mahasiswa baru:</p>
                        <ol>
                            <li>Buka menu "Data Mahasiswa"</li>
                            <li>Isi formulir dengan data lengkap mahasiswa</li>
                            <li>Klik tombol "Tambahkan Mahasiswa"</li>
                            <li>Password awal akan digenerate secara otomatis</li>
                        </ol>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        Bagaimana cara mengexport data?
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Untuk mengexport data:</p>
                        <ol>
                            <li>Buka menu "Rekapitulasi"</li>
                            <li>Pilih format export (Excel atau PDF)</li>
                            <li>Klik tombol download yang sesuai</li>
                        </ol>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($auth->isMahasiswa()): ?>
            <div class="section-container">
                <h3>Panduan Mahasiswa</h3>
                
                <div class="accordion-item">
                    <button class="accordion-header">
                        Bagaimana cara mengubah password?
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Untuk mengubah password:</p>
                        <ol>
                            <li>Klik menu "Pengaturan Profil"</li>
                            <li>Scroll ke bagian "Ubah Password"</li>
                            <li>Masukkan password lama dan password baru</li>
                            <li>Klik "Ubah Password"</li>
                        </ol>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        Bagaimana cara mengajukan perubahan data?
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Untuk mengajukan perubahan data:</p>
                        <ol>
                            <li>Klik menu "Ajukan Perubahan Data"</li>
                            <li>Isi formulir dengan data yang ingin diubah</li>
                            <li>Lampirkan dokumen pendukung jika diperlukan</li>
                            <li>Klik "Ajukan Perubahan"</li>
                        </ol>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="section-container">
                <h3>Kontak</h3>
                <div class="contact-info">
                    <p><i class="fas fa-envelope"></i> Email: support@simkip.ac.id</p>
                    <p><i class="fas fa-phone"></i> Telepon: (021) 1234567</p>
                    <p><i class="fas fa-clock"></i> Jam Kerja: Senin - Jumat, 08:00 - 16:00 WIB</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Accordion functionality
            document.querySelectorAll('.accordion-header').forEach(button => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;
                    button.classList.toggle('active');
                    content.classList.toggle('show');
                    
                    // Close other accordions
                    document.querySelectorAll('.accordion-content').forEach(item => {
                        if (item !== content) {
                            item.classList.remove('show');
                            item.previousElementSibling.classList.remove('active');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
