-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2024 at 11:34 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raincloud_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `drive`
--

CREATE TABLE `drive` (
  `id` int(11) NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_path` varchar(1024) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `folder_id` int(11) NOT NULL DEFAULT 0,
  `soft_delete` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `drive`
--

INSERT INTO `drive` (`id`, `file_name`, `file_size`, `file_type`, `file_path`, `user_id`, `folder_id`, `soft_delete`, `date_created`, `date_updated`) VALUES
(3, 'catcat.jpg', 6107, 'image/jpeg', 'storage/usersStorage/1_Admin/65b036c5eb4e12.80052561_catcat.jpg', 1, 0, 0, '2024-01-23 22:59:33', '2024-01-23 22:59:33'),
(4, 'Dog-in-shelter-1-061323-5c9ca02cca3e4862afe84a33671d5c9c.jpg', 127156, 'image/jpeg', 'storage/usersStorage/1_Admin/65b036c9b739f0.31670737_Dog-in-shelter-1-061323-5c9ca02cca3e4862afe84a33671d5c9c.jpg', 1, 0, 0, '2024-01-23 22:59:37', '2024-01-23 22:59:37'),
(5, 'male-avatar-profile-picture-vector-10211761.jpg', 8335, 'image/jpeg', 'storage/usersStorage/1_Admin/Images/65b03758940b13.24417532_male-avatar-profile-picture-vector-10211761.jpg', 1, 3, 0, '2024-01-23 23:02:00', '2024-01-23 23:02:00'),
(6, 'sloth123.jpg', 16161, 'image/jpeg', 'storage/usersStorage/1_Admin/Images/65b037589bed72.50792836_sloth123.jpg', 1, 3, 0, '2024-01-23 23:02:00', '2024-01-23 23:02:00'),
(7, 'small-dog-owners-1.jpg', 244014, 'image/jpeg', 'storage/usersStorage/1_Admin/Images/65b03758a178d2.67725255_small-dog-owners-1.jpg', 1, 3, 0, '2024-01-23 23:02:00', '2024-01-23 23:02:00'),
(9, 'New Funny Animals 😂 Funniest Cats and Dogs Videos 😺🐶.mp4', 86175082, 'video/mp4', 'storage/usersStorage/1_Admin/Images/65b03ac7906169.03506698_New Funny Animals 😂 Funniest Cats and Dogs Videos 😺🐶.mp4', 1, 3, 0, '2024-01-23 23:16:39', '2024-01-23 23:16:39'),
(10, 'The FUNNIEST Pet Videos of 2023! 🤣 _ BEST Compilation.mp4', 465254325, 'video/mp4', 'storage/usersStorage/1_Admin/Images/65b03acebb94e6.32045683_The FUNNIEST Pet Videos of 2023! 🤣 _ BEST Compilation.mp4', 1, 3, 0, '2024-01-23 23:16:46', '2024-01-23 23:16:46'),
(11, 'The FUNNIEST Pet Videos of 2023! 🤣 _ BEST Compilation.mp4', 465254325, 'video/mp4', 'storage/usersStorage/1_Admin/Images/Video/65b03aee033691.10274173_The FUNNIEST Pet Videos of 2023! 🤣 _ BEST Compilation.mp4', 1, 5, 0, '2024-01-23 23:17:18', '2024-01-23 23:17:18'),
(12, 'New Funny Animals 😂 Funniest Cats and Dogs Videos 😺🐶.mp4', 86175082, 'video/mp4', 'storage/usersStorage/1_Admin/Images/Video/Image 2/65b03b05864095.21079234_New Funny Animals 😂 Funniest Cats and Dogs Videos 😺🐶.mp4', 1, 6, 0, '2024-01-23 23:17:41', '2024-01-23 23:17:41'),
(13, 'The FUNNIEST Pet Videos of 2023! 🤣 _ BEST Compilation.mp4', 465254325, 'video/mp4', 'storage/usersStorage/1_Admin/Videos/65b03bcb224c69.71973658_The FUNNIEST Pet Videos of 2023! 🤣 _ BEST Compilation.mp4', 1, 4, 0, '2024-01-23 23:20:59', '2024-01-23 23:20:59'),
(14, 'The two talking cats.mp4', 2204335, 'video/mp4', 'storage/usersStorage/1_Admin/Videos/65b03bd2c82ee4.07410346_The two talking cats.mp4', 1, 4, 0, '2024-01-23 23:21:06', '2024-01-23 23:21:06'),
(15, 'sloth123.jpg', 16161, 'image/jpeg', 'storage/usersStorage/1_Admin/Videos/65b03be3435673.83112304_sloth123.jpg', 1, 4, 0, '2024-01-23 23:21:23', '2024-01-23 23:21:23'),
(16, 'small-dog-owners-1.jpg', 244014, 'image/jpeg', 'storage/usersStorage/1_Admin/Videos/65b03be347b873.95101697_small-dog-owners-1.jpg', 1, 4, 0, '2024-01-23 23:21:23', '2024-01-23 23:21:23');

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `parent` int(11) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `soft_delete` tinyint(1) DEFAULT 0,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `name`, `parent`, `user_id`, `soft_delete`, `date_created`, `date_updated`) VALUES
(3, 'Images', 0, 1, 0, '2024-01-23 22:28:12', '2024-01-23 22:28:12'),
(4, 'Videos', 0, 1, 0, '2024-01-23 22:28:19', '2024-01-23 22:28:19'),
(5, 'Video', 3, 1, 0, '2024-01-23 23:17:04', '2024-01-23 23:17:04'),
(6, 'Image 2', 5, 1, 0, '2024-01-23 23:17:30', '2024-01-23 23:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `date_created`, `date_updated`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$Xqk20nyj86n/NcdwLKXb7uqXN5h379QcyeSvZw/7pIQFoDXURY.Ny', '2024-01-23 22:08:23', '2024-01-23 22:08:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drive`
--
ALTER TABLE `drive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_name` (`file_name`,`user_id`,`folder_id`,`soft_delete`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`,`user_id`,`soft_delete`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drive`
--
ALTER TABLE `drive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
