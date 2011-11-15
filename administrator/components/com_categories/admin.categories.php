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

require_once ($mainframe->getPath('admin_html'));

define('COM_IMAGE_BASE',JPATH_BASE.DS.'images'.DS.'stories');

// get parameters from the URL or submitted form
$section = stripslashes(strval(mosGetParam($_REQUEST,'section','content')));

$cid = josGetArrayInts('cid');

switch($task) {
	case 'new':
		editCategory(0,$section);
		break;

	case 'edit':
		editCategory(intval($cid[0]));
		break;

	case 'editA':
		editCategory(intval($id));
		break;

	case 'movesave':
		moveCategorySave($cid,$section);
		break;

	case 'copyselect':
		js_menu_cache_clear();
		copyCategorySelect($option,$cid,$section);
		break;

	case 'copysave':
		js_menu_cache_clear();
		copyCategorySave($cid,$section);
		break;

	case 'go2menu':
	case 'go2menuitem':
	case 'menulink':
	case 'save':
	case 'apply':
	case 'save_and_new':
		js_menu_cache_clear();
		saveCategory($task);
		break;

	case 'remove':
		js_menu_cache_clear();
		removeCategories($section,$cid);
		break;

	case 'publish':
		js_menu_cache_clear();
		publishCategories($section,$id,$cid,1);
		break;

	case 'unpublish':
		js_menu_cache_clear();
		publishCategories($section,$id,$cid,0);
		break;

	case 'cancel':
		cancelCategory();
		break;

	case 'orderup':
		js_menu_cache_clear();
		orderCategory(intval($cid[0]),-1);
		break;

	case 'orderdown':
		js_menu_cache_clear();
		orderCategory(intval($cid[0]),1);
		break;

	case 'accesspublic':
		js_menu_cache_clear();
		accessMenu(intval($cid[0]),0,$section);
		break;

	case 'accessregistered':
		js_menu_cache_clear();
		accessMenu(intval($cid[0]),1,$section);
		break;

	case 'accessspecial':
		js_menu_cache_clear();
		accessMenu(intval($cid[0]),2,$section);
		break;

	case 'saveorder':
		js_menu_cache_clear();
		saveOrder($cid,$section);
		break;

	default:
		showCategories($section,$option);
		break;
}

/**
 * Compiles a list of categories for a section
 * @param string The name of the category section
 */
function showCategories($section,$option) {
	global $database,$mainframe,$mosConfig_list_limit,$mosConfig_dbprefix;

	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit",'limit',$mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$section}limitstart",'limitstart',0));

	$section_name = '';
	$order = "\n ORDER BY c.ordering, c.name";

	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__categories";
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once (JPATH_BASE_ADMIN.'/includes/pageNavigation.php');
	$pageNav = new mosPageNav($total,$limitstart,$limit);

	$tablesAllowed = $database->getTableList();


	$query = "SELECT  c.*, c.checked_out as checked_out_contact_category, g.name AS groupname, u.name AS editor, '0' AS active, '0' AS trash,"
			."0 AS checked_out"
			."\n FROM #__categories AS c"
			."\n LEFT JOIN #__users AS u ON u.id = c.checked_out"
			."\n LEFT JOIN #__groups AS g ON g.id = c.access"
			."\n AND c.published != -2"
			."\n GROUP BY c.id"
			.$order;

	$database->setQuery($query,$pageNav->limitstart,$pageNav->limit);

	$rows = $database->loadObjectList('id');

	if($database->getErrorNum()) {
		echo $database->stderr();
		return;
	}

	$cat_ids = array();
	foreach ($rows as $row) {
		$cat_ids[]=$row->id;
		unset($row);
	}

	$new_rows = array();

	foreach($rows as $v) {
		$new_rows[] = $v;
	}

	$rows = $new_rows;
	unset($new_rows);

	categories_html::show($rows,$section,$section_name,$pageNav,$lists,$type);
}

