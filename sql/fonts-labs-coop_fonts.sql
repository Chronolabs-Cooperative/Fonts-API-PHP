CREATE DATABASE  IF NOT EXISTS `fonts-labs-coop` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `fonts-labs-coop`;
-- MySQL dump 10.13  Distrib 5.7.12, for Linux (x86_64)
--
-- Host: localhost    Database: fonts-labs-coop
-- ------------------------------------------------------
-- Server version	5.7.12-0ubuntu1.1

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
-- Table structure for table `fonts`
--

DROP TABLE IF EXISTS `fonts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `type` enum('local','peer') NOT NULL DEFAULT 'local',
  `state` enum('online','offline','historical','onhold') NOT NULL DEFAULT 'online',
  `peer` varchar(44) DEFAULT '',
  `peer_id` varchar(32) DEFAULT '',
  `names` int(6) DEFAULT '0',
  `fingers` int(8) DEFAULT '0',
  `bytes` int(8) DEFAULT '0',
  `nodes` int(8) DEFAULT '0',
  `created` int(12) DEFAULT '0',
  `accessed` int(12) DEFAULT '0',
  `cached` int(12) DEFAULT '0',
  `failed` int(12) DEFAULT '0',
  `failures` mediumint(20) DEFAULT '0',
  `downloaded` mediumint(20) DEFAULT '0',
  `hits` mediumint(20) DEFAULT '0',
  `normal` enum('yes','no') DEFAULT 'no',
  `italic` enum('yes','no') DEFAULT 'no',
  `bold` enum('yes','no') DEFAULT 'no',
  `wide` enum('yes','no') DEFAULT 'no',
  `condensed` enum('yes','no') DEFAULT 'no',
  `light` enum('yes','no') DEFAULT 'no',
  `semi` enum('yes','no') DEFAULT 'no',
  `book` enum('yes','no') DEFAULT 'no',
  `body` enum('yes','no') DEFAULT 'no',
  `header` enum('yes','no') DEFAULT 'no',
  `heading` enum('yes','no') DEFAULT 'no',
  `footer` enum('yes','no') DEFAULT 'no',
  `graphic` enum('yes','no') DEFAULT 'no',
  `system` enum('yes','no') DEFAULT 'no',
  `quote` enum('yes','no') DEFAULT 'no',
  `block` enum('yes','no') DEFAULT 'no',
  `message` enum('yes','no') DEFAULT 'no',
  `admin` enum('yes','no') DEFAULT 'no',
  `logo` enum('yes','no') DEFAULT 'no',
  `slogon` enum('yes','no') DEFAULT 'no',
  `legal` enum('yes','no') DEFAULT 'no',
  `script` enum('yes','no') DEFAULT 'no',
  `data` mediumtext,
  `medium` enum('FONT_RESOURCES_RESOURCE','FONT_RESOURCES_REMOTE','FONT_RESOURCES_PEER','FONT_RESOURCES_CACHE') DEFAULT 'FONT_RESOURCES_RESOURCE',
  `longitude` float(12,8) DEFAULT '0.00000000',
  `latitude` float(12,8) DEFAULT '0.00000000',
  `version` float(6,3) DEFAULT '1.000',
  `date` varchar(32) DEFAULT '',
  `uploaded` int(13) DEFAULT '0',
  `licence` varchar(15) DEFAULT 'gpl3',
  `company` varchar(64) DEFAULT 'Chronolabs Cooperative',
  `matrix` varchar(128) DEFAULT '',
  `bbox` varchar(20) DEFAULT '0',
  `painttype` varchar(20) DEFAULT '0',
  `info` varchar(128) DEFAULT '',
  `family` varchar(128) DEFAULT '',
  `weight` varchar(32) DEFAULT 'Normal',
  `fstype` varchar(32) DEFAULT '0',
  `italicangle` varchar(16) DEFAULT '0',
  `fixedpitch` varchar(16) DEFAULT '0',
  `underlineposition` varchar(16) DEFAULT '1',
  `underlinethickness` varchar(16) DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`state`,`type`,`peer`(11),`names`,`nodes`,`hits`,`failed`,`failures`,`cached`,`medium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts`
--

LOCK TABLES `fonts` WRITE;
/*!40000 ALTER TABLE `fonts` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-28 17:12:11
