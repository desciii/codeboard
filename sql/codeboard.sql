-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 08:31 PM
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
-- Database: `codeboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `username`, `content`, `created_at`) VALUES
(2, 2, 'sigma', 'bruh', '2025-06-22 14:38:14'),
(3, 3, 'justin nabunturan', 'gulp', '2025-06-22 14:44:56'),
(4, 1, 's0lanin', 'who liked ts(this shit)', '2025-06-22 14:46:28'),
(5, 1, 'justin nabunturan', 'gulp', '2025-06-22 14:46:41'),
(6, 1, 'sigma', 'yo', '2025-06-22 14:47:25'),
(7, 1, 'sigma', 'gurt', '2025-06-22 14:47:29'),
(9, 1, 's0lanin', 'lmao', '2025-06-22 15:54:26'),
(11, 3, 's0lanin', 'gulp', '2025-06-22 17:51:19'),
(12, 6, 's0lanin', 'miho nomames', '2025-06-22 17:51:41'),
(13, 7, 's0lanin', 'this mad goofy bruh', '2025-06-22 18:29:27'),
(14, 7, 'justin nabunturan', 'ah hell nah bruh', '2025-06-22 18:29:59'),
(15, 7, 'sigma', 'ako moskov user', '2025-06-22 18:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `username`, `created_at`) VALUES
(3, 2, 'sigma', '2025-06-22 14:26:24'),
(4, 2, 'justin nabunturan', '2025-06-22 14:26:30'),
(5, 1, 'sigma', '2025-06-22 14:46:05'),
(6, 3, 'justin nabunturan', '2025-06-22 14:46:45'),
(9, 6, 's0lanin', '2025-06-22 17:51:33'),
(10, 7, 's0lanin', '2025-06-22 18:29:19'),
(11, 1, 's0lanin', '2025-06-22 18:29:37'),
(12, 7, 'sigma', '2025-06-22 18:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `language` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `username`, `title`, `content`, `language`, `created_at`) VALUES
(1, 's0lanin', 'Is this sigma?', '<!DOCTYPE html>\r\n<html lang=\"en\">\r\n  <head>\r\n    <meta charset=\"UTF-8\" />\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\r\n    <title>Angelo | About Me</title>\r\n    <script src=\"https://cdn.tailwindcss.com\"></script>\r\n    <script>\r\n      tailwind.config = {\r\n        theme: {\r\n          extend: {\r\n            animation: {\r\n              fadeInUp: \"fadeInUp 1s ease-out both\",\r\n              slideInLeft: \"slideInLeft 1s ease-out both\",\r\n              zoomIn: \"zoomIn 0.6s ease-out both\",\r\n            },\r\n            keyframes: {\r\n              fadeInUp: {\r\n                from: { opacity: \"0\", transform: \"translateY(30px)\" },\r\n                to: { opacity: \"1\", transform: \"translateY(0)\" },\r\n              },\r\n              slideInLeft: {\r\n                from: { opacity: \"0\", transform: \"translateX(-40px)\" },\r\n                to: { opacity: \"1\", transform: \"translateX(0)\" },\r\n              },\r\n              zoomIn: {\r\n                from: { opacity: \"0\", transform: \"scale(0.9)\" },\r\n                to: { opacity: \"1\", transform: \"scale(1)\" },\r\n              },\r\n            },\r\n          },\r\n        },\r\n      };\r\n    </script>\r\n  </head>\r\n  <body class=\"bg-black text-white font-sans\">\r\n    <!-- HERO SECTION -->\r\n    <section\r\n      class=\"min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 to-black px-6\"\r\n    >\r\n      <div class=\"max-w-6xl w-full text-center space-y-8 animate-fadeInUp\">\r\n        <h1 class=\"text-6xl font-extrabold leading-tight\">\r\n          Yo, I\'m <span class=\"text-blue-400\">Angelo</span>\r\n        </h1>\r\n        <p class=\"text-lg text-gray-300 max-w-xl mx-auto\">\r\n          A 19-year-old code wrangler, design enjoyer, and tech explorer. I make\r\n          stuff look clean and work fast.\r\n        </p>\r\n        <div class=\"flex justify-center gap-6 mt-8\">\r\n          <a\r\n            href=\"#\"\r\n            class=\"bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-xl text-lg font-semibold transition duration-300\"\r\n            >See Projects</a\r\n          >\r\n          <a\r\n            href=\"#\"\r\n            class=\"text-blue-300 underline hover:text-blue-400 text-lg transition\"\r\n            >Hit Me Up</a\r\n          >\r\n        </div>\r\n      </div>\r\n    </section>\r\n\r\n    <!-- PROFILE SPLIT SECTION -->\r\n    <section\r\n      class=\"min-h-screen grid md:grid-cols-2 gap-10 items-center px-10 py-20 bg-gray-900 animate-slideInLeft\"\r\n    >\r\n      <!-- Image -->\r\n      <div class=\"flex justify-center\">\r\n        <img\r\n          src=\"images/profile.png\"\r\n          alt=\"Angelo\"\r\n          class=\"w-80 h-80 object-cover rounded-3xl shadow-2xl border-4 border-blue-500 hover:scale-105 transition-transform duration-500\"\r\n        />\r\n      </div>\r\n\r\n      <!-- Text -->\r\n      <div class=\"space-y-6\">\r\n        <h2 class=\"text-4xl font-bold\">About Me</h2>\r\n        <p class=\"text-gray-300 leading-relaxed\">\r\n          Currently grinding through BSIT as a 2nd year student. I’m all about\r\n          making responsive, interactive, and beautiful interfaces — mixing art\r\n          and code, that’s the vibe. Outside screens? Catch me binging films or\r\n          planning my next big thing.\r\n        </p>\r\n        <div class=\"flex gap-4\">\r\n          <a\r\n            href=\"#\"\r\n            class=\"bg-white text-black px-5 py-2 rounded-lg font-medium hover:bg-blue-400 hover:text-white transition-all\"\r\n            >View Resume</a\r\n          >\r\n        </div>\r\n      </div>\r\n    </section>\r\n\r\n    <!-- SOCIAL FOOTER -->\r\n    <footer\r\n      class=\"bg-black py-10 flex flex-col md:flex-row justify-between items-center px-10 border-t border-gray-700\"\r\n    >\r\n      <p class=\"text-sm text-gray-500\">\r\n        &copy; 2025 Angelo. All rights reserved.\r\n      </p>\r\n      <div class=\"flex gap-5 mt-4 md:mt-0\">\r\n        <a href=\"#\"\r\n          ><img src=\"images/fb.png\" class=\"w-6 hover:scale-125 transition\"\r\n        /></a>\r\n        <a href=\"#\"\r\n          ><img src=\"images/insta.png\" class=\"w-6 hover:scale-125 transition\"\r\n        /></a>\r\n        <a href=\"#\"\r\n          ><img src=\"images/x.png\" class=\"w-6 hover:scale-125 transition\"\r\n        /></a>\r\n      </div>\r\n    </footer>\r\n  </body>\r\n</html>\r\n', 'HTML', '2025-06-22 21:47:01'),
(2, 'sigma', 'Am i Sigma?', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n<head>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n<title>Untitled Document</title>\r\n<style>\r\n</style>\r\n</head>\r\n<body>\r\n<div id=\"container\">\r\n	<div id=\"header\">\r\n    	   	<h1>Green Dream</h1>\r\n            <img src=\"images/logo.jpg\" />\r\n           	<ul>\r\n            	<li id=\"l1\"><a href=\"#\" id=\"home\"> Home </a></li>\r\n                <li><a href=\"#\"> Portfolio </a></li>\r\n                <li><a href=\"#\"> Blog </a></li>\r\n                <li><a href=\"#\"> About </a></li>\r\n                <li><a href=\"#\"> Contact </a></li>\r\n            </ul>\r\n    </div>\r\n        \r\n    <div id=\"banner\">\r\n    	     <img src=\"images/img1.jpg\"/>\r\n    </div>\r\n        \r\n    <div id=\"post\">\r\n    	  <h2>From the blog</h2>\r\n          <img src=\"images/blog01.jpg\" />\r\n          <h3>first post</h3>\r\n          <p>o Lorem ipsum dolor set amet del paniar folascar mi poibani a el tiempo dela fourtinq Lorem ipsum dolor tiempo dela fourtinq Lorem ipsum dolor</p>\r\n          \r\n          <img src=\"images/blog02.jpg\" />\r\n          <h3>second post</h3>\r\n          <p>o Lorem ipsum dolor set amet del paniar folascar mi poibani a el tiempo dela fourtinq Lorem ipsum dolor tiempo dela fourtinq Lorem ipsum dolor</p>\r\n        \r\n          <h2>From the portfolio</h2>\r\n          <img src=\"images/port02.jpg\" />\r\n 		  <p> o Lorem ipsum dolor set amet del <br /> paniar folascar mi poibani a el</p>\r\n          <img src=\"images/port03.jpg\"/><br />\r\n          <p> o Lorem ipsum dolor set amet del <br />paniar folascar mi poibani a el</p>\r\n          \r\n    </div>\r\n\r\n    <div id=\"footer\">\r\n    	<p><a href=\"\"> About Green Dream </a></p>\r\n        <p><a href=\"\"> Latest Tweets </a></p>\r\n        <p><a href=\"\"> Newsletter </a></p>\r\n        <p><a href=\"\"> Contact us </a></p>\r\n    </div>\r\n</div>\r\n</body>\r\n</html>\r\n', 'HTML', '2025-06-22 21:55:03'),
(3, 'justin nabunturan', 'Omsim Barabida', 'Gulp', 'HTML', '2025-06-22 22:08:20'),
(6, 's0lanin', 'yo', 'gurt', 'Python', '2025-06-23 01:51:32'),
(7, 'justin nabunturan', 'what do we think chat?', '<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Input</title>\r\n    <style>\r\n        body {\r\n            font-size: 30px;\r\n            color: grey;\r\n            font-style: bolder;\r\n\r\n        }\r\n\r\n    </style>\r\n</head>\r\n<body>\r\n\r\n<form method=\'post\'>\r\nUser Input: <Input type=\"number\" name=\"num\"/> <br>\r\n<input type=\"submit\" name=\"submit\" Value=\"Confirm\"/>\r\n</form>\r\n\r\n<?php\r\n$num = $_POST[\'num\'];\r\n$count = 0;\r\n\r\nwhile($num != 1) {\r\n    if ($num % 2 == 0) {\r\n        $num /= 2;\r\n    } \r\n    else { \r\n        $num = $num * 3 + 1;      \r\n    }\r\n    echo \"Next Value: $num <br>\";   \r\n    $count++;\r\n}\r\necho \"count: $count\";\r\n?>\r\n\r\n</body>\r\n</html>', 'CSS', '2025-06-23 02:29:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Student','Developer','Teacher') NOT NULL,
  `dob` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `status`, `dob`, `created_at`, `profile_picture`) VALUES
(3, 'sigma', '$2y$10$AJkC8FGM0vf9/xyBCUOdMeVKUZSf/2SyGH8/nRA3Hh34UmCw2Qmqq', 'Student', '2025-06-01', '2025-06-22 13:18:26', '1293448.jpg'),
(5, 's0lanin', '$2y$10$wOcsciDoN0du8MKPThUrjO98/VzGHY9./PWj60m2saKTBwMGJUzo.', 'Student', '2025-06-02', '2025-06-22 13:37:13', 'download (3).jpg'),
(6, 'justin nabunturan', '$2y$10$h1x8XslVGpPDZajsdyF0ce5fPWXAfKDOCYOyiruxwt72IUwEd9vmK', 'Student', '2025-06-03', '2025-06-22 14:02:19', 'download.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
