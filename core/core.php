<?php
/**
 * Joostina Lotos CMS 1.4.1
 * @package   CORE
 * @version   1.4.1
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2012 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      02.07.2012
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Основной класс - Ядро
 * @see http://wiki.joostina-cms.ru/index.php/JCore
 */
class JCore{
	/** @var array Массив для сбора ошибок */
	public static $_error_arr = array();

	/** @var object Интерфейс класса ядра*/
	private static $_instance;

	/** @var object Интерфейс класса конфигурации */
	public $_config;

	/**
	 * Конструктор
	 */
	private function __construct(){

		// подключения интерфейса Конфигурации
		//$this->_config = JConfig::getInstance();
	}

	/**
	 * @static Подключение класса
	 * @return object
	 */
	public static function getInstance(){
		if(!isset(self::$_instance)){
			$class_name = __CLASS__;
			self::$_instance = new $class_name;
		}
		return self::$_instance;
	}

	/**
	 * Подключение библиотек (/libraries/...)
	 *
	 * @param $str - имя библиотеки, оно же имя файла $str.php
	 *
	 * @return bool - false - нет файла, true - файл подключен
	 */
	public static function getLib($str){
		$file_lib = _JLPATH_LIBRARIES . DS . $str . '.php';
		if(is_file($file_lib)){
			require_once($file_lib);
			return true;
		} else{
			return false;
		}
	}

	/**
	 * Получение значения конфигурации
	 * @param $varname - параметр конфигурации
	 *
	 * @return null|string - значение параметра
	 */
	public function getCfg($varname = null){
		$varname = 'config_' . $varname;
		$varname = (isset($this->_config->$varname)) ? $this->_config->$varname : null;
		return $varname;
	}

	/**
	 * @static Запись строки в буффер
	 *
	 * @param $str - строка
	 */
	public static function setErrorArr($str){
		self::$_error_arr[] = $str;
	}

	/**
	 * @static Вывод всего массива
	 */
	public static function getErrorArr(){
		echo '<pre style="border:1px solid #ff0000;color:#ff0000;padding:5px;background-color:#ffffff;">';
		foreach(self::$_error_arr as $str){
			echo $str . "\n\n";
		}
		echo "</pre>";

	}

}

