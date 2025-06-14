-- phpMyAdmin SQL Dump
-- Table structure for table `perubahan_data`

CREATE TABLE `perubahan_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `current_value` text NOT NULL,
  `requested_value` text NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('Pending','Disetujui','Ditolak') NOT NULL DEFAULT 'Pending',
  `keterangan` text DEFAULT NULL,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `tanggal_pengajuan` datetime NOT NULL,
  `tanggal_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mahasiswa_id` (`mahasiswa_id`),
  CONSTRAINT `perubahan_data_ibfk_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id Mahasiswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add columns for payment tracking in mahasiswa table
ALTER TABLE `mahasiswa`
ADD COLUMN `status_pembayaran` enum('Pending','Diproses','Dicairkan','Ditolak') DEFAULT 'Pending',
ADD COLUMN `tanggal_pencairan` date DEFAULT NULL,
ADD COLUMN `jumlah_pencairan` decimal(12,2) DEFAULT NULL,
ADD COLUMN `keterangan` text DEFAULT NULL;
