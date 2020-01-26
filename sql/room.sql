-- phpMyAdmin SQL Dump
-- version 3.3.7deb2build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 24, 2011 at 08:48 AM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `drainware`
--

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE IF NOT EXISTS `room` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `rangea` int(10) unsigned NOT NULL,
  `rangeb` int(10) unsigned NOT NULL,
  `desc` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `name`, `rangea`, `rangeb`, `desc`, `status`) VALUES
(2, 'Ejemplo', 3232241431, 3232241432, 'Esto es una sala de ejemplo', 0);
