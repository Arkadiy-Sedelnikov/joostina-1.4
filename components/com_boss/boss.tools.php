<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined( '_VALID_MOS' ) or die();


function getIp() {
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function getBossItemid($directory, $catid) {
        $mainframe = mosMainFrame::getInstance();;
        $contentid = intval(mosGetParam($_REQUEST, 'contentid', 0));
        $itemid = $mainframe->getBossItemid($directory, $catid, $contentid);
        return $itemid;

}

/**
 * Checks if a given string is a valid email address
 *
 * @param	string	$email	String to check for a valid email address
 * @return	boolean
 */
function isValidEmail( $email ) {
	$valid = preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email );

	return $valid;
}

/**
 * Функция обработки изображений
 *
 * Параметры качества:
 * Значение параметра качества изображения для форматов:
 * GIF не имеет смысла, т.к параметр не поддерживается.
 * JPEG используется значение в диапазоне от 1 до 100 (1 - наихудшее, 100 - наилучшее).
 * PNG используется шкала качества от 0 до 9 (0 - наилучшее, 9 - наихудшее).
 *
 * Независимо от формата изображения необходимо использовать
 * шкалу качества JPEG (от 1 до 100). Для формата GIF он будет проигнорирован, а
 * для формата PNG будет пересчитан.
 *
 * Параметры цвета:
 * Регистр и использование символа решетки (#) не имеют значения.
 * ВАЖНО указывать полностью, 6 символов (ffffff),
 * а не укороченный вариант 3 (fff)
 * неправильно: #fff и т.д.
 * правильно: #FFFFFF, FFFFFF, ffffff, #ffffff
 *
 */
