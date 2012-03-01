<?php

/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_boss'))) {
	mosRedirect('index2.php', _NOT_AUTH);
}

$mainframe = mosMainFrame::getInstance();
$mainframe->addJS(JPATH_SITE . '/administrator/components/com_boss/js/function.js');
$mainframe->addCSS(JPATH_SITE . '/administrator/components/com_boss/css/boss_admin.css');

require_once( $mainframe->getPath('admin_html') );
require_once( $mainframe->getPath('class') );

include_once( JPATH_BASE . '/components/com_boss/lang/russian.php' );
require_once( JPATH_BASE . '/components/com_boss/boss.tools.php' );

$directory = intval(mosGetParam($_REQUEST, 'directory', 0));

if ($directory == 0) {
	$database = database::getInstance();
	$directory = $database->setQuery("SELECT MIN(id) FROM #__boss_config")->loadResult();
	if (!$directory){
		$directory = 0;
	}
}

$task = mosGetParam($_REQUEST, 'task', "");

$params = HTML_boss::getLayout();
$layout = $params['layout'];
$act = $params['act'];

switch ($act) {

	case "manager":
		switch ($task) {
			case "new":
				addDirectory();
				break;

			case "edit": {
					$tid = mosGetParam($_REQUEST, 'tid', 0);
					if (is_array($tid)) {
						$tid = $tid[0];
					}
					editConfiguration($tid);
				}
				break;

			case "delete" :
				deleteDirectory();
				break;

			default:
				showDirectories($directory);
				break;
		}
		break;

	case "configuration":
		switch ($task) {
			case "save":
            case "apply":
				saveConfiguration($directory);
				break;

			case "edit":
			default:
				//проверяем наличие каталогов, если нет - завершаем страницу
				if (!HTML_boss::check_dir($directory))
					break;
				editConfiguration($directory);
				break;
		}
		break;

	case "contents":
		switch ($task) {
			case "save" :
			case "apply" :
				saveContent($directory);
				break;

			case "edit" :
				displayContent($directory);
				break;

			case "copy" :
				displayContent($directory);
				break;

			case "new" :
				$id = '';
				newContent($directory);
				break;

			case "delete" :
				deleteContent($directory);
				break;

			case "publish" :
				publishContent($directory);
				break;

			default:
				//проверяем наличие каталогов, если нет - завершаем страницу
				if (!HTML_boss::check_dir($directory))
					break;
				listContents($directory);
				break;
		}
		break;

	case "categories" :
		switch ($task) {
			case "save" :
            case "apply" :
				saveCategory($directory);
				break;

			case "edit" :
				displayCategory($directory);
				break;

			case "new" :
				newCategory($directory);
				break;

			case "delete" :
				deleteCategory($directory);
				break;

			case "publish" :
				publishCategory($directory);
				break;

			case 'orderup':
				$tid = mosGetParam($_REQUEST, 'tid', array(0));
				if (!is_array($tid)) {
					$tid = array(0);
				}
				orderCategory(intval($tid[0]), -1, $directory);
				break;

			case 'orderdown':
				$tid = mosGetParam($_REQUEST, 'tid', array(0));
				if (!is_array($tid)) {
					$tid = array(0);
				}
				orderCategory(intval($tid[0]), 1, $directory);
				break;

			case 'saveorder':
				$tid = mosGetParam($_REQUEST, 'tid', array(0));
				if (!is_array($tid)) {
					$tid = array(0);
				}
				saveOrder($tid, $directory);
				break;

			default:
				//проверяем наличие каталогов, если нет - завершаем страницу
				if (!HTML_boss::check_dir($directory)) {
					break;
				}
				listCategories($directory);
		}
		break;

	case "templates":
		//type_tmpl - темплейт контента = 1, темплейт категории = 2
		$type_tmpl = mosGetParam($_REQUEST, 'type_tmpl', 0);
		$template = mosGetParam($_REQUEST, 'template', '');
		$source_file = mosGetParam($_REQUEST, 'source_file', '');

		switch ($task) {
			case "delete" :
				deleteTemplate($directory);
				break;

			case "edit_tmpl" :
				editTemplate($directory, $template, $type_tmpl);
				break;

			case "edit_tmpl_fields" :
				editTemplateFields($directory, $template, $type_tmpl);
				break;

			case "save":
			case "apply":
				saveTemplate($directory, $template, $type_tmpl);
				break;

			case "save_tmpl_fields":
				saveTemplateFields($directory, $template);
				break;
			case "edit_tmpl_source":
				editTmplSource($directory, $template, $source_file);
				break;
			case "save_tmpl_source":
				saveTmplSource($directory, $template, $source_file);
				break;
			default:
				//проверяем наличие каталогов, если нет - завершаем страницу
				if (!HTML_boss::check_dir($directory)) {
					break;
				}
				listTemplates($directory);
				break;
		}
		break;

	case "fieldimage":
		switch ($task) {
			case "delete" :
				deleteFieldImage($directory);
				break;

			case "upload":
				uploadFieldImage($directory);
				break;
			default:
				//проверяем наличие каталогов, если нет - завершаем страницу
				if (!HTML_boss::check_dir($directory)) {
					break;
				}
				listFieldImages($directory);
				break;
		}
		break;

	case "fields": {
			switch ($task) {
				case "new":
				case "edit":
					editField($directory);
					break;

				case "save":
				case "apply":
					saveField($directory);
					break;

				case "delete":
					removeField($directory);
					break;

				case 'orderup':
					$tid = mosGetParam($_REQUEST, 'tid', array(0));
					if (!is_array($tid)) {
						$tid = array(0);
					}
					orderField(intval($tid[0]), -1, $directory);
					break;

				case 'orderdown':
					$tid = mosGetParam($_REQUEST, 'tid', array(0));
					if (!is_array($tid)) {
						$tid = array(0);
					}
					orderField(intval($tid[0]), 1, $directory);
					break;

				case 'saveorder':
					$tid = mosGetParam($_REQUEST, 'tid', array(0));
					if (!is_array($tid)) {
						$tid = array(0);
					}
					saveFieldOrder($tid, $directory);
					break;

				case "publish" :
					publishField($directory);
					break;

				case "required" :
					requiredField($directory);
					break;

				default:
					//проверяем наличие каталогов, если нет - завершаем страницу
					if (!HTML_boss::check_dir($directory)) {
						break;
					}
					showField($directory);
					break;
			}
		}
		break;

	case "plugins": {
			switch ($task) {

				case "delete" :
					deletePlugin($directory);
					break;

				case "upload":
					installPlugin($directory);
					break;

				default:
					//проверяем наличие каталогов, если нет - завершаем страницу
					if (!HTML_boss::check_dir($directory)) {
						break;
					}
					listPlugins($directory);
					break;
			}
		}
		break;

	case "csv":
		switch ($task) {
			case "sql_update" :
				saveContent($directory);
				break;

			case "csv_preview" :
				CSVpreview($directory);
				break;

			case "csv_insert" :
				$id = '';
				CSVincert($directory);
				break;

			case "csv_update" :
				$id = '';
				CSVupdate($directory);
				break;

			case "build_insert" :
				CSVbuildinsert($directory);
				break;

			case "build_list" :
				CSVbuildinsert($directory);
				break;


			case "csc_metod" :
			default:
				//проверяем наличие каталогов, если нет - завершаем страницу
				if (!HTML_boss::check_dir($directory)) {
					break;
				}
				CSVmetod($directory);
				break;
		}
		break;

	case "export_import":
		switch ($task) {

			case "export":
				exportDirectory($directory);
				break;

			case "import" :
				importDirectory();
				break;

			case "import_joostina" :
				importJoostina($directory);
				break;

			default:
				//проверяем наличие каталогов, если нет - завершаем страницу
				if (!HTML_boss::check_dir($directory)) {
					break;
				}
				showImpExpForm($directory);
				break;
		}
		break;

	case "users": {
			switch ($task) {

				case "delete" :
					deleteUserInfo($directory);
					break;

                case "new":
				case "edit":
					editUserInfo($directory);
					break;

				case "save":
				case "apply":
					saveUserInfo($directory);
					break;

				default:
					//проверяем наличие каталогов, если нет - завершаем страницу
					if (!HTML_boss::check_dir($directory)) {
						break;
					}
					listUsers($directory);
					break;
			}
		}
		break;

	default:
		displayMain($directory);
		break;
}

/* * *************************************************************************** */
/* * *******               Manager    ****************************************** */
/* * *************************************************************************** */

//объект-лист каталогов
function getDirectories() {
	$database = database::getInstance();
	$directories = $database->setQuery("SELECT id,name FROM #__boss_config")->loadObjectList("id");
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	return $directories;
}

//массив всех категорий
function getAllCategories($directory, $rows = null) {
	if ($rows == null) {
		$database = database::getInstance();

        $src_cat = mosGetParam($_REQUEST, 'src_cat', '');
        $select_publish = mosGetParam($_REQUEST, 'select_publish', 0);

        $wheres = array();

        if ($src_cat) {
            $wheres[]= "c.name LIKE '%$src_cat%'";
        }
        if ($select_publish > 0) {
            switch ($select_publish) {

	    		case 1:
	    			$wheres[] = "c.published = '1'";
	    			break;
	    		case 2:
	    			$wheres[] = "c.published = '0'";
	    			break;
	    	}
        }
        $where = (count($wheres) > 0) ? "WHERE ".implode(' AND ', $wheres)." ": '';
        $q  = "SELECT c.* FROM #__boss_" . $directory . "_categories as c ";
        $q .= $where;
        $q .= "ORDER BY c.parent,c.ordering";
	    $rows = $database->setQuery($q)->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
		}
	}
	// establish the hierarchy of the menu
	$children = array();
	// first pass - collect children
	foreach ($rows as $v) {
		$pt = $v->parent;
		$list = isset($children[$pt]) ? $children[$pt] : array();
		array_push($list, $v);
		$children[$pt] = $list;
	}
	return $children;
}

// список каталогов
function showDirectories($directory) {
	$directories = getDirectories();
	HTML_boss::showDirectories($directories, $directory);
}

// создание нового каталога
function addDirectory() {
	installNewDirectory();
	mosRedirect("index2.php?option=com_boss&act=manager");
}

