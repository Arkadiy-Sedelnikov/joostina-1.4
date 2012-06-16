<?php

/**
 * @BOSS - Мамбот поиска в контенте JoiBoss CCK
 * @version 1.0.2 RE
 * @author: Joostina! Project <joostinacms@gmail.com>
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */

//запрет прямого доступа 
defined('_VALID_MOS') or die();

$_MAMBOTS->registerFunction('onSearch', 'botSearchBoss');

/**
 * Content Search method
 * запрос sql должен возвратить поля, используются в обычной операции
 * отображения: href, title, section, created, text, browsernav
 * @param определяет цель поиска
 * @param сопоставляет параметры: exact|any|all
 * @param определяет параметр сортировки: newest|oldest|popular|alpha|category
 */
function botSearchBoss($text, $phrase = '', $ordering = ''){
	$_MAMBOTS = mosMambotHandler::getInstance();

	$database = &database::getInstance();
	// check if param query has previously been processed
	if(!isset($_MAMBOTS->_search_mambot_params['boss'])){
		// load mambot params info
		$mambot = null;
		$query = "SELECT `params` FROM #__mambots WHERE `element` = 'boss.searchbot' AND `folder` = 'search'";
		$database->setQuery($query);
		$database->loadObject($mambot);
		// save query to class variable
		$_MAMBOTS->_search_mambot_params['boss'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['boss'];

	$botParams = new mosParameters($mambot->params);

	$limit = $botParams->get('search_limit', 50);
	$directories = $botParams->get('directories', '');
	$content_field = $botParams->get('content_field', 'content_text');
	$group_results = $botParams->get('group_results', 1);

	$text = trim($text);
	if($text == ''){
		return array();
	}

	if(!empty($directories)){
		$directories = explode(',', $directories);
	} else{
		$query = "SELECT `id` FROM #__boss_config";
		$database->setQuery($query);
		$directories = $database->loadResultArray();
	}
	$list = array();
	foreach($directories as $directory){
		$directory = intval(trim($directory));
		$query = "SELECT `name` FROM #__boss_" . $directory . "_fields ";
		//$query .= "WHERE `searchable` = 1 AND `type` IN ('text', 'textarea', 'editor')";
		$query .= "WHERE `searchable` = 1 AND `published` = 1";
		$database->setQuery($query);
		$fields = $database->loadResultArray();
		$wheres = array();
		foreach($fields as $field){
			$wheres[] = "LOWER(a.$field) LIKE LOWER('%$text%')";
		}
		$wheres[] = "LOWER(a.name) LIKE LOWER('%$text%')";

		switch($ordering){
			case 'popular':
				$order = 'a.views DESC';
				break;
			case 'alpha':
				$order = "a.name ASC";
				break;
			case 'category':
				$order = "a.name ASC";
				break;
			case 'oldest':
				$order = 'a.date_created ASC';
				break;
			case 'newest':
			default:
				$order = 'a.date_created DESC';
				break;
		}
		// проверка существует ли поле
		$sql = "SELECT count(*) AS count FROM #__boss_" . $directory . "_fields WHERE type='" . $content_field . "'";
		$database->setQuery($sql);

		if($database->loadResult()){
			$field_content = 'a.' . $content_field . ' AS text';
		} else{
			$field_content = "'' AS text";
		}

		// search content items
		$query = "SELECT a.name AS title," . "\n a.date_created AS created," . "\n " . $field_content . "," .
			"\n cat.name AS section," .
			"\n CONCAT( 'index.php?option=com_boss&directory=$directory&task=show_content&contentid=', a.id, '&catid=', cch.category_id ) AS href," .
			"\n '2' AS browsernav," .
			"\n 'content' AS type" . "\n, 3 AS sec_id, 4 as cat_id" .
			"\n FROM #__boss_" . $directory . "_contents AS a," .
			"\n #__boss_" . $directory . "_content_category_href AS cch, " .
			"\n #__boss_" . $directory . "_categories AS cat" .
			"\n WHERE ( " . implode(' OR ', $wheres) . " )" . "\n AND a.published = 1" .
			"\n AND a.id=cch.content_id" .
			"\n AND cat.id=cch.category_id"; //echo '<pre>';print_r($query);echo '</pre><br/>';

		if($group_results == 1)
			$query .= "\n GROUP BY a.id";

		$query .= "\n ORDER BY $order";

		$database->setQuery($query, 0, $limit);
		$list_tmp = $database->loadObjectList();

		if(is_array($list_tmp)){
			$list = array_merge($list, $list_tmp);
		}
	}
	return $list;
}