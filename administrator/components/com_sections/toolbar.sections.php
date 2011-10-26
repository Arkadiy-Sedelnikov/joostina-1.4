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
	case 'edit':
	case 'editA':
		TOOLBAR_sections::_EDIT();
		break;

	case 'masadd':
		TOOLBAR_sections::_MASADD();
		break;

	case 'massave':
		TOOLBAR_sections::_MASNEW();
		break;


	case 'copyselect':
		TOOLBAR_sections::_COPY();
		break;

	default:
		TOOLBAR_sections::_DEFAULT();
		break;
}