// удаление каталога
function deleteDirectory() {

	$tid = mosGetParam($_REQUEST, 'tid', 0);
	if (!is_array($tid) || count($tid) < 1) {
		echo "<script type=\"text/javascript\"> alert('" . BOSS_SELECT_GROUP_TO_BE_DELETED . "'); window.history.go(-1);</script>\n";
		exit;
	}
	$msg = '';

	if (count($tid)) {
		foreach ($tid as $id) {
			removeDirectory($id);
		}
	}

	mosRedirect("index2.php?option=com_boss&act=manager", $msg);
}

/* * ************************************************************************* */
/* * ****************       Configuration          *************************** */
/* * ************************************************************************* */

function saveConfiguration($directory) {
    $task = mosGetParam($_REQUEST, 'task');
	$database = database::getInstance();
	$row = new jDirectoryConf($database);

	// bind it to the table
	if (!$row->bind($_POST)) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// store it in the db
	if (!$row->store()) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

     if($task == 'apply')
         $link = "index2.php?option=com_boss&act=configuration&task=edit&directory=$directory";
     else
         $link = "index2.php?option=com_boss&act=configuration&directory=$directory";
	mosRedirect($link, BOSS_CONFIGURATION_SAVED);
}

function getTemplates() {
	//$templates
	$path = JPATH_BASE . '/templates/com_boss';
	if (!is_dir($path)) {
		echo '<script type="text/javascript">';
		echo 'alert(\'Установите хотя-бы один шаблон, желательно чтобы это был шаблон "default".\')';
		echo '</script>';
		return;
	}

	$handle = opendir($path);

	while ($file = readdir($handle)) {
		$dir = mosPathName($path . '/' . $file, false);
		if (is_dir($dir)) {
			if (($file != ".") && ($file != "..")) {
				$templates[] = $file;
			}
		}
	}
	closedir($handle);
	return $templates;
}

function editConfiguration($directory) {

	$database = database::getInstance();

	$conf = getConfig($directory);

	$templates = getTemplates();
	$directories = getDirectories();

	$sort_fields = $database->setQuery("SELECT `fieldid`, `title` FROM #__boss_" . $directory . "_fields WHERE `sort` = 1")->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return;
	}
    $filter = get_plugins($directory, 'filters');
    $filters = array();
    foreach ($filter as $key => $plug) {
		$filters[] = mosHTML::makeOption($key, $plug->getFieldName());
	}
	HTML_boss::editConfiguration($conf, $templates, $directory, $directories, $sort_fields, $filters);
}

/* * ************************************************************************* */
/* * ****************       Categories             *************************** */
/* * ************************************************************************* */

function saveOrder(&$tid, $directory) {
	$database = database::getInstance();

	$total = count($tid);
	$order = mosGetParam($_POST, 'order', array(0));
	$row = new jDirectoryCategory($database, $directory);

	// update ordering values
	for ($i = 0; $i < $total; $i++) {
		$row->load($tid[$i]);
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
				exit();
			} // if
		} // if
	} // for
	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory", BOSS_CATEGORIES_REORDER);
}

// saveOrder

/**
 * Moves the order of a record
 * @param integer The increment to reorder by
 */
function orderCategory($uid, $inc, $directory) {
	$database = database::getInstance();

	$row = new jDirectoryCategory($database, $directory);
	$row->load($uid);
	$row->move($inc, "parent = $row->parent");

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory");
}

function displayCategory($directory) {

	$id = mosGetParam($_REQUEST, 'tid', array(0));
	if (is_array($id)) {
		$id = $id[0];
	}

	if (!isset($id)) {
		mosRedirect("index2.php?option=com_boss&act=contest&directory=$directory", BOSS_ERROR_IN_URL);
		return;
	}

	$children = getAllCategories($directory);

	$database = database::getInstance();
	$rows = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_categories WHERE id=" . $id)->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$directories = getDirectories();
	$templates = getTemplates();

	HTML_boss::displaycategory(@$rows[0], $children, $directory, $directories, $templates);
}

function newCategory($directory) {
	$database = database::getInstance();
	$children = getAllCategories($directory);

	$row = new jDirectoryCategory($database, $directory);

	$directories = getDirectories();
	$templates = getTemplates();

	HTML_boss::displaycategory($row, $children, $directory, $directories, $templates);
}

function saveCategory($directory) {

	$database = database::getInstance();
	$task = mosGetParam($_REQUEST, 'task');
	$row = new jDirectoryCategory($database, $directory);

	// bind it to the table
	if (!$row->bind($_POST)) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// store it in the db
	if (!$row->store()) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// get configuration
	$conf = getConfig($directory);

	// image2 delete
	if ($_POST['cb_image'] == "delete") {
		$pict = JPATH_BASE . "/images/boss/$directory/categories/" . $row->id . "cat.jpg";
		if (file_exists($pict)) {
			unlink($pict);
		}
		$pict = JPATH_BASE . "/images/boss/$directory/categories/" . $row->id . "cat_t.jpg";
		if (file_exists($pict)) {
			unlink($pict);
		}
	}

	if (isset($_FILES['cat_image'])) {
		if ($_FILES['cat_image']['size'] > $conf->max_image_size) {
			mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory", BOSS_IMAGETOOBIG);
			return;
		}
	}

	// image1 upload
	if (isset($_FILES['cat_image']) and !$_FILES['cat_image']['error']) {
		$path = JPATH_BASE . "/images/boss/$directory/categories/";
		createImageAndThumb(
                        $_FILES['cat_image']['tmp_name'],
                        $_FILES['cat_image']['name'],
                        $path,
                        $row->id . "cat.jpg",
                        $row->id . "cat_t.jpg",
                        $conf->cat_max_width,
                        $conf->cat_max_height,
                        $conf->cat_max_width_t,
                        $conf->cat_max_height_t
                        );
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

     if($task == 'apply')
         $link = "index2.php?option=com_boss&directory=$directory&act=categories&task=edit&tid[]=$row->id";
     else
         $link = "index2.php?option=com_boss&act=categories&directory=$directory";
	mosRedirect($link, BOSS_CATEGORY_SAVED);
}

function deleteCategory($directory) {

	$tid = $_POST['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo "<script> alert('Select an category to delete'); window.history.go(-1);</script>\n";
		exit();
	}

	$database = database::getInstance();

	if (count($tid)) {
		$ids = implode(',', $tid);
		$database->setQuery("SELECT * FROM #__boss_" . $directory . "_categories \nWHERE id not IN ($ids) AND parent IN ($ids)");
		if ($database->loadResult()) {
			echo "<script> alert('" . BOSS_DELETE_CATEGORY_SELECT_CHIDLS . "'); window.history.go(-1); </script>\n";
			exit();
		}
		$database->setQuery("DELETE FROM #__boss_" . $directory . "_categories \nWHERE id IN ($ids)");
	}
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	if (count($tid)) {
		$ids = implode(',', $tid);
		$database->setQuery("DELETE FROM #__boss_" . $directory . "_contents \nWHERE category IN ($ids)");
	}
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory", BOSS_CATEGORIES_DELETED);
}

function listCategories($directory) {

	$conf = getConfig($directory);
	$defaultTemplate = $conf->template;
	$database = database::getInstance();

    $src_cat = mosGetParam($_REQUEST, 'src_cat', '');
    $select_publish = mosGetParam($_REQUEST, 'select_publish', 0);

    $wheres = array();

    if ($src_cat) {
         $wheres[]= "c.name LIKE '%$src_cat%'";
    }
    if ($select_publish > 0) {
        switch ($select_publish) {

			case 1:
				$wheres[] = "c.published = '1'";
				break;
			case 2:
				$wheres[] = "c.published = '0'";
				break;
		}
    }
    $where = (count($wheres) > 0) ? "WHERE ".implode(' AND ', $wheres)." ": '';
    $q  = "SELECT c.*, COUNT(cont.id) as num_cont FROM #__boss_" . $directory . "_categories as c ";
    $q .= "LEFT JOIN #__boss_" . $directory . "_content_category_href as cch ON cch.category_id = c.id ";
    $q .= "LEFT JOIN #__boss_" . $directory . "_contents as cont ON cont.id = cch.content_id ";
    $q .= $where;
    $q .= "GROUP BY c.id ";
    $q .= "ORDER BY c.parent,c.ordering";
	$rows = $database->setQuery($q)->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav(count($rows), 0, count($rows));

	$children = getAllCategories($directory, $rows);
	$directories = getDirectories();

	HTML_boss::listcategories(count($rows), $children, $pageNav, $directory, $directories, $defaultTemplate);
}

function publishCategory($directory) {

	$tid = $_GET['tid'];

	if (!is_array($tid) || count($tid) < 1) {
		echo "<script> alert('Select an Content to publish'); window.history.go(-1);</script>\n";
		exit();
	}

	if (isset($_GET['publish'])) {
		$publish = (int) $_GET['publish'];
	} else {
		mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory", BOSS_ERROR_IN_URL);
		return;
	}

	$database = database::getInstance();
	if (count($tid)) {
		$ids = implode(',', $tid);
		$database->setQuery("UPDATE #__boss_" . $directory . "_categories SET `published` = '$publish' WHERE `id` IN ($ids) ");
	}
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory");
}

/* * ************************************************************************* */
/* * ****************       Contents               *************************** */
/* * ************************************************************************* */

function saveContent($directory) {
	$database = database::getInstance();
	$row = new jDirectoryContent($database, $directory);

	// get configuration
	$conf = getConfig($directory);

	//get fields
	$fields = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE `published` = 1 AND `profile` = 0 ")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	//Save Field
	$row->save($directory, $fields, $conf);
}

function displayContent($directory) {

        $task = mosGetParam($_REQUEST, 'task', '');
	$id = mosGetParam($_REQUEST, 'tid', array(0));
	if (is_array($id)) {
		$id = $id[0];
	}

	if (!isset($id)) {
		mosRedirect("index2.php?option=com_boss&act=contents&directory=$directory", BOSS_ERROR_IN_URL);
		return;
	}

	$children = getAllCategories($directory);

	$database = database::getInstance();
	$row = null;
        $database->setQuery("SELECT * FROM #__boss_" . $directory . "_contents WHERE id=" . $id . " LIMIT 1")->loadObject($row);
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	$selected_categ = $database->setQuery("SELECT category_id FROM #__boss_" . $directory . "_content_category_href WHERE content_id=" . $id)->loadResultArray();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// get configuration
	$conf = getConfig($directory);

	//get fields
	$database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE `published` = 1 AND `profile` = 0 ORDER BY `ordering`, `fieldid`");
	$fields = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	//get value fields
	$fieldvalues = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_field_values ORDER by ordering ")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$users = $database->setQuery("SELECT u.* FROM #__users as u ORDER BY u.username ASC")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$field_values = array();
	// first pass - collect children
	foreach ($fieldvalues as $v) {
		$pt = $v->fieldid;
		$list = isset($field_values[$pt]) ? $field_values[$pt] : array();
		array_push($list, $v);
		$field_values[$pt] = $list;
	}

	$directories = getDirectories();

	mosMainFrame::addLib('tags');
	$jDirectoryContentTags = new contentTags($database);
	$obj = null;
	$obj->id = $row->id;
	$obj->obj_type = 'com_boss_' . $directory;
	$tags = $jDirectoryContentTags->load_by($obj);
	$tags = implode(', ', $tags);

	if ($task == 'copy') {
		$rowid = '';
	} else {
		$rowid = $row->id;
	}

	HTML_boss::displayContent($row, $fields, $field_values, $children, $users, $conf->nb_images, $directory, $directories, $selected_categ, $tags, $rowid);
}

function newContent($directory) {

        global $my;
	$database = database::getInstance();
	$children = getAllCategories($directory);

	$row = new jDirectoryContent($database, $directory);

	// get configuration
	$conf = getConfig($directory);

	//get fields
	$fields = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE published = 1 AND `profile` = 0 ORDER by ordering")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	//get value fields
	$fieldvalues = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_field_values ORDER by ordering ")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$field_values = array();
	// first pass - collect children
	foreach ($fieldvalues as $v) {
		$pt = $v->fieldid;
		$list = isset($field_values[$pt]) ? $field_values[$pt] : array();
		array_push($list, $v);
		$field_values[$pt] = $list;
	}

	$directories = getDirectories();

	$users = $database->setQuery("SELECT u.* FROM #__users as u ORDER BY u.username ASC")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$row->userid = $my->id;

	HTML_boss::displayContent($row, $fields, $field_values, $children, $users, $conf->nb_images, $directory, $directories, array(), '', '');
}

function deleteContent($directory) {

	$tid = $_REQUEST['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo sprintf("<script type='text/javascript'> alert('%s'); window.history.go(-1);</script>\n", BOSS_SELECT_CONTENT_TO_BE_DELETED);
		exit();
	}

	// get configuration
	$conf = getConfig($directory);
	$database = database::getInstance();

	foreach ($tid as $contentid) {
		$content = new jDirectoryContent($database, $directory);
		$content->load($contentid);
		if ($content != null) {
			$content->delete($directory, $conf);
		}
	}

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=com_boss&act=contents&directory=$directory", BOSS_CONTENTS_DELETED);
}

function recurseSearch($rows, &$list, $catid) {
	foreach ($rows as $row) {
		if ($row->parent == $catid) {
			$list[] = $row->id;
			recurseSearch($rows, $list, $row->id);
		}
	}
}

function listContents($directory) {
	global $mosConfig_list_limit;
    $mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();
    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );

	$catid = mosGetParam($_REQUEST, 'catid', 0);
    $selectedAutorId = mosGetParam($_REQUEST, 'autor', 0);
    $select_publish = mosGetParam($_REQUEST, 'select_publish', 0);

	if ($catid > 0) {
		$database->setQuery("SELECT c.name, c.id "
				. "FROM #__boss_" . $directory . "_categories as c "
				. "WHERE c.id = " . $catid);
		$cats = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	} else {
		$cats[0]->id = 0;
		$cats[0]->name = "";
	}
	/*	 * ************************* */
	$rows = $database->setQuery("SELECT c.* FROM #__boss_" . $directory . "_categories as c ORDER BY c.parent,c.ordering")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$children = getAllCategories($directory, $rows);

	// establish the hierarchy of the menu
	if ($catid != 0) {
		$list[] = $catid;
		recurseSearch($rows, $list, $catid);
	} else {
		$list = array();
	}
	$listids = implode(',', $list);

    $fields = array();
    $tables = array();
    $wheres = array();

    $fields[]= 'a.*';
    $tables[] = "#__boss_" . $directory . "_contents as a";

    if (!empty($listids)) {
        $fields[]= 'c.name as catname';

        $tables[] = "#__boss_" . $directory . "_categories as c";
		$tables[] = "#__boss_" . $directory . "_content_category_href as cch";

        $wheres[] = "a.id = cch.content_id";
        $wheres[] = "c.id = cch.category_id";
		$wheres[] = "cch.category_id IN ($listids)";
    }

    if($selectedAutorId > 0){
        $wheres[] = "a.userid = " . (int)$selectedAutorId;
    }
    if($select_publish > 0){
        $date = date('Y-m-d');
        switch ($select_publish) {

			case 1:
				$wheres[] = "a.published = 1";
				break;
			case 2:
				$wheres[] = "a.published = 0";
				break;
            case 3:
				$wheres[] = "(a.date_unpublish = '0000-00-00' OR a.date_unpublish < '$date')";
				break;
            case 4:
				$wheres[] = "(a.date_publish = '0000-00-00' OR a.date_publish > '$date')";
				break;
		}
    }

    $where = (count($wheres)>0) ? " WHERE ".implode(' AND ', $wheres) : '';

	$q = "SELECT ".implode(', ', $fields)
         . " FROM ".implode(', ', $tables)
		 . $where
		 . " GROUP BY a.id "
		 . " ORDER BY a.id DESC";

    $total = $database->setQuery($q)->loadResult();
	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{com_boss}limitstart", 'limitstart', 0));
    $pageNav = new mosPageNav($total, $limitstart, $limit);

	$rows = $database->setQuery($q, $limitstart, $limit)->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// get configuration
	$conf = getConfig($directory);

	$directories = getDirectories();

	$categs = $database->setQuery("SELECT c.name, c.id , cch.content_id "
			. "FROM #__boss_" . $directory . "_categories as c, "
			. "#__boss_" . $directory . "_content_category_href as cch "
			. "WHERE cch.category_id = c.id ")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

    $autors = $database->setQuery("SELECT DISTINCT c.userid, u.name "
			. "FROM #__boss_" . $directory . "_contents as c, "
			. "#__users as u "
			. "WHERE u.id = c.userid ORDER BY u.name")->loadObjectList('userid');
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	HTML_boss::listContents($cats[0], $rows, $pageNav, $children, $directory, $directories, $categs, $autors, $selectedAutorId);
}

