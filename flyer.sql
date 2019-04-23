-- MySQL dump 10.16  Distrib 10.3.7-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: flyer
-- ------------------------------------------------------
-- Server version	10.3.7-MariaDB

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
-- Table structure for table `check`
--

DROP TABLE IF EXISTS `check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_name` varchar(100) NOT NULL DEFAULT '' COMMENT '审核人',
  `u_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '审核人IP',
  `time` char(11) NOT NULL DEFAULT '' COMMENT '审核时间',
  `type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '审核类型',
  `hd_id` int(11) NOT NULL DEFAULT 0 COMMENT '活动ID',
  `u_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `u_name` varchar(100) NOT NULL DEFAULT '' COMMENT '被审核用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `check`
--

LOCK TABLES `check` WRITE;
/*!40000 ALTER TABLE `check` DISABLE KEYS */;
/*!40000 ALTER TABLE `check` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `huodong`
--

DROP TABLE IF EXISTS `huodong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `huodong` (
  `hd_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `hd_name` char(40) NOT NULL,
  `hd_time1` int(10) unsigned NOT NULL,
  `hd_time2` int(10) unsigned NOT NULL,
  `hd_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `hd_intro` text NOT NULL,
  `hd_rules` text NOT NULL,
  `hd_logo` char(120) NOT NULL,
  `hd_time` int(10) unsigned NOT NULL,
  `hd_zd_names` text NOT NULL,
  `hd_zd_pys` text NOT NULL,
  `hd_zd_types` text NOT NULL,
  `hd_time_limit` varchar(32) NOT NULL DEFAULT '0' COMMENT '申请限制时长',
  `hd_zd_vals` text NOT NULL,
  `hd_index` mediumint(9) NOT NULL DEFAULT 1000,
  `hd_time_update` enum('0','1','2') NOT NULL DEFAULT '1' COMMENT '是否刷新申请时间',
  PRIMARY KEY (`hd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `huodong`
--

LOCK TABLES `huodong` WRITE;
/*!40000 ALTER TABLE `huodong` DISABLE KEYS */;
/*!40000 ALTER TABLE `huodong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `wx_num` text NOT NULL,
  `img_url` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info`
--

LOCK TABLES `info` WRITE;
/*!40000 ALTER TABLE `info` DISABLE KEYS */;
INSERT INTO `info` VALUES (1,'ccx78248','images/1555840975photo_2019-04-19_12-59-00.jpg'),(2,'A16881788A','images/1555840988photo_2019-04-19_12-59-26.jpg'),(3,'l136916146','images/1555841002photo_2019-04-19_12-57-47.jpg');
/*!40000 ALTER TABLE `info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msg`
--

DROP TABLE IF EXISTS `msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `msg` (
  `gg_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `gg_content` text NOT NULL,
  `gg_time` int(10) unsigned NOT NULL,
  `gg_status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`gg_id`),
  KEY `gg_status` (`gg_status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `msg`
--

LOCK TABLES `msg` WRITE;
/*!40000 ALTER TABLE `msg` DISABLE KEYS */;
/*!40000 ALTER TABLE `msg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `update_time`
--

DROP TABLE IF EXISTS `update_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_time` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `time` varchar(16) DEFAULT '000000' COMMENT '刷新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `update_time`
--

LOCK TABLES `update_time` WRITE;
/*!40000 ALTER TABLE `update_time` DISABLE KEYS */;
INSERT INTO `update_time` VALUES (1,'000000');
/*!40000 ALTER TABLE `update_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `u_id` smallint(4) NOT NULL AUTO_INCREMENT,
  `u_name` char(30) NOT NULL,
  `password` char(32) NOT NULL,
  `u_true_name` char(20) NOT NULL,
  `u_type` tinyint(1) NOT NULL DEFAULT 0,
  `u_date` int(10) NOT NULL,
  `u_lastlogin` int(10) NOT NULL,
  `u_ip` char(20) NOT NULL,
  `u_status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','be6c513e84c21cfdcce570a0ded1d266','',1,1346314527,1555989695,'116.93.12.50',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-04-23 15:36:58
