<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 *
 * Backend controller
 *
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage    Admin
 * @filename admin.joieditor.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 *
 **/

defined('_VALID_MOS') or die();
require_once JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'language' . DS . 'russian.php';

switch ($task) {

    case 'config_elrte':
        config_elrte();
        break;

    case 'config_elfinder':
        config_elfinder();
        break;

    case 'save_config_elfinder':
        save_config_elfinder();
        break;

    case 'save_config_elrte':
        save_config_elrte();
        break;

    case 'info':
        info();
        break;

    case 'connector':
        connector();
        break;

    default:
        elfinder();
        break;
}

function elfinder()
{
    include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'view' . DS . 'elfinder.php');
}


function config_elrte()
{
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    if ($my->gid < 25)
            mosRedirect('index2.php?option=com_elrte', _ELRTE_NO_SUPERADMIN_REDIRECT);
    $database = database::getInstance();
    $q = "SELECT `group_id`, `name` FROM `#__core_acl_aro_groups` WHERE `lft` > 2";
    $groups = $database->setQuery($q)->loadObjectList();

    $panels_array = array(
        'copypaste' => _ELRTE_COPYPASTE,
        'undoredo' => _ELRTE_UNDOREDO,
        'style' => _ELRTE_STYLE,
        'colors' => _ELRTE_COLORS,
        'alignment' => _ELRTE_ALIGNMENT,
        'indent' => _ELRTE_INDENT,
        'format' => _ELRTE_FORMAT,
        'lists' => _ELRTE_LISTS,
        'elements' => _ELRTE_ELEMENTS,
        'direction' => _ELRTE_DIRECTION,
        'links' => _ELRTE_LINKS,
        'images' => _ELRTE_IMAGES,
        'media' => _ELRTE_MEDIA,
        'tables' => _ELRTE_TABLES,
        'elfinder' => _ELRTE_ELFINDER,
        'fullscreen' => _ELRTE_FULLSCREEN
    );
    $toolbars = array(
        'tiny' => _ELRTE_TOOLBAR_TINY,
        'compact' => _ELRTE_TOOLBAR_COMPACT,
        'normal' => _ELRTE_TOOLBAR_NORMAL,
        'complete' => _ELRTE_TOOLBAR_COMPLETE,
        'maxi' => _ELRTE_TOOLBAR_MAXI,
    );
    $toolbar_objectList = array();
    $i=0;
    foreach ($toolbars as $key=>$val){
       $toolbar_objectList[$i]->key  = $key;
       $toolbar_objectList[$i]->text = $val;
       $i++;
    }
    $percent = 100 / (count($groups) + 1);

    include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'config_elrte.php');

    $cssfiles = (is_array(@$cssfiles) && count(@$cssfiles) > 0) ? implode("\n", @$cssfiles) : '';

    include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'view' . DS . 'config_elrte.php');
}

function config_elfinder()
{
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    if ($my->gid < 25)
            mosRedirect('index2.php?option=com_elrte', _ELRTE_NO_SUPERADMIN_REDIRECT);
    $database = database::getInstance();
    $q = "SELECT `group_id`, `name` FROM `#__core_acl_aro_groups` WHERE `lft` > 2";
    $groups = $database->setQuery($q)->loadObjectList();

    $commands = array(
        'open'      => _ELRTE_ADMIN_IM_COMAND_OPEN,
        'mkdir'     => _ELRTE_ADMIN_IM_COMAND_MKDIR,
        'mkfile'    => _ELRTE_ADMIN_IM_COMAND_MKFILE,
        'rename'    => _ELRTE_ADMIN_IM_COMAND_RENAME,
        'upload'    => _ELRTE_ADMIN_IM_COMAND_UPLOAD,
        'ping'      => _ELRTE_ADMIN_IM_COMAND_PING,
        'paste'     => _ELRTE_ADMIN_IM_COMAND_PASTE,
        'rm'        => _ELRTE_ADMIN_IM_COMAND_RM,
        'duplicate' => _ELRTE_ADMIN_IM_COMAND_DUPLICATE,
        'read'      => _ELRTE_ADMIN_IM_COMAND_READ,
        'edit'      => _ELRTE_ADMIN_IM_COMAND_EDIT,
        'extract'   => _ELRTE_ADMIN_IM_COMAND_EXTRACT,
        'archive'   => _ELRTE_ADMIN_IM_COMAND_ARCHIVE,
        'tmb'       => _ELRTE_ADMIN_IM_COMAND_TMB,
        'resize'    => _ELRTE_ADMIN_IM_COMAND_RESIZE
    );
    $mimetypes = array(
        'all'                           => _ELRTE_ADMIN_IM_MIME_ALL,
        'audio'                         => _ELRTE_ADMIN_IM_MIME_AUDIO,
        'image'                         => _ELRTE_ADMIN_IM_MIME_IMAGE,
        'text'                          => _ELRTE_ADMIN_IM_MIME_TEXT,
        'video'                         => _ELRTE_ADMIN_IM_MIME_VIDEO,
        'application/pdf'               => _ELRTE_ADMIN_IM_MIME_PDF,
        'application/xml'               => _ELRTE_ADMIN_IM_MIME_XML,
        'application/x-shockwave-flash' => _ELRTE_ADMIN_IM_MIME_FLASH,
        'application/zip'               => _ELRTE_ADMIN_IM_MIME_ZIP,
        'application/x-rar-compressed'  => _ELRTE_ADMIN_IM_MIME_RAR,
        'application/x-tar'             => _ELRTE_ADMIN_IM_MIME_TAR
    );

    $img_libraries = array(
        'auto' => 'auto',
        'gd' => 'gd',
        'mogrify' => 'mogrify',
        'imagick' => 'imagick',
    );
    $img_lib_obList = array();
    $i=0;
    foreach ($img_libraries as $key=>$val){
       $img_lib_obList[$i]->key  = $key;
       $img_lib_obList[$i]->text = $val;
       $i++;
    }

    $views = array(
        'icons' => 'icons',
        'list' => 'list',
    );
    $view_obList = array();
    $i=0;
    foreach ($views as $key=>$val){
       $view_obList[$i]->key  = $key;
       $view_obList[$i]->text = $val;
       $i++;
    }

    $percent = 80 / (count($groups));

    include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'config_elfinder.php');

    $file_mode = (isset($file_mode) && !empty($file_mode)) ? $file_mode : '644';
    $dir_mode = (isset($dir_mode) && !empty($dir_mode)) ? $dir_mode : '755';

    include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'view' . DS . 'config_elfinder.php');
}

