--
-- Table structure for table `example_data`
--

CREATE TABLE IF NOT EXISTS `example_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `data` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator_id` bigint(20) NOT NULL,
  `dt_modified` datetime NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;