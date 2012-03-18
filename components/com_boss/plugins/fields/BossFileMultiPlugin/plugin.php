<?php

/**
 * @BOSS - Плагин файлов
 * @version 1.0.2
 * @author: Joostina! Project <joostinacms@gmail.com>
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
 
//запрет прямого доступа  
defined('_VALID_MOS') or die();

//подгружаем языковой файл плагина
boss_helpers::loadBossPluginLang($directory, 'fields', 'BossFileMultiPlugin');
		
	class BossFileMultiPlugin {
        
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'File (Muliple)';
        
        //тип плагина для записи в таблицы
        var $type = 'BossFileMultiPlugin';
        
        //скрипты и стили в голову, которые не кешируются
        function addInHead($field, $field_values)
        {
            $params = array();
            $params['css'] = JPATH_SITE.'/images/boss/' . mosGetParam($_REQUEST, 'directory') . '/plugins/fields/BossFilePlugin/css/plugin.css';
            return $params;
        }
				
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
            if ($value != "") {
                $value = json_decode($value, 1);
                if(!empty($field->text_before))
                    $return .= '<span>'.$field->text_before.'</span>';
                if(!empty($field->tags_open))
                    $return .= html_entity_decode($field->tags_open);				
                $return .= '<div class="boss_files">';

                if(is_array($value) && count($value)>0){
                    foreach($value as $row){
                        $row['counter'] = (!empty($row['counter'])) ? $row['counter'] : 0;
                        $dataArray[] = '<div class="boss_file">'
                                . '<span class="boss_file_desc">'.$row['signature'].'</span> &nbsp;&nbsp;'
				        		.  self::displayFileLink($directory, $content, $field, $field_values, $row['file'], 'joostfree', $row['counter'], 'front')
				        		.  '</div>';
				    }
                }
                $return .= implode( html_entity_decode($field->tags_separator), $dataArray);
                $return .= '</div>';
                if(!empty($field->tags_close))
                    $return .= html_entity_decode($field->tags_close);
                if(!empty($field->text_after))
                    $return .= '<span>'.$field->text_after.'</span>';
            }

            return $return;
        }
		
		//отображение ссылки на скачивание 
        private function displayFileLink($directory, $content, $field, $field_values, $filename, $template, $downloads=0, $type = "admin") {

            $mainframe = mosMainFrame::getInstance();;
            if($mainframe->isAdmin()){
                $fv = $field_values[$field->fieldid];
            }
            else{
                $fv = $field_values;
            }

            $fieldname = $field->name;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';

            $fValuers = array();
            foreach($fv as $field_value){
                $fValuers[$field_value->fieldtitle] = $field_value->fieldvalue;
            }
            $counter = (!empty($fValuers['counter'])) ? $fValuers['counter'] : 0;

			$date_created = (isset ($content->date_created)) ? ', <span class="boss_file_date">'. Jstring::strtolower(BOSS_DATE) . ':&nbsp;<span>' . mosFormatDate($content->date_created) . '</span></span>' : '';
            $ext = explode('.', $filename);
            $ext = Jstring::strtolower($ext[(count($ext)-1)]);
			$size = round(@filesize(JPATH_BASE. "/images/boss/" . $directory . "/files/" . $filename)/1024,2);
			$size = $size ? '<span>' . $size .'</span>Кб</span>' : '';
						
            switch ($ext) {
                case 'zip':
                case 'rar':
                case '7z':
                case 'gz':
                case 'bz':
                    $image = 'compress.png';
                    break;

                case 'xls':
                case 'xlt':
                case 'xlsx':
                    $image = 'excel.png';
                    break;

                case 'doc':
                case 'docx':
                case 'odt':
                    $image = 'word.png';
                    break;

                case 'txt':
                    $image = 'txt.png';
                    break;
					
				case 'pdf':
                    $image = 'pdf.png';
                    break;	

                default:
                    $image = 'file.png';
                    break;
            }
			
            $return = '';
            if ($filename) {
				if ($type == "front") {//отображение ссылки на фронте
                    if($counter){
                        $url = sefRelToAbs('ajax.index.php?option=com_boss&act=plugins&task=run_plugins_func&directory='.$directory.'&class=BossFileMultiPlugin&function=download&file='.$filename.'&cid='.$content->id.'&fname='.$fieldname);
                        $counterPrint = '['.BOSS_PLG_COUNTER.' '.$downloads.']';
                    }
                    else{
                        $url = JPATH_SITE . "/images/boss/" . $directory . "/files/" . $filename;
                        $counterPrint = '';
                    }
					$return .= "<img src=\"" . JPATH_SITE . "/administrator/templates/" .$template. "/images/file_ico/" . $image . "\" alt=\"".$ext."\" align=\"middle\" border=\"0\" />" 
							. "&nbsp;" . $filename . "&nbsp;"
							. "[&nbsp;<a href=\"" . $url . "\" target=\"_blank\">" . BOSS_DOWNLOAD_FILE . "</a>&nbsp;]" . $counterPrint
							. "<span class='boss_file_info'> &mdash;&nbsp;" . Jstring::strtolower(BOSS_FIELD_SIZE) . "&nbsp;<span class='boss_file_size'>" . $size . $date_created.'</span>';
					}
				else {//отображение ссылки в админке
					$return .= "<img src=\"" . JPATH_SITE . "/administrator/templates/" .$template. "/images/file_ico/" . $image . "\" alt=\"".$ext."\" align=\"middle\" border=\"0\" />&nbsp;" 
							.  "<a title='" . BOSS_DOWNLOAD_FILE . "' href=\"" . JPATH_SITE . "/images/boss/" . $directory . "/files/" . $filename . "\" target=\"_blank\">"
							.  $filename .  "</a> <span class='filesize'>&mdash;&nbsp;<span>" . $size .'</span></span>';
				}
            }

            return $return;
        }

        //отображение поля в админке в редактировании контента
        function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write") {
            mosCommonHTML::loadJquery();
            $mainframe = mosMainFrame::getInstance();
            $mainframe->addJS(JPATH_SITE.'/administrator/components/com_boss/js/upload.js');
            $mainframe->addJS(JPATH_SITE.'/images/boss/'.$directory.'/plugins/fields/BossFileMultiPlugin/js/script.js');
            $mainframe->addCSS(JPATH_SITE.'/images/boss/' . $directory . '/plugins/fields/BossFileMultiPlugin/css/plugin.css');

            $fieldname = $field->name;

            $isAdmin = ($mainframe->isAdmin() == 1) ? 1 : 0;

            $fValuers = array();
            foreach($field_values[$field->fieldid] as $field_value){
                $fValuers[$field_value->fieldtitle] = $field_value->fieldvalue;
            }

            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
            $value = (!empty($value)) ? json_decode($value, 1): '';
            $strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');

            $mosReq = (($mode == "write") && ($field->required == 1)) ? " mosReq='1' " : '';
            $read_only = (($mode == "write") && ($field->editable == 0)) ?  " readonly=true " : '';
            $class = (($mode == "write") && ($field->required == 1)) ? "boss_required" : 'boss';

            $nb_files = (!empty($fValuers['nb_files'])) ? $fValuers['nb_files'] : 0;
            $counter = (!empty($fValuers['counter'])) ? $fValuers['counter'] : 0;
            $enable_files = (!empty($fValuers['enable_files'])) ? implode("', '", explode(',', $fValuers['enable_files'])) : 'all';
            $return = '';
            $return .= "
                <script type=\"text/javascript\">
		            var boss_nb_files = ".(int)$nb_files.";
		            var boss_enable_files = new Array('".$enable_files."');
		            var boss_isadmin = ".$isAdmin.";
                </script>

                <div id='boss_plugin_file'>
                    <input id='upload' type=button value='".BOSS_PLG_FM_UPLOAD."' style='float: left;'/>
			        <div id='status'></div>
			        <br style='clear: both;' />
			        <div id='files'>
                    ";

            if (!empty($value)) {
                foreach($value as $i => $row){

                    $return .= "
                        <div id='file_".$i."'>
                        <label>".BOSS_PLG_DESC." </label>
                        <input type='text' size='40'
                            name='boss_file[".$i."][signature]' class='inputbox boss_file' value='".$row['signature']."' />";

                    if($counter){
                        $row['counter'] = (!empty($row['counter'])) ? $row['counter'] : 0;
                        $return .= "
                        <label>".BOSS_PLG_COUNTER." </label>
                        <input type='text' size='3' readonly='true'
                            name='boss_file[".$i."][counter]' class='inputbox boss_file' value='".$row['counter']."' />";
                    }

                    $return .= "
                        <input type='hidden' name='boss_file[".$i."][file]' value='".$row['file']."' />
                            &nbsp;&nbsp;&nbsp;"
                        .self::displayFileLink($directory, $content, $field, $field_values, $row['file'], JTEMPLATE, 0, 'admin')
                        . "&nbsp;&nbsp;<input type='button' value='X' class='button' onclick='bossDeleteFile(\"".$row['file']."\", \"file_".$i."\")' />
                    </div>";
                }

            }
			$return .= "</div>";
            return $return;
        }

        //функция вставки фрагмента ява-скрипта в скрипт
        //сохранения формы при редактировании контента с фронта.
        function addInWriteScript($field){

        }

        //действия при сохранении контента
        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $boss_file = mosGetParam($_REQUEST, 'boss_file', '');
            $boss_file = boss_helpers::json_encode_cyr($boss_file);
            return $boss_file;
        }
		
		//функция транслитерации и замены пробелов в названии файла
		private function tranform($str) {
                    
			$str = russian_transliterate($str);
						
			$maxchars = 70; //макс. кол-во символов
			
			if (Jstring::strlen ($str) > $maxchars) { //если длина названия превышает макс. кол-во символов
				
				//вычленяем из название расширение файла
				$ext = explode('.', $str);
				$ext = $ext[(count($ext)-1)];
				
				$length = strripos(Jstring::substr($str, 0, $maxchars), '_'); //ищем позицию последнего подчеркивания в названии
				$length = $length ? $length : $maxchars; //если нет подчеркиваний обрезаем по макс. кол-ву символов ($maxchars)
				$str = Jstring::substr($str, 0, $length) . '.' . $ext; //обрезаем по позиции найденного подчеркивания или по макс. кол-ву символов
			}
			return $str;
		}

        function onDelete($directory, $content) {
            $database = database::getInstance();
            $contents = null;
            $database->setQuery("SELECT * FROM #__boss_" . $directory . "_contents WHERE `id` = '".$content->id."'");
            $database->loadObject($contents);
            $database->setQuery("SELECT name FROM #__boss_" . $directory . "_fields WHERE `type` = '".$this->type."'");
            $file_fields = $database->loadObjectList();
            if(is_array($file_fields) && count($file_fields)>0){
                foreach ($file_fields as $file_field) {
                    $fileFieldName = $file_field->name;
                    $files = json_decode($contents->$fileFieldName);
                    if(is_array($files) && count($files)>0){
                         foreach ($files as $file){
                             @unlink(JPATH_BASE . "/images/boss/$directory/files/" . $file->file);
                         }
                    }
                }
            }
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues)
        {
            $return = '
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td>'.BOSS_PLG_NB_FILES.'</td>
                    <td><input type="text" name="nb_files" id="nb_files" value="'.@$fieldvalues['nb_files']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_PLG_NB_FILES_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_PLG_ENABLE_EXT.'</td>
                    <td><input type="text" name="enable_files" id="enable_files" value="'.@$fieldvalues['enable_files']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_PLG_ENABLE_EXT_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_PLG_ENABLE_COUNTER.'</td>
                    <td>'.mosHTML::yesnoRadioList('counter','class="inputbox"',@$fieldvalues['counter']->fieldvalue).'</td>
                    <td>'.boss_helpers::bossToolTip(BOSS_PLG_ENABLE_COUNTER_LONG).'</td>
                </tr>
            </table>';
            $return .= BOSS_PLG_NB_FILES_DESC.'
                <a href="#" id="filesize" onClick="setFileSizeFocus();">'. BOSS_PLG_NB_FILES_DESC_1 .'</a>
                '. BOSS_PLG_NB_FILES_DESC_2;
            $return .= "
			<script language=\"javascript\" type=\"text/javascript\">
				function setFileSizeFocus () {					
					jQuery('input[name=size]').focus().css('borderColor','red').css('color','red');
				}
			</script>
			";
			return $return;
        }

        //действия при сохранении настроек поля
        function saveFieldOptions($directory, $field) {
            $fieldId = $field->fieldid;
            $database = database::getInstance();
            $nb_files = mosGetParam($_POST, "nb_files", 0);
            $enable_files = str_replace(' ', '', mosGetParam($_POST, "enable_files", ''));
            $counter = mosGetParam($_POST, "counter", 0);
            $database->setQuery("DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = '" . $fieldId . "' ");
            $database->query();
            $database->setQuery("INSERT INTO #__boss_" . $directory . "_field_values
    		                    (fieldid, fieldtitle, fieldvalue, ordering, sys)
    		                    VALUES
    		                    ($fieldId,'nb_files',       '$nb_files',        1,0),
    		                    ($fieldId,'enable_files',   '$enable_files',    2,0),
    		                    ($fieldId,'counter',   '$counter',    3,0)
    		                    ");
            $database->query();
            //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
            //иначе true
            return false;
        }

        //расположение иконки плагина начиная со слеша от корня сайта
        function getFieldIcon($directory) {
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/image_add.png";
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

        function download(){

            $directory = mosGetParam($_REQUEST, 'directory', 0);
            $file = mosGetParam($_REQUEST, 'file', '');
            $fname = mosGetParam($_REQUEST, 'fname', '');
            $cid = mosGetParam($_REQUEST, 'cid', 0);

            $database = database::getInstance();
            $database->setQuery("SELECT ".$fname." FROM #__boss_" . $directory . "_contents WHERE `id` = '".$cid."'");
            $field = $database->loadResult();

            if(!empty($field)){
                $field = json_decode($field, 1);

                $newVal = array();
                foreach($field as $f){
                    if($f['file'] == $file){
                       $f['counter'] = (!empty($f['counter'])) ? $f['counter'] + 1 : 1;
                    }
                    $newVal[] = $f;
                }
                $newVal = boss_helpers::json_encode_cyr($newVal);

                $database->setQuery("UPDATE #__boss_" . $directory . "_contents SET ".$fname." = '$newVal' WHERE `id` = '".$cid."'");
                $database->query();
            }
            mosRedirect(JPATH_SITE . "/images/boss/" . $directory . "/files/" . $file);
        }
    }

?>