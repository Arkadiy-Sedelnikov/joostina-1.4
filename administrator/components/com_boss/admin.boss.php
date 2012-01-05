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
mosCommonHTML::loadJquery();
$mainframe->addJS(JPATH_SITE . '/administrator/components/com_boss/js/function.js');
$mainframe->addCSS(JPATH_SITE . '/administrator/components/com_boss/css/boss_admin.css');

require_once($mainframe->getPath('admin_html'));
require_once($mainframe->getPath('class'));
require_once(JPATH_BASE . '/components/com_boss/boss.tools.php');

$directory = intval(mosGetParam($_REQUEST, 'directory', 0));

if ($directory == 0) {
    $database = database::getInstance();
    $directory = $database->setQuery("SELECT MIN(id) FROM #__boss_config")->loadResult();
    if (!$directory) {
        $directory = 0;
    }
}

$act = mosGetParam($_REQUEST, 'act', "");
$task = mosGetParam($_REQUEST, 'task', "");

$params = HTML_boss::getLayout();

if($directory == 0 && (($act != 'manager' && $task !=  'new') && $act != 'export_import')){
    $params['act'] = $_REQUEST['act']  = $act  =  'manager';
    $_REQUEST['task'] = $task =  '';
}

boss_helpers::loadBossLang($directory);
boss_helpers::addDirectoryScript($directory);

$conf = getConfig($directory);

$layout = $params['layout'];

if(isset($conf->allow_rights) && !$conf->allow_rights){
    $act = $params['act'];
}

