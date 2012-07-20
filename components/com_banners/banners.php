<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

// including classes
require_once ($mainframe->getPath('class'));

$id = intval(mosGetParam($_REQUEST, 'id', 0));
$task = strval(mosGetParam($_REQUEST, 'task', ''));

switch($task){
	case 'clk':
		clickArtBanner($id);
		break;

	case 'statistics':
		showStatistics($id);
		break;
}

/* Function to redirect the clicks to the correct url and add 1 click
*/
function clickArtBanner($id){
	$database = database::getInstance();

	$banner = new mosArtBanner($database);
	$banner->load($id);
	$banner->clicks();

	$click_url = $banner->click_url; //default
	if(!preg_match('/http:\/\//i', $banner->click_url))
		$click_url = "http://$banner->click_url";

	mosRedirect($click_url);
}

function showStatistics($id){
	$database = database::getInstance();

	$password = strval(mosGetParam($_REQUEST, 'password', ''));

	if($id == 0 || $password == ""){
		echo _ABP_FAILED_ACCESS;
	} else{
		$banner = new mosArtBanner($database);
		$banner->load($id);

		// verifico password
		if($password == mosHash($banner->password)){
			echo '<br>';
			echo '<b>', _ABP_CLICKS, '</b>', '&nbsp;:&nbsp;', $banner->complete_clicks, ' / ', $banner->clicks, ' ( dal ', $banner->dta_mod_clicks, ' )<br><br>';
			echo '<b>', _ABP_IMPMADE, '</b>', '&nbsp;:&nbsp;', $banner->imp_made, '<br><br>';
			echo '<b>', _ABP_TOT_IMP_CLIC, '</b>', '&nbsp;:&nbsp; ', $banner->click_value * $banner->complete_clicks + $banner->imp_value * $banner->imp_made, '<br><br>';

			if($banner->custom_banner_code != ""){
				echo $banner->custom_banner_code;
			} else
				if(preg_match("/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i", $banner->image_url)){
					$image_url = JPATH_SITE . "/images/show/$banner->image_url";
					$imginfo = @getimagesize("JPATH_BASE/images/show/" . $banner->image_url);
					$border_value = $banner->border_value;
					$border_style = $banner->border_style;
					$border_color = $banner->border_color;
					$width = $imginfo[0] / 1.23;
					$height = $imginfo[1] / 1.23;
					echo "<img src=\"" . $image_url . "\" style=\"border:" . $border_value . "px " . $border_style . " " . $border_color . "\" vspace=\"0\" alt=\"$banner->name\" width=\"$width\" height=\"$height\" />";
				} else
					if(preg_match("/.swf/i", $banner->image_url)){
						$image_url = JPATH_SITE . "/images/show/" . $banner->image_url;
						$swfinfo = @getimagesize("JPATH_BASE/images/show/" . $banner->image_url);
						$width = $swfinfo[0] / 1.23;
						$height = $swfinfo[1] / 1.23;
						echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,2,0\" border=\"0\" width=\"$width\" height=\"$height\" vspace=\"0\"><param name=\"SRC\" value=\"$image_url\"><embed src=\"$image_url\" loop=\"false\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\"></object>";
					}
		} else{
			echo _COM_BANNERS_NONE_ACCESS;
		}
	}
}