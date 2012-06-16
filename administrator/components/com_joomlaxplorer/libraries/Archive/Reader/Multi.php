<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/Relay.php";
class File_Archive_Reader_Multi extends File_Archive_Reader_Relay{
	var $sources = array();
	var $currentIndex = 0;

	function File_Archive_Reader_Multi(){
		parent::File_Archive_Reader_Relay($tmp = null);
	}

	function addSource(&$source){
		$this->sources[] = &$source;
	}

	function next(){
		while(array_key_exists($this->currentIndex, $this->sources)){
			$this->source = &$this->sources[$this->currentIndex];
			if(($error = $this->source->next()) === false){
				$error = $this->source->close();
				if(PEAR::isError($error)){
					return $error;
				}
				$this->currentIndex++;
			} else{
				return $error;
			}
		}
		return false;
	}

	function close(){
		$error = parent::close();
		$this->currentIndex = 0;
		return $error;
	}
}

?>
