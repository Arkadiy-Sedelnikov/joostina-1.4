<?php

/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

/**
 * Класс конфигурации каталогов
 */
class jDirectoryConf extends mosDBTable {

    var $id = null;
    var $name = null;
    var $slug = null;
    var $meta_title = null;
    var $meta_desc = null;
    var $meta_keys = null;
    var $default_order_by = null;
    var $contents_per_page = null;
    var $root_allowed = null;    
    var $show_contact = null;   
    var $send_email_on_new = null;   
    var $send_email_on_update = null;
    var $auto_publish = null;
    var $fronttext = null;
    var $email_display = null;
    var $display_fullname = null;
    var $rules_text = null;
    var $expiration = null;
    var $content_duration = null;
    var $recall = null;
    var $recall_time = null;
    var $recall_text = null;    
    var $empty_cat = null;
    var $cat_max_width = null;
    var $cat_max_height = null;
    var $cat_max_width_t = null;
    var $cat_max_height_t = null;
    var $submission_type = null;
    var $nb_contents_by_user = null;
    var $allow_attachement = null;
    var $allow_contact_by_pms = null;
    var $allow_comments = null;
    var $rating = null;
    var $secure_comment = null;
    var $comment_sys = null;
    var $allow_unregisered_comment = null;
    var $allow_ratings = null;
    var $secure_new_content = null;
    var $use_content_mambot = null;
    var $show_rss = null;
    var $filter = null;
    var $template = null;
    var $allow_rights = null;
    var $rights = null;

    function __construct(&$db) {
        $this->mosDBTable('#__boss_config', 'id', $db);
    }

    // get configuration
    public static function getConfig($directory) {
    	$database = database::getInstance();
        $conf = null;
    	$database->setQuery("SELECT * FROM #__boss_config WHERE id = $directory",0,1)->loadObject($conf);
    	if ($database->getErrorNum()) {
    		echo $database->stderr();
    		return false;
    	}
    	return $conf;
    }
    
    /** редактирование конфигурации
     * @static
     * @param  $directory
     * @return
     */
    public static function editConfiguration($directory, $conf) {

        $database = database::getInstance();

        $templates = BossTemplates::getTemplates();
        $directories = BossDirectory::getDirectories();

        $sort_fields = $database->setQuery("SELECT `fieldid`, `title` FROM #__boss_" . $directory . "_fields WHERE `sort` = 1")->loadObjectList();

        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        $filter = BossPlugins::get_plugins($directory, 'filters');
        $filters = array();
        foreach ($filter as $key => $plug) {
            $filters[] = mosHTML::makeOption($key, $plug->name);
        }

        $rating = BossPlugins::get_plugins($directory, 'ratings');
        $ratings = array();
        foreach ($rating as $key => $plug) {
            $ratings[] = mosHTML::makeOption($key, $key);
        }
        

            $rights_admin = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_admin'));
            $rights_admin->bind_rights(@$conf->rights);
            
            $rights_front = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
            $rights_front->bind_rights(@$conf->rights);
            
            $rights = array(
                'admin' => $rights_admin,
                'front' => $rights_front
            );
        
        HTML_boss::editConfiguration($conf, $templates, $directory, $directories, $sort_fields, $filters, $ratings, $rights, $conf);
    }

    /** сохранение конфигурации
     * @static
     * @param  $directory
     * @return void
     */
    public static function saveConfiguration($directory) {
        $task = mosGetParam($_REQUEST, 'task');
        $database = database::getInstance();
        $row = new jDirectoryConf($database);

        // bind it to the table
        if (!$row->bind($_POST)) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }
        
        //если активировано управление правами пользователя
        if($row->allow_rights == 1){
            $rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_admin'));
            $row->rights = $rights->prepare_for_saving($_REQUEST['u_rights']);
        }
        
        // store it in the db
        if (!$row->store()) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        if ($task == 'apply')
            $link = "index2.php?option=com_boss&act=configuration&task=edit&directory=$directory";
        else
            $link = "index2.php?option=com_boss&act=configuration&directory=$directory";
        mosRedirect($link, BOSS_CONFIGURATION_SAVED);
    }

}

/**
 * Класс категорий
 */
class jDirectoryCategory extends mosDBTable {

    var $id = null;
    var $parent = null;
    var $name = null;
    var $slug = null;
    var $meta_title = null;
    var $meta_desc = null;
    var $meta_keys = null;
    var $description = null;
    var $ordering = null;
    var $published = 1;
    var $template = null;
    var $content_types = null;
    var $rights = null;

    function __construct(&$db, $directory) {
        $this->mosDBTable('#__boss_' . $directory . '_categories', 'id', $db);
    }

