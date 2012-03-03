<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

$_MAMBOTS->registerFunction('onPrepareContent','botMosImage');

/**
 */
function botMosImage($published,&$row,&$params) {
    $_MAMBOTS = mosMambotHandler::getInstance();
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    $database = database::getInstance();

	// simple performance check to determine whether bot should process further
	if(strpos($row->text,'mosimage') === false) {
		return true;
	}

	// expression to search for
	$regex = '/{mosimage\s*.*?}/i';

	// check whether mosimage has been disabled for page
	// check whether mambot has been unpublished
	if(!$published || !$params->get('image')) {
		$row->text = preg_replace($regex,'',$row->text);
		return true;
	}

	//count how many {mosimage} are in introtext if it is set to hidden.
	$introCount = 0;
	if(!$params->get('introtext') & !$params->get('intro_only')) {
		preg_match_all($regex,$row->introtext,$matches);
		$introCount = count($matches[0]);
	}

	// найти все образцы мамбота и вставить в $matches
	preg_match_all($regex,$row->text,$matches);

	// Количество мамботов
	$count = count($matches[0]);

	// мамбот производит обработку, если находит в тексте образцы мамбота
	if($count) {
		// check if param query has previously been processed
		if(!isset($_MAMBOTS->_content_mambot_params['mosimage'])) {
			// load mambot params info
			$query = "SELECT params FROM #__mambots WHERE element = 'mosimage' AND folder = 'content'";
			$database->setQuery($query);
			$database->loadObject($mambot);
			$_MAMBOTS->_content_mambot_params['mosimage'] = $mambot;
		}

		// pull query data from class variable
		$mambot = $_MAMBOTS->_content_mambot_params['mosimage'];
		$botParams = new mosParameters($mambot->params);

		$botParams->def('padding');
		$botParams->def('margin');
		$botParams->def('link',0);

		$images = processImages($row,$botParams,$introCount);

		// сохранение в глобальных переменных некоторых переменных для доступа из программы замены
		$GLOBALS['botMosImageCount'] = 0;
		$GLOBALS['botMosImageArray'] = &$images;

		// выполнение замены
		$row->text = preg_replace_callback($regex,'botMosImage_replacer',$row->text);

		// приведение в порядок глобальных значений
		unset($GLOBALS['botMosImageCount'],$GLOBALS['botMosImageArray']);
		return true;
	}
	return true;
}

function processImages(&$row,&$params,&$introCount) {
	$mainframe = mosMainFrame::getInstance();;

	$images = array();
	$div_style ='';
	// выдача  \n образов полей как массив
	$row->images = explode("\n",$row->images);
	$total = count($row->images);

	$start = $introCount;
	for($i = $start; $i < $total; $i++) {
		$img = trim($row->images[$i]);

		// разбиение атрибутов изображения
		if($img) {
			$attrib = explode('|',trim($img));
			// $attrib[0] - название изображения и путь до /images/stories

			// $attrib[1] выравнивание
			if(!isset($attrib[1]) || !$attrib[1]) {
				$attrib[1] = '';
			}

			// $attrib[2] альтернативный текст и заголовок
			if(!isset($attrib[2]) || !$attrib[2]) {
				$attrib[2] = $mainframe->getPageTitle().' #'.$i;
			} else {
				$attrib[2] = htmlspecialchars($attrib[2]);
			}

			// $attrib[3] рамка
			if(!isset($attrib[3]) || !$attrib[3]) {
				$attrib[3] = 0;
			}

			// $attrib[4] заголовок
			if(!isset($attrib[4]) || !$attrib[4]) {
				$attrib[4] = '';
				$border = $attrib[3];
			} else {
				$border = 0;
			}

			// $attrib[5] позиция заголовка
			if(!isset($attrib[5]) || !$attrib[5]) {
				$attrib[5] = '';
			}

			// $attrib[6] выравнивание заголовка
			if(!isset($attrib[6]) || !$attrib[6]) {
				$attrib[6] = '';
			}

			// $attrib[7] ширина
			if(!isset($attrib[7]) || !$attrib[7]) {
				$attrib[7] = '';
				$width = '';
			} else {
				$width = ' width: '.$attrib[7].'px;';
			}

			// атрибуты размера изображения
			$size = '';
			if(function_exists('getimagesize')) {
				$size = @getimagesize(JPATH_BASE.'/images/stories/'.$attrib[0]);
				if(is_array($size)) {
					$size = ' width="'.$size[0].'" height="'.$size[1].'"';
				}
			}

			// составление тэга <image>
			$image = '<img src="'.JPATH_SITE.'/images/stories/'.$attrib[0].'"'.$size;
			// если обнаружен заголовок, то выравнивание не меняется
			if(!$attrib[4]) {
				if($attrib[1] == 'left' or $attrib[1] == 'right') {
					$image .= ' style="float: '.$attrib[1].';"';
					$div_style = ' style="float: '.$attrib[1].';"';
				} else {
					$image .= $attrib[1]?' align="middle"':'';
				}
			}
			$image .= ' alt="'.$attrib[2].'" title="'.$attrib[2].'" border="'.$border.'" />';

			// создание заголовка если он обнаружен
			$caption = '';
			if($attrib[4]) {
				$caption = '<div class="mosimage_caption"';
				if($attrib[6]) {
					$caption .= ' style="text-align: '.$attrib[6].';"';
					$caption .= ' align="'.$attrib[6].'"';
				}
				$caption .= '>';
				$caption .= $attrib[4];
				$caption .= '</div>';
			}

			// заключительный вывод
			if($attrib[4]) {
				// initialize variables
				$margin = '';
				$padding = '';
				$float = '';
				$border_width = '';
				$style = '';
				if($params->def('margin')) {
					$margin = ' margin: '.$params->def('margin').'px;';
				}
				if($params->def('padding')) {
					$padding = ' padding: '.$params->def('padding').'px;';
				}
				if($attrib[1]) {
					$float = ' float: '.$attrib[1].';';
				}
				if($attrib[3]) {
					$border_width = ' border-width: '.$attrib[3].'px;';
				}

				if($params->def('margin') || $params->def('padding') || $attrib[1] || $attrib[3]) {
					$style = ' style="'.$border_width.$float.$margin.$padding.$width.'"';
				}

				$img = '<div class="mosimage" '.$style.' align="center">';

				// display caption in top position
				if($attrib[5] == 'top' && $caption) {
					$img .= $caption;
				}

				$img .= $image;

				// отображение заголовка в нижней позиции
				if($attrib[5] == 'bottom' && $caption) {
					$img .= $caption;
				}
				$img .= '</div>';
			} else {
				$img = '<div class="mosimage"'. $div_style .' >' . $image . '</div>';
			}

			$images[] = $img;
		}
	}

	return $images;
}

/**
 * Замена совпадающих тэгов an image
 * @param array - Массив соответствий (см. - preg_match_all)
 * @return string
 */
function botMosImage_replacer(&$matches) {
	$i = $GLOBALS['botMosImageCount']++;
	return @$GLOBALS['botMosImageArray'][$i];
}