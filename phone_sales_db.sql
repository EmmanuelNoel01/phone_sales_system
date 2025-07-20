-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2025 at 04:28 PM
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
(1, 'Apple', 'iPhone 13', '123456789012345', '128GB', 'Blue', 'New', 1150000.00, 43, NULL, '2025-05-07 04:49:05', 'Returned'),
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
  `status` enum('Repairing','Swapped','Refunded') DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `phone_id`, `customer_name`, `customer_phone`, `sale_price`, `sold_by`, `sale_date`, `amount_paid`, `approval_status`) VALUES
(1, 2, 'Walk-in Customer', '0778485512', 899.00, 118, '2025-07-12 14:27:35', 899.00, 'Approved'),
(2, 1, 'Walk-in Customer', '0778485512', 1050000.00, 118, '2025-07-12 18:52:29', 1050000.00, 'Approved'),
(3, 1, 'Walk-in Customer', '0778485512', 1150000.00, 119, '2025-07-17 09:19:03', 1150000.00, 'Approved'),
(4, 1, 'Walk-in Customer', '0778485512', 1150000.00, 118, '2025-07-17 11:52:11', 1150000.00, 'Approved'),
(5, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:12:35', 1000000.00, 'Pending'),
(6, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:16:48', 1000000.00, 'Pending'),
(7, 1, 'Walk-in Customer', '0778485512', 1150000.00, 119, '2025-07-17 17:17:08', 1000000.00, 'Pending'),
(8, 3, 'Walk-in Customer', '0778485512', 2800000.00, 119, '2025-07-17 17:19:31', 1000000.00, 'Pending'),
(9, 2, 'Walk-in Customer', '0778485512', 3500000.00, 119, '2025-07-17 17:21:22', 4000000.00, 'Pending'),
(10, 1, 'Walk-in Customer', '0778485512', 1150000.00, 119, '2025-07-17 17:26:21', 1000000.00, 'Pending'),
(11, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:29:07', 1150000.00, 'Pending'),
(12, 1, 'xxxxxx', '0778485512', 1150000.00, 119, '2025-07-17 17:30:06', 1150000.00, 'Pending'),
(13, 1, 'Walk-in Custo', '0778485512', 1150000.00, 119, '2025-07-17 17:31:47', 1150000.00, 'Pending'),
(14, 3, 'Walk-in Customer', '0778485512', 2800000.00, 119, '2025-07-17 17:35:11', 2800000.00, 'Pending'),
(15, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:37:14', 3500000.00, 'Pending'),
(16, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:40:26', 3500000.00, 'Pending'),
(17, 3, 'noel', '0778485512', 2800000.00, 119, '2025-07-17 17:41:55', 2800000.00, 'Pending'),
(18, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:43:10', 3500000.00, 'Pending'),
(19, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:46:00', 3500000.00, 'Pending'),
(20, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 17:50:56', 3500000.00, 'Pending'),
(21, 3, 'noel', '0778485512', 2800000.00, 119, '2025-07-17 17:53:37', 2800000.00, 'Pending'),
(22, 2, 'noel', '0778485512', 3500000.00, 119, '2025-07-17 18:08:22', 3500000.00, 'Pending'),
(23, 3, 'noel', '0778485512', 2800000.00, 119, '2025-07-17 18:13:21', 2800000.00, 'Pending'),
(24, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:15:33', 1150000.00, 'Pending'),
(25, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:17:19', 1150000.00, 'Pending'),
(26, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:19:13', 1150000.00, 'Pending'),
(27, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:21:26', 1150000.00, 'Pending'),
(28, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:26:49', 1150000.00, 'Pending'),
(29, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:29:12', 1150000.00, 'Pending'),
(30, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:30:41', 1150000.00, 'Pending'),
(31, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:34:06', 1150000.00, 'Pending'),
(32, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:36:24', 1150000.00, 'Pending'),
(33, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:39:03', 1150000.00, 'Pending'),
(34, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:41:45', 1150000.00, 'Pending'),
(35, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:44:45', 1150000.00, 'Pending'),
(36, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:47:54', 1150000.00, 'Pending'),
(37, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:50:55', 1150000.00, 'Pending'),
(38, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:53:59', 1150000.00, 'Pending'),
(39, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-17 18:55:04', 1150000.00, 'Pending'),
(40, 1, 'noel', '0778485512', 1150000.00, 119, '2025-07-18 17:49:54', 1150000.00, 'Approved');

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
  ADD KEY `sales_ibfk_2` (`sold_by`);

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
-- AUTO_INCREMENT for table `phones`
--
ALTER TABLE `phones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`sold_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
