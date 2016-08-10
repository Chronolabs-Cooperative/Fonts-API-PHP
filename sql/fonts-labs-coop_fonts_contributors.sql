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
-- Table structure for table `fonts_contributors`
--

DROP TABLE IF EXISTS `fonts_contributors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_contributors` (
  `id` mediumint(22) NOT NULL AUTO_INCREMENT,
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `ip_id` varchar(32) NOT NULL DEFAULT '',
  `flow_id` int(32) NOT NULL DEFAULT '0',
  `history_id` int(32) NOT NULL DEFAULT '0',
  `upload_id` int(18) NOT NULL DEFAULT '0',
  `when` int(13) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fonts_id_UNIQUE` (`font_id`),
  KEY `SEARCH` (`font_id`(12),`ip_id`(12),`flow_id`,`history_id`,`when`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_contributors`
--

LOCK TABLES `fonts_contributors` WRITE;
/*!40000 ALTER TABLE `fonts_contributors` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_contributors` ENABLE KEYS */;
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
