<?php
/**
 * @package Joostina Lotos
 * @copyright Авторские права (C) 2011-2012 Joostina Lotos. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina Lotos! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @autor Gold Dragon (http://gd.joostina-cms.ru)
 */

// запрет прямого доступа
defined('_JLINDEX') or die();


class SefJoossef implements JSefModel{

	const separator = '-';

	public static function getUrlToSef($link){
		// если ссылка идёт на компонент главной страницы - очистим её
		if((JSef::$cfg_frontpage AND stripos($link, 'option=com_frontpage') > 0 AND !(stripos($link, 'limit'))) OR $link == 'index.php' OR $link == 'index.php?'){
			$link = JPATH_SITE . '/';
		} else{
			// Оснавная обработка
			$link = str_replace('&amp;', '&', $link);

			// Разбирает URL и возвращает его компоненты
			$url = parse_url($link);

			// проверяем часть fragment (после знака диеза #)
			$fragment = '';
			if(isset($url['fragment'])){

				// Проверка на валидность
				if(preg_match('@^[A-Za-z][A-Za-z0-9:_.-]*$@', $url['fragment'])){
					$fragment = '#' . $url['fragment'];
				}
			}

			// проверяем часть query после знака вопроса ?
			if(isset($url['query'])){

				// специальная обработка для javascript
				$url['query'] = stripslashes(str_replace('+', '%2b', $url['query']));

				// очистить возможные атаки XSS
				$url['query'] = preg_replace("'%3Cscript[^%3E]*%3E.*?%3C/script%3E'si", '', $url['query']);

				// разбиваем строку (URL) на части
				parse_str($url['query'], $parts);

				// формируем ссылку
				$link = '';
				foreach($parts as $key => $value){
					// отдельно запоминаем option чтобы разместить его первым в адресе
					if($key != 'option')
						$link .= $key . self::separator . $value . '/';
					else
						$option = $value . '/';
				}
			}

			$link = (isset($option)) ? JPATH_SITE . '/' . $option . $link . $fragment : '';
		}
		return $link;
	}

	public static function getSefToUrl(){
		$option = false;
		// получаем URL
		$link = $_SERVER['REQUEST_URI'];

		// получаем массив с параметрами
		$url = explode("/", $link);

		// присваиваем значения глобальным переменным
		foreach($url as $value){
			$value = explode(self::separator, $value, "2");
			$val1 = (isset($value[0])) ? $value[0] : false;
			$val2 = (isset($value[1])) ? $value[1] : '';

			// присваиваем если есть ключ
			if($val1){
				if(!$option){
					$_GET['option'] = $val1;
					$_REQUEST['option'] = $val1;
					$option = true;
				} else{
					$_GET[$val1] = $val2;
					$_REQUEST[$val1] = $val2;
				}
			}
		}
	}
}
