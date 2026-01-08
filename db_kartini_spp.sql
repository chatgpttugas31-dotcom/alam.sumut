-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 04:11 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kartini_spp`
--

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `bulan` varchar(20) DEFAULT NULL,
  `tahun` varchar(4) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `tanggal_bayar` datetime DEFAULT current_timestamp(),
  `bukti` varchar(255) DEFAULT NULL,
  `status` enum('pending','lunas','ditolak') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `id_siswa`, `bulan`, `tahun`, `jumlah`, `tanggal_bayar`, `bukti`, `status`) VALUES
(8, 7, 'Agustus', '2025', 250000, '2025-08-02 00:00:00', '1754114995_siswa7.png', 'lunas'),
(9, 10, 'Agustus', '2025', 250000, '2025-08-02 00:00:00', '1754115793_siswa10.png', 'lunas'),
(10, 11, 'Agustus', '2025', 250000, '2025-08-02 00:00:00', '1754124965_siswa11.png', 'lunas'),
(13, 30, 'Agustus', '2025', 250000, '2025-08-12 00:00:00', '1754974953_siswa30.jpeg', 'lunas'),
(14, 2, 'Agustus', '2025', 250000, '2025-08-15 00:00:00', '1755276631_siswa2.jpg', 'lunas'),
(15, 19, 'Agustus', '2025', 250000, '2025-08-23 00:00:00', '1755967866_siswa19.png', 'lunas'),
(16, 45, 'Agustus', '2025', 250000, '2025-08-26 00:00:00', '1756211397_siswa45.png', 'lunas'),
(17, 2, 'September', '2025', 250000, '2025-09-11 00:00:00', '1757580768_siswa2.png', 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `nis` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `id_user`, `nama`, `kelas`, `nis`) VALUES
(1, 4, 'eva nurmalasari pasaribu', 'VII-A', '121203'),
(2, 2, 'kezia br purba ', 'VII-B', '121202'),
(3, 3, 'eka bangun', 'IX-B', '121201'),
(7, 8, 'michael', 'VII-A', '121204'),
(8, 9, 'edward warsito', 'VII-A', '121205'),
(9, 14, 'riska', 'VII-A', '121210'),
(10, 15, 'mikha nababan', 'VII-B', '121211'),
(11, 16, 'risky', 'VIII-B', '121213'),
(12, 17, 'grace pasaribu', 'VIII-A', '121214'),
(18, 18, 'ariston', 'VIII-B', '121215'),
(19, 19, 'amelia', 'IX-A', '121218'),
(20, 20, 'kristiani ', 'VII-C', '121219'),
(21, 21, 'merry', 'VIII-C', '121220'),
(22, 22, 'Randi silaen', 'IX-A', '121221'),
(23, 23, 'jelita', 'VII-B', '121222'),
(24, 24, 'andre', 'IX-C', '121223'),
(25, 25, 'jeky', 'VIII-B', '121224'),
(27, 27, 'masriani', 'VIII-A', '121226'),
(29, 29, 'jeffry', 'IX-C', '121228'),
(30, 30, 'aldi', 'VII-B', '121230'),
(31, 31, 'indris sanjaya', 'IX-A', '121231'),
(33, 33, 'zefanya', 'IX-B', '121232'),
(35, 35, 'nico tambunan', 'IX-C', '121234'),
(38, 38, 'Raya Panjaitan', 'IX-c', '121236'),
(41, 41, 'ferdinand', 'VII-C', '121239'),
(45, 45, 'valery', 'IX-C', '121241'),
(46, 46, 'raisa', 'IX-C', '121237'),
(47, 47, 'nadira ', 'VII-A', '222111');

-- --------------------------------------------------------

--
-- Table structure for table `spp`
--

CREATE TABLE `spp` (
  `id` int(11) NOT NULL,
  `tahun` int(4) DEFAULT NULL,
  `nominal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spp`
--

INSERT INTO `spp` (`id`, `tahun`, `nominal`) VALUES
(1, 2025, 250000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `level` enum('admin','siswa') DEFAULT NULL,
  `plain_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `plain_password`) VALUES
