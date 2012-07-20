<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

require_once ($mainframe->getPath('admin_html'));
require_once ($mainframe->getPath('installer_class', 'installer'));

// XML library
require_once (JPATH_BASE . '/includes/domit/xml_domit_lite_include.php');

$element = mosGetParam($_REQUEST, 'element', '');
$client = mosGetParam($_REQUEST, 'client', '');
$option = mosGetParam($_REQUEST, 'option', '');
$url = mosGetParam($_REQUEST, 'url', '');

// ensure user has access to this function
if(!$acl->acl_check('administration', 'install', 'users', $my->usertype, $element . 's', 'all')){
	mosRedirect('index2.php', _NOT_AUTH);
}

$path = _JLPATH_ADMINISTRATOR . "/components/com_installer/$element/$element.php";

if(file_exists($path)){
	require $path;
} else{
	echo "[$element] - " . _NO_INSTALLER;
}