function createImageAndThumb(
                $src_file,
                $orig_name,
                $path,
                $image_name,
                $thumb_name,
		$max_width,
		$max_height,
		$max_width_t,
		$max_height_t,
		$tag='',		
		$quality=75,//качество для основного изображения
		$quality_t=75,//качество для мини-эскиза
		$bg_color_hex="#ffffff",//белый для фона
		$bg_color_hex_t="#FFFFFF",
		$txt_color_hex="#000000",//черный для текста
		$txt_color_hex_t="000000"
		) {

	//ini_set('memory_limit', '32M');
	$font = JPATH_BASE . "/components/com_boss/font/verdana.ttf";
	$src_file = urldecode($src_file);

	// Переводим название файла в нижний регистр
	$orig_name = Jstring::strtolower($orig_name);

	// Выясняем тип изображения
        $type = explode('.', $orig_name);
        $type = strval($type[count($type)-1]);
        $type = ($type == 'jpg') ? 'jpeg' : $type;

        if($type == "png"){
            //Пересчитываем значение параметра качества для PNG
            $quality = 9 - min( round($quality / 10), 9 );
            $quality_t = 9 - min( round($quality_t / 10), 9 );
        }

	$max_h = $max_height;
	$max_w = $max_width;
	$max_thumb_h = $max_height_t;
	$max_thumb_w = $max_width_t;

	// Если изображение с таким именем уже существует - удаляем его
	if (file_exists( "$path/$image_name")) {
		unlink( "$path/$image_name");
	}
	// Если мини-эскиз с таким именем уже существует - удаляем
	if (file_exists( "$path/$thumb_name")) {
		unlink( "$path/$thumb_name");
	}

	$read = 'imagecreatefrom' . $type;
	$write = 'image' . $type;

	$src_img = $read($src_file);

	// Расчитываем пропорции создаваемого изображения
	$imginfo = getimagesize($src_file);
	$src_w = $imginfo[0];
	$src_h = $imginfo[1];

	$zoom_h = $max_h / $src_h;
	$zoom_w = $max_w / $src_w;
	$zoom   = min($zoom_h, $zoom_w);
	$dst_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
	$dst_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;

	$zoom_h = $max_thumb_h / $src_h;
	$zoom_w = $max_thumb_w / $src_w;
	$zoom   = min($zoom_h, $zoom_w);
	$dst_thumb_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
	$dst_thumb_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;

	/* ОСНОВНОЕ ИЗОБРАЖЕНИЕ */
	//if ($type == "gif")
	//	$dst_img = imagecreate($dst_w, $dst_h); //для GIF
	//else
	$dst_img = imagecreatetruecolor($dst_w, $dst_h);// JPG и PNG

	// Конвертируем значение параметра цвета фона из HEX в RGB
	$bg_color_rgb = array_map('hexdec', str_split(str_replace('#', '', $bg_color_hex), 2));

	// Заливаем фон изображения
	$white = imagecolorallocate($dst_img, $bg_color_rgb[0], $bg_color_rgb[1], $bg_color_rgb[2]);
	imagefill($dst_img, 0, 0, $white);

	// Копируем палитру цветов
	imagepalettecopy($dst_img, $src_img);

	// Изменяем размер
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

	// Ставим копирайт
	if (isset($tag)) {
		// Конвертируем значение параметра цвета текста из HEX в RGB
		$txt_color_rgb = array_map('hexdec', str_split(str_replace('#', '', $txt_color_hex), 2));
		$textcolor = imagecolorallocate($dst_img, $txt_color_rgb[0], $txt_color_rgb[1], $txt_color_rgb[2]);// Получаем цветовой идентификатор текста
		imagettftext($dst_img, 12, 0, 10, 10, $textcolor, $font, urldecode($tag));// Накладываем текст копирайта на изображение
	}
	// Создаем основное изображение
	$desc_img = $write($dst_img,"$path/$image_name", $quality);

	/* МИНИ-ЭСКИЗ */
	//if ($type == "gif")
	//	$dst_t_img = imagecreate($dst_thumb_w, $dst_thumb_h); //для GIF
	//else
	$dst_t_img = imagecreatetruecolor($dst_thumb_w, $dst_thumb_h);// JPG и PNG

	// Конвертируем значение параметра цвета фона из HEX в RGB
	$bg_color_rgb_t = array_map('hexdec', str_split(str_replace('#', '', $bg_color_hex_t), 2));

	// Заливаем фон изображения
	$white = imagecolorallocate($dst_img, $bg_color_rgb_t[0], $bg_color_rgb_t[1], $bg_color_rgb_t[2]);
	imagefill($dst_t_img, 0, 0, $white);

	// Копируем палитру цветов
	imagepalettecopy($dst_t_img, $src_img);

	// Изменяем размер
	imagecopyresampled($dst_t_img, $src_img, 0, 0, 0, 0, $dst_thumb_w, $dst_thumb_h, $src_w, $src_h);

	// Ставим копирайт
	if (isset($tag)) {
		// Конвертируем значение параметра цвета текста из HEX в RGB
		$txt_color_rgb_t = array_map('hexdec', str_split(str_replace('#', '', $txt_color_hex_t), 2));
		$textcolor = imagecolorallocate($dst_t_img, $txt_color_rgb_t[0], $txt_color_rgb_t[1], $txt_color_rgb_t[2]);// Получаем цветовой идентификатор текста
		imagettftext($dst_t_img, 8, 0, 5, 10, $textcolor, $font, urldecode($tag));// Накладываем текст копирайта на изображение
	}

	// Создаем мини-эскиз изображения
	$desc_img = $write($dst_t_img,"$path/$thumb_name", $quality);
}