/**
 * Compiles information to add or edit a category
 * @param string The name of the category section
 * @param integer The unique id of the category to edit (0 if new)
 * @param string The name of the current user
 */
function editCategory($uid = 0,$section = '') {
	global $database,$my,$mainframe;

	$type = strval(mosGetParam($_REQUEST,'type',''));
	$redirect = strval(mosGetParam($_REQUEST,'section','content'));


	$row = new mosCategory($database);
	// load the row from the db table
	$row->load((int)$uid);

	// fail if checked out not by 'me'
	if($row->checked_out && $row->checked_out != $my->id) {
		mosRedirect('index2.php?option=categories&section='.$row->section,str_replace("#CATNAME#",$row->title,_CATEGORY_IS_EDITING_NOW));
	}

	$lists['links'] = 0;
	$menus = null;
	$selected_folders = null;
	if($uid) {
		// existing record
		$row->checkout($my->id);

		// code for Link Menu
		switch($row->section) {
			case 'com_weblinks':
				$and = "\n AND type = 'weblink_category_table'";
				$link = _TABLE_LINKS_CATEGORY;
				break;

			case 'com_newsfeeds':
				$and = "\n AND type = 'newsfeed_category_table'";
				$link = _TABLE_NEWSFEEDS_CATEGORY;
				break;

			case 'com_contact_details':
				$and = "\n AND type = 'contact_category_table'";
				$link = _TABLE_CATEGORY_CONTACTS;
				break;

			default:
				$and = '';
				$link = '';
				break;
		}

		// content
		if($row->section > 0) {
			$query = "SELECT* FROM #__menu WHERE componentid = ".(int)$row->id." AND ( type = 'content_archive_category' OR type = 'content_blog_category' OR type = 'content_category' )";
			$database->setQuery($query);
			$menus = $database->loadObjectList();

			$count = count($menus);
			for($i = 0; $i < $count; $i++) {
				switch($menus[$i]->type) {
					case 'content_category':
						$menus[$i]->type = _TABLE_CATEGORY_CONTENT;
						break;

					case 'content_blog_category':
						$menus[$i]->type = _BLOG_CATEGORY_CONTENT;
						break;

					case 'content_archive_category':
						$menus[$i]->type = _BLOG_CATEGORY_ARCHIVE;
						break;
				}
			}
			$lists['links'] = 1;

			// handling for MOSImage directories
			if(trim($row->params)) {
				// get params definitions
				$params = new mosParameters($row->params,$mainframe->getPath('com_xml','com_categories'),'component');
				$temps = $params->get('imagefolders','');

				$temps = explode(',',$temps);
				foreach($temps as $temp) {
					$selected_folders[] = mosHTML::makeOption($temp,$temp);
				}
			} else {
				$selected_folders[] = mosHTML::makeOption('*2*');
			}
		} else {
			$query = "SELECT* FROM #__menu WHERE componentid = ".(int)$row->id.$and;
			$database->setQuery($query);
			$menus = $database->loadObjectList();

			$count = count($menus);
			for($i = 0; $i < $count; $i++) {
				$menus[$i]->type = $link;
			}
			$lists['links'] = 1;
		}
	} else {
		// new record
		$row->section = $section;
		$row->published = 1;
		$menus = null;

		// handling for MOSImage directories
		if($row->section == 'content') {
			$selected_folders[] = mosHTML::makeOption('*2*');
		}
	}

	// make order list
	$order = array();
	$query = "SELECT COUNT(*) FROM #__categories WHERE section = ".$database->Quote($row->section);
	$database->setQuery($query);
	$max = intval($database->loadResult()) + 1;

	for($i = 1; $i < $max; $i++) {
		$order[] = mosHTML::makeOption($i);
	}

	// build the html select list for sections
	$section_name = 'N/A';
	$lists['section'] = '<input type="hidden" name="section" value="'.$row->section.'" />'.$section_name;


	// build the html select list for category types
	$types[] = mosHTML::makeOption('',_SEL_TYPE);
	if($row->section == 'com_contact_details') {
		$types[] = mosHTML::makeOption('contact_category_table',_TABLE_CATEGORY_CONTACTS);
	} else
	if($row->section == 'com_newsfeeds') {
		$types[] = mosHTML::makeOption('newsfeed_category_table',_TABLE_NEWSFEEDS_CATEGORY);
	} else
	if($row->section == 'com_weblinks') {
		$types[] = mosHTML::makeOption('weblink_category_table',_TABLE_LINKS_CATEGORY);
	} else {
		$types[] = mosHTML::makeOption('content_category',_TABLE_CATEGORY_CONTENT);
		$types[] = mosHTML::makeOption('content_blog_category',_BLOG_CATEGORY_CONTENT);
		$types[] = mosHTML::makeOption('content_archive_category',_BLOG_CATEGORY_ARCHIVE);
	} // if
	$lists['link_type'] = mosHTML::selectList($types,'link_type','class="inputbox" size="1"','value','text');

	// build the html select list for ordering
	$query = "SELECT ordering AS value, title AS text"
			."\n FROM #__categories"
			."\n WHERE section = ".$database->Quote($row->section)
			."\n ORDER BY ordering";
	$lists['ordering'] = stripslashes(mosAdminMenus::SpecificOrdering($row,$uid,$query));

	// build the select list for the image positions
	$active = ($row->image_position?$row->image_position:'left');
	$lists['image_position'] = mosAdminMenus::Positions('image_position',$active,null,0,0);
	// Imagelist
	$lists['image'] = mosAdminMenus::Images('image',$row->image);
	// build the html select list for the group access
	$lists['access'] = mosAdminMenus::Access($row);
	// build the html radio buttons for published
	$lists['published'] = mosHTML::yesnoRadioList('published','class="inputbox"',$row->published);
	// build the html select list for menu selection
	$lists['menuselect'] = mosAdminMenus::MenuSelect();

	// handling for MOSImage directories
	if($row->section > 0 || $row->section == 'content') {
		// list of folders in images/stories/
		$imgFiles = recursive_listdir(COM_IMAGE_BASE);
		$len = strlen(COM_IMAGE_BASE);

		$folders[] = mosHTML::makeOption('*2*',_USE_SECTION_SETTINGS);
		$folders[] = mosHTML::makeOption('*#*','---------------------');
		$folders[] = mosHTML::makeOption('*1*',_ALL);
		$folders[] = mosHTML::makeOption('*0*',_NOT_EXISTS);
		$folders[] = mosHTML::makeOption('*#*','---------------------');
		$folders[] = mosHTML::makeOption('/');
		foreach($imgFiles as $file) {
			$folders[] = mosHTML::makeOption(substr($file,$len));
		}

		$lists['folders'] = mosHTML::selectList($folders,'folders[]','class="inputbox" size="17" multiple="multiple"','value','text',$selected_folders);
	}

	categories_html::edit($row,$lists,$redirect,$menus);
}

