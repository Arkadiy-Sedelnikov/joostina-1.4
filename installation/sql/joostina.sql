# --------------------------------------------------------
# Автор:                        Gold Dragon
# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.62-community-log - MySQL Community Server (GPL)
# Server OS:                    Win32
# HeidiSQL version:             7.0.0.4053
# Date/time:                    2012-05-28 21:55:56
# Автор:
# --------------------------------------------------------

# Dumping structure for table #__banners

CREATE TABLE IF NOT EXISTS `#__banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT 'banner',
  `name` varchar(50) NOT NULL DEFAULT '',
  `imp_total` int(11) NOT NULL DEFAULT '0',
  `imp_made` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `image_url` varchar(100) DEFAULT '',
  `click_url` varchar(200) DEFAULT '',
  `custom_banner_code` text,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `last_show` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `msec` int(11) NOT NULL DEFAULT '0',
  `publish_up_date` date NOT NULL DEFAULT '0000-00-00',
  `publish_up_time` time NOT NULL DEFAULT '00:00:00',
  `publish_down_date` date NOT NULL DEFAULT '0000-00-00',
  `publish_down_time` time NOT NULL DEFAULT '00:00:00',
  `reccurtype` tinyint(1) NOT NULL DEFAULT '0',
  `reccurweekdays` varchar(100) NOT NULL DEFAULT '',
  `access` int(11) NOT NULL DEFAULT '0',
  `target` varchar(15) NOT NULL DEFAULT '',
  `border_value` int(11) NOT NULL DEFAULT '0',
  `border_style` varchar(11) NOT NULL DEFAULT '',
  `border_color` varchar(11) NOT NULL DEFAULT '',
  `click_value` varchar(10) NOT NULL DEFAULT '',
  `complete_clicks` int(11) NOT NULL DEFAULT '0',
  `imp_value` varchar(10) NOT NULL DEFAULT '',
  `dta_mod_clicks` date DEFAULT NULL,
  `password` varchar(40) NOT NULL DEFAULT '',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `alt` varchar(200) DEFAULT '',
  `title` varchar(200) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ibx_select` (`state`,`last_show`,`msec`,`publish_up_date`,`publish_up_time`,`publish_down_date`,`publish_down_time`,`reccurtype`,`reccurweekdays`(2),`access`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

# Dumping structure for table #__banners_categories

CREATE TABLE IF NOT EXISTS `#__banners_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

# Dumping structure for table #__banners_clients

CREATE TABLE IF NOT EXISTS `#__banners_clients` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `contact` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `extrainfo` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`cid`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_5_categories

CREATE TABLE IF NOT EXISTS `#__boss_5_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(60) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `meta_keys` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `ordering` int(11) DEFAULT '0',
  `published` tinyint(1) DEFAULT '0',
  `content_types` int(11) DEFAULT '0',
  `template` varchar(50) NOT NULL,
  `rights` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_5_contents

CREATE TABLE IF NOT EXISTS `#__boss_5_contents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(60) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `meta_keys` varchar(200) NOT NULL,
  `userid` int(11) unsigned DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  `frontpage` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_last_сomment` datetime DEFAULT NULL,
  `date_publish` datetime NOT NULL,
  `date_unpublish` datetime NOT NULL,
  `views` int(11) unsigned DEFAULT '0',
  `type_content` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `content_editor` text NOT NULL,
  `content_editorfull` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_5_content_category_href

CREATE TABLE IF NOT EXISTS `#__boss_5_content_category_href` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='Привязка контента к категориям';

# Dumping structure for table #__boss_5_content_types

CREATE TABLE IF NOT EXISTS `#__boss_5_content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `fields` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_5_content_types: 1 rows

INSERT INTO `#__boss_5_content_types` (`id`, `name`, `desc`, `fields`, `published`, `ordering`) VALUES
	(1, 'Статьи', 'Обычные статьи без изысков, аналог ком-контент', 0, 1, 1);

# Dumping structure for table #__boss_5_fields

CREATE TABLE IF NOT EXISTS `#__boss_5_fields` (
  `fieldid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `display_title` tinyint(1) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `text_before` text NOT NULL,
  `text_after` text NOT NULL,
  `tags_open` varchar(150) NOT NULL,
  `tags_separator` varchar(100) NOT NULL,
  `tags_close` varchar(50) NOT NULL,
  `maxlength` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `required` tinyint(4) DEFAULT '0',
  `link_text` varchar(255) NOT NULL DEFAULT ',-1,',
  `link_image` varchar(255) NOT NULL DEFAULT ',-1,',
  `ordering` int(10) unsigned DEFAULT '0',
  `cols` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `profile` tinyint(1) NOT NULL DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `searchable` tinyint(1) NOT NULL DEFAULT '1',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  `sort_direction` varchar(4) NOT NULL DEFAULT 'DESC',
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `filter` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_5_fields: 2 rows

INSERT INTO `#__boss_5_fields` (`fieldid`, `name`, `title`, `display_title`, `description`, `type`, `text_before`, `text_after`, `tags_open`, `tags_separator`, `tags_close`, `maxlength`, `size`, `required`, `link_text`, `link_image`, `ordering`, `cols`, `rows`, `profile`, `editable`, `searchable`, `sort`, `sort_direction`, `catsid`, `published`, `filter`) VALUES
	(20, 'content_editor', 'Краткое описание', 0, 'Здесь пишем то, что будет отображаться в списке контента (поиск, категории и т.п.)', 'BossTextAreaEditorPlugin', '', '', '', '', '', 2000, 0, 1, '', '', 2, 200, 20, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
	(21, 'content_editorfull', 'Полное описание', 0, 'Здесь пишем основной текст', 'BossTextAreaEditorPlugin', '', '', '', '', '', 2000, 0, 1, '', '', 3, 50, 5, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0);

# Dumping structure for table #__boss_5_field_values

CREATE TABLE IF NOT EXISTS `#__boss_5_field_values` (
  `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(50) NOT NULL DEFAULT '',
  `fieldvalue` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldvalueid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Dumping structure for table #__boss_5_groupfields

CREATE TABLE IF NOT EXISTS `#__boss_5_groupfields` (
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`,`groupid`),
  KEY `template` (`template`),
  KEY `type_tmpl` (`type_tmpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_5_groupfields: 10 rows

INSERT INTO `#__boss_5_groupfields` (`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) VALUES
	(20, 28, 'default', 'category', 0),
	(21, 29, 'default', 'category', 0),
	(20, 30, 'default', 'content', 0),
	(21, 31, 'default', 'content', 0);

# Dumping structure for table #__boss_5_groups

CREATE TABLE IF NOT EXISTS `#__boss_5_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `desc` varchar(20) DEFAULT NULL,
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_5_groups: 20 rows

INSERT INTO `#__boss_5_groups` (`id`, `name`, `desc`, `template`, `type_tmpl`, `catsid`, `published`) VALUES
	(31, 'ConFull', 'ConFull', 'default', 'content', ',-1,', 1),
	(30, 'ConShort', 'ConShort', 'default', 'content', ',-1,', 1),
	(29, 'CatFull', 'CatFull', 'default', 'category', ',-1,', 1),
	(28, 'CatShort', 'CatShort', 'default', 'category', ',-1,', 1);


# Dumping structure for table #__boss_5_profile

CREATE TABLE IF NOT EXISTS `#__boss_5_profile` (
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_5_rating

CREATE TABLE IF NOT EXISTS `#__boss_5_rating` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contentid` int(10) DEFAULT '0',
  `userid` int(10) DEFAULT '0',
  `value` tinyint(1) DEFAULT '5',
  `ip` int(11) DEFAULT '0',
  `date` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_5_reviews

CREATE TABLE IF NOT EXISTS `#__boss_5_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contentid` int(10) unsigned DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_6_categories

CREATE TABLE IF NOT EXISTS `#__boss_6_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(60) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `meta_keys` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `ordering` int(11) DEFAULT '0',
  `published` tinyint(1) DEFAULT '0',
  `content_types` int(11) DEFAULT '0',
  `template` varchar(50) NOT NULL,
  `rights` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_6_contents

CREATE TABLE IF NOT EXISTS `#__boss_6_contents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(60) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `meta_keys` varchar(200) NOT NULL,
  `userid` int(11) unsigned DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  `frontpage` tinyint(1) DEFAULT '0',
  `featured` tinyint(1) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_last_сomment` datetime DEFAULT NULL,
  `date_publish` datetime NOT NULL,
  `date_unpublish` datetime NOT NULL,
  `views` int(11) unsigned DEFAULT '0',
  `type_content` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `content_version` text NOT NULL,
  `content_os` text NOT NULL,
  `content_file` text NOT NULL,
  `content_alldes` text NOT NULL,
  `content_datecreate` text NOT NULL,
  `content_lang` text NOT NULL,
  `content_price` text NOT NULL,
  `content_lic` text NOT NULL,
  `content_foto` text NOT NULL,
  `content_autor` text NOT NULL,
  `content_email` text NOT NULL,
  `content_url` text NOT NULL,
  `content_smalldes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_6_content_category_href

CREATE TABLE IF NOT EXISTS `#__boss_6_content_category_href` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='Привязка контента к категориям';

# Dumping structure for table #__boss_6_content_types

CREATE TABLE IF NOT EXISTS `#__boss_6_content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `fields` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_6_content_types: 1 rows

INSERT INTO `#__boss_6_content_types` (`id`, `name`, `desc`, `fields`, `published`, `ordering`) VALUES
	(1, 'Архив файла', '', 0, 1, 1);

# Dumping structure for table #__boss_6_fields

CREATE TABLE IF NOT EXISTS `#__boss_6_fields` (
  `fieldid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `display_title` tinyint(1) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `text_before` text NOT NULL,
  `text_after` text NOT NULL,
  `tags_open` varchar(150) NOT NULL,
  `tags_separator` varchar(100) NOT NULL,
  `tags_close` varchar(50) NOT NULL,
  `maxlength` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `required` tinyint(4) DEFAULT '0',
  `link_text` varchar(255) NOT NULL DEFAULT ',-1,',
  `link_image` varchar(255) NOT NULL DEFAULT ',-1,',
  `ordering` int(10) unsigned DEFAULT '0',
  `cols` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `profile` tinyint(1) NOT NULL DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `searchable` tinyint(1) NOT NULL DEFAULT '1',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  `sort_direction` varchar(4) NOT NULL DEFAULT 'DESC',
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `filter` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_6_fields: 13 rows

INSERT INTO `#__boss_6_fields` (`fieldid`, `name`, `title`, `display_title`, `description`, `type`, `text_before`, `text_after`, `tags_open`, `tags_separator`, `tags_close`, `maxlength`, `size`, `required`, `link_text`, `link_image`, `ordering`, `cols`, `rows`, `profile`, `editable`, `searchable`, `sort`, `sort_direction`, `catsid`, `published`, `filter`) VALUES
	(1, 'content_version', 'Версия', 3, '', 'BossTextFieldPlugin', '', '', '', '', '', 20, 0, 0, '', '', 0, 0, 0, 0, 1, 0, 0, 'DESC', ',1,', 1, 0),
	(2, 'content_os', 'Платформа', 3, '', 'BossSelectMultiPlugin', '', '', '', '', '', 75, 0, 0, ',-1,', ',-1,', 2, 0, 0, 0, 1, 1, 1, 'DESC', ',1,', 1, 1),
	(3, 'content_file', 'Файл', 0, '', 'BossFileMultiPlugin', '', '', '', '', '', 75, 10000000, 1, '', '', 6, 0, 0, 0, 1, 0, 0, 'DESC', ',1,', 1, 0),
	(14, 'content_smalldes', 'Краткое описание', 0, '', 'BossTextAreaPlugin', '', '', '', '', '', 500, 0, 1, ',-1,', ',-1,', 8, 60, 3, 0, 1, 1, 0, 'DESC', ',1,', 1, 0),
	(5, 'content_alldes', 'Описание', 3, '', 'BossTextAreaEditorPlugin', '', '', '', '', '', 2000, 0, 0, ',-1,', ',-1,', 9, 50, 7, 0, 1, 1, 1, 'DESC', ',1,', 1, 0),
	(6, 'content_datecreate', 'Дата создания', 3, '', 'BossDatePlugin', '', '', '', '', '', 75, 0, 1, ',-1,', ',-1,', 1, 0, 0, 0, 1, 0, 1, 'DESC', ',1,', 1, 0),
	(7, 'content_lang', 'Язык', 3, '', 'BossSelectMultiPlugin', '', '', '', '', '', 75, 0, 0, ',-1,', ',-1,', 3, 0, 0, 0, 1, 0, 1, 'DESC', ',1,', 1, 1),
	(8, 'content_price', 'Цена', 3, '', 'BossPricePlugin', '', '', '', '', '', 7, 0, 0, ',-1,', ',-1,', 7, 0, 0, 0, 1, 0, 1, 'DESC', ',1,', 1, 0),
	(9, 'content_lic', 'Лицензия', 3, '', 'BossSelectPlugin', '', '', '', '', '', 75, 0, 1, '', '', 4, 0, 0, 0, 1, 0, 0, 'DESC', ',1,', 1, 1),
	(10, 'content_foto', 'Изображение', 0, '', 'BossImagePlugin', '', '', '', '', '', 75, 0, 0, '', '', 10, 0, 0, 0, 1, 0, 0, 'DESC', ',1,', 1, 0),
	(11, 'content_autor', 'Автор', 3, '', 'BossTextFieldPlugin', '', '', '', '', '', 50, 0, 0, ',-1,', ',-1,', 11, 0, 0, 0, 1, 1, 1, 'DESC', ',1,', 1, 1),
	(12, 'content_email', 'EMail', 3, '', 'BossEmailPlugin', '', '', '', '', '', 75, 0, 0, '', '', 12, 0, 0, 0, 1, 0, 0, 'DESC', ',1,', 1, 0),
	(13, 'content_url', 'URL проекта', 3, '', 'BossURLPlugin', '', '', '', '', '', 200, 50, 0, '', 'null', 5, 0, 0, 0, 1, 1, 1, 'DESC', ',1,', 1, 0);

# Dumping structure for table #__boss_6_field_values

CREATE TABLE IF NOT EXISTS `#__boss_6_field_values` (
  `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(50) NOT NULL DEFAULT '',
  `fieldvalue` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldvalueid`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_6_field_values: 42 rows

INSERT INTO `#__boss_6_field_values` (`fieldvalueid`, `fieldid`, `fieldtitle`, `fieldvalue`, `ordering`, `sys`) VALUES
	(1, 2, 'Joostina 1.0.x', '10', 0, 0),
	(2, 2, 'Joostina 1.2.x', '12', 1, 0),
	(3, 2, 'Joostina 1.3.x', '13', 2, 0),
	(4, 2, 'Joostina 1.4.x', '14', 3, 0),
	(5, 2, 'Joomla 1.5.x', '15', 4, 0),
	(6, 2, 'Joomla 2.5.x', '25', 5, 0),
	(124, 3, 'show_date', '1', 7, 0),
	(10, 6, 'date_format', 'd.m.Y', 1, 0),
	(11, 7, 'Неизвестно', '0', 0, 0),
	(12, 7, 'Русский', '1', 1, 0),
	(13, 7, 'Английский', '2', 2, 0),
	(14, 7, 'Мультиязык', '3', 3, 0),
	(92, 9, 'FLOSS (бесплатное с открытым исходным кодом)', '8', 7, 0),
	(91, 9, 'Spyware', '7', 6, 0),
	(90, 9, 'Shareware', '6', 5, 0),
	(89, 9, 'Freeware', '5', 4, 0),
	(88, 9, 'Software', '4', 3, 0),
	(87, 9, 'Demoware', '3', 2, 0),
	(86, 9, 'Adware', '2', 1, 0),
	(100, 10, 'max_height', '800', 4, 0),
	(101, 10, 'max_width_t', '200', 5, 0),
	(102, 10, 'max_height_t', '200', 6, 0),
	(103, 10, 'tag', '', 7, 0),
	(104, 10, 'image_display', 'fancybox', 8, 0),
	(105, 10, 'cat_max_width', '0', 9, 0),
	(106, 10, 'cat_max_height', '0', 10, 0),
	(107, 10, 'cat_max_width_t', '0', 11, 0),
	(108, 10, 'cat_max_height_t', '0', 12, 0),
	(96, 12, 'email_display', '1', 1, 0),
	(99, 10, 'max_width', '800', 3, 0),
	(125, 3, 'show_desc', '1', 8, 0),
	(98, 10, 'max_image_size', '150000', 2, 0),
	(97, 10, 'nb_images', '1', 1, 0),
	(85, 9, 'Abandonware', '1', 0, 0),
	(93, 9, 'COSS (коммерческое с открытым исходным кодом)', '9', 8, 0),
	(123, 3, 'show_size', '1', 6, 0),
	(122, 3, 'show_button', '1', 5, 0),
	(121, 3, 'show_file', '1', 4, 0),
	(120, 3, 'counter', '1', 3, 0),
	(119, 3, 'enable_files', '7z,zip,rar,tgz', 2, 0),
	(118, 3, 'nb_files', '1', 1, 0),
	(126, 3, 'show_img', '1', 9, 0);

# Dumping structure for table #__boss_6_groupfields

CREATE TABLE IF NOT EXISTS `#__boss_6_groupfields` (
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`,`groupid`),
  KEY `template` (`template`),
  KEY `type_tmpl` (`type_tmpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_6_groupfields: 17 rows

INSERT INTO `#__boss_6_groupfields` (`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) VALUES
	(3, 3, 'files', 'category', 1),
	(14, 3, 'files', 'category', 2),
	(10, 1, 'files', 'category', 0),
	(6, 2, 'files', 'category', 2),
	(8, 5, 'files', 'content', 7),
	(3, 6, 'files', 'content', 1),
	(2, 5, 'files', 'content', 4),
	(5, 6, 'files', 'content', 2),
	(9, 5, 'files', 'content', 6),
	(10, 4, 'files', 'content', 0),
	(6, 5, 'files', 'content', 2),
	(1, 5, 'files', 'content', 1),
	(11, 5, 'files', 'content', 9),
	(1, 2, 'files', 'category', 1),
	(13, 5, 'files', 'content', 3),
	(12, 5, 'files', 'content', 10),
	(7, 5, 'files', 'content', 5);

# Dumping structure for table #__boss_6_groups

CREATE TABLE IF NOT EXISTS `#__boss_6_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `desc` varchar(20) DEFAULT NULL,
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_6_groups: 17 rows

INSERT INTO `#__boss_6_groups` (`id`, `name`, `desc`, `template`, `type_tmpl`, `catsid`, `published`) VALUES
	(1, 'CatImage', 'Изображение', 'files', 'category', ',-1,', 1),
	(2, 'CatInfo', 'Краткие данные', 'files', 'category', ',-1,', 1),
	(3, 'CatDescription', 'Краткое описание', 'files', 'category', ',-1,', 1),
	(4, 'ConImage', 'Изображение', 'files', 'content', ',-1,', 1),
	(5, 'ConInfo', 'Полные данные', 'files', 'content', ',-1,', 1),
	(6, 'ConDescription', 'Полное описание', 'files', 'content', ',-1,', 1),
	(7, 'ListSubtitle', 'ListSubtitle', 'default', 'category', ',-1,', 1),
	(8, 'ListDescription', 'ListDescription', 'default', 'category', ',-1,', 1),
	(9, 'ListBottom', 'ListBottom', 'default', 'category', ',-1,', 1),
	(10, 'ListImage', 'ListImage', 'default', 'category', ',-1,', 1),
	(11, 'DetailsSubtitle1', 'DetailsSubtitle1', 'default', 'content', ',-1,', 1),
	(12, 'DetailsSubtitle2', 'DetailsSubtitle2', 'default', 'content', ',-1,', 1),
	(13, 'DetailsSubtitle3', 'DetailsSubtitle3', 'default', 'content', ',-1,', 1),
	(14, 'DetailsDescription', 'DetailsDescription', 'default', 'content', ',-1,', 1),
	(15, 'DetailsFullText', 'DetailsFullText', 'default', 'content', ',-1,', 1),
	(16, 'DetailsBottom', 'DetailsBottom', 'default', 'content', ',-1,', 1),
	(17, 'DetailsImage', 'DetailsImage', 'default', 'content', ',-1,', 1);

# Dumping structure for table #__boss_6_profile

CREATE TABLE IF NOT EXISTS `#__boss_6_profile` (
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_6_rating

CREATE TABLE IF NOT EXISTS `#__boss_6_rating` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contentid` int(10) DEFAULT '0',
  `userid` int(10) DEFAULT '0',
  `value` tinyint(1) DEFAULT '5',
  `ip` int(11) DEFAULT '0',
  `date` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_6_reviews

CREATE TABLE IF NOT EXISTS `#__boss_6_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contentid` int(10) unsigned DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_7_categories

CREATE TABLE IF NOT EXISTS `#__boss_7_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(60) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `meta_keys` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `ordering` int(11) DEFAULT '0',
  `published` tinyint(1) DEFAULT '0',
  `content_types` int(11) DEFAULT '0',
  `template` varchar(50) NOT NULL,
  `rights` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_7_contents

CREATE TABLE IF NOT EXISTS `#__boss_7_contents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(60) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `meta_keys` varchar(200) NOT NULL,
  `userid` int(11) unsigned DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  `frontpage` tinyint(1) DEFAULT '0',
  `featured` tinyint(1) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_last_сomment` datetime DEFAULT NULL,
  `date_publish` datetime NOT NULL,
  `date_unpublish` datetime NOT NULL,
  `views` int(11) unsigned DEFAULT '0',
  `type_content` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `content_fam` text NOT NULL,
  `content_im` text NOT NULL,
  `content_ot` text NOT NULL,
  `content_dataa` text NOT NULL,
  `content_mail` text NOT NULL,
  `content_foto` text NOT NULL,
  `content_editorfull` text NOT NULL,
  `content_stel` text NOT NULL,
  `content_ltel` text NOT NULL,
  `content_skype` text NOT NULL,
  `content_icq` text NOT NULL,
  `content_jid` text NOT NULL,
  `content_url` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_7_content_category_href

CREATE TABLE IF NOT EXISTS `#__boss_7_content_category_href` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='Привязка контента к категориям';

# Dumping structure for table #__boss_7_content_types

CREATE TABLE IF NOT EXISTS `#__boss_7_content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `fields` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_7_content_types: 1 rows

INSERT INTO `#__boss_7_content_types` (`id`, `name`, `desc`, `fields`, `published`, `ordering`) VALUES
	(1, 'Основные контакты', '', 0, 1, 1);

# Dumping structure for table #__boss_7_fields

CREATE TABLE IF NOT EXISTS `#__boss_7_fields` (
  `fieldid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `display_title` tinyint(1) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `text_before` text NOT NULL,
  `text_after` text NOT NULL,
  `tags_open` varchar(150) NOT NULL,
  `tags_separator` varchar(100) NOT NULL,
  `tags_close` varchar(50) NOT NULL,
  `maxlength` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `required` tinyint(4) DEFAULT '0',
  `link_text` varchar(255) NOT NULL DEFAULT ',-1,',
  `link_image` varchar(255) NOT NULL DEFAULT ',-1,',
  `ordering` int(10) unsigned DEFAULT '0',
  `cols` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `profile` tinyint(1) NOT NULL DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `searchable` tinyint(1) NOT NULL DEFAULT '1',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  `sort_direction` varchar(4) NOT NULL DEFAULT 'DESC',
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `filter` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_7_fields: 13 rows

INSERT INTO `#__boss_7_fields` (`fieldid`, `name`, `title`, `display_title`, `description`, `type`, `text_before`, `text_after`, `tags_open`, `tags_separator`, `tags_close`, `maxlength`, `size`, `required`, `link_text`, `link_image`, `ordering`, `cols`, `rows`, `profile`, `editable`, `searchable`, `sort`, `sort_direction`, `catsid`, `published`, `filter`) VALUES
	(1, 'content_fam', 'Фамилия', 3, '', 'BossTextFieldPlugin', '', '', '', '', '', 30, 30, 0, '', '', 0, 0, 0, 0, 1, 1, 1, 'ASC', ',-1,', 1, 1),
	(9, 'content_stel', 'Рабочий телефон', 3, '', 'BossNumberTextPlugin', '', '', '', '', '', 11, 0, 0, ',-1,', ',-1,', 6, 0, 0, 0, 1, 0, 0, 'DESC', ',-1,', 1, 0),
	(3, 'content_im', 'Имя', 3, '', 'BossTextFieldPlugin', '', '', '', '', '', 30, 30, 0, '', '', 1, 0, 0, 0, 1, 1, 0, 'DESC', ',-1,', 1, 1),
	(4, 'content_ot', 'Отчество', 3, '', 'BossTextFieldPlugin', '', '', '', '', '', 30, 30, 0, '', '', 2, 0, 0, 0, 1, 1, 0, 'DESC', ',-1,', 1, 1),
	(5, 'content_dataa', 'Дата рождения', 3, '', 'BossDatePlugin', '', '', '', '', '', 75, 0, 0, '', '', 3, 0, 0, 0, 1, 0, 0, 'DESC', ',-1,', 1, 0),
	(6, 'content_mail', 'Электронный адрес', 1, '', 'BossEmailPlugin', '', '', '', '', '', 75, 0, 0, '', '', 11, 0, 0, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
	(7, 'content_foto', 'Фотография', 0, '', 'BossImagePlugin', '', '', '', '', '', 75, 0, 0, '', '', 4, 0, 0, 0, 1, 0, 0, 'DESC', ',-1,', 1, 0),
	(8, 'content_editorfull', 'Описание', 0, '', 'BossTextAreaEditorPlugin', '', '', '', '', '', 20, 0, 0, '', '', 5, 60, 10, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
	(10, 'content_ltel', 'Личный телефон', 3, '', 'BossNumberTextPlugin', '', '', '', '', '', 11, 0, 0, '', '', 7, 0, 0, 0, 1, 0, 0, 'DESC', ',-1,', 1, 0),
	(11, 'content_skype', 'Skype', 3, '', 'BossTextFieldPlugin', '', '', '', '', '', 30, 0, 0, '', '', 9, 0, 0, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
	(12, 'content_icq', 'ICQ', 3, '', 'BossNumberTextPlugin', '', '', '', '', '', 10, 0, 0, ',-1,', ',-1,', 8, 0, 0, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
	(13, 'content_jid', 'JID', 3, '', 'BossTextFieldPlugin', '', '', '', '', '', 100, 0, 0, ',-1,', ',-1,', 10, 0, 0, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
	(15, 'content_url', 'Сайт', 3, '', 'BossURLPlugin', '', '', '', '', '', 150, 0, 0, '', 'null', 12, 0, 0, 0, 1, 0, 0, 'DESC', ',-1,', 1, 0);

# Dumping structure for table #__boss_7_field_values

CREATE TABLE IF NOT EXISTS `#__boss_7_field_values` (
  `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(50) NOT NULL DEFAULT '',
  `fieldvalue` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldvalueid`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_7_field_values: 13 rows

INSERT INTO `#__boss_7_field_values` (`fieldvalueid`, `fieldid`, `fieldtitle`, `fieldvalue`, `ordering`, `sys`) VALUES
	(45, 6, 'email_display', '2', 1, 0),
	(37, 7, 'cat_max_height', '0', 10, 0),
	(36, 7, 'cat_max_width', '0', 9, 0),
	(35, 7, 'image_display', 'fancybox', 8, 0),
	(34, 7, 'tag', '', 7, 0),
	(33, 7, 'max_height_t', '200', 6, 0),
	(32, 7, 'max_width_t', '200', 5, 0),
	(31, 7, 'max_height', '600', 4, 0),
	(30, 7, 'max_width', '600', 3, 0),
	(29, 7, 'max_image_size', '1500000', 2, 0),
	(28, 7, 'nb_images', '1', 1, 0),
	(38, 7, 'cat_max_width_t', '0', 11, 0),
	(39, 7, 'cat_max_height_t', '0', 12, 0);

# Dumping structure for table #__boss_7_groupfields

CREATE TABLE IF NOT EXISTS `#__boss_7_groupfields` (
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`,`groupid`),
  KEY `template` (`template`),
  KEY `type_tmpl` (`type_tmpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_7_groupfields: 19 rows

INSERT INTO `#__boss_7_groupfields` (`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) VALUES
	(7, 5, 'contact', 'content', 0),
	(1, 3, 'contact', 'content', 1),
	(7, 2, 'contact', 'category', 0),
	(15, 3, 'contact', 'content', 11),
	(9, 3, 'contact', 'content', 5),
	(4, 3, 'contact', 'content', 3),
	(9, 1, 'contact', 'category', 1),
	(8, 4, 'contact', 'content', 0),
	(10, 3, 'contact', 'content', 6),
	(11, 1, 'contact', 'category', 4),
	(13, 1, 'contact', 'category', 2),
	(12, 1, 'contact', 'category', 1),
	(3, 3, 'contact', 'content', 2),
	(5, 3, 'contact', 'content', 4),
	(11, 3, 'contact', 'content', 9),
	(13, 3, 'contact', 'content', 8),
	(6, 1, 'contact', 'category', 5),
	(12, 3, 'contact', 'content', 7),
	(6, 3, 'contact', 'content', 10);

# Dumping structure for table #__boss_7_groups

CREATE TABLE IF NOT EXISTS `#__boss_7_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `desc` varchar(20) DEFAULT NULL,
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_7_groups: 5 rows

INSERT INTO `#__boss_7_groups` (`id`, `name`, `desc`, `template`, `type_tmpl`, `catsid`, `published`) VALUES
	(1, 'catSubtitle', 'catSubtitle', 'contact', 'category', ',-1,', 1),
	(2, 'catImage', 'catImage', 'contact', 'category', ',-1,', 1),
	(3, 'conSubtitle', 'conSubtitle', 'contact', 'content', ',-1,', 1),
	(4, 'conDescription', 'conDescription', 'contact', 'content', ',-1,', 1),
	(5, 'conImage', 'conImage', 'contact', 'content', ',-1,', 1);

# Dumping structure for table #__boss_7_profile

CREATE TABLE IF NOT EXISTS `#__boss_7_profile` (
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_7_rating

CREATE TABLE IF NOT EXISTS `#__boss_7_rating` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contentid` int(10) DEFAULT '0',
  `userid` int(10) DEFAULT '0',
  `value` tinyint(1) DEFAULT '5',
  `ip` int(11) DEFAULT '0',
  `date` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_7_reviews

CREATE TABLE IF NOT EXISTS `#__boss_7_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contentid` int(10) unsigned DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__boss_config

CREATE TABLE IF NOT EXISTS `#__boss_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `slug` varchar(200) NOT NULL,
  `meta_title` varchar(60) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `meta_keys` varchar(200) NOT NULL,
  `default_order_by` varchar(20) NOT NULL,
  `contents_per_page` int(10) unsigned NOT NULL DEFAULT '20',
  `root_allowed` tinyint(4) NOT NULL DEFAULT '1',
  `show_contact` tinyint(4) NOT NULL DEFAULT '1',
  `send_email_on_new` tinyint(4) NOT NULL DEFAULT '1',
  `send_email_on_update` tinyint(4) NOT NULL DEFAULT '1',
  `auto_publish` tinyint(4) NOT NULL DEFAULT '1',
  `fronttext` text NOT NULL,
  `email_display` tinyint(4) NOT NULL DEFAULT '0',
  `display_fullname` tinyint(4) NOT NULL DEFAULT '2',
  `rules_text` text NOT NULL,
  `expiration` tinyint(1) NOT NULL DEFAULT '0',
  `content_duration` int(4) NOT NULL DEFAULT '30',
  `recall` tinyint(1) NOT NULL DEFAULT '1',
  `recall_time` int(4) NOT NULL DEFAULT '7',
  `recall_text` text NOT NULL,
  `empty_cat` tinyint(1) NOT NULL DEFAULT '1',
  `cat_max_width` int(4) NOT NULL DEFAULT '150',
  `cat_max_height` int(4) NOT NULL DEFAULT '150',
  `cat_max_width_t` int(4) NOT NULL DEFAULT '30',
  `cat_max_height_t` int(4) NOT NULL DEFAULT '30',
  `submission_type` int(4) NOT NULL DEFAULT '30',
  `nb_contents_by_user` int(4) NOT NULL DEFAULT '-1',
  `allow_attachement` tinyint(1) NOT NULL DEFAULT '0',
  `allow_contact_by_pms` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '0',
  `rating` varchar(50) NOT NULL,
  `secure_comment` tinyint(1) NOT NULL DEFAULT '0',
  `comment_sys` tinyint(1) NOT NULL,
  `allow_unregisered_comment` tinyint(1) NOT NULL,
  `allow_ratings` tinyint(1) NOT NULL,
  `secure_new_content` tinyint(1) NOT NULL DEFAULT '0',
  `use_content_mambot` tinyint(1) NOT NULL DEFAULT '0',
  `show_rss` tinyint(1) NOT NULL DEFAULT '0',
  `filter` varchar(50) NOT NULL DEFAULT 'no',
  `template` varchar(255) NOT NULL DEFAULT 'default',
  `allow_rights` varchar(1) NOT NULL DEFAULT '0',
  `rights` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

# Dumping data for table #__boss_config: 3 rows

INSERT INTO `#__boss_config` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `default_order_by`, `contents_per_page`, `root_allowed`, `show_contact`, `send_email_on_new`, `send_email_on_update`, `auto_publish`, `fronttext`, `email_display`, `display_fullname`, `rules_text`, `expiration`, `content_duration`, `recall`, `recall_time`, `recall_text`, `empty_cat`, `cat_max_width`, `cat_max_height`, `cat_max_width_t`, `cat_max_height_t`, `submission_type`, `nb_contents_by_user`, `allow_attachement`, `allow_contact_by_pms`, `allow_comments`, `rating`, `secure_comment`, `comment_sys`, `allow_unregisered_comment`, `allow_ratings`, `secure_new_content`, `use_content_mambot`, `show_rss`, `filter`, `template`, `allow_rights`, `rights`) VALUES
	(6, 'Файловый архив', 'files', '', '', '', '0', 5, 0, 2, 0, 0, 1, '<br />', 0, 0, 'Это правила... // /', 0, 30, 1, 7, '<br /> ', 0, 250, 250, 80, 80, 0, -1, 0, 0, 1, 'GDRating', 1, 1, 0, 1, 0, 1, 1, '0', 'files', '1', 'edit_category=23,24,25*edit_content=23,24,25*edit_directories=23,24,25*edit_conf=23,24,25*edit_types=23,24,25*edit_fields=23,24,25*edit_fieldimages=23,24,25*edit_templates=23,24,25*edit_plugins=23,24,25*import_export=23,24,25*edit_users=23,24,25*show_user_content=0,18,19,20,21,23,24,25*show_all=0,18,19,20,21,23,24,25*show_search=0,18,19,20,21,23,24,25*show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(5, 'Основной', 'content', '', '', '', '0', 5, 0, 2, 0, 0, 1, ' ', 0, 0, ' ', 0, 30, 1, 7, ' ', 1, 150, 150, 30, 30, 0, -1, 0, 0, 1, 'GDRating', 0, 1, 1, 1, 1, 1, 1, '0', 'default', '1', 'edit_category=23,24,25*edit_content=20,21,23,24,25*edit_directories=23,24,25*edit_conf=23,24,25*edit_types=23,24,25*edit_fields=23,24,25*edit_fieldimages=23,24,25*edit_templates=23,24,25*edit_plugins=23,24,25*import_export=23,24,25*edit_users=24,25*show_user_content=0,18,19,20,21,23,24,25*show_all=0,18,19,20,21,23,24,25*show_search=0,18,19,20,21,23,24,25*show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(7, 'Контакты', 'contact', '', '', '', '1', 5, 0, 1, 0, 0, 1, 'Текст приветствия ', 0, 0, 'Это правила... ', 0, 30, 1, 7, ' ', 1, 150, 150, 80, 80, 0, -1, 0, 0, 1, 'GDRating', 1, 1, 0, 1, 0, 1, 0, '0', 'contact', '1', 'edit_category=23,24,25*edit_content=23,24,25*edit_directories=23,24,25*edit_conf=23,24,25*edit_types=23,24,25*edit_fields=23,24,25*edit_fieldimages=23,24,25*edit_templates=23,24,25*edit_plugins=23,24,25*import_export=23,24,25*edit_users=23,24,25*show_user_content=0,18,19,20,21,23,24,25*show_all=0,18,19,20,21,23,24,25*show_search=0,18,19,20,21,23,24,25*show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=19,20,21,23,24,25*edit_all_content=23,24,25*delete_user_content=19,20,21,23,24,25*delete_all_content=23,24,25*');

# Dumping structure for table #__boss_plug_config

CREATE TABLE IF NOT EXISTS `#__boss_plug_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `directory` int(11) NOT NULL,
  `plug_type` varchar(11) NOT NULL,
  `plug_name` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `value` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `directory` (`directory`,`plug_type`,`plug_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__categories

CREATE TABLE IF NOT EXISTS `#__categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(100) NOT NULL DEFAULT '',
  `section` varchar(50) NOT NULL DEFAULT '',
  `image_position` varchar(10) NOT NULL DEFAULT '',
  `description` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor` varchar(50) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `params` text,
  `templates` text,
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__components

CREATE TABLE IF NOT EXISTS `#__components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `menuid` int(11) unsigned NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `admin_menu_link` varchar(255) NOT NULL DEFAULT '',
  `admin_menu_alt` varchar(255) NOT NULL DEFAULT '',
  `option` varchar(50) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `admin_menu_img` varchar(255) NOT NULL DEFAULT '',
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

# Dumping data for table #__components: 36 rows

INSERT INTO `#__components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`) VALUES
	(1, 'Баннеры', '', 0, 0, 'option=com_banners', 'Управление баннерами', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, ''),
	(2, 'Баннеры', '', 0, 1, 'option=com_banners&task=banners', 'Активные баннеры', 'com_banners', 1, 'js/ThemeOffice/edit.png', 0, ''),
	(3, 'Клиенты', '', 0, 1, 'option=com_banners&task=clients', 'Управление клиентами', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, ''),
	(36, 'Категории', '', 0, 1, 'option=com_banners&task=categories', 'Управление категориями', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, ''),
	(4, 'Каталог ссылок', 'option=com_weblinks', 0, 0, '', 'Управление ссылками', 'com_weblinks', 0, 'js/ThemeOffice/globe2.png', 0, ''),
	(5, 'Ссылки', '', 0, 4, 'option=com_weblinks', 'Просмотр существующих ссылок', 'com_weblinks', 1, 'js/ThemeOffice/edit.png', 0, ''),
	(6, 'Категории', '', 0, 4, 'option=com_categories&section=com_weblinks', 'Управление категориями ссылок', '', 2, 'js/ThemeOffice/categories.png', 0, ''),
	(7, 'Контакты', 'option=com_contact', 0, 0, '', 'Редактировать контактную информацию', 'com_contact', 0, 'js/ThemeOffice/user.png', 1, ''),
	(8, 'Контакты', '', 0, 7, 'option=com_contact', 'Редактировать контактную информацию', 'com_contact', 0, 'js/ThemeOffice/edit.png', 1, ''),
	(9, 'Категории', '', 0, 7, 'option=com_categories&section=com_contact_details', 'Управление категориями контактов', '', 2, 'js/ThemeOffice/categories.png', 1, ''),
	(10, 'Главная страница', 'option=com_frontpage', 0, 0, '', 'Управление объектами главной страницы', 'com_frontpage', 0, 'js/ThemeOffice/component.png', 1, ''),
	(11, 'Опросы', 'option=com_poll', 0, 0, 'option=com_poll', 'Управление опросами', 'com_poll', 0, 'js/ThemeOffice/component.png', 0, ''),
	(12, 'Ленты новостей', 'option=com_newsfeeds', 0, 0, '', 'Управление настройками лент новостей', 'com_newsfeeds', 0, 'js/ThemeOffice/rss_go.png', 0, ''),
	(13, 'Ленты новостей', '', 0, 12, 'option=com_newsfeeds', 'Управление лентами новостей', 'com_newsfeeds', 1, 'js/ThemeOffice/edit.png', 0, ''),
	(14, 'Категории', '', 0, 12, 'option=com_categories&section=com_newsfeeds', 'Управление категориями', '', 2, 'js/ThemeOffice/categories.png', 0, ''),
	(15, 'Авторизация', 'option=com_login', 0, 0, '', '', 'com_login', 0, '', 1, ''),
	(16, 'Поиск', 'option=com_search', 0, 0, '', '', 'com_search', 0, '', 1, ''),
	(17, 'RSS экспорт', '', 0, 0, 'option=com_syndicate&hidemainmenu=1', 'Управление настройками экспорта новостей', 'com_syndicate', 0, 'js/ThemeOffice/component.png', 0, 'check=0\ncache=1\ncache_time=3600\ncount=5\ntitle=Создано Joostina CMS!\ndescription=Экспорт с сайта Joostina!\nimage_file=aload.gif\nimage_alt=Создано Joostina CMS!\nlimit_text=1\ntext_length=20\nyandex=0\nrss091=0\nrss10=0\nrss20=1\natom03=0\nopml=0\norderby=rdate\nlive_bookmark=RSS2.0'),
	(18, 'Рассылка почты', '', 0, 0, 'option=com_massmail&hidemainmenu=1', 'Массовая рассылка почты', 'com_massmail', 0, 'js/ThemeOffice/mass_email.png', 0, ''),
	(19, 'Карта сайта', 'option=com_xmap', 0, 0, 'option=com_xmap', '', 'com_xmap', 0, 'js/ThemeOffice/map.png', 0, ''),
	(20, 'JoiBOSS CCK', 'option=com_boss', 0, 0, 'option=com_boss', 'JoiBOSS CCK', 'com_boss', 0, '../administrator/components/com_boss/images/16x16/component.png', 1, ''),
	(21, 'Категории', '', 0, 20, 'option=com_boss&act=categories', 'Категории', 'com_boss', 0, '../administrator/components/com_boss/images/16x16/categories.png', 0, NULL),
	(22, 'Контент', '', 0, 20, 'option=com_boss&act=contents', 'Контент', 'com_boss', 1, '../administrator/components/com_boss/images/16x16/contents.png', 0, NULL),
	(23, 'Управление', '', 0, 20, 'option=com_boss&act=manager', 'Управление', 'com_boss', 2, '../administrator/components/com_boss/images/16x16/manager.png', 0, NULL),
	(24, 'Конфигурация', '', 0, 20, 'option=com_boss&act=configuration', 'Конфигурация', 'com_boss', 3, '../administrator/components/com_boss/images/16x16/configuration.png', 0, NULL),
	(25, 'Поля', '', 0, 20, 'option=com_boss&act=fields', 'Поля', 'com_boss', 4, '../administrator/components/com_boss/images/16x16/fields.png', 0, NULL),
	(26, 'Шаблоны', '', 0, 20, 'option=com_boss&act=templates', 'Шаблоны', 'com_boss', 5, '../administrator/components/com_boss/images/16x16/templates.png', 0, NULL),
	(27, 'Расширения', '', 0, 20, 'option=com_boss&act=plugins', 'Расширения', 'com_boss', 6, '../administrator/components/com_boss/images/16x16/plugins.png', 0, NULL),
	(28, 'Изображения', '', 0, 20, 'option=com_boss&act=fieldimage', 'Изображения', 'com_boss', 7, '../administrator/components/com_boss/images/16x16/fieldimage.png', 0, NULL),
	(29, 'Импорт / экспорт', '', 0, 20, 'option=com_boss&act=export_import', 'Импорт / экспорт', 'com_boss', 8, '../administrator/components/com_boss/images/16x16/export_import.png', 0, NULL),
	(30, 'Пользователи', '', 0, 20, 'option=com_boss&act=users', 'Пользователи', 'com_boss', 9, '../administrator/components/com_boss/images/16x16/user.png', 0, NULL),
	(31, 'elFinder + elRTE', 'option=com_elrte', 0, 0, 'option=com_elrte', 'elFinder + elRTE', 'com_elrte', 0, 'js/ThemeOffice/component.png', 0, ''),
	(32, 'Медиа менеджер elFinder', '', 0, 31, 'option=com_elrte', 'Медиа менеджер elFinder', 'com_elrte', 0, 'js/ThemeOffice/component.png', 0, NULL),
	(33, 'Конфигурация elRTE', '', 0, 31, 'option=com_elrte&task=config_elrte', 'Конфигурация elRTE', 'com_elrte', 1, 'js/ThemeOffice/component.png', 0, NULL),
	(34, 'Конфигурация elFinder', '', 0, 31, 'option=com_elrte&task=config_elfinder', 'Конфигурация elFinder', 'com_elrte', 2, 'js/ThemeOffice/component.png', 0, NULL),
	(35, 'Инфо', '', 0, 31, 'option=com_elrte&task=info', 'Инфо', 'com_elrte', 3, 'js/ThemeOffice/component.png', 0, NULL);

# Dumping structure for table #__config

CREATE TABLE IF NOT EXISTS `#__config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL,
  `subgroup` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

# Dumping data for table #__config: 2 rows

INSERT INTO `#__config` (`id`, `group`, `subgroup`, `name`, `value`) VALUES
	(1, 'com_frontpage', 'default', 'directory', 's:1{5}'),
	(2, 'com_frontpage', 'default', 'page', 's:14{show_frontpage}');

# Dumping structure for table #__contact_details

CREATE TABLE IF NOT EXISTS `#__contact_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `con_position` varchar(250) DEFAULT NULL,
  `address` text,
  `suburb` varchar(250) DEFAULT NULL,
  `state` varchar(250) DEFAULT NULL,
  `country` varchar(250) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `telephone` varchar(250) DEFAULT NULL,
  `fax` varchar(250) DEFAULT NULL,
  `misc` mediumtext,
  `image` varchar(100) DEFAULT NULL,
  `imagepos` varchar(20) DEFAULT NULL,
  `email_to` varchar(100) DEFAULT NULL,
  `default_con` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping data for table #__contact_details: 1 rows

INSERT INTO `#__contact_details` (`id`, `name`, `con_position`, `address`, `suburb`, `state`, `country`, `postcode`, `telephone`, `fax`, `misc`, `image`, `imagepos`, `email_to`, `default_con`, `published`, `checked_out`, `checked_out_time`, `ordering`, `params`, `user_id`, `catid`, `access`) VALUES
	(1, 'Joostina Lotos', 'Положение', 'Улица', 'Район', 'Область(край)', 'Российская Федерация', 'Индекс', 'Телефон', 'Факс', 'www.joostina-cms.ru', '', 'top', 'info@joostina-cms.ru', 0, 1, 0, '0000-00-00 00:00:00', 1, 'menu_image=-1\npageclass_sfx=\nprint=\nback_button=\nname=1\nposition=0\nemail=1\nstreet_address=0\nsuburb=0\nstate=0\ncountry=1\npostcode=0\ntelephone=0\nfax=0\nmisc=1\nimage=0\nvcard=0\nemail_description=0\nemail_description_text=\nemail_form=1\nemail_copy=0\ndrop_down=0\ncontact_icons=1\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=', 0, 12, 0);

# Dumping structure for table #__content_rating

CREATE TABLE IF NOT EXISTS `#__content_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `rating_sum` int(11) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(11) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__content_tags

CREATE TABLE IF NOT EXISTS `#__content_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL,
  `obj_type` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `obj_id` (`obj_id`,`tag`),
  KEY `obj_type` (`obj_type`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

# Dumping structure for table #__core_acl_aro

CREATE TABLE IF NOT EXISTS `#__core_acl_aro` (
  `aro_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_value` varchar(240) NOT NULL DEFAULT '0',
  `value` int(11) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aro_id`),
  UNIQUE KEY `value` (`value`),
  UNIQUE KEY `#__gacl_section_value_value_aro` (`section_value`(100),`value`),
  KEY `#__gacl_hidden_aro` (`hidden`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

# Dumping structure for table #__core_acl_aro_groups

CREATE TABLE IF NOT EXISTS `#__core_acl_aro_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  KEY `#__gacl_parent_id_aro_groups` (`parent_id`),
  KEY `#__gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

# Dumping data for table #__core_acl_aro_groups: 11 rows

INSERT INTO `#__core_acl_aro_groups` (`group_id`, `parent_id`, `name`, `lft`, `rgt`) VALUES
	(17, 0, 'ROOT', 1, 22),
	(28, 17, 'USERS', 2, 21),
	(29, 28, 'Public Frontend', 3, 12),
	(18, 29, 'Registered', 4, 11),
	(19, 18, 'Author', 5, 10),
	(20, 19, 'Editor', 6, 9),
	(21, 20, 'Publisher', 7, 8),
	(30, 28, 'Public Backend', 13, 20),
	(23, 30, 'Manager', 14, 19),
	(24, 23, 'Administrator', 15, 18),
	(25, 24, 'Super Administrator', 16, 17);

# Dumping structure for table #__core_acl_aro_sections

CREATE TABLE IF NOT EXISTS `#__core_acl_aro_sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(230) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`section_id`),
  UNIQUE KEY `value_aro_sections` (`value`),
  KEY `hidden_aro_sections` (`hidden`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

# Dumping data for table #__core_acl_aro_sections: 1 rows

INSERT INTO `#__core_acl_aro_sections` (`section_id`, `value`, `order_value`, `name`, `hidden`) VALUES
	(10, 'users', 1, 'Users', 0);

# Dumping structure for table #__core_acl_groups_aro_map

CREATE TABLE IF NOT EXISTS `#__core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(240) NOT NULL DEFAULT '',
  `aro_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`),
  KEY `aro_id` (`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__core_acl_groups_aro_map: 2 rows

INSERT INTO `#__core_acl_groups_aro_map` (`group_id`, `section_value`, `aro_id`) VALUES
	(18, '', 11),
	(25, '', 10);

# Dumping structure for table #__core_log_items

CREATE TABLE IF NOT EXISTS `#__core_log_items` (
  `time_stamp` date NOT NULL DEFAULT '0000-00-00',
  `item_table` varchar(50) NOT NULL DEFAULT '',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__core_log_searches

CREATE TABLE IF NOT EXISTS `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL DEFAULT '',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `hits` (`hits`),
  KEY `search_term` (`search_term`(16))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__groups

CREATE TABLE IF NOT EXISTS `#__groups` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__groups: 3 rows

INSERT INTO `#__groups` (`id`, `name`) VALUES
	(0, 'Общий'),
	(1, 'Участники'),
	(2, 'Специальный');

# Dumping structure for table #__jp_def

CREATE TABLE IF NOT EXISTS `#__jp_def` (
  `def_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `directory` varchar(255) NOT NULL,
  PRIMARY KEY (`def_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__jp_packvars

CREATE TABLE IF NOT EXISTS `#__jp_packvars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `value2` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__mambots

CREATE TABLE IF NOT EXISTS `#__mambots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `element` varchar(100) NOT NULL DEFAULT '',
  `folder` varchar(100) NOT NULL DEFAULT '',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `iscore` tinyint(3) NOT NULL DEFAULT '0',
  `client_id` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text,
  PRIMARY KEY (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

# Dumping data for table #__mambots: 22 rows

INSERT INTO `#__mambots` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
	(1, 'Изображение MOS', 'mosimage', 'content', 0, -10000, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(4, 'SEF', 'mossef', 'content', 0, 3, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(5, 'Рейтинг статей', 'plugin_jw_ajaxvote', 'content', 0, 4, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(6, 'Поиск в контенте JoiBoss', 'boss.searchbot', 'search', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', 'directory=1\ncontent_field=content_editor\nsearch_limit=50\ngroup_results=1'),
	(7, 'Поиск веб-ссылок', 'weblinks.searchbot', 'search', 0, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(8, 'Поддержка кода', 'moscode', 'content', 0, 2, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(9, 'Простой редактор HTML', 'none', 'editors', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(10, 'Кнопка изображения MOS в редакторе', 'mosimage.btn', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(11, 'Кнопка разрыва страницы MOS в редакторе', 'mospage.btn', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(12, 'Поиск контактов', 'contacts.searchbot', 'search', 0, 3, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(13, 'Поиск категорий', 'categories.searchbot', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(15, 'Маскировка E-mail', 'mosemailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(16, 'Поиск лент новостей', 'newsfeeds.searchbot', 'search', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(17, 'Позиции загрузки модуля', 'mosloadposition', 'content', 0, 6, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(18, 'Первый обработчик содержимого', 'first', 'mainbody', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(19, 'Модуль на главной странице', 'frontpagemodule', 'content', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 'mod_position=banner\nmod_type=1\nmod_after=1'),
	(20, 'Контактные данные пользователя', 'user_contacts', 'profile', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(22, 'Информация ', 'user_info', 'profile', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', 'header=Информация\nshow_header=1\nshow_location=1\ngender=1'),
	(23, 'Библиотека MyLib', 'mylib', 'system', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL),
	(24, 'System - JQuery', 'jquery', 'system', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', NULL),
	(25, 'elRTE Mambot', 'elrte', 'editors', 0, 3, 1, 0, 0, 0, '2011-10-22 22:19:50', NULL),
	(26, 'Spaw', 'spaw', 'editors', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', 'default_width=98%\ndefault_height=400px\nresizing_directions=vertical\nbeautify_xhtml_output=1\ndefault_toolbarset=all\ntemplate=1\nstrip_absolute_urls=1\nrendering_mode=xhtml\nconvert_html_entities=0\nallow_modify=0\nallow_upload=1\nuser_dir=0\nmax_upload_filesize=200000\ndropdown_data_core_style=contact_email\r<br />sectiontableheader\r<br />sectiontableentry1\r<br />sectiontableentry2 \r<br />date\r<br />small\r<br />smalldark\r<br />contentheading\r<br />footer\r<br />lcol\r<br />rcol\r<br />contentdescription\r<br />blog_more\ntable_styles=moduletable\r<br />content\r<br />contenttoc\r<br />contentpane\r<br />prctable пример таблицы');

# Dumping structure for table #__menu

CREATE TABLE IF NOT EXISTS `#__menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menutype` varchar(25) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `link` text,
  `type` varchar(50) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `componentid` int(11) unsigned NOT NULL DEFAULT '0',
  `sublevel` int(11) DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL DEFAULT '0',
  `browserNav` tinyint(4) DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `utaccess` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` text,
  PRIMARY KEY (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

# Dumping data for table #__menu: 33 rows

INSERT INTO `#__menu` (`id`, `menutype`, `name`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`) VALUES
	(1, 'mainmenu', 'Главная', 'index.php?option=com_frontpage', 'components', 1, 0, 10, 0, 12, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\npage_name=\nno_site_name=0\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=\nmenu_image=-1\npageclass_sfx=\nheader=Добро пожаловать на главную страницу\npage_title=0\nback_button=0\nleading=2\nintro=2\ncolumns=1\nlink=0\norderby_pri=\norderby_sec=front\npagination=2\npagination_results=0\nimage=1\nsection=0\nsection_link=0\nsection_link_type=blog\ncategory=1\ncategory_link=0\ncat_link_type=blog\nitem_title=1\nlink_titles=1\nintro_only=1\nview_introtext=1\nintrotext_limit=\nview_tags=1\nreadmore=0\nrating=0\nauthor=1\nauthor_name=0\ncreatedate=1\nmodifydate=0\nhits=\nprint=0\nemail=0\nunpublished=0'),
	(15, 'usermenu', 'Панель управления', 'administrator/', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1'),
	(16, 'usermenu', 'Добавить ссылку', 'index.php?option=com_weblinks&task=new', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, ''),
	(17, 'usermenu', 'Разблокировать содержимое', 'index.php?option=com_users&task=CheckIn', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, ''),
	(31, 'mainmenu', 'Файловый архив', 'index.php?option=com_boss&task=show_all&directory=6', 'boss_all_content', 1, 0, 0, 0, 15, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(32, 'mainmenu', 'Контакты', 'index.php?option=com_boss&task=show_all&directory=7', 'boss_all_content', 1, 0, 0, 0, 16, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(33, 'othermenu', 'Сайт поддержки', 'http://joostina-cms/', 'url', 1, 0, 0, 0, 7, 0, '0000-00-00 00:00:00', 0, 1, 0, 0, 'title=\nmenu_image='),
	(34, 'othermenu', 'Форум поддержки', 'http://joostina-cms/redirection/forum.html', 'url', 1, 0, 0, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(35, 'othermenu', 'Wiki-Справка', 'http://wiki.joostina-cms/', 'url', 1, 0, 0, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(36, 'topmenu', 'Главная', 'index.php?option=com_frontpage', 'components', 1, 0, 10, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\npage_name=\nno_site_name=0\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=\nmenu_image=\npageclass_sfx=\nheader=\npage_title=1\nback_button=0\nleading=1\nintro=4\ncolumns=2\nlink=4\norderby_pri=\norderby_sec=front\npagination=2\npagination_results=1\nimage=1\nsection=0\nsection_link=0\nsection_link_type=blog\ncategory=0\ncategory_link=0\ncat_link_type=blog\nitem_title=1\nlink_titles=\nintro_only=1\nview_introtext=1\nintrotext_limit=\nreadmore=\nrating=\nauthor=\nauthor_name=0\ncreatedate=\nmodifydate=\nview_tags=\nhits=\nprint=\nemail=\nunpublished=0'),
	(37, 'topmenu', 'Карта сайта', 'index.php?option=com_xmap', 'components', 1, 0, 19, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title='),
	(38, 'topmenu', 'Почта', 'info@joostina-cms.ru', 'url', 1, 0, 0, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image=');


# Dumping structure for table #__messages

CREATE TABLE IF NOT EXISTS `#__messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_from` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id_to` int(10) unsigned NOT NULL DEFAULT '0',
  `folder_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` int(11) NOT NULL DEFAULT '0',
  `priority` int(1) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(230) NOT NULL DEFAULT '',
  `message` text,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__messages_cfg

CREATE TABLE IF NOT EXISTS `#__messages_cfg` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cfg_name` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__modules

CREATE TABLE IF NOT EXISTS `#__modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text,
  `content` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `position` varchar(10) DEFAULT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `module` varchar(50) DEFAULT NULL,
  `numnews` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `showtitle` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `params` text,
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

# Dumping data for table #__modules: 37 rows

INSERT INTO `#__modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES
	(1, 'Ваше мнение', '', 4, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_poll', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-poll\ndef_itemid=0', 0, 0),
	(2, 'Меню пользователя', '', 1, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 1, 1, 'moduleclass_sfx=-new2\nclass_sfx=\nmenutype=usermenu\nmenu_style=ulli\nml_imaged=0\nml_module_number=1\nnumrow=Все\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=aload.gif\nindent_image2=aload.gif\nindent_image3=aload.gif\nindent_image4=aload.gif\nindent_image5=aload.gif\nindent_image6=aload.gif\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=\nml_image2=\nml_image3=\nml_image4=\nml_image5=\nml_image6=-1\nml_image7=-1\nml_image8=-1\nml_image9=-1\nml_image10=-1\nml_image11=-1\nml_image_roll_1=\nml_image_roll_2=\nml_image_roll_3=\nml_image_roll_4=\nml_image_roll_5=\nml_image_roll_6=\nml_image_roll_7=\nml_image_roll_8=\nml_image_roll_9=\nml_image_roll_10=\nml_image_roll_11=\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 1, 0),
	(3, 'Главное меню', '', 1, 'menu1', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-menu1\nclass_sfx=\nmenutype=mainmenu\nmenu_style=linksonly\nml_imaged=0\nml_module_number=1\nnumrow=10\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=-1\nml_image2=-1\nml_image3=-1\nml_image4=-1\nml_image5=-1\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=-1\nml_image_roll_2=-1\nml_image_roll_3=-1\nml_image_roll_4=-1\nml_image_roll_5=-1\nml_image_roll_6=-1\nml_image_roll_7=-1\nml_image_roll_8=-1\nml_image_roll_9=-1\nml_image_roll_10=-1\nml_image_roll_11=-1\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 1, 0),
	(4, 'Авторизация', '', 2, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_ml_login', 0, 0, 0, 'moduleclass_sfx=\ntemplate=popup.php\ntemplate_dir=1\ndr_login_text=Вход / Регистрация\nml_avatar=0\npretext=\nposttext=\nlogin=\nlogin_message=0\ngreeting=1\nuser_name=0\nprofile_link=0\nprofile_link_text=Личный кабинет\nlogout=\nlogout_message=0\nshow_login_text=1\nml_login_text=Логин\nshow_pass_text=1\nml_pass_text=\nshow_remember=0\nml_rem_text=\nshow_lost_pass=1\nml_rem_pass_text=\nshow_register=1\nml_reg_text=\nsubmit_button_text=', 1, 0),
	(5, 'Экспорт новостей', '', 3, 'bottom', 0, '0000-00-00 00:00:00', 1, 'mod_rssfeed', 0, 0, 0, 'cache_time=0\nmoduleclass_sfx=\ntext=\nyandex=0\nrss091=0\nrss10=0\nrss20=1\natom=0\nopml=0\nrss091_image=-1\nrss10_image=-1\nrss20_image=rss-new.png\natom_image=-1\nopml_image=-1\nyandex_image=-1', 1, 0),
	(46, 'Фото', NULL, 2, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-foto\ntemplate=foto\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=0\ncount_special=0\ncount_basic=3\ncolumns=1\ncount_reference=0\nshow_front=1\norderby=rand\ntime=30\nimage=1\nimage_link=1\nimage_default=1\nimage_prev=width\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=150\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_limit=20\ncrop_text_format=0\nshow_date=1\ndate_format=%d-%m-%Y %H:%M\nshow_author=4\nreadmore=1\nlink_text=\nhits=1', 0, 0),
	(7, 'Статистика', '', 3, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_stats', 0, 0, 0, 'cache=1\nserverinfo=1\nsiteinfo=0\ncounter=0\nincrease=0\nmoduleclass_sfx=-stat', 0, 0),
	(8, 'Пользователи', '', 3, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_whosonline', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-new2\nmodule_orientation=1\nall_user=1\nonline_user_count=1\nonline_users=1\nuser_avatar=1', 0, 0),
	(49, 'Помощь on-line', '', 2, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 1, 1, 'moduleclass_sfx=-new2\nclass_sfx=\nmenutype=othermenu\nmenu_style=ulli\nml_imaged=0\nml_module_number=6\nnumrow=Все\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=aload.gif\nindent_image2=aload.gif\nindent_image3=aload.gif\nindent_image4=aload.gif\nindent_image5=aload.gif\nindent_image6=aload.gif\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=\nml_image2=\nml_image3=\nml_image4=\nml_image5=\nml_image6=-1\nml_image7=-1\nml_image8=-1\nml_image9=-1\nml_image10=-1\nml_image11=-1\nml_image_roll_1=\nml_image_roll_2=\nml_image_roll_3=\nml_image_roll_4=\nml_image_roll_5=\nml_image_roll_6=\nml_image_roll_7=\nml_image_roll_8=\nml_image_roll_9=\nml_image_roll_10=\nml_image_roll_11=\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0),
	(10, 'Выбор шаблона', '', 4, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_templatechooser', 0, 0, 1, 'show_preview=1', 0, 0),
	(14, 'Взаимосвязанные элементы', '', 1, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_related_items', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-new1\nlimit=5', 0, 0),
	(15, 'Поиск', '', 1, 'user1', 0, '0000-00-00 00:00:00', 1, 'mod_search', 0, 0, 0, 'moduleclass_sfx=-search\ncache_time=0\ntemplate=default.php\ntemplate_dir=0\nset_itemid=5\nwidth=30\ntext=Поиск...\ntext_pos=inside\nbutton=0\nbutton_pos=bottom\nbutton_text=', 0, 0),
	(16, 'Слайдшоу', '', 1, 'banner1', 0, '0000-00-00 00:00:00', 0, 'mod_random_image', 0, 0, 0, 'rotate_type=1\ntype=jpg\nfolder=images/rotate\nlink=http://www.joostina.ru\nwidth=500\nheight=300\nmoduleclass_sfx=\nimg_pref=slide\ns_autoplay=1\ns_pause=6000\ns_fadeduration=600\npanel_height=55px\npanel_opacity=0.4\npanel_padding=5px\npanel_font=bold 11px Verdana', 0, 0),
	(40, 'Приветствие', '<h3>Добро пожаловать на Ваш первый сайт!</h3>\r\n<div style="text-align:justify">\r\n<p style="text-align:left">Поздравляем! Если Вы видите это сообщение, то Joostina «Lotos» успешно \r\nустановлена и готова к работе. Благодарим за выбор CMS Joostina, \r\nнадеемся что она оправдает возложенные на неё ожидания.\r\n</p><p style="text-align:left">После установки система уже содержит некоторое количество встроенных расширений, все они настроены для быстрого начала работы. </p><p style="text-align:left">Ваш первый тестовый сайт посвящён прекрасному цветку Лотос. Лотос - священный цветок древних египтян, символ красоты, чистоты, стремления к солнцу, свету. Этот образ пронизывает всё египетское искусство, начиная от лотосовидных капителей храмовых колонн и заканчивая миниатюрными туалетными сосудами и ювелирными украшениями.<br /></p></div>  ', 1, 'top', 62, '2012-05-24 08:29:09', 0, '', 0, 0, 0, 'moduleclass_sfx=-top\ncache_time=172800\nrssurl=\nrsstitle=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=0\nrsscache=3600', 0, 0),
	(18, 'Баннеры-3', '', 1, 'banner3', 0, '0000-00-00 00:00:00', 0, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=-ban1\ncategories=3\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 1, 0),
	(19, 'Компоненты', '', 2, 'cpanel', 0, '0000-00-00 00:00:00', 0, 'mod_components', 0, 99, 1, '', 1, 1),
	(22, 'Меню', '', 5, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_stats', 0, 99, 1, '', 0, 1),
	(23, 'Последние зарегистрированные пользователи', '', 4, 'advert2', 0, '0000-00-00 00:00:00', 1, 'mod_latest_users', 0, 99, 1, '', 1, 1),
	(24, 'Новые сообщения', '', 1, 'header', 0, '0000-00-00 00:00:00', 0, 'mod_unread', 0, 99, 1, '', 1, 1),
	(25, 'Активные пользователи', '', 2, 'header', 0, '0000-00-00 00:00:00', 0, 'mod_online', 0, 99, 1, '', 1, 1),
	(26, 'Полное меню', '', 1, 'top', 0, '0000-00-00 00:00:00', 1, 'mod_fullmenu', 0, 99, 1, '', 1, 1),
	(27, 'Путь', '', 1, 'pathway', 0, '0000-00-00 00:00:00', 0, 'mod_pathway', 0, 99, 1, '', 1, 1),
	(28, 'Панель инструментов', '', 1, 'toolbar', 0, '0000-00-00 00:00:00', 1, 'mod_toolbar', 0, 99, 1, '', 1, 1),
	(29, 'Системные сообщения', '', 1, 'inset', 0, '0000-00-00 00:00:00', 1, 'mod_mosmsg', 0, 99, 1, '', 1, 1),
	(30, 'Кнопки быстрого доступа', '', 2, 'icon', 0, '0000-00-00 00:00:00', 1, 'mod_quickicons', 0, 99, 1, '', 1, 1),
	(50, 'Новое в архиве', NULL, 1, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_gdnlotos', 0, 0, 1, 'moduleclass_sfx=-text\ntemplate=text\ncatid=\ncount_special=0\ncount_basic=5\ncolumns=1\ncount_reference=0\ntime=0\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=80\nimage_quality_b=75\ncrop_text_limit=20\ndate_format=%d-%m-%Y %H:%M\nlink_text=\ncache=0\ncache_time=0\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=6\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=6-content_smalldes\nshow_front=1\norderby=rdate\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_format=0\nshow_date=1\nshow_author=4\nreadmore=1\nhits=1', 0, 0),
	(51, 'Горячие новости', NULL, 1, 'user3', 0, '0000-00-00 00:00:00', 0, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-default\ntemplate=default\ntemplate_dir=0\nmodul_link=0\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=0\ndirectory_link=1\ncategory_name=0\ncategory_link=1\ncontent_field=0\ncount_special=1\ncount_basic=0\ncolumns=2\ncount_reference=4\nshow_front=1\norderby=rand\ntime=0\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nimage_size_s=100\nimage_quality_s=75\nimage_size_b=80\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=2\ncrop_text=word\ncrop_text_limit=30\ncrop_text_format=0\nshow_date=0\ndate_format=%d-%m-%Y %H:%M\nshow_author=0\nreadmore=0\nlink_text=\nhits=0', 0, 0),
	(32, 'Wrapper', '', 3, 'header', 0, '0000-00-00 00:00:00', 0, 'mod_wrapper', 0, 0, 1, 'category_a=2-1', 0, 0),
	(33, 'На сайте', '', 0, 'cpanel', 0, '0000-00-00 00:00:00', 0, 'mod_logged', 0, 99, 1, '', 0, 1),
	(34, 'Случайное фото', '', 2, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_random_image', 0, 0, 1, 'rotate_type=0\ntype=jpg\nfolder=images/rotate\nlink=http://www.joostina.ru\nwidth=180\nheight=150\nmoduleclass_sfx=\nslideshow_name=jstSlideShow_1\nimg_pref=pic\ns_autoplay=1\ns_pause=2500\ns_fadeduration=500\npanel_height=55px\npanel_opacity=0.4\npanel_padding=5px\npanel_font=bold 11px Verdana', 0, 0),
	(41, 'Популярные статьи', NULL, 3, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-text\ntemplate=text\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=0\ncount_special=0\ncount_basic=5\ncolumns=1\ncount_reference=0\nshow_front=1\norderby=rhits\ntime=0\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=80\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_limit=20\ncrop_text_format=0\nshow_date=1\ndate_format=%d-%m-%Y %H:%M\nshow_author=4\nreadmore=1\nlink_text=\nhits=1', 0, 0),
	(42, 'Баннеры-2', '', 1, 'banner2', 0, '0000-00-00 00:00:00', 0, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=\ncategories=1\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 0, 0),
	(43, 'Баннеры-4', '', 1, 'banner4', 0, '0000-00-00 00:00:00', 0, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=\ncategories=2\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 0, 0),
	(44, 'Копия Главное меню', '', 1, 'menu2', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-menu2\nclass_sfx=\nmenutype=mainmenu\nmenu_style=linksonly\nml_imaged=0\nml_module_number=1\nnumrow=10\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=-1\nml_image2=-1\nml_image3=-1\nml_image4=-1\nml_image5=-1\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=-1\nml_image_roll_2=-1\nml_image_roll_3=-1\nml_image_roll_4=-1\nml_image_roll_5=-1\nml_image_roll_6=-1\nml_image_roll_7=-1\nml_image_roll_8=-1\nml_image_roll_9=-1\nml_image_roll_10=-1\nml_image_roll_11=-1\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0),
	(21, 'BOSS - Объекты компонента', '', 1, 'banner1', 0, '0000-00-00 00:00:00', 0, 'mod_boss_admin_contents', 0, 99, 1, 'moduleclass_sfx=\ncache=0\nlimit=5\npubl=0\ndisplaycategory=1\ncontent_title=Последние добавленные объекты\ncontent_title_link=Все объекты\nsort=5\ndate_field=date_created\ndisplay_author=1\ndirectory=5\ncat_ids=', 1, 1),
	(45, 'Авторские права', '<div style="text-align:center">Авторские права (с) <a href="http://joostina-cms.ru">Joostina Lotos</a>, 2012<br />Разработка шаблона (с) <a href="http://gd.joostina-cms.ru">Gold Dragon</a>, 2000-2012</div>  ', 1, 'footer', 0, '0000-00-00 00:00:00', 1, '', 0, 0, 0, 'moduleclass_sfx=-footer\ncache_time=0\nrssurl=\nrsstitle=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=0\nrsscache=3600', 0, 0),
	(52, 'Верхнее меню', '', 1, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-topnemu\nclass_sfx=\nmenutype=topmenu\nmenu_style=divs\nml_imaged=1\nml_module_number=4\nnumrow=5\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=aload.gif\nindent_image2=aload.gif\nindent_image3=aload.gif\nindent_image4=aload.gif\nindent_image5=aload.gif\nindent_image6=aload.gif\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=home_new.png\nml_image2=network.png\nml_image3=email.png\nml_image4=\nml_image5=\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=\nml_image_roll_2=\nml_image_roll_3=\nml_image_roll_4=\nml_image_roll_5=\nml_image_roll_6=\nml_image_roll_7=\nml_image_roll_8=\nml_image_roll_9=\nml_image_roll_10=\nml_image_roll_11=\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0);

# Dumping structure for table #__modules_menu

CREATE TABLE IF NOT EXISTS `#__modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__modules_menu: 29 rows

INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES
	(1, 0),
	(2, 0),
	(3, 0),
	(4, 0),
	(5, 0),
	(6, 1),
	(7, 1),
	(8, 0),
	(10, 1),
	(14, 0),
	(15, 0),
	(16, 1),
	(18, 0),
	(21, 0),
	(30, 0),
	(32, 0),
	(34, 1),
	(37, 1),
	(40, 0),
	(41, 0),
	(42, 0),
	(43, 0),
	(44, 0),
	(45, 0),
	(46, 0),
	(49, 0),
	(50, 0),
	(51, 0),
	(52, 0);

# Dumping structure for table #__newsfeeds

CREATE TABLE IF NOT EXISTS `#__newsfeeds` (
  `catid` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `link` text,
  `filename` varchar(200) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `numarticles` int(11) unsigned NOT NULL DEFAULT '1',
  `cache_time` int(11) unsigned NOT NULL DEFAULT '3600',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `code` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

# Dumping structure for table #__polls

CREATE TABLE IF NOT EXISTS `#__polls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `voters` int(9) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `lag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

# Dumping structure for table #__poll_data

CREATE TABLE IF NOT EXISTS `#__poll_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollid` int(4) NOT NULL DEFAULT '0',
  `text` text,
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

# Dumping structure for table #__poll_date

CREATE TABLE IF NOT EXISTS `#__poll_date` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL DEFAULT '0',
  `poll_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

# Dumping structure for table #__poll_menu

CREATE TABLE IF NOT EXISTS `#__poll_menu` (
  `pollid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__poll_menu: 1 rows

INSERT INTO `#__poll_menu` (`pollid`, `menuid`) VALUES
	(14, 0);

# Dumping structure for table #__quickicons

CREATE TABLE IF NOT EXISTS `#__quickicons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(64) NOT NULL DEFAULT '',
  `target` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(100) NOT NULL DEFAULT '',
  `ordering` int(10) unsigned NOT NULL DEFAULT '0',
  `new_window` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `display` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) unsigned NOT NULL DEFAULT '0',
  `gid` int(3) DEFAULT '25',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

# Dumping data for table #__quickicons: 10 rows

INSERT INTO `#__quickicons` (`id`, `text`, `target`, `icon`, `ordering`, `new_window`, `published`, `title`, `display`, `access`, `gid`) VALUES
	(1, 'Контент JoiBoss', 'index2.php?option=com_boss', '/administrator/templates/joostfree/images/cpanel_ico/all_content.png', 1, 0, 1, 'Управление объектами содержимого', 0, 0, 0),
	(2, 'Главная страница', 'index2.php?option=com_frontpage', '/administrator/templates/joostfree/images/cpanel_ico/frontpage.png', 2, 0, 1, 'Управление объектами главной страницы', 0, 0, 0),
	(3, 'Медиа менеджер', 'index2.php?option=com_jwmmxtd', '/administrator/templates/joostfree/images/cpanel_ico/mediamanager.png', 3, 0, 1, 'Управление медиа файлами', 0, 0, 0),
	(4, 'Корзина', 'index2.php?option=com_trash', '/administrator/templates/joostfree/images/cpanel_ico/trash.png', 4, 0, 1, 'Управление корзиной удаленных объектов', 0, 0, 0),
	(5, 'Редактор меню', 'index2.php?option=com_menumanager', '/administrator/templates/joostfree/images/cpanel_ico/menu.png', 5, 0, 1, 'Управление объектами меню', 0, 0, 24),
	(6, 'Файловый менеджер', 'index2.php?option=com_joomlaxplorer', '/administrator/templates/joostfree/images/cpanel_ico/filemanager.png', 6, 0, 1, 'Управление всеми файлами', 0, 0, 25),
	(7, 'Пользователи', 'index2.php?option=com_users', '/administrator/templates/joostfree/images/cpanel_ico/user.png', 7, 0, 1, 'Управление пользователями', 0, 0, 24),
	(8, 'Глобальная конфигурация', 'index2.php?option=com_config&hidemainmenu=1', '/administrator/templates/joostfree/images/cpanel_ico/config.png', 8, 0, 1, 'Глобальная конфигурация сайта', 0, 0, 25),
	(9, 'Резервное копирование', 'index2.php?option=com_joomlapack&act=pack&hidemainmenu=1', '/administrator/templates/joostfree/images/cpanel_ico/backup.png', 9, 0, 1, 'Резервное копирование информации сайта', 0, 0, 24),
	(10, 'Очистить весь кэш', 'index2.php?option=com_admin&task=clean_all_cache', '/administrator/templates/joostfree/images/cpanel_ico/clear.png', 10, 0, 1, 'Очистить весь кэш сайта', 0, 0, 24);

# Dumping structure for table #__session

CREATE TABLE IF NOT EXISTS `#__session` (
  `username` varchar(50) DEFAULT '',
  `time` varchar(14) DEFAULT '',
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `guest` tinyint(4) DEFAULT '1',
  `userid` int(11) DEFAULT '0',
  `usertype` varchar(50) DEFAULT '',
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`(64)),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__stats_agents

CREATE TABLE IF NOT EXISTS `#__stats_agents` (
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '1',
  KEY `type_agent` (`type`,`agent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__supertest

CREATE TABLE IF NOT EXISTS `#__supertest` (
  `id` int(10) DEFAULT NULL,
  `text` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__templates_menu

CREATE TABLE IF NOT EXISTS `#__templates_menu` (
  `template` varchar(50) NOT NULL DEFAULT '',
  `menuid` int(11) NOT NULL DEFAULT '0',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`template`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__templates_menu: 2 rows

INSERT INTO `#__templates_menu` (`template`, `menuid`, `client_id`) VALUES
	('default_tpl', 0, 0),
	('joostfree', 0, 1);

# Dumping structure for table #__template_positions

CREATE TABLE IF NOT EXISTS `#__template_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(10) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

# Dumping data for table #__template_positions: 24 rows

INSERT INTO `#__template_positions` (`id`, `position`, `description`) VALUES
	(1, 'header', 'header'),
	(2, 'footer', 'footer'),
	(3, 'top', 'top'),
	(4, 'bottom', 'bottom'),
	(5, 'menu1', 'menu1'),
	(6, 'menu2', 'menu2'),
	(7, 'left', 'left'),
	(8, 'right', 'right'),
	(9, 'pathway', 'pathway'),
	(10, 'cpanel', 'cpanel'),
	(11, 'banner1', 'banner1'),
	(12, 'banner2', 'banner2'),
	(13, 'banner3', 'banner3'),
	(14, 'banner4', 'banner44'),
	(15, 'user1', 'user1 1'),
	(16, 'user2', 'user2'),
	(17, 'user3', 'user3'),
	(18, 'user4', 'user4'),
	(19, 'user5', 'user5'),
	(20, 'user6', 'user6'),
	(21, 'user7', 'user7'),
	(22, 'user8', 'user8'),
	(23, 'user9', 'user9'),
	(24, 'zero', 'zero');

# Dumping structure for table #__users

CREATE TABLE IF NOT EXISTS `#__users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `usertype` varchar(25) NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL DEFAULT '',
  `params` text,
  `bad_auth_count` int(2) DEFAULT '0',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `idxemail` (`email`),
  KEY `block_id` (`block`,`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

# Dumping structure for table #__users_extra

CREATE TABLE IF NOT EXISTS `#__users_extra` (
  `user_id` int(11) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `about` tinytext,
  `location` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icq` varchar(255) NOT NULL,
  `skype` varchar(255) NOT NULL,
  `jabber` varchar(255) NOT NULL,
  `msn` varchar(255) NOT NULL,
  `yahoo` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `mobil` varchar(255) NOT NULL,
  `birthdate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping structure for table #__usertypes

CREATE TABLE IF NOT EXISTS `#__usertypes` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `mask` varchar(11) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__usertypes: 7 rows

INSERT INTO `#__usertypes` (`id`, `name`, `mask`) VALUES
	(0, 'superadministrator', ''),
	(1, 'administrator', ''),
	(2, 'editor', ''),
	(3, 'user', ''),
	(4, 'author', ''),
	(5, 'publisher', ''),
	(6, 'manager', '');

# Dumping structure for table #__weblinks

CREATE TABLE IF NOT EXISTS `#__weblinks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `params` text,
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`,`published`,`archived`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

# Dumping structure for table #__xmap

CREATE TABLE IF NOT EXISTS `#__xmap` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Dumping data for table #__xmap: 11 rows

INSERT INTO `#__xmap` (`name`, `value`) VALUES
	('version', '1.0'),
	('classname', 'sitemap'),
	('expand_category', '1'),
	('expand_section', '1'),
	('show_menutitle', '1'),
	('columns', '1'),
	('exlinks', '1'),
	('ext_image', 'img_grey.gif'),
	('exclmenus', ''),
	('includelink', '1'),
	('sitemap_default', '1');

# Dumping structure for table #__xmap_ext

CREATE TABLE IF NOT EXISTS `#__xmap_ext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(100) NOT NULL,
  `published` int(1) DEFAULT '0',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

# Dumping data for table #__xmap_ext: 2 rows
DELETE FROM `#__xmap_ext`;
/*!40000 ALTER TABLE `#__xmap_ext` DISABLE KEYS */;
INSERT INTO `#__xmap_ext` (`id`, `extension`, `published`, `params`) VALUES
	(1, 'com_boss', 1, '-1{expand_categories=1\nexpand_sections=1\nshow_unauth=0\ncat_priority=-1\ncat_changefreq=-1\nart_priority=-1\nart_changefreq=-1}'),
	(2, 'com_weblinks', 1, '');

# Dumping structure for table #__xmap_sitemap

CREATE TABLE IF NOT EXISTS `#__xmap_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `expand_category` int(11) DEFAULT NULL,
  `expand_section` int(11) DEFAULT NULL,
  `show_menutitle` int(11) DEFAULT NULL,
  `columns` int(11) DEFAULT NULL,
  `exlinks` int(11) DEFAULT NULL,
  `ext_image` varchar(255) DEFAULT NULL,
  `menus` text,
  `exclmenus` varchar(255) DEFAULT NULL,
  `includelink` int(11) DEFAULT NULL,
  `usecache` int(11) DEFAULT NULL,
  `cachelifetime` int(11) DEFAULT NULL,
  `classname` varchar(255) DEFAULT NULL,
  `count_xml` int(11) DEFAULT NULL,
  `count_html` int(11) DEFAULT NULL,
  `views_xml` int(11) DEFAULT NULL,
  `views_html` int(11) DEFAULT NULL,
  `lastvisit_xml` int(11) DEFAULT NULL,
  `lastvisit_html` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

# Dumping data for table #__xmap_sitemap: 1 rows

INSERT INTO `#__xmap_sitemap` (`id`, `name`, `expand_category`, `expand_section`, `show_menutitle`, `columns`, `exlinks`, `ext_image`, `menus`, `exclmenus`, `includelink`, `usecache`, `cachelifetime`, `classname`, `count_xml`, `count_html`, `views_xml`, `views_html`, `lastvisit_xml`, `lastvisit_html`) VALUES
	(1, 'Карта сайта', 0, 0, 0, 1, 1, 'img_grey.gif', 'mainmenu,0,1,1,0.5,daily\ntopmenu,1,1,1,0.5,daily', '', 1, 1, 1800, 'sitemap', 0, 14, 0, 44, 0, 1337496336);