function createImage(
        $src_file,
        $orig_name,
        $path,
        $image_name,
		$max_width,
		$max_height,
		$tag='',
		$quality=75,//качество для основного изображения
		$bg_color_hex="#ffffff",//белый для фона
		$txt_color_hex="#000000"//черный для текста
		)
{

	//ini_set('memory_limit', '32M');
	$font = JPATH_BASE . "/components/com_boss/font/verdana.ttf";
	$src_file = urldecode($src_file);

	// Переводим название файла в нижний регистр
	$orig_name = Jstring::strtolower($orig_name);

	// Выясняем тип изображения
        $type = explode('.', $orig_name);
        $type = strval($type[count($type)-1]);
        $type = ($type == 'jpg') ? 'jpeg' : $type;

        if($type == "png"){
            //Пересчитываем значение параметра качества для PNG
            $quality = 9 - min( round($quality / 10), 9 );
        }

	$max_h = $max_height;
	$max_w = $max_width;

	// Если изображение с таким именем уже существует - удаляем его
	if (file_exists( "$path/$image_name")) {
		unlink( "$path/$image_name");
	}

	$read = 'imagecreatefrom' . $type;
	$write = 'image' . $type;

	$src_img = $read($src_file);

	// Расчитываем пропорции создаваемого изображения
	$imginfo = getimagesize($src_file);
	$src_w = $imginfo[0];
	$src_h = $imginfo[1];

	$zoom_h = $max_h / $src_h;
	$zoom_w = $max_w / $src_w;
	$zoom   = min($zoom_h, $zoom_w);
	$dst_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
	$dst_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;

	$zoom   = min($zoom_h, $zoom_w);
	$dst_thumb_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
	$dst_thumb_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;

	/* ОСНОВНОЕ ИЗОБРАЖЕНИЕ */
	//if ($type == "gif")
	//	$dst_img = imagecreate($dst_w, $dst_h); //для GIF
	//else
	$dst_img = imagecreatetruecolor($dst_w, $dst_h);// JPG и PNG

	// Конвертируем значение параметра цвета фона из HEX в RGB
	$bg_color_rgb = array_map('hexdec', str_split(str_replace('#', '', $bg_color_hex), 2));

	// Заливаем фон изображения
	$white = imagecolorallocate($dst_img, $bg_color_rgb[0], $bg_color_rgb[1], $bg_color_rgb[2]);
	imagefill($dst_img, 0, 0, $white);

	// Копируем палитру цветов
	imagepalettecopy($dst_img, $src_img);

	// Изменяем размер
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

	// Ставим копирайт
	if (!empty($tag)) {
		// Конвертируем значение параметра цвета текста из HEX в RGB
		$txt_color_rgb = array_map('hexdec', str_split(str_replace('#', '', $txt_color_hex), 2));
		$textcolor = imagecolorallocate($dst_img, $txt_color_rgb[0], $txt_color_rgb[1], $txt_color_rgb[2]);// Получаем цветовой идентификатор текста
		imagettftext($dst_img, 12, 0, 10, 10, $textcolor, $font, urldecode($tag));// Накладываем текст копирайта на изображение
	}
	// Создаем основное изображение
	$desc_img = $write($dst_img,"$path/$image_name", $quality);
}

function jdGetLangDefinition($text) {
	if(defined($text)) $returnText = constant($text);
	else $returnText = $text;
	return $returnText;
}
/**
 * @text Исходный текст
 * @limit Количество симловов которые надо вывести
 */
function cutLongWord($text, $limit = 80) {
    $fraza = "";
    $words = explode(" ", $text);
    for ($i = 0; $i < count($words); $i++) {
        $tmp_str = $fraza . $words[$i] . " ";
        if (Jstring::strlen($tmp_str) < $limit) {
            $fraza = $tmp_str;
        } else {
            if($i==0){
                $fraza = Jstring::substr($tmp_str, 0, $limit);
            }
            break;
        }
    }
    $fraza = trim($fraza);
    return $fraza;
} 

function jdreorderDate( $date ) {
	if (defined('BOSS_DATE_FORMAT_LC'))
		$format = BOSS_DATE_FORMAT_LC;
	else
		$format = _DATE_FORMAT_LC;

	if ( $date && preg_match( "|([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})|i", $date, $regs ) ) {
		$date = mktime( 0, 0, 0, $regs[2], $regs[3], $regs[1] );
		$date = $date > -1 ? strftime( $format, $date) : '-';
	}
	return $date;
}

// защита мыла от роботов
function Txt2Png( $text, $directory) {
	global $mosConfig_live_site;

	$png2display = md5($text);
	$filenameforpng = JPATH_BASE."/images/boss/$directory/email/". $png2display . ".png";
	$filename = $mosConfig_live_site."/images/boss/$directory/email/". $png2display . ".png";
	if (!file_exists($filenameforpng)) # we dont need to create file twice (md5)
	{
		# definitions
		$font = JPATH_BASE . "/components/com_boss/font/verdana.ttf";
		# create image / png
		$fontsize = 9;
		$textwerte = imagettfbbox($fontsize, 0, $font, $text);
		$textwerte[2] += 8;
		$textwerte[5] = abs($textwerte[5]);
		$textwerte[5] += 4;
		$image=imagecreate($textwerte[2], $textwerte[5]);
		$farbe_body=imagecolorallocate($image,255,255,255);
		$farbe_b = imagecolorallocate($image,0,0,0);
		$textwerte[5] -= 2;
		imagettftext ($image, 9, 0, 3,$textwerte[5],$farbe_b, $font, $text);
		#display image
		imagepng($image, "$filenameforpng");
	}

	$text = "<img src='".$filename."' border='0' alt='email' />";
	return $text;
}