function publishContent($directory) {

	$tid = $_GET['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo sprintf("<script> alert('%s'); window.history.go(-1);</script>\n", BOSS_SELECT_CONTENT_TO_BE_PUBLISH);
		exit();
	}

	$catid = $_GET['catid'];

	if (isset($_GET['publish'])) {
		$publish = (int) $_GET['publish'];
	} else {
		mosRedirect("index2.php?option=com_boss&act=contents&catid=" . $catid . "&directory=$directory", BOSS_ERROR_IN_URL);
		return;
	}

	$database = database::getInstance();

	if (count($tid)) {
		$ids = implode(',', $tid);
		$database->setQuery("UPDATE #__boss_" . $directory . "_contents SET `published` = '$publish' WHERE `id` IN ($ids) ");
	}
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}
	mosRedirect("index2.php?option=com_boss&act=contents&catid=" . $catid . "&directory=$directory");
}

function displayTools($directory) {
	$directories = getDirectories();
	HTML_boss::displayTools($directory, $directories);
}

function displayMain($directory) {
	$directories = getDirectories();
	HTML_boss::displayMain($directory, $directories);
}

function showField($directory) {

	if ($directory == 0) {
		return;
	}

	$database = database::getInstance();
	$rows = $database->setQuery("SELECT f.* FROM #__boss_" . $directory . "_fields AS f ORDER by f.ordering")->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return;
	}

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav(count($rows), 0, count($rows));

	$directories = getDirectories();

	HTML_boss::showFields($rows, $pageNav, $directory, $directories);
}

function editField($directory) {

	$tid = mosGetParam($_REQUEST, 'tid', 0);
	if (is_array($tid)) {
		$tid = $tid[0];
	}

	$database = database::getInstance();
	$row = new jDirectoryField($database, $directory);
	// load the row from the db table
	$row->load($tid);

	/*	 * ************************* */
	$catstemp = $database->setQuery("SELECT c.* FROM #__boss_" . $directory . "_categories as c ORDER BY c.parent,c.ordering")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// establish the hierarchy of the menu
	$cats = array();
	// first pass - collect children
	foreach ($catstemp as $v) {
		$pt = $v->parent;
		$listtemp = isset($cats[$pt]) ? $cats[$pt] : array();
		array_push($listtemp, $v);
		$cats[$pt] = $listtemp;
	}

	$types = array();
	$lists = array();
	$sort_direction = array();
	$display_title_list = array();

	$plugins = get_plugins($directory, 'fields');

	foreach ($plugins as $key => $plug) {
		$types[] = mosHTML::makeOption($key, $plug->getFieldName());
	}

	$database->setQuery("SELECT fieldtitle,fieldvalue "
			. "\n FROM #__boss_" . $directory . "_field_values"
			. "\n WHERE fieldid=$tid"
			. "\n ORDER BY ordering");
	$fvalues = $database->loadObjectList();

	$sort_direction[] = mosHTML::makeOption('DESC', BOSS_CMN_SORT_DESC);
	$sort_direction[] = mosHTML::makeOption('ASC', BOSS_CMN_SORT_ASC);

	$display_title_list[] = mosHTML::makeOption(0, BOSS_NO_DISPLAY);
	$display_title_list[] = mosHTML::makeOption(1, BOSS_DISPLAY_DETAILS);
	$display_title_list[] = mosHTML::makeOption(2, BOSS_DISPLAY_LIST);
	$display_title_list[] = mosHTML::makeOption(3, BOSS_DISPLAY_LIST_AND_DETAILS);

	$lists['display_title'] = mosHTML::selectList($display_title_list, 'display_title', 'class="inputbox" size="1"', 'value', 'text', $row->display_title);
	$lists['type'] = mosHTML::selectList($types, 'type', 'class="inputbox" size="1" onchange="selType(this.options[this.selectedIndex].value);"', 'value', 'text', $row->type, '', BOSS_SELECT);
	$lists['required'] = mosHTML::yesnoSelectList('required', 'class="inputbox" size="1"', $row->required);
	$lists['profile'] = mosHTML::yesnoSelectList('profile', 'class="inputbox" size="1"', $row->profile);
	$lists['editable'] = mosHTML::yesnoSelectList('editable', 'class="inputbox" size="1"', $row->editable);
	$lists['searchable'] = mosHTML::yesnoSelectList('searchable', 'class="inputbox" size="1"', $row->searchable);
	$lists['sort'] = mosHTML::yesnoSelectList('sort', 'class="inputbox" size="1"', $row->sort);
	$lists['sort_direction'] = mosHTML::selectList($sort_direction, 'sort_direction', 'class="inputbox" size="1"', 'value', 'text', $row->sort_direction);
	$lists['published'] = mosHTML::yesnoSelectList('published', 'class="inputbox" size="1"', $row->published);
	$lists['filter'] = mosHTML::yesnoSelectList('filter', 'class="inputbox" size="1"', $row->filter);

	$path = JPATH_BASE . "/images/boss/$directory/fields";
	$handle = opendir($path);

	$fieldimages = array();
	while ($file = readdir($handle)) {
		$dir = mosPathName($path . '/' . $file, false);
		if (!is_dir($dir)) {
			if (($file != ".") && ($file != "..")) {
				$fieldimages[] = $file;
			}
		}
	}
	closedir($handle);

	$directories = getDirectories();

	HTML_boss::editfield($row, $lists, $fvalues, $tid, $cats, count($catstemp), $fieldimages, $directory, $directories);
}

