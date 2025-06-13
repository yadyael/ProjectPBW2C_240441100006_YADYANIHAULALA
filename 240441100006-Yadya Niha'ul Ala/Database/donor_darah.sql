-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 05:10 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `donor_darah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(1, 'Zii', 'isterikecilzayne@gmail.com', '$2y$10$kdGeF5KgGagybrt2OGWplO9ARpyTTfdSVB5R/nKkr09BCxSKfzOX.');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `waktu` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id`, `tanggal`, `lokasi`, `waktu`) VALUES
(1, '2025-06-14', 'Gedung Pertemuan', '20:00:00'),
(2, '2025-06-14', 'Klinik UTM', '17:00:00'),
(3, '2025-06-13', 'Gedung Cakra Lt. 4', '09:00:00'),
(5, '2025-06-14', 'Gedung Cakra Lt. 1', '11:30:00'),
(6, '2025-06-15', 'Klinik UTM', '09:15:00'),
(7, '2025-06-16', 'Puskesmas Pelita Uwu', '13:00:00'),
(8, '2025-06-18', 'PMI Cabang Teyvat', '16:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nik` char(16) NOT NULL,
  `usia` int(11) NOT NULL,
  `berat_badan` float NOT NULL,
  `gol_darah` enum('A','B','AB','O') NOT NULL,
  `rhesus` enum('+','-') NOT NULL,
  `sudah_sarapan` enum('Ya','Tidak') NOT NULL,
  `haid_hamil_menyusui` enum('Tidak','Haid','Hamil','Menyusui') NOT NULL,
  `penyakit` text DEFAULT NULL,
  `konsumsi_alkohol` enum('Tidak Pernah','Pernah') NOT NULL,
  `terakhir_donor` enum('Pertama kali','< 2 bulan','> 2 bulan') NOT NULL,
  `jenis_identitas` enum('KTP','SIM','Kartu Pelajar','KTM') NOT NULL,
  `jadwal_id` int(11) DEFAULT NULL,
  `status` enum('Lolos','Gagal') NOT NULL,
  `alasan_gagal` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id`, `nama`, `nik`, `usia`, `berat_badan`, `gol_darah`, `rhesus`, `sudah_sarapan`, `haid_hamil_menyusui`, `penyakit`, `konsumsi_alkohol`, `terakhir_donor`, `jenis_identitas`, `jadwal_id`, `status`, `alasan_gagal`) VALUES
(4, 'Lin', '2444353565768795', 18, 45, 'B', '+', 'Ya', 'Tidak', 'Tidak ada', 'Tidak Pernah', 'Pertama kali', 'Kartu Pelajar', 2, 'Lolos', NULL),
(5, 'Lu Feng', '9279827219872911', 31, 75, 'AB', '+', 'Tidak', 'Tidak', 'Nonchalant', 'Tidak Pernah', '< 2 bulan', 'KTP', 2, 'Gagal', 'Belum sarapan; Terakhir donor kurang dari 2 bulan'),
(6, 'Niha', '5354423423346787', 19, 40, 'B', '+', 'Ya', 'Haid', 'Tidak ada', 'Tidak Pernah', 'Pertama kali', 'KTM', 2, 'Gagal', 'Berat badan kurang dari 45 kg; Dalam kondisi haid/hamil/menyusui'),
(7, 'Ala', '5636287923134444', 19, 46, 'B', '-', 'Ya', 'Tidak', 'Tidak ada', 'Tidak Pernah', '> 2 bulan', 'KTM', 1, 'Lolos', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_id` (`jadwal_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
