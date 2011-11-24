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
require_once( $mainframe->getPath( 'front_html' ) );
require_once($mainframe->getPath('class'));

//if(!boss_helpers::is_ajax()){
//    die('Is Not Ajax Query');
//}

$act = mosGetParam($_REQUEST, 'act', '');
$task = mosGetParam($_REQUEST, 'task', '');
$directory = mosGetParam($_REQUEST, 'directory', 0);
boss_helpers::loadBossLang($directory);

switch ($act) {

    case "upload_image":
        boss_helpers::upload_image($directory);
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
    
    case "upload_image":
        boss_helpers::upload_image($directory);
        break;

    case "upload_file":
        $folder = mosGetParam($_REQUEST, 'folder', '');
        boss_helpers::upload_file($directory, $folder);
        break;
    
    case "delete_file":
        boss_helpers::delete_file($directory);
        break;

    default :
        break;
}
?>
