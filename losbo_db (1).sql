-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 04:09 PM
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
-- Database: `losbo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','rejected','completed') DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price_status` enum('waiting','sent','accepted','rejected') DEFAULT 'waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `provider_id`, `date`, `time`, `description`, `address`, `photo`, `status`, `price`, `payment_status`, `created_at`, `price_status`) VALUES
(1, 1, 1, '2026-03-21', '11:45:00', 'adwsdwe', 'margao', '69bcca480b393_photo.png', 'rejected', NULL, NULL, '2026-03-20 04:17:12', 'waiting'),
(2, 1, 1, '2026-03-20', '09:51:00', 'sdsd', 'sad', NULL, 'completed', 1000.00, 'paid', '2026-03-20 04:21:25', 'sent'),
(3, 1, 1, '2026-03-20', '16:07:00', '', 'goa', NULL, 'completed', 1000.00, 'paid', '2026-03-20 09:37:55', 'sent'),
(4, 1, 1, '2026-03-18', '13:21:00', 'dsdscsc', 'goa', NULL, 'completed', 2500.00, NULL, '2026-03-23 16:51:33', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(5, 'Carpenter'),
(4, 'Cleaning'),
(2, 'Electrician'),
(3, 'IT'),
(1, 'Plumber');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `user_id`, `service_type`, `experience`, `description`, `address`, `photo`, `profile_image`) VALUES
(1, 2, 'Plumber', NULL, NULL, NULL, NULL, 'default.png'),
(2, 3, 'Electrician', NULL, NULL, NULL, NULL, 'default.png'),
(3, 4, 'IT', NULL, NULL, NULL, NULL, 'default.png'),
(4, 5, 'Cleaning', NULL, NULL, NULL, NULL, 'default.png'),
(5, 6, 'Carpenter', NULL, NULL, NULL, NULL, 'default.png'),
(6, 7, 'Cleaning', NULL, NULL, NULL, NULL, 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `booking_id`, `provider_id`, `customer_id`, `rating`, `review`, `created_at`) VALUES
(1, 2, 1, 1, 4, 'ihihoih', '2026-03-20 04:37:20'),
(2, 3, 1, 1, 5, '', '2026-03-23 15:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('customer','provider') DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `gender` varchar(10) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `providers`
--
ALTER TABLE `providers`
  ADD CONSTRAINT `providers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('customer','provider') NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    reference_id INT NULL,
    reference_type VARCHAR(50) NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE bookings
ADD CONSTRAINT fk_booking_customer
FOREIGN KEY (customer_id) REFERENCES users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE bookings
ADD CONSTRAINT fk_booking_provider
FOREIGN KEY (provider_id) REFERENCES providers(id)
ON DELETE CASCADE
ON UPDATE CASCADE;