/**
 * создание нового каталога, sql + необходимае каталоги
 * @param int $installPlugins
 * @return array
 * modification 06.03.2012 GoDr
 */
function installNewDirectory($installPlugins=1) {

	$database = database::getInstance();
        $errors = '';
    $database->setQuery("INSERT INTO `#__boss_config` ( `name`, `slug`, `contents_per_page`, `root_allowed`, `show_contact`, `send_email_on_new`, `send_email_on_update`, `auto_publish`, `fronttext`, `email_display`, `display_fullname`, `rules_text`, `content_duration`, `submission_type`, `nb_contents_by_user`, `allow_attachement`, `allow_contact_by_pms`, `allow_comments`, `secure_comment`, `secure_new_content`, `use_content_mambot`, `show_rss`, `template`, `comment_sys`, `rating`) VALUES 
( 'directory', 'dir', 20, 1, 10, 1, 1, 1, 'Текст приветствия\r\n', 0, 1, 'Это правила...\r\n', 30, 0, -1, 0, 0, 1, 1, 1, 1, 0, 'default', 1, 'defaultRating');");
    $database->query();
	$id = $database->insertid();

	$database->setQuery("UPDATE `#__boss_config` SET `name` = 'directory".$id."' WHERE `id` =".$id);
	$database->query();

	$query =    "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_categories` ( ".
                    "`id` int(10) unsigned NOT NULL auto_increment, ".
                    "`parent` int(10) unsigned default '0', ".
                    "`name` varchar(50) CHARACTER SET utf8 default NULL, ".
                    "`slug` varchar(100) CHARACTER SET utf8 NOT NULL, ".
                    "`meta_title` varchar(60) NOT NULL, ".
                    "`meta_desc` varchar(200) NOT NULL, ".
                    "`meta_keys` varchar(200) NOT NULL, ".
                    "`description` varchar(250) CHARACTER SET utf8 default NULL, ".
                    "`ordering` int(11) default '0', ".
                    "`published` tinyint(1) default '0', ".
                    "`content_types` int(11) default '0', ".
                    "`template` varchar(50) CHARACTER SET utf8 NOT NULL, ".
                    "`rights` TEXT NOT NULL, ".
                    "PRIMARY KEY  (`id`) ".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= $database->stderr();
        }
        
        $query  =   "CREATE TABLE IF NOT EXISTS  `#__boss_".$id."_content_category_href` ( ".
                    "`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , ".
                    "`category_id` INT( 11 ) NOT NULL , ".
                    "`content_id` INT( 11 ) NOT NULL , ".
                    "INDEX (  `category_id` ,  `content_id` ) ".
                    ") ENGINE = MYISAM DEFAULT CHARSET=utf8 ".
                    "COMMENT =  'Привязка контента к категориям';";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

	$query  =   "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_contents` ( ".
                    "`id` int(11) unsigned NOT NULL auto_increment, ".
                    "`name` text CHARACTER SET utf8 , ".
                    "`slug` varchar(100) CHARACTER SET utf8 NOT NULL, ".
                    "`meta_title` varchar(60) NOT NULL, ".
                    "`meta_desc` varchar(200) NOT NULL, ".
                    "`meta_keys` varchar(200) NOT NULL, ".
                    "`userid` int(11) unsigned default NULL, ".
                    "`published` tinyint(1) default '1', ".
                    "`frontpage` tinyint(1) default '0', ".
                    "`featured` tinyint(1) default '0', ".
                    "`date_created` datetime default NULL, ".
                    "`date_last_сomment` datetime DEFAULT NULL, ".
                    "`date_publish` DATETIME NOT NULL, ".
                    "`date_unpublish` DATETIME NOT NULL, ".
                    "`views` int(11) unsigned default '0', ".
                    "`type_content` int(11) NOT NULL, ".
                    "`ordering` int(11) NOT NULL, ".
                    "PRIMARY KEY  (`id`), ".
                    "KEY `published` (`published`)".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

	$query =    "CREATE TABLE  IF NOT EXISTS `#__boss_".$id."_fields` ( ".
                    "`fieldid` int(11) NOT NULL auto_increment, ".
                    "`name` varchar(50) CHARACTER SET utf8 NOT NULL default '', ".
                    "`title` varchar(255) CHARACTER SET utf8 NOT NULL default '', ".
                    "`display_title` tinyint(1) NOT NULL default '0', ".
                    "`description` mediumtext CHARACTER SET utf8 NOT NULL, ".
                    "`type` varchar(50) CHARACTER SET utf8 NOT NULL default '', ".
                    "`text_before` text CHARACTER SET utf8 NOT NULL, ".
                    "`text_after` text CHARACTER SET utf8 NOT NULL, ".
                    "`tags_open` varchar(150) CHARACTER SET utf8 NOT NULL, ".
                    "`tags_separator` varchar(100) CHARACTER SET utf8 NOT NULL, ".
                    "`tags_close` varchar(50) CHARACTER SET utf8 NOT NULL, ".
                    "`maxlength` int(11) default NULL, ".
                    "`size` int(11) default NULL, ".
                    "`required` tinyint(4) default '0', ".
                    "`link_text` varchar( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ',-1,', ".
                    "`link_image` varchar( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ',-1,', ".
                    "`ordering` int(10) unsigned default '0',  ".
                    "`cols` int(11) default NULL, ".
                    "`rows` int(11) default NULL, ".
                    "`profile` tinyint(1) NOT NULL default '0', ".
                    "`editable` tinyint(1) NOT NULL default '1', ".
                    "`searchable` tinyint(1) NOT NULL default '1', ".
                    "`sort` tinyint(1) NOT NULL default '0', ".
                    "`sort_direction` varchar(4) CHARACTER SET utf8 NOT NULL default 'DESC', ".
                    "`catsid` varchar( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ',-1,',  ".
                    "`published` tinyint(1) NOT NULL default '1', ".
                    "PRIMARY KEY  (`fieldid`), ".
                    "`filter` tinyint(1) NOT NULL default '0'".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

	$query =    "CREATE TABLE  IF NOT EXISTS `#__boss_".$id."_field_values` (  ".
                    "`fieldvalueid` int(11) NOT NULL auto_increment,  ".
                    "`fieldid` int(11) NOT NULL default '0',  ".
                    "`fieldtitle` varchar(50) CHARACTER SET utf8 NOT NULL default '',  ".
                    "`fieldvalue` text CHARACTER SET utf8 default NULL,  ".
                    "`ordering` int(11) NOT NULL default '0',  ".
                    "`sys` tinyint(4) NOT NULL default '0',  ".
                    "PRIMARY KEY  (`fieldvalueid`)  ".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;  ";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

	$query =    "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_groups` ( ".
                    "`id` int(11) NOT NULL auto_increment, ".
                    "`name` text CHARACTER SET utf8 NOT NULL, ".
					"`desc` varchar(20) character set utf8 default NULL, ".
                    "`template` varchar(20) character set utf8 default NULL, ".
                    "`type_tmpl` varchar(20) character set utf8 default NULL, ".
                    "`catsid` varchar( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ',-1,',  ".
                    "`published` tinyint(4) NOT NULL default '0', ".
                    "PRIMARY KEY  (`id`) ".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

	$query  =   "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_groupfields` ( ".
                "`fieldid` int(11) NOT NULL default '0', ".
                "`groupid` int(11) NOT NULL default '0', ".
                "`template` varchar(20) character set utf8 default NULL, ".
                "`type_tmpl` varchar(20) character set utf8 default NULL, ".
                "`ordering` int(11) NOT NULL default '0', ".
                "PRIMARY KEY  (`fieldid`,`groupid`), ".
                "KEY `template` (`template`), ".
                "KEY `type_tmpl` (`type_tmpl`) ".
                    ")ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }
        
        $query =    "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_rating` ( ".
                    "`id` int(10) unsigned NOT NULL auto_increment, ".
                    "`contentid` int(10) unsigned default NULL, ".
                    "`userid` int(10) unsigned default NULL, ".
                    "`note` int(10) unsigned default NULL, ".
                    "`ip` varchar(255) CHARACTER SET utf8 default NULL, ".
                    "`date` datetime default NULL, ".
                    "`published` tinyint(1) default '1', ".
                    "PRIMARY KEY  (`id`) ".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

	$query=     "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_reviews` ( ".
                    "`id` int(10) unsigned NOT NULL auto_increment, ".
                    "`contentid` int(10) unsigned default NULL, ".
                    "`userid` int(10) unsigned default NULL, ".
                    "`title` varchar(255) CHARACTER SET utf8 default NULL, ".
                    "`description`  text CHARACTER SET utf8 default NULL, ".
                    "`date` date default NULL, ".
                    "`published` tinyint(1) default '1', ".
                    "PRIMARY KEY  (`id`) ".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

        $query=     "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_content_types` (
                    `id` int(11) NOT NULL auto_increment,
                    `name` varchar(50) NOT NULL,
                    `desc` varchar(255) NOT NULL,
                    `fields` tinyint(1) NOT NULL default '0',
                    `published` tinyint(1) NOT NULL,
                    `ordering` int(11) NOT NULL,
                    PRIMARY KEY  (`id`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
        $database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

	$query =    "CREATE TABLE IF NOT EXISTS `#__boss_".$id."_profile` ( ".
                    "`userid` int(11) NOT NULL default '0', ".
                    "PRIMARY KEY  (`userid`) ".
                    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$database->setQuery($query);
	$database->query();
        if ($database->getErrorNum()) {
            $errors .= '<br />'.$database->stderr();
        }

    if(!is_dir(JPATH_BASE . "/images/boss/")) {
		@mkdir(JPATH_BASE . "/images/boss/");
        @copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/index.html");
	};

	if(!is_dir(JPATH_BASE . "/images/boss/$id/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/");
		@copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/index.html");
        @copy(JPATH_BASE . "/administrator/components/com_boss/cron.php", JPATH_BASE . "/images/boss/$id/cron.php");
	};

	if(!is_dir(JPATH_BASE . "/images/boss/$id/categories/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/categories/");
        @copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/categories/index.html");
	};

	if(!is_dir(JPATH_BASE . "/images/boss/$id/contents/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/contents/");
        @copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/contents/index.html");
	};

	if(!is_dir(JPATH_BASE . "/images/boss/$id/email/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/email/");
        @copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/email/index.html");
	};

	if(!is_dir(JPATH_BASE . "/images/boss/$id/fields/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/fields/");
        @copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/fields/index.html");
	};

    if(!is_dir(JPATH_BASE . "/images/boss/$id/files/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/files/");
        @copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/files/index.html");
	};

	if(!is_dir(JPATH_BASE . "/images/boss/$id/plugins/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/plugins/");
	};

    if(!is_dir(JPATH_BASE . "/images/boss/$id/lang/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/lang/");
        @copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/lang/index.html");
	};

	if(!is_dir(JPATH_BASE . "/images/boss/$id/js/")) {
		@mkdir(JPATH_BASE . "/images/boss/$id/js/");
		@copy(JPATH_BASE . "/images/index.html", JPATH_BASE . "/images/boss/$id/js/index.html");
		$f = fopen (JPATH_BASE . "/images/boss/$id/js/front.js", "w");
		fclose($f);
		$f = fopen (JPATH_BASE . "/images/boss/$id/js/admin.js", "w");
		fclose($f);
	};

	if($installPlugins == 1){
        boss_helpers::copy_folder_rf(JPATH_BASE . "/components/com_boss/plugins", JPATH_BASE . "/images/boss/$id/plugins");
    }
    
    return array('id'=>$id, 'errors'=>$errors);
}

function backup_table_structure($directory, $file, $table) {
    $database = database::getInstance();
    global $mosConfig_dbprefix;
    //получение и сохранение структуры таблицы
    $content = '$query = "DROP TABLE IF EXISTS `' . str_replace($mosConfig_dbprefix.'boss_'.$directory.'_', '#__boss_".$directory."_', $table) .'`";'."\n";
    $content .= '$database->setQuery($query);'."\n";
	$content .= '$database->query();'."\n\n";
    $result = $database->getTableCreate(array($table));
    $result = implode(' ',$result);
    $result = str_replace($mosConfig_dbprefix.'boss_'.$directory.'_', '#__boss_".$directory."_', $result);
    $content .= '$query = "'.$result . ';";'."\n";
    $content .= '$database->setQuery($query);'."\n";
	$content .= '$database->query();'."\n\n";
    file_put_contents($file, $content, FILE_APPEND);
}


function backup_table_data($directory, $file, $table) {
    global $mosConfig_dbprefix;
    $database = database::getInstance();
    //получение и сохранение данных таблицы
    $database->setQuery('SELECT COUNT(*) FROM `' . $table . '`;');
    $count = $database->loadResult();
    $delta = 500;

    //если данные существуют
    if ($count > 0) {
        //определяем не числовые поля
        $not_num = array();
        $result = $database->getTableFields(array($table));
        foreach ($result[$table] as $key => $value) {
            if (!preg_match("/^(tinyint|smallint|mediumint|bigint|int|float|double|real|decimal|numeric|year)/", $value)) {
                $not_num[$key] = 1;
            }
        }
        //начинаем производить выборки данных
        $start = 0;
        while ($count > 0) {
            $database->setQuery('SELECT * FROM `' . $table . '` LIMIT ' . $start . ', ' . $delta . ';');
            $result = $database->loadAssocList();
            $content = '$query = "INSERT INTO `' . str_replace($mosConfig_dbprefix.'boss_'.$directory.'_', '#__boss_".$directory."_', $table) . '` VALUES ';
            $first = true;
            foreach ($result as $row) {
                $content .= $first ? "\n(" : ",\n(";
                $first2 = true;
                foreach ($row as $index => $field) {
                    if (isset($not_num[$index])) {
                        $field = addslashes($field);
                        $field = preg_replace("/\n/", "/\/\n/", $field);
                        $content .= !$first2 ? (",'" . $field . "'") : ("'" . $field . "'");
                    } else {
                        $content .= !$first2 ? (",'" . $field . "'") : ("'" . $field . "'");
                    }
                    $first2 = false;
                }
                $content .= ')';
                $first = false;
            }
            $content .= '";'."\n";
            $content .= '$database->setQuery($query);'."\n";
	        $content .= '$database->query();';

            //сохраняем результаты выборки
            file_put_contents($file, $content . "\n\n", FILE_APPEND);
            $count -= $delta;
            $start += $delta;
        }
    }
}

function copyFolder($patchFrom, $patchTo) {
    require_once JPATH_BASE.'/includes/libraries/filesystem/folder.php';

    $foldersFrom = JFolder::folders($patchFrom, '.', true, true);
    $filesFrom = JFolder::files($patchFrom, '.', true, true);

    if(!is_dir($patchTo))
        mkdir($patchTo);

    foreach($foldersFrom as $folderFrom) {
      $folderTo = str_replace($patchFrom, $patchTo, $folderFrom);
      if(!is_dir($folderTo))
          mkdir($folderTo);
    }

    foreach($filesFrom as $fileFrom) {
      $fileTo = str_replace($patchFrom, $patchTo, $fileFrom);
      if(!is_file($fileTo) && is_file($fileFrom))
          copy($fileFrom, $fileTo);
    }
}

function zipFolder($patchFrom, $pack_name) {
    require_once JPATH_BASE.'/includes/libraries/filesystem/folder.php';
    // подключаем файлы классов
    require_once( JPATH_BASE . '/administrator/includes/pcl/pclzip.lib.php' );
	require_once( JPATH_BASE . '/administrator/includes/pcl/pclerror.lib.php' );
    //получаем массив файлов
    $filesFrom = JFolder::files($patchFrom, '.', true, true);
    //удаляем старый архив если он есть
    if (file_exists($pack_name)) {unlink($pack_name);}
    //объявляем новый экземпляр класса архива
    $zipfile = new PclZip($pack_name);
    //вычисляем ОС
	if(substr(PHP_OS, 0, 3) == 'WIN') {
		define('OS_WINDOWS',1);
	} else {
		define('OS_WINDOWS',0);
	}
    //убираем букву диска с двоеточим из начала пути если винда, иначе не работает
    if(OS_WINDOWS == 1) $patchFrom = substr($patchFrom, 2);
    //в цикле добавляем каждый файл к архиву.
	foreach($filesFrom as $fileFrom){
        $list = $zipfile->add($fileFrom, PCLZIP_OPT_REMOVE_PATH, $patchFrom);
        //если ошибка, то печатаем
        if ($list == 0) {
            echo "ERROR : ".$zipfile->errorInfo(true);
        }
    }

}

// get configuration
function getConfig($directory) {
	$database = database::getInstance();
    $conf = null;
	$database->setQuery("SELECT * FROM #__boss_config WHERE id = $directory",0,1)->loadObject($conf);
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	return $conf;
}

function russian_transliterate($string, $toLower = 1) {

    $converter = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v',
        'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V',
        'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
        'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
        'И' => 'I', 'Й' => 'Y', 'К' => 'K',
        'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U',
        'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
        'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ' ' => '_', '  ' => '_'
    );
    
    $string = strtr($string, $converter);
    
    if($toLower == 1){
        $string = Jstring::strtolower($string);
    }
    return $string;
}

