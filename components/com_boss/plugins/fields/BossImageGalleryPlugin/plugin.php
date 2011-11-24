<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

//подгружаем языковой файл плагина
boss_helpers::loadBossPluginLang($directory, 'fields', 'BossImageGalleryPlugin');

    class BossImageGalleryPlugin {
        
       //имя типа поля в выпадающем списке в настройках поля
        var $name = 'Image Gallery';
        
        //тип плагина для записи в таблицы
        var $type = __CLASS__;
        
        //отображение поля в категории
        function getListDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            $return = '';
            $conf = $this->fvalues($field_values);
            $fieldname = $field->name;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
            $images = (!empty($value)) ? json_decode($value, 1): '';
            if(!is_array($images) || count($images) == 0) return '';
            $return .= '<div class="thumbsContainer">';
            $pic = "/images/boss/$directory/contents/gallery/thumb/" . $images[0]['file'];
            if (file_exists(JPATH_BASE . $pic)) {
                $return .= "<img src='" . JPATH_SITE . $pic. "' alt='" . htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES) . "' />";

            }
            else{
                if ((BOSS_NOPIC != "") && (file_exists(JPATH_BASE . "/templates/com_boss/" . $conf->template . "/images/" . BOSS_NOPIC)))
                    $return .= "<img src='" . JPATH_SITE . "/templates/com_boss/" . $conf->template . "/images/" . BOSS_NOPIC . "' alt='nopic' />";
                else
                    $return .= "<img src='" . JPATH_SITE . "/templates/com_boss/" . $conf->template . "/images/nopic.gif' alt='nopic' />";

            }
            $return .= '</div>';
            $return .= '
            <a class="galleryView"
                onclick="$(\'#g'.$content->id.'\').mbGallery({
                    galleryTitle:\''. htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES) .'\',
                    maskBgnd:\'#ccc\',
                    minWidth: 50,
                    minHeight: 50,
                    overlayOpacity:.9,
                    startFrom: 0,
                    addRaster:true,
                    autoSlide: false,
                    printOutThumbs:false
                    });">
                '.BOSS_PLG_GALLERY_VIEW.'
            </a>';
            //контейнер с изображениями для галереи
            $return .= '<div id="g'.$content->id.'" class="galleryCont" style="display: none;">';
            foreach($images as $image){
            $return .= '
                <a class="imgThumb" href="/images/boss/'.$directory.'/contents/gallery/thumb/'.$image['file'].'"></a>
                <a class="imgFull"  href="/images/boss/'.$directory.'/contents/gallery/full/'.$image['file'].'"></a>
                <div class="imgDesc">'.$image['signature'].'</div>
                ';
            }
            $return .= '</div>';
            return $return;
        }

        //отображение поля в контенте
        function getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            $return = '<div class="galleryDiv">';
            $conf = $this->fvalues($field_values);
            $fieldname = $field->name;
            $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
            $images = (!empty($value)) ? json_decode($value, 1): '';
            if(!is_array($images) || count($images) == 0) return '';
            $i=0;
            $return .= '<div id="g'.$content->id.'_thumbsContainer" class="thumbsContainer" style="display: block;">';
            foreach($images as $image){
                $return .= '
                <div class="thumbBlock">
                <a class="galleryViewContent"
                onclick="$(\'#g'.$content->id.'\').mbGallery({
                    galleryTitle:\''. htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES) .'\',
                    maskBgnd:\'#ccc\',
                    minWidth: 50,
                    minHeight: 50,
                    overlayOpacity:.9,
                    startFrom: '.$i.',
                    addRaster:false,
                    autoSlide: false,
                    printOutThumbs:false
                    });">
                    <img id="mb_gallery_g1_thumb1_'.$i.'" class="thumb" src="/images/boss/'.$directory.'/contents/gallery/thumb/'.$image['file'].'" alt="'.$image['signature'].'">
                </a>
                <div class="imgDesc">'.$image['signature'].'</div>
                </div>
                ';
                $i++;
            }
            $return .= '</div>';
            //контейнер с изображениями для галереи
            $return .= '<div id="g'.$content->id.'" class="galleryCont" style="display: none;">';
            foreach($images as $image){
            $return .= '
                <a class="imgThumb" href="/images/boss/'.$directory.'/contents/gallery/thumb/'.$image['file'].'"></a>
                <a class="imgFull"  href="/images/boss/'.$directory.'/contents/gallery/full/'.$image['file'].'"></a>
                <div class="imgDesc">'.$image['signature'].'</div>
                ';
            }
            $return .= '</div>';
            $return .= '</div>';
            return $return;
        }

        //функция вставки фрагмента ява-скрипта в скрипт
        //сохранения формы при редактировании контента с фронта.
        function addInWriteScript($field){

        }

        //отображение поля в админке в редактировании контента
        function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write") {
            mosCommonHTML::loadJquery();
            //создаем файловую структуру для галереи
            $path = '/images/boss/'.$directory.'/contents/gallery/';
            if(!is_dir(JPATH_BASE.$path.'origin')) {
				mosMakePath(JPATH_BASE, $path.'origin');
			}
            if(!is_dir(JPATH_BASE.$path.'full')) {
				mosMakePath(JPATH_BASE, $path.'full');
			}
            if(!is_dir(JPATH_BASE.$path.'thumb')) {
				mosMakePath(JPATH_BASE, $path.'thumb');
			}

            $mainframe = mosMainFrame::getInstance();
            $mainframe->addJS(JPATH_SITE.'/administrator/components/com_boss/js/upload.js');
            $mainframe->addJS(JPATH_SITE.'/images/boss/'.$directory.'/plugins/fields/BossImageGalleryPlugin/js/script.js');

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

            $nb_files = (!empty($fValuers['nb_images'])) ? $fValuers['nb_images'] : 0;
            $return = '';
            $return .= "
                <script type=\"text/javascript\">
		            var boss_nb_images = ".(int)$nb_files.";
		            var boss_enable_images = new Array('jpg', 'png', 'gif');
		            var boss_isadmin = ".$isAdmin.";
                </script>

                <div id='boss_plugin_image'>
                    <input id='upload_image' type=button value='".BOSS_PLG_FM_UPLOAD."' style='float: left;'/>
			        <div id='status_image'></div>
			        <br style='clear: both;' />
			        <div id='gallery_images'>
                    ";

            if (!empty($value)) {
                foreach($value as $i => $row){
                    $return .= "
                        <div id='gallery_image_".$i."'>
                        <label>".BOSS_PLG_DESC." </label>
                        <input type='text' size='40'
                            name='boss_img_gallery[".$i."][signature]' class='inputbox boss_img_gallery' value='".urldecode($row['signature'])."' />

                        <input type='hidden' name='boss_img_gallery[".$i."][file]' value='".$row['file']."' />
                            &nbsp;&nbsp;&nbsp;"
                        .self::displayFileLink($directory, $row['file'])
                        . "&nbsp;&nbsp;<input type='button' value='X' class='button' onclick='bossDeleteImage(\"".$row['file']."\", \"gallery_image_".$i."\")' />
                    </div>";
                }

            }
			$return .= "</div>";
            return $return;
        }

        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {



            $database = database::getInstance();
            $conf = $database->setQuery("SELECT `fieldtitle`, `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = '$field->fieldid'")->loadObjectList('fieldtitle');
            
            $nbImages = $conf['nb_images']->fieldvalue;
            $boss_img_gallery = mosGetParam($_POST, "boss_img_gallery", array());
            var_dump($boss_img_gallery);

            $return = boss_helpers::json_encode_cyr($boss_img_gallery);
            var_dump($return);
            foreach ($boss_img_gallery as $boss_img) {
                //$ext_name = chr(ord('a') + $i - 1);

                $filename = $boss_img['file'];
                // image1 upload
                if(is_file(JPATH_BASE . "/images/boss/$directory/contents/gallery/origin/$filename")){
                    if(!is_file(JPATH_BASE . "/images/boss/$directory/contents/gallery/full/$filename"))
                    createImage(
                         JPATH_BASE . "/images/boss/$directory/contents/gallery/origin/$filename",
                         $filename,
                         JPATH_BASE . "/images/boss/$directory/contents/gallery/full/",
                         $filename,
                         $conf['max_width']->fieldvalue,
                         $conf['max_height']->fieldvalue,
                         $conf['tag']->fieldvalue
                         );
                    if(!is_file(JPATH_BASE . "/images/boss/$directory/contents/gallery/thumb/$filename"))
                    createImage(
                         JPATH_BASE . "/images/boss/$directory/contents/gallery/origin/$filename",
                         $filename,
                         JPATH_BASE . "/images/boss/$directory/contents/gallery/thumb/",
                         $filename,
                         $conf['max_width_t']->fieldvalue,
                         $conf['max_height_t']->fieldvalue,
                         $conf['tag']->fieldvalue
                         );
                }
            }
            return $return;
        }

        function onDelete($directory, $contentid = -1) {
            return ;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues)
        {
            $return = '<div id="divImageOptions">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td>'.BOSS_NB_IMAGES.'</td>
                    <td><input type="text" name="nb_images" id="nb_images" value="'.@$fieldvalues['nb_images']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_NB_IMAGES_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_MAX_IMAGE_SIZE.'</td>
                    <td><input type="text" name="max_image_size" id="max_image_size" value="'.@$fieldvalues['max_image_size']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_MAX_IMAGE_SIZE_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_MAX_IMAGE_WIDTH.'</td>
                    <td><input type="text" name="max_width" id="max_width" value="'.@$fieldvalues['max_width']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_MAX_IMAGE_WIDTH_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_MAX_IMAGE_HEIGHT.'</td>
                    <td><input type="text" name="max_height" id="max_height" value="'.@$fieldvalues['max_height']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_MAX_IMAGE_HEIGHT_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_MAX_THUMBNAIL_WIDTH.'</td>
                    <td><input type="text" name="max_width_t" id="max_width_t" value="'.@$fieldvalues['max_width_t']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_MAX_THUMBNAIL_WIDTH_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_MAX_THUMBNAIL_HEIGHT.'</td>
                    <td><input type="text" name="max_height_t" id="max_height_t" value="'.@$fieldvalues['max_height_t']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_MAX_THUMBNAIL_HEIGHT_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_IMAGE_TAG.'</td>
                    <td><input type="text" name="tag" id="tag" value="'.@$fieldvalues['tag']->fieldvalue.'"/></td>
                    <td>'.boss_helpers::bossToolTip(BOSS_IMAGE_TAG_LONG).'</td>
                </tr>
                <tr>
                    <td>'.BOSS_IMAGE_DISPLAY.'</td>
                    <td>
                        <select id="image_display" name="image_display" style="width: 140px">
                            <option value="default"';
                                $return .= (@$fieldvalues['image_display']->fieldvalue == 'default') ? 'selected="selected"' : '';
                                $return .= '>'.BOSS_IMAGE_DISPLAY_DEFAULT.'</option>
                            <option value="fancybox"';
                                $return .= (@$fieldvalues['image_display']->fieldvalue == 'fancybox') ? 'selected="selected"' : '';


                                $return .= '>'.BOSS_IMAGE_DISPLAY_FANCY.'</option>
                            <option value="popup"';
                                $return .= (@$fieldvalues['image_display']->fieldvalue == 'popup') ? 'selected="selected"' : '';
                                $return .= '>'.BOSS_IMAGE_DISPLAY_POPUP.'</option>
                            <option value="gallery"';
                                $return .= (@$fieldvalues['image_display']->fieldvalue == 'gallery') ? 'selected="selected"' : '';
                                $return .= '>'.BOSS_IMAGE_DISPLAY_GALLERY.'</option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            </div>';
            return $return;
        }

        //действия при сохранении настроек поля
        function saveFieldOptions($directory, $field) {

            $fieldid = $field->fieldid;
            $fieldname = $field->name;
            $database = database::getInstance();

            $nb_images          = mosGetParam($_POST, "nb_images", 0);
            $max_image_size     = mosGetParam($_POST, "max_image_size", 0);
            $max_width          = mosGetParam($_POST, "max_width", 0);
            $max_height         = mosGetParam($_POST, "max_height", 0);
            $max_width_t        = mosGetParam($_POST, "max_width_t", 0);
            $max_height_t       = mosGetParam($_POST, "max_height_t", 0);
            $tag                = mosGetParam($_POST, "tag", '');
            $image_display      = mosGetParam($_POST, "image_display", '');
            $cat_max_width      = mosGetParam($_POST, "cat_max_width", 0);
            $cat_max_height     = mosGetParam($_POST, "cat_max_height", 0);
            $cat_max_width_t    = mosGetParam($_POST, "cat_max_width_t", 0);
            $cat_max_height_t   = mosGetParam($_POST, "cat_max_height_t", 0);

            $q = "DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = '" . $fieldid . "' ";
            $database->setQuery($q)->query();

            $q = "INSERT INTO #__boss_" . $directory . "_field_values
            (fieldid, fieldtitle, fieldvalue, ordering, sys)
            VALUES
            ($fieldid,'nb_images', '$nb_images',  1,0),
            ($fieldid,'max_image_size', '$max_image_size', 2,0),
            ($fieldid,'max_width', '$max_width', 3,0),
            ($fieldid,'max_height', '$max_height', 4,0),
            ($fieldid,'max_width_t', '$max_width_t', 5,0),
            ($fieldid,'max_height_t', '$max_height_t', 6,0),
            ($fieldid,'tag', '$tag', 7,0),
            ($fieldid,'image_display', '$image_display', 8,0),
            ($fieldid,'cat_max_width', '$cat_max_width', 9,0),
            ($fieldid,'cat_max_height', '$cat_max_height', 10,0),
            ($fieldid,'cat_max_width_t', '$cat_max_width_t', 11,0),
            ($fieldid,'cat_max_height_t', '$cat_max_height_t', 12,0)
            ";
            $database->setQuery($q)->query();
            //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
            //иначе true
            return false;
        }

        //расположение иконки плагина начиная со слеша от корня сайта
        function getFieldIcon($directory) {
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/image_1.png";
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
            return $search;
        }
        
        //скрипты и стили в голову, которые не кешируются
        function addInHead($field, $field_values, $directory)
        {
            $params = array();
            $params['js']['galleryImg1'] = JPATH_SITE . '/images/boss/'.$directory.'/plugins/fields/BossImageGalleryPlugin/js/jquery.exif.js';
            $params['js']['galleryImg2'] = JPATH_SITE . '/images/boss/'.$directory.'/plugins/fields/BossImageGalleryPlugin/js/mbGallery.js';
            $params['css']['galleryImg1'] =  JPATH_SITE . '/images/boss/'.$directory.'/plugins/fields/BossImageGalleryPlugin/css/white.css';
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
        private function displayFileLink($directory, $filename) {
            $return = '';
            if ($filename) {
				$return .= "<img src=\"" . JPATH_SITE . "/images/boss/" . $directory . "/contents/gallery/thumb/" . $filename . "\"  align=\"middle\" border=\"0\" />&nbsp;";
            }
            return $return;
        }
    }
?>