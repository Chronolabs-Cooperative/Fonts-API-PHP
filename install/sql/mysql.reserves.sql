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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-13  1:56:36
