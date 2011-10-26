<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/Relay.php";
class File_Archive_Reader_Filter extends File_Archive_Reader_Relay {
	var $predicate;
	function File_Archive_Reader_Filter($predicate,&$source) {
		parent::File_Archive_Reader_Relay($source);
		$this->predicate = $predicate;
	}
	function next() {
		do {
			$error = $this->source->next();
			if($error !== true) {
				return $error;
			}
		} while(!$this->predicate->isTrue($this->source));
		return true;
	}
	function select($filename,$close = true) {
		if($close) {
			$error = $this->close();
			if(PEAR::isError($error)) {
				return $error;
			}
		}
		do {
			$error = $this->source->select($filename,false);
			if($error !== true) {
				return $error;
			}
		} while(!$this->predicate->isTrue($this->source));
		return true;
	}
}

?>
