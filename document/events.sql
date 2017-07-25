-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2017 at 05:06 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_registration`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` bigint(15) NOT NULL,
  `user_id` int(15) NOT NULL,
  `event_title` varchar(100) NOT NULL,
  `event_description` text NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_image` varchar(100) NOT NULL,
  `event_contact` bigint(15) NOT NULL,
  `event_starttime` datetime NOT NULL,
  `event_endtime` datetime NOT NULL,
  `event_status` tinyint(1) NOT NULL DEFAULT '1',
  `event_created` datetime NOT NULL,
  `event_modified` datetime NOT NULL,
  `event_venue` varchar(100) DEFAULT NULL,
  `event_address` varchar(100) DEFAULT NULL,
  `event_city` varchar(50) DEFAULT NULL,
  `event_state` varchar(50) DEFAULT NULL,
  `event_zipcode` bigint(20) DEFAULT NULL,
  `event_privacy` varchar(10) DEFAULT 'Public'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `user_id`, `event_title`, `event_description`, `event_type`, `event_image`, `event_contact`, `event_starttime`, `event_endtime`, `event_status`, `event_created`, `event_modified`, `event_venue`, `event_address`, `event_city`, `event_state`, `event_zipcode`, `event_privacy`) VALUES
(1, 0, 'Birthday Celebration', 'Celebrating birthday for Vinodkumar', 'Birthday', 'photo3.jpg', 9894946054, '2017-07-13 00:00:16', '2017-07-13 10:00:33', 0, '2017-07-12 17:37:51', '2017-07-12 17:37:51', NULL, NULL, NULL, NULL, NULL, 'Public'),
(2, 0, 'Birthday', '21st birthday celebrations', 'Birthday', 'HeroHonda-Bike-RC-Book-2.jpg', 98949460564, '2017-07-20 22:36:54', '2017-07-21 01:00:58', 0, '2017-07-20 17:07:16', '2017-07-20 17:07:16', NULL, NULL, NULL, NULL, NULL, 'Public'),
(3, 0, 'asdf', 'asdfasd', '2', 'Truck-Towing-51.jpg', 0, '2017-07-25 00:00:57', '2017-07-25 01:00:59', 1, '2017-07-24 18:42:27', '2017-07-24 18:42:27', NULL, 'This is Online event', '', '', 0, 'Public'),
(4, 0, 'asd', 'asdfas', '15', 'ace-banner-03.jpg', 0, '2017-07-25 02:00:19', '2017-07-25 03:00:22', 1, '2017-07-24 18:43:28', '2017-07-24 18:43:28', NULL, 'This is Online event', '', '', 0, 'Private'),
(5, 0, 'asdf', 'asdf ', '16', 'Photo1962_20131201T182426-100.jpg', 0, '2017-07-25 01:00:00', '2017-07-25 04:00:02', 1, '2017-07-24 18:44:12', '2017-07-24 18:44:12', NULL, 'This is Online event', '', '', 0, 'Public'),
(6, 0, 'Test event', 'Test eventTest eventTest eventTest event', '7', 'IMG_20161220_1600381.jpg', 0, '2017-09-25 14:00:40', '2017-07-25 16:00:43', 1, '2017-07-25 07:48:38', '2017-07-25 07:48:38', '', '', '', '', 0, 'Public');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` bigint(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
