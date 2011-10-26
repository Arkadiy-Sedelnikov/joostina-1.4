<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */

defined('_VALID_MOS') or die();
require_once( JPATH_BASE . DS . 'components' . DS . 'com_boss' . DS . 'boss.tools.php' );
require_once($mainframe->getPath('admin_html'));
require_once($mainframe->getPath('class'));

//if(!boss_helpers::is_ajax()){
//    die('Is Not Ajax Query');
//}

$act = mosGetParam($_REQUEST, 'act', '');
$task = mosGetParam($_REQUEST, 'task', '');
$directory = mosGetParam($_REQUEST, 'directory', 0);
$fieldid = mosGetParam($_REQUEST, 'fieldid', 0);
$fieldids = mosGetParam($_REQUEST, 'fieldids', array());
boss_helpers::loadBossLang($directory);

switch ($act) {
    case "builder":
        switch ($task) {
            case "new":
            case "edit":
                jDirectoryField::editField($directory);
                break;
            case "savefield":
                jDirectoryField::saveField($directory);
                break;
            case "savefieldorder":
                jDirectoryField::saveFieldOrder($fieldids, $directory);
                break;
            case "showfield":
                jDirectoryField::showField($directory, $fieldid);
                break;
            case "delete_field":
                jDirectoryField::removeField($directory, $fieldid);
                break;
            case "change_template":
                BossTemplates::change_template($directory, $fieldid);
                break;
            case "load_poz":
                BossTemplates::load_poz($directory, $fieldid);
                break;
            case "save_poz":
                BossTemplates::save_poz($directory, $fieldid);
                break;
            default :
                break;
        }
        break;
    case "fields":

        switch ($task) {
            case "publish":
                boss_helpers::changeState("#__boss_" . $directory . "_fields", 'published', mosGetParam($_REQUEST, 'tid', ''), 'fieldid');
                break;
            case "required":
                boss_helpers::changeState("#__boss_" . $directory . "_fields", 'required', mosGetParam($_REQUEST, 'tid', ''), 'fieldid');
                break;
            default :
                break;
        }

        break;
    case "groups":

        switch ($task) {
            case "publish":
                boss_helpers::changeState("#__boss_" . $directory . "_groups", 'published', mosGetParam($_REQUEST, 'tid', ''), 'id');
                break;
            default :
                break;
        }

        break;
    case "categories":

        switch ($task) {
            case "publish":
                boss_helpers::changeState("#__boss_" . $directory . "_categories", 'published', mosGetParam($_REQUEST, 'tid', ''), 'id');
                break;
            default :
                break;
        }

        break;
    case "content_types":

        switch ($task) {
            case "publish":
                boss_helpers::changeState("#__boss_" . $directory . "_content_types", 'published', mosGetParam($_REQUEST, 'tid', ''), 'id');
                break;
            default :
                break;
        }

        break;
    case "template_fields":

        switch ($task) {
            case "publish":
                boss_helpers::changeState("#__boss_" . $directory . "_groups", 'published', mosGetParam($_REQUEST, 'tid', ''), 'id');
                break;
            default :
                break;
        }

        break;
    case "contents":

        switch ($task) {
            case "publish":
                boss_helpers::changeState("#__boss_" . $directory . "_contents", 'published', mosGetParam($_REQUEST, 'tid', ''), 'id');
                break;
            default :
                break;
        }

        break;

    case "upload_image":
        boss_helpers::upload_image($directory);
        break;
    
    case "upload_file":
        $folder = mosGetParam($_REQUEST, 'folder', '');
        boss_helpers::upload_file($directory, $folder);
        break;

    case "delete_pack":
        boss_helpers::delete_pack($directory);
        break;

    case "delete_file":
        boss_helpers::delete_file($directory);
        break;

    case "plugins":
         switch ($task) {
            case "run_plugins_func":
                BossPlugins::run_plugins_func($directory);
                break;
            default :
                break;
        }
        break;

    default :
        break;
}
?>
