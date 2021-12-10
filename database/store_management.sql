-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 05, 2020 at 10:46 AM
-- Server version: 5.7.24
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `product_master`
--

DROP TABLE IF EXISTS `product_master`;
CREATE TABLE IF NOT EXISTS `product_master` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` text NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_discount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

DROP TABLE IF EXISTS `registration`;
CREATE TABLE IF NOT EXISTS `registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Store management';

-- --------------------------------------------------------

--
-- Table structure for table `sales_details`
--

DROP TABLE IF EXISTS `sales_details`;
CREATE TABLE IF NOT EXISTS `sales_details` (
  `sales_details_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` text NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_discount` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `sales_master_id` int(11) NOT NULL,
  PRIMARY KEY (`sales_details_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sales_master`
--

DROP TABLE IF EXISTS `sales_master`;
CREATE TABLE IF NOT EXISTS `sales_master` (
  `sales_master_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` text NOT NULL,
  `grand_amount` int(11) NOT NULL,
  `grand_discount` int(11) NOT NULL,
  `sales_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`sales_master_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
