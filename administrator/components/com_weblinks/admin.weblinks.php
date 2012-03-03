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
if(!($acl->acl_check('administration','edit','users',$my->usertype,'components','all') | $acl->acl_check('administration','edit','users',$my->usertype,'components','com_weblinks'))) {
	mosRedirect('index2.php',_NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));
require_once ($mainframe->getPath('class'));

$cid = josGetArrayInts('cid');

switch($task) {
	case 'new':
		editWeblink($option,0);
		break;

	case 'edit':
		editWeblink($option,$cid[0]);
		break;

	case 'editA':
		editWeblink($option,$id);
		break;

	case 'save':
		saveWeblink($option);
		break;

	case 'remove':
		removeWeblinks($cid,$option);
		break;

	case 'publish':
		publishWeblinks($cid,1,$option);
		break;

	case 'unpublish':
		publishWeblinks($cid,0,$option);
		break;

	case 'approve':
		break;

	case 'cancel':
		cancelWeblink($option);
		break;

	case 'orderup':
		orderWeblinks(intval($cid[0]),-1,$option);
		break;

	case 'orderdown':
		orderWeblinks(intval($cid[0]),1,$option);
		break;

	default:
		showWeblinks($option);
		break;
}

/**
 * Compiles a list of records
 * @param database A database connector object
 */
function showWeblinks($option) {
	global $$mosConfig_list_limit;
    $mainframe = mosMainFrame::getInstance();
    $database = database::getInstance();

	$catid = intval($mainframe->getUserStateFromRequest("catid{$option}",'catid',0));
	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit",'limit',$mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart",
			'limitstart',0));
	$search = $mainframe->getUserStateFromRequest("search{$option}",'search','');
	if(get_magic_quotes_gpc()) {
		$search = stripslashes($search);
	}

	$where = array();

	if($catid > 0) {
		$where[] = "a.catid = ".(int)$catid;
	}
	if($search) {
		$where[] = "LOWER(a.title) LIKE '%".$database->getEscaped(Jstring::trim(Jstring::strtolower($search))).
				"%'";
	}

	// get the total number of records
	$query = "SELECT COUNT(*)"."\n FROM #__weblinks AS a".(count($where)?"\n WHERE ".
					implode(' AND ',$where):"");
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once (JPATH_BASE.'/'.JADMIN_BASE.'/includes/pageNavigation.php');
	$pageNav = new mosPageNav($total,$limitstart,$limit);

	$query = "SELECT a.*, cc.name AS category, u.name AS editor"."\n FROM #__weblinks AS a".
			"\n LEFT JOIN #__categories AS cc ON cc.id = a.catid"."\n LEFT JOIN #__users AS u ON u.id = a.checked_out".(count
			($where)?"\n WHERE ".implode(' AND ',$where):"")."\n ORDER BY a.catid, a.ordering";
	$database->setQuery($query,$pageNav->limitstart,$pageNav->limit);

	$rows = $database->loadObjectList();
	if($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// build list of categories
	$javascript = 'onchange="document.adminForm.submit();"';
	$lists['catid'] = mosAdminMenus::ComponentCategory('catid',$option,intval($catid),
			$javascript);

	HTML_weblinks::showWeblinks($option,$rows,$lists,$search,$pageNav);
}

/**
 * Compiles information to add or edit
 * @param integer The unique id of the record to edit (0 if new)
 */
