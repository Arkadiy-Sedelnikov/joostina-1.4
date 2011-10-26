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

if(!$acl->acl_check('administration','config','users',$my->usertype)) {
    mosRedirect('index2.php?',_NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));

global $task;
$id		= mosGetParam($_REQUEST,'id',null);
$cid	= josGetArrayInts('cid');

if(!is_array($cid)) {
    $cid = array(0);
}

// при обращении к настройкам быстрых значков доступа почистим их кэш
mosCache::cleanCache('quick_icons');

/**
 * @package Custom QuickIcons
 */
class CustomQuickIcons extends mosDBTable {
    /**
     @var int Primary key*/
    var $id = null;
    /**
     @var string*/
    var $text = null;
    /**
     @var string*/
    var $target = null;
    /**
     @var string*/
    var $icon = null;
    /**
     @var int*/
    var $ordering = null;
    /**
     @var int*/
    var $new_window = null;
    /**
     @var int*/
    var $published = null;
    /* varchar(30) - title.tag*/
    var $title = null;
    /* tinyint(1) - outpu: only icon/text/text & icon*/
    var $display = null;
    /* access int(11)*/
    var $access = null;
    /* gid int(3) - acl-group.id*/
    var $gid = null;

    function CustomQuickIcons() {
        global $database;
        $this->mosDBTable('#__quickicons','id',$database);
    }

    function check() {
        $returnVar = true;

        if(empty($this->icon) && $this->display != '1') {
            $this->_error = _PLEASE_ENTER_NUTTON_LINK;
            $returnVar = false;
        }
        if(empty($this->target)) {
            $this->_error = _PLEASE_ENTER_NUTTON_LINK;
            $returnVar = false;
        }
        if(empty($this->text)) {
            $this->_error = _PLEASE_ENTER_BUTTON_TEXT;
            $returnVar = false;
        }

        return $returnVar;
    }
}

switch($task) {
    case 'new':
        editIcon(null,$option);
        break;

    case 'edit':
        editIcon($id,$option);
        break;

    case 'editA':
        editIcon($cid[0],$option);
        break;


    case 'delete':
        deleteIcon($cid,$option);
        break;

    case 'save':
        saveIcon(1,$option);
        break;

    case 'apply':
        saveIcon(0,$option);
        break;

    case 'publish':
        changeIcon($cid,1,$option);
        break;

    case 'unpublish':
        changeIcon($cid,0,$option);
        break;

    case 'orderUp':
        orderIcon($id,-1,$option);
        break;

    case 'orderDown':
        orderIcon($id,1,$option);
        break;

    case 'chooseIcon':
        chooseIcon($option);
        break;

    case 'saveorder':
        saveOrder($cid,$option);
        break;

    default:
        show($option);
        break;
}

