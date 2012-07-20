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
 * @Date      02.07.2012
 */

defined('_JLINDEX') or die;

/**
 * Класс для работы с URL
 *
 * @see       http://wiki.joostina-cms.ru/index.php/JLUrl
 */
class JLUrl{
	/** @var object */
	private static $_instance;

	/** @var string Переданный URL */
	public $_url = null;

	/** @var string Протокол */
	public $_scheme = null;

	/** @var string Хост */
	public $_host = null;

	/** @var integer Порт */
	public $_port = null;

	/** @var string Имя пользователя */
	public $_user = null;

	/** @var string Пароль */
	public $_pass = null;

	/** @var string Путь */
	public $_path = null;

	/** @var string Значение (Query) */
	public $_query = null;

	/** @var  string Якорь (Anchor) */
	public $_fragment = null;

	/** @var array Массив Значений */
	public $_vars = array();

	/** Конструктор
	 *
	 * @param null|string $url - Адрес (URL) Если не задан то берётся адрес сайта из конфигурации
	 */
	private function __construct($url = null){
		if(is_null($url)){
			$core = JCore::getInstance();
			$url = $core->getCfg('live_site');
		}
		// записываем оригинальный адрес
		$this->_url = $url;

		$this->parse($url);
	}

	/**
	 * @static Подключение интерфейса
	 *
	 * @param string $url - Адрес (URL) Если не задан то берётся адрес сайта из конфигурации
	 *
	 * @return object
	 */
	public static function getInstance($url = null){
		if(!isset(self::$_instance)){
			$class_name = __CLASS__;
			self::$_instance = new $class_name($url);
		}
		return self::$_instance;
	}

	/**
	 * Парсинг URL
	 *
	 * @param $url - адрес
	 *
	 * @return bool - результат
	 */
	public function parse($url = null){
		if(!is_null($url)){
			// записываем оригинальный адрес
			$this->_url = $url;

			// парсим URL
			$_parts = $this->parse_url($url);
			if($_parts){

				// Заменяем &amp; на & for
				if(isset($_parts['query']) and strpos($_parts['query'], '&amp;')){
					$_parts['query'] = str_replace('&amp;', '&', $_parts['query']);
				}

				// записываем занные
				$this->_scheme = isset($_parts['scheme']) ? $_parts['scheme'] : null;
				$this->_user = isset($_parts['user']) ? $_parts['user'] : null;
				$this->_pass = isset($_parts['pass']) ? $_parts['pass'] : null;
				$this->_host = isset($_parts['host']) ? $_parts['host'] : null;
				$this->_port = isset($_parts['port']) ? $_parts['port'] : null;
				$this->_path = isset($_parts['path']) ? $_parts['path'] : null;
				$this->_query = isset($_parts['query']) ? $_parts['query'] : null;
				$this->_fragment = isset($_parts['fragment']) ? $_parts['fragment'] : null;

				// Парсим значения
				$this->_vars = array();
				if(isset($_parts['query'])){
					parse_str($_parts['query'], $this->_vars);
				}
				return true;
			} else{
				return false;
			}
		} else{
			return false;
		}
	}

