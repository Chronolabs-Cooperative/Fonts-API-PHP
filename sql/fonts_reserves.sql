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
