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
if(!($acl->acl_check('administration','edit','users',$my->usertype,'components','all') | $acl->acl_check('administration','edit','users',$my->usertype,'components','com_frontpage'))) {
	mosRedirect('index2.php',_NOT_AUTH);
}

// call
require_once ($mainframe->getPath('admin_html'));
require_once ($mainframe->getPath('class'));
//подключаем класс босса
require_once(JPATH_BASE.'/components/com_boss/boss.class.php');

$conf = null;
$configObject = new frontpageConfig();
$conf->directory = $configObject->get('directory');
$conf->page = $configObject->get('page');

$cid = josGetArrayInts('cid');

    switch($task) {
    	case 'publish':
    		changeFrontPage($cid,1,$option);
    		break;

    	case 'unpublish':
    		changeFrontPage($cid,0,$option);
    		break;

    	case 'archive':
    		changeFrontPage($cid,-1,$option);
    		break;

    	case 'remove':
    		removeFrontPage($cid,$option);
    		break;

    	case 'orderup':
    		orderFrontPage(intval($cid[0]),-1,$option);
    		break;

    	case 'orderdown':
    		orderFrontPage(intval($cid[0]),1,$option);
    		break;

    	case 'saveorder':
    		saveOrder($cid);
    		break;

    	case 'accesspublic':
    		accessMenu(intval($cid[0]),0);
    		break;

    	case 'accessregistered':
    		accessMenu(intval($cid[0]),1);
    		break;

    	case 'accessspecial':
    		accessMenu(intval($cid[0]),2);
    		break;

    	case 'settings':
    		settings($conf);
    		break;

    	case 'save_settings':
    	case 'apply_settings':
    		saveSettings($task, $configObject);
    		break;

    	default:
            if(!isset($conf->directory)){
                mosRedirect("index2.php?option=com_frontpage&task=settings", _CONFIG_EMPTY);
            }
    		viewFrontPage($option, $conf->directory);
    		break;
    }


/**
 * Compiles a list of frontpage items
 */
function viewFrontPage($option, $directory) {
	global $mainframe,$mosConfig_list_limit;

	$database = database::getInstance();

	$catid = intval($mainframe->getUserStateFromRequest("catid{$option}",'catid',0));
	$filter_authorid = intval($mainframe->getUserStateFromRequest("filter_authorid{$option}",'filter_authorid',0));
	$filter_sectionid = intval($mainframe->getUserStateFromRequest("filter_sectionid{$option}",'filter_sectionid',0));

	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit",'limit',$mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart",'limitstart',0));
	$search = $mainframe->getUserStateFromRequest("search{$option}",'search','');
	if(get_magic_quotes_gpc()) {
		$search = stripslashes($search);
	}

	$where = array();

	// used by filter
	if($catid > 0) {
		$where[] = "cc.id = ".(int)$catid;
	}
	if($filter_authorid > 0) {
		$where[] = "c.userid = ".(int)$filter_authorid;
	}

	if($search) {
		$where[] = "LOWER( c.name ) LIKE '%".$database->getEscaped(Jstring::trim(Jstring::strtolower($search)))."%'";
	}

	// get the total number of records
	$query = "SELECT count(*)"
			."\n FROM FROM #__boss_" . $directory . "_contents AS c"
			."\n INNER JOIN #__boss_" . $directory . "_content_category_href AS cch ON cch.content_id = c.id"
			."\n INNER JOIN #__boss_" . $directory . "_categories AS cc ON cc.id = cch.category_id"
			."\n WHERE c.frontpage = 1 ".(count($where)?"\n AND ".implode(' AND ',$where):'');
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once (JPATH_BASE.'/'.JADMIN_BASE.'/includes/pageNavigation.php');
	$pageNav = new mosPageNav($total,$limitstart,$limit);

	$query = "SELECT c.*, cc.name as catname, cc.id as catid, v.name AS author"
			."\n FROM #__boss_" . $directory . "_contents AS c"
			."\n INNER JOIN #__boss_" . $directory . "_content_category_href AS cch ON cch.content_id = c.id"
			."\n INNER JOIN #__boss_" . $directory . "_categories AS cc ON cc.id = cch.category_id"
			."\n LEFT JOIN #__users AS v ON v.id = c.userid"
            ."\n WHERE c.frontpage = 1 ".(count($where)?"\nAND ".implode(' AND ',$where):"")
            ."\n ORDER BY  c.ordering";
	$database->setQuery($query,$pageNav->limitstart,$pageNav->limit);

	$rows = $database->loadObjectList();
	if($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// get list of categories for dropdown filter
	$query = "SELECT cc.id AS value, cc.name AS text"
			."\n FROM #__boss_" . $directory . "_contents AS c"
			."\n INNER JOIN #__boss_" . $directory . "_content_category_href AS cch ON cch.content_id = c.id"
			."\n INNER JOIN #__boss_" . $directory . "_categories AS cc ON cc.id = cch.category_id"
			."\n WHERE c.frontpage = 1";
	$categories[] = mosHTML::makeOption('0',_SEL_CATEGORY);
	$database->setQuery($query);
	$categories = array_merge($categories,$database->loadObjectList());
	$lists['catid'] = mosHTML::selectList($categories,'catid','class="inputbox" size="1" onchange="document.adminForm.submit( );"','value','text',$catid);

	// get list of Authors for dropdown filter
	$query = "SELECT c.userid, u.name"
			."\n FROM #__boss_" . $directory . "_contents AS c"
			."\n LEFT JOIN #__users AS u ON u.id = c.userid"
			."\n GROUP BY u.name"
			."\n ORDER BY u.name";
	$authors[] = mosHTML::makeOption('0',_SEL_AUTHOR,'userid','name');
	$database->setQuery($query);
	$authors = array_merge($authors,$database->loadObjectList());
	$lists['authorid'] = mosHTML::selectList($authors,'filter_authorid','class="inputbox" size="1" onchange="document.adminForm.submit( );"','userid','name',$filter_authorid);

	ContentView::showList($rows,$search,$pageNav,$option,$lists, $directory);
}

