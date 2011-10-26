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

require_once ($mainframe->getPath('toolbar_html'));

switch($task) {
	case 'new':
	case 'new_content_typed':
	case 'new_content_section':
	case 'edit':
	case 'editA':
	case 'edit_content_typed':
		TOOLBAR_content::_EDIT();
		break;

	case 'showarchive':
		TOOLBAR_content::_ARCHIVE();
		break;

	case 'movesect':
		TOOLBAR_content::_MOVE();
		break;

	case 'copy':
		TOOLBAR_content::_COPY();
		break;

	case 'config':
		TOOLBAR_content::_CONFIG();
		break;

	default:
		TOOLBAR_content::_DEFAULT();
		break;
}