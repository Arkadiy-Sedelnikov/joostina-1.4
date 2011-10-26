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
class Timer {
	var $startTime;
	var $stopTime;
	function start() {
		$this->startTime = microtime();
	}

	function stop() {
		$this->stopTime = microtime();
	}

	function getTime() {
		return $this->elapsed($this->startTime,$this->stopTime);
	}

	function elapsed($a,$b) {
		list($a_micro,$a_int) = explode(' ',$a);
		list($b_micro,$b_int) = explode(' ',$b);
		if($a_int > $b_int) {
			return ($a_int - $b_int) + ($a_micro - $b_micro);
		} else
		if($a_int == $b_int) {
			if($a_micro > $b_micro) {
				return ($a_int - $b_int) + ($a_micro - $b_micro);
			} else
			if($a_micro < $b_micro) {
				return ($b_int - $a_int) + ($b_micro - $a_micro);
			} else {
				return 0;
			}
		} else {

			return ($b_int - $a_int) + ($b_micro - $a_micro);
		}
	}
}