(1, 'admin', '$2y$10$mXsEwJWBVHqydwl9DxE6UOgczPkDzR9Oql9T5AzpP/TsC1XsVZL2O', 'admin', 'admin123'),
(2, 'Kezia br Purba', '$2y$10$gwApzuPo4GaTwJkjpCSQ3e.0zwLn.VPb3P1Da.q0HILhunOc5.q3i', 'siswa', '121202'),
(3, 'eka bangun', '$2y$10$FOoXv1E0AtiDq6Q3123lROor5QH/NzKI732jp6s0rjbiMDin7DKgu', 'siswa', '121201'),
(4, 'eva nurmalasari pasaribu', '$2y$10$QKoX4.giGi3UbQtIgwcwF.yFa7X.eKu2oVpg4ZUpRu8n5lVuxR8S2', 'siswa', '121203'),
(8, 'michael', '$2y$10$2W9ZXaRtQzLcl5xWTceYwe8mrdEJ0WVdwEL3wQmZyVg31hYoA36OO', 'siswa', '121204'),
(9, 'edward warsito', '$2y$10$1AMTF4IM7OwqgOvbCR1tX.WxI/tKJFAM7Xm38CYGVVzbbRKAJSEkK', 'siswa', '121205'),
(14, 'riska', '$2y$10$AvPr/VPq5p/5GOWEFp6jzuFuadnMm6qYJxAKO206OekpNnojk1EN.', 'siswa', '121210'),
(15, 'mikha nababan', '$2y$10$VbdzY/MO3kgfW4zPFrb8nO4lFEgsZB5o/sqpTstOSkVOdPa0//7Hu', 'siswa', '121211'),
(16, 'risky', '$2y$10$BSCE9oLUpPIna5Zr5.QLROsUsNUjN7nnAN2j4/MSMrNyrU517UHSa', 'siswa', '121213'),
(17, 'grace pasaribu', '$2y$10$oJI193bcMmnzw2BLP1KoduQVe.09KBEqlf5MvCd8l78.38bT5l.kC', 'siswa', '121214'),
(18, 'ariston', '$2y$10$rVGkENdAhSL.pFKJ6vfYs.TUFbhXwbCBWSPUQbi0sw7t/bTjm8z9q', 'siswa', '121215'),
(19, 'amelia', '$2y$10$yKm9nj6VGUPBNnLjwgdOz.mjz4l4meQoSVPZvVj1kLJS8fclURLfK', 'siswa', '121218'),
(20, 'kristiani', '$2y$10$elW3tzw1fB5gDnRYp8XIM.zyplAtS74mabaHaN0f0P19n.uvwneGq', 'siswa', '121219'),
(21, 'merry', '$2y$10$jZCCaP32dJoW6IXhTwauj.Ye6i5hBHqU2BegHsi2Xv02yK8uhIBbS', 'siswa', '121220'),
(22, 'randi silaen', '$2y$10$6B5tjggLJyyDdPU6tYV39.Cn1yIJZoYLS6OZc/uEw70Mrt0LsysFi', 'siswa', '121221'),
(23, 'jelita', '$2y$10$wUNwkdIRCaNFOzX05NSO1ehg49MQZ/Qm7h1hUUuKyJBF0Xji/tFoi', 'siswa', '121222'),
(24, 'andre', '$2y$10$sIm8yBeOBB/YYD32QtLpEesB/rmLpWceV3dysIwFWIqS1s1m3mIVa', 'siswa', '121223'),
(25, 'jeky', '$2y$10$6xWgtS3tUNKniMDsaCZG4erbJE2X5gjtIOp6gAwpCZ6jQ/XimZbVm', 'siswa', '121224'),
(27, 'masriani', '$2y$10$nfKZ5Fjv/inqCDiOgqzGg.fCmaatALF26tbxmUI2kumN1X1HmaZ8y', 'siswa', '121226'),
(29, 'jeffry', '$2y$10$V2leGwM6Z5nLNNIfG6GwvuI1p7pGg6XPlJsZVR1lYv5LuygL8/urC', 'siswa', '121228'),
(30, 'aldi', '$2y$10$QBDydYka9lu/i2oN4X2fluPm4EGSHxSB0QpgdE30JlbUwIcDc5TBK', 'siswa', '121230'),
(31, 'indris sanjaya', '$2y$10$LtOu9wTelNsEu3eJHhpoAeRumztOCQ2Ms6IG1Yx/ZoMcG0SQeSbGK', 'siswa', '121231'),
(33, 'zefanya', '$2y$10$DxG4cDDLX/jZ69m7OSi.Xuzvy/lfDE6s3RXZzK6yOn3CTYkg.mrrW', 'siswa', '121232'),
(35, 'nico tambunan', '$2y$10$dwsHiP5pgBUieXcdwqZTc.s9BdKGNdvZN6pBvwjVbPkn11XgI7mOG', 'siswa', '121234'),
(38, 'Raya Panjaitan', '$2y$10$Fl8UzlSfUaJ1AXwWXgQwNee8RNfdJmQXcIyGiuV26a2akDbA4J7Ee', 'siswa', '121236'),
(41, 'ferdinand', '$2y$10$GdlnmBu8MZ6pK1LET8fhZuA1Lky5mNSoylgTQgrsMMFLZ4DbjZwta', 'siswa', '121239'),
(45, 'valery', '$2y$10$B0gA6zAwypTVA8dizTVv9.5xGcFH7I1qQ4mRbcazvCjddZMw/YhQ2', 'siswa', '121241'),
(46, 'raisa', '$2y$10$zlPYttgkaZK7mCFgOpYqf.6micbp.VRqPzDgpzuWNBUx0gbdXF79W', 'siswa', '121237'),
(47, 'nadira ', '$2y$10$CKdub.Y.nf1wWglNXk8.C.EtaTCGBRIhcHzNoqu3uC7.DOlYazOcu', 'siswa', '222111');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_siswa_user` (`id_user`);

--
-- Indexes for table `spp`
--
ALTER TABLE `spp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `spp`
--
ALTER TABLE `spp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`);

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `fk_siswa_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
