<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

    class BossEmailPlugin {
        
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'Email Address';
        
        //тип плагина для записи в таблицы
        var $type = 'BossEmailPlugin';
	
        //отображение поля в категории
        function getListDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            return $this->getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf);
        }

        //отображение поля в контенте
        function getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            $database = database::getInstance();
            $fieldname = $field->name;
            $fieldid = $field->fieldid;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';

            $return = '';
            if(!empty($field->text_before))
                $return .= '<span>'.$field->text_before.'</span>';
            if(!empty($field->tags_open))
                $return .= html_entity_decode($field->tags_open);

            $database->setQuery("SELECT `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = '$fieldid' LIMIT 1");
            $config = $database->loadResult();
            if ($value != "") {
                switch ($config) {
                    case 2:
                        $emailForm = sefRelToAbs("index.php?option=com_boss&amp;task=show_message_form&amp;mode=0&amp;contentid=" . $content->id . "&amp;directory=$directory&amp;Itemid=" . $itemid);
                        $return .= '<a href="' . $emailForm . '">' . BOSS_EMAIL_FORM . '</a>';
                        break;
                    case 1:
                        $return .= Txt2Png($value, $directory);
                        break;
                    default:
                        $return .= "<a href='mailto:" . $value . "'>" . cutLongWord($value, $field->maxlength) . "</a>";
                        break;
                }
            }

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

            $mosReq = (($mode == "write") && ($field->required == 1)) ? " mosReq='1' " : '';
            $read_only = (($mode == "write") && ($field->editable == 0)) ?  " readonly=true " : '';
            $class = (($mode == "write") && ($field->required == 1)) ? "boss_required" : 'boss';

            $return = "<input class='$class' id='" . $field->name . "' type='text' test='emailaddress' mosLabel='" . $strtitle . "' name='" . $field->name . "' size='$field->size' maxlength='$field->maxlength' $read_only $mosReq value='$value' />\n";

            return $return;
        }

        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $return = mosGetParam($_POST, $field->name, "");
            return $return;
        }

        function onDelete($directory, $content) {
            return;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues) {
            $fieldid = $row->fieldid;
            $database = database::getInstance();
            $row = null;
            $database->setQuery("SELECT `fieldtitle`, `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = '$fieldid'");
            $row = $database->loadObjectList('fieldtitle');
            $return = "<div id='divEmailOptions'>\n";
            $return .= "\t<table class='adminform'>\n";
            $return .= "\t\t<tr>\n";
            $return .= "\t\t\t<td width='20%'>".BOSS_EMAIL_DISPLAY."</td>\n";
            $return .= "\t\t\t<td width='20%' align=left>\n";
            $return .= "\t\t\t\t<select id='email_display' name='email_display' mosReq=1 mosLabel='".BOSS_EMAIL_DISPLAY."'>\n";
            $selected = (@$row['email_display']->fieldvalue == 2) ? 'selected="selected"' : '';
            $return .= "\t\t\t\t\t<option value='2' ".$selected.">".BOSS_EMAIL_DISPLAY_FORM."</option>\n";
            $selected = (@$row['email_display']->fieldvalue == 1) ? 'selected="selected"' : '';
            $return .= "\t\t\t\t\t<option value='1' ".$selected.">".BOSS_EMAIL_DISPLAY_IMAGE."</option>\n";
            $selected = (@$row['email_display']->fieldvalue == 0) ? 'selected="selected"' : '';
            $return .= "\t\t\t\t\t<option value='0' ".$selected.">".BOSS_EMAIL_DISPLAY_LINK."</option>\n";
            $return .= "\t\t\t\t</select>\n";
            $return .= "\t\t\t<td>".BOSS_EMAIL_DISPLAY_LONG."</td>\n";
            $return .= "\t\t</tr>\n";
            $return .= "\t</table>\n";
            $return .= "</div>\n";
            return $return;
        }

        //действия при сохранении настроек поля
        function saveFieldOptions($directory, $field) {
            $fieldId = $field->fieldid;
            $database = database::getInstance();
            $email_display = mosGetParam($_POST, "email_display", 0);
            $database->setQuery("DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = '" . $fieldId . "' ");
            $database->query();
            $database->setQuery("INSERT INTO #__boss_" . $directory . "_field_values
    		                    (fieldid, fieldtitle, fieldvalue, ordering, sys)
    		                    VALUES
    		                    ($fieldId,'email_display', '$email_display', 1,0)
    		                    ");
            $database->query();
            //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
            //иначе true
            return false;
        }
        
        //расположение иконки плагина начиная со слеша от корня сайта
        function getFieldIcon($directory) {
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/email.png";
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
        function search($directory,$fieldName) {
            $search = '';
            $value = mosGetParam( $_REQUEST, $fieldName, "" );
					if ($value != "") {
						$search .= " AND a.$fieldName LIKE '%$value%'";
					}
            return $search;
        }
    }
?>