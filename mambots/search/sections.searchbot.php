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

$_MAMBOTS->registerFunction('onSearch','botSearchSections');

/**
 * Метод поиска секций
 *
 * запрос sql должен возвратить поля, используются в обычной операции
 * отображения: href, title, section, created, text, browsernav
 * @param определяет цель поиска
 * @param сопоставляет параметры: exact|any|all
 * @param определяет параметр сортировки: newest|oldest|popular|alpha|category
 */
function botSearchSections($text,$phrase = '',$ordering = '') {
	global $my,$_MAMBOTS;

	$database = database::getInstance();

	// check if param query has previously been processed
	if(!isset($_MAMBOTS->_search_mambot_params['sections'])) {
		// load mambot params info
		$query = "SELECT params FROM #__mambots WHERE element = 'sections.searchbot' AND folder = 'search'";
		$database->setQuery($query);
		$database->loadObject($mambot);

		// save query to class variable
		$_MAMBOTS->_search_mambot_params['sections'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['sections'];

	$botParams = new mosParameters($mambot->params);

	$limit = $botParams->def('search_limit',50);

	$text = trim($text);
	if($text == '') {
		return array();
	}

	switch($ordering) {
		case 'alpha':
			$order = 'a.title ASC';
			break;

		case 'category':
		case 'popular':
		case 'newest':
		case 'oldest':
		default:
			$order = 'a.title DESC';
	}

	$query = "SELECT a.title AS title,"."\n a.description AS text,"."\n '' AS created,".
			"\n '2' AS browsernav,"."\n a.id AS secid, m.id AS menuid, m.type AS menutype".
			"\n FROM #__sections AS a"."\n LEFT JOIN #__menu AS m ON m.componentid = a.id".
			"\n WHERE ( a.title LIKE '%$text%'"."\n OR a.title LIKE '%$text%'"."\n OR a.description LIKE '%$text%' )".
			"\n AND a.published = 1"."\n AND a.access <= ".(int)$my->gid."\n AND ( m.type = 'content_section' OR m.type = 'content_blog_section' )".
			"\n GROUP BY a.id"."\n ORDER BY $order";
	$database->setQuery($query,0,$limit);
	$rows = $database->loadObjectList();

	$count = count($rows);
	for($i = 0; $i < $count; $i++) {
		if($rows[$i]->menutype == 'content_section') {
			$rows[$i]->href = 'index.php?option=com_content&task=section&id='.$rows[$i]->secid.'&Itemid='.$rows[$i]->menuid;
			$rows[$i]->section = _SECTION_LIST;
		}
		if($rows[$i]->menutype == 'content_blog_section') {
			$rows[$i]->href = 'index.php?option=com_content&task=blogsection&id='.$rows[$i]->secid.'&Itemid='.$rows[$i]->menuid;
			$rows[$i]->section = _SECTION_BLOG;
		}
	}

	return $rows;
}