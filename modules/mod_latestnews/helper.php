<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

require_once ($mainframe->getPath('front_html', 'com_content'));

class mod_latestnews_Helper {

	var $_mainframe = null;

	function mod_latestnews_Helper($mainframe) {

		$this->_mainframe = $mainframe;
		mosMainFrame::addLib('text');
		mosMainFrame::addLib('images');
	}

	function get_static_items($params) {
		global $my;

		$mainframe	= $this->_mainframe;
		$database	= $this->_mainframe->getDBO();

		$now		= _CURRENT_SERVER_TIME;
		$access		= !$mainframe->getCfg( 'shownoauth' );
		$nullDate	= $database->getNullDate();

		$query = 'SELECT a.id, a.title, a.introtext, a.images, a.created, a.created_by, a.created_by_alias,
			u.name AS author, u.usertype, u.username
			FROM #__content AS a
			LEFT JOIN #__users AS u ON u.id = a.created_by
			WHERE (a.state = 1 AND a.sectionid = 0)
			AND (a.publish_up = '.$database->Quote($nullDate).' OR a.publish_up <= '.$database->Quote($now).' )
			AND (a.publish_down = '.$database->Quote( $nullDate ).' OR a.publish_down >= '.$database->Quote($now).' )
			'.( $access ? 'AND a.access <= ' . (int) $my->gid : '' ).'
			ORDER BY a.created DESC';

		return $database->setQuery($query, 0, intval($params->get('count',5)))->loadObjectList();
	}

