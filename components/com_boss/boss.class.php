<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();


require_once(JPATH_BASE . '/components/com_boss/boss.tools.php');

class jDirectoryConf extends mosDBTable {
    var $id = null;
    var $name = null;
    var $slug = null;
    var $meta_title = null;
    var $meta_desc = null;
    var $meta_keys = null;
    var $default_order_by = null;
    var $comment_sys = null;
    var $allow_unregisered_comment = null;
    var $contents_per_page = null;
    var $max_image_size = null;
    var $max_width = null;
    var $max_height = null;
    var $max_width_t = null;
    var $max_height_t = null;
    var $send_email_on_new = null;
    var $send_email_on_update = null;
    var $auto_publish = null;
    var $tag = null;
    var $fronttext = null;
    var $nb_images = null;
    var $show_contact = null;
    var $root_allowed = null;
    var $email_display = null;
    var $rules_text = null;
    var $display_expand = null;
    var $display_fullname = null;
    var $expiration = null;
    var $content_duration = null;
    var $recall = null;
    var $recall_time = null;
    var $recall_text = null;
    var $empty_cat = null;
    var $image_display = null;
    var $cat_max_width = null;
    var $cat_max_height = null;
    var $cat_max_width_t = null;
    var $cat_max_height_t = null;
    var $submission_type = null;
    var $nb_contents_by_user = null;
    var $allow_attachement = null;
    var $allow_contact_by_pms = null;
    var $show_rss = null;
    var $allow_comments = null;
    var $allow_ratings = null;
    var $secure_comment = null;
    var $secure_new_content = null;
    var $use_content_mambot = null;
    var $template = null;
    var $last_cron_date = null;
    var $filter = null;

    function __construct(&$db) {
        $this->mosDBTable('#__boss_config', 'id', $db);
    }
}

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

    function __construct(&$db, $directory) {
        $this->mosDBTable('#__boss_' . $directory . '_categories', 'id', $db);
    }
}

class jDirectoryVote extends mosDBTable {
    var $id = null;
    var $contentid = null;
    var $userid = null;
    var $note = null;
    var $ip = null;
    var $date = null;

    function __construct(&$db, $directory) {
        $this->mosDBTable('#__boss_' . $directory . '_rating', 'id', $db);
    }
}

class jDirectoryReview extends mosDBTable {
    var $id = null;
    var $contentid = null;
    var $userid = null;
    var $title = null;
    var $description = null;
    var $published = null;

    function __construct(&$db, $directory) {
        $this->mosDBTable('#__boss_' . $directory . '_reviews', 'id', $db);
    }
}

class jDirectoryContent extends mosDBTable {
    var $id = null;
    var $name;
    var $slug;
    var $meta_title = null;
    var $meta_desc = null;
    var $meta_keys = null;
    var $userid = null;
    var $published = null;
    var $date_created = null;
    var $date_publish = null;
    var $date_unpublish = null;

    function __construct(&$db, $directory) {
        $this->mosDBTable('#__boss_' . $directory . '_contents', 'id', $db);
    }

