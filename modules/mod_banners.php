<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

if(!function_exists('showBanners')) {
	// function that selecting one or more banner/s
	function showBanners(&$params, $mainframe) {
		global $my;

		$database = $mainframe->getDBO();

		$random = $params->get('random', 0);
		$count = $params->get('count', 1);
		$banners = $params->get('banners');
		$categories = $params->get('categories');
		$clients = $params->get('clients');
		$orientation = $params->get('orientation', 0);
		$moduleclass_sfx = $params->get('moduleclass_sfx');
		$text = $params->get('text',false);

		$weekday = mosCurrentDate("%w");
		$date = mosCurrentDate("%Y-%m-%d");
		$time = mosCurrentDate("%H:%M:%S");

		$where = array();
		if($categories != '') {
			$where[] = "b.tid IN ($categories)";
		}

		if($banners != '') {
			$where[] = "b.id IN ($banners)";
		}

		if($clients != '') {
			$where[] = "b.cid IN ($clients)";
		}

		if(count($where) > 0)
			$where = '(' . implode(' OR ', $where) . ') AND';
		else
			$where = '';

		$query ="SELECT b.* FROM #__banners AS b
		INNER JOIN #__banners_categories AS cat ON ( cat.published =1 AND cat.id = b.tid )
		INNER JOIN #__banners_clients AS cl ON ( cl.published =1 AND cl.cid = b.cid )
		WHERE b.access <= '$my->gid' AND b.state = '1'
		AND $where (
			('$date' <= b.publish_down_date OR b.publish_down_date = '0000-00-00')
			AND '$date' >= b.publish_up_date
			AND ((b.reccurtype =0) OR (b.reccurtype =1 AND b.reccurweekdays LIKE '%$weekday%'))
			AND '$time' >= b.publish_up_time
			AND ('$time' <= b.publish_down_time OR b.publish_down_time = '00:00:00')
		)
		ORDER BY b.last_show ASC , b.msec ASC";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		$numrows = count($rows);
		if(!$numrows) {
			return '&nbsp;';
		}

		$result = '<table cellpadding="0" cellspacing="0" class="banners' . $moduleclass_sfx . '">';

		if($random && $count == 1) {

			$bannum = 0;
			if($numrows > 1) {
				$numrows--;
				mt_srand((double)microtime() * 1000000);
				$bannum = mt_rand(0, $numrows);
			}

			if($numrows) {
				$result .= '<tr><td>' . showSingleBanner($text, $moduleclass_sfx, $rows[$bannum],$mainframe) . '</td></tr></table>';
				return $result;
			}
		}

		$showed = 0;

		$first = false;
		foreach($rows as $row) {

			//'0' -> Vertical
			//'1' -> Horizontal
			if($orientation == '0') {
				$result .= '<tr><td>' . showSingleBanner($text, $moduleclass_sfx, $row, $mainframe) . '</td></tr>';
			} else {

				if($first == false) {
					$result .= '<tr>';
					$first = true;
				}

				$result .= '<td>' . showSingleBanner($text, $moduleclass_sfx, $row, $mainframe) . '</td>';
			}

			$showed++;
			if($showed == $count) {
				break;
			}
		}

		if($orientation == '1') {
			$result .= '</tr>';
		}

		$result .= '</table>';

		return $result;
	}

	// function that showing one banner
	function showSingleBanner($text, $moduleclass_sfx, &$banner, $mainframe) {
		$database = $mainframe->getDBO();

		$result = '';

		$secs = gettimeofday();
		$database->setQuery("UPDATE #__banners SET imp_made=imp_made+1, last_show='" . mosCurrentDate("%Y-%m-%d %H:%M:%S") . "', msec='" . $secs["usec"] . "' WHERE id='$banner->id'");
		$database->query();

		$banner->imp_made++;
		if($banner->imp_total == $banner->imp_made) {
			$database->setQuery("UPDATE #__banners SET state='0' WHERE id='$banner->id'");
			$database->query();
		}

		if($banner->custom_banner_code != "") {
			$result .= $banner->custom_banner_code;
		} elseif(preg_match("/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i", $banner->image_url)) {
			$image_url = JPATH_SITE.'/images/show/'.$banner->image_url;
			$target = $banner->target;
			$border_value = $banner->border_value;
			$border_style = $banner->border_style;
			$border_color = $banner->border_color;
			$alt = $banner->name;

			if($banner->alt != '') {
				$alt = $banner->alt;
			}

			$title = $banner->title;
			$result = "<a href=\"".sefRelToAbs('index.php?option=com_banners&amp;task=clk&amp;id='.$banner->id)."\" target=\"_" . $target . "\"><img src=\"" . $image_url . "\" style=\"border:" . $border_value . "px " . $border_style ." " . $border_color . "\" vspace=\"0\" alt=\"$alt\" title=\"$title\"/></a>";
			if($text){
				$result = "<a href=\"".sefRelToAbs('index.php?option=com_banners&amp;task=clk&amp;id='.$banner->id)."\" target=\"_" . $target . "\" class='bantxt".$moduleclass_sfx."'>".$alt."</a><br />".$result;
			}
			$result = '<div class="banernblok'.$moduleclass_sfx.'">'.$result.'</div>';
		} elseif(preg_match("/.swf/", $banner->image_url)) {
			$image_url = JPATH_SITE.'/images/show/' . $banner->image_url;
			$swfinfo = @getimagesize(JPATH_BASE.'/images/banners/'. $banner->image_url);
			$result = "
                <object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
                codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\"
                border=\"0\"
                width=\"$swfinfo[0]\"
                height=\"$swfinfo[1]\"
                vspace=\"0\">
                    <param name=\"SRC\" value=\"$image_url\" />
                        <embed src=\"$image_url\"
                            loop=\"false\"
                            pluginspage=\"http://www.macromedia.com/go/get/flashplayer\"
                            type=\"application/x-shockwave-flash\"
                            width=\"$swfinfo[0]\"
                            height=\"$swfinfo[1]\">
                </object>
                ";
		}

		return $result;
	}
}

$params = new mosParameters($module->params);
$content = showBanners( $params, $mainframe );
unset($params, $mainframe);

