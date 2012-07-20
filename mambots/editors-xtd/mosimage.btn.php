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

$_MAMBOTS->registerFunction('onCustomEditorButton', 'botMosImageButton');

/**
 * кнопка изображения Joostina
 * @return array - возвращает массив из двух элементов: ( imageName, textToInsert )
 */
function botMosImageButton(){
	global $option;

	// button is not active in specific content components
	switch($option){
		case 'com_sections':
		case 'com_categories':
		case 'com_modules':
			$button = array('', '');
			break;

		default:
			$button = array('mosimage.png', '{mosimage}');
			break;
	}
	return $button;
}