function saveField($dir) {

    $database = database::getInstance();
    $task = mosGetParam($_REQUEST, 'task', '');
    $directories = mosGetParam($_REQUEST, 'directories', array());
    if(count($directories) == 0)
        $directories[] = $dir;
    
    foreach ($directories as $directory){
        $row = new jDirectoryField($database, $directory);

       if (!$row->bind($_POST)) {
          echo "<script type=\"text/javascript\"> alert('" . $row->getError(). "'); window.history.go(-1); </script>\n";
          exit();
       }

       mosMakeHtmlSafe($row);

       $row->name = str_replace(" ", "", strtolower($row->name));

       if (!$row->check()) {
          echo "<script type=\"text/javascript\"> alert('" . $row->getError(). "'); window.history.go(-2); </script>\n";
          exit();
       }
       if (!$row->store($_POST['fieldid'])) {
          echo "<script type=\"text/javascript\"> alert('" . $row->getError(). "'); window.history.go(-2); </script>\n";
          exit();
       }

       if ($row->fieldid > 0) {
          $database->setQuery("DELETE FROM #__boss_" . $directory . "_field_values WHERE fieldid='" . $row->fieldid . "'");
          if (!$database->query())
             echo $database->getErrorMsg();
       } else {
          $maxID = $database->setQuery("SELECT MAX(fieldid) FROM #__boss_" . $directory . "_fields")->loadResult();
          $row->fieldid = $maxID;
          echo $database->getErrorMsg();
       }

       $field_catsid = mosGetParam($_POST, "field_catsid", array());
       $field_catsid = "," . implode(',', $field_catsid). ",";
       if ($field_catsid != "") {
          $query = "UPDATE #__boss_" . $directory . "_fields SET catsid ='$field_catsid' WHERE fieldid=$row->fieldid ";
          $database->setQuery($query)->query();
       }

       //Update Content Fields
       $plugins = get_plugins($directory, 'fields');
       $plugfield = false;
       if (isset($plugins["$row->type"])) {
          $plugfield = $plugins["$row->type"]->saveFieldOptions($directory, $row);
       }

       if ($plugfield == false) {
          //если это поле используется в качестве поля профиля пользователя
          if ($row->profile == 1) {
             //добавляем поле в таблицу профиля
             $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_profile ADD `$row->name` TEXT NOT NULL")->query();
             //удаляем поле из таблицы контента
             $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_contents DROP `$row->name`")->query();
          } else {
             //удаляем поле в таблицу профиля
             $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_profile DROP `$row->name`")->query();
             //добавляем поле из таблицы контента
             $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_contents ADD `$row->name` TEXT NOT NULL")->query();
          }
       }

       // Обновляем порядок полей
        $row->updateOrder($row->fieldid);
        //вычисляем филдид поля в изначальном каталоге
        if($directory == $dir)
            $dirFieldid = $row->fieldid;
    }

    if ($task == 'apply')
        $link = "index2.php?option=com_boss&act=fields&task=edit&tid=$dirFieldid&directory=$dir";
    else
        $link = "index2.php?option=com_boss&act=fields&directory=$dir";
   mosRedirect($link, BOSS_UPDATE_SUCCESSFULL);
}

function removeField($directory) {

	$tid = mosGetParam($_REQUEST, 'tid', 0);

	if (!is_array($tid) || count($tid) < 1) {
		echo sprintf("<script type=\"text/javascript\"> alert('%s'); window.history.go(-1);</script>\n", BOSS_SELECT_CONTENT_TO_BE_DELETED);
		exit;
	}

	$database = database::getInstance();
	foreach ($tid as $id) {
		$row = new jDirectoryField($database, $directory);
		// load the row from the db table
		$row->load($id);

		if (($row->name == "name") || ($row->name == "email") || ($row->name == "ad_text") || ($row->name == "ad_headline")) {
			mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory", BOSS_ERROR_SYSTEM_FIELD);
			return;
		}

		//Update Content Fields
// TODO :boston - а опчему тут результат loadObjectList нигде не используется? WHERE 1 - это по идее true, т.е. можно и не использовать
		$database->setQuery("SELECT $row->name FROM #__boss_" . $directory . "_contents")->loadObjectList();
		if (!$database->getErrorNum()) {
			$database->setQuery("ALTER TABLE #__boss_" . $directory . "_contents DROP `$row->name`");
			$database->query();
		}

		//Update Profile Fields
		$database->setQuery("SELECT $row->name FROM #__boss_" . $directory . "_profile");
		$database->loadObjectList();
		if (!$database->getErrorNum()) {
			$database->setQuery("ALTER TABLE #__boss_" . $directory . "_profile DROP `$row->name`");
			$database->query();
		}
	}

	if (count($tid)) {
		$ids = implode(',', $tid);
		$database->setQuery("DELETE FROM #__boss_" . $directory . "_fields WHERE fieldid IN ($ids)");
	}
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	if (count($tid)) {

		$ids = implode(',', $tid);
		$database->setQuery("DELETE FROM #__boss_" . $directory . "_field_values WHERE fieldid  IN ($ids)");
	}
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory");
}

function saveFieldOrder(&$tid, $directory) {
	$database = database::getInstance();

	$total = count($tid);
	$order = mosGetParam($_POST, 'order', array(0));
	$row = new jDirectoryField($database, $directory);
 	$conditions = array();

	for ($i = 0; $i < $total; $i++) {
		$row->load($tid[$i]);

		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];

			if (!$row->store()) {
			  echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			  exit();
			}

			$condition = (int)$row->published;
			$found = false;

			for ($j = 0, $k = count($conditions); $j < $k; $j++) {
			  $cond = $conditions[$j];

			  if ($cond[1] == $condition) {
				  $found = true;
				  break;
			  }
			}
			if (!$found) {
			  $conditions[] = array($row->id, $condition);
			}
		}
	}

	for ($i = 0, $n = count($conditions); $i < $n; $i++) {
		$condition = $conditions[$i];
		$row->load($condition[0]);
		$row->updateOrder($condition[1]);
	}

	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory", BOSS_FIELDS_REORDER);
}

/**
 * Moves the order of a record
 * @param integer The increment to reorder by
 */
function orderField($uid, $inc, $directory) {
	$database = database::getInstance();

	$row = new jDirectoryField($database, $directory);
	$row->load($uid);
	$row->move($inc, "1");

	// clean any existing cache files
	mosCache::cleanCache('com_boss');

	mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory");
}

function publishField($directory) {
	$database = database::getInstance();

	$tid = $_GET['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo sprintf("<script> alert('%s'); window.history.go(-1);</script>\n", BOSS_SELECT_CONTENT_TO_BE_PUBLISH);
		exit();
	}

	if (isset($_GET['publish'])) {
		$publish = $_GET['publish'];
	} else {
		mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory", BOSS_ERROR_IN_URL);
		return;
	}

	if (count($tid)) {
		$ids = implode(',', $tid);
		$database->setQuery("UPDATE #__boss_" . $directory . "_fields SET `published` = '$publish' WHERE `fieldid` IN ($ids) ");
	}

	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	} else {
		mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory");
	}
}

function requiredField($directory) {

	$tid = $_GET['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo "<script> alert('Select an Content to publish'); window.history.go(-1);</script>\n";
		exit();
	}

	if (isset($_GET['required'])) {
		$required = $_GET['required'];
	} else {
		mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory", BOSS_ERROR_IN_URL);
		return;
	}

	$database = database::getInstance();

	if (count($tid)) {
		$ids = implode(',', $tid);
		$database->setQuery("UPDATE #__boss_" . $directory . "_fields SET `required` = '$required' WHERE `fieldid` IN ($ids) ");
	}
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	} else {
		mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory", BOSS_UPDATE_SUCCESSFULL);
	}
}

function rmdir_rf($dirName) {

	if ($handle = opendir($dirName)) {

		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					rmdir_rf($dirName . '/' . $file);
					@rmdir($dirName . '/' . $file);
				} elseif (is_file($dirName . '/' . $file)) {
					@unlink($dirName . '/' . $file);
				}
			}
		}

		closedir($handle);
	}

	@rmdir($dirName);
}

function copy_folder_rf($pathFrom, $pathTo) {

	mosMakePath(JPATH_BASE, str_replace(JPATH_BASE, '', $pathTo));

	if ($handle = opendir($pathFrom)) {

		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				if (is_dir($pathFrom . '/' . $file)) {
					if(!is_dir($pathTo . '/' . $file)){
                        @mkdir($pathTo . '/' . $file);
                    }
					copy_folder_rf($pathFrom . '/' . $file, $pathTo . '/' . $file);
				} elseif (is_file($pathFrom . '/' . $file)) {
                    if(is_file($pathTo . '/' . $file)) {
                        @unlink ($pathTo . '/' . $file);
                    }
					@copy($pathFrom . '/' . $file, $pathTo . '/' . $file);
				}
			}
		}

		closedir($handle);
	}
}

