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

// ensure user has access to this function
if(!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_contact'))){
	mosRedirect('index2.php', _NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));

switch($task){

	case 'save':
		saveSyndicate($option);
		break;

	case 'cancel':
		cancelSyndicate();
		break;

	default:
		showSyndicate($option);
		break;
}

/**
 * List the records
 * @param string The current GET/POST option
 */
function showSyndicate($option){
	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	$query = "SELECT a.id FROM #__components AS a WHERE ( a.admin_menu_link = 'option=com_syndicate' OR a.admin_menu_link = 'option=com_syndicate&hidemainmenu=1' ) AND a.option = 'com_syndicate'";
	$database->setQuery($query);
	$id = $database->loadResult();

	// load the row from the db table
	$row = new mosComponent($database);
	$row->load($id);

	// get params definitions
	$params = new mosParameters($row->params, $mainframe->getPath('com_xml', $row->option), 'component');

	HTML_syndicate::settings($option, $params, $id);
}

/**
 * Saves the record from an edit form submit
 * @param string The current GET/POST option
 */
function saveSyndicate($option){
	$database = database::getInstance();
	josSpoofCheck();
	$params = mosGetParam($_POST, 'params', '');
	if(is_array($params)){
		$txt = array();
		foreach($params as $k => $v){
			$txt[] = "$k=$v";
		}
		$_POST['params'] = mosParameters::textareaHandling($txt);
	}

	$id = intval(mosGetParam($_POST, 'id', '17'));
	$row = new mosComponent($database);
	$row->load($id);

	if(!$row->bind($_POST)){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(!$row->check()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$row->store()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$msg = _E_ITEM_SAVED;
	mosRedirect('index2.php?option=' . $option, $msg);
}

/**
 * Cancels editing and checks in the record
 */
function cancelSyndicate(){
	mosRedirect('index2.php');
}