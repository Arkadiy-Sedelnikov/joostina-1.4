<?php

/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
// запрет прямого доступа
defined('_JLINDEX') or die();

mosMainFrame::addLib('dbconfig');

/**
 * Category database table class
 * @package Joostina
 */
class searchByTagConfig extends dbConfig{

	/**
	 * Заголовок страницы
	 */
	var $title = _SEARCH_ALL_ITEM_W_TAG;

	function __construct($db, $group = 'com_search', $subgroup = 'search_by_tag'){
		parent::__construct($db, $group, $subgroup);
	}

}

class searchByTag{

	function construct_url($item, $group){

		$view_link = 'index.php?option=' . $group['group_name'];
		$view_link .= '&task=' . $group['task'];
		$view_link .= '&id=' . $item->id;

		if($group['url_params']){
			$url_params_arr = explode('&', $group['url_params']);
			foreach($url_params_arr as $v){
				$arr0 = explode('=', $v);
				$view_link .= '&' . $arr0[0];
				if(strpos($arr0[1], '%') !== false){
					$arr0[1] = str_replace('%', '', $arr0[1]);
					$view_link .= '=' . $item->$arr0[1];
				} else{
					$view_link .= '=' . $arr0[1];
				}
			}
		}

		$view_link = JSef::getUrlToSef($view_link);

		return $view_link;
	}

}
