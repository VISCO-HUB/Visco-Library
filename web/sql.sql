CREATE DATABASE  IF NOT EXISTS `assets_library` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `assets_library`;
-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: assets_library
-- ------------------------------------------------------
-- Server version	5.7.16-log

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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(800) DEFAULT NULL,
  `parent` int(11) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  `date` datetime DEFAULT NULL,
  `modified` varchar(200) DEFAULT NULL,
  `premissions` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `description` varchar(400) DEFAULT NULL,
  `type` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=258 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (246,'ArchViz','ArchViz',0,2,NULL,NULL,NULL,1,'Test description for ArchViz library.',1),(247,'Just Textures','JustTextures',0,0,NULL,NULL,NULL,1,NULL,2),(248,'Furniture','Furniture',246,0,NULL,NULL,NULL,1,NULL,1),(249,'Decor','Decor',246,0,NULL,NULL,NULL,1,NULL,1),(250,'Lights','Lights',246,0,NULL,NULL,NULL,1,NULL,1),(252,'Chairs','Chairs',248,0,NULL,NULL,NULL,1,NULL,1),(253,'Sofas','Sofas',248,0,NULL,NULL,NULL,1,NULL,1),(254,'Tables','Tables',248,0,NULL,NULL,NULL,1,NULL,1),(255,'Test','Test',0,1,NULL,NULL,NULL,1,NULL,1),(257,'Pendant','Pendant',249,0,NULL,NULL,NULL,1,NULL,1);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global`
--

DROP TABLE IF EXISTS `global`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global`
--

LOCK TABLES `global` WRITE;
/*!40000 ALTER TABLE `global` DISABLE KEYS */;
INSERT INTO `global` VALUES ('path','\\\\visco.local\\data\\Library\\');
/*!40000 ALTER TABLE `global` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `models`
--

DROP TABLE IF EXISTS `models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models` (
  `id` int(11) NOT NULL,
  `catid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `preview` int(11) DEFAULT '0',
  `units` varchar(45) DEFAULT NULL,
  `dim` varchar(255) DEFAULT NULL,
  `polys` varchar(45) DEFAULT NULL,
  `render` varchar(45) DEFAULT NULL,
  `rigged` int(11) DEFAULT '0',
  `uvw` int(11) DEFAULT '0',
  `unwrap` int(11) DEFAULT '0',
  `lods` int(11) DEFAULT '0',
  `projectid` int(11) DEFAULT '0',
  `modeller` varchar(45) DEFAULT NULL,
  `previews` varchar(800) DEFAULT NULL,
  `files` varchar(1000) DEFAULT NULL,
  `tags` varchar(600) DEFAULT NULL,
  `manufacture` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `models`
--

LOCK TABLES `models` WRITE;
/*!40000 ALTER TABLE `models` DISABLE KEYS */;
INSERT INTO `models` VALUES (0,252,'Tabano Chair Leather','2013',1,'Millimeters','923.17 x 968.23 x 1074.48','72 723','Corona',NULL,1,1,0,0,'v.lukyanenko',NULL,NULL,'chair,leather,aluminium,corona','Cassina');
/*!40000 ALTER TABLE `models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'Wind Jammer'),(2,'WBA 18');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES ('aluminium'),('chair'),('cool'),('corona'),('leather'),('light'),('ling'),('lip'),('lister'),('new'),('super');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `token` varchar(400) DEFAULT '',
  `rights` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (0,'e.astafiev','e00309ebe88dbeb6792fe9a7853760a4',2,1),(0,'v.lukyanenko','b7b9962a95ded7ed439e1328ff5facb1',2,1),(0,'v.melnykovych','0c5ffd3bf7584cc17aeee5174f8a9d10',0,1),(0,'y.bozhyk','11fa24b36c7e6eaf0f5ee6cb1aaafaac',0,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-07 17:28:08
