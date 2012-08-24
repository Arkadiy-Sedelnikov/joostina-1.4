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

require_once (JPATH_BASE . DS . JADMIN_BASE . '/components/com_xmap/classes/XmapSitemap.php');

class XmapConfig{
	var $version = '1.1';
	var $classname = 'sitemap';
	var $expand_category = 1;
	var $expand_section = 1;
	var $show_menutitle = 1;
	var $columns = 1;
	var $exlinks = 1;
	var $ext_image = 'img_grey.gif';
	var $exclmenus = '';
	var $includelink = 1;
	var $sitemap_default = 1;
	var $exclude_css = 0;
	var $exclude_xsl = 0;

	function XmapConfig(){
		$version = '1.1';
		$classname = 'sitemap';
		$expand_category = 1;
		$expand_section = 1;
		$show_menutitle = 1;
		$columns = 1;
		$exlinks = 1;
		$ext_image = 'img_grey.gif';
		$exclmenus = '';
		$includelink = 1;
		$sitemap_default = 1;
		$exclude_css = 0;
		$exclude_xsl = 0;

	}

	/** Return $menus as an associative array */
	function getSitemaps(){
		$database = database::getInstance();

		$query = "SELECT id FROM #__xmap_sitemap";
		$database->setQuery($query);
		$ids = $database->loadResultArray();
		$sitemaps = array();
		foreach($ids as $id){
			$sitemap = new XmapSitemap();
			$sitemap->load($id);
			$sitemaps[] = $sitemap;
		}
		return $sitemaps;

	}

	/** Create the settings table for Xmap and add initial default values */
	function create(){
		$database = database::getInstance();

		$fields = array();
		$fields[] = "`name` varchar(30) not null primary key";
		$fields[] = "`value` varchar(100)";

		$query = "CREATE TABLE #__xmap (" . implode(', ', $fields) . ")";
		$database->setQuery($query);
		if($database->query() === FALSE){
			echo _XMAP_ERR_NO_CREATE . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}


		$fields = array();
		$fields[] = "`id` int not null primary key auto_increment";
		$fields[] = "`extension` varchar(100) not null";
		$fields[] = "`published` int(1) default 0";
		$fields[] = "`params` text";


		$query = "CREATE TABLE #__xmap_ext (" . implode(', ', $fields) . ")";
		$database->setQuery($query);
		if($database->query() === FALSE){
			echo _XMAP_ERR_NO_CREATE . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}

		require_once(JPATH_BASE . '/' . JADMIN_BASE . '/components/com_xmap/classes/XmapPlugin.php');
		$extensions = array(
			//	name			published
			array('com_boss', 1)
		);
		foreach($extensions as $ext){
			$extension = new XmapPlugin($database);
			$extension->extension = $ext[0];
			$extension->published = $ext[1];
			$xmlfile = $extension->getXmlPath();
			$extension->setParams($extension->loadDefaultsParams(true), '-1');
			$extension->store();
			$extension->restore(); //Load settings from backup
		}

		$vars = get_class_vars('XmapSitemap');
		$fields = '';
		foreach($vars as $name => $value){
			if($name[0] !== '_'){
				if($name == 'id'){
					$fields[] = 'id INT NOT NULL PRIMARY KEY AUTO_INCREMENT';
				} else{
					switch(gettype($value)){
						case 'integer':
							$fields[] = "`$name` INTEGER NULL";
							break;
						case 'string':
							if($name == 'menus')
								$fields[] = "`$name` TEXT NULL";
							else
								$fields[] = "`$name` VARCHAR(255) NULL";
							break;
					}
				}
			}
		}
		$query = "CREATE TABLE #__xmap_sitemap (" . implode(', ', $fields) . ")";
		$database->setQuery($query);
		if($database->query() === FALSE){
			echo _XMAP_ERR_NO_CREATE . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
			return false;
		}
		echo _XMAP_MSG_SET_DB_CREATED . "<br />\n";


		// Insert default Settings

		$sitemap = new XmapSitemap();
		$sitemap->save();

		$fields = array();
		$vars = get_class_vars('XmapConfig');
		foreach($vars as $name => $value){
			if($name == 'sitemap_default'){
				$value = $sitemap->id;
			}
			$query = "INSERT INTO #__xmap (`name`,`value`) values ('$name','$value')";
			$database->setQuery($query);
			if($database->query() === FALSE){
				echo _XMAP_ERR_NO_DEFAULT_SET . "<br />\n";
				echo mosStripslashes($database->getErrorMsg());
				return false;
			}
		}

		echo _XMAP_MSG_SET_DEF_INSERT . "<br />\n";
		return true;
	}

