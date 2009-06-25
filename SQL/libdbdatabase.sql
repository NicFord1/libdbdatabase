-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2009 at 12:30 AM
-- Server version: 5.1.33
-- PHP Version: 5.2.9-2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `libdbdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `ldb_activeguests`
--

CREATE TABLE IF NOT EXISTS `ldb_activeguests` (
  `ip` varchar(15) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_activeguests`
--


-- --------------------------------------------------------

--
-- Table structure for table `ldb_activeusers`
--

CREATE TABLE IF NOT EXISTS `ldb_activeusers` (
  `uid` mediumint(8) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_activeusers`
--

INSERT INTO `ldb_activeusers` (`uid`, `timestamp`) VALUES
(13, 1245882628);

-- --------------------------------------------------------

--
-- Table structure for table `ldb_admins`
--

CREATE TABLE IF NOT EXISTS `ldb_admins` (
  `uid` mediumint(8) NOT NULL,
  `hiredate` int(11) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_admins`
--


-- --------------------------------------------------------

--
-- Table structure for table `ldb_bannedusers`
--

CREATE TABLE IF NOT EXISTS `ldb_bannedusers` (
  `username` varchar(16) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_bannedusers`
--


-- --------------------------------------------------------

--
-- Table structure for table `ldb_books`
--

CREATE TABLE IF NOT EXISTS `ldb_books` (
  `itemid` mediumint(8) NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `title` varchar(160) NOT NULL,
  `author` varchar(80) NOT NULL,
  `genre` varchar(30) NOT NULL,
  `publisher` varchar(80) NOT NULL,
  `releasedate` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(512) NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `latefee` decimal(6,2) NOT NULL,
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_books`
--

INSERT INTO `ldb_books` (`itemid`, `isbn`, `title`, `author`, `genre`, `publisher`, `releasedate`, `rating`, `description`, `cost`, `latefee`) VALUES
(2, '9780316769174', 'The Catcher in the Rye', 'J.D. Salinger', 'Fiction', 'Back Bay Books', 980830800, 4, 'Since his debut in 1951 as The Catcher in the Rye, Holden Caulfield has been synonymous with "cynical adolescent." Holden narrates the story of a couple of days in his sixteen-year-old life, just after he''s been expelled from prep school, in a slang that sounds edgy even today and keeps this novel on banned book lists.', '13.99', '0.50'),
(3, '9780806527208', 'The Alphabet of Manliness', 'Maddox', 'Awesome', 'Citadel Press', 1148965200, 5, 'This is the only sentence in the entire book that will give you a chance to adjust your face; take your time, because it’s about to be rocked off—permanently.', '15.95', '0.50'),
(6, '9780060935467', 'To Kill a Mockingbird', 'Harper Lee', 'Fiction', 'Harper Perennial Modern Classics', 1015304400, 5, 'Set in the small Southern town of Maycomb, Alabama, during the Depression, To Kill a Mockingbird follows three years in the life of 8-year-old Scout Finch, her brother, Jem, and their father, Atticus--three years punctuated by the arrest and eventual trial of a young black man accused of raping a white woman.', '12.95', '0.50'),
(8, '9780142000670', 'Of Mice and Men', 'John Steinbeck', 'Fiction', 'Penguin', 1010034000, 5, 'Tragic tale of a retarded man and the friend who loves and tries to protect him.', '14.00', '0.50'),
(9, '9780201760316', 'An Introduction to Object-Oriented Programming', 'Timothy Budd', 'Non-Fiction', 'Addison Wesley', 1002085200, 4, 'Timothy Budd provides a language-independent presentation of object-oriented principles, such as objects, methods, inheritance (including multiple inheritance) and polymorphism.', '98.80', '1.00');

-- --------------------------------------------------------

--
-- Table structure for table `ldb_borroweditems`
--

CREATE TABLE IF NOT EXISTS `ldb_borroweditems` (
  `itemid` mediumint(8) NOT NULL,
  `uid` mediumint(8) NOT NULL,
  `duedate` int(11) NOT NULL,
  KEY `itemid` (`itemid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_borroweditems`
--

INSERT INTO `ldb_borroweditems` (`itemid`, `uid`, `duedate`) VALUES
(7, 9, 1248973200),
(8, 9, 1248109200),
(8, 9, 1246035600),
(3, 13, 1249405200),
(16, 9, 1245517200),
(19, 9, 1246294800);

-- --------------------------------------------------------

--
-- Table structure for table `ldb_cds`
--

CREATE TABLE IF NOT EXISTS `ldb_cds` (
  `itemid` mediumint(8) NOT NULL,
  `upc` varchar(13) NOT NULL,
  `title` varchar(160) NOT NULL,
  `author` varchar(80) NOT NULL,
  `genre` varchar(30) NOT NULL,
  `publisher` varchar(80) NOT NULL,
  `releasedate` int(11) NOT NULL COMMENT 'unix timestamps',
  `rating` tinyint(1) NOT NULL DEFAULT '0' COMMENT '"5-star" ratings?',
  `description` varchar(512) NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `latefee` decimal(6,2) NOT NULL,
  PRIMARY KEY (`itemid`),
  KEY `upc` (`upc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_cds`
--

INSERT INTO `ldb_cds` (`itemid`, `upc`, `title`, `author`, `genre`, `publisher`, `releasedate`, `rating`, `description`, `cost`, `latefee`) VALUES
(14, 'B000UGG38W', 'The Dethalbum', 'Dethklok', 'Death Metal', 'Williams Street', 1190696400, 5, '', '13.98', '1.00'),
(15, 'B000001EPB', 'Michael Flatley''s Lord Of The Dance', 'Ronan Hardiman', 'Celtic', 'Philips', 857451600, 5, '', '17.98', '1.00'),
(17, 'B000000WBB', 'The Ressurection', 'Geto Boys', 'Hip Hop', 'Virgin Records Us', 829630800, 5, '', '13.77', '1.00'),
(18, '9780807206126', 'Magic Tree House Collection: Books 1-8', 'Mary Pope Osborne', 'Children', 'Imagination Studio', 1002603600, 4, 'The bestselling Magic Tree House series makes history fun by taking you right there, whether it''s to France in the Middle Ages, the prairies of America, the moon, or beyond. ', '30.00', '1.00'),
(19, 'B0000058HX', '25 Beethoven Favorites', 'Ludwig Van Beethoven', 'Classical', 'Vox', 840517200, 3, '', '4.98', '1.00');

-- --------------------------------------------------------

--
-- Table structure for table `ldb_customers`
--

CREATE TABLE IF NOT EXISTS `ldb_customers` (
  `uid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `password` varchar(57) NOT NULL,
  `email` varchar(96) NOT NULL,
  `regtime` int(11) unsigned NOT NULL,
  `lastvisit` int(11) unsigned NOT NULL,
  `firstname` varchar(40) NOT NULL,
  `lastname` varchar(40) NOT NULL,
  `birthdate` int(11) unsigned NOT NULL,
  `gender` char(1) DEFAULT NULL,
  `addrline1` varchar(80) DEFAULT NULL,
  `addrline2` varchar(80) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `phone` varchar(26) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `username` (`username`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ldb_customers`
--


-- --------------------------------------------------------

--
-- Table structure for table `ldb_dvds`
--

CREATE TABLE IF NOT EXISTS `ldb_dvds` (
  `itemid` mediumint(8) NOT NULL,
  `upc` varchar(13) NOT NULL,
  `title` varchar(160) NOT NULL,
  `author` varchar(80) NOT NULL,
  `genre` varchar(30) NOT NULL,
  `publisher` varchar(80) NOT NULL,
  `releasedate` int(11) NOT NULL COMMENT 'unix timestamps',
  `rating` tinyint(1) NOT NULL DEFAULT '0' COMMENT '"5-star" ratings?',
  `description` varchar(512) NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `latefee` decimal(6,2) NOT NULL,
  PRIMARY KEY (`itemid`),
  KEY `upc` (`upc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_dvds`
--

INSERT INTO `ldb_dvds` (`itemid`, `upc`, `title`, `author`, `genre`, `publisher`, `releasedate`, `rating`, `description`, `cost`, `latefee`) VALUES
(11, 'B000WZEZGS', 'Superbad', '', 'Comedy', 'Sony Pictures', 1196744400, 4, 'From the guy who brought you Knocked Up and The 40-Year-Old Virgin comes Superbad. Seth (Jonah Hill) and Evan (Michael Cera) want nothing more than to lose their virginity before they head off to college.', '19.94', '5.00'),
(13, 'B0002W4U9I', 'Santa Claus Conquers the Martians', '', 'Science Fiction', 'Alpha Video', 1098766800, 3, 'The children of Mars are in a funk, and nothing on the red planet seems to be able to cheer them up. Martian King Kimar comes up with the only reasonable solution: kidnap Santa Claus from Earth''s North Pole and bring him to their planet to make toys for their joyless, listless little green kids.', '7.98', '5.00'),
(16, '0783241895', 'Conan the Barbarian', '', 'Fantasy', 'Universal Studios', 959662800, 5, 'Conan the Barbarian, the movie that turned Arnold Schwarzenegger into a global superstar, is a prime example of a match made in heaven. It''s the movie that macho maverick writer-director John Milius was born to make, and Arnold was genetically engineered for his role as the muscle-bound, angst-ridden hero created in Robert E. Howard''s pulp novels.', '12.98', '5.00'),
(20, 'B00005JPP2', 'Aqua Teen Hunger Force Colon Movie Film for Theaters for DVD', '', 'Comedy', 'Turner Home Entertainment', 1187067600, 4, 'Fans of Cartoon Network’s Aqua Teen Hunger Force series (part of the cable channel’s Adult Swim programming) know what they’re in for with this feature-length extension of the nearly-indescribable animated show. Set in a rundown, Jersey suburb, Aqua Teen concerns the misadventures of three human-size characters who happen to be fast food refuse.', '29.98', '5.00'),
(21, 'B000GPIPSS', 'Dracula', '', 'Horror', 'Universal Studios', 1159246800, 5, 'The legend of Dracula continues in this gripping, masterful 2-disc edition of cinema''s most ominous vampire, digitally remastered for the 75th Anniversary Edition. Relive the horror, the mystery, and the intrigue of the original 1931 vampire masterpiece starring Bela Lugosi and directed by Tod Browning.', '26.98', '5.00');

-- --------------------------------------------------------

--
-- Table structure for table `ldb_items`
--

CREATE TABLE IF NOT EXISTS `ldb_items` (
  `itemid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `itemtype` enum('BOOK','PERIODICAL','CD','DVD') NOT NULL,
  `quantity` smallint(5) NOT NULL DEFAULT '0' COMMENT 'The quantity of this item in stock.',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `ldb_items`
--

INSERT INTO `ldb_items` (`itemid`, `itemtype`, `quantity`) VALUES
(2, 'BOOK', 3),
(3, 'BOOK', 2),
(4, 'PERIODICAL', 5),
(5, 'PERIODICAL', 6),
(6, 'BOOK', 1),
(7, 'PERIODICAL', 2),
(8, 'BOOK', 8),
(9, 'BOOK', 5),
(10, 'PERIODICAL', 16),
(11, 'DVD', 2),
(12, 'PERIODICAL', 0),
(13, 'DVD', 3),
(14, 'CD', 7),
(15, 'CD', 4),
(16, 'DVD', 0),
(17, 'CD', 21),
(18, 'CD', 3),
(19, 'CD', 4),
(20, 'DVD', 99),
(21, 'DVD', 61);

-- --------------------------------------------------------

--
-- Table structure for table `ldb_periodicals`
--

CREATE TABLE IF NOT EXISTS `ldb_periodicals` (
  `itemid` mediumint(8) NOT NULL,
  `isbn` varchar(13) DEFAULT NULL,
  `issn` varchar(8) DEFAULT NULL,
  `sici` varchar(100) DEFAULT NULL,
  `title` varchar(160) NOT NULL,
  `editor` varchar(80) NOT NULL,
  `genre` varchar(30) NOT NULL,
  `publisher` varchar(80) NOT NULL,
  `releasedate` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(512) NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `latefee` decimal(6,2) NOT NULL,
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_periodicals`
--

INSERT INTO `ldb_periodicals` (`itemid`, `isbn`, `issn`, `sici`, `title`, `editor`, `genre`, `publisher`, `releasedate`, `rating`, `description`, `cost`, `latefee`) VALUES
(4, '', '0040781X', '', 'TIME', 'Richard Stengel', 'News', 'Time Inc.', 0, 3, 'TIME gives you more than just a weekly news summary. TIME provides insightful analysis of today''s important events and what they mean to you and your family--from politics to scientific breakthroughs to human achievement. Plus, TIME helps you keep up with the arts, business, and society. That''s why 30 million people worldwide choose TIME.', '4.95', '0.50'),
(5, '', '00279358', '', 'National Geographic', 'Chris Johns', 'Geography', 'National Geographic Society', 0, 4, 'NATIONAL GEOGRAPHIC, the flagship magazine of the National Geographic Society, chronicles exploration and adventure, as well as changes that impact life on Earth. Editorial coverage encompasses people and places of the world, with an emphasis on human involvement in a changing universe. Major topics include culture, nature, geography, ecology, science and technology.', '3.95', '0.50'),
(7, '', '0037301X', '', 'Seventeen', 'Ann Shoket', 'Teen', 'Hearst Corporation', 0, 4, 'Seventeen is your handbook to life! Full of great fashion tips that keep you ahead of the trends... the hottest makeup, the best products for beautiful skin, must have jeans, the best shoes, belts & bags, and those great little dresses that keep you looking your best at school, parties... or just about anywhere!', '2.99', '0.50'),
(10, '', '01908286', '', 'The Washington Post', 'Marcus Brauchli', 'News', 'Katharine Weymouth', 0, 0, 'The Washington Post National Weekly edition is a digest of news, politics, and commentary. Each week is filled with incisive reporting, in-depth political analysis, and facts and figures from the most skilled, most seasoned news and editorial pros in Washington.', '1.99', '0.50'),
(12, '', '00999660', '', 'The Wall Street Journal', 'Robert Thomson', 'News', 'Les Hinton', 0, 0, 'This daily newspaper published the latest in news from the business and finance world. Additionally, it strives to connect current domestic and international news events to business fluctuations and market changes. It also seeks to inform the educated reader about pressing economic changes and evolution.', '1.10', '0.50');

-- --------------------------------------------------------

--
-- Table structure for table `ldb_tellers`
--

CREATE TABLE IF NOT EXISTS `ldb_tellers` (
  `uid` mediumint(8) NOT NULL,
  `hiredate` int(11) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ldb_tellers`
--


-- --------------------------------------------------------

--
-- Table structure for table `ldb_users`
--

CREATE TABLE IF NOT EXISTS `ldb_users` (
  `uid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `fullname` varchar(80) DEFAULT NULL,
  `password` varchar(57) NOT NULL,
  `userlevel` tinyint(1) NOT NULL,
  `email` varchar(96) DEFAULT NULL,
  `regtime` int(11) unsigned NOT NULL,
  `lastvisit` int(11) unsigned NOT NULL,
  `birthdate` int(11) unsigned NOT NULL,
  `sex` varchar(1) DEFAULT NULL,
  `address` varchar(160) NOT NULL,
  `phone` varchar(26) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `username` (`username`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='basic user data' AUTO_INCREMENT=17 ;

--
-- Dumping data for table `ldb_users`
--

INSERT INTO `ldb_users` (`uid`, `username`, `fullname`, `password`, `userlevel`, `email`, `regtime`, `lastvisit`, `birthdate`, `sex`, `address`, `phone`) VALUES
(1, 'NicFord1', 'Nicholas Ford', 'd6641a0d3ca7692b30d04e73bdbb6817af22aab612bd57e7d8920a0df', 9, 'Nicholas+LibDBDatabase@Nicks-Net.us', 1245411247, 1245787760, 499503600, 'M', '8131 Silo CT Severn, MD 21144', '(410) 419-5997'),
(13, 'dbergman', 'Drew Bergman', '11fce37a79abd95b75b2e69f25640071cf6345bf59756537da90f9459', 9, 'dbergman3@gmail.com', 1245526523, 1245882628, 486630000, 'M', '923 Chestnut Ridge Drive', '201-561-3853'),
(12, 'Teller1', '', 'f1659778a7aa44946cdf88408d278d288b3bfd597d6ab09b1816272a0', 5, 'demos@nicks-net.us', 1245521798, 1245521798, 1245481200, 'M', '', ''),
(9, 'Customer1', 'demo1', '94886410e9aee5632cc6cb96e00add0ff5b1a1b33ac071ae261d21cfe', 5, 'demo1@nicks-net.us', 1245521587, 1245787770, 1096095600, 'F', '123 Gate Dr', '(410) 555-5555'),
(14, 'demo5', 'Customer', 'de38097bc13991cea5891bc55bb1de7050839baa0dd8bd4e0ccb6ec6b', 1, 'demo@nicks-net.us', 1245553433, 1245787734, 836982000, 'F', '', '');