    /** массив всех категорий
     * @static
     * @param  $directory
     * @param null $rows
     * @return array
     */
    public static function getAllCategories($directory, $rows = null) {
        if ($rows == null) {
            $database = database::getInstance();

            $src_cat = mosGetParam($_REQUEST, 'src_cat', '');
            $select_publish = mosGetParam($_REQUEST, 'select_publish', 0);

            $wheres = array();

            if ($src_cat) {
                $wheres[] = "c.name LIKE '%$src_cat%'";
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
            $where = (count($wheres) > 0) ? "WHERE " . implode(' AND ', $wheres) . " " : '';
            $q = "SELECT c.* FROM #__boss_" . $directory . "_categories as c ";
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

    /** сохранение порядка сортировки
     * @static
     * @param  $tid
     * @param  $directory
     * @return void
     */
    public static function saveOrder(&$tid, $directory) {
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

    /** перемещение категории в списке при сортировке
     * @static
     * @param  $uid
     * @param  $inc
     * @param  $directory
     * @return void
     */
    public static function orderCategory($uid, $inc, $directory) {
        $database = database::getInstance();

        $row = new jDirectoryCategory($database, $directory);
        $row->load($uid);
        $row->move($inc, "parent = $row->parent");

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory");
    }

    /** форма создания категории
     * @static
     * @param  $directory
     * @return void
     */
    public static function newCategory($directory, $conf) {
        $database = database::getInstance();
        $children = self::getAllCategories($directory);

        $row = new jDirectoryCategory($database, $directory);

        $directories = BossDirectory::getDirectories();
        $templates = BossTemplates::getTemplates();
        $comtentTypes = BossContentTypes::getAllContentTypes($directory);
        $rights = null;
        if(@$conf->allow_rights == 1){
            $rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('category'));
        }
        
        HTML_boss::displaycategory($row, $children, $directory, $directories, $templates, $comtentTypes, $rights, $conf);
    }

    /** удаление категории
     * @static
     * @param  $directory
     * @return void
     */
    public static function deleteCategory($directory) {

        $tid = $_POST['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo '<script> alert(\'Select an category to delete\'); window.history.go(-1);</script>' . "\n";
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

    /**
     * @static список категорий
     * @param  $directory
     * @return bool
     */
    public static function listCategories($directory, $conf) {

        $defaultTemplate = $conf->template;
        $database = database::getInstance();

        $src_cat = mosGetParam($_REQUEST, 'src_cat', '');
        $select_publish = mosGetParam($_REQUEST, 'select_publish', 0);

        $wheres = array();

        if ($src_cat) {
            $wheres[] = "c.name LIKE '%$src_cat%'";
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
        $where = (count($wheres) > 0) ? "WHERE " . implode(' AND ', $wheres) . " " : '';
        $q = "SELECT c.*, COUNT(cont.id) as num_cont FROM #__boss_" . $directory . "_categories as c ";
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

        require_once(JPATH_BASE . '/administrator/includes/pageNavigation.php');
        $pageNav = new mosPageNav(count($rows), 0, count($rows));

        $children = self::getAllCategories($directory, $rows);
        $directories = BossDirectory::getDirectories();

        HTML_boss::listcategories(count($rows), $children, $pageNav, $directory, $directories, $defaultTemplate, $conf);
        return true;
    }

    /**
     * @static публикация категорий
     * @param  $directory
     * @return
     */
    public static function publishCategory($directory) {

        $tid = $_GET['tid'];

        if (!is_array($tid) || count($tid) < 1) {
            echo '<script> alert(\'Select an Content to publish\'); window.history.go(-1);</script>' . "\n";
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

    /** форма редактирования категории
     * @static
     * @param  $directory
     * @return bool
     */
    public static function displayCategory($directory, $conf) {

        $id = mosGetParam($_REQUEST, 'tid', array(0));
        if (is_array($id)) {
            $id = $id[0];
        }

        $children = self::getAllCategories($directory);
        $directories = BossDirectory::getDirectories();
        $templates = BossTemplates::getTemplates();
        $database = database::getInstance();
        $rights = null;

        if (!isset($id)) {
            mosRedirect("index2.php?option=com_boss&act=contest&directory=$directory", BOSS_ERROR_IN_URL);
        }

        $row = null;
        $database->setQuery("SELECT * FROM #__boss_" . $directory . "_categories WHERE id=" . $id . " LIMIT 1")->loadObject($row);
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }
        $comtentTypes = BossContentTypes::getAllContentTypes($directory);
        
        if(@$conf->allow_rights == 1){
            $rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('category'));
            $rights->bind_rights(@$row->rights);
        }
        
        HTML_boss::displaycategory(@$row, $children, $directory, $directories, $templates, $comtentTypes, $rights, $conf);
        return true;
    }

    /** сохранение категории
     * @static
     * @param  $directory
     * @return
     */
    public static function saveCategory($directory, $conf) {

        $database = database::getInstance();
        $task = mosGetParam($_REQUEST, 'task');
        $row = new jDirectoryCategory($database, $directory);

        // bind it to the table
        if (!$row->bind($_POST)) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }
        
        //если активировано управление правами пользователя
        if($conf->allow_rights == 1){
            $rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('category'));
            $row->rights = $rights->prepare_for_saving($_REQUEST['u_rights']);
        }
        
        // store it in the db
        if (!$row->store()) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }

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

        // image1 upload
        if (isset($_FILES['cat_image']) and !$_FILES['cat_image']['error']) {
            $path = JPATH_BASE . "/images/boss/$directory/categories/";
            createImageAndThumb(
                    $_FILES['cat_image']['tmp_name'], $_FILES['cat_image']['name'], $path, $row->id . "cat.jpg", $row->id . "cat_t.jpg", $conf->cat_max_width, $conf->cat_max_height, $conf->cat_max_width_t, $conf->cat_max_height_t
            );
        }

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        if ($task == 'apply')
            $link = "index2.php?option=com_boss&directory=$directory&act=categories&task=edit&tid[]=$row->id";
        else
            $link = "index2.php?option=com_boss&act=categories&directory=$directory";
        mosRedirect($link, BOSS_CATEGORY_SAVED);
    }

}

/**
 * Класс контента
 */
class jDirectoryContent extends mosDBTable {

    var $id = null;
    var $name;
    var $slug;
    var $meta_title = null;
    var $meta_desc = null;
    var $meta_keys = null;
    var $userid = null;
    var $published = null;
    var $frontpage = null;
    var $featured = null;
    var $date_created = null;
    var $date_publish = null;
    var $date_unpublish = null;
    var $type_content = null;

    function __construct(&$db, $directory) {
        $this->mosDBTable('#__boss_' . $directory . '_contents', 'id', $db);
    }

    /** форма создания нового контента
     * @static
     * @param  $directory
     * @return bool
     */
    public static function newContent($directory, $conf) {

        global $my;
        $database = database::getInstance();
        $children = jDirectoryCategory::getAllCategories($directory);
        $type_content = mosGetParam($_REQUEST, 'type_content', 0);

        $row = new jDirectoryContent($database, $directory);

        //get fields
        $fields = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE published = 1 AND `profile` = 0 AND (FIND_IN_SET($type_content, `catsid`) > 0 OR `catsid` = ',-1,') ORDER by ordering")->loadObjectList();
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

        $directories = BossDirectory::getDirectories();

        $users = $database->setQuery("SELECT u.* FROM #__users as u ORDER BY u.username ASC")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }

        $row->userid = $my->id;

        HTML_boss::displayContent($row, $fields, $field_values, $children, $users, $directory, $directories, $type_content, array(), '', '', $conf);
        return true;
    }

    /**
     * @static сохранение контента
     * @param  $directory
     * @return bool
     */
    public static function saveContent($directory, $conf) {
        $database = database::getInstance();
        $row = new jDirectoryContent($database, $directory);
        $type_content = mosGetParam($_REQUEST, 'type_content', 0);

        //get fields
        $fields = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE `published` = 1 AND (FIND_IN_SET($type_content, `catsid`) > 0 OR `catsid` = ',-1,') AND `profile` = 0 ")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }

        //Save Field
        $row->save($directory, $fields, $conf);
        return true;
    }

    /**
     * @param  $directory  - каталог
     * @param  $fields - поля
     * @param  $conf - конфигурация
     * @param int $isUpdateMode - обновление/новое поле
     * @param int $itemid - ид пункта меню.
     * @return
     */
    function save($directory, $fields, $conf, $isUpdateMode = 0, $itemid = 0) {
        global $mainframe;
        $database = database::getInstance();
        $category = mosGetParam($_REQUEST, 'category', array());
        $tags = mosGetParam($_REQUEST, 'tags', '');
        $act = mosGetParam($_REQUEST, 'act', '');
        $task = mosGetParam($_REQUEST, 'task', '');

        $plugins = BossPlugins::get_plugins($directory, 'fields');

        // bind it to the table
        if (!$this->bind($_POST)) {
            echo "<script> alert('" . $this->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }
        if (($this->id == "") || ($this->id == 0)) {
            $isUpdateMode = 0;
            $this->date_created = date('Y-m-d H:i:s');
            
            if (empty($this->date_publish) && $this->published == 1) {
                $this->date_publish = $this->date_created;
            }
        } else {
            $isUpdateMode = 1;
            $this->date_publish = (intval($this->date_publish) > 0) ? mosFormatDate($this->date_publish, '%Y-%m-%d %H:%M:%S', -$mainframe->getCfg('offset')) : '';
        }

        if ($isUpdateMode == 0 && $mainframe->isAdmin() != 1) {
            if ($conf->auto_publish == 2 || $conf->auto_publish == 1) {
                $this->published = 1;
                $redirect_text = BOSS_INSERT_SUCCESSFULL_PUBLISH;
            } else {
                $this->published = 0;
                $redirect_text = BOSS_INSERT_SUCCESSFULL_CONFIRM;
            }
        }
        else
            $redirect_text = BOSS_UPDATE_SUCCESSFULL;

        $this->date_unpublish = (intval($this->date_unpublish) > 0) ? mosFormatDate($this->date_unpublish, '%Y-%m-%d %H:%M:%S', -$mainframe->getCfg('offset')) : '';

        if ($this->slug == '')
            $this->slug = $this->name;

        // store it in the db
        if (!$this->store()) {
            echo "<script> alert('" . $this->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        //удаляем старые категории
        if ($isUpdateMode == 1) {
            $query = "DELETE FROM #__boss_" . $directory . "_content_category_href "
                    . "WHERE content_id = $this->id";
            $database->setQuery($query);
            $database->query();
            $content_id = $this->id;
        }
        else
            $content_id = $database->insertid();
        //вписываем новые категории
        if (!empty($category)) {
            $cat_arr = array();
            if (is_array($category)) {
                foreach ($category as $cat) {
                    $cat_arr[] = "($cat, $content_id)";
                }
            }
            else
                $cat_arr[] = "($category, $content_id)";

            $query = "INSERT INTO #__boss_" . $directory . "_content_category_href "
                    . "(category_id, content_id) "
                    . " VALUES " . implode(", ", $cat_arr);
            $database->setQuery($query);
            $database->query();
        }
        //теги
        require_once(JPATH_BASE . '/includes/libraries/tags/tags.php');
        $jDirectoryContentTags = new contentTags($database);
        $obj = null;
        $obj->id = $content_id;
        $obj->obj_type = 'com_boss_' . $directory;
        $tag_arr = array();
        if (!empty($tags)) {
            $tag_arr = explode(',', $tags);
            $tag_arr = $jDirectoryContentTags->clear_tags($tag_arr);
        }
        $jDirectoryContentTags->update($tag_arr, $obj);
        unset($tags, $tag_arr, $obj);
        //конец тегов
        $queryArray = array();
        $query = "UPDATE #__boss_" . $directory . "_contents SET ";

        $first = 0;
        if (isset($fields)) {
            foreach ($fields as $field) {
                //Plugins
                if (isset($plugins[$field->type])) {
                    $value = $plugins[$field->type]->onFormSave($directory, $this->id, $field, $isUpdateMode, $itemid);
                } else {
                    $value = mosGetParam($_POST, $field->name, "");
                }

                $queryArray[] = " $field->name = '" . $value . "'";
            }
        }
        $query .= implode(', ', $queryArray);
        $query .= "WHERE id = " . $this->id;

        if (count($queryArray) > 0) {
            $database->setQuery($query);
            $database->query();
        }

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        if ($act != '') {

            if ($task == 'apply')
                $url = "index2.php?option=com_boss&act=contents&task=edit&&directory=$directory&tid[]=$this->id";
            else
                $url = "index2.php?option=com_boss&act=contents&directory=" . $directory;
        }
        else
            $url = sefRelToAbs("index.php?option=com_boss&task=show_content&contentid=" . $this->id . "&catid=" . $category . "&directory=" . $directory . "&Itemid=" . $itemid);
        mosRedirect($url, $redirect_text);
    }

    /**  публикация контента
     * @static
     * @param  $directory
     * @return
     */
    public static function publishContent($directory) {

        $tid = $_GET['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo sprintf('<script> alert(\'%s\'); window.history.go(-1);</script>' . "\n", BOSS_SELECT_CONTENT_TO_BE_PUBLISH);
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

    /** форма редактирования контента
     * @static
     * @param  $directory
     * @return bool
     */
    public static function displayContent($directory, $conf) {

        $task = mosGetParam($_REQUEST, 'task', '');
        $id = mosGetParam($_REQUEST, 'tid', array(0));

        if (is_array($id)) {
            $id = $id[0];
        }

        if (!isset($id)) {
            mosRedirect("index2.php?option=com_boss&act=contents&directory=$directory", BOSS_ERROR_IN_URL);
        }

        $children = jDirectoryCategory::getAllCategories($directory);

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

        //get fields
        $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE `published` = 1 AND `profile` = 0 AND (FIND_IN_SET($row->type_content, `catsid`) > 0 OR `catsid` = ',-1,') ORDER BY `ordering`, `fieldid`");
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

        $directories = BossDirectory::getDirectories();

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

        HTML_boss::displayContent($row, $fields, $field_values, $children, $users, $directory, $directories, $row->type_content, $selected_categ, $tags, $rowid, $conf);
        return true;
    }

    /** удаление контента
     * @static
     * @param  $directory
     * @return void
     */
    public static function deleteContent($directory, $conf) {

        $tid = $_REQUEST['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo sprintf("<script type='text/javascript'> alert('%s'); window.history.go(-1);</script>\n", BOSS_SELECT_CONTENT_TO_BE_DELETED);
            exit();
        }

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

    /**
     * @param  $directory
     * @param  $conf
     * @return void
     */
    function delete($directory, $conf) {

        $database = database::getInstance();
        $plugins = BossPlugins::get_plugins($directory, 'fields');

        $database->setQuery("SELECT name FROM #__boss_" . $directory . "_fields WHERE `type` = 'file'");
        $file_fields = $database->loadObjectList();
        foreach ($file_fields as $file_field) {
            $filename = "\$content->" . $file_field->name;
            eval("\$filename = \"$filename\";");
            @unlink(JPATH_BASE . "/images/boss/$directory/files/" . $filename);
        }

        $database->setQuery("DELETE FROM #__boss_" . $directory . "_contents WHERE id=$this->id");
        if ($database->getErrorNum()) {
            echo $database->stderr();
        } else {
            $database->query();
        }
        //удаляем связи контента с категориями
        $database->setQuery("DELETE FROM #__boss_" . $directory . "_content_category_href WHERE content_id=$this->id");
        if ($database->getErrorNum()) {
            echo $database->stderr();
        } else {
            $database->query();
        }
        foreach ($plugins as $plugin) {
            $plugin->onDelete($directory, $this->id);
        }
        $nbImages = $conf->nb_images;

        for ($i = 1; $i < $nbImages + 1; $i++) {
            $ext_name = chr(ord('a') + $i - 1);
            $pict = JPATH_BASE . "/images/boss/$directory/contents/" . $this->id . $ext_name . "_t.jpg";
            if (file_exists($pict)) {
                unlink($pict);
            }
            $pic = JPATH_BASE . "/images/boss/$directory/contents/" . $this->id . $ext_name . ".jpg";
            if (file_exists($pic)) {
                unlink($pic);
            }
        }
    }

    /**
     * @static
     * @param  $rows
     * @param  $list
     * @param  $catid
     * @return void
     */
    public static function recurseSearch($rows, &$list, $catid) {
        foreach ($rows as $row) {
            if ($row->parent == $catid) {
                $list[] = $row->id;
                self::recurseSearch($rows, $list, $row->id);
            }
        }
    }

    /**
     * @static список контента
     * @param  $directory
     * @return bool
     */
    public static function listContents($directory, $conf) {
        global $mosConfig_list_limit;
        $mainframe = mosMainFrame::getInstance();
        $database = database::getInstance();
        require_once(JPATH_BASE . '/administrator/includes/pageNavigation.php');

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
        /*         * ************************* */
        $rows = $database->setQuery("SELECT c.* FROM #__boss_" . $directory . "_categories as c ORDER BY c.parent,c.ordering")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }

        $children = jDirectoryCategory::getAllCategories($directory, $rows);

        // establish the hierarchy of the menu
        if ($catid != 0) {
            $list[] = $catid;
            self::recurseSearch($rows, $list, $catid);
        } else {
            $list = array();
        }
        $listids = implode(',', $list);

        $fields = array();
        $tables = array();
        $wheres = array();
        //content
        $fields[] = 'a.*';
        $tables[] = "#__boss_" . $directory . "_contents as a";
        //type of content
        $tables[] = "#__boss_" . $directory . "_content_types as ct";
        $fields[] = 'ct.name as type_name';
        $wheres[] = "ct.id = a.type_content";
        
        if (!empty($listids)) {
            $fields[] = 'c.name as catname';

            $tables[] = "#__boss_" . $directory . "_categories as c";
            $tables[] = "#__boss_" . $directory . "_content_category_href as cch";

            $wheres[] = "a.id = cch.content_id";
            $wheres[] = "c.id = cch.category_id";
            $wheres[] = "cch.category_id IN ($listids)";
        }

        if ($selectedAutorId > 0) {
            $wheres[] = "a.userid = " . (int) $selectedAutorId;
        }
        if ($select_publish > 0) {
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

        $where = (count($wheres) > 0) ? " WHERE " . implode(' AND ', $wheres) : '';

        $q = "SELECT " . implode(', ', $fields)
                . " FROM " . implode(', ', $tables)
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

        $directories = BossDirectory::getDirectories();

        $categs = $database->setQuery("SELECT c.name, c.id , cch.content_id "
                        . "FROM #__boss_" . $directory . "_categories as c, "
                        . "#__boss_" . $directory . "_content_category_href as cch "
                        . "WHERE cch.category_id = c.id ")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }
        
        $typesContent = $database->setQuery("SELECT name, id "
                        . "FROM #__boss_" . $directory . "_content_types "
                        . "WHERE published = 1 ")->loadObjectList();
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

        HTML_boss::listContents($cats[0], $rows, $pageNav, $children, $directory, $directories, $categs, $autors, $selectedAutorId, $typesContent, $conf);
        return true;
    }

}

class jDirectoryField extends mosDBTable {

    var $fieldid = null;
    var $name = null;
    var $description = null;
    var $title = null;
    var $display_title = null;
    var $type = null;
    var $text_before = null;
    var $text_after = null;
    var $tags_open = null;
    var $tags_separator = null;
    var $tags_close = null;
    var $maxlength = 75;
    var $size = 20;
    var $required = 1;
    var $link_text = null;
    var $link_image = null;
    var $ordering = 0;
    var $cols = null;
    var $rows = null;
    var $profile = 0;
    var $editable = 1;
    var $searchable = 0;
    var $sort = 0;
    var $sort_direction = 'DESC';
    var $catsid = null;
    var $published = 1;
    var $filter = 0;

    /**
     * Constructor
     * @param database A database connector object
     */
    function __construct(&$db, $directory) {

        $this->mosDBTable('#__boss_' . $directory . '_fields', 'fieldid', $db);
    }

//end func

    function getFieldValue($field, $content, $field_values, $directory, $itemid, $conf) {
        global $task;
        $return = null;
        $return->value = null;
        $return->title = null;
        $field_values = (isset($field_values[$field->fieldid])) ? $field_values[$field->fieldid] : array();
        $plugins = BossPlugins::get_plugins($directory, 'fields');
        $fieldName = $field->name;
        if ($task == "show_content")
            $mode = 1;
        else
            $mode = 2;

        if (($field->display_title & $mode) == $mode) {
            $return->title = jdGetLangDefinition($field->title) . ": ";
        }

        if (isset($content->$fieldName))
            $value = $content->$fieldName;
        else
            $value = "";

        if ($value != "") {
            $value = jdGetLangDefinition($value);

            if (isset($plugins[$field->type])) {
                if ($mode == 2)
                    $return->value .= $plugins[$field->type]->getListDisplay($directory, $content, $field, $field_values, $itemid, $conf);
                else
                    $return->value .= $plugins[$field->type]->getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf);
            }
            else
                $return->value .= $value;
        }
        return $return;
    }

    public static function getFieldForm($field, $content, $user, $field_values, $directory, $plugins, $mode = "write") {
        global $mosConfig_live_site, $mainframe;
        $return = null;
        $return->input = "";
        $return->title = "";

        $act = mosGetParam($_REQUEST, 'act', '');
        if ($act == 'contents')
            $nameform = 'adminForm';
        else
            $nameform = 'saveForm';

        if (isset($field->title)) {
            $return->title = jdGetLangDefinition($field->title);
            $strtitle = htmlentities($return->title, ENT_QUOTES);
        }

        $fieldname = $field->name; // 2

        if (isset($plugins[$field->type])) {
            $return->input = $plugins[$field->type]->getFormDisplay($directory, $content, $field, $field_values, $nameform, $mode);
        }

        if ((@$field->description) && ($field->description != "")) {
            $fieldTip = str_replace(array('"', '<', '>', "\\"), array("&quot;", "&lt;", "&gt;", "\\\\"), jdGetLangDefinition($field->description));
            $tipTitle = str_replace(array('"', '<', '>', "\\"), array("&quot;", "&lt;", "&gt;", "\\\\"), jdGetLangDefinition($field->title));
            $fieldTip = str_replace(array("'", "&#039;"), "\\'", $fieldTip);
            $tipTitle = str_replace(array("'", "&#039;"), "\\'", $tipTitle);
            $return->input .= '<img src="/includes/js/ThemeOffice/tooltip.png" alt="tooltip" style="border:0" title="' . $tipTitle . ":: \n" . $fieldTip . '"  />';
        }

        return $return;
    }

    /** список полей
     * @static
     * @param  $directory
     * @return
     */
    public static function showFields($directory, $conf) {

        if ($directory == 0) {
            return;
        }
        //подключаем скрипты
        $mainframe = mosMainFrame::getInstance();
        $database = database::getInstance();
        $tabs = new Sliders();
        mosCommonHTML::loadJquery();
        mosCommonHTML::loadJqueryPlugins('jquery.form');
        mosCommonHTML::loadJqueryUI();
        $mainframe->addJS('/administrator/components/com_boss/js/formbuilder/edit_fields.js'); 
        $mainframe->addJS('/administrator/components/com_boss/js/upload.js');
        $mainframe->addCSS('/administrator/components/com_boss/css/formbuilder.css');

        $rows = $database->setQuery("SELECT f.* FROM #__boss_" . $directory . "_fields AS f ORDER by f.ordering")->loadObjectList();

        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        
        //создаем массив полей шаблонов для тултипа
        $groupfields = $database->setQuery(
                "SELECT * FROM #__boss_" . $directory . "_groupfields ORDER BY fieldid")->loadObjectList();

        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }

        $groups = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_groups")->loadObjectList();

        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        
        $tpl = array();
        foreach($groupfields as $groupfield){
            foreach($groups as $group){
                if($groupfield->groupid == $group->id){
                    $object = null;
                    $object->fieldid = $groupfield->fieldid;
                    $object->groupid = $groupfield->groupid;
                    $object->template = $groupfield->template;
                    $object->type_tmpl = ($groupfield->type_tmpl == 1) ? BOSS_CONTENTS : BOSS_FORM_CATEGORY;
                    $object->name = $group->name;
                    $tpl[$groupfield->fieldid][] = $object;
                }
            }
        }

        $directories = BossDirectory::getDirectories();
        $plugins = BossPlugins::get_plugins($directory, 'fields');
        HTML_boss::showFields($rows, $directory, $directories, $plugins, $tpl, $conf);
    }
    /** поле
     * @static
     * @param  $directory
     * @return
     */
    public static function showField($directory, $id) {

        if ($directory == 0) {
            return;
        }
        $row = null;
        $database = database::getInstance();
        $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE `fieldid` = " . $id . " ORDER by ordering")->loadObject($row);

        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        $plugin = BossPlugins::get_plugin($directory, $row->type, 'fields');
        HTML_boss::showField($row, $directory, $plugin);
    }
    
    /** редактирование поля
     * @static
     * @param  $directory
     * @return bool
     */
    public static function editField($directory) {
        $task = mosGetParam($_REQUEST, 'task', '');
        $plugin_name = mosGetParam($_REQUEST, 'plugin', '');
        $mainframe = mosMainFrame::getInstance(true);
        mosCommonHTML::loadJquery();
        mosCommonHTML::loadOverlib();
        
        $fieldid = mosGetParam($_REQUEST, 'fieldid', 0);
        if (is_array($fieldid)) {
            $fieldid = $fieldid[0];
        }
        if($fieldid>0){
            $task= 'edit';
        }
        else{
            $fieldid = 0;
        }

        $database = database::getInstance();
        $row = new jDirectoryField($database, $directory);
        // load the row from the db table
        $row->load($fieldid);

        /*         * ************************* */
        $contentTypes = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_content_types ORDER BY ordering")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }


        $lists = array();
        $sort_direction = array();
        $display_title_list = array();
            
        if($plugin_name == '' && !empty($fieldid)){
            $plugin_name = $row->type;
        }
        $plugin = BossPlugins::get_plugin($directory, $plugin_name);

        $plug = null;
        $plug->type = $plugin->type;
        $plug->name = $plugin->name;

        $database->setQuery("SELECT fieldtitle,fieldvalue "
                . "\n FROM #__boss_" . $directory . "_field_values"
                . "\n WHERE fieldid=$fieldid"
                . "\n ORDER BY ordering");
        $fvalues = $database->loadObjectList('fieldtitle');
        
        $fnames = $database->setQuery("SELECT `name`  "
                . "\n FROM #__boss_" . $directory . "_fields")->loadResultArray();

        $sort_direction[] = mosHTML::makeOption('DESC', BOSS_CMN_SORT_DESC);
        $sort_direction[] = mosHTML::makeOption('ASC', BOSS_CMN_SORT_ASC);

        $display_title_list[] = mosHTML::makeOption(0, BOSS_NO_DISPLAY);
        $display_title_list[] = mosHTML::makeOption(1, BOSS_DISPLAY_DETAILS);
        $display_title_list[] = mosHTML::makeOption(2, BOSS_DISPLAY_LIST);
        $display_title_list[] = mosHTML::makeOption(3, BOSS_DISPLAY_LIST_AND_DETAILS);

        $lists['display_title'] = mosHTML::selectList($display_title_list, 'display_title', 'class="inputbox" style="width:200px;" size="1"', 'value', 'text', $row->display_title);
        $lists['required'] = mosHTML::yesnoRadioList('required', 'class="inputbox" size="1"', $row->required);
        $lists['profile'] = mosHTML::yesnoRadioList('profile', 'class="inputbox" size="1"', $row->profile);
        $lists['editable'] = mosHTML::yesnoRadioList('editable', 'class="inputbox" size="1"', $row->editable);
        $lists['searchable'] = mosHTML::yesnoRadioList('searchable', 'class="inputbox" size="1"', $row->searchable);
        $lists['sort'] = mosHTML::yesnoRadioList('sort', 'class="inputbox" size="1"', $row->sort);
        $lists['sort_direction'] = mosHTML::selectList($sort_direction, 'sort_direction', 'class="inputbox" size="1"', 'value', 'text', $row->sort_direction);
        $lists['published'] = mosHTML::yesnoRadioList('published', 'class="inputbox" size="1"', $row->published);
        $lists['filter'] = mosHTML::yesnoRadioList('filter', 'class="inputbox" size="1"', $row->filter);

        $path = JPATH_BASE . "/images/boss/$directory/fields";
        $handle = opendir($path);

        $fieldimages = bossFieldImages::getFieldImages($directory);

        $directories = BossDirectory::getDirectories();
        $plug->html = $plugin->getEditFieldOptions($row, $directory, $fieldimages, $fvalues);

        HTML_boss::editfield($row, $lists, $plug, $contentTypes, $directory, $directories, $task, $fnames);
        return true;
    }

    /** сохранение поля
     * @static
     * @param  $dir
     * @return void
     */
    public static function saveField($dir) {

        $database = database::getInstance();
        $field_action = mosGetParam($_REQUEST, 'field_action', '');
        $directories = mosGetParam($_REQUEST, 'directories', array());
        $dirFieldid = array();
        if (count($directories) == 0)
            $directories[] = $dir;

        foreach ($directories as $directory) {
            $row = new jDirectoryField($database, $directory);

            if (!$row->bind($_POST)) {
                echo "<script type=\"text/javascript\"> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
                exit();
            }

            mosMakeHtmlSafe($row);

            $row->name = str_replace(" ", "", strtolower($row->name));
            
            if($field_action == 'new'){
                $row->ordering = $database->setQuery("SELECT MAX(ordering) FROM #__boss_" . $directory . "_fields")->loadResult()+1;             
            }

            if (!$row->check()) {
                echo "<script type=\"text/javascript\"> alert('" . $row->getError() . "'); window.history.go(-2); </script>\n";
                exit();
            }
            if (!$row->store($_POST['fieldid'])) {
                echo "<script type=\"text/javascript\"> alert('" . $row->getError() . "'); window.history.go(-2); </script>\n";
                exit();
            }

            if ($row->fieldid > 0) {
                $database->setQuery("DELETE FROM #__boss_" . $directory . "_field_values WHERE fieldid='" . $row->fieldid . "'")->query();
            } else {
                $maxID = $database->setQuery("SELECT MAX(fieldid) FROM #__boss_" . $directory . "_fields")->loadResult();
                $row->fieldid = $maxID;
            }

            $field_catsid = mosGetParam($_POST, "field_catsid", array());
            $field_catsid = "," . implode(',', $field_catsid) . ",";
            if ($field_catsid != "") {
                $query = "UPDATE #__boss_" . $directory . "_fields SET catsid ='$field_catsid' WHERE fieldid=$row->fieldid ";
                $database->setQuery($query)->query();
            }

            //Update Content Fields
            $plugins = BossPlugins::get_plugins($directory, 'fields');
            $plugfield = false;
            if (isset($plugins[$row->type])) {
                $plugfield = $plugins[$row->type]->saveFieldOptions($directory, $row);
            }

            if ($plugfield == false) {

                //запрашиваем столбцы таблиц
                $tableFields = $database->getTableFields(array("#__boss_" . $directory . "_profile", "#__boss_" . $directory . "_contents"));
                //переменная определяет есть-ли в таблице профиля поле с таким названием
                $issetProfileField = (isset( $tableFields["#__boss_" . $directory . "_profile"][$row->name])) ? true : false;
                //переменная определяет есть-ли в таблице контента поле с таким названием
                $issetContentField = (isset($tableFields["#__boss_" . $directory . "_contents"][$row->name])) ? true : false;

                //если это поле используется в качестве поля профиля пользователя
                if ($row->profile == 1) {

                    //добавляем поле в таблицу профиля
                    if(!$issetProfileField)
                        $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_profile ADD `$row->name` TEXT NOT NULL")->query();
                    //удаляем поле из таблицы контента
                    if($issetContentField)
                        $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_contents DROP `$row->name`")->query();
                } else {
                    //удаляем поле из таблицы профиля
                    if($issetProfileField)
                        $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_profile DROP `$row->name`")->query();
                    //добавляем поле в таблицу контента
                    if(!$issetContentField)
                        $database->setQuery("ALTER IGNORE TABLE #__boss_" . $directory . "_contents ADD `$row->name` TEXT NOT NULL")->query();
                }
            }
            //вычисляем филдид поля в изначальном каталоге
            //if ($directory == $dir)
                $dirFieldid[$directory] = $row->fieldid;
        }
        
        echo $dirFieldid[$dir];
        return $dirFieldid[$dir];
    }

    /** удаление поля
     * @static
     * @param  $directory
     * @return
     */
    public static function removeField($directory, $fieldid=null) {

        //$tid = mosGetParam($_REQUEST, 'tid', 0);
        $tid = array($fieldid);

        if (!is_array($tid) || count($tid) < 1 || $fieldid == null) {
            echo 'false';
            exit;
        }

        $database = database::getInstance();
        foreach ($tid as $id) {
            $row = new jDirectoryField($database, $directory);
            // load the row from the db table
            $row->load($id);

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
            echo 'false';
        }

        if (count($tid)) {

            $ids = implode(',', $tid);
            $database->setQuery("DELETE FROM #__boss_" . $directory . "_field_values WHERE fieldid  IN ($ids)");
        }
        if (!$database->query()) {
            echo 'false';
        }
        echo 'true';
    }

    /** сохраниение порядка сортировки полей
     * @static
     * @param  $tid
     * @param  $directory
     * @return void
     */
    public static function saveFieldOrder($fieldids, $directory) {
        $database = database::getInstance();   
        $row = new jDirectoryField($database, $directory);
        $i = 0;
        foreach ($fieldids as $fieldid) {
            $row->load($fieldid);

            if ($row->ordering != $i) {
                $row->ordering = $i;

                if (!$row->store()) {
                    echo "false";
                    exit();
                }
            }
            $i++;
        }

        mosCache::cleanCache('com_boss');       
        echo "true";
    }

    /**
     * перемещение порядка поля
     * @static
     * @param  $uid integer The increment to reorder by
     * @param  $inc
     * @param  $directory
     * @return void
     */
    public static function orderField($uid, $inc, $directory) {
        $database = database::getInstance();

        $row = new jDirectoryField($database, $directory);
        $row->load($uid);
        $row->move($inc, "1");

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        mosRedirect("index2.php?option=com_boss&act=fields&directory=$directory");
    }

    /** публикация поля
     * @static
     * @param  $directory
     * @return
     */
    public static function publishField($directory) {
        $database = database::getInstance();

        $tid = $_GET['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo sprintf('<script> alert(\'%s\'); window.history.go(-1);</script>' . "\n", BOSS_SELECT_CONTENT_TO_BE_PUBLISH);
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

    /** изменяет обязательность заполнения поля
     * @static
     * @param  $directory
     * @return
     */
    public static function requiredField($directory) {

        $tid = $_GET['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo '<script> alert(\'Select an Content to publish\'); window.history.go(-1);</script>' . "\n";
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

}

//end class

class jDirectoryTemplatePosition extends mosDBTable {

    var $id = null;
    var $name = null;
    var $template = null;
    var $catsid = null;
    var $published = 1;

    /**
     * Constructor
     * @param database A database connector object
     */
    function __construct(&$db, $directory) {

        $this->mosDBTable('#__boss_' . $directory . '_groups', 'id', $db);
    }

//end func
}

//end class

/**
 * Различные вспомогательные функции
 */
class boss_helpers {

    /**Декодирует в json без замены русских букв на сущности
     * @static
     * @param $str
     * @return mixed
     */
        public static function json_encode_cyr($str) {
            $arr_replace_utf = array(
                '\u0410','\u0430','\u0411','\u0431','\u0412','\u0432','\u0413','\u0433','\u0414','\u0434',
                '\u0415','\u0435','\u0401','\u0451','\u0416','\u0436','\u0417','\u0437','\u0418','\u0438',
                '\u0419','\u0439','\u041a','\u043a','\u041b','\u043b','\u041c','\u043c','\u041d','\u043d',
                '\u041e','\u043e','\u041f','\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442',
                '\u0423','\u0443','\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447',
                '\u0428','\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
                '\u042d','\u044d','\u042e','\u044e','\u042f','\u044f', '\'');
            $arr_replace_cyr = array(
                'А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д',
                'Е', 'е', 'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и',
                'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н',
                'О' ,'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т',
                'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч',
                'Ш', 'ш', 'Щ', 'щ', 'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь',
                'Э', 'э', 'Ю', 'ю', 'Я', 'я', '"'
            );
            $str1 = json_encode($str);
            $str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
            return $str2;
        }

    /**
	 * Получение параметра из заголовков сервера или клиенского браузера
	 *
	 * @param string $name    название параметра
	 * @param string $default значение для параметра, используемое по умолчанию
	 *
	 * @return mixed результат, массив или строка, либо false если параметр не обнаружен ( по умолчанию )
	 */
	public static function header( $name , $default = false ) {

		$name_ = 'HTTP_' . strtoupper( str_replace( '-' , '_' , $name ) );
		if ( isset( $_SERVER[$name_] ) ) {
			return $_SERVER[$name_];
		}

		if ( function_exists( 'apache_request_headers' ) ) {
			$headers = apache_request_headers();
			if ( isset( $headers[$name] ) ) {
				return $headers[$name];
			}
		}

		return $default;
	}

    /**
	 * Проверка работы через Ajax-соединение
	 * @return bool результат проверки
	 */
	public static function is_ajax() {
		return 'xmlhttprequest' == strtolower( self::header( 'X_REQUESTED_WITH' ) );
	}

    /** удаление непустой папки
     * @param  $dirName
     * @return void
     */
    public static function rmdir_rf($dirName) {

        if ($handle = opendir($dirName)) {

            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dirName . '/' . $file)) {
                        self::rmdir_rf($dirName . '/' . $file);
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

    /** копирование папки с содержимым
     * @static
     * @param  $pathFrom
     * @param  $pathTo
     * @return void
     */
    public static function copy_folder_rf($pathFrom, $pathTo) {

        mosMakePath(JPATH_BASE, str_replace(JPATH_BASE, '', $pathTo));

        if ($handle = opendir($pathFrom)) {

            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($pathFrom . '/' . $file)) {
                        if (!is_dir($pathTo . '/' . $file)) {
                            @mkdir($pathTo . '/' . $file);
                        }
                        boss_helpers::copy_folder_rf($pathFrom . '/' . $file, $pathTo . '/' . $file);
                    } elseif (is_file($pathFrom . '/' . $file)) {
                        if (is_file($pathTo . '/' . $file)) {
                            @unlink($pathTo . '/' . $file);
                        }
                        @copy($pathFrom . '/' . $file, $pathTo . '/' . $file);
                    }
                }
            }

            closedir($handle);
        }
    }

    /**
     * @static
     * @param  $directory
     * @return void
     */
    public static function loadBossLang($directory) {
        global $mosConfig_lang;
        $defaultLangPath = JPATH_BASE . DS . 'components' . DS . 'com_boss' . DS . 'lang' . DS;
        $directoryLangPath = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory . DS . 'lang/';
        if (!$directory) {
            if (is_file($defaultLangPath . $mosConfig_lang . '.php')) {
                require_once( $defaultLangPath . $mosConfig_lang . '.php' );
            } else if (is_file($defaultLangPath . 'russian.php')) {
                require_once( $defaultLangPath . 'russian.php' );
            }
        } else if (is_file($directoryLangPath . $mosConfig_lang . '.php')) {
            require_once( $directoryLangPath . $mosConfig_lang . '.php' );
        } else if (is_file($directoryLangPath . 'russian.php')) {
            require_once( $directoryLangPath . 'russian.php' );
        } else if (is_file($defaultLangPath . $mosConfig_lang . '.php')) {
            require_once( $defaultLangPath . $mosConfig_lang . '.php' );
        } else if (is_file($defaultLangPath . 'russian.php')) {
            require_once( $defaultLangPath . 'russian.php' );
        }
    }

    /** Подключает языковой файл плагина в случае его наличия.
     * @static
     * @param $directory - каталог
     * @param $typePlugin - название папки типа плагинов
     * @param $plugin - название папки и класса плагина
     * @return void
     */
    public static function loadBossPluginLang($directory, $typePlugin, $plugin) {
        global $mosConfig_lang;

        $langPath = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory . DS . 'plugins' . DS . $typePlugin . DS . $plugin . DS . 'lang' . DS;
        
        if (is_file($langPath. $mosConfig_lang . '.php')) {
            require_once( $langPath . $mosConfig_lang . '.php' );
        }
        else if (is_file($langPath . 'russian.php')) {
            require_once( $langPath . 'russian.php' );
        }
    }

    public static function bossToolTip($tooltip, $title = '', $image = 'tooltip.png') {
        if (!empty($title))
            $title = $title . ':: ';
        $image = JPATH_SITE . '/includes/js/ThemeOffice/' . $image;
        $tip = '<img src="' . $image . '" border="0" title="' . $title . $tooltip . '"/>';
        return $tip;
    }

    /**
     * @static
     * @param  $directory
     * @return void
     */
    public static function upload_image($directory) {
        $file = JPATH_BASE . "/images/boss/$directory/fields/" . basename($_FILES['uploadfile']['name']);

        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    /**
     * @static
     * @param $directory
     * @param $folder - может быть строкой или массивом
     * @return void
     */
    public static function upload_file($directory, $folder) {

        $max_filesize = mosGetParam($_REQUEST, 'max_filesize', 0);
        if($max_filesize > 0 && filesize($_FILES['uploadfile']['tmp_name']) > $max_filesize){
            echo "error_max_filesize";
            return;
        }

        if(is_array($folder)){
            $folder = implode('/', $folder);
        }
        //тарнслитерированное имя загружаемого файла
        $filename = russian_transliterate(basename($_FILES['uploadfile']['name']));
        //целевой файл
        $file = JPATH_BASE . "/images/boss/$directory/$folder/" . $filename;
        //если целевой файл есть, то даем ему уникальное имя
        if(is_file($file)){
            $i=0;
            $fname = $filename;
            while (file_exists(JPATH_BASE . "/images/boss/$directory/$folder/" . $fname)) {
                $i++;
                $fname = $i . "_" . $filename;
            }
            $file = JPATH_BASE . "/images/boss/$directory/$folder/" . $fname;
            $filename = $fname;
        }
        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
            echo $filename;
        } else {
            echo "error";
        }
    }

    /** Удаляет файл, созданный при экспорте каталога
     * @static
     * @param  $directory
     * @return void
     */
    public static function delete_pack($directory) {
        $pack = mosGetParam($_REQUEST, 'pack', '');
        $file = JPATH_BASE . "/images/boss/$directory/$pack.zip";
        if (is_file($file))
            unlink($file);
        if (is_file($file))
            echo 'no';
        else
            echo 'yes';
    }


    public static function delete_file($directory) {
        $file = mosGetParam($_REQUEST, 'file', '');
        $folder = mosGetParam($_REQUEST, 'folder', '');
        if(is_array($folder)){
            $folder = implode('/', $folder);
        }
        $file = JPATH_BASE . "/images/boss/$directory/$folder/$file";
        if (is_file($file))
            unlink($file);
        if (is_file($file))
            echo 'no';
        else
            echo 'yes';
    }

    /** универсальная функция изменения состояния поля с 0 на 1 и наоборот
     * @param  $table
     * @param  $field
     * @param  $id
     * @param  $where_field
     * @return
     */
    public static function changeState($table, $field, $id, $where_field) {
        if (empty($id)) {
            return;
        }

        $database = database::getInstance();
        $object = null;
        //вычисляем состояние поля
        $database->setQuery("SELECT `$field` FROM $table WHERE `$where_field` = $id LIMIT 1");
        $database->query();
        $state = $database->loadResult();
        //меняем состояние на противоположное
        if ($state == 1)
            $state_new = 0;
        else
            $state_new = 1;
        //обновляем состояние
        $database->setQuery("UPDATE $table SET `$field` = '$state_new' WHERE `$where_field` = $id ");
        $database->query();
        if ($database->getErrorNum())
            $ret = 'error.png';
        else {
            if (strpos($table, "_contents") === false) {
                $ret = ($state_new == 1) ? 'tick.png' : 'publish_x.png';
            } else {
                $date = date('Y-m-d');
                $q = "SELECT `published`, `date_publish`, `date_unpublish` FROM $table WHERE `id` = $id LIMIT 1";
                $database->setQuery($q);
                $ret = $q;
                $database->query();
                $database->loadObject($object);
                if ($object->published == 0) {
                    $ret = 'publish_x.png';
                } elseif ($object->published == 1 && ($object->date_publish > $date && $object->date_publish != '0000-00-00')) {
                    $ret = 'publish_y.png';
                } elseif ($object->published == 1 && ($object->date_unpublish < $date && $object->date_unpublish != '0000-00-00')) {
                    $ret = 'publish_r.png';
                } else {
                    $ret = 'tick.png';
                }
            }
        }
        echo $ret;
    }

    public static function loadCats($directory) {
        $database = database::getInstance();

        $q = "SELECT * " .
                "FROM #__boss_" . $directory . "_categories " .
                "WHERE published = 1 ORDER BY parent, ordering, name";

        $list = $database->setQuery($q)->loadObjectList();

        return $list;
    }

    public static function get_cattree($directory, $conf, $onlyNotEmpty=1, $mode='read', $isUpdateMode=0) {

        if ($directory == 0) {
            return;
        }

        $database = database::getInstance();
        
        //права пользователя
        if($conf->allow_rights){
            global $my;
            $my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
            $rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('category'));
            if($mode=='read'){
                $action = 'show_category';
            }
            else if($mode == 'write' && $isUpdateMode == 0){
                $action = 'create_content';
            }
            else if($mode == 'write' && $isUpdateMode == 1){
                $action = 'edit';
            }
        }

        if ($onlyNotEmpty == 1) {
            $q = "SELECT c.*, count(*) as num_contents,a.id as not_empty, parent.id as is_parent " .
                    "FROM #__boss_" . $directory . "_categories as c " .
                    "LEFT JOIN #__boss_" . $directory . "_categories as parent ON c.id = parent.parent " .
                    "LEFT JOIN #__boss_" . $directory . "_content_category_href as cch ON c.id = cch.category_id " .
                    "LEFT JOIN #__boss_" . $directory . "_contents as a ON a.id = cch.content_id " .
                    "WHERE  c.published = 1 " .
                    "GROUP BY c.id " .
                    "ORDER BY c.parent, c.ordering, c.name";
            $list = $database->setQuery($q)->loadObjectList();

            // establish the hierarchy of the menu
            $tree = array();
            // first pass - collect children
            if (isset($list)) {
                foreach ($list as $v) {
                    if ($onlyNotEmpty == 1 || $v->not_empty > 0 || $v->is_parent > 0) {
                        //если в категории нет контента, но есть подкатегории проверяем есть-ли в подкатегориях контент
                        if ($v->not_empty < 1 && $v->is_parent > 0 && $onlyNotEmpty == 1) {
                            $notEmptyChild = 0;
                            foreach ($list as $child) {
                                if ($child->parent == $v->id && $child->not_empty > 0) {
                                    $notEmptyChild = 1;
                                    break;
                                }
                            }
                        }
                        //если категория не пуста, или есть подкатегории и они не пусты
                        if ($onlyNotEmpty != 1 || $v->not_empty > 0 || ($v->is_parent > 0 && $notEmptyChild == 1)) {
                            $pt = $v->parent;
                            $list_temp = @$tree[$pt] ? $tree[$pt] : array();
                            array_push($list_temp, $v);
                            
                            //права пользователя
                            if($conf->allow_rights){
                                $rights->bind_rights($v->rights);
                                if($action == 'edit' && ($rights->allow_me('edit_all_content', $my->groop_id) || $rights->allow_me('edit_user_content', $my->groop_id))){
                                    $tree[$pt] = $list_temp; 
                                }
                                else if($rights->allow_me($action, $my->groop_id) ){
                                    $tree[$pt] = $list_temp; 
                                } 
                            }
                            else{
                                $tree[$pt] = $list_temp;
                            }
                        }
                    }
                }
            }
        } else {

            $list = boss_helpers::loadCats($directory);

            // establish the hierarchy of the menu
            $tree = array();
            // first pass - collect children
            if (isset($list)) {
                foreach ($list as $v) {
                    $pt = $v->parent;
                    $list_temp = @$tree[$pt] ? $tree[$pt] : array();
                    array_push($list_temp, $v);
                    //права пользователя
                    if($conf->allow_rights){
                        $rights->bind_rights($v->rights);
                        if($action == 'edit' && ($rights->allow_me('edit_all_content', $my->groop_id) || $rights->allow_me('edit_user_content', $my->groop_id))){
                            $tree[$pt] = $list_temp; 
                        }
                        else if($rights->allow_me($action, $my->groop_id) ){
                            $tree[$pt] = $list_temp; 
                        } 
                    }
                    else{
                        $tree[$pt] = $list_temp;
                    }
                }
            }
        }
        return $tree;
    }

    public static function get_subpathlist($cats, $catid, &$list, $itemid, $order, $directory) {
        $i = 0;
        if (isset($cats)) {
            foreach ($cats as $cat) {
                if ($cat->parent == $catid) {
                    $list[$i]->text = $cat->name; //." (".$cat->num_contents.")";
                    $list[$i++]->link = sefRelToAbs('index.php?option=com_boss&amp;task=show_category&amp;catid=' . $cat->id . '&amp;order=' . $order . '&amp;directory=' . $directory . '&amp;Itemid=' . $itemid);
                }
            }
        }
    }

    public static function get_pathlist($cats, $catid, $catname, &$list, $itemid, $order, $directory) {
        $orderlist = array();
        if (isset($cats)) {
            foreach ($cats as $c) {
                $orderlist[$c->id] = $c;
            }

            $i = 0;
            $list[$i]->text = $orderlist[$catid]->name;
            $list[$i]->link = sefRelToAbs('index.php?option=com_boss&amp;task=show_category&amp;catid=' . $catid . '&amp;slug=' . $orderlist[$catid]->slug . '&amp;order=' . $order . '&amp;directory=' . $directory . '&amp;Itemid=' . $itemid);
            $i++;

            if ($catid != -1) {
                $current = $catid;

                while ($orderlist[$current]->parent != 0) {
                    $current = $orderlist[$current]->parent;
                    $list[$i]->text = $orderlist[$current]->name;
                    $list[$i]->link = sefRelToAbs('index.php?option=com_boss&amp;task=show_category&amp;catid=' . $orderlist[$current]->id . '&amp;slug=' . $orderlist[$current]->slug . '&amp;order=' . $order . '&amp;directory=' . $directory . '&amp;Itemid=' . $itemid);
                    $i++;
                }
            }
        }
    }

    public static function recurse_search($rows, &$list, $catid) {
        if (isset($rows)) {
            foreach ($rows as $row) {
                if ($row->parent == $catid) {
                    $list[] = $row->id;
                    self::recurse_search($rows, $list, $row->id);
                }
            }
        }
    }

    public static function show_list($text, $description, $url, $task, $search, $text_search, $name_search, $order, $catid, $limitstart, $update_possible, $jDirectoryHtmlClass, $directory, $template_name, $tagContentIds=array(), $type_content = 0) {
        global $my;
        $database = database::getInstance();
        mosMainFrame::addLib('pageNavigation');
        // get configuration
        $conf = getConfig($directory);
        $sort = null;
        $tags = array();
        $params = array();

        $rating = BossPlugins::get_plugin($directory, $conf->rating, 'ratings');
        $viewsPlugin = BossPlugins::get_plugin($directory, 'viewSelector', 'views');
        $views = $viewsPlugin->getOptions($directory);
        $ratingQuery = $rating->queryString($directory, $conf);
        
        if($conf->comment_sys == 1) 
            $comment_sys = 'defaultComment';
        else
            $comment_sys = 'jcomment';
        $comments = BossPlugins::get_plugin($directory, $comment_sys, 'comments');
        $commentsQuery = $comments->queryStringList($directory, $conf);
        
        //подбираем таблицы  и поля поиска в зависимости от поисковых данных в $search
        $tables = $search1 = '';
        if (substr_count($search, "cch.category_id") > 0) {
            $tables .= ", #__boss_" . $directory . "_content_category_href AS cch ";
            $search1 = " AND a.id = cch.content_id ";
        }
      
        $type_content_q = ($type_content == 0) ? '' : "AND (FIND_IN_SET($type_content, `catsid`) > 0 OR `catsid` = ',-1,')";
          
        $fields_searchable = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields " .
                        "WHERE filter = 1 AND published = 1 AND profile = 0 " . $type_content_q .
                        " ORDER by ordering")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }

        if (isset($fields_searchable)) {
            $plugins = BossPlugins::get_plugins($directory, 'fields');
            foreach ($fields_searchable as $fsearch) {
                if (isset($plugins["$fsearch->type"])) {
                    $search .= $plugins["$fsearch->type"]->search($directory, $fsearch->name);
                }
            }
        }

        if (count($tagContentIds) > 0)
            $search .= " AND  a.id IN (" . implode(', ', $tagContentIds) . ") ";
        if ($text_search <> "") {
            $search .= " AND ( a.name LIKE '%$text_search%' ";
            foreach ($fields_searchable as $f) {
                $search .= " OR a.$f->name LIKE '%$text_search%' ";
            }
            $search .= " ) ";
        }
        if ($name_search <> "") {
            $search .= " AND ( a.name LIKE '%$name_search%' ) ";
        }

        $search .= " AND a.published = 1 \n";

        $search .= "AND (a.date_publish = '0000-00-00 00:00:00' OR a.date_publish <= NOW()) \n";
        $search .= "AND (a.date_unpublish = '0000-00-00 00:00:00' OR a.date_unpublish >= NOW()) \n";
        
        //если это главная страница
        if($task == 'show_frontpage'){
            $search .= "AND a.frontpage = 1 \n";
        }

        $total = $database->setQuery("SELECT COUNT(*) FROM #__boss_" . $directory . "_contents as a $tables WHERE $search $search1")->loadResult();
        $limit = $conf->contents_per_page;
        $taskNav = new mosPageNav($total, $limitstart, $limit);

        if (($conf->show_contact == 1) && ($my->id == "0")) {
            $show_contact = 0;
        } else {
            $show_contact = 1;
        }


        $order_request = mosGetParam($_REQUEST, 'order', '');

        if ($order == -1) {
            $order_text = "a.views DESC, a.date_created DESC ,a.id DESC";
        } else if ($order != 0) {
            $database->setQuery("SELECT f.name,f.sort_direction,f.type FROM #__boss_" . $directory . "_fields AS f WHERE f.fieldid=$order AND f.published = 1");
            $database->loadObject($sort);

            if (($sort->type == "number") || ($sort->type == "price"))
                $order_text = "a." . $sort->name . " * 1 " . $sort->sort_direction;
            else
                $order_text = "a." . $sort->name . " " . $sort->sort_direction;
        }
        elseif ($order_request == 'last_comment')
            $order_text = "a.date_last_сomment DESC, a.id DESC";
        else {
            //default ordering
            $default_order_by = $conf->default_order_by;
            switch ($default_order_by) {
                case 'last_comment':
                    $order_text = "a.date_last_сomment DESC, a.id DESC";
                    break;
                case 0:
                    $order_text = "a.date_created DESC, a.id DESC";
                    break;
                default:
                    $database->setQuery("SELECT f.name,f.sort_direction,f.type FROM #__boss_" . $directory . "_fields AS f WHERE f.fieldid='" . (int) $default_order_by . "' AND f.published = 1");
                    $database->loadObject($sort);
                    if (empty($sort))
                        $order_text = "a.date_created DESC, a.id DESC";
                    elseif (($sort->type == "number") || ($sort->type == "price"))
                        $order_text = "a." . $sort->name . " * 1 " . $sort->sort_direction;
                    else
                        $order_text = "a." . $sort->name . " " . $sort->sort_direction;
                    break;
            }
        }

        //сортировка в зависимости от главная страница
        $ordering = ($task == 'show_frontpage') ? "a.ordering" : $order_text;

        $q = "SELECT a.*, a.userid as user_id, p.name as parent, p.id as parentid, c.name as cat, c.id as catid, c.rights as rights, \n";
        if ($show_contact == 1) {
            $q .= "profile.*, \n";
            $q .= "u.email as user_email, u.name as user_fio, \n";
        }
        $q .= $ratingQuery['fields'];
        $q .= $commentsQuery['fields'];
        $q .= " u.username as user ";
        $q .=   "FROM #__boss_" . $directory . "_contents as a \n" .
                "LEFT JOIN  #__boss_" . $directory . "_content_category_href AS cch ON a.id = cch.content_id \n" .
                $commentsQuery['tables'] .
                $ratingQuery['tables'] .
                "LEFT JOIN #__users as u ON a.userid = u.id \n";
        if ($show_contact == 1) {
            $q .= "LEFT JOIN #__boss_" . $directory . "_profile as profile ON a.userid = profile.userid \n";
        }
        $q .= "LEFT JOIN #__boss_" . $directory . "_categories as c ON cch.category_id = c.id \n" .
                "LEFT JOIN #__boss_" . $directory . "_categories as p ON c.parent = p.id \n" .
                "WHERE $search AND c.published = 1 \n" .
                "GROUP BY a.id \n" .
                "ORDER BY $ordering LIMIT " .
                $limitstart . ',' . $limit;
        $contents = $database->setQuery($q)->loadObjectList('id');
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }
        if ($show_contact == 1) { //вычисляем название полей профиля пользователя для идентификации их в контенте.
            $profileFields = $database->setQuery("SELECT f.name, f.title FROM #__boss_" . $directory . "_fields AS f WHERE f.profile = 1 ORDER BY f.ordering")->loadObjectList();
        } else {
            $profileFields = array();
        }

        //вылавливаем иды контента
        $contentIds = array_keys($contents);
        //запрашиваем теги, соотв. идам контента
        if (count($contentIds) > 0) {
            $database->setQuery("SELECT obj_id, tag FROM #__content_tags WHERE  obj_type = 'com_boss_" . $directory . "' AND obj_id IN (" . implode(', ', $contentIds) . ") ORDER BY obj_id, tag");
            $tags = $database->loadObjectList();
            if ($database->getErrorNum()) {
                echo $database->stderr();
                return false;
            }
            //преобразуем объект в массив
            $tagArr = array();
            foreach ($tags as $tag) {
                $tagArr[$tag->obj_id][] = $tag->tag;
            }
            //преобразуем двумерный массив в список строк
            $tags = array();
            foreach ($tagArr as $key => $val) {
                $tags[$key] = self::arr_to_links($directory, $val);
            }
            unset($tagArr);
        }

        $field_values = self::loadFieldValues($directory);

        $itemid = getBossItemid($directory, $catid);

        $nav_link = $url . "&amp;Itemid=" . $itemid;

        $conf->show_contact = $show_contact;
        $conf->update_possible = $update_possible;

        $database->setQuery("SELECT g.* FROM #__boss_" . $directory . "_groups AS g WHERE g.published = 1 AND g.template='" . $template_name . "'");
        $groupstemp = $database->loadObjectList('name');

        $groups = array();
        foreach ($groupstemp as $grp) {
            if ((strpos($grp->catsid, ",$catid,") !== false) || (strpos($grp->catsid, ",-1,") !== false)) {
                $groups[] = $grp->id;
            }
        }

        if (count($groups) > 0) {
            $groupids = implode(',', $groups);
            $groupids = "AND g.id IN ($groupids)";
        } else {
            $groupids = '';
        }

        $database->setQuery("SELECT g.name as gname,f.* FROM #__boss_" . $directory . "_groupfields as fg " .
                "LEFT JOIN #__boss_" . $directory . "_groups AS g ON fg.groupid = g.id " .
                "LEFT JOIN #__boss_" . $directory . "_fields AS f ON fg.fieldid = f.fieldid " .
                "WHERE g.published = 1 $groupids AND f.published = 1 AND fg.type_tmpl = 'category' " .
                "ORDER BY fg.ordering ASC ");
        $fieldsgrouptemp = $database->loadObjectList();

        $fieldsgroup = array();
        $fields = array();

        foreach ($fieldsgrouptemp as $f) {
            //отвязываем группы от привязки полей к категориям для автономной работы редактирования с фронта и показа.
            if (!isset($fieldsgroup[$f->gname]))
                $fieldsgroup[$f->gname] = array();

            if (!isset($fields[$f->name]))
                $fields[$f->name] = $f;

            $fieldsgroup[$f->gname][] = $f;
        }
        
        //права пользователя
        $perms = null;
        $rights = null;
        if($conf->allow_rights){
            $rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
        }

        $jDirectoryHtmlClass->rights = $rights;
        $jDirectoryHtmlClass->fieldsgroup = $fieldsgroup;
        $jDirectoryHtmlClass->views = $views;
        $jDirectoryHtmlClass->viewsPlugin = $viewsPlugin;
        $jDirectoryHtmlClass->fields = $fields;
        $jDirectoryHtmlClass->fields_searchable = $fields_searchable;
        $jDirectoryHtmlClass->tags = $tags;
        $jDirectoryHtmlClass->category->id = $catid;
        $jDirectoryHtmlClass->category->title = $text;
        $jDirectoryHtmlClass->category->description = $description;
        $jDirectoryHtmlClass->tasknav = $taskNav;
        $jDirectoryHtmlClass->nav_link = $nav_link;
        $jDirectoryHtmlClass->task = $task;
        $jDirectoryHtmlClass->itemid = $itemid;
        $jDirectoryHtmlClass->contents = $contents;
        $jDirectoryHtmlClass->url = $url;
        $jDirectoryHtmlClass->field_values = $field_values;
        $jDirectoryHtmlClass->conf = $conf;
        $jDirectoryHtmlClass->profileFields = $profileFields;
        $jDirectoryHtmlClass->plugins = $plugins = BossPlugins::get_plugins($directory, 'fields');
        $jDirectoryHtmlClass->directory = $directory;
        $jDirectoryHtmlClass->template_name = $template_name;
        $jDirectoryHtmlClass->rating = $rating;
        $jDirectoryHtmlClass->comments = $comments;
        if ($conf->allow_comments == 1) {
            $jDirectoryHtmlClass->fields['last_comment']->sort = 1;
            $jDirectoryHtmlClass->fields['last_comment']->fieldid = 'last_comment';
            $jDirectoryHtmlClass->fields['last_comment']->title = BOSS_DATE_LAST_COMMENT;
        }

        $jDirectoryHtmlClass->displayList();


        $fields = $database->setQuery( "SELECT f.* FROM #__boss_".$directory."_fields AS f WHERE f.published = 1" )->loadObjectList('name');
        //подключаем некешируемую информацию из плагинов.
        foreach($fields as $field){
            if(method_exists($plugins[$field->type],'addInHead')){
                $params = array_merge_recursive($params, $plugins[$field->type]->addInHead($fields, $field_values[$field->fieldid], $directory));
            }
        }
        return $params;
    }

    /**
     * @static
     * @param  $directory - каталог
     * @param  $tags - массив тэгов
     * @param string $ds - разделитель тегов
     * @return bool|string - возвращает строку, состоящую из ссылок на список контента, содержащий этот тег
     */
    public static function arr_to_links($directory, $tags, $ds = ', ') {
        if (!$tags) {
            return false;
        }
        $itemid = intval(mosGetParam($_REQUEST, 'Itemid', 0));
        $return = array();
        foreach ($tags as $tag) {
            $return[] = '<a class="tag" href="' . sefRelToAbs('index.php?option=com_boss&task=search_tags&directory=' . $directory . '&tag=' . urlencode($tag) . '&Itemid=' . $itemid) . '">' . $tag . '</a>';
        }

        return implode($ds, $return);
    }

    /**
     * @static
     * @param  $username
     * @param  $password
     * @param  $email
     * @param  $userid
     * @param  $conf
     * @return null|string
     */
    public static function check_account($username, $password, $email, &$userid, $conf) {
        global $mosConfig_uniquemail;
        $mainframe = mosMainFrame::getInstance();
        $database = database::getInstance();

        $user = null;

        josSpoofCheck();

        $database->setQuery("SELECT * "
                . "\nFROM #__users u "
                . "\nWHERE u.username='" . $username . "'"
        );
        $database->loadObject($user);
        if (isset($user)) {
            //User exist, Verify Password
            if ((strpos($user->password, ':') === false) && $user->password == md5($password)) {
                // Old password hash storage but authentic ... lets convert it
                $salt = mosMakePassword(16);
                $crypt = md5($password . $salt);
                $user->password = $crypt . ':' . $salt;

                // Now lets store it in the database
                $query = 'UPDATE #__users'
                        . ' SET password = ' . $database->Quote($user->password)
                        . ' WHERE id = ' . (int) $user->id;
                $database->setQuery($query);
                if (!$database->query()) {
                    // This is an error but not sure what to do with it ... we'll still work for now
                }
            }
            list($hash, $salt) = explode(':', $user->password);
            $cryptpass = md5($password . $salt);
            if ($hash == $cryptpass) {
                //Login Ok
                $mainframe->login($username, $password);
                $userid = $user->id;
                return null;
            } else {
                //Login Failed
                return "bad_password";
            }
        } else {
            if ($mosConfig_uniquemail == 1) {
                $database->setQuery("SELECT * "
                        . "\nFROM #__users u "
                        . "\nWHERE u.email='" . $email . "'"
                );
                $database->loadObject($user);
                if (isset($user)) {
                    //Login Failed
                    return "email_already_used";
                }
            }

            //Create Account
            echo BOSS_ACCOUNT_CREATE;
            $userid = self::saveRegistration();
            $mainframe->login($username, $password);
            return null;
        }
    }

    /**
     * @static
     * @return
     */
    public static function saveRegistration() {
        global $acl;
        $database = database::getInstance();

        josSpoofCheck();

        $row = new mosUser($database);

        if (!$row->bind($_POST, 'usertype')) {
            mosErrorAlert($row->getError());
        }

        mosMakeHtmlSafe($row);

        $row->id = 0;
        $row->usertype = '';
        $row->gid = $acl->get_group_id('Registered', 'ARO');

        if (!$row->check()) {
            echo "<script> alert('" . html_entity_decode($row->getError()) . "'); window.history.go(-1); </script>\n";
            exit();
        }

        $row->password = md5($row->password);
        $row->registerDate = date('Y-m-d H:i:s');

        if (!$row->store()) {
            echo "<script> alert('" . html_entity_decode($row->getError()) . "'); window.history.go(-1); </script>\n";
            exit();
        }
        $row->checkin();

        $database->setQuery("SELECT u.id "
                . "\nFROM #__users u "
                . "\nWHERE u.username='" . $row->username . "'"
        );
        $userid = $database->loadResult();

        return $userid;
    }

    /**
     * @static
     * @param  $directory
     * @return array
     */
    public static function loadAlphaIndex($directory) {
        $database = database::getInstance();
        $return = array();
        $return['ruAlf'] = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л',
            'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш',
            'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
        $return['enAlf'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $return['numeric'] = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        $return['alphaContent'] = $database->setQuery("SELECT DISTINCT UCASE(LEFT(`name`, 1)) FROM #__boss_" . $directory . "_contents WHERE (date_publish = '0000-00-00 00:00:00' OR date_publish <= NOW()) AND (date_unpublish = '0000-00-00 00:00:00' OR date_unpublish >= NOW()) AND published = '1'")->loadResultArray();

        return $return;
    }

    /**
     * @static
     * @param  $interval
     * @param  $number
     * @param  $date
     * @return int|string
     */
    public static function DateAdd($interval, $number, $date) {
        $date_time_array = getdate(strtotime($date));
        $hours = $date_time_array['hours'];
        $minutes = $date_time_array['minutes'];
        $seconds = $date_time_array['seconds'];
        $month = $date_time_array['mon'];
        $day = $date_time_array['mday'];
        $year = $date_time_array['year'];

        switch ($interval) {

            case 'yyyy':
                $year+=$number;
                break;
            case 'q':
                $year+= ( $number * 3);
                break;
            case 'm':
                $month+=$number;
                break;
            case 'y':
            case 'd':
            case 'w':
                $day+=$number;
                break;
            case 'ww':
                $day+= ( $number * 7);
                break;
            case 'h':
                $hours+=$number;
                break;
            case 'n':
                $minutes+=$number;
                break;
            case 's':
                $seconds+=$number;
                break;
        }
        $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
        $timestamp = date("d.m.Y", $timestamp);
        return $timestamp;
    }

    public static function loadFieldValues($directory) {
        $database = database::getInstance();
        $field_values = array();
        //get value fields
        $fieldvalues = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_field_values ORDER by ordering ")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }

        // first pass - collect children
        if (isset($fieldvalues)) {
            foreach ($fieldvalues as $v) {
                $pt = $v->fieldid;
                $field_values[$pt][] = $v;
            }
        }
        return $field_values;
    }

}

/**
 * Класс работы с каталогами.
 */
class BossDirectory {

    /** объект-лист каталогов
     * @static
     * @return bool
     */
    public static function getDirectories() {
        $database = database::getInstance();
        $directories = $database->setQuery("SELECT id,name FROM #__boss_config")->loadObjectList("id");
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }
        return $directories;
    }

    /** список каталогов
     * @static
     * @param  $directory
     * @return void
     */
    public static function showDirectories($directory, $conf) {
        $directories = self::getDirectories();
        HTML_boss::showDirectories($directories, $directory, $conf);
    }

    /** создание нового каталога
     * @return void
     */
    public static function addDirectory() {
        $dir = installNewDirectory();
        mosRedirect("index2.php?option=com_boss&act=manager", $dir['errors']);
    }

    /*     * удаление каталога
     * @static
     * @return void
     */

    public static function deleteDirectory() {

        $tid = mosGetParam($_REQUEST, 'tid', 0);
        if (!is_array($tid) || count($tid) < 1) {
            echo "<script type=\"text/javascript\"> alert('" . BOSS_SELECT_GROUP_TO_BE_DELETED . "'); window.history.go(-1);</script>\n";
            exit;
        }
        $msg = '';

        if (count($tid)) {
            foreach ($tid as $id) {
                self::removeDirectory($id);
            }
        }

        mosRedirect("index2.php?option=com_boss&act=manager", $msg);
    }

// удаление каталога и всех его дирректорий
    /**
     * @static
     * @param  $id
     * @return void
     */
    public static function removeDirectory($id) {
        $database = database::getInstance();

        $database->setQuery("DROP TABLE IF EXISTS `#__boss_" . $id . "_categories`, " .
                "`#__boss_" . $id . "_contents`, " .
                "`#__boss_" . $id . "_content_category_href`, " .
                "`#__boss_" . $id . "_content_types`, " .
                "`#__boss_" . $id . "_field_values`, " .
                "`#__boss_" . $id . "_fields`, " .
                "`#__boss_" . $id . "_groupfields`, " .
                "`#__boss_" . $id . "_groups`, " .
                "`#__boss_" . $id . "_profile`, " .
                "`#__boss_" . $id . "_rating`, " .
                "`#__boss_" . $id . "_reviews`; ")->query();

        $database->setQuery("DELETE FROM `#__boss_config` WHERE `id` = $id")->query();

        boss_helpers::rmdir_rf(JPATH_BASE . "/images/boss/$id");
    }

}

/**
 * Класс работы с шаблонами.
 */
class BossTemplates {

    public static function change_template($directory, $fieldid){
        $database = database::getInstance();
        $q = "SELECT `title` FROM #__boss_" . $directory . "_fields 
        WHERE `fieldid` = $fieldid LIMIT 1";
        $fieldtitle = $database->setQuery($q)->loadResult();
        
        $templates = self::getTemplates();
        $tpl=array();
        $tpl[] = mosHTML::makeOption('0', BOSS_SELECT_TEMPLATE);
        foreach($templates as $template){
            $tpl[] = mosHTML::makeOption($template, $template);
        }
        $templates = mosHTML::selectList($tpl, 'template', 'class="inputbox_tpl" id="template" size="1" onchange="tpl_poz_field()"', 'value', 'text', '');
        
        $tpl=array();
        $tpl[] = mosHTML::makeOption(0, BOSS_SELECT_TYPE_TEMPLATE);
        $tpl[] = mosHTML::makeOption('category', BOSS_FORM_CATEGORY);
        $tpl[] = mosHTML::makeOption('content', BOSS_LIST_CONTENTS);
        $type_tpl = mosHTML::selectList($tpl, 'template_type', 'class="inputbox_tpl" id="template_type" size="1" onchange="tpl_poz_field()"', 'value', 'text', '');
        
        HTML_boss::change_template($directory, $fieldid, $fieldtitle, $templates, $type_tpl);
    }
    
    public static function load_poz($directory, $fieldid){
        $database = database::getInstance();
        $template = mosGetParam($_REQUEST, 'template', '');
        $template_type = mosGetParam($_REQUEST, 'template_type', '');
        if(empty($template) || empty($template_type)){
            return;
        }
        
        $q = "SELECT `id`, `name` FROM #__boss_" . $directory . "_groups 
                WHERE `template` = '$template' AND `published` = 1 
                AND `type_tmpl` = '$template_type' ORDER BY `name`";
        $poz = $database->setQuery($q)->loadObjectList();
        
        $q = "SELECT `groupid` FROM #__boss_" . $directory . "_groupfields 
                WHERE `template` = '$template' AND `type_tmpl` = '$template_type' 
                AND `fieldid` = '$fieldid'";
        $selected_poz = $database->setQuery($q)->loadResultArray(); 

        HTML_boss::load_poz($poz, $selected_poz);
    }
    
    public static function save_poz($directory, $fieldid){
        $database = database::getInstance();
        $template = mosGetParam($_REQUEST, 'template', '');
        $template_type = mosGetParam($_REQUEST, 'template_type', '');
        $pozitions = mosGetParam($_REQUEST, 'pozitions', array());
        
        if(empty($template) || empty($template_type)){
            return;
        }
        

        
        $q = "DELETE FROM #__boss_" . $directory . "_groupfields 
                WHERE `template` = '$template' AND `type_tmpl` = '$template_type' 
                AND `fieldid` = '$fieldid'";
        $database->setQuery($q)->query(); 
        
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }
        foreach($pozitions as $pozition){
              $q = "INSERT INTO #__boss_" . $directory . "_groupfields " .
                    "(`fieldid`, `groupid`, `template`, `type_tmpl`, `ordering`) " .
                    "VALUES " .
                    "('" . $fieldid . "', '" . $pozition . "', '" . $template . "', '" . $template_type . "', '0')";
            $database->setQuery($q)->query();

            if ($database->getErrorNum()) {
                echo $database->stderr();
                return false;
            }
        }
        echo BOSS_SAVED_FIELD_POZ;
        return true;
    }
    
    
    /** список шаблонов
     * @static
     * @return array
     */
    public static function getTemplates() {
        $templates = array();
        $path = JPATH_BASE . '/templates/com_boss';
        if (!is_dir($path)) {
            echo '<script type="text/javascript">';
            echo 'alert(\'Установите хотя-бы один шаблон, желательно чтобы это был шаблон "default".\')';
            echo '</script>';
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

    /** сохранение позиции шаблона
     * @static
     * @param  $directory
     * @param  $group
     * @return void
     */
    public static function saveGroup($directory, $group) {
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

    /**  список шаблонов
     * @static
     * @param  $directory
     * @return void
     */
    public static function listTemplates($directory, $conf) {
        $templates = self::getTemplates();
        $directories = BossDirectory::getDirectories();
        HTML_boss::listTemplates($templates, $directory, $directories, $conf);
    }

    /** редактирование привязки полей к позициям шаблона
     * @static
     * @param  $directory
     * @param  $template
     * @param  $type_tmpl
     * @return bool
     */
    public static function editTemplate($directory, $template, $type_tmpl, $conf) {
        $database = database::getInstance();
        $groupfieldsArray = array();
        $positions = BossTemplateFields::getTemplateFields($directory, $template, $type_tmpl);
        
        $query = "SELECT fieldid, groupid, ordering " .
                "FROM #__boss_" . $directory . "_groupfields " .
                "WHERE template = '" . $template . "' AND type_tmpl = '$type_tmpl' " .
                "ORDER BY ordering ASC";
        $groupfields = $database->setQuery($query)->loadObjectList();

        foreach($positions as $group){
            $groupfieldsArray[$group->id]['catsid'] = $group->catsid;
            $groupfieldsArray[$group->id]['published'] = $group->published; 
            if (count($groupfields) > 0) {
                foreach ($groupfields as $groupfield) {
                    if($groupfield->groupid == $group->id){
                        $groupfieldsArray[$group->id]['fieldid'][] = $groupfield->fieldid;
                        $groupfieldsArray[$group->id][$groupfield->fieldid]['ordering'] = $groupfield->ordering;                        
                    }
                }
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

        $directories = BossDirectory::getDirectories();

        HTML_boss::editTemplate($directory, $directories, $template, $type_tmpl, $positions, $groupfieldsArray, $fields, $cats, $conf);
        return true;
    }

    /** сохранение шаблона
     * @static
     * @param  $directory
     * @param  $template
     * @param  $type_tmpl
     * @return void
     */
    public static function saveTemplate($directory, $template, $type_tmpl) {
        $database = database::getInstance();
        $task = mosGetParam($_REQUEST, 'task', '');
        
        //удаляем старые строки связи полей с группами.
        $q = "DELETE FROM #__boss_" . $directory . "_groupfields WHERE `template` = '" . $template . "' AND `type_tmpl` = '" . $type_tmpl . "'";
        $database->setQuery($q)->query();
        $groups = array();
        $fields = array();
        //делаем массивы полей и групп из пост
        foreach ($_POST as $key => $val) {
            if (strpos($key, '|') !== false) {
                $key = explode('|', $key);
                $action = $key[0];
                $groupid = $key[1];
                $fieldid = $key[2];
                switch ($action) {
                    case 'required':
                        $ordering = $_POST['ordering|' . $groupid . '|' . $fieldid];

                        $fields[] = array(
                            'fieldid' => $fieldid,
                            'groupid' => $groupid,
                            'ordering' => $ordering
                        );
                        break;
                }
            }
        }

        //записываем связи полей с группами.
        foreach ($fields as $field) {

            $q = "INSERT INTO #__boss_" . $directory . "_groupfields " .
                    "(`fieldid`,               `groupid`,      `template`,      `type_tmpl`,      `ordering`) " .
                    "VALUES " .
                    "('" . $field['fieldid'] . "', '" . $field['groupid'] . "', '" . $template . "', '" . $type_tmpl . "', '" . $field['ordering'] . "')";
            $database->setQuery($q)->query();
        }

        mosCache::cleanCache('com_boss');

        if ($task == 'apply')
            $link = "index2.php?option=com_boss&directory=$directory&act=templates&task=edit_tmpl&template=$template&type_tmpl=$type_tmpl";
        else
            $link = "index2.php?option=com_boss&act=templates&directory=$directory";
        mosRedirect($link);
    }

    /**  удаление шаблона
     * @static
     * @param  $directory
     * @return void
     */
    public static function deleteTemplate($directory) {
        $database = database::getInstance();
        $tid = $_POST['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo sprintf('<script> alert(\'%s\'); window.history.go(-1);</script>' . "\n", BOSS_SELECT_TEMPLATE_TO_BE_DELETED);
            exit();
        }
        foreach ($tid as $template) {
            if ($template != "") {
                boss_helpers::rmdir_rf(JPATH_BASE . "/templates/com_boss/" . $template);
            }

            $database->setQuery("DELETE FROM #__boss_" . $directory . "_groups \nWHERE `template` = '" . $template . "'")->query();
            $database->setQuery("DELETE FROM #__boss_" . $directory . "_groupfields \nWHERE `template` = '" . $template . "'")->query();
        }

        mosCache::cleanCache('com_boss');
        mosRedirect("index2.php?option=com_boss&act=templates&directory=$directory");
    }
}

class BossTemplateFields{
    /** редактирование списка позиций шаблона
     * @static
     * @param  $directory
     * @param  $template
     * @param  $type_tmpl
     * @return void
     */
    public static function listTemplateFields($directory, $template, $conf) {
        $database = database::getInstance();
        $directories = BossDirectory::getDirectories();
        
        $fields = self::getTemplateFields($directory, $template);
              
        $cats = $database->setQuery("SELECT id, name FROM #__boss_" . $directory . "_categories ")->loadObjectList('id');

        HTML_boss::listTemplateFields($directory, $directories, $fields, $cats, $template, $conf);
    }
    
    public static function editTemplateField($directory, $conf) {
        $database = database::getInstance();
        $directories = BossDirectory::getDirectories();
        $id = mosGetParam($_REQUEST, 'fieldid', 0);

        $field = null;
        $database->setQuery("SELECT * FROM #__boss_" . $directory . "_groups WHERE id = '".$id."' LIMIT 1")->loadObject($field);
              
        $cats = jDirectoryCategory::getAllCategories($directory);
        
        $selectedCats = (!empty($field->catsid)) ? explode(',', $field->catsid) : array();

        $templates = BossTemplates::getTemplates();
        HTML_boss::editTemplateField($directory, $directories, $field, $cats, $selectedCats, $templates, $conf);
    }
    
    public static function deleteTemplateFields($directory){
        $database = database::getInstance();
        $template = mosGetParam($_REQUEST, 'template', '');
        $id = mosGetParam($_REQUEST, 'fieldid', 0);

        $q = "DELETE FROM #__boss_" . $directory . "_groups WHERE `id` = '" . $id . "'";
        $database->setQuery($q)->query();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }
        mosRedirect("index2.php?option=com_boss&directory=$directory&act=templates&task=list_tmpl_fields&template=$template");
    }
    
    public static function saveTmplField($directory){
        $database = database::getInstance();
        
        $published = mosGetParam($_REQUEST, 'published', 0);
        $name = mosGetParam($_REQUEST, 'name', '');
        $desc = mosGetParam($_REQUEST, 'desc', '');
        $template = mosGetParam($_REQUEST, 'template', '');
        $type_tmpl = mosGetParam($_REQUEST, 'type_tmpl', '');
        $catsid = mosGetParam($_REQUEST, 'catsid', array());
        $id = mosGetParam($_REQUEST, 'fieldid', 0);
        
        $catsid = (!empty($catsid)) ? ','.implode(',', $catsid).',' : '';

        if($id >0){
            $q = "UPDATE #__boss_" . $directory . "_groups SET " .
                 "`name`='$name', `desc`='$desc', `template`='$template', `type_tmpl`='$type_tmpl', `published`='$published', `catsid`='$catsid' " .
                 "WHERE  `id`= '$id' LIMIT 1";
            $database->setQuery($q)->query();
            if ($database->getErrorNum()) {
                echo $database->stderr();
                return false;
            }
            
        }
        else{
            $q = "INSERT INTO #__boss_" . $directory . "_groups " .
                 "(`name`, `desc`, `template`, `type_tmpl`, `published`, `catsid`) " .
                 "VALUES " .
                 "('$name', '$desc', '$template', '$type_tmpl', '$published', `catsid`) ";
            $database->setQuery($q)->query();
            if ($database->getErrorNum()) {
                echo $database->stderr();
                return false;
            }
        }
        mosRedirect("index2.php?option=com_boss&directory=$directory&act=templates&task=list_tmpl_fields&template=$template");
    }
    
    public static function getTemplateFields($directory, $template, $type_tmpl='') {
        $database = database::getInstance();
        
        $where = ($type_tmpl=='') ? '' : " AND `type_tmpl` = '$type_tmpl'";
        
        $fields = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_groups WHERE template = '".$template."' $where ORDER BY template, type_tmpl, name")->loadObjectList();
        if(count($fields) == 0){
            $positions = array();
            require(JPATH_BASE . "/templates/com_boss/$template/_service.php");
            $q = "INSERT INTO #__boss_" . $directory . "_groups " .
                 "(`name`, `desc`, `template`, `type_tmpl`, `published`, `catsid`) " .
                 "VALUES ";
            $q1 = array();
            foreach($positions['category'] as $key => $val){                     
                $q1[] = "('" . $key . "', '" . $val . "', '" . $template . "', 'category', '1', ',-1,')";
            }
            foreach($positions['content'] as $key => $val){                     
                $q1[] = "('" . $key . "', '" . $val . "', '" . $template . "',  'content', '1', ',-1,')";
            }
            $q = $q . implode(', ', $q1);
            $database->setQuery($q)->query();
            $fields = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_groups WHERE template = '".$template."' $where ORDER BY template, type_tmpl, name")->loadObjectList();
        }
        return $fields;
    }
}
/**
 * Класс работы с плагинами.
 */
class BossPlugins extends mosDBTable {

    var $id = null;
    var $directory = null;
    var $plug_type = null;
    var $plug_name = null;
    var $title = null;
    var $value = null;

    function __construct() {
        $this->mosDBTable('#__boss_plug_config', 'id');
    }

    /**
     * @static
     * @param  $directory - каталог
     * @param string $plug_type - тип плагина
     * @param string $plug_name - название плагина
     * @return array|bool - возвращает массив настроек плагинов
     */
    public static function getPluginsSettings($directory, $plug_type='', $plug_name='') {
        $database = database::getInstance();

        $wheres = array();
        $wheres[] = '`directory` = ' . $directory;
        (!empty($plug_type)) ? $wheres[] = '`plug_type` = ' . $plug_type : null;
        (!empty($plug_name)) ? $wheres[] = '`plug_name` = ' . $plug_name : null;

        $values = $database->setQuery("SELECT * FROM #__boss_plug_config WHERE " . implode(' AND ', $wheres) . " ORDER by `directory`, `plug_type`, `plug_name`")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }

        $return = array();
        if (!empty($values)) {
            foreach ($values as $value) {
                $return[$value->directory][$value->plug_type][$value->plug_name][$value->title] = $value->value;
            }
        }

        return $return;
    }

    /**
     * @param  $directory
     * @return void
     */
    public function save($directory) {

        // bind it to the table
        if (!$this->bind($_POST)) {
            echo "<script> alert('" . $this->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        // store it in the db
        if (!$this->store()) {
            echo "<script> alert('" . $this->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }
    }

    /**
     * @static
     * @param  $directory - каталог
     * @param string $folder - тип/папка плагинов
     * @return array - возвращает объектлист плагинов заданного типа.
     */
    public static function get_plugins($directory, $folder='fields') {
        $bossPlugins = array();
        $path = JPATH_BASE . DS. 'images' . DS . 'boss' . DS . $directory . DS . 'plugins' . DS . $folder . DS;
        $handle = opendir($path);
        while ($file = readdir($handle)) {
            if (($file != ".") && ($file != "..")) {
                if(is_file($path . $file . DS . 'plugin.php')){
                    if(!class_exists ( $file )){
                        include_once($path . $file . DS . 'plugin.php');
                    }
                    $bossPlugins[$file] = new $file($directory);
                }
            }
        }
        closedir($handle);
        return $bossPlugins;
    }

    /**
     * @static
     * @param  $directory - каталог
     * @param  $name - название класса плагина
     * @param string $folder - тип/папка плагина
     * @return - возвращает объект класса плагина.
     */
    public static function get_plugin($directory, $name, $folder='fields', $params=array()) {
        $bossPlugins = array();
        $path = JPATH_BASE . DS. 'images' . DS. 'boss' . DS. $directory . DS. 'plugins' . DS. $folder . DS. $name . DS;
        require_once($path . 'plugin.php');
        $bossPlugin = new $name($directory, $params);
        return $bossPlugin;
    }

    /** запускает функцию из плагина
     * @param  $directory
     * @return bool
     */
    public static function run_plugins_func($directory) {
        $class = mosGetParam($_REQUEST, 'class', '');
        $function = mosGetParam($_REQUEST, 'function', '');
        $path = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory . DS . 'plugins' . DS . 'fields' . DS . $class . '/plugin.php';
        if (is_file($path)) {
            require_once($path);
        } else {
            return false;
        }
        $class = new $class;
        $class->$function();
        return true;
    }

    /**  удаление плагина
     * @static
     * @param  $directory
     * @return void
     */
    public static function deletePlugin($directory) {
        $database = database::getInstance();

        $tid = $_POST['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo sprintf('<script> alert(\'%s\'); window.history.go(-1);</script>' . "\n", BOSS_SELECT_PLUGIN_TO_BE_DELETED);
            exit();
        }
        foreach ($tid as $pluginname) {
            $bossPlugins = array();
            $path = JPATH_BASE . "/images/boss/$directory/plugins/$pluginname";

            require_once($path . '/plugin.php');
            foreach ($bossPlugins as $plug) {
                $plug->uninstall($directory);
            }
            boss_helpers::rmdir_rf($path);
        }

        mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory");
    }

    /** установка плагина
     * @static
     * @param  $directory
     * @return bool
     */
    public static function installPlugin($directory) {

        // Check that the zlib is available
        if (!extension_loaded('zlib')) {
            mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory", BOSS_ZLIB_NOT_FOUND);
        }

        $userfile = mosGetParam($_FILES, 'userfile', '');
        if (empty($userfile['name'])) {
            mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory", BOSS_EMPTY_FILENAME);
        }

        $directories = mosGetParam($_REQUEST, 'directories', array());
        if (count($directories) == 0) {
            mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory", BOSS_EMPTY_DIRS);
        }

        $bossPlugins = array();
        $fileProps = explode('.', $userfile['name']);
        $folder = $fileProps[0];
        $name = $fileProps[1];
        $ext = $fileProps[2];
        if ($ext == 'zip') {
            // Extract functions
            require_once(JPATH_BASE . '/administrator/includes/pcl/pclzip.lib.php');
            require_once(JPATH_BASE . '/administrator/includes/pcl/pclerror.lib.php');
            $zipfile = new PclZip($userfile['tmp_name']);
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                define('OS_WINDOWS', 1);
            } else {
                define('OS_WINDOWS', 0);
            }
        } else {
            require_once(JPATH_BASE . '/includes/Archive/Tar.php');
            $archive = new Archive_Tar($userfile['tmp_name']);
            $archive->setErrorHandling(PEAR_ERROR_PRINT);
        }

        foreach ($directories as $dir) {
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
            require_once(JPATH_BASE . "/images/boss/$dir/plugins/$folder/$name/plugin.php");

            foreach ($bossPlugins as $plug) {
                $plug->install($dir);
            }
        }
        mosRedirect("index2.php?option=com_boss&act=plugins&directory=$directory");
        return true;
    }

    /** редактирование настроек плагина
     * @static
     * @param  $directory
     * @return void
     */
    public static function editPlugin($directory, $conf) {
        $folder = mosGetParam($_REQUEST, 'folder', null);
        $plugin = mosGetParam($_REQUEST, 'plugin', null);
        $directories = BossDirectory::getDirectories();
        $bossPlugin = BossPlugins::get_plugin($directory, $plugin, $folder);
        HTML_boss::editPlugin($directory, $directories, $bossPlugin, $conf);
    }
    
    public static function savePlugin($directory) {
        $folder = mosGetParam($_REQUEST, 'folder', null);
        $plugin = mosGetParam($_REQUEST, 'plugin', null);

        $bossPlugin = BossPlugins::get_plugin($directory, $plugin, $folder);
        if (method_exists($bossPlugin, 'saveOptions')) {
            $bossPlugin->saveOptions($directory);
        }
    }

    /**  список плагинов
     * @static
     * @param  $directory
     * @return void
     */
    public static function listPlugins($directory, $conf) {
        $database = database::getInstance();
        //значение селекта использования
        $used = mosGetParam($_REQUEST, 'used', '');
        $plugins = array();
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
                        require_once($new_path . '/' . $file . '/plugin.php');
                        $plugins[$i]['type'] = $file;

                        if ($used === '0' && in_array($plugins[$i]['type'], $usedPlugins)) {
                            unset($plugins[$i]);
                        } elseif ($used === '1' && !in_array($plugins[$i]['type'], $usedPlugins)) {
                            unset($plugins[$i]);
                        }
                        $i++;
                    }
                }
                closedir($subdir);
            }
        }
        closedir($handle);

        $directories = BossDirectory::getDirectories();

        HTML_boss::listPlugins($directory, $directories, $plugins, $used, $conf);
    }

}

/**
 * 
 */
class BossMain {