	/** Create a backup of the settings */
	function backup(){
		$database = database::getInstance();

		$query = "DROP TABLE IF EXISTS #__xmap_backup"; // remove old backup
		$database->setQuery($query);
		if($database->query() === FALSE){
			echo _XMAP_ERR_NO_PREV_BU . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
		}

		$query = "DROP TABLE IF EXISTS #__xmap_ext_backup"; // remove old backup
		$database->setQuery($query);
		if($database->query() === FALSE){
			echo _XMAP_ERR_NO_PREV_BU . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
		}

		$query = "DROP TABLE IF EXISTS #__xmap_sitemap_backup"; // remove old backup
		$database->setQuery($query);
		if($database->query() === FALSE){
			echo _XMAP_ERR_NO_PREV_BU . "<br />\n";
			echo mosStripslashes($database->getErrorMsg());
		}

		$querys[] = "CREATE TABLE #__xmap_backup SELECT * FROM #__xmap"; // backup current settings
		$querys[] = "CREATE TABLE #__xmap_sitemap_backup SELECT * FROM #__xmap_sitemap"; // backup current settings
		$querys[] = "CREATE TABLE #__xmap_ext_backup SELECT * FROM #__xmap_ext "; // backup current extensions settings
		$querys[] = "DELETE from #__xmap_ext_backup where extension like '%.bak' and extension in (select concat(extension,'.bak') FROM #__xmap_ext where extension not like '%.bak')"; // remove old extensions backups
		$querys[] = "UPDATE #__xmap_ext_backup SET extension=concat(extension,'.bak') where extension not like '%.bak'"; // backup current settings
		foreach($querys as $query){
			$database->setQuery($query);
			if($database->query() === FALSE){
				echo _XMAP_ERR_NO_BACKUP . "<br />\n";
				echo mosStripslashes($database->getErrorMsg());
				return false;
			}
		}


		return true;
	}

	/** Restore backup settings */
	function restore(){
		global $mosConfig_dbprefix;

		$database = database::getInstance();

		$query = "show table status like '" . $mosConfig_dbprefix . "xmap_backup'";
		$database->setQuery($query);
		if(!$database->query()){
			echo $database->getErrorMsg();
		}
		$exists = ($database->getNumRows() > 0);
		if(!$exists)
			return false;

		$query = "SELECT * FROM #__xmap_backup"; // restore backup settings
		$database->setQuery($query);

		if($result = $database->loadAssocList('name')){
			$backup = new stdClass;
			foreach($result as $name => $row){
				if($name){
					$backup->$name = $row['value'];
				}
			}
		} else{
			return false;
		}

		if(isset($this) && is_object($this)){
			$config = &$this;
		} else{
			$config = new XmapConfig;
		}

		$vars = get_class_vars('XmapConfig'); // assign current settings
		foreach($vars as $var => $value){
			if(isset($backup->$var))
				$config->$var = $backup->$var;
		}

		$config->save(); // save current settings

		$query = "DELETE FROM `#__xmap_sitemap`";
		$database->setQuery($query);
		$database->query();

		$query = "SELECT * FROM #__xmap_sitemap_backup"; // restore backup settings
		$database->setQuery($query);
		if($result = $database->loadAssocList()){
			foreach($result as $values){
				$sitemap = new XmapSitemap();
				mosBindArrayToObject($values, $sitemap);
				$sitemap->save(true);
			}
		} else{
			return false;
		}

		$query = "show table status like '" . $mosConfig_dbprefix . "xmap_ext_backup'";
		$database->setQuery($query);
		if(!$database->query()){
			echo $database->getErrorMsg();
		}
		$exists = ($database->getNumRows() > 0);
		if(!$exists)
			return true;

		$query = "SELECT * FROM #__xmap_ext_backup"; // restore backup settings
		$database->setQuery($query);

		if($result = $database->loadAssocList('extension')){
			foreach($result as $name => $row){
				if($name && strpos($name, '.bak')){
					$extension = new XmapPlugin($database);
					mosBindArrayToObject($row, $extension);
					$extension->id = NULL;
					$extension->store();
				}
			}
		} else{
			return false;
		}

		return true;
	}

	/** Remove the settings table */
	function remove(){
		$database = database::getInstance();
		$querys[] = "DROP TABLE IF EXISTS #__xmap";
		$querys[] = "DROP TABLE IF EXISTS #__xmap_sitemap";
		$querys[] = "DROP TABLE IF EXISTS #__xmap_ext";
		foreach($querys as $query){
			$database->setQuery($query);
			if($database->query() === FALSE){
				echo _XMAP_ERR_NO_DROP_DB . "<br />\n";
				echo mosStripslashes($database->getErrorMsg());
				return false;
			}
		}
		echo  "Xmap's tables have been saved!<br />\n";

	}

	/** Load settings from the database into this instance */
	function load(){
		$database = database::getInstance();

		$query = "SELECT * FROM #__xmap";
		$database->setQuery($query);
		if($result = $database->loadAssocList('name')){
			foreach($result as $name => $row){
				$this->$name = $row['value'];
			}
			return true; // defaults are still set, though
		}
		$this->_sitemaps = array();
		return false;
	}

	/** Save current settings to the database */
	function save(){
		$database = database::getInstance();

		$vars = get_object_vars($this);
		$query = "DELETE FROM `#__xmap`";
		$database->setQuery($query);
		$database->query();
		foreach($vars as $name => $value){
			if(substr($name, 0, 1) !== '_'){
				$query = "INSERT INTO #__xmap (`name`,`value`) values ('$name','$value')";
				$database->setQuery($query);
				if($database->query() === FALSE){
					return false;
				}
			}
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
}