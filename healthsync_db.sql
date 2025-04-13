-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2025 at 01:22 PM
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
-- Database: `healthsync_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 5, 'Approved appointment ID: 2', '2025-04-04 19:31:11'),
(2, 3, 'Sent reminder for appointment ID: 2', '2025-04-04 19:31:11'),
(3, 5, 'Appointment canceled (ID: 1)', '2025-04-04 19:36:28'),
(4, 5, 'Appointment approved (ID: 4)', '2025-04-05 14:06:59'),
(5, 6, 'Sent reminder for appointment ID: 4', '2025-04-05 14:08:09'),
(6, 5, 'Marked medicine order as delivered (ID: 1)', '2025-04-05 14:35:26'),
(7, 5, 'Marked medicine order as delivered (ID: 4)', '2025-04-05 15:38:27'),
(8, 3, 'Sent reminder for appointment ID: 1', '2025-04-05 16:29:24'),
(9, 1, 'Canceled medicine order (ID: 3)', '2025-04-05 19:14:23'),
(10, 1, 'Canceled medicine order (ID: 3)', '2025-04-05 19:14:35'),
(11, 3, 'Sent reminder for appointment ID: 1', '2025-04-05 19:15:26'),
(12, 1, 'Canceled medicine order (ID: 5)', '2025-04-05 19:37:47'),
(13, 1, 'Rescheduled appointment (ID: 4)', '2025-04-05 19:38:53'),
(14, 5, 'Appointment canceled (ID: 4)', '2025-04-05 20:00:10'),
(15, 9, 'Booked appointment (ID: 5)', '2025-04-05 20:13:40'),
(16, 5, 'Appointment approved (ID: 5)', '2025-04-05 20:14:11'),
(17, 6, 'Sent reminder for appointment ID: 5', '2025-04-05 20:28:35'),
(18, 6, 'Sent reminder for appointment ID: 5', '2025-04-05 20:30:03'),
(19, 10, 'Booked appointment (ID: 6)', '2025-04-05 20:35:38'),
(20, 5, 'Appointment approved (ID: 6)', '2025-04-05 20:36:15'),
(21, 3, 'Sent reminder for appointment ID: 6', '2025-04-05 20:37:55'),
(22, 9, 'Booked appointment (ID: 7)', '2025-04-05 20:39:19'),
(23, 5, 'Appointment approved (ID: 7)', '2025-04-05 20:39:42'),
(24, 11, 'Booked appointment (ID: 8)', '2025-04-05 20:43:08'),
(25, 5, 'Appointment canceled (ID: 8)', '2025-04-05 20:43:43'),
(26, 9, 'Booked appointment (ID: 9)', '2025-04-05 20:45:39'),
(27, 5, 'Appointment canceled (ID: 9)', '2025-04-05 20:46:06'),
(28, 11, 'Booked appointment (ID: 10)', '2025-04-05 20:51:25'),
(29, 5, 'Appointment approved (ID: 10)', '2025-04-05 20:51:47'),
(30, 9, 'Rescheduled appointment (ID: 7)', '2025-04-06 07:10:54'),
(31, 5, 'Appointment approved (ID: 7)', '2025-04-06 07:21:25'),
(32, 5, 'Appointment approved (ID: 7)', '2025-04-06 07:28:11'),
(33, 3, 'Sent reminder for appointment ID: 7', '2025-04-06 07:47:38'),
(34, 9, 'Placed medicine order (ID: 6)', '2025-04-06 08:02:21'),
(35, 9, 'Booked appointment (ID: 11)', '2025-04-06 08:04:47'),
(36, 5, 'Appointment canceled (ID: 11)', '2025-04-06 08:05:42'),
(37, 9, 'Booked appointment (ID: 12)', '2025-04-06 08:17:33'),
(38, 5, 'Appointment canceled (ID: 12)', '2025-04-06 08:17:55'),
(39, 5, 'Marked medicine order as delivered (ID: 6)', '2025-04-06 08:19:16'),
(40, 10, 'Placed medicine order (ID: 7)', '2025-04-06 08:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `reason`, `status`, `created_at`) VALUES
(1, 1, 3, '2025-04-10 10:00:00', 'Chest pain', 'canceled', '2025-04-04 19:30:41'),
(4, 1, 6, '2025-04-24 08:30:00', 'headache', 'canceled', '2025-04-05 14:04:39'),
(5, 9, 6, '2025-04-07 13:00:00', 'back pain', 'approved', '2025-04-05 20:13:40'),
(6, 10, 3, '2025-04-07 22:30:00', 'coughs', 'approved', '2025-04-05 20:35:38'),
(7, 9, 3, '2025-04-22 10:10:00', 'fever', 'approved', '2025-04-05 20:39:19'),
(8, 11, 3, '2025-04-17 21:30:00', 'fever', 'canceled', '2025-04-05 20:43:08'),
(9, 9, 3, '2025-04-17 23:45:00', 'fever', 'canceled', '2025-04-05 20:45:39'),
(10, 11, 3, '2025-04-08 23:51:00', 'fever', 'approved', '2025-04-05 20:51:25'),
(11, 9, 3, '2025-04-30 11:04:00', 'fever', 'canceled', '2025-04-06 08:04:47'),
(12, 9, 3, '2025-04-30 11:17:00', 'FEVER', 'canceled', '2025-04-06 08:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_orders`
--

