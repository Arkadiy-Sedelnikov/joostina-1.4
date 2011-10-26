<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
* @category   Networking
* @package    FTP
* @author     Tobias Schlitt <toby@php.net>
* @author     Laurent Laville <pear@laurent-laville.org>
* @author     Chuck Hagenbuch <chuck@horde.org>
* @copyright  1997-2005 The PHP Group
* @license    http://www.php.net/license/3_0.txt  PHP License 3.0
* @version    CVS: $Id:Observer.php 13 2007-05-13 07:10:43Z soeren $
* @link       http://pear.php.net/package/Net_FTP
* @since      File available since Release 0.0.1
**/
defined('_VALID_MOS') or die();
class Net_FTP_Observer {
	var $_id;
	function Net_FTP_Observer() {
		$this->_id = md5(microtime());
	}
	function getId() {
		return $this->_id;
	}
	function notify($event) {
		return;
	}
}
?>
