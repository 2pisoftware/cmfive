CREATE TABLE IF NOT EXISTS `form` (
`id` bigint(20) AUTO_INCREMENT NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
   PRIMARY KEY('id')
) ENGINE=InnoDB DEFAULT CHARSET=utf-8;
