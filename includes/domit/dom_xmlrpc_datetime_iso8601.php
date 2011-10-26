<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*
* dom_xmlrpc_array_document wraps a PHP array with the DOM XML-RPC API
* @package dom-xmlrpc
* @copyright (C) 2004 John Heinstein. All rights reserved
* @license http://www.gnu.org/copyleft/lesser.html LGPL License
* @author John Heinstein <johnkarl@nbnet.nb.ca>
* @link http://www.engageinteractive.com/dom_xmlrpc/ DOM XML-RPC Home Page
* DOM XML-RPC is Free Software
**/

defined('_VALID_MOS') or die();


class dom_xmlrpc_datetime_iso8601 {
	var $year;
	var $month;
	var $day;
	var $hour;
	var $minute;
	var $second;
	function dom_xmlrpc_datetime_iso8601($datetime) {
		if(is_int($datetime)) {
			$this->fromDateTime_php($datetime);
		} else {
			$this->fromDateTime_iso($datetime);
		}
	}

	function phpToISO(&$phpDate) {
		return (date('Y',$phpDate).date('m',$phpDate).date('d',$phpDate).'T'.date('H',$phpDate).
			':'.date('i',$phpDate).':'.date('s',$phpDate));
	}

	function fromDateTime_php($phpdatetime) {

		$this->year = date('Y',$phpdatetime);
		$this->month = date('m',$phpdatetime);
		$this->day = date('d',$phpdatetime);
		$this->hour = date('H',$phpdatetime);
		$this->minute = date('i',$phpdatetime);
		$this->second = date('s',$phpdatetime);
	}

	function fromDateTime_iso($isoFormattedString) {

		$this->year = substr($isoFormattedString,0,4);
		$this->month = substr($isoFormattedString,4,2);
		$this->day = substr($isoFormattedString,6,2);
		$this->hour = substr($isoFormattedString,9,2);
		$this->minute = substr($isoFormattedString,12,2);
		$this->second = substr($isoFormattedString,15,2);
	}

	function getDateTime_iso() {

		return ($this->year.$this->month.$this->day.'T'.$this->hour.':'.$this->minute.
			':'.$this->second);
	}

	function getDateTime_php() {

		return mktime($this->hour,$this->minute,$this->second,$this->month,$this->day,$this->year);
	}

}




?>
