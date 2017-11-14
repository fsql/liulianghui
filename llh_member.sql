-- phpMyAdmin SQL Dump
-- version 4.4.15.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-11-14 10:07:39
-- 服务器版本： 5.5.48-log
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fsql`
--

-- --------------------------------------------------------

--
-- 表的结构 `llh_member`
--

CREATE TABLE IF NOT EXISTS `llh_member` (
  `uid` int(64) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `addtime` date NOT NULL,
  `credit` varchar(32) NOT NULL,
  `jsessionid` varchar(64) NOT NULL,
  `cookie` varchar(1024) NOT NULL,
  `last` date NOT NULL DEFAULT '1111-11-11',
  `error` int(1) NOT NULL DEFAULT '0',
  `share_switch` int(1) NOT NULL DEFAULT '0',
  `share_last` date NOT NULL DEFAULT '1111-11-11',
  `share_getlast` date NOT NULL DEFAULT '1111-11-11',
  `share_message` varchar(32) NOT NULL,
  `puzzle_last` date NOT NULL DEFAULT '1111-11-11',
  `puzzle_message` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `llh_member`
--
ALTER TABLE `llh_member`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `llh_member`
--
ALTER TABLE `llh_member`
  MODIFY `uid` int(64) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
