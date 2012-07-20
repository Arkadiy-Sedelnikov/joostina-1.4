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

require_once ($mainframe->getPath('installer_html', 'template'));
require_once ($mainframe->getPath('installer_class', 'template'));

switch($task){
	case 'remove':
		removeElement($client);
		js_menu_cache_clear();
		break;

	default:
		viewTemplates('com_installer', $client);
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

	$installer = new mosInstallerTemplate();
	$result = false;
	if($cid[0]){
		$result = $installer->uninstall($cid[0], $option, $client);
	}

	$msg = $installer->getError();

	mosRedirect($installer->returnTo('com_installer', 'template', $client), $result ? _DELETE_SUCCESS . ' ' . $msg : _UNSUCCESS . ' ' . $msg);
}

/**
 * Compiles a list of installed, version 4.5+ templates
 * Based on xml files found.  If no xml file found the template
 * is ignored
 */
function viewTemplates($option, $client){
	$mainframe = mosMainFrame::getInstance(true);
	$database = database::getInstance();

	$limit = $mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mainframe->getCfg('list_limit'));
	$limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);

	if($client == 'admin'){
		$templateBaseDir = mosPathName(JPATH_BASE . DS . JADMIN_BASE . DS . 'templates');
	} else{
		$templateBaseDir = mosPathName(JPATH_BASE . DS . 'templates');
	}

	$rows = array();
	// Read the template dir to find templates
	$templateDirs = mosReadDirectory($templateBaseDir);

	$id = intval($client == 'admin');
	if($client == 'admin'){
		$query = "SELECT template"
			. "\n FROM #__templates_menu"
			. "\n WHERE client_id = 1"
			. "\n AND menuid = 0";
		$database->setQuery($query);
	} else{
		$query = "SELECT template"
			. "\n FROM #__templates_menu"
			. "\n WHERE client_id = 0"
			. "\n AND menuid = 0";
		$database->setQuery($query);
	}
	$cur_template = $database->loadResult();

	$rowid = 0;
	// Check that the directory contains an xml file
	foreach($templateDirs as $templateDir){
		$dirName = mosPathName($templateBaseDir . $templateDir);
		$xmlFilesInDir = mosReadDirectory($dirName, '.xml$');

		foreach($xmlFilesInDir as $xmlfile){
			// Read the file to see if it's a valid template XML file
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors(true);
			if(!$xmlDoc->loadXML($dirName . $xmlfile, false, true)){
				continue;
			}

			$root = $xmlDoc->documentElement;

			if($root->getTagName() != 'mosinstall'){
				continue;
			}
			if($root->getAttribute('type') != 'template'){
				continue;
			}

			$row = new StdClass();
			$row->id = $rowid;
			$row->directory = $templateDir;
			$element = $root->getElementsByPath('name', 1);
			$row->name = $element->getText();

			$element = $root->getElementsByPath('creationDate', 1);
			$row->creationdate = $element ? $element->getText() : 'Unknown';

			$element = $root->getElementsByPath('author', 1);
			$row->author = $element ? $element->getText() : 'Unknown';

			$element = $root->getElementsByPath('copyright', 1);
			$row->copyright = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('authorEmail', 1);
			$row->authorEmail = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('authorUrl', 1);
			$row->authorUrl = $element ? $element->getText() : '';

			$element = $root->getElementsByPath('version', 1);
			$row->version = $element ? $element->getText() : '';

			// Get info from db
			if($cur_template == $templateDir){
				$row->published = 1;
			} else{
				$row->published = 0;
			}

			$row->checked_out = 0;
			$row->mosname = strtolower(str_replace(' ', '_', $row->name));

			// check if template is assigned
			$query = "SELECT COUNT(*) FROM #__templates_menu WHERE client_id = 0 AND template = " . $database->Quote($row->directory) . "\n AND menuid != 0";
			$database->setQuery($query);
			$row->assigned = $database->loadResult() ? 1 : 0;

			$rows[] = $row;
			$rowid++;
		}
	}

	mosMainFrame::addLib('pagenavigation');
	$pageNav = new mosPageNav(count($rows), $limitstart, $limit);

	$rows = array_slice($rows, $pageNav->limitstart, $pageNav->limit);

	HTML_templates::showTemplates($rows, $pageNav, $option, $client);
}
