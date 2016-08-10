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
-- Table structure for table `fonts_files`
--

DROP TABLE IF EXISTS `fonts_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_files` (
  `id` mediumint(24) NOT NULL AUTO_INCREMENT,
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `type` enum('json','diz','pfa','pfb','pt3','t42','sfd','ttf','bdf','otf','otb','cff','cef','gai','woff','svg','ufo','pf3','ttc','gsf','cid','bin','hqx','dfont','mf','ik','fon','fnt','pcf','pmf','pdb','eot','afm','php','z','png','gif','jpg','data','css','other') NOT NULL DEFAULT 'other',
  `extension` varchar(12) NOT NULL DEFAULT '',
  `filename` varchar(128) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `bytes` int(12) NOT NULL DEFAULT '0',
  `hits` int(20) NOT NULL DEFAULT '0',
  `accessing` int(20) DEFAULT NULL COMMENT 'Number of time file has accessed',
  `updates` int(20) DEFAULT NULL COMMENT 'Number of time file has updated',
  `caching` int(20) DEFAULT NULL COMMENT 'Number of time the file has been cached',
  `sourcing` int(20) DEFAULT NULL COMMENT 'Number of time the file has been physically remotely downloaded',
  `created` int(13) NOT NULL DEFAULT '0',
  `accessed` int(13) NOT NULL DEFAULT '0',
  `updated` int(13) DEFAULT NULL COMMENT 'When it was last updated',
  `cached` int(13) DEFAULT NULL COMMENT 'When it was last cached',
  `sourced` int(13) DEFAULT NULL COMMENT 'When it was last the file was remotely downloaded',
  PRIMARY KEY (`id`),
  KEY `SEARCH` (`font_id`(14),`archive_id`,`type`,`extension`,`filename`(12),`path`,`id`),
  KEY `CHRONOLOGISTIC` (`updated`,`cached`,`sourced`,`accessed`,`created`,`bytes`,`path`,`filename`,`extension`,`font_id`,`id`,`archive_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25407 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_files`
--

LOCK TABLES `fonts_files` WRITE;
/*!40000 ALTER TABLE `fonts_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_files` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-10 22:05:05
