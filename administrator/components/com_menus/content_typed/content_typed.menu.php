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

mosAdminMenus::menuItem($type);

$scid = mosGetParam($_POST,'scid','');

switch($task) {
	case 'content_typed':
		// this is the new item, ie, the same name as the menu `type`
		content_typed_menu::edit(0,$menutype,$option,$menu);
		break;

	case 'edit':
		content_typed_menu::edit($cid[0],$menutype,$option,$menu);
		break;

	case 'save':
	case 'apply':
	case 'save_and_new':
		saveMenu($option,$task);
		break;

	case 'redirect':
		content_typed_menu::redirect($scid);
		break;
}