    function save($directory, $fields, $conf, $isUpdateMode = 0, $itemid = 0) {
        global $mainframe;
        $database = database::getInstance();
        $category = mosGetParam($_REQUEST, 'category', array());
        $tags = mosGetParam($_REQUEST, 'tags', '');
        $act = mosGetParam($_REQUEST, 'act', '');
        $task = mosGetParam($_REQUEST, 'task', '');

        $plugins = get_plugins($directory, 'fields');

        // bind it to the table
        if (!$this->bind($_POST)) {
            echo "<script> alert('" . $this->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }
        if (($this->id == "") || ($this->id == 0)) {
            $isUpdateMode = 0;
            $this->date_created = date('Y-m-d H:i:s');

            if(empty($this->date_publish) && $this->published == 1) {
               $this->date_publish = $this->date_created;
            }
        }
        else {
            $isUpdateMode = 1;
            $this->date_publish = (intval($this->date_publish)>0)   ? mosFormatDate($this->date_publish,  '%Y-%m-%d %H:%M:%S',-$mainframe->getCfg('offset')):'';
        }


        if ($isUpdateMode == 0  && $mainframe->isAdmin()!=1) {
            if ($conf->auto_publish == 2 || $conf->auto_publish == 1) {
                $this->published = 1;
                $redirect_text = BOSS_INSERT_SUCCESSFULL_PUBLISH;
            }
            else {
                $this->published = 0;
                $redirect_text = BOSS_INSERT_SUCCESSFULL_CONFIRM;
            }
        }
        else
            $redirect_text = BOSS_UPDATE_SUCCESSFULL;

        $this->date_unpublish = (intval($this->date_unpublish)>0) ? mosFormatDate($this->date_unpublish,'%Y-%m-%d %H:%M:%S',-$mainframe->getCfg('offset')):'';

        if($this->slug == '')
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
        else $content_id = $database->insertid();
        //вписываем новые категории
        if (!empty($category)) {
            $cat_arr = array();
            if (is_array($category)) {
                foreach ($category as $cat) {
                    $cat_arr[] = "($cat, $content_id)";
                }
            }
            else $cat_arr[] = "($category, $content_id)";

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
                }
                else {
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

        $nbImages = $conf->nb_images;

        for ($i = 1; $i < $nbImages + 1; $i++) {
            $ext_name = chr(ord('a') + $i - 1);
            $cb_image = mosGetParam($_POST, "cb_image$i", "");
            // image1 delete
            if ($cb_image == "delete") {
                $pict = JPATH_BASE . "/images/boss/$directory/contents/" . $this->id . $ext_name . "_t.jpg";
                if (file_exists($pict)) {
                    unlink($pict);
                }
                $pic = JPATH_BASE . "/images/boss/$directory/contents/" . $this->id . $ext_name . ".jpg";
                if (file_exists($pic)) {
                    unlink($pic);
                }
            }

            if (isset($_FILES["content_picture$i"])) {
                if ($_FILES["content_picture$i"]['size'] > $conf->max_image_size) {
                    mosRedirect(sefRelToAbs("index.php?option=com_boss&amp;act=contents&amp;catid=" . $category . "&amp;directory=$directory&amp;Itemid=" . $itemid), BOSS_IMAGETOOBIG);
                    return;
                }
            }

            // image1 upload
            if (isset($_FILES["content_picture$i"]) and !$_FILES["content_picture$i"]['error']) {
                createImageAndThumb(
                        $_FILES["content_picture$i"]['tmp_name'],
                        $_FILES["content_picture$i"]['name'],
                        JPATH_BASE . "/images/boss/$directory/contents/",
                        $this->id . $ext_name . ".jpg",
                        $this->id . $ext_name . "_t.jpg",
                        $conf->max_width,
                        $conf->max_height,
                        $conf->max_width_t,
                        $conf->max_height_t,
                        $conf->tag                        
                        );
            }
        }
        // clean any existing cache files
	    mosCache::cleanCache( 'com_boss' );

        if ($act != '') {

            if($task == 'apply')
                $url = "index2.php?option=com_boss&act=contents&task=edit&&directory=$directory&tid[]=$this->id";
            else
                $url = "index2.php?option=com_boss&act=contents&directory=" . $directory;
        }
        else
            $url = sefRelToAbs("index.php?option=com_boss&task=show_content&contentid=" . $this->id . "&catid=" . $category . "&directory=" . $directory . "&Itemid=" . $itemid);
        mosRedirect($url, $redirect_text);
    }

    function delete($directory, $conf) {

        $database = database::getInstance();
        $plugins = get_plugins($directory, 'fields');

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
        foreach ($plugins as $plugin)
        {
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

    function getFieldValue($field, $content, $field_values, $directory, $itemid, $conf) {
        global $task;
        $return = null;
        $return->value = null;
        $return->title = null;
        $plugins = get_plugins($directory, 'fields');
        $fieldName = $field->name;
        if ($task == "show_content")
            $mode = 1;
        else
            $mode = 2;

        if (($field->display_title & $mode) == $mode) {
            $return->title = jdGetLangDefinition($field->title) . ": ";
        }

        if(isset($content->$fieldName))
            $value = $content->$fieldName;
        else
            $value = "";

        if ($value != "") {
            $value = jdGetLangDefinition($value);

            if (isset($plugins[$field->type])) {
                if ($mode == 1)
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
        global $mosConfig_live_site;
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

    /**
     * Constructor
     * @param database A database connector object
     */
    function __construct(&$db, $directory) {

        $this->mosDBTable('#__boss_' . $directory . '_fields', 'fieldid', $db);

    } //end func
} //end class

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

    } //end func
} //end class