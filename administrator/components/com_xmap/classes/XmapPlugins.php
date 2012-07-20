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

/** Wraps all configuration functions for Xmap */
class XmapPlugin extends mosDBTable{
	var $id = '';
	var $extension = '';
	var $published = 0;
	var $params = '';
	var $_params = '';

	function XmapPlugin(&$_db, $id = NULL){
		$this->mosDBTable('#__xmap_ext', 'id', $_db);
		if($id){
			$this->load($id);
		}
	}

	function getParams($asTXT = 0){
		if(!is_array($this->_params)){
			$this->parseParams();
		}
		$params = $this->_params[-1];
		if($asTXT){
			return $params['__TXT__'];
		}
		return $params;
	}

	function parseParams(){
		$this->_params = array('-1'=> array());
		if($this->params){
			preg_match_all('/(.?[0-9]+){([^}]+)}/', $this->params, $paramsList);
			$count = count($paramsList[1]);
			for($i = 0; $i < $count; $i++){
				$this->_params[$paramsList[1][$i]] = $this->paramsToArray($paramsList[2][$i]);
			}
		}
	}

	function &loadDefaultsParams($asText){

		$path = $this->getXmlPath();
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors(true);

		$params = null;
		if($xmlDoc->loadXML($path, false, true)){
			$root =& $xmlDoc->documentElement;
			$tagName = $root->getTagName();
			$isParamsFile = ($tagName == 'mosinstall' || $tagName == 'mosparams');
			if($isParamsFile && $root->getAttribute('type') == 'xmap_ext'){
				$params = &$root->getElementsByPath('params', 1);
			}
		}

		$result = ($asText) ? '' : array();

		if(is_object($params)){
			foreach($params->childNodes as $param){
				$name = $param->getAttribute('name');
				$label = $param->getAttribute('label');

				$key = $name ? $name : $label;
				if($label != '@spacer' && $name != '@spacer'){
					$value = str_replace("\n", '\n', $param->getAttribute('default'));
					if($asText){
						$result .= "$key=$value\n";
					} else{
						$result[$key] = $value;
					}
				}
			}
		}
		return $result;
	}

	/** convert a menuitem's params field to an array */
	function paramsToArray(&$menuparams){
		$tmp = explode("\n", $menuparams);
		$res = array();
		foreach($tmp AS $a){
			@list($key, $val) = explode('=', $a, 2);
			$res[$key] = str_replace('\n', "\n", $val);
		}
		$res['__TXT__'] = $menuparams;
		return $res;
	}

	function getXmlPath(){
		return _JLPATH_ADMINISTRATOR . '/components/com_xmap/extensions/' . $this->extension . '.xml';
	}

	// тут зачем-то было store
	function check(){
		if(is_array($this->_params)){
			$this->params = '';
		}
		return true;
	}

	function restore(){
		$database = database::getInstance();
		$row = null;
		$query = "select * from #__xmap_ext where extension='" . $this->extension . ".bak'";
		$database->setQuery($query);
		if($database->loadObject($row)){
			$this->params = $row->params;
			//mosDBTable::store();
		}
	}
}


/** Wraps all extension functions for Xmap */
class XmapPlugins{

	/** list all extension files found in the extensions directory */
	public static function loadAvailablePlugins(){
		$database = database::getInstance();

		$list = array();

		$query = "SELECT * FROM `#__xmap_ext` WHERE `published`=1";
		$database->setQuery($query);
		$rows = $database->loadAssocList();
		foreach($rows as $row){
			$extension = new XmapPlugin($database);
			$extension->bind($row);
			require_once(JPATH_BASE . '/' . JADMIN_BASE . '/components/com_xmap/extensions/' . $extension->extension . '.php');
			$list[$extension->extension] = $extension;
		}
		return $list;
	}

	public static function printTree(&$xmap, &$parent, &$cache, &$extensions){
		$result = null;

		$matches = array();
		if(preg_match('#^/?index.php.*option=(com_[^&]+)#', $parent->link, $matches)){
			$option = $matches[1];
			if(!empty($extensions[$option])){
				$parent->uid = "plug" . $extensions[$option]->id;
				$className = 'xmap_' . $option;
				$result = call_user_func_array(array($className, 'getTree'), array($xmap, $parent, $extensions[$option]->getParams()));
			}
		}
		return $result;
	}
}