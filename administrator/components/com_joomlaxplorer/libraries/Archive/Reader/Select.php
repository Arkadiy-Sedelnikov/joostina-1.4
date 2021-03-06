<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_JLINDEX') or die();
require_once dirname(__file__) . "/Relay.php";
class File_Archive_Reader_Select extends File_Archive_Reader_Relay{
	var $filename;

	function File_Archive_Reader_Select($filename, &$source){
		parent::File_Archive_Reader_Relay($source);
		$this->filename = $filename;
	}

	function next(){
		return $this->source->select($this->filename, false);
	}
}

?>
