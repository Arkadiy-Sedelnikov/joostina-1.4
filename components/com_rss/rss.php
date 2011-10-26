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


global $task;

// load feed creator class
mosMainFrame::addLib('feedcreator');
mosMainFrame::addLib('html_optimize');

$info = null;
$rss = null;

switch($task) {
	case 'live_bookmark':
		feedFrontpage(false);
		break;

	default:
		feedFrontpage(true);
		break;
}

/*
* Creates feed from Content Iems associated to teh frontpage component
*/
function feedFrontpage($showFeed) {
	$mainframe = mosMainFrame::getInstance();

	$database = $mainframe->getDBO();
	$config = &$mainframe->config;


	$nullDate = $database->getNullDate();
	// pull id of syndication component
	$query = "SELECT a.id FROM #__components AS a WHERE ( a.admin_menu_link = 'option=com_syndicate' OR a.admin_menu_link = 'option=com_syndicate&hidemainmenu=1' ) AND a.option = 'com_syndicate'";
	$database->setQuery($query);
	$id = $database->loadResult();

	// load syndication parameters
	$component = new mosComponent($database);
	$component->load((int)$id);
	$params = new mosParameters($component->params);

	// test if security check is enbled
	$check = $params->def('check',1);
	if($check) {
		// test if rssfeed module is published
		// if not disable access
		$query = "SELECT m.id FROM #__modules AS m WHERE m.module = 'mod_rssfeed' AND m.published = 1";
		$database->setQuery($query);
		$check = $database->loadResultArray();
		if(empty($check)) {
			mosNotAuth();
			return;
		}
	}

	$now = _CURRENT_SERVER_TIME;
	$iso = explode('=',_ISO);

	// parameter intilization
	$info['date'] = date('r');
	$info['year'] = date('Y');
	$info['encoding'] = $iso[1];
	$info['link'] = htmlspecialchars(JPATH_SITE);
	$info['cache'] = $params->def('cache',1);
	$info['cache_time'] = $params->def('cache_time',3600);
	$info['count'] = $params->def('count',5);
	$info['orderby'] = $params->def('orderby','');
	$info['title'] = $params->def('title',$config->config_sitename);
	$info['description'] = $params->def('description',$config->config_MetaDesc);
	$info['image_file'] = $params->def('image_file','joostina_rss.png');
	if($info['image_file'] == -1) {
		$info['image'] = null;
	} else {
		$info['image'] = JPATH_SITE.'/images/M_images/'.$info['image_file'];
	}
	$info['image_alt'] = $params->def('image_alt','Joostina CMS!');
	$info['limit_text'] = $params->def('limit_text',0);
	$info['text_length'] = $params->def('text_length',20);
	// get feed type from url
	$info['feed'] = strval(mosGetParam($_GET,'feed','RSS2.0'));
	// live bookmarks
	$info['live_bookmark'] = $params->def('live_bookmark','');
	$info['bookmark_file'] = $params->def('bookmark_file','');

	// set filename for live bookmarks feed
	if(!$showFeed & $info['live_bookmark']) {
		if($info['bookmark_file']) {
			// custom bookmark filename
			$filename = $info['bookmark_file'];
		} else {
			// standard bookmark filename
			$filename = $info['live_bookmark'];
		}
	} else {
		// set filename for rss feeds
		$info['file'] = strtolower(str_replace('.','',$info['feed']));

		// security check to limit arbitrary file creation.
		// and to allow disabling/enabling of selected feed types
		switch($info['file']) {
			case 'rss091':
				if(!$params->get('rss091',1)) {
					echo _NOT_AUTH;
					return;
				}
				break;

			case 'rss10':
				if(!$params->get('rss10',1)) {
					echo _NOT_AUTH;
					return;
				}
				break;

			case 'rss20':
				if(!$params->get('rss20',1)) {
					echo _NOT_AUTH;
					return;
				}
				break;

			case 'atom03':
				if(!$params->get('atom03',1)) {
					echo _NOT_AUTH;
					return;
				}
				break;

			case 'opml':
				if(!$params->get('opml',1)) {
					echo _NOT_AUTH;
					return;
				}
				break;


			case 'yandex':
				$yes_yandex = 1;
				if(!$params->get('yandex',1)) {
					echo _NOT_AUTH;
					return;
				}
				break;

			default:
				echo _NOT_AUTH;
				return;
				break;
		}
	}
	$filename = $info['file'].'.xml';

	// security check to stop server path disclosure
	if(strstr($filename,'/')) {
		echo _NOT_AUTH;
		return;
	}
	$info['file'] = $config->config_cachepath.DS.$filename;

	// load feed creator class
	$rss = new UniversalFeedCreator();
	// load image creator class
	$image = new FeedImage();

	// loads cache file
	if($showFeed && $info['cache']) {
		$rss->useCached($info['feed'],$info['file'],$info['cache_time']);
	}

	$rss->title = $info['title'];
	$rss->description = $info['description'];
	$rss->link = $info['link'];
	$rss->syndicationURL = $info['link'];
	$rss->cssStyleSheet = null;
	$rss->encoding = $info['encoding'];

	if($info['image']) {
		$image->url = $info['image'];
		$image->link = $info['link'];
		$image->title = $info['image_alt'];
		$image->description = $info['description'];
		// loads image info into rss array
		$rss->image = $image;
	}

	// Determine ordering for sql
	switch(strtolower($info['orderby'])) {
		case 'date':
			$orderby = 'a.created';
			break;

		case 'rdate':
			$orderby = 'a.created DESC';
			break;

		case 'alpha':
			$orderby = 'a.title';
			break;

		case 'ralpha':
			$orderby = 'a.title DESC';
			break;

		case 'hits':
			$orderby = 'a.hits DESC';
			break;

		case 'rhits':
			$orderby = 'a.hits ASC';
			break;

		case 'front':
			$orderby = 'f.ordering';
			break;

		default:
			$orderby = 'f.ordering';
			break;
	}

	// query of frontpage content items
	$query = "SELECT a.*, u.name AS author, u.usertype, UNIX_TIMESTAMP( a.created ) AS created_ts, cat.title AS cat_title, sec.title AS section_title".
			"\n FROM #__content AS a INNER JOIN #__content_frontpage AS f ON f.content_id = a.id".
			"\n LEFT JOIN #__users AS u ON u.id = a.created_by LEFT JOIN #__categories AS cat ON cat.id = a.catid".
			"\n LEFT JOIN #__sections AS sec ON sec.id = a.sectionid WHERE a.state = 1".
			"\n AND cat.published = 1 AND sec.published = 1 AND a.access = 0 AND cat.access = 0".
			"\n AND sec.access = 0 AND ( a.publish_up = ".$database->Quote($nullDate).
			" OR a.publish_up <= ".$database->Quote($now)." ) AND ( a.publish_down = ".$database->Quote($nullDate)." OR a.publish_down >= ".$database->Quote($now)." ) ORDER BY $orderby";
	$database->setQuery($query,0,$info['count']);
	$rows = $database->loadObjectList();

	foreach($rows as $row) {
		// title for particular item
		$item_title = htmlspecialchars($row->title);
		$item_title = html_entity_decode($item_title);

		// url link to article
		// & used instead of &amp; as this is converted by feed creator
		$_Itemid = '';
		$itemid = $mainframe->getItemid($row->id);
		if($itemid) {
			$_Itemid = '&Itemid='.$itemid;
		}

		$item_link = 'index.php?option=com_content&task=view&id='.$row->id.$_Itemid;
		$item_link = sefRelToAbs($item_link);

		// removes all formating from the intro text for the description text
		$item_description = $row->introtext;
		$item_description = mosHTML::cleanText($item_description);
		$item_description = html_entity_decode($item_description);
		if($info['limit_text']) {
			if($info['text_length']) {
				// limits description text to x words
				$item_description_array = explode(' ',$item_description);
				$count = count($item_description_array);
				if($count > $info['text_length']) {
					$item_description = '';
					for($a = 0; $a < $info['text_length']; $a++) {
						$item_description .= $item_description_array[$a].' ';
					}
					$item_description = trim($item_description);
					$item_description .= '...';
				}
			} else {
				// do not include description when text_length = 0
				$item_description = null;
			}
		}

		// load individual item creator class
		$item = new FeedItem();
		// item info
		$item->title = $item_title;
		$item->link = $item_link;
		$item->description = $item_description;
		$item->source = $info['link'];
		$item->date = date('r',$row->created_ts);
		$item->category = $row->section_title.' - '.$row->cat_title;
		// патч для экспорта новостей в Yandex ленту

		if(isset($yes_yandex)) {
			// yandex export
			$item->fulltext = $row->fulltext?$row->introtext.$row->fulltext:$row->introtext;
			$item->fulltext = htmlspecialchars(strip_tags($item->fulltext));
			$item->fulltext = str_replace("'","&apos;",$item->fulltext);
			$item->fulltext = preg_replace('/{mosimage\s*.*?}/iu','',$item->fulltext);

			if($row->images) {
				$item->images = array();
				$row->images = explode("\n",$row->images);
				foreach($row->images as $img) {
					$img = trim($img);
					if($img) {
						$temp = explode('|',trim($img));
						$item->images[] = '/images/stories/'.$temp[0];
					}
				}
			}
			// yandex export
		}
		$item->fulltext = isset($item->fulltext) ? html_optimize($item->fulltext) : '';
		// loads item info into rss array
		$rss->addItem($item);
	}

	// save feed file
	$rss->saveFeed($info['feed'],$info['file'],$showFeed);
}