<?php

/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */

defined('_JLINDEX') or die();

//подгружаем языковой файл плагина
boss_helpers::loadBossPluginLang($directory, 'fields', 'BossImageGalleryPlugin');

class BossImageGalleryPlugin{
	//имя типа поля в выпадающем списке в настройках поля
	var $name = 'Image Gallery';

	//тип плагина для записи в таблицы
	var $type = __CLASS__;

	// получение файла инициализации
	function getEngine($engine){
		require_once (dirname(__FILE__) . DS . 'engines' . DS . $engine . DS . $engine . '.php');
	}

	//отображение поля в категории
	function getListDisplay($directory, $content, $field, $field_values, $conf){
		$conf_fields = self::fvalues($field_values);
		$engine = (!empty($conf_fields['galleryScript'])) ? $conf_fields['galleryScript'] : 'mbGallery';
		$fieldname = $field->name;
		$value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
		$images = (!empty($value)) ? json_decode($value, 1) : '';

		//если нет изображений - выходим
		if(!is_array($images) || count($images) == 0) return '';

		//загружаем файл инциализации скрипта галереи
		self::getEngine($engine);
		galleryListDisplay($directory, $content, $images, $conf_fields, $conf);
		return;
	}

	//отображение поля в содержимом
	function getDetailsDisplay($directory, $content, $field, $field_values, $conf){
		$conf_fields = self::fvalues($field_values);
		$engine = (!empty($conf_fields['galleryScript'])) ? $conf_fields['galleryScript'] : 'mbGallery';
		$fieldname = $field->name;
		$value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
		$images = (!empty($value)) ? json_decode($value, 1) : '';

		if(!is_array($images) || count($images) == 0) return '';

		//загружаем файл инциализации скрипта галереи
		self::getEngine($engine);
		galleryDetailsDisplay($directory, $content, $images, $conf_fields, $conf);

		return;
	}

	//функция вставки фрагмента ява-скрипта в скрипт
	//сохранения формы при редактировании контента с фронта.
	function addInWriteScript($field){
	}

	//отображение поля в админке в редактировании контента
	function getFormDisplay($directory, $content, $field,
		$field_values, $nameform = 'adminForm',
		$mode = "write"){
		mosCommonHTML::loadJquery();

		//создаем файловую структуру для галереи
		$path = '/images/boss/' . $directory . '/contents/gallery/';

		if(!is_dir(JPATH_BASE . $path . 'origin')){
			mosMakePath(JPATH_BASE, $path . 'origin');
		}

		if(!is_dir(JPATH_BASE . $path . 'full')){
			mosMakePath(JPATH_BASE, $path . 'full');
		}

		if(!is_dir(JPATH_BASE . $path . 'thumb')){
			mosMakePath(JPATH_BASE, $path . 'thumb');
		}

		$mainframe = mosMainFrame::getInstance();
		$mainframe->addJS(JPATH_SITE . '/administrator/components/com_boss/js/upload.js');
		$mainframe->addJS(JPATH_SITE . '/images/boss/' . $directory . '/plugins/fields/BossImageGalleryPlugin/js/script.js');

		$fieldname = $field->name;

		$isAdmin = ($mainframe->isAdmin() == 1) ? 1 : 0;

		$fValuers = array();
		foreach($field_values[$field->fieldid] as $field_value){
			$fValuers[$field_value->fieldtitle] = $field_value->fieldvalue;
		}

		$value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
		$value = (!empty($value)) ? json_decode($value, 1) : '';
		$strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');

		$mosReq = (($mode == "write") && ($field->required == 1)) ? " mosReq='1' " : '';
		$read_only = (($mode == "write") && ($field->editable == 0)) ? " readonly=true " : '';
		$class = (($mode == "write") && ($field->required == 1)) ? "boss_required" : 'boss';

		$nb_files = (!empty($fValuers['nb_images'])) ? (int)$fValuers['nb_images'] : 0;
		$max_image_size = (!empty($fValuers['max_image_size'])) ? (int)$fValuers['max_image_size'] : 0;

		$return = '';
		$return .= "
                <script type=\"text/javascript\">
		            var boss_nb_images = " . $nb_files . ";
		            var boss_max_imgsize = " . $max_image_size . ";
		            var boss_enable_images = new Array('jpg', 'png', 'gif');
		            var boss_isadmin = " . $isAdmin . ";
                </script>";

		$return .= "<div id='gallery_images'>";
		if(!empty($value)){
			$i = 0;
			foreach($value as $row){
				$return .= "
                        <div id='gallery_image_" . $i . "' class='gallery_image_div'>
                        <label>" . BOSS_PLG_DESC . " </label>
                        <input type='text' size='40'
                            name='boss_img_gallery[" . $i . "][signature]' class='inputbox boss_img_gallery' value='" . urldecode($row['signature']) . "' />
                        <input type='hidden' name='boss_img_gallery[" . $i . "][file]' value='" . $row['file'] . "' />"
					. self::displayFileLink($directory, $row['file'])
					. "<input type='button' value='X' class='button' onclick='bossDeleteImage(\"" . $row['file'] . "\", \"gallery_image_" . $i . "\")' />
						</div>";
				$i++;
			}
		}
		$return .= "</div>";
		$return .= "
				<div id='boss_plugin_image'>
                    <input id='upload_image' type='button' class='button' value='" . BOSS_PLG_FM_UPLOAD . "' />
			        <span id='status_image'></span>
				</div>";

		return $return;
	}

