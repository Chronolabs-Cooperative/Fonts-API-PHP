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
-- Table structure for table `flows_history`
--

DROP TABLE IF EXISTS `flows_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flows_history` (
  `history_id` mediumint(42) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL DEFAULT '',
  `ip_id` varchar(32) NOT NULL DEFAULT '',
  `flow_id` mediumint(22) NOT NULL DEFAULT '0',
  `font_id` varchar(32) DEFAULT '',
  `upload_id` int(18) NOT NULL DEFAULT '0',
  `questions` int(10) NOT NULL DEFAULT '2',
  `keys` int(10) NOT NULL DEFAULT '0',
  `fixes` int(10) NOT NULL DEFAULT '0',
  `typals` int(10) NOT NULL DEFAULT '0',
  `elapsed` float(14,8) NOT NULL DEFAULT '0.00000000',
  `score` float(14,8) NOT NULL DEFAULT '0.00000000',
  `longitude` float(12,8) NOT NULL DEFAULT '0.00000000',
  `latitude` float(12,8) NOT NULL DEFAULT '0.00000000',
  `started` int(12) NOT NULL DEFAULT '0',
  `expiring` int(12) NOT NULL DEFAULT '0',
  `reminding` int(12) unsigned NOT NULL DEFAULT '0',
  `reminders` int(6) NOT NULL DEFAULT '3',
  `data` mediumtext,
  `step` varchar(45) NOT NULL DEFAULT 'expired',
  PRIMARY KEY (`history_id`),
  KEY `SEARCH` (`key`(12),`font_id`(12),`flow_id`,`questions`,`keys`,`fixes`,`typals`,`started`,`reminding`,`expiring`,`upload_id`)
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

-- Dump completed on 2016-05-29 13:21:11
