<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

    class BossTextFieldPlugin {
        
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'Text Field';
        
        //тип плагина для записи в таблицы
        var $type = 'BossTextFieldPlugin';
        
        //отображение поля в категории
        function getListDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            return $this->getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf);
        }

        //отображение поля в контенте
        function getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            $fieldname = $field->name;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
            if(!$value)
                return false;

            if($conf->use_content_mambot == 1){
                global $_MAMBOTS;
                $_MAMBOTS->loadBotGroup( 'content' );
                $params = new mosParameters('');
                $row = null;
                $row->text = $value;
                $_MAMBOTS->trigger( 'onPrepareContent', array( &$row, &$params, 0 ), true );
                $content->$fieldname = $value;
            }

            $return = '';
            if(!empty($field->text_before))
                $return .= '<span>'.$field->text_before.'</span>';
            if(!empty($field->tags_open))
                $return .= html_entity_decode($field->tags_open);

            $return .= $value;

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

            $read_only = (($mode == "write") && ($field->editable == 0)) ?  " readonly=true " : '';
            $req = (($mode == "write") && ($field->required == 1)) ? " class='boss_required' mosReq='1' " : " class='boss' ";

            $return = "<input $req id='" . $field->name . "' type='text' mosLabel='" . $strtitle . "' name='" . $field->name . "' size='$field->size' maxlength='$field->maxlength' $read_only value='" . htmlspecialchars($value, ENT_QUOTES) . "' />\n";

            return $return;
        }

        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $return = mosGetParam($_POST, $field->name, "");
            return $return;
        }

        function onDelete($directory, $contentid = -1) {
            return;
        }

        function getEditFieldJavaScriptDisable() {
            return;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues) {
            $return =  '
            <div id="divTextLength">
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%">'.BOSS_FIELD_MAX_LENGTH.'</td> ';

                        if (!isset($row->maxlength) || ($row->maxlength == ""))
                    $row->maxlength = 20;
                    $return .=  '
                    <td width="20%"><input type="text" name="maxlength" mosLabel="Max Length" class="inputbox"
                                           value="'.$row->maxlength.'"/></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        ';
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
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/textfield.png";
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