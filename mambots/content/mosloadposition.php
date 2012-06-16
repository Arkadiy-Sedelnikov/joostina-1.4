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

$_MAMBOTS->registerFunction('onPrepareContent', 'botMosLoadPosition');

/**
 * Мамбот, загружающий модули в пределах содержимого
 */
function botMosLoadPosition($published, &$row){
	$_MAMBOTS = mosMambotHandler::getInstance();
	$database = database::getInstance();

	// simple performance check to determine whether bot should process further
	if(strpos($row->text, 'mosloadposition') === false){
		return true;
	}

	// expression to search for
	$regex = '/{mosloadposition\s*.*?}/i';

	// check whether mambot has been unpublished
	if(!$published){
		$row->text = preg_replace($regex, '', $row->text);
		return true;
	}

	// найти все образцы мамбота и вставить в $matches
	preg_match_all($regex, $row->text, $matches);

	// Количество мамботов
	$count = count($matches[0]);

	// мамбот производит обработку если находит в тексте образцы мамбота
	if($count){
		// check if param query has previously been processed
		if(!isset($_MAMBOTS->_content_mambot_params['mosloadposition'])){
			// load mambot params info
			$query = "SELECT params FROM #__mambots WHERE element = 'mosloadposition' AND folder = 'content'";
			$database->setQuery($query);
			$database->loadObject($mambot);

			// save query to class variable
			$_MAMBOTS->_content_mambot_params['mosloadposition'] = $mambot;
		}

		// pull query data from class variable
		$mambot = $_MAMBOTS->_content_mambot_params['mosloadposition'];

		$botParams = new mosParameters($mambot->params);

		$style = $botParams->def('style', -2);

		processPositions($row, $matches, $count, $regex, $style);
	}
}

function processPositions(&$row, &$matches, $count, $regex, $style){
	$database = database::getInstance();

	$query = "SELECT position" . "\n FROM #__template_positions" . "\n ORDER BY position";
	$database->setQuery($query);
	$positions = $database->loadResultArray();

	for($i = 0; $i < $count; $i++){
		$load = str_replace('mosloadposition', '', $matches[0][$i]);
		$load = str_replace('{', '', $load);
		$load = str_replace('}', '', $load);
		$load = trim($load);

		foreach($positions as $position){
			if($position == @$load){
				$modules = loadPosition($load, $style);
				$row->text = str_replace($matches[0][$i], $modules, $row->text);
				break;
			}
		}
	}

	// удаление тэгов, не соответствующих позиции модуля
	$row->text = preg_replace($regex, '', $row->text);
}

function loadPosition($position, $style = -2){
	$modules = '';
	if(mosCountModules($position)){
		ob_start();
		mosLoadModules($position, $style);
		$modules = ob_get_contents();
		ob_end_clean();
	}
	return $modules;
}