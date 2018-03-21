CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%media_manager_plus_breakpoints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `mediaquery` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `%TABLE_PREFIX%media_manager_plus_breakpoints` (`id`, `name`, `mediaquery`) VALUES (1, 'XS', '(max-width: 375px)');
INSERT IGNORE INTO `%TABLE_PREFIX%media_manager_plus_breakpoints` (`id`, `name`, `mediaquery`) VALUES (2, 'S', '(min-width: 376px) and (max-width: 750px)');
INSERT IGNORE INTO `%TABLE_PREFIX%media_manager_plus_breakpoints` (`id`, `name`, `mediaquery`) VALUES (3, 'M', '(min-width: 751px) and (max-width: 1024px)');
INSERT IGNORE INTO `%TABLE_PREFIX%media_manager_plus_breakpoints` (`id`, `name`, `mediaquery`) VALUES (4, 'L', '(min-width: 1025px) and (max-width: 1300px)');
INSERT IGNORE INTO `%TABLE_PREFIX%media_manager_plus_breakpoints` (`id`, `name`, `mediaquery`) VALUES (5, 'XL', '(min-width: 1301px)');