// удаление каталога и всех его дирректорий
function removeDirectory($id) {
	$database = database::getInstance();

	$database->setQuery("DROP TABLE `#__boss_" . $id . "_categories`, " .
			"`#__boss_" . $id . "_contents`, " .
			"`#__boss_" . $id . "_content_category_href`, " .
			"`#__boss_" . $id . "_field_values`, " .
			"`#__boss_" . $id . "_fields`, " .
			"`#__boss_" . $id . "_groupfields`, " .
			"`#__boss_" . $id . "_groups`, " .
			"`#__boss_" . $id . "_profile`, " .
			"`#__boss_" . $id . "_rating`, " .
			"`#__boss_" . $id . "_reviews`; ")->query();

	$database->setQuery("DELETE FROM `#__boss_config` WHERE `id` = $id")->query();

	rmdir_rf(JPATH_BASE . "/images/boss/$id");
}

function saveGroup($directory, $group) {
	$database = database::getInstance();

	$row = new jDirectoryTemplatePosition($database, $directory);
	if (!$row->bind($group)) {
		echo "<script type=\"text/javascript\"> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script type=\"text/javascript\"> alert('" . $row->getError() . "'); window.history.go(-2); </script>\n";
		exit();
	}
}

function listTemplates($directory) {
	$templates = getTemplates();
	$directories = getDirectories();
	HTML_boss::listTemplates($templates, $directory, $directories);
}

function editTemplate($directory, $template, $type_tmpl) {

	$groupfieldsArray = array();
	$positions = array();
	require(JPATH_BASE . "/templates/com_boss/$template/_service.php");
	if ($type_tmpl == 1) {
		$position = $positions['content'];
		$positionDesc = $positions['content_desc'];
	} else {
		$position = $positions['category'];
		$positionDesc = $positions['category_desc'];
	}
	$query = "SELECT gf.fieldid, gf.ordering, g.name, g.catsid, g.published " .
			"FROM #__boss_" . $directory . "_groupfields AS gf " .
			"LEFT JOIN #__boss_" . $directory . "_groups as g ON gf.groupid = g.id " .
			"WHERE gf.template = '" . $template . "' AND gf.type_tmpl = $type_tmpl " .
			"ORDER BY gf.ordering ASC";

	$database = database::getInstance();
	$groupfields = $database->setQuery($query)->loadObjectList();

	if (count($groupfields) > 0) {
		foreach ($groupfields as $groupfield) {
			$groupfieldsArray[$groupfield->name]['fieldid'][] = $groupfield->fieldid;
			$groupfieldsArray[$groupfield->name][$groupfield->fieldid]['ordering'] = $groupfield->ordering;
			$groupfieldsArray[$groupfield->name]['catsid'] = $groupfield->catsid;
			$groupfieldsArray[$groupfield->name]['published'] = $groupfield->published;
		}
	}
	$query = "SELECT `fieldid`, `name`, `title` " .
			"FROM #__boss_" . $directory . "_fields WHERE published = 1 AND `profile` = 0 " .
			"ORDER BY `title` ASC";
	$fields = $database->setQuery($query)->loadObjectList();

	$catstemp = $database->setQuery("SELECT c.* FROM #__boss_" . $directory . "_categories as c ORDER BY c.parent,c.ordering")->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// establish the hierarchy of the menu
	$cats = array();
	// first pass - collect children
	foreach ($catstemp as $v) {
		$pt = $v->parent;
		$listtemp = isset($cats[$pt]) ? $cats[$pt] : array();
		array_push($listtemp, $v);
		$cats[$pt] = $listtemp;
	}

	$directories = getDirectories();

	HTML_boss::editTemplate($directory, $directories, $template, $type_tmpl, $position, $positionDesc, $groupfieldsArray, $fields, $cats);
}

function editTemplateFields($directory, $template, $type_tmpl) {
	$positions = array();
	require(JPATH_BASE . "/templates/com_boss/$template/_service.php");
	$directories = getDirectories();
	HTML_boss::editTemplateFields($directory, $directories, $template, $type_tmpl, $positions);
}

function saveTemplate($directory, $template, $type_tmpl) {
	$database = database::getInstance();
    $task = mosGetParam($_REQUEST, 'task', '');

	$q = "DELETE FROM #__boss_" . $directory . "_groupfields WHERE `template` = '" . $template . "' AND `type_tmpl` = '" . $type_tmpl . "'";
	$database->setQuery($q)->query();
	$groups = array();
	$fields = array();
	//делаем массивы полей и групп из пост
	foreach ($_POST as $key => $val) {
		if (strpos($key, '|') !== false) {
			$key = explode('|', $key);
			$action = $key[0];
			$groupname = $key[1];
			$fieldid = $key[2];
			switch ($action) {

				case 'published':
					$groups[$groupname]['published'] = $val;
					$groups[$groupname]['name'] = $groupname;
					$groups[$groupname]['template'] = $template;
					$q = "SELECT `id` FROM " .
							"#__boss_" . $directory . "_groups " .
							"WHERE `name` = '" . $groupname . "' AND `template` = '" . $template . "'";
					$groupId = $database->setQuery($q,0,1)->loadResult();

					if (isset($groupId) && $groupId > 0) {
						$groups[$groupname]['id'] = $groupId;
					}
					break;

				case 'catsid':
					$groups[$groupname]['catsid'] = "," . implode(',', $val) . ",";
					break;

				case 'required':
					$ordering = $_POST['ordering|' . $groupname . '|' . $fieldid];

					$fields[] = array(
						'fieldid' => $fieldid,
						'groupname' => $groupname,
						'ordering' => $ordering
					);
					break;
			}
		}
	}

	//записываем группы
	foreach ($groups as $group) {
		saveGroup($directory, $group);
	}

	//записываем связи полей с группами.
	foreach ($fields as $field) {

		$q = "SELECT `id` FROM " .
				"#__boss_" . $directory . "_groups " .
				"WHERE `name` = '" . $field['groupname'] . "' AND `template` = '" . $template . "'";
		$groupid = $database->setQuery($q,0,1)->loadResult();

		$q = "INSERT INTO #__boss_" . $directory . "_groupfields " .
				"(`fieldid`,               `groupid`,      `template`,      `type_tmpl`,      `ordering`) " .
				"VALUES " .
				"('" . $field['fieldid'] . "', '" . $groupid . "', '" . $template . "', '" . $type_tmpl . "', '" . $field['ordering'] . "')";
		$database->setQuery($q)->query();
	}

	mosCache::cleanCache('com_boss');

    if($task == 'apply')
        $link = "index2.php?option=com_boss&directory=$directory&act=templates&task=edit_tmpl&template=$template&type_tmpl=$type_tmpl";
    else
        $link = "index2.php?option=com_boss&act=templates&directory=$directory";
	mosRedirect($link);
}

function saveTemplateFields($directory, $template) {
	$category = $_POST['category'];
	$category_desc = $_POST['category_desc'];
	$content = $_POST['content'];
	$content_desc = $_POST['content_desc'];

	$contents = "<?php\n";
	$contents .= "defined( '_VALID_MOS' ) or die();\n\n";
	$contents .= '$positions = array(' . "\n";

	//записываем позиции категорий
	$contents .= "'category' => array(" . "\n";
	for ($i = 0, $n = count($category); $i < $n; $i++) {
		if (!ini_get('magic_quotes_gpc')) {
			$category[$i] = addslashes($category[$i]);
		}
		if (empty($category[$i]))
			break;
		$contents .= "'" . $category[$i] . "',\n";
	}
	$contents .= "),\n";

	//записываем описания позиций категорий
	$contents .= "'category_desc' => array(" . "\n";
	for ($i = 0, $n = count($category_desc); $i < $n; $i++) {
		if (!ini_get('magic_quotes_gpc')) {
			$category_desc[$i] = addslashes($category_desc[$i]);
		}
		$contents .= "'" . $category_desc[$i] . "',\n";
	}
	$contents .= "),\n";

	//записываем позиции контента
	$contents .= "'content' => array(" . "\n";
	for ($i = 0, $n = count($content); $i < $n; $i++) {
		if (!ini_get('magic_quotes_gpc')) {
			$content[$i] = addslashes($content[$i]);
		}
		if (empty($content[$i]))
			break;
		$contents .= "'" . $content[$i] . "',\n";
	}
	$contents .= "),\n";

	//записываем описания позиций контента
	$contents .= "'content_desc' => array(" . "\n";
	for ($i = 0, $n = count($content_desc); $i < $n; $i++) {
		if (!ini_get('magic_quotes_gpc')) {
			$content_desc[$i] = addslashes($content_desc[$i]);
		}
		$contents .= "'" . $content_desc[$i] . "',\n";
	}
	$contents .= ")\n";

	$contents .= ");\n\n?>";
	if (!is_writable(JPATH_BASE . "/templates/com_boss/$template/_service.php")) {
		mosRedirect("index2.php?option=com_boss&act=templates&directory=$directory", "Configuration file is Not writable");
		return;
	}
	$fp = fopen(JPATH_BASE . "/templates/com_boss/$template/_service.php", 'w');
	fwrite($fp, $contents);
	fclose($fp);

	mosCache::cleanCache('com_boss');
	mosRedirect("index2.php?option=com_boss&act=templates&directory=$directory");
}

function deleteTemplate($directory) {
	$database = database::getInstance();
	$tid = $_POST['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo sprintf("<script> alert('%s'); window.history.go(-1);</script>\n", BOSS_SELECT_TEMPLATE_TO_BE_DELETED);
		exit();
	}
	foreach ($tid as $template) {
		if ($template != "") {
			rmdir_rf(JPATH_BASE . "/templates/com_boss/" . $template);
		}

		$database->setQuery("DELETE FROM #__boss_" . $directory . "_groups \nWHERE `template` = '" . $template . "'")->query();
		$database->setQuery("DELETE FROM #__boss_" . $directory . "_groupfields \nWHERE `template` = '" . $template . "'")->query();
	}

	mosCache::cleanCache('com_boss');
	mosRedirect("index2.php?option=com_boss&act=templates&directory=$directory");
}

function editTmplSource($directory, $template, $source_file) {
	$files = array();
	$source = '';
	$path = JPATH_BASE . "/templates/com_boss/$template";
	$files[] = "css/boss.css";
	$handle = opendir($path);
	while ($file = readdir($handle)) {
		$dir = mosPathName($path, false);
		if (is_dir($dir)) {
			if (($file != ".") && ($file != "..") && (strpos($file, '.php') !== false || strpos($file, '.css') !== false) && strpos($file, '_service.php') === false) {
				$files[] = $file;
			}
		}
	}
	closedir($handle);

	if (!empty($source_file)) {
		$handle = fopen($path . "/" . $source_file, "r");
		$source = fread($handle, 10000);
		$source = htmlspecialchars($source);
		fclose($handle);
	}
	$directories = getDirectories();

	HTML_boss::editTmplSource($directory, $directories, $template, $source_file, $files, $source);
}

