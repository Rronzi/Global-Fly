-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2026 at 07:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `globalfly`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `flight_number` varchar(20) NOT NULL,
  `departure` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `departure_date` datetime NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `flight_number`, `departure`, `destination`, `departure_date`, `price`, `image`, `created_by`, `created_at`) VALUES
(1, 'GF001', 'New York City, USA', 'Miami, Florida', '2026-03-01 10:00:00', 190, 'photos/miami.jpg', 1, '2026-02-01 00:00:00'),
(2, 'GF002', 'New York City, USA', 'Dubai, UAE', '2026-03-02 12:00:00', 250, 'photos/dubai.webp', 1, '2026-02-01 00:00:00'),
(3, 'GF003', 'New York City, USA', 'Istanbul, Turkey', '2026-03-03 14:00:00', 500, 'photos/istanbul.jpg', 1, '2026-02-01 00:00:00'),
(4, 'GF004', 'London, UK', 'Madrid, Spain', '2026-03-04 16:00:00', 800, 'photos/madrid.jpg', 1, '2026-02-01 00:00:00'),
(5, 'GF005', 'London, UK', 'Paris, France', '2026-03-05 18:00:00', 1200, 'photos/paris.webp', 1, '2026-02-01 00:00:00'),
(6, 'GF006', 'London, UK', 'Rome, Italy', '2026-03-06 20:00:00', 2000, 'photos/rome.webp', 1, '2026-02-01 00:00:00'),
(7, 'GF007', 'Prishtina, Kosovo', 'Rio de Janeiro, Brazil', '2026-03-07 22:00:00', 190, 'photos/riodejaneiro.jpg', 1, '2026-02-01 00:00:00'),
(8, 'GF008', 'Prishtina, Kosovo', 'Rome, Italy', '2026-03-08 08:00:00', 250, 'photos/rome.webp', 1, '2026-02-01 00:00:00'),
(9, 'GF009', 'Prishtina, Kosovo', 'Tokyo, Japan', '2026-03-09 06:00:00', 500, 'photos/tokyo.webp', 1, '2026-02-01 00:00:00'),
(10, 'GF010', 'Tokyo, Japan', 'Hong Kong, China', '2026-03-10 04:00:00', 800, 'photos/hongkong.jpg', 1, '2026-02-01 00:00:00'),
(11, 'GF011', 'Tokyo, Japan', 'New York City, USA', '2026-03-11 02:00:00', 1200, 'photos/NYC.jpg', 1, '2026-02-01 00:00:00'),
(12, 'GF012', 'Tokyo, Japan', 'London, UK', '2026-03-12 00:00:00', 2000, 'photos/london.png', 1, '2026-02-01 00:00:00');

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(200) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updates_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bookings_user_id` (`user_id`),
  ADD KEY `fk_bookings_flight_id` (`flight_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_flights_created_by` (`created_by`),
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_news_created_by` (`created_by`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pages_updated_by` (`updated_by`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_flight_id` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `fk_flights_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `fk_pages_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Sample data for table `news`
--
INSERT INTO `news` (`id`, `title`, `content`, `image`, `created_by`, `created_at`) VALUES
(1, 'New Routes Announced', 'GlobalFly is excited to announce new flight routes to Europe and Asia. We are expanding our network to serve more destinations worldwide.', 'photos/news1.jpg', 1, NOW()),
(2, 'Summer Sale - 50% Off Flights', 'Book your summer vacation now! Get up to 50% off on selected flights across all regions. Limited time offer valid until June 30th.', 'photos/news2.jpg', 1, NOW()),
(3, 'Severe Weather Alert', 'Due to severe thunderstorms in the Northeast region, flights may experience delays of up to 2 hours. We apologize for any inconvenience and appreciate your patience.', 'photos/news3.jpg', 1, NOW()),
(4, 'Award for Best Customer Service', 'GlobalFly has won the 2026 Award for Best Customer Service in the aviation industry. Thank you for your continued support!', 'photos/news4.jpg', 1, NOW()),
(5, 'Safety Enhancements', 'We have implemented new safety protocols and health measures across all our flights. Your safety is our top priority.', 'photos/news5.jpg', 1, NOW()),
(6, 'Flight Cancellations - Winter Storm', 'All flights to Denver and Salt Lake City have been canceled due to an incoming winter storm. Affected passengers will receive full refunds or rebooking options.', 'photos/news6.jpg', 1, NOW()),
(7, 'Multiple Flight Delays Expected', 'Heavy fog conditions at major airports are expected to cause delays throughout the day. We recommend arriving at the airport early. Check your flight status regularly.', 'photos/news7.jpg', 1, NOW());

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
