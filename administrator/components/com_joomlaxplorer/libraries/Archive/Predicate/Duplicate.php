<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_JLINDEX') or die();
require_once dirname(__file__) . "/../Predicate.php";
class File_Archive_Predicate_Duplicate extends File_Archive_Predicate{
	var $newest = array();
	var $pos = 0;

	function File_Archive_Predicate_Duplicate(&$source){
		$source->close();
		$pos = 0;
		while($source->next()){
			$filename = $source->getFilename();
			$stat = $source->getStat();
			$value = isset($this->newest[$filename]) ? $this->newest[$filename] : null;
			if($value === null || $this->compare($stat[9], $value[0]) >= 0){
				$this->newest[$filename] = array($stat[9], $pos);
			}
			$pos++;
		}
	}

	function compare($a, $b){
		return ($a === null ? -1 : $a) - ($b === null ? -1 : $b);
	}

	function isTrue(&$source){
		$filename = $source->getFilename();
		$stat = $source->getStat();
		$value = isset($this->newest[$filename]) ? $this->newest[$filename] : null;
		if($value === null){
			$delete = false;
		} else{
			$comp = $this->compare($stat[9], $value[0]);
			$delete = $comp < 0 || ($comp == 0 && $this->pos != $value[1]);
		}
		$this->pos++;
		return $delete;
	}
}

?>
