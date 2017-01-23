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
) ENGINE=InnoDB AUTO_INCREMENT=409 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (261,'Evermotion','Evermotion',0,0,NULL,NULL,';',1,'Professional, highly detailed 3d models for architectural visualizations by Evermotion.',1,'m.assachia;a.samson'),(265,'Props And Gadgets','Props-And-Gadgets',261,0,NULL,NULL,NULL,1,NULL,1,NULL),(266,'Home','Home',265,0,NULL,NULL,NULL,1,NULL,1,NULL),(267,'Office','Office',265,0,NULL,NULL,NULL,1,NULL,1,NULL),(268,'Ceramics','Ceramics',265,0,NULL,NULL,NULL,1,NULL,1,NULL),(278,'ArchViz','ArchViz',0,1,NULL,NULL,';',1,'High detailed interrior models',1,'a.samson'),(279,'Furniture','Furniture',278,0,NULL,NULL,NULL,1,NULL,1,NULL),(280,'Decor','Decor',278,0,NULL,NULL,NULL,1,NULL,1,NULL),(281,'Chair','Chair',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(282,'Light','Light',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(283,'Stool','Stool',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(284,'Sofa','Sofa',279,0,NULL,NULL,NULL,1,NULL,1,NULL),(285,'Model Set','Model-Set',278,0,NULL,NULL,NULL,1,NULL,1,NULL),(286,'Kitchen','Kitchen',285,0,NULL,NULL,NULL,1,NULL,1,NULL),(297,'Animals','Animals',261,0,NULL,NULL,NULL,1,NULL,1,NULL),(298,'Fish','Fish',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(299,'Cats','Cats',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(300,'Horses','Horses',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(301,'Dogs','Dogs',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(302,'Birds','Birds',297,0,NULL,NULL,NULL,1,NULL,1,NULL),(303,'Set','Set',280,0,NULL,NULL,NULL,1,NULL,1,NULL),(304,'IKEA','IKEA',0,0,NULL,NULL,NULL,1,NULL,1,NULL),(311,'Lighting','Lighting',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(320,'Appliancess','Appliancess',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(338,'Floor Lamps','Floor-Lamps',311,0,NULL,NULL,NULL,1,NULL,1,NULL),(339,'Table Lamps','Table-Lamps-',311,0,NULL,NULL,NULL,1,NULL,1,NULL),(342,'Fridges','Fridges',320,0,NULL,NULL,NULL,1,NULL,1,NULL),(343,'Washing Machines','Washing-Machines',320,0,NULL,NULL,NULL,1,NULL,1,NULL),(344,'Dishwashers','Dishwashers',320,0,NULL,NULL,NULL,1,NULL,1,NULL),(345,'Extraction Hoods','Extraction-Hoods',320,0,NULL,NULL,NULL,1,NULL,1,NULL),(346,'Gas Hobs','Gas-Hobs',320,0,NULL,NULL,NULL,1,NULL,1,NULL),(347,'Ovens','Ovens',320,0,NULL,NULL,NULL,1,NULL,1,NULL),(348,'Microwave Ovens','Microwave-Ovens',320,0,NULL,NULL,NULL,1,NULL,1,NULL),(351,'Tables','Tables',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(352,'Dinner Tables','Dinner-Tables',351,0,NULL,NULL,NULL,1,NULL,1,NULL),(353,'Desks','Desks',351,0,NULL,NULL,NULL,1,NULL,1,NULL),(354,'Coffe Tables and Side Tables','Coffe-Tables-and-Side-Tables-',351,0,NULL,NULL,NULL,1,NULL,1,NULL),(355,'Kitchen Islands and Troleys','Kitchen-Islands-and-Troleys-',351,0,NULL,NULL,NULL,1,NULL,1,NULL),(356,'Ceilling Lamps','Ceilling-Lamps',311,0,NULL,NULL,NULL,1,NULL,1,NULL),(357,'Wall Lamps','Wall-Lamps',311,0,NULL,NULL,NULL,1,NULL,1,NULL),(358,'Chairs','Chairs',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(359,'Chairs','Chairs',358,0,NULL,NULL,NULL,1,NULL,1,NULL),(360,'Stools','Stools',358,0,NULL,NULL,NULL,1,NULL,1,NULL),(361,'Office Chairs','Office-Chairs',358,0,NULL,NULL,NULL,1,NULL,1,NULL),(362,'Bar Chairs','Bar-Chairs',358,0,NULL,NULL,NULL,1,NULL,1,NULL),(363,'Chair Cowers','Chair-Cowers',358,0,NULL,NULL,NULL,1,NULL,1,NULL),(364,'Lounge and Rocking Chairs','Lounge-and-Rocking-Chairs',358,0,NULL,NULL,NULL,1,NULL,1,NULL),(365,'Underframes','Underframes',351,0,NULL,NULL,NULL,1,NULL,1,NULL),(366,'Cabinets and Chests','Cabinets-and-Chests',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(367,'Chests','Chests',366,0,NULL,NULL,NULL,1,NULL,1,NULL),(368,'TV Benches','TV-Benches',366,0,NULL,NULL,NULL,1,NULL,1,NULL),(369,'Book Shelves','Book-Shelves',366,0,NULL,NULL,NULL,1,NULL,1,NULL),(370,'Shoe Cabinets','Shoe-Cabinets',366,0,NULL,NULL,NULL,1,NULL,1,NULL),(371,'Wall Cabinets','Wall-Cabinets',366,0,NULL,NULL,NULL,1,NULL,1,NULL),(372,'High Cabinets','High-Cabinets',366,0,NULL,NULL,NULL,1,NULL,1,NULL),(373,'Seat Shells','Seat-Shells',358,0,NULL,NULL,NULL,1,NULL,1,NULL),(374,'Beds','Beds',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(375,'Bed Frames','Bed-Frames',374,0,NULL,NULL,NULL,1,NULL,1,NULL),(376,'Mattresses','Mattresses',374,0,NULL,NULL,NULL,1,NULL,1,NULL),(377,'Soft Products','Soft-Products',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(378,'Sofas','Sofas',377,0,NULL,NULL,NULL,1,NULL,1,NULL),(379,'Armchairs','Armchairs',377,0,NULL,NULL,NULL,1,NULL,1,NULL),(380,'Pillows and Chairpads','Pillows-and-Chairpads',377,0,NULL,NULL,NULL,1,NULL,1,NULL),(381,'Plumbing','Plumbing',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(382,'Kitchen Sinks','Kitchen-Sinks',381,0,NULL,NULL,NULL,1,NULL,1,NULL),(384,'Bathroom Sinks','Bathroom-Sinks',381,0,NULL,NULL,NULL,1,NULL,1,NULL),(385,'Kitchen Taps','Kitchen-Taps',381,0,NULL,NULL,NULL,1,NULL,1,NULL),(386,'Bathroom Taps','Bathroom-Taps',381,0,NULL,NULL,NULL,1,NULL,1,NULL),(387,'Cutlery','Cutlery',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(388,'Knives','Knives',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(389,'Forks','Forks',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(390,'Spoons','Spoons',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(391,'Jars and Bottles','Jars-and-Bottles',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(392,'Food Containers','Food-Containers',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(393,'Frying Pans and Casseroles','Frying-Pans-and-Casseroles',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(394,'Cups and Glasses','Cups-and-Glasses',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(395,'Plates','Plates',387,0,NULL,NULL,NULL,1,NULL,1,NULL),(396,'Shades','Shades',311,0,NULL,NULL,NULL,1,NULL,1,NULL),(397,'Accessories','Accessories',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(398,'Kitchen Accessories','Kitchen-Accessories',397,0,NULL,NULL,NULL,1,NULL,1,NULL),(399,'Bathroom Accessories','Bathroom-Accessories',397,0,NULL,NULL,NULL,1,NULL,1,NULL),(400,'Furniture Accessories','Furniture-Accessories',397,0,NULL,NULL,NULL,1,NULL,1,NULL),(401,'Klocks','Klocks',397,0,NULL,NULL,NULL,1,NULL,1,NULL),(402,'Pictures and Photoframes','Pictures-and-Photoframes',397,0,NULL,NULL,NULL,1,NULL,1,NULL),(403,'Storage Boxes','Storage-Boxes',397,0,NULL,NULL,NULL,1,NULL,1,NULL),(404,'Other Accessories','Other-Accessories',397,0,NULL,NULL,NULL,1,NULL,1,NULL),(405,'Outdoor','Outdoor',304,0,NULL,NULL,NULL,1,NULL,1,NULL),(406,'Outdoor Tables','Outdoor-Tables',405,0,NULL,NULL,NULL,1,NULL,1,NULL),(407,'Garden Chairs and Benches','Garden-Chairs-and-Benches',405,0,NULL,NULL,NULL,1,NULL,1,NULL),(408,'Baby Cots','Baby-Cots',374,0,NULL,NULL,NULL,1,NULL,1,NULL);
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
  `custom1` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `models`
--

LOCK TABLES `models` WRITE;
/*!40000 ALTER TABLE `models` DISABLE KEYS */;
INSERT INTO `models` VALUES (84,1,266,'Interior Props 16 am155','2013','1','Centimeters','42.54 x 42.5 x 45.13','1 337 691','VRay',NULL,NULL,'1',NULL,'Evermotion','Evermotion','266-Interior-Props-16-am155-VRay-0',0,'cd155,evermotion,vray,flower,box,wood,bucket,painted,steel,','Evermotion','Highly detailed 3d models of interior props with all textures, shaders and materials. It is ready to use, just put it into your scene.',1483628271,NULL),(90,1,266,'Interior Props 09 am155','2013','1','Centimeters','42.77 x 30.52 x 10.57','41 936','VRay',NULL,NULL,'1',NULL,'Evermotion','Evermotion','266-Interior-Props-09-am155-VRay-0',0,'cd155,evermotion,vray,book,pen,pyramid,glasses,','Evermotion','Highly detailed 3d models of interior props with all textures, shaders and materials. It is ready to use, just put it into your scene.',1483628349,NULL),(91,1,266,'Interior Props 08 am155','2013','1','Centimeters','31.73 x 32.25 x 32.07','273 326','VRay',NULL,NULL,'1',NULL,'Evermotion','Evermotion','266-Interior-Props-08-am155-VRay-0',0,'cd155,evermotion,owl,books,bronze,statuette,','Evermotion','Highly detailed 3d models of interior props with all textures, shaders and materials. It is ready to use, just put it into your scene.',1483628434,NULL),(92,1,298,'aquarium 01 am83','2013','1','Meters','0.25 x 0.25 x 0.26','151 520','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-01-am83-VRay-0',0,'fish,gold,aquarium,interior,props,glass,cd83,','Evermotion',NULL,1483631016,NULL),(93,1,298,'aquarium 02 am83','2013','1','Meters','0.3 x 0.75 x 0.5','195 956','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-02-am83-VRay-0',0,'aquarium,fish,props,cd83,interior,glass,','Evermotion',NULL,1483631042,NULL),(94,1,298,'aquarium 03 am83','2013','1','Millimeters','629.54 x 1243.56 x 1819.38','190 053','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-03-am83-VRay-0',0,'fish,aquarium,props,cd83,','Evermotion',NULL,1483631086,NULL),(96,1,298,'aquarium 04 am83','2013','1','Millimeters','519.02 x 519.02 x 2171.31','118 758','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','298-aquarium-04-am83-VRay-0',0,'fish,aquarium,interior,props,cd83,','Evermotion','about 21 meters height',1483631517,NULL),(97,1,302,'Canary cage 05 am83','2013','1','Millimeters','210.53 x 263.16 x 546.5','18 799','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','302-canary-cage-05-am83-VRay-0',0,'bird,pets,pet,cage,wood,cd83,canaries,','Evermotion',NULL,1483632936,NULL),(98,1,302,'Parrot 06 am83','2013','1','Millimeters','406.29 x 406.29 x 661.72','49 312','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','302-parrot-06-am83-VRay-0',0,'bird,cage,pets,pet,metal,cd83,parrot,','Evermotion',NULL,1483632955,NULL),(100,1,302,'Pigeon 08 am83','2013','1','Millimeters','252.62 x 80.73 x 193.92','3 512','VRay','',NULL,'1','','N/A','Evermotion','302-pigeon-08-am83-VRay-0',0,'bird,wild,cd83,pigeons,','Evermotion','',1483635974,NULL),(101,1,302,'Parrots 07 am83','2013','1','Millimeters','422.04 x 422.04 x 1807.15','47 604','VRay',NULL,NULL,NULL,NULL,'N/A','Evermotion','302-Parrots-07-am83-VRay-0',0,'bird,pet,pets,metal,gold,cage,cd83,','Evermotion',NULL,1483639951,NULL),(102,1,302,'Pigeon 09 am83','2013','1','Millimeters','473.13 x 235.39 x 197.56','4 959','VRay',NULL,NULL,'1',NULL,'N/A','Evermotion','302-Pigeon-09-am83-VRay-0;302-Pigeon-09-am83-VRay-1',0,'bird,wild,cd83,pigeons,','Evermotion',NULL,1483971311,NULL),(103,1,302,'Seagull 15 am83','2013','1','Millimeters','627.19 x 327.55 x 231.18','8 480','VRay',NULL,NULL,'1',NULL,'N/A','Evermotion','302-Seagull-15-am83-VRay-0;302-Seagull-15-am83-VRay-1',0,'bird,wild,sea,cd83,seagulls,','Evermotion',NULL,1483971316,NULL),(104,1,302,'Pigeon 10 am83','2013','1','Millimeters','463.64 x 330.64 x 223.95','4 960','VRay',NULL,NULL,'1',NULL,'N/A','Evermotion','302-Pigeon-10-am83-VRay-0;302-Pigeon-10-am83-VRay-1',0,'bird,wild,cd83,pigeons,','Evermotion',NULL,1483971676,NULL),(105,1,302,'Pigeon 11 am83','2013','1','Millimeters','99.21 x 310.44 x 238.31','3 512','VRay',NULL,NULL,'1',NULL,'N/A','Evermotion','302-Pigeon-11-am83-VRay-0;302-Pigeon-11-am83-VRay-1',0,'bird,wild,cd83,pigeons,','Evermotion',NULL,1483972102,NULL),(106,1,302,'Pigeon 12 am83','2013','1','Millimeters','571.31 x 284.23 x 238.56','5 004','VRay',NULL,NULL,'1',NULL,'N/A','Evermotion','302-Pigeon-12-am83-VRay-0;302-Pigeon-12-am83-VRay-1',0,'bird,wild,cd83,pigeons,','Evermotion',NULL,1483972410,NULL),(107,0,302,'Pigeon 13 am83','2013','1','Millimeters','555.18 x 365.28 x 292.33','4 962','VRay',NULL,NULL,'1',NULL,'N/A','Evermotion','302-Pigeon-13-am83-VRay-0;302-Pigeon-13-am83-VRay-1',1,'bird,wild,pigeons,cd83,','Evermotion',NULL,1483973240,NULL),(108,0,302,'Seagull 14 am83','2013','1','Millimeters','99.56 x 275.36 x 232.64','10 336','VRay',NULL,NULL,'1',NULL,'N/A','Evermotion','302-Seagull-14-am83-VRay-0;302-Seagull-14-am83-VRay-1',1,'bird,wild,sea,seagulls,cd83,','Evermotion',NULL,1483973544,NULL),(109,0,302,'Seagull 16 am83','2013','1','Millimeters','600.68 x 403.08 x 214.49','8 480','VRay',NULL,NULL,'1',NULL,'N/A','evermotion','302-Seagull-16-am83-VRay-0;302-Seagull-16-am83-VRay-1',1,'bird,seagulls,sea,cd83,','evermotion',NULL,1483981325,NULL),(110,0,302,'Swan 17 am83','2013','1','Millimeters','464.11 x 1178.66 x 1128.53','17 864','VRay',NULL,NULL,'1',NULL,'N/A','evermotion','302-Swan-17-am83-VRay-0;302-Swan-17-am83-VRay-1',1,'swans,bird,wild,cd83,','evermotion',NULL,1483983205,NULL),(111,0,302,'Cygnet 18 am83','2013','1','Millimeters','262.62 x 143.25 x 250.41','40 658','VRay',NULL,NULL,NULL,NULL,'N/A','evermotion','302-Cygnet-18-am83-VRay-0;302-Cygnet-18-am83-VRay-1',1,'swans,young,wild,cd83,bird,','evermotion','Fur made with quad polygons.',1483985541,NULL),(115,0,385,'SUNDSVIK kit mix chrome-plated','2011','1','Millimeters','52.64 x 268.5 x 188.91','43 106','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','385-SUNDSVIK-kit-mix-chrome-plated-VRay-0;385-SUNDSVIK-kit-mix-chrome-plated-VRay-1',1,'tap,chrome,sink,kitchen,','IKEA',NULL,1484664094,'40244162'),(116,0,399,'SVARTSJON soap dish turquoise','2011','1','Millimeters','128.0 x 87.91 x 18.99','81 118','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','399-SVARTSJON-soap-dish-turquoise-VRay-0;399-SVARTSJON-soap-dish-turquoise-VRay-1',1,'bathroom,soapdish,plastic,transparent,','IKEA',NULL,1484668590,'00264388'),(117,0,345,'POTENTIELL wall-mounted extraction hood','2011','1','Millimeters','596.52 x 318.21 x 597.99','83 537','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','345-POTENTIELL-wll-mount-extr-hood-VRay-0;345-POTENTIELL-wll-mount-extr-hood-VRay-1',1,'extractionhood,black,gloss,plastic,appliancess,kitchen,','IKEA',NULL,1484733237,'30244704'),(118,0,372,'IKEA PS corner cabinet','2011','1','Millimeters','465.22 x 465.22 x 1101.98','68 586','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','372-IKEA-PS-corner-cabinet-VRay-0;372-IKEA-PS-corner-cabinet-VRay-1',1,'cabinet,white,corner,livingroom,shelving,storage,','IKEA',NULL,1484734922,'00260696'),(119,0,375,'SUNDVIK extendable frame black-brown','2011','1','Millimeters','914.0 x 1372.96 x 802.0','20 568','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','375-SUNDVIK-bed-ext-80x200-black-brown-VRay-0;375-SUNDVIK-bed-ext-80x200-black-brown-VRay-1',1,'bed,bedroom,wood,pine,','IKEA',NULL,1484736363,'40251685'),(120,0,352,'INGATORP extendable white','2011','1','Millimeters','1100.01 x 1099.99 x 743.03','185 116','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','352-INGATORP-ext-tbl-white-VRay-0;352-INGATORP-ext-tbl-white-VRay-1',1,'round,wood,white,lacquer,diningroom,','IKEA',NULL,1484740058,'40217069'),(121,0,376,'MORGEDAL foam matt TW dark grey','2011','1','Millimeters','974.64 x 1899.01 x 178.35','808 193','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','376-MORGEDAL-foam-matt-TW-dark-grey-VRay-0;376-MORGEDAL-foam-matt-TW-dark-grey-VRay-1',1,'bedroom,soft,foam,white,grey,','IKEA',NULL,1484741686,'90277391'),(122,0,403,'PINGLA box pink','2011','1','Millimeters','369.82 x 278.17 x 181.0','29 390','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','403-PINGLA-box-w-lid-28x37x18-pink-VRay-0;403-PINGLA-box-w-lid-28x37x18-pink-VRay-1',1,'paper,pink,storage,','IKEA',NULL,1484744629,'50243195'),(123,0,361,'SKRUVSTA Majviken multicolour','2011','1','Millimeters','718.82 x 692.36 x 811.44','186 976','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','361-SKRUVSTA-swivel-chrair-multicolour-VRay-0;361-SKRUVSTA-swivel-chrair-multicolour-VRay-1',1,'office,swivel,colorfull,triangles,fabric,','IKEA',NULL,1484748071,'60278641'),(124,0,404,'DANKA N ironing board','2011','1','Millimeters','1405.04 x 440.22 x 891.49','840 358','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','404-DANKA-N-ironing-board-VRay-0;404-DANKA-N-ironing-board-VRay-1',1,'textile,fabric,metal,','IKEA',NULL,1484749553,'00263727'),(125,0,354,'IKEA PS 2014 storage table multicolour','2011','1','Millimeters','439.75 x 439.74 x 451.99','149 604','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','354-IKEA-PS-2014-storage-table-multicolour-VRay-0;354-IKEA-PS-2014-storage-table-multicolour-VRay-1',1,'plastic,round,colorfull,storage,livingroom,','IKEA',NULL,1484752537,'70263998'),(126,0,364,'FLAXIG rocking-chair black','2011','1','Millimeters','521.97 x 796.77 x 645.68','815 224','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','364-FLAXIG-rocking-chair-black-VRay-0;364-FLAXIG-rocking-chair-black-VRay-1',1,'relax,black,textile,fabric,plastic,metal,','IKEA',NULL,1484821237,'00254011'),(128,0,346,'LIVSGNISTA gas hob glass black','2011','1','Millimeters','590.02 x 510.02 x 94.8','153 004','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','346-LIVSGNISTA-gas-hob-glass-black-VRay-0;346-LIVSGNISTA-gas-hob-glass-black-VRay-1',1,'kitchen,black,glass,gloss,cooking,','IKEA',NULL,1484825542,'80273609'),(129,0,378,'TIMSFORS Corner sofa Mjuk-kimstad black','2011','1','Millimeters','2598.0 x 2596.28 x 909.68','3 104 096','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','378-TIMSFORS-Corner-sofa-Mjuk-kimstad-black-VRay-0;378-TIMSFORS-Corner-sofa-Mjuk-kimstad-black-VRay-1',1,'livingroom,relax,lether,artificial,soft,','IKEA',NULL,1484836776,'70272889'),(130,0,384,'DOMSJO sink bowl','2011','1','Millimeters','624.0 x 669.71 x 230.0','30 314','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','384-DOMSJO-sink-bowl-VRay-0;384-DOMSJO-sink-bowl-VRay-1',1,'bathroom,white,porcelain,','IKEA',NULL,1484836963,'39158179'),(131,0,393,'KASTRULL pot with lid 3 l green','2011','1','Millimeters','297.44 x 215.76 x 175.53','20 648','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','393-KASTRULL-pot-with-lid-3-l-green-VRay-0;393-KASTRULL-pot-with-lid-3-l-green-VRay-1',1,'kitchen,cookware,steel,enamel,','IKEA',NULL,1484837931,'30232956'),(132,0,364,'IKEA PS LOMSK N swivel chair','2011','1','Millimeters','590.78 x 614.34 x 801.77','468 645','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','364-IKEA-PS-LOMSK-N-swivel-chair-VRay-0;364-IKEA-PS-LOMSK-N-swivel-chair-VRay-1',1,'lounge,relax,blue,plastic,children,play,','IKEA',NULL,1484900972,'30264216'),(133,0,354,'ARKELSTORP window table','2011','1','Millimeters','800.0 x 400.0 x 750.0','192 141','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','354-ARKELSTORP-window-table-VRay-0;354-ARKELSTORP-window-table-VRay-1',1,'kitcken,green,wood,','IKEA',NULL,1484908205,'60260815'),(134,0,392,'SMASKA lunch box','2011','1','Millimeters','361.69 x 236.45 x 61.73','28 651','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','392-SMASKA-lunch-box-VRay-0;392-SMASKA-lunch-box-VRay-1',1,'food,kitchen,plastic,orange,','IKEA',NULL,1484909701,'20160403'),(135,0,367,'ASKVOLL chest of 3 drawers','2011','1','Millimeters','702.0 x 413.94 x 685.0','120 453','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','367-ASKVOLL-chest-of-3-drawers-VRay-0;367-ASKVOLL-chest-of-3-drawers-VRay-1',1,'livingroom,wood,oak,white,','IKEA',NULL,1484915514,'20270802'),(136,0,408,'GULLIVER N cot','2011','1','Millimeters','1236.52 x 660.0 x 794.98','64 772','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','408-GULLIVER-N-cot-VRay-0;408-GULLIVER-N-cot-VRay-1',1,'bedroom,children,white,wood,','IKEA',NULL,1484918702,'10248519'),(137,0,348,'LAGAN microwave oven','2011','1','Millimeters','760.2 x 449.67 x 442.43','190 178','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','348-LAGAN-microwave-oven-VRay-0;348-LAGAN-microwave-oven-VRay-1',1,'kitchen,plastic,white,buttons,figures,kooking,','IKEA',NULL,1484926273,'00288916'),(138,0,363,'HENRIKSDAL chair cover Rutna','2011','1','Millimeters','524.06 x 585.41 x 622.07','163 952','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','363-HENRIKSDAL-chair-cover-Rutna-VRay-0;363-HENRIKSDAL-chair-cover-Rutna-VRay-1',1,'livingroom,fabric,textile,blue,','IKEA',NULL,1484928946,'40268563'),(139,0,303,'Teapot','2013','1','Meters','99.87 x 59.79 x 40.18','1 024','VRay','1',NULL,'1','1','N/A','N/A','303-Teapot-VRay-0;303-Teapot-VRay-1',1,'sea,seagulls,teapots,','N/A',NULL,1485159154,NULL),(140,0,346,'BETRODD double oven with gas hob','2011','1','Millimeters','760.14 x 723.13 x 1199.48','587 816','VRay',NULL,NULL,'1',NULL,'IKEA','IKEA','346-BETRODD-double-oven-with-gas-hob-VRay-0;346-BETRODD-double-oven-with-gas-hob-VRay-1',1,'kitchen,cooking,steel,grid,','IKEA',NULL,1485185264,'40288561');
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
INSERT INTO `tags` VALUES ('aluminium'),('appliancess'),('aquarium'),('artificial'),('bathroom'),('bed'),('bedroom'),('bird'),('black'),('blue'),('book'),('books'),('box'),('bronze'),('bucket'),('buttons'),('cabinet'),('cage'),('cashmere'),('cd155'),('cd83'),('chair'),('children'),('chrome'),('cloth'),('colorfull'),('cooking'),('cookware'),('corner'),('corona'),('diningroom'),('enamel'),('evermotion'),('extractionhood'),('fabric'),('figures'),('fish'),('flower'),('foam'),('food'),('glass'),('glasses'),('gloss'),('gold'),('green'),('grey'),('grid'),('interior'),('kitchen'),('kitcken'),('kooking'),('lacquer'),('leather'),('lether'),('livingroom'),('lounge'),('metal'),('oak'),('office'),('orange'),('owl'),('painted'),('paper'),('pen'),('pet'),('pets'),('pigeons'),('pine'),('pink'),('plastic'),('play'),('porcelain'),('props'),('pyramid'),('relax'),('rest'),('round'),('sea'),('seagulls'),('set'),('shelving'),('sink'),('soapdish'),('sofa'),('soft'),('statuette'),('steel'),('stool'),('storage'),('swans'),('swivel'),('tabano'),('table'),('tap'),('teapots'),('textile'),('transparent'),('triangles'),('vray'),('white'),('wild'),('wood'),('young');
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (31,'y.bozhyk','6de36f5e65b25258882dafa61d50e486',2,1,'Lviv','Yuriy Bojyk','Sightline'),(32,'v.lukyanenko','d6ac4a8d0671934d3f0720b7443018ce',2,1,'Lviv','Vasiliy Lukyanenko','SoftDev_Lviv'),(33,'m.assachia','e403f36dc74608c3aad205b42c1c4b6a',1,1,'Lviv','Mykola Assachia','IT'),(34,'e.astafiev','3acdde9275c5d101d54bb9bfe321f450',2,1,'Lviv','Eugeniy Astafiev','Sightline'),(35,'a.samson','aacfcde2e2f2521632fdcd4fd7815a89',1,1,'Lviv','Anatoliy Samson','Sightline'),(36,'a.pavlyuk','9513f3d65c540a759cd02afe00408288',2,1,'Lviv','Andriy Pavlyuk','Ikea'),(37,'i.sereda','fe62ef2ec4a5ecfb26fcbb6bba3fc8ff',0,1,'Lviv','Igor Sereda','SoftDev_Lviv');
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

-- Dump completed on 2017-01-23 17:30:02
