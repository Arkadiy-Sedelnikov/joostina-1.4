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
		
	class BossFilePlugin {
        
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'File';
        
        //тип плагина для записи в таблицы
        var $type = 'BossFilePlugin';
        
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

            $return = '';
            if ($value != "") {
                if(!empty($field->text_before))
                    $return .= '<span>'.$field->text_before.'</span>';
                if(!empty($field->tags_open))
                    $return .= html_entity_decode($field->tags_open);				

                $return .= '<div class="boss_file">' 
						.  self::displayFileLink($directory, $content, $field, $field_values, 'joostfree', 'front') 
						.  '</div>';
				
                if(!empty($field->tags_close))
                    $return .= html_entity_decode($field->tags_close);
                if(!empty($field->text_after))
                    $return .= '<span>'.$field->text_after.'</span>';
            }

            return $return;
        }
		
		//отображение ссылки на скачивание 
        function displayFileLink($directory, $content, $field, $field_values, $template, $type = "admin") {
            $fieldname = $field->name;			
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
			$date_created = (isset ($content->date_created)) ? ', <span class="boss_file_date">'. Jstring::strtolower(BOSS_DATE) . ':&nbsp;<span>' . mosFormatDate($content->date_created) . '</span></span>' : '';
            $ext = explode('.', $value);
            $ext = Jstring::strtolower($ext[(count($ext)-1)]);
			$size = round(filesize(JPATH_BASE. "/images/boss/" . $directory . "/files/" . $value)/1024,2);
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
            if ($value) {
				if ($type == "front") {//отображение ссылки на фронте
					$return .= "<img src=\"" . JPATH_SITE . "/administrator/templates/" .$template. "/images/file_ico/" . $image . "\" alt=\"".$ext."\" align=\"middle\" border=\"0\" />" 
							. "&nbsp;" . $value . "&nbsp;"
							. "[&nbsp;<a href=\"" . JPATH_SITE . "/images/boss/" . $directory . "/files/" . $value . "\" target=\"_blank\">" . BOSS_DOWNLOAD_FILE . "</a>&nbsp;]"
							. "<span class='boss_file_info'> &mdash;&nbsp;" . Jstring::strtolower(BOSS_FIELD_SIZE) . "&nbsp;<span class='boss_file_size'>" . $size . $date_created.'</span>';
					}
				else {//отображение ссылки в админке
					$return .= "<img src=\"" . JPATH_SITE . "/administrator/templates/" .$template. "/images/file_ico/" . $image . "\" alt=\"".$ext."\" align=\"middle\" border=\"0\" />&nbsp;" 
							.  "<a title='" . BOSS_DOWNLOAD_FILE . "' href=\"" . JPATH_SITE . "/images/boss/" . $directory . "/files/" . $value . "\" target=\"_blank\">"						
							.  $value .  "</a> <span class='filesize'>&mdash;&nbsp;<span>" . $size .'</span></span>';
				}
            }

            return $return;
        }

        //отображение поля в админке в редактировании контента
        function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write") {
            $fieldname = $field->name;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';            
            $strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');

            $mosReq = (($mode == "write") && ($field->required == 1)) ? " mosReq='1' " : '';
            $read_only = (($mode == "write") && ($field->editable == 0)) ?  " readonly=true " : '';
            $class = (($mode == "write") && ($field->required == 1)) ? "boss_required" : 'boss';

			$return = '';
            $return .= "<div class='boss_plugin_file'><table><tr>";
			$return .= "<td><input class='".$class."' id='" . $field->name . "' type='file' name='" . $field->name . "' mosLabel='" . $strtitle . "' " . $mosReq . $read_only . " /></td>";
			
            if (isset($value) && ($value != "")) {
                $return .= "<td>&nbsp;&nbsp;&nbsp;".self::displayFileLink($directory, $content, $field, $field_values, JTEMPLATE, 'admin') . "</td>";                
            }
			$return .= "</tr></table></div>";
            return $return;
        }

        //функция вставки фрагмента ява-скрипта в скрипт
        //сохранения формы при редактировании контента с фронта.
        function addInWriteScript($field){

        }

        //действия при сохранении контента
        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $database = database::getInstance();
            
            $contentId = mosGetParam($_REQUEST, 'id', 0);

            $q = "SELECT " . $field->name . " FROM #__boss_" . $directory . "_contents WHERE id = " . $contentId;
            $database->setQuery($q);
            $value = $database->loadResult();

            if (isset($_FILES[$field->name]) and !$_FILES[$field->name]['error']) {
                //если файл больше, указанного в настройках размера, закругляемся
                if ($_FILES[$field->name]['size'] > $field->size) {
                    return false;
                }

                @unlink(JPATH_BASE . "/images/boss/$directory/files/" . $value );
                			
				$filename = self::tranform($_FILES[$field->name]['name']);
				
                //если файл с таким названием уже существует, делаем добавляем уникальный префикс
				while (file_exists(JPATH_BASE . "/images/boss/$directory/files/" . $filename)) {
                    $filename = uniqid("copy_") . "_" . $filename;
                }

                @move_uploaded_file($_FILES[$field->name]['tmp_name'],
									JPATH_BASE . "/images/boss/$directory/files/" . $filename);
                $queryArray[] = " $field->name = '" . $filename . "' ";
                $value = ($_FILES[$field->name]['size'] > $field->size) ? '' : $filename;				
            }
            return $value;
        }
		
		//функция транслитерации и замены пробелов в названии файла
		function tranform($str) {
                    
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
            foreach ($file_fields as $file_field) {
                $fileFieldName = $file_field->name;
                $filename = $contents->$fileFieldName;
                @unlink(JPATH_BASE . "/images/boss/$directory/files/" . $filename);
            }
            return;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues)
        {
            $return = '<div id="divFileOptions"><strong>Внимание!</strong> Необходимо задать максимальный размер файла в байтах в поле &laquo;<a href="#" id="filesize" onClick="setFileSizeFocus();">Размер</a>&raquo;. <br/><br/>1 Мегабайт = 1 024 000 байта<br/>1 Килобайт = 1 024 байт<br/><br/></div>';
            ?>
			<script language="javascript" type="text/javascript">		
				function setFileSizeFocus () {					
					jQuery('input[name=size]').focus().css('borderColor','red').css('color','red');
				}
			</script>
			<?php
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
    }

?>