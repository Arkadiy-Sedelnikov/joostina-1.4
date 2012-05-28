<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// Установка флага, что это - родительский файл
define('_VALID_MOS',1);
// корень файлов
define('JPATH_BASE', dirname(__FILE__) );
// разделитель каталогов
define('DS', DIRECTORY_SEPARATOR );

// для совместимости
$mosConfig_absolute_path = JPATH_BASE;

// подключение файла эмуляции отключения регистрации глобальных переменных
(ini_get('register_globals') == 1) ? require_once (JPATH_BASE.DS.'includes'.DS.'globals.php') : null;

// подключение файла конфигурации
require_once (JPATH_BASE.DS.'configuration.php');

// live_site
define('JPATH_SITE', $mosConfig_live_site );

// подключение SEF
require_once (JPATH_BASE . DS . 'includes' . DS . 'sef.php');
JSef::run($mosConfig_sef, $mosConfig_com_frontpage_clear);

// SSL check - $http_host returns <live site url>:<port number if it is 443>
$http_host = explode(':',$_SERVER['HTTP_HOST']);
if((!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' || isset($http_host[1]) && $http_host[1] == 443) && substr($mosConfig_live_site,0,8) != 'https://') {
	$mosConfig_live_site = 'https://'.substr($mosConfig_live_site,7);
}

// подключение главного файла - ядра системы
require_once (JPATH_BASE.DS.'includes'.DS.'joostina.php');

// отображение состояния выключенного сайта
if($mosConfig_offline == 1) {
	require (JPATH_BASE.'/templates/system/offline.php');
}

// проверяем, разрешено ли использование системных мамботов
if($mosConfig_mmb_system_off == 0) {
	$_MAMBOTS->loadBotGroup('system');
	// триггер событий onStart
	$_MAMBOTS->trigger('onStart');
}

require_once (JPATH_BASE.DS.'includes'.DS.'frontend.php');

// запрос ожидаемых аргументов url (или формы)
$option		= strtolower(strval(mosGetParam($_REQUEST,'option')));
$Itemid		= intval(mosGetParam($_REQUEST,'Itemid',0));
$no_html	= intval(mosGetParam($_REQUEST,'no_html',0));
$act		= strval(mosGetParam($_REQUEST,'act',''));
$pop		= intval(mosGetParam($_GET,'pop'));
$page		= intval(mosGetParam($_GET,'page'));

$print = false;
if($pop=='1' && $page==0) $print = true;

// главное окно рабочего компонента API, для взаимодействия многих 'ядер'
//$mainframe = new mosMainFrame($database,$option,'.');
$mainframe = mosMainFrame::getInstance();

if($mosConfig_no_session_front == 0) {
	$mainframe->initSession();
}

// триггер событий onAfterStart
if($mosConfig_mmb_system_off == 0) {
	$_MAMBOTS->trigger('onAfterStart');
}

$my = $mainframe->getUser();

$gid = intval($my->gid);
// patch to lessen the impact on templates
if($option == 'search') {
	$option = 'com_search';
}

// загрузка файла русского языка по умолчанию
$mosConfig_lang = ($mosConfig_lang == '') ? 'russian' : $mosConfig_lang;
$mainframe->set('lang', $mosConfig_lang);
include_once($mainframe->getLangFile());

if($option == 'login') {
	$mainframe->login();
	mosRedirect('index.php');
} elseif($option == 'logout') {
	$mainframe->logout();
	mosRedirect('index.php');
}

$cur_template = $mainframe->getTemplate();
define('JTEMPLATE', $cur_template );

// подключаем визуальный редактор
require_once (JPATH_BASE . '/includes/editor.php');

ob_start();

if($path = $mainframe->getPath('front')) {
	$task = strval(mosGetParam($_REQUEST,'task',''));
	$ret = mosMenuCheck($Itemid,$option,$task,$gid,$mainframe);
	if($ret) {
		//Подключаем язык компонента
		if($mainframe->getLangFile($option)) {
			include_once($mainframe->getLangFile($option));
		}
		//$mainframe->addLib('mylib');
		require_once ($path);
	} else {
		mosNotAuth();
	}
} else {
	header("HTTP/1.0 404 Not Found");
	echo _NOT_EXIST;
}
$_MOS_OPTION['buffer'] = ob_get_contents();

ob_end_clean();

global $mosConfig_custom_print;

// печать страницы
if($print) {
	$cpex = 0;
	if($mosConfig_custom_print) {
		$cust_print_file = JPATH_BASE.'/templates/'.$cur_template.'/html/print.php';
		if(file_exists($cust_print_file)) {
			ob_start();
			include($cust_print_file);
			$_MOS_OPTION['buffer'] = ob_get_contents();
			ob_end_clean();
			$cpex = 1;
		}
	}
	if(!$cpex) {
		$mainframe->addCSS($mosConfig_live_site.'/templates/css/print.css');
		$mainframe->addJS($mosConfig_live_site.'/includes/js/print/print.js');

		$pg_link	= str_replace(array('&pop=1','&page=0'),'',$_SERVER['REQUEST_URI']);
		$pg_link	= str_replace('index2.php','index.php',$pg_link);
		$pg_link = ltrim($pg_link,'/');

		$_MOS_OPTION['buffer'] = '<div class="logo">'. $mosConfig_sitename .'</div><div id="main">'.$_MOS_OPTION['buffer']."\n</div>\n<div id=\"ju_foo\">"._PRINT_PAGE_LINK." :<br /><i>".sefRelToAbs($pg_link)."</i><br /><br />&copy; ".$mosConfig_sitename.",&nbsp;".date('Y').'</div>';
	}
}else {
	$mainframe->addCSS($mosConfig_live_site.'/templates/'.$cur_template.'/css/template_css.css');
}

// подключение js библиотеки системы
if($my->id || $mainframe->get('joomlaJavascript')) {
	$mainframe->addJS($mosConfig_live_site.'/includes/js/joomla.javascript.js');
}

initGzip();
header('Content-type: text/html; charset=UTF-8');
/*
// при активном кэшировании отправим браузеру более "правильные" заголовки
if(!$mosConfig_caching) { // не кэшируется
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0',false);
	header('Pragma: no-cache');
} else { // кэшируется
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	// 60*60=3600 - использования кэширования на 1 час
	header('Expires: '.gmdate('D, d M Y H:i:s',time() + 3600).' GMT');
	header('Cache-Control: max-age=3600');
}*/

// отображение состояния выключенного сайта при входе админа
if(defined('_ADMIN_OFFLINE')) {
	include (JPATH_BASE.'/templates/system/offlinebar.php');
}

// старт основного HTML
if($no_html == 0) {
	$customIndex2 = 'templates/'.JTEMPLATE.'/index2.php';
	if(file_exists($customIndex2)) {
		require ($customIndex2);
	} else {
		// требуется для отделения номера ISO от константы  _ISO языкового файла языка
		$iso = explode('=',_ISO);
		// пролог xml
		echo '<?xml version="1.0" encoding="'.$iso[1].'"?'.'>';
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="shortcut icon" href="<?php echo $mosConfig_live_site; ?>/images/favicon.ico" />
		<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
				<?php echo $mainframe->getHead(); ?>
	</head>
	<body class="contentpane">
				<?php mosMainBody(); ?>
	</body>
</html>
		<?php
	}
} else {
	mosMainBody();
}
doGzip();