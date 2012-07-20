<?php
/**
 * @package Joostina
 * @copyright (C) 2009 Extention Team. Joostina Team. Все права защищены.
 * @license GNU/GPL, подробнее в help/lisense.php
 * @version $Id: array.php 05.07.2009 12:07:48 megazaisl $;
 * @since Version 1.3
 * Класс работы с массивами

 */
defined('_JLINDEX') or die();

class ArrayHelper{
	/**
	 * clear
	 * remove empty elements
	 * @access  public
	 * @param   type     $param  param_descr
	 * @return  Array
	 */
	public static function clear($array){
		$res = array();
		foreach($array as $key => $val){
			if(!empty($val)){
				if(is_numeric($key)){
					array_push($res, $val);
				} else{
					$res[$key] = $val;
				}
			}
		}
		$array = $res;
		return $array;
	}


	/**
	 * first
	 * return first element
	 * @access  public
	 * @return  mixed
	 */
	public static function first($array){
		$first = array_shift($array);
		array_unshift($array, $first);

		return $first;
	}

	/**
	 * last
	 * return last element
	 * @access  public
	 * @return  mixed
	 */
	public static function last($array){
		$lastt = array_pop($array);
		array_push($array, $last);

		return $last;
	}
}