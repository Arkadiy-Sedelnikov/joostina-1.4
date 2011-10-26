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

$use_ext = $params->get('use_ext',0);
$use_cache = $params->get('use_cache',1);

if(!defined('_QUICKICON_MODULE')) {
    define('_QUICKICON_MODULE',1);

    function show_quick_icons($use_ext) {
        if($use_ext) {
            // использование значков отображения шаблона
            if(file_exists(JPATH_BASE_ADMIN.'/templates/'.JTEMPLATE.'/html/quickicons.php')) {
                require_once (JPATH_BASE_ADMIN.'/templates/'.JTEMPLATE.'/html/quickicons.php');
            } else {
                // использование стандартных значков отображения
                require_once (JPATH_BASE_ADMIN.'/components/com_quickicons/quickicons.php');
            }
        }else {
            // использование стандартных значков отображения
            require_once (JPATH_BASE_ADMIN.'/components/com_quickicons/quickicons.php');
        }
    }
}
// значки быстрого доступа кэшируем
$cache = mosCache::getCache('quick_icons', 'callback','file',2592000);
$cache->_options['caching'] = $use_cache;
$r = $cache->call('show_quick_icons', $use_ext);
unset($cache,$r);