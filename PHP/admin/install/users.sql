-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: 10.8.11.195
-- Generation Time: Jun 30, 2009 at 02:40 PM
-- Server version: 5.0.67
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `LibDBDatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `ldb_admins`
--

DROP TABLE IF EXISTS `ldb_admins`;
CREATE TABLE `ldb_admins` (
  `uid` mediumint(8) NOT NULL,
  `hiredate` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_admins`
--

INSERT INTO `ldb_admins` VALUES(4, 1246394321);
INSERT INTO `ldb_admins` VALUES(1, 1246393073);

-- --------------------------------------------------------

--
-- Table structure for table `ldb_customers`
--

DROP TABLE IF EXISTS `ldb_customers`;
CREATE TABLE `ldb_customers` (
  `uid` mediumint(8) NOT NULL auto_increment,
  `username` varchar(16) NOT NULL,
  `password` varchar(57) NOT NULL,
  `email` varchar(96) NOT NULL,
  `regtime` int(11) unsigned NOT NULL,
  `lastvisit` int(11) unsigned NOT NULL,
  `firstname` varchar(40) NOT NULL,
  `lastname` varchar(40) NOT NULL,
  `birthdate` int(11) NOT NULL,
  `gender` char(1) default NULL,
  `addrline1` varchar(80) default NULL,
  `addrline2` varchar(80) default NULL,
  `city` varchar(40) default NULL,
  `state` varchar(2) default NULL,
  `zip` varchar(10) default NULL,
  `phone` varchar(26) default NULL,
  PRIMARY KEY  (`uid`),
  KEY `username` (`username`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ldb_customers`
--

INSERT INTO `ldb_customers` VALUES(1, 'NicFord1', '83792f8769cd033cd759216515683ff3c3d909269268a8cf318ba0437', 'Nicholas+LibDBDatabase@Nicks-Net.us', 1246393073, 1246394383, 'Nicholas', 'Ford', 499496400, 'M', '8131 Silo CT', '', 'Severn', 'MD', '21144-2304', '(410) 419-5997');
INSERT INTO `ldb_customers` VALUES(2, 'CrysMF4', '5ab8ff537fe9f4f27c1c2111c723c995b0df22c505d97ea84e3f6c2f4', 'Nicholas+CrysMF4LDB@Nicks-Net.us', 1246393636, 1246394280, 'Crystal', 'Freeman', 523684800, 'F', '', '', '', '', '', '');
INSERT INTO `ldb_customers` VALUES(3, 'dteller', '1579d972c7dc81e550540ab750a7260a9c1990db99bf294e8b1684d40', 'Nicholas+LDB@Nicks-Net.us', 1246393844, 1246394287, 'Demo', 'Teller', 899179200, 'M', '', '', '', '', '', '');
INSERT INTO `ldb_customers` VALUES(4, 'dadmin', 'ae96e5183deb3238fe3dedb27bafd99a9aab2055c8201d9599fe474a3', 'Nicholas+ldb@Nicks-Net.us', 1246394321, 0, '', '', 899179200, 'M', '', '', '', '', '', '');
INSERT INTO `ldb_customers` VALUES(5, 'dcust', '7d7b9aa4019ad2c32a5089f1b48cdc60482addee218a0ed5c4b12f5c2', 'Nicholas+ldb@Nicks-Net.us', 1246394381, 0, '', '', 904449600, 'M', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `ldb_tellers`
--

DROP TABLE IF EXISTS `ldb_tellers`;
CREATE TABLE `ldb_tellers` (
  `uid` mediumint(8) NOT NULL COMMENT 'id of teller user',
  `hiredate` int(11) unsigned NOT NULL COMMENT 'unix timestamp',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_tellers`
--

INSERT INTO `ldb_tellers` VALUES(3, 1246393844);
