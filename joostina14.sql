-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.1.62-community-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-05-28 21:55:56
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
	(8, 1, 1, '', 'Joostina 5', 0, 596, 2, '005.jpg', 'joostina-cms.ru', '', 1, '2012-05-28 17:50:11', 850266, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 2, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(7, 1, 1, '', 'Joostina 4', 0, 597, 0, '004.jpg', 'joostina-cms.ru', '', 1, '2012-05-28 17:50:59', 760261, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(6, 1, 3, '', 'Joostina 3', 0, 379, 1, '003.jpg', 'joostina-cms.ru', '', 1, '2012-05-28 17:50:59', 768801, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 1, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(4, 1, 3, '', 'Joostina 1', 0, 392, 0, '001.jpg', 'joostina-cms.ru', '', 1, '2012-05-28 17:50:37', 588452, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(5, 1, 3, '', 'Joostina 2', 0, 423, 0, '002.jpg', 'joostina-cms.ru', '', 1, '2012-05-28 17:50:47', 711554, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(9, 1, 2, '', 'Joostina 6', 0, 608, 0, '006.jpg', 'joostina-cms.ru', '', 1, '2012-05-28 17:50:58', 382652, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', ''),
	(10, 1, 2, '', 'Joostina 7', 0, 585, 0, '007.jpg', 'joostina-cms.ru', '', 1, '2012-05-28 17:50:59', 779304, '2012-04-27', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2012-04-27', '', 0, '0000-00-00 00:00:00', '', '');
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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_contents: 7 rows
DELETE FROM `jos_boss_5_contents`;
/*!40000 ALTER TABLE `jos_boss_5_contents` DISABLE KEYS */;
INSERT INTO `jos_boss_5_contents` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `userid`, `published`, `frontpage`, `featured`, `date_created`, `date_last_сomment`, `date_publish`, `date_unpublish`, `views`, `type_content`, `ordering`, `content_editor`, `content_editorfull`) VALUES
	(10, 'Лотос. Первое', 'Лотос. Первое', '', '', '', 62, 1, 1, 0, '2012-05-28 06:23:23', NULL, '2012-05-28 00:23:23', '0000-00-00 00:00:00', 3, 1, 0, '<div style="text-align: justify;"><img align="left" border="0" alt="" style="height: 150px; width: 200px;" src="/images/stories/lotus2.jpg" />В различных традициях реализация потенциальных возможностей изображается как распускание цветка на поверхности вод; на Западе — это роза или лилия, на Востоке — лотос. Космический лотос выступает как образ творения, возникновения мира из первоначальных вод или из пустоты; это особый универсальный принцип, управляющий миром и развивающейся в нем жизнью. \r\n</div>', '<div style="text-align: justify;">Этот символ соединяет в себе солнечный и лунный принципы; он одинаково близок воде и огню, хаосу тьмы и божественному свету. Лотос — это результат взаимодействия созидательных сил Солнца и лунных сил воды, это Космос, поднявшийся из водного хаоса, подобно Солнцу, взошедшему в начале времен, «мир развивающейся жизни в вихре перерождений» (Дж. Кэмпбелл). Это время — прошлое, настоящее и будущее, поскольку каждое растение имеет бутоны, цветы и семена одновременно. «Время и вечность являются двумя аспектами одного и того же восприятия целого, двумя планами единственной, недуалистичной невыразимости; таким образом, сокровище вечности покоится на лотосе рождения и смерти» (Дж. Кэмпбелл).<br />Лотос — символ возрождения и бессмертия<br /><br />Раскрываясь с рассветом и закрываясь на закате, лотос олицетворяет возрождение Солнца, а значит, и любое другое возрождение, возобновление жизненных сил, возращение молодости, бессмертие.<br /><br />По словам Е. П. Блаватской, «лотос символизирует жизнь человека, как и Вселенной», при этом его корень, погруженный в илистую почву, олицетворяет материю, стебель, тянущийся сквозь воду, — душу, а цветок, обращенный к Солнцу, является символом духа. Цветок лотоса не смачивается водой, также как дух не пятнается материей, поэтому лотос олицетворяет вечную жизнь, бессмертную природу человека, духовное раскрытие.<br /> \r\n</div>'),
	(11, 'Лотос. Египет', 'Лотос. Египет', '', '', '', 62, 1, 1, 0, '2012-05-28 16:30:31', NULL, '2012-05-28 16:30:31', '0000-00-00 00:00:00', 2, 1, 0, '<img align="left" border="0" alt="" style="height: 280px; width: 220px;" src="/images/stories/lotus.jpg" />В Древнем Египте с образом лотоса связывалось творение, рождение и Солнце как источник жизни. Этот великий цветок распустился, поднявшись из глубин первичных вод, и вынес на своих лепестках бытие, воплощенное в образе солнечного божества, золотого младенца: из лотоса рождается бог солнца Ра. Восходящее Солнце также часто представляли в виде Гора, который поднимается из лотоса, олицетворяющего Вселенную. Цветок лотоса мог служить троном Осириса, Изиды и Нефтиды.', '<br /> \r\n<div style="text-align: justify;"> Лотос символизировал обновление жизненных сил и возращение молодости, ибо по воззрениям египтян, старый бог умирает, чтобы возродиться молодым. Изображение умершего, держащего цветок лотоса, говорит о воскрешении из мертвых, пробуждении на духовном плане.<br /><br />Как символ процветания и плодородия лотос был атрибутом мемфисского бога растительности Нефертума, который изображался юношей в головном уборе в виде цветка лотоса. В «Текстах пирамид» он назван «лотосом из носа Ра». Каждое утро бог Нефертум встает из лотоса и каждый вечер опускается в воду священного озера.<br /><br />С древнейших времен лотос ассоциировался с верховной властью: лотос был символом Верхнего Египта, а скипетр египетских фараонов выполнялся в виде цветка лотоса на длинном стебле. \r\n</div>'),
	(12, 'Лотос. Индия.', 'Лотос. Индия.', '', '', '', 62, 1, 1, 0, '2012-05-28 06:36:17', NULL, '2012-05-28 12:36:17', '0000-00-00 00:00:00', 0, 1, 0, '<div style="text-align: justify;"><img align="left" border="0" alt="" style="height: 280px; width: 220px;" src="/images/stories/buddalotus.jpg" />В Древней Индии лотос выступает как символ творческой силы, как образ сотворения мира. В лотосе усматривали символ Вселенной, отображение земли, которая плавает, подобно цветку по поверхности океана. Раскрытая чашечка цветка, расположенная посредине,— гора богов Меру.<br /> \r\n</div>', '<div style="text-align: justify;">В Упанишадах творцом и хранителем мира становится Вишну. Он — начало, середина и конец всего мира. Когда Вишну просыпается, из его пупа вырастает цветок лотоса, а в нем рождается Брахма, созидающий миры. В центре небесного рая Вишну течет небесный Ганг, дворец Вишну окружают пять озер с синими, белыми и красными лотосами, которые блестят, как изумруды и сапфиры.<br /><br />С лотосом связана супруга Вишну — Лакшми, богиня счастья, богатства и красоты. Согласно одному из мифов, когда боги и асуры пахтали океан, из него с лотосом в руках вышла Лакшми. По другим представлениям, Лакшми возникла в самом начале творения, всплыв из первозданных вод на цветке лотоса; отсюда ее имена Падма или Камала («лотосная»). Трон в виде лотоса — атрибут большинства индуистских и наиболее почитаемых буддийских божеств.<br /><br />В буддизме лотос символизирует изначальные воды, духовное раскрытие, мудрость и нирвану. Лотос посвящен Будде, «Жемчужине Лотоса», явившемуся из лотоса в виде пламени. Это образ чистоты и совершенства: вырастая из грязи, он остается чистым — так же, как Будда, рожденный в мире. Будда считается сердцем лотоса, он восседает на троне в виде полностью раскрывшегося цветка.<br /><br />Кроме того, в буддизме с появлением лотоса связывается начало новой космической эры. Полный расцвет лотоса олицетворяет колесо непрерывного цикла существования и является символом Гуань-иня, Будды Майтрейи и Амитабхи. В буддийском раю, как и в раю Вишну, в водоемах, сделанных из драгоценностей, «цветут удивительные лотосы разных цветов».<br />Будда в Лотосе<br /><br />«Одним из наиболее могущественных и любимых бодхисаттв буддизма махаяны Тибета, Китая и Японии, является Несущий Лотос Авалокитешвара, „Бог, который смотрит вниз с сочувствием“... К нему обращена миллионы раз повторяемая молитва: Ом мани падме хум, „О Сокровище в сердцевине лотоса“... Он удерживает в одной из левых рук лотос мира». (Дж. Кэмпбелл).<br /> \r\n</div>'),
	(13, 'Лотос. Китай.', 'Лотос. Китай.', '', '', '', 62, 1, 1, 0, '2012-05-28 06:38:47', NULL, '2012-05-28 12:38:47', '0000-00-00 00:00:00', 3, 1, 0, '<div style="text-align: justify;"><img align="left" border="0" alt="" style="height: 200px; width: 300px;" src="/images/stories/lotus1.jpg" />В Китае лотос почитался как священное растение ещё до распространения буддизма и олицетворял чистоту и целомудрие, плодородие и производительную силу.<br /> \r\n</div>', '<div style="text-align: justify;">Согласно традиции китайского буддизма, «Лотос Сердца» олицетворяет солярный огонь, а также время,невидимое и всепоглощающее, раскрытие всего сущего, мир и гармонию. На западном небе, в лотосовом раю, находится лотосовое озеро, где среди цветов, в окружении бодхисаттв восседает Амитофо (Амитабха), Будда Запада. Каждый лотос, растущий на этом озере, соотносится с душой умершего человека.<br /><br />В даосской традиции одна из восьми бессмертных, добродетельная дева Хэ Сянь-гу изображалась держащей в руках символ чистоты — цветок белого лотоса на длинном стебле, изогнутом подобно священному жезлу исполнения желаний.<br /> \r\n</div>'),
	(14, 'Из истории лотоса', 'Из истории лотоса', '', '', '', 62, 1, 1, 0, '2012-05-28 02:43:21', NULL, '2012-05-28 08:43:21', '0000-00-00 00:00:00', 1, 1, 0, '<div style="text-align: justify;"><img align="left" border="0" alt="" style="height: 250px; width: 200px;" src="/images/stories/enc_2551.jpg" />Одно из прекраснейших водных растений на нашей планете - это, конечно, лотос, “нимфа нелюмбо”, блистательная красавица, для которой в водах всего света нет соперниц, законная владычица всех цветов, которые перед ней то же, что мерцающие звезды перед луной в полном сиянии”. Такие слова написал в Вестнике естественных наук за 1856 год ботаник С. И. Гремяченский о лотосе-священном растении у представителей самых разных религий в странах Центральной и Юго-Восточной Азии. <br /> \r\n</div>', '<div style="text-align: justify;">Следует заметить, что священный лотос древних египтян, из которого был рожден бог Ра и который служил троном для Исиды и Осириса, - растение другое, это знаменитая нильская кувшинка (Nymphaea lotos).<br /><br />Священное растение Востока многие века лотосу на Востоке поклонялись, он занимал почетное место в религиозных обрядах, преданиях и легендах, об этом свидетельствуют многочисленные памятники письменности, архитектуры и искусства. Мифопоэтическая традиция Древней Индии представляла нашу землю как гигантский лотос, распустившийся на поверхности вод, а рай - как огромное озеро, поросшее прекрасными розовыми лотосами, где обитают праведные, чистые души. Белый лотос - непременный атрибут божественной власти. Поэтому многие боги Индии традиционно изображались стоящими или сидящими на лотосе или с цветком лотоса в руке. На лотосе восседает Будда и покоится Брахма. Вишну-демиург вселенной держит в одной из четырех рук лотос. “Лотосовые богини” изображаются с цветком лотоса в волосах. Обильный дождь из лотосов полил с неба в момент рождения Будды, и всюду, где только ступала нога божественного новорожденного, вырастал огромный лотос. <br /><br />В Китае лотос почитался как священное растение еще до распространения буддизма. Так, одна из восьми бессмертных, добродетельная дева Хэ Синь-гу изображалась держащей в руках “цветок открытой сердечности” - лотос. В китайской живописи была широко распространена тема “западного рая” - лотосового озера. Каждый лотос, растущий на этом озере, соответствует душе умершего человека. В зависимости от добродетельности или греховности земной жизни человека цветы лотоса либо расцветают, либо вянут. <br /><br />Почему же и в древности и в наши дни люди поклоняются этому растению? Может быть, причина в том, что его цветки изумительно красивы и всегда повернуты к солнцу? А может быть, в том, что оно давало людям вкусную пищу и лекарство от многих болезней. Как лекарственное растение лотос был известен в Китае за несколько тысячелетий до новой эры. В традиционной китайской, индийской, вьетнамской, арабской, тибетской медицине для приготовления лекарств использовали все части растения - семена целиком или их крупные мучнистые зародыши, цветоложе, лепестки, цветоножки, тычинки, пестики, листья, корни и корневища. <br /><br />В наше время в растении обнаружены различные биологически активные вещества, в основном алкалоиды и флавоноиды. Препараты из лотоса употребляются в качестве тонизирующего, кардиотонического, общеукрепляющего средства. Кроме того, лотос - ценное пищевое и диетическое растение. В странах Юго-Восточной Азии его издавна используют в питании и специально выращивают как овощ. Корневища едят в сыром, вареном, жареном виде, маринуют на зиму. Из корней варят суп, получают крахмал и масло. Молодые листья употребляют в пищу наподобие спаржи. Семена едят сырыми и засахаренными как лакомство, засахаривают и кусочки корневищ -получается своеобразный “мармелад”. Из семян и корневищ приготовляют муку. Едят даже тычинки и стебли.<br /> \r\n</div>'),
	(15, 'Лотос орехоносный, или индийский — Nelumbo nucifera', 'Лотос орехоносный, или индийский — Nelumbo nucifera', '', '', '', 62, 1, 0, 0, '2012-05-28 02:46:54', NULL, '2012-05-28 08:46:54', '0000-00-00 00:00:00', 1, 1, 0, '<img align="left" border="0" alt="" style="height: 150px; width: 220px;" src="/images/stories/enc_2550.jpg" />Область распространения лотоса орехоносного обширна. Он растет в северо-восточной части Австралии, на островах Малайского архипелага, острове Шри-Ланка, на Филиппинских островах, на юге Японии, на полуостровах Индостан и Индокитай, в Китае. На территории России лотос встречается в трех местах: по берегам Каспийского моря в дельте Волги и устье Куры, на Дальнем Востоке и в Кубанских лиманах, на восточном побережье Азовского моря.<br />', '<div style="text-align: justify;">На Кубани лотос появился уже в наше время благодаря энтузиазму ученых. В 1938 году гидробиолог С. К. Троицкий впервые стал высаживать привезенные из Астрахани семена в кубанских лиманах - водоемах, расположенных по восточному побережью Азовского моря, в основном в дельте реки Кубани. Не сразу лотос прижился, первые посадки почти исчезли из-за изменения экологических условий. В 60-е годы ботаник А. Г. Шехов стал возрождать лотос в лиманах, и уже через 10 лет растения сильно разрослись и прижились. <br /><br />Лотос - земноводное травянистое многолетнее растение. Стебли лотоса, превратившиеся в мощное толстое корневище, погружены в подводный грунт. Одни листья подводные, чешуевидные, другие - надводные, плавающие или высоко поднятые над водой. Листья плавающие - на длинных гибких черешках, по форме плоские и округлые. Листья возвышающиеся - на прямостоящих черешках, они крупнее, имеют форму воронки диаметром 50-70 см. <br /><br /><img align="right" border="0" alt="" style="height: 200px; width: 200px;" src="/images/stories/enc_2552.jpg" />Цветки крупные, до 30 см в диаметре, с многочисленными розовыми или белыми лепестками, они высоко поднимаются над водой на прямой цветоножке. Чуть ниже места прикрепления цветка имеется так называемая зона реагирования, в которой лотос меняет свое положение вслед за солнцем. Центр цветка составляют многочисленные ярко-желтые тычинки и широкое обратноконическое цветоложе. Цветки обладают несильным, но приятным ароматом. Плод — многоорешек, обратноконической формы — напоминает раструб садовой лейки, с крупными гнездами, в каждом из которых сидит по одному семени. Они темно-коричневые, величиной с небольшой желудь, в плоде их до 30 штук. В сухом месте они сохраняют всхожесть очень долго, иногда столетиями.<br />Известны случаи, когда семена, хранившиеся в музейных коллекциях, прорастали через 150 и даже через 200 лет после сбора. <br /><br />Листья и цветки покрыты тончайшим восковым налетом. Под лучами солнца они светятся и переливаются, как перламутр. Капли воды, словно шарики ртути, перекатываются по листьям. В жаркий солнечный день можно наблюдать очень интересное явление — «живую лабораторию» в действии — «кипение» воды. В углублении листа воздухом, выходящим из отверстий черешка, вода выбрасывается мелкими брызгами.<br /><br />Лотос из дельты Волги несколько отличается от типичного и поэтому выделен в отдельный вид - лотос каспийский (N. caspica). Дальневосточный лотос тоже рассматривается как отдельный вид, он назван лотос Комарова (N. Komarowie) в честь крупнейшего российского ботаника. Впрочем, не все ученые признают эти виды как самостоятельные и считают их разновидностями лотоса орехоносного.<br /><br />В кубанских лиманах, в старых густых зарослях лотоса первые небольшие плавающие листья появляются в мае. Через месяц-полтора вырастают надводные листья, за ними развиваются бутоны, которые увеличиваются и через 15—20 дней раскрываются, превращаясь в ослепительно яркие цветки. После полудня лепестки смыкаются, на второй день рано утром вновь полностью расходятся, после полудня слегка закрываются, а на третий-чет-вертый начинают опадать от малейшего ветерка. Семена вызревают через 35— 40 дней. Они выпадают в воду из поникающих плодов и тонут. Цветение лотоса длится с начала — середины июля до конца сентября. Иногда отдельные цветки встречаются и в октябре.<br /><br />Опыт культивирования сортов еще более скудный. Можно только назвать те, что считаются перспективными для Европы: «Kermesina» - красный махровый японский сорт; «Lily Pons» - с лососево-розовыми чашевидными цветками; «Mrs Perry D. Slocum» - очень крупный розовый махровый, с возрастом цветок делается кремовым; «Moto Botan» - мелкий сорт для бочек, с сильно махровыми малиновыми цветками; «Pygmaea Alba» - листья до 30 см высотой, чисто-белые цветки до 10 см в диаметре. <br /> \r\n</div>'),
	(16, 'Лотос желтый, или американский — Nelumbo lutea', 'Лотос желтый, или американский — Nelumbo lutea', '', '', '', 62, 1, 0, 0, '2012-05-28 02:50:35', '2012-05-28 03:51:54', '2012-05-28 08:50:35', '0000-00-00 00:00:00', 13, 1, 0, '<img align="left" border="0" alt="" style="height: 199px; width: 220px;" src="/images/stories/enc_2549.jpg" />Распространен в Новом Свете. Он встречается в Северной и Южной Америке, на Антильских и Гавайских островах. В Восточном полушарии его выращивают лишь в ботанических садах. Сведений о культуре л. желтого очень мало. Из литературы известно, что содержать его следует в бассейне при температуре не выше 20°С, так как в более теплой воде он не зацветает.<br />', 'Интродукция лотоса желтого успешно прошла на Кубани.&nbsp; Из ботанических садов Сухуми, Душанбе, Ташкента и из Сочи были получины семена. Посеянные весной на мелководье, они хорошо проросли и в середине мая (на 10-15 дней позже л. индийского) на поверхности воды появились плавающие листья. <br /><br />На другой год плавающие листья у сеянцев появились уже в начале мая. В июне развились надводные листья, в июле — бутоны, а цветки диаметром около 20 см начали распускаться в сентябре. К концу октября было собрано более 60 зрелых семян-орешков. Температура воды в водоеме в течение лета неоднократно повышалась до 25-35°, и тем не менее, растения дружно цвели и обильно плодоносили. В дальнейшем развитие листьев, бутонов, цветение и созревание плодов у л. желтого также происходило позже, чем у л. индийского. Лишь воздушные листья у него оказались более долговечными по сравнению со вторым видом. <br /><br />В массе л. желтый представляет красочную картину. Поверхность воды устилают плавающие листья, а над ними возвышаются на стройных, высоких (до 1 м) черешках круглые, около 70 см в диаметре, воздушные надводные листья. Многочисленные желтые или кремовые цветки раскрываются на восходе солнца. Они более ароматны нежели у л. индийского. К полудню лепестки смыкаются в плотный бутон. Так повторяется 4-5 дней, а затем лепестки опадают. Поверхность листьев и цветки покрыты тончайшим восковым налетом. Плоды л. желтого напоминают раструб садовой лейки. На его поверхности в ячейках созревает до 25 круглых орешков диаметром около 1 см с твердой оболочкой. Всхожесть сохраняется чрезвычайно долго. <br /><br />Корневище залегает на глубине 60 см. Из каждого узла отрастают многочисленные корни, два листа и цветонос. Благодаря постоянному росту корневищ цветение л. желтого продолжается до октября-ноября. В настоящее время этот вид обитает на Кубани в двух водоемах: в ботаническом саду КГУ и в станице Марьянской. <br />');
/*!40000 ALTER TABLE `jos_boss_5_contents` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_5_content_category_href
DROP TABLE IF EXISTS `jos_boss_5_content_category_href`;
CREATE TABLE IF NOT EXISTS `jos_boss_5_content_category_href` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='Привязка контента к категориям';

-- Dumping data for table joostina14.jos_boss_5_content_category_href: 7 rows
DELETE FROM `jos_boss_5_content_category_href`;
/*!40000 ALTER TABLE `jos_boss_5_content_category_href` DISABLE KEYS */;
INSERT INTO `jos_boss_5_content_category_href` (`id`, `category_id`, `content_id`) VALUES
	(49, 1, 10),
	(67, 1, 11),
	(66, 1, 12),
	(65, 1, 13),
	(62, 2, 14),
	(71, 2, 15),
	(69, 2, 16);
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_rating: 8 rows
DELETE FROM `jos_boss_5_rating`;
/*!40000 ALTER TABLE `jos_boss_5_rating` DISABLE KEYS */;
INSERT INTO `jos_boss_5_rating` (`id`, `contentid`, `userid`, `value`, `ip`, `date`) VALUES
	(1, 5, 0, 4, 2130706433, 1337839134),
	(2, 7, 62, 4, 2130706433, 1338195786),
	(3, 8, 62, 7, 2130706433, 1338196621),
	(4, 9, 62, 7, 2130706433, 1338196650),
	(5, 4, 62, 2, 2130706433, 1338196669),
	(6, 14, 62, 7, 2130706433, 1338202135),
	(7, 16, 62, 6, 2130706433, 1338202405),
	(8, 13, 62, 5, 2130706433, 1338210000);
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_5_reviews: 9 rows
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
	(8, 5, 0, 'wrwr', 'wrwrwr', '2012-05-24', 1),
	(9, 16, 0, 'Гостья', 'Интересная статья', '2012-05-28', 1);
/*!40000 ALTER TABLE `jos_boss_5_reviews` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_categories
DROP TABLE IF EXISTS `jos_boss_6_categories`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_categories` (
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

-- Dumping data for table joostina14.jos_boss_6_categories: 2 rows
DELETE FROM `jos_boss_6_categories`;
/*!40000 ALTER TABLE `jos_boss_6_categories` DISABLE KEYS */;
INSERT INTO `jos_boss_6_categories` (`id`, `parent`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `description`, `ordering`, `published`, `content_types`, `template`, `rights`) VALUES
	(6, 0, 'Анектоды', '', '', '', '', '<br /> ', 0, 1, 1, 'files', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(8, 0, 'Фотозаставки', '', '', '', '', '<br /> ', 0, 1, 1, 'files', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*');
/*!40000 ALTER TABLE `jos_boss_6_categories` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_contents
DROP TABLE IF EXISTS `jos_boss_6_contents`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_contents` (
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

-- Dumping data for table joostina14.jos_boss_6_contents: 5 rows
DELETE FROM `jos_boss_6_contents`;
/*!40000 ALTER TABLE `jos_boss_6_contents` DISABLE KEYS */;
INSERT INTO `jos_boss_6_contents` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `userid`, `published`, `frontpage`, `featured`, `date_created`, `date_last_сomment`, `date_publish`, `date_unpublish`, `views`, `type_content`, `ordering`, `content_version`, `content_os`, `content_file`, `content_alldes`, `content_datecreate`, `content_lang`, `content_price`, `content_lic`, `content_foto`, `content_autor`, `content_email`, `content_url`, `content_smalldes`) VALUES
	(3, 'Фото1', 'Фото1', '', '', '', 62, 1, 0, 0, '2012-05-28 06:34:24', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 0, '', ',,', '[{"signature":"","counter":"0","file":"foto1.zip"}]', '<br />', '', ',,', '0', '5', '1', 'Неизвестно', 'info@joostina-cms.ru', 'joostina-cms.ru', 'Краткое описание фотографии № 1'),
	(4, 'Фото2', 'Фото2', '', '', '', 62, 1, 0, 0, '2012-05-28 16:37:49', NULL, '2012-05-28 12:37:49', '0000-00-00 00:00:00', 0, 1, 0, '', ',,', '[{"signature":"Описание файла","file":"foto2.zip"}]', '<br />', '', ',,', '0', '5', '1', 'Неизвестно', 'info@joostina-cms.ru', 'joostina-cms.ru', 'Краткое описание фотографии 1'),
	(5, 'Фото3', 'Фото3', '', '', '', 62, 1, 0, 0, '2012-05-28 16:39:29', '2012-05-28 04:46:07', '2012-05-28 12:39:29', '0000-00-00 00:00:00', 10, 1, 0, '', ',,', '[{"signature":"","file":"foto3.zip"}]', '<br />', '', ',1,', '10 руб', '6', '1', 'Неизвестно', 'info@joostina-cms.ru', 'joostina-cms.ru', ''),
	(6, 'Депресивные сказки', 'Депресивные сказки', '', '', '', 62, 1, 0, 0, '2012-05-28 16:41:00', NULL, '2012-05-28 12:41:00', '0000-00-00 00:00:00', 8, 1, 0, '', ',,', '[{"signature":"","file":"akulchenko.zip"}]', '<br />', '1999-08-09', ',1,', '0', '5', '1', 'Юрий Аульченко', 'yurii@bionet.nsc.ru', 'main.sicnit.ru/fen-club/', 'Вот жил человек, который очень много спал. Бывало, откроет он глаза рано-рано утром, посмотрит вокруг, отвернется к стенке и опять засыпает.'),
	(7, 'Милицейские байки', 'Милицейские байки', '', '', '', 62, 1, 0, 0, '2012-05-28 16:44:04', NULL, '2012-05-28 12:44:04', '0000-00-00 00:00:00', 13, 1, 0, '', ',,', '[{"signature":"","file":"miliceis_004.zip","counter":1}]', 'Андрея Объедкова в анекдоте привлекает, как и должно, меткость, острота, злободневность, краткость. От собирательства он незаметно переходит к созданию юморесок из милицейской жизни и находит им своё определение «милицейские байки». И вот уже многие столичные газеты наперебой печатают эти объедковские байки: «Литературная Россия», «Патриот», «Правда России», журналы «Милиция», «Жеглов и Шарапов» и даже знаменитый «Крокодил»<br />', '', ',,', '0', '5', '1', 'Андрей Объедков', '', '', '');
/*!40000 ALTER TABLE `jos_boss_6_contents` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_content_category_href
DROP TABLE IF EXISTS `jos_boss_6_content_category_href`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_content_category_href` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='Привязка контента к категориям';

-- Dumping data for table joostina14.jos_boss_6_content_category_href: 5 rows
DELETE FROM `jos_boss_6_content_category_href`;
/*!40000 ALTER TABLE `jos_boss_6_content_category_href` DISABLE KEYS */;
INSERT INTO `jos_boss_6_content_category_href` (`id`, `category_id`, `content_id`) VALUES
	(17, 8, 3),
	(18, 8, 4),
	(19, 8, 5),
	(20, 6, 6),
	(21, 6, 7);
/*!40000 ALTER TABLE `jos_boss_6_content_category_href` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_content_types
DROP TABLE IF EXISTS `jos_boss_6_content_types`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `fields` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_6_content_types: 1 rows
DELETE FROM `jos_boss_6_content_types`;
/*!40000 ALTER TABLE `jos_boss_6_content_types` DISABLE KEYS */;
INSERT INTO `jos_boss_6_content_types` (`id`, `name`, `desc`, `fields`, `published`, `ordering`) VALUES
	(1, 'Архив файла', '', 0, 1, 1);
/*!40000 ALTER TABLE `jos_boss_6_content_types` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_fields
DROP TABLE IF EXISTS `jos_boss_6_fields`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_fields` (
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

-- Dumping data for table joostina14.jos_boss_6_fields: 13 rows
DELETE FROM `jos_boss_6_fields`;
/*!40000 ALTER TABLE `jos_boss_6_fields` DISABLE KEYS */;
INSERT INTO `jos_boss_6_fields` (`fieldid`, `name`, `title`, `display_title`, `description`, `type`, `text_before`, `text_after`, `tags_open`, `tags_separator`, `tags_close`, `maxlength`, `size`, `required`, `link_text`, `link_image`, `ordering`, `cols`, `rows`, `profile`, `editable`, `searchable`, `sort`, `sort_direction`, `catsid`, `published`, `filter`) VALUES
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
/*!40000 ALTER TABLE `jos_boss_6_fields` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_field_values
DROP TABLE IF EXISTS `jos_boss_6_field_values`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_field_values` (
  `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(50) NOT NULL DEFAULT '',
  `fieldvalue` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldvalueid`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_6_field_values: 42 rows
DELETE FROM `jos_boss_6_field_values`;
/*!40000 ALTER TABLE `jos_boss_6_field_values` DISABLE KEYS */;
INSERT INTO `jos_boss_6_field_values` (`fieldvalueid`, `fieldid`, `fieldtitle`, `fieldvalue`, `ordering`, `sys`) VALUES
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
/*!40000 ALTER TABLE `jos_boss_6_field_values` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_groupfields
DROP TABLE IF EXISTS `jos_boss_6_groupfields`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_groupfields` (
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`,`groupid`),
  KEY `template` (`template`),
  KEY `type_tmpl` (`type_tmpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_6_groupfields: 17 rows
DELETE FROM `jos_boss_6_groupfields`;
/*!40000 ALTER TABLE `jos_boss_6_groupfields` DISABLE KEYS */;
INSERT INTO `jos_boss_6_groupfields` (`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) VALUES
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
/*!40000 ALTER TABLE `jos_boss_6_groupfields` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_groups
DROP TABLE IF EXISTS `jos_boss_6_groups`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `desc` varchar(20) DEFAULT NULL,
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_6_groups: 17 rows
DELETE FROM `jos_boss_6_groups`;
/*!40000 ALTER TABLE `jos_boss_6_groups` DISABLE KEYS */;
INSERT INTO `jos_boss_6_groups` (`id`, `name`, `desc`, `template`, `type_tmpl`, `catsid`, `published`) VALUES
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
/*!40000 ALTER TABLE `jos_boss_6_groups` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_profile
DROP TABLE IF EXISTS `jos_boss_6_profile`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_profile` (
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_6_profile: 0 rows
DELETE FROM `jos_boss_6_profile`;
/*!40000 ALTER TABLE `jos_boss_6_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_boss_6_profile` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_rating
DROP TABLE IF EXISTS `jos_boss_6_rating`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_rating` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contentid` int(10) DEFAULT '0',
  `userid` int(10) DEFAULT '0',
  `value` tinyint(1) DEFAULT '5',
  `ip` int(11) DEFAULT '0',
  `date` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_6_rating: 3 rows
DELETE FROM `jos_boss_6_rating`;
/*!40000 ALTER TABLE `jos_boss_6_rating` DISABLE KEYS */;
INSERT INTO `jos_boss_6_rating` (`id`, `contentid`, `userid`, `value`, `ip`, `date`) VALUES
	(1, 7, 62, 6, 2130706433, 1338196802),
	(2, 6, 62, 8, 2130706433, 1338198433),
	(3, 5, 62, 6, 2130706433, 1338209318);
/*!40000 ALTER TABLE `jos_boss_6_rating` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_6_reviews
DROP TABLE IF EXISTS `jos_boss_6_reviews`;
CREATE TABLE IF NOT EXISTS `jos_boss_6_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contentid` int(10) unsigned DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_6_reviews: 1 rows
DELETE FROM `jos_boss_6_reviews`;
/*!40000 ALTER TABLE `jos_boss_6_reviews` DISABLE KEYS */;
INSERT INTO `jos_boss_6_reviews` (`id`, `contentid`, `userid`, `title`, `description`, `date`, `published`) VALUES
	(1, 5, 62, 'Administrator', 'Тестовый комментарий', '2012-05-28', 1);
/*!40000 ALTER TABLE `jos_boss_6_reviews` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_categories
DROP TABLE IF EXISTS `jos_boss_7_categories`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_categories` (
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

-- Dumping data for table joostina14.jos_boss_7_categories: 5 rows
DELETE FROM `jos_boss_7_categories`;
/*!40000 ALTER TABLE `jos_boss_7_categories` DISABLE KEYS */;
INSERT INTO `jos_boss_7_categories` (`id`, `parent`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `description`, `ordering`, `published`, `content_types`, `template`, `rights`) VALUES
	(4, 0, 'Мультгерои', '', '', '', '', ' ', 0, 1, 1, 'contact', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=19,20,21,23,24,25*edit_all_content=20,21,23,24,25*delete_user_content=19,20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(5, 4, 'Иностранные', '', '', '', '', ' ', 0, 1, 1, 'contact', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=19,20,21,23,24,25*edit_all_content=20,21,23,24,25*delete_user_content=19,20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(6, 4, 'Российские', '', '', '', '', ' ', 0, 1, 1, 'contact', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=19,20,21,23,24,25*edit_all_content=20,21,23,24,25*delete_user_content=19,20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(7, 0, 'Президенты', '', '', '', '', '<br /> ', 0, 1, 1, 'contact', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=19,20,21,23,24,25*edit_all_content=20,21,23,24,25*delete_user_content=19,20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(8, 0, 'Учёные', '', '', '', '', ' ', 0, 1, 1, 'contact', 'show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=19,20,21,23,24,25*edit_all_content=20,21,23,24,25*delete_user_content=19,20,21,23,24,25*delete_all_content=21,23,24,25*');
/*!40000 ALTER TABLE `jos_boss_7_categories` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_contents
DROP TABLE IF EXISTS `jos_boss_7_contents`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_contents` (
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

-- Dumping data for table joostina14.jos_boss_7_contents: 12 rows
DELETE FROM `jos_boss_7_contents`;
/*!40000 ALTER TABLE `jos_boss_7_contents` DISABLE KEYS */;
INSERT INTO `jos_boss_7_contents` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `userid`, `published`, `frontpage`, `featured`, `date_created`, `date_last_сomment`, `date_publish`, `date_unpublish`, `views`, `type_content`, `ordering`, `content_fam`, `content_im`, `content_ot`, `content_dataa`, `content_mail`, `content_foto`, `content_editorfull`, `content_stel`, `content_ltel`, `content_skype`, `content_icq`, `content_jid`, `content_url`) VALUES
	(18, 'Микки Маус  Диснеевич', 'Микки Маус  Диснеевич', '', '', '', 62, 1, 1, 0, '2012-03-04 16:22:01', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 0, 'Микки', 'Маус', 'Диснеевич', '1928-05-15', 'mickey.mouse@disney.com', '1', '<div style="text-align:justify">МиккиМаус (Мышонок Микки, англ. Mickey Mouse) — мультипликационный персонаж, один из символов компании Уолта Диснея и американской поп-культуры вообще. Представляет собой антропоморфного мышонка. Официально днём рождения Микки считается 15 мая 1928 года, когда он появился в мультфильме под названием «Безумный самолёт»,<br /><br />Говорит высоким и тонким голосом. До 1947 года Уолт Дисней лично озвучивал Микки Мауса, из-за хронического кашля вследствие курения был вынужден прекратить озвучку. Тогда компания Дисней поручила эту работу Джимми Макдональду. С 1977 по 2009 Микки Мауса озвучивал Уэйн Оллвайн.<br /><br />Часто появляется в компании друга Дональда и собаки по кличке Плуто. Кроме того, у Микки есть подружка по имени Минни. Микки Маус фигурирует в мультфильмах, комиксах, видеоиграх и развлекательных парках.<br /><br />Микки Маус появился в 1928 году, после того как Уолт Дисней потерял права на своего первого персонажа, удачливого кролика Освальда. Первые короткие анимационные фильмы с Микки Маусом были нарисованы Абом Айверксом, главным компаньоном Уолта Диснея. Впоследствии, с ростом популярности, Микки Маус начал фигурировать в полнометражных мультфильмах, на телевидении, комиксах и различных предметах.  </div>', '1234567890', '1234567890', 'mickey.mouse', '123456789', 'mickey@mouse.disney/all', 'www.disney.ru/mickey/'),
	(19, 'Чип и Дейл  Диснеевич', 'Чип и Дейл  Диснеевич', '', '', '', 62, 1, 1, 0, '2012-03-04 16:33:18', '0000-00-00 00:00:00', '2012-03-04 16:33:18', '0000-00-00 00:00:00', 0, 1, 0, 'Чип и Дейл', '', 'Диснеевич', '1943-04-02', 'Chip.n.Dale@disney.com', '1', '<div style="text-align:justify">Чип и Дейл (англ. Chip \'n\' Dale) — анимационные персонажи ряда мультфильмов и мультсериалов, созданные американской компанией «Disney». Наибольшую известность они приобрели благодаря мультсериалу «Чип и Дейл спешат на помощь» (англ. «Chip \'n Dale Rescue Rangers»), снятому в 1989—1990 годах.<br /><br />Чип и Дейл — это бурундуки, живущие на дереве. Чип — трудолюбивый и вспыльчивый герой. Он постоянно получает увечья и прочее. Дейл глуповат, но весьма отважен. От Чипа его отличает коричневато-красный нос и раставленные в разные стороны зубы.<br />[править] История<br /><br />Впервые Чип и Дейл появились в мультфильме «Рядовой Плуто» (англ. «Private Pluto») 2 апреля 1943 года. Изначально они не имели отличительных черт. До 1956 года было снято 24 мультфильма с участием Чипа и Дейла. В 1983 году Чип и Дейл появились в эпизодических ролях в полнометражном мультфильме «Mickey’s Christmas Carol».  </div>', '1234567890', '1234567890', 'Chip.n.Dale', '123456789', 'Chip.n.Dale@disney.com', 'cdrrhq.ru/'),
	(20, 'Дональд Фаунтлерой  Дак', 'Дональд Фаунтлерой  Дак', '', '', '', 62, 1, 1, 0, '2012-03-04 16:37:40', '0000-00-00 00:00:00', '2012-03-04 16:37:40', '0000-00-00 00:00:00', 2, 1, 0, 'Дональд', 'Фаунтлерой', 'Дак', '1939-06-09', 'dfd@disney.com', '1', '<div style="text-align:justify">Дональд Фаунтлерой Дак&nbsp; (англ. Donald Fauntleroy Duck) — герой мультфильмов студии Walt Disney. Дональд — белая антропоморфная утка с жёлтым клювом.<br /><br />Официально день рождения Дональда — 9 июня 1934 года, день, когда вышла короткометражка «Маленькая умная курочка» («The Wise Little Hen»). Однако в короткометражке Donald\'s Happy Birthday его день рождения — 13 марта. У Дональда есть сестра — Делла Тельма Дак (англ. Della Thelma Duck), также известная под прозвищем Дамбелла (англ. Dumbella, от Dumb «тупой»)[2].<br /><br />Голосом Дональда, одним из самых узнаваемых, до 1985 года был Кларенс Нэш.<br /><br />В августе 2004 года Дональд Дак получил звезду на аллее славы в Голливуде.  </div>', '1234567890', '1234567890', 'Donald.Fauntleroy.Duck', '123456789', 'dfd@disney.com', 'crossflow.ru/characters/donald-duck-bolshoj-chudak-2009'),
	(21, 'Крокодил Гена Качановович', 'Крокодил Гена Качановович', '', '', '', 62, 1, 1, 0, '2012-03-04 16:43:58', '0000-00-00 00:00:00', '2012-03-04 16:43:58', '0000-00-00 00:00:00', 0, 1, 0, 'Крокодил', 'Гена', 'Качановович', '1969-03-04', '', '1', '<div style="text-align:justify">«Крокодил Гена» — кукольный мультипликационный фильм Романа Качанова, выпущенный студией «Союзмультфильм» в 1969 году.<br /><br />Фильм снят по мотивам книги Эдуарда Успенского «Крокодил Гена и его друзья». В этом фильме впервые появились широко известные до сих пор анимационные образы — Крокодил Гена, Чебурашка и Шапокляк.<br /><br />Крокодил Гена работает в зоопарке — крокодилом. Каждый день вечером он возвращается домой в свою одинокую квартиру. Наконец ему надоедает играть самому с собой в шахматы и Гена решает завести себе друзей. На объявления расклеенные по городу откликаются звери и люди. Первой приходит девочка Галя с бездомным щенком, а вслед за ней Чебурашка…</div>', '1234567890', '1234567890', '', '', '', 'www.imdb.com/title/tt0146970/'),
	(22, 'Вовкин Вовка Вовкович', 'Вовкин Вовка Вовкович', '', '', '', 62, 1, 1, 0, '2012-03-04 16:46:14', '0000-00-00 00:00:00', '2012-03-04 16:46:14', '0000-00-00 00:00:00', 3, 1, 0, 'Вовкин', 'Вовка', 'Вовкович', '1965-03-04', '', '1', '<div style="text-align:justify">Школьник Вовка мечтал о сказочной жизни, ведь в сказках всё делается по щучьему веленью. С помощью советов из справочника «Сделай сам» библиотекарша создаёт нарисованного мальчика — копию Вовки — и отправляет его в Тридевятое царство, существующее в книге сказок.</div>', '', '', '', '', '', 'www.animator.ru/db/?p=show_film&fid=2160'),
	(23, 'Успенская Чебурашка  Эдуардовна', 'Успенская Чебурашка  Эдуардовна', '', '', '', 62, 1, 1, 1, '2012-03-04 16:48:04', '0000-00-00 00:00:00', '2012-03-04 16:48:04', '0000-00-00 00:00:00', 0, 1, 0, 'Успенская', 'Чебурашка', 'Эдуардовна', '1969-08-20', '', '1', '<div style="text-align:justify">Чебурашка — персонаж книги Эдуарда Успенского «Крокодил Гена и его друзья» и фильма Романа Качанова «Крокодил Гена», снятого по этой книге в 1969 году. Широкую известность получил после выхода этого фильма на экраны.<br /><br />Внешне представляет собой существо с огромными ушами, большими глазами и коричневой шерстью, ходящее на задних лапах. Известный сегодня образ Чебурашки впервые появился в мультфильме Романа Качанова «Крокодил Гена» (1969) и был создан при непосредственном участии художника-постановщика фильма Леонида Шварцмана.<br /><br />После выхода фильма на английский язык первоначально переводился как «Topple», а на шведский как «Drutten».</div>', '', '', '', '', '', 'www.chebuday.ru/'),
	(24, 'Путин Владимир Владимирович', 'Путин Владимир Владимирович', '', '', '', 62, 1, 1, 1, '2012-03-04 16:51:14', '0000-00-00 00:00:00', '2012-03-04 16:51:14', '0000-00-00 00:00:00', 0, 1, 0, 'Путин', 'Владимир', 'Владимирович', '1952-10-07', '', '1', '<div style="text-align:justify">Владимир Владимирович Путин (род. 7 октября 1952, Ленинград) — российский государственный и политический деятель, с 16 августа по 31 декабря 1999 года и с 8 мая 2008 года — председатель Правительства Российской Федерации. Второй президент Российской Федерации с 7 мая 2000 года по 7 мая 2008 года (после отставки президента Бориса Ельцина исполнял его обязанности с 31 декабря 1999 по 7 мая 2000 года). Имеет юридическое образование. Кандидат экономических наук. На выборах в Государственную думу 2007 года Путин возглавил избирательный список политической партии «Единая Россия», оставаясь беспартийным.[3] Председатель политической партии «Единая Россия» с 7 мая 2008 года[4]. 24 сентября 2011 года по предложению третьего президента России Дмитрия Медведева стал кандидатом от «Единой России» на президентских выборах 2012 года.  </div>', '', '', '', '', '', 'premier.gov.ru/'),
	(25, 'Медведев Дмитрий Анатольевич', 'Медведев Дмитрий Анатольевич', '', '', '', 62, 1, 1, 0, '2012-03-04 17:14:54', '0000-00-00 00:00:00', '2012-03-04 17:14:54', '0000-00-00 00:00:00', 3, 1, 0, 'Медведев', 'Дмитрий', 'Анатольевич', '1965-09-14', '', '1', '<div style="text-align:justify">Дмитрий Анатольевич Медведев (род. 14 сентября 1965, Ленинград) — российский государственный и политический деятель, третий Президент Российской Федерации, избранный на выборах 2 марта 2008 года. Верховный Главнокомандующий Вооружёнными Силами Российской Федерации и Председатель Совета Безопасности Российской Федерации. По образованию юрист, кандидат юридических наук.<br /><br />В 2000—2001, 2002—2008 гг. — председатель совета директоров ОАО «Газпром». C 14 ноября 2005 года по 7 мая 2008 года — первый заместитель Председателя Правительства Российской Федерации, куратор национальных проектов.<br /><br />10 декабря 2007 года было объявлено о том, что его кандидатура на президентские выборы 2008 года предложена партиями «Единая Россия», «Справедливая Россия», «Гражданская сила», «Аграрная партия России» и поддержана Владимиром Путиным, Президентом Российской Федерации на тот момент[1]. 2 марта 2008 года, официально набрав 70,28 % (52 530 712) голосов принявших в выборах избирателей, избран Президентом Российской Федерации[2]. 7 мая 2008 года вступил в должность Президента Российской Федерации, став самым молодым президентом в истории России (42 года и 8 месяцев). 24 сентября 2011 года возглавил предвыборный список «Единой России» на выборах в Госдуму и предложил в 2012 году поддержать Владимира Путина на выборах Президента России</div>', '', '', '', '', '', 'kremlin.ru/'),
	(26, 'Ульцин Борис Николаевич', 'Ульцин Борис Николаевич', '', '', '', 62, 1, 1, 0, '2012-03-04 17:27:19', '0000-00-00 00:00:00', '2012-03-04 17:27:19', '0000-00-00 00:00:00', 1, 1, 0, 'Ульцин', 'Борис', 'Николаевич', '1931-02-01', '', '1', '<div style="text-align:justify">Борис Николаевич Ельцин (1 февраля 1931 года, село Бутка, Буткинский район, Уральская область, РСФСР, СССР — 23 апреля 2007 года, Москва, Россия) — советский партийный и российский политический и государственный деятель, первый Президент Российской Федерации. Избирался Президентом два раза — 12 июня 1991 года и 3 июля 1996 года, занимал эту должность с 10 июля 1991 года по 31 декабря 1999 года[2].<br /><br />Вошёл в историю как первый всенародно избранный глава государства, один из организаторов сопротивления действиям ГКЧП, радикальный реформатор общественно-политического и экономического устройства России.  </div>', '', '', '', '', '', 'ельцин.рф'),
	(27, 'Нобель Альфред Бернхард', 'Нобель Альфред Бернхард', '', '', '', 62, 1, 1, 1, '2012-03-04 17:31:18', '0000-00-00 00:00:00', '2012-03-04 17:31:18', '0000-00-00 00:00:00', 0, 1, 0, 'Нобель', 'Альфред', 'Бернхард', '1933-10-21', '', '1', '<div style="text-align:justify">Альфред Бернхард Нобель (швед. Alfred Bernhard Nobel (инф.); 21 октября 1833, Стокгольм, Шведско-норвежская уния — 10 декабря 1896, Сан-Ремо, Королевство Италия) — шведский химик, инженер, изобретатель динамита.<br /><br />Завещал своё огромное состояние на учреждение Нобелевской премии. В его честь назван синтезированный химический элемент нобелий. В честь Нобеля назван Нобелевский физико-химический институт в Стокгольме  </div>', '', '', '', '', '', ''),
	(28, 'Склодовская-Кюри Мария ', 'Склодовская-Кюри Мария ', '', '', '', 62, 1, 1, 0, '2012-03-04 17:34:56', '0000-00-00 00:00:00', '2012-03-04 17:34:56', '0000-00-00 00:00:00', 0, 1, 0, 'Склодовская-Кюри', 'Мария', '', '', '', '1', '<div style="text-align:justify">Мария Склодовская-Кюри (фр. Marie Curie, польск. Maria Sklodowska-Curie; урождённая Мария Саломея Склодовская, польск. Maria Salomea Sklodowska; 7 ноября 1867 года, Варшава, Царство Польское, Российская империя — 4 июля 1934 года, близ Санселльмоза, Франция) — польско-французский учёный-экспериментатор (физик, химик), педагог, общественный деятель. Дважды лауреат Нобелевской премии: по физике (1903) и химии (1911)[1]. Основала институты Кюри в Париже и в Варшаве. Жена Пьера Кюри, вместе с ним занималась исследованием радиоактивности. Совместно с мужем открыла элементы радий (от лат. radiare «излучать») и полоний (от латинского названия Польши Polonia, — дань уважения родине Марии Склодовской).  </div>', '', '', '', '', '', ''),
	(29, 'Сенгер  Фредерик  ', 'Сенгер  Фредерик  ', '', '', '', 62, 1, 1, 0, '2012-03-04 17:41:58', '0000-00-00 00:00:00', '2012-03-04 17:41:58', '0000-00-00 00:00:00', 0, 1, 0, 'Сенгер', 'Фредерик', '', '1913-08-13', '', '1', '<div style="text-align:justify">Фредерик Сенгер (англ. Frederick Sanger; род. 13 августа 1918, Рендкомб, Глостершир) — английский биохимик, единственный ученый — дважды лауреат Нобелевской премии по химии — в 1958 и 1980 (совместно с У. Гилбертом и П. Бергом). Женат, имеет двух сыновей и дочь.<br /><br />Окончил Кембриджский университет (1939). С 1940 года работает там же. Докторская степень с 1943. C 1951 член Медицинского исследовательского совета и руководитель лаборатории молекулярной биологии этого совета. С 1983 в отставке.<br /><br />Основные работы посвящены химии белка и нуклеиновых кислот. С 1945 года Сенгер изучал структуру инсулина. Им был разработан динитро-фторбензольный метод идентификации концевых аминогрупп в пептидах, с помощью которого ему удалось установить природу и последовательность чередования аминогрупп в инсулине и расшифровать его строение (1944—1954). Сенгер установил, что инсулин имеет общую формулу C337N65O75S6, три сульфидных мостика и состоит из двух цепей: цепи A, содержащей 21 аминокислотный остаток, и цепи B, содержащей 30 аминокислотных остатков. Эти работы послужили основой для синтетического получения инсулина и других гормонов.<br /><br />В 1965 году предложил метить РНК и ДНК, предназначенные для структурных исследований, радиоактивным изотопом фосфора 32Р, что позволило осуществлять работы с чрезвычайно малым количеством материала — 10?6 г. Установил структуру 5S PHK (120 оснований; 1967) и ДНК фага ФХ174 (5375 оснований; 1977). В 1977 предложил метод расшифровки первичной структуры ДНК, основанный на ферментативном синтезе высокорадиоактивной комплементарной последовательности ДНК на изучаемой однонитчатой ДНК как на матрице.  </div>', '', '', '', '', '', '');
/*!40000 ALTER TABLE `jos_boss_7_contents` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_content_category_href
DROP TABLE IF EXISTS `jos_boss_7_content_category_href`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_content_category_href` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='Привязка контента к категориям';

-- Dumping data for table joostina14.jos_boss_7_content_category_href: 12 rows
DELETE FROM `jos_boss_7_content_category_href`;
/*!40000 ALTER TABLE `jos_boss_7_content_category_href` DISABLE KEYS */;
INSERT INTO `jos_boss_7_content_category_href` (`id`, `category_id`, `content_id`) VALUES
	(62, 5, 19),
	(63, 5, 20),
	(64, 6, 21),
	(60, 5, 18),
	(65, 6, 22),
	(66, 6, 23),
	(67, 7, 24),
	(73, 7, 25),
	(75, 7, 26),
	(76, 8, 27),
	(77, 8, 28),
	(78, 8, 29);
/*!40000 ALTER TABLE `jos_boss_7_content_category_href` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_content_types
DROP TABLE IF EXISTS `jos_boss_7_content_types`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `fields` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_7_content_types: 1 rows
DELETE FROM `jos_boss_7_content_types`;
/*!40000 ALTER TABLE `jos_boss_7_content_types` DISABLE KEYS */;
INSERT INTO `jos_boss_7_content_types` (`id`, `name`, `desc`, `fields`, `published`, `ordering`) VALUES
	(1, 'Основные контакты', '', 0, 1, 1);
/*!40000 ALTER TABLE `jos_boss_7_content_types` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_fields
DROP TABLE IF EXISTS `jos_boss_7_fields`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_fields` (
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

-- Dumping data for table joostina14.jos_boss_7_fields: 13 rows
DELETE FROM `jos_boss_7_fields`;
/*!40000 ALTER TABLE `jos_boss_7_fields` DISABLE KEYS */;
INSERT INTO `jos_boss_7_fields` (`fieldid`, `name`, `title`, `display_title`, `description`, `type`, `text_before`, `text_after`, `tags_open`, `tags_separator`, `tags_close`, `maxlength`, `size`, `required`, `link_text`, `link_image`, `ordering`, `cols`, `rows`, `profile`, `editable`, `searchable`, `sort`, `sort_direction`, `catsid`, `published`, `filter`) VALUES
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
/*!40000 ALTER TABLE `jos_boss_7_fields` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_field_values
DROP TABLE IF EXISTS `jos_boss_7_field_values`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_field_values` (
  `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(50) NOT NULL DEFAULT '',
  `fieldvalue` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldvalueid`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_7_field_values: 13 rows
DELETE FROM `jos_boss_7_field_values`;
/*!40000 ALTER TABLE `jos_boss_7_field_values` DISABLE KEYS */;
INSERT INTO `jos_boss_7_field_values` (`fieldvalueid`, `fieldid`, `fieldtitle`, `fieldvalue`, `ordering`, `sys`) VALUES
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
/*!40000 ALTER TABLE `jos_boss_7_field_values` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_groupfields
DROP TABLE IF EXISTS `jos_boss_7_groupfields`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_groupfields` (
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`,`groupid`),
  KEY `template` (`template`),
  KEY `type_tmpl` (`type_tmpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_7_groupfields: 19 rows
DELETE FROM `jos_boss_7_groupfields`;
/*!40000 ALTER TABLE `jos_boss_7_groupfields` DISABLE KEYS */;
INSERT INTO `jos_boss_7_groupfields` (`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) VALUES
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
/*!40000 ALTER TABLE `jos_boss_7_groupfields` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_groups
DROP TABLE IF EXISTS `jos_boss_7_groups`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `desc` varchar(20) DEFAULT NULL,
  `template` varchar(20) DEFAULT NULL,
  `type_tmpl` varchar(20) DEFAULT NULL,
  `catsid` varchar(255) NOT NULL DEFAULT ',-1,',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_7_groups: 5 rows
DELETE FROM `jos_boss_7_groups`;
/*!40000 ALTER TABLE `jos_boss_7_groups` DISABLE KEYS */;
INSERT INTO `jos_boss_7_groups` (`id`, `name`, `desc`, `template`, `type_tmpl`, `catsid`, `published`) VALUES
	(1, 'catSubtitle', 'catSubtitle', 'contact', 'category', ',-1,', 1),
	(2, 'catImage', 'catImage', 'contact', 'category', ',-1,', 1),
	(3, 'conSubtitle', 'conSubtitle', 'contact', 'content', ',-1,', 1),
	(4, 'conDescription', 'conDescription', 'contact', 'content', ',-1,', 1),
	(5, 'conImage', 'conImage', 'contact', 'content', ',-1,', 1);
/*!40000 ALTER TABLE `jos_boss_7_groups` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_profile
DROP TABLE IF EXISTS `jos_boss_7_profile`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_profile` (
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_7_profile: 0 rows
DELETE FROM `jos_boss_7_profile`;
/*!40000 ALTER TABLE `jos_boss_7_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_boss_7_profile` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_rating
DROP TABLE IF EXISTS `jos_boss_7_rating`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_rating` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contentid` int(10) DEFAULT '0',
  `userid` int(10) DEFAULT '0',
  `value` tinyint(1) DEFAULT '5',
  `ip` int(11) DEFAULT '0',
  `date` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_7_rating: 1 rows
DELETE FROM `jos_boss_7_rating`;
/*!40000 ALTER TABLE `jos_boss_7_rating` DISABLE KEYS */;
INSERT INTO `jos_boss_7_rating` (`id`, `contentid`, `userid`, `value`, `ip`, `date`) VALUES
	(1, 22, 62, 5, 2130706433, 1338206521);
/*!40000 ALTER TABLE `jos_boss_7_rating` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_boss_7_reviews
DROP TABLE IF EXISTS `jos_boss_7_reviews`;
CREATE TABLE IF NOT EXISTS `jos_boss_7_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contentid` int(10) unsigned DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_7_reviews: 0 rows
DELETE FROM `jos_boss_7_reviews`;
/*!40000 ALTER TABLE `jos_boss_7_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `jos_boss_7_reviews` ENABLE KEYS */;


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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_boss_config: 3 rows
DELETE FROM `jos_boss_config`;
/*!40000 ALTER TABLE `jos_boss_config` DISABLE KEYS */;
INSERT INTO `jos_boss_config` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `default_order_by`, `contents_per_page`, `root_allowed`, `show_contact`, `send_email_on_new`, `send_email_on_update`, `auto_publish`, `fronttext`, `email_display`, `display_fullname`, `rules_text`, `expiration`, `content_duration`, `recall`, `recall_time`, `recall_text`, `empty_cat`, `cat_max_width`, `cat_max_height`, `cat_max_width_t`, `cat_max_height_t`, `submission_type`, `nb_contents_by_user`, `allow_attachement`, `allow_contact_by_pms`, `allow_comments`, `rating`, `secure_comment`, `comment_sys`, `allow_unregisered_comment`, `allow_ratings`, `secure_new_content`, `use_content_mambot`, `show_rss`, `filter`, `template`, `allow_rights`, `rights`) VALUES
	(6, 'Файловый архив', 'files', '', '', '', '0', 5, 0, 2, 0, 0, 1, '<br />', 0, 0, 'Это правила... // /', 0, 30, 1, 7, '<br /> ', 0, 250, 250, 80, 80, 0, -1, 0, 0, 1, 'GDRating', 1, 1, 0, 1, 0, 1, 1, '0', 'files', '1', 'edit_category=23,24,25*edit_content=23,24,25*edit_directories=23,24,25*edit_conf=23,24,25*edit_types=23,24,25*edit_fields=23,24,25*edit_fieldimages=23,24,25*edit_templates=23,24,25*edit_plugins=23,24,25*import_export=23,24,25*edit_users=23,24,25*show_user_content=0,18,19,20,21,23,24,25*show_all=0,18,19,20,21,23,24,25*show_search=0,18,19,20,21,23,24,25*show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(5, 'Основной', 'content', '', '', '', '0', 5, 0, 2, 0, 0, 1, ' ', 0, 0, ' ', 0, 30, 1, 7, ' ', 1, 150, 150, 30, 30, 0, -1, 0, 0, 1, 'GDRating', 0, 1, 1, 1, 1, 1, 1, '0', 'default', '1', 'edit_category=23,24,25*edit_content=20,21,23,24,25*edit_directories=23,24,25*edit_conf=23,24,25*edit_types=23,24,25*edit_fields=23,24,25*edit_fieldimages=23,24,25*edit_templates=23,24,25*edit_plugins=23,24,25*import_export=23,24,25*edit_users=24,25*show_user_content=0,18,19,20,21,23,24,25*show_all=0,18,19,20,21,23,24,25*show_search=0,18,19,20,21,23,24,25*show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=20,21,23,24,25*edit_all_content=21,23,24,25*delete_user_content=20,21,23,24,25*delete_all_content=21,23,24,25*'),
	(7, 'Контакты', 'contact', '', '', '', '1', 5, 0, 1, 0, 0, 1, 'Текст приветствия ', 0, 0, 'Это правила... ', 0, 30, 1, 7, ' ', 1, 150, 150, 80, 80, 0, -1, 0, 0, 1, 'GDRating', 1, 1, 0, 1, 0, 1, 0, '0', 'contact', '1', 'edit_category=23,24,25*edit_content=23,24,25*edit_directories=23,24,25*edit_conf=23,24,25*edit_types=23,24,25*edit_fields=23,24,25*edit_fieldimages=23,24,25*edit_templates=23,24,25*edit_plugins=23,24,25*import_export=23,24,25*edit_users=23,24,25*show_user_content=0,18,19,20,21,23,24,25*show_all=0,18,19,20,21,23,24,25*show_search=0,18,19,20,21,23,24,25*show_all_content=0,18,19,20,21,23,24,25*show_category=0,18,19,20,21,23,24,25*show_my_content=0,18,19,20,21,23,24,25*show_category_content=0,18,19,20,21,23,24,25*create_content=19,20,21,23,24,25*edit_user_content=19,20,21,23,24,25*edit_all_content=23,24,25*delete_user_content=19,20,21,23,24,25*delete_all_content=23,24,25*');
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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_content_tags: 9 rows
DELETE FROM `jos_content_tags`;
/*!40000 ALTER TABLE `jos_content_tags` DISABLE KEYS */;
INSERT INTO `jos_content_tags` (`id`, `obj_id`, `obj_type`, `tag`) VALUES
	(1, 1, 'com_boss_1', 'Первая статья'),
	(2, 5, 'com_boss_1', 'Первая статья'),
	(3, 6, 'com_boss_1', 'Первая статья'),
	(6, 5, 'com_boss_5', 'йцукку werwe'),
	(7, 7, 'com_boss_5', 'йцукку werwe'),
	(8, 13, 'com_boss_5', 'лотос'),
	(9, 12, 'com_boss_5', 'лотос'),
	(10, 11, 'com_boss_5', 'лотос'),
	(12, 16, 'com_boss_5', 'лотос');
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
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_menu: 33 rows
DELETE FROM `jos_menu`;
/*!40000 ALTER TABLE `jos_menu` DISABLE KEYS */;
INSERT INTO `jos_menu` (`id`, `menutype`, `name`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`) VALUES
	(1, 'mainmenu', 'Главная', 'index.php?option=com_frontpage', 'components', 1, 0, 10, 0, 12, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\npage_name=\nno_site_name=0\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=\nmenu_image=-1\npageclass_sfx=\nheader=Добро пожаловать на главную страницу\npage_title=0\nback_button=0\nleading=2\nintro=2\ncolumns=1\nlink=0\norderby_pri=\norderby_sec=front\npagination=2\npagination_results=0\nimage=1\nsection=0\nsection_link=0\nsection_link_type=blog\ncategory=1\ncategory_link=0\ncat_link_type=blog\nitem_title=1\nlink_titles=1\nintro_only=1\nview_introtext=1\nintrotext_limit=\nview_tags=1\nreadmore=0\nrating=0\nauthor=1\nauthor_name=0\ncreatedate=1\nmodifydate=0\nhits=\nprint=0\nemail=0\nunpublished=0'),
	(2, 'mainmenu', 'Босс', 'index.php?option=com_boss', 'components', -2, 0, 26, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=boss\ndirectory=1\ntask=\ncatid='),
	(4, 'mainmenu', 'Контакты', 'index.php?option=com_contact', 'components', -2, 0, 7, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(5, 'mainmenu', 'Ссылки', 'index.php?option=com_weblinks', 'components', -2, 0, 4, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=web_links.jpg\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nheadings=1\nhits=\nitem_description=1\nother_cat=1\ndescription=1\ndescription_text=\nimage=-1\nimage_align=right\nweblink_icons='),
	(6, 'mainmenu', 'Поиск', 'index.php?option=com_search', 'components', -2, 0, 16, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(7, 'mainmenu', 'Ленты новостей', 'index.php?option=com_newsfeeds', 'components', -2, 0, 12, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nother_cat_section=1\nother_cat=1\ncat_description=1\ncat_items=1\ndescription=0\ndescription_text=\nimage=-1\nimage_align=right\nheadings=1\nname=1\narticles=1\nlink=0\nfeed_image=1\nfeed_descr=1\nitem_descr=1\nword_count=0'),
	(8, 'mainmenu', 'В окне', 'index.php?option=com_wrapper', 'wrapper', -2, 0, 0, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\npage_name=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nscrolling=auto\nwidth=300\nheight=600\nheight_auto=1\nadd=1\nurl=www.joostina.ru'),
	(9, 'othermenu', 'joostina.ru', 'http://www.joostina.ru', 'url', -2, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(10, 'othermenu', 'joom.ru', 'http://www.joom.ru', 'url', -2, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(11, 'othermenu', 'joomlaportal.ru', 'http://www.joomlaportal.ru', 'url', -2, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(12, 'othermenu', 'joomlaforum.ru', 'http://www.joomlaforum.ru', 'url', -2, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(13, 'othermenu', 'joomla-support.ru', 'http://www.joomla-support.ru', 'url', -2, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(14, 'othermenu', 'joomla.ru', 'http://www.joomla.ru', 'url', -2, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, ''),
	(15, 'usermenu', 'Панель управления', 'administrator/', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1'),
	(16, 'usermenu', 'Добавить ссылку', 'index.php?option=com_weblinks&task=new', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, ''),
	(17, 'usermenu', 'Разблокировать содержимое', 'index.php?option=com_users&task=CheckIn', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, ''),
	(18, 'mainmenu', 'Карта сайта', 'index.php?option=com_xmap', 'components', -2, 0, 24, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, ''),
	(20, 'mainmenu', 'Опросы', 'index.php?option=com_poll', 'components', -2, 0, 11, 0, 11, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader='),
	(24, 'mainmenu', 'www', 'index.php?option=com_boss', 'components', -2, 4, 20, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title='),
	(25, 'mainmenu', 'fgdg', 'index.php?option=com_boss', 'components', -2, 24, 20, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title='),
	(26, 'mainmenu', 'ййй', 'index.php?option=com_boss&task=show_category&catid=1&directory=2', 'boss_category_content', -2, 0, 0, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(27, 'mainmenu', 'ййц', 'index.php?option=com_boss&task=show_all&directory=4', 'boss_all_content', -2, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(28, 'mainmenu', 'Новости', 'index.php?option=com_boss&task=show_category&catid=1&directory=5', 'boss_category_content', 1, 0, 0, 0, 13, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(29, 'mainmenu', 'Статьи', 'index.php?option=com_boss&task=show_category&catid=2&directory=5', 'boss_category_content', 1, 0, 0, 0, 14, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(30, 'mainmenu', 'Файловый архив', 'index.php?option=com_boss&task=show_all&directory=5', 'boss_all_content', -2, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(31, 'mainmenu', 'Файловый архив', 'index.php?option=com_boss&task=show_all&directory=6', 'boss_all_content', 1, 0, 0, 0, 15, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(32, 'mainmenu', 'Контакты', 'index.php?option=com_boss&task=show_all&directory=7', 'boss_all_content', 1, 0, 0, 0, 16, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(33, 'othermenu', 'Сайт поддержки', 'http://joostina-cms/', 'url', 1, 0, 0, 0, 7, 0, '0000-00-00 00:00:00', 0, 1, 0, 0, 'title=\nmenu_image='),
	(34, 'othermenu', 'Форум поддержки', 'http://joostina-cms/index.php?option=comfireboard', 'url', 1, 0, 0, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(35, 'othermenu', 'Wiki-Справка', 'http://wiki.joostina-cms/', 'url', 1, 0, 0, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image='),
	(36, 'topmenu', 'Главная', 'index.php?option=com_frontpage', 'components', 1, 0, 10, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\npage_name=\nno_site_name=0\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=\nmenu_image=\npageclass_sfx=\nheader=\npage_title=1\nback_button=0\nleading=1\nintro=4\ncolumns=2\nlink=4\norderby_pri=\norderby_sec=front\npagination=2\npagination_results=1\nimage=1\nsection=0\nsection_link=0\nsection_link_type=blog\ncategory=0\ncategory_link=0\ncat_link_type=blog\nitem_title=1\nlink_titles=\nintro_only=1\nview_introtext=1\nintrotext_limit=\nreadmore=\nrating=\nauthor=\nauthor_name=0\ncreatedate=\nmodifydate=\nview_tags=\nhits=\nprint=\nemail=\nunpublished=0'),
	(37, 'topmenu', 'Карта сайта', 'index.php?option=com_xmap', 'components', 1, 0, 19, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title='),
	(38, 'topmenu', 'Почта', 'info@joostina-cms.ru', 'url', 1, 0, 0, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image=');
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
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_modules: 37 rows
DELETE FROM `jos_modules`;
/*!40000 ALTER TABLE `jos_modules` DISABLE KEYS */;
INSERT INTO `jos_modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES
	(1, 'Ваше мнение', '', 4, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_poll', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-poll\ndef_itemid=0', 0, 0),
	(2, 'Меню пользователя', '', 1, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 1, 1, 'moduleclass_sfx=-new2\nclass_sfx=\nmenutype=usermenu\nmenu_style=ulli\nml_imaged=0\nml_module_number=1\nnumrow=Все\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=aload.gif\nindent_image2=aload.gif\nindent_image3=aload.gif\nindent_image4=aload.gif\nindent_image5=aload.gif\nindent_image6=aload.gif\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=\nml_image2=\nml_image3=\nml_image4=\nml_image5=\nml_image6=-1\nml_image7=-1\nml_image8=-1\nml_image9=-1\nml_image10=-1\nml_image11=-1\nml_image_roll_1=\nml_image_roll_2=\nml_image_roll_3=\nml_image_roll_4=\nml_image_roll_5=\nml_image_roll_6=\nml_image_roll_7=\nml_image_roll_8=\nml_image_roll_9=\nml_image_roll_10=\nml_image_roll_11=\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 1, 0),
	(3, 'Главное меню', '', 1, 'menu1', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-menu1\nclass_sfx=\nmenutype=mainmenu\nmenu_style=linksonly\nml_imaged=0\nml_module_number=1\nnumrow=10\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=-1\nml_image2=-1\nml_image3=-1\nml_image4=-1\nml_image5=-1\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=-1\nml_image_roll_2=-1\nml_image_roll_3=-1\nml_image_roll_4=-1\nml_image_roll_5=-1\nml_image_roll_6=-1\nml_image_roll_7=-1\nml_image_roll_8=-1\nml_image_roll_9=-1\nml_image_roll_10=-1\nml_image_roll_11=-1\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 1, 0),
	(4, 'Авторизация', '', 2, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_ml_login', 0, 0, 0, 'moduleclass_sfx=\ntemplate=popup.php\ntemplate_dir=1\ndr_login_text=Вход / Регистрация\nml_avatar=0\npretext=\nposttext=\nlogin=\nlogin_message=0\ngreeting=1\nuser_name=0\nprofile_link=0\nprofile_link_text=Личный кабинет\nlogout=\nlogout_message=0\nshow_login_text=1\nml_login_text=Логин\nshow_pass_text=1\nml_pass_text=\nshow_remember=0\nml_rem_text=\nshow_lost_pass=1\nml_rem_pass_text=\nshow_register=1\nml_reg_text=\nsubmit_button_text=', 1, 0),
	(5, 'Экспорт новостей', '', 3, 'bottom', 0, '0000-00-00 00:00:00', 1, 'mod_rssfeed', 0, 0, 0, 'cache_time=0\nmoduleclass_sfx=\ntext=\nyandex=0\nrss091=0\nrss10=0\nrss20=1\natom=0\nopml=0\nrss091_image=-1\nrss10_image=-1\nrss20_image=rss-new.png\natom_image=-1\nopml_image=-1\nyandex_image=-1', 1, 0),
	(46, 'Фото', NULL, 2, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-foto\ntemplate=foto\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=0\ncount_special=0\ncount_basic=3\ncolumns=1\ncount_reference=0\nshow_front=1\norderby=rand\ntime=30\nimage=1\nimage_link=1\nimage_default=1\nimage_prev=width\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=150\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_limit=20\ncrop_text_format=0\nshow_date=1\ndate_format=%d-%m-%Y %H:%M\nshow_author=4\nreadmore=1\nlink_text=\nhits=1', 0, 0),
	(7, 'Статистика', '', 3, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_stats', 0, 0, 0, 'cache=1\nserverinfo=1\nsiteinfo=0\ncounter=0\nincrease=0\nmoduleclass_sfx=-stat', 0, 0),
	(8, 'Пользователи', '', 3, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_whosonline', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-new2\nmodule_orientation=1\nall_user=1\nonline_user_count=1\nonline_users=1\nuser_avatar=1', 0, 0),
	(49, 'Помощь on-line', '', 2, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 1, 1, 'moduleclass_sfx=-new2\nclass_sfx=\nmenutype=othermenu\nmenu_style=ulli\nml_imaged=0\nml_module_number=6\nnumrow=Все\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=aload.gif\nindent_image2=aload.gif\nindent_image3=aload.gif\nindent_image4=aload.gif\nindent_image5=aload.gif\nindent_image6=aload.gif\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=\nml_image2=\nml_image3=\nml_image4=\nml_image5=\nml_image6=-1\nml_image7=-1\nml_image8=-1\nml_image9=-1\nml_image10=-1\nml_image11=-1\nml_image_roll_1=\nml_image_roll_2=\nml_image_roll_3=\nml_image_roll_4=\nml_image_roll_5=\nml_image_roll_6=\nml_image_roll_7=\nml_image_roll_8=\nml_image_roll_9=\nml_image_roll_10=\nml_image_roll_11=\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0),
	(10, 'Выбор шаблона', '', 4, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_templatechooser', 0, 0, 1, 'show_preview=1', 0, 0),
	(14, 'Взаимосвязанные элементы', '', 1, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_related_items', 0, 0, 1, 'cache_time=0\nmoduleclass_sfx=-new1\nlimit=5', 0, 0),
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
	(50, 'Новое в архиве', NULL, 1, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_gdnlotos', 0, 0, 1, 'moduleclass_sfx=-text\ntemplate=text\ncatid=\ncount_special=0\ncount_basic=5\ncolumns=1\ncount_reference=0\ntime=0\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=80\nimage_quality_b=75\ncrop_text_limit=20\ndate_format=%d-%m-%Y %H:%M\nlink_text=\ncache=0\ncache_time=0\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=6\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=6-content_smalldes\nshow_front=1\norderby=rdate\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_format=0\nshow_date=1\nshow_author=4\nreadmore=1\nhits=1', 0, 0),
	(51, 'Горячие новости', NULL, 1, 'user3', 0, '0000-00-00 00:00:00', 1, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-default\ntemplate=default\ntemplate_dir=0\nmodul_link=0\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=0\ndirectory_link=1\ncategory_name=0\ncategory_link=1\ncontent_field=0\ncount_special=1\ncount_basic=0\ncolumns=2\ncount_reference=4\nshow_front=1\norderby=rand\ntime=0\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nimage_size_s=100\nimage_quality_s=75\nimage_size_b=80\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=2\ncrop_text=word\ncrop_text_limit=30\ncrop_text_format=0\nshow_date=0\ndate_format=%d-%m-%Y %H:%M\nshow_author=0\nreadmore=0\nlink_text=\nhits=0', 0, 0),
	(32, 'Wrapper', '', 3, 'header', 0, '0000-00-00 00:00:00', 0, 'mod_wrapper', 0, 0, 1, 'category_a=2-1', 0, 0),
	(33, 'На сайте', '', 0, 'cpanel', 0, '0000-00-00 00:00:00', 0, 'mod_logged', 0, 99, 1, '', 0, 1),
	(34, 'Случайное фото', '', 2, 'zero', 0, '0000-00-00 00:00:00', 0, 'mod_random_image', 0, 0, 1, 'rotate_type=0\ntype=jpg\nfolder=images/rotate\nlink=http://www.joostina.ru\nwidth=180\nheight=150\nmoduleclass_sfx=\nslideshow_name=jstSlideShow_1\nimg_pref=pic\ns_autoplay=1\ns_pause=2500\ns_fadeduration=500\npanel_height=55px\npanel_opacity=0.4\npanel_padding=5px\npanel_font=bold 11px Verdana', 0, 0),
	(41, 'Популярные статьи', NULL, 3, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_gdnlotos', 0, 0, 1, 'cache=0\ncache_time=0\nmoduleclass_sfx=-text\ntemplate=text\ntemplate_dir=0\nmodul_link=1\nmodul_link_cat=0\ndirectory=5\ncatid=\ndirectory_name=1\ndirectory_link=1\ncategory_name=1\ncategory_link=1\ncontent_field=0\ncount_special=0\ncount_basic=5\ncolumns=1\ncount_reference=0\nshow_front=1\norderby=rhits\ntime=0\nimage=1\nimage_link=2\nimage_default=1\nimage_prev=width\nimage_size_s=200\nimage_quality_s=75\nimage_size_b=80\nimage_quality_b=75\nitem_title=1\nlink_titles=1\ntext=1\ncrop_text=word\ncrop_text_limit=20\ncrop_text_format=0\nshow_date=1\ndate_format=%d-%m-%Y %H:%M\nshow_author=4\nreadmore=1\nlink_text=\nhits=1', 0, 0),
	(42, 'Баннеры-2', '', 1, 'banner2', 0, '0000-00-00 00:00:00', 1, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=\ncategories=1\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 0, 0),
	(43, 'Баннеры-4', '', 1, 'banner4', 0, '0000-00-00 00:00:00', 1, 'mod_banners', 0, 0, 0, 'moduleclass_sfx=\ncategories=2\nbanners=\nclients=\ncount=1\nrandom=1\ntext=0\norientation=0', 0, 0),
	(44, 'Копия Главное меню', '', 1, 'menu2', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-menu2\nclass_sfx=\nmenutype=mainmenu\nmenu_style=linksonly\nml_imaged=0\nml_module_number=1\nnumrow=10\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=-1\nml_image2=-1\nml_image3=-1\nml_image4=-1\nml_image5=-1\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=-1\nml_image_roll_2=-1\nml_image_roll_3=-1\nml_image_roll_4=-1\nml_image_roll_5=-1\nml_image_roll_6=-1\nml_image_roll_7=-1\nml_image_roll_8=-1\nml_image_roll_9=-1\nml_image_roll_10=-1\nml_image_roll_11=-1\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0),
	(21, 'BOSS - Объекты компонента', '', 1, 'banner1', 0, '0000-00-00 00:00:00', 1, 'mod_boss_admin_contents', 0, 99, 1, 'moduleclass_sfx=\ncache=0\nlimit=5\npubl=0\ndisplaycategory=1\ncontent_title=Последние добавленные объекты\ncontent_title_link=Все объекты\nsort=5\ndate_field=date_created\ndisplay_author=1\ndirectory=5\ncat_ids=', 1, 1),
	(45, 'Авторские права', '<div style="text-align:center">Авторские права (с) <a href="http://joostina-cms.ru">Joostina Lotos</a>, 2012<br />Разработка шаблона (с) <a href="http://gd.joostina-cms.ru">Gold Dragon</a>, 2000-2012</div>  ', 1, 'footer', 0, '0000-00-00 00:00:00', 1, '', 0, 0, 0, 'moduleclass_sfx=-footer\ncache_time=0\nrssurl=\nrsstitle=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=0\nrsscache=3600', 0, 0),
	(52, 'Верхнее меню', '', 1, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_mljoostinamenu', 0, 0, 0, 'moduleclass_sfx=-topnemu\nclass_sfx=\nmenutype=topmenu\nmenu_style=divs\nml_imaged=1\nml_module_number=4\nnumrow=5\nml_first_hidden=0\nfull_active_id=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nactivate_parent=0\nindent_image=0\nindent_image1=aload.gif\nindent_image2=aload.gif\nindent_image3=aload.gif\nindent_image4=aload.gif\nindent_image5=aload.gif\nindent_image6=aload.gif\nml_separated_link=0\nml_linked_sep=0\nml_separated_link_first=0\nml_separated_link_last=0\nml_hide_active=0\nml_separated_active=0\nml_linked_sep_active=0\nml_separated_active_first=0\nml_separated_active_last=0\nml_separated_element=0\nml_separated_element_first=0\nml_separated_element_last=0\nml_td_width=0\nml_div=0\nml_aligner=left\nml_rollover_use=0\nml_image1=home_new.png\nml_image2=network.png\nml_image3=email.png\nml_image4=\nml_image5=\nml_image6=apply.png\nml_image7=apply.png\nml_image8=apply.png\nml_image9=apply.png\nml_image10=apply.png\nml_image11=apply.png\nml_image_roll_1=\nml_image_roll_2=\nml_image_roll_3=\nml_image_roll_4=\nml_image_roll_5=\nml_image_roll_6=\nml_image_roll_7=\nml_image_roll_8=\nml_image_roll_9=\nml_image_roll_10=\nml_image_roll_11=\nml_hide_logged1=1\nml_hide_logged2=1\nml_hide_logged3=1\nml_hide_logged4=1\nml_hide_logged5=1\nml_hide_logged6=1\nml_hide_logged7=1\nml_hide_logged8=1\nml_hide_logged9=1\nml_hide_logged10=1\nml_hide_logged11=1', 0, 0);
/*!40000 ALTER TABLE `jos_modules` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_modules_menu
DROP TABLE IF EXISTS `jos_modules_menu`;
CREATE TABLE IF NOT EXISTS `jos_modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_modules_menu: 29 rows
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
	(14, 'Что такое Лотос?', 6, 0, '0000-00-00 00:00:00', 1, 0, 86400);
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
	(1, 14, 'Второе имя Joostina 1.4 ', 4),
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_poll_date: 4 rows
DELETE FROM `jos_poll_date`;
/*!40000 ALTER TABLE `jos_poll_date` DISABLE KEYS */;
INSERT INTO `jos_poll_date` (`id`, `date`, `vote_id`, `poll_id`) VALUES
	(1, '2012-04-27 14:13:59', 4, 14),
	(2, '2012-04-27 14:14:18', 4, 14),
	(3, '2012-04-27 14:14:33', 1, 14),
	(4, '2012-05-28 15:52:11', 1, 14);
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

-- Dumping data for table joostina14.jos_session: 10 rows
DELETE FROM `jos_session`;
/*!40000 ALTER TABLE `jos_session` DISABLE KEYS */;
INSERT INTO `jos_session` (`username`, `time`, `session_id`, `guest`, `userid`, `usertype`, `gid`) VALUES
	('', '1338226960', '07e6fce925a9c379b6f89b6e8a0c897d', 1, 0, '', 0),
	('', '1338227446', 'a22516ad35d83ceb810a4e967984978c', 1, 0, '', 0),
	('admin', '1338227335', '3194f916ee3a89c2c9864ded076804dd', 1, 62, 'Super Administrator', 0),
	('', '1338227436', '184615b8e6940bead38b15125a6abe25', 1, 0, '', 0),
	('', '1338227051', '3f8082539a9849c95c446206e443dd3b', 1, 0, '', 0),
	('', '1338227458', '0f2e8856b28fa7ea22f37a0d870508b9', 1, 0, '', 0),
	('', '1338227061', '7b529e0413e21bc9f90f536066268671', 1, 0, '', 0),
	('', '1338227401', 'b6788cdc91e22950d25660751b4b5e5f', 1, 0, '', 0),
	('', '1338227072', '30bd1f92cdaec060e2d9a31868b07eb4', 1, 0, '', 0),
	('', '1338227369', '2a9bef3c8bfaf9c23fd195ee5cc90bb1', 1, 0, '', 0);
/*!40000 ALTER TABLE `jos_session` ENABLE KEYS */;


-- Dumping structure for table joostina14.jos_stats_agents
DROP TABLE IF EXISTS `jos_stats_agents`;
CREATE TABLE IF NOT EXISTS `jos_stats_agents` (
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '1',
  KEY `type_agent` (`type`,`agent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table joostina14.jos_stats_agents: 12 rows
DELETE FROM `jos_stats_agents`;
/*!40000 ALTER TABLE `jos_stats_agents` DISABLE KEYS */;
INSERT INTO `jos_stats_agents` (`agent`, `type`, `hits`) VALUES
	('Mozilla Firefox 12.0', 0, 3),
	('Windows XP', 1, 13),
	('joostina14.qqq', 2, 8),
	(' 0', 0, 9),
	('Unknown', 1, 9),
	('Mozilla Firefox 11.0', 0, 4),
	('openserver', 2, 14),
	('Safari 535.11', 0, 1),
	('Safari 535.12', 0, 2),
	('Microsoft Internet Explorer 8.0', 0, 1),
	('Opera 9.80', 0, 1),
	('Safari 536.5', 0, 1);
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
	(62, 'Administrator', 'admin', 'mail@mail.ru', 'f7225f4d33c1f648bb09f74142f70c4a:PSA9wQ7PMO4kEdJj', 'Super Administrator', 0, 1, 25, '2012-04-25 10:58:21', '2012-05-28 16:48:27', '', 'editor=\nexpired=\nexpired_time=', 0, 'av_1335358203.jpg'),
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