    /**
     * @static
     * @param  $directory
     * @return void
     */
    public static function displayMain($directory, $conf) {
        $directories = BossDirectory::getDirectories();
        HTML_boss::displayMain($directory, $directories, $conf);
    }

}

class Sliders {

    function __construct() {
        /* запрет повторного включения css и js файлов в документ */
        if (!defined('_SLIDER_LOADED')) {
            define('_SLIDER_LOADED', 1);
        }
    }

    /**
     * creates a tab pane and creates JS obj
     * @param string The Tab Pane Name
     */
    function startPane($paneid) {
        echo '<div class="slider-page" id="' . $paneid . '">';
    }

    /**
     * Ends Tab Pane
     */
    function endPane() {
        echo '</div>';
    }

    /*
     * Creates a tab with title text and starts that tabs page
     * @param tabText - This is what is displayed on the tab
     * @param paneid - This is the parent pane to build this tab on
     */

    function startTab($tabText, $paneid) {
        echo "<div class=\"jwts_title\">";
        echo "<div class=\"jwts_title_left\">";
        echo "<a href=\"javascript:void(null);\" onclick=\"showHide('$paneid')\" title=\"Click to open!\" class=\"jwts_title_text\">" . $tabText . "</a>";
        echo "</div>";
        echo "</div>";
        echo "<div class=\"jwts_slidewrapper\" id=\"$paneid\" style='display: none;'><div>";
    }