class bossQuotes{

    // Функция возвращает текст с расставленными кавычками
	public static function replaceQuotes ($str) {

		// Замена "_" на «_»
		if( Jstring::stristr( $str, '"' ) ) {
			$str = preg_replace( '/([\x01\(  ]|^)"([^"]*)([^  "\(])"/', '\\1«\\2\\3»', $str );
			$str = self::doubleQuote ($str);// Замена вложенных кавычек "_" на („ “)
			$str = self::singleQuote ($str);// Замена вложенных кавычек '_' на („ “)
			$str = self::obliqueQuote ($str);// Замена вложенных кавычек `_` на („ “)
		}

		// Замена '_' на «_»
		if( Jstring::stristr( $str, "'" ) ) {
			$str = preg_replace( "/([\x01\(  ]|^)'([^']*)([^  '\(])'/", "\\1«\\2\\3»", $str );
			$str = self::singleQuote ($str);// Замена вложенных кавычек '_' на („ “)
			$str = self::doubleQuote ($str);// Замена вложенных кавычек "_" на („ “)
			$str = self::obliqueQuote ($str);// Замена вложенных кавычек `_` на („ “)
		}

		// Замена `_` на «_»
		if( Jstring::stristr( $str, "`" ) ) {
			$str = preg_replace( "/([\x01\(  ]|^)`([^`]*)([^  `\(])`/", "\\1«\\2\\3»", $str );
			$str = self::obliqueQuote ($str);// Замена вложенных кавычек `_` на („ “)
			$str = self::doubleQuote ($str);// Замена вложенных кавычек "_" на („ “)
			$str = self::singleQuote ($str);// Замена вложенных кавычек '_' на („ “)
		}

		return $str;
	}//replace_Quotes ($str)

