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

$_MAMBOTS->registerFunction('onSearch','botSearchNewsfeedslinks');

/**
 * Метод поиска контактов
 *
 * запрос sql должен возвратить поля, используются в обычной операции
 * отображения: href, title, section, created, text, browsernav
 * @param определяет цель поиска
 * @param сопоставляет параметры: exact|any|all
 * @param определяет параметр сортировки: newest|oldest|popular|alpha|category
 */
function botSearchNewsfeedslinks($text,$phrase = '',$ordering = '') {
	global $_MAMBOTS;
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
	$database = database::getInstance();

	// check if param query has previously been processed
	if(!isset($_MAMBOTS->_search_mambot_params['newsfeeds'])) {
		// load mambot params info
		$query = "SELECT params FROM #__mambots WHERE element = 'newsfeeds.searchbot' AND folder = 'search'";
		$database->setQuery($query);
		$database->loadObject($mambot);

		// save query to class variable
		$_MAMBOTS->_search_mambot_params['newsfeeds'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['newsfeeds'];

	$botParams = new mosParameters($mambot->params);

	$limit = $botParams->def('search_limit',50);

	$text = trim($text);
	if($text == '') {
		return array();
	}

	$wheres = array();
	switch($phrase) {
		case 'exact':
			$wheres2 = array();
			$wheres2[] = "LOWER(a.name) LIKE '%$text%'";
			$wheres2[] = "LOWER(a.link) LIKE '%$text%'";
			$where = '('.implode(') OR (',$wheres2).')';
			break;

		case 'all':
		case 'any':
		default:
			$words = explode(' ',$text);
			$wheres = array();
			foreach($words as $word) {
				$wheres2 = array();
				$wheres2[] = "LOWER(a.name) LIKE '%$word%'";
				$wheres2[] = "LOWER(a.link) LIKE '%$word%'";
				$wheres[] = implode(' OR ',$wheres2);
			}
			$where = '('.implode(($phrase == 'all'?') AND (':') OR ('),$wheres).')';
			break;
	}


	switch($ordering) {
		case 'alpha':
			$order = 'a.name ASC';
			break;

		case 'category':
			$order = 'b.title ASC, a.name ASC';
			break;

		case 'oldest':
		case 'popular':
		case 'newest':
		default:
			$order = 'a.name ASC';
	}

	$query = "SELECT a.name AS title,"."\n '' AS created,"."\n a.link AS text,"."\n CONCAT_WS( ' / ',".
			$database->Quote(_SEARCH_NEWSFEEDS).", b.title )AS section,"."\n CONCAT( 'index.php?option=com_newsfeeds&task=view&feedid=', a.id ) AS href,".
			"\n '1' AS browsernav"."\n FROM #__newsfeeds AS a"."\n INNER JOIN #__categories AS b ON b.id = a.catid".
			"\n WHERE ( $where )"."\n AND a.published = 1"."\n AND b.published = 1"."\n AND b.access <= ".(int)
			$my->gid."\n ORDER BY $order";
	$database->setQuery($query,0,$limit);
	$rows = $database->loadObjectList();

	return $rows;
}