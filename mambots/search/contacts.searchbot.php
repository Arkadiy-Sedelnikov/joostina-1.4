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

$_MAMBOTS->registerFunction('onSearch','botSearchContacts');

/**
 * Метод поиска контактов
 *
 * запрос sql должен возвратить поля, используются в обычной операции
 * отображения: href, title, section, created, text, browsernav
 * @param определяет цель поиска
 * @param сопоставляет параметры: exact|any|all
 * @param определяет порядок сортировки: newest|oldest|popular|alpha|category
 */
function botSearchContacts($text,$phrase = '',$ordering = '') {
	global $my,$_MAMBOTS;

	$database = database::getInstance();

	// check if param query has previously been processed
	if(!isset($_MAMBOTS->_search_mambot_params['contacts'])) {
		// load mambot params info
		$query = "SELECT params FROM #__mambots WHERE element = 'contacts.searchbot' AND folder = 'search'";
		$database->setQuery($query);
		$database->loadObject($mambot);

		// save query to class variable
		$_MAMBOTS->_search_mambot_params['contacts'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['contacts'];

	$botParams = new mosParameters($mambot->params);

	$limit = $botParams->def('search_limit',50);

	$text = trim($text);
	if($text == '') {
		return array();
	}

	$section = _CONTACT_TITLE;

	switch($ordering) {
		case 'alpha':
			$order = 'a.name ASC';
			break;

		case 'category':
			$order = 'b.title ASC, a.name ASC';
			break;

		case 'popular':
		case 'newest':
		case 'oldest':
		default:
			$order = 'a.name DESC';
			break;
	}

	$query = "SELECT a.name AS title,"."\n CONCAT_WS( ', ', a.name, a.con_position, a.misc ) AS text,".
			"\n '' AS created,"."\n CONCAT_WS( ' / ', ".$database->Quote($section).
			", b.title ) AS section,"."\n '2' AS browsernav,"."\n CONCAT( 'index.php?option=com_contact&task=view&contact_id=', a.id ) AS href".
			"\n FROM #__contact_details AS a"."\n INNER JOIN #__categories AS b ON b.id = a.catid".
			"\n WHERE ( a.name LIKE '%$text%'"."\n OR a.misc LIKE '%$text%'"."\n OR a.con_position LIKE '%$text%'".
			"\n OR a.address LIKE '%$text%'"."\n OR a.suburb LIKE '%$text%'"."\n OR a.state LIKE '%$text%'".
			"\n OR a.country LIKE '%$text%'"."\n OR a.postcode LIKE '%$text%'"."\n OR a.telephone LIKE '%$text%'".
			"\n OR a.fax LIKE '%$text%' )"."\n AND a.published = 1"."\n AND b.published = 1".
			"\n AND a.access <= ".(int)$my->gid."\n AND b.access <= ".(int)$my->gid."\n GROUP BY a.id".
			"\n ORDER BY $order";
	$database->setQuery($query,0,$limit);
	$rows = $database->loadObjectList();

	return $rows;
}