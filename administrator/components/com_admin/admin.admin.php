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

require_once ($mainframe->getPath('admin_html'));

switch($task){

	// очистка кэша содержимого
	case 'clean_cache':
		mosCache::cleanCache('com_boss');
		mosRedirect('index2.php', _CACHE_CLEAR_CONTENT);
		break;

	// очистка всего кэша
	case 'clean_all_cache':
		mosCache::cleanCache();
		mosCache::cleanCache('page');
		mosRedirect('index2.php', _CACHE_CLEAR_ALL);
		break;

	case 'redirect':
		$goto = strval(strtolower(mosGetParam($_REQUEST, 'link')));
		if($goto == 'null'){
			$msg = _COM_ADMIN_NON_LINK_OBJ;
			mosRedirect('index2.php?option=com_admin&task=listcomponents', $msg);
			exit();
		}
		$goto = str_replace("'", '', $goto);
		mosRedirect($goto);
		break;

	case 'listcomponents':
		HTML_admin_misc::ListComponents();
		break;

	case 'sysinfo':
		$version = new joomlaVersion();
		HTML_admin_misc::system_info($version, $option);
		break;

	case 'changelog':
		HTML_admin_misc::changelog();
		break;

	case 'help':
		HTML_admin_misc::help();
		break;

	case 'version':
		HTML_admin_misc::version();
		break;

	case 'preview':
		HTML_admin_misc::preview();
		break;

	case 'preview2':
		HTML_admin_misc::preview(1);
		break;

	case 'cpanel':
	default:
		HTML_admin_misc::controlPanel();
		break;
}