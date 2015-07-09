-- MySQL dump 10.13  Distrib 5.5.43, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: fcstimesheets
-- ------------------------------------------------------
-- Server version	5.5.43-0ubuntu0.14.10.1

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
-- Table structure for table `attachment`
--

DROP TABLE IF EXISTS `attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_table` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `modifier_user_id` bigint(20) DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mimetype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `fullpath` text COLLATE utf8_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `type_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachment`
--

LOCK TABLES `attachment` WRITE;
/*!40000 ALTER TABLE `attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachment_type`
--

DROP TABLE IF EXISTS `attachment_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachment_type` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachment_type`
--

LOCK TABLES `attachment_type` WRITE;
/*!40000 ALTER TABLE `attachment_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachment_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit`
--

DROP TABLE IF EXISTS `audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dt_created` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `submodule` text COLLATE utf8_unicode_ci,
  `message` text COLLATE utf8_unicode_ci,
  `module` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `db_class` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `db_action` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `db_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit`
--

LOCK TABLES `audit` WRITE;
/*!40000 ALTER TABLE `audit` DISABLE KEYS */;
INSERT INTO `audit` VALUES (77,'2015-05-28 11:20:01',NULL,NULL,NULL,'auth','login','/auth/login','127.0.0.1',NULL,NULL,NULL),(78,'2015-05-28 11:20:07',NULL,NULL,NULL,'auth','login','/auth/login','127.0.0.1',NULL,NULL,NULL),(79,'2015-05-28 11:20:07',1,NULL,NULL,'main','index','/main/index','127.0.0.1',NULL,NULL,NULL),(80,'2015-05-28 11:20:14',1,NULL,NULL,'admin','users','/admin/users','127.0.0.1',NULL,NULL,NULL);
/*!40000 ALTER TABLE `audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channel`
--

DROP TABLE IF EXISTS `channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notify_user_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notify_user_id` bigint(20) DEFAULT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `do_processing` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channel`
--

LOCK TABLES `channel` WRITE;
/*!40000 ALTER TABLE `channel` DISABLE KEYS */;
/*!40000 ALTER TABLE `channel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channel_email_option`
--

DROP TABLE IF EXISTS `channel_email_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_email_option` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(20) NOT NULL,
  `server` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `s_username` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s_password` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `use_auth` tinyint(4) NOT NULL DEFAULT '1',
  `folder` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `protocol` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cc_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_read_action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_read_parameter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channel_email_option`
--

LOCK TABLES `channel_email_option` WRITE;
/*!40000 ALTER TABLE `channel_email_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `channel_email_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channel_message`
--

DROP TABLE IF EXISTS `channel_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(20) NOT NULL,
  `message_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT '0',
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channel_message`
--

LOCK TABLES `channel_message` WRITE;
/*!40000 ALTER TABLE `channel_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `channel_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channel_message_status`
--

DROP TABLE IF EXISTS `channel_message_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_message_status` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) NOT NULL,
  `processor_id` bigint(20) NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `is_successful` tinyint(1) NOT NULL DEFAULT '0',
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channel_message_status`
--

LOCK TABLES `channel_message_status` WRITE;
/*!40000 ALTER TABLE `channel_message_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `channel_message_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channel_processor`
--

DROP TABLE IF EXISTS `channel_processor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_processor` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `channel_id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `processor_settings` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channel_processor`
--

LOCK TABLES `channel_processor` WRITE;
/*!40000 ALTER TABLE `channel_processor` DISABLE KEYS */;
/*!40000 ALTER TABLE `channel_processor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `obj_table` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `obj_id` bigint(20) DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `is_internal` tinyint(4) NOT NULL DEFAULT '0',
  `is_system` tinyint(4) NOT NULL DEFAULT '0',
  `creator_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `othername` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `homephone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `workphone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `priv_mobile` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `dt_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `private_to_user_id` bigint(20) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,'Administrator','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin@tripleacs.com',NULL,'2012-04-26 20:31:52','0000-00-00 00:00:00',0,NULL,NULL);
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `example_data`
--

DROP TABLE IF EXISTS `example_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `example_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `data` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `dt_modified` datetime NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `example_data`
--

LOCK TABLES `example_data` WRITE;
/*!40000 ALTER TABLE `example_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `example_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_user`
--

DROP TABLE IF EXISTS `group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `dt_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_user`
--

LOCK TABLES `group_user` WRITE;
/*!40000 ALTER TABLE `group_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inbox`
--

DROP TABLE IF EXISTS `inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inbox` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_read` datetime DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT '1',
  `dt_archived` datetime DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `parent_message_id` int(11) DEFAULT NULL,
  `has_parent` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `del_forever` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inbox`
--

LOCK TABLES `inbox` WRITE;
/*!40000 ALTER TABLE `inbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inbox_message`
--

DROP TABLE IF EXISTS `inbox_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inbox_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `digest` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inbox_message`
--

LOCK TABLES `inbox_message` WRITE;
/*!40000 ALTER TABLE `inbox_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `inbox_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lookup`
--

DROP TABLE IF EXISTS `lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lookup` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `weight` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lookup`
--

LOCK TABLES `lookup` WRITE;
/*!40000 ALTER TABLE `lookup` DISABLE KEYS */;
/*!40000 ALTER TABLE `lookup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `object_history`
--

DROP TABLE IF EXISTS `object_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `object_history`
--

LOCK TABLES `object_history` WRITE;
/*!40000 ALTER TABLE `object_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `object_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `object_history_entry`
--

DROP TABLE IF EXISTS `object_history_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_history_entry` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `history_id` bigint(20) NOT NULL,
  `attr_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attr_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `object_history_entry`
--

LOCK TABLES `object_history_entry` WRITE;
/*!40000 ALTER TABLE `object_history_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `object_history_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `object_index`
--

DROP TABLE IF EXISTS `object_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_index` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `object_index_content` (`content`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `object_index`
--

LOCK TABLES `object_index` WRITE;
/*!40000 ALTER TABLE `object_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `object_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `object_modification`
--

DROP TABLE IF EXISTS `object_modification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_modification` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `object_modification`
--

LOCK TABLES `object_modification` WRITE;
/*!40000 ALTER TABLE `object_modification` DISABLE KEYS */;
/*!40000 ALTER TABLE `object_modification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `printer`
--

DROP TABLE IF EXISTS `printer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `printer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) NOT NULL,
  `server` varchar(512) NOT NULL,
  `port` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `printer`
--

LOCK TABLES `printer` WRITE;
/*!40000 ALTER TABLE `printer` DISABLE KEYS */;
/*!40000 ALTER TABLE `printer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_connection_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `report_code` text COLLATE utf8_unicode_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `sqltype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
INSERT INTO `report` VALUES (1,NULL,'Audit','admin','','[[dt_from||date||Date From]]\r\n\r\n[[dt_to||date||Date To]]\r\n\r\n[[user_id||select||User||select u.id as value, concat(c.firstname,\' \',c.lastname) as title from user u, contact c where u.contact_id = c.id order by title]]\r\n\r\n[[module||select||Module||select distinct module as value, module as title from audit order by module asc]]\r\n\r\n[[action||select||Action||select distinct action as value, concat(module,\'/\',action) as title from audit order by title]]\r\n\r\n@@Audit Report||\r\n\r\nselect \r\na.dt_created as Date, \r\nconcat(c.firstname,\' \',c.lastname) as User,  \r\na.module as Module,\r\na.path as Url,\r\na.db_class as \'Class\',\r\na.db_action as \'Action\',\r\na.db_id as \'DB Id\'\r\n\r\nfrom audit a\r\n\r\nleft join user u on u.id = a.creator_id\r\nleft join contact c on c.id = u.contact_id\r\n\r\nwhere \r\na.dt_created >= \'{{dt_from}} 00:00:00\' \r\nand a.dt_created <= \'{{dt_to}} 23:59:59\' \r\nand (\'{{module}}\' = \'\' or a.module = \'{{module}}\')\r\nand (\'{{action}}\' = \'\' or a.action = \'{{action}}\') \r\nand (\'{{user_id}}\' = \'\' or a.creator_id = \'{{user_id}}\')\r\n\r\n@@\r\n',1,0,'Show Audit Information','select'),(2,NULL,'Contacts','admin','','@@Contacts||\r\nselect * from contact\r\n@@',0,0,'','select');
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_connection`
--

DROP TABLE IF EXISTS `report_connection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_connection` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `db_driver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `db_host` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `db_port` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `db_database` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `db_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s_db_user` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s_db_password` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_connection`
--

LOCK TABLES `report_connection` WRITE;
/*!40000 ALTER TABLE `report_connection` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_connection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_feed`
--

DROP TABLE IF EXISTS `report_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `report_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `dt_created` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_feed`
--

LOCK TABLES `report_feed` WRITE;
/*!40000 ALTER TABLE `report_feed` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_feed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_member`
--

DROP TABLE IF EXISTS `report_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_member`
--

LOCK TABLES `report_member` WRITE;
/*!40000 ALTER TABLE `report_member` DISABLE KEYS */;
INSERT INTO `report_member` VALUES (1,1,1,'OWNER',0),(2,2,1,'OWNER',0);
/*!40000 ALTER TABLE `report_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_template`
--

DROP TABLE IF EXISTS `report_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `template_id` bigint(20) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_updated` datetime DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_template`
--

LOCK TABLES `report_template` WRITE;
/*!40000 ALTER TABLE `report_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_session`
--

DROP TABLE IF EXISTS `rest_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rest_session` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `token` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_session`
--

LOCK TABLES `rest_session` WRITE;
/*!40000 ALTER TABLE `rest_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `rest_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `session_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `session_data` text COLLATE utf8_unicode_ci NOT NULL,
  `expires` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `task_group_id` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `task_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assignee_id` int(11) NOT NULL,
  `dt_assigned` datetime NOT NULL,
  `dt_first_assigned` datetime NOT NULL,
  `first_assignee_id` int(11) NOT NULL,
  `dt_completed` datetime DEFAULT NULL,
  `dt_planned` datetime DEFAULT NULL,
  `dt_due` datetime DEFAULT NULL,
  `estimate_hours` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `latitude` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_data`
--

DROP TABLE IF EXISTS `task_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `data_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_data`
--

LOCK TABLES `task_data` WRITE;
/*!40000 ALTER TABLE `task_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_group`
--

DROP TABLE IF EXISTS `task_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `can_assign` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `can_view` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `can_create` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `task_group_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `default_assignee_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_group`
--

LOCK TABLES `task_group` WRITE;
/*!40000 ALTER TABLE `task_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_group_member`
--

DROP TABLE IF EXISTS `task_group_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_group_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_group_id` int(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `priority` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_group_member`
--

LOCK TABLES `task_group_member` WRITE;
/*!40000 ALTER TABLE `task_group_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_group_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_group_notify`
--

DROP TABLE IF EXISTS `task_group_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_group_notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_group_id` int(11) NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_group_notify`
--

LOCK TABLES `task_group_notify` WRITE;
/*!40000 ALTER TABLE `task_group_notify` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_group_notify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_group_user_notify`
--

DROP TABLE IF EXISTS `task_group_user_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_group_user_notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_group_id` int(11) NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` tinyint(1) DEFAULT '0',
  `task_creation` tinyint(1) NOT NULL DEFAULT '0',
  `task_details` tinyint(1) NOT NULL DEFAULT '0',
  `task_comments` tinyint(1) NOT NULL DEFAULT '0',
  `time_log` tinyint(1) NOT NULL DEFAULT '0',
  `task_documents` tinyint(1) NOT NULL DEFAULT '0',
  `task_pages` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_group_user_notify`
--

LOCK TABLES `task_group_user_notify` WRITE;
/*!40000 ALTER TABLE `task_group_user_notify` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_group_user_notify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_object`
--

DROP TABLE IF EXISTS `task_object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_object`
--

LOCK TABLES `task_object` WRITE;
/*!40000 ALTER TABLE `task_object` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_object` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_time`
--

DROP TABLE IF EXISTS `task_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `dt_created` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `dt_start` datetime NOT NULL,
  `dt_end` datetime NOT NULL,
  `comment_id` int(11) NOT NULL,
  `is_suspect` tinyint(4) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `time_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_time`
--

LOCK TABLES `task_time` WRITE;
/*!40000 ALTER TABLE `task_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_user_notify`
--

DROP TABLE IF EXISTS `task_user_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_user_notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `task_creation` tinyint(1) NOT NULL DEFAULT '0',
  `task_details` tinyint(1) NOT NULL DEFAULT '0',
  `task_comments` tinyint(1) NOT NULL DEFAULT '0',
  `time_log` tinyint(1) NOT NULL DEFAULT '0',
  `task_documents` tinyint(1) NOT NULL DEFAULT '0',
  `task_pages` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_user_notify`
--

LOCK TABLES `task_user_notify` WRITE;
/*!40000 ALTER TABLE `task_user_notify` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_user_notify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template`
--

DROP TABLE IF EXISTS `template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_title` text COLLATE utf8_unicode_ci,
  `template_body` longtext COLLATE utf8_unicode_ci,
  `test_title_json` text COLLATE utf8_unicode_ci,
  `test_body_json` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template`
--

LOCK TABLES `template` WRITE;
/*!40000 ALTER TABLE `template` DISABLE KEYS */;
/*!40000 ALTER TABLE `template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_id` bigint(20) DEFAULT NULL,
  `password_reset_token` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `dt_password_reset_at` timestamp NULL DEFAULT NULL,
  `redirect_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'main/index',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_group` tinyint(4) NOT NULL DEFAULT '0',
  `dt_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_lastlogin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','ca1e51f19afbe6e0fb51dde5bcf01ab73e52c7cd','9b618fbc7f9509fc28ebea98becfdd58',1,'',NULL,'main/index',1,1,0,0,'2012-04-26 20:31:07','2015-05-28 11:20:07');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_per_user` (`user_id`,`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widget_config`
--

DROP TABLE IF EXISTS `widget_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `destination_module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `source_module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `widget_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `custom_config` text COLLATE utf8_unicode_ci,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widget_config`
--

LOCK TABLES `widget_config` WRITE;
/*!40000 ALTER TABLE `widget_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `widget_config` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-05-28 11:21:32
