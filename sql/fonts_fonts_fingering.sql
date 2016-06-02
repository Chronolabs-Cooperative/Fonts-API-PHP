CREATE DATABASE  IF NOT EXISTS `fonts` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `fonts`;
-- MySQL dump 10.13  Distrib 5.6.30, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: fonts
-- ------------------------------------------------------
-- Server version	5.6.30-0ubuntu0.15.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `fonts_fingering`
--

DROP TABLE IF EXISTS `fonts_fingering`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_fingering` (
  `type` enum('pfa','pfb','pt3','t42','sfd','ttf','bdf','otf','otb','cff','cef','gai','woff','svg','ufo','pf3','ttc','gsf','cid','bin','hqx','dfont','mf','ik','fon','fnt','pcf','pmf','pdb','eot','afm','other') NOT NULL DEFAULT 'other',
  `fingerprint` varchar(32) NOT NULL DEFAULT '',
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `file_id` mediumint(24) NOT NULL DEFAULT '0',
  `upload_id` int(18) NOT NULL DEFAULT '0',
  `created` int(13) NOT NULL DEFAULT '0',
  `polled` int(13) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type`,`fingerprint`,`font_id`,`archive_id`,`upload_id`),
  KEY `PINGERING` (`type`,`font_id`(14),`archive_id`,`upload_id`,`fingerprint`(14))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-29 13:21:10
