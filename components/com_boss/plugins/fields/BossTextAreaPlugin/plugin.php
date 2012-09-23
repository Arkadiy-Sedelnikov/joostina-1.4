<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_JLINDEX') or die();

class BossTextAreaPlugin{

	//имя типа поля в выпадающем списке в настройках поля
	var $name = 'Text Area';

	//тип плагина для записи в таблицы
	var $type = 'BossTextAreaPlugin';

	//отображение поля в категории
	function getListDisplay($directory, $content, $field, $field_values, $conf){
		return $this->getDetailsDisplay($directory, $content, $field, $field_values, $conf);
	}

	//отображение поля в контенте
	function getDetailsDisplay($directory, $content, $field, $field_values, $conf){
		$fieldname = $field->name;
		$value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
		if(!$value)
			return false;

		if($conf->use_content_mambot == 1){
			$_MAMBOTS = mosMambotHandler::getInstance();
			$_MAMBOTS->loadBotGroup('content');
			$params = new mosParameters('');
			$row = new stdClass();
			$row->text = $value;
			$_MAMBOTS->trigger('onPrepareContent', array(&$row, &$params, 0), true);
			$content->$fieldname = $value;
		}

		$return = '';
		if(!empty($field->text_before))
			$return .= '<span>' . $field->text_before . '</span>';
		if(!empty($field->tags_open))
			$return .= html_entity_decode($field->tags_open);

		$return .= str_replace(array("\r\n", "\n", "\r"), "<br />", $value);

		if(!empty($field->tags_close))
			$return .= html_entity_decode($field->tags_close);
		if(!empty($field->text_after))
			$return .= '<span>' . $field->text_after . '</span>';

		return $return;
	}

	//функция вставки фрагмента ява-скрипта в скрипт
	//сохранения формы при редактировании контента с фронта.
	function addInWriteScript($field){

	}

	//отображение поля в админке в редактировании контента
	function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write"){
		$fieldname = $field->name;
		$value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
		$strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');
		if($mode == "search"){
			$strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');
			$value = mosGetParam($_REQUEST, $field->name, '');
			$return = "<input class='boss' id='" . $field->name . "' type='text' name='" . $field->name . "' mosLabel='" . $strtitle . "' size='$field->size' maxlength='$field->maxlength' value='" . htmlspecialchars($value, ENT_QUOTES) . "' />\n";
		} else{
			$read_only = (($mode == "write") && ($field->editable == 0)) ? " readonly=true " : '';
			$req = (($mode == "write") && ($field->required == 1)) ? " class='boss_required' mosReq='1' " : " class='boss' ";
			$return = "<textarea $req mosLabel='" . $strtitle . "' id='" . $field->name . "' name='" . $field->name . "' cols='" . $field->cols . "' rows='" . $field->rows . "' wrap='VIRTUAL' onkeypress='CaracMax(this, $field->maxlength) ;' $read_only>$value</textarea>\n";
		}
		return $return;
	}

	function onFormSave($directory, $contentid, $field, $isUpdateMode){
		$return = mosGetParam($_POST, $field->name, "");
		return $return;
	}

	function onDelete($directory, $content){
		return;
	}

	//отображение поля в админке в настройках поля
	function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues){
		$return = '
            <div id="divTextLength">
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%">' . BOSS_FIELD_MAX_LENGTH . '</td> ';

		if(!isset($row->maxlength) || ($row->maxlength == ""))
			$row->maxlength = 20;
		$return .= '
                    <td width="20%"><input type="text" name="maxlength" mosLabel="Max Length" class="inputbox"
                                           value="' . $row->maxlength . '"/></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>

        <div id=divColsRows>
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%">' . BOSS_FIELD_COLS . '</td>
                    <td width="20%"><input type="text" name="cols" mosLabel="Cols" class="inputbox"
                                           value="' . $row->cols . '"/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%">' . BOSS_FIELD_ROWS . '</td>
                    <td width="20%"><input type="text" name="rows" mosLabel="Rows" class="inputbox"
                                           value="' . $row->rows . '"/></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
            ';
		return $return;
	}

	//действия при сохранении настроек поля
	function saveFieldOptions($directory, $field){
		//если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
		//иначе true
		return false;
	}

	//расположение иконки плагина начиная со слеша от корня сайта
	function getFieldIcon($directory){
		return "/images/boss/$directory/plugins/fields/" . __CLASS__ . "/images/comment.png";
	}

	//действия при установке плагина
	function install($directory){
		return;
	}

	//действия при удалении плагина
	function uninstall($directory){
		return;
	}

	//действия при поиске
	function search($directory, $fieldName){
		$search = '';
		$value = mosGetParam($_REQUEST, $fieldName, "");
		if($value != ""){
			$search .= " AND a.$fieldName LIKE '%$value%'";
		}
		return $search;
	}
}

?>