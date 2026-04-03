-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 05:09 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smk_cahaya`
--

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id_jurusan` int(11) NOT NULL,
  `singkatan` varchar(10) NOT NULL,
  `nama_keahlian` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id_jurusan`, `singkatan`, `nama_keahlian`) VALUES
(1, 'MPLB', 'Manajemen Perkantoran dan Layanan Bisnis'),
(2, 'BD', 'Bisnis Digital'),
(3, 'TLM', 'Teknologi Laboratorium Medik'),
(4, 'TKJT', 'Teknik Komputer Jaringan dan Telekomunikasi'),
(5, 'TKR', 'Teknik Kendaraan Ringan ');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftar`
--

CREATE TABLE `pendaftar` (
  `id_pendaftar` int(11) NOT NULL,
  `no_pendaftaran` varchar(15) NOT NULL,
  `tgl_daftar` date NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `nama_panggilan` varchar(50) DEFAULT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `agama` varchar(30) NOT NULL,
  `status_yatim` enum('Tidak','Yatim','Piatu','Yatim Piatu') NOT NULL DEFAULT 'Tidak',
  `asal_sekolah` varchar(150) NOT NULL,
  `alamat_sekolah` text NOT NULL,
  `no_hp_siswa` varchar(15) NOT NULL,
  `alamat_siswa` text NOT NULL,
  `nama_ayah` varchar(150) NOT NULL,
  `pekerjaan_ayah` varchar(100) NOT NULL,
  `nama_ibu` varchar(150) NOT NULL,
  `pekerjaan_ibu` varchar(100) NOT NULL,
  `nama_wali` varchar(150) DEFAULT NULL,
  `hubungan_wali` varchar(50) DEFAULT NULL,
  `id_jurusan_pilihan` int(11) DEFAULT NULL,
  `status_verifikasi` varchar(50) NOT NULL DEFAULT 'Menunggu Verifikasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pendaftar`
--

INSERT INTO `pendaftar` (`id_pendaftar`, `no_pendaftaran`, `tgl_daftar`, `nama_lengkap`, `nama_panggilan`, `nisn`, `jenis_kelamin`, `tempat_lahir`, `tgl_lahir`, `agama`, `status_yatim`, `asal_sekolah`, `alamat_sekolah`, `no_hp_siswa`, `alamat_siswa`, `nama_ayah`, `pekerjaan_ayah`, `nama_ibu`, `pekerjaan_ibu`, `nama_wali`, `hubungan_wali`, `id_jurusan_pilihan`, `status_verifikasi`) VALUES
(1, 'PPDB-2025-0001', '2025-09-28', 'Sint in excepturi qu', 'Reprehenderit itaque', 'Elit nulla', 'L', 'Perspiciatis anim e', '1995-10-16', 'Katolik', 'Yatim', 'Voluptate labore fac', 'Voluptatem Impedit', '+1 (911) 672-74', 'Ipsam id quia ea a o', 'Sequi velit deserunt', 'Reprehenderit est ul', 'Delectus dicta obca', 'Non elit quaerat vo', 'Optio et architecto', 'Nisi perspiciatis e', 1, 'Diverifikasi'),
(7, 'PPDB-2025-0002', '2025-10-06', 'Anim Nam eum vero qu', 'Aute ipsam qui ab qu', 'Sed aliqui', 'P', 'Dolorum est voluptat', '2003-05-27', 'Islam', 'Yatim Piatu', 'Aut voluptatem rerum', 'Quia labore exercita', '+1 (251) 666-17', 'Laborum ut molestias', 'Sit odit nulla et r', 'Ad ea facilis molest', 'Deserunt quis beatae', 'Non cillum exercitat', 'Minima natus ipsum q', 'Amet esse voluptat', 4, 'Diverifikasi'),
(8, 'PPDB-2025-0003', '2025-10-06', 'Minim possimus ea a', 'Dolor lorem esse ess', 'Quos nesci', 'P', 'Saepe cupiditate eni', '2018-10-22', 'Kristen', 'Yatim', 'Rem corporis ipsam n', 'Consequatur tempor m', '+1 (754) 742-74', 'Sit suscipit quis i', 'Eos qui itaque paria', 'Architecto et ut lab', 'Dolor et autem deser', 'Consequatur Eaque e', 'Dolore ad esse dolo', 'Explicabo Exercitat', 1, 'Diverifikasi'),
(9, 'PPDB-2025-0004', '2025-10-06', 'Rem ea nisi dolor eu', 'Commodi commodo quis', 'Est rerum ', 'L', 'Duis atque qui conse', '1981-11-07', 'Hindu', 'Piatu', 'In laborum Aut quis', 'Ab sit consectetur', '+1 (101) 464-67', 'Sed laboriosam volu', 'Adipisci nulla simil', 'Eos est commodi co', 'Soluta sed non volup', 'Ipsa corporis vel c', 'Qui modi aperiam eiu', 'Facere nostrum duis', 1, 'Diverifikasi'),
(10, 'PPDB-2025-0005', '2025-10-24', 'joko', 'Quia consequatur vel', 'Et sunt el', 'L', 'Velit dolor ut atqu', '2017-05-19', 'Islam', 'Piatu', 'Odio quod aut quo bl', 'Optio reprehenderit', '+1 (352) 277-33', 'Corrupti est duis e', 'Veritatis excepturi', 'Quam consequatur ei', 'Id itaque aliquid a', 'Possimus elit maxi', 'Minima molestiae est', 'Sint exercitationem', 3, 'Menunggu Verifikasi'),
(11, 'PPDB-2025-0006', '2025-12-12', 'gitara', 'Labore saepe consequ', 'Ut distinc', 'P', 'Minima reprehenderit', '2025-08-17', 'Kristen', 'Yatim', 'Vero consequuntur au', 'Dignissimos eos ali', '+1 (833) 956-77', 'Et iusto accusamus q', 'Sapiente ea totam co', 'Harum neque quia nul', 'Nisi cumque voluptat', 'Qui voluptate qui et', 'Aliquam nostrud perf', 'Qui officiis assumen', 1, 'Diverifikasi');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `role_name`) VALUES
(1, 'Admin'),
(3, 'Kepsek'),
(2, 'Siswa');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `id_pendaftar` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `id_pendaftar`) VALUES
(1, 'admin', '$2y$10$Tc2j8DAfl/.6feoapIhs/eeDIlQiILNL5IE9i5HRQndKSksPSBxoG', 'Admin', NULL),
(2, 'aan', '$2y$10$RwzGM6p9ocpURqN/gkhPgery3tY8DaEn/mgV2Gm4DhWaZTQyWM6r6', 'pendaftar', 1),
(3, 'aan2', '$2y$10$V./AQZ66MSNw02ZPC5A9.uhuAWVIZtA1Dj0WK0u.OIs8lLDAwfr4K', 'daptar2', NULL),
(5, 'kepsek', '$2y$10$/fUX.A2fCCsIlcgU0pLrNOP6R2gTlMW0xNTeFtbtWFRyZnW0THu9m', '', NULL),
(7, 'siswa2', '$2y$10$ryYaGNPxdG47NxaqtvqMseWa3Ct77pjV6lSVauXO6pZf6oWj9mXO6', 'Quis quis dolore nih', 7),
(8, 'siswa3', '$2y$10$HmkrYo00ZJVBzw8n9v2EEOjgwfFt60wzb.zk4JUNApA1N6l9Ou6r2', 'Et sit est ut error', 8),
(9, 'siswa4', '$2y$10$40.ZkvL5OY8SZojRNYnMiOX5YzD769dbQdBoIlIK2x2umnrscA7VW', 'Commodi ducimus dol', 9),
(10, 'siswamagang', '$2y$10$l/r66Mh1K0hlhVmt7DOdF.lwqZVi3lKh2ELwlbsDgfsqs9XBwcy0u', 'pwpwpwp', NULL),
(11, 'joko', '$2y$10$pcDTTbiituevaytJJQpQDODn7ryvoy/B9OTBNfalLgZNPq2az.q3.', 'joko', 10),
(12, 'gitara', '$2y$10$.PQC/Mo5BJzNv8/A4M3qleuJGecKtKn38r/d8R0eK3A/pcWL.0Aj6', 'gitara', 11),
(13, 'joni', '$2y$10$JCWVoVhE7vzJiUvIyiQ39.jHuQQ4NxZoc4nqVMySjq8jFi0fM2TjS', 'joni', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id_user_role` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id_user_role`, `user_id`, `role_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 2),
(4, 5, 3),
(6, 7, 2),
(7, 8, 2),
(8, 9, 2),
(9, 10, 2),
(10, 11, 2),
(11, 12, 2),
(12, 13, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id_jurusan`),
  ADD UNIQUE KEY `singkatan` (`singkatan`);

--
-- Indexes for table `pendaftar`
--
ALTER TABLE `pendaftar`
  ADD PRIMARY KEY (`id_pendaftar`),
  ADD UNIQUE KEY `no_pendaftaran` (`no_pendaftaran`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `nisn_2` (`nisn`),
  ADD KEY `nama_lengkap` (`nama_lengkap`),
  ADD KEY `fk_jurusan_pilihan` (`id_jurusan_pilihan`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id_pendaftar` (`id_pendaftar`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id_user_role`),
  ADD UNIQUE KEY `idx_user_role_unique` (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id_jurusan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pendaftar`
--
ALTER TABLE `pendaftar`
  MODIFY `id_pendaftar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id_user_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pendaftar`
--
ALTER TABLE `pendaftar`
  ADD CONSTRAINT `fk_jurusan_pilihan` FOREIGN KEY (`id_jurusan_pilihan`) REFERENCES `jurusan` (`id_jurusan`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_pendaftar`) REFERENCES `pendaftar` (`id_pendaftar`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
