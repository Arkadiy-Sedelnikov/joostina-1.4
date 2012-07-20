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
if(!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_banners'))){
	mosRedirect('index2.php', _NOT_AUTH);
}

define('BANNER_IN_ATTIVAZIONE', 1);
define('BANNER_ATTIV0', 2);
define('BANNER_TERMINATO', 3);
define('BANNER_NON_PUBBLICATO', 4);

require_once ($mainframe->getPath('admin_html'));
require_once ($mainframe->getPath('class'));

$cid = josGetArrayInts('cid');

if(intval($cid[0]) == 0){
	$cid[0] = intval(mosGetParam($_REQUEST, 'cid', 0));
}

switch($task){
	// OTHER EVENTS

	case 'newcategory':
		editCategory(0, $option);
		break;

	case 'editcategory':
		editCategory(intval($cid[0]), $option);
		break;

	case 'cancelcategory':
		cancelEditCategory($option);
		break;

	case 'savecategory':
		saveCategory($option);
		break;

	case 'removecategory':
		removeCategories($cid, $option);
		break;

	case 'publishcategory':
		publishCategories($cid, 1);
		break;

	case 'unpublishcategory':
		publishCategories($cid, 0);
		break;

	case 'categories':
		viewCategories($option);
		break;

	// CLIENT EVENTS

	case 'newclient':
		editBannerClient(0, $option);
		break;

	case 'editclient':
		editBannerClient(intval($cid[0]), $option);
		break;

	case 'cancelclient':
		cancelEditClient($option);
		break;

	case 'saveclient':
		saveBannerClient($option);
		break;

	case 'removeclients':
		removeBannerClients($cid, $option);
		break;

	case 'unpublishclient':
		publishClient($cid, 0);
		break;

	case 'publishclient':
		publishClient($cid, 1);
		break;

	case 'clients':
		viewBannerClients($option);
		break;

	// BANNER EVENTS

	case 'newbanner':
		editBanner(0, $option);
		break;

	case 'editbanner':
		editBanner(intval($cid[0]), $option);
		break;

	case 'cancelbanner':
		cancelEditBanner($option);
		break;

	case 'applybanner':
	case 'savebanner':
	case 'resethits':
		saveBanner($option, $task);
		break;

	case 'removebanners':
		removeBanner($cid, $option);
		break;

	case 'publishbanner':
		publishBanner($cid, 1, $option);
		break;

	case 'unpublishbanner':
		publishBanner($cid, 0, $option);
		break;

	case 'banners':
		viewBanners($option);
		break;

	case 'backup':
		doBackup();
		break;

	case 'restore':
		restore($option);
		break;

	case 'dorestore':
		doRestore($option);
		break;

	default:
		cPanel($option);
		break;
}


function cPanel($option){
	$database = database::getInstance();

	$info_banner = array();
	$info_categories = array();
	$info_clients = array();

	$date = mosCurrentDate("%Y-%m-%d");
	$time = mosCurrentDate("%H:%M:%S");

	/*
	** Conta i banner attivi
	*/
	$sql = "SELECT count(b.id) as attivi"
		. "\nFROM #__banners as b"
		. "\nwhere b.state = 1"
		. "\nAND ('$date' <= b.publish_down_date OR b.publish_down_date = '0000-00-00')"
		. "\nAND '$date' >= b.publish_up_date"
		. "\nAND '$time' >= b.publish_up_time"
		. "\nAND ('$time' <= b.publish_down_time OR b.publish_down_time = '00:00:00')";


	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}

	$info_banner['attivi'] = $result[0]->attivi;

	/*
	** Conta i banner terminati
	*/
	$sql = "SELECT count(b.id) as terminati FROM #__banners as b where b.state = 1 AND  '$date' >= b.publish_down_date and b.publish_down_date != '0000-00-00'";

	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}

	$info_banner['terminati'] = $result[0]->terminati;

	/*
	** Conta i banner non_publicati
	*/
	$sql = "SELECT count(b.id) as non_publ FROM #__banners as b where b.state = 0";

	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}

	$info_banner['non_publ'] = $result[0]->non_publ;

	/*
	** Conta i banner in attivazione
	*/
	$sql = "SELECT count(b.id) as in_attiv FROM #__banners as b where b.state = 1 AND  '$date' < b.publish_up_date ";

	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}

	$info_banner['in_attiv'] = $result[0]->in_attiv;


	/*
	** Conta i clienti attivi
	*/
	$sql = "SELECT count(c.cid) as attivi FROM #__banners_clients as c where c.published  = 1";

	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}


	$info_clients['attivi'] = $result[0]->attivi;

	/*
	** Conta i clienti non pubblicati
	*/
	$sql = "SELECT count(c.cid) as non_publ FROM #__banners_clients as c where c.published  = 0";

	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}


	$info_clients['non_publ'] = $result[0]->non_publ;

	/*
	** Conta le categorie attive
	*/
	$sql = "SELECT count(c.id) as attivi FROM #__banners_categories as c where c.published  = 1";

	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}


	$info_categories['attivi'] = $result[0]->attivi;

	/*
	** Conta le categorie non pubblicate
	*/
	$sql = "SELECT count(c.id) as non_publ FROM #__banners_categories as c where c.published  = 0";

	$database->setQuery($sql);

	if(!$result = $database->loadObjectList()){
		echo $database->stderr();
		return false;
	}


	$info_categories['non_publ'] = $result[0]->non_publ;

	return HTML_banners::cPanel($info_banner, $info_clients, $info_categories, $option);
}