    /*
     * Ends a tab page
     */

    function endTab() {
        echo '</div></div>';
    }

}

class bossFieldImages {

    /** удаление катринки
     * @param  $directory
     * @return void
     */
    public static function deleteFieldImage($directory) {

        $tid = $_POST['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo '<script> alert(\'Select an category to delete\'); window.history.go(-1);</script>' . "\n";
            exit();
        }
        foreach ($tid as $filename) {
            if ($filename != "") {
                @unlink(JPATH_BASE . "/images/boss/$directory/fields/" . $filename);
            }
        }
        mosRedirect("index2.php?option=com_boss&act=fieldimage&directory=$directory");
    }

    /**  загрузка картинки
     * @static
     * @param  $directory
     * @return void
     */
    public static function uploadFieldImage($directory) {

        $userfile = mosGetParam($_FILES, 'userfile', null);
        $filename = russian_transliterate($userfile['name']);
        while (file_exists(JPATH_BASE . "/images/boss/$directory/fields/" . $filename)) {
            $filename = "copy_" . $filename;
        }
        is_file($userfile['tmp_name']) ? move_uploaded_file($userfile['tmp_name'], JPATH_BASE . "/images/boss/$directory/fields/" . $filename) : null;
        
        mosRedirect("index2.php?option=com_boss&act=fieldimage&directory=$directory");
    }

    /** список картинок
     * @static
     * @param  $directory
     * @return void
     */
    public static function listFieldImages($directory, $conf) {
        $fieldimages = self::getFieldImages($directory);
        $directories = BossDirectory::getDirectories();
        HTML_boss::listFieldImages($fieldimages, $directory, $directories, $conf);
    }
    
     /** список картинок
     * @static
     * @param  $directory
     * @return void
     */
    public static function getFieldImages($directory) {

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

        return $fieldimages;
    }

}

class bossExportImport {