function save_config_elrte()
{   //конфиг редактора
    $input = '';

    $toolbar_metod = mosGetParam($_REQUEST, 'toolbar_metod', 0);
    $input .= '$toolbar_metod = \'' . $toolbar_metod . "';\n";

    $select_toolbar = mosGetParam($_REQUEST, 'select_toolbar', 'tiny');
    $input .= '$select_toolbar = \'' . $select_toolbar . "';\n";

    //подготавливаем панели инсрументов для групп пользователей
    $permissions = mosGetParam($_REQUEST, 'permissions', array());
    $panels = array();
    foreach($permissions as $permission){
       $perms = (explode('_', $permission));
       $panel = $perms[0];
       $groupId = $perms[1];
       $panels[$groupId][] = $panel;
    }

    $input .= '$panels = array (' . "\n";
    foreach($panels as $key => $val){
       $input .= '\'' . $key . '\' => \'"' . implode("\", \"", $val) . "\"',\n";
    }
    $input .=  ");\n\n";

    $doctype = mosGetParam($_REQUEST, 'doctype', '');
    $input .= '$doctype = \'' . $doctype . "';\n";

    $css_class = mosGetParam($_REQUEST, 'css_class', '');
    $input .= '$css_class = \'' . $css_class . "';\n";

    $cssfiles = mosGetParam($_REQUEST, 'cssfiles', '');
    $input .= '$cssfiles = array("' . str_replace("\r", "", implode("\", \"", explode("\n", $cssfiles))) . "\");\n";

    $absolute_urls = mosGetParam($_REQUEST, 'absolute_urls', false);
    $input .= '$absolute_urls = \'' . $absolute_urls . "';\n";

    $allow_source = mosGetParam($_REQUEST, 'allow_source', true);
    $input .= '$allow_source = \'' . $allow_source . "';\n";

    $style_with_css = mosGetParam($_REQUEST, 'style_with_css', false);
    $input .= '$style_with_css = \'' . $style_with_css . "';\n";

    $fm_allow = mosGetParam($_REQUEST, 'fm_allow', array());
    $input .= '$fm_allow = array(' . implode(', ', $fm_allow) . ");\n";

    $editor_height = mosGetParam($_REQUEST, 'editor_height', 0);
    $input .= '$editor_height = \'' . $editor_height . "';\n";

    $editor_width = mosGetParam($_REQUEST, 'editor_width', 0);
    $input .= '$editor_width = \'' . $editor_width . "';\n";

    $contents = "<?php\n";
    $contents .= "defined('_VALID_MOS') or die();\n\n";
    $contents .= $input;
    $contents .= "\n?>";
    if (!is_writable(dirname(__FILE__) . '/config_elrte.php')) {
        mosRedirect('index2.php?option=com_elrte&task=config_elrte', 'Configuration file "config_elrte" is Not writable');
        return;
    }

    $fp = fopen(dirname(__FILE__) . '/config_elrte.php', 'w');

    fwrite($fp, $contents);
    fclose($fp);

    mosRedirect('index2.php?option=com_elrte&task=config_elrte', _CONFIG_SAVED);
}
function save_config_elfinder()
{
    //конфиг файлового менеджера
    $input = '';

    //подготавливаем запрещенные команды для групп пользователей
    $disabled_command = mosGetParam($_REQUEST, 'disabled_command', array());
    $panels = array();
    foreach($disabled_command as $permission){
       $perms = (explode('_', $permission));
       $panel = $perms[0];
       $groupId = $perms[1];
       $panels[$groupId][] = $panel;
    }

    $input .= '$disabled_command = array (' . "\n";
    foreach($panels as $key => $val){
       $input .= '\'' . $key . '\' => array("' . implode("\", \"", $val) . "\"),\n";
    }
    $input .=  ");\n\n";

    //подготавливаем разрешенные для загрузки файлы для групп пользователей
    $upload_allow = mosGetParam($_REQUEST, 'upload_allow', array());
    $panels = array();
    foreach($upload_allow as $permission){
       $perms = (explode('_', $permission));
       $panel = $perms[0];
       $groupId = $perms[1];
       $panels[$groupId][] = $panel;
    }

    $input .= '$upload_allow = array (' . "\n";
    foreach($panels as $key => $val){
       $input .= '\'' . $key . '\' => array("' . implode("\", \"", $val) . "\"),\n";
    }
    $input .=  ");\n\n";


    $file_manager_dir = mosGetParam($_REQUEST, 'file_manager_dir', '');
    $input .= '$file_manager_dir = \'' . $file_manager_dir . "';\n";

    $file_manager_owndir = mosGetParam($_REQUEST, 'file_manager_owndir', 0);
    $input .= '$file_manager_owndir = \'' . $file_manager_owndir . "';\n";

    $root_alias = mosGetParam($_REQUEST, 'root_alias', '');
    $input .= '$root_alias = \'' . $root_alias . "';\n";

    $dot_files = mosGetParam($_REQUEST, 'dot_files', 0);
    $input .= '$dot_files = \'' . $dot_files . "';\n";

    $dir_size = mosGetParam($_REQUEST, 'dir_size', 0);
    $input .= '$dir_size = \'' . $dir_size . "';\n";

    $file_mode = (!empty($_REQUEST['file_mode']) ? $_REQUEST['file_mode'] : '0644');
    $input .= '$file_mode = \'' . $file_mode . "';\n";

    $dir_mode = (!empty($_REQUEST['dir_mode']) ? $_REQUEST['dir_mode'] : '0755');
    $input .= '$dir_mode = \'' . $dir_mode . "';\n";

    $img_lib = mosGetParam($_REQUEST, 'img_lib', '');
    $input .= '$img_lib = \'' . $img_lib . "';\n";

    $tmb_dir = mosGetParam($_REQUEST, 'tmb_dir', '');
    $input .= '$tmb_dir = \'' . $tmb_dir . "';\n";

    $tmb_clean_prob = mosGetParam($_REQUEST, 'tmb_clean_prob', 0);
    $input .= '$tmb_clean_prob = \'' . $tmb_clean_prob . "';\n";

    $tmb_at_once = mosGetParam($_REQUEST, 'tmb_at_once', 5);
    $input .= '$tmb_at_once = \'' . $tmb_at_once . "';\n";

    $tmb_size = mosGetParam($_REQUEST, 'tmb_size', 20);
    $input .= '$tmb_size = \'' . $tmb_size . "';\n";

    $file_url = mosGetParam($_REQUEST, 'file_url', 0);
    $input .= '$file_url = \'' . $file_url . "';\n";

    $places = mosGetParam($_REQUEST, 'places', '');
    $input .= '$places = \'' . $places . "';\n";

    $places_first = mosGetParam($_REQUEST, 'places_first', 1);
    $input .= '$places_first = \'' . $places_first . "';\n";

    $view = mosGetParam($_REQUEST, 'view', '');
    $input .= '$view = \'' . $view . "';\n";

    $remember_last_dir = mosGetParam($_REQUEST, 'remember_last_dir', 0);
    $input .= '$remember_last_dir = \'' . $remember_last_dir . "';\n";

    $contents = "<?php\n";
    $contents .= "defined('_VALID_MOS') or die();\n\n";
    $contents .= $input;
    $contents .= "\n?>";
    if (!is_writable(dirname(__FILE__) . '/config_elfinder.php')) {
        mosRedirect('index2.php?option=com_elrte&task=config_elfinder', 'Configuration file "config_elfinder" is Not writable');
        return;
    }

    $fp = fopen(dirname(__FILE__) . '/config_elfinder.php', 'w');

    fwrite($fp, $contents);
    fclose($fp);

    mosRedirect('index2.php?option=com_elrte&task=config_elfinder', _CONFIG_SAVED);
}

function info()
{
    include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'view' . DS . 'info.php');
}
function connector()
{
    include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'connector.php');
}