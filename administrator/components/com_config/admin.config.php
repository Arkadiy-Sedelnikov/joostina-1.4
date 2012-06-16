<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

if(!$acl->acl_check('administration', 'config', 'users', $my->usertype)){
	mosRedirect('index2.php?', _NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));

switch($task){

	case 'apply':
	case 'save':
		js_menu_cache_clear();
		saveconfig($task);
		break;

	case 'cancel':
		mosRedirect('index2.php');
		break;

	default:
		showconfig($option);
		break;
}

/**
 * Show the configuration edit form
 * @param string The URL option
 */
function showconfig($option){
	global $database, $mosConfig_editor, $mosConfig_cache_handler;

	$row = new JConfig();
	$row->bindGlobals();

	// compile list of the languages
	$langs = array();
	$lists = array();

	// PRE-PROCESS SOME LISTS

	// -- Языки --
	if($handle = opendir(JPATH_BASE . '/language/')){
		$i = 0;
		while(false !== ($file = readdir($handle))){
			if(!strcasecmp(substr($file, -4), ".xml") && $file != "." && $file != ".."){
				$langs[] = mosHTML::makeOption(substr($file, 0, -4));
			}
		}
	}

	// сортировка списка языков
	sort($langs);
	reset($langs);

	// -- Редакторы --
	$query = "SELECT element AS value, name AS text"
		. "\n FROM #__mambots"
		. "\n WHERE folder = 'editors'"
		. "\n AND published = 1"
		. "\n ORDER BY ordering, name";
	$database->setQuery($query);
	$edits = $database->loadObjectList();

	if(!$row->config_editor){
		$row->config_editor = '';
	}
	// build the html select list
	$lists['editor'] = mosHTML::selectList($edits, 'config_editor', 'class="inputbox" size="1"', 'value', 'text', $row->config_editor);


	// НАСТРОЙКИ САЙТА
	$lists['offline'] = mosHTML::yesnoRadioList('config_offline', 'class="inputbox"', $row->config_offline);


	$listLimit = array(
		mosHTML::makeOption(5, 5),
		mosHTML::makeOption(10, 10),
		mosHTML::makeOption(15, 15),
		mosHTML::makeOption(20, 20),
		mosHTML::makeOption(25, 25),
		mosHTML::makeOption(30, 30),
		mosHTML::makeOption(50, 50),
		mosHTML::makeOption(100, 100),
		mosHTML::makeOption(150, 150),
	);

	$lists['list_limit'] = mosHTML::selectList($listLimit, 'config_list_limit', 'class="inputbox" size="1"', 'value', 'text', ($row->config_list_limit ? $row->config_list_limit : 50));

	$lists['frontend_login'] = mosHTML::yesnoRadioList('config_frontend_login', 'class="inputbox"', $row->config_frontend_login);

	// отключение ведения сессий подсчета числа пользователей на сайте
	$lists['session_front'] = mosHTML::yesnoRadioList('config_no_session_front', 'class="inputbox"', $row->config_no_session_front);
	// отключение syndicate
	$lists['syndicate_off'] = mosHTML::yesnoRadioList('config_syndicate_off', 'class="inputbox"', $row->config_syndicate_off);
	// отключение тега Generator
	$lists['generator_off'] = mosHTML::yesnoRadioList('config_generator_off', 'class="inputbox"', $row->config_generator_off);
	// отключение мамботов группы system
	$lists['mmb_system_off'] = mosHTML::yesnoRadioList('config_mmb_system_off', 'class="inputbox"', $row->config_mmb_system_off);
	// получаем список шаблонов. Код получен из модуля выбора шаблона
	$titlelength = 20;
	$template_path = JPATH_BASE . DS . 'templates';
	$templatefolder = @dir($template_path);
	$darray = array();
	$darray[] = mosHTML::makeOption('...', _O_OTHER); // параметр по умолчанию - позволяет использовать стандартный способ определения шаблона
	if($templatefolder){
		while($templatefile = $templatefolder->read()){
			if($templatefile != 'system' && $templatefile != "." && $templatefile != ".." && $templatefile != ".svn" && $templatefile != "css" && is_dir("$template_path/$templatefile")){
				if(strlen($templatefile) > $titlelength){
					$templatename = substr($templatefile, 0, $titlelength - 3);
					$templatename .= "...";
				} else{
					$templatename = $templatefile;
				}
				$darray[] = mosHTML::makeOption($templatefile, $templatename);
			}
		}
		$templatefolder->close();
	}
	sort($darray);
	$lists['one_template'] = mosHTML::selectList($darray, 'config_one_template', "class=\"inputbox\" ", 'value', 'text', $row->config_one_template);
	// время генерации страницы
	$lists['config_time_generate'] = mosHTML::yesnoRadioList('config_time_generate', 'class="inputbox"', $row->config_time_generate);
	//индексация страницы печати
	$lists['index_print'] = mosHTML::yesnoRadioList('config_index_print', 'class="inputbox"', $row->config_index_print);
	// расширенные теги индексации
	$lists['index_tag'] = mosHTML::yesnoRadioList('config_index_tag', 'class="inputbox"', $row->config_index_tag);
	// ежесуточная оптимизация таблиц бд
	$lists['optimizetables'] = mosHTML::yesnoRadioList('config_optimizetables', 'class="inputbox"', $row->config_optimizetables);
	// отключение мамботов группы content
	$lists['mmb_content_off'] = mosHTML::yesnoRadioList('config_mmb_content_off', 'class="inputbox"', $row->config_mmb_content_off);
	// кэширование меню панели управления
	$lists['adm_menu_cache'] = mosHTML::yesnoRadioList('config_adm_menu_cache', 'class="inputbox"', $row->config_adm_menu_cache);
	// управление captcha
	$lists['captcha'] = mosHTML::yesnoRadioList('config_captcha', 'class="inputbox"', $row->config_captcha);
	// управление captcha
	$lists['com_frontpage_clear'] = mosHTML::yesnoRadioList('config_com_frontpage_clear', 'class="inputbox"', $row->config_com_frontpage_clear);
	// корень файлового менеджера
	$row->config_joomlaxplorer_dir = $row->config_joomlaxplorer_dir ? $row->config_joomlaxplorer_dir : JPATH_BASE;
	// автоматическая установка чекбокса "Публиковать на главной"
	$lists['auto_frontpage'] = mosHTML::yesnoRadioList('config_auto_frontpage', 'class="inputbox"', $row->config_auto_frontpage);
	// уникальные идентификаторы новостей
	$lists['config_uid_news'] = mosHTML::yesnoRadioList('config_uid_news', 'class="inputbox"', $row->config_uid_news);
	// подсчет прочтений содержимого
	$lists['config_content_hits'] = mosHTML::yesnoRadioList('config_content_hits', 'class="inputbox"', $row->config_content_hits);
	// формат времени
	$date_help = array(
		mosHTML::makeOption('%d.%m.%Y ' . _COM_CONFIG_YEAR . ' %H:%M', strftime('%d.%m.%Y ' . _COM_CONFIG_YEAR . ' %H:%M')),
		mosHTML::makeOption('%d:%m:%Y ' . _COM_CONFIG_YEAR . ' %H:%M', strftime('%d:%m:%Y ' . _COM_CONFIG_YEAR . ' %H:%M')),
		mosHTML::makeOption('%d-%m-%Y ' . _COM_CONFIG_YEAR . ' %H-%M', strftime('%d-%m-%Y ' . _COM_CONFIG_YEAR . ' %H-%M')),
		mosHTML::makeOption('%d/%m/%Y ' . _COM_CONFIG_YEAR . ' %H/%M', strftime('%d/%m/%Y ' . _COM_CONFIG_YEAR . ' %H/%M')),
		mosHTML::makeOption('%d/%m/%Y %H/%M', strftime('%d/%m/%Y %H/%M')),
		mosHTML::makeOption('%d/%m/%Y', strftime('%d/%m/%Y')),
		mosHTML::makeOption('%d:%m:%Y', strftime('%d:%m:%Y')),
		mosHTML::makeOption('%d.%m.%Y', strftime('%d.%m.%Y')),
		mosHTML::makeOption('%d/%m/%Y ' . _COM_CONFIG_YEAR, strftime('%d/%m/%Y ' . _COM_CONFIG_YEAR)),
		mosHTML::makeOption('%d:%m:%Y ' . _COM_CONFIG_YEAR, strftime('%d:%m:%Y ' . _COM_CONFIG_YEAR)),
		mosHTML::makeOption('%d.%m.%Y ' . _COM_CONFIG_YEAR, strftime('%d.%m.%Y ' . _COM_CONFIG_YEAR)),
		mosHTML::makeOption('%H/%M', strftime('%H/%M')),
		mosHTML::makeOption('%H:%M', strftime('%H:%M')),
		mosHTML::makeOption('%H ' . _COM_CONFIG_HOURS . '%M ' . _COM_CONFIG_MONTH, strftime('%H ' . _COM_CONFIG_HOURS . ' %M ' . _COM_CONFIG_MONTH)),
		mosHTML::makeOption('%A %d/%m/%Y ' . _COM_CONFIG_YEAR . ' %H/%M', Jstring::to_utf8(strftime('%A %d/%m/%Y ' . _COM_CONFIG_YEAR . ' %H/%M'))),
		mosHTML::makeOption('%d %B %Y', Jstring::to_utf8(strftime('%d %B %Y')))
	);
	$lists['form_date_help'] = mosHTML::selectList($date_help, 'config_form_date_h', 'class="inputbox" size="1" onchange="adminForm.config_form_date.value=this.value;"', 'value', 'text', $row->config_form_date);
	// полный формат даты и времени
	$lists['form_date_full_help'] = mosHTML::selectList($date_help, 'config_form_date_full_h', 'class="inputbox" size="1" onchange="adminForm.config_form_date_full.value=this.value;"', 'value', 'text', $row->config_form_date_full);
	// поддержка работы на младших версиях MySQL
	$lists['config_pathway_clean'] = mosHTML::yesnoRadioList('config_pathway_clean', 'class="inputbox"', $row->config_pathway_clean);
	// отключение удаления сессий в панели управления
	$lists['config_admin_autologout'] = mosHTML::yesnoRadioList('config_admin_autologout', 'class="inputbox"', $row->config_admin_autologout);
	// отключение кнопки "Помощь"
	$lists['config_disable_button_help'] = mosHTML::yesnoRadioList('config_disable_button_help', 'class="inputbox"', $row->config_disable_button_help);
	// отключение блокировок объектов
	$lists['config_disable_checked_out'] = mosHTML::yesnoRadioList('config_disable_checked_out', 'class="inputbox"', $row->config_disable_checked_out);
	// отключение favicon
	$lists['config_disable_favicon'] = mosHTML::yesnoRadioList('config_disable_favicon', 'class="inputbox"', $row->config_disable_favicon);
	// использование расширенного отладчика на фронте
	$lists['config_front_debug'] = mosHTML::yesnoRadioList('config_front_debug', 'class="inputbox"', $row->config_front_debug);
	// использование мамботов группы mainbody
	$lists['config_mmb_mainbody_off'] = mosHTML::yesnoRadioList('config_mmb_mainbody_off', 'class="inputbox"', $row->config_mmb_mainbody_off);
	// автоматическая авторизация после подтверждения регистрации
	$lists['config_auto_activ_login'] = mosHTML::yesnoRadioList('config_auto_activ_login', 'class="inputbox"', $row->config_auto_activ_login);
	// отключение вкладки 'Изображения'
	$lists['config_disable_image_tab'] = mosHTML::yesnoRadioList('config_disable_image_tab', 'class="inputbox"', $row->config_disable_image_tab);
	// отключить проверки публикаций по датам
	$lists['config_disable_date_state'] = mosHTML::yesnoRadioList('config_disable_date_state', 'class="inputbox"', $row->config_disable_date_state);
	// отключить проверку доступа к содержимому
	$lists['config_disable_access_control'] = mosHTML::yesnoRadioList('config_disable_access_control', 'class="inputbox"', $row->config_disable_access_control);
	// оптимизация функции кэширования
	$lists['config_cache_opt'] = mosHTML::yesnoRadioList('config_cache_opt', 'class="inputbox"', $row->config_cache_opt);
	// включение сжатия css и js файлов
	$lists['config_gz_js_css'] = mosHTML::yesnoRadioList('config_gz_js_css', 'class="inputbox"', $row->config_gz_js_css);
	// captcha для регистрации
	$lists['config_captcha_reg'] = mosHTML::yesnoRadioList('config_captcha_reg', 'class="inputbox"', $row->config_captcha_reg);
	// captcha для формы контактов
	$lists['config_captcha_cont'] = mosHTML::yesnoRadioList('config_captcha_cont', 'class="inputbox"', $row->config_captcha_cont);
	// визуальный редактор для html и css - codepress
	$lists['config_codepress'] = mosHTML::yesnoRadioList('config_codepress', 'class="inputbox"', $row->config_codepress);

	// DEBUG - ОТЛАДКА
	$lists['debug'] = mosHTML::yesnoRadioList('config_debug', 'class="inputbox"', $row->config_debug);

	// НАСТРОЙКИ СЕРВЕРА
	$lists['gzip'] = mosHTML::yesnoRadioList('config_gzip', 'class="inputbox"', $row->config_gzip);

	$session = array(
		mosHTML::makeOption(0, _SECURITY_LEVEL3),
		mosHTML::makeOption(1, _SECURITY_LEVEL2),
		mosHTML::makeOption(2, _SECURITY_LEVEL1)
	);
	$lists['session_type'] = mosHTML::selectList($session, 'config_session_type', 'class="inputbox" size="1"', 'value', 'text', $row->config_session_type);

	$errors = array(
		mosHTML::makeOption(E_ALL, _COM_CONFIG_ERROR_ALL),
		mosHTML::makeOption(-1, _COM_CONFIG_ERROR_SYSTEM),
		mosHTML::makeOption(0, _COM_CONFIG_ERROR_HIDE),
		mosHTML::makeOption(E_ERROR | E_WARNING | E_PARSE, _COM_CONFIG_ERROR_TINY),
		mosHTML::makeOption(E_ALL & ~E_NOTICE, _COM_CONFIG_ERROR_PARANOIDAL),
	);

	$lists['error_reporting'] = mosHTML::selectList($errors, 'config_error_reporting', 'class="inputbox" size="1"', 'value', 'text', $row->config_error_reporting);

	$lists['admin_expired'] = mosHTML::yesnoRadioList('config_admin_expired', 'class="inputbox"', $row->config_admin_expired);

	// НАСТРОЙКИ ЛОКАЛИ СТРАНЫ
	$lists['lang'] = mosHTML::selectList($langs, 'config_lang', 'class="inputbox" size="1"', 'value', 'text', $row->config_lang);

	$timeoffset = array(
		mosHTML::makeOption(-12, _TIME_OFFSET_M_12),
		mosHTML::makeOption(-11, _TIME_OFFSET_M_11),
		mosHTML::makeOption(-10, _TIME_OFFSET_M_10),
		mosHTML::makeOption(-9.5, _TIME_OFFSET_M_9_5),
		mosHTML::makeOption(-9, _TIME_OFFSET_M_9),
		mosHTML::makeOption(-8, _TIME_OFFSET_M_8),
		mosHTML::makeOption(-7, _TIME_OFFSET_M_7),
		mosHTML::makeOption(-6, _TIME_OFFSET_M_6),
		mosHTML::makeOption(-5, _TIME_OFFSET_M_5),
		mosHTML::makeOption(-4, _TIME_OFFSET_M_4),
		mosHTML::makeOption(-3.5, _TIME_OFFSET_M_3_5),
		mosHTML::makeOption(-3, _TIME_OFFSET_M_3),
		mosHTML::makeOption(-2, _TIME_OFFSET_M_2),
		mosHTML::makeOption(-1, _TIME_OFFSET_M_1),
		mosHTML::makeOption(0, _TIME_OFFSET_M_0),
		mosHTML::makeOption(1, _TIME_OFFSET_P_1),
		mosHTML::makeOption(2, _TIME_OFFSET_P_2),
		mosHTML::makeOption(3, _TIME_OFFSET_P_3),
		mosHTML::makeOption(3.5, _TIME_OFFSET_P_3_5),
		mosHTML::makeOption(4, _TIME_OFFSET_P_4),
		mosHTML::makeOption(4.5, _TIME_OFFSET_P_4_5),
		mosHTML::makeOption(5, _TIME_OFFSET_P_5),
		mosHTML::makeOption(5.5, _TIME_OFFSET_P_5_5),
		mosHTML::makeOption(5.75, _TIME_OFFSET_P_5_75),
		mosHTML::makeOption(6, _TIME_OFFSET_P_6),
		mosHTML::makeOption(6.30, _TIME_OFFSET_P_6_5),
		mosHTML::makeOption(7, _TIME_OFFSET_P_7),
		mosHTML::makeOption(8, _TIME_OFFSET_P_8),
		mosHTML::makeOption(8.75, _TIME_OFFSET_P_8_75),
		mosHTML::makeOption(9, _TIME_OFFSET_P_9),
		mosHTML::makeOption(9.5, _TIME_OFFSET_P_9_5),
		mosHTML::makeOption(10, _TIME_OFFSET_P_10),
		mosHTML::makeOption(10.5, _TIME_OFFSET_P_10_5),
		mosHTML::makeOption(11, _TIME_OFFSET_P_11),
		mosHTML::makeOption(11.30, _TIME_OFFSET_P_11_5),
		mosHTML::makeOption(12, _TIME_OFFSET_P_12),
		mosHTML::makeOption(12.75, _TIME_OFFSET_P_12_75),
		mosHTML::makeOption(13, _TIME_OFFSET_P_13),
		mosHTML::makeOption(14, _TIME_OFFSET_P_14),);

	$lists['offset'] = mosHTML::selectList($timeoffset, 'config_offset_user', 'class="inputbox" size="1"', 'value', 'text', $row->config_offset_user);

	$feed_timeoffset = array(
		mosHTML::makeOption('-12:00', _TIME_OFFSET_M_12),
		mosHTML::makeOption('-11:00', _TIME_OFFSET_M_11),
		mosHTML::makeOption('-10:00', _TIME_OFFSET_M_10),
		mosHTML::makeOption('-09:30', _TIME_OFFSET_M_9_5),
		mosHTML::makeOption('-09:00', _TIME_OFFSET_M_9),
		mosHTML::makeOption('-08:00', _TIME_OFFSET_M_8),
		mosHTML::makeOption('-07:00', _TIME_OFFSET_M_7),
		mosHTML::makeOption('-06:00', _TIME_OFFSET_M_6),
		mosHTML::makeOption('-05:00', _TIME_OFFSET_M_5),
		mosHTML::makeOption('-04:00', _TIME_OFFSET_M_4),
		mosHTML::makeOption('-03:30', _TIME_OFFSET_M_3_5),
		mosHTML::makeOption('-03:00', _TIME_OFFSET_M_3),
		mosHTML::makeOption('-02:00', _TIME_OFFSET_M_2),
		mosHTML::makeOption('-01:00', _TIME_OFFSET_M_1),
		mosHTML::makeOption('00:00', _TIME_OFFSET_M_0),
		mosHTML::makeOption('+01:00', _TIME_OFFSET_P_1),
		mosHTML::makeOption('+02:00', _TIME_OFFSET_P_2),
		mosHTML::makeOption('+03:00', _TIME_OFFSET_P_3),
		mosHTML::makeOption('+03:30', _TIME_OFFSET_P_3_5),
		mosHTML::makeOption('+04:00', _TIME_OFFSET_P_4),
		mosHTML::makeOption('+04:30', _TIME_OFFSET_P_4_5),
		mosHTML::makeOption('+05:00', _TIME_OFFSET_P_5),
		mosHTML::makeOption('+05:30', _TIME_OFFSET_P_5_5),
		mosHTML::makeOption('+05:45', _TIME_OFFSET_P_5_75),
		mosHTML::makeOption('+06:00', _TIME_OFFSET_P_6),
		mosHTML::makeOption('+06:30', _TIME_OFFSET_P_6_5),
		mosHTML::makeOption('+07:00', _TIME_OFFSET_P_7),
		mosHTML::makeOption('+08:00', _TIME_OFFSET_P_8),
		mosHTML::makeOption('+08:45', _TIME_OFFSET_P_8_75),
		mosHTML::makeOption('+09:00', _TIME_OFFSET_P_9),
		mosHTML::makeOption('+09:30', _TIME_OFFSET_P_9_5),
		mosHTML::makeOption('+10:00', _TIME_OFFSET_P_10),
		mosHTML::makeOption('+10:30', _TIME_OFFSET_P_10_5),
		mosHTML::makeOption('+11:00', _TIME_OFFSET_P_11),
		mosHTML::makeOption('+11:30', _TIME_OFFSET_P_11_5),
		mosHTML::makeOption('+12:00', _TIME_OFFSET_P_12),
		mosHTML::makeOption('+12:45', _TIME_OFFSET_P_12_75),
		mosHTML::makeOption('+13:00', _TIME_OFFSET_P_13),
		mosHTML::makeOption('+14:00', _TIME_OFFSET_P_14)
	);
	$lists['feed_timeoffset'] = mosHTML::selectList($feed_timeoffset, 'config_feed_timeoffset', 'class="inputbox" size="1"', 'value', 'text', $row->config_feed_timeoffset);

// НАСТРОЙКИ ПОЧТЫ
	$mailer = array(
		mosHTML::makeOption('mail', _PHP_MAIL_FUNCTION),
		mosHTML::makeOption('sendmail', 'Sendmail'),
		mosHTML::makeOption('smtp', _SMTP_SERVER)
	);
	$lists['mailer'] = mosHTML::selectList($mailer, 'config_mailer', 'class="inputbox" size="1"', 'value', 'text', $row->config_mailer);
	$lists['smtpauth'] = mosHTML::yesnoRadioList('config_smtpauth', 'class="inputbox"', $row->config_smtpauth);


	// НАСТРОЙКИ КЭША
	$lists['caching'] = mosHTML::yesnoRadioList('config_caching', 'class="inputbox"', $row->config_caching);

// НАСТРОЙКИ ПОЛЬЗОВАТЕЛЕЙ

	$lists['useractivation'] = mosHTML::yesnoRadioList('config_useractivation', 'class="inputbox"', $row->config_useractivation);
	$lists['uniquemail'] = mosHTML::yesnoRadioList('config_uniquemail', 'class="inputbox"', $row->config_uniquemail);
	$lists['shownoauth'] = mosHTML::yesnoRadioList('config_shownoauth', 'class="inputbox"', $row->config_shownoauth);
	$lists['frontend_userparams'] = mosHTML::yesnoRadioList('config_frontend_userparams', 'class="inputbox"', $row->config_frontend_userparams);
	$lists['allowUserRegistration'] = mosHTML::yesnoRadioList('config_allowUserRegistration', 'class="inputbox"', $row->config_allowUserRegistration);

// НАСТРОЙКИ META-ДАННЫХ
	$lists['MetaAuthor'] = mosHTML::yesnoRadioList('config_MetaAuthor', 'class="inputbox"', $row->config_MetaAuthor);
	$lists['MetaTitle'] = mosHTML::yesnoRadioList('config_MetaTitle', 'class="inputbox"', $row->config_MetaTitle);

// НАСТРОЙКИ СТАТИСТИКИ
	$lists['log_searches'] = mosHTML::yesnoRadioList('config_enable_log_searches', 'class="inputbox"', $row->config_enable_log_searches);
	$lists['enable_stats'] = mosHTML::yesnoRadioList('config_enable_stats', 'class="inputbox"', $row->config_enable_stats);
	$lists['log_items'] = mosHTML::yesnoRadioList('config_enable_log_items', 'class="inputbox"', $row->config_enable_log_items);

// НАСТРОЙКИ SEO
	$lists['sef'] = mosHTML::yesnoRadioList('config_sef', 'class="inputbox" onclick="javascript: if (document.adminForm.config_sef[1].checked) { alert(\'' . _C_CONFIG_HTACCESS_RENAME . '\') }"', $row->config_sef);
	$lists['pagetitles'] = mosHTML::yesnoRadioList('config_pagetitles', 'class="inputbox"', $row->config_pagetitles);

	$pagetitles_first = array(
		mosHTML::makeOption(0, _COM_CONFIG_SEO_TYPE_1),
		mosHTML::makeOption(1, _COM_CONFIG_SEO_TYPE_2),
		mosHTML::makeOption(2, _COM_CONFIG_SEO_TYPE_3),
		mosHTML::makeOption(3, _COM_CONFIG_SEO_TYPE_4),
	);
	$lists['pagetitles_first'] = mosHTML::selectList($pagetitles_first, 'config_pagetitles_first', 'class="inputbox" size="1"', 'value', 'text', $row->config_pagetitles_first);

// НАСТРОЙКИ СОДЕРЖИМОГО
	$author_name_type = array(
		mosHTML::makeOption(1, _COM_CONFIG_CC_NAME_TEXT),
		mosHTML::makeOption(2, _COM_CONFIG_CC_LOGIN_TEXT),
		mosHTML::makeOption(3, _COM_CONFIG_CC_NAME_LINK),
		mosHTML::makeOption(4, _COM_CONFIG_CC_LIGIN_LINK),
	);
	$lists['authorName'] = mosHTML::selectList($author_name_type, 'config_author_name', 'class="inputbox" size="1"', 'value', 'text', $row->config_author_name);

	$lists['link_titles'] = mosHTML::yesnoRadioList('config_link_titles', 'class="inputbox"', $row->config_link_titles);
	$lists['readmore'] = mosHTML::yesnoRadioList('config_readmore', 'class="inputbox"', $row->config_readmore);
	$lists['vote'] = mosHTML::yesnoRadioList('config_vote', 'class="inputbox"', $row->config_vote);
	$lists['showAuthor'] = mosHTML::yesnoRadioList('config_showAuthor', 'class="inputbox"', $row->config_showAuthor);
	$lists['showCreateDate'] = mosHTML::yesnoRadioList('config_showCreateDate', 'class="inputbox"', $row->config_showCreateDate);
	$lists['showModifyDate'] = mosHTML::yesnoRadioList('config_showModifyDate', 'class="inputbox"', $row->config_showModifyDate);
	$lists['hits'] = mosHTML::yesnoRadioList('config_hits', 'class="inputbox"', $row->config_hits);
	$lists['tags'] = mosHTML::yesnoRadioList('config_tags', 'class="inputbox"', $row->config_tags);
	$lists['back_button'] = mosHTML::yesnoRadioList('config_back_button', 'class="inputbox"', $row->config_back_button);
	$lists['item_navigation'] = mosHTML::yesnoRadioList('config_item_navigation', 'class="inputbox"', $row->config_item_navigation);
	$lists['multipage_toc'] = mosHTML::yesnoRadioList('config_multipage_toc', 'class="inputbox"', $row->config_multipage_toc);
	$lists['showPrint'] = mosHTML::yesnoRadioList('config_showPrint', 'class="inputbox"', $row->config_showPrint);
	$lists['showEmail'] = mosHTML::yesnoRadioList('config_showEmail', 'class="inputbox"', $row->config_showEmail);
	$lists['icons'] = mosHTML::yesnoRadioList('config_icons', 'class="inputbox"', $row->config_icons);
	$lists['mtage_base'] = mosHTML::yesnoRadioList('config_mtage_base', 'class="inputbox"', $row->config_mtage_base);
	$lists['config_custom_print'] = mosHTML::yesnoRadioList('config_custom_print', 'class="inputbox"', $row->config_custom_print);
	$lists['config_old_toolbar'] = mosHTML::yesnoRadioList('config_old_toolbar', 'class="inputbox"', $row->config_old_toolbar);
	$global_templates = array(
		mosHTML::makeOption(0, _GLOBAL_TEMPLATES_SYSTEMDIR),
		mosHTML::makeOption(1, _GLOBAL_TEMPLATES_CURTEMPLATE),
	);
	$lists['global_templates'] = mosHTML::selectList($global_templates, 'config_global_templates', 'class="inputbox" size="1"', 'value', 'text', $row->config_global_templates);

	$lists['tpreview'] = mosHTML::yesnoRadioList('config_disable_tpreview', 'class="inputbox"', $row->config_disable_tpreview);

	$locales = array(
		mosHTML::makeOption('ru_RU.utf8', 'ru_RU.utf8'),
		mosHTML::makeOption('russian', 'russian (windows)'),
		mosHTML::makeOption('english', 'english (for windows)'),
		mosHTML::makeOption('az_AZ.utf8', 'az_AZ.utf8'),
		mosHTML::makeOption('ar_EG.utf8', 'ar_EG.utf8'),
		mosHTML::makeOption('ar_LB.utf8', 'ar_LB.utf8'),
		mosHTML::makeOption('eu_ES.utf8', 'eu_ES.utf8'),
		mosHTML::makeOption('bg_BG.utf8', 'bg_BG.utf8'),
		mosHTML::makeOption('ca_ES.utf8', 'ca_ES.utf8'),
		mosHTML::makeOption('zh_CN.utf8', 'zh_CN.utf8'),
		mosHTML::makeOption('zh_TW.utf8', 'zh_TW.utf8'),
		mosHTML::makeOption('hr_HR.utf8', 'hr_HR.utf8'),
		mosHTML::makeOption('cs_CZ.utf8', 'cs_CZ.utf8'),
		mosHTML::makeOption('da_DK.utf8', 'da_DK.utf8'),
		mosHTML::makeOption('nl_NL.utf8', 'nl_NL.utf8'),
		mosHTML::makeOption('et_EE.utf8', 'et_EE.utf8'),
		mosHTML::makeOption('en_GB.utf8', 'en_GB.utf8'),
		mosHTML::makeOption('en_US.utf8', 'en_US.utf8'),
		mosHTML::makeOption('en_AU.utf8', 'en_AU.utf8'),
		mosHTML::makeOption('en_IE.utf8', 'en_IE.utf8'),
		mosHTML::makeOption('fa_IR.utf8', 'fa_IR.utf8'),
		mosHTML::makeOption('fi_FI.utf8', 'fi_FI.utf8'),
		mosHTML::makeOption('fr_FR.utf8', 'fr_FR.utf8'),
		mosHTML::makeOption('gl_ES.utf8', 'gl_ES.utf8'),
		mosHTML::makeOption('de_DE.utf8', 'de_DE.utf8'),
		mosHTML::makeOption('el_GR.utf8', 'el_GR.utf8'),
		mosHTML::makeOption('he_IL.utf8', 'he_IL.utf8'),
		mosHTML::makeOption('hu_HU.utf8', 'hu_HU.utf8'),
		mosHTML::makeOption('is_IS.utf8', 'is_IS.utf8'),
		mosHTML::makeOption('ga_IE.utf8', 'ga_IE.utf8'),
		mosHTML::makeOption('it_IT.utf8', 'it_IT.utf8'),
		mosHTML::makeOption('ja_JP.utf8', 'ja_JP.utf8'),
		mosHTML::makeOption('ko_KR.utf8', 'ko_KR.utf8'),
		mosHTML::makeOption('lv_LV.utf8', 'lv_LV.utf8'),
		mosHTML::makeOption('lt_LT.utf8', 'lt_LT.utf8'),
		mosHTML::makeOption('mk_MK.utf8', 'mk_MK.utf8'),
		mosHTML::makeOption('ms_MY.utf8', 'ms_MY.utf8'),
		mosHTML::makeOption('no_NO.utf8', 'no_NO.utf8'),
		mosHTML::makeOption('nn_NO.utf8', 'nn_NO.utf8'),
		mosHTML::makeOption('pl_PL.utf8', 'pl_PL.utf8'),
		mosHTML::makeOption('pt_PT.utf8', 'pt_PT.utf8'),
		mosHTML::makeOption('pt_BR.utf8', 'pt_BR.utf8'),
		mosHTML::makeOption('ro_RO.utf8', 'ro_RO.utf8'),
		mosHTML::makeOption('sk_SK.utf8', 'sk_SK.utf8'),
		mosHTML::makeOption('sl_SI.utf8', 'sl_SI.utf8'),
		mosHTML::makeOption('sr_CS.utf8', 'sr_CS.utf8'),
		mosHTML::makeOption('rs_SR.utf8', 'rs_SR.utf8'),
		mosHTML::makeOption('es_ES.utf8', 'es_ES.utf8'),
		mosHTML::makeOption('es_MX.utf8', 'es_MX.utf8'),
		mosHTML::makeOption('sv_SE.utf8', 'sv_SE.utf8'),
		mosHTML::makeOption('sv_FI.utf8', 'sv_FI.utf8'),
		mosHTML::makeOption('ta_IN.utf8', 'ta_IN.utf8'),
		mosHTML::makeOption('tr_TR.utf8', 'tr_TR.utf8'),
		mosHTML::makeOption('uk_UA.utf8', 'uk_UA.utf8'),
		mosHTML::makeOption('vi_VN.utf8', 'vi_VN.utf8'),
		mosHTML::makeOption('wa_BE.utf8', 'wa_BE.utf8')
	);
	$lists['locale'] = mosHTML::selectList($locales, 'config_locale', 'class="selectbox" size="1" dir="ltr"', 'value', 'text', $row->config_locale);

	// включение кода безопасности для доступа к панели управления
	$lists['config_enable_admin_secure_code'] = mosHTML::yesnoRadioList('config_enable_admin_secure_code', 'class="inputbox"', $row->config_enable_admin_secure_code);

	// режим редиректа при включенном коде безопасноти
	$redirect_r = array(
		mosHTML::makeOption(0, 'index.php'),
		mosHTML::makeOption(1, _ADMIN_REDIRECT_PAGE)
	);
	$lists['config_admin_redirect_options'] = mosHTML::RadioList($redirect_r, 'config_admin_redirect_options', 'class="inputbox"', $row->config_admin_redirect_options, 'value', 'text');

	// обработчики кэширования
	$cache_handler = array();
	$cache_handler[] = mosHTML::makeOption('file', 'file');
	if(function_exists('eaccelerator_get')) $cache_handler[] = mosHTML::makeOption('eaccelerator', 'eAccelerator');
	if(extension_loaded('apc')) $cache_handler[] = mosHTML::makeOption('apc', 'APC');
	if(class_exists('Memcache')) $cache_handler[] = mosHTML::makeOption('memcache', 'Memcache');
	if(function_exists('xcache_set')) $cache_handler[] = mosHTML::makeOption('xcache', 'Xcache');

	?>
<script>
	function showHideMemCacheSettings() {
		if (document.getElementById("config_cache_handler").value != "memcache") {
			document.getElementById("memcache_persist").style.display = "none";
			document.getElementById("memcache_compress").style.display = "none";
			document.getElementById("memcache_server").style.display = "none";
		}
		else {
			document.getElementById("memcache_persist").style.display = "";
			document.getElementById("memcache_compress").style.display = "";
			document.getElementById("memcache_server").style.display = "";
		}
	}
</script>

<?php
	// оработчик кэширования
	$lists['cache_handler'] = mosHTML::selectList($cache_handler, 'config_cache_handler', 'class="inputbox" id="config_cache_handler" onchange="showHideMemCacheSettings();" ', 'value', 'text', $row->config_cache_handler);

	if(!empty($row->config_memcache_settings) && !is_array($row->config_memcache_settings)){
		$row->config_memcache_settings = unserialize(stripslashes($row->config_memcache_settings));
	}
	$lists['memcache_persist'] = mosHTML::yesnoRadioList('config_memcache_persistent', 'class="inputbox"', $row->config_memcache_persistent);
	$lists['memcache_compress'] = mosHTML::yesnoRadioList('config_memcache_compression', 'class="inputbox"', $row->config_memcache_compression);

	// использование неопубликованных мамботов
	$lists['config_use_unpublished_mambots'] = mosHTML::yesnoRadioList('config_use_unpublished_mambots', 'class="inputbox"', $row->config_use_unpublished_mambots);

	// отключение syndicate
	$lists['syndicate_off'] = mosHTML::yesnoRadioList('config_syndicate_off', 'class="inputbox"', $row->config_syndicate_off);

	// список шаблонов панели управления
	$titlelength = 20;
	$admin_template_path = JPATH_BASE . DS . 'administrator' . DS . 'templates';
	$templatefolder = @dir($admin_template_path);

	$admin_templates = array();
	$admin_templates[] = mosHTML::makeOption('...', _O_OTHER); // параметр по умолчанию - позволяет использовать стандартный способ определения шаблона
	if($templatefolder){
		while($templatefile = $templatefolder->read()){
			if($templatefile != "." && $templatefile != ".." && $templatefile != ".svn" && is_dir($admin_template_path . DS . $templatefile)){
				if(strlen($templatefile) > $titlelength){
					$templatename = substr($templatefile, 0, $titlelength - 3);
					$templatename .= "...";
				} else{
					$templatename = $templatefile;
				}
				$admin_templates[] = mosHTML::makeOption($templatefile, $templatename);
			}
		}
		$templatefolder->close();
	}
	sort($admin_templates);
	$lists['config_admin_template'] = mosHTML::selectList($admin_templates, 'config_admin_template', 'class="inputbox" ', 'value', 'text', $row->config_admin_template);

	// режим сортировки содержимого в панели управления
	$order_list = array(
		mosHTML::makeOption(0, _ORDER_BY_NAME),
		mosHTML::makeOption(1, _ORDER_BY_HEADERS),
		mosHTML::makeOption(2, _ORDER_BY_DATE_CR),
		mosHTML::makeOption(3, _ORDER_BY_DATE_MOD),
		mosHTML::makeOption(4, _ORDER_BY_ID),
		mosHTML::makeOption(5, _ORDER_BY_HITS)
	);
	$lists['admin_content_order_by'] = mosHTML::selectList($order_list, 'config_admin_content_order_by', 'class="inputbox" size="1"', 'value', 'text', $row->config_admin_content_order_by);

	$order_sort_list = array(
		mosHTML::makeOption(1, _SORT_ASC),
		mosHTML::makeOption(0, _SORT_DESC)
	);
	$lists['admin_content_order_sort'] = mosHTML::selectList($order_sort_list, 'config_admin_content_order_sort', 'class="inputbox" size="1"', 'value', 'text', $row->config_admin_content_order_sort);

	// блокировка компонентов
	$lists['components_access'] = mosHTML::yesnoRadioList('config_components_access', 'class="inputbox"', $row->config_components_access);

	// использование мамботов удаления содержимого
	$lists['config_use_content_delete_mambots'] = mosHTML::yesnoRadioList('config_use_content_delete_mambots', 'class="inputbox"', $row->config_use_content_delete_mambots);
	// использование редактирования смодержимого
	$lists['config_use_content_edit_mambots'] = mosHTML::yesnoRadioList('config_use_content_edit_mambots', 'class="inputbox"', $row->config_use_content_edit_mambots);
	// использование мамботов сохранения содержимого
	$lists['config_use_content_save_mambots'] = mosHTML::yesnoRadioList('config_use_content_save_mambots', 'class="inputbox"', $row->config_use_content_save_mambots);

	HTML_config::showconfig($row, $lists, $option);
}

