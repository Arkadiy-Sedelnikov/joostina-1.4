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

require_once ($mainframe->getPath('toolbar_html'));

// handle the task
$act = mosGetParam($_REQUEST, 'act', '');
$task = mosGetParam($_REQUEST, 'task', '');

switch($act){
	case 'config':
		switch($task){
			case 'save':
				break;
			case 'apply':
				TOOLBAR_jpack::_CONFIG();
				break;
			case '':
				TOOLBAR_jpack::_CONFIG();
				break;
			default:
				break;
		}
		break;

	case 'db':
		switch($task){
			case 'doBackup':
				TOOLBAR_jpack::_DB_MENU($option);
				break;
			case 'doCheck':
				TOOLBAR_jpack::_DB_MENU($option);
				break;
			case 'doAnalyze':
				TOOLBAR_jpack::_DB_MENU($option);
				break;
			case 'doOptimize':
				TOOLBAR_jpack::_DB_MENU($option);
				break;
			case 'doRepair':
				TOOLBAR_jpack::_DB_MENU($option);
				break;
			default:
				TOOLBAR_jpack::_DB_DEFAULT();
				break;
		}
		break;

	case 'pack':
		TOOLBAR_jpack::_PACK();
		break;

	case 'log':
	case 'def':
		TOOLBAR_jpack::_DEF();
		break;
}