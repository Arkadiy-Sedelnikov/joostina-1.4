<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

    class BossDatePlugin {
                
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'Date';
        
        //тип плагина для записи в таблицы
        var $type = 'BossDatePlugin';
	
        //отображение поля в категории
        function getListDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            return $this->getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf);
        }

        //отображение поля в контенте
        function getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            $fieldname = $field->name;

            $return = '';
            if(!empty($field->text_before))
                $return .= '<span>'.$field->text_before.'</span>';
            if(!empty($field->tags_open))
                $return .= html_entity_decode($field->tags_open);

            $return .= (isset ($content->$fieldname)) ? $content->$fieldname : '';

            if(!empty($field->tags_close))
                $return .= html_entity_decode($field->tags_close);
            if(!empty($field->text_after))
                $return .= '<span>'.$field->text_after.'</span>';

            return $return;
        }

        //функция вставки фрагмента ява-скрипта в скрипт
        //сохранения формы при редактировании контента с фронта.
        function addInWriteScript($field){

        }

        //отображение поля в админке в редактировании контента
        function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write") {
            $fieldname = $field->name;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
            $strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');
            $return ='';
            $mainframe = mosMainFrame::getInstance();
			$mainframe->addJS(JPATH_SITE.'/includes/js/joomla.javascript.js');
            mosCommonHTML::loadCalendar();
             if (($mode == "write") && ($field->required == 1)) {
                    $class = "class='boss_required' mosReq='1' mosLabel='" . $strtitle . "'";
                    $return .= "<input $class type='text' name='" . $field->name . "' id='" . $field->name . "' size='25' maxlength='19' value='" . $value . "' readonly='true' />";
                    $return .= "<span class='button'><input name='reset' type='reset' class='button' onclick=\"return showCalendar('" . $field->name . "');\" value='...' /></span>";
                }
                else if($mode == "search"){
                    $class = "class='boss'";
                    $return .= "<input $class type='text' name='" . $field->name . "_from' id='" . $field->name . "_from' size='25' maxlength='19' value='" . $value . "' readonly='true' />";
                    $return .= "<span class='button'><input name='reset' type='reset' class='button' onclick=\"return showCalendar('" . $field->name . "_from');\" value='...' /></span>";

                    $return .= "<input $class type='text' name='" . $field->name . "_to' id='" . $field->name . "_to' size='25' maxlength='19' value='" . $value . "' readonly='true' />";
                    $return .= "<span class='button'><input name='reset' type='reset' class='button' onclick=\"return showCalendar('" . $field->name . "_to');\" value='...' /></span>";
                }
                else  {
                    $class = "class='boss'";
                    $return .= "<input $class type='text' name='" . $field->name . "' id='" . $field->name . "' size='25' maxlength='19' value='" . $value . "' readonly='true' />";
                    $return .= "<span class='button'><input name='reset' type='reset' class='button' onclick=\"return showCalendar('" . $field->name . "');\" value='...' /></span>";
                }
            return $return;
        }

        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $return = mosGetParam($_POST, $field->name, "");
            return $return;
        }

        function onDelete($directory, $contentid = -1) {
            return;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues) {
            $return = "";
            return $return;
        }

        //действия при сохранении настроек поля
        function saveFieldOptions($directory, $field) {
            //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
            //иначе true
            return false;
        }

        //расположение иконки плагина начиная со слеша от корня сайта
        function getFieldIcon($directory) {
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/date.png";
        }

        //действия при установке плагина
        function install($directory) {
            return;
        }

        //действия при удалении плагина
        function uninstall($directory) {
            return;
        }

        //действия при поиске
        function search($directory, $fieldName) {
            $search = '';
            $from   = mosGetParam( $_REQUEST, $fieldName.'_from', "" );
            $to     = mosGetParam( $_REQUEST, $fieldName.'_to', "" );
			if($from != "")
				$search .= " AND a.$fieldName >= '$from'";
            if($to != "")
				$search .= " AND a.$fieldName <= '$to'";
            return $search;
        }
    }
?>