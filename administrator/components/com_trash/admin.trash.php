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
if(!($acl->acl_check('administration','manage','users',$my->usertype,'components','com_trash'))) {
	mosRedirect('index2.php',_NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));
require_once ($mainframe->getPath('class','com_frontpage'));

$mid = josGetArrayInts('mid');
$cid = josGetArrayInts('cid');

switch($task) {
	case 'deleteconfirm':
		viewdeleteTrash($cid,$mid,$option);
		break;

	case 'delete':
		deleteTrash($cid,$option);
		break;
	// полная очистка содержимого корзины
	case 'deleteall':
		clearTrash();
		break;

	case 'restoreconfirm':
		viewrestoreTrash($cid,$mid,$option);
		break;

	case 'restore':
		restoreTrash($cid,$option);
		break;

	default:
		viewTrash($option);
		break;
}


/**
 * Compiles a list of trash items
 */
function viewTrash($option) {
	global $mosConfig_list_limit;

	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance();

	$catid = $mainframe->getUserStateFromRequest("catid{$option}",'catid','content');
	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit",'limit',$mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart",'limitstart',0));

	require_once (JPATH_BASE.'/'.JADMIN_BASE.'/includes/pageNavigation.php');

	if($catid == "content") {
		// get the total number of content
		$query = "SELECT count(*)"
				."\n FROM #__content AS c"
				."\n LEFT JOIN #__categories AS cc ON cc.id = c.catid"
				."\n LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope = 'content'"
				."\n WHERE c.state = -2";
		$database->setQuery($query);
		$total = $database->loadResult();
		$pageNav = new mosPageNav($total,$limitstart,$limit);

		// Query content items
		$query = "SELECT c.*, g.name AS groupname, cc.name AS catname, s.name AS sectname"
				."\n FROM #__content AS c"
				."\n LEFT JOIN #__categories AS cc ON cc.id = c.catid"
				."\n LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope='content'"
				."\n INNER JOIN #__groups AS g ON g.id = c.access"
				."\n LEFT JOIN #__users AS u ON u.id = c.checked_out"
				."\n WHERE c.state = -2"."\n ORDER BY s.name, cc.name, c.title";
		$database->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$content = $database->loadObjectList();

		$num = $total;
		if($limit < $total) {
			$num = $limit;
		}
		for($i = 0; $i < $num - 1; $i++) {
			if(($content[$i]->sectionid == 0) && ($content[$i]->catid == 0)) {
				$content[$i]->sectname = 'Static Content';
			}
		}
	} else {
		// get the total number of menu
		$query = "SELECT count(*) FROM #__menu AS m LEFT JOIN #__users AS u ON u.id = m.checked_out WHERE m.published = -2";
		$database->setQuery($query);
		$total = $database->loadResult();

		$pageNav = new mosPageNav($total,$limitstart,$limit);

		// Query menu items
		$query = "SELECT m.name AS title, m.menutype AS sectname, m.type AS catname, m.id AS id"
				."\n FROM #__menu AS m"
				."\n LEFT JOIN #__users AS u ON u.id = m.checked_out"
				."\n WHERE m.published = -2"
				."\n ORDER BY m.menutype, m.ordering, m.ordering, m.name";
		$database->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$content = $database->loadObjectList();
	}

	// Build the select list
	$listselect = array();
	$listselect[] = mosHTML::makeOption('content',_CONTENT_ITEMS);
	$listselect[] = mosHTML::makeOption('menu',_MENU_ITEMS);
	$selected = "all";

	$list = mosHTML::selectList($listselect,'catid','class="inputbox" size="1" onchange="document.adminForm.submit();"','value','text',$catid);

	HTML_trash::showList($option,$content,$pageNav,$list,$catid);
}


/**
 * Compiles a list of the items you have selected to permanently delte
 */
function viewdeleteTrash($cid,$mid,$option) {

	$database = database::getInstance();

	if(!in_array(0,$cid)) {
		// Content Items query
		mosArrayToInts($cid);
		$cids = 'a.id='.implode(' OR a.id=',$cid);
		$query = "SELECT a.title AS name FROM #__content AS a WHERE ( $cids ) ORDER BY a.title";
		$database->setQuery($query);
		$items = $database->loadObjectList();
		$id = $cid;
		$type = 'content';
	} else
	if(!in_array(0,$mid)) {
		// Content Items query
		mosArrayToInts($mid);
		$mids = 'a.id='.implode(' OR a.id=',$mid);
		$query = "SELECT a.name FROM #__menu AS a WHERE ( $mids ) ORDER BY a.name";
		$database->setQuery($query);
		$items = $database->loadObjectList();
		$id = $mid;
		$type = 'menu';
	}

	HTML_trash::showDelete($option,$id,$items,$type);
}


/**
 * Permanently deletes the selected list of trash items
 */
