<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/../Predicate.php";
class File_Archive_Predicate_Index extends File_Archive_Predicate {
	var $indexes;
	var $pos = 0;
	function File_Archive_Predicate_Index($indexes) {
		$this->indexes = $indexes;
	}
	function isTrue(&$source) {
		return isset($this->indexes[$this->pos++]);
	}
}

?>