/**
 * Changes the state of one or more content pages
 * @param array An array of unique category id numbers
 * @param integer 0 if unpublishing, 1 if publishing
 */
function changeFrontPage($cid = null,$state = 0,$option) {
	global $my;
    $directory = mosGetParam($_REQUEST,'directory',0);
	$database = database::getInstance();

	josSpoofCheck();
	if(count($cid) < 1) {
		$action = $state == 1 ? _CHANGE_TO_PUBLISH : ($state == -1 ? _CHANGE_TO_ARH  : _CHANGE_TO_UNPUBLISH);
		echo "<script> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts($cid);
	$cids = 'id='.implode(' OR id=',$cid);

    $query = "UPDATE #__boss_" . $directory . "_contents"
			."\n SET published = ".(int)$state
			."\n WHERE ( $cids ) ";
	$database->setQuery($query);
	if(!$database->query()) {
		echo "<script> alert('".$database->stderr()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=$option");
}

function removeFrontPage(&$cid,$option) {
	josSpoofCheck();

	$database = database::getInstance();
    $directory = mosGetParam($_REQUEST,'directory',0);

	if(!is_array($cid) || count($cid) < 1) {
		echo "<script> alert('"._CHOOSE_OBJ_DELETE."'); window.history.go(-1);</script>\n";
		exit;
	}

	foreach($cid as $id) {
        $query = "UPDATE #__boss_" . $directory . "_contents"
	    		."\n SET frontpage = 0"
	    		."\n WHERE id = ".(int)$id;
	    $database->setQuery($query);
	    if(!$database->query()) {
            echo "<script> alert('".$database->stderr()."'); window.history.go(-1);</script>\n";
	    		exit();
	    }
	}
	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=$option");
}

/**
 * Moves the order of a record
 * @param integer The increment to reorder by
 */
function orderFrontPage($uid,$inc,$option) {
	josSpoofCheck();

	$database = database::getInstance();
    $directory = mosGetParam($_REQUEST,'directory',0);
    
	$fp = new mosFrontPage($database, $directory);
	$fp->load((int)$uid);
	$fp->move($inc);

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=$option");
}

/**
 * @param integer The id of the content item
 * @param integer The new access level
 * @param string The URL option
 */
function accessMenu($uid,$access) {
	josSpoofCheck();

	$database = database::getInstance();

	$row = new mosContent($database);
	$row->load((int)$uid);
	$row->access = $access;

	if(!$row->check()) {
		return $row->getError();
	}
	if(!$row->store()) {
		return $row->getError();
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect('index2.php?option=com_frontpage');
}

function saveOrder(&$cid) {
	josSpoofCheck();

	$database = database::getInstance();
    $directory = mosGetParam($_REQUEST,'directory',0);
    
	$total = count($cid);
	$order = josGetArrayInts('order');

	for($i = 0; $i < $total; $i++) {
		$query = "UPDATE #__boss_" . $directory . "_contents SET ordering = ".(int)$order[$i]." WHERE id = ".(int)$cid[$i];
		$database->setQuery($query);
		if(!$database->query()) {
			echo "<script> alert('".$database->stderr()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	$msg = _NEW_ORDER_SAVED;
	mosRedirect( 'index2.php?option=com_frontpage', $msg );
}

function settings($conf) {

	$database = database::getInstance();

    $directories = BossDirectory::getDirectories();
    $dirOptions = array();
	foreach($directories as $directory){
        $dirOptions[] = mosHTML::makeOption($directory->id, $directory->name);
    }
    $directorylist = mosHTML::selectList($dirOptions, 'directory', 'class="inputbox" size="1"', 'value', 'text', @$conf->directory);
    $pages = array();
    $pages[] = mosHTML::makeOption('show_frontpage', _DIRECTORY_CONTENT);
    $pages[] = mosHTML::makeOption('front', _DIRECTORY_CATEGORY);
    $pageslist = mosHTML::selectList($pages, 'page', 'class="inputbox" size="1"', 'value', 'text', @$conf->page);
    ContentView::showConf($directorylist,$pageslist);
}

function saveSettings($task, $configObject){
    $configObject->save_config();
    $task = ($task == 'apply_settings') ? 'settings' : '';
    mosRedirect("index2.php?option=com_frontpage&task=".$task, _CONFIG_SAVED);
}