function viewBanners($option){

	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();

	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mainframe->getCfg('list_limit')));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}bannerslimitstart", 'limitstart', 0));
	$catid = intval($mainframe->getUserStateFromRequest("category{$option}id", 'catid', 0));
	$cliid = intval($mainframe->getUserStateFromRequest("client{$option}id", 'cliid', 0));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__banners");
	if(($total = $database->loadResult()) == null){
		echo $database->stderr();
		return false;
	}

	mosMainFrame::addLib('pagenavigation');
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$where = '';
	if($catid > 0){
		$where = " AND a.tid = '$catid' ";
	}

	if($cliid > 0){
		$where .= " AND a.cid = '$cliid' ";
	}

	$query = "SELECT a.*, u.name AS editor, "
		. "\n c.name as category, c.published as cat_pub, cl.published as cl_pub, "
		. "\n cl.name as cl_name"
		. "\n FROM #__banners AS a"
		. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
		. "\n LEFT JOIN #__banners_categories AS c ON c.id = a.tid"
		. "\n LEFT JOIN #__banners_clients AS cl ON cl.cid = a.cid"
		. "\n WHERE 1 $where"
		. "\n ORDER BY c.name, a.name";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
	$banners = $database->loadObjectList();

	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	// get list of categories
	$query = "SELECT id AS value, name AS text FROM #__banners_categories ORDER BY name";
	$database->setQuery($query);
	$banners_categories = $database->loadObjectList();

	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}
	// Build Categories select list
	$categories[] = mosHTML::makeOption('-1', _ABP_ALLCAT);
	$categories = array_merge($categories, $banners_categories);
	$categorieslist = mosHTML::selectList($categories, 'catid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $catid);

	// get list of clients
	$query = "SELECT cid as value, name as text FROM #__banners_clients ORDER BY name";
	$database->setQuery($query);
	$banners_clients = $database->loadObjectList();

	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}
	// Build Client select list
	$clients[] = mosHTML::makeOption('-1', _ABP_ALLCLI);
	$clients = array_merge($clients, $banners_clients);
	$clientlist = mosHTML::selectList($clients, 'cliid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $cliid);

	HTML_banners::showBanners($banners, $categorieslist, $clientlist, $my->id, $pageNav, $option);
}

function editBanner($bannerid, $option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$banner = new mosArtBanner($database);

	if($bannerid){
		// load the row from the db table
		$banner->load($bannerid);

		// fail if checked out not by 'me'
		if($banner->checked_out && $banner->checked_out != $my->id){
			mosRedirect("index2.php?option=$option&task=banners", sprintf(_ABP_BANNER_IN_USE, $banner->name));
		}
	}

	// get list of clients
	$query = "SELECT cid as value, name as text FROM #__banners_clients ORDER BY name";
	$database->setQuery($query);
	$banners_clients = $database->loadObjectList();

	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}
	// Build Client select list
	$clients[] = mosHTML::makeOption('0', _ABP_SELECT_CLIENT);
	$clients = array_merge($clients, $banners_clients);
	$clientlist = mosHTML::selectList($clients, 'cid', 'class="inputbox" size="1"', 'value', 'text', $banner->cid);

	// get list of categories
	$query = "SELECT id AS value, name AS text FROM #__banners_categories ORDER BY name";
	$database->setQuery($query);
	$banners_categories = $database->loadObjectList();

	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}
	// Build Categories select list
	$categories[] = mosHTML::makeOption('0', _ABP_SELECT_CATEGORY);
	$categories = array_merge($categories, $banners_categories);
	$categorieslist = mosHTML::selectList($categories, 'tid', 'class="inputbox" size="1"', 'value', 'text', $banner->tid);

	// Imagelist
	// get list of images
	$dimension = array();
	$imgFiles = mosReadDirectory(JPATH_BASE . "/images/show");
	$images = array();
	$images[] = mosHTML::makeOption('', _ABP_PSANIMG);
	foreach($imgFiles as $file){
		if(preg_match("/(\.bmp|\.gif|\.jpg|\.jpeg|\.png|\.swf)$/i", $file)){
			$images[] = mosHTML::makeOption($file);
			// get image info
			$image_info = @getimagesize(JPATH_BASE . "/images/show/" . $file);
			$dimension[$file]['w'] = $image_info[0];
			$dimension[$file]['h'] = $image_info[1];
		}
	}

	$ilist = mosHTML::selectList($images, 'image_url', "class=\"inputbox\" size=\"1\" onchange=\"changeDisplayImage(1);\"", 'value', 'text', $banner->image_url);

	// get list of groups
	$database->setQuery("SELECT id AS value, name AS text FROM #__groups ORDER BY id");
	$groups = $database->loadObjectList();

	// build the html select list
	$glist = mosHTML::selectList($groups, 'access', 'class="inputbox" size="1"', 'value', 'text', intval($banner->access));

	if($bannerid){
		$banner->checkout($my->id);

		if($banner->publish_down_date == "0000-00-00"){
			$banner->publish_down_date = _ABP_NEVER;
		}

		$event_up = new mosArtBannersTime($banner->publish_up_time);
		$banner->publish_up_hour = $event_up->hour;
		$banner->publish_up_minute = $event_up->minute;

		$event_down = new mosArtBannersTime($banner->publish_down_time);
		$banner->publish_down_hour = $event_down->hour;
		$banner->publish_down_minute = $event_down->minute;
	} else{
		$banner->publish_up_date = mosCurrentDate("%Y-%m-%d");
		$banner->publish_down_date = _ABP_NEVER;
		$banner->publish_up_hour = "00";
		$banner->publish_up_minute = "00";
		$banner->publish_down_hour = "00";
		$banner->publish_down_minute = "00";

		$banner->dta_mod_clicks = mosCurrentDate("%Y-%m-%d");
	}

	HTML_banners::editBanner($banner, $clientlist, $categorieslist, $ilist, $glist, $option, $dimension);
}