    /** Форма импорта-экспорта
     * @static
     * @param  $directory
     * @return void
     */
    public static function showImpExpForm($directory, $conf) {

        $directories = BossDirectory::getDirectories();
        $packs = array();

        if ($handle = opendir(JPATH_BASE . '/images/boss/' . $directory)) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file, -4) == '.zip')
                    $packs[] = $file;
            }
            closedir($handle);
        }

        HTML_boss::showImpExpForm($directory, $directories, $packs, $conf);
    }

    /** экспорт каталога
     * @static
     * @param  $directory
     * @return void
     */
    public static function exportDirectory($directory) {

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
            $result = $database->setQuery('SELECT * FROM #__boss_config WHERE id = ' . $directory, 0, 1)->loadAssocRow();

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
        boss_helpers::rmdir_rf($patch);
        mosRedirect("index2.php?option=com_boss&act=export_import&directory=" . $directory);
    }

    /** импорт каталога
     * @static
     * @return bool
     */
    public static function importDirectory() {

        $database = database::getInstance();
        $pack = mosGetParam($_FILES, 'pack', '');
        $directory = mosGetParam($_REQUEST, 'new_directory', 0);

        if ($pack['name'] == '') {
            mosRedirect("index2.php?option=com_boss&act=export_import", BOSS_IM_SELECT_ARCHIV);
            return false;
        }

        // Extract functions
        require_once(JPATH_BASE . '/administrator/includes/pcl/pclzip.lib.php');
        require_once(JPATH_BASE . '/administrator/includes/pcl/pclerror.lib.php');
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
        if ($directory == 0) {
            $directory = installNewDirectory(0);
            $directory = (int) $directory['id'];
        } else {
            //проверим существование каталога
            $q = "SELECT `id` FROM #__boss_config WHERE `id` = '$directory'";
            $result = $database->setQuery($q)->loadObjectList();
            if (count($result) == 0) {
                $directory = installNewDirectory(0);
                $directory = (int) $directory['id'];
            }
        }
        //путь до копируемых файлов
        $pathFrom = JPATH_BASE . "/images/boss/tmp";
        //путь куда копировать
        $pathTo = JPATH_BASE . "/images/boss/$directory";
        //копируем файлы контента
        is_dir($pathFrom . '/content') ? boss_helpers::copy_folder_rf($pathFrom . '/content', $pathTo) : null;
        //копируем файлы плагинов
        is_dir($pathFrom . '/plugins') ? boss_helpers::copy_folder_rf($pathFrom . '/plugins', $pathTo . '/plugins') : null;
        //копируем файлы шаблонов
        is_dir($pathFrom . '/templates') ? boss_helpers::copy_folder_rf($pathFrom . '/templates', JPATH_BASE . "/templates/com_boss") : null;
        //включаем файл с запросами, делаем запросы в БД
        if (is_file($pathFrom . '/table.sql.php')) {
            require $pathFrom . '/table.sql.php';
        }

        //удаляем временные файлы
        boss_helpers::rmdir_rf($pathFrom);

        //проверка существования плагинов, в случае отсутствия копируем из резерва или создаем
        $pathToReserve = JPATH_BASE . '/components/com_boss';
        !is_dir($pathTo . '/plugins/fields') ? boss_helpers::copy_folder_rf($pathToReserve . '/plugins', $pathTo . '/plugins') : null;


        //редиректим на настройки
        mosRedirect("index2.php?option=com_boss&act=configuration&directory=" . $directory);
        return true;
    }

    /** импорт содержимого из джустины
     * @static
     * @param  $directory
     * @return bool
     */
    public static function importJoostina($directory) {

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
        return true;
    }

}

