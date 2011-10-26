<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

require_once( $mainframe->getPath( 'class', 'com_content') );
require_once( $mainframe->getPath( 'front_html', 'com_content') );
global $my, $mosConfig_link_titles;

$category = new stdClass();
$category->id = intval($params->get('catid'));

$access = new stdClass();
$access->canEdit	= 0;
$access->canEditOwn = 0;
$access->canPublish = 0;

$date_type = $params->get('date_type', 'created');

$content_params = $params;
$content_params->set('rating', 0);
$content_params->set('limitstart', 0);
$content_params->set('limit', $params->get('items', 3));
$content_params->set('orderby_pri', '');
$content_params->set('orderby_sec', $date_type);


$content_items = new mosContent($mainframe->getDBO());
$items = $content_items->_load_blog_category($category, $content_params, $access);
$params->def('numrows', count($items));
$params->set('intro_only',1);

$catid	= intval( $params->get('catid') );
$link_titles = $params->get('link_titles', $mosConfig_link_titles);

if(!$params->get('template', '')) {
	switch ($params->get('style', 'vert')) {
		
		case 'horiz':
			$params->set('template', 'gorizontal.php');
			break;

		case 'vert':
			$params->set('template', 'vertical.php');
			break;

		case 'random':
		default:
			srand ((double) microtime()* 1000000);
			$flashnum = rand( 0, $params->get('numrows') - 1 );
			$row = $items[$flashnum];
			$params->set('template', 'flash.php');
			break;
	}
}
else {

	if($params->get('style')=='random') {
		srand ((double) microtime()* 1000000);
		$flashnum = rand( 0, $params->get('numrows') - 1 );
		$row = $items[$flashnum];
	}
}

//Подключаем вспомогательный класс
$module->get_helper($mainframe);

//Подключаем шаблон
if($module->set_template($params)) {
	require($module->template);
}