function saveBanner($option, $task){
	$database = database::getInstance();

	$_publish_up_date = trim(mosGetParam($_POST, '_publish_up_date', '0000-00-00'));
	$_publish_up_hour = trim(mosGetParam($_POST, '_publish_up_hour', '00'));
	$_publish_up_minute = trim(mosGetParam($_POST, '_publish_up_minute', '00'));
	$_publish_down_date = trim(mosGetParam($_POST, '_publish_down_date', '0000-00-00'));
	$_publish_down_hour = trim(mosGetParam($_POST, '_publish_down_hour', '00'));
	$_publish_down_minute = trim(mosGetParam($_POST, '_publish_down_minute', '00'));
	$reccurweekdays = mosGetParam($_POST, 'reccurweekdays', '');
	$send_email = mosGetParam($_POST, 'send_email', '');

	$banner = new mosArtBanner($database);

	if(!$banner->bind($_POST)){
		echo "<script> alert('" . $banner->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$msg = '';

	// Resets clicks when `Reset Clicks` button is used instead of `Save` button
	if($task == 'resethits'){
		$banner->clicks = 0;
		$msg = _BANNER_COUNTER_RESETTED;
		$banner->dta_mod_clicks = mosCurrentDate("%Y-%m-%d");
	} else
		if($banner->dta_mod_clicks == '0000-00-00'){
			$banner->dta_mod_clicks = mosCurrentDate("%Y-%m-%d");
		}

	// assemble the starting time
	if(intval($_publish_up_date) && $_publish_up_date != '0000-00-00'){
		$banner->publish_up_date = $_publish_up_date;

		// verifica formalita' della data inizio
		if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $banner->publish_up_date, $regs)){
			if(!checkdate($regs[2], $regs[3], $regs[1])){
				echo "<script> alert('" . _CHECK_PUBLISH_DATE . "'); window.history.go(-1); </script>\n";
				exit();
			}
		} else{
			echo "<script> alert('" . _CHECK_PUBLISH_DATE . "'); window.history.go(-1); </script>\n";
			exit();
		}
	} else{
		$banner->publish_up_date = mosCurrentDate("%Y-%m-%d");
	}

	$banner->publish_up_time = "$_publish_up_hour:$_publish_up_minute:00";

	if(preg_match("/([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $banner->publish_up_time, $regs)){
		if($regs[1] > 24 || $regs[2] > 60 || $regs[3] > 60){
			echo "<script> alert('" . _CHECK_START_PUBLICATION_DATE . "'); window.history.go(-1); </script>\n";
			exit();
		}
	} else{
		echo "<script> alert('" . _CHECK_START_PUBLICATION_DATE . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// assemble the finishing time
	if(intval($_publish_down_date) && $_publish_down_date != '0000-00-00'){
		$banner->publish_down_date = $_publish_down_date;

		// verifica formalita' della data fine
		if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $banner->publish_down_date, $regs)){
			if(!checkdate($regs[2], $regs[3], $regs[1])){
				echo "<script> alert('" . _CHECK_END_PUBLICATION_DATE . "'); window.history.go(-1); </script>\n";
				exit();
			}
		} else{
			echo "<script> alert('" . _CHECK_END_PUBLICATION_DATE . "'); window.history.go(-1); </script>\n";
			exit();
		}

	} else{
		$banner->publish_down_date = "0000-00-00";
	}

	$banner->publish_down_time = "$_publish_down_hour:$_publish_down_minute:00";

	if(preg_match("/([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $banner->publish_down_time, $regs)){
		if($regs[1] > 24 || $regs[2] > 60 || $regs[3] > 60){
			echo "<script> alert('" . _CHECK_END_PUBLICATION_DATE . "'); window.history.go(-1); </script>\n";
			exit();
		}
	} else{
		echo "<script> alert('" . _CHECK_END_PUBLICATION_DATE . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// Verifico se la data fine e' piu' grande della data inizio
	if($banner->publish_down_date != '0000-00-00' && $banner->publish_up_date > $banner->publish_down_date){
		echo "<script> alert('" . _ABP_BN_DATE . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if($banner->publish_up_date != $banner->publish_down_date){
		$banner->reccurtype = intval($banner->reccurtype);
	} else{
		$banner->reccurtype = 0;
	}

	// Reccur week days
	if($banner->reccurtype != 0 && is_array($reccurweekdays)){
		$banner->reccurweekdays = implode(',', $reccurweekdays);
	}

	if(!$banner->check()){
		echo "<script> alert('" . $banner->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$banner->store()){
		echo "<script> alert('" . $banner->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$banner->checkin();

	if($send_email == 'on'){
		$client = new mosArtBannerClient($database);
		$client->load($banner->cid);

		global $mosConfig_mailfrom, $mosConfig_fromname;
		$link = JPATH_SITE . '/index.php?option=com_banners&task=statistics&id=' . $banner->id . '&password=' . mosHash($banner->password);
		$result = mosMail($mosConfig_mailfrom, $mosConfig_fromname, $client->email, _ABP_SUBJECT_MAIL, _ABP_BODY_MAIL . $link);

		if($result === false)
			$msg .= ' ' . _ABP_ERROR_SEND_MAIL;
	}

	$catid = intval(mosGetParam($_REQUEST, 'catid', 0));
	$cliid = intval(mosGetParam($_REQUEST, 'cliid', 0));

	$catid = ($catid > 0) ? '&catid=' . $catid : '';
	$cliid = ($cliid > 0) ? '&cliid=' . $cliid : '';

	if($task == 'applybanner'){
		mosRedirect("index2.php?option=$option" . $catid . $cliid . "&task=editbanner&cid=" . $banner->id, $msg);
	} else{
		mosRedirect("index2.php?option=$option&task=banners" . $catid . $cliid, $msg);
	}
}

function cancelEditBanner($option){
	$database = database::getInstance();

	$banner = new mosArtBanner($database);
	$banner->bind($_POST);
	$banner->checkin();

	$catid = intval(mosGetParam($_REQUEST, 'catid', 0));
	$cliid = intval(mosGetParam($_REQUEST, 'cliid', 0));

	$catid = ($catid > 0) ? '&catid=' . $catid : '';
	$cliid = ($cliid > 0) ? '&cliid=' . $cliid : '';

	mosRedirect("index2.php?option=$option&task=banners" . $catid . $cliid);
}

function publishBanner($cid, $publish = 1, $option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!is_array($cid) || count($cid) == 0){
		$action = $publish ? _ABP_L_PUBLISH : _HIDE;
		echo "<script> alert('" . sprintf(_ABP_SELECT_ITEM_TO, $action) . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$date = mosCurrentDate("%Y-%m-%d");

	$database->setQuery("UPDATE #__banners SET state='$publish'" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))" . "\nand ('$date' <= publish_down_date or publish_down_date = '0000-00-00')");
	if(!$database->query()){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(count($cid) == 1){
		$banner = new mosArtBanner($database);
		$banner->checkin($cid[0]);
	}

	$catid = intval(mosGetParam($_REQUEST, 'catid', 0));
	$cliid = intval(mosGetParam($_REQUEST, 'cliid', 0));

	$catid = ($catid > 0) ? '&catid=' . $catid : '';
	$cliid = ($cliid > 0) ? '&cliid=' . $cliid : '';

	mosRedirect("index2.php?option=$option&task=banners" . $catid . $cliid);
}

function removeBanner($cid, $option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!is_array($cid) || count($cid) == 0){
		echo "<script> alert('" . _ABP_PSACLI . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$msg = '';

	$banner = new mosArtBanner($database);

	for($i = 0, $count = count($cid); $i < $count; $i++){
		$cids = $cid[$i];
		$banner->load($cids);
		if($banner->checked_out && $banner->checked_out != $my->id){
			$msg .= sprintf(_ABP_BANNER_IN_USE, $banner->name) . '<br>';
		} else{
			$database->setQuery("DELETE FROM #__banners WHERE id = $cids");
			if(!$database->query()){
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}
	}

	$catid = intval(mosGetParam($_REQUEST, 'catid', 0));
	$cliid = intval(mosGetParam($_REQUEST, 'cliid', 0));

	$catid = ($catid > 0) ? '&catid=' . $catid : '';
	$cliid = ($cliid > 0) ? '&cliid=' . $cliid : '';

	mosRedirect("index2.php?option=$option&task=banners" . $catid . $cliid, $msg);
}

// ---------- BANNER CLIENTS ----------

function viewBannerClients($option){
	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();

	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mainframe->getCfg('list_limit')));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}clientslimitstart", 'limitstart', 0));
	$published = $mainframe->getUserStateFromRequest("published{$option}", 'published', -1);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__banners_clients");
	if(($total = $database->loadResult()) == null){
		echo $database->stderr();
		return false;
	}

	$weekday = mosCurrentDate("%w");
	$date = mosCurrentDate("%Y-%m-%d");
	$time = mosCurrentDate("%H:%M:%S");

	mosMainFrame::addLib('pagenavigation');
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$where = '';
	if($published != -1){
		$published = intval($published);
		$where = " where a.published  = $published ";
	}

	$query = "SELECT a.*, count(b.id) AS id, u.name AS editor FROM #__banners_clients AS a LEFT JOIN #__banners AS b ON a.cid = b.cid LEFT JOIN #__users AS u ON u.id = a.checked_out $where GROUP BY a.cid";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
	$clients = $database->loadObjectList();

	$info_banner = array();
	$_c = count($clients);
	for($i = 0, $n = $_c; $i < $n; $i++){

		$cid = $clients[$i]->cid;

		/*
		** Conta i banner attivi del cliente
		*/
		$sql = "SELECT count(b.id) as attivi" . "\nFROM #__banners_clients as c, #__banners as b where c.cid = $cid and b.cid = c.cid and b.state = 1 AND ('$date' <= b.publish_down_date OR b.publish_down_date = '0000-00-00') AND '$date' >= b.publish_up_date" . "\nAND '$time' >= b.publish_up_time" . "\nAND ('$time' <= b.publish_down_time OR b.publish_down_time = '00:00:00')";
		$database->setQuery($sql);
		$result = $database->loadObjectList();
		$info_banner[$i]['attivi'] = $result[0]->attivi;

		/*
		** Conta i banner terminati del cliente
		*/
		$sql = "SELECT count(b.id) as terminati FROM #__banners_clients as c, #__banners as b" . "\nwhere c.cid = $cid and b.cid = c.cid and b.state = 1" . "\nAND  '$date' >= b.publish_down_date and b.publish_down_date != '0000-00-00'";
		$database->setQuery($sql);
		$result = $database->loadObjectList();
		$info_banner[$i]['terminati'] = $result[0]->terminati;

		/*
		** Conta i banner non_publicati del cliente
		*/
		$sql = "SELECT count(b.id) as non_publ" . "\nFROM #__banners_clients as c, #__banners as b" . "\nwhere c.cid = $cid and b.cid = c.cid and b.state = 0";
		$database->setQuery($sql);
		$result = $database->loadObjectList();
		$info_banner[$i]['non_publ'] = $result[0]->non_publ;

		/*
		** Conta i banner in attivazione del cliente
		*/
		$sql = "SELECT count(b.id) as in_attiv" . "\nFROM #__banners_clients as c, #__banners as b" . "\nwhere c.cid = $cid and b.cid = c.cid and b.state = 1" . "\nAND  '$date' < b.publish_up_date ";
		$database->setQuery($sql);
		$result = $database->loadObjectList();
		$info_banner[$i]['in_attiv'] = $result[0]->in_attiv;
	}

	// Build States select list
	$states[] = mosHTML::makeOption('-1', _ABP_ALLCLI);
	$states[] = mosHTML::makeOption('1', _ABP_BANNERS_ATT);
	$states[] = mosHTML::makeOption('0', _ABP_BANNERS_NO_PUB);

	$stateslist = mosHTML::selectList($states, 'published', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $published);

	HTML_bannerClient::showClients($clients, $info_banner, $my->id, $pageNav, $option, $stateslist);
}

function editBannerClient($clientid, $option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$client = new mosArtBannerClient($database);

	if($clientid){
		// load the row from the db table
		$client->load($clientid);

		// fail if checked out not by 'me'
		if($client->checked_out && $client->checked_out != $my->id){
			mosRedirect("index2.php?option=$option&task=clients", sprintf(_ABP_CICBEBAA, $client->name));
		}

		// do stuff for existing record
		$client->checkout($my->id);
	}

	HTML_bannerClient::editClient($client, $option);
}

function saveBannerClient($option){
	$database = database::getInstance();

	$client = new mosArtBannerClient($database);

	$client->published = 1;

	if(!$client->bind($_POST)){
		echo "<script> alert('" . $client->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(!$client->check()){
		echo "<script> alert('" . $client->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(!$client->store()){
		echo "<script> alert('" . $client->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$client->checkin();

	mosRedirect("index2.php?option=$option&task=clients");
}

function publishClient($cid = null, $publish = 1){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!is_array($cid) || count($cid) == 0){
		$action = $publish ? _ABP_SACT_PUB : _ABP_SACT_UNPUB;
		echo "<script> alert('" . sprintf(_ABP_SACT, $action) . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$database->setQuery("UPDATE #__banners_clients SET published='$publish'" . "\nWHERE cid IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))");

	if(!$database->query()){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(count($cid) == 1){
		$category = new mosArtCategory($database);
		$category->checkin($cid[0]);
	}

	mosRedirect("index2.php?option=com_banners&task=clients");
}

function cancelEditClient($option){
	$database = database::getInstance();

	$client = new mosArtBannerClient($database);
	$client->bind($_POST);
	$client->checkin();
	mosRedirect("index2.php?option=$option&task=clients");
}

function removeBannerClients($cid, $option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!is_array($cid) || count($cid) == 0){
		echo "<script> alert('" . _ABP_PSACLI . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$database->setQuery("SELECT c.cid, c.name" . "\nFROM #__banners_clients AS c" . "\nWHERE c.cid IN ($cids)" . "\nGROUP BY c.cid");

	if(!($rows = $database->loadObjectList())){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	$err = array();
	$cid = array();
	foreach($rows as $row){

		$database->setQuery("SELECT count(*) from #__banners where cid = " . $row->cid);
		if(($count = $database->loadResult()) == null){
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		if($count == 0){
			$cid[] = $row->cid;
		} else{
			$err[] = $row->name;
		}
	}

	if(count($cid)){
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__banners_clients WHERE cid IN ($cids)");
		if(!$database->query()){
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	if(count($err)){
		$err = implode("\', \'", $err);
		mosRedirect("index2.php?option=com_banners&task=clients" . "&mosmsg=" . urlencode(sprintf(_ABP_CDCATTATHABSR, $err)));
	}

	mosRedirect("index2.php?option=$option&task=clients");
}

// BANNER CATEGORIES

/**
 * Compiles a list of com_artbanners for a section
 * @param string The name of the category section
 * @param string The name of the current user
 */
function viewCategories($option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mainframe->getCfg('list_limit')));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}categorieslimitstart", 'limitstart', 0));
	$published = $mainframe->getUserStateFromRequest("published{$option}", 'published', -1);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__banners_categories");
	if(($total = $database->loadResult()) == null){
		echo $database->stderr();
		return false;
	}

	mosMainFrame::addLib('pagenavigation');
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$where = '';
	if($published != -1){
		$published = intval($published);
		$where = " where c.published  = $published ";
	}

	$query = "SELECT c.*,u.name AS editor, COUNT(DISTINCT b.id) AS banners FROM #__banners_categories AS c LEFT JOIN #__users AS u ON u.id = c.checked_out LEFT JOIN #__banners AS b ON b.tid = c.id $where GROUP BY c.id ORDER BY c.name";

	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);

	$rows = $database->loadObjectList();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	// Build States select list
	$states[] = mosHTML::makeOption('-1', _ABP_ALLCAT);
	$states[] = mosHTML::makeOption('1', _ABP_BANNERS_ATT);
	$states[] = mosHTML::makeOption('0', _ABP_BANNERS_NO_PUB);

	$stateslist = mosHTML::selectList($states, 'published', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $published);

	HTML_bannerCategory::showCategories($rows, $my->id, $pageNav, $option, $stateslist);
}

/**
 * Compiles information to add or edit a category
 * @param string The name of the category section
 * @param integer The unique id of the category to edit (0 if new)
 * @param string The name of the current user
 */
function editCategory($cid, $option){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$category = new mosArtCategory($database);

	if($cid){
		// load the row from the db table
		$category->load(intval($cid));

		// fail if checked out not by 'me'
		if($category->checked_out && $category->checked_out != $my->id){
			mosRedirect("index2.php?option=com_banners&task=categories", sprintf(_ABP_TCICBEBAA, $category->name));
		}

		$category->checkout($my->id);
	}

	HTML_bannerCategory::editCategory($category, $option);
}

/**
 * Saves the catefory after an edit form submit
 * @param string The name of the category section
 */
function saveCategory($option){
	$database = database::getInstance();

	$category = new mosArtCategory($database);

	$category->published = 1;

	if(!$category->bind($_POST)){
		echo "<script> alert('" . $category->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$category->check()){
		echo "<script> alert('" . $category->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(!$category->store()){
		echo "<script> alert('" . $category->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	$category->checkin();

	mosRedirect("index2.php?option=$option&task=categories");
}

/**
 * Deletes one or more com_artbanners from the artbanners_categories table
 * @param array An array of unique category id numbers
 */
function removeCategories($cid, $option){
	$database = database::getInstance();

	if(!is_array($cid) || count($cid) == 0){
		echo "<script> alert('" . _CHOOSE_CATEGORY_TO_REMOVE . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$database->setQuery("SELECT c.id, c.name" . "\nFROM #__banners_categories AS c" . "\nWHERE c.id IN ($cids)" . "\nGROUP BY c.id");

	if(!($rows = $database->loadObjectList())){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	$err = array();
	$cid = array();
	foreach($rows as $row){

		$database->setQuery("SELECT count(*) from #__banners where tid = " . $row->id);
		$count = $database->loadResult();

		if($count == 0){
			$cid[] = $row->id;
		} else{
			$err[] = $row->name;
		}
	}

	if(count($cid)){
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__banners_categories WHERE id IN ($cids)");
		if(!$database->query()){
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	if(count($err)){
		$cids = implode("\', \'", $err);
		mosRedirect("index2.php?option=com_banners&task=categories" . "&mosmsg=" . urlencode(sprintf(_ABP_CCBRATCR, $cids)));
	}

	mosRedirect("index2.php?option=$option&task=categories");
}

/**
 * Publishes or Unpublishes one or more artbanners_categories
 * @param integer A unique category id (passed from an edit form)
 * @param array An array of unique category id numbers
 * @param integer 0 if unpublishing, 1 if publishing
 */
function publishCategories($cid = null, $publish = 1){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!is_array($cid) || count($cid) == 0){
		$action = $publish ? _ABP_SACT_PUB : _ABP_SACT_UNPUB;
		echo "<script> alert('" . sprintf(_ABP_SACT, $action) . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$database->setQuery("UPDATE #__banners_categories SET published='$publish'" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))");
	if(!$database->query()){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(count($cid) == 1){
		$category = new mosArtCategory($database);
		$category->checkin($cid[0]);
	}

	mosRedirect("index2.php?option=com_banners&task=categories");
}

/**
 * Cancels an edit operation
 */
function cancelEditCategory($option){
	$database = database::getInstance();

	$category = new mosArtCategory($database);
	$category->bind($_POST);
	$category->checkin();
	mosRedirect("index2.php?option=$option&task=categories");
}


function buildReccurTypeSelect($reccurtype, $args){
	$recur[] = mosHTML::makeOption('0', _ABP_ALLDAYS);
	$recur[] = mosHTML::makeOption('1', _ABP_EACHWEEK);
	$tosend = mosHTML::selectList($recur, 'reccurtype', $args, 'value', 'text', $reccurtype);
	echo $tosend;
}

function buildWeekDaysCheck($reccurweekdays, $args){
	$day_name = array('<span style="color:#ff0000">' . _ABP_SUN . "</span>", _ABP_MON, _ABP_TUE, _ABP_WED, _ABP_THU, _ABP_FRI, _ABP_SAT);

	$tosend = '';
	$split = array();
	$countsplit = 0;
	if(!empty($reccurweekdays)){
		$split = explode(",", $reccurweekdays);
		$countsplit = count($split);
	}

	for($a = 0; $a < 7; $a++){
		$checked = '';
		for($x = 0; $x < $countsplit; $x++){
			if($split[$x] == $a){
				$checked = 'CHECKED';
			}
		}
		$tosend .= "<input type='checkbox' id='cb_wd" . $a . "' name='reccurweekdays[]' value='" . $a . "' " . $args . " " . $checked . "/>" . $day_name[$a] . "\n";
	}
	echo $tosend;
}

function getLongDayName($daynb){

	if($daynb == "0"){
		$dayname = '<span style="color:#ff0000">' . _ABP_SUNDAY . "</span>";
	} elseif($daynb == "1"){
		$dayname = _ABP_MONDAY;
	} elseif($daynb == "2"){
		$dayname = _ABP_TUESDAY;
	} elseif($daynb == "3"){
		$dayname = _ABP_WEDNESDAY;
	} elseif($daynb == "4"){
		$dayname = _ABP_THURSDAY;
	} elseif($daynb == "5"){
		$dayname = _ABP_FRIDAY;
	} elseif($daynb == "6"){
		$dayname = _ABP_SATURDAY;
	}

	return $dayname;
}

function getShortDayName($daynb){
	if($daynb == "0"){
		$dayname = '<span style="color:#ff0000">' . _ABP_SUN . "</span>";
	} elseif($daynb == "1"){
		$dayname = _ABP_MON;
	} elseif($daynb == "2"){
		$dayname = _ABP_TUE;
	} elseif($daynb == "3"){
		$dayname = _ABP_WED;
	} elseif($daynb == "4"){
		$dayname = _ABP_THU;
	} elseif($daynb == "5"){
		$dayname = _ABP_FRI;
	} elseif($daynb == "6"){
		$dayname = _ABP_SAT;
	}
	return $dayname;
}

function restore($option){
	HTML_bannersOther::restore($option);
}

function getTextNode($node, $tag, $default = ''){

	$element = $node->getElementsByPath($tag, 1);

	$return = $element ? $element->getText() : $default;

	return $return;
}

function doRestore($option){
	$database = database::getInstance();

	$media_path = JPATH_BASE . '/media/';

	$userfile2 = (isset($_FILES['userfile']['tmp_name']) ? $_FILES['userfile']['tmp_name'] : "");
	$userfile_name = (isset($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : "");

	if(isset($_FILES['userfile'])){

		if(empty($userfile_name)){
			echo "<script>alert('", _ABP_SELECT_FILE, "');</script>";
			mosRedirect("index2.php?option=$option&task=restore");
		}

		$filename = explode("\.", $userfile_name);

		if(preg_match("/[^0-9a-zA-Z_]/i", $filename[0])){
			mosErrorAlert(_ABP_ERROR_FILENAME);
			mosRedirect("index2.php?option=$option&task=restore");
		}

		if(strcasecmp(substr($userfile_name, -4), ".xml")){
			mosErrorAlert(_ABP_ERROR_NOT_XML_FILE);
			mosRedirect("index2.php?option=$option&task=restore");
		}

		if(!move_uploaded_file($_FILES['userfile']['tmp_name'], $media_path . $_FILES['userfile']['name']) || !mosChmod($media_path . $_FILES['userfile']['name'])){
			mosErrorAlert(_ABP_ERROR_LOAD_FILE . $userfile_name);
			mosRedirect("index2.php?option=$option&task=restore");
		}
	}

	require_once (JPATH_BASE . '/includes/domit/xml_domit_include.php');

	//instantiate a new DOMIT! document
	$xmldoc = new DOMIT_Document();

	$success = $xmldoc->loadXML($media_path . $userfile_name);

	if(!$success){
		//an error has occurred; echo to browser
		$error = "Error code: " . $cdCollection->getErrorCode() . " - Error string: " . $cdCollection->getErrorString();
		mosErrorAlert($error);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	//process XML
	$root = &$xmldoc->documentElement;

	//verifico se ha figli
	if(!$xmldoc->documentElement->hasChildNodes()){
		mosErrorAlert(_NONET_EXIST_BANNER_RESTORE);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	/*
	** Leggo i banners
	*/
	$bannersNodes = &$root->getElementsByPath('#__banners', 1);
	if(is_null($bannersNodes) || !$bannersNodes->hasChildNodes()){
		mosErrorAlert(_NONET_EXIST_BANNER_RESTORE);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	$banners = $bannersNodes->childNodes;
	if(count($banners) == 0){
		mosErrorAlert(_NONET_EXIST_BANNER_RESTORE);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	$clientsNodes = &$root->getElementsByPath('#__banners_clients', 1);
	if(is_null($clientsNodes) || !$clientsNodes->hasChildNodes()){
		mosErrorAlert(_ABP_ERROR_NOT_EXIST_CLIENTS);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	$clients = $clientsNodes->childNodes;
	if(count($clients) == 0){
		mosErrorAlert(_ABP_ERROR_NOT_EXIST_CLIENTS);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	$categoriesNodes = &$root->getElementsByPath('#__banners_categories', 1);
	if(is_null($categoriesNodes) || !$categoriesNodes->hasChildNodes()){
		mosErrorAlert(_ABP_ERROR_NOT_EXIST_CATEGORIES);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	$categories = $categoriesNodes->childNodes;
	if(count($categories) == 0){
		mosErrorAlert(_ABP_ERROR_NOT_EXIST_CATEGORIES);
		mosRedirect("index2.php?option=$option&task=restore");
	}

	$clienti_inserti = array();
	$categorie_inserte = array();

	$clienti = array();
	$categorie = array();

	// per ogni banner presente nel XML
	foreach($banners as $banner){

		$new_cid = 0;
		$new_tid = 0;

		$cid = getTextNode($banner, 'cid');
		// verifico se ho gia' inserito questo cliente
		if(!in_array($cid, $clienti_inserti)){
			// ... mi scorro tutti i clienti
			foreach($clients as $client){
				// ... trovo il cliente che mi interessa
				if($cid == getTextNode($client, 'cid', 0)){
					$artbannersclientsplus = new mosArtBannerClient($database);

					$artbannersclientsplus->name = getTextNode($client, 'name');
					$artbannersclientsplus->contact = getTextNode($client, 'contact');
					$artbannersclientsplus->email = getTextNode($client, 'email');
					$artbannersclientsplus->extrainfo = getTextNode($client, 'extrainfo');
					$artbannersclientsplus->published = getTextNode($client, 'published', 0);

					// salvo il cliente
					$artbannersclientsplus->store();

					// memorizzo il nuovo ID del cliente
					$new_cid = $database->insertid();

					break;
				}
			}

			// memorizzo il cliente appena inserito
			$clienti_inserti[] = $cid;

			$clienti['$cid'] = $new_cid;
		} else{
			$new_cid = $clienti['$cid'];
		}

		$tid = getTextNode($banner, 'tid');
		// verifico se ho gia' inserito questa categoria
		if(!in_array($tid, $categorie_inserte)){
			// ... mi scorro tutte le categorie
			foreach($categories as $category){
				// ... trovo la categoria che mi interessa
				if($tid == getTextNode($category, 'id', 0)){
					$artbannerscategoiriesplus = new mosArtCategory($database);

					$artbannerscategoiriesplus->name = getTextNode($category, 'name');
					$artbannerscategoiriesplus->description = getTextNode($category, 'description');
					$artbannerscategoiriesplus->published = getTextNode($category, 'published', 0);

					// salvo il cliente
					$artbannerscategoiriesplus->store();

					// memorizzo il nuovo ID del cliente
					$new_tid = $database->insertid();
					break;
				}
			}
			// memorizzo la categoria appena inserita
			$categorie_inserte[] = $tid;
			$categorie['$tid'] = $new_tid;
		} else{
			$new_tid = $categorie['$tid'];
		}

		// inserisco il banner
		$artbannersplus = new mosArtBanner($database);

		$artbannersplus->cid = $new_cid;
		$artbannersplus->tid = $new_tid;
		$artbannersplus->type = getTextNode($banner, 'banner');
		$artbannersplus->name = getTextNode($banner, 'name');
		$artbannersplus->imp_total = getTextNode($banner, 'imp_total', 0);
		$artbannersplus->imp_made = getTextNode($banner, 'imp_made', 0);
		$artbannersplus->clicks = getTextNode($banner, 'clicks', 0);
		$artbannersplus->image_url = getTextNode($banner, 'image_url');
		$artbannersplus->click_url = getTextNode($banner, 'click_url');
		$artbannersplus->last_show = getTextNode($banner, 'last_show', '0000-00-00 00:00:00');
		$artbannersplus->msec = getTextNode($banner, 'msec', 0);
		$artbannersplus->state = getTextNode($banner, 'state', 0);
		$artbannersplus->reccurtype = getTextNode($banner, 'reccurtype', 0);
		$artbannersplus->reccurweekdays = getTextNode($banner, 'reccurweekdays');
		$artbannersplus->custom_banner_code = getTextNode($banner, 'custom_banner_code');
		$artbannersplus->access = getTextNode($banner, 'access', 0);
		$artbannersplus->target = getTextNode($banner, 'target');
		$artbannersplus->border_value = getTextNode($banner, 'border_value', 0);
		$artbannersplus->border_style = getTextNode($banner, 'border_style');
		$artbannersplus->border_color = getTextNode($banner, 'border_color');
		$artbannersplus->click_value = getTextNode($banner, 'click_value', 0);
		$artbannersplus->complete_clicks = getTextNode($banner, 'complete_clicks', 0);
		$artbannersplus->imp_value = getTextNode($banner, 'imp_value', 0);
		$artbannersplus->publish_up_date = getTextNode($banner, 'publish_up_date', '0000-00-00');
		$artbannersplus->publish_up_time = getTextNode($banner, 'publish_up_time', '00:00:00');
		$artbannersplus->publish_down_date = getTextNode($banner, 'publish_down_date', '0000-00-00');
		$artbannersplus->publish_down_time = getTextNode($banner, 'publish_down_time', '00:00:00');
		$artbannersplus->alt = getTextNode($banner, 'alt');
		$artbannersplus->title = getTextNode($banner, 'title');

		// salvo il banner
		$artbannersplus->store();

		// memorizzo il nuovo ID del banner
		$new_artbannersplus_id = $database->insertid();
	}

	@unlink($media_path . $userfile_name);
	mosRedirect("index2.php?option=$option&catid=0&cliid=0", _ABP_RESTORE_OK);
}

function doBackup(){
	global $mosConfig_db, $mosConfig_sitename;

	$database = database::getInstance();

	$UserAgent = $_SERVER['HTTP_USER_AGENT'];

	if(preg_match('/Opera(/| )([0-9].[0-9]{1,2})/', $UserAgent)){
		$UserBrowser = "Opera";
	} elseif(preg_match('/MSIE ([0-9].[0-9]{1,2})/', $UserAgent)){
		$UserBrowser = "IE";
	} else{
		$UserBrowser = '';
	}

	/* Determine the mime type and file extension for the output file */
	//$filename  = date("Ymdhis").'_artbannersplus.sql';
	$filename = date("Ymdhis") . '_artbannersplus.xml';
	$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';

	/* Store all the tables we want to back-up in variable $tables[] */
	$tables = array();
	$tables[] = '#__banners_categories';
	$tables[] = '#__banners_clients';
	$tables[] = '#__banners';

	/* Store all the FIELD TYPES being backed-up (text fields need to be delimited) in variable $FieldType*/
	foreach($tables as $tblval){
		$database->setQuery("SHOW FIELDS FROM $tblval");
		$database->query();
		$fields = $database->loadObjectList();
		foreach($fields as $field){
			$FieldType[$tblval][$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
		}
	}

	require_once (JPATH_BASE . '/includes/domit/xml_domit_include.php');

	//instantiate a new DOMIT! document
	$xmldoc = new DOMIT_Document();

	//create XML declaration
	$xmlDecl = &$xmldoc->createProcessingInstruction('xml', 'version="1.2"');

	//append XML declaration to new DOMIT_Document
	$xmldoc->appendChild($xmlDecl);

	//create backup node
	$rootElement = &$xmldoc->createElement('artbannerplus_backup');

	//append cdlibrary node to new DOMIT_Document
	$xmldoc->appendChild($rootElement);

	/* Okay, here's the meat & potatoes */
	foreach($tables as $tblval){
		//create backup node
		$TableElement = &$xmldoc->createElement($tblval);

		$database->setQuery("SELECT * FROM $tblval");
		$rows = $database->loadObjectList();
		foreach($rows as $row){
			$record = &$xmldoc->createElement('record');

			$arr = mosObjectToArray($row);
			foreach($arr as $key => $value){
				//create backup node
				$fieldElement = &$xmldoc->createElement($key);

				///create and append text node to name element
				$fieldElement->appendChild($xmldoc->createTextNode($value));

				$record->appendChild($fieldElement);
			}

			//append name element to cd element
			$TableElement->appendChild($record);

		}

		$rootElement->appendChild($TableElement);
	}

	$OutBuffer = $xmldoc->toNormalizedString();

	/* Send the HTML headers */
	// dump anything in the buffer
	@ob_end_clean();
	ob_start();
	header('Content-Type: ' . $mime_type);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

	if($UserBrowser == 'IE'){
		header('Content-Disposition: inline; filename="' . $filename . '"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	} else{
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Pragma: no-cache');
	}

	echo $OutBuffer;
	ob_end_flush();
	ob_start();
	//// do no more
	exit();
}

/*
** Return state of the banner
*/
function getStato($row){

	if($row->imp_made == $row->imp_total){
		return BANNER_TERMINATO;
	}

	$iRet = BANNER_NON_PUBBLICATO;

	if($row->state == '1'){

		$now = mosCurrentDate("%Y-%m-%d");
		$time = mosCurrentDate("%H:%M:%S");

		if($now < $row->publish_up_date){
			$iRet = BANNER_IN_ATTIVAZIONE;
		} else
			if($now == $row->publish_up_date && $time < $row->publish_up_time){
				$iRet = BANNER_IN_ATTIVAZIONE;
			} else
				if($now < $row->publish_down_date || $row->publish_down_date == '0000-00-00'){

					$iRet = BANNER_ATTIV0;
					if($row->publish_down_time < $time && $row->publish_down_time != '00:00:00'){
						$iRet = BANNER_TERMINATO;
					}

				} else
					if($now == $row->publish_down_date && ($time <= $row->publish_down_time || $row->publish_down_time == '00:00:00')){
						$iRet = BANNER_ATTIV0;
					} else
						if($now >= $row->publish_down_date){
							$iRet = BANNER_TERMINATO;
						}
	}
	return $iRet;
}