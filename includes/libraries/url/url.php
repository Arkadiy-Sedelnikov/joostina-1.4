<?php
/**
 * Класс работы с ссылками
 * @package Joostina
 * @copyright (C) 2009 Extention Team. Joostina Team. Все права защищены.
 * @license GNU/GPL, подробнее в help/lisense.php
 * @version $Id: array.php 05.07.2009 12:07:48 megazaisl $;
 * @since Version 1.3
 */
defined('_JLINDEX') or die();

class UrlHelper{

	/**
	 * Prep URL
	 * Simply adds the http:// part if missing
	 * @access    public
	 * @param    string    the URL
	 * @return    string
	 */
	function prep_url($str = ''){
		if($str == 'http://' OR $str == ''){
			return '';
		}
		if(substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://'){
			$str = 'http://' . $str;
		}
		return $str;
	}
}