-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 09:19 AM
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
-- Database: `attendance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('Present','Leave','WFH') DEFAULT 'Present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `check_in`, `check_out`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-12-05', '09:35:00', '18:20:00', 'Present', '2024-12-05 10:48:14', '2024-12-05 10:48:14'),
(2, 1, '2024-12-06', '09:45:00', '00:00:00', 'Present', '2024-12-06 08:11:48', '2024-12-06 08:11:48'),
(3, 1, '2024-12-10', '09:30:00', '00:00:00', 'Present', '2024-12-10 06:58:42', '2024-12-10 06:58:42'),
(4, 1, '2024-12-10', '12:30:00', '18:30:00', 'Present', '2024-12-10 06:59:13', '2024-12-10 06:59:13'),
(5, 1, '2024-12-10', '00:00:00', '00:00:00', 'Present', '2024-12-10 06:59:57', '2024-12-10 06:59:57'),
(6, 1, '2024-12-10', '00:00:00', '00:00:00', 'Present', '2024-12-10 07:03:00', '2024-12-10 07:03:00'),
(7, 1, '2024-12-10', '12:35:00', '18:33:00', 'Present', '2024-12-10 07:03:13', '2024-12-10 07:03:13'),
(8, 1, '2024-12-10', '15:33:00', '18:33:00', 'Present', '2024-12-10 07:03:30', '2024-12-10 07:03:30'),
(9, 1, '2024-12-10', '18:37:00', '18:37:00', 'Present', '2024-12-10 07:07:11', '2024-12-10 07:07:11'),
(10, 1, '2024-12-10', '18:37:00', '18:37:00', 'Present', '2024-12-10 07:07:38', '2024-12-10 07:07:38'),
(11, 1, '2024-12-10', '17:39:00', '06:39:00', 'Present', '2024-12-10 07:09:31', '2024-12-10 07:09:31');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `date_of_joining` date NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `position`, `date_of_joining`, `status`) VALUES
(1, 'Tushar Lohar', 'tusharlohar1137@gmail.com', 'Web Developer', '2023-09-20', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `date`, `description`, `created_at`) VALUES
(1, '2024-12-25', 'Mera Yesu Yesu', '2024-12-05 10:51:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date` (`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
