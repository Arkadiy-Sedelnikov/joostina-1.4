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

/** Handles standard Joomla Content */
class xmap_com_content {

	/** return a node-tree */
	public static function getTree($xmap, $parent, $params) {
		$result = null;
		if($parent->type === 'component') {
			$task = preg_replace("/.*view=([^&]+).*/", '$1', $parent->link);
			$id = preg_replace("/.*[&\?]id=([0-9]+).*/", '$1', $parent->link);
			$type = "content_$task";
		} else {
			$type = $parent->type;
			$id = $parent->componentid;
		}

		/***
		* Parameters Initialitation
		**/
		//----- Set expand_categories param
		$expand_categories = mosGetParam($params, 'expand_categories', 1);
		$expand_categories = ($expand_categories == 1 || ($expand_categories == 2 && $xmap->view == 'xml') || ($expand_categories == 3 && $xmap->view == 'html'));
		$params['expand_categories'] = $expand_categories;

		//----- Set expand_sections param
		$expand_sections = mosGetParam($params, 'expand_sections', 1);
		$expand_sections = ($expand_sections == 1 || ($expand_sections == 2 && $xmap->view == 'xml') || ($expand_sections == 3 && $xmap->view == 'html'));
		$params['expand_sections'] = $expand_sections;

		//----- Set show_unauth param
		$show_unauth = mosGetParam($params, 'show_unauth', 1);
		$show_unauth = ($show_unauth == 1 || ($show_unauth == 2 && $xmap->view == 'xml') || ($show_unauth == 3 && $xmap->view == 'html'));
		$params['show_unauth'] = $show_unauth;

		//----- разделение на подстраницы
		$params['use_mospagebreak'] = mosGetParam($params, 'use_mospagebreak', 1);


		//----- Set cat_priority and cat_changefreq params
		$priority = mosGetParam($params, 'cat_priority', $parent->priority);
		$changefreq = mosGetParam($params, 'cat_changefreq', $parent->changefreq);
		if($priority == '-1')
			$priority = $parent->priority;
		if($changefreq == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		//----- Set art_priority and art_changefreq params
		$priority = mosGetParam($params, 'art_priority', $parent->priority);
		$changefreq = mosGetParam($params, 'art_changefreq', $parent->changefreq);
		if($priority == '-1')
			$priority = $parent->priority;
		if($changefreq == '-1')
			$changefreq = $parent->changefreq;

		$params['art_priority'] = $priority;
		$params['art_changefreq'] = $changefreq;

		switch ($type) {
			case 'content_blog_category':
				if($params['expand_categories']) {
					$menuparams = xmap_com_content::paramsToArray($parent->params);
					if($id == 0) // Multi category
						$id = mosGetParam($menuparams, 'categoryid', $id);
					$result = xmap_com_content::getContentCategory($xmap, $parent, $id, $params, $menuparams);
				}
				break;
			case 'content_category':
				if($params['expand_categories']) {
					$menuparams = xmap_com_content::paramsToArray($parent->params);
					$result = xmap_com_content::getContentCategory($xmap, $parent, $id, $params, $menuparams);
				}
				break;
			case 'content_section':
				if($params['expand_sections']) {
					$menuparams = xmap_com_content::paramsToArray($parent->params);
					$result = xmap_com_content::getContentSection($xmap, $parent, $id, $params, $menuparams);
				}
				break;
			case 'content_blog_section':
				if($params['expand_sections']) {
					$menuparams = xmap_com_content::paramsToArray($parent->params);
					$result = xmap_com_content::getContentBlogSection($xmap, $parent, $id, $params, $menuparams);
				}
				break;
			case 'content_typed':
				$database = database::getInstance();
				$database->setQuery("SELECT modified, created FROM #__content WHERE id=" . $id);
				$database->loadObject($item);
				if((isset($item->modified))&&$item->modified == '0000-00-00 00:00:00') {
					$item->modified = $item->created;
				}
				$parent->modified = xmap_com_content::toTimestamp($item->modified);
				break;
		}
		return $result;
	}

	/** Get all content items within a content category.
	 * Returns an array of all contained content items. */
	public static function getContentCategory($xmap, $parent, $catid, $params, $menuparams) {
		$database = database::getInstance();

		$orderby = !empty($menuparams['orderby']) ? $menuparams['orderby'] : (!empty($menuparams['orderby_sec']) ? $menuparams['orderby_sec'] : 'rdate');
		$orderby = xmap_com_content::orderby_sec($orderby);

		$isJ15 = 0;
		$query = "SELECT a.id, a.introtext, a.fulltext, a.title, a.modified, a.created"
				. "\n FROM #__content AS a"
				. "\n WHERE a.catid=(" . $catid . ")"
				. "\n AND a.state='1'"
				. "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '" . date('Y-m-d H:i:s', $xmap->now) . "' )"
				. "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '" . date('Y-m-d H:i:s', $xmap->now) . "' )"
				. ($xmap->noauth ? '' : "\n AND a.access<='" . $xmap->gid . "'") // authentication required ?
				. ($xmap->view != 'xml' ? "\n ORDER BY " . $orderby . "" : '');
		$database->setQuery($query);
		$items = $database->loadObjectList();

		if(count($items) > 0) {
			$xmap->changeLevel(1);
			foreach ($items as $item) {
				$node = new stdclass();
				$node->id = $parent->id;
				$node->uid = $parent->uid . 'a' . $item->id;
				$node->browserNav = $parent->browserNav;
				$node->priority = $params['art_priority'];
				$node->changefreq = $params['art_changefreq'];
				$node->name = $item->title;

				if($item->modified == '0000-00-00 00:00:00')
					$item->modified = $item->created;

				$node->modified = xmap_com_content::toTimestamp($item->modified);

				if($params['use_mospagebreak']) {
					$text = $item->introtext.$item->fulltext;
					$node->end_line = to_page($text,$item->id,$node->id);
				}
				$node->link = 'index.php?option=com_content&amp;task=view&amp;id=' . $item->id;
				$xmap->printNode($node);
			}
			$xmap->changeLevel(-1);
		}
		return true;
	}

	/** Get all Categories within a Section.
	 * Also call getCategory() for each Category to include it's items */
	public static function getContentSection(&$xmap, &$parent, $secid, &$params, &$menuparams) {
		$database = database::getInstance();

		$orderby = isset($menuparams['orderby']) ? $menuparams['orderby'] : '';
		$orderby = xmap_com_content::orderby_sec($orderby);

		$isJ15 = ($parent->type == 'component' ? 1 : 0);
		$query = "SELECT a.id, a.title, a.name, a.params" . ($isJ15 ? ",a.alias" : "") . ($isJ15 ? ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' : '') . "\n FROM #__categories AS a" . "\n LEFT JOIN #__content AS b ON b.catid = a.id " . "\n AND b.state = '1'" . "\n AND ( b.publish_up = '0000-00-00 00:00:00' OR b.publish_up <= '" . date('Y-m-d H:i:s', $xmap->now) . "' )" . "\n AND ( b.publish_down = '0000-00-00 00:00:00' OR b.publish_down >= '" . date('Y-m-d H:i:s', $xmap->now) . "' )" . ($xmap->noauth ? '' : "\n AND b.access <= " . $xmap->gid) // authentication required ?
				. "\n WHERE a.section = '" . $secid . "'" . "\n AND a.published = '1'" . ($xmap->noauth ? '' : "\n AND a.access <= " . $xmap->gid) // authentication required ?
				. "\n GROUP BY a.id" . (@$menuparams['empty_cat'] ? '' : "\n HAVING COUNT( b.id ) > 0") // hide empty categories ?
				. ($xmap->view != 'xml' ? "\n ORDER BY " . $orderby : '');
		$database->setQuery($query);
		$items = $database->loadObjectList();

		$layout = '';
		if($isJ15 && preg_match('/^.*&layout=([a-z]+).*/', $parent->link, $matches)) {
			$layout = '&amp;layout=' . $matches[1];
		}

		$xmap->changeLevel(1);
		foreach ($items as $item) {
			$node = new stdclass();
			$node->id = $parent->id;
			$node->uid = $parent->uid . 'c' . $item->id;
			$node->name = ($isJ15 ? $item->title : $item->name);
			$node->browserNav = $parent->browserNav;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = 'index.php?option=com_content&amp;task=category&amp;sectionid=' . $secid . '&amp;id=' . $item->id;
			$xmap->printNode($node);
			if($params['expand_categories']) {
				xmap_com_content::getContentCategory($xmap, $parent, $item->id, $params, $menuparams);
			}

		}
		$xmap->changeLevel(-1);
		return true;
	}

	/** Return an array with all Items in a Section */
	public static function getContentBlogSection(&$xmap, &$parent, $secid, &$params, &$menuparams) {
		$database = database::getInstance();

		$order_pri = isset($menuparams['orderby_pri']) ? $menuparams['orderby_pri'] : '';
		$order_sec = isset($menuparams['orderby_sec']) && !empty($menuparams['orderby_sec']) ? $menuparams['orderby_sec'] : 'rdate';
		$order_pri = xmap_com_content::orderby_pri($order_pri);
		$order_sec = xmap_com_content::orderby_sec($order_sec);
		if($secid == 0) // Multi section

			$secid = mosGetParam($menuparams, 'sectionid', $secid);
		$where = xmap_com_content::where(1, $xmap->access, $xmap->noauth, $xmap->gid, $secid, date('Y-m-d H:i:s', $xmap->now));

		$isJ15 = ($parent->type == 'component' ? 1 : 0);
		$query = "SELECT a.id, a.introtext, a.fulltext, a.title, a.modified, a.created FROM #__content AS a INNER JOIN #__categories AS cc ON cc.id = a.catid LEFT JOIN #__users AS u ON u.id = a.created_by LEFT JOIN #__content_rating AS v ON a.id = v.content_id LEFT JOIN #__sections AS s ON a.sectionid = s.id LEFT JOIN #__groups AS g ON a.access = g.id WHERE " . implode("\n AND ", $where) . "\n AND s.access <= " . $xmap->gid . " AND cc.access <= " . $xmap->gid . "\n AND s.published = 1" . "\n AND cc.published = 1" . ($xmap->view != 'xmal' ? "\n ORDER BY $order_pri $order_sec" : '');

		$database->setQuery($query);
		$items = $database->loadObjectList();

		$xmap->changeLevel(1);
		foreach ($items as $item) {
			$node = new stdclass();
			$node->id = $parent->id;
			$node->uid = $parent->uid . 'a' . $item->id;
			$node->browserNav = $parent->browserNav;
			$node->priority = $params['art_priority'];
			$node->changefreq = $params['art_changefreq'];
			$node->name = $item->title;

			if($item->modified == '0000-00-00 00:00:00') {
				$item->modified = $item->created;
			}
			$node->modified = xmap_com_content::toTimestamp($item->modified);
			if($params['use_mospagebreak']) {
				$text = $item->introtext.$item->fulltext;
				$node->end_line = to_page($text,$item->id,$node->id);
			}
			$node->link = 'index.php?option=com_content&amp;task=view&amp;id=' . $item->id;

			$xmap->printNode($node);
		}
		$xmap->changeLevel(-1);
		return true;
	}

	/***************************************************/
	/* copied from /components/com_content/content.php */
	/***************************************************/

	/** convert a menuitem's params field to an array */
	public static function paramsToArray(&$menuparams) {
		$tmp = explode("\n", $menuparams);
		$res = array();
		foreach ($tmp as $a) {
			@list($key, $val) = explode('=', $a, 2);
			$res[$key] = $val;
		}
		return $res;
	}
	/** Translate Joomla datestring to timestamp */
	public static function toTimestamp(&$date) {
		if($date && preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs)) {
			return mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return false;
	}

	/** translate primary order parameter to sort field */
	public static function orderby_pri($orderby) {
		switch ($orderby) {
			case 'alpha':
				$orderby = 'cc.title, ';
				break;

			case 'ralpha':
				$orderby = 'cc.title DESC, ';
				break;

			case 'order':
				$orderby = 'cc.ordering, ';
				break;

			default:
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/** translate secondary order parameter to sort field */
	public static function orderby_sec($orderby) {
		switch ($orderby) {
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
				$orderby = 'a.hits';
				break;

			case 'rhits':
				$orderby = 'a.hits DESC';
				break;

			case 'order':
				$orderby = 'a.ordering';
				break;

			case 'author':
				$orderby = 'a.created_by_alias, u.name';
				break;

			case 'rauthor':
				$orderby = 'a.created_by_alias DESC, u.name DESC';
				break;

			case 'front':
				$orderby = 'f.ordering';
				break;

			default:
				$orderby = 'a.ordering';
				break;
		}

		return $orderby;
	}
	/**
	 @param int 0 = Archives, 1 = Section, 2 = Category */
	public static function where($type = 1, &$access, &$noauth, $gid, $id, $now = null, $year = null, $month = null) {
		$database = database::getInstance();

		$nullDate = $database->getNullDate();
		$where = array();

		// normal
		if($type > 0) {
			$where[] = "a.state = '1'";
			if(!$access->canEdit) {
				$where[] = "( a.publish_up = '$nullDate' OR a.publish_up <= '$now' )";
				$where[] = "( a.publish_down = '$nullDate' OR a.publish_down >= '$now' )";
			}
			if($noauth) {
				$where[] = "a.access <= $gid";
			}
			if($id > 0) {
				if($type == 1) {
					$where[] = "a.sectionid IN ( $id ) ";
				} else
				if($type == 2) {
					$where[] = "a.catid IN ( $id ) ";
				}
			}
		}

		// archive
		if($type < 0) {
			$where[] = "a.state='-1'";
			if($year) {
				$where[] = "YEAR( a.created ) = '$year'";
			}
			if($month) {
				$where[] = "MONTH( a.created ) = '$month'";
			}
			if($noauth) {
				$where[] = "a.access <= $gid";
			}
			if($id > 0) {
				if($type == -1) {
					$where[] = "a.sectionid = $id";
				} else
				if($type == -2) {
					$where[] = "a.catid = $id";
				}
			}
		}

		return $where;
	}
}

function to_page($text,$id,$Itemid) {
	if(strpos($text,'mospagebreak') === false) {
		return;
	}

	// найти все образцы мамбота и вставить в $matches
	$matches = array();
	$regex = '/{(mospagebreak)\s*(.*?)}/i';
	preg_match_all($regex,$text,$matches,PREG_SET_ORDER);

	// мамбот разрыва текста
	$text = preg_split($regex,$text);

	// подсчет числа страниц
	$n = count($text)-1;
	$ret = '';
	for ($i = 1; $i <= $n; $i++) {
		$ret .= ', <a href="'.sefRelToAbs('index.php?option=com_content&amp;task=view&amp;id='.$id.'&amp;limit=1&amp;limitstart='.$i.'&amp;Itemid='.$Itemid).'" >'.($i + 1).' '._PN_PAGE.'</a>';
	}
	return $ret;
}