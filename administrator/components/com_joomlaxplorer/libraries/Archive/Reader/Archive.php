<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/../Reader.php";
class File_Archive_Reader_Archive extends File_Archive_Reader {
	var $source = null;
	var $sourceOpened = false;
	var $sourceInitiallyOpened;
	function next() {
		if(!$this->sourceOpened && ($error = $this->source->next()) !== true) {
			return $error;
		}
		$this->sourceOpened = true;
		return true;
	}
	function File_Archive_Reader_Archive(&$source,$sourceOpened = false) {
		$this->source = &$source;
		$this->sourceOpened = $this->sourceInitiallyOpened = $sourceOpened;
	}
	function close() {
		if(!$this->sourceInitiallyOpened && $this->sourceOpened) {
			$this->sourceOpened = false;
			if($this->source !== null) {
				return $this->source->close();
			}
		}
	}
}

?>
