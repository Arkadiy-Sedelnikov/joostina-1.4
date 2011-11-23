<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

    class BossCheckboxMulitiImgPlugin {
        
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'Check Box (Muliple Images)';
        
        //тип плагина для записи в таблицы
        var $type = 'BossCheckboxMulitiImgPlugin';
        
        //отображение поля в категории
        function getListDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            return $this->getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf);
        }

        //отображение поля в контенте
        function getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            $fieldname = $field->name;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
            $dataArray = array();
            $return = '';
            if(!empty($field->text_before))
                $return .= '<span>'.$field->text_before.'</span>';
            if(!empty($field->tags_open))
                $return .= html_entity_decode($field->tags_open);

            for ($i = 0, $nb = count($field_values); $i < $nb; $i++) {
                $fieldvalue = @$field_values[$i]->fieldvalue;
                $fieldtitle = @$field_values[$i]->fieldtitle;

                if (strpos($value, $fieldvalue) !== false) {
                    $dataArray[] = "<img src='" . JPATH_SITE . "/images/boss/$directory/fields/" . $fieldtitle . "' alt='$fieldtitle' />";
                }
            }

            $return .= implode( html_entity_decode($field->tags_separator), $dataArray);
            
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
            $k = 0;
            $return = "<table>";
            for ($i = 0; $i < $field->rows; $i++) {
                    $return .= "<tr>";
                    for ($j = 0; $j < $field->cols; $j++) {

                        $fieldvalue = @$field_values[$field->fieldid][$k]->fieldvalue;
                        $fieldtitle = @$field_values[$field->fieldid][$k]->fieldtitle;
                        if ($field->type == 'multicheckbox') {
                            if (isset($fieldtitle))
                                $fieldtitle = jdGetLangDefinition($fieldtitle);
                        }
                        else {
                            $fieldtitle = "<img src=\"" . JPATH_SITE . "/images/boss/" . $directory . "/fields/" . $fieldtitle . "\" alt='$fieldtitle' />";
                        }

                        $mosReq = '';
                        $checked = '';

                        if (($mode == "write") && ($field->required == 1) && ($k == 0))
                            $mosReq = "mosReq='1'";

                        if (!($mode == "write" && strpos($value, $fieldvalue) === false))
                            $checked = 'checked="checked"';

                        $return .= "<td>";
                        if(!empty($fieldvalue))
                            $return .= "<input class='inputbox' type='checkbox' mosLabel='" . $strtitle . "' name='" . $field->name . "[]' value='$fieldvalue' $mosReq $checked />&nbsp;$fieldtitle&nbsp;\n";
                        $return .= "</td>";

                        $k++;
                    }
                    $return .= "</tr>";
                }
            $return .= "</table>";

            return $return;
        }

        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $return = mosGetParam($_POST, $field->name, array());
            $return = "," . implode(',', $return) . ",";
            return $return;
        }

        function onDelete($directory, $contentid = -1) {
            return;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues) {

            $mainframe = mosMainFrame::getInstance();
            $mainframe->addJS(JPATH_SITE.'/administrator/components/com_boss/js/upload.js');
            $mainframe->addJS(JPATH_SITE.'/images/boss/'.$directory.'/plugins/fields/BossCheckboxMulitiImgPlugin/js/script.js');

            $img = '';
            if (isset($fieldimages)) {
                foreach ($fieldimages as $image) {
                    $img .= '
			    			k++;
			    			oSelect.length++;
			    			oSelect.options[k].text = "'.$image.'";
			    			oSelect.options[k].value = "'.$image.'";
			    	        ';

                }
            }
            $return = '
        <script type="text/javascript">
            function getSelectedValue(obj) {
                var i = obj.selectedIndex;
                if (i != null && i > -1) {
                    return obj.options[i].value;
                } else {
                    return null;
                }
            }

            function showimage(preview, obj) {
                if (getSelectedValue(obj) == "null" || !getSelectedValue(obj))
                    var imgPath = "'.JPATH_SITE.'/templates/com_boss/default/images/nopic.gif";
                else
                    imgPath = "'.JPATH_SITE.'/images/boss/'.$directory.'/fields/" + getSelectedValue(obj);
                var img = getObject(preview);
                img.src = imgPath;
            }

            function insertImageRow() {
            var oTable = getObject("ImagesfieldValuesBody");
            var oRow, oCell;
            var oCell2, oInput2,oImage,oSelect;
            var i, k;
            i = document.fieldForm.ImagevalueCount.value;
            i++;
            // Create and insert rows and cells into the first body.
            oRow = document.createElement("tr");
            oTable.appendChild(oRow);

            oCell = document.createElement("td");
            oSelect = document.createElement("select");
            oSelect.onchange = function() {
                showimage("preview" + i, this);
            };
            oSelect.id = "vSelectImages[" + i + "]";
            oSelect.name = "vSelectImages[" + i + "]";
            oSelect.setAttribute("class", "img_select");
            k = 0;
            oSelect.length++;
            oSelect.options[0].text = "No Image";
            oSelect.options[0].value = "null";
            '.$img.'
            oCell.appendChild(oSelect);
            oImage = document.createElement("img");
            oImage.setAttribute("src", "' . JPATH_SITE . '/images/boss/' . $directory . '/fields/' . $row->link_image .'");
            oImage.setAttribute("id", "preview" + i);
            oImage.setAttribute("name", "preview" + i);
            oCell.appendChild(oImage);
            oCell2 = document.createElement("td");
            oInput2 = document.createElement("input");
            oInput2.name = "vImagesValues[" + i + "]";
            oInput2.setAttribute("mosLabel", "Value");
            oInput2.setAttribute("mosReq", 0);
            oCell2.appendChild(oInput2);

            oRow.appendChild(oCell);
            oRow.appendChild(oCell2);
            oSelect.focus();

            document.fieldForm.ImagevalueCount.value = i;
        }
        </script>
        <div id=divColsRows>
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%">'.BOSS_FIELD_COLS.'</td>
                    <td width="20%"><input type="text" name="cols" mosLabel="Cols" class="inputbox"
                                           value="'.$row->cols.'"/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%">'.BOSS_FIELD_ROWS.'</td>
                    <td width="20%"><input type="text" name="rows" mosLabel="Rows" class="inputbox"
                                           value="'.$row->rows.'"/></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <div id=divImagesValues style="text-align:left;">
            '.BOSS_IMAGE_FIELD_VALUES_EXPLANATION.'
            <input type=button onclick="insertImageRow();" value="'.BOSS_FIELD_ADD_VALUES.'"/>
            <input id="upload" type=button value="'.BOSS_FIELD_UPLOAD_FILE.'"/>
            <table align=left id="divImagesFieldValues" cellpadding="4" cellspacing="1" border="0" width="100%"
                   class="adminform">
                <tr>
                    <th width="20%">'.BOSS_FIELD_VALUE_IMAGE.'</th>
                    <th width="20%">'.BOSS_FIELD_VALUE_VALUE.'</th>
                </tr>
                <tbody id="ImagesfieldValuesBody">
                <tr>
                    <td colspan="2">
                        <div id="files" style="text-align:center;"></div>
                    </td>
                </tr>
                ';
                $j=0;
                if(count($fieldvalues) > 0){
                    foreach ($fieldvalues as $fieldvalue) {

                        $return .= '
                        <tr>
                            <td width="20%">
                                <select class="img_select" id="vSelectImages['.$j.']" mosLabel="Image" mosReq=0
                                        name="vSelectImages['.$j.']" onchange="showimage(\'preview'.$j.'\',this)">
                                    <option value="null" selected="selected">No Image</option>
                                ';
                                if (isset($fieldimages)) {
                                    foreach ($fieldimages as $image) {
                                        $return .= '
                                            <option value="'.$image.'"';
                                        if (stripslashes($fieldvalue->fieldtitle) == $image) {
                                                $return .= ' selected="selected" ';
                                            }
                                        $return .= '>'.$image.'</option>';
                                    }
                                }

                                $return .='
                                </select>
                                <img src="'.JPATH_SITE.'/images/boss/'.$directory.'/fields/' . stripslashes($fieldvalue->fieldtitle).'"
                                     id="preview'.$j.'" name="preview'.$j.'" alt="'.@$row->image.'"/>
                            </td>
                            <td width="20%">
                                <input type=text mosReq=0 mosLabel="Value"  value="'.stripslashes($fieldvalue->fieldvalue).'"
                                       name="vImagesValues['.$j.']" id="vImagesValues['.$j.']"/>
                            </td>
                        </tr>';

                    }
                }
                if ($j > 0)
                    $j--;
                if (count($fieldvalues) < 1) {
                    $return .= '
                    <tr>
                        <td width="20%">
                            <select class="img_select" id="vSelectImages[0]" name="vSelectImages[0]" mosReq=0
                                    mosLabel="Image"
                                    onchange="showimage(\'preview0\',this)">
                                <option value="null" selected="selected">No Image</option>';

                            if (isset($fieldimages)) {
                                foreach ($fieldimages as $image) {
                                    $return .= '
                                        <option value="'.$image.'"';
                                    if ($row->link_image == $image) {
                                            $return .= ' selected="selected" ';
                                    }
                                    $return .= '>'.$image.'</option>';


                                }
                            }
                            $return .= '
                            </select>
                            <img src="" id="preview0" name="preview0" alt="'.$row->link_image.'"/>
                        </td>
                        <td width="20%">
                            <input type=text mosReq=0 mosLabel="Value" value="" name="vImagesValues[0]"
                                   id="vImagesValues[0]"/>
                        </td>
                    </tr> ';

                                                    $j = 0;
                }
                $return .= '
                </tbody>
            </table>
        </div>
        <input type="hidden" name="ImagevalueCount" value="'.$j.'"/>
            ';
            return $return;
        }

        //действия при сохранении настроек поля
        function saveFieldOptions($directory, $field) {
            $database = database::getInstance();
            $fieldImagesSelect = $_POST['vSelectImages'];
	        $fieldImagesValues = $_POST['vImagesValues'];
            $j=0;
			$i=0;
            $values = array();
            
			while(isset($fieldImagesSelect[$i])) {
				$fieldName  = $fieldImagesSelect[$i];
				$fieldValue = $fieldImagesValues[$i];
				$i++;

				if(trim($fieldName)!=null && trim($fieldName)!='' && trim($fieldName)!='null') {
					$values[] = "('$field->fieldid','".htmlspecialchars($fieldName)."','".htmlspecialchars($fieldValue)."',$j)";
					$j++;
				}
			}

            $database->setQuery( "INSERT INTO #__boss_".$directory."_field_values "
                . "(fieldid,fieldtitle,fieldvalue,ordering)"
				. " VALUES"
                . implode(', ', $values)
            )->query();
            //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
            //иначе true
            return false;
        }

        //расположение иконки плагина начиная со слеша от корня сайта
        function getFieldIcon($directory) {
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/checkbox.png";
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
            $values = mosGetParam( $_REQUEST, $fieldName, array() );
            $search = '';
            $tmp = array();
            foreach($values as $value){
                $tmp[]= "FIND_IN_SET( '$value', a.$fieldName )>0";
            }

			if(is_array($values) && count($values)>0){
                $search = " AND ( ".implode(" OR ", $tmp)." ) ";
            }
            var_dump($search);
            return $search;
        }
    }
?>