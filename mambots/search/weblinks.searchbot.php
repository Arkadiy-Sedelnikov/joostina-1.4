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

$_MAMBOTS->registerFunction('onSearch','botSearchWeblinks');

/**
 * Метод поиска интернет-ссылок
 *
 * запрос sql должен возвратить поля, используются в обычной операции
 * отображения: href, title, section, created, text, browsernav
 * @param определяет цель поиска
 * @param сопоставляет параметры: exact|any|all
 * @param определяет параметр сортировки: newest|oldest|popular|alpha|category
 */
function botSearchWeblinks($text,$phrase = '',$ordering = '') {
	global $my,$_MAMBOTS;

	$database = database::getInstance();

	// check if param query has previously been processed
	if(!isset($_MAMBOTS->_search_mambot_params['weblinks'])) {
		// load mambot params info
		$query = "SELECT params FROM #__mambots WHERE element = 'weblinks.searchbot' AND folder = 'search'";
		$database->setQuery($query);
		$database->loadObject($mambot);

		// save query to class variable
		$_MAMBOTS->_search_mambot_params['weblinks'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['weblinks'];

	$botParams = new mosParameters($mambot->params);

	$limit = $botParams->def('search_limit',50);

	$text = trim($text);
	if($text == '') {
		return array();
	}
	$section = _LINKS;

	$wheres = array();
	switch($phrase) {
		case 'exact':
			$wheres2 = array();

			$wheres2[] = "LOWER(a.url) LIKE '%$text%'";
			$wheres2[] = "LOWER(a.description) LIKE '%$text%'";
			$wheres2[] = "LOWER(a.title) LIKE '%$text%'";
			$where = '('.implode(') OR (',$wheres2).')';
			break;

		case 'all':
		case 'any':
		default:
			$words = explode(' ',$text);
			$wheres = array();
			foreach($words as $word) {
				$wheres2 = array();
				$wheres2[] = "LOWER(a.url) LIKE '%$word%'";
				$wheres2[] = "LOWER(a.description) LIKE '%$word%'";
				$wheres2[] = "LOWER(a.title) LIKE '%$word%'";
				$wheres[] = implode(' OR ',$wheres2);
			}
			$where = '('.implode(($phrase == 'all'?') AND (':') OR ('),$wheres).')';
			break;
	}

	switch($ordering) {
		case 'oldest':
			$order = 'a.date ASC';
			break;

		case 'popular':
			$order = 'a.hits DESC';
			break;

		case 'alpha':
			$order = 'a.title ASC';
			break;

		case 'category':
			$order = 'b.title ASC, a.title ASC';
			break;

		case 'newest':
		default:
			$order = 'a.date DESC';
	}

	$query = "SELECT a.title AS title,"."\n a.description AS text,"."\n a.date AS created,".
			"\n CONCAT_WS( ' / ', ".$database->Quote($section).", b.title ) AS section,"."\n '1' AS browsernav,".
			"\n a.url AS href"."\n FROM #__weblinks AS a"."\n INNER JOIN #__categories AS b ON b.id = a.catid".
			"\n WHERE ($where)"."\n AND a.published = 1"."\n AND b.published = 1"."\n AND b.access <= ".(int)
			$my->gid."\n ORDER BY $order";
	$database->setQuery($query,0,$limit);
	$rows = $database->loadObjectList();

	return $rows;
}