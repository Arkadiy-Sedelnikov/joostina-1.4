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

class contentTags extends mosDBTable {

	var $id = null;
	var $obj_id = null;
	var $obj_type = null;
	var $tag = '';

	function contentTags(&$_db) {
		$this->mosDBTable('#__content_tags', 'id', $_db);
	}

	function check() {
		return true;
	}

	function classname() {
		return __CLASS__;
	}

	function load_all() {
		$sql = 'SELECT tag FROM #__content_tags ORDER BY tag ASC';
		return $this->_db->setQuery($sql)->loadResultArray();
	}

	function load_by($obj) {

		if (!isset($obj->obj_type) || !$obj->obj_type) {
			$obj->obj_type = 'com_content';
		};
		$sql = 'SELECT tag FROM #__content_tags WHERE obj_id = ' . $obj->id . ' AND obj_type=' . $this->_db->Quote($obj->obj_type);
		return $this->_db->setQuery($sql)->loadResultArray();
	}

	function load_by_tag($tag) {
		$sql = 'SELECT tag.obj_id, tag.obj_type, content.*  FROM #__content_tags AS tag
				WHERE tag.tag =\'' . $tag . '\'';
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	function load_by_type($type) {
		$sql = 'SELECT * FROM #__content_tags WHERE obj_type =\'' . $type . '\'';
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	function load_by_type_tag($group, $tag) {

		$select = '';
		$where = '';
		$order = 'item.id DESC';

		if ($group["select"]) {
			$select = ', ' . $group["select"];
		}
		if ($group["where"]) {
			$where = 'AND ' . $group["where"];
		}
		if ($group["order"]) {
			$order = 'item.' . $group["order"];
		}

		$sql = 'SELECT tag.*,
				item.' . $group["id"] . ' AS id, item.' . $group["title"] . ' AS title, item.' . $group["text"] . ' AS text, item.' . $group["date"] . ' AS date
				' . $select . '
				FROM #__content_tags AS tag
				INNER JOIN #__' . $group["table"] . ' AS item ON item.' . $group["id"] . ' = tag.obj_id
				' . $group["join"] . '
				WHERE tag.tag = \'' . $tag . '\' ' . $where . ' AND tag.obj_type =\'' . $group["group_name"] . '\'
				ORDER BY ' . $order;
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	function add($tags, $obj) {
		$sql_temp = '';
		$max = count($tags);
		$n = 1;
		foreach ($tags as $title) {
			$sql_temp .= '(' . $obj->id . ', \'' . $obj->obj_type . '\',   \'' . $title . '\')';
			if ($n < $max) {
				$sql_temp .= ',';
			}
			$n++;
		}
		$sql = 'INSERT  #__content_tags (obj_id, obj_type, tag) VALUES  ' . $sql_temp;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}

	function update($tags, $obj) {
		$sql = 'DELETE FROM #__content_tags WHERE obj_id = ' . $obj->id . ' AND obj_type=' . $this->_db->Quote($obj->obj_type);
		$this->_db->setQuery($sql)->Query();
		$sql_temp = '';
		$max = count($tags);
		$n = 1;
		foreach ($tags as $title) {
			$sql_temp .= '(' . $obj->id . ', \'' . $obj->obj_type . '\',   \'' . $title . '\')';
			if ($n < $max) {
				$sql_temp .= ',';
			}
			$n++;
		}

		$sql = 'INSERT  #__content_tags (obj_id, obj_type,  tag) VALUES  ' . $sql_temp;
		$this->_db->setQuery($sql)->query();
	}

	function clear_tags($tags) {
		$return = array();
		foreach ($tags as $tag) {
			$tag = self::good_tag($tag);
			if ($tag) {
				$return[] = $tag;
			}
		}

		return $return;
	}

	function good_tag($tag) {
		$bad_tag = array('я', ' ');

		if (in_array($tag, $bad_tag)) {
			return false;
		}

		if ($tag == '') {
			return false;
		}

		$tag = mosHTML::cleanText($tag);
		return trim($tag);
	}

	function get_tag_url($tag) {
		return sefRelToAbs('index.php?option=com_search&tag=' . urlencode($tag));
	}

	function arr_to_links($tags, $ds = ', ') {
		if (!$tags) {
			return;
		}

		$return = array();
		foreach ($tags as $tag) {
			$return[] = '<a class="tag" href="' . self::get_tag_url($tag) . '">' . $tag . '</a>';
		}

		return implode($ds, $return);
	}

}

class TagsCloud {

	var $tags;
	var $font_size_min = 14;
	var $font_size_step = 5;

	function __construct($tags) {

		//shuffle($tags);
		$this->tags = $tags;
	}

	function get_tag_count($tag_name, $tags) {

		$count = 0;

		foreach ($tags as $tag) {
			if ($tag == $tag_name) {
				$count++;
			}
		}

		return $count;
	}

	// проверить необходимость
	function gettagscloud($tags) {
		$tags_list = array();

		foreach ($tags as $tag) {
			$tags_list[$tag] = self::get_tag_count($tag, $tags);
		}

		return $tags_list;
	}

	function get_min_count($tags_list) {
		$min = $tags_list[$this->tags[0]];

		foreach ($tags_list as $tag_count) {
			if ($tag_count < $min)
				$min = $tag_count;
		}

		return $min;
	}

	function get_cloud() {

		$cloud = Array();

		$tags_list = self::gettagscloud($this->tags);
		$min_count = self::get_min_count($tags_list);

		foreach ($tags_list as $tag => $count) {

			$font_steps = $count - $min_count;
			$font_size = $this->font_size_min + $this->font_size_step * $font_steps;

			$cloud[$tag][] = $font_size;
			// $cloud['tag'][] =
		}
		return $cloud;
	}

}