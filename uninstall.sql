DROP TABLE IF EXISTS `%TABLE_PREFIX%media_manager_plus_breakpoints`;

ALTER TABLE `%TABLE_PREFIX%media_manager_type` DROP COLUMN `group`;
ALTER TABLE `%TABLE_PREFIX%media_manager_type` DROP COLUMN `subgroup`;