	function get_items_both($params) {
		global $my;

		$mainframe = $this->_mainframe;
		$database = $this->_mainframe->getDBO();

		$now = _CURRENT_SERVER_TIME;
		$access	= !$mainframe->getCfg( 'shownoauth' );
		$nullDate = $database->getNullDate();

		$count = intval($params->get('count',5));
		$catid = trim($params->get('catid'));
		$secid = trim($params->get('secid', 1));
		$show_front	= $params->get('show_front', 1);

		$whereCatid = '';
		if ($catid) {
			$catids = explode( ',', $catid );
			mosArrayToInts( $catids );
			$whereCatid = " AND ( a.catid=" . implode( " OR a.catid=", $catids ) . " )";
		}

		$whereSecid = '';
		if ($secid) {
			$secids = explode( ',', $secid );
			mosArrayToInts( $secids );
			$whereSecid = " AND (a.sectionid=" . implode(" OR a.sectionid=", $secids ) . ")";
		}

		$query = "SELECT a.id, a.title, a.sectionid, a.catid,
			a.introtext, a.images, a.created, a.created_by, a.created_by_alias,
			u.name AS author, u.usertype, u.username,
			cc.access AS cat_access, s.access AS sec_access, cc.published AS cat_state, s.published AS sec_state
			FROM #__content AS a
			LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id
			LEFT JOIN #__categories AS cc ON cc.id = a.catid
			LEFT JOIN #__sections AS s ON s.id = a.sectionid
			LEFT JOIN #__users AS u ON u.id = a.created_by
			WHERE a.state = 1
			AND ( a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " )
			AND ( a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )
			". ( $access ? " AND a.access <= " . (int) $my->gid : '' )
				. $whereCatid
				. $whereSecid
				. ( $show_front == '0' ? " AND f.content_id IS NULL" : '' )
				. " ORDER BY a.created DESC";

		$temp = $database->setQuery( $query, 0, $count )->loadObjectList();

		$rows = array();
		if (count($temp)) {
			foreach ($temp as $row ) {
				if (($row->cat_state == 1 || $row->cat_state == '') &&  ($row->sec_state == 1 || $row->sec_state == '') &&  ($row->cat_access <= $my->gid || $row->cat_access == '' || !$access) &&  ($row->sec_access <= $my->gid || $row->sec_access == '' || !$access)) {
					$rows[] = $row;
				}
			}
		}
		unset($temp);

		return $rows;
	}

	function get_category_items($params) {
		global $my;

		$mainframe = $this->_mainframe;
		$database = $this->_mainframe->getDBO();

		$now = _CURRENT_SERVER_TIME;
		$access	= !$mainframe->getCfg( 'shownoauth' );
		$nullDate = $database->getNullDate();

		$count = intval($params->get('count',5));
		$catid = trim($params->get('catid'));
		$secid = trim($params->get('secid'));
		$show_front	= $params->get('show_front', 1);

		$whereCatid = '';
		if ($catid) {
			$catids = explode( ',', $catid );
			mosArrayToInts( $catids );
			$whereCatid = " AND ( a.catid=" . implode( " OR a.catid=", $catids ) . " )";
		}

		$whereSecid = '';
		if ($secid) {
			$secids = explode( ',', $secid );
			mosArrayToInts( $secids );
			$whereSecid = " AND ( a.sectionid=" . implode( " OR a.sectionid=", $secids ) . " )";
		}

		$query = "SELECT a.id, a.title, a.sectionid, a.catid, a.introtext,
			a.images, a.created, a.created_by, a.created_by_alias,
			u.name AS author, u.usertype, u.username
			FROM #__content AS a
			LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id
			INNER JOIN #__categories AS cc ON cc.id = a.catid
			INNER JOIN #__sections AS s ON s.id = a.sectionid
			LEFT JOIN #__users AS u ON u.id = a.created_by
			WHERE ( a.state = 1 AND a.sectionid > 0 )
			AND ( a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " )
			AND ( a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )"
				. ( $access ? " AND a.access <= " . (int) $my->gid . " AND cc.access <= " . (int) $my->gid . " AND s.access <= " . (int) $my->gid : '' )
				. $whereCatid
				. $whereSecid
				. ($show_front == '0' ? " AND f.content_id IS NULL" : ''). "
			AND s.published = 1
			AND cc.published = 1
			ORDER BY a.created DESC";

		return $database->setQuery( $query, 0, $count )->loadObjectList();
	}

	function get_itemid($row, $params) {
		$mainframe = $this->_mainframe;
		$database = $this->_mainframe->getDBO();

		$type = intval($params->get('type', 1));

		switch ($type) {
			case 2:
				$query = "SELECT id	FROM #__menu WHERE type = 'content_typed' AND componentid = ".(int) $row->id;
				$database->setQuery($query);
				$Itemid = $database->loadResult();
				break;

			case 3:
				if ($row->sectionid) {
					$Itemid = $mainframe->getItemid( $row->id, 0, 0, $params->get('bs'), $params->get('bc'), $params->get('gbs') );
				} else {
					$query = "SELECT id FROM #__menu WHERE type = 'content_typed' AND componentid = ".(int) $row->id;
					$database->setQuery( $query );
					$Itemid = $database->loadResult();
				}
				break;

			case 1:
			default:
				$Itemid = $mainframe->getItemid( $row->id, 0, 0, $params->get('bs'), $params->get('bc'), $params->get('gbs') );
				break;
		}

		return $Itemid;
	}

	function prepare_row($row, $params) {

		$row->Itemid_link = '';
		if($params->get('def_itemid', '')) {
			$row->Itemid_link = '&amp;Itemid='.$params->get('def_itemid');
		}
		else {
			$_itemid = $this->get_itemid($row, $params);
			if($_itemid) {
				$row->Itemid_link = '&amp;Itemid='.$_itemid;
			}
		}

		$row->link_on = sefRelToAbs('index.php?option=com_content&amp;task=view&amp;id='.$row->id.$row->Itemid_link);
		$row->link_text = $params->get('link_text', _READ_MORE);
		$readmore = ContentView::ReadMore($row,$params);

		$text = $row->introtext;
		$text = Text::simple_clean($text);
		if($params->get('crop_text', 1)) {

			switch ($params->get('crop_text', 1)) {
				case 'simbol':
				default:
					$text = Text::character_limiter($text, $params->get('text_limit', 250), '');
					break;

				case 'word':
					$text = Text::word_limiter($text, $params->get('text_limit', 25), '');
					break;
			}
		}
		if($params->get('text', 0)==2) {
			$text = '<a href="'.$row->link_on.'">'.$text.'</a>';
		}

		$row->image = '';
		if($params->get('image', 'mosimage')) {

			$text_with_image = $row->introtext;

			if($params->get('image', 'mosimage')=='mosimage') {
				$text_with_image = $row->images;
			}
			$img = Image::get_image_from_text($text_with_image, $params->get('image', 'mosimage'), $params->get('image_default',1));

			if( trim($img)!='' ){
				if(substr($img, 0, 4)=='http') {
					$row->image = '<img title="'.$row->title.'" alt="" src="'.$img.'" />';
				} else {
					$row->image = '<img title="'.$row->title.'" alt="" src="'.JPATH_SITE.$img.'" />';
				}

				if($params->get('image_link',1) && $row->image) {
					$row->image =  '<a class="thumb" href="'.$row->link_on.'">'.$row->image.'</a>';
				}
			}
		}
		$row->author =  mosContent::Author($row,$params);
		$row->title = ContentView::Title($row,$params);
		$row->text = $text;
		$row->readmore = $readmore;

		return $row;
	}
}