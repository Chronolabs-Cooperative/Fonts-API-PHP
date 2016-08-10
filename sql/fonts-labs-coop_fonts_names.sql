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
-- Table structure for table `fonts_names`
--

DROP TABLE IF EXISTS `fonts_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_names` (
  `font_id` varchar(32) DEFAULT '',
  `upload_id` int(18) DEFAULT '0',
  `name` varchar(64) DEFAULT '',
  `longitude` float(12,8) DEFAULT '0.00000000',
  `latitude` float(12,8) DEFAULT '0.00000000',
  `country` varchar(3) DEFAULT 'USA',
  `region` varchar(64) DEFAULT '',
  `city` varchar(64) DEFAULT '',
  KEY `POINTING` (`upload_id`,`font_id`(14),`name`(12)),
  KEY `LOCALITY` (`longitude`,`latitude`,`country`(2),`region`(10),`city`(10),`font_id`(13),`upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_names`
--

LOCK TABLES `fonts_names` WRITE;
/*!40000 ALTER TABLE `fonts_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_names` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-10 22:05:07
