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

if(!$acl->acl_check('administration', 'install', 'users', $my->usertype, $element . 's', 'all')){
	mosRedirect('index2.php', _NOT_AUTH);
}

require_once ($mainframe->getPath('installer_html', 'mambot'));
require_once ($mainframe->getPath('installer_class', 'mambot'));

switch($task){
	case 'remove':
		removeElement($client);
		js_menu_cache_clear();
		break;

	default:
		showInstalledMambots($option);
		js_menu_cache_clear();
		break;
}

/**
 * @param
 */
function removeElement($client){
	josSpoofCheck(null, null, 'request');
	$cid = mosGetParam($_REQUEST, 'cid', array(0));
	$option = mosGetParam($_REQUEST, 'option', 'com_installer');

	if(!is_array($cid)){
		$cid = array(0);
	}

	$installer = new mosInstallerMambot();
	$result = false;
	if($cid[0]){
		$result = $installer->uninstall($cid[0], $option, $client);
	}

	$msg = $installer->getError();

	mosRedirect($installer->returnTo('com_installer', 'mambot', $client), $result ? _DELETE_SUCCESS . ' ' . $msg : _UNSUCCESS . ' ' . $msg);
}

function showInstalledMambots($_option){
	$database = database::getInstance();

	$query = "SELECT id, name, folder, element, client_id FROM #__mambots WHERE iscore = 0 ORDER BY folder, name";
	$database->setQuery($query);
	$rows = $database->loadObjectList();

	// path to mambot directory
	$mambotBaseDir = mosPathName(mosPathName(JPATH_BASE) . "mambots");

	$id = 0;
	$n = count($rows);
	for($i = 0; $i < $n; $i++){
		$row = $rows[$i];
		// xml file for module
		$xmlfile = $mambotBaseDir . DS . $row->folder . DS . $row->element . ".xml";

		if(file_exists($xmlfile)){
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors(true);
			if(!$xmlDoc->loadXML($xmlfile, false, true)){
				continue;
			}

			$root = $xmlDoc->documentElement;

			if($root->getTagName() != 'mosinstall'){
				continue;
			}
			if($root->getAttribute("type") != "mambot"){
				continue;
			}

			$element = $root->getElementsByPath('creationDate', 1);
			$row->creationdate = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('author', 1);
			$row->author = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('copyright', 1);
			$row->copyright = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('authorEmail', 1);
			$row->authorEmail = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('authorUrl', 1);
			$row->authorUrl = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('version', 1);
			$row->version = $element ? $element->getText() : '';
		}
	}
	HTML_mambot::showInstalledMambots($rows, $_option, $id, $xmlfile);
}