-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2013 at 08:57 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `changedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `difference`
--

CREATE TABLE IF NOT EXISTS `difference` (
  `id_change` int(10) NOT NULL AUTO_INCREMENT,
  `date_change` date NOT NULL,
  `table_name` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `field_name` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `field_type` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `field_null` varchar(8) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `field_key` varchar(8) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `field_default` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `field_extra` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_change`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `difference`
--

INSERT INTO `difference` (`id_change`, `date_change`, `table_name`, `field_name`, `field_type`, `field_null`, `field_key`, `field_default`, `field_extra`) VALUES
(1, '2013-04-22', 'test', 'id', 'int(5)', 'NO', 'PRI', '', 'auto_increment'),
(2, '2013-04-22', 'test', 'name', 'varchar(20)', 'NO', '', '', ''),
(3, '2013-04-23', 'test', 'idl', 'int(5)', 'NO', 'PRI', '', 'auto_increment'),
(4, '2013-04-23', 'test', 'name', 'varchar(20)', 'NO', '', '', ''),
(5, '2013-04-23', 'test2', 'id', 'int(5)', 'NO', 'PRI', '', ''),
(6, '2013-04-23', 'test2', 'passwort', 'varchar(20)', 'NO', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
