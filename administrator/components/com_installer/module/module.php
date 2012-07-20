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

require_once ($mainframe->getPath('installer_html', 'module'));
require_once ($mainframe->getPath('installer_class', 'module'));

switch($task){
	case 'remove':
		removeElement($client);
		js_menu_cache_clear();
		break;

	default:
		showInstalledModules($option);
		js_menu_cache_clear();
		break;
}


/**
 * @param
 */
function removeElement($client){
	josSpoofCheck(null, null, 'request');
	$cid = mosGetParam($_REQUEST, 'cid', array(0));
	if(!is_array($cid)){
		$cid = array(0);
	}

	$installer = new mosInstallerModule();
	$result = false;
	if($cid[0]){
		$result = $installer->uninstall($cid[0], $option, $client);
	}

	$msg = $installer->getError();

	mosRedirect($installer->returnTo('com_installer', 'module', $client), $result ? _DELETE_SUCCESS . ' ' . $msg : _UNSUCCESS . ' ' . $msg);
}

function showInstalledModules($_option){
	$database = database::getInstance();

	$filter = mosGetParam($_POST, 'filter', '');
	$select[] = mosHTML::makeOption('', _ALL);
	$select[] = mosHTML::makeOption('0', _SITE_MODULES);
	$select[] = mosHTML::makeOption('1', _ADMIN_MODULES);
	$lists['filter'] = mosHTML::selectList($select, 'filter', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter);
	if($filter == null){
		$and = '';
	} else
		if(!$filter){
			$and = "\n AND client_id = 0";
		} else
			if($filter){
				$and = "\n AND client_id = 1";
			}

	$query = "SELECT id, module, client_id FROM #__modules WHERE module LIKE 'mod_%' AND iscore='0'" . $and . " GROUP BY module, client_id ORDER BY client_id, module";
	$database->setQuery($query);
	$rows = $database->loadObjectList();

	$n = count($rows);
	for($i = 0; $i < $n; $i++){
		$row = &$rows[$i];

		// path to module directory
		if($row->client_id == "1"){
			$moduleBaseDir = mosPathName(mosPathName(JPATH_BASE) . JADMIN_BASE . DS . 'modules');
		} else{
			$moduleBaseDir = mosPathName(mosPathName(JPATH_BASE) . 'modules');
		}

		// xml file for module
		$xmlfile = $moduleBaseDir . DS . $row->module . ".xml";

		if(file_exists($xmlfile)){
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors(true);
			if(!$xmlDoc->loadXML($xmlfile, false, true)){
				continue;
			}

			$root = &$xmlDoc->documentElement;

			if($root->getTagName() != 'mosinstall'){
				continue;
			}
			if($root->getAttribute("type") != "module"){
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
			unset($xmlDoc, $row);
		}
	}
	HTML_module::showInstalledModules($rows, $_option, $xmlfile, $lists);
}