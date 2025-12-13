-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 06:16 AM
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
-- Database: `travel_db`
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
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `username`, `password`) VALUES
(1, 'Admin', 'Admin@gmail.com', 'tester@123', '$2y$10$vvbDe84/lPKi9bDXuZ4cz.8xAIXntU5rdhixs8cBcOi/zMgnhn5Fe');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `region` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `location` varchar(100) NOT NULL,
  `rating` decimal(2,1) NOT NULL CHECK (`rating` between 0.0 and 5.0),
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `name`, `region`, `description`, `category`, `status`, `location`, `rating`, `image`) VALUES
(7, 'fxgds', 'kashmir', 'dfsgs', 'lake', 'active', 'sdfg', 3.0, 'dest_68fdcebc7256f.jpg'),
(8, 'jammu', 'kashmir', 'hello world thsi is iry', 'lake', 'active', 'kashmir', 4.5, 'dest_693abe5bee108.jpeg'),
(9, 'jammu', 'jammu', 'this is testing', 'adventure', 'active', 'jammu', 2.1, 'dest_693ac6f180f20.png'),
(10, 'Pangong Lake', 'ladakh', 'Famous for its changing colors from blue to green to red. A breathtaking high-altitude lake that stretches from India to China.', 'mountain', 'active', 'chaitti', 4.0, 'dest_693ac76da73e8.jpg'),
(11, 'tulain vally', 'kashmir', 'tulain cftubihunj sbdfnsjk bndsnlfgd bsbfdhds; jahdogheoq sdfhasiufh sdfjnanfdjknaj You use LIMIT in SQL to control how many rows your query should return. It’s simple and very useful when you want only a few records.\r\n\r\nHere’s the basic syntax:\r\n\r\nSELECT', 'lake', 'active', 'jammu', 3.4, 'dest_693acac607cf7.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_admins_email` (`email`),
  ADD UNIQUE KEY `ux_admins_username` (`username`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
