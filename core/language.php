<?php
/**
 * Joostina Lotos CMS 1.4.1
 * @package   LANGUAGE
 * @version   1.4.1
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2012 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      30.08.2012
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Класс для работы с языковыми файлами
 * @see http://wiki.joostina-cms.ru/index.php/JLang
 *
 * @example
 *     типы языковых файлов
 *        'front' - системные клиентской части
 *        'admin' - системные администранивной части
 *        'com' - все компоненты
 *        'mod' - все модули
 *        'plg' - все плагины
 *        'tpl' - все шаблоны
 *     структура языкового файла соответвует требованиям для INI файлов
 *     комментарии начинаются с "точка с запятой"
 *     пример названия файла: com.mycomponent.lang.ini, mod.mymodules.lang.ini
 */
class JLang{

	/** @var object Интерфейс */
	private static $_instance = null;

	/** @var array Массив Ключ-Значение */
	public static $language = array();

	/** @var array Коды языка */
	public $lang_codes = array();

	/** @var null|string - Языковая константа (например, 'ru-RU') */
	public $lang = null;

	/** @var null|string - расширение (например, 'com_mycom', 'mod_mymod', 'plg_myplagin') */
	public $expansion = null;

	/** @var null|string - путь до языкового файла */
	public $lang_path = null;

	/**
	 * Конструктор
	 *
	 * @param
	 * @param string      $lang      - языковые константы
	 */
	private function __construct($expansion, $lang){
		$this->lang_codes = array(
			'be-BY' => 'belorussian', 'bg-BG' => 'bulgarian', 'ca-ES' => 'catalan', 'cs-CZ' => 'czech', 'da-DK' => 'danish', 'de-DE' => 'german', 'el-GR' => 'greek', 'en-GB' => 'english', 'es-ES' => 'spanish', 'et-EE' => 'estonian', 'eu-ES' => 'basque', 'fi-FI' => 'finnish', 'fr-FR' => 'french', 'gl-ES' => 'galician', 'hr-HR' => 'croatian', 'hu-HU' => 'hungarian', 'it-IT' => 'italian', 'ja-JP' => 'japanese', 'lv-LV' => 'latvian', 'nb-NO' => 'norwegian', 'nl-NL' => 'dutch', 'pl-PL' => 'polish', 'pt-BR' => 'portuguese', 'pt-PT' => 'portuguese', 'ro-RO' => 'romanian', 'ru-RU' => 'russian', 'sk-SK' => 'slovak', 'sl-SL' => 'slovenian ', 'sr-YU' => 'serbian ', 'sv-SE' => 'swedish ', 'th-TH' => 'thai ', 'tr-TR' => 'turkish', 'uk-UA' => 'ukrainian'
		);

		if(!preg_match('#([a-z]{2}-[A-Z]{2})#', $lang)){
			// попытка получить код языка из его названия
			$lang = $this->checkLang($lang);
		}

		// на всякий случай корректируем расширение если вдруг ошибка
		$type_a = array('sys_', 'front_', 'admin_', 'com_', 'mod_', 'plg_', 'tpl_');
		$type_b = array('sys.', 'front.', 'admin.', 'com.', 'mod.', 'plg.', 'tpl.');
		$count = 1;
		$this->expansion = str_replace($type_a, $type_b, $expansion, $count);

		$this->lang = $lang;

		$this->load($expansion, $lang);
	}

	/**
	 * @static   Получаем объект
	 *
	 * @param null|string $expansion - расширение (компонент, модуль, плагин)
	 * @param string      $lang      - язык
	 *
	 * @return object
	 */
	public static function getInstance($expansion = null, $lang = 'ru-RU'){
		if(!is_object(self::$_instance)){
			self::$_instance = new JLang($expansion, $lang);
		}
		return self::$_instance;
	}

	/**
	 * Попытка найти код языка по названию или вернцуть по умолчанию
	 * @param null|string $lang - название языка
	 *
	 * @return mixed|null|string - код языка
	 */
	private function checkLang($lang = null){
		$lang = array_search($lang, $this->lang_codes);
		// если ничего не найдено, то берём "русский" по умолчанию
		if(!$lang){
			$lang = 'ru-RU';
		}
		return $lang;
	}

	/**
	 * Подключение языкового файла
	 */
	private function load(){
		$pathFile = _JLPATH_LANG . DS . $this->lang . DS . $this->expansion . '.lang.ini';
		if(!isset(self::$language[$this->expansion]) and is_file($pathFile)){
			$this->lang_path;
			self::$language = parse_ini_file($pathFile);
		}
	}

	/**
	 * @static - Возвращает значение ключа
	 *
	 * @param string $key    - ключ языка
	 * @param bool   $jsSafe - TRUE если необходимо экранирование
	 *
	 * @return string - значение ключа
	 */
	public static function _($key, $jsSafe = false){
		$string = '';

		if(isset(self::$language[$key])){
			$string = self::$language[$key];

			if($jsSafe){
				$string = addslashes($string);
			}
		} else{
			_pdump($key);
			$string = $key;
		}
		return $string;
	}

	/**
	 * @static Возвращает код языка
	 *
	 * @return mixed|null|string - код языка
	 */
	public function getLanguage(){
		return $this->lang;
	}

	/**
	 * @static Возвращает путь до языкового файла
	 *
	 * @return null|string - путь до файла
	 */
	public function getLangFile(){
		return $this->lang_path;
	}

	/**
	 * @static Подключение языковых файлов по категориям
	 *
	 * @param array $ind - тип языковых фалов
	 *     'front' - системные клиентской части
	 *     'admin' - системные администранивной части
	 *     'com' - все компоненты
	 *     'mod' - все модули
	 *     'plg' - все плагины
	 *     'tpl' - все шаблоны
	 */
	public function loadAll($ind = array()){
		if(!is_array($ind)){
			$ind = array($ind);
		}
		$fileNames = scandir(_JLPATH_LANG . DS . $this->lang);

		$expansion = array();

		foreach($fileNames as $fileName){
			$tmp1 = preg_match('#^(front|admin|com|mod|plg|tpl)\.([a-z0-9_]+)(\.lang\.ini)$#', $fileName, $tmp2);
			if($tmp1){
				foreach($ind as $value){
					if($tmp2[1] == $value){
						$this->expansion = $tmp2[1] . '.' . $tmp2[2];
						$this->load();
						$expansion = array_merge($expansion, self::$language);
					}
				}
			}
		}
		self::$language = $expansion;
	}
}