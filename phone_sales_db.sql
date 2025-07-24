-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 24, 2025 at 09:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phone_sales_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenditures`
--

CREATE TABLE `expenditures` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenditures`
--

INSERT INTO `expenditures` (`id`, `title`, `amount`, `category`, `description`, `added_by`, `created_at`) VALUES
(1, 'food', 15000.00, 'Food', 'we were 2 people me , etc, etc', 119, '2025-07-17 09:10:37'),
(2, 'food', 15000.00, 'Food', 'we were 2 people me , etc, etc', 119, '2025-07-17 09:11:04'),
(3, 'food', 15000.00, 'Food', 'we were 2 people me , etc, etc', 119, '2025-07-17 09:13:03'),
(4, 'outting', 5000000.00, 'Other', 'we were 2 people me , etc, etc', 119, '2025-07-17 09:20:41');

-- --------------------------------------------------------

--
-- Table structure for table `gadgets`
--

CREATE TABLE `gadgets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 1,
  `added_by` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gadgets`
--

INSERT INTO `gadgets` (`id`, `name`, `model`, `serial_number`, `specifications`, `price`, `quantity`, `added_by`, `added_at`) VALUES
(1, 'lightening cable', '', '', '3 m;', 20000, 16, 119, '2025-07-22 04:47:35');

-- --------------------------------------------------------

--
-- Table structure for table `phones`
--

