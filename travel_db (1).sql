-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 20, 2026 at 09:13 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u255290550_zubitours`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','editor','viewer') NOT NULL DEFAULT 'admin',
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expiry` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `username`, `password`, `role`, `last_login`, `is_active`, `failed_attempts`, `locked_until`, `created_at`, `updated_at`, `reset_token`, `reset_token_expiry`) VALUES
(2, 'irfan manzoor', 'drop.mail.iry@gmail.com', 'irony', '$2y$10$AzHWNQOy/Yn/tUtzWknt6.aVqQwB39O6dihaYQiuJrEVNkKsxmiJu', 'admin', NULL, 1, 0, NULL, '2025-12-18 13:05:36', '2025-12-18 13:07:47', NULL, NULL),
(3, 'admin zubitours', 'admin@gmail.com', 'admin', '$2y$10$aff3FEGqOvrSXhqdWx1vNOLSXRrR0yF7o/qETvl38do5D6DgQMFoa', 'admin', NULL, 1, 0, NULL, '2025-12-19 05:37:35', '2025-12-19 10:45:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `permission` varchar(100) NOT NULL,
  `allowed` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `role`, `permission`, `allowed`) VALUES
(1, 'super_admin', 'manage_admins', 1),
(2, 'super_admin', 'manage_packages', 1),
(3, 'super_admin', 'manage_bookings', 1),
(4, 'super_admin', 'manage_cars', 1),
(5, 'super_admin', 'manage_gallery', 1),
(6, 'super_admin', 'manage_destinations', 1),
(7, 'super_admin', 'manage_settings', 1),
(8, 'super_admin', 'view_reports', 1),
(9, 'admin', 'manage_packages', 1),
(10, 'admin', 'manage_bookings', 1),
(11, 'admin', 'manage_cars', 1),
(12, 'admin', 'manage_gallery', 1),
(13, 'admin', 'view_reports', 1),
(14, 'editor', 'manage_packages', 1),
(15, 'editor', 'manage_gallery', 1),
(16, 'viewer', 'view_reports', 1);

-- --------------------------------------------------------

--
-- Table structure for table `callback_leads`
--

CREATE TABLE `callback_leads` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `source_page` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('new','contacted','closed') DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `is_bot` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `callback_leads`
--

