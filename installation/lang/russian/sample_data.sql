#
# Данные для таблицы `#__banners_categories`
#
INSERT INTO `#__banners_categories` VALUES (1, 'Вертикальные баннеры', '', 1, 0, '0000-00-00 00:00:00');

#
# Данные для таблицы `#__banners_categories`
#
INSERT INTO `#__banners_clients` VALUES (1, 'Joostina Team', 'Joostina', 'info@joostina.ru', 'Разработчики Joostina CMS.', 1, 0, '0000-00-00 00:00:00');

#
# Данные для таблицы `#__banners`
#
INSERT INTO `#__banners` VALUES (1, 1, 1, '', 'Joostina_1', 0, 297, 0, 'joostina_v1.jpg', 'www.joostina.ru', '', 1, '2009-01-31 22:22:17', 843753, '2009-01-29', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2009-01-29', '', 0, '0000-00-00 00:00:00', 'www.joostina.ru', 'Официальный сайт Joostina CMS');
INSERT INTO `#__banners` VALUES (2, 1, 1, '', 'Joostina_2', 0, 297, 0, 'joostina_v2.jpg', 'www.joostina.ru', '', 1, '2009-01-31 22:23:12', 937503, '2009-01-29', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2009-01-29', '', 0, '0000-00-00 00:00:00', 'www.joostina.ru', 'Официальный сайт Joostina CMS');
INSERT INTO `#__banners` VALUES (3, 1, 1, '', 'Joostina_3', 0, 297, 0, 'joostina_v3.jpg', 'www.joostina.ru', '', 1, '2009-01-31 22:23:33', 750003, '2009-01-29', '00:00:00', '0000-00-00', '00:00:00', 0, '', 0, 'blank', 0, 'solid', 'green', '0', 0, '0', '2009-01-29', '', 0, '0000-00-00 00:00:00', 'www.joostina.ru', 'Официальный сайт Joostina CMS');

#
# Данные для таблицы `#__contact_details`
#
INSERT INTO `#__contact_details` VALUES (1, 'Joostina Team', 'Положение', 'Улица', 'Район', 'Область(край)', 'Российская Федерация', 'Индекс', 'Телефон', 'Факс', 'www.joostina.ru', '', 'top', 'info@joostina.ru', 0, 1, 0, '0000-00-00 00:00:00', 1, 'menu_image=-1\npageclass_sfx=\nprint=\nback_button=\nname=1\nposition=0\nemail=1\nstreet_address=0\nsuburb=0\nstate=0\ncountry=1\npostcode=0\ntelephone=0\nfax=0\nmisc=1\nimage=0\nvcard=0\nemail_description=0\nemail_description_text=\nemail_form=1\nemail_copy=0\ndrop_down=0\ncontact_icons=1\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nrobots=-1\nmeta_description=\nmeta_keywords=\nmeta_author=', 0, 12, 0);

#
# Данные для таблицы `#__menu`
#

INSERT INTO `#__menu` VALUES (4, 'mainmenu', 'Контакты', 'index.php?option=com_contact', 'components', 0, 0, 7, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (5, 'mainmenu', 'Ссылки', 'index.php?option=com_weblinks', 'components', 1, 0, 4, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=web_links.jpg\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nheadings=1\nhits=\nitem_description=1\nother_cat=1\ndescription=1\ndescription_text=\nimage=-1\nimage_align=right\nweblink_icons=');
INSERT INTO `#__menu` VALUES (6, 'mainmenu', 'Поиск', 'index.php?option=com_search', 'components', 1, 0, 16, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (7, 'mainmenu', 'Ленты новостей', 'index.php?option=com_newsfeeds', 'components', 1, 0, 12, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nother_cat_section=1\nother_cat=1\ncat_description=1\ncat_items=1\ndescription=0\ndescription_text=\nimage=-1\nimage_align=right\nheadings=1\nname=1\narticles=1\nlink=0\nfeed_image=1\nfeed_descr=1\nitem_descr=1\nword_count=0');
INSERT INTO `#__menu` VALUES (8, 'mainmenu', 'В окне', 'index.php?option=com_wrapper', 'wrapper', 1, 0, 0, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'title=\npage_name=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nscrolling=auto\nwidth=300\nheight=600\nheight_auto=1\nadd=1\nurl=www.joostina.ru');
INSERT INTO `#__menu` VALUES (9, 'othermenu', 'joostina.ru', 'http://www.joostina.ru', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (10, 'othermenu', 'joom.ru', 'http://www.joom.ru', 'url', 1, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (11, 'othermenu', 'joomlaportal.ru', 'http://www.joomlaportal.ru', 'url', 1, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (12, 'othermenu', 'joomlaforum.ru', 'http://www.joomlaforum.ru', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (13, 'othermenu', 'joomla-support.ru', 'http://www.joomla-support.ru', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (14, 'othermenu', 'joomla.ru', 'http://www.joomla.ru', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (15, 'usermenu', 'Панель управления', 'administrator/', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1');
INSERT INTO `#__menu` VALUES (16, 'usermenu', 'Добавить ссылку', 'index.php?option=com_weblinks&task=new', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, '');
INSERT INTO `#__menu` VALUES (17, 'usermenu', 'Разблокировать содержимое', 'index.php?option=com_users&task=CheckIn', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, '');
INSERT INTO `#__menu` VALUES (18, 'mainmenu', 'Карта сайта', 'index.php?option=com_xmap', 'components', 1, 0, 24, 0, 11, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '');
INSERT INTO `#__menu` VALUES (19, 'topmenu', 'Контакты', 'index.php?option=com_contact', 'components', 1, 0, 7, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=Контакты');
INSERT INTO `#__menu` VALUES (20, 'mainmenu', 'Опросы', 'index.php?option=com_poll', 'components', 1, 0, 11, 0, 12, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'title=\nmenu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=');


# Данные для таблицы `#__newsfeeds`
#
INSERT INTO `#__newsfeeds` VALUES (4, 1, 'Joostina! - Новости официального сайта', 'http://www.joostina.ru/index2.php?option=com_rss&feed=RSS2.0&no_html=1', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 8,0);
INSERT INTO `#__newsfeeds` VALUES (4, 11, 'Новости Joostina и Joomla! в России', 'http://www.joomlaportal.ru/component/option,com_rss/feed,RSS2.0/no_html,1/', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 2,0);
INSERT INTO `#__newsfeeds` VALUES (4, 12, 'Форумы о Joomla! в России', 'http://forum.joom.ru/index.php?type=rss;action=.xml', '', 1, 5, 1200, 0, '0000-00-00 00:00:00', 1,0);
INSERT INTO `#__newsfeeds` VALUES (5, 5, 'Хабрахабр', 'http://www.habrahabr.ru/rss/', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 2,0);

#
# Данные для таблицы `#__poll_data`
#
INSERT INTO `#__poll_data` VALUES (1, 14, 'Нормально, без проблем', 2);
INSERT INTO `#__poll_data` VALUES (2, 14, 'Были некоторые затруднения', 0);
INSERT INTO `#__poll_data` VALUES (3, 14, 'А что, уже все установлено? Какой я молодец!', 0);
INSERT INTO `#__poll_data` VALUES (4, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (5, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (6, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (7, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (8, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (9, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (10, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (11, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (12, 14, '', 0);

#
# Данные для таблицы `#__polls`
#
INSERT INTO `#__polls` VALUES (14, 'Как прошла установка?', 2, 0, '0000-00-00 00:00:00', 1, 0, 86400);

#
# Данные для таблицы `#__poll_menu`
#
INSERT INTO `#__poll_menu` VALUES (14, 0);

#
# Данные для таблицы `#__weblinks`
#
INSERT INTO `#__weblinks` VALUES (1, 2, 0, 'Joostina!', 'http://www.joostina.ru', 'Домашняя страница Joostina!', '2007-10-28 23:20:02', 3, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0');
INSERT INTO `#__weblinks` VALUES (2, 2, 0, 'php.net', 'http://www.php.net', 'Язык программирования, на котором написана Joostina!', '2004-07-07 11:33:24', 0, 1, 0, '0000-00-00 00:00:00', 3, 0, 1, '');
INSERT INTO `#__weblinks` VALUES (3, 2, 0, 'MySQL', 'http://www.mysql.com', 'База данных, используемая Joostina!', '2004-07-07 10:18:31', 0, 1, 0, '0000-00-00 00:00:00', 5, 0, 1, '');
INSERT INTO `#__weblinks` VALUES (6, 13, 0, 'Joom.Ru - Русский дом Joomla!', 'http://joom.ru/', 'Русский дом Joomla!', '2005-10-26 22:07:32', 0, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0');
INSERT INTO `#__weblinks` VALUES (7, 13, 0, 'Форумы Joomla!', 'http://joomla-support.ru/', 'Форумы поддержки пользователей Joomla! в России.', '2005-10-26 22:10:39', 0, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 'target=0');
INSERT INTO `#__weblinks` VALUES (8, 13, 0, 'Joomlaportal.ru!', 'http://www.joomlaportal.ru/?from_joostina', 'Информация о Joostina и Joomla! в России', '2005-10-26 22:07:32', 0, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0');
INSERT INTO `#__weblinks` VALUES (9, 13, 0, 'Joomlaforum.ru', 'http://www.joomlaforum.ru/?from_joostina', 'Русский форум поддержки Joostina и Joomla.', '2007-10-28 23:21:39', 0, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 'target=0');
INSERT INTO `#__weblinks` VALUES (10, 13, 0, 'Joomla.ru', 'http://www.joomla.ru/', 'О Joostina и Joomla в России.', '2007-10-28 23:21:39', 0, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 'target=0');

# Базовая карта для Xmap
INSERT INTO `#__xmap_sitemap` VALUES (1, 'Карта сайта', 0, 0, 0, 1, 1, 'img_grey.gif', 'mainmenu,0,1,1,0.5,daily\ntopmenu,1,1,1,0.5,daily', '', 1, 1, 1800, 'sitemap', 0, 43, 0, 39, 0, 1233415318);

# предустанровленные модули
INSERT INTO `#__modules` VALUES (38, 'Спасибо за выбор Joostina!', 'Теперь мы вместе, и это очень радует ). Если вас интересует вопрос &quot;А почему именно Joostina?&quot;, мы хотели бы обратить ваше внимание на некоторые примечательные особенности этой CMS:\r\n<ul class="marker_round">\r\n	<li><strong>1</strong>Удачное сочетание мощности, скорости работы и нетребовательности к ресурсам сервера. Да, бывает и такое.</li>\r\n	<li><strong>2</strong>Расширяемость за счет использования сторонних компонентов и модулей. А также простота разработки расширений для Joostina CMS. </li>\r\n	<li><strong>3</strong>Данный продукт разрабатывается с большой любовью к коду и огромным  вниманием к пользователям. И наоборот. </li>\r\n</ul>\r\n<a href="http://www.joostina.ru" class="readmore_big">Узнать больше</a>\r\n', 1, 'user2', 0, '0000-00-00 00:00:00', 1, '', 0, 0, 1, 'moduleclass_sfx=\ncache=1\nfirebots=1\nrssurl=\nrsstitle=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=0\nrsscache=3600', 0, 0);
INSERT INTO `#__modules` VALUES (39, 'Полезная информация', '<ul class="bigred">\r\n<li>Файлы шаблонов Joostina CMS находятся в templates/[название_вашего_шаблона] </li>\r\n	<li>Отключите в глобальной конфигурации ненужные функции и мамботы для ускорение работы сайта </li>\r\n	<li>Правильно настроенное кэширование - залог здоровья высокопосещаемого сайта </li>\r\n</ul>\r\n', 1, 'user8', 0, '0000-00-00 00:00:00', 1, '', 0, 0, 1, 'moduleclass_sfx=\ncache=1\nfirebots=1\nrssurl=\nrsstitle=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=0\nrsscache=3600', 0, 0);

#
# Dumping data for table `#__boss_1_categories`
#

INSERT INTO `#__boss_1_categories` (`id`, `parent`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `description`, `ordering`, `published`, `content_types`, `template`, `rights`) VALUES
(1, 0, 'Категория 1', 'category1', '', '', '', 'Описание категории 1\r\n', 0, 1, 1, '0', '');

INSERT INTO `#__boss_1_contents` (`id`, `name`, `slug`, `meta_title`, `meta_desc`, `meta_keys`, `userid`, `published`, `date_created`, `date_last_сomment`, `date_publish`, `date_unpublish`, `views`, `type_content`, `content_editor`, `content_editorfull`) VALUES
();

INSERT INTO `#__boss_1_content_category_href` (`id`, `category_id`, `content_id`) VALUES
(2, 1, 1);

INSERT INTO `#__boss_1_content_types` (`id`, `name`, `desc`, `fields`, `published`, `ordering`) VALUES
(1, 'Статьи', 'Обычные статьи без изысков, аналог ком-контент', 0, 1, 1);


INSERT INTO `#__boss_1_fields` (`fieldid`, `name`, `title`, `display_title`, `description`, `type`, `text_before`, `text_after`, `tags_open`, `tags_separator`, `tags_close`, `maxlength`, `size`, `required`, `link_text`, `link_image`, `ordering`, `cols`, `rows`, `profile`, `editable`, `searchable`, `sort`, `sort_direction`, `catsid`, `published`, `filter`) VALUES
(20, 'content_editor', 'Краткое описание', 0, 'Здесь пишем то, что будет отображаться в списке контента (поиск, категории и т.п.)', 'BossTextAreaEditorPlugin', '', '', '', '', '', 2000, 0, 1, '', '', 2, 200, 20, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
(21, 'content_editorfull', 'Полное описание', 0, 'Здесь пишем основной текст', 'BossTextAreaEditorPlugin', '', '', '', '', '', 2000, 0, 1, '', '', 3, 50, 5, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0),
(22, 'content_homepage', 'На главной', 0, 'Опубликовать этот контент на главной странице.', 'BossCheckboxPlugin', '', '', '', '', '', 75, 0, 0, ',-1,', ',-1,', 0, 0, 0, 0, 1, 1, 0, 'DESC', ',-1,', 1, 0);

INSERT INTO `#__boss_1_groupfields` (`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) VALUES
(20, 12, 'blog', 'category', 0),
(21, 14, 'blog', 'content', 0),
(20, 2, 'default', 'category', 0),
(21, 9, 'default', 'content', 0),
(20, 16, 'table', 'category', 0),
(21, 19, 'table', 'content', 0),
(20, 21, 'template2', 'category', 0),
(21, 24, 'template2', 'content', 0);

INSERT INTO `#__boss_1_groups` (`id`, `name`, `desc`, `template`, `type_tmpl`, `catsid`, `published`) VALUES
(1, 'ListSubtitle', 'ListSubtitle', 'default', 'category', ',-1,', 1),
(2, 'ListDescription', 'ListDescription', 'default', 'category', ',-1,', 1),
(3, 'ListBottom', 'ListBottom', 'default', 'category', ',-1,', 1),
(4, 'ListImage', 'ListImage', 'default', 'category', ',-1,', 1),
(5, 'DetailsSubtitle1', 'DetailsSubtitle1', 'default', 'content', ',-1,', 1),
(6, 'DetailsSubtitle2', 'DetailsSubtitle2', 'default', 'content', ',-1,', 1),
(7, 'DetailsSubtitle3', 'DetailsSubtitle3', 'default', 'content', ',-1,', 1),
(8, 'DetailsDescription', 'DetailsDescription', 'default', 'content', ',-1,', 1),
(9, 'DetailsFullText', 'DetailsFullText', 'default', 'content', ',-1,', 1),
(10, 'DetailsBottom', 'DetailsBottom', 'default', 'content', ',-1,', 1),
(11, 'DetailsImage', 'DetailsImage', 'default', 'content', ',-1,', 1),
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


