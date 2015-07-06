-- Update structure and rename
ALTER TABLE task_time
    ADD COLUMN `object_table` VARCHAR(255) NOT NULL AFTER `id`,
    CHANGE COLUMN `task_id` `object_id` BIGINT(20) NULL,
    DROP COLUMN `comment_id`, 
    ADD COLUMN `dt_modified` DATETIME NOT NULL AFTER `dt_created`, 
    ADD COLUMN `modifier_id` BIGINT(20) NOT NULL AFTER `creator_id`, 
    RENAME TO  `2picrm`.`timelog`;

-- Change layout
ALTER TABLE timelog
    CHANGE COLUMN `user_id` `user_id` BIGINT(20) NOT NULL AFTER `id`, 
    CHANGE COLUMN `time_type` `time_type` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL AFTER `dt_end`, 
    CHANGE COLUMN `dt_created` `dt_created` DATETIME NOT NULL AFTER `is_suspect`, 
    CHANGE COLUMN `dt_modified` `dt_modified` DATETIME NOT NULL AFTER `dt_created`, 
    CHANGE COLUMN `creator_id` `creator_id` BIGINT(20) NOT NULL AFTER `dt_modified`, 
    CHANGE COLUMN `modifier_id` `modifier_id` BIGINT(20) NOT NULL AFTER `creator_id`, 
    CHANGE COLUMN `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `dt_end` `dt_end` DATETIME NULL;

-- Migrate data (All time logs were previously attached to Tasks)
UPDATE timelog SET object_class = "Task" WHERE 1;