class BossUsers {

    /**  список пользователей
     * @static
     * @param  $directory
     * @return bool
     */
    public static function listUsers($directory, $conf) {
        global $mosConfig_list_limit;
        $database = database::getInstance();

        $q = "SELECT count(*) FROM #__boss_" . $directory . "_profile";

        $total = $database->setQuery($q)->loadResult();

        $mainframe = mosMainFrame::getInstance();
        $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
        $limitstart = intval($mainframe->getUserStateFromRequest("view{com_boss}limitstart", 'limitstart', 0));

        require_once(JPATH_BASE . '/administrator/includes/pageNavigation.php');
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

        $directories = BossDirectory::getDirectories();

        HTML_boss::listUsers($directory, $directories, $pageNav, $users, $conf);
        return true;
    }

    /** редактирование информации пользователя
     * @static
     * @param  $directory
     * @return bool
     */
    public static function editUserInfo($directory, $conf) {
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

        $directories = BossDirectory::getDirectories();
        HTML_boss::editUserInfo($directory, $directories, $userFields, $fields, $users, $selectedUserId, $conf);
        return true;
    }

    /** сохранение информации пользователя
     * @static
     * @param  $directory
     * @return bool
     */
    public static function saveUserInfo($directory) {
        $database = database::getInstance();
        $userid = mosGetParam($_REQUEST, 'userid', 0);
        $task = mosGetParam($_REQUEST, 'task', '');

        if ($userid == 0)
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
        foreach ($tableFields as $key => $val) {
            $fields[] = "`" . $key . "`";
            $values[] = "'" . $database->getEscaped(mosGetParam($_REQUEST, $key, '')) . "'";
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
        return true;
    }

    /** удаление информации пользователя
     * @static
     * @param  $directory
     * @return bool
     */
    public static function deleteUserInfo($directory) {
        $database = database::getInstance();
        $userids = mosGetParam($_REQUEST, 'tid', array());

        if (count($userids) == 0)
            return false;

        foreach ($userids as $userid) {
            $q = "DELETE FROM #__boss_" . $directory . "_profile WHERE userid = $userid ";
            $database->setQuery($q)->query();
            if ($database->getErrorNum()) {
                echo $database->stderr();
                return false;
            }
        }
        mosRedirect("index2.php?option=com_boss&act=users&directory=$directory", BOSS_FORM_USER_DELETED);
        return true;
    }

}

/**
 * Класс типов контента
 */
class BossContentTypes extends mosDBTable {

