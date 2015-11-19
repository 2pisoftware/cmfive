ALTER TABLE `fcstimesheetsdev`.`report` 

CHANGE COLUMN `category` `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL ,

CHANGE COLUMN `report_code` `report_code` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL ,
CHANGE COLUMN `is_approved` `is_approved` TINYINT(1) NOT NULL DEFAULT '1' ;
