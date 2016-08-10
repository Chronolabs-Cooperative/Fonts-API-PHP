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
-- Table structure for table `networking`
--

DROP TABLE IF EXISTS `networking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `networking` (
  `ip_id` varchar(32) NOT NULL DEFAULT '',
  `type` enum('ipv4','ipv6') NOT NULL DEFAULT 'ipv4',
  `ipaddy` varchar(64) NOT NULL DEFAULT '',
  `netbios` varchar(198) NOT NULL DEFAULT '',
  `domain` varchar(128) NOT NULL DEFAULT '',
  `country` varchar(3) NOT NULL DEFAULT '',
  `region` varchar(128) NOT NULL DEFAULT '',
  `city` varchar(128) NOT NULL DEFAULT '',
  `postcode` varchar(15) NOT NULL DEFAULT '',
  `timezone` varchar(10) NOT NULL DEFAULT '',
  `longitude` float(12,8) NOT NULL DEFAULT '0.00000000',
  `latitude` float(12,8) NOT NULL DEFAULT '0.00000000',
  `contributes` int(16) NOT NULL DEFAULT '0',
  `downloads` int(16) NOT NULL DEFAULT '0',
  `uploads` int(16) NOT NULL DEFAULT '0',
  `fonts` int(16) NOT NULL DEFAULT '0',
  `surveys` int(16) NOT NULL DEFAULT '0',
  `created` int(13) NOT NULL DEFAULT '0',
  `last` int(13) NOT NULL DEFAULT '0',
  `data` mediumtext,
  `whois` text,
  PRIMARY KEY (`ip_id`,`type`,`ipaddy`(15)),
  KEY `SEARCH` (`type`,`ipaddy`(15),`netbios`(12),`domain`(12),`country`(2),`city`(12),`region`(12),`postcode`(6),`longitude`,`latitude`,`created`,`last`,`timezone`(6))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `networking`
--

LOCK TABLES `networking` WRITE;
/*!40000 ALTER TABLE `networking` DISABLE KEYS */;
/*!40000 ALTER TABLE `networking` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-10 22:05:06
