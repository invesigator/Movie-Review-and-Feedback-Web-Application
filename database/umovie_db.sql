-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2024 at 02:53 PM
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
-- Database: `umovie_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments_ratings`
--

CREATE TABLE `comments_ratings` (
  `feedback_id` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `date_posted` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments_ratings`
--

INSERT INTO `comments_ratings` (`feedback_id`, `movie_id`, `user_id`, `comment`, `rating`, `date_posted`) VALUES
(3, 2, 9, 'Nice movie', 5, '2024-08-14 15:24:58'),
(7, 1, 9, 'Good', 5, '2024-08-14 15:33:36'),
(12, 2, 9, 'Nice', 5, '2024-08-15 12:42:12'),
(13, 7, 9, 'Good', 5, '2024-08-15 12:42:23'),
(14, 2, 12, 'Great movie', 4, '2024-08-19 20:41:29'),
(15, 4, 15, 'Laugh die me!', 5, '2024-08-19 20:51:49');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `director` varchar(255) DEFAULT NULL,
  `classification` varchar(50) DEFAULT NULL,
  `poster_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `genre`, `release_date`, `director`, `classification`, `poster_url`) VALUES
(1, 'Despicable Me 4', 'Animation', '2024-07-04', 'Chris Renaud', 'P12', '../images/describeme4.png'),
(2, 'Inside Out 2', 'Animation', '2024-06-13', 'Kelsey Mann', 'P12', '../images/insideout.webp'),
(3, 'Bad Newz', 'Comedy', '2024-07-19', 'Anand Tiwari', 'P16', '../images/BadNewz.png'),
(4, 'Deadpool & Wolverine', 'Action', '2024-07-25', 'Shawn Levy', 'P18', '../images/deadpool.png'),
(5, 'Detective Conan: The Million-Dollar Pentagram', 'Animation', '2024-07-11', 'Minami Takayama', 'P12', '../images/DetectiveConan.png'),
(6, 'Jurnal Risa', 'Documentary', '2024-07-18', 'Rizal Mantovani', 'P18', '../images/JurnalRisa.png'),
(7, 'Customs Frontline', 'Action', '2024-07-05', 'Director 7', 'P16', '../images/CustomsFrontline.png'),
(8, 'Indian 2', 'Action', '2024-07-12', 'S. Shankar', 'P16', '../images/Indian2.png'),
(9, '(MIFF24) Muallaf', 'Drama', '2024-07-22', 'Yasmin Ahmad', 'P13', '../images/MIFF24Muallaf.png'),
(10, '(MIFF24) Betania', 'Genre 8', '2024-07-22', 'Marcelo Botta', 'P12', '../images/MIFF24Betania.png'),
(12, 'Alien: Romulus', 'Horror', '2024-08-15', 'Federico Alvarez', 'P16', '../images/AlienRomulus_big.webp'),
(13, 'Bocchi The Rock! Recap Part 1', 'Animation', '2024-08-08', 'Keiichiro Saito', 'P12', '../images/BOCCHITHEROCKRecapPart1_big.webp');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `password`, `role`, `create_at`, `profile_pic`) VALUES
(1, 'c', 'cl', 'ccl@gmail.com', '0161234567', 'No.17 Jalan Besar', '$2y$10$dnawjbU7FbmHLi176CeeVeLPyhZ5dwl9CBWy1uxC2Jgg2mTMQWJb6', 'user', '2024-07-24 15:22:06', '../uploads/66af793ddea98.png'),
(3, 'test', '456', 'test@gmail.com', '0121234567', 'No 99 Jalan Kecil', '$2y$10$r23QKH7yoUf/FsnT0yo7COWRUcGdybxGE4qQNwqFJxwLgGmBAdB3C', 'user', '2024-08-04 04:59:52', '../uploads/66af7d1dc2759.jfif'),
(5, 'test', '2', 'test2@gmail.com', '0191234567', 'AAAAAAAAAAA', '$2y$10$P2pB2ndOZujX9E8u3Jq0ve0R3YSqctJ4Mlz1Y6SUvcoApyFqL8tG2', 'user', '2024-08-05 03:19:48', '../uploads/default-avatar.png'),
(6, 'admin', '1', 'admin1@gmail.com', '0191234567', 'AAAAAAAAAAA', '$2y$10$P2pB2ndOZujX9E8u3Jq0ve0R3YSqctJ4Mlz1Y6SUvcoApyFqL8tG2', 'admin', '2024-08-05 03:19:48', '../uploads/default-avatar.png'),
(7, 'chow', 'mun kent', 'munkent1030@1utar.my', '0125192230', '18 jalan 6A/2', '$2y$10$RtN1c8w1Zw6xVW/4MO/tj.msIyxiyRLhWjIVWxTCkP/yAL9.tTz0S', 'user', '2024-08-07 21:18:29', '../uploads/default-avatar.png'),
(8, '567', '567', 'munkent1031@1utar.my', '0125192230', '123456', '$2y$10$hmUiitDL8d.pyhoinMqnyO2Os7bevrQll.qyYWwU9BbNLfkkS91DG', 'user', '2024-08-08 13:20:42', '../uploads/default-avatar.png'),
(9, 'hello', 'y', 'hello@gmail.com', '0123456789', 'No100, xxx', '$2y$10$3SXj21ct.5cXnH2mZ8q79OMeIVC9s5twVLoPMdq2OyrENJHD/ZTBW', 'user', '2024-08-14 07:20:25', '../uploads/default-avatar.png'),
(10, 'admin', '2', 'admin2@gmail.com', '0123456777', 'No 999, xxx', '$2y$10$Pp/GySSuYajcOz27cbHjeeG/46Vu7VH/Pd./AbEaWDl8o.Ah3xEHS', 'admin', '2024-08-14 07:49:45', '../uploads/66c33929205a7.png'),
(11, 'bye', 'bye', 'bye@gmail.com', '0123446678', 'No 98, xxx', '$2y$10$2do8grRjlnm//Nu8ZZM7p.4BeNH9n3Fedg/IXvVFwjQ84Q17ssh3S', 'user', '2024-08-15 06:39:19', '../uploads/default-avatar.png'),
(12, 'Peter', 'Parker', 'petter@gmail.com', '0143456789', '15th Street, Queens, New York City, New York', '$2y$10$1luwy1zhQ9jPeIdJImF.s.Ock4W4/fXxDtrbEdHVh6nupBwXIL4KG', 'user', '2024-08-19 14:33:33', '../uploads/66c33b9d1eb45.jpeg'),
(14, 'a2', '22', 'a22@gmail.com', '0123488889', 'No 10, xxxxxxxxx', '$2y$10$8xugPneU0wSjqAoEnCORje2MW.ZbGk7bif0VHlbRsj/HTmsCp865O', 'user', '2024-08-19 14:45:43', '../uploads/default-avatar.png'),
(15, 'Mary', 'Jie', 'mary@gmail.com', '0123433789', 'No 2, XXXX', '$2y$10$z8at8ZHHBuoxWHPqO4YAp.ng0lBYt6Jk6FfHDiq8sOkzIdr.TbNoS', 'user', '2024-08-19 14:51:07', '../uploads/66c340266c04b.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments_ratings`
--
ALTER TABLE `comments_ratings`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

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
-- AUTO_INCREMENT for table `comments_ratings`
--
ALTER TABLE `comments_ratings`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments_ratings`
--
ALTER TABLE `comments_ratings`
  ADD CONSTRAINT `comments_ratings_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
