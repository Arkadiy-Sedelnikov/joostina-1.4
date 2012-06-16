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

// ensure user has access to this function
if(!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_poll'))){
	mosRedirect('index2.php', _NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));
require_once ($mainframe->getPath('class'));

$cid = josGetArrayInts('cid');

switch($task){
	case 'new':
		editPoll(0, $option);
		break;

	case 'edit':
		editPoll(intval($cid[0]), $option);
		break;

	case 'editA':
		editPoll($id, $option);
		break;

	case 'save':
		savePoll($option);
		break;

	case 'remove':
		removePoll($cid, $option);
		break;

	case 'publish':
		publishPolls($cid, 1, $option);
		break;

	case 'unpublish':
		publishPolls($cid, 0, $option);
		break;

	case 'cancel':
		cancelPoll($option);
		break;

	default:
		showPolls($option);
		break;
}

function showPolls($option){
	global $mosConfig_list_limit;
	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));

	$query = "SELECT COUNT(*)" . "\n FROM #__polls";
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once (JPATH_BASE . '/' . JADMIN_BASE . '/includes/pageNavigation.php');
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$query = "SELECT m.*, u.name AS editor, COUNT(d.id) AS numoptions FROM #__polls AS m LEFT JOIN #__users AS u ON u.id = m.checked_out LEFT JOIN #__poll_data AS d ON d.pollid = m.id AND d.text != ''" . "\n GROUP BY m.id";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	HTML_poll::showPolls($rows, $pageNav, $option);
}

function editPoll($uid = 0, $option = 'com_poll'){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$row = new mosPoll($database);
	// load the row from the db table
	$row->load((int)$uid);

	// fail if checked out not by 'me'
	if($row->isCheckedOut($my->id)){
		mosRedirect('index2.php?option=' . $option, $row->title . ' - ' . _POLL_IS_BEING_EDITED_BY_ADMIN);
	}

	$options = array();

	if($uid){
		$row->checkout($my->id);
		$query = "SELECT id, text FROM #__poll_data WHERE pollid = " . (int)$uid . " ORDER BY id";
		$database->setQuery($query);
		$options = $database->loadObjectList();
	} else{
		$row->lag = 3600 * 24;
		$row->published = 1;
	}

	// get selected pages
	if($uid){
		$query = "SELECT menuid AS value FROM #__poll_menu WHERE pollid = " . (int)$row->id;
		$database->setQuery($query);
		$lookup = $database->loadObjectList();
	} else{
		$lookup = array(mosHTML::makeOption(0, 'All'));
	}

	// build the html select list
	$lists['select'] = mosAdminMenus::MenuLinks($lookup, 1, 1);

	// build the html select list for published
	$lists['published'] = mosAdminMenus::Published($row);

	HTML_poll::editPoll($row, $options, $lists);
}

function savePoll($option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();
	josSpoofCheck();
	// save the poll parent information
	$row = new mosPoll($database);
	if(!$row->bind($_POST)){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	$isNew = ($row->id == 0);

	if(!$row->check()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(!$row->store()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	// save the poll options
	$options = mosGetParam($_POST, 'polloption', array());

	foreach($options as $i => $text){
		if(!get_magic_quotes_gpc()){
			// The poll module has always been this way, so we'll just stick with that and add
			// additional backslashes if needed. They will be stripped upon display
			$text = addslashes($text);
		}
		if($isNew){
			$query = "INSERT INTO #__poll_data ( pollid, text ) VALUES ( " . (int)$row->id . ", " . $database->Quote($text) . " )";
			$database->setQuery($query);
			$database->query();
		} else{
			$query = "UPDATE #__poll_data SET text = " . $database->Quote($text) . " WHERE id = " . (int)$i . " AND pollid = " . (int)$row->id;
			$database->setQuery($query);
			$database->query();
		}
	}

	// update the menu visibility
	$selections = mosGetParam($_POST, 'selections', array());

	$query = "DELETE FROM #__poll_menu WHERE pollid = " . (int)$row->id;
	$database->setQuery($query);
	$database->query();

	for($i = 0, $n = count($selections); $i < $n; $i++){
		$query = "INSERT INTO #__poll_menu SET pollid = " . (int)$row->id . ", menuid = " . (int)$selections[$i];
		$database->setQuery($query);
		$database->query();
	}

	mosRedirect('index2.php?option=' . $option);
}

function removePoll($cid, $option){
	$database = database::getInstance();
	josSpoofCheck();
	$msg = '';
	for($i = 0, $n = count($cid); $i < $n; $i++){
		$poll = new mosPoll($database);
		if(!$poll->delete($cid[$i])){
			$msg .= $poll->getError();
		}
	}
	mosRedirect('index2.php?option=' . $option . '&mosmsg=' . $msg);
}

/**
 * Publishes or Unpublishes one or more records
 * @param array An array of unique category id numbers
 * @param integer 0 if unpublishing, 1 if publishing
 * @param string The current url option
 */
function publishPolls($cid = null, $publish = 1, $option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();
	josSpoofCheck();
	if(!is_array($cid) || count($cid) < 1){
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('" . _CHOOSE_OBJECT_FOR . " $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts($cid);
	$cids = 'id=' . implode(' OR id=', $cid);

	$query = "UPDATE #__polls SET published = " . intval($publish) . " WHERE ( $cids )" . " AND ( checked_out = 0 OR ( checked_out = " . (int)$my->id . " ) )";
	$database->setQuery($query);
	if(!$database->query()){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(count($cid) == 1){
		$row = new mosPoll($database);
		$row->checkin($cid[0]);
	}
	mosRedirect('index2.php?option=' . $option);
}

function cancelPoll($option){
	$database = database::getInstance();
	josSpoofCheck();
	$row = new mosPoll($database);
	$row->bind($_POST);
	$row->checkin();
	mosRedirect('index2.php?option=' . $option);
}