function deleteTrash($cid,$option) {
	josSpoofCheck();

	$database = database::getInstance();
	$config = Jconfig::getInstance();

	$type = mosGetParam($_POST,'type',array(0));
	$total = count($cid);

	if(Jconfig::getInstance()->config_use_content_delete_mambots) {
		global $_MAMBOTS;
		$_MAMBOTS->loadBotGroup('content');
	}

	if($type == 'content') {
		$obj = new mosContent($database);
		$fp = new mosFrontPage($database);
		foreach($cid as $id) {
			$id = intval($id);
			// обработка мамботами удаления содержимого
			$config->config_use_content_delete_mambots ? $_MAMBOTS->trigger('onDeleteContent',array($id)) : null;
			$obj->delete($id);
			$fp->delete($id);
		}
	} else
	if($type == 'menu') {
		$obj = new mosMenu($database);
		foreach($cid as $id) {
			$id = intval($id);
			$obj->delete($id);
		}
	}

	$msg = $total." "._OBJECTS_DELETED;
	mosRedirect('index2.php?option='.$option,$msg);
}

/**
 * Полная очистка корзины
 *
 */
function clearTrash() {
	josSpoofCheck();

	$database = database::getInstance();
	$config = Jconfig::getInstance();

	// выбираем из таблицы содержимого записи помеченные как удалённые
	$sql_content = 'SELECT id FROM #__content WHERE state="-2" ';
	$database->setQuery($sql_content);
	// загрузим массив идентификаторов элементов меню для удаления
	$cid = $database->loadResultArray();
	// число элементов содержимого для удаления
	$total_content = count($cid);
	// создаём объекты содержимого и главной страницы
	$obj = new mosContent($database);
	$fp = new mosFrontPage($database);

	if($config->config_use_content_delete_mambots) {
		global $_MAMBOTS;
		$_MAMBOTS->loadBotGroup('content');
	}

	// перебирая по циклу элементы помеченные как удалённые произведём их физическое удаление
	foreach($cid as $id) {
		$id = intval($id);
		// обработка мамботами удаления содержимого
		$config->config_use_content_delete_mambots ? $_MAMBOTS->trigger('onDeleteContent',array($id)) : null;
		$obj->delete($id);
		$fp->delete($id);
	}
	// выбираем из таблицы содержимого меню помеченные как удалённые
	$sql_content = 'SELECT id FROM #__menu WHERE published="-2" ';
	$database->setQuery($sql_content);
	$cid = $database->loadResultArray();
	// число пунктов меню для удаления
	$total_menu = count($cid);
	// создадим объект меню
	$obj = new mosMenu($database);
	// перебирая по циклу элементы помеченные как удалённые произведём их физическое удаление
	foreach($cid as $id) {
		$id = intval($id);
		$obj->delete($id);
	}
	//собираем итоговое сообщение
	$msg = _SUCCESS_DELETION.': content: '.$total_content.', menu: '.$total_menu;
	mosRedirect('index2.php?option='.$option,$msg);
}
/**
 * Compiles a list of the items you have selected to permanently delte
 */
function viewrestoreTrash($cid,$mid,$option) {
	$database = database::getInstance();

	if(!in_array(0,$cid)) {
		// Content Items query
		mosArrayToInts($cid);
		$cids = 'a.id='.implode(' OR a.id=',$cid);
		$query = "SELECT a.title AS name"
				."\n FROM #__content AS a"
				."\n WHERE ( $cids )"
				."\n ORDER BY a.title";
		$database->setQuery($query);
		$items = $database->loadObjectList();
		$id = $cid;
		$type = "content";
	} else
	if(!in_array(0,$mid)) {
		// Content Items query
		mosArrayToInts($mid);
		$mids = 'a.id='.implode(' OR a.id=',$mid);
		$query = "SELECT a.name"."\n FROM #__menu AS a"."\n WHERE ( $mids )"."\n ORDER BY a.name";
		$database->setQuery($query);
		$items = $database->loadObjectList();
		$id = $mid;
		$type = "menu";
	}

	HTML_trash::showRestore($option,$id,$items,$type);
}


/**
 * Restores items selected to normal - restores to an unpublished state
 */
function restoreTrash($cid,$option) {
	josSpoofCheck();

	$database = database::getInstance();

	$type = mosGetParam($_POST,'type',array(0));

	$total = count($cid);

	// restores to an unpublished state
	$state = 0;
	$ordering = 9999;

	if($type == 'content') {
		// query to restore content items
		mosArrayToInts($cid);
		$cids = 'id='.implode(' OR id=',$cid);
		$query = "UPDATE #__content"."\n SET state = ".(int)$state.", ordering = ".(int)$ordering."\n WHERE ( $cids )";
		$database->setQuery($query);
		if(!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	} else
	if($type == 'menu') {
		sort($cid);

		foreach($cid as $id) {
			$check = 1;
			$row = new mosMenu($database);
			$row->load($id);

			// check if menu item is a child item
			if($row->parent != 0) {
				$query = "SELECT id FROM #__menu"
						."\n WHERE id = ".(int)$row->parent
						."\n AND ( published = 0 OR published = 1 )";
				$database->setQuery($query);
				$check = $database->loadResult();

				if(!$check) {
					// if menu items parent is not found that are published/unpublished make it a root menu item
					$query = "UPDATE #__menu SET parent = 0, published = ".(int)$state.", ordering = 9999 WHERE id = ".(int)$id;
				}
			}

			if($check) {
				// query to restore menu items
				$query = "UPDATE #__menu SET published = ".(int)$state.", ordering = 9999 WHERE id = ".(int)$id;
			}

			$database->setQuery($query);
			if(!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
	}

	$msg = $total." "._OBJECTS_RESTORED;
	mosRedirect("index2.php?option=".$option,$msg);
}