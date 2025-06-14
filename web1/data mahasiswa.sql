-- Buat database
CREATE DATABASE IF NOT EXISTS simkip;
USE simkip;

-- Tabel mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    agama VARCHAR(20) NOT NULL,
    nama_ibu VARCHAR(100) NOT NULL,
    no_hp_ortu VARCHAR(15) NOT NULL,
    email_ortu VARCHAR(100),
    status_kip ENUM('Aktif', 'Pending', 'Tidak Aktif') DEFAULT 'Pending',
    periode_aktif VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel pembayaran
CREATE TABLE IF NOT EXISTS pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    periode VARCHAR(50) NOT NULL,
    tanggal_cair DATE,
    jumlah DECIMAL(10,2),
    status ENUM('Cair', 'Pending', 'Gagal') DEFAULT 'Pending',
    keterangan TEXT,
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
);

-- Tabel rekening
CREATE TABLE IF NOT EXISTS rekening (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    nama_bank VARCHAR(50) NOT NULL,
    nomor_rekening VARCHAR(50) NOT NULL,
    atas_nama VARCHAR(100) NOT NULL,
    cabang_bank VARCHAR(100),
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
);

-- Data admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data admin contoh
INSERT INTO admin (username, password, nama_lengkap, email) 
VALUES ('alip', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin SIM-KIP', 'admin@simkip.com');

-- Insert data mahasiswa contoh
INSERT INTO mahasiswa (nim, password, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, nama_ibu, no_hp_ortu, email_ortu, status_kip, periode_aktif) 
VALUES 
('12345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'Laki-laki', 'Jakarta', '2000-05-15', 'Islam', 'Siti Aminah', '081234567890', 'ibu.budi@email.com', 'Aktif', '2023/2024 Semester Ganjil'),
('2021005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ani Wijaya', 'Perempuan', 'Bandung', '2001-08-20', 'Kristen', 'Maria', '081298765432', 'maria@email.com', 'Pending', NULL),
('2023010', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Citra Dewi', 'Perempuan', 'Surabaya', '2002-03-10', 'Islam', 'Ratna', '081112223344', NULL, 'Tidak Aktif', NULL);

-- Insert data rekening contoh
INSERT INTO rekening (mahasiswa_id, nama_bank, nomor_rekening, atas_nama, cabang_bank)
VALUES 
(1, 'Bank Mandiri', '1234567890', 'Budi Santoso', 'Jakarta Pusat'),
(2, 'Bank BCA', '0987654321', 'Ani Wijaya', 'Bandung');

-- Insert data pembayaran contoh
INSERT INTO pembayaran (mahasiswa_id, periode, tanggal_cair, jumlah, status, keterangan)
VALUES 
(1, '2023/2024 Semester Ganjil', '2023-09-15', 4500000.00, 'Cair', 'Pencairan pertama'),
(1, '2023/2024 Semester Genap', NULL, 4500000.00, 'Pending', 'Menunggu verifikasi'),
(2, '2023/2024 Semester Ganjil', NULL, 4500000.00, 'Pending', 'Proses verifikasi');