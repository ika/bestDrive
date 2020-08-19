-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:80
-- Generation Time: Aug 19, 2020 at 09:02 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tireDB`
--
CREATE DATABASE IF NOT EXISTS `tireDB` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tireDB`;

-- --------------------------------------------------------

--
-- Table structure for table `db_config`
--

CREATE TABLE `db_config` (
  `cn_id` varchar(8) NOT NULL,
  `cn_markup` varchar(8) NOT NULL,
  `cn_version` varchar(16) NOT NULL,
  `cn_software` varchar(32) NOT NULL,
  `cn_domain` varchar(32) NOT NULL,
  `cn_tzone` varchar(32) NOT NULL,
  `cn_upload` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `db_hydrolics`
--

CREATE TABLE `db_hydrolics` (
  `hy_id` int(11) NOT NULL,
  `hy_partid` varchar(32) NOT NULL,
  `hy_name` varchar(32) NOT NULL,
  `hy_date` varchar(32) NOT NULL,
  `hy_size` varchar(32) NOT NULL,
  `hy_cost` varchar(32) NOT NULL,
  `hy_onhand` varchar(32) NOT NULL,
  `hy_descr` varchar(256) NOT NULL,
  `hy_uid` varchar(32) NOT NULL,
  `hy_time` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='spares';

-- --------------------------------------------------------

--
-- Table structure for table `db_modules`
--

CREATE TABLE `db_modules` (
  `mo_wkspace` varchar(32) NOT NULL,
  `mo_modname` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MultiDB modules';

-- --------------------------------------------------------

--
-- Table structure for table `db_placements`
--

CREATE TABLE `db_placements` (
  `pl_id` int(11) NOT NULL,
  `pl_name` varchar(32) NOT NULL,
  `pl_descr` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `db_segments`
--

CREATE TABLE `db_segments` (
  `ca_id` int(11) NOT NULL,
  `ca_name` varchar(32) NOT NULL,
  `ca_descr` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `db_spares`
--

CREATE TABLE `db_spares` (
  `sp_id` int(11) NOT NULL,
  `sp_partid` varchar(32) NOT NULL,
  `sp_partno` varchar(32) NOT NULL,
  `sp_suppinv` varchar(32) NOT NULL,
  `sp_datein` varchar(32) NOT NULL,
  `sp_jobcard` varchar(32) NOT NULL,
  `sp_cost` varchar(32) NOT NULL,
  `sp_onhand` varchar(32) NOT NULL,
  `sp_descr` varchar(256) NOT NULL,
  `sp_uid` varchar(32) NOT NULL,
  `sp_time` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='spares';

-- --------------------------------------------------------

--
-- Table structure for table `db_tires`
--

CREATE TABLE `db_tires` (
  `ty_id` int(11) UNSIGNED NOT NULL,
  `ty_seg` varchar(16) DEFAULT NULL,
  `ty_place` varchar(32) DEFAULT 'UND',
  `ty_brand` varchar(32) DEFAULT NULL,
  `ty_inch` varchar(16) DEFAULT NULL,
  `ty_size` varchar(32) DEFAULT NULL,
  `ty_li` varchar(16) DEFAULT NULL,
  `ty_si` varchar(16) DEFAULT NULL,
  `ty_design` varchar(32) DEFAULT NULL,
  `ty_article` varchar(32) DEFAULT NULL,
  `ty_descr` varchar(256) DEFAULT NULL,
  `ty_ssr` varchar(16) DEFAULT NULL,
  `ty_net` varchar(32) NOT NULL DEFAULT '0',
  `ty_onhand` varchar(32) NOT NULL DEFAULT '0',
  `ty_time` varchar(32) NOT NULL,
  `ty_uid` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MultiDB tyres';

-- --------------------------------------------------------

--
-- Table structure for table `db_users`
--

CREATE TABLE `db_users` (
  `us_id` int(11) UNSIGNED NOT NULL,
  `us_name` varchar(32) DEFAULT NULL,
  `us_surname` varchar(32) DEFAULT NULL,
  `us_pass` varchar(32) DEFAULT NULL,
  `us_email` varchar(64) DEFAULT NULL,
  `us_tel` varchar(32) DEFAULT NULL,
  `us_active` enum('yes','no','del') NOT NULL DEFAULT 'yes',
  `us_time` varchar(32) DEFAULT NULL,
  `us_notes` varchar(512) DEFAULT NULL,
  `us_start` varchar(16) NOT NULL,
  `us_uid` varchar(32) DEFAULT NULL,
  `us_wkspace` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MultiDB users';

-- --------------------------------------------------------

--
-- Table structure for table `db_wkspaces`
--

CREATE TABLE `db_wkspaces` (
  `ws_id` int(11) UNSIGNED NOT NULL,
  `ws_title` varchar(32) NOT NULL,
  `ws_wkspace` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MultiDB workspaces';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `db_config`
--
ALTER TABLE `db_config`
  ADD PRIMARY KEY (`cn_id`);

--
-- Indexes for table `db_hydrolics`
--
ALTER TABLE `db_hydrolics`
  ADD PRIMARY KEY (`hy_id`);

--
-- Indexes for table `db_placements`
--
ALTER TABLE `db_placements`
  ADD PRIMARY KEY (`pl_id`);

--
-- Indexes for table `db_segments`
--
ALTER TABLE `db_segments`
  ADD PRIMARY KEY (`ca_id`);

--
-- Indexes for table `db_spares`
--
ALTER TABLE `db_spares`
  ADD PRIMARY KEY (`sp_id`);

--
-- Indexes for table `db_tires`
--
ALTER TABLE `db_tires`
  ADD PRIMARY KEY (`ty_id`);

--
-- Indexes for table `db_users`
--
ALTER TABLE `db_users`
  ADD PRIMARY KEY (`us_id`);

--
-- Indexes for table `db_wkspaces`
--
ALTER TABLE `db_wkspaces`
  ADD PRIMARY KEY (`ws_id`),
  ADD UNIQUE KEY `ws_wkspace` (`ws_wkspace`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `db_hydrolics`
--
ALTER TABLE `db_hydrolics`
  MODIFY `hy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_placements`
--
ALTER TABLE `db_placements`
  MODIFY `pl_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_segments`
--
ALTER TABLE `db_segments`
  MODIFY `ca_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_spares`
--
ALTER TABLE `db_spares`
  MODIFY `sp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_tires`
--
ALTER TABLE `db_tires`
  MODIFY `ty_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_users`
--
ALTER TABLE `db_users`
  MODIFY `us_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_wkspaces`
--
ALTER TABLE `db_wkspaces`
  MODIFY `ws_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