function saveTmplSource($directory, $template, $source_file) {
	$path = JPATH_BASE . "/templates/com_boss/$template/$source_file";
	$source = mosGetParam($_REQUEST, 'source', '', _MOS_ALLOWHTML);
	$file = fopen($path, 'w');
	fputs($file, stripslashes($source), strlen($source));
	fclose($file);
	mosRedirect("index2.php?option=com_boss&act=templates&task=edit_tmpl_source&source_file=&template=$template&directory=$directory");
}

function listPlugins($directory) {
    $database = database::getInstance();
    //значение селекта использования
    $used = mosGetParam($_REQUEST, 'used', '');
	$plugins = array();
    $bossPlugins = array();
	$i = 0;
    //используемые плагины
    $usedPlugins = $database->setQuery("SELECT DISTINCT `type` FROM #__boss_" . $directory . "_fields")->loadResultArray();
	$path = JPATH_BASE . "/images/boss/$directory/plugins";
	if (!is_dir($path))
		mkdir($path);

	$handle = opendir($path);
	while ($dir = readdir($handle)) {
		$new_path = $path . '/' . $dir;
		if (is_dir($new_path) && $dir != "." && $dir != "..") {
			$subdir = opendir($new_path);
			while ($file = readdir($subdir)) {

				if (($file != ".") && ($file != "..")) {
					$plugins[$i]['file'] = $file;
					$plugins[$i]['folder'] = $dir;
                    require($new_path.'/'.$file.'/plugin.php');
                    $plugins[$i]['type'] = key($bossPlugins);
                    unset($bossPlugins);

                    if($used === '0' && in_array($plugins[$i]['type'], $usedPlugins)){
                        unset($plugins[$i]);
                    }
                    elseif ($used === '1' && !in_array($plugins[$i]['type'], $usedPlugins)){
                        unset($plugins[$i]);
                    }
					$i++;
				}
			}
			closedir($subdir);
		}
	}
	closedir($handle);

	$directories = getDirectories();

	HTML_boss::listPlugins($directory, $directories, $plugins, $used);
}

/**
 * @param string The class name for the installer
 * @param string The URL option
 * @param string The element name
 */
function installPlugin($directory) {

	// Check that the zlib is available
	if (!extension_loaded('zlib')) {
		mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory", BOSS_ZLIB_NOT_FOUND);
	}

        $userfile = mosGetParam($_FILES, 'userfile', '');
        if (empty ($userfile['name'])) {
		mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory", BOSS_EMPTY_FILENAME);
	}

        $directories = mosGetParam($_REQUEST, 'directories', array());
        if (count($directories)==0) {
		mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory", BOSS_EMPTY_DIRS);
	}

	$bossPlugins = array();		
	$fileProps = explode('.', $userfile['name']);
	$folder = $fileProps[0];
	$name = $fileProps[1];
	$ext = $fileProps[2];
        if ($ext == 'zip') {
                // Extract functions
                require_once( JPATH_BASE . '/administrator/includes/pcl/pclzip.lib.php' );
                require_once( JPATH_BASE . '/administrator/includes/pcl/pclerror.lib.php' );
                $zipfile = new PclZip($userfile['tmp_name']);
                if (substr(PHP_OS, 0, 3) == 'WIN') {
                        define('OS_WINDOWS', 1);
                } else {
                        define('OS_WINDOWS', 0);
                }

        } else {
                require_once( JPATH_BASE . '/includes/Archive/Tar.php' );
                $archive = new Archive_Tar($userfile['tmp_name']);
                $archive->setErrorHandling(PEAR_ERROR_PRINT);
        }

        foreach($directories as $dir){
            if ($ext == 'zip') {
                    // Extract functions
                    $ret = $zipfile->extract(PCLZIP_OPT_PATH, JPATH_BASE . "/images/boss/$dir/plugins/$folder");
                    if ($ret == 0) {
                            $zipfile->setError(1, 'Unrecoverable error "' . $zipfile->errorName(true) . '"');
                            return false;
                    }
            } else {
                    if (!$archive->extractModify(JPATH_BASE . "/images/boss/$dir/plugins/$folder", '')) {
                            $archive->setError(1, 'Extract Error');
                            return false;
                    }
            }
            require_once(JPATH_BASE . "/images/boss/$dir/plugins/$folder/" . $name . "/plugin.php");

            foreach ($bossPlugins as $plug) {
                    $plug->install($dir);
            }
        }
	mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory");
}

function deletePlugin($directory) {
	$database = database::getInstance();

	$tid = $_POST['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo sprintf("<script> alert('%s'); window.history.go(-1);</script>\n", BOSS_SELECT_PLUGIN_TO_BE_DELETED);
		exit();
	}
	foreach ($tid as $pluginname) {
		$bossPlugins = array();
		$path = JPATH_BASE . "/images/boss/$directory/plugins/$pluginname";

		require_once($path.'/plugin.php');
		foreach ($bossPlugins as $plug) {
			$plug->uninstall($directory);
		}
		rmdir_rf($path);
	}

	mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory");
}

function listFieldImages($directory) {

	$path = JPATH_BASE . "/images/boss/$directory/fields";
	$handle = opendir($path);
	$fieldimages = array();
	while ($file = readdir($handle)) {
		$dir = mosPathName($path . '/' . $file, false);
		if (!is_dir($dir)) {
			if (($file != ".") && ($file != "..")) {
				$fieldimages[] = $file;
			}
		}
	}
	closedir($handle);

	$directories = getDirectories();

	HTML_boss::listFieldImages($fieldimages, $directory, $directories);
}

/**
 * @param string The class name for the installer
 * @param string The URL option
 * @param string The element name
 */
function uploadFieldImage($directory) {

	$userfile = mosGetParam($_FILES, 'userfile', null);
	$filename = $userfile['name'];
	while (file_exists(JPATH_BASE . "/images/boss/$directory/fields/" . $filename)) {
		$filename = "copy_" . $filename;
	}
	is_file($userfile['tmp_name']) ? move_uploaded_file($userfile['tmp_name'], JPATH_BASE . "/images/boss/$directory/fields/" . $filename) : null;

	mosRedirect("index2.php?option=com_boss&act=fieldimage&directory=$directory");
}

function deleteFieldImage($directory) {

	$tid = $_POST['tid'];
	if (!is_array($tid) || count($tid) < 1) {
		echo "<script> alert('Select an category to delete'); window.history.go(-1);</script>\n";
		exit();
	}
	foreach ($tid as $filename) {
		if ($filename != "") {
			@unlink(JPATH_BASE . "/images/boss/$directory/fields/" . $filename);
		}
	}
	mosRedirect("index2.php?option=com_boss&act=fieldimage&directory=$directory");
}

/*
 *  Функции для загрузки csv
 */

function CSVupdate($directory) {
	$database = database::getInstance();

	$csv_line_length = 1000;
	$fgetcsv_delimiter = ';';
	$fgetcsv_enclosure = '"';

	$replacethesecharacters = "`";
	$replacewithcharacters = "'";

	$filename = $_POST['filename'];
	$csvcat = $_POST['csvcat'];
	if (!empty($filename)) {
		$handle = fopen("$filename", "r");
	} else {
		mosRedirect("index2.php?option=com_boss&act=csv&task=build_insert", BOSS_CSV_FILE);
		return;
	}
	if (empty($csvcat)) {
		mosRedirect("index2.php?option=com_boss&act=csv&task=build_insert", BOSS_CSV_CAT);
		return;
	}
	$first_pass = 1;

	$query = "SELECT fieldid, name FROM #__boss_" . $directory . "_fields WHERE type = 'multicheckbox' OR type = 'multiselect' ORDER BY name ASC";
	$specfiles = $database->setQuery($query)->loadObjectList();

	$specfiles_name = array();
	foreach ($specfiles as $sf) {
		$specfiles_name[] = $sf->name;
	}

	while (($data = fgetcsv($handle, $csv_line_length, $fgetcsv_delimiter, $fgetcsv_enclosure)) !== FALSE) {

		if ($first_pass) {
			$size = count($data) - 1;
			$update_field = "id";
			$loop = 0;
			while ($data[$loop]) {
				$data[$loop] = strtr($data[$loop], $replacethesecharacters, $replacewithcharacters);
				$field_names[] = $data[$loop];
				if ($update_field == $data[$loop]) {
					$update_index = $loop;
				}
				$loop++;
			}
			$first_pass = 0;

			$specfiles_now = array_intersect_key($specfiles_name, $data);
		} else {
			$loop = 0;

			$sql = "UPDATE #__boss_" . $directory . "_contents SET ";
			foreach ($data as $value) {
				if ($value != "") {
					//$data[$loop] = mysqli_real_escape_string($cxn,$data[$loop]);
					$sql = $sql . "`" . $field_names[$loop] . "` = '" . $data[$loop] . "', ";
				}
				$loop++;
			}
			$sql = trim($sql, " ,");

			$sql = $sql . " WHERE `" . $update_field . "` = '" . $data[$update_index] . "';";
			$database->setQuery($sql)->query();

			$sql_incert_cat = "UPDATE  #__boss_" . $directory . "_content_category_href SET category_id = " . $csvcat . " WHERE content_id = " . $data[$update_index];
			$database->setQuery($sql_incert_cat)->query();
		}
	}
	fclose($handle);
	$path = JPATH_BASE . "/media/";
	$uploadfile = $path . basename($filename);
	if (file_exists($uploadfile)) {
		unlink($uploadfile);
	}
	mosRedirect("index2.php?option=com_boss&act=csv", BOSS_CSV_REZULT);
}

