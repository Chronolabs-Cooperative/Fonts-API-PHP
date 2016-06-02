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
-- Table structure for table `fonts_archiving`
--

DROP TABLE IF EXISTS `fonts_archiving`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_archiving` (
  `id` mediumint(24) NOT NULL AUTO_INCREMENT,
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `repository` varchar(300) NOT NULL DEFAULT '',
  `files` int(10) NOT NULL DEFAULT '0',
  `bytes` int(18) NOT NULL DEFAULT '0',
  `fingerprint` varchar(32) NOT NULL DEFAULT '',
  `repacks` int(24) NOT NULL DEFAULT '0',
  `unlocalisations` int(24) NOT NULL DEFAULT '0',
  `cachings` int(24) NOT NULL DEFAULT '0',
  `sourcings` int(24) NOT NULL DEFAULT '0' COMMENT 'Number of times archive was source for remote downloading',
  `packing` enum('7z','zip','rar','rar5','zoo','tar.gz','store') NOT NULL DEFAULT 'zip',
  `added` int(13) NOT NULL DEFAULT '0',
  `packed` int(13) NOT NULL DEFAULT '0',
  `repacked` int(13) NOT NULL DEFAULT '0',
  `unlocalise` int(13) NOT NULL DEFAULT '0',
  `accessed` int(13) NOT NULL DEFAULT '0',
  `checked` int(13) NOT NULL DEFAULT '0',
  `cached` int(13) NOT NULL DEFAULT '0',
  `sourced` int(13) NOT NULL DEFAULT '0' COMMENT 'When last archive was sourced for remote downloading',
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`font_id`(17),`fingerprint`(14),`id`),
  KEY `CHRONOLOGISTIC` (`accessed`,`unlocalise`,`repacked`,`packed`,`added`,`packing`,`path`,`filename`,`font_id`,`id`,`checked`,`cached`,`fingerprint`,`sourced`)
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
