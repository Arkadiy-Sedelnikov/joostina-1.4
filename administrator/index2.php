<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// Установка флага родительского файла
define('_JLINDEX', 1);

// корень файлов
define('_JLPATH_ROOT',dirname(dirname(__FILE__)));

// подключение основных глобальных переменных
require_once _JLPATH_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'defines.php';

define('JPATH_BASE', dirname(dirname(__FILE__)));

(ini_get('register_globals') == 1) ? require_once (JPATH_BASE . DS . 'includes' . DS . 'globals.php') : null;
require_once (JPATH_BASE . DS . 'configuration.php');

// для совместимости
$mosConfig_absolute_path = JPATH_BASE;

// SSL проверка  - $http_host returns <live site url>:<port number if it is 443>
$http_host = explode(':', $_SERVER['HTTP_HOST']);
if((!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' || isset($http_host[1]) && $http_host[1] == 443) && substr($mosConfig_live_site, 0, 8) != 'https://'){
	$mosConfig_live_site = 'https://' . substr($mosConfig_live_site, 7);
}

// live_site
define('JPATH_SITE', $mosConfig_live_site);
if(!defined('IS_ADMIN')) define('IS_ADMIN', 1);
// подключаем ядро
require_once (JPATH_BASE . DS . 'includes' . DS . 'joostina.php');


// работа с сессиями начинается до создания главного объекта взаимодействия с ядром
session_name(md5(JPATH_SITE));
session_start();

header('Content-type: text/html; charset=UTF-8');

// получение основных параметров
$option = strval(strtolower(mosGetParam($_REQUEST, 'option', '')));
$task = strval(mosGetParam($_REQUEST, 'task', ''));
$act = strtolower(mosGetParam($_REQUEST, 'act', ''));
$section = mosGetParam($_REQUEST, 'section', '');
$no_html = intval(mosGetParam($_REQUEST, 'no_html', 0));
$id = intval(mosGetParam($_REQUEST, 'id', 0));

// mainframe - основная рабочая среда API, осуществляет взаимодействие с 'ядром'
$mainframe = mosMainFrame::getInstance(true);

// объект работы с базой данных
$database = database::getInstance();

// класс работы с правами пользователей
$acl = gacl::getInstance();

// установка языка систему
$mainframe->set('lang', $mosConfig_lang);

// получаем название шаблона для панели управления
$cur_template = $mainframe->getTemplate();
define('JTEMPLATE', $cur_template);

require_once($mainframe->getLangFile());
require_once($mainframe->getLangFile('administrator'));

require_once (_JLPATH_ADMINISTRATOR . DS . 'includes' . DS . 'admin.php');

// запуск сессий панели управления
$my = $mainframe->initSessionAdmin($option, $task);

// установка параметра overlib
$mainframe->set('loadOverlib', false);

// страница панели управления по умолчанию
if($option == ''){
	$option = 'com_admin';
}

if($mosConfig_mmb_system_off == 0){
	$_MAMBOTS = mosMambotHandler::getInstance();
	$_MAMBOTS->loadBotGroup('admin');
	$_MAMBOTS->trigger('onAfterAdminStart');
}

// инициализация редактора
$mainframe->set('allow_wysiwyg', 1);
require_once (JPATH_BASE . '/includes/editor.php');

ob_start();
if($path = $mainframe->getPath('admin')){
	//Подключаем язык компонента
	if($mainframe->getLangFile($option)){
		include_once($mainframe->getLangFile($option));
	}
	require_once ($path);
} else{
	?>
<img src="<?php echo JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE; ?>/images/ico/error.png" border="0" alt="Joostina!"/>
<?php
}

$_MOS_OPTION['buffer'] = ob_get_contents();
ob_end_clean();

if($mosConfig_mmb_system_off == 0){
	$_MAMBOTS->trigger('onBeforeAdminOutput');
}

initGzip();

// начало вывода html
if($no_html == 0){
	// загрузка файла шаблона
	if(!file_exists(JPATH_BASE . DS . JADMIN_BASE . DS . 'templates' . DS . JTEMPLATE . DS . 'index.php')){
		echo _TEMPLATE_NOT_FOUND . ': ' . JTEMPLATE;
	} else{
		//Подключаем язык шаблона
		if($mainframe->getLangFile('tmpl_' . JTEMPLATE)){
			include_once($mainframe->getLangFile('tmpl_' . JTEMPLATE));
		}
		require_once (JPATH_BASE . DS . JADMIN_BASE . DS . 'templates' . DS . JTEMPLATE . DS . 'index.php');
	}
} else{
	mosMainBody_Admin();
}

// информация отладки, число запросов в БД
if($mosConfig_debug){
	jd_get();
}

// восстановление сессий
if($task == 'save' || $task == 'apply' || $task == 'save_and_new'){
	$mainframe->initSessionAdmin($option, '');
}

doGzip();