-- --------------------------------------------------------
-- Host:                         openserver
-- Server version:               5.1.62-community-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-05-25 13:33:49
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table joostina14.jos_banners
DROP TABLE IF EXISTS `jos_banners`;
CREATE TABLE IF NOT EXISTS `jos_banners` (
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

-- Dumping data for table joostina14.jos_banners: 7 rows
DELETE FROM `jos_banners`;
/*!40000 ALTER TABLE `jos_banners` DISABLE KEYS */;
INSERT INTO `jos_banners` (`id`, `cid`, `tid`, `type`, `name`, `imp_total`, `imp_made`, `clicks`, `image_url`, `click_url`, `custom_banner_code`, `state`, `last_show`, `msec`, `publish_up_date`, `publish_up_time`, `publish_down_date`, `publish_down_time`, `reccurtype`, `reccurweekdays`, `access`, `target`, `border_value`, `border_style`, `border_color`, `click_value`, `complete_clicks`, `imp_value`, `dta_mod_clicks`, `password`, `checked_out`, `checked_out_time`, `alt`, `title`) VALUES
	(8, 1, 1, '', 'Joostina 5', 0, 395, 1, '005.jpg', 'joostina-cms.ru', '', 1, '2012-05-25 09:12:18', 357462, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 1, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(7, 1, 1, '', 'Joostina 4', 0, 381, 0, '004.jpg', 'joostina-cms.ru', '', 1, '2012-05-25 09:13:10', 166214, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(6, 1, 3, '', 'Joostina 3', 0, 245, 0, '003.jpg', 'joostina-cms.ru', '', 1, '2012-05-25 09:12:18', 363877, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(4, 1, 3, '', 'Joostina 1', 0, 265, 0, '001.jpg', 'joostina-cms.ru', '', 1, '2012-05-25 09:13:10', 171192, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(5, 1, 3, '', 'Joostina 2', 0, 267, 0, '002.jpg', 'joostina-cms.ru', '', 1, '2012-05-25 09:10:18', 283928, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(9, 1, 2, '', 'Joostina 6', 0, 403, 0, '006.jpg', 'joostina-cms.ru', '', 1, '2012-05-25 09:12:18', 370025, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(10, 1, 2, '', 'Joostina 7', 0, 373, 0, '007.jpg', 'joostina-cms.ru', '', 1, '2012-05-25 09:13:10', 176158, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', '');
/*!40000 ALTER TABLE `jos_banners` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_banners_categories
DROP TABLE IF EXISTS `jos_banners_categories`;
CREATE TABLE IF NOT EXISTS `jos_banners_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_banners_categories: 3 rows
DELETE FROM `jos_banners_categories`;
/*!40000 ALTER TABLE `jos_banners_categories` DISABLE KEYS */;
INSERT INTO `jos_banners_categories` (`id`, `name`, `description`, `published`, `checked_out`, `checked_out_time`) VALUES
	(1, '300x140x1', '', 1, 0, '0000-00-00 00:00:00'),
	(2, '300x140x2', '', 1, 0, '0000-00-00 00:00:00'),
	(3, '470x140', '', 1, 0, '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `jos_banners_categories` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_banners_clients
DROP TABLE IF EXISTS `jos_banners_clients`;
CREATE TABLE IF NOT EXISTS `jos_banners_clients` (
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

-- Dumping data for table joostina14.jos_banners_clients: 1 rows
DELETE FROM `jos_banners_clients`;
/*!40000 ALTER TABLE `jos_banners_clients` DISABLE KEYS */;
INSERT INTO `jos_banners_clients` (`cid`, `name`, `contact`, `email`, `extrainfo`, `published`, `checked_out`, `checked_out_time`) VALUES
	(1, 'Joostina', 'Joostina', 'info@joostina-cms.ru', 'Разработчики Joostina CMS.', 1, 0, '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `jos_banners_clients` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_categories
DROP TABLE IF EXISTS `jos_boss_5_categories`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_categories` (
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

-- Dumping data for table joostina14.jos_boss_5_categories: 2 rows
DELETE FROM `jos_boss_5_categories`;
/*!40000 ALTER TABLE `jos_boss_5_categories` DISABLE KEYS */;
INSERT INTO `jos_boss_5_categories` (`id`, `parent`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `description`, `ordering`, `published`, `content_types`, `template`, `rights`) VALUES
	(1, 0, 'Новости', 'news', '', '', '', ' ', 0, 1, 1, 'default', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(2, 0, 'Статьи', 'articles', '', '', '', '', 0, 1, 1, 'default', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*');
/*!40000 ALTER TABLE `jos_boss_5_categories` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_contents
DROP TABLE IF EXISTS `jos_boss_5_contents`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_contents` (
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_contents: 6 rows
DELETE FROM `jos_boss_5_contents`;
/*!40000 ALTER TABLE `jos_boss_5_contents` DISABLE KEYS */;
INSERT INTO `jos_boss_5_contents` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `userid`, `published`, `frontpage`, `featured`, `date_created`, `date_last_сomment`, `date_publish`, `date_unpublish`, `views`, `type_content`, `ordering`, `content_editor`, `content_editorfull`) VALUES
	(4, 'Первая статья', 'Первая статья', '', '', '', 63, 1, 1, 1, '2012-05-05 23:35:12', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 38, 1, 0, '<p><em>Этот раздел описывает статус данного документа на время публикации. // /Другие документы могут заменять этот документ. Современное состояние документов // /этой серии поддерживается на W3C.</em></p>', '<p>Этот документ специфицирует HTML 4.01, являющийся частью спецификации линии // /HTML 4.&nbsp;<br />Первой версией HTML 4 был HTML 4.0 <a rel="biblioentry" href="/references.html#ref-HTML40">[HTML40]</a>, опубликованный // /18 декабря 1997 и пересмотренный 24 апреля 1998.&nbsp;<br />Эта спецификация является // /первыми рекомендациями по HTML 4.01. Она включает дополнительные <a href="/changes.html#19991224">изменения после версии HTML 4.0 от 24 // /апреля</a>.&nbsp;<br />Внесены некоторые изменения в DTD/ОТД. Этот документ объявляет // /предыдущую версию HTML 4.0 устаревшей, хотя W3C оставляет её спецификацию&nbsp;и ОТД // /доступными на сайте W3C.</p> // / \r\n<p>Этот документ был рассмотрен членами W3C и других заинтересованных сторон и // /утверждён Директором как Рекомендации W3C. Это неизменяемый документ, он может // /использоваться как справочный материал или цитироваться в других документах. // /Задачей W3C является привлечение внимания к Рекомендациям и этой спецификации и // /её широкое распространение. Это расширит функциональные возможности Web.</p> // / \r\n<p>W3C рекомендует создание пользовательскими агентами (ПА) и авторами (в // /частности, утилитами разработки) документов HTML 4.01, а не HTML 4.0.<br />W3C // /рекомендует создавать документы HTML 4 вместо документов HTML 3.2. Из // /соображений обратной совместимости, W3C также рекомендует, чтобы утилиты, // /интерпретирующие HTML 4, продолжали поддерживать HTML 3.2 и HTML 2.0.</p> // / \r\n<p>За информацией о следующем поколении HTML, "The Extensible HyperText Markup // /Language" <a rel="biblioentry" href="/references.html#ref-XHTML">[XHTML]</a>, обращайтесь на <a href="http://www.w3.org/MarkUp/">W3C HTML Activity</a> и к списку <a href="http://www.w3.org/TR">W3C Technical Reports</a>.</p>'),
	(5, 'Вторая статья статья', 'Вторая статья статья', '', '', '', 63, 1, 1, 1, '2012-05-06 19:35:00', '2012-05-24 02:14:40', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 120, 1, 0, '<img align="left" border="0" alt="" style="height: 108px; width: 160px;" src="/images/stories/pastarchives.jpg" />Этот раздел описывает статус данного документа на время публикации. Другие документы могут заменять этот документ. Современное состояние документов этой серии поддерживается на W3C.', '<p style="text-align: justify;">Этот документ специфицирует HTML 4.01, являющийся частью спецификации линии // /HTML 4. Первой версией HTML 4 был HTML 4.0 <a rel="biblioentry" href="/references.html#ref-HTML40">[HTML40]</a>, опубликованный // /18 декабря 1997 и пересмотренный 24 апреля 1998. Эта спецификация является // /первыми рекомендациями по HTML 4.01. Она включает дополнительные <a href="/changes.html#19991224">изменения после версии HTML 4.0 от 24 // /апреля</a>.&nbsp;<br />Внесены некоторые изменения в DTD/ОТД. Этот документ объявляет // /предыдущую версию HTML 4.0 устаревшей, хотя W3C оставляет её спецификацию&nbsp;и ОТД // /доступными на сайте W3C.</p> \r\n<p style="text-align: justify;">Этот документ был рассмотрен членами W3C и других заинтересованных сторон и // /утверждён Директором как Рекомендации W3C. Это неизменяемый документ, он может // /использоваться как справочный материал или цитироваться в других документах. // /Задачей W3C является привлечение внимания к Рекомендациям и этой спецификации и // /её широкое распространение. Это расширит функциональные возможности Web.</p>&nbsp;W3C рекомендует создание пользовательскими агентами (ПА) и авторами (в // /частности, утилитами разработки) документов HTML 4.01, а не HTML 4.0. W3C // /рекомендует создавать документы HTML 4 вместо документов HTML 3.2. Из // /соображений обратной совместимости, W3C также рекомендует, чтобы утилиты, // /интерпретирующие HTML 4, продолжали поддерживать HTML 3.2 и HTML 2.0. // / \r\n<p style="text-align: justify;">За информацией о следующем поколении HTML, "The Extensible HyperText Markup // /Language" <a rel="biblioentry" href="/references.html#ref-XHTML">[XHTML]</a>, обращайтесь на <a href="http://www.w3.org/MarkUp/">W3C HTML Activity</a> и к списку <a href="http://www.w3.org/TR">W3C Technical Reports</a>.</p>'),
	(6, 'Очень длинное название этой статья и что-то там ещё даже в две строчки', 'Первая статья', '', '', '', 63, 1, 1, 0, '2012-05-06 07:34:48', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4, 1, 0, '<p><em>Этот раздел описывает статус данного документа на время публикации. // /Другие документы могут заменять этот документ. Современное состояние документов // /этой серии поддерживается на W3C.</em><em>&nbsp;</em></p> \r\n<p><em><em>Этот раздел описывает статус данного документа на время публикации. // /Другие документы могут заменять этот документ. Современное состояние документов // /этой серии поддерживается на W3C.</em></em></p>', '<p>Этот документ специфицирует HTML 4.01, являющийся частью спецификации линии // /HTML 4.&nbsp;<br />Первой версией HTML 4 был HTML 4.0 <a rel="biblioentry" href="/references.html#ref-HTML40">[HTML40]</a>, опубликованный // /18 декабря 1997 и пересмотренный 24 апреля 1998.&nbsp;<br />Эта спецификация является // /первыми рекомендациями по HTML 4.01. Она включает дополнительные <a href="/changes.html#19991224">изменения после версии HTML 4.0 от 24 // /апреля</a>.&nbsp;<br />Внесены некоторые изменения в DTD/ОТД. Этот документ объявляет // /предыдущую версию HTML 4.0 устаревшей, хотя W3C оставляет её спецификацию&nbsp;и ОТД // /доступными на сайте W3C.</p> // / \r\n<p>Этот документ был рассмотрен членами W3C и других заинтересованных сторон и // /утверждён Директором как Рекомендации W3C. Это неизменяемый документ, он может // /использоваться как справочный материал или цитироваться в других документах. // /Задачей W3C является привлечение внимания к Рекомендациям и этой спецификации и // /её широкое распространение. Это расширит функциональные возможности Web.</p> // / \r\n<p>W3C рекомендует создание пользовательскими агентами (ПА) и авторами (в // /частности, утилитами разработки) документов HTML 4.01, а не HTML 4.0.<br />W3C // /рекомендует создавать документы HTML 4 вместо документов HTML 3.2. Из // /соображений обратной совместимости, W3C также рекомендует, чтобы утилиты, // /интерпретирующие HTML 4, продолжали поддерживать HTML 3.2 и HTML 2.0.</p> // / \r\n<p>За информацией о следующем поколении HTML, "The Extensible HyperText Markup // /Language" <a rel="biblioentry" href="/references.html#ref-XHTML">[XHTML]</a>, обращайтесь на <a href="http://www.w3.org/MarkUp/">W3C HTML Activity</a> и к списку <a href="http://www.w3.org/TR">W3C Technical Reports</a>.</p>'),
	(7, 'Вторая статья статья', 'Вторая статья статья', '', '', '', 63, 1, 1, 1, '2012-05-06 23:35:00', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 20, 1, 0, '<img align="left" border="0" alt="" style="height: 108px; width: 160px;" src="/images/stories/pastarchives.jpg" />Этот раздел описывает статус данного документа на время публикации. Другие документы могут заменять этот документ. Современное состояние документов этой серии поддерживается на W3C.', '<p style="text-align: justify;">Этот документ специфицирует HTML 4.01, являющийся частью спецификации линии // /HTML 4. Первой версией HTML 4 был HTML 4.0 <a rel="biblioentry" href="/references.html#ref-HTML40">[HTML40]</a>, опубликованный // /18 декабря 1997 и пересмотренный 24 апреля 1998. Эта спецификация является // /первыми рекомендациями по HTML 4.01. Она включает дополнительные <a href="/changes.html#19991224">изменения после версии HTML 4.0 от 24 // /апреля</a>.&nbsp;<br />Внесены некоторые изменения в DTD/ОТД. Этот документ объявляет // /предыдущую версию HTML 4.0 устаревшей, хотя W3C оставляет её спецификацию&nbsp;и ОТД // /доступными на сайте W3C.</p> \r\n<p style="text-align: justify;">Этот документ был рассмотрен членами W3C и других заинтересованных сторон и // /утверждён Директором как Рекомендации W3C. Это неизменяемый документ, он может // /использоваться как справочный материал или цитироваться в других документах. // /Задачей W3C является привлечение внимания к Рекомендациям и этой спецификации и // /её широкое распространение. Это расширит функциональные возможности Web.</p>&nbsp;W3C рекомендует создание пользовательскими агентами (ПА) и авторами (в // /частности, утилитами разработки) документов HTML 4.01, а не HTML 4.0. W3C // /рекомендует создавать документы HTML 4 вместо документов HTML 3.2. Из // /соображений обратной совместимости, W3C также рекомендует, чтобы утилиты, // /интерпретирующие HTML 4, продолжали поддерживать HTML 3.2 и HTML 2.0. // / \r\n<p style="text-align: justify;">За информацией о следующем поколении HTML, "The Extensible HyperText Markup // /Language" <a rel="biblioentry" href="/references.html#ref-XHTML">[XHTML]</a>, обращайтесь на <a href="http://www.w3.org/MarkUp/">W3C HTML Activity</a> и к списку <a href="http://www.w3.org/TR">W3C Technical Reports</a>.</p>'),
	(8, 'Очень длинное название этой статья и что-то там ещё даже в две строчки', 'Первая статья', '', '', '', 63, 1, 1, 0, '2012-05-06 11:34:48', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 0, '<p><em>Этот раздел описывает статус данного документа на время публикации. // /Другие документы могут заменять этот документ. Современное состояние документов // /этой серии поддерживается на W3C.</em><em>&nbsp;</em></p> \r\n<p><em><em>Этот раздел описывает статус данного документа на время публикации. // /Другие документы могут заменять этот документ. Современное состояние документов // /этой серии поддерживается на W3C.</em></em></p>', '<p>Этот документ специфицирует HTML 4.01, являющийся частью спецификации линии // /HTML 4.&nbsp;<br />Первой версией HTML 4 был HTML 4.0 <a rel="biblioentry" href="/references.html#ref-HTML40">[HTML40]</a>, опубликованный // /18 декабря 1997 и пересмотренный 24 апреля 1998.&nbsp;<br />Эта спецификация является // /первыми рекомендациями по HTML 4.01. Она включает дополнительные <a href="/changes.html#19991224">изменения после версии HTML 4.0 от 24 // /апреля</a>.&nbsp;<br />Внесены некоторые изменения в DTD/ОТД. Этот документ объявляет // /предыдущую версию HTML 4.0 устаревшей, хотя W3C оставляет её спецификацию&nbsp;и ОТД // /доступными на сайте W3C.</p> // / \r\n<p>Этот документ был рассмотрен членами W3C и других заинтересованных сторон и // /утверждён Директором как Рекомендации W3C. Это неизменяемый документ, он может // /использоваться как справочный материал или цитироваться в других документах. // /Задачей W3C является привлечение внимания к Рекомендациям и этой спецификации и // /её широкое распространение. Это расширит функциональные возможности Web.</p> // / \r\n<p>W3C рекомендует создание пользовательскими агентами (ПА) и авторами (в // /частности, утилитами разработки) документов HTML 4.01, а не HTML 4.0.<br />W3C // /рекомендует создавать документы HTML 4 вместо документов HTML 3.2. Из // /соображений обратной совместимости, W3C также рекомендует, чтобы утилиты, // /интерпретирующие HTML 4, продолжали поддерживать HTML 3.2 и HTML 2.0.</p> // / \r\n<p>За информацией о следующем поколении HTML, "The Extensible HyperText Markup // /Language" <a rel="biblioentry" href="/references.html#ref-XHTML">[XHTML]</a>, обращайтесь на <a href="http://www.w3.org/MarkUp/">W3C HTML Activity</a> и к списку <a href="http://www.w3.org/TR">W3C Technical Reports</a>.</p>'),
	(9, 'Первая статья', 'Первая статья', '', '', '', 63, 1, 1, 1, '2012-05-06 03:35:12', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 0, '<p><em>Этот раздел описывает статус данного документа на время публикации. // /Другие документы могут заменять этот документ. Современное состояние документов // /этой серии поддерживается на W3C.</em></p>', '<p>Этот документ специфицирует HTML 4.01, являющийся частью спецификации линии // /HTML 4.&nbsp;<br />Первой версией HTML 4 был HTML 4.0 <a rel="biblioentry" href="/references.html#ref-HTML40">[HTML40]</a>, опубликованный // /18 декабря 1997 и пересмотренный 24 апреля 1998.&nbsp;<br />Эта спецификация является // /первыми рекомендациями по HTML 4.01. Она включает дополнительные <a href="/changes.html#19991224">изменения после версии HTML 4.0 от 24 // /апреля</a>.&nbsp;<br />Внесены некоторые изменения в DTD/ОТД. Этот документ объявляет // /предыдущую версию HTML 4.0 устаревшей, хотя W3C оставляет её спецификацию&nbsp;и ОТД // /доступными на сайте W3C.</p> // / \r\n<p>Этот документ был рассмотрен членами W3C и других заинтересованных сторон и // /утверждён Директором как Рекомендации W3C. Это неизменяемый документ, он может // /использоваться как справочный материал или цитироваться в других документах. // /Задачей W3C является привлечение внимания к Рекомендациям и этой спецификации и // /её широкое распространение. Это расширит функциональные возможности Web.</p> // / \r\n<p>W3C рекомендует создание пользовательскими агентами (ПА) и авторами (в // /частности, утилитами разработки) документов HTML 4.01, а не HTML 4.0.<br />W3C // /рекомендует создавать документы HTML 4 вместо документов HTML 3.2. Из // /соображений обратной совместимости, W3C также рекомендует, чтобы утилиты, // /интерпретирующие HTML 4, продолжали поддерживать HTML 3.2 и HTML 2.0.</p> // / \r\n<p>За информацией о следующем поколении HTML, "The Extensible HyperText Markup // /Language" <a rel="biblioentry" href="/references.html#ref-XHTML">[XHTML]</a>, обращайтесь на <a href="http://www.w3.org/MarkUp/">W3C HTML Activity</a> и к списку <a href="http://www.w3.org/TR">W3C Technical Reports</a>.</p>');
/*!40000 ALTER TABLE `jos_boss_5_contents` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_content_category_href
DROP TABLE IF EXISTS `jos_boss_5_content_category_href`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_content_category_href` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COMMENT='Привязка контента к категориям';

-- Dumping data for table joostina14.jos_boss_5_content_category_href: 6 rows
DELETE FROM `jos_boss_5_content_category_href`;
/*!40000 ALTER TABLE `jos_boss_5_content_category_href` DISABLE KEYS */;
INSERT INTO `jos_boss_5_content_category_href` (`id`, `category_id`, `content_id`) VALUES
	(41, 1, 4),
	(40, 1, 5),
	(43, 1, 6),
	(42, 1, 7),
	(44, 1, 8),
	(45, 1, 9);
/*!40000 ALTER TABLE `jos_boss_5_content_category_href` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_content_types
DROP TABLE IF EXISTS `jos_boss_5_content_types`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `fields` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_content_types: 1 rows
DELETE FROM `jos_boss_5_content_types`;
/*!40000 ALTER TABLE `jos_boss_5_content_types` DISABLE KEYS */;
INSERT INTO `jos_boss_5_content_types` (`id`, `name`, `desc`, `fields`, `published`, `ordering`) VALUES
	(1, 'Статьи', 'Обычные статьи без изысков, аналог ком-контент', 0, 1, 1);
/*!40000 ALTER TABLE `jos_boss_5_content_types` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_fields
DROP TABLE IF EXISTS `jos_boss_5_fields`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_fields` (
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

-- Dumping data for table joostina14.jos_boss_5_fields: 2 rows
DELETE FROM `jos_boss_5_fields`;
/*!40000 ALTER TABLE `jos_boss_5_fields` DISABLE KEYS */;
INSERT INTO `jos_boss_5_fields` (`fieldid`, `name`, `title`, `display_title`, `description`, `type`, `text_before`, `text_after`, `tags_open`, `tags_separator`, `tags_close`, `maxlength`, `size`, `required`, `link_text`, `link_image`, `ordering`, `cols`, `rows`, `profile`, `editable`, `searchable`, `sort`, `sort_direction`, `catsid`, `published`, `filter`) VALUES
	(20, 'content_editor', 'Краткое описание', 0, 'Здесь пишем то, что будет отображаться в списке контента (поиск, категории и т.п.)', 'BossTextAreaEditorPlugin', '', '', '', '', '', 2000, 0, 1, '', '', 2, 200, 20, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
	(21, 'content_editorfull', 'Полное описание', 0, 'Здесь пишем основной текст', 'BossTextAreaEditorPlugin', '', '', '', '', '', 2000, 0, 1, '', '', 3, 50, 5, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0);
/*!40000 ALTER TABLE `jos_boss_5_fields` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_field_values
DROP TABLE IF EXISTS `jos_boss_5_field_values`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_field_values` (
  `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(50) NOT NULL DEFAULT '',
  `fieldvalue` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldvalueid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_field_values: 0 rows
DELETE FROM `jos_boss_5_field_values`;
/*!40000 ALTER TABLE `jos_boss_5_field_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_boss_5_field_values` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_groupfields
DROP TABLE IF EXISTS `jos_boss_5_groupfields`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_groupfields` (
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`,`groupid`),
  KEY `template` (`template`),
  KEY `type_tmpl` (`type_tmpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_groupfields: 10 rows
DELETE FROM `jos_boss_5_groupfields`;
/*!40000 ALTER TABLE `jos_boss_5_groupfields` DISABLE KEYS */;
INSERT INTO `jos_boss_5_groupfields` (`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) VALUES
	(20, 12, 'blog', 'category', 0),
	(20, 14, 'blog', 'content', 1),
	(20, 28, 'default', 'category', 0),
	(20, 30, 'default', 'content', 0),
	(20, 16, 'table', 'category', 0),
	(21, 19, 'table', 'content', 0),
	(20, 21, 'template2', 'category', 0),
	(21, 24, 'template2', 'content', 0),
	(21, 14, 'blog', 'content', 2),
	(21, 31, 'default', 'content', 0);
/*!40000 ALTER TABLE `jos_boss_5_groupfields` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_groups
DROP TABLE IF EXISTS `jos_boss_5_groups`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `desc` varchar(20) DEFAULT NULL,
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_groups: 20 rows
DELETE FROM `jos_boss_5_groups`;
/*!40000 ALTER TABLE `jos_boss_5_groups` DISABLE KEYS */;
INSERT INTO `jos_boss_5_groups` (`id`, `name`, `desc`, `template`, `type_tmpl`, `catsid`, `published`) VALUES
	(31, 'ConFull', 'ConFull', 'default', 'content', ',-1,', 1),
	(30, 'ConShort', 'ConShort', 'default', 'content', ',-1,', 1),
	(29, 'CatFull', 'CatFull', 'default', 'category', ',-1,', 1),
	(28, 'CatShort', 'CatShort', 'default', 'category', ',-1,', 1),
	(12, 'ListDescription', 'ListDescription', 'blog', 'category', ',-1,', 1),
	(13, 'ListImage', 'ListImage', 'blog', 'category', ',-1,', 1),
	(14, 'DetailsDescription', 'DetailsDescription', 'blog', 'content', ',-1,', 1),
	(15, 'DetailsImage', 'DetailsImage', 'blog', 'content', ',-1,', 1),
	(16, 'GroupList1', 'GroupList1', 'table', 'category', ',-1,', 1),
	(17, 'GroupDetails1', 'GroupDetails1', 'table', 'content', ',-1,', 1),
	(18, 'GroupDetails2', 'GroupDetails2', 'table', 'content', ',-1,', 1),
	(19, 'GroupDetails3', 'GroupDetails3', 'table', 'content', ',-1,', 1),
	(20, 'GroupDetails4', 'GroupDetails4', 'table', 'content', ',-1,', 1),
	(21, 'GroupList1', 'GroupList1', 'template2', 'category', ',-1,', 1),
	(22, 'GroupList2', 'GroupList2', 'template2', 'category', ',-1,', 1),
	(23, 'ListImage', 'ListImage', 'template2', 'category', ',-1,', 1),
	(24, 'GroupDetails1', 'GroupDetails1', 'template2', 'content', ',-1,', 1),
	(25, 'GroupDetails2', 'GroupDetails2', 'template2', 'content', ',-1,', 1),
	(26, 'GroupDetails3', 'GroupDetails3', 'template2', 'content', ',-1,', 1),
	(27, 'DetailsImage', 'DetailsImage', 'template2', 'content', ',-1,', 1);
/*!40000 ALTER TABLE `jos_boss_5_groups` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_profile
DROP TABLE IF EXISTS `jos_boss_5_profile`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_profile` (
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_profile: 1 rows
DELETE FROM `jos_boss_5_profile`;
/*!40000 ALTER TABLE `jos_boss_5_profile` DISABLE KEYS */;
INSERT INTO `jos_boss_5_profile` (`userid`) VALUES
	(62);
/*!40000 ALTER TABLE `jos_boss_5_profile` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_rating
DROP TABLE IF EXISTS `jos_boss_5_rating`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_rating` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contentid` int(10) DEFAULT '0',
  `userid` int(10) DEFAULT '0',
  `value` tinyint(1) DEFAULT '5',
  `ip` int(11) DEFAULT '0',
  `date` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_rating: 1 rows
DELETE FROM `jos_boss_5_rating`;
/*!40000 ALTER TABLE `jos_boss_5_rating` DISABLE KEYS */;
INSERT INTO `jos_boss_5_rating` (`id`, `contentid`, `userid`, `value`, `ip`, `date`) VALUES
	(1, 5, 0, 4, 2130706433, 1337839134);
/*!40000 ALTER TABLE `jos_boss_5_rating` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_reviews
DROP TABLE IF EXISTS `jos_boss_5_reviews`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contentid` int(10) unsigned DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_reviews: 8 rows
DELETE FROM `jos_boss_5_reviews`;
/*!40000 ALTER TABLE `jos_boss_5_reviews` DISABLE KEYS */;
INSERT INTO `jos_boss_5_reviews` (`id`, `contentid`, `userid`, `title`, `description`, `date`, `published`) VALUES
	(1, 5, 63, 'Gold Dragon', 'sasas', '2012-04-05', 1),
	(2, 5, 62, 'Administrator', 'rewrwerwerwe', '2012-05-24', 1),
	(3, 5, 62, 'Administrator', 'Этот документ был рассмотрен членами W3C и других заинтересованных сторон и // /утверждён Директором как Рекомендации W3C. Это неизменяемый документ, он может // /использоваться как справочный материал или цитироваться в других документах. // /Задачей W3C является привлечение внимания к Рекомендациям и этой спецификации и // /её широкое распространение. Это расширит функциональные возможности Web.', '2012-05-24', 1),
	(4, 5, 0, 'erreer', 'reerer', '2012-05-24', 1),
	(5, 5, 0, 'dds', 'sdsdsd', '2012-05-24', 1),
	(6, 5, 0, 'dssdsd', 'dssdds', '2012-05-24', 1),
	(7, 5, 0, 'ererre', 'erererer', '2012-05-24', 1),
	(8, 5, 0, 'wrwr', 'wrwrwr', '2012-05-24', 1);
/*!40000 ALTER TABLE `jos_boss_5_reviews` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_config
DROP TABLE IF EXISTS `jos_boss_config`;
CREATE TABLE IF NOT EXISTS `jos_boss_config` (
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_config: 1 rows
DELETE FROM `jos_boss_config`;
/*!40000 ALTER TABLE `jos_boss_config` DISABLE KEYS */;
INSERT INTO `jos_boss_config` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `default_order_by`, `contents_per_page`, `root_allowed`, `show_contact`, `send_email_on_new`, `send_email_on_update`, `auto_publish`, `fronttext`, `email_display`, `display_fullname`, `rules_text`, `expiration`, `content_duration`, `recall`, `recall_time`, `recall_text`, `empty_cat`, `cat_max_width`, `cat_max_height`, `cat_max_width_t`, `cat_max_height_t`, `submission_type`, `nb_contents_by_user`, `allow_attachement`, `allow_contact_by_pms`, `allow_comments`, `rating`, `secure_comment`, `comment_sys`, `allow_unregisered_comment`, `allow_ratings`, `secure_new_content`, `use_content_mambot`, `show_rss`, `filter`, `template`, `allow_rights`, `rights`) VALUES
	(5, 'Основной', 'content', '', '', '', '0', 20, 0, 2, 0, 0, 1, '', 0, 0, '', 0, 30, 1, 7, '', 1, 150, 150, 30, 30, 0, -1, 0, 0, 1, 'GDRating', 0, 1, 1, 1, 1, 1, 1, '0', 'default', '1', 'edit_category=23,24,25*edit_content=20,21,23,24,25*edit_directories=23,24,25*edit_conf=23,24,25*edit_types=23,24,25*edit_fields=23,24,25*edit_fieldimages=23,24,25*edit_templates=23,24,25*edit_plugins=23,24,25*import_export=23,24,25*edit_users=24,25*show_user_content=0,18,19,20,21,23,24,25*show_all=0,18,19,20,21,23,24,25*show_search=0,18,19,20,21,23,24,25*show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*');
/*!40000 ALTER TABLE `jos_boss_config` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_plug_config
DROP TABLE IF EXISTS `jos_boss_plug_config`;
CREATE TABLE IF NOT EXISTS `jos_boss_plug_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `directory` int(11) NOT NULL,
  `plug_type` varchar(11) NOT NULL,
  `plug_name` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `value` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `directory` (`directory`,`plug_type`,`plug_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_plug_config: 0 rows
DELETE FROM `jos_boss_plug_config`;
/*!40000 ALTER TABLE `jos_boss_plug_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_boss_plug_config` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_categories
DROP TABLE IF EXISTS `jos_categories`;
CREATE TABLE IF NOT EXISTS `jos_categories` (
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

-- Dumping data for table joostina14.jos_categories: 0 rows
DELETE FROM `jos_categories`;
/*!40000 ALTER TABLE `jos_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_categories` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_components
DROP TABLE IF EXISTS `jos_components`;
CREATE TABLE IF NOT EXISTS `jos_components` (
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

-- Dumping data for table joostina14.jos_components: 36 rows
DELETE FROM `jos_components`;
/*!40000 ALTER TABLE `jos_components` DISABLE KEYS */;
INSERT INTO `jos_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`) VALUES
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
/*!40000 ALTER TABLE `jos_components` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_config
DROP TABLE IF EXISTS `jos_config`;
CREATE TABLE IF NOT EXISTS `jos_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL,
  `subgroup` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_config: 2 rows
DELETE FROM `jos_config`;
/*!40000 ALTER TABLE `jos_config` DISABLE KEYS */;
INSERT INTO `jos_config` (`id`, `group`, `subgroup`, `name`, `value`) VALUES
	(1, 'com_frontpage', 'default', 'directory', 's:1{5}'),
	(2, 'com_frontpage', 'default', 'page', 's:14{show_frontpage}');
/*!40000 ALTER TABLE `jos_config` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_contact_details
DROP TABLE IF EXISTS `jos_contact_details`;
CREATE TABLE IF NOT EXISTS `jos_contact_details` (
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

-- Dumping data for table joostina14.jos_contact_details: 1 rows
DELETE FROM `jos_contact_details`;
/*!40000 ALTER TABLE `jos_contact_details` DISABLE KEYS */;
INSERT INTO `jos_contact_details` (`id`, `name`, `con_position`, `address`, `suburb`, `state`, `country`, `postcode`, `telephone`, `fax`, `misc`, `image`, `imagepos`, `email_to`, `default_con`, `published`, `checked_out`, `checked_out_time`, `ordering`, `params`, `user_id`, `catid`, `access`) VALUES
	(1, 'Joostina Team', 'Положение', 'Улица', 'Район', 'Область(край)', 'Российская Федерация', 'Индекс', 'Телефон', 'Факс', 'www.joostina.ru', '', 'top', 'info@joostina.ru', 0, 1, 0, '0000-00-00 00:00:00', 1, 'menu_image=-1\npageclass_sfx=\nprint=\nback_button=\nname=1\nposition=0\nemail=1\nstreet_address=0\nsuburb=0\nstate=0\ncountry=1\npostcode=0\ntelephone=0\nfax=0\nmisc=1\nimage=0\nvcard=0\nemail_description=0\nemail_description_text=\nemail_form=1\nemail_copy=0\ndrop_down=0\ncontact_icons=1\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=', 0, 12, 0);
/*!40000 ALTER TABLE `jos_contact_details` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_content_rating
DROP TABLE IF EXISTS `jos_content_rating`;
CREATE TABLE IF NOT EXISTS `jos_content_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `rating_sum` int(11) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(11) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_content_rating: 0 rows
DELETE FROM `jos_content_rating`;
/*!40000 ALTER TABLE `jos_content_rating` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_content_rating` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_content_tags
DROP TABLE IF EXISTS `jos_content_tags`;
CREATE TABLE IF NOT EXISTS `jos_content_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL,
  `obj_type` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `obj_id` (`obj_id`,`tag`),
  KEY `obj_type` (`obj_type`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_content_tags: 5 rows
DELETE FROM `jos_content_tags`;
/*!40000 ALTER TABLE `jos_content_tags` DISABLE KEYS */;
INSERT INTO `jos_content_tags` (`id`, `obj_id`, `obj_type`, `tag`) VALUES
	(1, 1, 'com_boss_1', 'Первая статья'),
	(2, 5, 'com_boss_1', 'Первая статья'),
	(3, 6, 'com_boss_1', 'Первая статья'),
	(6, 5, 'com_boss_5', 'йцукку werwe'),
	(7, 7, 'com_boss_5', 'йцукку werwe');
/*!40000 ALTER TABLE `jos_content_tags` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_core_acl_aro
DROP TABLE IF EXISTS `jos_core_acl_aro`;
CREATE TABLE IF NOT EXISTS `jos_core_acl_aro` (
  `aro_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_value` varchar(240) NOT NULL DEFAULT '0',
  `value` int(11) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aro_id`),
  UNIQUE KEY `value` (`value`),
  UNIQUE KEY `jos_gacl_section_value_value_aro` (`section_value`(100),`value`),
  KEY `jos_gacl_hidden_aro` (`hidden`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_core_acl_aro: 2 rows
DELETE FROM `jos_core_acl_aro`;
/*!40000 ALTER TABLE `jos_core_acl_aro` DISABLE KEYS */;
INSERT INTO `jos_core_acl_aro` (`aro_id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
	(10, 'users', 62, 0, 'Administrator', 0),
	(11, 'users', 63, 0, 'test', 0);
/*!40000 ALTER TABLE `jos_core_acl_aro` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_core_acl_aro_groups
DROP TABLE IF EXISTS `jos_core_acl_aro_groups`;
CREATE TABLE IF NOT EXISTS `jos_core_acl_aro_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  KEY `jos_gacl_parent_id_aro_groups` (`parent_id`),
  KEY `jos_gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_core_acl_aro_groups: 11 rows
DELETE FROM `jos_core_acl_aro_groups`;
/*!40000 ALTER TABLE `jos_core_acl_aro_groups` DISABLE KEYS */;
INSERT INTO `jos_core_acl_aro_groups` (`group_id`, `parent_id`, `name`, `lft`, `rgt`) VALUES
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
/*!40000 ALTER TABLE `jos_core_acl_aro_groups` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_core_acl_aro_sections
DROP TABLE IF EXISTS `jos_core_acl_aro_sections`;
CREATE TABLE IF NOT EXISTS `jos_core_acl_aro_sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(230) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`section_id`),
  UNIQUE KEY `value_aro_sections` (`value`),
  KEY `hidden_aro_sections` (`hidden`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_core_acl_aro_sections: 1 rows
DELETE FROM `jos_core_acl_aro_sections`;
/*!40000 ALTER TABLE `jos_core_acl_aro_sections` DISABLE KEYS */;
INSERT INTO `jos_core_acl_aro_sections` (`section_id`, `value`, `order_value`, `name`, `hidden`) VALUES
	(10, 'users', 1, 'Users', 0);
/*!40000 ALTER TABLE `jos_core_acl_aro_sections` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_core_acl_groups_aro_map
DROP TABLE IF EXISTS `jos_core_acl_groups_aro_map`;
CREATE TABLE IF NOT EXISTS `jos_core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(240) NOT NULL DEFAULT '',
  `aro_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`),
  KEY `aro_id` (`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_core_acl_groups_aro_map: 2 rows
DELETE FROM `jos_core_acl_groups_aro_map`;
/*!40000 ALTER TABLE `jos_core_acl_groups_aro_map` DISABLE KEYS */;
INSERT INTO `jos_core_acl_groups_aro_map` (`group_id`, `section_value`, `aro_id`) VALUES
	(18, '', 11),
	(25, '', 10);
/*!40000 ALTER TABLE `jos_core_acl_groups_aro_map` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_core_log_items
DROP TABLE IF EXISTS `jos_core_log_items`;
CREATE TABLE IF NOT EXISTS `jos_core_log_items` (
  `time_stamp` date NOT NULL DEFAULT '0000-00-00',
  `item_table` varchar(50) NOT NULL DEFAULT '',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_core_log_items: 0 rows
DELETE FROM `jos_core_log_items`;
/*!40000 ALTER TABLE `jos_core_log_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_core_log_items` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_core_log_searches
DROP TABLE IF EXISTS `jos_core_log_searches`;
CREATE TABLE IF NOT EXISTS `jos_core_log_searches` (
  `search_term` varchar(128) NOT NULL DEFAULT '',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `hits` (`hits`),
  KEY `search_term` (`search_term`(16))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_core_log_searches: 0 rows
DELETE FROM `jos_core_log_searches`;
/*!40000 ALTER TABLE `jos_core_log_searches` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_core_log_searches` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_groups
DROP TABLE IF EXISTS `jos_groups`;
CREATE TABLE IF NOT EXISTS `jos_groups` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_groups: 3 rows
DELETE FROM `jos_groups`;
/*!40000 ALTER TABLE `jos_groups` DISABLE KEYS */;
INSERT INTO `jos_groups` (`id`, `name`) VALUES
	(0, 'Общий'),
	(1, 'Участники'),
	(2, 'Специальный');
/*!40000 ALTER TABLE `jos_groups` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_jp_def
DROP TABLE IF EXISTS `jos_jp_def`;
CREATE TABLE IF NOT EXISTS `jos_jp_def` (
  `def_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `directory` varchar(255) NOT NULL,
  PRIMARY KEY (`def_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_jp_def: 0 rows
DELETE FROM `jos_jp_def`;
/*!40000 ALTER TABLE `jos_jp_def` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_jp_def` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_jp_packvars
DROP TABLE IF EXISTS `jos_jp_packvars`;
CREATE TABLE IF NOT EXISTS `jos_jp_packvars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `value2` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_jp_packvars: 0 rows
DELETE FROM `jos_jp_packvars`;
/*!40000 ALTER TABLE `jos_jp_packvars` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_jp_packvars` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_mambots
DROP TABLE IF EXISTS `jos_mambots`;
CREATE TABLE IF NOT EXISTS `jos_mambots` (
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

-- Dumping data for table joostina14.jos_mambots: 22 rows
DELETE FROM `jos_mambots`;
/*!40000 ALTER TABLE `jos_mambots` DISABLE KEYS */;
INSERT INTO `jos_mambots` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
	(1, 'Изображение MOS', 'mosimage', 'content', 0, -10000, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(4, 'SEF', 'mossef', 'content', 0, 3, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(5, 'Рейтинг статей', 'plugin_jw_ajaxvote', 'content', 0, 4, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(6, 'Поиск в контенте JoiBoss', 'boss.searchbot', 'search', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', 'directory=1\ncontent_field=content_editorfull\nsearch_limit=50\ngroup_results=0'),
	(7, 'Поиск веб-ссылок', 'weblinks.searchbot', 'search', 0, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(8, 'Поддержка кода', 'moscode', 'content', 0, 2, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(9, 'Простой редактор HTML', 'none', 'editors', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(10, 'Кнопка изображения MOS в редакторе', 'mosimage.btn', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(11, 'Кнопка разрыва страницы MOS в редакторе', 'mospage.btn', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(12, 'Поиск контактов', 'contacts.searchbot', 'search', 0, 3, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
	(13, 'Поиск категорий', 'categories.searchbot', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(15, 'Маскировка E-mail', 'mosemailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(16, 'Поиск лент новостей', 'newsfeeds.searchbot', 'search', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(17, 'Позиции загрузки модуля', 'mosloadposition', 'content', 0, 6, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(18, 'Первый обработчик содержимого', 'first', 'mainbody', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(19, 'Модуль на главной странице', 'frontpagemodule', 'content', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 'mod_position=banner\nmod_type=1\nmod_after=1'),
	(20, 'Контактные данные пользователя', 'user_contacts', 'profile', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	(22, 'Информация ', 'user_info', 'profile', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', 'header=Информация\nshow_header=1\nshow_location=1\ngender=1'),
	(23, 'Библиотека MyLib', 'mylib', 'system', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL),
	(24, 'System - JQuery', 'jquery', 'system', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', NULL),
	(25, 'elRTE Mambot', 'elrte', 'editors', 0, 3, 1, 0, 0, 0, '2011-10-22 22:19:50', NULL),
	(26, 'Spaw', 'spaw', 'editors', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', 'default_width=98%\ndefault_height=400px\nresizing_directions=vertical\nbeautify_xhtml_output=1\ndefault_toolbarset=all\ntemplate=1\nstrip_absolute_urls=1\nrendering_mode=xhtml\nconvert_html_entities=0\nallow_modify=0\nallow_upload=1\nuser_dir=0\nmax_upload_filesize=200000\ndropdown_data_core_style=contact_email\r<br />sectiontableheader\r<br />sectiontableentry1\r<br />sectiontableentry2 \r<br />date\r<br />small\r<br />smalldark\r<br />contentheading\r<br />footer\r<br />lcol\r<br />rcol\r<br />contentdescription\r<br />blog_more\ntable_styles=moduletable\r<br />content\r<br />contenttoc\r<br />contentpane\r<br />prctable пример таблицы');
/*!40000 ALTER TABLE `jos_mambots` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_menu
DROP TABLE IF EXISTS `jos_menu`;
CREATE TABLE IF NOT EXISTS `jos_menu` (
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
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_menu: 24 rows
DELETE FROM `jos_menu`;
/*!40000 ALTER TABLE `jos_menu` DISABLE KEYS */;
INSERT INTO `jos_menu` (`id`, `menutype`, `name`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`) VALUES
	(1, 'mainmenu', 'Главная', 'index.php?option=com_frontpage', 'components', 1, 0, 10, 0, 11, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\npage_name=\nno_site_name=0\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=\nmenu_image=-1\npageclass_sfx=\nheader=Добро пожаловать на главную страницу\npage_title=0\nback_button=0\nleading=2\nintro=2\ncolumns=1\nlink=0\norderby_pri=\norderby_sec=front\npagination=2\npagination_results=0\nimage=1\nsection=0\nsection_link=0\nsection_link_type=blog\ncategory=1\ncategory_link=0\ncat_link_type=blog\nitem_title=1\nlink_titles=1\nintro_only=1\nview_introtext=1\nintrotext_limit=\nview_tags=1\nreadmore=0\nrating=0\nauthor=1\nauthor_name=0\ncreatedate=1\nmodifydate=0\nhits=\nprint=0\nemail=0\nunpublished=0'),
	(2, 'mainmenu', 'Босс', 'index.php?option=com_boss', 'components', -2, 0, 26, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=boss\ndirectory=1\ntask=\ncatid='),
	(4, 'mainmenu', 'Контакты', 'index.php?option=com_contact', 'components', -2, 0, 7, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(5, 'mainmenu', 'Ссылки', 'index.php?option=com_weblinks', 'components', -2, 0, 4, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=web_links.jpg\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nheadings=1\nhits=\nitem_description=1\nother_cat=1\ndescription=1\ndescription_text=\nimage=-1\nimage_align=right\nweblink_icons='),
	(6, 'mainmenu', 'Поиск', 'index.php?option=com_search', 'components', -2, 0, 16, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(7, 'mainmenu', 'Ленты новостей', 'index.php?option=com_newsfeeds', 'components', -2, 0, 12, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nother_cat_section=1\nother_cat=1\ncat_description=1\ncat_items=1\ndescription=0\ndescription_text=\nimage=-1\nimage_align=right\nheadings=1\nname=1\narticles=1\nlink=0\nfeed_image=1\nfeed_descr=1\nitem_descr=1\nword_count=0'),
	(8, 'mainmenu', 'В окне', 'index.php?option=com_wrapper', 'wrapper', -2, 0, 0, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\npage_name=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nscrolling=auto\nwidth=300\nheight=600\nheight_auto=1\nadd=1\nurl=www.joostina.ru'),
	(9, 'othermenu', 'joostina.ru', 'http://www.joostina.ru', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(10, 'othermenu', 'joom.ru', 'http://www.joom.ru', 'url', 1, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(11, 'othermenu', 'joomlaportal.ru', 'http://www.joomlaportal.ru', 'url', 1, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(12, 'othermenu', 'joomlaforum.ru', 'http://www.joomlaforum.ru', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(13, 'othermenu', 'joomla-support.ru', 'http://www.joomla-support.ru', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(14, 'othermenu', 'joomla.ru', 'http://www.joomla.ru', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(15, 'usermenu', 'Панель управления', 'administrator/', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1'),
	(16, 'usermenu', 'Добавить ссылку', 'index.php?option=com_weblinks&task=new', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, ''),
	(17, 'usermenu', 'Разблокировать содержимое', 'index.php?option=com_users&task=CheckIn', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, ''),
	(18, 'mainmenu', 'Карта сайта', 'index.php?option=com_xmap', 'components', -2, 0, 24, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, ''),
	(20, 'mainmenu', 'Опросы', 'index.php?option=com_poll', 'components', -2, 0, 11, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader='),
	(24, 'mainmenu', 'www', 'index.php?option=com_boss', 'components', -2, 4, 20, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title='),
	(25, 'mainmenu', 'fgdg', 'index.php?option=com_boss', 'components', -2, 24, 20, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title='),
	(26, 'mainmenu', 'ййй', 'index.php?option=com_boss&task=show_category&catid=1&directory=2', 'boss_category_content', -2, 0, 0, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(27, 'mainmenu', 'ййц', 'index.php?option=com_boss&task=show_all&directory=4', 'boss_all_content', -2, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(28, 'mainmenu', 'Новости', 'index.php?option=com_boss&task=show_category&catid=1&directory=5', 'boss_category_content', 1, 0, 0, 0, 12, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(29, 'mainmenu', 'Статьи', 'index.php?option=com_boss&task=show_category&catid=2&directory=5', 'boss_category_content', 1, 0, 0, 0, 13, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image=');
/*!40000 ALTER TABLE `jos_menu` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_messages
DROP TABLE IF EXISTS `jos_messages`;
CREATE TABLE IF NOT EXISTS `jos_messages` (
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

-- Dumping data for table joostina14.jos_messages: 0 rows
DELETE FROM `jos_messages`;
/*!40000 ALTER TABLE `jos_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_messages` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_messages_cfg
DROP TABLE IF EXISTS `jos_messages_cfg`;
CREATE TABLE IF NOT EXISTS `jos_messages_cfg` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cfg_name` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_messages_cfg: 0 rows
DELETE FROM `jos_messages_cfg`;
/*!40000 ALTER TABLE `jos_messages_cfg` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_messages_cfg` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_modules
DROP TABLE IF EXISTS `jos_modules`;
CREATE TABLE IF NOT EXISTS `jos_modules` (
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
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_modules: 35 rows
DELETE FROM `jos_modules`;
/*!40000 ALTER TABLE `jos_modules` DISABLE KEYS */;
INSERT INTO `jos_modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES
	(1, 'Ваше мнение', '', 3, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_poll', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-poll\ndef_itemid=0', 0, 0),
	(2, 'Меню пользователя', '', 1, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 1, 1, 'moduleclass_sfx=-new2\nclass_sfx=\nmenutype=usermenu\nmenu_style=ulli\nml_imaged=0\nml_module_number=1\nnumrow=Все\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=aload.gif\nindent_image2=aload.gif\nindent_image3=aload.gif\nindent_image4=aload.gif\nindent_image5=aload.gif\nindent_image6=aload.gif\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=\nml_image2=\nml_image3=\nml_image4=\nml_image5=\nml_image6=-1\nml_image7=-1\nml_image8=-1\nml_image9=-1\nml_image10=-1\nml_image11=-1\nml_image_roll_1=\nml_image_roll_2=\nml_image_roll_3=\nml_image_roll_4=\nml_image_roll_5=\nml_image_roll_6=\nml_image_roll_7=\nml_image_roll_8=\nml_image_roll_9=\nml_image_roll_10=\nml_image_roll_11=\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 1, 0),
	(3, 'Главное меню', '', 1, 'menu1', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-menu1\nclass_sfx=\nmenutype=mainmenu\nmenu_style=linksonly\nml_imaged=0\nml_module_number=1\nnumrow=10\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=-1\nml_image2=-1\nml_image3=-1\nml_image4=-1\nml_image5=-1\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=-1\nml_image_roll_2=-1\nml_image_roll_3=-1\nml_image_roll_4=-1\nml_image_roll_5=-1\nml_image_roll_6=-1\nml_image_roll_7=-1\nml_image_roll_8=-1\nml_image_roll_9=-1\nml_image_roll_10=-1\nml_image_roll_11=-1\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 1, 0),
	(4, 'Авторизация', '', 1, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_ml_login', 0, 0, 0, 'moduleclass_sfx=\ntemplate=popup.php\ntemplate_dir=1\ndr_login_text=Вход / Регистрация\nml_avatar=0\npretext=\nposttext=\nlogin=\nlogin_message=0\ngreeting=1\nuser_name=0\nprofile_link=0\nprofile_link_text=Личный кабинет\nlogout=\nlogout_message=0\nshow_login_text=1\nml_login_text=Логин\nshow_pass_text=1\nml_pass_text=\nshow_remember=0\nml_rem_text=\nshow_lost_pass=1\nml_rem_pass_text=\nshow_register=1\nml_reg_text=\nsubmit_button_text=', 1, 0),
	(5, 'Экспорт новостей', '', 3, 'bottom', 0, '0000-00-00 00:00:00', 1, 'mod_rssfeed', 0, 0, 0, 'cache_time=0\nmoduleclass_sfx=\ntext=\nyandex=0\nrss091=0\nrss10=0\nrss20=1\natom=0\nopml=0\nrss091_image=-1\nrss10_image=-1\nrss20_image=rss-new.png\natom_image=-1\nopml_image=-1\nyandex_image=-1', 1, 0),
	(46, 'Фото', NULL, 2, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-foto\ntemplate=foto\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=0\ncount_special=0\ncount_basic=3\ncolumns=1\ncount_reference=0\nshow_front=1\norderby=rhits\ntime=30\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=150\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_limit=20\ncrop_text_format=0\nshow_date=1\ndate_format=%d-%m-%Y %H:%M\nshow_author=4\nreadmore=1\nlink_text=\nhits=1', 0, 0),
	(7, 'Статистика', '', 3, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_stats', 0, 0, 0, 'cache=1\nserverinfo=1\nsiteinfo=0\ncounter=0\nincrease=0\nmoduleclass_sfx=-stat', 0, 0),
	(8, 'Пользователи', '', 2, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_whosonline', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-new2\nmodule_orientation=1\nall_user=1\nonline_user_count=1\nonline_users=1\nuser_avatar=1', 0, 0),
	(9, 'Популярное', '', 2, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_mostread', 0, 0, 1, 'cache=1\nmoduleclass_sfx=\ncache=1\nnoncss=0\ntype=1\nshow_front=1\nshow_hits=0\ncount=3\ncatid=\nsecid=\ndef_itemid=0', 0, 0),
	(10, 'Выбор шаблона', '', 7, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_templatechooser', 0, 0, 1, 'show_preview=1', 0, 0),
	(14, 'Взаимосвязанные элементы', '', 4, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_related_items', 0, 0, 1, 'cache=0\nmoduleclass_sfx=', 0, 0),
	(15, 'Поиск', '', 1, 'user1', 0, '0000-00-00 00:00:00', 1, 'mod_search', 0, 0, 0, 'moduleclass_sfx=-search\ncache_time=0\ntemplate=default.php\ntemplate_dir=0\nset_itemid=5\nwidth=30\ntext=Поиск...\ntext_pos=inside\nbutton=0\nbutton_pos=bottom\nbutton_text=', 0, 0),
	(16, 'Слайдшоу', '', 1, 'banner1', 0, '0000-00-00 00:00:00', 1, 'mod_random_image', 0, 0, 0, 'rotate_type=1\ntype=jpg\nfolder=images/rotate\nlink=http://www.joostina.ru\nwidth=500\nheight=300\nmoduleclass_sfx=\nimg_pref=slide\ns_autoplay=1\ns_pause=6000\ns_fadeduration=600\npanel_height=55px\npanel_opacity=0.4\npanel_padding=5px\npanel_font=bold 11px Verdana', 0, 0),
	(40, 'Приветствие', '<h3>Добро пожаловать на Ваш первый сайт!</h3>\r\n<div style="text-align:justify">\r\n<p style="text-align:left">Поздравляем! Если Вы видите это сообщение, то Joostina «Lotos» успешно \r\nустановлена и готова к работе. Благодарим за выбор CMS Joostina, \r\nнадеемся что она оправдает возложенные на неё ожидания.\r\n</p><p style="text-align:left">После установки система уже содержит некоторое количество встроенных расширений, все они настроены для быстрого начала работы. </p><p style="text-align:left">Ваш первый тестовый сайт посвящён прекрасному цветку Лотос. Лотос - священный цветок древних египтян, символ красоты, чистоты, стремления к солнцу, свету. Этот образ пронизывает всё египетское искусство, начиная от лотосовидных капителей храмовых колонн и заканчивая миниатюрными туалетными сосудами и ювелирными украшениями.<br /></p></div>  ', 1, 'top', 62, '2012-05-24 08:29:09', 1, '', 0, 0, 0, 'moduleclass_sfx=-top\ncache_time=172800\nrssurl=\nrsstitle=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=0\nrsscache=3600', 0, 0),
	(18, 'Баннеры-3', '', 1, 'banner3', 0, '0000-00-00 00:00:00', 1, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=-ban1\ncategories=3\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 1, 0),
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
	(31, 'Помощь on-line', '', 6, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_mljoostinamenu', 0, 0, 1, 'moduleclass_sfx=-help\nclass_sfx=\nmenutype=othermenu\nmenu_style=ulli\nml_imaged=0\nml_module_number=1\nnumrow=3\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=-1\nml_image2=-1\nml_image3=-1\nml_image4=-1\nml_image5=-1\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=-1\nml_image_roll_2=-1\nml_image_roll_3=-1\nml_image_roll_4=-1\nml_image_roll_5=-1\nml_image_roll_6=-1\nml_image_roll_7=-1\nml_image_roll_8=-1\nml_image_roll_9=-1\nml_image_roll_10=-1\nml_image_roll_11=-1\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0),
	(32, 'Wrapper', '', 2, 'header', 0, '0000-00-00 00:00:00', 0, 'mod_wrapper', 0, 0, 1, 'category_a=2-1', 0, 0),
	(33, 'На сайте', '', 0, 'cpanel', 0, '0000-00-00 00:00:00', 0, 'mod_logged', 0, 99, 1, '', 0, 1),
	(34, 'Случайное фото', '', 1, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_random_image', 0, 0, 1, 'rotate_type=0\ntype=jpg\nfolder=images/rotate\nlink=http://www.joostina.ru\nwidth=180\nheight=150\nmoduleclass_sfx=\nslideshow_name=jstSlideShow_1\nimg_pref=pic\ns_autoplay=1\ns_pause=2500\ns_fadeduration=500\npanel_height=55px\npanel_opacity=0.4\npanel_padding=5px\npanel_font=bold 11px Verdana', 0, 0),
	(41, 'Популярные статьи', NULL, 1, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-text\ntemplate=text\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=0\ncount_special=0\ncount_basic=5\ncolumns=1\ncount_reference=0\nshow_front=1\norderby=rhits\ntime=30\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=80\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_limit=20\ncrop_text_format=0\nshow_date=1\ndate_format=%d-%m-%Y %H:%M\nshow_author=4\nreadmore=1\nlink_text=\nhits=1', 0, 0),
	(42, 'Баннеры-2', '', 1, 'banner2', 0, '0000-00-00 00:00:00', 1, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=\ncategories=1\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 0, 0),
	(43, 'Баннеры-4', '', 1, 'banner4', 0, '0000-00-00 00:00:00', 1, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=\ncategories=2\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 0, 0),
	(44, 'Копия Главное меню', '', 1, 'menu2', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-menu2\nclass_sfx=\nmenutype=mainmenu\nmenu_style=linksonly\nml_imaged=0\nml_module_number=1\nnumrow=10\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=-1\nml_image2=-1\nml_image3=-1\nml_image4=-1\nml_image5=-1\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=-1\nml_image_roll_2=-1\nml_image_roll_3=-1\nml_image_roll_4=-1\nml_image_roll_5=-1\nml_image_roll_6=-1\nml_image_roll_7=-1\nml_image_roll_8=-1\nml_image_roll_9=-1\nml_image_roll_10=-1\nml_image_roll_11=-1\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0),
	(21, 'BOSS - Объекты компонента', '', 1, 'banner1', 0, '0000-00-00 00:00:00', 1, 'mod_boss_admin_contents', 0, 99, 1, 'moduleclass_sfx=\ncache=0\nlimit=5\npubl=0\ndisplaycategory=1\ncontent_title=Последние добавленные объекты\ncontent_title_link=Все объекты\nsort=5\ndate_field=date_created\ndisplay_author=1\ndirectory=5\ncat_ids=', 1, 1),
	(45, 'Авторские права', '<div style="text-align:center">Авторские права (с) <a href="http://joostina-cms.ru">Joostina Lotos</a>, 2012<br />Разработка шаблона (с) <a href="http://gd.joostina-cms.ru">Gold Dragon</a>, 2000-2012</div>  ', 1, 'footer', 0, '0000-00-00 00:00:00', 1, '', 0, 0, 0, 'moduleclass_sfx=-footer\ncache_time=0\nrssurl=\nrsstitle=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=0\nrsscache=3600', 0, 0);
/*!40000 ALTER TABLE `jos_modules` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_modules_menu
DROP TABLE IF EXISTS `jos_modules_menu`;
CREATE TABLE IF NOT EXISTS `jos_modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_modules_menu: 26 rows
DELETE FROM `jos_modules_menu`;
/*!40000 ALTER TABLE `jos_modules_menu` DISABLE KEYS */;
INSERT INTO `jos_modules_menu` (`moduleid`, `menuid`) VALUES
	(1, 0),
	(2, 0),
	(3, 0),
	(4, 0),
	(5, 0),
	(6, 1),
	(7, 1),
	(8, 0),
	(9, 1),
	(10, 1),
	(15, 0),
	(16, 1),
	(18, 0),
	(21, 0),
	(30, 0),
	(31, 0),
	(32, 0),
	(34, 1),
	(37, 1),
	(40, 0),
	(41, 0),
	(42, 0),
	(43, 0),
	(44, 0),
	(45, 0),
	(46, 0);
/*!40000 ALTER TABLE `jos_modules_menu` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_newsfeeds
DROP TABLE IF EXISTS `jos_newsfeeds`;
CREATE TABLE IF NOT EXISTS `jos_newsfeeds` (
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

-- Dumping data for table joostina14.jos_newsfeeds: 4 rows
DELETE FROM `jos_newsfeeds`;
/*!40000 ALTER TABLE `jos_newsfeeds` DISABLE KEYS */;
INSERT INTO `jos_newsfeeds` (`catid`, `id`, `name`, `link`, `filename`, `published`, `numarticles`, `cache_time`, `checked_out`, `checked_out_time`, `ordering`, `code`) VALUES
	(4, 1, 'Joostina! - Новости официального сайта', 'http://www.joostina.ru/index2.php?option=com_rss&feed=RSS2.0&no_html=1', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 8, 0),
	(4, 11, 'Новости Joostina и Joomla! в России', 'http://www.joomlaportal.ru/component/option,com_rss/feed,RSS2.0/no_html,1/', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 2, 0),
	(4, 12, 'Форумы о Joomla! в России', 'http://forum.joom.ru/index.php?type=rss;action=.xml', '', 1, 5, 1200, 0, '0000-00-00 00:00:00', 1, 0),
	(5, 5, 'Хабрахабр', 'http://www.habrahabr.ru/rss/', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 2, 0);
/*!40000 ALTER TABLE `jos_newsfeeds` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_polls
DROP TABLE IF EXISTS `jos_polls`;
CREATE TABLE IF NOT EXISTS `jos_polls` (
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

-- Dumping data for table joostina14.jos_polls: 1 rows
DELETE FROM `jos_polls`;
/*!40000 ALTER TABLE `jos_polls` DISABLE KEYS */;
INSERT INTO `jos_polls` (`id`, `title`, `voters`, `checked_out`, `checked_out_time`, `published`, `access`, `lag`) VALUES
	(14, 'Что такое Лотос?', 5, 0, '0000-00-00 00:00:00', 1, 0, 86400);
/*!40000 ALTER TABLE `jos_polls` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_poll_data
DROP TABLE IF EXISTS `jos_poll_data`;
CREATE TABLE IF NOT EXISTS `jos_poll_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollid` int(4) NOT NULL DEFAULT '0',
  `text` text,
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_poll_data: 12 rows
DELETE FROM `jos_poll_data`;
/*!40000 ALTER TABLE `jos_poll_data` DISABLE KEYS */;
INSERT INTO `jos_poll_data` (`id`, `pollid`, `text`, `hits`) VALUES
	(1, 14, 'Второе имя Joostina 1.4 ', 3),
	(2, 14, 'Прекрасный цветок', 0),
	(3, 14, 'Город на Марсе', 0),
	(4, 14, 'А что такое Лотос?', 2),
	(5, 14, 'Не знаю и знать не хочу', 0),
	(6, 14, '', 0),
	(7, 14, '', 0),
	(8, 14, '', 0),
	(9, 14, '', 0),
	(10, 14, '', 0),
	(11, 14, '', 0),
	(12, 14, '', 0);
/*!40000 ALTER TABLE `jos_poll_data` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_poll_date
DROP TABLE IF EXISTS `jos_poll_date`;
CREATE TABLE IF NOT EXISTS `jos_poll_date` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL DEFAULT '0',
  `poll_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_poll_date: 3 rows
DELETE FROM `jos_poll_date`;
/*!40000 ALTER TABLE `jos_poll_date` DISABLE KEYS */;
INSERT INTO `jos_poll_date` (`id`, `date`, `vote_id`, `poll_id`) VALUES
	(1, '2012-04-27 14:13:59', 4, 14),
	(2, '2012-04-27 14:14:18', 4, 14),
	(3, '2012-04-27 14:14:33', 1, 14);
/*!40000 ALTER TABLE `jos_poll_date` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_poll_menu
DROP TABLE IF EXISTS `jos_poll_menu`;
CREATE TABLE IF NOT EXISTS `jos_poll_menu` (
  `pollid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_poll_menu: 1 rows
DELETE FROM `jos_poll_menu`;
/*!40000 ALTER TABLE `jos_poll_menu` DISABLE KEYS */;
INSERT INTO `jos_poll_menu` (`pollid`, `menuid`) VALUES
	(14, 0);
/*!40000 ALTER TABLE `jos_poll_menu` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_quickicons
DROP TABLE IF EXISTS `jos_quickicons`;
CREATE TABLE IF NOT EXISTS `jos_quickicons` (
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

-- Dumping data for table joostina14.jos_quickicons: 10 rows
DELETE FROM `jos_quickicons`;
/*!40000 ALTER TABLE `jos_quickicons` DISABLE KEYS */;
INSERT INTO `jos_quickicons` (`id`, `text`, `target`, `icon`, `ordering`, `new_window`, `published`, `title`, `display`, `access`, `gid`) VALUES
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
/*!40000 ALTER TABLE `jos_quickicons` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_session
DROP TABLE IF EXISTS `jos_session`;
CREATE TABLE IF NOT EXISTS `jos_session` (
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

-- Dumping data for table joostina14.jos_session: 2 rows
DELETE FROM `jos_session`;
/*!40000 ALTER TABLE `jos_session` DISABLE KEYS */;
INSERT INTO `jos_session` (`username`, `time`, `session_id`, `guest`, `userid`, `usertype`, `gid`) VALUES
	('', '1337937192', 'c2535634924c16c76ce9ef634aa6a3c7', 1, 0, '', 0),
	('admin', '1337936975', 'd561af7eb6f3b238887f30627da8698a', 1, 62, 'Super Administrator', 0);
/*!40000 ALTER TABLE `jos_session` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_stats_agents
DROP TABLE IF EXISTS `jos_stats_agents`;
CREATE TABLE IF NOT EXISTS `jos_stats_agents` (
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '1',
  KEY `type_agent` (`type`,`agent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_stats_agents: 9 rows
DELETE FROM `jos_stats_agents`;
/*!40000 ALTER TABLE `jos_stats_agents` DISABLE KEYS */;
INSERT INTO `jos_stats_agents` (`agent`, `type`, `hits`) VALUES
	('Mozilla Firefox 12.0', 0, 3),
	('Windows XP', 1, 8),
	('joostina14.qqq', 2, 4),
	(' 0', 0, 9),
	('Unknown', 1, 9),
	('Mozilla Firefox 11.0', 0, 3),
	('openserver', 2, 13),
	('Safari 535.11', 0, 1),
	('Safari 535.12', 0, 1);
/*!40000 ALTER TABLE `jos_stats_agents` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_supertest
DROP TABLE IF EXISTS `jos_supertest`;
CREATE TABLE IF NOT EXISTS `jos_supertest` (
  `id` int(10) DEFAULT NULL,
  `text` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_supertest: 0 rows
DELETE FROM `jos_supertest`;
/*!40000 ALTER TABLE `jos_supertest` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_supertest` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_templates_menu
DROP TABLE IF EXISTS `jos_templates_menu`;
CREATE TABLE IF NOT EXISTS `jos_templates_menu` (
  `template` varchar(50) NOT NULL DEFAULT '',
  `menuid` int(11) NOT NULL DEFAULT '0',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`template`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_templates_menu: 2 rows
DELETE FROM `jos_templates_menu`;
/*!40000 ALTER TABLE `jos_templates_menu` DISABLE KEYS */;
INSERT INTO `jos_templates_menu` (`template`, `menuid`, `client_id`) VALUES
	('default_tpl', 0, 0),
	('joostfree', 0, 1);
/*!40000 ALTER TABLE `jos_templates_menu` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_template_positions
DROP TABLE IF EXISTS `jos_template_positions`;
CREATE TABLE IF NOT EXISTS `jos_template_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(10) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_template_positions: 24 rows
DELETE FROM `jos_template_positions`;
/*!40000 ALTER TABLE `jos_template_positions` DISABLE KEYS */;
INSERT INTO `jos_template_positions` (`id`, `position`, `description`) VALUES
	(1, 'header', 'Заголовок'),
	(2, 'footer', 'Нижний колонтитул'),
	(3, 'top', 'Верх'),
	(4, 'bottom', 'Низ'),
	(5, 'menu1', 'Меню 1'),
	(6, 'menu2', 'Меню 2'),
	(7, 'left', 'Левая часть'),
	(8, 'right', 'Правая часть'),
	(9, 'pathway', 'Навигация'),
	(10, 'cpanel', 'Управление'),
	(11, 'banner1', 'Баннер 1'),
	(12, 'banner2', 'Баннер 2'),
	(13, 'banner3', 'Баннер 3'),
	(14, 'banner4', 'Баннер 4'),
	(15, 'user1', 'Пользователь 1'),
	(16, 'user2', 'Пользователь 2'),
	(17, 'user3', 'Пользователь 3'),
	(18, 'user4', 'Пользователь 4'),
	(19, 'user5', 'Пользователь 5'),
	(20, 'user6', 'Пользователь 6'),
	(21, 'user7', 'Пользователь 7'),
	(22, 'user8', 'Пользователь 8'),
	(23, 'user9', 'Пользователь 9'),
	(24, 'zero', 'Нулевая часть');
/*!40000 ALTER TABLE `jos_template_positions` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_users
DROP TABLE IF EXISTS `jos_users`;
CREATE TABLE IF NOT EXISTS `jos_users` (
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

-- Dumping data for table joostina14.jos_users: 2 rows
DELETE FROM `jos_users`;
/*!40000 ALTER TABLE `jos_users` DISABLE KEYS */;
INSERT INTO `jos_users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `gid`, `registerDate`, `lastvisitDate`, `activation`, `params`, `bad_auth_count`, `avatar`) VALUES
	(62, 'Administrator', 'admin', 'mail@mail.ru', 'f7225f4d33c1f648bb09f74142f70c4a:PSA9wQ7PMO4kEdJj', 'Super Administrator', 0, 1, 25, '2012-04-25 10:58:21', '2012-05-25 08:46:44', '', 'editor=\nexpired=\nexpired_time=', 0, 'av_1335358203.jpg'),
	(63, 'test', 'test', 'test@test.test', 'a210f4fbcb66fc18a3f08f1a70c25ad8:kdQz2H4bCP3OsC0q', 'Registered', 0, 0, 18, '2012-04-25 16:44:40', '2012-05-24 14:48:49', '', 'editor=', 0, 'av_1335358219.jpg');
/*!40000 ALTER TABLE `jos_users` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_users_extra
DROP TABLE IF EXISTS `jos_users_extra`;
CREATE TABLE IF NOT EXISTS `jos_users_extra` (
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

-- Dumping data for table joostina14.jos_users_extra: 2 rows
DELETE FROM `jos_users_extra`;
/*!40000 ALTER TABLE `jos_users_extra` DISABLE KEYS */;
INSERT INTO `jos_users_extra` (`user_id`, `gender`, `about`, `location`, `url`, `icq`, `skype`, `jabber`, `msn`, `yahoo`, `phone`, `fax`, `mobil`, `birthdate`) VALUES
	(63, 'no_gender', '', '', '', '', '', '', '', '', '', '', '', '1900-01-01 00:00:00'),
	(62, 'no_gender', '', '', '', '', '', '', '', '', '', '', '', '1900-01-01 00:00:00');
/*!40000 ALTER TABLE `jos_users_extra` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_usertypes
DROP TABLE IF EXISTS `jos_usertypes`;
CREATE TABLE IF NOT EXISTS `jos_usertypes` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `mask` varchar(11) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_usertypes: 7 rows
DELETE FROM `jos_usertypes`;
/*!40000 ALTER TABLE `jos_usertypes` DISABLE KEYS */;
INSERT INTO `jos_usertypes` (`id`, `name`, `mask`) VALUES
	(0, 'superadministrator', ''),
	(1, 'administrator', ''),
	(2, 'editor', ''),
	(3, 'user', ''),
	(4, 'author', ''),
	(5, 'publisher', ''),
	(6, 'manager', '');
/*!40000 ALTER TABLE `jos_usertypes` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_weblinks
DROP TABLE IF EXISTS `jos_weblinks`;
CREATE TABLE IF NOT EXISTS `jos_weblinks` (
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

-- Dumping data for table joostina14.jos_weblinks: 8 rows
DELETE FROM `jos_weblinks`;
/*!40000 ALTER TABLE `jos_weblinks` DISABLE KEYS */;
INSERT INTO `jos_weblinks` (`id`, `catid`, `sid`, `title`, `url`, `description`, `date`, `hits`, `published`, `checked_out`, `checked_out_time`, `ordering`, `archived`, `approved`, `params`) VALUES
	(1, 2, 0, 'Joostina!', 'http://www.joostina.ru', 'Домашняя страница Joostina!', '2007-10-28 23:20:02', 3, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0'),
	(2, 2, 0, 'php.net', 'http://www.php.net', 'Язык программирования, на котором написана Joostina!', '2004-07-07 11:33:24', 0, 1, 0, '0000-00-00 00:00:00', 3, 0, 1, ''),
	(3, 2, 0, 'MySQL', 'http://www.mysql.com', 'База данных, используемая Joostina!', '2004-07-07 10:18:31', 0, 1, 0, '0000-00-00 00:00:00', 5, 0, 1, ''),
	(6, 13, 0, 'Joom.Ru - Русский дом Joomla!', 'http://joom.ru/', 'Русский дом Joomla!', '2005-10-26 22:07:32', 0, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0'),
	(7, 13, 0, 'Форумы Joomla!', 'http://joomla-support.ru/', 'Форумы поддержки пользователей Joomla! в России.', '2005-10-26 22:10:39', 0, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 'target=0'),
	(8, 13, 0, 'Joomlaportal.ru!', 'http://www.joomlaportal.ru/?from_joostina', 'Информация о Joostina и Joomla! в России', '2005-10-26 22:07:32', 0, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0'),
	(9, 13, 0, 'Joomlaforum.ru', 'http://www.joomlaforum.ru/?from_joostina', 'Русский форум поддержки Joostina и Joomla.', '2007-10-28 23:21:39', 0, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 'target=0'),
	(10, 13, 0, 'Joomla.ru', 'http://www.joomla.ru/', 'О Joostina и Joomla в России.', '2007-10-28 23:21:39', 0, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 'target=0');
/*!40000 ALTER TABLE `jos_weblinks` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_xmap
DROP TABLE IF EXISTS `jos_xmap`;
CREATE TABLE IF NOT EXISTS `jos_xmap` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_xmap: 11 rows
DELETE FROM `jos_xmap`;
/*!40000 ALTER TABLE `jos_xmap` DISABLE KEYS */;
INSERT INTO `jos_xmap` (`name`, `value`) VALUES
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
/*!40000 ALTER TABLE `jos_xmap` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_xmap_ext
DROP TABLE IF EXISTS `jos_xmap_ext`;
CREATE TABLE IF NOT EXISTS `jos_xmap_ext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(100) NOT NULL,
  `published` int(1) DEFAULT '0',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_xmap_ext: 2 rows
DELETE FROM `jos_xmap_ext`;
/*!40000 ALTER TABLE `jos_xmap_ext` DISABLE KEYS */;
INSERT INTO `jos_xmap_ext` (`id`, `extension`, `published`, `params`) VALUES
	(1, 'com_boss', 1, '-1{expand_categories=1\nexpand_sections=1\nshow_unauth=0\ncat_priority=-1\ncat_changefreq=-1\nart_priority=-1\nart_changefreq=-1}'),
	(2, 'com_weblinks', 1, '');
/*!40000 ALTER TABLE `jos_xmap_ext` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_xmap_sitemap
DROP TABLE IF EXISTS `jos_xmap_sitemap`;
CREATE TABLE IF NOT EXISTS `jos_xmap_sitemap` (
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

-- Dumping data for table joostina14.jos_xmap_sitemap: 1 rows
DELETE FROM `jos_xmap_sitemap`;
/*!40000 ALTER TABLE `jos_xmap_sitemap` DISABLE KEYS */;
INSERT INTO `jos_xmap_sitemap` (`id`, `name`, `expand_category`, `expand_section`, `show_menutitle`, `columns`, `exlinks`, `ext_image`, `menus`, `exclmenus`, `includelink`, `usecache`, `cachelifetime`, `classname`, `count_xml`, `count_html`, `views_xml`, `views_html`, `lastvisit_xml`, `lastvisit_html`) VALUES
	(1, 'Карта сайта', 0, 0, 0, 1, 1, 'img_grey.gif', 'mainmenu,0,1,1,0.5,daily\ntopmenu,1,1,1,0.5,daily', '', 1, 1, 1800, 'sitemap', 0, 14, 0, 44, 0, 1337496336);
/*!40000 ALTER TABLE `jos_xmap_sitemap` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