function CSVincert($directory) {
	$database = database::getInstance();

	$csv_line_length = 1000;
	$fgetcsv_delimiter = ';';
	$fgetcsv_enclosure = '"';

	$replacethesecharacters = "`";
	$replacewithcharacters = "'";

	$filename = $_POST['filename'];
	$csvcat = $_POST['csvcat'];
	if (!empty($filename)) {
		$handle = fopen("$filename", "r");
	} else {
		mosRedirect("index2.php?option=com_boss&act=csv&task=build_insert", BOSS_CSV_FILE);
		return;
	}
	if (empty($csvcat)) {
		mosRedirect("index2.php?option=com_boss&act=csv&task=build_insert", BOSS_CSV_CAT);
		return;
	}
	$first_pass = 1;
	while (($data = fgetcsv($handle, $csv_line_length, $fgetcsv_delimiter, $fgetcsv_enclosure)) !== FALSE) {
		if ($first_pass) {
			$size = count($data) - 1;
			$sql_first_part = "INSERT INTO #__boss_" . $directory . "_contents (`category`,`userid`,`name`,`email`,`date_created`,`date_recall`,`";
			$loop = 0;
			while ($data[$loop]) {
				$data[$loop] = strtr($data[$loop], $replacethesecharacters, $replacewithcharacters);
				if ($loop == $size) {
					$sql_first_part = $sql_first_part . $data[$loop] . "`) VALUES (" . "'0','62','NULL','NULL',NOW(),NOW(),";
				} else {
					$sql_first_part = $sql_first_part . $data[$loop] . "` , `";
				}
				$loop++;
			}
			$first_pass = 0;
		} else {
			$loop = 0;
			foreach ($data as $value) {
				if ($value != "") {
					$data[$loop] = $value;
				} else {
					$data[$loop] = " ";
				}
				$loop++;
			}
			$sql = $sql_first_part;


			$loop = 0;
			while ($loop <= $size) {
				$data[$loop] = strtr($data[$loop], $fgetcsv_delimiter, $fgetcsv_enclosure);
				//$data[$loop] = mysqli_real_escape_string($database,$data[$loop]);
				if ($loop == $size) {
					$sql = $sql . "'" . $data[$loop] . "');";
				} else {
					$sql = $sql . "'" . $data[$loop] . "', ";
				}
				$loop++;
			}
			$database->setQuery($sql)->query();

			$sql_incert_cat = "INSERT INTO #__boss_" . $directory . "_content_category_href (content_id,category_id) values ('" . mysql_insert_id() . "', '" . $csvcat . "')";
			$database->setQuery($sql_incert_cat)->query();
		}
	}
	fclose($handle);
	$path = JPATH_BASE . "/media/";
	$uploadfile = $path . basename($filename);
	if (file_exists($uploadfile)) {
		unlink($uploadfile);
	}

	mosRedirect("index2.php?option=com_boss&act=csv", BOSS_CSV_REZULT);
}

function CSVpreview($directory) {

	$actprev = mosGetParam($_POST, 'actprev', '');
	$filename = $_FILES['csvname']['name'];
	$filenametmp = $_FILES['csvname']['tmp_name'];

	$csvcat = $_POST['csvcat'];
	$csv_line_length = 1000;
	$fgetcsv_delimiter = ';';
	$fgetcsv_enclosure = '"';

	$max_file_size = ini_get('upload_max_filesize');
	$max_file_size = 3 * $max_file_size;
	if (strlen($max_file_size) <= 3) {
		$max_file_size = $max_file_size * 1024 * 1024;
	}

	if ($_FILES['csvname']['size'] > $max_file_size) {
		mosRedirect("index2.php?option=com_boss&act=csv&task=csv_preview", BOSS_FILE_TOO_BIG);
		return;
	}

	$path = JPATH_BASE . "/media/";
	$uploadfile = $path . basename($filename);
	$src_file = urldecode($filenametmp);
	move_uploaded_file($src_file, $uploadfile);

	if (!empty($uploadfile)) {
		$filename = $uploadfile;
	}

	if (!empty($filename)) {
		$handle = fopen("$filename", "r");
	} else {
		mosRedirect("index2.php?option=com_boss&act=csv&task=build_insert", BOSS_CSV_FILE);
		return;
	}

	if (empty($csvcat)) {
		mosRedirect("index2.php?option=com_boss&act=csv&task=build_insert", BOSS_CSV_CAT);
		return;
	}

	$line = 0;
	$maxCols = 0;
	$previewLimit = 5;
	$directories = getDirectories();

	HTML_boss::displaycsvpreview($directory, $directories, $filename, $csvcat, $handle, $fgetcsv_delimiter, $fgetcsv_enclosure, $line, $maxCols, $previewLimit, $actprev);
}

function CSVmetod($directory) {
	$directories = getDirectories();
	HTML_boss::displaycsvmetod($directory, $directories);
}

function CSVbuildinsert($directory) {

	$task = mosGetParam($_REQUEST, 'task', '');

	if ($task == "build_insert") {
		$actprev = "csv_insert";
	}
	if ($task == "build_list") {
		$actprev = "csv_update";
	}

	$id = mosGetParam($_REQUEST, 'tid', array(0));
	if (is_array($id)) {
		$id = $id[0];
	}

	if (!isset($id)) {
		mosRedirect("index2.php?option=com_boss&act=contest", BOSS_ERROR_IN_URL);
		return;
	}

	$children = getAllCategories($directory);
	$directories = getDirectories();

	HTML_boss::displaycsvbuildinsert($directory, $directories, $children, $actprev);
}

function showImpExpForm($directory) {

	$directories = getDirectories();
	$packs = array();

	if ($handle = opendir(JPATH_BASE . '/images/boss/' . $directory)) {
		while (false !== ($file = readdir($handle))) {
			if (substr($file, -4) == '.zip')
				$packs[] = $file;
		}
		closedir($handle);
	}

	HTML_boss::showImpExpForm($directory, $directories, $packs);
}

function exportDirectory($directory) {

	$exp_tables = mosGetParam($_REQUEST, 'exp_tables', 0);
	$exp_content = mosGetParam($_REQUEST, 'exp_content', 0);
	$exp_templates = mosGetParam($_REQUEST, 'exp_templates', 0);
	$exp_plugins = mosGetParam($_REQUEST, 'exp_plugins', 0);
	$pack_name = mosGetParam($_REQUEST, 'pack_name', 'pack');
	$database = database::getInstance();

	$patch = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory . DS . $pack_name;
	$patchToTemplates = JPATH_BASE . DS . 'templates' . DS . 'com_boss';
	$patchToPlugins = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory . DS . 'plugins';
	$patchToContentFolders = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory;

	//если нет папки с названием пака, то создаем
	if (!is_dir($patch))
		mkdir($patch);

	//если выбрано экспортировать таблицы
	if ($exp_tables) {
		//создаем файл дампа бд если его нет
		$file = $patch . DS . 'table.sql.php';
		file_put_contents($file, '<?php' . "\n");

		//запрашиваем названия таблиц
		$tablesArr = $database->getTableList();
		$tables = array();
		//берем в массив только нужные таблицы
		foreach ($tablesArr as $table) {
			if (preg_match("/_boss_" . $directory . "/", $table)) {
				$tables[] = $table;
			}
		}

		//обработка таблиц
		if (count($tables) > 0) {
			foreach ($tables as $table) {
				//создаем дамп структуры таблицы
				backup_table_structure($directory, $file, $table);
				//если выбрано не экспортировать контент исключаем таблицу контента из бекапа
				if (!($exp_content == 0 && (substr($table, -9) == '_contents' || substr($table, -11) == '_categories' || substr($table, -22) == '_content_category_href'))) {
					//создаем дамп данных таблицы
					backup_table_data($directory, $file, $table);
				}
			}
		}

		//настройки каталога
		//определяем не числовые поля
		$not_num = array();
		$result = $database->getTableFields(array('#__boss_config'));
		foreach ($result['#__boss_config'] as $key => $value) {
			if (!preg_match("/^(tinyint|smallint|mediumint|bigint|int|float|double|real|decimal|numeric|year)/", $value)) {
				$not_num[$key] = 1;
			}
		}
		$result = $database->setQuery('SELECT * FROM #__boss_config WHERE id = ' . $directory,0,1)->loadAssocRow();

		$content = '$query = "DELETE FROM `#__boss_config` WHERE `id` = ".$directory;' . "\n";
		$content .= '$database->setQuery($query);' . "\n";
		$content .= '$database->query();' . "\n\n";

		$content .= '$query = "INSERT INTO `#__boss_config` VALUES ';
		$content .= "\n(";
		$first2 = true;
		foreach ($result as $index => $field) {
			if (isset($not_num[$index])) {
				$field = addslashes($field);
				$field = preg_replace("/\n/", "/\/\n/", $field);
				$content .= ! $first2 ? (",'" . $field . "'") : '".$directory."';
			} else {
				$content .= ! $first2 ? (',' . $field) : '".$directory."';
			}
			$first2 = false;
		}
		$content .= ')";' . "\n";
		$content .= '$database->setQuery($query);' . "\n";
		$content .= '$database->query();' . "\n";
		$content .= '?>';
		//сохраняем результаты выборки
		file_put_contents($file, $content, FILE_APPEND);
	}

	//файлы
	//экспортируем шаблоны если выбрано
	if ($exp_templates) {
		copyFolder($patchToTemplates, $patch . DS . 'templates');
	}

	//экспортируем плагины если выбрано
	if ($exp_plugins) {
		copyFolder($patchToPlugins, $patch . DS . 'plugins');
	}

	if ($exp_content) {
		!is_dir($patch . DS . 'content') ? mkdir($patch . DS . 'content') : null;

		copyFolder($patchToContentFolders . DS . 'categories', $patch . DS . 'content' . DS . 'categories');
		copyFolder($patchToContentFolders . DS . 'contents', $patch . DS . 'content' . DS . 'contents');
		copyFolder($patchToContentFolders . DS . 'email', $patch . DS . 'content' . DS . 'email');
		copyFolder($patchToContentFolders . DS . 'fields', $patch . DS . 'content' . DS . 'fields');
		copyFolder($patchToContentFolders . DS . 'files', $patch . DS . 'content' . DS . 'files');
	}
	//архивируем файлы
	zipFolder($patch, JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory . DS . $pack_name . '.zip');
	//удаляем файлы после архивирования
	rmdir_rf($patch);
	mosRedirect("index2.php?option=com_boss&act=export_import&directory=" . $directory);
}