    var $id = null;
    var $name = null;
    var $desc = null;
    var $fields = null;
    var $published = null;
    var $ordering = null;

    function __construct(&$db, $directory) {
        $this->mosDBTable('#__boss_' . $directory . '_content_types', 'id', $db);
    }

    public static function getAllContentTypes($directory) {
        $database = database::getInstance();
        $rows = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_content_types ORDER BY ordering")->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }  
        return $rows;
    }

    /** сохранение порядка сортировки
     * @static
     * @param  $tid
     * @param  $directory
     * @return void
     */
    public static function saveOrder(&$tid, $directory) {
        $database = database::getInstance();

        $total = count($tid);
        $order = mosGetParam($_POST, 'order', array(0));
        $row = new BossContentTypes($database, $directory);

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

        mosRedirect("index2.php?option=com_boss&act=content_types&directory=$directory", BOSS_CATEGORIES_REORDER);
    }

    /** перемещение категории в списке при сортировке
     * @static
     * @param  $uid
     * @param  $inc
     * @param  $directory
     * @return void
     */
    public static function orderContentTypes($uid, $inc, $directory) {
        $database = database::getInstance();

        $row = new BossContentTypes($database, $directory);
        $row->load($uid);
        $row->move($inc);
        
        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        mosRedirect("index2.php?option=com_boss&act=content_types&directory=$directory");
    }

