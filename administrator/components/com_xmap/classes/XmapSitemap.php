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

/** Wraps all configuration functions for Xmap */
class XmapSitemap{
	var $id = NULL;
	var $name = "";
	var $expand_category = 1;
	var $expand_section = 1;
	var $show_menutitle = 1;
	var $columns = 1;
	var $exlinks = 1;
	var $ext_image = 'img_grey.gif';
	var $menus = "mainmenu,0,1,1,0.5,daily";
	var $exclmenus = '';
	var $includelink = 1;
	var $usecache = 1;
	var $cachelifetime = 900;
	var $classname = 'xmap';
	var $count_xml = 0;
	var $count_html = 0;
	var $views_xml = 0;
	var $views_html = 0;
	var $lastvisit_xml = 0;
	var $lastvisit_html = 0;

	function XmapSitemap(){
		global $mosConfig_cachetime, $mosConfig_caching;
		$this->name = '';
		$this->usecache = $mosConfig_caching;
		$this->cachelifetime = $mosConfig_cachetime;
	}

	/** Return $menus as an associative array */
	function &getMenus(){
		$lines = explode("\n", $this->menus);

		$menus = array();
		foreach($lines as $line){
			if($line){
				list($menutype, $ordering, $show, $showXML, $priority, $changefreq) = explode(',', $line);
				$menu = new stdclass;
				$menu->menutype = $menutype;
				$menu->ordering = $ordering;
				$menu->show = $show;
				$menu->showXML = $showXML;
				$menu->priority = ($priority ? $priority : '0.5');
				$menu->changefreq = ($changefreq ? $changefreq : 'weekly');
				$menus[$menutype] = $menu;
			}
		}
		return $menus;
	}

	/** Set $menus from an associoative array of menu objects */
	function setMenus(&$menus){
		$lines = array();
		foreach($menus as $menutype => $menu){
			$show = $menu->show ? 1 : 0;
			$showXML = $menu->showXML ? 1 : 0;
			$lines[] = mysql_real_escape_string($menutype) . ',' . intval($menu->ordering) . ',' . $show . ',' . $showXML . ',' . mysql_real_escape_string($menu->priority) . ',' . mysql_real_escape_string($menu->changefreq);
		}
		$this->menus = implode("\n", $lines);
	}

	/** Remove the sitemap from the table */
	function remove(){
		$database = database::getInstance();

		$query = "delete from #__xmap_sitemap where `id`=" . $this->id;
		$database->setQuery($query);
		if($database->query() === FALSE){
			echo _XMAP_ERR_NO_DROP_DB . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		return true;
	}

	/** Load settings from the database into this instance */
	function load($id){
		$database = database::getInstance();

		$id = intval($id);
		$query = "SELECT * FROM #__xmap_sitemap where id=$id";
		$database->setQuery($query);
		if($database->loadObject($this) === FALSE){
			return false; // defaults are still set, though
		}
		return true;
	}

	/** Save current settings to the database */
	function save($forceinstall = false){
		$database = database::getInstance();

		$fields = array();

		$vars = get_object_vars($this);
		foreach($vars as $name => $value){
			if(is_array($value) || is_object($value)){
				continue;
			}
			if($name[0] !== '_' && ($name != 'id' || ($forceinstall && $value))){
				$fields["`{$name}`"] = "'{$value}'";
			}
		}

		if($this->id && !$forceinstall){
			$sep = "";
			$values = "";
			foreach($fields as $k  => $value){
				if($k != 'id'){
					$values .= "$sep$k=$value";
					$sep = ",";
				}
			}
			$query = "UPDATE #__xmap_sitemap SET $values WHERE id=" . intval($this->id);
			$isInsert = 0;
		} else{
			$query = "INSERT INTO #__xmap_sitemap (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', $fields) . ")";
			$isInsert = 1;
		}
		$database->setQuery($query);
		# echo $database->getQuery( );
		if($database->query() === FALSE){
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		if($isInsert){
			$this->id = mysql_insert_id($database->_resource);
		}
		return true;
	}

	/** Debug output of current settings */
	function dump(){
		$vars = get_object_vars($this);
		echo '<pre style="text-align:left">';
		foreach($vars as $name => $value){
			echo $name . ': ' . $value . "\n";
		}
		echo '</pre>';
	}

	function bind($array, $ignore = ''){
		if(!is_array($array)){
			$this->_error = strtolower(get_class($this)) . "::bind failed.";
			return false;
		} else{
			foreach(get_object_vars($this) as $k => $v){
				if(substr($k, 0, 1) != '_'){ // internal attributes of an object are ignored
					if(isset($array[$k])){
						$this->$k = get_magic_quotes_gpc() ? mosStripslashes($array[$k]) : $array[$k];
					} elseif(!in_array($k, array('id', 'count_xml', 'views_xml', 'views_html', 'lastvisit_xml', 'count_html', 'views_html', 'lastvisit_html'))){
						$this->$k = '';
					}
				}
			}
		}
	}

	/** Move the display order of a record */
	function orderMenu($menutype, $inc){

		$menus = $this->getMenus();
		if(empty($menus[$menutype])){
			return false;
		}

		if($menus[$menutype]->ordering == 0 && $inc < 0) return false;
		if($menus[$menutype]->ordering >= count($menus) && $inc > 0) return false;

		$menus[$menutype]->ordering += $inc; // move position up/down

		foreach($menus as $type => $menu){ // swap position of previous entry at that position
			if($type != $menutype
				&& $menu->ordering == $menus[$menutype]->ordering
			)
				$menus[$type]->ordering -= $inc;
		}

		$this->sortMenus($menus);
		$this->setMenus($menus);
	}


	/** uasort function that compares element ordering */
	function sort_ordering(&$a, &$b){
		if($a->ordering == $b->ordering){
			return 0;
		}
		return $a->ordering < $b->ordering ? -1 : 1;
	}

	/** make menu ordering continuous*/
	function sortMenus(&$menus){
		uasort($menus, array('XmapSitemap', 'sort_ordering'));
		$i = 0;
		foreach($menus as $key => $menu)
			$menus[$key]->ordering = $i++;
	}

}