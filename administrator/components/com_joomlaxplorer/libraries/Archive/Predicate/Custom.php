<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/../Predicate.php";
class File_Archive_Predicate_Custom extends File_Archive_Predicate{
	var $expression;
	var $useName;
	var $useStat;
	var $useMIME;

	function File_Archive_Predicate_Custom($expression){
		$this->expression = $expression . ";";
		if(strpos($this->expression, "return") === false){
			$this->expression = "return " . $this->expression;
		}
		$this->useName = (strpos($this->expression, '$name') !== false);
		$this->useStat = (strpos($this->expression, '$stat') !== false);
		$this->useMIME = (strpos($this->expression, '$mime') !== false);
	}

	function isTrue(&$source){
		if($this->useName){
			$name = $source->getFilename();
		}
		if($this->useStat){
			$stat = $source->getStat();
			$size = $stat[7];
			$time = (isset($stat[9]) ? $stat[9] : null);
		}
		if($this->useMIME){
			$mime = $source->getMIME();
		}
		return eval($this->expression);
	}
}

?>
