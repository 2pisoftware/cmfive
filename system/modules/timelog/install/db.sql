
-- Table structure for table `timelog`
--

DROP TABLE IF EXISTS `timelog`;
CREATE TABLE `timelog` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `dt_start` datetime NOT NULL,
  `dt_end` datetime NULL,
  `time_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_suspect` tinyint(4) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

