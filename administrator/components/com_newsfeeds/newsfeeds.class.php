<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

/**
 * @package Joostina
 * @subpackage Newsfeeds
 */
class mosNewsFeed extends mosDBTable {
	/**
	 @var int Primary key*/
	var $id = null;
	/**
	 @var int*/
	var $catid = null;
	/**
	 @var string*/
	var $name = null;
	/**
	 @var string*/
	var $link = null;
	/**
	 @var string*/
	var $filename = null;
	/**
	 @var int*/
	var $published = null;
	/**
	 @var int*/
	var $numarticles = null;
	/**
	 @var int*/
	var $cache_time = null;
	/**
	 @var int*/
	var $checked_out = null;
	/**
	 @var time*/
	var $checked_out_time = null;
	/**
	 @var int*/
	var $ordering = null;
	/**
	 @var int кодировка ленты*/
	var $code = null;
	/**
	 * @param database A database connector object
	 */
	function mosNewsFeed(&$db) {
		$this->mosDBTable('#__newsfeeds','id',$db);
	}
}