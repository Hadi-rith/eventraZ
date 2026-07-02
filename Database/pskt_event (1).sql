-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 02, 2026 at 07:31 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pskt_event`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id`, `username`, `name`, `email`, `password`, `is_active`, `created_at`) VALUES
(1, 'admin01', 'Admin Satu', 'admin01@eventraz.com', 'admin123', 1, '2026-06-24 06:23:14');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` int UNSIGNED NOT NULL,
  `session_id` int UNSIGNED NOT NULL,
  `user_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_type` enum('school','public') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `attendance_time` datetime NOT NULL,
  `method` enum('qr','link') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `session_id`, `user_key`, `user_type`, `display_name`, `attendance_time`, `method`, `ip_address`, `created_at`) VALUES
(1, 2, 'TBA1001', 'school', 'Sekolah Kebangsaan TBA', '2026-07-02 04:16:38', 'qr', '::1', '2026-07-02 04:16:38'),
(2, 2, '2', 'public', 'Hadi', '2026-07-02 04:45:35', 'qr', '::1', '2026-07-02 04:45:35');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `id` int UNSIGNED NOT NULL,
  `event_id` int NOT NULL,
  `session_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `session_date` date NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('active','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_sessions`
--

INSERT INTO `attendance_sessions` (`id`, `event_id`, `session_name`, `token`, `session_date`, `start_time`, `end_time`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 1, 'test', '3b1397a161e1104078c2826df64bcd2b783c90311c67a5d6', '2026-07-02', '2026-07-02 06:00:00', '2026-07-02 19:00:00', 'active', NULL, '2026-07-02 02:13:36', '2026-07-02 04:16:16'),
(3, 5, 'yoyoyooyo', '2fe6b0d15d1e028b337b91cc778f84696711610a3c6575ce', '2026-09-12', '2026-09-12 06:00:00', '2026-09-12 12:00:00', 'active', NULL, '2026-07-02 02:49:10', '2026-07-02 02:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `daftar_awam`
--

CREATE TABLE `daftar_awam` (
  `id` int UNSIGNED NOT NULL,
  `program_id` int NOT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ic` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tel` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_general_ci NOT NULL,
  `bil_ahli` int UNSIGNED NOT NULL DEFAULT '0',
  `status_hadir` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Hadir',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_awam`
--

INSERT INTO `daftar_awam` (`id`, `program_id`, `program_name`, `nama`, `ic`, `tel`, `email`, `bil_ahli`, `status_hadir`, `created_at`) VALUES
(1, 5, 'Festival STEM Terengganu 2026', 'Muhammad Hadi Harith Bin Mohd Rushaidi', '050605110145', '0197142154', 'hb710956@gmail.com', 0, 'Belum Hadir', '2026-06-15 08:02:15'),
(2, 15, 'LARIAN', 'Adi', '678434875425', '32487362', 'hb710956@gmail.com', 1, 'Belum Hadir', '2026-06-23 01:21:54'),
(3, 15, 'LARIAN', 'HADIIIIIIIIIIIIII', '74882838323', '92183456', 'hb710956@gmail.com', 0, 'Belum Hadir', '2026-06-23 01:27:09'),
(4, 4, 'MINGGU SAINS NEGARA', 'Hadi', '012345678901', '019999999', 'hb710956@gmail.com', 1, 'Belum Hadir', '2026-06-29 03:05:54');

-- --------------------------------------------------------

--
-- Table structure for table `daftar_family`
--

CREATE TABLE `daftar_family` (
  `id` int UNSIGNED NOT NULL,
  `registration_id` int UNSIGNED NOT NULL,
  `nama_ahli` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ic_ahli` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_family`
--

INSERT INTO `daftar_family` (`id`, `registration_id`, `nama_ahli`, `ic_ahli`) VALUES
(1, 2, 'Hadi', '134356474'),
(2, 4, 'Hazim', '018888888888');

-- --------------------------------------------------------

--
-- Table structure for table `daftar_guru`
--

CREATE TABLE `daftar_guru` (
  `id` int UNSIGNED NOT NULL,
  `registration_id` int UNSIGNED NOT NULL,
  `nama_guru` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ic_guru` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_guru`
--

INSERT INTO `daftar_guru` (`id`, `registration_id`, `nama_guru`, `ic_guru`) VALUES
(1, 1, 'AHMAD', '01233456789'),
(2, 2, 'Hazif', '4269676767'),
(3, 3, 'Mezi', '67484839878'),
(4, 4, 'MOHAMAD ', '123456789'),
(5, 6, 'HAAAAAAAAAAAAAAALANDDD', '6708676086769');

-- --------------------------------------------------------

--
-- Table structure for table `daftar_murid`
--

CREATE TABLE `daftar_murid` (
  `id` int UNSIGNED NOT NULL,
  `registration_id` int UNSIGNED NOT NULL,
  `nama_murid` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ic_murid` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_murid`
--

INSERT INTO `daftar_murid` (`id`, `registration_id`, `nama_murid`, `ic_murid`) VALUES
(1, 1, 'HAZIF', '30499203467'),
(2, 2, 'Haidil', '42696767'),
(3, 3, 'eff', '324243556'),
(4, 4, 'AHMAD ', '123456789'),
(5, 6, 'sddff', '948328247293');

-- --------------------------------------------------------

--
-- Table structure for table `daftar_sekolah`
--

CREATE TABLE `daftar_sekolah` (
  `id` int UNSIGNED NOT NULL,
  `program_id` int NOT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_sekolah` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `kod_sekolah` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email_sekolah` varchar(190) COLLATE utf8mb4_general_ci NOT NULL,
  `tel_sekolah` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `bil_murid` int UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Baru',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_sekolah`
--

INSERT INTO `daftar_sekolah` (`id`, `program_id`, `program_name`, `nama_sekolah`, `kod_sekolah`, `email_sekolah`, `tel_sekolah`, `bil_murid`, `status`, `created_at`) VALUES
(1, 2, 'Karnival Sains & Teknologi', 'SEKOLAH MENENGAH KEBANGSAAN 1001', 'TBA1001', 'SMK1001@gmail.com', '032656382', 1, 'Baru', '2026-06-15 02:32:06'),
(2, 5, 'Festival STEM Terengganu 2026', 'Sekolah Kebangsaan TBA', 'TBA1001', 'HB710956@gmail.com', '676967696769', 1, 'Baru', '2026-06-15 04:18:36'),
(3, 15, 'LARIAN', 'Sekolah Kebangsaan TBA', 'TBA1001', 'SEKOLAHTBA@Gmail.com', '32983749823', 1, 'Baru', '2026-06-23 02:50:56'),
(4, 15, 'LARIAN', 'SEKOLAH KEBANGSAAN TBA', 'TBA1001', 'ahmad123@gmail.com', '0123456789', 1, 'Baru', '2026-06-23 07:22:31'),
(5, 15, 'LARIAN', 'SMKBB', 'TBA1234', 'hb710956@gmail.com', '0676767676', 0, 'Baru', '2026-06-21 03:17:25'),
(6, 1, 'Program Kepimpinan Pelajar 2026', 'Sekolah Kebangsaan TBA', 'TBA1001', 'test@sekolah.com', 'jfdsjkdskajfdsf', 1, 'Baru', '2026-07-02 02:12:24');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-06-15-101500', 'App\\Database\\Migrations\\AddProgramDates', 'default', 'App', 1781490043, 1),
(2, '2026-06-15-111500', 'App\\Database\\Migrations\\AddSignupAccounts', 'default', 'App', 1781494138, 2),
(3, '2026-06-15-102000', 'App\\Database\\Migrations\\AddProgramParentId', 'default', 'App', 1781757902, 3);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int NOT NULL,
  `admin_id` int UNSIGNED DEFAULT NULL,
  `program_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `event_time` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `organizer` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('AKTIF','TIDAK AKTIF') COLLATE utf8mb4_general_ci DEFAULT 'AKTIF',
  `is_featured` tinyint(1) DEFAULT '0',
  `registration_limit` int UNSIGNED NOT NULL DEFAULT '0',
  `pic_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pic_tel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `poster_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `admin_id`, `program_code`, `parent_id`, `program_name`, `description`, `start_date`, `end_date`, `event_time`, `location`, `organizer`, `status`, `is_featured`, `registration_limit`, `pic_nama`, `pic_tel`, `poster_image`, `created_at`) VALUES
(1, NULL, 'PROG001', NULL, 'Program Kepimpinan Pelajar 2026', 'test', '2026-07-02', '2026-07-03', '08:00', 'test', 'test', 'AKTIF', 0, 0, 'test', '68676767676', NULL, '2026-06-14 08:31:33'),
(2, NULL, 'PROG002', NULL, 'Karnival Sains & Teknologi', NULL, '2026-06-15', '2026-06-18', NULL, NULL, NULL, 'TIDAK AKTIF', 0, 0, NULL, NULL, NULL, '2026-06-14 08:31:33'),
(4, NULL, 'MSN2026', NULL, 'MINGGU SAINS NEGARA', '', '2026-06-23', '2026-06-29', '', 'PSKT', '', 'TIDAK AKTIF', 0, 0, 'PSKT', '0123456', 'uploads/posters/1782197755_97d500917dd39df1ffe3.jpg', '2026-06-15 02:22:15'),
(5, NULL, 'FESTEM-T2026', NULL, 'Festival STEM Terengganu 2026', NULL, '2026-09-12', '2026-09-17', NULL, NULL, NULL, 'AKTIF', 0, 0, NULL, NULL, NULL, '2026-06-15 03:30:50'),
(14, NULL, 'HSK', NULL, 'HARI SUKAN', NULL, '2026-06-23', '2026-06-25', NULL, NULL, NULL, 'TIDAK AKTIF', 0, 0, 'PSKT', '01967676767', NULL, '2026-06-18 04:46:18'),
(15, NULL, 'LARI', 14, 'LARIAN', '', '2026-06-23', '2026-06-23', '', 'PSKT', '', 'TIDAK AKTIF', 0, 0, 'PSKT', '019 617 4004', 'uploads/posters/1782196570_db856c403022a9164dec.jpg', '2026-06-18 04:47:01');

-- --------------------------------------------------------

--
-- Table structure for table `public_accounts`
--

CREATE TABLE `public_accounts` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(190) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_accounts`
--

INSERT INTO `public_accounts` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(2, 'Hadi', 'hb710956@gmail.com', 'Bankdig5', '2026-06-21 02:29:42');

-- --------------------------------------------------------

--
-- Table structure for table `school_accounts`
--

CREATE TABLE `school_accounts` (
  `id` int NOT NULL,
  `school_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_accounts`
--

INSERT INTO `school_accounts` (`id`, `school_code`, `school_name`, `email`, `password`, `created_at`) VALUES
(1, 'adminpskt', 'Admin PSKT', NULL, 'pskt2026', '2026-06-14 08:31:33'),
(2, 'TBA1001', 'Sekolah Kebangsaan TBA', NULL, 'pass123', '2026-06-14 08:31:33'),
(3, 'TBA2002', 'Sekolah Kebangsaan TBA 2', NULL, 'sekolahku2026', '2026-06-14 08:31:33'),
(4, 'TEA3003', 'Sekolah Kebangsaan TEA', NULL, 'psktSains789', '2026-06-14 08:31:33'),
(5, 'TBA1234', 'SMK SULTAN SULAIMAN', 'smkss@gmail.com', '123456', '2026-06-23 07:01:06'),
(6, 'TEA3119', 'SEKOLAH MENENGAH KEBANGSAAN BUKIT BESAR', 'smkbb@gmail.com', 'smkbb123', '2026-06-29 03:21:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admin_username` (`username`),
  ADD UNIQUE KEY `uq_admin_email` (`email`);

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_att_record_checkin` (`session_id`,`user_key`,`user_type`),
  ADD KEY `idx_att_record_session` (`session_id`);

--
-- Indexes for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_att_session_token` (`token`),
  ADD KEY `idx_att_session_event` (`event_id`),
  ADD KEY `idx_att_session_created_by` (`created_by`);

--
-- Indexes for table `daftar_awam`
--
ALTER TABLE `daftar_awam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_da_program_id` (`program_id`),
  ADD KEY `idx_da_email` (`email`);

--
-- Indexes for table `daftar_family`
--
ALTER TABLE `daftar_family`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_df_registration_id` (`registration_id`);

--
-- Indexes for table `daftar_guru`
--
ALTER TABLE `daftar_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dg_registration_id` (`registration_id`);

--
-- Indexes for table `daftar_murid`
--
ALTER TABLE `daftar_murid`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dm_registration_id` (`registration_id`);

--
-- Indexes for table `daftar_sekolah`
--
ALTER TABLE `daftar_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ds_program_id` (`program_id`),
  ADD KEY `idx_ds_kod_sekolah` (`kod_sekolah`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_code` (`program_code`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `fk_programs_admin` (`admin_id`);

--
-- Indexes for table `public_accounts`
--
ALTER TABLE `public_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `school_accounts`
--
ALTER TABLE `school_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_code` (`school_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `daftar_awam`
--
ALTER TABLE `daftar_awam`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `daftar_family`
--
ALTER TABLE `daftar_family`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `daftar_guru`
--
ALTER TABLE `daftar_guru`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `daftar_murid`
--
ALTER TABLE `daftar_murid`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `daftar_sekolah`
--
ALTER TABLE `daftar_sekolah`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `public_accounts`
--
ALTER TABLE `public_accounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `school_accounts`
--
ALTER TABLE `school_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `fk_att_record_session` FOREIGN KEY (`session_id`) REFERENCES `attendance_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD CONSTRAINT `fk_att_session_admin` FOREIGN KEY (`created_by`) REFERENCES `admin_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_att_session_program` FOREIGN KEY (`event_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daftar_family`
--
ALTER TABLE `daftar_family`
  ADD CONSTRAINT `fk_df_registration` FOREIGN KEY (`registration_id`) REFERENCES `daftar_awam` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daftar_guru`
--
ALTER TABLE `daftar_guru`
  ADD CONSTRAINT `fk_dg_registration` FOREIGN KEY (`registration_id`) REFERENCES `daftar_sekolah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daftar_murid`
--
ALTER TABLE `daftar_murid`
  ADD CONSTRAINT `fk_dm_registration` FOREIGN KEY (`registration_id`) REFERENCES `daftar_sekolah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `fk_programs_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `programs_parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
