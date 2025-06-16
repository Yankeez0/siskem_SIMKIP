-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql210.infinityfree.com
-- Waktu pembuatan: 16 Jun 2025 pada 10.39
-- Versi server: 10.6.19-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39240463_simkip`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','mahasiswa') DEFAULT 'mahasiswa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `created_at`, `avatar`) VALUES
(1, 'alip', 'melpi', 'Admin SIM-KIP', 'admin@simkip.com', 'admin', '2025-06-15 18:09:55', 'default.png'),
(2, 'admin', '$argon2id$v=19$m=65536,t=2,p=1$vDbLhowmY7+tqM7gczAdOQ$RT8w2ZoMzZVKx+RM0zalTVQ6IHZcGNACI1IpMgaIH9w', 'yudha-kelana', '', 'admin', '2025-06-15 18:53:11', 'assets/uploads/1750016833_Screenshot2025-06-02033426.png'),
(4, 'admin1', '$argon2id$v=19$m=65536,t=2,p=1$aab0b4k3L2c0SpKHLQZu5A$raFaRgOWE+w4bwXbTFjXuM68h5mQG8CgSkqkVDDcaMI', 'Admin Utama', '', 'admin', '2025-06-15 22:18:30', 'assets/uploads/1750027091_IMG-20200313-WA0028.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `agama` varchar(20) NOT NULL,
  `nama_ibu` blob DEFAULT NULL,
  `no_hp_ortu` varchar(15) NOT NULL,
  `email_ortu` varchar(100) DEFAULT NULL,
  `no_rekening` blob DEFAULT NULL,
  `status` enum('Aktif','Pending','Tidak Aktif') DEFAULT 'Pending',
  `periode_aktif` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `avatar` varchar(255) DEFAULT 'assets/uploads/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `password`, `nama`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `agama`, `nama_ibu`, `no_hp_ortu`, `email_ortu`, `no_rekening`, `status`, `periode_aktif`, `created_at`, `updated_at`, `avatar`) VALUES
(1, '12345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'Laki-laki', 'Jakarta', '2000-05-15', 'Islam', 0x5369746920416d696e6168, '081234567890', 'ibu.budi@email.com', NULL, 'Aktif', '2023/2024 Semester Ganjil', '2025-06-15 18:09:55', '2025-06-15 18:09:55', 'assets/uploads/default.png'),
(2, '2021005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ani Wijaya', 'Perempuan', 'Bandung', '2001-08-20', 'Kristen', 0x4d61726961, '081298765432', 'maria@email.com', NULL, 'Pending', NULL, '2025-06-15 18:09:55', '2025-06-15 18:09:55', 'assets/uploads/default.png'),
(3, '2023010', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Citra Dewi', 'Perempuan', 'Surabaya', '2002-03-10', 'Islam', 0x5261746e61, '081112223344', NULL, NULL, 'Tidak Aktif', NULL, '2025-06-15 18:09:55', '2025-06-15 18:09:55', 'assets/uploads/default.png'),
(5, '2301020111', '$2y$10$PmRc4tLPoqO5xBLCAYwM4e0bdngSjASlyPmAMHtm2Wqpm/NkbovyS', 'a', 'Laki-laki', 'a', '2025-06-16', 'a', 0x45656d474c436f4b546f6b2b424a6777596c5144656b3073416c4b57615a7332387875575837593d, 'a', 'a@gmail.com', 0x5366612b7555334a4b34724433334b364d4752347531774b4857544b4946624e6b6535724f65733d, 'Pending', NULL, '2025-06-15 21:59:59', '2025-06-15 21:59:59', 'assets/uploads/default.png'),
(6, '2301020133', '$2y$10$MEuURf.F5j1DGtQuedvLqO2UKSSZM6KKgzEjCWuaBblN7R3skOHvm', 'Tasbih', 'Laki-laki', 'Gunung Bintan', '2005-12-06', 'Islam Protestan', 0x714f32726b6741485a537938793363445a347a66436830344e7839672b64434d6666346f5a665138612f6e7547506b3d, '077123456799', 'ausyisauh@gmail.com', 0x5a312b66613665565031344c647151464637587a6970664e524a4c68586863614e76766d7778334d376e314169706e566b6c6f3d, 'Pending', NULL, '2025-06-15 22:37:30', '2025-06-15 22:37:30', 'assets/uploads/default.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `periode` varchar(50) NOT NULL,
  `tanggal_cair` date DEFAULT NULL,
  `jumlah` decimal(10,2) DEFAULT NULL,
  `status` enum('Cair','Pending','Gagal') DEFAULT 'Pending',
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `mahasiswa_id`, `periode`, `tanggal_cair`, `jumlah`, `status`, `keterangan`) VALUES
(1, 1, '2023/2024 Semester Ganjil', '2023-09-15', '4500000.00', 'Cair', 'Pencairan pertama'),
(2, 1, '2023/2024 Semester Genap', NULL, '4500000.00', 'Pending', 'Menunggu verifikasi'),
(3, 2, '2023/2024 Semester Ganjil', NULL, '4500000.00', 'Pending', 'Proses verifikasi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekening`
--

CREATE TABLE `rekening` (
  `id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `nama_bank` varchar(50) NOT NULL,
  `nomor_rekening` varchar(50) NOT NULL,
  `atas_nama` varchar(100) NOT NULL,
  `cabang_bank` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rekening`
--

INSERT INTO `rekening` (`id`, `mahasiswa_id`, `nama_bank`, `nomor_rekening`, `atas_nama`, `cabang_bank`) VALUES
(1, 1, 'Bank Mandiri', '1234567890', 'Budi Santoso', 'Jakarta Pusat'),
(2, 2, 'Bank BCA', '0987654321', 'Ani Wijaya', 'Bandung');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mahasiswa_id` (`mahasiswa_id`);

--
-- Indeks untuk tabel `rekening`
--
ALTER TABLE `rekening`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mahasiswa_id` (`mahasiswa_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `rekening`
--
ALTER TABLE `rekening`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rekening`
--
ALTER TABLE `rekening`
  ADD CONSTRAINT `rekening_ibfk_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
