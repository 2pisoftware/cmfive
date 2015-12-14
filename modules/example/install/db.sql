--
-- Table structure for table `example_data`
--

CREATE TABLE IF NOT EXISTS `example_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `data` varchar(1024) NOT NULL,
  `example_checkbox` tinyint(1) NOT NULL DEFAULT '0',
  `select_field` varchar(255) NOT NULL,
  `autocomplete_field` varchar(255) NOT NULL,
  `multiselect_field` varchar(255) NOT NULL,
  `radio_field` varchar(255) NOT NULL,
  `password_field` varchar(255) NOT NULL,
  `email_field` varchar(255) NOT NULL,
  `hidden_field` varchar(255) NOT NULL,
  `d_date_field`  date NOT NULL,
  `dt_datetime_field`  datetime NOT NULL,
  `t_time_field` time NOT NULL,
  `rte_field` varchar(255) NOT NULL,
  `file_field` varchar(255) NOT NULL,
  `multifile_field` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `dt_modified` datetime NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
