<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

    class BossURLPlugin {
        
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'URL';
        
        //тип плагина для записи в таблицы
        var $type = 'BossURLPlugin';
       
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

            $return = '';
            if(!empty($field->text_before))
                $return .= '<span>'.$field->text_before.'</span>';
            if(!empty($field->tags_open))
                $return .= html_entity_decode($field->tags_open);

            $linkObj = '';
            if ((isset($field->link_image)) && (file_exists(JPATH_BASE . "/images/boss/$directory/fields/" . $field->link_image))) {
                $linkObj .= "<img src='" . JPATH_SITE . "/images/boss/$directory/fields/" . $field->link_image . "' />";
            }
            if ((isset($field->link_text)) && ($field->link_text != ""))  {
                $linkObj .= $field->link_text;
            }
            else {
                $linkObj .= $value;
            }

            if ($value != "") {
                $return .= "<a href='http://".$value."' target='_blank'>$linkObj</a>";
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

            $read_only = (($mode == "write") && ($field->editable == 0)) ?  " readonly=true " : '';
            $req = (($mode == "write") && ($field->required == 1)) ? " class='boss_required' mosReq='1' " : " class='boss' ";

            $return = "http://";
            $return .= "<input $req id='" . $field->name . "' type='text' mosLabel='" . $strtitle . "' name='" . $field->name . "' size='$field->size' maxlength='$field->maxlength' $read_only value='" . htmlspecialchars($value, ENT_QUOTES) . "' />\n";

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

        <div id=divLink>
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%">'.BOSS_LINK_TEXT.'</td>
                    <td width="20%">
                        <input type="text" name="link_text" mosLabel="Link Text" class="inputbox" value="'.$row->link_text.'"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%">'.BOSS_LINK_IMAGE.'</td>
                    <td width="20%">
                        <select id="link_image" mosLabel="Image" mosReq=0 name="link_image"
                                onchange="showimage(\'previewlink\',this)">
                            <option value="null" selected="selected">No Image</option> ';

                        if (isset($fieldimages)) {
                            foreach ($fieldimages as $image) {                                
                                $return .=  '<option value="'.$image.'" ';  
                                if ($row->link_image == $image) {
                                    $return .=  ' selected="selected" ';
                                }
                                $return .=  '>'.$image.'</option>';
                            }
                        }
                        $return .=  '
                    </select>

                    </td>
                    <td>
                        <img src="'.JPATH_SITE.'/images/boss/$directory/fields/' . $row->link_image.'"
                             id="previewlink" name="previewlink"/>
                    </td>
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
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/link.png";
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