function editWeblink($option,$id) {
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    $database = database::getInstance();

	$lists = array();

	$row = new mosWeblink($database);
	// load the row from the db table
	$row->load((int)$id);

	// fail if checked out not by 'me'
	if($row->isCheckedOut($my->id)) {
		mosRedirect('index2.php?option='.$option,$row->title.' - '._MODULE_IS_EDITING_BY_ADMIN);
	}

	if($id) {
		$row->checkout($my->id);
	} else {
		// initialise new record
		$row->published = 1;
		$row->approved = 1;
		$row->order = 0;
		$row->catid = intval(mosGetParam($_POST,'catid',0));
	}

	// build the html select list for ordering
	$query = "SELECT ordering AS value, title AS text"."\n FROM #__weblinks"."\n WHERE catid = ".(int)
			$row->catid."\n ORDER BY ordering";
	$lists['ordering'] = mosAdminMenus::SpecificOrdering($row,$id,$query,1);

	// build list of categories
	$lists['catid'] = mosAdminMenus::ComponentCategory('catid',$option,intval($row->catid));
	// build the html select list
	$lists['published'] = mosHTML::yesnoRadioList('published','class="inputbox"',$row->published);

	$file = JPATH_BASE_ADMIN.'/components/com_weblinks/weblinks_item.xml';
	$params = new mosParameters($row->params,$file,'component');

	HTML_weblinks::editWeblink($row,$lists,$params,$option);
}

/**
 * Saves the record on an edit form submit
 * @param database A database connector object
 */
function saveWeblink($option) {
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    $database = database::getInstance();
	josSpoofCheck();
	$row = new mosWeblink($database);
	if(!$row->bind($_POST)) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	// save params
	$params = mosGetParam($_POST,'params','');
	if(is_array($params)) {
		$txt = array();
		foreach($params as $k => $v) {
			$txt[] = "$k=$v";
		}
		$row->params = implode("\n",$txt);
	}

	$row->date = date('Y-m-d H:i:s');
	if(!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder("catid = ".(int)$row->catid);

	mosRedirect("index2.php?option=$option");
}

/**
 * Deletes one or more records
 * @param array An array of unique category id numbers
 * @param string The current url option
 */
function removeWeblinks($cid,$option) {
	$database = database::getInstance();
	josSpoofCheck();
	if(!is_array($cid) || count($cid) < 1) {
		echo "<script> alert('"._CHOOSE_OBJ_DELETE."'); window.history.go(-1);</script>\n";
		exit;
	}
	if(count($cid)) {
		mosArrayToInts($cid);
		$cids = 'id='.implode(' OR id=',$cid);
		$query = "DELETE FROM #__weblinks"."\n WHERE ( $cids )";
		$database->setQuery($query);
		if(!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg().
					"'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect("index2.php?option=$option");
}

/**
 * Publishes or Unpublishes one or more records
 * @param array An array of unique category id numbers
 * @param integer 0 if unpublishing, 1 if publishing
 * @param string The current url option
 */
function publishWeblinks($cid = null,$publish = 1,$option) {
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    $database = database::getInstance();
	josSpoofCheck();
	if(!is_array($cid) || count($cid) < 1) {
		$action = $publish?'publish':'unpublish';
		echo "<script> alert('"._CHOOSE_OBJECT_FOR." $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts($cid);
	$cids = 'id='.implode(' OR id=',$cid);

	$query = "UPDATE #__weblinks"."\n SET published = ".(int)$publish."\n WHERE ( $cids )".
			"\n AND ( checked_out = 0 OR ( checked_out = ".(int)$my->id." ) )";
	$database->setQuery($query);
	if(!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg().
				"'); window.history.go(-1); </script>\n";
		exit();
	}

	if(count($cid) == 1) {
		$row = new mosWeblink($database);
		$row->checkin($cid[0]);
	}
	mosRedirect("index2.php?option=$option");
}
/**
 * Moves the order of a record
 * @param integer The increment to reorder by
 */
function orderWeblinks($uid,$inc,$option) {
	$database = database::getInstance();
	josSpoofCheck();
	$row = new mosWeblink($database);
	$row->load((int)$uid);
	$row->updateOrder();
	$row->move($inc,"published >= 0");
	$row->updateOrder();

	mosRedirect("index2.php?option=$option");
}

/**
 * Cancels an edit operation
 * @param string The current url option
 */
function cancelWeblink($option) {
	$database = database::getInstance();
	josSpoofCheck();
	$row = new mosWeblink($database);
	$row->bind($_POST);
	$row->checkin();
	mosRedirect("index2.php?option=$option");
}