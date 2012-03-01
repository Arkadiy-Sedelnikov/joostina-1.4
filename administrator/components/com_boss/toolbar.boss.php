<?php

/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

require_once($mainframe->getPath('toolbar_html'));

$act = mosGetParam($_REQUEST, 'act', "");
$task = mosGetParam($_REQUEST, 'task', "");

switch ($act) {

	case "plugins" :
	case "fieldimage" :
		menuBOSS::delete();
		break;

	case "configuration" :
		menuBOSS::backSave();
		break;

	case "tools" :
	case "csv" :
	case "positions" :
    case "export_import" :
		break;

	case "templates" :
		switch ($task) {
			case "edit_tmpl" :
				menuBOSS::backSave();
				break;
			
			case "edit_tmpl_fields" :
				menuBOSS::edit_tmpl_fields();
				break;

			case "edit_tmpl_source" :
				menuBOSS::edit_tmpl_source();
				break;

			default:
				menuBOSS::delete();
				break;
		}
		break;
	case "contents" :
		switch ($task) {
			case "new" :
			case "edit" :
			case "copy" :
				menuBOSS::backSave();
				break;
			
			default:
				menuBOSS::addEditDeleteCopy();
				break;
		}
		break;
	default:
		switch ($task) {
			case "new" :
			case "edit" :
				menuBOSS::backSave();
				break;

			default:
				menuBOSS::addEditDelete();
				break;
		}
		break;
}