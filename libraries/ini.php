<?php
/**
 * Joostina Lotos CMS 1.4
 * @package   LIBRARIES
 * @version   1.4.1
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2012 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      17.07.2012
 */

defined('_JLINDEX') or die;

/**
 * Класс для работы с INI-файлами
 *
 * @see       http://wiki.joostina-cms.ru/index.php/JLIni
 */
class JLIni{
	/** @var object */
	private static $_instance;

	/** @var string Путь до файла */
	public $file_url;

	/** @var array Содержимое файла */
	public $file_parse = array();

	/** @var bool true - многомерный массив, false - массив без секций (по умолчанию)*/
	private $proces_sec;

	/** @var INI_SCANNER_RAW, INI_SCANNER_NORMAL */
	private $scan_mode;

	/** Конструктор
	 *
	 * @param null|string $file_url   - Путь до файла
	 * @param bool        $proces_sec - true - многомерный массив, false - массив без секций (по умолчанию)
	 * @param bool        $scan_mode  - true - INI_SCANNER_RAW, false - INI_SCANNER_NORMAL
	 */
	private function __construct($file_url, $proces_sec, $scan_mode){
		$this->filename = trim(strval(strtolower($file_url)));
		$this->proces_sec = $proces_sec;
		$this->scan_mode = ($scan_mode) ? INI_SCANNER_RAW : INI_SCANNER_NORMAL;
		$this->parse($this->file_url, $this->proces_sec, $this->scan_mode);
	}

	/**
	 * @static Подключение интерфейса
	 *
	 * @param null|string $file_url   - Путь до файла
	 * @param bool        $proces_sec - true - многомерный массив, false - массив без секций (по умолчанию)
	 * @param bool        $scan_mode  - true - INI_SCANNER_RAW, false - INI_SCANNER_NORMAL
	 *
	 * @return object
	 */
	public static function getInstance($file_url = null, $proces_sec = false, $scan_mode = false){
		if(!isset(self::$_instance)){
			$class_name = __CLASS__;
			self::$_instance = new $class_name($file_url, $proces_sec, $scan_mode);
		}
		return self::$_instance;
	}

	/**
	 * Парсинг INI-файла
	 * @param $file_url
	 */
	public function parse($file_url){
		if($file_url){
			if(file_exists($file_url)){
				$this->file_parse = parse_ini_file($file_url, $this->proces_sec, $this->scan_mode);
			}
		}
	}

	/**
	 * Прверяет существует ли ключ (параметр) в file_parse
	 *
	 * @param string $str - имя ключа
	 *
	 * @return null - Возвращает результат: значение или false
	 */
	public function checkKey($str = ''){
		if(array_key_exists($str, $this->file_parse)){
			return $this->file_parse[$str];
		} else{
			return null;
		}
	}

	/**
	 * Получает значение по ключу
	 * @param string     $key     - ключ (параметр)
	 * @param string     $section - секция (зависит от $scan_mode)
	 *
	 * @return null|string    Результат
	 */
	public function getValue($key, $section = ''){
		if($this->proces_sec){
			if(isset($this->file_parse[$section][$key])){
				return $this->file_parse[$section][$key];
			} else{
				return null;
			}
		} else{
			return $this->checkKey($key);
		}
	}


	public function getKey($str, $strict=false){
		return array_search ($str, $this->file_parse, $strict);
	}

}