	/**
	 * Does a UTF-8 safe version of PHP parse_url function
	 *
	 * @param   string  $url  URL to parse
	 *
	 * @return  mixed  Associative array or false if badly formed URL.
	 *
	 * @see     http://us3.php.net/manual/en/function.parse-url.php
	 * @since   11.1
	 */
	public static function parse_url($url){
		$result = array();
		// Build arrays of values we need to decode before parsing
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "$", ",", "/", "?", "%", "#", "[", "]");
		// Create encoded URL with special URL characters decoded so it can be parsed
		// All other characters will be encoded
		$encodedURL = str_replace($entities, $replacements, urlencode($url));
		// Parse the encoded URL
		$encodedParts = parse_url($encodedURL);
		// Now, decode each value of the resulting array
		foreach($encodedParts as $key => $value){
			$result[$key] = urldecode($value);
		}
		return $result;
	}

	/**
	 * Возвращает полный адрес.
	 *
	 * @param   array  $parts  Массив, определяющий набор частей адерса.
	 *
	 * @return  string  URI строка
	 */
	public function _toString($parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment')){
		// Make sure the query is created
		$query = $this->getQuery();

		$url = '';
		$url .= in_array('scheme', $parts) ? (!empty($this->_scheme) ? $this->_scheme . '://' : '') : '';
		$url .= in_array('user', $parts) ? $this->_user : '';
		$url .= in_array('pass', $parts) ? (!empty($this->_pass) ? ':' : '') . $this->_pass . (!empty($this->_user) ? '@' : '') : '';
		$url .= in_array('host', $parts) ? $this->_host : '';
		$url .= in_array('port', $parts) ? (!empty($this->_port) ? ':' : '') . $this->_port : '';
		$url .= in_array('path', $parts) ? $this->_path : '';
		$url .= in_array('query', $parts) ? (!empty($query) ? '?' . $query : '') : '';
		$url .= in_array('fragment', $parts) ? (!empty($this->_fragment) ? '#' . $this->_fragment : '') : '';

		return $url;
	}

	/**
	 * Устанавливает значение
	 *
	 * @param   string  $name   Имя Параметр
	 * @param   string  $value  Значение
	 *
	 * @return  string  Previous value for the query variable.
	 *
	 * @since   11.1
	 */
	public function setVar($name, $value){
		$this->_vars[$name] = $value;
	}

	/**
	 * Проверка параметра
	 *
	 * @param   string  $name  Имя параметра.
	 *
	 * @return  boolean  True если параметр есть, False - если параметра нет
	 */
	public function checkVar($name){
		return array_key_exists($name, $this->_vars);
	}

	/**
	 * Возвращает значение параметра
	 *
	 * @param   string  $name     Имя параметра.
	 * @param   string  $default  Значение по умолчанию.
	 *
	 * @return  string  Значение.
	 */
	public function getVar($name, $default = null){
		if(array_key_exists($name, $this->_vars)) {
			return $this->_vars[$name];
		} else{
			return $default;
		}
	}

	/**
	 * Удаление параметра из массива
	 *
	 * @param   string  $name  Имя параметра
	 *
	 * @return  void
	 */
	public function delVar($name){
		if(array_key_exists($name, $this->_vars)){
			unset($this->_vars[$name]);
		}
	}

	/**
	 * Получить протокол
	 *
	 * @return  string Протокол URL (http, https, ftp, etc...).
	 */
	public function getScheme(){
		return $this->_scheme;
	}

	/**
	 * Установить протокол
	 *
	 * @param   string  $scheme  (http, https, ftp, etc...)
	 *
	 * @return  void
	 */
	public function setScheme($scheme){
		$this->_scheme = $scheme;
	}

	/**
	 * Получить  username
	 *
	 * @return  string  URI username.
	 */
	public function getUser(){
		return $this->_user;
	}

	/**
	 * Установить URI username.
	 *
	 * @param   string  $user  URI username.
	 *
	 * @return  void
	 */
	public function setUser($user){
		$this->_user = $user;
	}

	/**
	 * Получить URI-пароль
	 *
	 * @return  string URI-пароль
	 */
	public function getPass(){
		return $this->_pass;
	}

	/**
	 * Установить URI-пароль
	 *
	 * @param   string $pass URI-пароль
	 *
	 * @return  void
	 */
	public function setPass($pass){
		$this->_pass = $pass;
	}

	/**
	 * Получить URI-хост
	 *
	 * @return  string URI-хост
	 */
	public function getHost(){
		return $this->_host;
	}

	/**
	 * Установить URI-хост
	 *
	 * @param   string  $host URI-хост
	 *
	 * @return  void
	 */
	public function setHost($host){
		$this->_host = $host;
	}

	/**
	 * Получить URI-порт
	 *
	 * @return  integer  URI-порт
	 */
	public function getPort(){
		return (isset($this->_port)) ? $this->_port : null;
	}

	/**
	 * Установить URI-порт
	 *
	 * @param   integer  $port  URI-порт
	 *
	 * @return  void
	 */
	public function setPort($port){
		$this->_port = $port;
	}

	/**
	 * получить URI-путь
	 *
	 * @return  string URI-путь
	 */
	public function getPath(){
		return $this->_path;
	}

	/**
	 * Установить URI-путь
	 *
	 * @param   string  $path URI-путь
	 *
	 * @return  void
	 */
	public function setPath($path){
		$this->_path = $this->_cleanPath($path);
	}

	/**
	 * Получить URI-archor (значение после "#")
	 *
	 * @return  string  URI-archor
	 *
	 * @since   11.1
	 */
	public function getFragment(){
		return $this->_fragment;
	}

	/**
	 * Установить URI-archor (значение после "#")
	 *
	 * @param   string  $anchor  URI-archor
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setFragment($anchor){
		$this->_fragment = $anchor;
	}


	/**
	 * Resolves //, ../ and ./ from a path and returns
	 * the result. Eg:
	 *
	 * /foo/bar/../boo.php    => /foo/boo.php
	 * /foo/bar/../../boo.php => /boo.php
	 * /foo/bar/.././/boo.php => /foo/boo.php
	 *
	 * @param   string  $path  The URI path to clean.
	 *
	 * @return  string  Cleaned and resolved URI path.
	 */
	protected function _cleanPath($path){
		$path = explode('/', preg_replace('#(/+)#', '/', $path));

		for($i = 0, $n = count($path); $i < $n; $i++){
			if($path[$i] == '.' or $path[$i] == '..'){
				if(($path[$i] == '.') or ($path[$i] == '..' and $i == 1 and $path[0] == '')){
					unset($path[$i]);
					$path = array_values($path);
					$i--;
					$n--;
				} elseif($path[$i] == '..' and ($i > 1 or ($i == 1 and $path[0] != ''))){
					unset($path[$i]);
					unset($path[$i - 1]);
					$path = array_values($path);
					$i -= 2;
					$n -= 2;
				}
			}
		}

		return implode('/', $path);
	}

	/**
	 * Добавление к адресу http://
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public function prepUrl($str = ''){
		if($str == 'http://' OR $str == '') {
			return '';
		}

		if(substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://'){
			$str = 'http://' . $str;
		}
		return $str;
	}

	public function compareUrls($url_a, $url_b, $ind = false){
		$link_a = html_entity_decode($url_a);
		$link_b = html_entity_decode($url_b);

		$link_a_arr = parse_url($link_a);
		$link_b_arr = parse_url($link_b);
		if(!$ind){
			$arr_a[] = isset($link_a_arr['scheme']) ? $link_a_arr['scheme'] : '';
			$arr_a[] = isset($link_a_arr['user']) ? $link_a_arr['user'] : '';
			$arr_a[] = isset($link_a_arr['pass']) ? $link_a_arr['pass'] : '';
			$arr_a[] = isset($link_a_arr['host']) ? $link_a_arr['host'] : '';
			$arr_a[] = isset($link_a_arr['port']) ? $link_a_arr['port'] : '';
			$arr_a[] = isset($link_a_arr['path']) ? $link_a_arr['path'] : '';
			if(isset($link_a_arr['query'])){
				parse_str($link_a_arr['query'], $tmp);
				ksort($tmp);
				$arr_a[] = array_merge($arr_a, $tmp);
			}
			$arr_a[] = isset($link_a_arr['fragment']) ? $link_a_arr['fragment'] : '';

			$arr_b[] = isset($link_b_arr['scheme']) ? $link_a_arr['scheme'] : '';
			$arr_b[] = isset($link_b_arr['user']) ? $link_a_arr['user'] : '';
			$arr_b[] = isset($link_b_arr['pass']) ? $link_a_arr['pass'] : '';
			$arr_b[] = isset($link_b_arr['host']) ? $link_a_arr['host'] : '';
			$arr_b[] = isset($link_b_arr['port']) ? $link_a_arr['port'] : '';
			$arr_b[] = isset($link_b_arr['path']) ? $link_a_arr['path'] : '';
			if(isset($link_b_arr['query'])){
				parse_str($link_b_arr['query'], $tmp);
				ksort($tmp);
				$arr_b[] = array_merge($arr_b, $tmp);
			}
			$arr_b[] = isset($link_b_arr['fragment']) ? $link_b_arr['fragment'] : '';
		} else{
			parse_str($link_a_arr['query'], $arr_a);
			parse_str($link_b_arr['query'], $arr_b);
		}
		if(serialize($arr_a) === serialize($arr_b)){
			return true;
		} else{
			return false;
		}
	}
}