CREATE TABLE `medicine_orders` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('pending','delivered','canceled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_orders`
--

INSERT INTO `medicine_orders` (`id`, `patient_id`, `medicine_name`, `quantity`, `status`, `created_at`) VALUES
(1, 1, 'Paracetamol', 10, 'delivered', '2025-04-04 19:31:33'),
(4, 1, 'strepsils', 2, 'delivered', '2025-04-05 15:21:41'),
(5, 1, 'vitamins', 12, 'canceled', '2025-04-05 16:30:25'),
(6, 9, 'Asprin', 12, 'delivered', '2025-04-06 08:02:21'),
(7, 10, 'vitamins', 12, 'pending', '2025-04-06 08:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('patient','doctor','admin') NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `first_name`, `last_name`, `specialty`, `created_at`) VALUES
(1, 'patient1@example.com', '$2y$10$LS1NTul84T2.X6oDHAek4.T/1T2nRgyxCwpCtrU37bWMO6gex0.Gu', 'patient', 'John', 'K', NULL, '2025-04-04 19:29:57'),
(3, 'doctor1@example.com', '$2y$10$opg7hEN3BGTg8XkMVtP2YuHj67bzFMpEB/RxupZWdtmIFjxp.AEse', 'doctor', 'Alice', 'B', 'Cardiology', '2025-04-04 19:29:57'),
(5, 'admin@example.com', '$2y$10$3DLJqYsfpNQECsAin/DpxOLaHg4uIhGXT1kZQr7joE5tpiG9283Oa', 'admin', 'Admin', 'User', NULL, '2025-04-04 19:29:57'),
(6, 'dockev@gmail.com', '$2y$10$kUvVbCdu7b0.rPRbUdsFWOixgySzCU7.feyjIiUJrSI.FIQn59g1m', 'doctor', 'kev', '', 'General practice', '2025-04-05 13:59:36'),
(9, 'karanjapitah1@gmail.com', '$2y$10$9LZittmt2.wMp2qoz3DkBu6tQEDfJgdYjZfg2WzduXk9cWTyzIS0W', 'patient', 'peter', '', NULL, '2025-04-05 20:12:45'),
(10, 'kelvinmwaniki62@gmail.com', '$2y$10$gnAAY9xdcohxCwlD/hrDDO1YF5VDk89GSK3OXMDSrwxpCdt509NQW', 'patient', 'Manix', '', NULL, '2025-04-05 20:34:36'),
(11, 'chachawilliam2000@gmail.com', '$2y$10$wfonOn7XgV6P7cRimeoa.ekQGhBUxmgIi96PkEwFPP45pSsntuAYO', 'patient', 'Matundaree', '', NULL, '2025-04-05 20:42:16');

-- --------------------------------------------------------

--
-- Table structure for table `wellness_content`
--

CREATE TABLE `wellness_content` (
  `id` int(11) NOT NULL,
  `type` enum('tip','video') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wellness_content`
--

INSERT INTO `wellness_content` (`id`, `type`, `title`, `content`, `created_at`) VALUES
(1, 'tip', 'Stay Hydrated', 'Drink at least 8 glasses of water daily to maintain optimal health.', '2025-04-05 15:09:27'),
(3, 'tip', 'Eat Balanced Meals', 'Incorporate a variety of fruits, vegetables, and lean proteins.', '2025-04-05 15:09:27'),
(4, 'tip', 'Get Enough Sleep', 'Aim for 7-8 hours of sleep per night to support recovery.', '2025-04-05 15:09:27'),
(10, 'tip', 'Exercise Regularly', 'Aim for 30 minutes of moderate exercise most days to boost energy.', '2025-04-05 15:28:13'),
(15, 'video', 'Yoga For Complete Beginners - 20 Minute Home Yoga Workout!', 'https://www.youtube.com/embed/v7AYKMP6rOE', '2025-04-05 15:28:13'),
(16, 'video', 'Stress Management Strategies: Ways to Unwind', 'https://www.youtube.com/embed/0fL-pn80s-c', '2025-04-05 15:28:13'),
(17, 'video', '15 Minute Beginner Stretch Flexibility Routine! (FOLLOW ALONG)', 'https://www.youtube.com/embed/L_xrDAtykMI', '2025-04-05 15:28:13'),
(18, 'video', '10 Minute Full Body Stretch', 'https://www.youtube.com/embed/QR0JKN1NmV8', '2025-04-05 15:28:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `medicine_orders`
--
ALTER TABLE `medicine_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wellness_content`
--
ALTER TABLE `wellness_content`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `medicine_orders`
--
ALTER TABLE `medicine_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `wellness_content`
--
ALTER TABLE `wellness_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_orders`
--
ALTER TABLE `medicine_orders`
  ADD CONSTRAINT `medicine_orders_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