	function onFormSave($directory, $contentid, $field, $isUpdateMode){
		mosMainFrame::addLib('easythumb');
		$database = database::getInstance();
		$conf = $database->setQuery("SELECT `fieldtitle`, `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = '$field->fieldid'")->loadObjectList('fieldtitle');

		//общие настройки эскизов
		$thumb = new easyphpthumbnail;
		$thumb->Chmodlevel = '0644';
		$thumb->Quality = 90;

		if(!empty($conf['tag']->fieldvalue)){
			$thumb->Copyrighttext = $conf['tag']->fieldvalue;
			$thumb->Copyrightposition = (!empty($conf['tag_position']->fieldvalue)) ? @$conf['tag_position']->fieldvalue : '50% 90%';
			$thumb->Copyrightfontsize = ((int)$conf['tag_fontsize']->fieldvalue > 0) ? (int)$conf['tag_fontsize']->fieldvalue : 8;
			$thumb->Copyrightfonttype = JPATH_BASE . '/components/com_boss/font/verdana.ttf';
			$thumb->Copyrighttextcolor = (!empty($conf['tag_color']->fieldvalue)) ? @$conf['tag_color']->fieldvalue : '#FFFFFF';
		}

		//разрешенное количество изображений
		$nbImages = ((int)$conf['nb_images']->fieldvalue > 0) ? (int)$conf['nb_images']->fieldvalue : 1;

		//массив изображений
		$boss_img_gallery = mosGetParam($_POST, "boss_img_gallery", array());

		//подрезаем массив изображений до разрешенного количества
		$boss_img_gallery = array_slice($boss_img_gallery, 0, $nbImages);

		//переводим в транслит названия файлов если был загружен файл с кириллическим названием
		for($i = 0; $i < count($boss_img_gallery); $i++){
			$boss_img_gallery[$i]['file'] = russian_transliterate($boss_img_gallery[$i]['file']);
		}

		//возвращаем json с изображениями
		$return = boss_helpers::json_encode_cyr($boss_img_gallery);

		//создаем эскизы изображения
		foreach($boss_img_gallery as $boss_img){
			$filename = $boss_img['file'];

			// image1 upload
			$origin = JPATH_BASE . "/images/boss/" . $directory . "/contents/gallery/origin/" . $filename;

			//если есть оригинальный файл
			if(is_file($origin)){
				//если нет эскиза
				if(!is_file(JPATH_BASE . "/images/boss/" . $directory . "/contents/gallery/full/" . $filename)){
					$thumb->Thumbsize = $conf['max_size']->fieldvalue;
					$thumb->Thumblocation = JPATH_BASE . "/images/boss/$directory/contents/gallery/full/";
					$thumb->Createthumb($origin, 'file');
				}

				//если нет миниатюры
				if(!is_file(JPATH_BASE . "/images/boss/" . $directory . "/contents/gallery/thumb/" . $filename)){
					$thumb->Thumbsize = $conf['max_size_t']->fieldvalue;
					$thumb->Thumblocation = JPATH_BASE . "/images/boss/" . $directory . "/contents/gallery/thumb/";
					$thumb->Createthumb($origin, 'file');
				}
			}
		}
		return $return;
	}

