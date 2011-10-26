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

class xmap_com_weblinks {
	public static function getTree( $xmap, $parent ) {
		global $my, $Itemid;
		$list = array();

		$database = database::getInstance();

		// include popular bloggers by default
		$sql = 'SELECT id, title FROM #__categories WHERE section=\'com_weblinks\' and published=1';
		$objResults = $database->setQuery($sql);
		$rows = $database->loadObjectList();
		$modified = time();

		$xmap->changeLevel(1);
		foreach($rows as $row) {
			$node = new stdclass;

			$node->id = $parent->id;
			$node->browserNav = $parent->browserNav;
			$node->name = $row->title;
			$node->modified = $modified;
			$node->link = "index.php?option=com_weblinks&amp;catid=".$row->id; //."&Itemid".$row->id;
			$node->pid = $parent->id;// parent id

			$xmap->printNode($node);

			// get links
			$sql = 'SELECT id, title FROM #__weblinks WHERE catid='. $row->id . ' and published=1';
			$linksResults = $database->setQuery($sql);
			$links = $database->loadObjectList();

			//http://archive/component/option,com_weblinks/task,view/catid,19/id,1/
			$xmap->changeLevel(1);
			foreach($links as $curlink) {
				$child = new stdclass;
				$child->id = $node->id;
				$child->browserNav = $node->browserNav;
				$child->modified = $modified;
				$child->name = $curlink->title;
				$child->link = "index.php?option=com_weblinks&amp;task=view&amp;catid=".$row->id."&amp;id=".$curlink->id."&amp;Itemid=".$Itemid;
				$child->pid = $node->pid;

				$xmap->printNode($child);

			}
			$xmap->changeLevel(-1);
		}
		$xmap->changeLevel(-1);
		return $list;
	}
}