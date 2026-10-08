-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 05:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smart_dustbin`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('critical','warning','system','info') NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`id`, `type`, `message`, `status`, `created_at`) VALUES
(1, 'warning', 'Wet Waste Bin is 80% full.', 'unread', '2026-09-22 05:44:15'),
(2, 'critical', 'Dry Waste Bin is 95% full. Please empty the compartment.', 'unread', '2026-09-22 05:44:15');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'system_version', '1.0'),
(2, 'warning_threshold', '85'),
(3, 'critical_threshold', '95'),
(4, 'alert_hardware_server', 'https://script.google.com/macros/s/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/exec'),
(5, 'alert_receiver_name', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `system_status`
--

CREATE TABLE `system_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `arduino_status` enum('online','offline') NOT NULL DEFAULT 'offline',
  `communication_status` enum('connected','disconnected') NOT NULL DEFAULT 'disconnected',
  `wet_capacity` float UNSIGNED DEFAULT NULL,
  `dry_capacity` float UNSIGNED DEFAULT NULL,
  `current_classification` enum('WET','DRY','NONE') DEFAULT 'NONE',
  `green_led` enum('on','off') NOT NULL DEFAULT 'off',
  `yellow_led` enum('on','off') NOT NULL DEFAULT 'off',
  `last_updated` datetime DEFAULT NULL,
  `status_count` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_status`
--

INSERT INTO `system_status` (`id`, `arduino_status`, `communication_status`, `wet_capacity`, `dry_capacity`, `current_classification`, `green_led`, `yellow_led`, `last_updated`, `status_count`) VALUES
(1, 'online', 'connected', 80, 95, 'DRY', 'on', 'off', '2026-09-22 13:44:15', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$5XfPNXAz3TJCiVfEHro9PuSJjBe9.lTKUymAk.wtmE7OBGH6erwdS', 'admin', '2026-09-22 05:43:03');

-- --------------------------------------------------------

--
-- Table structure for table `waste_records`
--

CREATE TABLE `waste_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `classification` enum('WET','DRY') NOT NULL,
  `detected_item` varchar(100) DEFAULT NULL,
  `wet_capacity` float UNSIGNED DEFAULT NULL,
  `dry_capacity` float UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `waste_records`
--

INSERT INTO `waste_records` (`id`, `classification`, `detected_item`, `wet_capacity`, `dry_capacity`, `date`, `time`, `created_at`) VALUES
(1, 'WET', 'Banana peel', 45, 30, '2026-09-22', '08:12:00', '2026-09-22 05:42:33'),
(2, 'DRY', 'Water bottle', 45, 35, '2026-09-22', '08:41:00', '2026-09-22 05:42:33'),
(3, 'WET', 'Rice leftovers', 48, 35, '2026-09-22', '09:05:00', '2026-09-22 05:42:33'),
(4, 'DRY', 'Paper cup', 48, 40, '2026-09-22', '09:27:00', '2026-09-22 05:42:33'),
(5, 'WET', 'Vegetable scraps', 52, 40, '2026-09-22', '10:02:00', '2026-09-22 05:42:33'),
(6, 'DRY', 'Plastic wrapper', 52, 45, '2026-09-22', '10:29:00', '2026-09-22 05:42:33'),
(7, 'WET', 'Fruit peel', 55, 45, '2026-09-22', '10:47:00', '2026-09-22 05:42:33'),
(8, 'DRY', 'Can', 55, 48, '2026-09-22', '11:13:00', '2026-09-22 05:42:33'),
(9, 'WET', 'Coffee grounds', 58, 48, '2026-09-22', '12:20:00', '2026-09-22 05:42:33'),
(10, 'DRY', 'Cardboard', 58, 52, '2026-09-22', '13:04:00', '2026-09-22 05:42:33'),
(11, 'WET', 'Fish scraps', 61, 52, '2026-09-22', '14:18:00', '2026-09-22 05:42:33'),
(12, 'DRY', 'Plastic bag', 61, 55, '2026-09-22', '15:36:00', '2026-09-22 05:42:33'),
(13, 'DRY', 'plastic cup', 80, 95, '2026-09-22', '13:44:15', '2026-09-22 05:44:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `system_status`
--
ALTER TABLE `system_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `waste_records`
--
ALTER TABLE `waste_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_classification` (`classification`),
  ADD KEY `idx_date` (`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `system_status`
--
ALTER TABLE `system_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `waste_records`
--
ALTER TABLE `waste_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
