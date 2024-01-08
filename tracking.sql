-- MySQL dump 10.14  Distrib 5.5.68-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: tracking_web
-- ------------------------------------------------------
-- Server version	5.5.68-MariaDB

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
-- Table structure for table `alert_device`
--

DROP TABLE IF EXISTS `alert_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_device` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` int(10) unsigned NOT NULL,
  `device_id` int(10) unsigned NOT NULL,
  `overspeed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alert_device_alert_id_index` (`alert_id`),
  KEY `alert_device_device_id_index` (`device_id`),
  CONSTRAINT `alert_device_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alert_device_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_device`
--

LOCK TABLES `alert_device` WRITE;
/*!40000 ALTER TABLE `alert_device` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_driver_pivot`
--

DROP TABLE IF EXISTS `alert_driver_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_driver_pivot` (
  `alert_id` int(10) unsigned NOT NULL,
  `driver_id` int(10) unsigned NOT NULL,
  KEY `alert_driver_pivot_alert_id_index` (`alert_id`),
  KEY `alert_driver_pivot_driver_id_index` (`driver_id`),
  CONSTRAINT `alert_driver_pivot_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alert_driver_pivot_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `user_drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_driver_pivot`
--

LOCK TABLES `alert_driver_pivot` WRITE;
/*!40000 ALTER TABLE `alert_driver_pivot` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_driver_pivot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_event_pivot`
--

DROP TABLE IF EXISTS `alert_event_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_event_pivot` (
  `alert_id` int(10) unsigned NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  KEY `alert_event_pivot_alert_id_index` (`alert_id`),
  KEY `alert_event_pivot_event_id_index` (`event_id`),
  CONSTRAINT `alert_event_pivot_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alert_event_pivot_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events_custom` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_event_pivot`
--

LOCK TABLES `alert_event_pivot` WRITE;
/*!40000 ALTER TABLE `alert_event_pivot` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_event_pivot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_fuel_consumption`
--

DROP TABLE IF EXISTS `alert_fuel_consumption`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_fuel_consumption` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` int(10) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `fuel_type` tinyint(4) NOT NULL,
  `from` date NOT NULL,
  `to` date NOT NULL,
  `done` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `alert_fuel_consumption_alert_id_foreign` (`alert_id`),
  CONSTRAINT `alert_fuel_consumption_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_fuel_consumption`
--

LOCK TABLES `alert_fuel_consumption` WRITE;
/*!40000 ALTER TABLE `alert_fuel_consumption` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_fuel_consumption` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_geofence`
--

DROP TABLE IF EXISTS `alert_geofence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_geofence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zone` tinyint(4) NOT NULL,
  `alert_id` int(10) unsigned NOT NULL,
  `geofence_id` int(10) unsigned NOT NULL,
  `time_from` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00:00',
  `time_to` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00:00',
  PRIMARY KEY (`id`),
  KEY `alert_geofence_zone_index` (`zone`),
  KEY `alert_geofence_alert_id_index` (`alert_id`),
  KEY `alert_geofence_geofence_id_index` (`geofence_id`),
  CONSTRAINT `alert_geofence_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alert_geofence_geofence_id_foreign` FOREIGN KEY (`geofence_id`) REFERENCES `geofences` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_geofence`
--

LOCK TABLES `alert_geofence` WRITE;
/*!40000 ALTER TABLE `alert_geofence` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_geofence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alerts`
--

DROP TABLE IF EXISTS `alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` text COLLATE utf8_unicode_ci,
  `mobile_phone` text COLLATE utf8_unicode_ci,
  `overspeed_speed` int(11) NOT NULL,
  `overspeed_distance` tinyint(4) NOT NULL,
  `ac_alarm` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `alerts_user_id_foreign` (`user_id`),
  KEY `alerts_active_index` (`active`),
  KEY `alerts_ac_alarm_index` (`ac_alarm`),
  CONSTRAINT `alerts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alerts`
--

LOCK TABLES `alerts` WRITE;
/*!40000 ALTER TABLE `alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_plan_permissions`
--

DROP TABLE IF EXISTS `billing_plan_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_plan_permissions` (
  `plan_id` int(10) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `remove` tinyint(1) NOT NULL DEFAULT '0',
  KEY `billing_plan_permissions_plan_id_index` (`plan_id`),
  CONSTRAINT `billing_plan_permissions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `billing_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_plan_permissions`
--

LOCK TABLES `billing_plan_permissions` WRITE;
/*!40000 ALTER TABLE `billing_plan_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_plan_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_plans`
--

DROP TABLE IF EXISTS `billing_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_plans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double(8,2) unsigned NOT NULL,
  `objects` int(10) unsigned NOT NULL,
  `duration_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `duration_value` int(10) unsigned NOT NULL,
  `days` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_plans`
--

LOCK TABLES `billing_plans` WRITE;
/*!40000 ALTER TABLE `billing_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configs`
--

DROP TABLE IF EXISTS `configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configs`
--

LOCK TABLES `configs` WRITE;
/*!40000 ALTER TABLE `configs` DISABLE KEYS */;
INSERT INTO `configs` VALUES (1,'alerts_last_check','1652442182','0000-00-00 00:00:00','2022-05-13 09:43:02'),(2,'email','a:10:{s:9:\"from_name\";s:37:\"Inner Fleet - Fleet Tracking Platform\";s:13:\"noreply_email\";s:36:\"notify-gps@dubaipropertylistings.com\";s:15:\"use_smtp_server\";s:1:\"1\";s:16:\"smtp_server_host\";s:25:\"dubaipropertylistings.com\";s:16:\"smtp_server_port\";s:2:\"25\";s:13:\"smtp_security\";s:1:\"0\";s:13:\"smtp_username\";s:36:\"notify-gps@dubaipropertylistings.com\";s:13:\"smtp_password\";s:9:\"Notify135\";s:8:\"provider\";s:4:\"smtp\";s:9:\"signature\";s:0:\"\";}','0000-00-00 00:00:00','2022-05-02 19:23:30'),(3,'main_settings','a:41:{s:11:\"server_name\";s:49:\"INNER FLEET TRACKING | Your fleet is safe with us\";s:14:\"available_maps\";a:5:{i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";i:3;s:1:\"3\";}s:16:\"default_language\";s:2:\"en\";s:16:\"default_timezone\";s:2:\"65\";s:19:\"default_date_format\";s:5:\"d-m-Y\";s:19:\"default_time_format\";s:7:\"h:i:s A\";s:24:\"default_unit_of_distance\";s:2:\"km\";s:24:\"default_unit_of_capacity\";s:2:\"lt\";s:24:\"default_unit_of_altitude\";s:2:\"mt\";s:11:\"default_map\";s:1:\"1\";s:29:\"default_object_online_timeout\";s:1:\"1\";s:24:\"allow_users_registration\";b:1;s:13:\"devices_limit\";s:2:\"80\";s:34:\"subscription_expiration_after_days\";s:3:\"365\";s:12:\"enable_plans\";b:0;s:12:\"payment_type\";i:1;s:16:\"paypal_client_id\";s:0:\"\";s:13:\"paypal_secret\";s:0:\"\";s:15:\"paypal_currency\";s:0:\"\";s:19:\"paypal_payment_name\";s:0:\"\";s:20:\"default_billing_plan\";N;s:3:\"dst\";N;s:13:\"dst_date_from\";s:0:\"\";s:11:\"dst_date_to\";s:0:\"\";s:12:\"geocoder_api\";s:6:\"google\";s:7:\"api_key\";s:39:\"AIzaSyCcwn1AgSpjyMqe8qWluL0Du7L7om2np_g\";s:19:\"map_center_latitude\";s:5:\"25.10\";s:20:\"map_center_longitude\";s:5:\"55.20\";s:14:\"map_zoom_level\";s:2:\"10\";s:16:\"user_permissions\";a:9:{s:7:\"devices\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:1;s:6:\"remove\";i:0;}s:6:\"alerts\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:1;s:6:\"remove\";i:0;}s:9:\"geofences\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:1;s:6:\"remove\";i:0;}s:6:\"routes\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:1;s:6:\"remove\";i:0;}s:3:\"poi\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:1;s:6:\"remove\";i:0;}s:11:\"sms_gateway\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:0;s:6:\"remove\";i:0;}s:8:\"protocol\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:0;s:6:\"remove\";i:0;}s:12:\"send_command\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:0;s:6:\"remove\";i:0;}s:7:\"history\";a:3:{s:4:\"view\";i:1;s:4:\"edit\";i:0;s:6:\"remove\";i:0;}}s:22:\"geocoder_cache_enabled\";s:1:\"1\";s:19:\"geocoder_cache_days\";s:2:\"90\";s:14:\"template_color\";s:12:\"light-orange\";s:12:\"welcome_text\";s:6:\"&#8203\";s:11:\"bottom_text\";s:0:\"\";s:16:\"apple_store_link\";s:0:\"\";s:16:\"google_play_link\";s:0:\"\";s:26:\"frontpage_logo_padding_top\";s:1:\"5\";s:7:\"api_url\";s:44:\"https://nominatim.openstreetmap.org/reverse?\";s:27:\"login_page_background_color\";s:7:\"#f0f1f4\";s:21:\"login_page_text_color\";s:7:\"#252839\";}','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'server_version','{\"version_int\":\"190\",\"version\":\"1.90\",\"date\":\"2017-09-08 20:28:57\",\"error\":null}','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'backups','a:0:{}','2017-09-08 20:44:45','2017-09-08 20:44:45'),(6,'db_clear','a:2:{s:6:\"status\";i:0;s:4:\"days\";s:3:\"365\";}','2017-09-08 20:44:45','2018-11-10 02:30:49'),(7,'plugins','a:3:{s:22:\"show_object_info_after\";a:1:{s:6:\"status\";s:1:\"1\";}s:15:\"object_listview\";a:1:{s:6:\"status\";s:1:\"1\";}s:22:\"business_private_drive\";a:2:{s:6:\"status\";s:1:\"1\";s:7:\"options\";a:2:{s:14:\"business_color\";a:1:{s:5:\"value\";s:7:\"#2727ab\";}s:13:\"private_color\";a:1:{s:5:\"value\";s:7:\"#ff7a00\";}}}}','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_fuel_measurements`
--

DROP TABLE IF EXISTS `device_fuel_measurements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_fuel_measurements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fuel_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `distance_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lang` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `device_fuel_measurements_lang_index` (`lang`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_fuel_measurements`
--

LOCK TABLES `device_fuel_measurements` WRITE;
/*!40000 ALTER TABLE `device_fuel_measurements` DISABLE KEYS */;
INSERT INTO `device_fuel_measurements` VALUES (1,'l/100km','liter','Kilometers','en','2017-09-08 20:28:50','2017-09-08 20:28:50'),(2,'MPG','gallon','Miles','en','2017-09-08 20:28:50','2017-09-08 20:28:50');
/*!40000 ALTER TABLE `device_fuel_measurements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_groups`
--

DROP TABLE IF EXISTS `device_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `device_groups_user_id_index` (`user_id`),
  CONSTRAINT `device_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_groups`
--

LOCK TABLES `device_groups` WRITE;
/*!40000 ALTER TABLE `device_groups` DISABLE KEYS */;
INSERT INTO `device_groups` VALUES (1,3,'Divizioni 1'),(2,3,'Divizioni 2'),(3,3,'Divizoni 3'),(5,3,'Mikrodistribucioni'),(7,3,'Zingjiret'),(12,23,'Divizioni 1'),(13,23,'Divizioni 2'),(14,23,'Divizoni 3'),(15,23,'Mikrodistribucioni'),(16,23,'Zingjiret'),(17,23,'HO-RE-CA'),(18,23,'Shofera'),(19,23,'Servisi'),(20,23,'Administrata'),(21,3,'HO-RE-CA'),(22,3,'Shofera'),(23,3,'Servisi'),(24,3,'Administrata');
/*!40000 ALTER TABLE `device_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_icons`
--

DROP TABLE IF EXISTS `device_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_icons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'icon',
  `order` tinyint(4) DEFAULT NULL,
  `width` double(8,2) NOT NULL,
  `height` double(8,2) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_icons`
--

LOCK TABLES `device_icons` WRITE;
/*!40000 ALTER TABLE `device_icons` DISABLE KEYS */;
INSERT INTO `device_icons` VALUES (0,'arrow',1,25.00,33.00,'assets/img/arrow-ack.png'),(1,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_78.png'),(2,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_05.png'),(3,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_11.png'),(4,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_60.png'),(5,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_38.png'),(6,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_61.png'),(7,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_56.png'),(8,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_77.png'),(9,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_83.png'),(10,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_35.png'),(11,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_17.png'),(12,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_19.png'),(13,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_41.png'),(14,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_79.png'),(15,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_07.png'),(16,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_58.png'),(17,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_40.png'),(18,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_62.png'),(19,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_44.png'),(20,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_09.png'),(21,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_43.png'),(22,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_81.png'),(23,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_63.png'),(24,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_03.png'),(25,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_34.png'),(26,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_33.png'),(27,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_04.png'),(28,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_57.png'),(29,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_76.png'),(30,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_87.png'),(31,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_88.png'),(32,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_55.png'),(33,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_64.png'),(34,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_15.png'),(35,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_82.png'),(36,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_84.png'),(37,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_80.png'),(38,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_42.png'),(39,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_36.png'),(40,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_21.png'),(41,'icon',3,46.00,64.00,'images/device_icons/v2/objects2a_65.png'),(42,'rotating',3,38.00,121.00,'images/device_icons/rotating/94.png'),(43,'rotating',3,37.00,69.00,'images/device_icons/rotating/6.png'),(44,'rotating',3,33.00,87.00,'images/device_icons/rotating/8.png'),(45,'rotating',3,43.00,186.00,'images/device_icons/rotating/93.png'),(46,'rotating',3,26.00,52.00,'images/device_icons/rotating/2.png'),(47,'rotating',3,27.00,49.00,'images/device_icons/rotating/1.png'),(48,'rotating',3,41.00,87.00,'images/device_icons/rotating/9.png'),(49,'rotating',3,42.00,188.00,'images/device_icons/rotating/92.png'),(50,'rotating',3,29.00,51.00,'images/device_icons/rotating/3.png'),(51,'rotating',3,34.00,76.00,'images/device_icons/rotating/91.png'),(52,'rotating',3,38.00,74.00,'images/device_icons/rotating/7.png'),(53,'rotating',3,29.00,47.00,'images/device_icons/rotating/4.png'),(54,'rotating',3,35.00,64.00,'images/device_icons/rotating/5.png');
/*!40000 ALTER TABLE `device_icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_sensors`
--

DROP TABLE IF EXISTS `device_sensors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_sensors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `device_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `tag_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_to_history` tinyint(1) NOT NULL DEFAULT '0',
  `on_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `off_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shown_value_by` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fuel_tank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `full_tank` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `full_tank_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `min_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `formula` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `odometer_value_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `odometer_value` double(13,2) unsigned DEFAULT NULL,
  `odometer_value_unit` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'km',
  `temperature_max` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature_max_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature_min` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature_min_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT '-',
  `value_formula` int(11) NOT NULL DEFAULT '0',
  `show_in_popup` tinyint(1) NOT NULL DEFAULT '0',
  `unit_of_measurement` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `on_tag_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `off_tag_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `on_type` tinyint(4) DEFAULT NULL,
  `off_type` tinyint(4) DEFAULT NULL,
  `calibrations` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `device_sensors_user_id_index` (`user_id`),
  KEY `device_sensors_device_id_index` (`device_id`),
  KEY `device_sensors_type_index` (`type`),
  KEY `device_sensors_tag_name_index` (`tag_name`),
  KEY `device_sensors_add_to_history_index` (`add_to_history`),
  KEY `device_sensors_show_in_popup_index` (`show_in_popup`),
  CONSTRAINT `device_sensors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_sensors`
--

LOCK TABLES `device_sensors` WRITE;
/*!40000 ALTER TABLE `device_sensors` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_sensors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_services`
--

DROP TABLE IF EXISTS `device_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `device_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expiration_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interval` int(11) NOT NULL DEFAULT '1',
  `last_service` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trigger_event_left` int(10) unsigned NOT NULL DEFAULT '0',
  `renew_after_expiration` tinyint(1) NOT NULL DEFAULT '0',
  `expires` double unsigned NOT NULL DEFAULT '0',
  `expires_date` date DEFAULT NULL,
  `remind` double unsigned NOT NULL DEFAULT '0',
  `remind_date` date DEFAULT NULL,
  `event_sent` tinyint(1) NOT NULL DEFAULT '0',
  `expired` tinyint(1) NOT NULL DEFAULT '0',
  `email` text COLLATE utf8_unicode_ci,
  `mobile_phone` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `device_services_user_id_index` (`user_id`),
  KEY `device_services_device_id_index` (`device_id`),
  KEY `device_services_event_sent_index` (`event_sent`),
  KEY `device_services_expired_index` (`expired`),
  CONSTRAINT `device_services_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `device_services_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_services`
--

LOCK TABLES `device_services` WRITE;
/*!40000 ALTER TABLE `device_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `traccar_device_id` bigint(20) NOT NULL,
  `icon_id` int(10) unsigned DEFAULT NULL,
  `icon_colors` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '{"moving":"green","stopped":"yellow","offline":"red","engine":"yellow"}',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imei` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fuel_measurement_id` int(10) unsigned DEFAULT NULL,
  `fuel_quantity` decimal(8,2) NOT NULL,
  `fuel_price` decimal(8,2) NOT NULL,
  `fuel_per_km` decimal(8,2) NOT NULL,
  `sim_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plate_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registration_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_owner` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `additional_notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiration_date` date NOT NULL DEFAULT '0000-00-00',
  `tail_color` char(7) COLLATE utf8_unicode_ci DEFAULT '#33CC33',
  `tail_length` int(11) DEFAULT '5',
  `engine_hours` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'gps',
  `detect_engine` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'gps',
  `min_moving_speed` int(10) unsigned DEFAULT '6',
  `min_fuel_fillings` int(10) unsigned DEFAULT '10',
  `min_fuel_thefts` int(10) unsigned DEFAULT '10',
  `snap_to_road` tinyint(1) NOT NULL DEFAULT '0',
  `gprs_templates_only` tinyint(1) NOT NULL DEFAULT '0',
  `parameters` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `devices_imei_unique` (`imei`),
  KEY `devices_user_id_foreign` (`user_id`),
  KEY `devices_icon_id_foreign` (`icon_id`),
  KEY `devices_fuel_measurement_id_foreign` (`fuel_measurement_id`),
  KEY `devices_traccar_device_id_index` (`traccar_device_id`),
  KEY `devices_active_index` (`active`),
  KEY `devices_deleted_index` (`deleted`),
  CONSTRAINT `devices_fuel_measurement_id_foreign` FOREIGN KEY (`fuel_measurement_id`) REFERENCES `device_fuel_measurements` (`id`) ON DELETE SET NULL,
  CONSTRAINT `devices_icon_id_foreign` FOREIGN KEY (`icon_id`) REFERENCES `device_icons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `devices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=443 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` VALUES (338,NULL,338,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Menduh Shabani(05-435-FI)','354017119812739',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 07:53:52','2022-04-11 03:27:53'),(339,NULL,339,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Shk√´lzen Shabani (05-539-FA)','359633102155210',1,0.00,0.00,0.00,'37744127571','','(05-539-FA)','W0L0XEP05G4237389','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 08:03:26','2022-04-11 03:19:08'),(341,NULL,341,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arian Haxhani(05-591-EA)','354017119812648',1,0.00,0.00,0.00,'37744125798','Opel Corsa S-D','(05-591-EA)','W0LVSDL08E4026390','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 08:13:44','2022-04-11 03:10:05'),(342,NULL,342,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Qendrim Ramadani (05-762-CG)','358480084044827',1,0.00,0.00,0.00,'37744213975','VW Golf V','(05-762-CG)','WVWZZZ1KZ8P125668','','','534.561','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 08:23:14','2022-04-11 03:17:18'),(343,NULL,343,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Idriz Shehu(05-683-EA)','352094085188891',1,0.00,0.00,0.00,'37744408740','Opel Corsa E/VAN (3)','37744408740','W0L0XEP05G4015469','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-02-25 08:24:48','2022-04-11 03:14:54'),(344,NULL,344,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Bujar Gashi(05-537-FA)','359633102540346',1,0.00,0.00,0.00,'37744214766','Opel Corsa E/VAN (3)','(05-537-FA)','W0L0XEP05G4015469','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 08:25:31','2022-04-11 03:11:21'),(345,NULL,345,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arb√´r Vranovci (05-624-DS)','352094088618290',1,0.00,0.00,0.00,'37744102349','Opel Corsa S-D/VAN','(05-624-DS)','W0LVSDL08E4291500','37744102349','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-02-25 08:26:20','2022-02-25 17:11:54'),(346,NULL,346,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Faton Krasni√ßi (05-625-DS)','358480084131533',1,0.00,0.00,0.00,'37744214691','Opel Corsa S-D/VAN','(05-625-DS)','W0LVSD08D4217725','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 08:29:06','2022-04-11 03:13:12'),(347,NULL,347,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arben Aliu(05-536-FA)','359633101844079',1,0.00,0.00,0.00,'37744245033','Opel Corsa E/VAN (4)','(05-536-FA)','W0L0XEP05G4245366','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 08:29:57','2022-04-11 03:09:01'),(348,NULL,348,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Adnan Krasni√ßi(05-192-DS)','352094088848897',1,0.00,0.00,0.00,'37744102257','Opel Corsa S-D/VAN','(05-192-DS)','WOLVSDL08E4289938','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-02-25 13:46:40','2022-04-11 03:07:07'),(349,NULL,349,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Artan Ahmeti (05-853-DK)','352093080625469',1,0.00,0.00,0.00,'37744102248','Opel Corsa S-D/VAN','(05-853-DK)','W0LVSDL08E4056790','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,NULL,'2022-02-25 13:47:12','2022-05-04 06:53:17'),(350,NULL,350,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Adnan Istogu(05-538-FA)','359633101688963',1,0.00,0.00,0.00,'37744215154','Opel Corsa E/VAN (6)','(05-538-FA)','W0L0XEP05F4312556','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 13:47:43','2022-04-11 03:06:54'),(351,NULL,351,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Besjan Sahiti (05-810-DK)','352094084681250',1,0.00,0.00,0.00,'37744410184','Opel Corsa S-D/VAN','37744410184','W0LVSDL08C6047232','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-02-25 13:48:33','2022-02-25 13:48:33'),(352,NULL,352,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Liridon Gabrrica (05-592-EA)','358480084373598',1,0.00,0.00,0.00,'37744101368','Opel Corsa S-D/VAN','(05-592-EA)','W0LVSDL08E4081593','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:27:38','2022-02-25 16:27:38'),(353,NULL,353,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Ardian Shorra (05-674-GB)','350424064868431',1,0.00,0.00,0.00,'37744666198','Opel Corsa E (12)','(05-674-GB)','W0L0XEP05G4215978','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:28:09','2022-02-25 16:28:09'),(354,NULL,354,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Agon Kulludra (05-753-DG)','359632100364477',1,0.00,0.00,0.00,'37744102287','Opel Corsa S-D','(05-753-DG)','W0LVSDL08C6045129','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:28:40','2022-02-25 16:28:40'),(355,NULL,355,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Alban Mustafa (05-914-FQ)352093081741133 ','354017119812879',1,0.00,0.00,0.00,'37744102297','VW Caddy MAX','(05-914-FQ/05-229-CO)','WV1ZZZ2KZCX056088','','730','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\"]','2022-02-25 16:29:17','2022-05-09 04:24:05'),(356,NULL,356,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Meriton Hashani (05-426-FI)','354018115763637',1,0.00,0.00,0.00,'37744247947','Opel Corsa E (8)','(05-426-FI)','W0L0XEP68G4320196','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:32:28','2022-02-25 16:32:35'),(357,NULL,357,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Egzon Hyseni (05-328-DP)','352094084674586',1,0.00,0.00,0.00,'37744372487','Opel Corsa E (11)','W0L0XEP05H4203789','W0L0XEP05H4203789','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-02-25 16:33:03','2022-04-11 03:12:33'),(358,NULL,358,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Drilon Berisha (05-246-DG)','358480084493461',1,0.00,0.00,0.00,'37744245045','Opel Corsa D','(05-246-DG)','WOLOSDL08C6102590','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:33:35','2022-02-25 16:33:35'),(359,NULL,359,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Leotrim Boshnjaku (05-397-FD)','359633102155111',1,0.00,0.00,0.00,'37744252563','Opel Corsa E (9)','(05-397-FD)','W0L0XEP05G4013393','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:34:31','2022-02-25 16:34:31'),(360,NULL,360,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Jeton Bajrami (05-983-CG)','352094088738742',1,0.00,0.00,0.00,'37744103451','VW Golf V','(05-983-CG)','WVWZZZ1KZ9W016965','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-02-25 16:36:04','2022-02-25 16:36:04'),(361,NULL,361,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Orest Hoxha (05-712-BQ)','354017119812853',1,0.00,0.00,0.00,'37744127538','Opel Corsa S-D','(05-712-BQ)','W0LVSDL0894210935','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:36:32','2022-02-25 16:36:32'),(362,NULL,362,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Besfor Krasni√ßi (05-498-DO)','357073293419772',1,0.00,0.00,0.00,'37744666485','Opel Corsa S-D ','(05-498-DO)','WOLOSDL68D4215992','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:37:12','2022-02-25 16:37:12'),(363,NULL,363,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Bleron Koxha (05-439-FI)','358480084478413',1,0.00,0.00,0.00,'37744216023','Opel Corsa D','(05-439-FI)','W0V0XEP68J4076240','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:37:45','2022-02-25 16:37:45'),(364,NULL,364,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arton Bunjaku(05-694-CT)','358480084373846',1,0.00,0.00,0.00,'37744101357','Opel Corsa D','(05-349-FT)','W0LVSDL08C6035557','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:38:18','2022-04-28 10:30:39'),(365,NULL,365,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Shpejtim Vranovci(05-749-GB)','356307044109816',1,0.00,0.00,0.00,'37744104651','Kamion ATEGO 1223 (Autobartesi)','','WDB9702551K341675','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-02-25 16:38:54','2022-04-28 06:07:06'),(366,NULL,366,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Servisi T5 (05-237-GB)','350424064868597',1,0.00,0.00,0.00,'37744777523','VW T5 Transportues','(SK-4922-AF)','WV1ZZZ7HZ4H025494','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-02-25 16:39:22','2022-04-11 03:19:39'),(367,NULL,367,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Astrit Krasniqi (05-278-FO)','359633102155103',1,0.00,0.00,0.00,'44201439','VW Caddy','(05-469-FA)','WV1ZZZ2KZCX111427','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-01 11:14:43','2022-04-11 03:10:35'),(368,NULL,368,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Shkodran Selimi (05-625-FP)','359633102155285',1,0.00,0.00,0.00,'073057299','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\",\"operator\",\"odometer\"]','2022-03-01 11:48:29','2022-04-19 08:43:48'),(369,NULL,369,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Xheladin Zeneli (05-925-EM)','359632100364410',1,0.00,0.00,0.00,'44245013','Mercedes Sprinter 516 CDI','(05-925-EM)','WDB9076551P142988','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 08:59:26','2022-03-03 08:59:26'),(370,NULL,370,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Reshat Shabani (05-924-EM)','358480084131590',1,0.00,0.00,0.00,'44242889','Mercedes Sprinter 515 CDI','44242889','WDB9076551P143625','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 09:00:14','2022-03-03 11:27:48'),(371,NULL,371,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Besnik Lika (05-724-EE)','352094085194337',1,0.00,0.00,0.00,'44215270','Mercedes Sprinter 513 CDI','(05-724-EE)','WDB9066551S908320','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-03-03 09:01:00','2022-03-03 09:01:00'),(372,NULL,372,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Visar Rrahmani (05-571-DD)','358480084480914',1,0.00,0.00,0.00,'44214536','Mercedes Sprinter 516 CDI','(05-571-DD)','WDB9066551S707794','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\"]','2022-03-03 09:02:21','2022-03-03 09:02:21'),(373,NULL,373,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Drilon Tabaku (05-859-ED)','352094085194410',1,0.00,0.00,0.00,'44103436','Mercedes Sprinter 519 CDI','(05-859-ED)','WDB9066551S489548','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-03-03 09:03:35','2022-03-03 09:03:35'),(374,NULL,374,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Faton Bekteshi (05-406-DD)','356307040805474',1,0.00,0.00,0.00,'44101378','Mercedes Sprinter 516 CDI','(05-406-DD)','WDB9066551S662401','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 09:04:05','2022-03-03 09:04:05'),(375,NULL,375,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Edmond Istogu (05-891-CR)','356307046906086',1,0.00,0.00,0.00,'44104652','Mercedes Sprinter 516 CDI','(05-891-CR)','44104652','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 09:06:15','2022-03-03 09:06:15'),(376,NULL,376,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Blerim Tabaku (05-538-DD)','356173062659946',1,0.00,0.00,0.00,'44103455','Mercedes Sprinter 516 CDI','(05-538-DD)','WDB9066571S681555','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 09:07:02','2022-03-03 09:07:02'),(377,NULL,377,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Fadil Guri (05-547-CN)','356173062788752',1,0.00,0.00,0.00,'44397631','Mercedes Sprinter 513 CDI','(05-547-CN)','WDB9066551S438186','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 09:07:48','2022-03-03 09:07:48'),(378,NULL,378,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Hedon Bytyqi (05-306-CT )','356307046779269',1,0.00,0.00,0.00,'44101696','Mercedes Sprinter 516 CDI','(05-306-CT)','WDB9066551S503262','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 09:08:23','2022-05-05 04:45:41'),(379,NULL,379,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Labinot Sejdiu (05-562-CT)','352093081612854',1,0.00,0.00,0.00,'44102279','Mercedes Sprinter 516 CDI','(05-562-CT)','WDB9066551S510236','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 09:08:48','2022-03-03 09:08:48'),(380,NULL,380,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Burim Arifi (05-374-DS)','358480084478397',1,0.00,0.00,0.00,'44246053','Mercedes Sprinter 515 CDI','(05-374-DS)','WDB9066551S356815','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\",\"io252\"]','2022-03-03 09:10:22','2022-03-03 09:10:22'),(381,NULL,381,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Albert Lushi (05-289-CO)','354018115762290',1,0.00,0.00,0.00,'44215301','Mercedes Sprinter 515 CDI','(05-289-CO)','WDB9066551S360183','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 09:10:47','2022-03-03 09:10:47'),(382,NULL,382,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Ahmet Pllana (05-865-CC)','352094084684841',1,0.00,0.00,0.00,'44397468','Mercedes Sprinter 516 CDI','(05-865-CC)','WDB9066551S415691','','','56948','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-03-03 09:11:08','2022-05-05 04:52:51'),(383,NULL,383,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Blerim Azemi (05-153-CH)','356307046776695',1,0.00,0.00,0.00,'44214436','Mercedes Sprinter 515 CDI','(05-153-CH)','WDB9066551S195194','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 09:11:35','2022-03-03 09:11:35'),(384,NULL,384,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Agon Krasniqi (05-860-ED)','352093081017005',1,0.00,0.00,0.00,'44102219','Mercedes Sprinter 515 CDI','(05-860-ED)','WDB9066571S234977','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 09:12:20','2022-03-03 09:12:20'),(385,NULL,385,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Blerim Suka (05-532-DD)','352094084686226',1,0.00,0.00,0.00,'44375954','Mercedes Sprinter 516 CDI','(05-532-DD)','WDB9066551S581502','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-03-03 09:12:41','2022-03-03 09:12:41'),(386,NULL,386,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Kushtrim Sylejmani (05-274-DS0)','352094088615668',1,0.00,0.00,0.00,'44213869','Mercedes Sprinter 513 CDI','(05-274-DS0))','WDB9066551S455839','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-03-03 09:13:12','2022-03-03 09:13:12'),(387,NULL,387,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Besnik Xhokli (05-722-DN)','352094084679957',1,0.00,0.00,0.00,'44213869','Mercedes Sprinter 515 CDI','(05-154-CH)','WDB9066551S146582','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-03-03 09:13:35','2022-04-16 06:54:21'),(388,NULL,388,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'-','356307044665031',1,0.00,0.00,0.00,'44103441','Mercedes Benz Actros 2541','(05-401-FP)','WDB9302041L139539','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,NULL,'2022-03-03 11:33:21','2022-05-06 04:43:47'),(389,NULL,389,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Riza Mustafa(05-463-FQ)','357454073983858',1,0.00,0.00,0.00,'44102282','Mercedes Benz Actros 2541','(05-463-FQ)','WDB9302041L451773','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:33:54','2022-04-28 06:05:54'),(391,NULL,391,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Qazim Sejdiu (05-438-FI)','352094089522798',1,0.00,0.00,0.00,'44215637','Mercedes Sprinter 311 CDI (KL3A4)','(05-278-FO)','W1V9106331P323302','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-03-03 11:34:50','2022-04-11 03:17:03'),(392,NULL,392,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Burim Malsiu (Pickup) (05-279-FO)','354017119812762',1,0.00,0.00,0.00,'44201485','VW Caddy','(05-625-FP)','WV1ZZZ2KZCX097168','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:35:36','2022-05-04 06:34:46'),(393,NULL,393,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Rinor Morina(05-237-DA)','359633102155095',1,0.00,0.00,0.00,'44245017','','(05-237-DA)','ZCFC270C505336779','','','349716','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:36:52','2022-04-19 08:44:12'),(394,NULL,394,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Shukri Rudi (05-428-FA)','359633100431241',1,0.00,0.00,0.00,'44215240','Iveco Dayli (70C17)','(05-563-GB)','ZCFC270C305336778','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:37:16','2022-04-11 03:20:09'),(395,NULL,395,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Mentor Maliqi (05-265-FH)','359633101844004',1,0.00,0.00,0.00,'44201467','Iveco Dayli (70C17)','(05-304-AO)','ZCFC270C605380712','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:38:21','2022-04-11 03:16:08'),(396,NULL,396,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Zufer Selimi (05-563-GB)','350424064868449',1,0.00,0.00,0.00,'44777529','Iveco Dayli (70C17 me frigo)','(05-376-BA/05-921-BG)','ZCFC170C005075256','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\"]','2022-03-03 11:39:00','2022-04-11 03:21:08'),(397,NULL,397,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Afrim Begunca (05-304-AO)','356307041798264',1,0.00,0.00,0.00,'44101368','Iveco Dayli 65c15','(05-649-CR)','ZCFC65A2005675394','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:39:32','2022-04-11 03:07:26'),(398,NULL,398,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'-','352094084683587',1,0.00,0.00,0.00,'44398279','Mercedes Atego 1218','(05-832-AN)','WDB9700551L107566','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,NULL,'2022-03-03 11:40:03','2022-05-06 04:43:58'),(399,NULL,399,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'-','356307046566468',1,0.00,0.00,0.00,'44101720','Mercedes Atego 1218','(05-296-EP)','WDB9700581L314543','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:40:34','2022-05-06 04:44:06'),(400,NULL,400,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'-','356307046566462',1,0.00,0.00,0.00,'44101720','Mercedes Atego 1218','(05-296-EP)','WDB9700581L314543','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,NULL,'2022-03-03 11:41:16','2022-05-06 04:44:16'),(401,NULL,401,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Leonor Brati (05-649-CR)','356307043671196',1,0.00,0.00,0.00,'44101704','Mercedes Benz Atego 1523 ','(05-896-BU)','WDB9702771K565034','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:41:45','2022-05-04 06:36:19'),(402,NULL,402,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Kujtim Aliu (05-614-AU)','359632100366522',1,0.00,0.00,0.00,'44244997','Mercedes Benz Atego 1218','(05-276-BB)','WDB9702551K755066','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\"]','2022-03-03 11:42:11','2022-04-12 09:08:21'),(403,NULL,403,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Shkelzen Deda (05-832-AN)','359632100321592',1,0.00,0.00,0.00,'072019993','Mercedes Benz Atego 1223','(05-349-FT/05-832-AN)','WDB9702571K727341','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\"]','2022-03-03 11:42:32','2022-04-15 05:06:49'),(404,NULL,404,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Gazmend Derguti (05-276-BB)','354018115763819',1,0.00,0.00,0.00,'44201390','Mercedes Benz Atego 1223','(05-276-BB)','WDB9702551K863624','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:42:53','2022-04-15 06:57:59'),(405,NULL,405,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'-','356307045960423',1,0.00,0.00,0.00,'44101728','Mercedes Benz Atego 1318','(05-304-DM)','WDB9700671L544519','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:43:14','2022-05-06 04:44:25'),(406,NULL,406,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Remzi Hashani (05-304-DM)','358480083964306',1,0.00,0.00,0.00,'44245037','Mercedes Sprinter 515 CDI','(05-685-DA)','WDB9066531S375712','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:43:37','2022-04-11 03:17:39'),(407,NULL,407,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Xhelal Koxha (05-968-GB)','356307044260874',1,0.00,0.00,0.00,'44102256','Mercedes Sprinter 516 CDI (Furgon Frigo)','(05-873-FQ)','WDB9066551S452735','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:44:17','2022-04-11 03:20:57'),(408,NULL,408,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Taulant Teneqja (05-685-DA)','354018115762340',1,0.00,0.00,0.00,'44201240','Mercedes Sprinter 315 CDI','(05-397-DS)','WDF9066331A993804','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:44:39','2022-05-04 06:38:26'),(409,NULL,409,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Faton Krasni√ßi (Shofer) (05-873-FQ)','357454070316268',1,0.00,0.00,0.00,'44201520','Mercedes Sprinter 316 CDI','(05-594-CR)','WDB9066331S919592','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:45:11','2022-04-16 07:09:07'),(410,NULL,410,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Mynir Gashi (05-397-DS)','358480084493354',1,0.00,0.00,0.00,'44102275','Mercedes Sprinter 515 CDI','(05-683-DS)','WDB9066531S366555','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:45:49','2022-04-11 03:16:29'),(411,NULL,411,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arben Hoxha (05-594-CR)','356307045082475',1,0.00,0.00,0.00,'44104655','Mercedes Sprinter 516 CDI','(05-623-DS)','WDB9066551S431482','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:46:26','2022-04-11 03:09:32'),(412,NULL,412,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Kushtrim Selimi (05-683-DS)','356307040609660',1,0.00,0.00,0.00,'44101712','Mercedes Sprinter 519 CDI','(05-682-DS)','WDB9066531S469601','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:47:13','2022-05-04 06:39:11'),(413,NULL,413,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'-','356307044979085',1,0.00,0.00,0.00,'44102250','Mercedes Vito (109 CDI) Frigo','(05-681-DS)','WDF63960313526207','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:47:34','2022-05-06 04:44:45'),(414,NULL,414,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Muhamet Gajtani (05-682-DS)','356307046546445',1,0.00,0.00,0.00,'44101364','Mercedes Vito 111','(05-621-DS)','WDF63960113360882','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"io250\",\"distance\",\"totalDistance\",\"motion\"]','2022-03-03 11:47:56','2022-05-05 04:48:15'),(415,NULL,415,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arlind Krasniqi (05-681-DS)','352094088615775',1,0.00,0.00,0.00,'44216033','Mercedes Vito 639/4','(05-722-DN)','WDF63960113702755','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"gpsStatus\"]','2022-03-03 11:48:17','2022-04-11 03:10:18'),(416,NULL,416,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Feriz Beqa (05-376-BA)','359632100364394',1,0.00,0.00,0.00,'44201432','Mercedes Vito 111 CDI (639)','(05-749-DN)','WDF63960113264709','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\",\"gpsStatus\"]','2022-03-03 11:50:07','2022-04-11 03:13:39'),(418,NULL,418,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Agon Thaqi (05-368-CN)','352094086820351',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-04-11 03:08:05','2022-04-16 06:44:35'),(419,NULL,419,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Albi Koxha (05-984-CG)','352094084539185',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-04-11 03:08:49','2022-04-16 06:43:31'),(420,NULL,420,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arben Baliu (05-719-GB)','357073298257748',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\"]','2022-04-11 03:09:15','2022-04-16 06:43:41'),(421,NULL,421,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Arben Rexhepi (05-834-EA)','357073298014735',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\"]','2022-04-11 03:09:44','2022-04-16 06:44:10'),(422,NULL,422,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Donik Berisha (05-397-FA)','359633100389381',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\"]','2022-04-11 03:11:59','2022-04-16 06:44:48'),(423,NULL,423,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Fatlum Muharremi (05-219-DG)','356173062731471',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,NULL,'2022-04-11 03:12:48','2022-04-16 06:44:25'),(424,NULL,424,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Fisnik Xhemajli (05-968-CR)','352094084678926',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-04-11 03:13:52','2022-04-16 06:42:54'),(425,NULL,425,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Florim Sahiti(05-436-FI)','354017119812895',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\"]','2022-04-11 03:14:05','2022-04-16 06:43:18'),(426,NULL,426,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Granit Kerceli (05-613-FA)','357073298257821',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"io251\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"io247\",\"io246\"]','2022-04-11 03:14:42','2022-04-16 08:35:36'),(427,NULL,427,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Sadik Krasniqi (05-427-FI)','354017119812556',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\"]','2022-04-11 03:18:36','2022-04-16 06:43:07'),(428,NULL,428,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Riad Derguti (05-592-FO)','352094086109763',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-04-15 05:49:18','2022-05-09 16:55:12'),(429,NULL,429,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'No Name','356307042151976',1,0.00,0.00,0.00,'37744101705','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"distance\",\"totalDistance\",\"motion\"]','2022-04-16 09:46:54','2022-04-16 09:48:23'),(430,NULL,430,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Vahedin Topalli (05-896-BU)e','356307046793575',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"distance\",\"totalDistance\",\"motion\"]','2022-04-16 10:22:29','2022-05-06 04:46:19'),(431,NULL,431,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Naser Guri (05-623-DS)s','356307046763966',1,0.00,0.00,0.00,'37744248029','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"di2\",\"di3\",\"out1\",\"out2\",\"io69\",\"rssi\",\"io24\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"motion\"]','2022-04-16 10:52:12','2022-05-06 04:46:06'),(432,NULL,432,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Adnan Haliti (05-401-FP)','350424064868514',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\"]','2022-04-28 05:39:48','2022-05-06 04:45:46'),(433,NULL,433,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Burim Malsiu (05-349-FT)','359632100367843',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"distance\",\"totalDistance\",\"hours\"]','2022-04-28 06:45:21','2022-05-04 06:37:30'),(434,NULL,434,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'No name','357073298257839',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\"]','2022-05-01 08:41:26','2022-05-01 08:41:26'),(435,NULL,435,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Ford Focus Transit Connect','357544371720505',1,6.00,1.69,0.17,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"io252\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\",\"io37\",\"io48\",\"io255\",\"io60\",\"io247\"]','2022-05-01 08:49:32','2022-05-03 16:53:33'),(436,NULL,436,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'No Name 2','357073298014743',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"io252\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"io247\"]','2022-05-01 09:30:54','2022-05-01 09:30:54'),(437,NULL,437,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'No Name 3','357073298257870',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"io252\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"io247\"]','2022-05-01 09:39:22','2022-05-01 09:39:22'),(438,NULL,438,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'No Name 4','357073298257904',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"io252\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"io247\"]','2022-05-01 09:56:33','2022-05-01 09:56:33'),(439,NULL,439,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'No Name 5','350424064864349',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"io252\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"io247\"]','2022-05-01 10:07:36','2022-05-01 10:07:36'),(440,NULL,440,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'Faton Sejdiu (05-796-GB)-','357073298140803',1,0.00,0.00,0.00,'044666513','Mercedes Atego 1218','','WDB9700581L314543','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"ignition\",\"motion\",\"workMode\",\"rssi\",\"io200\",\"io69\",\"pdop\",\"hdop\",\"power\",\"io24\",\"battery\",\"io68\",\"distance\",\"totalDistance\",\"hours\"]','2022-05-05 04:25:26','2022-05-10 11:18:55'),(441,NULL,441,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'No Name ( Dauti )','352094084945515',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"io69\",\"motion\",\"rssi\",\"hdop\",\"power\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\"]','2022-05-12 05:03:58','2022-05-12 05:04:33'),(442,NULL,442,0,'{\"moving\":\"green\",\"stopped\":\"yellow\",\"offline\":\"red\",\"engine\":\"yellow\",\"idle\":\"yellow\"}',1,0,'-','356307042976711',1,0.00,0.00,0.00,'','','','','','','','0000-00-00','#33cc33',5,'gps','gps',6,10,10,0,0,'[\"priority\",\"sat\",\"event\",\"di1\",\"di2\",\"di3\",\"out1\",\"out2\",\"io69\",\"rssi\",\"io24\",\"tripOdometer\",\"operator\",\"distance\",\"totalDistance\",\"motion\"]','2022-05-12 05:19:45','2022-05-12 05:19:45');
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES (1,'event','Alert from Tracking Server: [device] [event]','Hello,<br><br>An alert has been created by your vehicle. Below is the description of the event:<br><br><b>Alert Message:</b>&nbsp;[event]<br><b>Geofence:</b>&nbsp;[geofence]<br><b>Vehicle Name:</b>&nbsp;[device]<br><b>Address:</b>&nbsp;[address]<br><b>Position:</b>&nbsp;[position]<br><b>Altitude:</b>&nbsp;[altitude]<br><b>Speed:</b>&nbsp;[speed]<br><b>Time:</b>&nbsp;[time]<br><br>Click the link below to see on Google Maps:<br><a href=\"https://www.google.com/maps?q=[position]\" target=\"_blank\" rel=\"nofollow\">https://www.google.com/maps?q=[position]</a>&nbsp;<br><br>','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'service_expiration','Service expiration','Hello, device service is about to expire.<br><br>Device: [device]<br>Service: [service]<br>Expiration date: [expiration_date]','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'report','Report \"[name]\"','Hello,<br><br>Name: [name]<br>Period: [period]','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'service_expired','Service expired','Hello, device service is expired.<br><br>Device: [device]<br>Service: [service]','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'registration','Registration confirmation','Hello,<br><br>Thank you for registering, here\'s your login information:<br>Email: [email]<br>Password: [password]','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_custom_tags`
--

DROP TABLE IF EXISTS `event_custom_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_custom_tags` (
  `event_custom_id` int(10) unsigned NOT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  KEY `event_custom_tags_event_custom_id_index` (`event_custom_id`),
  KEY `event_custom_tags_tag_index` (`tag`),
  CONSTRAINT `event_custom_tags_event_custom_id_foreign` FOREIGN KEY (`event_custom_id`) REFERENCES `events_custom` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_custom_tags`
--

LOCK TABLES `event_custom_tags` WRITE;
/*!40000 ALTER TABLE `event_custom_tags` DISABLE KEYS */;
INSERT INTO `event_custom_tags` VALUES (7,'ignition');
/*!40000 ALTER TABLE `event_custom_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `device_id` int(10) unsigned DEFAULT NULL,
  `geofence_id` int(10) unsigned DEFAULT NULL,
  `position_id` int(10) unsigned DEFAULT NULL,
  `alert_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `altitude` double DEFAULT NULL,
  `course` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `power` double DEFAULT NULL,
  `speed` double DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `events_user_id_foreign` (`user_id`),
  KEY `events_device_id_foreign` (`device_id`),
  KEY `events_geofence_id_foreign` (`geofence_id`),
  KEY `events_alert_id_foreign` (`alert_id`),
  KEY `events_deleted_index` (`deleted`),
  KEY `events_created_at_index` (`created_at`),
  CONSTRAINT `events_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_geofence_id_foreign` FOREIGN KEY (`geofence_id`) REFERENCES `geofences` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_custom`
--

DROP TABLE IF EXISTS `events_custom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_custom` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `protocol` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `conditions` text COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `always` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `events_custom_always_index` (`always`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_custom`
--

LOCK TABLES `events_custom` WRITE;
/*!40000 ALTER TABLE `events_custom` DISABLE KEYS */;
INSERT INTO `events_custom` VALUES (7,3,'teltonika','a:1:{i:0;a:3:{s:3:\"tag\";s:8:\"ignition\";s:4:\"type\";s:1:\"1\";s:9:\"tag_value\";s:4:\"true\";}}','IGN',0);
/*!40000 ALTER TABLE `events_custom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_queue`
--

DROP TABLE IF EXISTS `events_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `device_id` int(10) unsigned NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_queue_user_id_index` (`user_id`),
  KEY `events_queue_device_id_index` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_queue`
--

LOCK TABLES `events_queue` WRITE;
/*!40000 ALTER TABLE `events_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `events_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fcm_tokens`
--

DROP TABLE IF EXISTS `fcm_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fcm_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `fcm_tokens_user_id_index` (`user_id`),
  CONSTRAINT `fcm_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcm_tokens`
--

LOCK TABLES `fcm_tokens` WRITE;
/*!40000 ALTER TABLE `fcm_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcm_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geofence_groups`
--

DROP TABLE IF EXISTS `geofence_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geofence_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `geofence_groups_user_id_index` (`user_id`),
  CONSTRAINT `geofence_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geofence_groups`
--

LOCK TABLES `geofence_groups` WRITE;
/*!40000 ALTER TABLE `geofence_groups` DISABLE KEYS */;
INSERT INTO `geofence_groups` VALUES (1,23,'Divizioni I'),(2,23,'Divizioni II'),(3,23,'Divizioni III'),(4,23,'Mikrodistribucion'),(5,23,'Zingjir');
/*!40000 ALTER TABLE `geofence_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geofences`
--

DROP TABLE IF EXISTS `geofences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geofences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `coordinates` text COLLATE utf8_unicode_ci NOT NULL,
  `polygon` polygon DEFAULT NULL,
  `polygon_color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `geofences_user_id_index` (`user_id`),
  KEY `geofences_group_id_index` (`group_id`),
  KEY `geofences_active_index` (`active`),
  CONSTRAINT `geofences_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `geofence_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `geofences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geofences`
--

LOCK TABLES `geofences` WRITE;
/*!40000 ALTER TABLE `geofences` DISABLE KEYS */;
INSERT INTO `geofences` VALUES (14,3,NULL,1,'Besfort  Krasniqi Sht√´pi','[{\"lat\":42.294095121235,\"lng\":21.226781130099},{\"lat\":42.294363361588,\"lng\":21.226845503115},{\"lat\":42.294461769113,\"lng\":21.226987123555},{\"lat\":42.294103057359,\"lng\":21.227306843066}]','\0\0\0\0\0\0\0\0\0\0\0\0\0\nœØË§%E@ T:5@ûîŸ≤≠%E@§å:5@û!ZÏ∞%E@&H\0‘:5@YÅB+•%E@ »0:5@\nœØË§%E@ T:5@','#b3f1b7','2022-02-09 05:25:49','2022-02-09 05:25:49'),(15,23,NULL,1,'Viva Fresh Store','[{\"lat\":42.645623718837,\"lng\":21.168808937073},{\"lat\":42.646760110346,\"lng\":21.167221069336},{\"lat\":42.646791676481,\"lng\":21.169152259827},{\"lat\":42.646255050016,\"lng\":21.171190738678}]','\0\0\0\0\0\0\0\0\0\0\0\0\0¢ˆJÃ£RE@E\0\07+5@è%		…RE@\0\0\0œ*5@ÃÁ‘ RE@`\0\0êM+5@«XH|∏RE@\0\0(”+5@¢ˆJÃ£RE@E\0\07+5@','#fa9eff','2022-05-10 11:53:52','2022-05-10 11:54:02');
/*!40000 ALTER TABLE `geofences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_icons`
--

DROP TABLE IF EXISTS `map_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `map_icons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `width` double(8,2) NOT NULL,
  `height` double(8,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_icons`
--

LOCK TABLES `map_icons` WRITE;
/*!40000 ALTER TABLE `map_icons` DISABLE KEYS */;
INSERT INTO `map_icons` VALUES (1,'images/map_icons/POI_28.png',32.00,32.00),(2,'images/map_icons/POI_155.png',32.00,32.00),(3,'images/map_icons/POI_13.png',32.00,32.00),(4,'images/map_icons/POI_219.png',32.00,32.00),(5,'images/map_icons/POI_197.png',32.00,32.00),(6,'images/map_icons/POI_72.png',32.00,32.00),(7,'images/map_icons/POI_174.png',32.00,32.00),(8,'images/map_icons/POI_224.png',32.00,32.00),(9,'images/map_icons/POI_135.png',32.00,32.00),(10,'images/map_icons/POI_51.png',32.00,32.00),(11,'images/map_icons/POI_121.png',32.00,32.00),(12,'images/map_icons/POI_119.png',32.00,32.00),(13,'images/map_icons/POI_93.png',32.00,32.00),(14,'images/map_icons/POI_221.png',32.00,32.00),(15,'images/map_icons/POI_205.png',32.00,32.00),(16,'images/map_icons/POI_43.png',32.00,32.00),(17,'images/map_icons/POI_115.png',32.00,32.00),(18,'images/map_icons/POI_158.png',32.00,32.00),(19,'images/map_icons/POI_75.png',32.00,32.00),(20,'images/map_icons/POI_70.png',32.00,32.00),(21,'images/map_icons/POI_139.png',32.00,32.00),(22,'images/map_icons/POI_183.png',32.00,32.00),(23,'images/map_icons/POI_154.png',32.00,32.00),(24,'images/map_icons/POI_96.png',32.00,32.00),(25,'images/map_icons/POI_33.png',32.00,32.00),(26,'images/map_icons/POI_68.png',32.00,32.00),(27,'images/map_icons/POI_159.png',32.00,32.00),(28,'images/map_icons/POI_218.png',32.00,32.00),(29,'images/map_icons/POI_53.png',32.00,32.00),(30,'images/map_icons/POI_160.png',32.00,32.00),(31,'images/map_icons/POI_94.png',32.00,32.00),(32,'images/map_icons/POI_113.png',32.00,32.00),(33,'images/map_icons/POI_47.png',32.00,32.00),(34,'images/map_icons/POI_176.png',32.00,32.00),(35,'images/map_icons/POI_91.png',32.00,32.00),(36,'images/map_icons/POI_07.png',32.00,32.00),(37,'images/map_icons/POI_163.png',32.00,32.00),(38,'images/map_icons/POI_143.png',32.00,32.00),(39,'images/map_icons/POI_196.png',32.00,32.00),(40,'images/map_icons/POI_74.png',32.00,32.00),(41,'images/map_icons/POI_203.png',32.00,32.00),(42,'images/map_icons/POI_15.png',32.00,32.00),(43,'images/map_icons/POI_206.png',32.00,32.00),(44,'images/map_icons/POI_69.png',32.00,32.00),(45,'images/map_icons/POI_45.png',32.00,32.00),(46,'images/map_icons/POI_03.png',32.00,32.00),(47,'images/map_icons/POI_101.png',32.00,32.00),(48,'images/map_icons/POI_182.png',32.00,32.00),(49,'images/map_icons/POI_200.png',32.00,32.00),(50,'images/map_icons/POI_05.png',32.00,32.00),(51,'images/map_icons/POI_67.png',32.00,32.00),(52,'images/map_icons/POI_31.png',32.00,32.00),(53,'images/map_icons/POI_141.png',32.00,32.00),(54,'images/map_icons/POI_32.png',32.00,32.00),(55,'images/map_icons/POI_137.png',32.00,32.00),(56,'images/map_icons/POI_65.png',32.00,32.00),(57,'images/map_icons/POI_142.png',32.00,32.00),(58,'images/map_icons/POI_184.png',32.00,32.00),(59,'images/map_icons/POI_116.png',32.00,32.00),(60,'images/map_icons/POI_42.png',32.00,32.00),(61,'images/map_icons/POI_46.png',32.00,32.00),(62,'images/map_icons/POI_140.png',32.00,32.00),(63,'images/map_icons/POI_11.png',32.00,32.00),(64,'images/map_icons/POI_71.png',32.00,32.00),(65,'images/map_icons/POI_120.png',32.00,32.00),(66,'images/map_icons/POI_204.png',32.00,32.00),(67,'images/map_icons/POI_181.png',32.00,32.00),(68,'images/map_icons/POI_92.png',32.00,32.00),(69,'images/map_icons/POI_199.png',32.00,32.00),(70,'images/map_icons/POI_180.png',32.00,32.00),(71,'images/map_icons/POI_157.png',32.00,32.00),(72,'images/map_icons/POI_138.png',32.00,32.00),(73,'images/map_icons/POI_90.png',32.00,32.00),(74,'images/map_icons/POI_34.png',32.00,32.00),(75,'images/map_icons/POI_179.png',32.00,32.00),(76,'images/map_icons/POI_86.png',32.00,32.00),(77,'images/map_icons/POI_178.png',32.00,32.00),(78,'images/map_icons/POI_220.png',32.00,32.00),(79,'images/map_icons/POI_161.png',32.00,32.00),(80,'images/map_icons/POI_49.png',32.00,32.00),(81,'images/map_icons/POI_30.png',32.00,32.00),(82,'images/map_icons/POI_98.png',32.00,32.00),(83,'images/map_icons/POI_100.png',32.00,32.00),(84,'images/map_icons/POI_09.png',32.00,32.00),(85,'images/map_icons/POI_44.png',32.00,32.00),(86,'images/map_icons/POI_201.png',32.00,32.00),(87,'images/map_icons/POI_136.png',32.00,32.00),(88,'images/map_icons/POI_99.png',32.00,32.00),(89,'images/map_icons/POI_87.png',32.00,32.00),(90,'images/map_icons/POI_217.png',32.00,32.00),(91,'images/map_icons/POI_17.png',32.00,32.00),(92,'images/map_icons/POI_162.png',32.00,32.00),(93,'images/map_icons/POI_117.png',32.00,32.00),(94,'images/map_icons/POI_185.png',32.00,32.00),(95,'images/map_icons/POI_134.png',32.00,32.00),(96,'images/map_icons/POI_48.png',32.00,32.00),(97,'images/map_icons/POI_112.png',32.00,32.00),(98,'images/map_icons/POI_223.png',32.00,32.00),(99,'images/map_icons/POI_114.png',32.00,32.00),(100,'images/map_icons/POI_198.png',32.00,32.00),(101,'images/map_icons/POI_156.png',32.00,32.00),(102,'images/map_icons/POI_122.png',32.00,32.00),(103,'images/map_icons/POI_73.png',32.00,32.00);
/*!40000 ALTER TABLE `map_icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES ('2014_08_04_093531_create_device_icons_table',1),('2014_08_04_105731_create_device_fuel_measurements_table',1),('2014_09_12_140638_create_configs_table',1),('2014_09_29_093018_create_map_icons_table',1),('2014_10_02_122922_create_email_templates_table',1),('2014_10_02_122922_create_sms_templates_table',1),('2014_10_23_122927_create_password_reminders_table',1),('2014_10_23_135140_create_subscriptions_table',1),('2014_28_27_091650_create_billing_plans_table',1),('2014_28_27_091651_create_users_table',1),('2014_28_28_185621_create_geofence_groups_table',1),('2014_28_29_094724_create_user_map_icons_table',1),('2014_29_04_120108_create_geofences_table',1),('2015_08_04_094256_create_devices_table',1),('2015_08_05_185233_create_alerts_table',1),('2015_09_08_185621_create_alert_geofence_table',1),('2015_09_08_185621_create_events_custom_table',1),('2015_09_08_185621_create_position_geofence_table',1),('2015_09_08_185641_create_alert_device_table',1),('2015_09_10_174725_create_alert_fuel_consumption_table',1),('2015_09_15_135650_add_users_meters_table',1),('2015_09_15_135650_alter_users_table',1),('2015_09_15_135650_create_events_table',1),('2015_09_15_135650_create_timezones_table',1),('2015_28_27_091651_create_users_dst_table',1),('2016_05_20_135650_sms_gateway',1),('2016_09_08_185621_create_device_groups_table',1),('2016_09_08_185621_create_device_sensors_table',1),('2016_09_08_185621_create_device_services_table',1),('2016_09_08_185621_create_user_drivers_table',1),('2016_09_08_185622_create_alert_driver_pivot_table',1),('2016_09_08_185622_create_user_driver_position_pivot_table',1),('2016_09_08_185623_create_user_device_pivot_table',1),('2016_09_08_185625_create_alert_event_pivot_table',1),('2016_09_09_120108_create_routes_table',1),('2016_09_09_185622_create_event_custom_tags_table',1),('2016_09_09_185622_create_reports_table',1),('2016_09_09_185623_create_billing_plan_permissions_table',1),('2016_09_09_185623_create_events_queue_table',1),('2016_09_09_185623_create_report_device_pivot_table',1),('2016_09_09_185623_create_report_geofence_pivot_table',1),('2016_09_09_185623_create_sensor_groups_table',1),('2016_09_09_185623_create_sms_events_queue_table',1),('2016_09_09_185623_create_tracker_ports_table',1),('2016_09_09_185623_create_unregistered_devices_log_table',1),('2016_09_09_185623_create_user_gprs_templates_table',1),('2016_09_09_185623_create_user_permissions_table',1),('2016_09_09_185623_create_user_sms_templates_table',1),('2016_09_09_185624_create_report_logs_sensors_table',1),('2016_09_09_185624_create_sensor_group_sensors_table',1),('2016_11_04_185624_migrate_version',1),('2016_11_07_185624_device_table_alter',1),('2016_11_28_185624_reports_table_alter',1),('2017_01_24_185624_devices_sensors_table_alter',1),('2017_01_29_185624_alter_user_table_settings',1),('2017_01_30_185624_alter_report_logs_table',1),('2017_02_02_185624_alter_report_logs_table_email_error',1),('2017_03_25_185624_plugin_settings_structure_change',1),('2017_04_03_185624_unregistered_devices_log_collection',1),('2017_04_03_185624_unregistered_devices_log_collection_change',1),('2017_04_24_185625_add_additional_notes_device_field_alter',1),('2017_06_16_185625_events_table_index_created_at',1),('2014_08_04_093531_create_device_icons_table',1),('2014_08_04_105731_create_device_fuel_measurements_table',1),('2014_09_12_140638_create_configs_table',1),('2014_09_29_093018_create_map_icons_table',1),('2014_10_02_122922_create_email_templates_table',1),('2014_10_02_122922_create_sms_templates_table',1),('2014_10_23_122927_create_password_reminders_table',1),('2014_10_23_135140_create_subscriptions_table',1),('2014_28_27_091650_create_billing_plans_table',1),('2014_28_27_091651_create_users_table',1),('2014_28_28_185621_create_geofence_groups_table',1),('2014_28_29_094724_create_user_map_icons_table',1),('2014_29_04_120108_create_geofences_table',1),('2015_08_04_094256_create_devices_table',1),('2015_08_05_185233_create_alerts_table',1),('2015_09_08_185621_create_alert_geofence_table',1),('2015_09_08_185621_create_events_custom_table',1),('2015_09_08_185621_create_position_geofence_table',1),('2015_09_08_185641_create_alert_device_table',1),('2015_09_10_174725_create_alert_fuel_consumption_table',1),('2015_09_15_135650_add_users_meters_table',1),('2015_09_15_135650_alter_users_table',1),('2015_09_15_135650_create_events_table',1),('2015_09_15_135650_create_timezones_table',1),('2015_28_27_091651_create_users_dst_table',1),('2016_05_20_135650_sms_gateway',1),('2016_09_08_185621_create_device_groups_table',1),('2016_09_08_185621_create_device_sensors_table',1),('2016_09_08_185621_create_device_services_table',1),('2016_09_08_185621_create_user_drivers_table',1),('2016_09_08_185622_create_alert_driver_pivot_table',1),('2016_09_08_185622_create_user_driver_position_pivot_table',1),('2016_09_08_185623_create_user_device_pivot_table',1),('2016_09_08_185625_create_alert_event_pivot_table',1),('2016_09_09_120108_create_routes_table',1),('2016_09_09_185622_create_event_custom_tags_table',1),('2016_09_09_185622_create_reports_table',1),('2016_09_09_185623_create_billing_plan_permissions_table',1),('2016_09_09_185623_create_events_queue_table',1),('2016_09_09_185623_create_report_device_pivot_table',1),('2016_09_09_185623_create_report_geofence_pivot_table',1),('2016_09_09_185623_create_sensor_groups_table',1),('2016_09_09_185623_create_sms_events_queue_table',1),('2016_09_09_185623_create_tracker_ports_table',1),('2016_09_09_185623_create_unregistered_devices_log_table',1),('2016_09_09_185623_create_user_gprs_templates_table',1),('2016_09_09_185623_create_user_permissions_table',1),('2016_09_09_185623_create_user_sms_templates_table',1),('2016_09_09_185624_create_report_logs_sensors_table',1),('2016_09_09_185624_create_sensor_group_sensors_table',1),('2016_11_04_185624_migrate_version',1),('2016_11_07_185624_device_table_alter',1),('2016_11_28_185624_reports_table_alter',1),('2017_01_24_185624_devices_sensors_table_alter',1),('2017_01_29_185624_alter_user_table_settings',1),('2017_01_30_185624_alter_report_logs_table',1),('2017_02_02_185624_alter_report_logs_table_email_error',1),('2017_03_25_185624_plugin_settings_structure_change',1),('2017_04_03_185624_unregistered_devices_log_collection',1),('2017_04_03_185624_unregistered_devices_log_collection_change',1),('2017_04_24_185625_add_additional_notes_device_field_alter',1),('2017_06_16_185625_events_table_index_created_at',1),('2014_08_04_093531_create_device_icons_table',1),('2014_08_04_105731_create_device_fuel_measurements_table',1),('2014_09_12_140638_create_configs_table',1),('2014_09_29_093018_create_map_icons_table',1),('2014_10_02_122922_create_email_templates_table',1),('2014_10_02_122922_create_sms_templates_table',1),('2014_10_23_122927_create_password_reminders_table',1),('2014_10_23_135140_create_subscriptions_table',1),('2014_28_27_091650_create_billing_plans_table',1),('2014_28_27_091651_create_users_table',1),('2014_28_28_185621_create_geofence_groups_table',1),('2014_28_29_094724_create_user_map_icons_table',1),('2014_29_04_120108_create_geofences_table',1),('2015_08_04_094256_create_devices_table',1),('2015_08_05_185233_create_alerts_table',1),('2015_09_08_185621_create_alert_geofence_table',1),('2015_09_08_185621_create_events_custom_table',1),('2015_09_08_185621_create_position_geofence_table',1),('2015_09_08_185641_create_alert_device_table',1),('2015_09_10_174725_create_alert_fuel_consumption_table',1),('2015_09_15_135650_add_users_meters_table',1),('2015_09_15_135650_alter_users_table',1),('2015_09_15_135650_create_events_table',1),('2015_09_15_135650_create_timezones_table',1),('2015_28_27_091651_create_users_dst_table',1),('2016_05_20_135650_sms_gateway',1),('2016_09_08_185621_create_device_groups_table',1),('2016_09_08_185621_create_device_sensors_table',1),('2016_09_08_185621_create_device_services_table',1),('2016_09_08_185621_create_user_drivers_table',1),('2016_09_08_185622_create_alert_driver_pivot_table',1),('2016_09_08_185622_create_user_driver_position_pivot_table',1),('2016_09_08_185623_create_user_device_pivot_table',1),('2016_09_08_185625_create_alert_event_pivot_table',1),('2016_09_09_120108_create_routes_table',1),('2016_09_09_185622_create_event_custom_tags_table',1),('2016_09_09_185622_create_reports_table',1),('2016_09_09_185623_create_billing_plan_permissions_table',1),('2016_09_09_185623_create_events_queue_table',1),('2016_09_09_185623_create_report_device_pivot_table',1),('2016_09_09_185623_create_report_geofence_pivot_table',1),('2016_09_09_185623_create_sensor_groups_table',1),('2016_09_09_185623_create_sms_events_queue_table',1),('2016_09_09_185623_create_tracker_ports_table',1),('2016_09_09_185623_create_unregistered_devices_log_table',1),('2016_09_09_185623_create_user_gprs_templates_table',1),('2016_09_09_185623_create_user_permissions_table',1),('2016_09_09_185623_create_user_sms_templates_table',1),('2016_09_09_185624_create_report_logs_sensors_table',1),('2016_09_09_185624_create_sensor_group_sensors_table',1),('2016_11_04_185624_migrate_version',1),('2016_11_07_185624_device_table_alter',1),('2016_11_28_185624_reports_table_alter',1),('2017_01_24_185624_devices_sensors_table_alter',1),('2017_01_29_185624_alter_user_table_settings',1),('2017_01_30_185624_alter_report_logs_table',1),('2017_02_02_185624_alter_report_logs_table_email_error',1),('2017_03_25_185624_plugin_settings_structure_change',1),('2017_04_03_185624_unregistered_devices_log_collection',1),('2017_04_03_185624_unregistered_devices_log_collection_change',1),('2017_04_24_185625_add_additional_notes_device_field_alter',1),('2017_06_16_185625_events_table_index_created_at',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reminders`
--

DROP TABLE IF EXISTS `password_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reminders` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_reminders_email_index` (`email`),
  KEY `password_reminders_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reminders`
--

LOCK TABLES `password_reminders` WRITE;
/*!40000 ALTER TABLE `password_reminders` DISABLE KEYS */;
INSERT INTO `password_reminders` VALUES ('leo_godinez@outlook.com','976e177675edd551baf13f36e9fc24e5dfb53f233d0008fd3b5a58527f739e70','2018-03-14 05:15:36'),('gowloads@gmail.com','2b714d2e3eb36eb0a9d6b64e15b760b569b2f63a7d51e56ca72b79ba66762223','2019-02-15 17:12:43'),('gwloader@gmail.com','c521ad716a15cdb66941c4b9f6af471863fce89909d44a10b267b79c7d5bc220','2019-03-06 21:05:12'),('mmhrishi@gmail.com','c0d0eefeb96ccd5553dfb18c0b63c40df35341355215148becfa427c18bcd7e7','2019-10-20 19:35:44'),('leo_godinez@outlook.com','976e177675edd551baf13f36e9fc24e5dfb53f233d0008fd3b5a58527f739e70','2018-03-14 05:15:36'),('leo_godinez@outlook.com','976e177675edd551baf13f36e9fc24e5dfb53f233d0008fd3b5a58527f739e70','2018-03-14 05:15:36'),('admin@gmail.com','79e94b683eccd7d97358203b36c5513f70cd89d723cb334511c17aa4087a7f22','2019-10-31 11:39:43');
/*!40000 ALTER TABLE `password_reminders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_geofence`
--

DROP TABLE IF EXISTS `position_geofence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `position_geofence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` int(10) unsigned NOT NULL,
  `geofence_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `position_geofence_position_id_index` (`position_id`),
  KEY `position_geofence_geofence_id_index` (`geofence_id`),
  CONSTRAINT `position_geofence_geofence_id_foreign` FOREIGN KEY (`geofence_id`) REFERENCES `geofences` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_geofence`
--

LOCK TABLES `position_geofence` WRITE;
/*!40000 ALTER TABLE `position_geofence` DISABLE KEYS */;
/*!40000 ALTER TABLE `position_geofence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_device_pivot`
--

DROP TABLE IF EXISTS `report_device_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_device_pivot` (
  `report_id` int(10) unsigned NOT NULL,
  `device_id` int(10) unsigned NOT NULL,
  KEY `report_device_pivot_report_id_index` (`report_id`),
  KEY `report_device_pivot_device_id_index` (`device_id`),
  CONSTRAINT `report_device_pivot_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_device_pivot_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_device_pivot`
--

LOCK TABLES `report_device_pivot` WRITE;
/*!40000 ALTER TABLE `report_device_pivot` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_device_pivot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_geofence_pivot`
--

DROP TABLE IF EXISTS `report_geofence_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_geofence_pivot` (
  `report_id` int(10) unsigned NOT NULL,
  `geofence_id` int(10) unsigned NOT NULL,
  KEY `report_geofence_pivot_report_id_index` (`report_id`),
  KEY `report_geofence_pivot_geofence_id_index` (`geofence_id`),
  CONSTRAINT `report_geofence_pivot_geofence_id_foreign` FOREIGN KEY (`geofence_id`) REFERENCES `geofences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_geofence_pivot_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_geofence_pivot`
--

LOCK TABLES `report_geofence_pivot` WRITE;
/*!40000 ALTER TABLE `report_geofence_pivot` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_geofence_pivot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_logs`
--

DROP TABLE IF EXISTS `report_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL,
  `format` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `is_send` tinyint(1) NOT NULL DEFAULT '0',
  `error` text COLLATE utf8_unicode_ci,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `report_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `report_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_logs`
--

LOCK TABLES `report_logs` WRITE;
/*!40000 ALTER TABLE `report_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` tinyint(3) unsigned DEFAULT NULL,
  `format` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_addresses` tinyint(1) DEFAULT NULL,
  `zones_instead` tinyint(1) DEFAULT NULL,
  `stops` tinyint(3) unsigned DEFAULT NULL,
  `speed_limit` double(8,2) unsigned DEFAULT NULL,
  `daily` tinyint(1) DEFAULT NULL,
  `daily_time` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00:00',
  `weekly` tinyint(1) DEFAULT NULL,
  `weekly_time` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00:00',
  `email` text COLLATE utf8_unicode_ci,
  `weekly_email_sent` datetime DEFAULT NULL,
  `daily_email_sent` datetime DEFAULT NULL,
  `from_format` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_format` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_user_id_index` (`user_id`),
  KEY `reports_daily_index` (`daily`),
  KEY `reports_weekly_index` (`weekly`),
  KEY `reports_weekly_email_sent_index` (`weekly_email_sent`),
  KEY `reports_daily_email_sent_index` (`daily_email_sent`),
  CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `coordinates` text COLLATE utf8_unicode_ci NOT NULL,
  `polyline` linestring DEFAULT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `routes_user_id_foreign` (`user_id`),
  KEY `routes_active_index` (`active`),
  CONSTRAINT `routes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensor_group_sensors`
--

DROP TABLE IF EXISTS `sensor_group_sensors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensor_group_sensors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `tag_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_to_history` tinyint(1) NOT NULL DEFAULT '0',
  `on_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `off_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shown_value_by` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fuel_tank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `full_tank` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `full_tank_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `min_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `formula` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `odometer_value_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `odometer_value` double(8,2) unsigned DEFAULT NULL,
  `odometer_value_unit` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'km',
  `temperature_max` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature_max_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature_min` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `temperature_min_value` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT '-',
  `value_formula` int(11) NOT NULL DEFAULT '0',
  `show_in_popup` tinyint(1) NOT NULL DEFAULT '0',
  `unit_of_measurement` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `on_tag_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `off_tag_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `on_type` tinyint(4) DEFAULT NULL,
  `off_type` tinyint(4) DEFAULT NULL,
  `calibrations` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `sensor_group_sensors_group_id_index` (`group_id`),
  KEY `sensor_group_sensors_type_index` (`type`),
  KEY `sensor_group_sensors_tag_name_index` (`tag_name`),
  KEY `sensor_group_sensors_add_to_history_index` (`add_to_history`),
  KEY `sensor_group_sensors_show_in_popup_index` (`show_in_popup`),
  CONSTRAINT `sensor_group_sensors_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `sensor_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensor_group_sensors`
--

LOCK TABLES `sensor_group_sensors` WRITE;
/*!40000 ALTER TABLE `sensor_group_sensors` DISABLE KEYS */;
INSERT INTO `sensor_group_sensors` VALUES (1,3,'ODO','odometer',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'virtual_odometer',0.00,'km',NULL,NULL,NULL,NULL,'-',0,0,'km',NULL,NULL,NULL,NULL,'N;'),(2,3,'battery','battery','power',1,NULL,NULL,'tag_value',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'km',NULL,NULL,NULL,NULL,'-',0,0,'V',NULL,NULL,NULL,NULL,'N;'),(3,3,'ignition','ignition','ignition',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'km',NULL,NULL,NULL,NULL,'-',0,0,'','true','false',1,1,'N;'),(4,3,'Privat','drive_private','Privat',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'km',NULL,NULL,NULL,NULL,'-',0,0,'','16:00','06:00',2,3,'N;');
/*!40000 ALTER TABLE `sensor_group_sensors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensor_groups`
--

DROP TABLE IF EXISTS `sensor_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensor_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sensor_groups_title_index` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensor_groups`
--

LOCK TABLES `sensor_groups` WRITE;
/*!40000 ALTER TABLE `sensor_groups` DISABLE KEYS */;
INSERT INTO `sensor_groups` VALUES (3,'tel-Sensor',4);
/*!40000 ALTER TABLE `sensor_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_events_queue`
--

DROP TABLE IF EXISTS `sms_events_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_events_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `sms_events_queue_user_id_index` (`user_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `sms_events_queue_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_events_queue`
--

LOCK TABLES `sms_events_queue` WRITE;
/*!40000 ALTER TABLE `sms_events_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_events_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_templates`
--

DROP TABLE IF EXISTS `sms_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_templates`
--

LOCK TABLES `sms_templates` WRITE;
/*!40000 ALTER TABLE `sms_templates` DISABLE KEYS */;
INSERT INTO `sms_templates` VALUES (1,'event','New event','Hello,\\r\\nEvent: [event]\\r\\nGeofence: [geofence]\\r\\nDevice: [device]\\r\\nTime: [time]','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'report','Report \"[name]\"','Hello,\\r\\nName: [name]\\r\\nPeriod: [period]','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'service_expiration','Service expiration','Hello, device service is about to expire.\\r\\n\\r\\nDevice: [device]\\r\\nService: [service]\\r\\nLeft: [left]','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'service_expired','Service expired','Hello, device service is expired.\\r\\n\\r\\nDevice: [device]\\r\\nService: [service]','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sms_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `period_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `devices_limit` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `trial` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timezones`
--

DROP TABLE IF EXISTS `timezones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timezones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` double(8,2) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prefix` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timezones_order_index` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timezones`
--

LOCK TABLES `timezones` WRITE;
/*!40000 ALTER TABLE `timezones` DISABLE KEYS */;
INSERT INTO `timezones` VALUES (1,1.00,'UTC -14:00','-14hours','minus','14 0'),(2,1.10,'UTC -13:45','-13hours -45minutes','minus','13 45'),(3,1.20,'UTC -13:30','-13hours -30minutes','minus','13 30'),(4,1.30,'UTC -13:15','-13hours -15minutes','minus','13 15'),(5,1.40,'UTC -13:00','-13hours','minus','13 0'),(6,1.50,'UTC -12:45','-12hours -45minutes','minus','12 45'),(7,1.60,'UTC -12:30','-12hours -30minutes','minus','12 30'),(8,1.70,'UTC -12:15','-12hours -15minutes','minus','12 15'),(9,2.00,'UTC -12:00','-12hours','minus','12 0'),(10,2.10,'UTC -11:45','-11hours -45minutes','minus','11 45'),(11,2.20,'UTC -11:30','-11hours -30minutes','minus','11 30'),(12,2.30,'UTC -11:15','-11hours -15minutes','minus','11 15'),(13,3.00,'UTC -11:00','-11hours','minus','11 0'),(14,3.10,'UTC -10:45','-10hours -45minutes','minus','10 45'),(15,4.00,'UTC -10:30','-10hours -30minutes','minus','10 30'),(16,4.10,'UTC -10:15','-10hours -15minutes','minus','10 15'),(17,4.20,'UTC -10:00','-10hours','minus','10 0'),(18,4.20,'UTC -9:45','-9hours -45minutes','minus','9 45'),(19,4.30,'UTC -9:30','-9hours -30minutes','minus','9 30'),(20,4.40,'UTC -9:15','-9hours -15minutes','minus','9 15'),(21,5.00,'UTC -9:00','-9hours','minus','9 0'),(22,5.10,'UTC -8:45','-8hours -45minutes','minus','8 45'),(23,5.20,'UTC -8:30','-8hours -30minutes','minus','8 30'),(24,5.30,'UTC -8:15','-8hours -15minutes','minus','8 15'),(25,6.00,'UTC -8:00','-8hours','minus','8 0'),(26,6.10,'UTC -7:45','-7hours -45minutes','minus','7 45'),(27,6.20,'UTC -7:30','-7hours -30minutes','minus','7 30'),(28,6.30,'UTC -7:15','-7hours -15minutes','minus','7 15'),(29,7.00,'UTC -7:00','-7hours','minus','7 0'),(30,7.10,'UTC -6:45','-6hours -45minutes','minus','6 45'),(31,7.20,'UTC -6:30','-6hours -30minutes','minus','6 30'),(32,7.30,'UTC -6:15','-6hours -15minutes','minus','6 15'),(33,8.00,'UTC -6:00','-6hours','minus','6 0'),(34,8.10,'UTC -5:45','-5hours -45minutes','minus','5 45'),(35,8.20,'UTC -5:30','-5hours -30minutes','minus','5 30'),(36,8.30,'UTC -5:15','-5hours -15minutes','minus','5 15'),(37,9.00,'UTC -5:00','-5hours','minus','5 0'),(38,9.10,'UTC -4:45','-4hours -45minutes','minus','4 45'),(39,10.00,'UTC -4:30','-4hours -30minutes','minus','4 30'),(40,10.10,'UTC -4:15','-4hours -15minutes','minus','4 15'),(41,11.00,'UTC -4:00','-4hours','minus','4 0'),(42,11.10,'UTC -3:45','-3hours -45minutes','minus','3 45'),(43,12.00,'UTC -3:30','-3hours -30minutes','minus','3 30'),(44,12.10,'UTC -3:15','-3hours -15minutes','minus','3 15'),(45,13.00,'UTC -3:00','-3hours','minus','3 0'),(46,13.10,'UTC -2:45','-2hours -45minutes','minus','2 45'),(47,13.20,'UTC -2:30','-2hours -30minutes','minus','2 30'),(48,13.30,'UTC -2:15','-2hours -15minutes','minus','2 15'),(49,14.00,'UTC -2:00','-2hours','minus','2 0'),(50,14.10,'UTC -1:45','-1hours -45minutes','minus','1 45'),(51,14.20,'UTC -1:30','-1hours -30minutes','minus','1 30'),(52,14.30,'UTC -1:15','-1hours -15minutes','minus','1 15'),(53,15.00,'UTC -1:00','-1hours','minus','1 0'),(54,15.10,'UTC -0:45','-0hours -45minutes','minus','0 45'),(55,15.20,'UTC -0:30','-0hours -30minutes','minus','0 30'),(56,15.30,'UTC -0:15','-0hours -15minutes','minus','0 15'),(57,16.00,'UTC 00:00','+0hours','plus','0 0'),(58,16.10,'UTC +0:15','+0hours +15minutes','plus','0 15'),(59,17.00,'UTC +0:30','+0hours +30minutes','plus','0 30'),(60,17.10,'UTC +0:45','+0hours +45minutes','plus','0 45'),(61,18.00,'UTC +1:00','+1hours','plus','1 0'),(62,18.10,'UTC +1:15','+1hours +15minutes','plus','1 15'),(63,18.20,'UTC +1:30','+1hours +30minutes','plus','1 30'),(64,18.30,'UTC +1:45','+1hours +45minutes','plus','1 45'),(65,19.00,'UTC +2:00','+2hours','plus','2 0'),(66,19.10,'UTC +2:15','+2hours +15minutes','plus','2 15'),(67,19.20,'UTC +2:30','+2hours +30minutes','plus','2 30'),(68,19.30,'UTC +2:45','+2hours +45minutes','plus','2 45'),(69,20.00,'UTC +3:00','+3hours','plus','3 0'),(70,20.10,'UTC +3:15','+3hours +15minutes','plus','3 15'),(71,21.00,'UTC +3:30','+3hours +30minutes','plus','3 30'),(72,21.10,'UTC +3:45','+3hours +45minutes','plus','3 45'),(73,22.00,'UTC +4:00','+4hours','plus','4 0'),(74,22.10,'UTC +4:15','+4hours +15minutes','plus','4 15'),(75,23.00,'UTC +4:30','+4hours +30minutes','plus','4 30'),(76,24.00,'UTC +4:45','+4hours +45minutes','plus','4 45'),(77,25.00,'UTC +5:00','+5hours','plus','5 0'),(78,25.10,'UTC +5:15','+5hours +15minutes','plus','5 15'),(79,26.00,'UTC +5:30','+5hours +30minutes','plus','5 30'),(80,27.00,'UTC +5:45','+5hours +45minutes','plus','5 45'),(81,28.00,'UTC +6:00','+6hours','plus','6 0'),(82,28.10,'UTC +6:15','+6hours +15minutes','plus','6 15'),(83,29.00,'UTC +6:30','+6hours +30minutes','plus','6 30'),(84,29.10,'UTC +6:45','+6hours +45minutes','plus','6 45'),(85,30.00,'UTC +7:00','+7hours','plus','7 0'),(86,30.10,'UTC +7:15','+7hours +15minutes','plus','7 15'),(87,30.20,'UTC +7:30','+7hours +30minutes','plus','7 30'),(88,30.30,'UTC +7:45','+7hours +45minutes','plus','7 45'),(89,31.00,'UTC +8:00','+8hours','plus','8 0'),(90,31.10,'UTC +8:15','+8hours +15minutes','plus','8 15'),(91,31.20,'UTC +8:30','+8hours +30minutes','plus','8 30'),(92,31.30,'UTC +8:45','+8hours +45minutes','plus','8 45'),(93,32.00,'UTC +9:00','+9hours','plus','9 0'),(94,32.10,'UTC +9:15','+9hours +15minutes','plus','9 15'),(95,32.20,'UTC +9:30','+9hours +30minutes','plus','9 30'),(96,32.30,'UTC +9:45','+9hours +45minutes','plus','9 45'),(97,33.00,'UTC +10:00','+10hours','plus','10 0'),(98,33.10,'UTC +10:15','+10hours +15minutes','plus','10 15'),(99,34.00,'UTC +10:30','+10hours +30minutes','plus','10 30'),(100,34.10,'UTC +10:45','+10hours +45minutes','plus','10 45'),(101,35.00,'UTC +11:00','+11hours','plus','11 0'),(102,35.10,'UTC +11:15','+11hours +15minutes','plus','11 15'),(103,35.20,'UTC +11:30','+11hours +30minutes','plus','11 30'),(104,35.30,'UTC +11:45','+11hours +45minutes','plus','11 45'),(105,36.00,'UTC +12:00','+12hours','plus','12 0'),(106,36.10,'UTC +12:15','+12hours +15minutes','plus','12 15'),(107,36.20,'UTC +12:30','+12hours +30minutes','plus','12 30'),(108,36.30,'UTC +12:45','+12hours +45minutes','plus','12 45'),(109,37.00,'UTC +13:00','+13hours','plus','13 0'),(110,37.10,'UTC +13:15','+13hours +15minutes','plus','13 15'),(111,37.20,'UTC +13:30','+13hours +30minutes','plus','13 30'),(112,37.30,'UTC +13:45','+13hours +45minutes','plus','13 45'),(113,38.00,'UTC +14:00','+14hours','plus','14 0');
/*!40000 ALTER TABLE `timezones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timezones_dst`
--

DROP TABLE IF EXISTS `timezones_dst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timezones_dst` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(50) NOT NULL,
  `from_period` varchar(50) NOT NULL,
  `from_time` varchar(5) DEFAULT NULL,
  `to_period` varchar(50) NOT NULL,
  `to_time` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timezones_dst`
--

LOCK TABLES `timezones_dst` WRITE;
/*!40000 ALTER TABLE `timezones_dst` DISABLE KEYS */;
INSERT INTO `timezones_dst` VALUES (1,'Akrotiri and Dhekelia(UK)','Last Sunday of March',NULL,'Last Sunday of October',NULL),(2,'Albania','Last Sunday of March',NULL,'Last Sunday of October',NULL),(3,'Andorra','Last Sunday of March',NULL,'Last Sunday of October',NULL),(4,'Australia','First Sunday of October',NULL,'First Sunday of April',NULL),(5,'Austria','last Sunday of March','01:00','last Sunday of October','01:00'),(6,'Bahamas','Second Sunday of March',NULL,'First Sunday November',NULL),(7,'Belgium','last Sunday of March','01:00','last Sunday of October','01:00'),(8,'Bermuda (UK)','Second Sunday of March',NULL,'First Sunday of November',NULL),(9,'Bosnia and Herzegovina','Last Sunday of March',NULL,'Last Sunday of October',NULL),(10,'Brazil','Third Sunday of October',NULL,'Third Sunday of February',NULL),(11,'Bulgaria','last Sunday of March','01:00','last Sunday of October','01:00'),(12,'Canada','Second Sunday of March',NULL,'First Sunday of November',NULL),(13,'Chile','August 13',NULL,'May 14',NULL),(14,'Croatia','last Sunday of March','01:00','last Sunday of October','01:00'),(15,'Cuba','Second Sunday of March',NULL,'First Sunday of November',NULL),(16,'Cyprus','last Sunday of March','01:00','last Sunday of October','01:00'),(17,'Czech Republic','last Sunday of March','01:00','last Sunday of October','01:00'),(18,'Denmark','last Sunday of March','01:00','last Sunday of October','01:00'),(19,'Egypt','July 8',NULL,'Last friday of October',NULL),(20,'Estonia','last Sunday of March',NULL,'last Sunday of October',NULL),(21,'Faroe Islands (DK)','Last Sunday of March',NULL,'Last Sunday of October',NULL),(22,'Fiji','First Sunday of November',NULL,'Third Sunday of January',NULL),(23,'Finland','Last Sunday of March',NULL,'Last Sunday of October',NULL),(24,'France','last Sunday of March','01:00','last Sunday of October','01:00'),(25,'Germany','last Sunday of March','01:00','last Sunday of October','01:00'),(26,'Greece','last Sunday of March','01:00','last Sunday of October','01:00'),(27,'Greenland (DK)','last Saturday of March','22:00','last Saturday of October','23:00'),(28,'Guernsey (UK)','last Sunday of March','01:00','last Sunday of October','01:00'),(29,'Holy See','Last Sunday of March',NULL,'Last Sunday of October',NULL),(30,'Hungary','last Sunday of March','01:00','last Sunday of October','01:00'),(31,'Iran','March 21',NULL,'September 21',NULL),(32,'Ireland','last Sunday of March','01:00','last Sunday of October','01:00'),(33,'Isle of Man (UK)','last Sunday of March','01:00','last Sunday of October','01:00'),(34,'Israel','Last Friday of March',NULL,'Last Friday of October',NULL),(35,'Italy','last Sunday of March','01:00','last Sunday of October','01:00'),(36,'Jersey (UK)','last Sunday of March','01:00','last Sunday of October','01:00'),(37,'Jordan','Last Friday of March',NULL,'Last Friday of October',NULL),(38,'Kosovo','Last Sunday of March',NULL,'Last Sunday of October',NULL),(39,'Latvia','last Sunday of March','01:00','last Sunday of  October','01:00'),(40,'Lebanon','Last Sunday of March',NULL,'Last Sunday of October',NULL),(41,'Liechtenstein','Last Sunday of March',NULL,'Last Sunday of October',NULL),(42,'Lithuania','last Sunday of March','01:00','last Sunday of October','01:00'),(43,'Luxembourg','last Sunday of March','01:00','last Sunday of October','01:00'),(44,'Macedonia','Last Sunday of March',NULL,'Last Sunday of October',NULL),(45,'Malta','last Sunday of March','01:00','last Sunday of October','01:00'),(46,'Mexico','First Sunday of April',NULL,'Last Sunday of October',NULL),(47,'Moldova','Last Sunday of March',NULL,'Last Sunday of October',NULL),(48,'Monaco','Last Sunday of March',NULL,'Last Sunday of October',NULL),(49,'Mongolia','Last Saturday of March',NULL,'Last Saturday of September',NULL),(50,'Montenegro','Last Sunday of March',NULL,'Last Sunday of October',NULL),(51,'Morocco','Last Sunday of March',NULL,'Last Sunday of October',NULL),(52,'Namibia','First Sunday of September',NULL,'First Sunday of April',NULL),(53,'Netherlands','last Sunday of March','01:00','last Sunday of October','01:00'),(54,'New Zealand','Last Sunday of September',NULL,'First Sunday of April',NULL),(55,'Norway','last Sunday of March','01:00','last Sunday of October','01:00'),(56,'Paraguay','First Sunday of October',NULL,'Fourth Sunday of March',NULL),(57,'Poland','last Sunday of March','01:00','last Sunday of October','01:00'),(58,'Portugal','last Sunday of March','01:00','last Sunday of October','01:00'),(59,'Romania','last Sunday of March','01:00','last Sunday of October','01:00'),(60,'Saint Pierre and Miquelon?(FR)','Second Sunday of March',NULL,'First Sunday of November',NULL),(61,'Samoa','Last Sunday of September',NULL,'First Sunday of April',NULL),(62,'San Marino','Last Sunday of March',NULL,'Last Sunday of October',NULL),(63,'Serbia','last Sunday of March','01:00','last Sunday of October','01:00'),(64,'Slovakia','last Sunday of March','01:00','last Sunday of October','01:00'),(65,'Slovenia','last Sunday of March','01:00','last Sunday of October','01:00'),(66,'Spain','last Sunday of March','01:00','last Sunday of October','01:00'),(67,'Sweden','last Sunday of March','01:00','last Sunday of October','01:00'),(68,'Switzerland','last Sunday of March','01:00','last Sunday of October','01:00'),(69,'Syria','Last Friday of March',NULL,'Last Friday of October',NULL),(70,'Turkey','last Sunday of March','01:00','last Sunday of October','01:00'),(71,'Ukraine','Last Sunday of March',NULL,'Last Sunday of October',NULL),(72,'United Kingdom','last Sunday of March','01:00','last Sunday of October','01:00'),(73,'United States','Second Sunday of March',NULL,'First Sunday of November',NULL),(74,'Western Sahara','Last Sunday of March',NULL,'Last Sunday of October',NULL);
/*!40000 ALTER TABLE `timezones_dst` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tracker_ports`
--

DROP TABLE IF EXISTS `tracker_ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracker_ports` (
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `port` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `extra` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `tracker_ports_port_unique` (`port`),
  UNIQUE KEY `tracker_ports_name_unique` (`name`),
  KEY `tracker_ports_active_index` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tracker_ports`
--

LOCK TABLES `tracker_ports` WRITE;
/*!40000 ALTER TABLE `tracker_ports` DISABLE KEYS */;
INSERT INTO `tracker_ports` VALUES (1,'6000','detector','[]'),(1,'6001','gps103','[]'),(1,'6002','tk103','[]'),(1,'6003','gl100','[]'),(1,'6004','gl200','{\"ignoreFixTime\":\"true\"}'),(1,'6005','t55','[]'),(1,'6006','xexun','{\"extended\":\"false\"}'),(1,'6007','totem','[]'),(1,'6008','enfora','[]'),(1,'6009','meiligao','[]'),(1,'6010','trv','[]'),(1,'6011','suntech','[]'),(1,'6012','progress','[]'),(1,'6013','h02','[]'),(1,'6014','jt600','[]'),(1,'6015','huabao','[]'),(1,'6016','v680','[]'),(1,'6017','pt502','[]'),(1,'6018','tr20','[]'),(1,'6019','navis','[]'),(1,'6020','meitrack','[]'),(1,'6021','skypatrol','[]'),(1,'6022','gt02','[]'),(1,'6023','gt06','[]'),(1,'6024','megastek','[]'),(1,'6025','navigil','[]'),(1,'6026','gpsgate','[]'),(1,'6027','teltonika','[]'),(1,'6028','mta6','[]'),(1,'6029','tzone','[]'),(1,'6030','tlt2h','[]'),(1,'6031','taip','[]'),(1,'6032','wondex','[]'),(1,'6033','cellocator','[]'),(1,'6034','galileo','[]'),(1,'6035','ywt','[]'),(1,'6036','tk102','[]'),(1,'6037','intellitrac','[]'),(1,'6038','xt7','[]'),(1,'6039','wialon','[]'),(1,'6040','carscop','[]'),(1,'6041','apel','[]'),(1,'6042','manpower','[]'),(1,'6043','globalsat','[]'),(1,'6044','atrack','[]'),(1,'6045','pt3000','[]'),(1,'6046','ruptela','[]'),(1,'6047','topflytech','[]'),(1,'6048','laipac','[]'),(1,'6049','aplicom','{\"can\":\"false\"}'),(1,'6050','gotop','[]'),(1,'6051','sanav','[]'),(1,'6052','gator','[]'),(1,'6053','noran','[]'),(1,'6054','m2m','[]'),(1,'6055','osmand','[]'),(1,'6056','easytrack','[]'),(1,'6057','gpsmarker','[]'),(1,'6058','khd','[]'),(1,'6059','piligrim','[]'),(1,'6060','stl060','[]'),(1,'6061','cartrack','[]'),(1,'6062','minifinder','[]'),(1,'6063','haicom','[]'),(1,'6064','eelink','[]'),(1,'6065','box','[]'),(1,'6066','freedom','[]'),(1,'6067','telic','[]'),(1,'6068','trackbox','[]'),(1,'6069','visiontek','[]'),(1,'6070','orion','[]'),(1,'6071','riti','[]'),(1,'6072','ulbotech','[]'),(1,'6073','tramigo','[]'),(1,'6074','tr900','[]'),(1,'6075','ardi01','[]'),(1,'6076','xt013','[]'),(1,'6077','autofon','[]'),(1,'6078','gosafe','[]'),(1,'6079','tt8850','[]'),(1,'6080','bce','[]'),(1,'6081','xirgo','[]'),(1,'6082','calamp','[]'),(1,'6083','mtx','[]'),(1,'6084','tytan','[]'),(1,'6085','avl301','[]'),(1,'6086','castel','[]'),(1,'6087','mxt','[]'),(1,'6088','cityeasy','[]'),(1,'6089','aquila','[]'),(1,'6090','flextrack','[]'),(1,'6091','blackkite','[]'),(1,'6092','adm','[]'),(1,'6093','watch','[]'),(1,'6094','t800x','[]'),(1,'6095','upro','[]'),(1,'6096','auro','[]'),(1,'6097','disha','[]'),(1,'6098','thinkrace','[]'),(1,'6099','pathaway','[]'),(1,'6100','arnavi','[]'),(1,'6101','nvs','[]'),(1,'6102','kenji','[]'),(1,'6103','astra','[]'),(1,'6104','homtecs','[]'),(1,'6105','fox','[]'),(1,'6106','gnx','[]'),(1,'6107','arknav','[]'),(1,'6108','supermate','[]'),(1,'6109','appello','[]'),(1,'6110','idpl','[]'),(1,'6111','huasheng','[]'),(1,'6112','l100','[]'),(1,'6113','granit','[]'),(1,'6114','carcell','[]'),(1,'6115','obddongle','[]'),(1,'6117','raveon','[]'),(1,'6118','cradlepoint','[]'),(1,'6119','arknavx8','[]'),(1,'6120','autograde','[]'),(1,'6121','oigo','[]'),(1,'6122','jpkorjar','[]'),(1,'6123','cguard','[]'),(1,'6124','fifotrack','[]'),(1,'6125','smokey','[]'),(1,'6126','extremtrac','[]'),(1,'6127','trakmate','[]'),(1,'6129','maestro','[]'),(1,'6130','ais','[]'),(1,'6131','gt30','[]'),(1,'6132','tmg','[]'),(1,'6133','pretrace','[]'),(1,'6134','pricol','[]'),(1,'6135','siwi','[]'),(1,'6136','starlink','[]'),(1,'6137','dmt','[]'),(1,'6138','xt2400','[]'),(1,'6139','dmthttp','[]'),(1,'6140','alematics','[]'),(1,'6141','gps056','[]'),(1,'6142','flexcomm','[]'),(1,'6143','vt200','[]'),(1,'6144','owntracks','[]'),(1,'6145','vtfms','[]'),(1,'6146','tlv','[]'),(1,'6147','esky','[]'),(1,'6148','genx','[]'),(1,'6149','flespi','[]'),(1,'6150','dway','[]'),(1,'6151','recoda','[]'),(1,'6152','oko','[]'),(1,'6153','ivt401','[]'),(1,'6154','sigfox','[]'),(1,'6155','t57','[]'),(1,'6156','spot','[]'),(1,'6157','m2c','[]'),(1,'6158','austinnb','[]'),(1,'6159','opengts','[]'),(1,'6160','cautela','[]'),(1,'6161','continental','[]'),(1,'6162','egts','[]'),(1,'6163','robotrack','[]'),(1,'6164','pt60','[]'),(1,'6165','telemax','[]'),(1,'6166','sabertek','[]'),(1,'6167','retranslator','[]'),(1,'6168','svias','[]'),(1,'6169','eseal','[]'),(1,'6170','freematics','[]'),(1,'6171','avema','[]'),(1,'6172','autotrack','[]'),(1,'6173','tek','[]'),(1,'6174','wristband','[]'),(1,'6175','applet','[]'),(1,'6176','milesmate','[]'),(1,'6177','anytrek','[]'),(1,'6178','smartsole','[]'),(1,'6179','its','[]'),(1,'6180','xrb28','[]'),(1,'6181','c2stek','[]'),(1,'6182','nyitech','[]'),(1,'6183','neos','[]'),(1,'6184','satsol','[]'),(1,'6185','globalstar','[]'),(1,'6186','sanul','[]'),(1,'6187','pebbell','[]'),(1,'6188','radar','[]'),(1,'6189','techtlt','[]'),(1,'6190','starcom','[]'),(1,'6191','mictrack','[]'),(1,'6192','plugin','[]'),(1,'6193','leafspy','[]'),(1,'6194','naviset','[]'),(1,'6300','suntech2','{\"hbm\":\"true\",\"includeAdc\":\"true\"}'),(1,'6301','its2','[]'),(1,'7023','gt062','[]'),(1,'7027','at2000','[]');
/*!40000 ALTER TABLE `tracker_ports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tracker_ports---bkp`
--

DROP TABLE IF EXISTS `tracker_ports---bkp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracker_ports---bkp` (
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `port` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `extra` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `tracker_ports_port_unique` (`port`),
  UNIQUE KEY `tracker_ports_name_unique` (`name`),
  KEY `tracker_ports_active_index` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tracker_ports---bkp`
--

LOCK TABLES `tracker_ports---bkp` WRITE;
/*!40000 ALTER TABLE `tracker_ports---bkp` DISABLE KEYS */;
INSERT INTO `tracker_ports---bkp` VALUES (1,'6000','detector','[]'),(1,'6001','gps103','[]'),(1,'6002','tk103','[]'),(1,'6003','gl100','[]'),(1,'6004','gl200','{\"ignoreFixTime\":\"true\"}'),(1,'6005','t55','[]'),(1,'6006','xexun','{\"extended\":\"false\"}'),(1,'6007','totem','[]'),(1,'6008','enfora','[]'),(1,'6009','meiligao','[]'),(1,'6010','trv','[]'),(1,'6011','suntech','[]'),(1,'6012','progress','[]'),(1,'6013','h02','[]'),(1,'6014','jt600','[]'),(1,'6015','ev603','[]'),(1,'6016','v680','[]'),(1,'6017','pt502','[]'),(1,'6018','tr20','[]'),(1,'6019','navis','[]'),(1,'6020','meitrack','[]'),(1,'6021','skypatrol','[]'),(1,'6022','gt02','[]'),(1,'6023','gt06','[]'),(1,'6024','megastek','[]'),(1,'6025','navigil','[]'),(1,'6026','gpsgate','[]'),(1,'6027','teltonika','[]'),(1,'6028','mta6','[]'),(1,'6029','tzone','[]'),(1,'6030','tlt2h','[]'),(1,'6031','taip','[]'),(1,'6032','wondex','[]'),(1,'6033','cellocator','[]'),(1,'6034','galileo','[]'),(1,'6035','ywt','[]'),(1,'6036','tk102','[]'),(1,'6037','intellitrac','[]'),(1,'6038','xt7','[]'),(1,'6039','wialon','[]'),(1,'6040','carscop','[]'),(1,'6041','apel','[]'),(1,'6042','manpower','[]'),(1,'6043','globalsat','[]'),(1,'6044','atrack','[]'),(1,'6045','pt3000','[]'),(1,'6046','ruptela','[]'),(1,'6047','topflytech','[]'),(1,'6048','laipac','[]'),(1,'6049','aplicom','{\"can\":\"false\"}'),(1,'6050','gotop','[]'),(1,'6051','sanav','[]'),(1,'6052','gator','[]'),(1,'6053','noran','[]'),(1,'6054','m2m','[]'),(1,'6055','osmand','[]'),(1,'6056','easytrack','[]'),(1,'6057','gpsmarker','[]'),(1,'6058','khd','[]'),(1,'6059','piligrim','[]'),(1,'6060','stl060','[]'),(1,'6061','cartrack','[]'),(1,'6062','minifinder','[]'),(1,'6063','haicom','[]'),(1,'6064','eelink','[]'),(1,'6065','box','[]'),(1,'6066','freedom','[]'),(1,'6067','telik','[]'),(1,'6068','trackbox','[]'),(1,'6069','visiontek','[]'),(1,'6070','orion','[]'),(1,'6071','riti','[]'),(1,'6072','ulbotech','[]'),(1,'6073','tramigo','[]'),(1,'6074','tr900','[]'),(1,'6075','ardi01','[]'),(1,'6076','xt013','[]'),(1,'6077','autofon','[]'),(1,'6078','gosafe','[]'),(1,'6079','tt8850','[]'),(1,'6080','bce','[]'),(1,'6081','xirgo','[]'),(1,'6082','calamp','[]'),(1,'6083','mtx','[]'),(1,'6084','tytan','[]'),(1,'6085','avl301','[]'),(1,'6086','castel','[]'),(1,'6087','mxt','[]'),(1,'6088','cityeasy','[]'),(1,'6089','aquila','[]'),(1,'6090','flextrack','[]'),(1,'6091','blackkite','[]'),(1,'6092','adm','[]'),(1,'6093','watch','[]'),(1,'6094','t800x','[]'),(1,'6095','upro','[]'),(1,'6096','auro','[]'),(1,'6097','disha','[]'),(1,'6098','thinkrace','[]'),(1,'6099','pathaway','[]'),(1,'6100','arnavi','[]'),(1,'6102','kenji','[]'),(1,'6103','astra','[]'),(1,'6104','homtecs','[]'),(1,'6105','fox','[]'),(1,'6106','gnx','[]'),(1,'6107','arknav','[]'),(1,'6108','supermate','[]'),(1,'6109','appello','[]'),(1,'6110','idpl','[]'),(1,'6111','huasheng','[]'),(1,'6113','granit','[]'),(1,'6114','carcell','[]'),(1,'6115','obddongle','[]'),(1,'6117','raveon','[]'),(1,'6118','cradlepoint','[]'),(1,'6119','arknavx8','[]'),(1,'6120','autograde','[]'),(1,'6121','oigo','[]'),(1,'6123','cguard','[]'),(1,'6124','fifotrack','[]'),(1,'6125','smokey','[]'),(1,'6126','extremtrac','[]'),(1,'6127','trakmate','[]'),(1,'6129','maestro','[]'),(1,'6130','ais','[]'),(1,'6131','gt30','[]'),(1,'6132','tmg','[]'),(1,'6133','pretrace','[]'),(1,'6134','pricol','[]'),(1,'6135','siwi','[]'),(1,'6136','starlink','[]'),(1,'6137','dmt','[]'),(1,'6142','flexcomm','[]'),(1,'7027','at2000','[]');
/*!40000 ALTER TABLE `tracker_ports---bkp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_device_pivot`
--

DROP TABLE IF EXISTS `user_device_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_device_pivot` (
  `user_id` int(10) unsigned NOT NULL,
  `device_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `current_driver_id` int(10) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `current_geofences` text COLLATE utf8_unicode_ci,
  `current_events` text COLLATE utf8_unicode_ci,
  `timezone_id` int(10) unsigned DEFAULT NULL,
  KEY `user_device_pivot_user_id_index` (`user_id`),
  KEY `user_device_pivot_device_id_index` (`device_id`),
  KEY `user_device_pivot_group_id_index` (`group_id`),
  KEY `user_device_pivot_current_driver_id_index` (`current_driver_id`),
  KEY `user_device_pivot_active_index` (`active`),
  KEY `user_device_pivot_timezone_id_index` (`timezone_id`),
  CONSTRAINT `user_device_pivot_current_driver_id_foreign` FOREIGN KEY (`current_driver_id`) REFERENCES `user_drivers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_device_pivot_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_device_pivot_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `device_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_device_pivot_timezone_id_foreign` FOREIGN KEY (`timezone_id`) REFERENCES `timezones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_device_pivot_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_device_pivot`
--

LOCK TABLES `user_device_pivot` WRITE;
/*!40000 ALTER TABLE `user_device_pivot` DISABLE KEYS */;
INSERT INTO `user_device_pivot` VALUES (23,338,14,NULL,1,NULL,NULL,NULL),(23,339,16,NULL,1,NULL,NULL,NULL),(23,341,16,NULL,1,NULL,NULL,NULL),(23,342,16,NULL,1,NULL,NULL,NULL),(23,343,12,NULL,1,NULL,NULL,NULL),(23,344,12,NULL,1,NULL,NULL,NULL),(23,345,12,NULL,1,NULL,NULL,NULL),(23,346,12,NULL,1,NULL,NULL,NULL),(23,347,12,NULL,1,NULL,NULL,NULL),(23,348,13,NULL,1,NULL,NULL,NULL),(23,349,13,NULL,1,NULL,NULL,NULL),(23,350,13,NULL,1,NULL,NULL,NULL),(23,351,13,NULL,1,NULL,NULL,NULL),(23,352,14,NULL,1,NULL,NULL,NULL),(23,353,14,NULL,1,NULL,NULL,NULL),(23,354,14,NULL,1,NULL,NULL,NULL),(23,355,14,NULL,1,NULL,NULL,NULL),(23,356,17,NULL,1,NULL,NULL,NULL),(23,357,17,NULL,1,NULL,NULL,NULL),(23,358,17,NULL,1,NULL,NULL,NULL),(23,359,17,NULL,1,NULL,NULL,NULL),(23,360,17,NULL,1,NULL,NULL,NULL),(23,361,17,NULL,1,NULL,NULL,NULL),(23,362,20,NULL,1,NULL,NULL,NULL),(23,363,20,NULL,1,NULL,NULL,NULL),(23,364,16,NULL,1,NULL,NULL,NULL),(23,365,19,NULL,1,NULL,NULL,NULL),(23,366,19,NULL,1,NULL,NULL,NULL),(23,367,18,NULL,1,NULL,NULL,NULL),(23,368,18,NULL,1,NULL,NULL,NULL),(23,369,15,NULL,1,NULL,NULL,NULL),(23,370,15,NULL,1,NULL,NULL,NULL),(23,371,15,NULL,1,NULL,NULL,NULL),(23,372,15,NULL,1,NULL,NULL,NULL),(23,373,15,NULL,1,NULL,NULL,NULL),(23,374,15,NULL,1,NULL,NULL,NULL),(23,375,15,NULL,1,NULL,NULL,NULL),(23,376,15,NULL,1,NULL,NULL,NULL),(23,377,15,NULL,1,NULL,NULL,NULL),(23,378,15,NULL,1,NULL,NULL,NULL),(23,379,15,NULL,1,NULL,NULL,NULL),(23,380,15,NULL,1,NULL,NULL,NULL),(23,381,15,NULL,1,NULL,NULL,NULL),(23,382,15,NULL,1,NULL,NULL,NULL),(23,383,15,NULL,1,NULL,NULL,NULL),(23,384,15,NULL,1,NULL,NULL,NULL),(23,385,15,NULL,1,NULL,NULL,NULL),(23,386,15,NULL,1,NULL,NULL,NULL),(23,387,18,NULL,1,NULL,NULL,NULL),(23,389,18,NULL,1,NULL,NULL,NULL),(23,391,18,NULL,1,NULL,NULL,NULL),(23,392,18,NULL,1,NULL,NULL,NULL),(23,393,16,NULL,1,NULL,NULL,NULL),(23,394,18,NULL,1,NULL,NULL,NULL),(23,395,18,NULL,1,NULL,NULL,NULL),(23,396,18,NULL,1,NULL,NULL,NULL),(23,397,18,NULL,1,NULL,NULL,NULL),(23,401,18,NULL,1,NULL,NULL,NULL),(23,402,18,NULL,1,NULL,NULL,NULL),(23,403,18,NULL,1,NULL,NULL,NULL),(23,404,18,NULL,1,NULL,NULL,NULL),(23,406,18,NULL,1,NULL,NULL,NULL),(23,407,18,NULL,1,NULL,NULL,NULL),(23,408,18,NULL,1,NULL,NULL,NULL),(23,409,18,NULL,1,NULL,NULL,NULL),(23,410,18,NULL,1,NULL,NULL,NULL),(23,411,18,NULL,1,NULL,NULL,NULL),(23,412,18,NULL,1,NULL,NULL,NULL),(23,414,18,NULL,1,NULL,NULL,NULL),(23,415,18,NULL,1,NULL,NULL,NULL),(23,416,18,NULL,1,NULL,NULL,NULL),(23,418,16,NULL,1,NULL,NULL,NULL),(23,419,16,NULL,1,NULL,NULL,NULL),(23,420,17,NULL,1,NULL,NULL,NULL),(23,421,15,NULL,1,NULL,NULL,NULL),(23,422,14,NULL,1,NULL,NULL,NULL),(23,423,16,NULL,1,NULL,NULL,NULL),(23,424,15,NULL,1,NULL,NULL,NULL),(23,425,16,NULL,1,NULL,NULL,NULL),(23,426,18,NULL,1,NULL,NULL,NULL),(23,427,13,NULL,1,NULL,NULL,NULL),(24,350,NULL,NULL,1,NULL,NULL,NULL),(24,348,NULL,NULL,1,NULL,NULL,NULL),(24,397,NULL,NULL,1,NULL,NULL,NULL),(24,384,NULL,NULL,1,NULL,NULL,NULL),(24,354,NULL,NULL,1,NULL,NULL,NULL),(24,418,NULL,NULL,1,NULL,NULL,NULL),(24,382,NULL,NULL,1,NULL,NULL,NULL),(24,355,NULL,NULL,1,NULL,NULL,NULL),(24,381,NULL,NULL,1,NULL,NULL,NULL),(24,419,NULL,NULL,1,NULL,NULL,NULL),(24,347,NULL,NULL,1,NULL,NULL,NULL),(24,420,NULL,NULL,1,NULL,NULL,NULL),(24,411,NULL,NULL,1,NULL,NULL,NULL),(24,421,NULL,NULL,1,NULL,NULL,NULL),(24,345,NULL,NULL,1,NULL,NULL,NULL),(24,353,NULL,NULL,1,NULL,NULL,NULL),(24,341,NULL,NULL,1,NULL,NULL,NULL),(24,415,NULL,NULL,1,NULL,NULL,NULL),(24,349,NULL,NULL,1,NULL,NULL,NULL),(24,367,NULL,NULL,1,NULL,NULL,NULL),(24,362,NULL,NULL,1,NULL,NULL,NULL),(24,351,NULL,NULL,1,NULL,NULL,NULL),(24,371,NULL,NULL,1,NULL,NULL,NULL),(24,383,NULL,NULL,1,NULL,NULL,NULL),(24,385,NULL,NULL,1,NULL,NULL,NULL),(24,376,NULL,NULL,1,NULL,NULL,NULL),(24,363,NULL,NULL,1,NULL,NULL,NULL),(24,344,NULL,NULL,1,NULL,NULL,NULL),(24,380,NULL,NULL,1,NULL,NULL,NULL),(24,392,NULL,NULL,1,NULL,NULL,NULL),(24,422,NULL,NULL,1,NULL,NULL,NULL),(24,358,NULL,NULL,1,NULL,NULL,NULL),(24,373,NULL,NULL,1,NULL,NULL,NULL),(24,364,NULL,NULL,1,NULL,NULL,NULL),(24,375,NULL,NULL,1,NULL,NULL,NULL),(24,357,NULL,NULL,1,NULL,NULL,NULL),(24,377,NULL,NULL,1,NULL,NULL,NULL),(24,423,NULL,NULL,1,NULL,NULL,NULL),(24,374,NULL,NULL,1,NULL,NULL,NULL),(24,346,NULL,NULL,1,NULL,NULL,NULL),(24,409,NULL,NULL,1,NULL,NULL,NULL),(24,416,NULL,NULL,1,NULL,NULL,NULL),(24,424,NULL,NULL,1,NULL,NULL,NULL),(24,425,NULL,NULL,1,NULL,NULL,NULL),(24,404,NULL,NULL,1,NULL,NULL,NULL),(24,393,NULL,NULL,1,NULL,NULL,NULL),(24,378,NULL,NULL,1,NULL,NULL,NULL),(24,426,NULL,NULL,1,NULL,NULL,NULL),(24,343,NULL,NULL,1,NULL,NULL,NULL),(24,360,NULL,NULL,1,NULL,NULL,NULL),(24,402,NULL,NULL,1,NULL,NULL,NULL),(24,412,NULL,NULL,1,NULL,NULL,NULL),(24,386,NULL,NULL,1,NULL,NULL,NULL),(24,379,NULL,NULL,1,NULL,NULL,NULL),(24,401,NULL,NULL,1,NULL,NULL,NULL),(24,359,NULL,NULL,1,NULL,NULL,NULL),(24,352,NULL,NULL,1,NULL,NULL,NULL),(24,338,NULL,NULL,1,NULL,NULL,NULL),(24,395,NULL,NULL,1,NULL,NULL,NULL),(24,356,NULL,NULL,1,NULL,NULL,NULL),(24,414,NULL,NULL,1,NULL,NULL,NULL),(24,410,NULL,NULL,1,NULL,NULL,NULL),(24,403,NULL,NULL,1,NULL,NULL,NULL),(24,361,NULL,NULL,1,NULL,NULL,NULL),(24,391,NULL,NULL,1,NULL,NULL,NULL),(24,342,NULL,NULL,1,NULL,NULL,NULL),(24,406,NULL,NULL,1,NULL,NULL,NULL),(24,370,NULL,NULL,1,NULL,NULL,NULL),(24,387,NULL,NULL,1,NULL,NULL,NULL),(24,368,NULL,NULL,1,NULL,NULL,NULL),(24,427,NULL,NULL,1,NULL,NULL,NULL),(24,366,NULL,NULL,1,NULL,NULL,NULL),(24,339,NULL,NULL,1,NULL,NULL,NULL),(24,365,NULL,NULL,1,NULL,NULL,NULL),(24,394,NULL,NULL,1,NULL,NULL,NULL),(24,408,NULL,NULL,1,NULL,NULL,NULL),(24,389,NULL,NULL,1,NULL,NULL,NULL),(24,372,NULL,NULL,1,NULL,NULL,NULL),(24,369,NULL,NULL,1,NULL,NULL,NULL),(24,407,NULL,NULL,1,NULL,NULL,NULL),(24,396,NULL,NULL,1,NULL,NULL,NULL),(25,350,NULL,NULL,1,NULL,NULL,NULL),(25,348,NULL,NULL,1,NULL,NULL,NULL),(25,397,NULL,NULL,1,NULL,NULL,NULL),(25,384,NULL,NULL,1,NULL,NULL,NULL),(25,354,NULL,NULL,1,NULL,NULL,NULL),(25,418,NULL,NULL,1,NULL,NULL,NULL),(25,382,NULL,NULL,1,NULL,NULL,NULL),(25,355,NULL,NULL,1,NULL,NULL,NULL),(25,381,NULL,NULL,1,NULL,NULL,NULL),(25,419,NULL,NULL,1,NULL,NULL,NULL),(25,347,NULL,NULL,1,NULL,NULL,NULL),(25,420,NULL,NULL,1,NULL,NULL,NULL),(25,411,NULL,NULL,1,NULL,NULL,NULL),(25,421,NULL,NULL,1,NULL,NULL,NULL),(25,345,NULL,NULL,1,NULL,NULL,NULL),(25,353,NULL,NULL,1,NULL,NULL,NULL),(25,341,NULL,NULL,1,NULL,NULL,NULL),(25,415,NULL,NULL,1,NULL,NULL,NULL),(25,349,NULL,NULL,1,NULL,NULL,NULL),(25,367,NULL,NULL,1,NULL,NULL,NULL),(25,362,NULL,NULL,1,NULL,NULL,NULL),(25,351,NULL,NULL,1,NULL,NULL,NULL),(25,371,NULL,NULL,1,NULL,NULL,NULL),(25,383,NULL,NULL,1,NULL,NULL,NULL),(25,385,NULL,NULL,1,NULL,NULL,NULL),(25,376,NULL,NULL,1,NULL,NULL,NULL),(25,363,NULL,NULL,1,NULL,NULL,NULL),(25,344,NULL,NULL,1,NULL,NULL,NULL),(25,380,NULL,NULL,1,NULL,NULL,NULL),(25,392,NULL,NULL,1,NULL,NULL,NULL),(25,422,NULL,NULL,1,NULL,NULL,NULL),(25,358,NULL,NULL,1,NULL,NULL,NULL),(25,373,NULL,NULL,1,NULL,NULL,NULL),(25,364,NULL,NULL,1,NULL,NULL,NULL),(25,375,NULL,NULL,1,NULL,NULL,NULL),(25,357,NULL,NULL,1,NULL,NULL,NULL),(25,377,NULL,NULL,1,NULL,NULL,NULL),(25,423,NULL,NULL,1,NULL,NULL,NULL),(25,374,NULL,NULL,1,NULL,NULL,NULL),(25,346,NULL,NULL,1,NULL,NULL,NULL),(25,409,NULL,NULL,1,NULL,NULL,NULL),(25,416,NULL,NULL,1,NULL,NULL,NULL),(25,424,NULL,NULL,1,NULL,NULL,NULL),(25,425,NULL,NULL,1,NULL,NULL,NULL),(25,404,NULL,NULL,1,NULL,NULL,NULL),(25,393,NULL,NULL,1,NULL,NULL,NULL),(25,378,NULL,NULL,1,NULL,NULL,NULL),(25,426,NULL,NULL,1,NULL,NULL,NULL),(25,343,NULL,NULL,1,NULL,NULL,NULL),(25,360,NULL,NULL,1,NULL,NULL,NULL),(25,402,NULL,NULL,1,NULL,NULL,NULL),(25,412,NULL,NULL,1,NULL,NULL,NULL),(25,386,NULL,NULL,1,NULL,NULL,NULL),(25,379,NULL,NULL,1,NULL,NULL,NULL),(25,401,NULL,NULL,1,NULL,NULL,NULL),(25,359,NULL,NULL,1,NULL,NULL,NULL),(25,352,NULL,NULL,1,NULL,NULL,NULL),(25,338,NULL,NULL,1,NULL,NULL,NULL),(25,395,NULL,NULL,1,NULL,NULL,NULL),(25,356,NULL,NULL,1,NULL,NULL,NULL),(25,414,NULL,NULL,1,NULL,NULL,NULL),(25,410,NULL,NULL,1,NULL,NULL,NULL),(25,403,NULL,NULL,1,NULL,NULL,NULL),(25,361,NULL,NULL,1,NULL,NULL,NULL),(25,391,NULL,NULL,1,NULL,NULL,NULL),(25,342,NULL,NULL,1,NULL,NULL,NULL),(25,406,NULL,NULL,1,NULL,NULL,NULL),(25,370,NULL,NULL,1,NULL,NULL,NULL),(25,387,NULL,NULL,1,NULL,NULL,NULL),(25,368,NULL,NULL,1,NULL,NULL,NULL),(25,427,NULL,NULL,1,NULL,NULL,NULL),(25,366,NULL,NULL,1,NULL,NULL,NULL),(25,339,NULL,NULL,1,NULL,NULL,NULL),(25,365,NULL,NULL,1,NULL,NULL,NULL),(25,394,NULL,NULL,1,NULL,NULL,NULL),(25,408,NULL,NULL,1,NULL,NULL,NULL),(25,389,NULL,NULL,1,NULL,NULL,NULL),(25,372,NULL,NULL,1,NULL,NULL,NULL),(25,369,NULL,NULL,1,NULL,NULL,NULL),(25,407,NULL,NULL,1,NULL,NULL,NULL),(25,396,NULL,NULL,1,NULL,NULL,NULL),(26,350,NULL,NULL,1,NULL,NULL,NULL),(26,348,NULL,NULL,1,NULL,NULL,NULL),(26,397,NULL,NULL,1,NULL,NULL,NULL),(26,384,NULL,NULL,1,NULL,NULL,NULL),(26,354,NULL,NULL,1,NULL,NULL,NULL),(26,418,NULL,NULL,1,NULL,NULL,NULL),(26,382,NULL,NULL,1,NULL,NULL,NULL),(26,355,NULL,NULL,1,NULL,NULL,NULL),(26,381,NULL,NULL,1,NULL,NULL,NULL),(26,419,NULL,NULL,1,NULL,NULL,NULL),(26,347,NULL,NULL,1,NULL,NULL,NULL),(26,420,NULL,NULL,1,NULL,NULL,NULL),(26,411,NULL,NULL,1,NULL,NULL,NULL),(26,421,NULL,NULL,1,NULL,NULL,NULL),(26,345,NULL,NULL,1,NULL,NULL,NULL),(26,353,NULL,NULL,1,NULL,NULL,NULL),(26,341,NULL,NULL,1,NULL,NULL,NULL),(26,415,NULL,NULL,1,NULL,NULL,NULL),(26,349,NULL,NULL,1,NULL,NULL,NULL),(26,367,NULL,NULL,1,NULL,NULL,NULL),(26,362,NULL,NULL,1,NULL,NULL,NULL),(26,351,NULL,NULL,1,NULL,NULL,NULL),(26,371,NULL,NULL,1,NULL,NULL,NULL),(26,383,NULL,NULL,1,NULL,NULL,NULL),(26,385,NULL,NULL,1,NULL,NULL,NULL),(26,376,NULL,NULL,1,NULL,NULL,NULL),(26,363,NULL,NULL,1,NULL,NULL,NULL),(26,344,NULL,NULL,1,NULL,NULL,NULL),(26,380,NULL,NULL,1,NULL,NULL,NULL),(26,392,NULL,NULL,1,NULL,NULL,NULL),(26,422,NULL,NULL,1,NULL,NULL,NULL),(26,358,NULL,NULL,1,NULL,NULL,NULL),(26,373,NULL,NULL,1,NULL,NULL,NULL),(26,364,NULL,NULL,1,NULL,NULL,NULL),(26,375,NULL,NULL,1,NULL,NULL,NULL),(26,357,NULL,NULL,1,NULL,NULL,NULL),(26,377,NULL,NULL,1,NULL,NULL,NULL),(26,423,NULL,NULL,1,NULL,NULL,NULL),(26,374,NULL,NULL,1,NULL,NULL,NULL),(26,346,NULL,NULL,1,NULL,NULL,NULL),(26,409,NULL,NULL,1,NULL,NULL,NULL),(26,416,NULL,NULL,1,NULL,NULL,NULL),(26,424,NULL,NULL,1,NULL,NULL,NULL),(26,425,NULL,NULL,1,NULL,NULL,NULL),(26,404,NULL,NULL,1,NULL,NULL,NULL),(26,393,NULL,NULL,1,NULL,NULL,NULL),(26,378,NULL,NULL,1,NULL,NULL,NULL),(26,426,NULL,NULL,1,NULL,NULL,NULL),(26,343,NULL,NULL,1,NULL,NULL,NULL),(26,360,NULL,NULL,1,NULL,NULL,NULL),(26,402,NULL,NULL,1,NULL,NULL,NULL),(26,412,NULL,NULL,1,NULL,NULL,NULL),(26,386,NULL,NULL,1,NULL,NULL,NULL),(26,379,NULL,NULL,1,NULL,NULL,NULL),(26,401,NULL,NULL,1,NULL,NULL,NULL),(26,359,NULL,NULL,1,NULL,NULL,NULL),(26,352,NULL,NULL,1,NULL,NULL,NULL),(26,338,NULL,NULL,1,NULL,NULL,NULL),(26,395,NULL,NULL,1,NULL,NULL,NULL),(26,356,NULL,NULL,1,NULL,NULL,NULL),(26,414,NULL,NULL,1,NULL,NULL,NULL),(26,410,NULL,NULL,1,NULL,NULL,NULL),(26,403,NULL,NULL,1,NULL,NULL,NULL),(26,361,NULL,NULL,1,NULL,NULL,NULL),(26,391,NULL,NULL,1,NULL,NULL,NULL),(26,342,NULL,NULL,1,NULL,NULL,NULL),(26,406,NULL,NULL,1,NULL,NULL,NULL),(26,370,NULL,NULL,1,NULL,NULL,NULL),(26,387,NULL,NULL,1,NULL,NULL,NULL),(26,368,NULL,NULL,1,NULL,NULL,NULL),(26,427,NULL,NULL,1,NULL,NULL,NULL),(26,366,NULL,NULL,1,NULL,NULL,NULL),(26,339,NULL,NULL,1,NULL,NULL,NULL),(26,365,NULL,NULL,1,NULL,NULL,NULL),(26,394,NULL,NULL,1,NULL,NULL,NULL),(26,408,NULL,NULL,1,NULL,NULL,NULL),(26,389,NULL,NULL,1,NULL,NULL,NULL),(26,372,NULL,NULL,1,NULL,NULL,NULL),(26,369,NULL,NULL,1,NULL,NULL,NULL),(26,407,NULL,NULL,1,NULL,NULL,NULL),(26,396,NULL,NULL,1,NULL,NULL,NULL),(27,350,NULL,NULL,1,NULL,NULL,NULL),(27,348,NULL,NULL,1,NULL,NULL,NULL),(27,397,NULL,NULL,1,NULL,NULL,NULL),(27,384,NULL,NULL,1,NULL,NULL,NULL),(27,354,NULL,NULL,1,NULL,NULL,NULL),(27,418,NULL,NULL,1,NULL,NULL,NULL),(27,382,NULL,NULL,1,NULL,NULL,NULL),(27,355,NULL,NULL,1,NULL,NULL,NULL),(27,381,NULL,NULL,1,NULL,NULL,NULL),(27,419,NULL,NULL,1,NULL,NULL,NULL),(27,347,NULL,NULL,1,NULL,NULL,NULL),(27,420,NULL,NULL,1,NULL,NULL,NULL),(27,411,NULL,NULL,1,NULL,NULL,NULL),(27,421,NULL,NULL,1,NULL,NULL,NULL),(27,345,NULL,NULL,1,NULL,NULL,NULL),(27,353,NULL,NULL,1,NULL,NULL,NULL),(27,341,NULL,NULL,1,NULL,NULL,NULL),(27,415,NULL,NULL,1,NULL,NULL,NULL),(27,349,NULL,NULL,1,NULL,NULL,NULL),(27,367,NULL,NULL,1,NULL,NULL,NULL),(27,362,NULL,NULL,1,NULL,NULL,NULL),(27,351,NULL,NULL,1,NULL,NULL,NULL),(27,371,NULL,NULL,1,NULL,NULL,NULL),(27,383,NULL,NULL,1,NULL,NULL,NULL),(27,385,NULL,NULL,1,NULL,NULL,NULL),(27,376,NULL,NULL,1,NULL,NULL,NULL),(27,363,NULL,NULL,1,NULL,NULL,NULL),(27,344,NULL,NULL,1,NULL,NULL,NULL),(27,380,NULL,NULL,1,NULL,NULL,NULL),(27,392,NULL,NULL,1,NULL,NULL,NULL),(27,422,NULL,NULL,1,NULL,NULL,NULL),(27,358,NULL,NULL,1,NULL,NULL,NULL),(27,373,NULL,NULL,1,NULL,NULL,NULL),(27,364,NULL,NULL,1,NULL,NULL,NULL),(27,375,NULL,NULL,1,NULL,NULL,NULL),(27,357,NULL,NULL,1,NULL,NULL,NULL),(27,377,NULL,NULL,1,NULL,NULL,NULL),(27,423,NULL,NULL,1,NULL,NULL,NULL),(27,374,NULL,NULL,1,NULL,NULL,NULL),(27,346,NULL,NULL,1,NULL,NULL,NULL),(27,409,NULL,NULL,1,NULL,NULL,NULL),(27,416,NULL,NULL,1,NULL,NULL,NULL),(27,424,NULL,NULL,1,NULL,NULL,NULL),(27,425,NULL,NULL,1,NULL,NULL,NULL),(27,404,NULL,NULL,1,NULL,NULL,NULL),(27,393,NULL,NULL,1,NULL,NULL,NULL),(27,378,NULL,NULL,1,NULL,NULL,NULL),(27,426,NULL,NULL,1,NULL,NULL,NULL),(27,343,NULL,NULL,1,NULL,NULL,NULL),(27,360,NULL,NULL,1,NULL,NULL,NULL),(27,402,NULL,NULL,1,NULL,NULL,NULL),(27,412,NULL,NULL,1,NULL,NULL,NULL),(27,386,NULL,NULL,1,NULL,NULL,NULL),(27,379,NULL,NULL,1,NULL,NULL,NULL),(27,401,NULL,NULL,1,NULL,NULL,NULL),(27,359,NULL,NULL,1,NULL,NULL,NULL),(27,352,NULL,NULL,1,NULL,NULL,NULL),(27,338,NULL,NULL,1,NULL,NULL,NULL),(27,395,NULL,NULL,1,NULL,NULL,NULL),(27,356,NULL,NULL,1,NULL,NULL,NULL),(27,414,NULL,NULL,1,NULL,NULL,NULL),(27,410,NULL,NULL,1,NULL,NULL,NULL),(27,403,NULL,NULL,1,NULL,NULL,NULL),(27,361,NULL,NULL,1,NULL,NULL,NULL),(27,391,NULL,NULL,1,NULL,NULL,NULL),(27,342,NULL,NULL,1,NULL,NULL,NULL),(27,406,NULL,NULL,1,NULL,NULL,NULL),(27,370,NULL,NULL,1,NULL,NULL,NULL),(27,387,NULL,NULL,1,NULL,NULL,NULL),(27,368,NULL,NULL,1,NULL,NULL,NULL),(27,427,NULL,NULL,1,NULL,NULL,NULL),(27,366,NULL,NULL,1,NULL,NULL,NULL),(27,339,NULL,NULL,1,NULL,NULL,NULL),(27,365,NULL,NULL,1,NULL,NULL,NULL),(27,394,NULL,NULL,1,NULL,NULL,NULL),(27,408,NULL,NULL,1,NULL,NULL,NULL),(27,389,NULL,NULL,1,NULL,NULL,NULL),(27,372,NULL,NULL,1,NULL,NULL,NULL),(27,369,NULL,NULL,1,NULL,NULL,NULL),(27,407,NULL,NULL,1,NULL,NULL,NULL),(27,396,NULL,NULL,1,NULL,NULL,NULL),(28,350,NULL,NULL,1,NULL,NULL,NULL),(28,348,NULL,NULL,1,NULL,NULL,NULL),(28,397,NULL,NULL,1,NULL,NULL,NULL),(28,384,NULL,NULL,1,NULL,NULL,NULL),(28,354,NULL,NULL,1,NULL,NULL,NULL),(28,418,NULL,NULL,1,NULL,NULL,NULL),(28,382,NULL,NULL,1,NULL,NULL,NULL),(28,355,NULL,NULL,1,NULL,NULL,NULL),(28,381,NULL,NULL,1,NULL,NULL,NULL),(28,419,NULL,NULL,1,NULL,NULL,NULL),(28,347,NULL,NULL,1,NULL,NULL,NULL),(28,420,NULL,NULL,1,NULL,NULL,NULL),(28,411,NULL,NULL,1,NULL,NULL,NULL),(28,421,NULL,NULL,1,NULL,NULL,NULL),(28,345,NULL,NULL,1,NULL,NULL,NULL),(28,353,NULL,NULL,1,NULL,NULL,NULL),(28,341,NULL,NULL,1,NULL,NULL,NULL),(28,415,NULL,NULL,1,NULL,NULL,NULL),(28,349,NULL,NULL,1,NULL,NULL,NULL),(28,367,NULL,NULL,1,NULL,NULL,NULL),(28,362,NULL,NULL,1,NULL,NULL,NULL),(28,351,NULL,NULL,1,NULL,NULL,NULL),(28,371,NULL,NULL,1,NULL,NULL,NULL),(28,383,NULL,NULL,1,NULL,NULL,NULL),(28,385,NULL,NULL,1,NULL,NULL,NULL),(28,376,NULL,NULL,1,NULL,NULL,NULL),(28,363,NULL,NULL,1,NULL,NULL,NULL),(28,344,NULL,NULL,1,NULL,NULL,NULL),(28,380,NULL,NULL,1,NULL,NULL,NULL),(28,392,NULL,NULL,1,NULL,NULL,NULL),(28,422,NULL,NULL,1,NULL,NULL,NULL),(28,358,NULL,NULL,1,NULL,NULL,NULL),(28,373,NULL,NULL,1,NULL,NULL,NULL),(28,364,NULL,NULL,1,NULL,NULL,NULL),(28,375,NULL,NULL,1,NULL,NULL,NULL),(28,357,NULL,NULL,1,NULL,NULL,NULL),(28,377,NULL,NULL,1,NULL,NULL,NULL),(28,423,NULL,NULL,1,NULL,NULL,NULL),(28,374,NULL,NULL,1,NULL,NULL,NULL),(28,346,NULL,NULL,1,NULL,NULL,NULL),(28,409,NULL,NULL,1,NULL,NULL,NULL),(28,416,NULL,NULL,1,NULL,NULL,NULL),(28,424,NULL,NULL,1,NULL,NULL,NULL),(28,425,NULL,NULL,1,NULL,NULL,NULL),(28,404,NULL,NULL,1,NULL,NULL,NULL),(28,393,NULL,NULL,1,NULL,NULL,NULL),(28,378,NULL,NULL,1,NULL,NULL,NULL),(28,426,NULL,NULL,1,NULL,NULL,NULL),(28,343,NULL,NULL,1,NULL,NULL,NULL),(28,360,NULL,NULL,1,NULL,NULL,NULL),(28,402,NULL,NULL,1,NULL,NULL,NULL),(28,412,NULL,NULL,1,NULL,NULL,NULL),(28,386,NULL,NULL,1,NULL,NULL,NULL),(28,379,NULL,NULL,1,NULL,NULL,NULL),(28,401,NULL,NULL,1,NULL,NULL,NULL),(28,359,NULL,NULL,1,NULL,NULL,NULL),(28,352,NULL,NULL,1,NULL,NULL,NULL),(28,338,NULL,NULL,1,NULL,NULL,NULL),(28,395,NULL,NULL,1,NULL,NULL,NULL),(28,356,NULL,NULL,1,NULL,NULL,NULL),(28,414,NULL,NULL,1,NULL,NULL,NULL),(28,410,NULL,NULL,1,NULL,NULL,NULL),(28,403,NULL,NULL,1,NULL,NULL,NULL),(28,361,NULL,NULL,1,NULL,NULL,NULL),(28,391,NULL,NULL,1,NULL,NULL,NULL),(28,342,NULL,NULL,1,NULL,NULL,NULL),(28,406,NULL,NULL,1,NULL,NULL,NULL),(28,370,NULL,NULL,1,NULL,NULL,NULL),(28,387,NULL,NULL,1,NULL,NULL,NULL),(28,368,NULL,NULL,1,NULL,NULL,NULL),(28,427,NULL,NULL,1,NULL,NULL,NULL),(28,366,NULL,NULL,1,NULL,NULL,NULL),(28,339,NULL,NULL,1,NULL,NULL,NULL),(28,365,NULL,NULL,1,NULL,NULL,NULL),(28,394,NULL,NULL,1,NULL,NULL,NULL),(28,408,NULL,NULL,1,NULL,NULL,NULL),(28,389,NULL,NULL,1,NULL,NULL,NULL),(28,372,NULL,NULL,1,NULL,NULL,NULL),(28,369,NULL,NULL,1,NULL,NULL,NULL),(28,407,NULL,NULL,1,NULL,NULL,NULL),(28,396,NULL,NULL,1,NULL,NULL,NULL),(25,429,NULL,NULL,1,NULL,NULL,NULL),(27,429,NULL,NULL,1,NULL,NULL,NULL),(23,429,NULL,NULL,1,NULL,NULL,NULL),(28,429,NULL,NULL,1,NULL,NULL,NULL),(24,429,NULL,NULL,1,NULL,NULL,NULL),(26,429,NULL,NULL,1,NULL,NULL,NULL),(23,431,18,NULL,1,NULL,NULL,NULL),(25,431,NULL,NULL,1,NULL,NULL,NULL),(27,431,NULL,NULL,1,NULL,NULL,NULL),(28,431,NULL,NULL,1,NULL,NULL,NULL),(24,431,NULL,NULL,1,NULL,NULL,NULL),(26,431,NULL,NULL,1,NULL,NULL,NULL),(25,430,NULL,NULL,1,NULL,NULL,NULL),(27,430,NULL,NULL,1,NULL,NULL,NULL),(23,430,18,NULL,1,NULL,NULL,NULL),(28,430,NULL,NULL,1,NULL,NULL,NULL),(24,430,NULL,NULL,1,NULL,NULL,NULL),(26,430,NULL,NULL,1,NULL,NULL,NULL),(25,428,NULL,NULL,1,NULL,NULL,NULL),(27,428,NULL,NULL,1,NULL,NULL,NULL),(23,428,18,NULL,1,NULL,NULL,NULL),(28,428,NULL,NULL,1,NULL,NULL,NULL),(24,428,NULL,NULL,1,NULL,NULL,NULL),(26,428,NULL,NULL,1,NULL,NULL,NULL),(33,428,NULL,NULL,1,NULL,NULL,NULL),(33,350,NULL,NULL,1,NULL,NULL,NULL),(33,348,NULL,NULL,1,NULL,NULL,NULL),(33,397,NULL,NULL,1,NULL,NULL,NULL),(33,384,NULL,NULL,1,NULL,NULL,NULL),(33,354,NULL,NULL,1,NULL,NULL,NULL),(33,418,NULL,NULL,1,NULL,NULL,NULL),(33,382,NULL,NULL,1,NULL,NULL,NULL),(33,355,NULL,NULL,1,NULL,NULL,NULL),(33,381,NULL,NULL,1,NULL,NULL,NULL),(33,419,NULL,NULL,1,NULL,NULL,NULL),(33,347,NULL,NULL,1,NULL,NULL,NULL),(33,420,NULL,NULL,1,NULL,NULL,NULL),(33,411,NULL,NULL,1,NULL,NULL,NULL),(33,421,NULL,NULL,1,NULL,NULL,NULL),(33,345,NULL,NULL,1,NULL,NULL,NULL),(33,353,NULL,NULL,1,NULL,NULL,NULL),(33,341,NULL,NULL,1,NULL,NULL,NULL),(33,415,NULL,NULL,1,NULL,NULL,NULL),(33,349,NULL,NULL,1,NULL,NULL,NULL),(33,364,NULL,NULL,1,NULL,NULL,NULL),(33,367,NULL,NULL,1,NULL,NULL,NULL),(33,362,NULL,NULL,1,NULL,NULL,NULL),(33,351,NULL,NULL,1,NULL,NULL,NULL),(33,371,NULL,NULL,1,NULL,NULL,NULL),(33,387,NULL,NULL,1,NULL,NULL,NULL),(33,383,NULL,NULL,1,NULL,NULL,NULL),(33,385,NULL,NULL,1,NULL,NULL,NULL),(33,376,NULL,NULL,1,NULL,NULL,NULL),(33,363,NULL,NULL,1,NULL,NULL,NULL),(33,344,NULL,NULL,1,NULL,NULL,NULL),(33,380,NULL,NULL,1,NULL,NULL,NULL),(33,392,NULL,NULL,1,NULL,NULL,NULL),(33,422,NULL,NULL,1,NULL,NULL,NULL),(33,358,NULL,NULL,1,NULL,NULL,NULL),(33,373,NULL,NULL,1,NULL,NULL,NULL),(33,375,NULL,NULL,1,NULL,NULL,NULL),(33,357,NULL,NULL,1,NULL,NULL,NULL),(33,377,NULL,NULL,1,NULL,NULL,NULL),(33,423,NULL,NULL,1,NULL,NULL,NULL),(33,374,NULL,NULL,1,NULL,NULL,NULL),(33,346,NULL,NULL,1,NULL,NULL,NULL),(33,409,NULL,NULL,1,NULL,NULL,NULL),(33,416,NULL,NULL,1,NULL,NULL,NULL),(33,424,NULL,NULL,1,NULL,NULL,NULL),(33,425,NULL,NULL,1,NULL,NULL,NULL),(33,404,NULL,NULL,1,NULL,NULL,NULL),(33,426,NULL,NULL,1,NULL,NULL,NULL),(33,378,NULL,NULL,1,NULL,NULL,NULL),(33,343,NULL,NULL,1,NULL,NULL,NULL),(33,360,NULL,NULL,1,NULL,NULL,NULL),(33,402,NULL,NULL,1,NULL,NULL,NULL),(33,412,NULL,NULL,1,NULL,NULL,NULL),(33,386,NULL,NULL,1,NULL,NULL,NULL),(33,379,NULL,NULL,1,NULL,NULL,NULL),(33,401,NULL,NULL,1,NULL,NULL,NULL),(33,359,NULL,NULL,1,NULL,NULL,NULL),(33,352,NULL,NULL,1,NULL,NULL,NULL),(33,338,NULL,NULL,1,NULL,NULL,NULL),(33,395,NULL,NULL,1,NULL,NULL,NULL),(33,356,NULL,NULL,1,NULL,NULL,NULL),(33,414,NULL,NULL,1,NULL,NULL,NULL),(33,410,NULL,NULL,1,NULL,NULL,NULL),(33,429,NULL,NULL,1,NULL,NULL,NULL),(33,430,NULL,NULL,1,NULL,NULL,NULL),(33,431,NULL,NULL,1,NULL,NULL,NULL),(33,361,NULL,NULL,1,NULL,NULL,NULL),(33,391,NULL,NULL,1,NULL,NULL,NULL),(33,342,NULL,NULL,1,NULL,NULL,NULL),(33,406,NULL,NULL,1,NULL,NULL,NULL),(33,370,NULL,NULL,1,NULL,NULL,NULL),(33,393,NULL,NULL,1,NULL,NULL,NULL),(33,427,NULL,NULL,1,NULL,NULL,NULL),(33,366,NULL,NULL,1,NULL,NULL,NULL),(33,403,NULL,NULL,1,NULL,NULL,NULL),(33,339,NULL,NULL,1,NULL,NULL,NULL),(33,368,NULL,NULL,1,NULL,NULL,NULL),(33,365,NULL,NULL,1,NULL,NULL,NULL),(33,394,NULL,NULL,1,NULL,NULL,NULL),(33,408,NULL,NULL,1,NULL,NULL,NULL),(33,389,NULL,NULL,1,NULL,NULL,NULL),(33,372,NULL,NULL,1,NULL,NULL,NULL),(33,369,NULL,NULL,1,NULL,NULL,NULL),(33,407,NULL,NULL,1,NULL,NULL,NULL),(33,396,NULL,NULL,1,NULL,NULL,NULL),(25,432,NULL,NULL,1,NULL,NULL,NULL),(27,432,NULL,NULL,1,NULL,NULL,NULL),(33,432,NULL,NULL,1,NULL,NULL,NULL),(23,432,18,NULL,1,NULL,NULL,NULL),(28,432,NULL,NULL,1,NULL,NULL,NULL),(24,432,NULL,NULL,1,NULL,NULL,NULL),(26,432,NULL,NULL,1,NULL,NULL,NULL),(23,433,18,NULL,1,NULL,NULL,NULL),(34,434,NULL,NULL,1,NULL,NULL,NULL),(34,435,NULL,NULL,1,NULL,NULL,NULL),(34,436,NULL,NULL,1,NULL,NULL,NULL),(34,437,NULL,NULL,1,NULL,NULL,NULL),(34,438,NULL,NULL,1,NULL,NULL,NULL),(34,439,NULL,NULL,1,NULL,NULL,NULL),(3,413,NULL,NULL,1,NULL,NULL,NULL),(3,400,NULL,NULL,1,NULL,NULL,NULL),(3,405,NULL,NULL,1,NULL,NULL,NULL),(3,432,NULL,NULL,1,NULL,NULL,NULL),(3,428,NULL,NULL,1,NULL,NULL,NULL),(3,398,NULL,NULL,1,NULL,NULL,NULL),(3,388,NULL,NULL,1,NULL,NULL,NULL),(3,350,NULL,NULL,1,NULL,NULL,NULL),(3,348,NULL,NULL,1,NULL,NULL,NULL),(3,397,NULL,NULL,1,NULL,NULL,NULL),(3,384,NULL,NULL,1,NULL,NULL,NULL),(3,354,NULL,NULL,1,NULL,NULL,NULL),(3,418,NULL,NULL,1,NULL,NULL,NULL),(3,382,NULL,NULL,1,NULL,NULL,NULL),(3,355,NULL,NULL,1,NULL,NULL,NULL),(3,381,NULL,NULL,1,NULL,NULL,NULL),(3,419,NULL,NULL,1,NULL,NULL,NULL),(3,347,NULL,NULL,1,NULL,NULL,NULL),(3,420,NULL,NULL,1,NULL,NULL,NULL),(3,411,NULL,NULL,1,NULL,NULL,NULL),(3,421,NULL,NULL,1,NULL,NULL,NULL),(3,345,NULL,NULL,1,NULL,NULL,NULL),(3,353,NULL,NULL,1,NULL,NULL,NULL),(3,341,NULL,NULL,1,NULL,NULL,NULL),(3,415,NULL,NULL,1,NULL,NULL,NULL),(3,349,NULL,NULL,1,NULL,NULL,NULL),(3,364,NULL,NULL,1,NULL,NULL,NULL),(3,367,NULL,NULL,1,NULL,NULL,NULL),(3,362,NULL,NULL,1,NULL,NULL,NULL),(3,351,NULL,NULL,1,NULL,NULL,NULL),(3,371,NULL,NULL,1,NULL,NULL,NULL),(3,387,NULL,NULL,1,NULL,NULL,NULL),(3,383,NULL,NULL,1,NULL,NULL,NULL),(3,385,NULL,NULL,1,NULL,NULL,NULL),(3,376,NULL,NULL,1,NULL,NULL,NULL),(3,363,NULL,NULL,1,NULL,NULL,NULL),(3,344,NULL,NULL,1,NULL,NULL,NULL),(3,380,NULL,NULL,1,NULL,NULL,NULL),(3,433,NULL,NULL,1,NULL,NULL,NULL),(3,392,NULL,NULL,1,NULL,NULL,NULL),(3,422,NULL,NULL,1,NULL,NULL,NULL),(3,358,NULL,NULL,1,NULL,NULL,NULL),(3,373,NULL,NULL,1,NULL,NULL,NULL),(3,375,NULL,NULL,1,NULL,NULL,NULL),(3,357,NULL,NULL,1,NULL,NULL,NULL),(3,377,NULL,NULL,1,NULL,NULL,NULL),(3,423,NULL,NULL,1,NULL,NULL,NULL),(3,374,NULL,NULL,1,NULL,NULL,NULL),(3,346,NULL,NULL,1,NULL,NULL,NULL),(3,409,NULL,NULL,1,NULL,NULL,NULL),(3,399,NULL,NULL,1,NULL,NULL,NULL),(3,416,NULL,NULL,1,NULL,NULL,NULL),(3,424,NULL,NULL,1,NULL,NULL,NULL),(3,425,NULL,NULL,1,NULL,NULL,NULL),(3,435,NULL,NULL,1,NULL,NULL,NULL),(3,404,NULL,NULL,1,NULL,NULL,NULL),(3,426,NULL,NULL,1,NULL,NULL,NULL),(3,378,NULL,NULL,1,NULL,NULL,NULL),(3,343,NULL,NULL,1,NULL,NULL,NULL),(3,360,NULL,NULL,1,NULL,NULL,NULL),(3,402,NULL,NULL,1,NULL,NULL,NULL),(3,412,NULL,NULL,1,NULL,NULL,NULL),(3,386,NULL,NULL,1,NULL,NULL,NULL),(3,379,NULL,NULL,1,NULL,NULL,NULL),(3,401,NULL,NULL,1,NULL,NULL,NULL),(3,359,NULL,NULL,1,NULL,NULL,NULL),(3,352,NULL,NULL,1,NULL,NULL,NULL),(3,414,NULL,NULL,1,NULL,NULL,NULL),(3,338,NULL,NULL,1,NULL,NULL,NULL),(3,395,NULL,NULL,1,NULL,NULL,NULL),(3,356,NULL,NULL,1,NULL,NULL,NULL),(3,410,NULL,NULL,1,NULL,NULL,NULL),(3,431,NULL,NULL,1,NULL,NULL,NULL),(3,429,NULL,NULL,1,NULL,NULL,NULL),(3,434,NULL,NULL,1,NULL,NULL,NULL),(3,436,NULL,NULL,1,NULL,NULL,NULL),(3,437,NULL,NULL,1,NULL,NULL,NULL),(3,438,NULL,NULL,1,NULL,NULL,NULL),(3,439,NULL,NULL,1,NULL,NULL,NULL),(3,361,NULL,NULL,1,NULL,NULL,NULL),(3,391,NULL,NULL,1,NULL,NULL,NULL),(3,342,NULL,NULL,1,NULL,NULL,NULL),(3,406,NULL,NULL,1,NULL,NULL,NULL),(3,370,NULL,NULL,1,NULL,NULL,NULL),(3,393,NULL,NULL,1,NULL,NULL,NULL),(3,389,NULL,NULL,1,NULL,NULL,NULL),(3,427,NULL,NULL,1,NULL,NULL,NULL),(3,366,NULL,NULL,1,NULL,NULL,NULL),(3,403,NULL,NULL,1,NULL,NULL,NULL),(3,339,NULL,NULL,1,NULL,NULL,NULL),(3,368,NULL,NULL,1,NULL,NULL,NULL),(3,365,NULL,NULL,1,NULL,NULL,NULL),(3,394,NULL,NULL,1,NULL,NULL,NULL),(3,408,NULL,NULL,1,NULL,NULL,NULL),(3,430,NULL,NULL,1,NULL,NULL,NULL),(3,372,NULL,NULL,1,NULL,NULL,NULL),(3,369,NULL,NULL,1,NULL,NULL,NULL),(3,407,NULL,NULL,1,NULL,NULL,NULL),(3,396,NULL,NULL,1,NULL,NULL,NULL),(25,433,NULL,NULL,1,NULL,NULL,NULL),(27,433,NULL,NULL,1,NULL,NULL,NULL),(33,433,NULL,NULL,1,NULL,NULL,NULL),(28,433,NULL,NULL,1,NULL,NULL,NULL),(24,433,NULL,NULL,1,NULL,NULL,NULL),(26,433,NULL,NULL,1,NULL,NULL,NULL),(23,440,18,NULL,1,NULL,NULL,NULL),(35,432,NULL,NULL,1,NULL,NULL,NULL),(35,350,NULL,NULL,1,NULL,NULL,NULL),(35,348,NULL,NULL,1,NULL,NULL,NULL),(35,397,NULL,NULL,1,NULL,NULL,NULL),(35,384,NULL,NULL,1,NULL,NULL,NULL),(35,354,NULL,NULL,1,NULL,NULL,NULL),(35,418,NULL,NULL,1,NULL,NULL,NULL),(35,382,NULL,NULL,1,NULL,NULL,NULL),(35,355,NULL,NULL,1,NULL,NULL,NULL),(35,381,NULL,NULL,1,NULL,NULL,NULL),(35,419,NULL,NULL,1,NULL,NULL,NULL),(35,347,NULL,NULL,1,NULL,NULL,NULL),(35,420,NULL,NULL,1,NULL,NULL,NULL),(35,411,NULL,NULL,1,NULL,NULL,NULL),(35,421,NULL,NULL,1,NULL,NULL,NULL),(35,345,NULL,NULL,1,NULL,NULL,NULL),(35,353,NULL,NULL,1,NULL,NULL,NULL),(35,341,NULL,NULL,1,NULL,NULL,NULL),(35,415,NULL,NULL,1,NULL,NULL,NULL),(35,349,NULL,NULL,1,NULL,NULL,NULL),(35,364,NULL,NULL,1,NULL,NULL,NULL),(35,367,NULL,NULL,1,NULL,NULL,NULL),(35,362,NULL,NULL,1,NULL,NULL,NULL),(35,351,NULL,NULL,1,NULL,NULL,NULL),(35,371,NULL,NULL,1,NULL,NULL,NULL),(35,387,NULL,NULL,1,NULL,NULL,NULL),(35,383,NULL,NULL,1,NULL,NULL,NULL),(35,385,NULL,NULL,1,NULL,NULL,NULL),(35,376,NULL,NULL,1,NULL,NULL,NULL),(35,363,NULL,NULL,1,NULL,NULL,NULL),(35,344,NULL,NULL,1,NULL,NULL,NULL),(35,380,NULL,NULL,1,NULL,NULL,NULL),(35,433,NULL,NULL,1,NULL,NULL,NULL),(35,392,NULL,NULL,1,NULL,NULL,NULL),(35,422,NULL,NULL,1,NULL,NULL,NULL),(35,358,NULL,NULL,1,NULL,NULL,NULL),(35,373,NULL,NULL,1,NULL,NULL,NULL),(35,375,NULL,NULL,1,NULL,NULL,NULL),(35,357,NULL,NULL,1,NULL,NULL,NULL),(35,377,NULL,NULL,1,NULL,NULL,NULL),(35,423,NULL,NULL,1,NULL,NULL,NULL),(35,374,NULL,NULL,1,NULL,NULL,NULL),(35,346,NULL,NULL,1,NULL,NULL,NULL),(35,409,NULL,NULL,1,NULL,NULL,NULL),(35,440,NULL,NULL,1,NULL,NULL,NULL),(35,416,NULL,NULL,1,NULL,NULL,NULL),(35,424,NULL,NULL,1,NULL,NULL,NULL),(35,425,NULL,NULL,1,NULL,NULL,NULL),(35,404,NULL,NULL,1,NULL,NULL,NULL),(35,426,NULL,NULL,1,NULL,NULL,NULL),(35,378,NULL,NULL,1,NULL,NULL,NULL),(35,343,NULL,NULL,1,NULL,NULL,NULL),(35,360,NULL,NULL,1,NULL,NULL,NULL),(35,402,NULL,NULL,1,NULL,NULL,NULL),(35,412,NULL,NULL,1,NULL,NULL,NULL),(35,386,NULL,NULL,1,NULL,NULL,NULL),(35,379,NULL,NULL,1,NULL,NULL,NULL),(35,401,NULL,NULL,1,NULL,NULL,NULL),(35,359,NULL,NULL,1,NULL,NULL,NULL),(35,352,NULL,NULL,1,NULL,NULL,NULL),(35,338,NULL,NULL,1,NULL,NULL,NULL),(35,395,NULL,NULL,1,NULL,NULL,NULL),(35,356,NULL,NULL,1,NULL,NULL,NULL),(35,414,NULL,NULL,1,NULL,NULL,NULL),(35,410,NULL,NULL,1,NULL,NULL,NULL),(35,431,NULL,NULL,1,NULL,NULL,NULL),(35,429,NULL,NULL,1,NULL,NULL,NULL),(35,361,NULL,NULL,1,NULL,NULL,NULL),(35,391,NULL,NULL,1,NULL,NULL,NULL),(35,342,NULL,NULL,1,NULL,NULL,NULL),(35,406,NULL,NULL,1,NULL,NULL,NULL),(35,370,NULL,NULL,1,NULL,NULL,NULL),(35,428,NULL,NULL,1,NULL,NULL,NULL),(35,393,NULL,NULL,1,NULL,NULL,NULL),(35,389,NULL,NULL,1,NULL,NULL,NULL),(35,427,NULL,NULL,1,NULL,NULL,NULL),(35,366,NULL,NULL,1,NULL,NULL,NULL),(35,403,NULL,NULL,1,NULL,NULL,NULL),(35,339,NULL,NULL,1,NULL,NULL,NULL),(35,368,NULL,NULL,1,NULL,NULL,NULL),(35,365,NULL,NULL,1,NULL,NULL,NULL),(35,394,NULL,NULL,1,NULL,NULL,NULL),(35,408,NULL,NULL,1,NULL,NULL,NULL),(35,430,NULL,NULL,1,NULL,NULL,NULL),(35,372,NULL,NULL,1,NULL,NULL,NULL),(35,369,NULL,NULL,1,NULL,NULL,NULL),(35,407,NULL,NULL,1,NULL,NULL,NULL),(35,396,NULL,NULL,1,NULL,NULL,NULL),(36,432,NULL,NULL,1,NULL,NULL,NULL),(36,350,NULL,NULL,1,NULL,NULL,NULL),(36,348,NULL,NULL,1,NULL,NULL,NULL),(36,397,NULL,NULL,1,NULL,NULL,NULL),(36,384,NULL,NULL,1,NULL,NULL,NULL),(36,354,NULL,NULL,1,NULL,NULL,NULL),(36,418,NULL,NULL,1,NULL,NULL,NULL),(36,382,NULL,NULL,1,NULL,NULL,NULL),(36,355,NULL,NULL,1,NULL,NULL,NULL),(36,381,NULL,NULL,1,NULL,NULL,NULL),(36,419,NULL,NULL,1,NULL,NULL,NULL),(36,347,NULL,NULL,1,NULL,NULL,NULL),(36,420,NULL,NULL,1,NULL,NULL,NULL),(36,411,NULL,NULL,1,NULL,NULL,NULL),(36,421,NULL,NULL,1,NULL,NULL,NULL),(36,345,NULL,NULL,1,NULL,NULL,NULL),(36,353,NULL,NULL,1,NULL,NULL,NULL),(36,341,NULL,NULL,1,NULL,NULL,NULL),(36,415,NULL,NULL,1,NULL,NULL,NULL),(36,349,NULL,NULL,1,NULL,NULL,NULL),(36,364,NULL,NULL,1,NULL,NULL,NULL),(36,367,NULL,NULL,1,NULL,NULL,NULL),(36,362,NULL,NULL,1,NULL,NULL,NULL),(36,351,NULL,NULL,1,NULL,NULL,NULL),(36,371,NULL,NULL,1,NULL,NULL,NULL),(36,387,NULL,NULL,1,NULL,NULL,NULL),(36,383,NULL,NULL,1,NULL,NULL,NULL),(36,385,NULL,NULL,1,NULL,NULL,NULL),(36,376,NULL,NULL,1,NULL,NULL,NULL),(36,363,NULL,NULL,1,NULL,NULL,NULL),(36,344,NULL,NULL,1,NULL,NULL,NULL),(36,380,NULL,NULL,1,NULL,NULL,NULL),(36,433,NULL,NULL,1,NULL,NULL,NULL),(36,392,NULL,NULL,1,NULL,NULL,NULL),(36,422,NULL,NULL,1,NULL,NULL,NULL),(36,358,NULL,NULL,1,NULL,NULL,NULL),(36,373,NULL,NULL,1,NULL,NULL,NULL),(36,375,NULL,NULL,1,NULL,NULL,NULL),(36,357,NULL,NULL,1,NULL,NULL,NULL),(36,377,NULL,NULL,1,NULL,NULL,NULL),(36,423,NULL,NULL,1,NULL,NULL,NULL),(36,374,NULL,NULL,1,NULL,NULL,NULL),(36,346,NULL,NULL,1,NULL,NULL,NULL),(36,409,NULL,NULL,1,NULL,NULL,NULL),(36,440,NULL,NULL,1,NULL,NULL,NULL),(36,416,NULL,NULL,1,NULL,NULL,NULL),(36,424,NULL,NULL,1,NULL,NULL,NULL),(36,425,NULL,NULL,1,NULL,NULL,NULL),(36,404,NULL,NULL,1,NULL,NULL,NULL),(36,426,NULL,NULL,1,NULL,NULL,NULL),(36,378,NULL,NULL,1,NULL,NULL,NULL),(36,343,NULL,NULL,1,NULL,NULL,NULL),(36,360,NULL,NULL,1,NULL,NULL,NULL),(36,402,NULL,NULL,1,NULL,NULL,NULL),(36,412,NULL,NULL,1,NULL,NULL,NULL),(36,386,NULL,NULL,1,NULL,NULL,NULL),(36,379,NULL,NULL,1,NULL,NULL,NULL),(36,401,NULL,NULL,1,NULL,NULL,NULL),(36,359,NULL,NULL,1,NULL,NULL,NULL),(36,352,NULL,NULL,1,NULL,NULL,NULL),(36,338,NULL,NULL,1,NULL,NULL,NULL),(36,395,NULL,NULL,1,NULL,NULL,NULL),(36,356,NULL,NULL,1,NULL,NULL,NULL),(36,414,NULL,NULL,1,NULL,NULL,NULL),(36,410,NULL,NULL,1,NULL,NULL,NULL),(36,431,NULL,NULL,1,NULL,NULL,NULL),(36,429,NULL,NULL,1,NULL,NULL,NULL),(36,361,NULL,NULL,1,NULL,NULL,NULL),(36,391,NULL,NULL,1,NULL,NULL,NULL),(36,342,NULL,NULL,1,NULL,NULL,NULL),(36,406,NULL,NULL,1,NULL,NULL,NULL),(36,370,NULL,NULL,1,NULL,NULL,NULL),(36,428,NULL,NULL,1,NULL,NULL,NULL),(36,393,NULL,NULL,1,NULL,NULL,NULL),(36,389,NULL,NULL,1,NULL,NULL,NULL),(36,427,NULL,NULL,1,NULL,NULL,NULL),(36,366,NULL,NULL,1,NULL,NULL,NULL),(36,403,NULL,NULL,1,NULL,NULL,NULL),(36,339,NULL,NULL,1,NULL,NULL,NULL),(36,368,NULL,NULL,1,NULL,NULL,NULL),(36,365,NULL,NULL,1,NULL,NULL,NULL),(36,394,NULL,NULL,1,NULL,NULL,NULL),(36,408,NULL,NULL,1,NULL,NULL,NULL),(36,430,NULL,NULL,1,NULL,NULL,NULL),(36,372,NULL,NULL,1,NULL,NULL,NULL),(36,369,NULL,NULL,1,NULL,NULL,NULL),(36,407,NULL,NULL,1,NULL,NULL,NULL),(36,396,NULL,NULL,1,NULL,NULL,NULL),(25,440,NULL,NULL,1,NULL,NULL,NULL),(27,440,NULL,NULL,1,NULL,NULL,NULL),(33,440,NULL,NULL,1,NULL,NULL,NULL),(28,440,NULL,NULL,1,NULL,NULL,NULL),(24,440,NULL,NULL,1,NULL,NULL,NULL),(26,440,NULL,NULL,1,NULL,NULL,NULL),(3,441,NULL,NULL,1,NULL,NULL,NULL),(36,441,NULL,NULL,1,NULL,NULL,NULL),(25,441,NULL,NULL,1,NULL,NULL,NULL),(27,441,NULL,NULL,1,NULL,NULL,NULL),(33,441,NULL,NULL,1,NULL,NULL,NULL),(23,441,NULL,NULL,1,NULL,NULL,NULL),(28,441,NULL,NULL,1,NULL,NULL,NULL),(35,441,NULL,NULL,1,NULL,NULL,NULL),(24,441,NULL,NULL,1,NULL,NULL,NULL),(26,441,NULL,NULL,1,NULL,NULL,NULL),(3,442,NULL,NULL,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `user_device_pivot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_driver_position_pivot`
--

DROP TABLE IF EXISTS `user_driver_position_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_driver_position_pivot` (
  `device_id` int(10) unsigned NOT NULL,
  `driver_id` int(10) unsigned NOT NULL,
  `date` datetime DEFAULT NULL,
  KEY `user_driver_position_pivot_device_id_index` (`device_id`),
  KEY `user_driver_position_pivot_driver_id_index` (`driver_id`),
  CONSTRAINT `user_driver_position_pivot_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_driver_position_pivot_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `user_drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_driver_position_pivot`
--

LOCK TABLES `user_driver_position_pivot` WRITE;
/*!40000 ALTER TABLE `user_driver_position_pivot` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_driver_position_pivot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_drivers`
--

DROP TABLE IF EXISTS `user_drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_drivers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `device_id` int(10) unsigned DEFAULT NULL,
  `device_port` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rfid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_drivers_user_id_index` (`user_id`),
  KEY `user_drivers_device_id_index` (`device_id`),
  CONSTRAINT `user_drivers_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_drivers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_drivers`
--

LOCK TABLES `user_drivers` WRITE;
/*!40000 ALTER TABLE `user_drivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_gprs_templates`
--

DROP TABLE IF EXISTS `user_gprs_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_gprs_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_gprs_templates_user_id_index` (`user_id`),
  CONSTRAINT `user_gprs_templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_gprs_templates`
--

LOCK TABLES `user_gprs_templates` WRITE;
/*!40000 ALTER TABLE `user_gprs_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_gprs_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_map_icons`
--

DROP TABLE IF EXISTS `user_map_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_map_icons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `map_icon_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `coordinates` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_map_icons_user_id_foreign` (`user_id`),
  KEY `user_map_icons_map_icon_id_foreign` (`map_icon_id`),
  KEY `user_map_icons_active_index` (`active`),
  CONSTRAINT `user_map_icons_map_icon_id_foreign` FOREIGN KEY (`map_icon_id`) REFERENCES `map_icons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_map_icons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_map_icons`
--

LOCK TABLES `user_map_icons` WRITE;
/*!40000 ALTER TABLE `user_map_icons` DISABLE KEYS */;
INSERT INTO `user_map_icons` VALUES (5,3,49,1,'Service Center','Vehicle maintenance and repair shop ','{\"lat\":33.34458730586423,\"lng\":-111.96856856346132}','2018-03-08 20:57:08','2018-03-10 15:50:43'),(8,3,1,1,'Home','I am home..','{\"lat\":33.47890088545383,\"lng\":-112.1599978208542}','2018-03-12 02:47:06','2018-05-29 04:31:02'),(9,3,1,1,'tghh','my home','{\"lat\":51.781435604431195,\"lng\":32.87109375000001}','2019-09-05 10:04:54','2019-09-05 10:04:54');
/*!40000 ALTER TABLE `user_map_icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_permissions` (
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `remove` tinyint(1) NOT NULL DEFAULT '0',
  KEY `user_permissions_user_id_index` (`user_id`),
  CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` VALUES (27,'devices',1,1,0),(27,'alerts',1,1,0),(27,'geofences',1,1,0),(27,'routes',1,1,0),(27,'poi',1,1,0),(27,'sms_gateway',1,0,0),(27,'protocol',1,0,0),(27,'send_command',1,0,0),(23,'devices',1,1,1),(23,'alerts',1,1,1),(23,'geofences',1,1,1),(23,'routes',1,1,1),(23,'poi',1,1,1),(23,'sms_gateway',1,0,0),(23,'protocol',1,0,0),(23,'send_command',1,0,0),(23,'history',1,0,1),(33,'devices',1,1,0),(33,'alerts',1,1,0),(33,'geofences',1,1,0),(33,'routes',1,1,0),(33,'poi',1,1,0),(33,'sms_gateway',1,0,0),(33,'protocol',1,0,0),(33,'send_command',1,0,0),(33,'history',1,0,0),(26,'devices',1,1,0),(26,'alerts',1,1,0),(26,'geofences',1,1,0),(26,'routes',1,1,0),(26,'poi',1,1,0),(26,'sms_gateway',1,0,0),(26,'protocol',1,0,0),(26,'send_command',1,0,0),(25,'devices',1,1,0),(25,'alerts',1,1,0),(25,'geofences',1,1,0),(25,'routes',1,1,0),(25,'poi',1,1,0),(25,'sms_gateway',1,0,0),(25,'protocol',1,0,0),(25,'send_command',1,0,0),(24,'devices',1,1,0),(24,'alerts',1,1,0),(24,'geofences',1,1,0),(24,'routes',1,1,0),(24,'poi',1,1,0),(24,'sms_gateway',1,0,0),(24,'protocol',1,0,0),(24,'send_command',1,0,0),(24,'history',1,0,0),(28,'devices',1,1,0),(28,'alerts',1,1,0),(28,'geofences',1,1,0),(28,'routes',1,1,0),(28,'poi',1,1,0),(28,'sms_gateway',1,0,0),(28,'protocol',1,0,0),(28,'send_command',1,0,0),(28,'history',1,0,0),(34,'devices',1,1,0),(34,'alerts',1,1,0),(34,'geofences',1,1,0),(34,'routes',1,1,0),(34,'poi',1,1,0),(34,'sms_gateway',1,0,0),(34,'protocol',1,0,0),(34,'send_command',1,0,0),(34,'history',1,0,0),(3,'devices',1,1,1),(3,'alerts',1,1,1),(3,'geofences',1,1,1),(3,'routes',1,1,1),(3,'poi',1,1,1),(3,'sms_gateway',1,0,0),(3,'protocol',1,0,0),(3,'send_command',1,0,0),(3,'history',1,0,1),(35,'devices',1,1,0),(35,'alerts',1,1,0),(35,'geofences',1,1,0),(35,'routes',1,1,0),(35,'poi',1,1,0),(35,'sms_gateway',1,0,0),(35,'protocol',1,0,0),(35,'send_command',1,0,0),(35,'history',1,0,0),(36,'devices',1,1,0),(36,'alerts',1,1,0),(36,'geofences',1,1,0),(36,'routes',1,1,0),(36,'poi',1,1,0),(36,'sms_gateway',1,0,0),(36,'protocol',1,0,0),(36,'send_command',1,0,0),(36,'history',1,0,0);
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sms_templates`
--

DROP TABLE IF EXISTS `user_sms_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sms_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_sms_templates_user_id_index` (`user_id`),
  CONSTRAINT `user_sms_templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sms_templates`
--

LOCK TABLES `user_sms_templates` WRITE;
/*!40000 ALTER TABLE `user_sms_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sms_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `group_id` int(10) unsigned DEFAULT NULL,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `billing_plan_id` int(10) unsigned DEFAULT NULL,
  `map_id` int(10) unsigned DEFAULT NULL,
  `devices_limit` int(10) unsigned DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subscription_expiration` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `loged_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `api_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `available_maps` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a:5:{i:3;s:1:"3";i:1;s:1:"1";i:4;s:1:"4";i:5;s:1:"5";i:2;s:1:"2";}',
  `sms_gateway_app_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sms_gateway_params` text COLLATE utf8_unicode_ci,
  `open_geofence_groups` text COLLATE utf8_unicode_ci,
  `open_device_groups` text COLLATE utf8_unicode_ci,
  `week_start_day` tinyint(4) NOT NULL DEFAULT '1',
  `top_toolbar_open` tinyint(4) NOT NULL DEFAULT '1',
  `map_controls` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '{}',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `unit_of_altitude` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'mt',
  `lang` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `unit_of_distance` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'km',
  `unit_of_capacity` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'lt',
  `timezone_id` int(10) unsigned NOT NULL DEFAULT '57',
  `sms_gateway` tinyint(1) NOT NULL DEFAULT '0',
  `sms_gateway_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_hash_unique` (`api_hash`),
  KEY `users_active_index` (`active`),
  KEY `users_group_id_index` (`group_id`),
  KEY `users_manager_id_index` (`manager_id`),
  KEY `users_billing_plan_id_index` (`billing_plan_id`),
  KEY `users_devices_limit_index` (`devices_limit`),
  KEY `users_loged_at_index` (`loged_at`),
  KEY `users_timezone_id_index` (`timezone_id`),
  KEY `users_sms_gateway_index` (`sms_gateway`),
  CONSTRAINT `users_billing_plan_id_foreign` FOREIGN KEY (`billing_plan_id`) REFERENCES `billing_plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,1,1,NULL,NULL,2,NULL,'Admin','','admin@gmail.com','$2y$10$qI2spCcU7FfbaVK1SeR9nezhdM4NykUMCj3vL6A8y/mr3naEvWMM2','vYdzMEsDHQB9nSgFH64ADBScrD9Lc8jm3fsJ9N6WPuRuBlu8FPZd9agmFEmd','0000-00-00 00:00:00','2022-05-13 09:02:31','$2y$10$8P2MxeWXEJiwOOBgGiWq2eIa31ljp7WLyAaBLa5UMGTzdfiy80Ify','a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00','a:8:{s:14:\"request_method\";s:5:\"plivo\";s:14:\"authentication\";s:1:\"0\";s:8:\"username\";s:26:\"admin@fleetmatricksgps.com\";s:8:\"password\";s:0:\"\";s:8:\"encoding\";s:1:\"0\";s:7:\"auth_id\";s:20:\"MAMJG0MGIYYZEWODY4ZG\";s:10:\"auth_token\";s:40:\"ZDkwYmI2ODIzZDg1ODk3OTZmNjBlMjk1NTdiZTU1\";s:13:\"senders_phone\";s:12:\"+16238667620\";}','[0,\"2\"]','{\"9\":1,\"13\":2,\"14\":\"0\"}',1,1,'{\"m_show_tails\":0}','2017-09-08 20:44:14','2022-05-13 09:02:31','mt','en','km','lt',65,0,'','{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"},{\"field\":\"0551ee62583501b1d24835328371dba1\",\"class\":\"sensor\",\"type\":\"ignition\"}],\"groupby\":\"protocol\"},\"widgets\":{\"status\":\"1\",\"list\":[\"device\",\"sensors\",\"services\",\"streetview\"]}}'),(23,1,3,NULL,NULL,1,NULL,'Cali','Berisha','caliberisha@dauti-kos.com','$2y$10$g1dPbvRx6nHSzivc40e6au91p2W9j09jqNsdwaqexN6KAS93c04Wq','R455hdaSZSfLbz7E7q6Gk4wv6xr4AgZtz2IEZqST3NgQPlBmDAEIsJxc5MTb','0000-00-00 00:00:00','2022-05-12 04:25:05',NULL,'a:6:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";i:6;s:1:\"6\";}','0000-00-00 00:00:00','a:8:{s:14:\"request_method\";s:3:\"get\";s:14:\"authentication\";s:1:\"0\";s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";s:8:\"encoding\";s:1:\"0\";s:7:\"auth_id\";s:0:\"\";s:10:\"auth_token\";s:0:\"\";s:13:\"senders_phone\";s:0:\"\";}','[\"0\"]','{\"9\":19,\"10\":0,\"11\":20,\"12\":\"18\"}',1,1,'[]','2022-02-09 18:39:57','2022-05-12 04:25:05','mt','en','km','lt',65,0,'','{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"},{\"field\":\"imei\",\"class\":\"device\"},{\"field\":\"registration_number\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"plate_number\",\"class\":\"device\"},{\"field\":\"device_model\",\"class\":\"device\"},{\"field\":\"address\",\"class\":\"device\"},{\"field\":\"speed\",\"class\":\"device\"}],\"groupby\":\"protocol\"},\"widgets\":null}'),(24,1,2,23,NULL,1,NULL,'Xheneta','Ismajli','xheneta@dauti-kos.com','$2y$10$6pBX/.xSRMVSDjofqLN2MOAgntbv6nzZBLBL9vvfkYaV9VROUBwgm','UhNcbOTjxt8spcwi0t99ET84sjW7wZKKYZDHwZLuQytIsafTg2XxAmhplSI8','0000-00-00 00:00:00','2022-05-10 08:59:44',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-04-11 03:33:34','2022-05-10 08:59:44','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"protocol\"}}'),(25,1,2,23,NULL,4,NULL,'Astrit','Guri','astrit@dauti-kos.com','$2y$10$0mRZlVLEjV261BIjlkx25ediXZn28ALodGpnm87lrPSWGG3gNhVfq','p0k9PH7xnW3GAhAt2tXuPzzMgO794uvKiSkZhqAMpDvHq7dDP1K65HisYmKV','0000-00-00 00:00:00','2022-04-28 06:38:58',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-04-11 03:35:46','2022-05-02 19:26:51','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"protocol\"}}'),(26,1,2,23,NULL,1,NULL,'Zana','Shabani','zana@dauti-kos.com','$2y$10$NXAuYu3agViIGKP40KIedePexlY1DH5qCoHWBNzgBUX46heO8vLoW','6c0nqGtiFNrgVuNBngBE12GdVnfLrCmCqCyCmUAFPvvWp6HhMyEvzzTW3Gci','0000-00-00 00:00:00','2022-04-19 09:00:22',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-04-11 03:36:57','2022-05-02 19:26:46','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"protocol\"}}'),(27,1,2,23,NULL,1,NULL,'Bleron','Koxha','bleron@dauti-kos.com','$2y$10$VoQYtBENZ/JkwQuwHPM7ye2Vk1XWmQ5NoYq62os8T9OifOd48MrTm','4Pisr2bRLXwlSRSYy0tS7LyxUwDow3TaWWhdi9916N2W2OVNvTm8uiVzEkMT','0000-00-00 00:00:00','2022-05-11 08:41:57',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-04-11 04:04:03','2022-05-11 08:41:57','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"protocol\"}}'),(28,1,2,23,NULL,1,NULL,'Elmedina','Luta','elmedina@dauti-kos.com','$2y$10$NP1zE70FneTwDbnK8mKuSu.RKpXa6ib2./tG6aFmPYCU3AfN6/lDS','CKxXLgs1yiK71XKuNCIltA1oF65hSdZHoxawfD49g671asyV4Gxh84B0jSai','0000-00-00 00:00:00','2022-05-12 07:07:11',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-04-11 04:05:14','2022-05-12 07:07:11','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"protocol\"}}'),(33,1,2,23,NULL,1,NULL,'Bujar','Mema','bujar@dauti-kos.com','$2y$10$WD49G58B0dC3.Y01Z1k7POs9sRNnXuz2YJhjF43pYtFGtyYhkKJBC','freAL2Ed9jUMaTvEiprtUVzAKjOn1UjLoT8O6hW6G0lH0I06qnWGk8zLmcbo','0000-00-00 00:00:00','2022-05-13 04:52:40',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-04-19 09:06:28','2022-05-13 04:52:40','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"protocol\"}}'),(34,1,3,NULL,NULL,2,NULL,'Union','','union@inner-fleet.com','$2y$10$NKEyntTUm.HQTtN4FaypbeEUMZB9cyunHFtJgQMCpLMqyIyNl6OPq','FG4j8G7MwkS55ovziEnHY14NkHCCUkFEJ1s4IjuE33ZTbpM8qKBga2yI8Hg8','0000-00-00 00:00:00','2022-05-09 16:07:51',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-05-01 08:40:32','2022-05-09 16:07:51','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"protocol\"}}'),(35,1,2,23,NULL,1,NULL,'Fatbardha ','Selimi','fatbardha@dauti-kos.com','$2y$10$YTx8AgGgz6YrjxLUd1QVLuuie4TzbIezTd50LD3or40ltLRdl/bBW','enNj5ZOtL7EZH1I7Z5fCbSPbHx2DBj0NATdID0kuI3nv7w6YKegF67QMDn0Z','2023-05-10 08:39:05','2022-05-10 08:40:23',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-05-10 08:39:43','2022-05-10 08:40:23','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":[{\"field\":\"name\",\"class\":\"device\"},{\"field\":\"status\",\"class\":\"device\"},{\"field\":\"time\",\"class\":\"device\"},{\"field\":\"position\",\"class\":\"device\"}],\"groupby\":\"group\"}}'),(36,1,2,23,NULL,1,NULL,'Albana','√áerkini','albana@dauti-kos.com','$2y$10$asrzWVJocaYpaTn6dk44xO0LjhnhD5tnJiRwipEp4SljkbQwdY9G2','Vy513eDG6lAS0Cn9KSsfB65RYZXdBKCA77xKIKLh3izvYxOh50oHckS8c7DB','0000-00-00 00:00:00','2022-05-12 19:03:48',NULL,'a:5:{i:3;s:1:\"3\";i:1;s:1:\"1\";i:4;s:1:\"4\";i:5;s:1:\"5\";i:2;s:1:\"2\";}','0000-00-00 00:00:00',NULL,'[\"0\"]','[\"0\"]',1,1,'{}','2022-05-10 08:43:35','2022-05-12 19:03:48','mt','en','km','lt',65,0,NULL,'{\"listview\":{\"columns\":{\"5\":{\"field\":\"group\",\"class\":\"device\"},\"0\":{\"field\":\"name\",\"class\":\"device\"},\"1\":{\"field\":\"status\",\"class\":\"device\"},\"2\":{\"field\":\"time\",\"class\":\"device\"},\"3\":{\"field\":\"position\",\"class\":\"device\"}},\"groupby\":\"group\"}}');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_dst`
--

DROP TABLE IF EXISTS `users_dst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_dst` (
  `user_id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date_from` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_to` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `month_from` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `week_pos_from` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `week_day_from` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time_from` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `month_to` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `week_pos_to` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `week_day_to` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time_to` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`,`type`),
  KEY `users_dst_user_id_index` (`user_id`),
  KEY `users_dst_country_id_index` (`country_id`),
  KEY `users_dst_type_index` (`type`),
  CONSTRAINT `users_dst_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_dst`
--

LOCK TABLES `users_dst` WRITE;
/*!40000 ALTER TABLE `users_dst` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_dst` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-13 13:43:22
