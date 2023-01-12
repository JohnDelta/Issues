-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Server IP: 127.0.0.1:3306
-- Date of creation: 01 Νοε 2022 στις 09:42:01
-- Server Version: 5.7.36
-- Version of PHP: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `issuesdb`
--

-- --------------------------------------------------------

--
-- Table structure `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Data for table `departments`
--

INSERT INTO `departments` (`department_id`, `value`, `is_default`) VALUES
(1, 'Human Resources', 1),
(2, 'Sales', 1),
(3, 'Finance', 1),
(4, 'Marketing', 0);

-- --------------------------------------------------------

--
-- Table structure `issues`
--

DROP TABLE IF EXISTS `issues`;
CREATE TABLE IF NOT EXISTS `issues` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `full_name` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `department_id` int(11) NOT NULL,
  `office` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `issue_type_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`issue_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure `issue_types`
--

DROP TABLE IF EXISTS `issue_types`;
CREATE TABLE IF NOT EXISTS `issue_types` (
  `issue_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`issue_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Data for table `issue_types`
--

INSERT INTO `issue_types` (`issue_type_id`, `value`, `is_default`) VALUES
(1, 'Network', 0),
(2, 'Internet/WiFi', 0),
(3, 'Printers/Scanners', 1),
(4, 'Operating Systems', 0),
(5, 'PC', 0),
(9, 'Phones', 0),
(7, 'Other', 0);

-- --------------------------------------------------------

--
-- Table structure `priorities`
--

DROP TABLE IF EXISTS `priorities`;
CREATE TABLE IF NOT EXISTS `priorities` (
  `priority_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`priority_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Data for table `priorities`
--

INSERT INTO `priorities` (`priority_id`, `value`, `is_default`) VALUES
(1, 'Low', 1),
(2, 'Medium', 0),
(3, 'High', 0);

-- --------------------------------------------------------

--
-- Table structure `states`
--

DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`state_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Data for table `states`
--

INSERT INTO `states` (`state_id`, `value`, `is_default`) VALUES
(1, 'In progress', 0),
(2, 'Completed', 0),
(3, 'New', 1);

-- --------------------------------------------------------

--
-- Table structure `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `is_admin`) VALUES
(1, 'admin', ']znae.zvMPdyIgUX', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
