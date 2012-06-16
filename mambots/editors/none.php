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

$_MAMBOTS->registerFunction('onInitEditor', 'botNoEditorInit');
$_MAMBOTS->registerFunction('onGetEditorContents', 'botNoEditorGetContents');
$_MAMBOTS->registerFunction('onEditorArea', 'botNoEditorEditorArea');

/**
 * Не визуальный редактор - инициализация javascript
 */
function botNoEditorInit(){
	return <<< EOD
<script type="text/javascript">
	function insertAtCursor(myField, myValue) {
		if (document.selection) {
			// IE
			myField.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
		} else if (myField.selectionStart || myField.selectionStart == '0') {
			// MOZILLA/NETSCAPE
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
		} else {
			myField.value += myValue;
		}
	}
</script>
EOD;
}

/**
 * Не визуальный редактор - копирование содержимого редактора в поле формы
 * @param string - Название области редактора
 * @param string - Название поля формы
 */
function botNoEditorGetContents(){
	return <<< EOD
EOD;
}

/**
 * Не визуальный редактор - отображение редактора
 * @param string - Название области редактора
 * @param string - Поле содержимого
 * @param string - Название поля формы
 * @param string - Ширина области редактора
 * @param string - Высота области редактора
 * @param int - Число столбцов области редактора
 * @param int - Число строк области редактора
 */
function botNoEditorEditorArea($name, $content, $hiddenField, $width, $height, $col, $row){
	$_MAMBOTS = mosMambotHandler::getInstance();
	$results = $_MAMBOTS->trigger('onCustomEditorButton');
	$buttons = array();
	foreach($results as $result){
		if($result[0]){
			$buttons[] = '<img src="' . JPATH_SITE . '/mambots/editors-xtd/' . $result[0] . '" onclick="insertAtCursor( document.adminForm.' . $hiddenField . ', \'' . $result[1] . '\' )" alt="' . $result[1] . '"/>';
		}
	}
	$buttons = implode("", $buttons);
	$width = $width . 'px';
	$height = $height . 'px';

	return <<< EOD
<textarea name="$hiddenField" id="$hiddenField" cols="$col" rows="$row" style="width:$width;height:$height;">$content</textarea>
<br />$buttons
EOD;
}