/**
 * Сохранение конфигурации
 */
function saveconfig($task){
	global $database, $mosConfig_password, $mosConfig_session_type;
	josSpoofCheck();

	$row = new JConfig();
	if(!$row->bind($_POST)){
		mosRedirect('index2.php', $row->getError());
	}

	// if Session Authentication Type changed, delete all old Frontend sessions only - which used old Authentication Type
	if($mosConfig_session_type != $row->config_session_type){
		$past = time();
		$query = "DELETE FROM #__session WHERE time < " . $database->Quote($past) . " AND ( ( guest = 1 AND userid = 0 ) OR ( guest = 0 AND gid > 0 ) )";
		$database->setQuery($query);
		$database->query();
	}

	$server_time = date('O') / 100;
	$offset = $_POST['config_offset_user'] - $server_time;
	$row->config_offset = $offset;

	//override any possible database password change
	$row->config_password = $mosConfig_password;

	// handling of special characters
	$row->config_sitename = htmlspecialchars($row->config_sitename, ENT_QUOTES);

	// handling of quotes (double and single) and amp characters
	// htmlspecialchars not used to preserve ability to insert other html characters
	$row->config_offline_message = ampReplace($row->config_offline_message);
	$row->config_offline_message = str_replace('"', '&quot;', $row->config_offline_message);
	$row->config_offline_message = str_replace("'", '&#039;', $row->config_offline_message);

	// handling of quotes (double and single) and amp characters
	// htmlspecialchars not used to preserve ability to insert other html characters
	$row->config_error_message = ampReplace($row->config_error_message);
	$row->config_error_message = str_replace('"', '&quot;', $row->config_error_message);
	$row->config_error_message = str_replace("'", '&#039;', $row->config_error_message);

	// ключ кэша
	$row->config_cache_key = time();

	if($row->config_joomlaxplorer_dir == $row->config_absolute_path) $row->config_joomlaxplorer_dir = 0;

	$config = "<?php \n";

	$config .= $row->getVarText();
	$config .= "setlocale (LC_TIME, \$mosConfig_locale);\n";
	$config .= '?>';

	$fname = JPATH_BASE . '/configuration.php';

	$enable_write = intval(mosGetParam($_POST, 'enable_write', 0));
	$oldperms = fileperms($fname);
	if($enable_write){
		@chmod($fname, $oldperms | 0222);
	}

	if($fp = fopen($fname, 'w')){
		fputs($fp, $config, strlen($config));
		fclose($fp);
		if($enable_write){
			@chmod($fname, $oldperms);
		} else{
			if(mosGetParam($_POST, 'disable_write', 0)) @chmod($fname, $oldperms & 0777555);
		} // if

		$msg = _CONFIGURATION_IS_UPDATED;

		// apply file and directory permissions if requested by user
		$applyFilePerms = mosGetParam($_POST, 'applyFilePerms', 0) && $row->config_fileperms != '';
		$applyDirPerms = mosGetParam($_POST, 'applyDirPerms', 0) && $row->config_dirperms != '';
		if($applyFilePerms || $applyDirPerms){
			$mosrootfiles = array(JADMIN_BASE, 'cache', 'components', 'images', 'language', 'mambots', 'media', 'modules', 'templates', 'configuration.php');
			$filemode = null;
			if($applyFilePerms){
				$filemode = octdec($row->config_fileperms);
			}
			$dirmode = null;
			if($applyDirPerms){
				$dirmode = octdec($row->config_dirperms);
			}
			foreach($mosrootfiles as $file){
				mosChmodRecursive(JPATH_BASE . '/' . $file, $filemode, $dirmode);
			}
		} // if

		switch($task){
			case 'apply':
				mosRedirect('index2.php?option=com_config&hidemainmenu=1', $msg);
				break;
			case 'save':
			default:
				mosRedirect('index2.php', $msg);
				break;
		}
	} else{
		if($enable_write){
			@chmod($fname, $oldperms);
		}
		mosRedirect('index2.php', _CANNOT_OPEN_CONF_FILE);
	}
}