	// Замена вложенных кавычек "_" на („ “)
	public static function doubleQuote ($str) {
		if( Jstring::stristr( $str, '"' ) ) {
			$str = preg_replace( '/([\x02(  ]|^)"([^"]*)([^  "(])"/', '\\1«\\2\\3»', $str );
			while( preg_match( '/«[^»]*«[^»]*»/', $str ) )
				$str = preg_replace( '/«([^»]*)«([^»]*)»/', '«\\1„\\2“', $str );
		}
		return $str;
	}
	// Замена вложенных кавычек '_' на („ “)
	public static function singleQuote ($str) {
		if( Jstring::stristr( $str, "'" ) ) {
			$str = preg_replace( "/([\x02(  ]|^)'([^']*)([^  '(])'/", "\\1«\\2\\3»", $str );
			while( preg_match( "/«[^»]*«[^»]*»/", $str ) )
				$str = preg_replace( "/«([^»]*)«([^»]*)»/", "«\\1„\\2“", $str );
		}
		return $str;
	}
	// Замена вложенных кавычек `_` на („ “)
	public static function obliqueQuote ($str) {
		if( Jstring::stristr( $str, "`" ) ) {
			$str = preg_replace( "/([\x02(  ]|^)`([^`]*)([^  `(])`/", "\\1«\\2\\3»", $str );
			while( preg_match( "/«[^»]*«[^»]*»/", $str ) )
				$str = preg_replace( "/«([^»]*)«([^»]*)»/", "«\\1„\\2“", $str );
		}
		return $str;
	}
}
