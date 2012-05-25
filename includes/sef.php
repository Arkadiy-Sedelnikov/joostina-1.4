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
defined('_VALID_MOS') or die();

// TODO GoDr Необходимо вынести в настройки
// удалять из ссылок парамтер ItemId
DEFINE('_SEF_DELETE_ITEMID', false);

/**
 * Основной класс для обработки SEF
 */
class JSef{
	// разрешён ли SEF
	/**
	 * @var разрешён ли SEF
	 */
	public static $cfg_sef;

	// очищать ссылку на главную
	/**
	 * @var
	 */
	public static $cfg_frontpage;

	/**
	 * @var имя компонента
	 */
	public static $option;

	/**
	 * @var массив sef-файлов компонентов
	 */
	public static $sef_files;

	/**
	 * @static Подключение класса
	 * @param int $cfg_sef
	 * @param int $cfg_frontpage
	 * @return object JSef
	 */
	public static function run($cfg_sef = 0, $cfg_frontpage = 0){
		// запоминаем настройки
		self::$cfg_sef = $cfg_sef;
		self::$cfg_frontpage = $cfg_frontpage;

		// имя компонента в адресной строке
		self::$option = self::getOption();

		// список sef-файлов
		$sef_com = scandir(JPATH_BASE . DS . 'includes' . DS . 'sef' . DS);
		foreach($sef_com as $value){
			if(preg_match('#\.sef\.php$#i', $value, $tmp))
				self::$sef_files[] = $value;
		}

		//используется ли SEF
		if(self::$cfg_sef){
			// Существует ли сторонний обработчик, то подключаем его, если нет, то подключаем стандартный обработчик
			if(file_exists(JPATH_BASE . DS . 'components' . DS . 'com_sef' . DS . 'sef.php')){
				require_once (JPATH_BASE . DS . 'components' . DS . 'com_sef' . DS . 'sef.php');
			} else{
				// перебрасываем на корректный адрес
				if(ltrim(strpos($_SERVER['REQUEST_URI'], 'index.php'), '/') == 1 AND $_SERVER['REQUEST_METHOD'] == 'GET'){
					//Преобразование URL`а
					$link = self::getUrlToSef('index.php?' . $_SERVER['QUERY_STRING']);
					// Переадресация на SEF-адрес
					header("Location: " . $link, TRUE, 301);
					exit(301);
				}else{
					// получаем имя класса
					$sefclass = self::getSefClass($_SERVER['REQUEST_URI']);

					// Передаём значения из Url глобальным переменным
					$sefclass::getSefToUrl();
				}
			}
		}
	}

	/**
	 * @static Получение имени компонета
	 * @param string $link - нормальная ссылка
	 * @return string - имя компонента
	 */
	private static function getOption($link = ''){
		if($link != ''){
			$option = (isset($_REQUEST['option'])) ? $_REQUEST['option'] : '';
		} else{
			$option = (isset($_REQUEST['option'])) ? $_REQUEST['option'] : '';
		}
		if($option == ''){
			$link = explode("/", preg_replace('#(^\/)|(\/$)#', '', $_SERVER['REQUEST_URI']));
			$option = (isset($link[0])) ? $link[0] : '';
		}
		return $option;
	}

	/**
	 * @static преобразует нормальную ссылку в Sef-ссылку
	 * @param $link - нормальная ссылка
	 * @return mixed - sef-ссылка
	 */
	public static function getUrlToSef($link){
		if(self::$cfg_sef){
			// получаем имя класса
			$sefclass = self::getSefClass($link);

			// получаем Sef-ссылку
			$link = $sefclass::getUrlToSef($link);
		}
		return $link;
	}

	/**
	 * @static - Получаем имя класса
	 * @param $link - ссылка
	 * @return string - имя класса
	 */
	private static function getSefClass($link){
		// получаем имя компонента из ссылки
		$option = self::getOption($link);

		// существует ли sef-файл для этого компонента
		if(array_key_exists($option . '.sef.php', self::$sef_files)){
			require_once(JPATH_BASE . DS . 'includes' . DS . 'sef' . DS . $option . '.sef.php');
		}else{
			require_once(JPATH_BASE . DS . 'includes' . DS . 'sef' . DS . 'joossef.sef.php');
		}

		// проверка существует ли сответсвующий SEF-класс
		$sefclass = ucfirst(preg_replace('#^com_#', '', $option));
		$sefclass = (class_exists('Sef' . $sefclass)) ? 'Sef' . $sefclass : 'SefJoossef';

		// TODO GoDr Временная заглушка: всегда используется sef-файл по умолчанию. Удалить к версии 1.4.1
		$sefclass = 'SefJoossef';

		return $sefclass;
	}

}

/**
 * Интерфейс для SEF-расширений
 */
interface JSefModel{

	/**
	 * @static Преобразование простой ссылки в SEF
	 * @abstract
	 * @param $link
	 * @return mixed
	 */
	public static function getUrlToSef($link);

	/**
	 * @static Загрузка параметров из SEF-url в глобальные
	 * @abstract
	 * @return mixed
	 */
	public static function getSefToUrl();
}


// TODO GoDr временная заглушка
function sefRelToAbs($link){
	return JSef::getUrlToSef($link);
}


// TODO GoDr временная функция для тестирования
function _v($var){
	echo '<pre style="border:1px solid #ff0000;color:#ff0000;padding:5px;background-color:#ffffff;">';
	var_dump($var);
	echo "</pre>";
}

// TODO GoDr временная функция для тестирования
function _p($var){
	echo '<pre style="border:1px solid #ff0000;color:#ff0000;padding:5px;background-color:#ffffff;">';
	print_r($var);
	echo "</pre>";
}


