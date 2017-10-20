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
-- Table structure for table `callbacks`
--

DROP TABLE IF EXISTS `callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callbacks` (
  `when` int(12) NOT NULL,
  `uri` varchar(250) NOT NULL DEFAULT '',
  `timeout` int(4) NOT NULL DEFAULT '0',
  `connection` int(4) NOT NULL DEFAULT '0',
  `data` mediumtext NOT NULL,
  `queries` mediumtext NOT NULL,
  `fails` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`when`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callbacks`
--

LOCK TABLES `callbacks` WRITE;
/*!40000 ALTER TABLE `callbacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `callbacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emails` (
  `id` varchar(32) NOT NULL,
  `emails` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emails`
--

LOCK TABLES `emails` WRITE;
/*!40000 ALTER TABLE `emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flows`
--

DROP TABLE IF EXISTS `flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flows` (
  `flow_id` mediumint(22) NOT NULL AUTO_INCREMENT,
  `ip_id` varchar(32) DEFAULT '',
  `last_history_id` mediumint(42) DEFAULT '0',
  `email` varchar(198) NOT NULL DEFAULT '',
  `name` varchar(64) DEFAULT '',
  `participate` enum('yes','no','banned') DEFAULT 'yes',
  `fonts` int(10) DEFAULT '0',
  `surveys` int(10) DEFAULT '0',
  `score` float(14,8) DEFAULT '0.00000000',
  `last` int(12) DEFAULT '0',
  `reminder` int(12) DEFAULT '0',
  `available` int(8) DEFAULT '8',
  `currently` int(8) DEFAULT '0',
  `code` varchar(6) DEFAULT '00000A',
  PRIMARY KEY (`flow_id`),
  KEY `SEARCH` (`email`(12),`participate`,`fonts`,`surveys`,`last`,`score`,`reminder`,`available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flows`
--

LOCK TABLES `flows` WRITE;
/*!40000 ALTER TABLE `flows` DISABLE KEYS */;
/*!40000 ALTER TABLE `flows` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Dumping data for table `flows_history`
--

LOCK TABLES `flows_history` WRITE;
/*!40000 ALTER TABLE `flows_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `flows_history` ENABLE KEYS */;
UNLOCK TABLES;

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
  `repacks` int(24) NOT NULL DEFAULT '0' COMMENT 'Number of times the cache has regenerated',
  `unlocalisations` int(24) NOT NULL DEFAULT '0' COMMENT 'Number of times the cache has regenerated',
  `cachings` int(24) NOT NULL DEFAULT '0' COMMENT 'Number of times the cache has regenerated',
  `sourcings` int(24) NOT NULL DEFAULT '0' COMMENT 'Number of times archive was source for remote downloading',
  `packing` enum('7z','zip','rar','rar5','zoo','tar.gz','store') NOT NULL DEFAULT 'zip',
  `added` int(13) NOT NULL DEFAULT '0' COMMENT 'Date Archive was added to DB',
  `packed` int(13) NOT NULL DEFAULT '0' COMMENT 'Date Archive was first packed',
  `repacked` int(13) NOT NULL DEFAULT '0' COMMENT 'Date Archive was repaired or repacked',
  `unlocalise` int(13) NOT NULL DEFAULT '0' COMMENT 'Date Archive was delocalised from server too cold SVN',
  `accessed` int(13) NOT NULL DEFAULT '0' COMMENT 'Date Archive was last accessed on a hit',
  `checked` int(13) NOT NULL DEFAULT '0' COMMENT 'Date Archive was last spot check for flaws or errors',
  `cached` int(13) NOT NULL DEFAULT '0' COMMENT 'Last time the cache was regenerated',
  `sourced` int(13) NOT NULL DEFAULT '0' COMMENT 'When last archive was sourced for remote downloading',
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`font_id`(17),`fingerprint`(14),`id`),
  KEY `CHRONOLOGISTIC` (`accessed`,`unlocalise`,`repacked`,`packed`,`added`,`packing`,`path`,`filename`,`font_id`,`id`,`checked`,`cached`,`fingerprint`,`sourced`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_archiving`
--

LOCK TABLES `fonts_archiving` WRITE;
/*!40000 ALTER TABLE `fonts_archiving` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_archiving` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonts_callbacks`
--

DROP TABLE IF EXISTS `fonts_callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_callbacks` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `type` enum('upload','archive','fonthit') NOT NULL DEFAULT 'upload',
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `upload_id` int(18) NOT NULL DEFAULT '0',
  `uri` varchar(350) NOT NULL DEFAULT 'http://',
  `email` varchar(198) NOT NULL DEFAULT '',
  `last` int(13) NOT NULL DEFAULT '0',
  `calls` int(20) NOT NULL DEFAULT '0',
  `fails` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`font_id`(12),`upload_id`),
  KEY `SEARCH` (`font_id`(12),`upload_id`,`uri`(12),`last`,`calls`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_callbacks`
--

LOCK TABLES `fonts_callbacks` WRITE;
/*!40000 ALTER TABLE `fonts_callbacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_callbacks` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `fonts_downloads`
--

DROP TABLE IF EXISTS `fonts_downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_downloads` (
  `id` mediumint(22) NOT NULL AUTO_INCREMENT,
  `type` enum('7z','zip','rar','rar5','zoo','tar.gz','exe') NOT NULL DEFAULT 'zip',
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `fingerprint` varchar(32) NOT NULL DEFAULT '',
  `filename` varchar(128) NOT NULL DEFAULT '',
  `ip_id` varchar(32) NOT NULL DEFAULT '',
  `when` int(13) NOT NULL DEFAULT '0',
  `size` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `SEARCH` (`font_id`(12),`ip_id`(12),`when`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_downloads`
--

LOCK TABLES `fonts_downloads` WRITE;
/*!40000 ALTER TABLE `fonts_downloads` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_downloads` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_files`
--

LOCK TABLES `fonts_files` WRITE;
/*!40000 ALTER TABLE `fonts_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_files` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Dumping data for table `fonts_fingering`
--

LOCK TABLES `fonts_fingering` WRITE;
/*!40000 ALTER TABLE `fonts_fingering` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_fingering` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonts_glyphs`
--

DROP TABLE IF EXISTS `fonts_glyphs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_glyphs` (
  `glyph-id` varchar(32) NOT NULL DEFAULT '--------------------------------',
  `font-id` varchar(32) NOT NULL DEFAULT '--------------------------------',
  `fingerprint` varchar(44) NOT NULL DEFAULT '--------------------------------------------',
  `name` varchar(64) NOT NULL DEFAULT '',
  `ufofile` varchar(64) NOT NULL DEFAULT '',
  `unicode` varchar(8) NOT NULL DEFAULT '--------',
  `format` int(10) NOT NULL DEFAULT '1',
  `width` int(10) NOT NULL DEFAULT '0',
  `contours` int(8) NOT NULL DEFAULT '0',
  `pointers` int(8) NOT NULL DEFAULT '0',
  `smoothers` int(8) NOT NULL DEFAULT '0',
  `addon` enum('yes','no') NOT NULL DEFAULT 'no',
  `addon-glyph-id` varchar(32) NOT NULL DEFAULT '',
  `addon-font-id` varchar(32) NOT NULL DEFAULT '',
  `created` int(13) NOT NULL DEFAULT '0',
  `occurences` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`glyph-id`,`font-id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_glyphs`
--

LOCK TABLES `fonts_glyphs` WRITE;
/*!40000 ALTER TABLE `fonts_glyphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_glyphs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonts_glyphs_contours`
--

DROP TABLE IF EXISTS `fonts_glyphs_contours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts_glyphs_contours` (
  `id` mediumint(41) NOT NULL AUTO_INCREMENT,
  `font-id` varchar(32) NOT NULL DEFAULT '--------------------------------',
  `glyph-id` varchar(32) NOT NULL DEFAULT '--------------------------------',
  `contour` int(10) NOT NULL DEFAULT '0',
  `weight` int(10) NOT NULL DEFAULT '0',
  `x` int(8) NOT NULL DEFAULT '0',
  `y` int(8) NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL DEFAULT '-----',
  `smooth` enum('yes','no','-----') NOT NULL DEFAULT '-----',
  `created` int(13) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`font-id`,`glyph-id`,`weight`,`contour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts_glyphs_contours`
--

LOCK TABLES `fonts_glyphs_contours` WRITE;
/*!40000 ALTER TABLE `fonts_glyphs_contours` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonts_glyphs_contours` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes` (
  `id` int(23) NOT NULL AUTO_INCREMENT,
  `type` enum('typal','fixes','keys') DEFAULT NULL,
  `node` varchar(64) DEFAULT '0',
  `usage` int(12) DEFAULT '0',
  `weight` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`node`(21),`type`,`usage`,`weight`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nodes`
--

LOCK TABLES `nodes` WRITE;
/*!40000 ALTER TABLE `nodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `nodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nodes_linking`
--

DROP TABLE IF EXISTS `nodes_linking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes_linking` (
  `font_id` varchar(32) DEFAULT NULL,
  `node_id` int(23) DEFAULT '0',
  KEY `PINGERING` (`node_id`,`font_id`(11))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nodes_linking`
--

LOCK TABLES `nodes_linking` WRITE;
/*!40000 ALTER TABLE `nodes_linking` DISABLE KEYS */;
/*!40000 ALTER TABLE `nodes_linking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peers`
--

DROP TABLE IF EXISTS `peers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peers` (
  `peer-id` varchar(32) NOT NULL,
  `api-uri` varchar(200) NOT NULL,
  `api-uri-callback` varchar(200) NOT NULL,
  `api-uri-zip` varchar(200) NOT NULL,
  `api-uri-fonts` varchar(200) NOT NULL,
  `polinating` enum('Yes','No') NOT NULL,
  `version` varchar(20) NOT NULL,
  `heard` int(12) NOT NULL DEFAULT '0',
  `called` int(12) NOT NULL DEFAULT '0',
  `down` int(12) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`peer-id`,`api-uri`,`api-uri-callback`,`api-uri-zip`,`api-uri-fonts`,`polinating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peers`
--

LOCK TABLES `peers` WRITE;
/*!40000 ALTER TABLE `peers` DISABLE KEYS */;
/*!40000 ALTER TABLE `peers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `releases`
--

DROP TABLE IF EXISTS `releases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `releases` (
  `id` int(22) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(198) NOT NULL,
  `org` varchar(150) NOT NULL,
  `callback` varchar(300) NOT NULL,
  `method` enum('subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
  `sent` int(22) NOT NULL DEFAULT '0',
  `failed` int(22) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL DEFAULT '0',
  `updated` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `SEARCH` (`name`,`email`,`org`,`method`) USING BTREE KEY_BLOCK_SIZE=12
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `releases`
--

LOCK TABLES `releases` WRITE;
/*!40000 ALTER TABLE `releases` DISABLE KEYS */;
/*!40000 ALTER TABLE `releases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserves`
--

DROP TABLE IF EXISTS `reserves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserves` (
  `reserved_id` int(12) NOT NULL AUTO_INCREMENT,
  `parent` enum('normal','italic','bold','wide','condensed','ultra','extra','light','semi','book','body','header','heading','footer','graphic','system','quote','blocks','message','admin','logo','slogan','legal','script') NOT NULL DEFAULT 'normal',
  `child` enum('','normal','italic','bold','wide','condensed','ultra','extra','light','semi','book','body','header','heading','footer','graphic','system','quote','blocks','message','admin','logo','slogan','legal','script') NOT NULL DEFAULT '',
  `keyword` varchar(20) NOT NULL DEFAULT '',
  `font-size` enum('','inherit','unset','initial','unset','xx-small','x-small','small','medium','large','x-large','xx-large','1','2','3','4','5','6','7','8','9','8px','10px','12px','13px','14px','16px','0.55em','0.65em','0.75em','0.85em','0.95em','1.05em','1.15em','1.25em','1.35em') NOT NULL DEFAULT 'inherit',
  `font-size-adjust` enum('','none','inherit','initial','unset','0.05','0.15','0.25','0.35','0.45','0.55','0.65','0.75','0.85','0.95','1.05','1.15','1.25','1.35','1.45','1.55','1.65','1.75','1.85','1.95','2.05') NOT NULL DEFAULT 'none',
  `font-stretch` enum('','none','inherit','initial','unset','ultra-condensed','extra-condensed','condensed','semi-condensed','normal','semi-expanded','expanded','extra-expanded','ultra-expanded') NOT NULL DEFAULT 'normal',
  `font-style` enum('','none','inherit','initial','unset','italic','oblique','normal') NOT NULL DEFAULT 'inherit',
  `font-synthesis` enum('','none','inherit','initial','unset','weight','style','weight style') NOT NULL DEFAULT 'inherit',
  `font-kerning` enum('','none','inherit','initial','unset','auto','normal') NOT NULL DEFAULT 'normal',
  `font-weight` enum('','lighter','bolder','inherit','initial','unset','auto','normal','100','200','300','400','500','600','700','800','900','bold') NOT NULL DEFAULT 'auto',
  PRIMARY KEY (`reserved_id`),
  KEY `SEARCH` (`parent`,`child`,`keyword`(15),`reserved_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserves`
--

LOCK TABLES `reserves` WRITE;
/*!40000 ALTER TABLE `reserves` DISABLE KEYS */;
INSERT INTO `reserves` VALUES (1,'normal','','std','inherit','none','none','normal','inherit','normal','normal'),(2,'normal','','normal','inherit','none','normal','normal','inherit','normal','normal'),(3,'bold','','Black','inherit','none','normal','oblique','weight','auto','bold'),(4,'bold','bold','Bold','inherit','none','normal','oblique','weight','auto','900'),(5,'italic','','Italic','inherit','none','inherit','italic','weight','auto','normal'),(6,'wide','','Wide','inherit','none','normal','normal','weight','auto','inherit'),(7,'condensed','','Condensed','inherit','none','condensed','inherit','weight','auto','normal'),(8,'condensed','','Cond','inherit','none','condensed','inherit','weight','auto','normal'),(9,'ultra','','Ultra','inherit','none','ultra-expanded','oblique','weight','auto','bold'),(10,'extra','','Extra','inherit','none','extra-condensed','normal','weight','auto','normal'),(11,'light','','Light','inherit','none','inherit','inherit','weight','inherit','lighter'),(12,'bold','semi','Semi','inherit','none','normal','oblique','weight','auto','500'),(13,'normal','book','Book','inherit','none','normal','normal','weight','auto','normal'),(14,'normal','script','Scipt','inherit','none','inherit','inherit','inherit','inherit','inherit'),(15,'legal','','Lt','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(16,'legal','','LL','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(17,'legal','','LF','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(18,'legal','','LT','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(19,'system','','SMono','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(20,'system','','Mono','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(21,'system','','MONOTYPE','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(22,'body','','ITC','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(23,'body','','ST','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(24,'body','','SSi','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(25,'body','','SSK','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(26,'body','','Display','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(27,'body','','Disp','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(28,'body','','Dis','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(29,'body','','Ds','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(30,'condensed','','Cd','inherit','inherit','condensed','inherit','inherit','inherit','inherit'),(31,'ultra','','Superexpanded','inherit','inherit','ultra-expanded','inherit','inherit','inherit','inherit'),(32,'condensed','','Semicondensed','inherit','none','extra-condensed','normal','inherit','inherit','inherit'),(33,'heading','','DCaps','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(34,'heading','','Caps','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(35,'header','','OSC','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(36,'footer','','Serif','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(37,'slogan','','TMed','inherit','inherit','inherit','inherit','inherit','normal','inherit'),(38,'header','','OS','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(39,'wide','','Wid','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(40,'bold','','Bol','inherit','inherit','inherit','oblique','weight','auto','800'),(41,'bold','','Bd','inherit','inherit','inherit','inherit','weight','inherit','700'),(42,'extra','','Ep','inherit','inherit','expanded','inherit','inherit','inherit','inherit'),(43,'ultra','','Exp','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(44,'body','','LTStd','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(45,'semi','','Med','inherit','inherit','inherit','inherit','weight','auto','400'),(46,'semi','','Medium','inherit','inherit','inherit','inherit','weight','normal','400'),(47,'semi','','Demi','inherit','inherit','inherit','inherit','weight','normal','600'),(48,'heading','','Th','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(49,'heading','','Pro','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(50,'semi','','Sb','inherit','inherit','inherit','inherit','weight','inherit','600'),(51,'message','','Scn','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(52,'message','','Screen','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(53,'body','','ITCStd','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(54,'body','','ITC','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(55,'blocks','','EF','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(56,'quote','','NF','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(57,'condensed','','LCon','inherit','inherit','semi-condensed','inherit','inherit','inherit','inherit'),(58,'body','','Plain','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(59,'bold','semi','Semibold','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(60,'heading','','Outline','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(61,'footer','','Stencil','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(62,'italic','','Italique','inherit','inherit','inherit','italic','inherit','inherit','inherit'),(63,'italic','','Slant','inherit','inherit','inherit','italic','inherit','inherit','inherit'),(64,'italic','','Slanted','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(65,'book','','Roman','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(66,'ultra','','Expanded','inherit','inherit','ultra-expanded','inherit','inherit','inherit','inherit'),(67,'normal','body','OT','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(68,'heading','','Expert','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(70,'italic','','Ita','inherit','inherit','inherit','italic','inherit','inherit','inherit'),(71,'normal','body','Sans','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(72,'bold','heading','Obl','inherit','none','inherit','oblique','weight','inherit','bold'),(73,'blocks','','Thin','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(74,'bold','heading','Heavy','inherit','inherit','inherit','oblique','inherit','inherit','inherit'),(75,'bold','','Oblique','inherit','inherit','inherit','oblique','weight style','normal','auto'),(76,'body','normal','REG','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(77,'body','normal','Rg','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(78,'normal','body','ST','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(79,'body','normal','Regular','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(80,'book','normal','BT','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(81,'bold','heading','Bk','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(82,'message','normal','MT','inherit','inherit','inherit','inherit','inherit','inherit','inherit'),(83,'quote','','SC','inherit','inherit','inherit','inherit','inherit','inherit','inherit');
/*!40000 ALTER TABLE `reserves` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `whois`
--

DROP TABLE IF EXISTS `whois`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whois` (
  `id` varchar(32) NOT NULL,
  `whois` mediumtext NOT NULL,
  `created` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  `instances` mediumint(18) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whois`
--

LOCK TABLES `whois` WRITE;
/*!40000 ALTER TABLE `whois` DISABLE KEYS */;
/*!40000 ALTER TABLE `whois` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'fonts-labs-coop'
--

--
-- Dumping routines for database 'fonts-labs-coop'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-28 17:12:39