INSERT INTO `callback_leads` (`id`, `name`, `phone`, `source_page`, `ip_address`, `user_agent`, `submitted_at`, `status`, `admin_notes`, `is_bot`) VALUES
(2, 'Reviral Factor', '6006801960', 'https://zubitours.com/', '2409:4054:217:9d8d:4669:1a4b:ba37:edb5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-03 12:38:29', 'new', NULL, 0),
(3, 'Amar', '9622123254', 'https://zubitours.com/', '2409:40d5:1083:4554:f920:51c:97ce:bb9b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-03 12:41:10', 'new', NULL, 1),
(4, 'Anand Kumar', '9622181911', 'https://zubitours.com/', '2409:40d5:100e:246a:546c:5cff:fefe:e169', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2026-01-03 13:26:39', 'new', NULL, 0),
(5, 'ZUBARE AHMAD DAR', '7006948538', 'https://zubitours.com/', '110.224.248.171', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-03 17:53:50', 'new', NULL, 0),
(6, 'Ummul fazul', '8075840618', 'https://zubitours.com/?gad_source=5&gad_campaignid=21650142842&gclid=Cj0KCQiAgvPKBhCxARIsAOlK_Ep9CFZKA0-lggbykxHP2rq80dSBy7xZQdZ4TA1lpZYHfQR9VEIyBa8aAnWAEALw_wcB', '2402:8100:2a50:6572:0:34:4b8a:8401', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-06 14:06:11', 'new', NULL, 0),
(7, 'Tilak Raj Singh', '9811894612', 'https://zubitours.com/?gad_source=1&gad_campaignid=23410318255&gbraid=0AAAAA9QVA6-0F2S46E1w58YKA5CcXDsZe&gclid=Cj0KCQiApfjKBhC0ARIsAMiR_IuyVplo2jSW56Gy4iEKUYwhTASIAYfA5vXCBPs3e7ZagjAqbo6fQUYaAk2nEALw_wcB', '103.68.30.17', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-07 17:51:18', 'new', NULL, 0),
(8, 'Pqawan', '8409767556', 'https://zubitours.com/?gad_source=1&gad_campaignid=23407198535&gclid=CjwKCAiA64LLBhBhEiwA-Pxgu59esT4cGBTI6DUtB0XIhpBNaOXJs-DydnrpWnEbD_zYSq7O9qM7xRoC_i8QAvD_BwE', '2401:4900:5d38:fe4e:8151:90d:639e:aadd', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-09 18:47:46', 'new', NULL, 0),
(9, 'Devender', '8059629071', 'https://zubitours.com/?gad_source=1&gad_campaignid=23407198535&gclid=CjwKCAiA64LLBhBhEiwA-PxguyPEIlG_cH6WuxrDH9tKXhNM9TKXN4lkCMMVX2qQkogjJaRSasAdtBoCo2oQAvD_BwE', '2405:201:5509:c01a:e91f:c458:649b:f7c4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-10 05:29:34', 'new', NULL, 0),
(10, 'Kanta Madaan', '7289972316', 'https://zubitours.com/?gad_source=1&gad_campaignid=23410318255&gbraid=0AAAAA9QVA69ACrlTg-kEtTJpO7bMpv8qX&gclid=CjwKCAiAjojLBhAlEiwAcjhrDqmkrkCsaPkMS1-EdbS5hb6tVEOv6ZsqBEXeg5lmN_L9C8BvTF7xcBoC1PYQAvD_BwE', '2401:4900:30e7:23a:0:6a:5cfc:7601', 'Mozilla/5.0 (Linux; Android 11; RMX3231 Build/RP1A.201005.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.7499.146 Mobile Safari/537.36', '2026-01-11 04:01:39', 'new', NULL, 0),
(11, 'tawseef', '9103245972', 'https://zubitours.com/?gad_source=1&gad_campaignid=23407198535&gbraid=0AAAAA9QVA695TYhRugYkcZK5F6url2pwZ&gclid=Cj0KCQiA1JLLBhCDARIsAAVfy7juPDsY5_3jC8XNSHjSIu45erDlxLbtZp8Pit0fOgyX1nYIHhyEBtoaAsTzEALw_wcB', '2409:40d5:1009:b3:b52f:1647:ff29:a138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_3_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.3.1 Mobile/15E148 Safari/604.1', '2026-01-12 15:32:59', 'new', NULL, 0),
(12, 'Sahabul Sk', '9382843864', 'https://zubitours.com/?gad_source=1&gad_campaignid=23407198535&gclid=Cj0KCQiA1JLLBhCDARIsAAVfy7grVlKrIhp3dO4KWnTCAtr7P8NIHQODpMY81AkXLOhIGQzxavpV5DcaAm56EALw_wcB', '2409:40d5:1037:96c8:8000::', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2026-01-12 15:35:41', 'new', NULL, 0),
(13, 'Mohd Aamir', '8279567723', 'https://zubitours.com/?gad_source=1&gad_campaignid=23407198535&gclid=CjwKCAiA95fLBhBPEiwATXUsxNA1IOTQ6eBHkd9D1OiB_vn1rFcmsX4v3x6flG3GFuqGZVgggJ9LbBoCutwQAvD_BwE', '2409:40d5:103a:ab31:cce1:7426:5d3b:5c97', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/397.0.836500703 Mobile/15E148 Safari/604.1', '2026-01-14 03:57:53', 'new', NULL, 1),
(14, 'Jeetu Kumar', '9632281474', 'https://www.zubitours.com/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnZiXcsTUkvHF6NSfMFNRhMdGRukGET8bQkXqPrAywl-XxvGfVf4z4TPsQmjI_aem_GIR_xNCGAtiGNNtT9xSM4A', '2402:3a80:19:4a4b:0:e:a704:7601', 'Mozilla/5.0 (Linux; Android 15; CPH2579 Build/AP3A.240617.008; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.186 Mobile Safari/537.36 Instagram 411.0.0.23.257 Android (35/15; 320dpi; 720x1612; OPPO; CPH2579; OP5759L1; mt6768; en_GB; 860442306; IABMV/1)', '2026-01-16 17:55:19', 'new', NULL, 1),
(15, 'Payal Thakur thakur', '9193736984', 'https://www.zubitours.com/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPMTI0MDI0NTc0Mjg3NDE0AAGn7FwFhaQK-T9ceorH2XmAxijLPYJTBioKLIsRq7LCi6vbkGXWjXZt8tz7wdA_aem_4W6xKVkLuX8MPOzfpuSgpw', '103.167.102.149', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23C55 Instagram 410.1.0.36.70 (iPhone13,2; iOS 26_2; en_GB; en-GB; scale=3.00; 1170x2532; IABMV/1; 849447290)', '2026-01-20 08:13:22', 'new', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `car_bookings`
--

CREATE TABLE `car_bookings` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_driving_license` varchar(50) DEFAULT NULL,
  `pickup_location` text NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_bookings`
--

INSERT INTO `car_bookings` (`id`, `car_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_driving_license`, `pickup_location`, `pickup_date`, `return_date`, `total_days`, `total_amount`, `status`, `notes`, `booking_date`) VALUES
(1, 5, 'RIZWAN LONE', 'drop.mail.iry@gmail.com', '6006801960', '', 'hotel residency', '2025-12-18', '2025-12-19', 1, 2200.00, 'confirmed', '', '2025-12-17 13:19:39'),
(2, 5, 'Zubair Dar', 'drop.mail.iry@gmail.com', '6006801960', '', 'hotel residency', '2025-12-19', '2025-12-20', 1, 2200.00, 'cancelled', '', '2025-12-18 10:46:11'),
(3, 5, 'rayees', 'drop.mail.iry@gmail.com', '6006801960', '', 'hotel residency', '2025-12-20', '2025-12-21', 1, 2200.00, 'cancelled', '', '2025-12-19 10:37:51'),
(4, 4, 'tabish', 'tbshmehraj@gmail.com', '6006696105', '', 'srinagar airport', '2025-12-21', '2025-12-22', 1, 2500.00, 'confirmed', '', '2025-12-19 11:01:26');

-- --------------------------------------------------------

--
-- Table structure for table `car_rentals`
--

CREATE TABLE `car_rentals` (
  `id` int(11) NOT NULL,
  `car_name` varchar(100) NOT NULL,
  `car_type` enum('suv','sedan','luxury','economy') NOT NULL,
  `capacity` int(11) NOT NULL,
  `transmission` enum('manual','automatic') NOT NULL DEFAULT 'manual',
  `fuel_type` enum('petrol','diesel','electric','hybrid') NOT NULL DEFAULT 'petrol',
  `price_per_day` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_rentals`
--

INSERT INTO `car_rentals` (`id`, `car_name`, `car_type`, `capacity`, `transmission`, `fuel_type`, `price_per_day`, `description`, `image_path`, `badge`, `is_available`, `created_at`, `updated_at`) VALUES
(4, 'Toyota Rumian', 'economy', 6, 'manual', 'petrol', 2500.00, 'Reliable and fuel-efficient vehicle', 'cars/6943d8f507057_rumian.webp', 'Popular', 1, '2025-12-17 12:51:49', '2025-12-18 10:35:33'),
(5, 'Maruti Swift Dzire', 'sedan', 4, 'manual', 'petrol', 2200.00, 'Compact sedan perfect for city travel', 'cars/6943d90126dbe_swift.avif', 'Popular', 1, '2025-12-17 12:51:49', '2025-12-18 10:40:27'),
(6, 'Toyota Innova', 'suv', 7, 'manual', 'diesel', 2500.00, 'Comfortable family vehicle', 'cars/6943d9df7831d_innova.jpeg', 'Group', 1, '2025-12-17 12:51:49', '2025-12-18 10:39:27'),
(7, 'Tempo Traveller 14', 'suv', 14, 'manual', 'diesel', 4000.00, '14-seater for medium-sized groups', 'cars/6943da0d57f75_traveller14.jpeg', 'Group', 1, '2025-12-17 12:51:49', '2025-12-18 10:40:13'),
(8, 'Urbania', 'luxury', 14, 'manual', 'diesel', 5500.00, 'Premium luxury transport', 'cars/6943d9fe7ab43_urbania.webp', 'Group', 1, '2025-12-17 12:51:49', '2025-12-18 10:39:58'),
(17, 'Toyota Innova Crysta', 'suv', 7, 'manual', 'diesel', 2800.00, '0', 'cars/6943d75772a64_innova-crysta.avif', 'Popular', 1, '2025-12-18 10:28:39', '2025-12-18 10:28:39'),
(18, 'Tempo Traveller', 'suv', 17, 'manual', 'diesel', 4200.00, '0', 'cars/6943d780db82f_traveller.jpeg', 'Group', 1, '2025-12-18 10:29:20', '2025-12-18 10:29:20'),
(19, 'Volvo Bus', 'luxury', 40, 'manual', 'diesel', 7500.00, '0', 'cars/6943d822f00ce_volvo_bus.jpg', 'Group', 1, '2025-12-18 10:32:02', '2025-12-19 05:30:38');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` enum('general','booking','custom','feedback','complaint','other') NOT NULL DEFAULT 'general',
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_spam` tinyint(1) DEFAULT 0,
  `spam_score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `admin_notes`, `ip_address`, `user_agent`, `created_at`, `updated_at`, `is_spam`, `spam_score`) VALUES
(2, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', 'new', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-17 14:40:11', '2025-12-17 14:40:11', 0, 0),
(3, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', 'new', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-17 14:40:41', '2025-12-17 14:40:41', 0, 0),
(4, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', 'new', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-17 14:40:49', '2025-12-17 14:40:49', 0, 0),
(5, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', 'new', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-17 14:41:48', '2025-12-17 14:41:48', 0, 0),
(6, 'ARSALAN TARIQ', 'at@gmail.com', '6006801960', 'booking', 'i want to bookgulmarg tour package', 'new', NULL, '2405:201:5509:90aa:f164:b598:d58:ecfc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-19 10:40:30', '2025-12-19 10:40:30', 0, 0),
(7, 'ARSALAN TARIQ', 'at@gmail.com', '6006801960', 'booking', 'i want to bookgulmarg tour package', 'new', NULL, '2405:201:5509:90aa:f164:b598:d58:ecfc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-19 11:06:08', '2025-12-19 11:06:08', 0, 0),
(8, 'Arturo Macknight', 'arturo.macknight@gmail.com', '6505422768', 'booking', 'AI Turbo Creator turns ideas into traffic magnets.\r\nMake your creations visible, compelling, and unforgettable.\r\n\r\n\r\nhttps://lnunquedays.site/AITurboCreator?zubitours.com\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nYou are receiving this message \r\nbecause we believe \r\nthe offer we provide \r\ncould be relevant to you.\r\n\r\nIf you would prefer not to receive \r\nadditional emails from us, \r\nyou can \r\nstop receiving emails:\r\n\r\nhttps://lnunquedays.site/unsub?domain=zubitours.com \r\nAddress: Address: 1707   Spanheimerstrasse 39, UPPER AUSTRIA  4076\r\nLooking out for you, Arturo Macknight.', 'new', NULL, '104.207.35.126', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Vivaldi/5.3.2679.68', '2025-12-20 08:27:34', '2025-12-20 08:27:34', 0, 0),
(9, 'Chang Pina', 'chang.pina@gmail.com', '890152930', 'general', 'GET INSTANT AI POWERED COURSE CREATION, MARKETING STRATEGIES, AND COMPELLING CONTENT THAT ADAPTS TO YOUR EXACT NEEDS - ALL AT SUPERHERO SPEED!\r\n\r\n\r\nhttps://lordvpn.site/HeroCommandersAI?zubitours.com\r\n\r\n\r\nYour Course Creation Superhero That Delivers Exactly What You Want, Instantly - Even If You\'re A Complete Newbie!\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nThis message is sent to you \r\nas we believe \r\nthe offer we provide \r\nmay interest you.\r\n\r\nIf you do not wish to receive \r\nfuture messages from us, \r\nsimply \r\nunsubscribe:\r\n\r\nhttps://lordvpn.site/unsub?domain=zubitours.com \r\nAddress: Address: 6982   4 Muscat Street, WA  6463\r\nLooking out for you, Chang Pina.', 'new', NULL, '194.67.213.223', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Vivaldi/5.3.2679.68', '2025-12-20 16:57:59', '2025-12-20 16:57:59', 0, 0),
(10, 'Sheri McMahan', 'sheri.mcmahan@gmail.com', '4063284499', 'other', 'Hi Zubitours Com,\r\n\r\nIf you’re still dealing with no traffic or dead lists, this might be one you want to check out.\r\n\r\n\r\nShout out to our own Harris Fellman and his partner Bill McRea for their new ������ offer...\r\n\r\n\r\nIt’s called AI Traffic Whale, and get this:\r\n\r\n\r\nThis doesn’t just teach you traffic...\r\n\r\n\r\nIt delivers it.\r\n\r\n\r\nNo bots. No theory. Just real buyer-intent traffic (the best kind of traffic) sent straight to your offer:\r\n\r\n\r\n������ Click here to check it out >> https://ai-traffic-whale.blogspot.com\r\n\r\n\r\nHeads up: The front-end gives you the system, training, and tools...\r\n\r\n\r\nIf you want DFY \'traffic\'... that’s offered in the optional upgrades.\r\n\r\n\r\nHere’s how it works:\r\n\r\n\r\nYou plug in your link\r\nTheir AI filters for real buyers\r\nYou start getting traffic in 24–48 hours\r\nDone.\r\nAnd it’s built for affiliates, biz-op, side hustle, MMO…\r\n\r\n\r\nThe kind of offers where traffic actually needs to convert.\r\n\r\n\r\nYou also get:\r\n\r\n\r\nAI Bridge Page Builder\r\nAI Landing Page Builder\r\nQuick-start training\r\nConversion tools + human support\r\n(Value: $658 - all included)\r\n\r\n\r\nBottom line: If you\'re tired of watching another “traffic hack” collect dust in your inbox...\r\n\r\n\r\nAnd you’d rather just get clicks from people who are actively looking to buy…\r\n\r\n\r\nThis one’s worth a look.\r\n\r\n\r\n������ Get access to AI Traffic Whale here >> https://ai-traffic-whale.blogspot.com\r\n\r\n\r\n\r\nTo your viral success,\r\n[Sheri McMahan]\r\n\r\nEmpowering Your Digital Marketing Success', 'new', NULL, '106.215.148.15', 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:114.0) Gecko/20100101 Firefox/114.0', '2025-12-23 11:44:26', '2025-12-23 11:44:26', 0, 0),
(11, 'Christopher Hughes', 'christopher.hughes@jmailservice.com', '8054002077', 'general', 'Imagine showing up first whenever someone searches for your product or service - that\'s what we make happen.\r\n\r\nWe launch campaigns in less than 24 hours, and you\'ll immediately start seeing targeted visitors coming to your site.\r\nCan I show you how easy it is to get started?https://www.google.com', 'new', NULL, '193.34.75.49', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:127.0) Gecko/20100101 Firefox/127.0', '2026-01-15 14:37:49', '2026-01-15 14:37:49', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `contact_settings`
--

CREATE TABLE `contact_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','email','phone','url','social') DEFAULT 'text',
  `display_name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_settings`
--

INSERT INTO `contact_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `display_name`, `category`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'contact_address', 'R-13 Wichka Complex Naqashpora Barbar Shah- Bab-demb Rd, Srinagar, 190001', 'textarea', 'Address', 'contact_info', 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(2, 'contact_phone_1', '+91 7051073293', 'phone', 'Phone 1', 'contact_info', 2, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(3, 'contact_phone_2', '+91 7006296814', 'phone', 'Phone 2', 'contact_info', 3, '2025-12-17 13:25:09', '2025-12-17 14:40:24'),
(4, 'contact_phone_3', '+91 6006696105', 'phone', 'Phone 3', 'contact_info', 4, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(5, 'contact_phone_4', '+91 9149736660', 'phone', 'Phone 4', 'contact_info', 5, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(6, 'contact_email_1', 'info@zubitours.com', 'email', 'Primary Email', 'contact_info', 6, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(7, 'contact_email_2', 'saleszubitours@gmail.com', 'email', 'Sales Email', 'contact_info', 7, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(8, 'contact_email_3', 'b2b.zubitourskashmir@gmail.com', 'email', 'B2B Email', 'contact_info', 8, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(9, 'business_hours_weekdays', 'Monday - Saturday: 9:00 AM - 6:00 PM', 'text', 'Weekdays Hours', 'contact_info', 9, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(10, 'business_hours_weekends', 'Sunday: 10:00 AM - 2:00 PM', 'text', 'Weekend Hours', 'contact_info', 10, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(11, 'social_facebook', 'https://facebook.com/zubitours', 'url', 'Facebook', 'social_media', 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(12, 'social_instagram', 'https://www.instagram.com/zubi_tours_n_holidays_kashmir', 'url', 'Instagram', 'social_media', 2, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(13, 'social_twitter', 'https://twitter.com/zubitours', 'url', 'Twitter', 'social_media', 3, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(14, 'social_youtube', 'https://youtube.com/zubitours', 'url', 'YouTube', 'social_media', 4, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(15, 'social_linkedin', 'https://linkedin.com/company/zubitours', 'url', 'LinkedIn', 'social_media', 5, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(16, 'faq_category_general', 'General Questions', 'text', 'General', 'faq_categories', 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(17, 'faq_category_booking', 'Booking & Payments', 'text', 'Booking', 'faq_categories', 2, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(18, 'faq_category_travel', 'Travel & Logistics', 'text', 'Travel', 'faq_categories', 3, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(19, 'faq_category_safety', 'Safety & Health', 'text', 'Safety', 'faq_categories', 4, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(20, 'faq_category_custom', 'Custom Packages', 'text', 'Custom', 'faq_categories', 5, '2025-12-17 13:25:09', '2025-12-17 13:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` int(11) NOT NULL,
  `destination_name` varchar(255) NOT NULL,
  `region` enum('kashmir','ladakh','jammu') NOT NULL,
  `destination_type` enum('lake','valley','mountain','monastery','hill','desert','cultural','adventure','scenic') NOT NULL,
  `best_seasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`best_seasons`)),
  `location` varchar(255) NOT NULL,
  `short_description` text NOT NULL,
  `detailed_description` longtext NOT NULL,
  `rating` decimal(3,2) DEFAULT 4.50,
  `reviews_count` int(11) DEFAULT 0,
  `badge` varchar(50) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `destination_name`, `region`, `destination_type`, `best_seasons`, `location`, `short_description`, `detailed_description`, `rating`, `reviews_count`, `badge`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dal Lake', 'kashmir', 'lake', '[\"spring\",\"summer\",\"autumn\"]', 'Srinagar', 'The jewel of Srinagar, famous for its houseboats, shikaras, and floating gardens.', 'Dal Lake is the jewel in the crown of Kashmir tourism. Spread over 18 square kilometers, this iconic lake is famous for its intricately carved houseboats, colorful shikaras (wooden boats), and floating vegetable gardens called \"Rad\". The lake is divided into four basins: Gagribal, Lokut Dal, Bod Dal and Nagin. Visitors can stay in luxurious houseboats, shop at floating markets, and experience the unique lifestyle of the lake dwellers.', 4.80, 342, 'Popular', 1, 1, '2025-12-18 06:48:44', '2025-12-19 07:03:58'),
(2, 'Pangong Lake', 'ladakh', 'lake', '[\"summer\"]', 'Changthang', 'Famous for its changing colors from blue to green to red.', 'Pangong Tso (Lake) is one of the most spectacular high-altitude lakes in the world, located at an elevation of 4,225 meters. Stretching 134 km from India to China, the lake is famous for its magical color-changing properties - from shades of blue and green to violet and red. The lake freezes completely during winter. Featured in the Bollywood movie \"3 Idiots\", it has become a must-visit destination for travelers seeking breathtaking landscapes.', 4.90, 289, 'Adventure', 1, 1, '2025-12-18 06:48:44', '2025-12-18 06:48:44'),
(3, 'Leh :  Land  of High Passes', 'ladakh', 'hill', '[\"summer\",\"winter\"]', 'Baramulla', 'Leh is the cultural capital of Ladakh, known for its monasteries, rugged landscapes, and thrilling road trips.', 'Gulmarg, meaning \"Meadow of Flowers\", is a world-class ski destination and the premier hill station of Kashmir. Located at 2,650 meters, it offers the highest gondola ride in the world reaching 3,980 meters at Apharwat Peak. In winter, it transforms into a skier\'s paradise with pristine powder snow. In summer, the meadows bloom with thousands of wildflowers, offering excellent opportunities for golfing, trekking, and nature walks.', 4.70, 456, 'Scenic', 1, 1, '2025-12-18 06:48:44', '2025-12-19 06:49:08'),
(6, 'Nubra Valley', 'ladakh', 'desert', '[\"summer\"]', 'Diskit', 'Cold desert valley famous for sand dunes, double-humped camels, and picturesque river confluence.', 'Nubra Valley, also called the \"Valley of Flowers\", is a high-altitude cold desert located 150 km north of Leh. The valley is famous for its white sand dunes, double-humped Bactrian camels, and the picturesque confluence of the Shyok and Nubra rivers. Visitors must cross the world\'s highest motorable road, Khardung La Pass (5,359m), to reach this magical valley. The Diskit Monastery with its 32-meter Maitreya Buddha statue overlooks the entire valley.', 4.90, 234, NULL, 1, 1, '2025-12-18 06:48:44', '2025-12-19 07:03:17'),
(8, 'Sonamarg', 'kashmir', 'adventure', '[\"spring\",\"summer\",\"autumn\",\"winter\"]', 'Ganderbal District, Kashmir', 'Sonamarg is known for its breathtaking glaciers, alpine meadows, and as a gateway to Ladakh via the Zoji La Pass.', '', 4.50, 58, 'Adventure', 1, 1, '2025-12-18 14:00:43', '2025-12-19 07:08:21'),
(11, 'Pahalgam', 'kashmir', 'valley', '[\"spring\",\"summer\",\"autumn\"]', 'Anantnag District, Kashmir', 'Pahalgam is a serene valley surrounded by pine forests and rivers, serving as a base for Amarnath Yatra and popular trekking routes.', '', 4.50, 140, '', 1, 1, '2025-12-18 14:45:54', '2025-12-19 07:05:45'),
(14, 'Gulmarg', 'kashmir', 'hill', '[\"spring\",\"summer\",\"winter\"]', 'Baramulla District, Kashmir', 'Gulmarg is a world-famous hill station known for Asia’s highest cable car, lush green meadows, and premier skiing slopes, making it a year-round adventure destination.', '', 4.50, 100, 'Scenic', 1, 1, '2025-12-18 17:27:36', '2025-12-19 06:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `destination_activities`
--

CREATE TABLE `destination_activities` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `activity_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty_level` enum('easy','medium','hard') DEFAULT NULL,
  `duration_hours` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `destination_highlights`
--

CREATE TABLE `destination_highlights` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `destination_images`
--

CREATE TABLE `destination_images` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destination_images`
--

INSERT INTO `destination_images` (`id`, `destination_id`, `image_path`, `is_primary`, `caption`, `created_at`) VALUES
(9, 6, 'destinations/6944f8b5848b3_sourav-bhadra-0pnQv-E92Zo-unsplash.jpeg', 1, NULL, '2025-12-19 07:03:17'),
(10, 1, 'destinations/6944f8ded26a3_naweedey-XHG0uFAlEGM-unsplash.jpeg', 1, NULL, '2025-12-19 07:03:58'),
(11, 14, 'destinations/6944f904260d0_digjot-singh-Vkkg9XoUcZI-unsplash.jpeg', 1, NULL, '2025-12-19 07:04:36'),
(12, 11, 'destinations/6944f949e6bb2_sourav-bhadra--zRhJ0V9vKI-unsplash.jpeg', 1, NULL, '2025-12-19 07:05:45'),
(13, 2, 'destinations/6944f9a1f326b_hans-jurgen-mager-MrW_AqAFNR0-unsplash.jpeg', 1, NULL, '2025-12-19 07:07:13'),
(14, 3, 'destinations/6944f9b52fc56_t2-graphy-IJfpVYlRv5I-unsplash.jpeg', 1, NULL, '2025-12-19 07:07:33'),
(15, 8, 'destinations/6944f9e5c79e4_yasser-mir-iMnIu-GoEeE-unsplash.jpeg', 1, NULL, '2025-12-19 07:08:21');

-- --------------------------------------------------------

--
-- Table structure for table `destination_tips`
--

CREATE TABLE `destination_tips` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `tip_type` enum('best_time','what_to_pack','safety','transport','food','accommodation','general') DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(50) DEFAULT 'general',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'What is the best time to visit Kashmir?', 'Kashmir is beautiful throughout the year, but the best time depends on your preferences. Spring (March to May) offers blooming flowers, summer (June to August) is perfect for sightseeing, autumn (September to November) has stunning foliage, and winter (December to February) is ideal for snow activities.', 'general', 1, 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(2, 'Do I need any permits for Ladakh?', 'Yes, certain areas in Ladakh require permits for domestic and international tourists. We handle all permit applications for our clients as part of our tour packages, making the process hassle-free for you.', 'travel', 1, 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(3, 'What is your cancellation policy?', 'We offer a flexible cancellation policy. Cancellations made 30 days before the tour receive a full refund. Between 15-30 days, we refund 70% of the amount. For cancellations within 15 days, we offer a 50% refund or the option to reschedule.', 'booking', 1, 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(4, 'Are your tours suitable for elderly travelers?', 'Absolutely! We offer specially curated tours with comfortable transportation, manageable itineraries, and accommodations that cater to the needs of elderly travelers. We can also arrange for medical assistance if needed.', 'safety', 1, 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09'),
(5, 'Do you offer customized packages?', 'Yes, we specialize in creating personalized itineraries based on your preferences, budget, and time constraints. Our travel experts will work with you to design the perfect Kashmir or Ladakh experience.', 'custom', 1, 1, '2025-12-17 13:25:09', '2025-12-17 13:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `categories` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image_path`, `categories`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(10, 'gulmarg 2025', 'heaven experiance', 'upload/gallery/1766120112_6944dab0216d8.jpg', 'lakes', 3, 1, '2025-12-19 04:55:12', '2025-12-19 04:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Kashmir', 'kashmir', '2025-12-14 08:31:26'),
(2, 'Ladakh', 'ladakh', '2025-12-14 08:31:26'),
(3, 'Lakes', 'lakes', '2025-12-14 08:31:26'),
(4, 'Mountains', 'mountains', '2025-12-14 08:31:26'),
(5, 'Culture', 'culture', '2025-12-14 08:31:26'),
(6, 'Adventure', 'adventure', '2025-12-14 08:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_brands`
--

CREATE TABLE `homepage_brands` (
  `id` int(11) NOT NULL,
  `logo_path` varchar(500) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `brand_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_brands`
--

INSERT INTO `homepage_brands` (`id`, `logo_path`, `brand_name`, `brand_url`, `created_at`) VALUES
(4, 'homepage/brands/6944eb70ddc69_Jk_Tourism.webp', 'jktourism', '', '2025-12-19 06:06:40'),
(5, 'homepage/brands/6944eb81eab6f_jktc (1).jpg', 'jktc', '', '2025-12-19 06:06:57'),
(6, 'homepage/brands/6944eb8f4e5c5_TOA-New.png', 'toa', '', '2025-12-19 06:07:11'),
(7, 'homepage/brands/6944eba331f86_travelassociation.jpg', 'travel association', '', '2025-12-19 06:07:31'),
(8, 'homepage/brands/6944ebcaccf46_zubilogo.jpg', 'zubilogo', '', '2025-12-19 06:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_gallery`
--

CREATE TABLE `homepage_gallery` (
  `id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_gallery`
--

INSERT INTO `homepage_gallery` (`id`, `image_path`, `title`, `location`, `category`, `created_at`) VALUES
(2, 'homepage/gallery/69451f186e6f5_1-amarnath-yatra-pahalgam-jammu-kashmir-city-hero.jfif', 'sonamarg', 'sonamarg', 'mountains', '2025-12-19 09:47:04');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_sections`
--

CREATE TABLE `homepage_sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `background_image` varchar(500) DEFAULT NULL,
  `meta_data` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_sections`
--

INSERT INTO `homepage_sections` (`id`, `section_name`, `title`, `subtitle`, `description`, `background_image`, `meta_data`, `updated_at`) VALUES
(1, 'hero', 'Discover Kashmir & Ladakh', 'Welcome to Paradise', 'Experience the breathtaking landscapes, rich culture, and adventure activities in these stunning regions of India.', 'homepage/69451e5f2c5f9_1-amarnath-yatra-pahalgam-jammu-kashmir-city-hero.jfif', NULL, '2025-12-23 14:38:08'),
(2, 'destinations', 'Popular Destinations', 'Explore', 'Discover the most breathtaking locations in Kashmir and Ladakh that will leave you with unforgettable memories.', NULL, NULL, '2025-12-19 09:48:42'),
(3, 'packages', 'Popular Packages', NULL, 'Carefully crafted itineraries for unforgettable experiences in Kashmir and Ladakh', NULL, NULL, '2025-12-18 16:54:08'),
(4, 'gallery', 'Photo Gallery', NULL, 'Carefully crafted itineraries for unforgettable experiences in Kashmir and Ladakh', NULL, NULL, '2025-12-18 16:54:08'),
(5, 'brands', 'Trusted By', NULL, 'Our partners and clients who trust us for their travel needs', NULL, NULL, '2025-12-18 16:54:08'),
(6, 'cta', 'Ready to Explore Kashmir & Ladakh?', NULL, 'Contact us now to plan your dream vacation with our expert guides', NULL, NULL, '2025-12-18 16:54:08');

-- --------------------------------------------------------

--
-- Table structure for table `ip_blacklist`
--

CREATE TABLE `ip_blacklist` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `attempts` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nearby_attractions`
--

CREATE TABLE `nearby_attractions` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `attraction_name` varchar(255) NOT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `package_type` enum('cultural','adventure','luxury','honeymoon','family') NOT NULL,
  `duration_days` int(11) NOT NULL,
  `max_people` int(11) NOT NULL,
  `accommodation_type` varchar(100) DEFAULT NULL,
  `price_per_person` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `itinerary` text DEFAULT NULL,
  `inclusions` text DEFAULT NULL,
  `exclusions` text DEFAULT NULL,
  `faqs` text DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 0.0,
  `reviews_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `package_name`, `package_type`, `duration_days`, `max_people`, `accommodation_type`, `price_per_person`, `description`, `itinerary`, `inclusions`, `exclusions`, `faqs`, `highlights`, `badge`, `rating`, `reviews_count`, `is_featured`, `is_active`, `created_at`, `updated_at`, `views`) VALUES
(9, 'Amazing kashmir ', 'honeymoon', 5, 6, '3 star premium', 7999.00, '0', '[{\"day\":\"1\",\"title\":\"Arrival in srinagar and local ightseeing\",\"description\":\"On arrival in Srinagar, you will be escorted your hotel, then check in to the hotel. You can also enjoy the stunning views of the Zabarwan Mountain Ranges along with a 1 hours Shikara Ride (Optional Tour) in the Dal Lake and visit famous mughal gardens Nishat,Shalimar.Dinner and overnight stay at Srinagar.\",\"activities\":[]},{\"day\":\"2\",\"title\":\"Srinagar to Sonmarg to Srinagar.\",\"description\":\"Proceed for full day excursion for Sonamarg, also called \\\"Meadow of Gold\\\", Sonamarg is located at 9000 ft. against snow-clad mountains, with the Sindh River rich in trout and mahseer flowing. One can ride on horse (own cost) to visit Thajiwas Glacier, a major attraction especially during the summers, where snow remains round the year and Sonamarg is known as Gateway of Ladakh. Return back to Srinagar. Overnight stay in the Srinagar.\",\"activities\":[]},{\"day\":\"3\",\"title\":\"Srinagar to Gulmarg to Srinagar.\",\"description\":\"Proceed to Gulmarg. The drive past colorful villages and rice fields, gives you an insight of the rich cultural past of Kashmir. We arrive at a small picturesue market town of Tangmarg and drive ahead on a scenic drive of 14 kilometers to Gulmarg. Check-in hotel after visit to world highest Golf Course and Gulmarg is also famous for winter sports skiing. One can take the Gondola Ride (optional tour) up to Alapather via Khilanmarg. Later you can do some Horse Riding (own cost) to the strawberry valley. Return back to Srinagar.Dinner and overnight stay in hotel.\",\"activities\":[]},{\"day\":\"4\",\"title\":\"Srinagar to Pahalgam to Srinagar.\",\"description\":\"Proceed to Pahalgam for full day excursion, On the way visit Saffron field and Avantipur ruins, which is eleven hundred years old temple? Finally by the afternoon you will reach Pahalgam, which is the most famous palace for Indian Film Industry. In Pahalgam, Enjoy the nature & walk around the banks of River Lidder. Here you visit to the Betaab Valley (optional tour) Aru Valley (optional tour). Come back to Srinagar. Overnight stay in the Srinagar.\",\"activities\":[]},{\"day\":\"5\",\"title\":\"Departure\",\"description\":\"After breakfast check out from the hotel and we will transfer you to airport with memories of kashmir.\",\"activities\":[]}]', '[\"Welcome drink on arrival.\",\"Accomodation on Double sharing basis.\",\"Hotels as per the plan mentioned against each hotel.\",\"Meal plan breakfast & dinner on MAPAI Basis.\",\"Pickup Srinagar airport and departure Srinagar airport.\",\"All Parking, Toll Taxes, Driver\\u2019s allowances.\"]', '[\"Any kind of Airfare, Tips, Drinks, Lunch, Laundry, Telephone Charges.\",\"Horse riding, Guide services.\",\"Any optional tour\\/Trip other than mentioned in the tour program.\",\"Vehicle from Tangmarg-Gulmarg-Tangmarg.\",\"Cable Car Ride at Gulmarg\\/Thajwas Glacier at Sonamarg.\",\"Gondola Cable car ride till phase 1 & phase 2.\",\"Sumo for sightseeing Aru, Chandanwari & Betaab valley.\",\"Entrances to Mughal Gardens\\/Betaab valley &Aru valley.\"]', '[{\"question\":\"What is the best time to take this tour?\",\"answer\":\"The best time to visit kashmir depends on what you want to experience,but overall april to october is considered ideal.\"}]', '[{\"title\":\"3 Star premium\",\"description\":\"Comfortabe stays with modern aminites\"}]', 'Romantic', 4.9, 145, 0, 1, '2025-12-24 07:44:37', '2026-01-20 07:39:24', 50);

-- --------------------------------------------------------

--
-- Table structure for table `package_bookings`
--

CREATE TABLE `package_bookings` (
  `id` int(11) NOT NULL,
  `booking_reference` varchar(20) NOT NULL,
  `package_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_notes` text DEFAULT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `number_of_adults` int(11) NOT NULL DEFAULT 1,
  `number_of_children` int(11) NOT NULL DEFAULT 0,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','cancelled','completed','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','partial','paid','refunded','failed') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_images`
--

CREATE TABLE `package_images` (
  `id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_images`
--

INSERT INTO `package_images` (`id`, `package_id`, `image_path`, `is_primary`, `display_order`, `created_at`) VALUES
(12, 9, 'packages/694b99e507277_pahalgam_kashmir_brown_chinar_kashmir.jpg.webp', 1, 0, '2025-12-24 07:44:37');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `reset_token` varchar(100) NOT NULL,
  `token_expiry` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spam_logs`
--

CREATE TABLE `spam_logs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `spam_score` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spam_logs`
--

INSERT INTO `spam_logs` (`id`, `name`, `email`, `phone`, `subject`, `message`, `ip_address`, `user_agent`, `spam_score`, `created_at`) VALUES
(1, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:42:20'),
(2, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:42:26'),
(3, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:43:11'),
(4, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:45:08'),
(5, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:45:15'),
(6, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:46:00'),
(7, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:46:31'),
(8, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:48:01'),
(9, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:48:51'),
(10, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:48:58'),
(11, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:49:00'),
(12, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:49:04'),
(13, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:49:09'),
(14, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:49:29'),
(15, 'irfan manzoor', 'drop.mail.iry@gmail.com', '06006801960', 'booking', 'm asn. .d sa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-17 14:51:37');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `testimonial_text` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `avatar_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_admins_email` (`email`),
  ADD UNIQUE KEY `ux_admins_username` (`username`),
  ADD KEY `idx_admins_email` (`email`),
  ADD KEY `idx_admins_username` (`username`),
  ADD KEY `idx_admins_role` (`role`),
  ADD KEY `idx_admins_active` (`is_active`),
  ADD KEY `idx_admins_created` (`created_at`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permission` (`role`,`permission`);

--
-- Indexes for table `callback_leads`
--
ALTER TABLE `callback_leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_bookings`
--
ALTER TABLE `car_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `car_rentals`
--
ALTER TABLE `car_rentals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_subject` (`subject`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_is_spam` (`is_spam`);

--
-- Indexes for table `contact_settings`
--
ALTER TABLE `contact_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `destination_activities`
--
ALTER TABLE `destination_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `destination_highlights`
--
ALTER TABLE `destination_highlights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `destination_images`
--
ALTER TABLE `destination_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `destination_tips`
--
ALTER TABLE `destination_tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_sort_order` (`sort_order`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `homepage_brands`
--
ALTER TABLE `homepage_brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_gallery`
--
ALTER TABLE `homepage_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_name` (`section_name`);

--
-- Indexes for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `idx_ip` (`ip_address`);

--
-- Indexes for table `nearby_attractions`
--
ALTER TABLE `nearby_attractions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`package_type`),
  ADD KEY `idx_price` (`price_per_person`),
  ADD KEY `idx_duration` (`duration_days`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_packages_active` (`is_active`),
  ADD KEY `idx_packages_featured` (`is_featured`),
  ADD KEY `idx_packages_type` (`package_type`);

--
-- Indexes for table `package_bookings`
--
ALTER TABLE `package_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_reference` (`booking_reference`),
  ADD KEY `idx_package` (`package_id`),
  ADD KEY `idx_status` (`booking_status`),
  ADD KEY `idx_payment` (`payment_status`),
  ADD KEY `idx_dates` (`checkin_date`,`checkout_date`),
  ADD KEY `idx_bookings_status` (`booking_status`),
  ADD KEY `idx_bookings_dates` (`checkin_date`,`checkout_date`);

--
-- Indexes for table `package_images`
--
ALTER TABLE `package_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_package` (`package_id`),
  ADD KEY `idx_primary` (`is_primary`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reset_token` (`reset_token`),
  ADD KEY `idx_reset_token` (`reset_token`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_token_expiry` (`token_expiry`);

--
-- Indexes for table `spam_logs`
--
ALTER TABLE `spam_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip` (`ip_address`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `callback_leads`
--
ALTER TABLE `callback_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `car_bookings`
--
ALTER TABLE `car_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `car_rentals`
--
ALTER TABLE `car_rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `contact_settings`
--
ALTER TABLE `contact_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `destination_activities`
--
ALTER TABLE `destination_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `destination_highlights`
--
ALTER TABLE `destination_highlights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `destination_images`
--
ALTER TABLE `destination_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `destination_tips`
--
ALTER TABLE `destination_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `homepage_brands`
--
ALTER TABLE `homepage_brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `homepage_gallery`
--
ALTER TABLE `homepage_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nearby_attractions`
--
ALTER TABLE `nearby_attractions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `package_bookings`
--
ALTER TABLE `package_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `package_images`
--
ALTER TABLE `package_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spam_logs`
--
ALTER TABLE `spam_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `car_bookings`
--
ALTER TABLE `car_bookings`
  ADD CONSTRAINT `car_bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `car_rentals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `destination_activities`
--
ALTER TABLE `destination_activities`
  ADD CONSTRAINT `destination_activities_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `destination_highlights`
--
ALTER TABLE `destination_highlights`
  ADD CONSTRAINT `destination_highlights_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `destination_images`
--
ALTER TABLE `destination_images`
  ADD CONSTRAINT `destination_images_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `destination_tips`
--
ALTER TABLE `destination_tips`
  ADD CONSTRAINT `destination_tips_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nearby_attractions`
--
ALTER TABLE `nearby_attractions`
  ADD CONSTRAINT `nearby_attractions_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_bookings`
--
ALTER TABLE `package_bookings`
  ADD CONSTRAINT `package_bookings_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

--
-- Constraints for table `package_images`
--
ALTER TABLE `package_images`
  ADD CONSTRAINT `package_images_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