    /** форма создания категории
     * @static
     * @param  $directory
     * @return void
     */
    public static function newContentTypes($directory, $conf) {
        $database = database::getInstance();
        $row = new BossContentTypes($database, $directory);
        $directories = BossDirectory::getDirectories();
        HTML_boss::displayContentTypes($row, $directory, $directories, $conf);
    }

    /** удаление категории
     * @static
     * @param  $directory
     * @return void
     */
    public static function deleteContentTypes($directory) {

        $tid = $_POST['tid'];
        if (!is_array($tid) || count($tid) < 1) {
            echo '<script> alert(\'Select an category to delete\'); window.history.go(-1);</script>' . "\n";
            exit();
        }

        $database = database::getInstance();

        if (count($tid)) {
            $ids = implode(',', $tid);
            $database->setQuery("DELETE FROM #__boss_" . $directory . "_content_types \nWHERE id IN ($ids)");
        }
        if (!$database->query()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        }

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        mosRedirect("index2.php?option=com_boss&act=content_types&directory=$directory", BOSS_CATEGORIES_DELETED);
    }

    /**
     * @static список категорий
     * @param  $directory
     * @return bool
     */
    public static function listContentTypes($directory, $conf) {

        $database = database::getInstance();
        $mainframe = mosMainFrame::getInstance();
        global $mosConfig_list_limit;

        $src_cat = mosGetParam($_REQUEST, 'src_ctype', '');
        $select_publish = mosGetParam($_REQUEST, 'select_publish', 0);

        $wheres = array();

        if ($src_cat) {
            $wheres[] = "c.name LIKE '%$src_cat%'";
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
        $where = (count($wheres) > 0) ? "WHERE " . implode(' AND ', $wheres) . " " : '';
        $q = "SELECT * FROM #__boss_" . $directory . "_content_types ";
        $q .= $where;
        $q .= " ORDER BY `ordering`";
        
        $types = $database->setQuery($q)->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }
        
        $total = count($types);
        $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
        $limitstart = intval($mainframe->getUserStateFromRequest("view{com_boss}limitstart", 'limitstart', 0));
        require_once(JPATH_BASE . '/administrator/includes/pageNavigation.php');
        $pageNav = new mosPageNav($total, $limitstart, $limit);


        $directories = BossDirectory::getDirectories();

        HTML_boss::listContentTypes($types, $pageNav, $directory, $directories, count($types), $conf);
        return true;
    }

    /**
     * @static публикация категорий
     * @param  $directory
     * @return
     */
    public static function publishContentTypes($directory) {

        $tid = $_GET['tid'];

        if (!is_array($tid) || count($tid) < 1) {
            echo '<script> alert(\'Select an Content to publish\'); window.history.go(-1);</script>' . "\n";
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
            $database->setQuery("UPDATE #__boss_" . $directory . "_content_types SET `published` = '$publish' WHERE `id` IN ($ids) ");
        }
        if (!$database->query()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        }

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        mosRedirect("index2.php?option=com_boss&act=categories&directory=$directory");
    }

    /** форма редактирования категории
     * @static
     * @param  $directory
     * @return bool
     */
    public static function displayContentTypes($directory, $conf) {

        $id = mosGetParam($_REQUEST, 'tid', array(0));
        if (is_array($id)) {
            $id = $id[0];
        }

        $directories = BossDirectory::getDirectories();
        $database = database::getInstance();

        if (!isset($id)) {
            mosRedirect("index2.php?option=com_boss&act=contest&directory=$directory", BOSS_ERROR_IN_URL);
        }

        $rows = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_content_types WHERE id=" . $id)->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return false;
        }

        HTML_boss::displayContentTypes(@$rows[0], $directory, $directories, $conf);
        return true;
    }

    /** сохранение категории
     * @static
     * @param  $directory
     * @return
     */
    public static function saveContentTypes($directory) {

        $database = database::getInstance();
        $task = mosGetParam($_REQUEST, 'task');
        $row = new BossContentTypes($database, $directory);
        // bind it to the table
        if (!$row->bind($_POST)) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }
        if((int)$row->ordering < 1){
            $row->ordering = $database->setQuery("SELECT MAX(`ordering`) FROM #__boss_" . $directory . "_content_types")->loadResult()+1;           
        }

        // store it in the db
        if (!$row->store()) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        // clean any existing cache files
        mosCache::cleanCache('com_boss');

        if ($task == 'apply')
            $link = "index2.php?option=com_boss&directory=$directory&act=content_types&task=edit&tid[]=$row->id";
        else
            $link = "index2.php?option=com_boss&act=content_types&directory=$directory";
        mosRedirect($link, BOSS_CONTENT_TYPE_SAVED);
    }
}