switch ($act) {

    case "manager":
        switch ($task) {
            case "new":
                BossDirectory::addDirectory();
                break;

            case "edit":
                {
                $tid = mosGetParam($_REQUEST, 'tid', 0);
                if (is_array($tid)) {
                    $tid = $tid[0];
                }
                jDirectoryConf::editConfiguration($tid, $conf);
                }
                break;

            case "delete" :
                BossDirectory::deleteDirectory();
                break;

            default:
                BossDirectory::showDirectories($directory, $conf);
                break;
        }
        break;

    case "content_types":
        $tid = mosGetParam($_REQUEST, 'tid', array(0));
        if (!is_array($tid)) {
            $tid = array(0);
        }
        switch ($task) {
            case "save" :
            case "apply" :
                BossContentTypes::saveContentTypes($directory);
                break;

            case "edit" :
                BossContentTypes::displayContentTypes($directory, $conf);
                break;

            case "new" :
                BossContentTypes::newContentTypes($directory, $conf);
                break;

            case "delete" :
                BossContentTypes::deleteContentTypes($directory);
                break;

            case "publish" :
                BossContentTypes::publishContentTypes($directory);
                break;

            case 'orderup':
                BossContentTypes::orderContentTypes(intval($tid[0]), -1, $directory);
                break;

            case 'orderdown':
                BossContentTypes::orderContentTypes(intval($tid[0]), 1, $directory);
                break;

            case 'saveorder':
                BossContentTypes::saveOrder($tid, $directory);
                break;

            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf)) {
                    break;
                }
                BossContentTypes::listContentTypes($directory, $conf);
        }
        break;
    
    case "configuration":
        switch ($task) {
            case "save":
            case "apply":
                jDirectoryConf::saveConfiguration($directory);
                break;

            case "edit":
            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf))
                    break;
                jDirectoryConf::editConfiguration($directory, $conf);
                break;
        }
        break;

    case "contents":
        switch ($task) {
            case "save" :
            case "apply" :
                jDirectoryContent::saveContent($directory, $conf);
                break;

            case "edit" :
            case "copy" :
                jDirectoryContent::displayContent($directory, $conf);
                break;

            case "new" :
                $id = '';
                jDirectoryContent::newContent($directory, $conf);
                break;

            case "delete" :
                jDirectoryContent::deleteContent($directory, $conf);
                break;

            case "publish" :
                jDirectoryContent::publishContent($directory);
                break;

            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf))
                    break;
                jDirectoryContent::listContents($directory, $conf);
                break;
        }
        break;

    case "categories" :
        switch ($task) {
            case "save" :
            case "apply" :
                jDirectoryCategory::saveCategory($directory, $conf);
                break;

            case "edit" :
                jDirectoryCategory::displayCategory($directory, $conf);
                break;

            case "new" :
                jDirectoryCategory::newCategory($directory, $conf);
                break;

            case "delete" :
                jDirectoryCategory::deleteCategory($directory);
                break;

            case "publish" :
                jDirectoryCategory::publishCategory($directory);
                break;

            case 'orderup':
                $tid = mosGetParam($_REQUEST, 'tid', array(0));
                if (!is_array($tid)) {
                    $tid = array(0);
                }
                jDirectoryCategory::orderCategory(intval($tid[0]), -1, $directory);
                break;

            case 'orderdown':
                $tid = mosGetParam($_REQUEST, 'tid', array(0));
                if (!is_array($tid)) {
                    $tid = array(0);
                }
                jDirectoryCategory::orderCategory(intval($tid[0]), 1, $directory);
                break;

            case 'saveorder':
                $tid = mosGetParam($_REQUEST, 'tid', array(0));
                if (!is_array($tid)) {
                    $tid = array(0);
                }
                jDirectoryCategory::saveOrder($tid, $directory);
                break;

            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf)) {
                    break;
                }
                jDirectoryCategory::listCategories($directory, $conf);
        }
        break;

    case "templates":
        $type_tmpl = mosGetParam($_REQUEST, 'type_tmpl', '');
        $template = mosGetParam($_REQUEST, 'template', '');
        $source_file = mosGetParam($_REQUEST, 'source_file', '');

        switch ($task) {
            case "delete" :
                BossTemplates::deleteTemplate($directory);
                break;

            case "edit_tmpl" :
                BossTemplates::editTemplate($directory, $template, $type_tmpl, $conf);
                break;

            case "new_tmpl_field" :
            case "edit_tmpl_field" :
                
                BossTemplateFields::editTemplateField($directory, $conf);
                break;

            case "list_tmpl_fields" :
                BossTemplateFields::listTemplateFields($directory, $template, $conf);
                break;

            case "delete_tmpl_field" :
                BossTemplateFields::deleteTemplateFields($directory);
                break;
            
            case "save_tmpl_field" :
                BossTemplateFields::saveTmplField($directory);
                break;
            
            case "edit_tmpl_source":
				BossTemplateFields::editTmplSource($directory, $template, $source_file, $conf);
				break;
			case "save_tmpl_source":
				BossTemplateFields::saveTmplSource($directory, $template, $source_file);
				break;

            case "save":
            case "apply":
                BossTemplates::saveTemplate($directory, $template, $type_tmpl);
                break;            
            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf)) {
                    break;
                }
                BossTemplates::listTemplates($directory, $conf);
                break;
        }
        break;

    case "fieldimage":
        switch ($task) {
            case "delete" :
                bossFieldImages::deleteFieldImage($directory);
                break;

            case "upload":
                bossFieldImages::uploadFieldImage($directory);
                break;
            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf)) {
                    break;
                }
                bossFieldImages::listFieldImages($directory, $conf);
                break;
        }
        break;

    case "fields":
        {
        switch ($task) {
            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf)) {
                    break;
                }
                jDirectoryField::showFields($directory, $conf);
                break;
        }
        }
        break;

    case "plugins":
        {
        switch ($task) {

            case "delete" :
                BossPlugins::deletePlugin($directory);
                break;

            case "upload":
                BossPlugins::installPlugin($directory);
                break;

            case "edit":
                BossPlugins::editPlugin($directory, $conf);
                break;
            
            case "save":
                BossPlugins::savePlugin($directory);
                break;

            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf)) {
                    break;
                }
                BossPlugins::listPlugins($directory, $conf);
                break;
        }
        }
        break;

    case "export_import":
        switch ($task) {

            case "export":
                bossExportImport::exportDirectory($directory);
                break;

            case "import" :
                bossExportImport::importDirectory();
                break;

            default:
                bossExportImport::showImpExpForm($directory, $conf);
                break;
        }
        break;

    case "users":
        {
        switch ($task) {

            case "delete" :
                BossUsers::deleteUserInfo($directory);
                break;

            case "new":
            case "edit":
                BossUsers::editUserInfo($directory, $conf);
                break;

            case "save":
            case "apply":
                BossUsers::saveUserInfo($directory);
                break;

            default:
                //проверяем наличие каталогов, если нет - завершаем страницу
                if (!HTML_boss::check_dir($directory, $conf)) {
                    break;
                }
                BossUsers::listUsers($directory, $conf);
                break;
        }
        }
        break;

    default:
        BossMain::displayMain($directory, $conf);
        break;
}