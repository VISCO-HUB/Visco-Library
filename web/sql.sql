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
  `premissions` varchar(900) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `description` varchar(400) DEFAULT NULL,
  `type` int(11) DEFAULT '0',
  `editors` varchar(900) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (261,'Evermotion','Evermotion',0,1,NULL,NULL,';',1,'Professional, highly detailed 3d models for architectural visualizations by Evermotion.',1,'y.bozhyk;'),(265,'Props And Gadgets','Props-And-Gadgets',261,0,NULL,NULL,NULL,1,NULL,1,NULL),(266,'Home','Home',265,0,NULL,NULL,NULL,1,NULL,1,NULL),(267,'Office','Office',265,0,NULL,NULL,NULL,1,NULL,1,NULL),(268,'Ceramics','Ceramics',265,0,NULL,NULL,NULL,1,NULL,1,NULL),(278,'ArchViz','ArchViz',0,0,NULL,NULL,';',1,'High detailed interrior models',1,';'),(279,'Furniture','Furniture',278,0,NULL,NULL,NULL,1,NULL,1,NULL),(280,'Decor','Decor',278,0,NULL,NULL,NULL,1,NULL,1,NULL),(281,'Chair','Chair',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(282,'Light','Light',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(283,'Stool','Stool',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(284,'Sofa','Sofa',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(285,'Model Set','Model-Set',278,0,NULL,NULL,NULL,1,NULL,1,NULL),(286,'Kitchen','Kitchen',285,0,NULL,NULL,NULL,1,NULL,1,NULL),(297,'Animals','Animals',261,0,NULL,NULL,NULL,1,NULL,1,NULL),(298,'Fish','Fish',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(299,'Cats','Cats',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(300,'Horses','Horses',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(301,'Dogs','Dogs',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(302,'Birds','Birds',297,0,NULL,NULL,NULL,1,NULL,1,NULL);
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) DEFAULT '0',
  `catid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `preview` varchar(45) DEFAULT NULL,
  `units` varchar(45) DEFAULT NULL,
  `dim` varchar(255) DEFAULT NULL,
  `polys` varchar(45) DEFAULT NULL,
  `render` varchar(45) DEFAULT NULL,
  `rigged` varchar(45) DEFAULT NULL,
  `baked` varchar(45) DEFAULT NULL,
  `unwrap` varchar(45) DEFAULT NULL,
  `lods` varchar(45) DEFAULT NULL,
  `project` varchar(45) DEFAULT NULL,
  `modeller` varchar(45) DEFAULT NULL,
  `previews` varchar(1200) DEFAULT NULL,
  `pending` int(11) DEFAULT '1',
  `tags` varchar(600) DEFAULT NULL,
  `manufacturer` varchar(200) DEFAULT NULL,
  `overview` varchar(1000) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `models`
--

LOCK TABLES `models` WRITE;
/*!40000 ALTER TABLE `models` DISABLE KEYS */;
INSERT INTO `models` VALUES (84,1,266,'Interior Props 16 am155','2013','1','Centimeters','42.54 x 42.5 x 45.13','1 337 691','VRay',NULL,NULL,'1',NULL,'Evermotion','Evermotion','266-Interior-Props-16-am155-VRay-0',0,'cd155,evermotion,vray,flower,box,wood,bucket,painted,steel,','Evermotion','Highly detailed 3d models of interior props with all textures, shaders and materials. It is ready to use, just put it into your scene.',1483628271),(90,1,266,'Interior Props 09 am155','2013','1','Centimeters','42.77 x 30.52 x 10.57','41 936','VRay',NULL,NULL,'1',NULL,'Evermotion','Evermotion','266-Interior-Props-09-am155-VRay-0',0,'cd155,evermotion,vray,book,pen,pyramid,glasses,','Evermotion','Highly detailed 3d models of interior props with all textures, shaders and materials. It is ready to use, just put it into your scene.',1483628349),(91,1,266,'Interior Props 08 am155','2013','1','Centimeters','31.73 x 32.25 x 32.07','273 326','VRay',NULL,NULL,'1',NULL,'Evermotion','Evermotion','266-Interior-Props-08-am155-VRay-0',0,'cd155,evermotion,owl,books,bronze,statuette,','Evermotion','Highly detailed 3d models of interior props with all textures, shaders and materials. It is ready to use, just put it into your scene.',1483628434),(92,1,298,'aquarium 01 am83','2013','1','Meters','0.25 x 0.25 x 0.26','151 520','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-01-am83-VRay-0',0,'fish,gold,aquarium,interior,props,glass,cd83,','Evermotion',NULL,1483631016),(93,1,298,'aquarium 02 am83','2013','1','Meters','0.3 x 0.75 x 0.5','195 956','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-02-am83-VRay-0',0,'aquarium,fish,props,cd83,interior,glass,','Evermotion',NULL,1483631042),(94,1,298,'aquarium 03 am83','2013','1','Millimeters','629.54 x 1243.56 x 1819.38','190 053','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-03-am83-VRay-0',0,'fish,aquarium,props,cd83,','Evermotion',NULL,1483631086),(96,1,298,'aquarium 04 am83','2013','1','Millimeters','519.02 x 519.02 x 2171.31','118 758','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-04-am83-VRay-0',0,'fish,aquarium,interior,props,cd83,','Evermotion','about 21 meters height',1483631517),(97,1,302,'Canary cage 05 am83','2013','1','Millimeters','210.53 x 263.16 x 546.5','18 799','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','302-canary-cage-05-am83-VRay-0',0,'bird,pets,pet,cage,wood,cd83,canaries,','Evermotion',NULL,1483632936),(98,1,302,'Parrot 06 am83','2013','1','Millimeters','406.29 x 406.29 x 661.72','49 312','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','302-parrot-06-am83-VRay-0',0,'bird,cage,pets,pet,metal,cd83,parrot,','Evermotion',NULL,1483632955),(100,1,302,'Pigeon 08 am83','2013','1','Millimeters','252.62 x 80.73 x 193.92','3 512','VRay','',NULL,'1','','N/A','Evermotion','302-pigeon-08-am83-VRay-0',0,'bird,wild,cd83,','Evermotion','',1483635974),(101,1,302,'Parrots 07 am83','2013','1','Millimeters','422.04 x 422.04 x 1807.15','47 604','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','302-Parrots-07-am83-VRay-0',0,'bird,pet,pets,metal,gold,cage,cd83,','Evermotion',NULL,1483639951);
/*!40000 ALTER TABLE `models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES ('WBA 18'),('Wind Jammer');
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
INSERT INTO `tags` VALUES ('aquarium'),('bird'),('book'),('books'),('box'),('bronze'),('bucket'),('cage'),('cashmere'),('cd155'),('cd83'),('chair'),('chrome'),('cloth'),('evermotion'),('fish'),('flower'),('glass'),('glasses'),('gold'),('interior'),('kitchen'),('leather'),('metal'),('owl'),('painted'),('pen'),('pet'),('pets'),('props'),('pyramid'),('rest'),('set'),('sofa'),('statuette'),('steel'),('stool'),('tabano'),('table'),('vray'),('white'),('wild'),('wood');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `textures`
--

DROP TABLE IF EXISTS `textures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `textures` (
  `id` int(11) NOT NULL,
  `tags` varchar(900) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `textures`
--

LOCK TABLES `textures` WRITE;
/*!40000 ALTER TABLE `textures` DISABLE KEYS */;
/*!40000 ALTER TABLE `textures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(45) NOT NULL DEFAULT '',
  `token` varchar(400) DEFAULT '',
  `rights` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `office` varchar(45) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `grp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`,`user`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (31,'y.bozhyk','625b966b4a1c01b9ad28687f776a2a45',1,1,'Lviv','Yuriy Bojyk','Sightline'),(32,'v.lukyanenko','ed576d0be4e91ef46b2bb5af285ebdc5',2,1,'Lviv','Vasiliy Lukyanenko','SoftDev_Lviv'),(33,'m.assachia','e403f36dc74608c3aad205b42c1c4b6a',1,1,'Lviv','Mykola Assachia','IT'),(34,'e.astafiev','2b05c90e90f5fd3d8e92a6fe7c17c66a',2,1,'Lviv','Eugeniy Astafiev','Sightline'),(35,'a.samson','aacfcde2e2f2521632fdcd4fd7815a89',1,1,'Lviv','Anatoliy Samson','Sightline');
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

-- Dump completed on 2017-01-05 19:20:45
