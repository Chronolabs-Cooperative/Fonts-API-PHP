CREATE DATABASE  IF NOT EXISTS `fonts-labs-coop` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `fonts-labs-coop`;
-- MySQL dump 10.13  Distrib 5.6.28, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: fonts-labs-coop
-- ------------------------------------------------------
-- Server version	5.6.28-0ubuntu0.15.04.1

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
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploads` (
  `id` int(18) NOT NULL AUTO_INCREMENT,
  `ip_id` varchar(32) DEFAULT '',
  `uploaded_file` varchar(255) DEFAULT '',
  `uploaded_path` varchar(255) DEFAULT '',
  `currently_path` varchar(255) DEFAULT '',
  `field` varchar(64) DEFAULT '',
  `key` varchar(44) DEFAULT '',
  `email` varchar(198) DEFAULT '',
  `cc` varchar(32) DEFAULT NULL,
  `bcc` varchar(32) DEFAULT NULL,
  `font_id` varchar(32) DEFAULT '',
  `prefix` varchar(11) DEFAULT 'labscoop:',
  `referee_uri` varchar(350) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `reminder` int(12) DEFAULT '0',
  `scope` enum('none','to','cc','bcc','all') DEFAULT 'none',
  `uploaded` int(12) DEFAULT '0',
  `converted` int(12) DEFAULT '0',
  `quizing` int(12) DEFAULT '0',
  `storaged` int(12) DEFAULT '0',
  `sorting` int(12) DEFAULT '0',
  `cleaned` int(12) DEFAULT '0',
  `released` int(12) DEFAULT '0',
  `notified` int(12) DEFAULT '0',
  `bytes` int(8) DEFAULT '0',
  `batch-size` int(8) DEFAULT '0',
  `archived` int(8) DEFAULT '0',
  `surveys` int(8) DEFAULT '0',
  `available` int(8) DEFAULT '18',
  `slotting` int(8) DEFAULT '0',
  `needing` int(8) DEFAULT '5',
  `finished` int(8) DEFAULT '0',
  `elapses` int(8) DEFAULT '0',
  `frequency` int(8) DEFAULT '0',
  `expired` int(8) DEFAULT '0',
  `datastore` mediumtext,
  `callback` varchar(300) DEFAULT '',
  `longitude` float(12,6) DEFAULT '0.000000',
  `latitude` float(12,6) DEFAULT '0.000000',
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`key`(24),`email`(13),`font_id`(11),`archived`,`batch-size`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploads`
--

LOCK TABLES `uploads` WRITE;
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-13  1:56:38