/**
 * Saves the catefory after an edit form submit
 * @param string The name of the category section
 */
function saveCategory($task) {
	global $database;
	josSpoofCheck();

	$menu = strval(mosGetParam($_POST,'menu','mainmenu'));
	$menuid = intval(mosGetParam($_POST,'menuid',0));
	$redirect = strval(mosGetParam($_POST,'redirect',''));
	$oldtitle = stripslashes(strval(mosGetParam($_POST,'oldtitle',null)));

	$row = new mosCategory($database);
	if(!$row->bind($_POST,'folders')) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->title = addslashes($row->title);
	$row->name = addslashes($row->name);

	// handling for MOSImage directories
	if($row->section > 0) {
		$folders = mosGetParam($_POST,'folders',array());
		$folders = implode(',',$folders);

		if(strpos($folders,'*2*') !== false) {
			$folders = '*2*';
		} else
		if(strpos($folders,'*1*') !== false) {
			$folders = '*1*';
		} else
		if(strpos($folders,'*0*') !== false) {
			$folders = '*0*';
		} else
		if(strpos($folders,',*#*') !== false) {
			$folders = str_replace(',*#*','',$folders);
		} else
		if(strpos($folders,'*#*,') !== false) {
			$folders = str_replace('*#*,','',$folders);
		} else
		if(strpos($folders,'*#*') !== false) {
			$folders = str_replace('*#*','',$folders);
		}

		$row->params = 'imagefolders='.$folders;
	}

	if(!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$templates = new ContentTemplate();
	$row->templates = $templates->prepare_for_save(mosGetParam($_POST,'templates',array()));

	if(!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();
	$row->updateOrder("section = ".$database->Quote($row->section));

	if($oldtitle) {
		if($oldtitle != $row->title) {
			$query = "UPDATE #__menu SET name = ".$database->Quote($row->title)." WHERE name = ".$database->Quote($oldtitle)." AND type = 'content_category'";
			$database->setQuery($query);
			$database->query();
		}
	}

	if(!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if($redirect == 'content') {
		// clean any existing cache files
		mosCache::cleanCache('com_content');
	}

	switch($task) {
		case 'go2menu':
			mosRedirect('index2.php?option=com_menus&menutype='.$menu);
			break;

		case 'go2menuitem':
			mosRedirect('index2.php?option=com_menus&menutype='.$menu.'&task=edit&hidemainmenu=1&id='.$menuid);
			break;

		case 'menulink':
			menuLink($row->id);
			break;

		case 'apply':
			mosRedirect('index2.php?option=com_categories&section='.$redirect.'&task=editA&hidemainmenu=1&id='.$row->id,_CATEGORY_CHANGES_SAVED);
			break;

		/* boston, после сохранения возвращаемся в окно добавления новой категории*/
		case 'save_and_new':
			$msg = $row->title._COM_CATEGORIES_SAVED_2;
			mosRedirect('index2.php?option=com_categories&task=new',$msg);
			break;

		case 'save':
		default:
			mosRedirect('index2.php?option=com_categories&section='.$redirect,_COM_CATEGORIES_SAVED);
			break;
	}
}

/**
 * Deletes one or more categories from the categories table
 * @param string The name of the category section
 * @param array An array of unique category id numbers
 */
function removeCategories($section,$cid) {
	global $database,$mosConfig_dbprefix;
	josSpoofCheck();
	if(count($cid) < 1) {
		echo "<script> alert('"._CHOOSE_CATEGORY_TO_REMOVE."'); window.history.go(-1);</script>\n";
		exit;
	}

	if(intval($section) > 0) {
		$table = 'content';
	} else
	if(strpos($section,'com_') === 0) {
		$table = substr($section,4);
	} else {
		$table = $section;
	}

	$tablesAllowed = $database->getTableList();
	if(!in_array($mosConfig_dbprefix.$table,$tablesAllowed)) {
		$table = 'content';
	}
	mosArrayToInts($cid);
	$cids = 'c.id='.implode(' OR c.id=',$cid);
	$query = "SELECT c.id, c.name, COUNT( s.catid ) AS numcat FROM #__categories AS c LEFT JOIN `#__$table` AS s ON s.catid = c.id WHERE ( $cids ) GROUP BY c.id";
	$database->setQuery($query);

	if(!($rows = $database->loadObjectList())) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}

	$err = array();
	$cid = array();
	foreach($rows as $row) {
		if($row->numcat == 0) {
			$cid[] = $row->id;
		} else {
			$err[] = $row->name;
		}
	}

	if(count($cid)) {
		mosArrayToInts($cid);
		$cids = 'id='.implode(' OR id=',$cid);
		$query = "DELETE FROM #__categories WHERE ( $cids )";
		$database->setQuery($query);
		if(!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	if($section == 'content') {
		// clean any existing cache files
		mosCache::cleanCache('com_content');
	}

	if(count($err)) {
		$cids = implode("\', \'",$err);
		$msg = str_replace("#CIDS#",$cids,_CANNOT_REMOVE_CATEGORY);
		mosRedirect('index2.php?option=com_categories&section='.$section.'&mosmsg='.$msg);
	}
	$msg = _CONTENT_CATEGORIES.': '.$names.' - '._OBJECTS_DELETED;
	mosRedirect('index2.php?option=com_categories&section='.$section,$msg);
}

/**
 * Publishes or Unpublishes one or more categories
 * @param string The name of the category section
 * @param integer A unique category id (passed from an edit form)
 * @param array An array of unique category id numbers
 * @param integer 0 if unpublishing, 1 if publishing
 * @param string The name of the current user
 */
function publishCategories($section,$categoryid = null,$cid = null,$publish = 1) {
	global $database,$my;
	josSpoofCheck();

	if(!is_array($cid)) {
		$cid = array();
	}
	if($categoryid) {
		$cid[] = $categoryid;
	}

	if(count($cid) < 1) {
		$action = $publish? _PUBLISHED : _UNPUBLISHED;
		echo "<script> alert('"._CHOOSE_CATEGORY_FOR_." $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts($cid);
	$cids = 'id='.implode(' OR id=',$cid);

	$query = "UPDATE #__categories SET published = ".(int)$publish."\n WHERE ( $cids ) AND ( checked_out = 0 OR ( checked_out = ".(int)$my->id." ) )";
	$database->setQuery($query);
	if(!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if(count($cid) == 1) {
		$row = new mosCategory($database);
		$row->checkin($cid[0]);
	}

	if($section == 'content') {
		mosCache::cleanCache('com_content');
	}

	mosRedirect('index2.php?option=com_categories&section='.$section);
}

/**
 * Cancels an edit operation
 * @param string The name of the category section
 * @param integer A unique category id
 */
function cancelCategory() {
	global $database;
	josSpoofCheck();

	$redirect = strval(mosGetParam($_POST,'redirect',''));

	$row = new mosCategory($database);
	$row->bind($_POST);
	$row->checkin();

	mosRedirect('index2.php?option=com_categories&section='.$redirect);
}

/**
 * Moves the order of a record
 * @param integer The increment to reorder by
 */
function orderCategory($uid,$inc) {
	global $database;
	josSpoofCheck();

	$row = new mosCategory($database);
	$row->load((int)$uid);
	$row->move($inc,"section = ".$database->Quote($row->section));

	// clean any existing cache files
	mosCache::cleanCache('com_content');

	mosRedirect('index2.php?option=com_categories&section='.$row->section);
}

/**
 * Save the item(s) to the menu selected
 */
function moveCategorySave($cid,$sectionOld) {
	global $database;
	josSpoofCheck();

	if(!is_array($cid) || count($cid) < 1) {
		echo "<script> alert('"._CHOOSE_OBJECT_TO_MOVE."'); window.history.go(-1);</script>\n";
		exit;
	}

	$sectionMove = intval(mosGetParam($_REQUEST,'sectionmove',''));
	if(!$sectionMove) {
		mosRedirect('index.php?option=com_categories&mosmsg=An error has occurred');
	}

	mosArrayToInts($cid);
	$cids = 'id='.implode(' OR id=',$cid);
	$query = "UPDATE #__categories SET section = ".$sectionMove." WHERE ( $cids )";
	$database->setQuery($query);
	if(!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	// mosArrayToInts( $cid ); // Just done a few lines earlier
	$cids = 'catid='.implode(' OR catid=',$cid);

	$sectionNew = new mosSection($database);
	$sectionNew->load($sectionMove);

	if($sectionOld == 'content') {
		// clean any existing cache files
		mosCache::cleanCache('com_content');
	}

	$msg = ((count($cid) - 1)? _CATEGORIES_MOVED_TO : _CATEGORY_MOVED_TO ).''.$sectionNew->name;
	mosRedirect('index2.php?option=com_categories&section='.$sectionOld.'&mosmsg='.$msg);
}

/**
 * Form for copying item(s) to a specific menu
 */
function copyCategorySelect($option,$cid,$sectionOld) {
	global $database;

	$redirect = mosGetParam($_POST,'section','content');
	;

	if(!is_array($cid) || count($cid) < 1) {
		echo "<script> alert('"._CHOOSE_OBJECT_TO_MOVE."'); window.history.go(-1);</script>\n";
		exit;
	}

	## query to list selected categories
	mosArrayToInts($cid);
	$cids = 'a.id='.implode(' OR a.id=',$cid);
	$query = "SELECT a.name, a.section"."\n FROM #__categories AS a"."\n WHERE ( $cids )";
	$database->setQuery($query);
	$items = $database->loadObjectList();

	$contents = array();

	// build the html select list
	$SectionList ='';

	categories_html::copyCategorySelect($option,$cid,$SectionList,$items,$sectionOld,$contents,$redirect);
}


/**
 * Save the item(s) to the menu selected
 */
function copyCategorySave($cid,$sectionOld) {
	global $database;
	josSpoofCheck();

	$sectionMove = intval(mosGetParam($_REQUEST,'sectionmove',''));
	if(!$sectionMove) {
		mosRedirect('index.php?option=com_categories&mosmsg=An error has occurred');
	}

	$contentid = josGetArrayInts('item',$_REQUEST);

	$category = new mosCategory($database);
	foreach($cid as $id) {
		$category->load((int)$id);
		$category->id = null;
		$category->title = _CATEGORY_COPYING.' '.$category->title;
		$category->name = _CATEGORY_COPYING.' '.$category->name;
		$category->section = $sectionMove;
		if(!$category->check()) {
			echo "<script> alert('".$category->getError().
					"'); window.history.go(-1); </script>\n";
			exit();
		}

		if(!$category->store()) {
			echo "<script> alert('".$category->getError().
					"'); window.history.go(-1); </script>\n";
			exit();
		}
		$category->checkin();
		// stores original catid
		$newcatids[]["old"] = $id;
		// pulls new catid
		$newcatids[]["new"] = $category->id;
	}

	$content = new mosContent($database);
	foreach($contentid as $id) {
		$content->load((int)$id);
		$content->id = null;
		$content->sectionid = $sectionMove;
		$content->hits = 0;
		foreach($newcatids as $newcatid) {
			if($content->catid == $newcatid['old']) {
				$content->catid = $newcatid['new'];
			}
		}
		if(!$content->check()) {
			echo "<script> alert('".$content->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if(!$content->store()) {
			echo "<script> alert('".$content->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$content->checkin();
	}

	$sectionNew = new mosSection($database);
	$sectionNew->load($sectionMove);

	if($sectionOld == 'content') {
		// clean any existing cache files
		mosCache::cleanCache('com_content');
	}

	$msg = ((count($cid) - 1)? _CATEGORIES_COPIED_TO : _CATEGORY_COPIED_TO).''.$sectionNew->name;
	mosRedirect('index2.php?option=com_categories&section='.$sectionOld.'&mosmsg='.$msg);
}

/**
 * changes the access level of a record
 * @param integer The increment to reorder by
 */
function accessMenu($uid,$access,$section) {
	global $database;

	$row = new mosCategory($database);
	$row->load((int)$uid);
	$row->access = $access;

	if(!$row->check()) {
		return $row->getError();
	}
	if(!$row->store()) {
		return $row->getError();
	}

	if($section == 'content') {
		// clean any existing cache files
		mosCache::cleanCache('com_content');
	}

	mosRedirect('index2.php?option=com_categories&section='.$section);
}

function menuLink($id) {
	global $database;
	josSpoofCheck();

	$category = new mosCategory($database);
	$category->bind($_POST);
	$category->checkin();

	$redirect = strval(mosGetParam($_POST,'redirect',''));
	$menu = stripslashes(strval(mosGetParam($_POST,'menuselect','')));
	$name = strval(mosGetParam($_POST,'link_name',''));
	$sectionid = mosGetParam($_POST,'sectionid','');
	$type = strval(mosGetParam($_POST,'link_type',''));

	$name = stripslashes(ampReplace($name));

	switch($type) {
		case 'content_category':
			$link = 'index.php?option=com_content&task=category&sectionid='.$sectionid.
					'&id='.$id;
			$menutype = _TABLE_CATEGORY_CONTENT;
			break;

		case 'content_blog_category':
			$link = 'index.php?option=com_content&task=blogcategory&id='.$id;
			$menutype = _BLOG_CATEGORY_CONTENT;
			break;

		case 'content_archive_category':
			$link = 'index.php?option=com_content&task=archivecategory&id='.$id;
			$menutype = _BLOG_CATEGORY_ARCHIVE;
			break;

		case 'contact_category_table':
			$link = 'index.php?option=com_contact&catid='.$id;
			$menutype = _TABLE_CATEGORY_CONTACTS;
			break;

		case 'newsfeed_category_table':
			$link = 'index.php?option=com_newsfeeds&catid='.$id;
			$menutype = _TABLE_NEWSFEEDS_CATEGORY;
			break;

		case 'weblink_category_table':
			$link = 'index.php?option=com_weblinks&catid='.$id;
			$menutype = _TABLE_LINKS_CATEGORY;
			break;
	}

	$row = new mosMenu($database);
	$row->menutype = $menu;
	$row->name = $name;
	$row->type = $type;
	$row->published = 1;
	$row->componentid = $id;
	$row->link = $link;
	$row->ordering = 9999;

	if($type == 'content_blog_category') {
		$row->params = 'categoryid='.$id;
	}

	if(!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder("menutype = ".$database->Quote($menu));

	if($redirect == 'content') {
		mosCache::cleanCache('com_content');
	}

	$msg = $name.' ( '.$menutype.' ) in menu: '.$menu.' successfully created';
	mosRedirect('index2.php?option=com_categories&section='.$redirect.'&task=editA&hidemainmenu=1&id='.$id,$msg);
}

function saveOrder(&$cid,$section) {
	global $database;
	josSpoofCheck();

	$total = count($cid);
	$order = josGetArrayInts('order');

	$row = new mosCategory($database);
	$conditions = array();

	// update ordering values
	for($i = 0; $i < $total; $i++) {
		$row->load((int)$cid[$i]);
		if($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if(!$row->store()) {
				echo "<script> alert('".$database->getErrorMsg().
						"'); window.history.go(-1); </script>\n";
				exit();
			} // if
			// remember to updateOrder this group
			$condition = "section=".$database->Quote($row->section);
			$found = false;
			foreach($conditions as $cond)
				if($cond[1] == $condition) {
					$found = true;
					break;
				} // if
			if(!$found) {
				$conditions[] = array($row->id,$condition);
			}

		} // if
	} // for

	// execute updateOrder for each group
	foreach($conditions as $cond) {
		$row->load($cond[0]);
		$row->updateOrder($cond[1]);
	} // foreach

	if($section == 'content') {
		// clean any existing cache files
		mosCache::cleanCache('com_content');
	}

	$msg = _NEW_ORDER_SAVED;
	mosRedirect('index2.php?option=com_categories&section='.$section,$msg);
} // saveOrder

function recursive_listdir($base) {
	static $filelist = array();
	static $dirlist = array();

	if(is_dir($base)) {
		$dh = opendir($base);
		while(false !== ($dir = readdir($dh))) {
			if($dir !== '.' && $dir !== '..' && is_dir($base.'/'.$dir) && strtolower($dir)
					!== 'cvs' && strtolower($dir) !== '.svn') {
				$subbase = $base.'/'.$dir;
				$dirlist[] = $subbase;
				$subdirlist = recursive_listdir($subbase);
			}
		}
		closedir($dh);
	}
	return $dirlist;
}