	function onDelete($directory, $content){
		$database = database::getInstance();
		$contents = null;
		$database->setQuery("SELECT * FROM #__boss_" . $directory . "_contents WHERE `id` = '" . $content->id . "'");
		$database->loadObject($contents);
		$database->setQuery("SELECT name FROM #__boss_" . $directory . "_fields WHERE `type` = '" . $this->type . "'");
		$file_fields = $database->loadObjectList();
		if(is_array($file_fields) && count($file_fields) > 0){
			foreach($file_fields as $file_field){
				$fileFieldName = $file_field->name;
				$files = json_decode($contents->$fileFieldName);
				if(is_array($files) && count($files) > 0){
					foreach($files as $file){
						@unlink(JPATH_BASE . "/images/boss/$directory/contents/gallery/origin/" . $file->file);
						@unlink(JPATH_BASE . "/images/boss/$directory/contents/gallery/full/" . $file->file);
						@unlink(JPATH_BASE . "/images/boss/$directory/contents/gallery/thumb/" . $file->file);
					}
				}
			}
		}
	}

	//отображение поля в админке в настройках поля
	function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues){
		$path = dirname(__FILE__) . DS . 'engines' . DS;
		$files = mosReadDirectory($path, '');
		$options = array();
		foreach($files as $file){ // формируем выпадающий список
			if(is_dir($path . $file)){ // добавляем только директории
				$options[] = mosHTML::makeOption($file, $file);
			}
		}
		$engine_select = mosHTML::selectList($options, 'galleryScript', 'id="galleryScript" class="inputbox" style="width:140px"', 'value', 'text', @$fieldvalues['galleryScript']->fieldvalue, "");

		$return = '<div id="divImageOptions">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td colspan="3"><strong>' . BOSS_PLG_GALLERY_IMAGE_SETTINGS . '</strong></td>
                </tr>
                <tr>
                    <td>' . BOSS_NB_IMAGES . '</td>
                    <td><input type="text" name="nb_images" id="nb_images" value="' . @$fieldvalues['nb_images']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_NB_IMAGES_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_MAX_IMAGE_SIZE . '</td>
                    <td><input type="text" name="max_image_size" id="max_image_size" value="' . @$fieldvalues['']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_MAX_IMAGE_SIZE_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_MAX_SIZE . '</td>
                    <td><input type="text" name="max_size" id="max_size" value="' . @$fieldvalues['max_size']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_MAX_SIZE_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_MAX_SIZE_T . '</td>
                    <td><input type="text" name="max_size_t" id="max_size_t" value="' . @$fieldvalues['max_size_t']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_MAX_SIZE_T_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_IMAGE_TAG . '</td>
                    <td><input type="text" name="tag" id="tag" value="' . @$fieldvalues['tag']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_IMAGE_TAG_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_TAG_POSITION . '</td>
                    <td><input type="text" name="tag_position" id="tag_position" value="' . @$fieldvalues['tag_position']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_TAG_POSITION_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_TAG_FONTSIZE . '</td>
                    <td><input type="text" name="tag_fontsize" id="tag_fontsize" value="' . @$fieldvalues['tag_fontsize']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_TAG_FONTSIZE_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_TAG_COLOR . '</td>
                    <td><input type="text" name="tag_color" id="tag_color" value="' . @$fieldvalues['tag_color']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_TAG_COLOR_LONG) . '</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>' . BOSS_PLG_GALLERY_GALLERY_SETTINGS . '</strong></td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_GAL_NAME . '</td>
                    <td><input type="text" name="galleryTitle" id="galleryTitle" value="' . @$fieldvalues['galleryTitle']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_GAL_NAME_LONG) . '</td>
                </tr>               
                <tr>
                    <td>' . BOSS_PLG_GALLERY_OVER_BG . '</td>
                    <td><input type="text" name="overlayBackground" id="overlayBackground" value="' . @$fieldvalues['overlayBackground']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_OVER_BG_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_OVER_OPAC . '</td>
                    <td><input type="text" name="overlayOpacity" id="overlayOpacity" value="' . @$fieldvalues['overlayOpacity']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_OVER_OPAC_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_MIN_WIDTH . '</td>
                    <td><input type="text" name="minWidth" id="minWidth" value="' . @$fieldvalues['minWidth']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_MIN_WIDTH_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_MIN_HEIGHT . '</td>
                    <td><input type="text" name="minHeight" id="minHeight" value="' . @$fieldvalues['minHeight']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_MIN_HEIGHT_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_MAX_WIDTH . '</td>
                    <td><input type="text" name="maxWidth" id="maxWidth" value="' . @$fieldvalues['maxWidth']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_MAX_WIDTH_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_SLIDE_TIMER . '</td>
                    <td><input type="text" name="slideTimer" id="slideTimer" value="' . @$fieldvalues['slideTimer']->fieldvalue . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_SLIDE_TIMER_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_AUROSLIDE . '</td>
                    <td>
                        <select id="autoSlide" name="autoSlide" style="width: 140px">
                            <option value="1"';
		$return .= (@$fieldvalues['autoSlide']->fieldvalue == '1') ? 'selected="selected"' : '';
		$return .= '>' . BOSS_YES . '</option>
                            <option value="0"';
		$return .= (@$fieldvalues['autoSlide']->fieldvalue == '0') ? 'selected="selected"' : '';


		$return .= '>' . BOSS_NO . '</option>
                        </select>
                    </td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_AUROSLIDE_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_GALLERY_SCRIPT . '</td>
                    <td>' . $engine_select . ' </td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_GALLERY_SCRIPT_LONG) . '</td>
                </tr>
            </table>
            </div>';

		return $return;
	}

	//действия при сохранении настроек поля
	function saveFieldOptions($directory, $field){
		$fieldid = $field->fieldid;
		$fieldname = $field->name;
		$database = database::getInstance();

		$nb_images = mosGetParam($_POST, "nb_images", 0);
		$max_image_size = mosGetParam($_POST, "max_image_size", 0);
		$max_size = mosGetParam($_POST, "max_size", 0);
		$max_size_t = mosGetParam($_POST, "max_size_t", 0);

		$tag = mosGetParam($_POST, "tag", '');
		$tag_fontsize = mosGetParam($_POST, "tag_fontsize", '');
		$tag_position = mosGetParam($_POST, "tag_position", '');
		$tag_color = mosGetParam($_POST, "tag_color", '');

		$image_display = mosGetParam($_POST, "image_display", '');
		$cat_max_width = mosGetParam($_POST, "cat_max_width", 0);
		$cat_max_height = mosGetParam($_POST, "cat_max_height", 0);
		$cat_max_width_t = mosGetParam($_POST, "cat_max_width_t", 0);
		$cat_max_height_t = mosGetParam($_POST, "cat_max_height_t", 0);

		$galleryTitle = mosGetParam($_POST, "galleryTitle", '');
		$overlayBackground = mosGetParam($_POST, "overlayBackground", '');
		$overlayOpacity = mosGetParam($_POST, "overlayOpacity", '');
		$minWidth = mosGetParam($_POST, "minWidth", '');
		$minHeight = mosGetParam($_POST, "minHeight", '');
		$maxWidth = mosGetParam($_POST, "maxWidth", '');
		$slideTimer = mosGetParam($_POST, "slideTimer", '');
		$autoSlide = mosGetParam($_POST, "autoSlide", '');
		$engine = mosGetParam($_POST, "galleryScript", '');

		$q = "DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = '" . $fieldid . "' ";
		$database->setQuery($q)->query();

		$q = "INSERT INTO #__boss_" . $directory . "_field_values
            (fieldid, fieldtitle, fieldvalue, ordering, sys)
            VALUES
            ($fieldid,'nb_images', '$nb_images',  1,0),
            ($fieldid,'max_image_size', '$max_image_size', 2,0),
            ($fieldid,'max_size_t', '$max_size_t', 3,0),
            ($fieldid,'max_size', '$max_size', 4,0),
            ($fieldid,'tag', '$tag', 5,0),
            ($fieldid,'tag_fontsize', '$tag_fontsize', 6,0),
            ($fieldid,'tag_position', '$tag_position', 7,0),
            ($fieldid,'tag_color', '$tag_color', 8,0),
            ($fieldid,'image_display', '$image_display', 9,0),
            ($fieldid,'cat_max_width', '$cat_max_width', 10,0),
            ($fieldid,'cat_max_height', '$cat_max_height', 11,0),
            ($fieldid,'cat_max_width_t', '$cat_max_width_t', 12,0),
            ($fieldid,'cat_max_height_t', '$cat_max_height_t', 13,0),
            ($fieldid,'galleryTitle', '$galleryTitle', 14,0),           
            ($fieldid,'overlayBackground', '$overlayBackground', 16,0),
            ($fieldid,'overlayOpacity', '$overlayOpacity', 17,0),
            ($fieldid,'minWidth', '$minWidth', 18,0),
            ($fieldid,'minHeight', '$minHeight', 19,0),
            ($fieldid,'maxWidth', '$maxWidth', 20,0),
            ($fieldid,'slideTimer', '$slideTimer', 21,0),
            ($fieldid,'autoSlide', '$autoSlide', 22,0),            
            ($fieldid,'galleryScript', '$engine', 23,0)
            ";
		$database->setQuery($q)->query();
		//если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
		//иначе true
		return false;
	}

	//расположение иконки плагина начиная со слеша от корня сайта
	function getFieldIcon($directory){
		return "/images/boss/" . $directory . "/plugins/fields/" . __CLASS__ . "/images/image_1.png";
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
		return $search;
	}

	//скрипты и стили в голову, которые не кешируются
	function addInHead($field, $field_values, $directory){
		$conf = self::fvalues($field_values);
		$engine = !empty($conf['galleryScript']) ? $conf['galleryScript'] : 'mbGallery';
		$path = JPATH_SITE . '/images/boss/' . $directory
			. '/plugins/fields/BossImageGalleryPlugin/engines/' . $engine;
		$params = array();

		//загружаем файл инциализации скрипта галереи
		self::getEngine($engine);
		$params = galleryAddInHead($path);
		//$params['css']['galleryImg0'] = JPATH_SITE . '/images/boss/' . $directory . '/plugins/fields/BossImageGalleryPlugin/css/style.admin.css';

		$task = mosGetParam($_REQUEST, 'task', '');

		if($task == 'show_content' || $task == 'show_category' || $task == ''){ // добавляем параметры, при просмотре категории или контента или главной страницы
			$conf_fields = self::fvalues($field_values);

			$engine = (!empty($conf_fields['galleryScript'])) ? $conf_fields['galleryScript'] : 'mbGallery';
			$galleryTitle = htmlspecialchars(stripslashes(cutLongWord($conf_fields['galleryTitle'])), ENT_QUOTES);

			$overlayBackground = (!empty($conf_fields['overlayBackground'])) ? $conf_fields['overlayBackground'] : '#666';
			$overlayOpacity = (!empty($conf_fields['overlayOpacity'])) ? (int)$conf_fields['overlayOpacity'] : '.3';

			$minWidth = (!empty($conf_fields['minWidth'])) ? (int)$conf_fields['minWidth'] : '';
			$minHeight = (!empty($conf_fields['minHeight'])) ? (int)$conf_fields['minHeight'] : '';
			$maxWidth = (!empty($conf_fields['maxWidth'])) ? (int)$conf_fields['maxWidth'] : '';
			$maxHeight = (!empty($conf_fields['maxHeight'])) ? (int)$conf_fields['maxHeight'] : '';

			$slideTimer = (!empty($conf_fields['slideTimer'])) ? (int)$conf_fields['slideTimer'] : '6000';
			$autoSlide = (!empty($conf_fields['autoSlide'])) ? $conf_fields['autoSlide'] : false;


			$html = "<script type='text/javascript' charset='utf-8'>\n";
			$html .= "var galleryOptions = {\n";
			$html .= "galleryTitle: '" . $galleryTitle . "',\n";
			$html .= "overlayBackground: '" . $overlayBackground . "',\n";
			$html .= "overlayOpacity: '" . $overlayOpacity . "',\n";
			$html .= "maxWidth: '" . $maxHeight . "',\n";
			$html .= "maxHeight: '" . $maxHeight . "',\n";
			$html .= "slideTimer: '" . $slideTimer . "',\n";
			$html .= "autoSlide: '" . $autoSlide . "',\n";
			$html .= "}";
			$html .= "</script>";

			$params['custom_script']['imageGallery'] = $html;
		}
		return $params;
	}

	private function fvalues($field_values){
		$fieldvalue = array();
		foreach($field_values as $field_value){
			$fieldvalue[$field_value->fieldtitle] = $field_value->fieldvalue;
		}
		return $fieldvalue;
	}

	//отображение ссылки на скачивание
	private function displayFileLink($directory, $filename){
		$return = '';
		if($filename){
			$return .= "<img class='thumb' src=\"" . JPATH_SITE . "/images/boss/" . $directory . "/contents/gallery/thumb/" . $filename . "\" align=\"middle\" border=\"0\" />&nbsp;";
		}

		return $return;
	}
}

?>