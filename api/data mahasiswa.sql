-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 06:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biodata_mahasiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id Mahasiswa` int(11) NOT NULL,
  `Nomor Induk Mahasiswa (NIM)` varchar(20) NOT NULL,
  `Nama Siswa` varchar(100) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `Nomor Induk Kependudukan (NIK)` varchar(20) NOT NULL,
  `No KIP` varchar(20) NOT NULL,
  `Nomor Rekening` varchar(100) NOT NULL,
  `Tempat Lahir` varchar(100) NOT NULL,
  `Tanggal Lahir` date NOT NULL,
  `Jenis Kelamin` enum('Laki-Laki','Perempuan') NOT NULL,
  `Agama` varchar(20) NOT NULL,
  `Alamat` text NOT NULL,
  `Provinsi` varchar(50) NOT NULL,
  `Kabupaten/Kota` varchar(50) NOT NULL,
  `Kode Pos` varchar(10) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `No Handphone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id Mahasiswa`, `Nomor Induk Mahasiswa (NIM)`, `Nama Siswa`, `Password`, `Nomor Induk Kependudukan (NIK)`, `No KIP`, `Nomor Rekening`, `Tempat Lahir`, `Tanggal Lahir`, `Jenis Kelamin`, `Agama`, `Alamat`, `Provinsi`, `Kabupaten/Kota`, `Kode Pos`, `Email`, `No Handphone`) VALUES
(1001, '2401020012', 'Budi Santoso', 'Budi2912', '2172011503040001', 'KIPTPN001', '0987-6543-2100-123', 'Tanjung Pinang', '2005-12-29', 'Laki-Laki', 'Islam', 'Jl. Bakti No. 5, Batu 9', 'Kepulauan Riau', 'Tanjung Pinang', '29125', 'budisnts@gmail.com', '081234567890'),
(1002, '2401020090', 'Indah Permata Sari', 'IndahPS123', '2172022809050002', 'KIPTPN002', '5432-1098-7654-321\r\n', 'Batam', '2006-12-10', 'Perempuan', 'Islam', 'Jl. Pramuka No. 12, Batu 7', 'Kepulauan Riau', 'Tanjung Pinang', '29123', 'indahpmsr10@gmail.com', '081345678901'),
(1003, '2201020076', 'Liana Shaqina', 'LiliSha08', '2172030101060003', 'KIPTPN003', '1122-3344-5566-778\r\n', 'DKI Jakarta', '2003-11-08', 'Perempuan', 'Kristen', 'Jl. Sei Jang No. 20, Batu 4', 'Kepulauan Riau', 'Tanjung Pinang', '29113', 'lianaqina@gamil.com', '081567890123'),
(1004, '2301020390', 'Citra Kirana', 'Iraa0607', '2172041707050004', 'KIPTPN004', '9988-7766-5544-332\r\n', 'Tanjung Pinang', '2005-07-06', 'Perempuan', 'Islam', 'Perumahan Kijang Kencana III Blok C No. 1', 'Kepulauan Riau', 'Tanjung Pinang', '29124', 'citrakirana@email.com\r\n', '081789012345'),
(1005, '2301020300', 'Muhammad Putra Pratama', 'Putra12345', '2172052512040005', 'KIPTPN005', '2345-6789-0123-456', 'Tanjung Balai Karimun', '2005-03-09', 'Laki-Laki', 'Islam', 'Jl. WR Supratman KM 8, Gang Cempaka', 'Kepulauan Riau', 'Tanjung Pinang', '29122', 'Putraprtm09@gmail.com', '081901234567');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id Mahasiswa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id Mahasiswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