function importDirectory() {

	$database = database::getInstance();
	$pack = mosGetParam($_FILES, 'pack', '');
	$directory = mosGetParam($_REQUEST, 'new_directory', 0);

	if ($pack['name'] == '') {
		mosRedirect("index2.php?option=com_boss&act=export_import", BOSS_IM_SELECT_ARCHIV);
		return false;
	}

	// Extract functions
	require_once( JPATH_BASE . '/administrator/includes/pcl/pclzip.lib.php' );
	require_once( JPATH_BASE . '/administrator/includes/pcl/pclerror.lib.php' );
	$zipfile = new PclZip($pack['tmp_name']);

	if (substr(PHP_OS, 0, 3) == 'WIN') {
		define('OS_WINDOWS', 1);
	} else {
		define('OS_WINDOWS', 0);
	}

	//на всякий случай создаем папки
	mosMakePath(JPATH_BASE . "/images", 'boss/tmp');
	//разархивируем во временную папку
	$ret = $zipfile->extract(PCLZIP_OPT_PATH, JPATH_BASE . "/images/boss/tmp");
	if ($ret == 0) {
		mosRedirect("index2.php?option=com_boss&act=export_import", BOSS_IM_NOT_ARCH);
		return false;
	}
    //если каталог не введен, то делаем новый каталог
    if($directory == 0){
		$directory = (int)installNewDirectory(0);
    } else if ($directory != 0){
        //проверим существование каталога
        $q = "SELECT `id` FROM #__boss_config WHERE `id` = '$directory'";
		$result = $database->setQuery($q)->loadObjectList();
        if(count($result) == 0){
            $directory = (int)installNewDirectory(0);
        }
    }
	//путь до копируемых файлов
	$pathFrom = JPATH_BASE . "/images/boss/tmp";
	//путь куда копировать
	$pathTo = JPATH_BASE . "/images/boss/$directory";
	//копируем файлы контента
	is_dir($pathFrom . '/content') ? copy_folder_rf($pathFrom . '/content', $pathTo) : null;
	//копируем файлы плагинов
	is_dir($pathFrom . '/plugins') ? copy_folder_rf($pathFrom . '/plugins', $pathTo . '/plugins') : null;
	//копируем файлы шаблонов
	is_dir($pathFrom . '/templates') ? copy_folder_rf($pathFrom . '/templates', JPATH_BASE . "/templates/com_boss") : null;
	//включаем файл с запросами, делаем запросы в БД
	if (is_file($pathFrom . '/table.sql.php')) {
		require $pathFrom . '/table.sql.php';
	}

	//удаляем временные файлы
	rmdir_rf($pathFrom);
	//редиректим на настройки
	mosRedirect("index2.php?option=com_boss&act=configuration&directory=" . $directory);
}

function importJoostina($directory) {

	$database = database::getInstance();

	$imp_category = mosGetParam($_REQUEST, 'imp_category', 0);
	$imp_content = mosGetParam($_REQUEST, 'imp_content', 0);
	$introtext = mosGetParam($_REQUEST, 'introtext', '');
	$fulltext = mosGetParam($_REQUEST, 'fulltext', '');

	if ($imp_category == 1) {

		$q = "SELECT * FROM #__sections";
		$result = $database->setQuery($q)->loadObjectList();

		if (count($result) > 0) {
			foreach ($result as $section) {
				$q = "INSERT INTO  `#__boss_" . $directory . "_categories` ";
				$q .= "(`parent`, `name`, `description`, `meta_title`, `ordering`, `published`) ";
				$q .= "VALUES ";
				$q .= "(0, '" . $section->name . "', '" . $section->description . "', '" . $section->title . "', '" . $section->ordering . "', " . $section->published . ") ";
				$database->setQuery($q)->query();
				if ($database->getErrorNum()) {
					echo $database->stderr();
					return false;
				}

				$newSectoinId = $database->insertid();

				$q = "SELECT * FROM #__categories WHERE `section` = " . $section->id;
				$cats = $database->setQuery($q)->loadObjectList();
				if ($database->getErrorNum()) {
					echo $database->stderr();
					return false;
				}
				if (count($cats) > 0) {
					foreach ($cats as $cat) {
						$q = "INSERT INTO  `#__boss_" . $directory . "_categories` ";
						$q .= "(`parent`, `name`, `description`, `meta_title`, `ordering`, `published`) ";
						$q .= "VALUES ";
						$q .= "(" . $newSectoinId . ", '" . $cat->name . "', '" . $cat->description . "', '" . $cat->title . "', '" . $cat->ordering . "', " . $cat->published . ") ";
						$database->setQuery($q)->query();
						if ($database->getErrorNum()) {
							echo $database->stderr();
							return false;
						}
						$newCatId = $database->insertid();

						if ($imp_content == 1) {
							$q = "SELECT * FROM #__content WHERE `catid` = " . $cat->id;
							$contents = $database->setQuery($q)->loadObjectList();
							if ($database->getErrorNum()) {
								echo $database->stderr();
								return false;
							}
							if (count($contents) > 0) {
								foreach ($contents as $content) {
									$q = "INSERT INTO  `#__boss_" . $directory . "_contents` ";
									$q .= "(`name`,`slug`,`userid`,`published`,`date_created`,`views`,`" . $introtext . "`,`" . $fulltext . "`) ";
									$q .= "VALUES ";
									$q .= "('" . addslashes($content->title) . "', '" . addslashes($content->title_alias) . "', " . $content->created_by . ", " . $content->state . ", '" . $content->created . "', " . $content->hits . ", '" . addslashes($content->introtext) . "', '" . addslashes($content->fulltext) . "')";
									$database->setQuery($q)->query();
									if ($database->getErrorNum()) {
										echo $database->stderr();
										return false;
									}
									$newContentId = $database->insertid();

									$q = "INSERT INTO  `#__boss_" . $directory . "_content_category_href` ";
									$q .= "(`category_id`,`content_id`) ";
									$q .= "VALUES ";
									$q .= "(" . intval($newCatId) . ", " . intval($newContentId) . ") ";
									$database->setQuery($q)->query();
									if ($database->getErrorNum()) {
										echo $database->stderr();
										return false;
									}
								}
							}
						}
					}
				}
			}
		}
	} elseif ($imp_content == 1) {
		$q = "SELECT * FROM #__content";
		$contents = $database->setQuery($q)->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		if (count($contents) > 0) {
			foreach ($contents as $content) {
				$q = "INSERT INTO  `#__boss_" . $directory . "_contents` ";
				$q .= "(`name`,`slug`,`userid`,`published`,`date_created`,`views`,`" . $introtext . "`,`" . $fulltext . "`) ";
				$q .= "VALUES ";
				$q .= "('" . addslashes($content->title) . "', '" . addslashes($content->title_alias) . "', " . $content->created_by . ", " . $content->state . ", '" . $content->created . "', " . $content->hits . ", '" . addslashes($content->introtext) . "', '" . addslashes($content->fulltext) . "')";
				$database->setQuery($q)->query();
				if ($database->getErrorNum()) {
					echo $database->stderr();
					return false;
				}
			}
		}
	}
    mosRedirect("index2.php?option=com_boss&act=contents&directory=$directory");
}

function listUsers($directory) {
	global $mosConfig_list_limit;
	$database = database::getInstance();

	$q = "SELECT count(*) FROM #__boss_" . $directory . "_profile";

	$total = $database->setQuery($q)->loadResult();

	$mainframe = mosMainFrame::getInstance();
	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{com_boss}limitstart", 'limitstart', 0));

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav($total, $limitstart, $limit);

    $q = "SELECT p.userid, u.name, u.username, u.registerDate, u.lastvisitDate, u.usertype "
        . "FROM #__boss_" . $directory . "_profile as p "
        . "LEFT JOIN #__users as u ON p.userid = u.id "
        . "ORDER BY u.username";
	$users = $database->setQuery($q, $limitstart, $limit)->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$directories = getDirectories();

	HTML_boss::listUsers($directory, $directories, $pageNav, $users);
}

function editUserInfo($directory) {
    $database = database::getInstance();
    $userid = mosGetParam($_REQUEST, 'tid', array(0));
    $selectedUserId = $userid[0];
    $userFields = null;

    $q = "SELECT * "
        . "FROM #__boss_" . $directory . "_profile  "
        . "WHERE userid = " . $userid[0] . " LIMIT 1";
	 $database->setQuery($q)->loadObject($userFields);
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

    $q = "SELECT * "
        . "FROM #__boss_" . $directory . "_fields "
        . "WHERE profile = 1 "
        . "ORDER BY ordering";
	$fields = $database->setQuery($q)->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

    $q = "SELECT id as userid, name "
        . "FROM #__users "
        //. "WHERE profile = 1 "
        . "ORDER BY name";
	$users = $database->setQuery($q)->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

    $directories = getDirectories();
    HTML_boss::editUserInfo($directory, $directories, $userFields, $fields, $users, $selectedUserId);
}

function saveUserInfo($directory){
    $database = database::getInstance();
    $userid = mosGetParam($_REQUEST, 'userid', 0);
    $task = mosGetParam($_REQUEST, 'task', '');

    if($userid == 0)
        return false;

    $q = "DELETE FROM #__boss_" . $directory . "_profile WHERE userid = $userid ";
        $database->setQuery($q)->query();
        if ($database->getErrorNum()) {
		    echo $database->stderr();
		    return false;
	    }

    $tableFields = $database->getTableFields(array("#__boss_" . $directory . "_profile"));
    $tableFields = $tableFields["#__boss_" . $directory . "_profile"];

    $fields = array();
    $values = array();
    foreach($tableFields as $key => $val) {
        $fields[] = "`".$key."`";
        $values[] = "'".$database->getEscaped(mosGetParam($_REQUEST, $key, ''))."'";
    }
    $fields = implode(', ', $fields);
    $values = implode(', ', $values);

    $q = "INSERT INTO #__boss_" . $directory . "_profile "
        . "(" . $fields . ") "
        . "VALUES "
        . "(" . $values . ")";
        $database->setQuery($q)->query();
        if ($database->getErrorNum()) {
		    echo $database->stderr();
		    return false;
	    }
        if ($task == 'apply')
            $link = "index2.php?option=com_boss&act=users&task=edit&directory=$directory&tid[]=$userid";
        else
            $link = "index2.php?option=com_boss&act=users&directory=$directory";
	    mosRedirect($link, BOSS_UPDATE_SUCCESSFULL);
}

function deleteUserInfo($directory) {
      $database = database::getInstance();
      $userids = mosGetParam($_REQUEST, 'tid', array());

      if(count($userids) == 0)
          return false;

      foreach($userids as $userid){
        $q = "DELETE FROM #__boss_" . $directory . "_profile WHERE userid = $userid ";
        $database->setQuery($q)->query();
        if ($database->getErrorNum()) {
		    echo $database->stderr();
		    return false;
	    }
      }
      mosRedirect("index2.php?option=com_boss&act=users&directory=$directory", BOSS_FORM_USER_DELETED);
}