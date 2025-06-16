<?php
// Konfigurasi koneksi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Default user XAMPP
define('DB_PASS', '');            // Kosongkan jika belum diset password MySQL
define('DB_NAME', 'simkip');      // Ganti sesuai nama database kamu

// Kunci enkripsi (gunakan nilai yang kuat dan acak untuk produksi)
define('ENCRYPTION_MASTER_KEY', 'ganti_dengan_kunci_rahasia_yang_kuat');

// Fungsi untuk membuat koneksi database
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Cek koneksi
    if ($conn->connect_error) {
        die('Koneksi database gagal: ' . $conn->connect_error);
    }

    // Set charset ke utf8mb4 untuk dukungan karakter penuh (emoji, simbol, dll)
    $conn->set_charset('utf8mb4');

    return $conn;
}
?>
