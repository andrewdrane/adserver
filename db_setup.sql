-- Database setup for adserver
-- 2013 Andrew Drane
--
-- Setup database schema, and populate first tables

--
-- Table structure for table `ad_serve`
--

DROP TABLE IF EXISTS `ad_serve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_serve` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(10) unsigned NOT NULL,
  `ad_id` int(10) unsigned NOT NULL COMMENT 'This is duplicated from the campaign_id for fast lookup',
  `pending` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pending` (`pending`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The go to table for finding which ad to serve';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `advertisements`
--

DROP TABLE IF EXISTS `advertisements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `advertisements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `html` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'actual ad stuff.\n',
  `url` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'URL for redirect',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaigns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ad_id` int(10) unsigned NOT NULL,
  `impressions` int(10) unsigned NOT NULL COMMENT 'total number of impressions purchased\n',
  `allocated` int(10) unsigned NOT NULL COMMENT 'Impressions that have been allocated for display\n',
  `start` datetime NOT NULL COMMENT 'when the campaign was started',
  `duration` int(10) unsigned NOT NULL COMMENT 'how many days to run the campaign',
  `active` int(1) NOT NULL DEFAULT '1' COMMENT 'Is the campaign currently running',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `start` (`start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clicks`
--

DROP TABLE IF EXISTS `clicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clicks` (
  `ad_id` int(10) unsigned DEFAULT NULL,
  `source_url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Where was the ad viewed',
  `ip_address` varchar(39) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'set to max length of IPV6',
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='tracking clicks on ads';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `impressions`
--

DROP TABLE IF EXISTS `impressions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impressions` (
  `ad_id` int(10) unsigned DEFAULT NULL,
  `source_url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Where was the ad viewed',
  `ip_address` varchar(39) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'set to max length of IPV6',
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='tracking impressions on the adserver';


-- Insert necessary data

-- Advertisements
INSERT INTO `advertisements` VALUES (1,1,'<img src=\"http://adserver.local/creative/728_90_best_electronics.gif\" />','http://bestbuy.com'),(2,2,'<img src=\"http://adserver.local/creative/728_90_prescriptions_r_us_special_redbull_offer.gif\" />','http://cvs.com');

-- Campaigns
INSERT INTO `campaigns` VALUES (1,1,1000000,0,NOW(),30,1);
INSERT INTO `campaigns` VALUES (2,2,1000000,0,NOW(),30,1);