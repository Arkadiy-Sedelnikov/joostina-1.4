<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

$_MAMBOTS->registerFunction('onAfterDisplayContent', 'frontpagemodule');

function frontpagemodule($row, &$params){
	global $option;
	$_MAMBOTS = mosMambotHandler::getInstance();
	$database = database::getInstance();

	// исключаем работу мамбота в модулях и при выводе на печать
	$pvars = array_keys(get_object_vars($params->_params));
	if($params->get('popup') || in_array('moduleclass_sfx', $pvars)){
		return;
	}

	if($option == 'com_frontpage'){
		if(!isset($_MAMBOTS->_content_mambot_params['frontpagemodule'])){
			$database->setQuery("SELECT params FROM #__mambots WHERE element = 'frontpagemodule' AND folder = 'content'");
			$database->loadObject($mambot);
			$_MAMBOTS->_content_mambot_params['bot_frontpagemodule'] = $mambot;
		}

		if(!isset($_MAMBOTS->_content_mambot_params['_frontpagemodule']->params->i)){
			$_MAMBOTS->_content_mambot_params['_frontpagemodule']->params->i = 1;
		} else{
			$_MAMBOTS->_content_mambot_params['_frontpagemodule']->params->i++;
		}

		$params_bot = new mosParameters($_MAMBOTS->_content_mambot_params['frontpagemodule']->params);

		$mod_position = $params_bot->def('mod_position', 'banner');
		$mod_type = $params_bot->def('mod_type', '1');
		$mod_after = $params_bot->def('mod_after', '1');

		if(mosCountModules($mod_position) > 0 && $_MAMBOTS->_content_mambot_params['_frontpagemodule']->params->i == $mod_after){
			echo '<div class="frontpagemodule">';
			mosLoadModules($mod_position, $mod_type);
			echo '</div>';
		}
	}
}