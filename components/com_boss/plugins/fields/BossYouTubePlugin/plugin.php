<?php
/**
 * Field Plug for AdsManager
 * Author: Thomas PAPIN
 * URL:  http://www.joomprod.com
 * mail: webmaster@joomprod.com
 **/
defined('_VALID_MOS') or die();

class BossYouTubePlugin{

	//имя типа поля в выпадающем списке в настройках поля
	var $name = 'YouTube Player';

	//тип плагина для записи в таблицы
	var $type = 'BossYouTubePlugin';

	//отображение поля в категории
	function getListDisplay($directory, $content, $field, $field_values, $conf){
		return BossYouTubePlugin::getDetailsDisplay($directory, $content, $field, $field_values, $conf);
	}

	//отображение поля в контенте
	function getDetailsDisplay($directory, $content, $field, $field_values, $conf){

		$fieldName = $field->name;
		$fieldId = $field->fieldid;
		$value = $content->$fieldName;
		$return = "";

		if($value != ""){

			if(!empty($field->text_before))
				$return .= '<span>' . $field->text_before . '</span>';
			if(!empty($field->tags_open))
				$return .= html_entity_decode($field->tags_open);

			$database = database::getInstance();

			$key = explode('v=', $value);
			$key = $key[1];
			$key = explode('&', $key);
			$key = $key[0];

			$database->setQuery("SELECT `fieldtitle`, `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = '$fieldId' LIMIT 2");
			$conf = $database->loadObjectList('fieldtitle');
			$width = $conf['width']->fieldvalue; //500;
			$height = $conf['height']->fieldvalue; //300;

			$return .= '<object width="' . $width . '" height="' . $height . '">';
			$return .= '<param name="movie" value="http://www.youtube.com/v/' . $key . '&hl=fr&fs=1"></param>';
			$return .= '<param name="allowFullScreen" value="true"> </param>';
			$return .= '<param name="allowscriptaccess" value="always"> </param>';
			$return .= '<embed src="http://www.youtube.com/v/' . $key . '&hl=fr&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $width . '" height="' . $height . '">';
			$return .= '</embed>';
			$return .= '</object>';
			$return .= '<br/>';

			if(!empty($field->tags_close))
				$return .= html_entity_decode($field->tags_close);
			if(!empty($field->text_after))
				$return .= '<span>' . $field->text_after . '</span>';
		}
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

		$read_only = (($mode == "write") && ($field->editable == 0)) ? " readonly=true " : '';
		$req = (($mode == "write") && ($field->required == 1)) ? " class='boss_required' mosReq='1' " : " class='boss' ";

		$return = "<input $req id='" . $field->name . "' type='text' test='YouTube' mosLabel='" . $strtitle . "' name='" . $field->name . "' size='$field->size' maxlength='$field->maxlength' $read_only value='$value' />\n";

		return $return;
	}

	function onFormSave($directory, $contentid, $field, $isUpdateMode){
		$return = mosGetParam($_POST, $field->name, "");
		return $return;
	}

	function onDelete($directory, $content){
		return;
	}

	function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues){
		$width = @$fieldvalues['width']->fieldvalue;
		$height = @$fieldvalues['height']->fieldvalue;
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
        ';
		$return .= "<div id='divYouTubeOptions'>";
		$return .= "<table class='adminform'>";
		$return .= "<tr>";
		$return .= "<td width='20%'>Player Width</td>";
		$return .= "<td width='20%' align=left><input type='text' id='youtube_width' name='youtube_width' mosReq=1 mosLabel='Player Width' class='inputbox' value='" . @$width . "' /></td>";
		$return .= "<td>&nbsp;</td>";
		$return .= "</tr>";
		$return .= "<tr>";
		$return .= "<td width='20%'>Player Height</td>";
		$return .= "<td width='20%' align=left><input type='text' id='youtube_height' name='youtube_height' mosReq=1 mosLabel='Player Height' class='inputbox' value='" . @$height . "' /></td>";
		$return .= "<td>&nbsp;</td>";
		$return .= "</tr>";
		$return .= "</table>";
		$return .= "</div>";
		return $return;
	}

	//действия при сохранении настроек поля
	function saveFieldOptions($directory, $field){
		$fieldId = $field->fieldid;
		$database = database::getInstance();

		$width = mosGetParam($_POST, "youtube_width", 0);
		$height = mosGetParam($_POST, "youtube_height", 0);

		$database->setQuery("DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = '" . $fieldId . "' ");
		$database->query();
		$database->setQuery("INSERT INTO #__boss_" . $directory . "_field_values
    		                    (fieldid, fieldtitle, fieldvalue, ordering, sys)
    		                    VALUES
    		                    ($fieldId,'width', '$width',  1,0),
    		                    ($fieldId,'height','$height', 2,0)
    		                    ");
		$database->query();
		//если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
		//иначе true
		return false;
	}

	//расположение иконки плагина начиная со слеша от корня сайта
	function getFieldIcon($directory){
		return "/images/boss/$directory/plugins/fields/" . __CLASS__ . "/images/webcam.png";
	}

	function install(){
		return;
	}

	function uninstall(){
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