CREATE TABLE `phones` (
  `id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `imei` varchar(20) NOT NULL,
  `storage` varchar(20) NOT NULL,
  `color` varchar(30) NOT NULL,
  `condition` enum('New','Refurbished','Used') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_by` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Available','Sold','Returned','Swapped','Damaged') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phones`
--

INSERT INTO `phones` (`id`, `brand`, `model`, `imei`, `storage`, `color`, `condition`, `price`, `quantity`, `added_by`, `added_at`, `status`) VALUES
(1, 'Apple', 'iPhone 13', '123456789012345', '128GB', 'Blue', 'New', 1150000.00, 42, NULL, '2025-05-07 04:49:05', 'Available'),
(2, 'Samsung', 'Galaxy S22', '234567890123456', '256GB', 'Black', 'New', 3500000.00, 0, NULL, '2025-05-07 04:49:05', 'Available'),
(3, 'Google', 'Pixel 6', '345678901234567', '128GB', 'White', 'Refurbished', 2800000.00, 0, NULL, '2025-05-07 04:49:05', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `phone_id` int(11) DEFAULT NULL,
  `return_reason` text DEFAULT NULL,
  `status` enum('taken','swapped','Returned') DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`id`, `sale_id`, `phone_id`, `return_reason`, `status`, `processed_by`, `return_date`) VALUES
(2, 60, 1, 'wasnt taken', 'Returned', 119, '2025-07-24 16:48:17'),
(3, 61, 1, 'not taken', 'Returned', 119, '2025-07-24 16:52:38'),
(4, 63, 1, 'yes', 'Returned', 119, '2025-07-24 17:40:56'),
(5, 63, 1, 'yes', 'Returned', 119, '2025-07-24 17:42:59'),
(6, 63, 1, 'yes', 'Returned', 119, '2025-07-24 17:45:52'),
(7, 64, 1, 'yah', 'Returned', 119, '2025-07-24 17:48:49'),
(8, 66, 1, 'no', 'Returned', 119, '2025-07-24 18:45:01'),
(9, 65, 1, 'rjeirbge', 'Returned', 119, '2025-07-24 18:46:16'),
(10, 67, 1, 'fsr', 'Returned', 119, '2025-07-24 18:54:09'),
(11, 67, 1, 'fsr', 'Returned', 119, '2025-07-24 18:57:34'),
(12, 67, 1, 'fsr', 'Returned', 119, '2025-07-24 19:00:16'),
(13, 62, 1, 'cfgd', 'Returned', 119, '2025-07-24 19:35:15'),
(14, 61, 1, 'jdicjd', 'Returned', 119, '2025-07-24 19:35:48');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `phone_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `sold_by` int(11) DEFAULT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `balance_due` decimal(10,2) GENERATED ALWAYS AS (`sale_price` - `amount_paid`) STORED,
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `gadget_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `phone_id`, `customer_name`, `customer_phone`, `sale_price`, `sold_by`, `sale_date`, `amount_paid`, `approval_status`, `gadget_id`) VALUES
(1, 2, 'Walk-in Customer', '0778485512', 899.00, 118, '2025-07-12 14:27:35', 899.00, 'Approved', NULL),
(2, 1, 'Walk-in Customer', '0778485512', 1050000.00, 118, '2025-07-12 18:52:29', 1050000.00, 'Approved', NULL),
(3, 1, 'Walk-in Customer', '0778485512', 1150000.00, 119, '2025-07-17 09:19:03', 1150000.00, 'Approved', NULL),
(4, 1, 'Walk-in Customer', '0778485512', 1150000.00, 118, '2025-07-17 11:52:11', 1150000.00, 'Approved', NULL),
(5, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:12:35', 1000000.00, 'Pending', NULL),
(6, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:16:48', 1000000.00, 'Pending', NULL),
(7, 1, 'Walk-in Customer', '0778485512', 1150000.00, 119, '2025-07-17 17:17:08', 1000000.00, 'Pending', NULL),
(8, 3, 'Walk-in Customer', '0778485512', 2800000.00, 119, '2025-07-17 17:19:31', 1000000.00, 'Pending', NULL),
(9, 2, 'Walk-in Customer', '0778485512', 3500000.00, 119, '2025-07-17 17:21:22', 4000000.00, 'Pending', NULL),
(10, 1, 'Walk-in Customer', '0778485512', 1150000.00, 119, '2025-07-17 17:26:21', 1000000.00, 'Pending', NULL),
(11, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:29:07', 1150000.00, 'Pending', NULL),
(12, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:30:06', 1150000.00, 'Pending', NULL),
(13, 1, 'Walk-in Custo', '0778485512', 1150000.00, 119, '2025-07-17 17:31:47', 1150000.00, 'Pending', NULL),
(14, 3, 'Walk-in Customer', '0778485512', 2800000.00, 119, '2025-07-17 17:35:11', 2800000.00, 'Pending', NULL),
(15, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:37:14', 3500000.00, 'Pending', NULL),
(16, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:40:26', 3500000.00, 'Pending', NULL),
(17, 3, 'noel', '0778485512', 2800000.00, 119, '2025-07-17 17:41:55', 2800000.00, 'Pending', NULL),
(18, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:43:10', 3500000.00, 'Pending', NULL),
(19, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:46:00', 3500000.00, 'Pending', NULL),
(20, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:50:56', 3500000.00, 'Pending', NULL),
(21, 3, 'noel', '0778485512', 2800000.00, 119, '2025-07-17 17:53:37', 2800000.00, 'Pending', NULL),
(22, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 18:08:22', 3500000.00, 'Pending', NULL),
(23, 3, 'noel', '0778485512', 2800000.00, 119, '2025-07-17 18:13:21', 2800000.00, 'Pending', NULL),
(24, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:15:33', 1150000.00, 'Pending', NULL),
(25, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:17:19', 1150000.00, 'Pending', NULL),
(26, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:19:13', 1150000.00, 'Pending', NULL),
(27, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:21:26', 1150000.00, 'Pending', NULL),
(28, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:26:49', 1150000.00, 'Pending', NULL),
(29, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:29:12', 1150000.00, 'Pending', NULL),
(30, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:30:41', 1150000.00, 'Pending', NULL),
(31, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:34:06', 1150000.00, 'Pending', NULL),
(32, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:36:24', 1150000.00, 'Pending', NULL),
(33, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:39:03', 1150000.00, 'Pending', NULL),
(34, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:41:45', 1150000.00, 'Pending', NULL),
(35, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:44:45', 1150000.00, 'Pending', NULL),
(36, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:47:54', 1150000.00, 'Pending', NULL),
(37, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:50:55', 1150000.00, 'Pending', NULL),
(38, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:53:59', 1150000.00, 'Pending', NULL),
(39, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:55:04', 1150000.00, 'Pending', NULL),
(40, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-18 17:49:54', 1150000.00, 'Approved', NULL),
(41, 1, 'phillipo', '0778485512', 1150000.00, 119, '2025-07-20 17:34:28', 2800000.00, 'Pending', NULL),
(42, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-20 17:35:45', 1150000.00, 'Pending', NULL),
(43, 1, 'phillipo', '0778485512', 1150000.00, 119, '2025-07-20 17:36:44', 1150000.00, 'Pending', NULL),
(44, 1, 'phillipo', '0778485512', 1150000.00, 119, '2025-07-22 05:04:38', 1150000.00, 'Pending', NULL),
(45, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 06:50:51', 20000.00, 'Approved', 1),
(46, NULL, 'phillipo', '0778485512', 20000.00, 119, '2025-07-22 07:09:25', 20000.00, 'Pending', 1),
(47, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 07:15:20', 20000.00, 'Pending', 1),
(48, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 07:25:08', 20000.00, 'Pending', 1),
(49, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 07:27:08', 20000.00, 'Pending', 1),
(50, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 07:50:39', 20000.00, 'Pending', 1),
(51, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 07:55:41', 20000.00, 'Pending', 1),
(52, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 07:56:33', 20000.00, 'Pending', 1),
(53, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 07:58:06', 20000.00, 'Pending', 1),
(54, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 08:00:27', 20000.00, 'Pending', 1),
(55, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 08:04:32', 20000.00, 'Pending', 1),
(56, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 08:13:24', 20000.00, 'Pending', 1),
(57, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 08:15:08', 20000.00, 'Pending', 1),
(58, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 08:15:32', 20000.00, 'Pending', 1),
(59, NULL, 'noel', '0778485512', 20000.00, 119, '2025-07-22 08:15:59', 20000.00, 'Pending', 1),
(60, 1, 'Omuyiribi', '0778485512', 1150000.00, 119, '2025-07-24 15:58:24', 1050000.00, 'Pending', NULL),
(61, 1, 'jamilah', '0778485512', 1150000.00, 119, '2025-07-24 16:51:23', 0.00, 'Rejected', NULL),
(62, 1, 'Martin', '0778485512', 1150000.00, 119, '2025-07-24 17:20:52', 0.00, 'Rejected', NULL),
(63, 1, 'dan', '0778485512', 1150000.00, 119, '2025-07-24 17:40:32', 0.00, 'Rejected', NULL),
(64, 1, 'Walk-in Customer', '0778485512', 1150000.00, 119, '2025-07-24 17:48:12', 0.00, 'Rejected', NULL),
(65, 1, 'example', '0778485512', 1150000.00, 119, '2025-07-24 18:34:52', 0.00, 'Rejected', NULL),
(66, 1, 'trial', '0778485512', 1150000.00, 119, '2025-07-24 18:35:33', 0.00, 'Rejected', NULL),
(67, 1, 'phillipo', '0778485512', 1150000.00, 119, '2025-07-24 18:52:42', 0.00, 'Rejected', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `swaps`
--

CREATE TABLE `swaps` (
  `id` int(11) NOT NULL,
  `return_id` int(11) DEFAULT NULL,
  `old_phone_id` int(11) DEFAULT NULL,
  `new_phone_id` int(11) DEFAULT NULL,
  `top_up_amount` decimal(10,2) DEFAULT 0.00,
  `swapped_by` int(11) DEFAULT NULL,
  `swap_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `old_phone_serial` varchar(50) DEFAULT NULL,
  `new_phone_serial` varchar(50) DEFAULT NULL,
  `old_phone_brand_model` varchar(100) DEFAULT NULL,
  `new_phone_brand_model` varchar(100) DEFAULT NULL,
  `valued_amount` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `recipient_name` varchar(100) DEFAULT NULL,
  `balance_due` int(11) DEFAULT 0,
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `swaps`
--

INSERT INTO `swaps` (`id`, `return_id`, `old_phone_id`, `new_phone_id`, `top_up_amount`, `swapped_by`, `swap_date`, `old_phone_serial`, `new_phone_serial`, `old_phone_brand_model`, `new_phone_brand_model`, `valued_amount`, `amount_paid`, `recipient_name`, `balance_due`, `approval_status`) VALUES
(1, NULL, NULL, 1, 450000.00, 118, '2025-07-12 18:24:57', 'MY988403S', 'FYTUJHUTY7', 'iPhone 11', 'Apple iPhone 13', 500000.00, 600000.00, 'NOEL ', 0, 'Pending'),
(2, NULL, NULL, 1, 450000.00, 119, '2025-07-17 09:18:40', 'MY988403S', 'FYTUJHUTY7', 'iPhone 11', 'Apple iPhone 13', 500000.00, 650000.00, 'NOEL ', 0, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `last_login`) VALUES
(118, 'RUTAHIGWA EMMANUEL NOEL', 'noel@gmail.com', '$2y$12$KBWS37miSnkOXncR941ceendWgtVCpXxZroxjwgjs8Bo4xq9KASbG', 'staff', '2025-07-03 03:58:57', NULL),
(119, 'Admin', 'admin@gmail.com', '$2y$12$ZI40x7.QxDGZplr.5XLXqeuSxOBUEWdfibL2Xcz3ImF.4AF2PGiZi', 'admin', '2025-07-12 14:05:53', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expenditures`
--
ALTER TABLE `expenditures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `gadgets`
--
ALTER TABLE `gadgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`);

--
-- Indexes for table `phones`
--
ALTER TABLE `phones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `imei` (`imei`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `phone_id` (`phone_id`),
  ADD KEY `returns_ibfk_3` (`processed_by`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phone_id` (`phone_id`),
  ADD KEY `sales_ibfk_2` (`sold_by`),
  ADD KEY `sales_ibfk_3` (`gadget_id`);

--
-- Indexes for table `swaps`
--
ALTER TABLE `swaps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `old_phone_id` (`old_phone_id`),
  ADD KEY `new_phone_id` (`new_phone_id`),
  ADD KEY `swapped_by` (`swapped_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expenditures`
--
ALTER TABLE `expenditures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gadgets`
--
ALTER TABLE `gadgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phones`
--
ALTER TABLE `phones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `swaps`
--
ALTER TABLE `swaps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenditures`
--
ALTER TABLE `expenditures`
  ADD CONSTRAINT `expenditures_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `phones`
--
ALTER TABLE `phones`
  ADD CONSTRAINT `phones_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `returns_ibfk_2` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`),
  ADD CONSTRAINT `returns_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`sold_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`gadget_id`) REFERENCES `gadgets` (`id`);

--
-- Constraints for table `swaps`
--
ALTER TABLE `swaps`
  ADD CONSTRAINT `swaps_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `returns` (`id`),
  ADD CONSTRAINT `swaps_ibfk_2` FOREIGN KEY (`old_phone_id`) REFERENCES `phones` (`id`),
  ADD CONSTRAINT `swaps_ibfk_3` FOREIGN KEY (`new_phone_id`) REFERENCES `phones` (`id`),
  ADD CONSTRAINT `swaps_ibfk_4` FOREIGN KEY (`swapped_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