// show the Items
function show($option) {
    global $database,$mainframe,$mosConfig_list_limit;

    $limit		= intval($mainframe->getUserStateFromRequest('viewlistlimit','limit',$mosConfig_list_limit));
    $limitstart	= intval($mainframe->getUserStateFromRequest("view{$option}limitstart",'limitstart',0));
    $search		= $mainframe->getUserStateFromRequest("search{$option}",'search','');
    $search		= $database->getEscaped(Jstring::trim(Jstring::strtolower($search)));

    $where = array();

    if($search) {
        $where[] = 'LOWER( a.text ) LIKE \'%$search%\' OR LOWER( a.target ) LIKE \'%$search%\' OR LOWER( a.cm_path ) LIKE \'%$search%\'';
    }

    // get the total number of records
    $query = 'SELECT COUNT(*) FROM #__quickicons AS a'.(count($where)?' WHERE '.implode(' AND ',$where):'');
    $database->setQuery($query);
    $total = $database->loadResult();

    require_once (JPATH_BASE.'/'.JADMIN_BASE.'/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total,$limitstart,$limit);

    // Load Items
    $query = 'SELECT a.*, g.name AS groupname FROM #__quickicons AS a LEFT JOIN #__core_acl_aro_groups AS g ON g.group_id = a.gid'.(count($where)?' WHERE '.implode(' AND ',$where):'').' ORDER BY ordering';
    $database->setQuery($query,$pageNav->limitstart,$pageNav->limit);
    $rows = $database->loadObjectList();

    // Output
    HTML_QuickIcons::show($rows,$option,$search,$pageNav);
}

/**
 * Function to edit existing or creaate new item
 *
 * @param int $id icon id
 * @param string	$option	internal task
 */
function editIcon($id,$option) {
    global $database,$my,$acl;

    // Load Item
    $row = new CustomQuickIcons();
    $row->load($id);

    $row->published = 1;

    $query = 'SELECT ordering AS value, text AS text FROM #__quickicons ORDER BY ordering';
    $lists['ordering'] = mosAdminMenus::SpecificOrdering($row,$id,$query,1);

    // build the html select list for the components
    $query = 'SELECT CONCAT_WS( \' \', link, admin_menu_link ) AS value, name AS text, id, parent FROM #__components WHERE link != \'\' OR admin_menu_link != \'\' ORDER BY id, parent';
    $lists['components'] = mosAdminMenus::SpecificOrdering($row,$id = true,$query,1); // id special handling

    // get list of menu entries in all menus
    $query = 'SELECT admin_menu_link AS value, CONCAT_WS( \' :: \', name, `option` ) AS text FROM #__components WHERE admin_menu_link != \'\' AND (parent = 0 OR parent = 1) ORDER BY name';
    $database->setQuery($query);
    $targets = $database->loadObjectList();
    $lists['targets'] = mosHTML::selectList($targets,'tar_gets','id="tar_gets" class="inputbox" size="1"','value','text',null);

    // components (with name) for check
    $query = 'SELECT name AS value, CONCAT_WS( \' :: \', `option`, name ) AS text FROM #__components WHERE parent = \'0\' AND `option` != \'\' ORDER BY name';
    $database->setQuery($query);
    $ccheck = $database->loadObjectList();
    $lists['components_check'] = mosHTML::selectList($ccheck,'ccheck','id="ccheck" class="inputbox" size="1"','value','text',null);

    // list for usergroups
    $my_group = strtolower($acl->get_group_name($row->gid,'ARO'));
    if($my_group == 'siteowner') {
        $lists['gid'] = '<input class="inputbox" type="hidden" name="gid" value="'.$my->gid.'" /><strong>Site Owner</strong>';
    } else {
        // ensure user can't add group higher than themselves
        $my_groups = $acl->get_object_groups('users',$my->id,'ARO');
        if(is_array($my_groups) && count($my_groups) > 0) {
            $ex_groups = $acl->get_group_children($my_groups[0],'ARO','RECURSE');
        } else {
            $ex_groups = array();
        }

        // add unwanted groups to be removed below
        $ex_groups[] = '29'; // Public Frontend
        $ex_groups[] = '18'; // Registered
        $ex_groups[] = '19'; // Author
        $ex_groups[] = '20'; // Editor
        $ex_groups[] = '21'; // Publisher
        $ex_groups[] = '30'; // Public Backend

        $gtree = $acl->get_group_children_tree(null,'USERS',false);

        // remove users 'above' me and unwanted groups as defined above
        $i = 0;
        while($i < count($gtree)) {
            if(in_array($gtree[$i]->value,$ex_groups)) {
                array_splice($gtree,$i,1);
            } else {
                $i++;
            }
        }
        $lists['gid'] = mosHTML::selectList($gtree,'gid','class="inputbox" size="4"','value','text',$row->gid);
    }

    // display
    $display[] = mosHTML::makeOption('',_DISPLAY_TEXT_AND_ICON);
    $display[] = mosHTML::makeOption('1',_DISPLAY_ONLY_TEXT);
    $display[] = mosHTML::makeOption('2',_DISPLAY_ONLY_ICON);

    $lists['display'] = mosHTML::selectList($display,'display','class="inputbox" size="1"','value','text',$row->display);

    HTML_QuickIcons::edit($row,$lists,$option);
}

// Publish an Item
function changeIcon($cid,$action,$option) {
    global $database;

    if(!is_array($cid) || count($cid) < 1) {
        $errMsg = $action ? _BUTTON_ERROR_PUBLISHING:_BUTTON_ERROR_UNPUBLISHING;
        echo "<script> alert('".$errMsg."'); window.history.go(-1);</script>\n";
        exit();
    }

    $cids = implode(',',$cid);

    $query = 'UPDATE #__quickicons SET published = '.$action.' WHERE id IN ( '.$cids.' )';
    $database->setQuery($query);
    if(!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
    }
    mosRedirect('index2.php?option='.$option);
}

/**
 * Save Icon
 *
 * @param bool		$redirect	where to go after savin
 * @param string	$option		internal var
 * @since v.2.0.7	deleting common path
 */
function saveIcon($redirect,$option) {
    $row = new CustomQuickIcons();

    if(!$row->bind($_POST)) {
        echo "<script> alert('1 ".html_entity_decode($row->getError())."'); window.history.go(-1); </script>\n";
        exit();
    }

    // удаление пути
    $row->icon = str_replace(JPATH_SITE,'',$row->icon);

    // pre-save checks
    if(!$row->check()) {
        echo "<script> alert('".html_entity_decode($row->getError())."'); window.history.go(-1); </script>\n";
        exit();
    }

    if($row->target == 'index2.php?option=' || !$row->target) {
        $row->published = 0;
    }

    // save the changes
    if(!$row->store()) {
        echo "<script> alert('3 ".html_entity_decode($row->getError())."'); window.history.go(-1); </script>\n";
        exit();
    }
    $row->checkin();
    $row->updateOrder();

    if($redirect) {
        mosRedirect('index2.php?option='.$option);
    } else {
        mosRedirect('index2.php?option='.$option.'&amp;task=edit&amp;id='.$row->id);
    }
}

// Reorder an Item
function orderIcon($id,$inc,$option) {
    global $database;

    // Cleaning ordering
    $query = 'SELECT id, ordering FROM #__quickicons ORDER BY ordering';
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    $i = 0;
    foreach($rows as $row) {
        $query = 'UPDATE #__quickicons SET ordering = '.$i.' WHERE id = '.$row->id;
        $database->setQuery($query);
        $database->query();
        $i++;
    }

    $query = 'SELECT ordering FROM #__quickicons WHERE id = '.$id;
    $database->setQuery($query);
    $database->loadObject($row);

    if($row) {
        $newOrder = $row->ordering + $inc;

        $query = 'SELECT id FROM #__quickicons WHERE ordering = '.$newOrder;
        $database->setQuery($query);
        $database->loadObject($row2);

        if($row2) {
            $query = 'UPDATE #__quickicons SET ordering = '.$newOrder.' WHERE id = '.$id;
            $database->setQuery($query);
            if(!$database->query()) {
                echo "<script> alert('".$database->getErrorMsg().
                        "'); window.history.go(-1); </script>\n";
                exit();
            }

            $query = 'UPDATE #__quickicons SET ordering = '.$row->ordering.' WHERE id = '.$row2->id;
            $database->setQuery($query);
            if(!$database->query()) {
                echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
                exit();
            }
        }

        mosRedirect('index2.php?option='.$option);
    } else {
        // for debug
        //var_dump($row);
        //exit;
        // end for debug
        mosRedirect('index2.php?option='.$option);
    }
}

/* This feature (save order) is added by Eric C. Thanks Eric!*/
//Save ordering of icons
function saveOrder(&$cid,$option) {
    global $database;

    $total = count($cid);
    $order = mosGetParam($_POST,'order',array(0));

    for($i = 0; $i < $total; $i++) {
        $query = 'UPDATE #__quickicons SET ordering = '.$order[$i].' WHERE id = '.$cid[$i];
        $database->setQuery($query);
        if(!$database->query()) {
            echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
            exit();
        }

        // update ordering
        $row = new CustomQuickicons($database);
        $row->load($cid[$i]);
        $row->updateOrder();
    }

    $msg = _NEW_ORDER_SAVED;
    mosRedirect('index2.php?option='.$option,$msg);
} // saveOrder

// Delete icons
function deleteIcon(&$cid,$option) {
    global $database;

    if(count($cid)) {
        $cids = implode(',',$cid);

        $query = 'DELETE FROM #__quickicons'.' WHERE id IN ( '.$cids.' )';
        $database->setQuery($query);
        if(!$database->query()) {
            echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        }
    }

    $msg = _BUTTONS_DELETED;
    mosRedirect('index2.php?option='.$option,$msg);
}

/**
 * Gets images from folder admin and user
 *
 * @param string	$option	internal task
 * @since 2.0.7:
 *	- get images also from user images folder (optional the folder icons can be created new)
 *  - checks for double images
 *  - sort the array before output
 */
function chooseIcon($option) {
    global $cur_template;

    $icons = 0;
    $imgs = array();
    $folder[] = JPATH_BASE_ADMIN.'/images/';
    $folder[] = JPATH_BASE_ADMIN.'/templates/'.$cur_template.'/images/cpanel_ico/';

    foreach($folder as $fold) {
        if(file_exists($fold)) {
            $handle = opendir($fold); // 'images/'
            while($file = readdir($handle)) {
                if(strpos($file,'.jpg') || strpos($file,'.jpeg') || strpos($file,'.gif') ||
                        strpos($file,'.png')) {
                    if(!in_array($fold.$file,$imgs)) { // $file
                        $imgs[] = $fold.$file;
                        $icons++;
                    }
                }
            }
            closedir($handle);
        }
    }
    sort($imgs);
    HTML_QuickIcons::chooseIcon($imgs,$option,$icons);
}