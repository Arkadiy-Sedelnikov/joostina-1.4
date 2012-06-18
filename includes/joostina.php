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

//Europe/Moscow // GMT0
function_exists('date_default_timezone_set') ? date_default_timezone_set(date_default_timezone_get()) : null;

// каталог администратора
DEFINE('JADMIN_BASE', 'administrator');
// формат даты
DEFINE('_CURRENT_SERVER_TIME_FORMAT', '%Y-%m-%d %H:%M:%S');
// текущее время сервера
DEFINE('_CURRENT_SERVER_TIME', date('Y-m-d H:i:s', time()));
// схемы не http/https протоколов
DEFINE('_URL_SCHEMES', 'data:, file:, ftp:, gopher:, imap:, ldap:, mailto:, news:, nntp:, telnet:, javascript:, irc:, mms:');

//path to admin folder (experiment)
DEFINE("ADMINISTRATOR_DIRECTORY", 'administrator');

// языковые константы
DEFINE('_ISO2', 'utf-8');
DEFINE('_ISO', 'charset=UTF-8');

// пробуем устанавить более удобный режим работы
ini_set("magic_quotes_runtime", 0);

// установка режима отображения ошибок
if($mosConfig_error_reporting == 0){
	error_reporting(0);
} elseif($mosConfig_error_reporting != 0){
	error_reporting($mosConfig_error_reporting);
}
/* ядро отладчика */
mosMainFrame::addLib('debug');
/* ядро для работы с юникодом */
mosMainFrame::addLib('utf8');

/* файл данных версии */
require_once (JPATH_BASE . '/includes/version.php');
/* ядро работы с XML */
require_once (JPATH_BASE . '/includes/parameters.xml.php');
/* класс фильтрации данных */
mosMainFrame::addLib('inputfilter');
/* класс работы с базой данных */
mosMainFrame::addLib('database');

$database = database::getInstance();

/* класс работы с правами пользователей */
mosMainFrame::addLib('gacl');

$acl = gacl::getInstance();

if(isset($_SERVER['REQUEST_URI'])){
	$request_uri = $_SERVER['REQUEST_URI'];
} else{
	$request_uri = $_SERVER['SCRIPT_NAME'];
	if(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])){
		$request_uri .= '?' . $_SERVER['QUERY_STRING'];
	}
}
$_SERVER['REQUEST_URI'] = $request_uri;
unset($request_uri);

/**
 * Joostina! Mainframe class
 * Provide many supporting API functions
 * @package Joostina
 */
class mosMainFrame{

	/**
	@var database Internal database class pointer */
	var $_db = null;
	/**
	@var object An object of configuration variables */
	public $_config = null;
	public $config = null;
	/**
	@var object An object of path variables */
	public $_path = null;
	/**
	@var mosSession The current session */
	public $_session = null;
	/**
	@var string The current template */
	public $_template = null;
	/**
	@var array An array to hold global user state within a session */
	public $_userstate = null;
	/**
	@var array An array of page meta information */
	public $_head = null;
	/**
	@var string Custom html string to append to the pathway */
	public $_custom_pathway = null;
	/**
	@var boolean True if in the admin client */
	public $_isAdmin = false;
	/**
	 * флаг визуального редактора
	 */
	public $allow_wysiwyg = 0;
	/**
	@var массив данных выводящися в нижней части страницы */
	public $_footer = null;
	/**
	 * системное сообщение
	 */
	public $mosmsg = '';
	/**
	 * текущий язык
	 */
	public $lang = null;

	/**
	 * The Singleton instance of the class
	 * @var mosMainframe
	 */
	private static $_instance = null;

	private static $staticUser = null;

	/**
	 * Class constructor
	 * @param database A database connection object
	 * @param string The url option
	 * @param string The path of the mos directory
	 */
	function mosMainFrame($db, $option, $basePath = null, $isAdmin = false){
		unset($db, $option, $basePath);

		$this->config = Jconfig::getInstance();

		$this->_db = database::getInstance();

		if(!$isAdmin){
			$current = $this->get_option();
			$this->option = $option = $current['option'];
			unset($current);
		} else{ // для панели управления работаем с меню напрямую
			$option = strval(strtolower(mosGetParam($_REQUEST, 'option')));
		}

		$this->_setTemplate($isAdmin);
		$this->_setAdminPaths($option, JPATH_BASE);
		$this->_isAdmin = (boolean)$isAdmin;
		$this->set('now', date('Y-m-d H:i:s', time()));

		if(isset($_SESSION['session_userstate'])){
			$this->_userstate = &$_SESSION['session_userstate'];
		} else{
			$this->_userstate = null;
		}

		if(!$isAdmin){
			$this->getCfg('components_access') ? $this->check_option($option) : null;
			$this->_head = array();
			$this->_head['title'] = $this->getCfg('sitename');
			$this->_head['meta'] = array();
			$this->_head['custom'] = array();
			$this->loadOverlib = false;
			$this->detect();
		}
	}

	public static function getInstance($isAdmin = false){
		//jd_inc('mosMainFrame::getInstance()');

		if(!is_object(self::$_instance)){
			self::$_instance = new mosMainFrame(null, null, null, $isAdmin);
		}

		return self::$_instance;
	}

	/**
	 * Check if there are any instance of mosMainframe initialized
	 * @return bool
	 */
	public static function hasInstance(){
		$result = is_object(self::$_instance);
		return $result;
	}

	function adminView($target){
		global $option;

		$default = 'administrator' . DS . 'components' . DS . $option . DS . 'view' . DS . $target . '.php';
		$from_template = 'administrator' . DS . 'templates' . DS . JTEMPLATE . DS . 'html' . DS . $option . DS . $target . '.php';

		if(is_file($return = JPATH_BASE . DS . $from_template)){
			return $return;
		} elseif(is_file($return = JPATH_BASE . DS . $default)){
			return $return;
		} else{
			return false;
		}
	}

	/**
	 * Gets the id number for a client
	 * @param mixed A client identifier
	 */
	function getClientID($client){
		switch($client){
			case '2':
			case 'installation':
				return 2;
				break;
			case '1':
			case 'admin':
			case 'administrator':
				return 1;
				break;
			case '0':
			case 'site':
			case 'front':
			default:
				return 0;
				break;
		}
		return 0;
	}

	/**
	 * Gets the client name
	 * @param int The client identifier
	 * @return strint The text name of the client
	 */
	function getClientName($client_id){
		// do not translate
		$clients = array('site', 'admin', 'installer');
		return mosGetParam($clients, $client_id, 'unknown');
	}

	/**
	 * Gets the base path for the client
	 * @param mixed A client identifier
	 * @param boolean True (default) to add traling slash
	 */
	function getBasePath($client = 0, $addTrailingSlash = true){

		switch($client){
			case '0':
			case 'site':
			case 'front':
			default:
				return mosPathName(JPATH_BASE, $addTrailingSlash);
				break;

			case '2':
			case 'installation':
				return mosPathName(JPATH_BASE . DS . 'installation', $addTrailingSlash);
				break;

			case '1':
			case 'admin':
			case 'administrator':
				return mosPathName(JPATH_BASE . DS . JADMIN_BASE, $addTrailingSlash);
				break;
		}
	}

	// получение объекта базы данных
	public function getDBO(){
		return $this->_db;
	}

	/**
	 * Подключение библиотеки
	 * @param string $lib Название библиотеки. Может быть сформировано как: `lib_name`, `lib_name/lib_name.php`, `lib_name.php`
	 * @param string $dir Директория библиотеки. Необязательный параметр. По умолчанию, поиск файла осуществляется в 'includes/libraries'
	 */
	public static function addLib($lib, $dir = ''){
		$dir = (!$dir) ? 'includes/libraries' : $dir;

		$file_lib = JPATH_BASE . DS . $dir . DS . $lib . DS . $lib . '.php';
		if(is_file($file_lib)){
			require_once($file_lib);
		}
	}

	public static function getLangFile($name = '', $mosConfig_lang = ''){
		if(empty($mosConfig_lang)){
			global $mosConfig_lang;
		}

		$mainframe = mosMainFrame::getInstance();

		$lang = $mosConfig_lang;

		if(!$name){
			return JPATH_BASE . DS . 'language' . DS . $lang . DS . 'system.php';
		} else{
			$file = $name;
		}
		if($mainframe->isAdmin() == true){
			if(is_file(JPATH_BASE . DS . 'language' . DS . $lang . DS . 'administrator' . DS . $file . '.php')){
				return JPATH_BASE . DS . 'language' . DS . $lang . DS . 'administrator' . DS . $file . '.php';
			} else{
				if(is_file(JPATH_BASE . DS . 'language' . DS . $lang . DS . 'frontend' . DS . $file . '.php')){
					return JPATH_BASE . DS . 'language' . DS . $lang . DS . 'frontend' . DS . $file . '.php';
				}
			}
		} else{
			if(is_file(JPATH_BASE . DS . 'language' . DS . $lang . DS . 'frontend' . DS . $file . '.php')){
				return JPATH_BASE . DS . 'language' . DS . $lang . DS . 'frontend' . DS . $file . '.php';
			}
		}

		return null;
	}

	/**
	 * установка title страницы
	 */
	function setPageTitle($title = null, $pageparams = null){

		$sitename = $page_title = $this->getCfg('sitename');
		$config_tseparator = $this->getCfg('tseparator');

		if($this->getCfg('pagetitles')){
			$title = Jstring::trim(strip_tags($title));
			// разделитель названия страницы и сайта
			$tseparator = $config_tseparator ? $config_tseparator : ' - ';
			if($pageparams != null){
				// название страницы указанное в настройках пункта меню или свойствах содержимого
				$pageownname = Jstring::trim(htmlspecialchars($pageparams->get('page_name')));
				$page_title = $pageparams->get('no_site_name') ? ($pageownname ? $pageownname : ($title ? $title : $sitename)) : ($this->getCfg('pagetitles_first') ? (($pageownname ? $pageownname : $title) . $tseparator . $sitename) : ($this->getCfg('sitename') . $tseparator . ($pageownname ? $pageownname : $title)));
			} elseif($this->getCfg('pagetitles_first') == 1){
				$pageownname = null;
				$page_title = $title ? $title . $tseparator . $sitename : $sitename;
			} else{
				$pageownname = null;
				$page_title = $title ? $sitename . $tseparator . $title : $sitename;
			}
		}

		// название страницы, не title!
		$this->_head['pagename'] = isset($pageownname) ? $pageownname : $title;

		switch($this->getCfg('pagetitles_first')){
			case '0':
			case '1':
			default:
				$this->_head['title'] = $page_title;
				break;
			case '2':
				$this->_head['title'] = $sitename;
				break;
			case '3':
				$this->_head['title'] = $pageownname ? $pageownname : $title;
				break;
		}
	}

	/**
	 * @param string The value of the name attibute
	 * @param string The value of the content attibute
	 * @param string Text to display before the tag
	 * @param string Text to display after the tag
	 */
	function addMetaTag($name, $content, $prepend = '', $append = ''){
		$name = Jstring::trim(htmlspecialchars($name));
		$content = Jstring::trim(htmlspecialchars($content));
		$prepend = Jstring::trim($prepend);
		$append = Jstring::trim($append);
		$this->_head['meta'][] = array($name, $content, $prepend, $append);
	}

	/**
	 * @param string The value of the name attibute
	 * @param string The value of the content attibute to append to the existing
	 * Tags ordered in with Site Keywords and Description first
	 */
	function appendMetaTag($name, $content){
		$name = Jstring::trim(htmlspecialchars($name));
		$n = count($this->_head['meta']);
		for($i = 0; $i < $n; $i++){
			if($this->_head['meta'][$i][0] == $name){
				$content = Jstring::trim(htmlspecialchars($content));
				if($content != '' & $this->_head['meta'][$i][1] == ""){
					$this->_head['meta'][$i][1] .= ' ' . $content;
				}
				;
				return;
			}
		}

		$this->addMetaTag($name, $content);
	}

	/**
	 * @param string The value of the name attibute
	 * @param string The value of the content attibute to append to the existing
	 */
	function prependMetaTag($name, $content){
		$name = trim(htmlspecialchars($name));
		$n = count($this->_head['meta']);
		for($i = 0; $i < $n; $i++){
			if($this->_head['meta'][$i][0] == $name){
				$content = trim(htmlspecialchars($content));
				$this->_head['meta'][$i][1] = $content . $this->_head['meta'][$i][1];
				return;
			}
		}
		$this->addMetaTag($name, $content);
	}

	// расширенные мета-тэги для улучшенного SEO
	function set_robot_metatag($robots){

		if($robots == 0){
			$this->addMetaTag('robots', 'index, follow');
		}
		if($robots == 1){
			$this->addMetaTag('robots', 'index, nofollow');
		}
		if($robots == 2){
			$this->addMetaTag('robots', 'noindex, follow');
		}
		if($robots == 3){
			$this->addMetaTag('robots', 'noindex, nofollow');
		}
	}

	/**
	 * Adds a custom html string to the head block
	 * @param string The html to add to the head
	 */
	function addCustomHeadTag($html){
		$this->_head['custom'][] = trim($html);
	}

	/**
	 * Adds a custom html string to the footer block
	 * @param string The html to add to the footer
	 */
	function addCustomFooterTag($html){
		$this->_footer['custom'][] = trim($html);
	}

	/**
	 * @return string
	 */
	function getHead($params = array('js' => 1, 'css' => 1, 'jquery' => 0)){
		$head = array();
		$head[] = '<title>' . $this->_head['title'] . '</title>';

		foreach($this->_head['meta'] as $meta){
			if($meta[2]){
				$head[] = $meta[2];
			}
			$head[] = '<meta name="' . $meta[0] . '" content="' . $meta[1] . '" />';
			if($meta[3]){
				$head[] = $meta[3];
			}
		}

		if(isset($params['jquery']) && $params['jquery'] == 1){
			$head[] = mosCommonHTML::loadJquery(true, true);
		}


		if(isset($params['js']) && $params['js'] == 1 && isset($this->_head['js'])){
			foreach($this->_head['js'] as $html){
				$head[] = $html;
			}
		}

		foreach($this->_head['custom'] as $html){
			$head[] = $html;
		}

		if(isset($params['css']) && $params['css'] == 1 && isset($this->_head['css'])){
			foreach($this->_head['css'] as $html){
				$head[] = $html;
			}
		}
		//unset($this->_head);
		return implode("\n", $head) . "\n";
	}

	public function getHeadData($name){
		return isset($this->_head[$name]) ? $this->_head[$name] : array();
	}

	function getFooter($params = array('fromheader' => 1, 'custom' => 0, 'js' => 1, 'css' => 1)){
		$footer = array();

		if(isset($params['fromheader']) && $params['fromheader'] == 1){
			$this->_footer = $this->_head;
		}

		if(isset($params['custom']) && $params['custom'] == 1 && isset($this->_footer['custom'])){
			foreach($this->_footer['custom'] as $html){
				$footer[] = $html;
			}
		}

		if(isset($params['jquery']) && $params['jquery'] == 1){
			$footer[] = mosCommonHTML::loadJquery(true, true);
		}

		if(isset($params['js']) && $params['js'] == 1 && isset($this->_footer['js'])){
			foreach($this->_footer['js'] as $html){
				$footer[] = $html;
			}
		}

		if(isset($params['css']) && $params['css'] == 1 && isset($this->_footer['css'])){
			foreach($this->_footer['css'] as $html){
				$footer[] = $html;
			}
		}
		//unset($this->_footer);
		return implode("\n", $footer) . "\n";
	}

	/**
	 * добавление js файлов в шапку или футер страницы
	 * если $footer - скрипт будет добавлен в $mainframe->_footer
	 * возможные значения $footer:
	 *     'js' - скрипт будет добавлен в $mainfrane->_footer['js'] (первый этап вывода футера)
	 *     'custom' - скрипт будет добавлен в $mainfrane->_footer['custom'] (второй этап вывода футера)
	 */
	function addJS($path, $footer = '', &$def = ''){
		if($footer){
			$this->_footer[$footer][] = '<script language="JavaScript" src="' . $path . '" type="text/javascript"></script>';
		} else{
			$this->_head['js'][] = '<script language="JavaScript" src="' . $path . '" type="text/javascript"></script>';
		}
	}

	/**
	 * добавление css файлов в шапку страницы
	 */
	function addCSS($path){
		$this->_head['css'][] = '<link type="text/css" rel="stylesheet" href="' . $path . '" />';
	}

	/**
	 * @return string
	 */
	function getPageTitle(){
		return $this->_head['title'];
	}

	/**
	 * @return string
	 */
	function getCustomPathWay(){
		return $this->_custom_pathway;
	}

	function appendPathWay($html){
		$this->_custom_pathway[] = $html;
	}

	/**
	 * Gets the value of a user state variable
	 * @param string The name of the variable
	 */
	function getUserState($var_name){
		if(is_array($this->_userstate)){
			return mosGetParam($this->_userstate, $var_name, null);
		} else{
			return null;
		}
	}

	/**
	 * Gets the value of a user state variable
	 * @param string The name of the user state variable
	 * @param string The name of the variable passed in a request
	 * @param string The default value for the variable if not found
	 */
	function getUserStateFromRequest($var_name, $req_name, $var_default = null){
		if(is_array($this->_userstate)){
			if(isset($_REQUEST[$req_name])){
				$this->setUserState($var_name, $_REQUEST[$req_name]);
			} else if(!isset($this->_userstate[$var_name])){
				$this->setUserState($var_name, $var_default);
			}

			// filter input
			$iFilter = new InputFilter();
			$this->_userstate[$var_name] = $iFilter->process($this->_userstate[$var_name]);
			return $this->_userstate[$var_name];
		} else{
			return null;
		}
	}

	/**
	 * Sets the value of a user state variable
	 * @param string The name of the variable
	 * @param string The value of the variable
	 */
	function setUserState($var_name, $var_value){
		if(is_array($this->_userstate)){
			$this->_userstate[$var_name] = $var_value;
		}
	}

	/**
	 * Initialises the user session
	 * Old sessions are flushed based on the configuration value for the cookie
	 * lifetime. If an existing session, then the last access time is updated.
	 * If a new session, a session id is generated and a record is created in
	 * the jos_sessions table.
	 */
	function initSession(){
		if($this->getCfg('no_session_front'))
			return;

		// initailize session variables
		$session = &$this->_session;
		$session = new mosSession($this->_db);
		// purge expired sessions
		(rand(0, 2) == 1) ? $session->purge('core', '', $this->config->config_lifetime) : null;

		// Session Cookie `name`
		$sessionCookieName = mosMainFrame::sessionCookieName();


		// Get Session Cookie `value`
		$sessioncookie = strval(mosGetParam($_COOKIE, $sessionCookieName, null));
		// Session ID / `value`
		$sessionValueCheck = mosMainFrame::sessionCookieValue($sessioncookie);

		// Check if existing session exists in db corresponding to Session cookie `value`
		// extra check added in 1.0.8 to test sessioncookie value is of correct length
		if($sessioncookie && strlen($sessioncookie) == 32 && $sessioncookie != '-' && $session->load($sessionValueCheck)){
			// update time in session table
			$session->time = time();
			$session->update();
		} else{
			// Remember Me Cookie `name`
			$remCookieName = mosMainFrame::remCookieName_User();

			// test if cookie found
			$cookie_found = false;
			if(isset($_COOKIE[$sessionCookieName]) || isset($_COOKIE[$remCookieName]) || isset($_POST['force_session'])){
				$cookie_found = true;
			}

			// check if neither remembermecookie or sessioncookie found
			if(!$cookie_found){
				// create sessioncookie and set it to a test value set to expire on session end
				setcookie($sessionCookieName, '-', false, '/');
			} else{
				// otherwise, sessioncookie was found, but set to test val or the session expired, prepare for session registration and register the session
				$url = strval(mosGetParam($_SERVER, 'REQUEST_URI', null));
				// stop sessions being created for requests to syndicated feeds
				if(strpos($url, 'option=com_rss') === false && strpos($url, 'feed=') === false){
					$session->guest = 1;
					$session->username = '';
					$session->time = time();
					$session->gid = 0;
					// Generate Session Cookie `value`
					$session->generateId();
					if(!$session->insert()){
						die($session->getError());
					}
					// create Session Tracking Cookie set to expire on session end
					setcookie($sessionCookieName, $session->getCookie(), false, '/');
				}
			}
			// Cookie used by Remember me functionality
			$remCookieValue = strval(mosGetParam($_COOKIE, $remCookieName, null));

			// test if cookie is correct length
			if(strlen($remCookieValue) > 64){
				// Separate Values from Remember Me Cookie
				$remUser = substr($remCookieValue, 0, 32);
				$remPass = substr($remCookieValue, 32, 32);
				$remID = intval(substr($remCookieValue, 64));

				// check if Remember me cookie exists. Login with usercookie info.
				if(strlen($remUser) == 32 && strlen($remPass) == 32){
					$this->login($remUser, $remPass, 1, $remID);
				}
			}
		}
	}

	/*
	 * Function used to conduct admin session duties
	 * Added as of 1.0.8
	 * Deperciated 1.1
	 */

	function initSessionAdmin($option, $task){

		if(!empty(self::$staticUser)){
			return self::$staticUser;
		}

		$_config = $this->get('config');

		// logout check
		if($option == 'logout'){
			require JPATH_BASE_ADMIN . DS . 'logout.php';
			exit();
		}

		// check if session name corresponds to correct format
		if(session_name() != md5(JPATH_SITE)){
			echo "<script>document.location.href='index.php'</script>\n";
			exit();
		}

		// restore some session variables
		$my = new mosUser($this->_db);
		$my->id = intval(mosGetParam($_SESSION, 'session_user_id', ''));
		$my->username = strval(mosGetParam($_SESSION, 'session_USER', ''));
		$my->usertype = strval(mosGetParam($_SESSION, 'session_usertype', ''));
		$my->gid = intval(mosGetParam($_SESSION, 'session_gid', ''));
		$my->params = mosGetParam($_SESSION, 'session_user_params', '');
		$my->bad_auth_count = mosGetParam($_SESSION, 'session_bad_auth_count', '');

		$session_id = mosGetParam($_SESSION, 'session_id', '');
		$logintime = mosGetParam($_SESSION, 'session_logintime', '');

		if($session_id != session_id()){
			// session id does not correspond to required session format
			mosRedirect(JPATH_SITE . '/' . JADMIN_BASE . '/', _YOU_NEED_TO_AUTH);
			exit();
		}

		// check to see if session id corresponds with correct format
		if($session_id == md5($my->id . $my->username . $my->usertype . $logintime)){
			// if task action is to `save` or `apply` complete action before doing session checks.
			if($task != 'save' && $task != 'apply'){
				// test for session_life_admin
				if($_config->config_session_life_admin){
					$session_life_admin = $_config->config_session_life_admin;
				} else{
					$session_life_admin = 1800;
				}

				// если в настройка не указано что сессии админки не уничтожаются - выполняем запрос по очистке сессий
				if($_config->config_admin_autologout == 1){
					// purge expired admin sessions only
					$past = time() - $session_life_admin;
					$query = "DELETE FROM #__session WHERE time < '" . (int)$past . "' AND guest = 1 AND gid = 0 AND userid <> 0";
					$this->_db->setQuery($query)->query();
				}

				$current_time = time();

				// update session timestamp
				$query = "UPDATE #__session SET time = " . $this->_db->Quote($current_time) . " WHERE session_id = " . $this->_db->Quote($session_id);
				$this->_db->setQuery($query);
				$_config->config_admin_autologout == 1 ? $this->_db->query() : null;

				// set garbage cleaning timeout
				$this->setSessionGarbageClean();

				// check against db record of session
				$query = "SELECT COUNT( session_id ) FROM #__session WHERE session_id = " . $this->_db->Quote($session_id) . " AND username = " . $this->_db->Quote($my->username) . "\n AND userid = " . intval($my->id);
				$this->_db->setQuery($query);
				$count = ($_config->config_admin_autologout == 1) ? $this->_db->loadResult() : 1;

				// если в таблице
				if($count == 0){
					$link = null;
					if($_SERVER['QUERY_STRING']){
						$link = 'index2.php?' . $_SERVER['QUERY_STRING'];
					}

					// check if site designated as a production site
					// for a demo site disallow expired page functionality
					// link must also be a Joomla link to stop malicious redirection
					if($link && strpos($link, 'index2.php?option=com_') === 0 && joomlaVersion::get('SITE') == 1){
						$now = time();

						$file = $this->getPath('com_xml', 'com_users');
						$params = new mosParameters($my->params, $file, 'component');

						// return to expired page functionality
						$params->set('expired', $link);
						$params->set('expired_time', $now);

						// param handling
						if(is_array($params->toArray())){
							$txt = array();
							foreach($params->toArray() as $k => $v){
								$txt[] = "$k=$v";
							}
							$saveparams = implode("\n", $txt);
						}

						$query = "UPDATE #__users SET params = " . $this->_db->Quote($saveparams) . " WHERE id = " . (int)$my->id . " AND username = " . $this->_db->Quote($my->username) . " AND usertype = " . $this->_db->Quote($my->usertype);
						$this->_db->setQuery($query)->query();
					}

					mosRedirect(JPATH_SITE . '/' . JADMIN_BASE . '/', _ADMIN_SESSION_ENDED);
				} else{
					// load variables into session, used to help secure /popups/ functionality
					$_SESSION['option'] = $option;
					$_SESSION['task'] = $task;
				}
			}
		} else if($session_id == ''){
			// no session_id as user has not attempted to login, or session.auto_start is switched on
			if(ini_get('session.auto_start') || !ini_get('session.use_cookies')){
				echo "<script>document.location.href='index.php?mosmsg=" . _YOU_NEED_TO_AUTH_AND_FIX_PHP_INI . "'</script>\n";
			} else{
				echo "<script>document.location.href='index.php?mosmsg=" . _YOU_NEED_TO_AUTH . "'</script>\n";
			}
			exit();
		} else{
			// session id does not correspond to required session format
			echo "<script>document.location.href='index.php?mosmsg=" . _WRONG_USER_SESSION . "'</script>\n";
			exit();
		}

		self::$staticUser = $my;
		return $my;
	}

	/*
	 * Function used to set Session Garbage Cleaning
	 * garbage cleaning set at configured session time + 600 seconds
	 * Added as of 1.0.8
	 * Deperciated 1.1
	 */

	function setSessionGarbageClean(){
		/** ensure that funciton is only called once */
		if(!defined('_JOS_GARBAGECLEAN')){
			define('_JOS_GARBAGECLEAN', 1);

			$garbage_timeout = $this->getCfg('session_life_admin') + 600;
			@ini_set('session.gc_maxlifetime', $garbage_timeout);
		}
	}

	/*
	 * Static Function used to generate the Session Cookie Name
	 * Added as of 1.0.8
	 * Deperciated 1.1
	 */

	public static function sessionCookieName($site_name = ''){

		if(!$site_name){
			$site_name = JPATH_SITE;
		}

		if(substr($site_name, 0, 7) == 'http://'){
			$hash = md5('site' . substr($site_name, 7));
		} elseif(substr($site_name, 0, 8) == 'https://'){
			$hash = md5('site' . substr($site_name, 8));
		} else{
			$hash = md5('site' . $site_name);
		}

		return $hash;
	}

	/*
	 * Static Function used to generate the Session Cookie Value
	 * Added as of 1.0.8
	 * Deperciated 1.1
	 */

	public static function sessionCookieValue($id = null){
		$config = Jconfig::getInstance();
		$type = $config->config_session_type;
		$browser = @$_SERVER['HTTP_USER_AGENT'];

		switch($type){
			case 2:
				// 1.0.0 to 1.0.7 Compatibility
				// lowest level security
				$value = md5($id . $_SERVER['REMOTE_ADDR']);
				break;

			case 1:
				// slightly reduced security - 3rd level IP authentication for those behind IP Proxy
				$remote_addr = explode('.', $_SERVER['REMOTE_ADDR']);
				$ip = $remote_addr[0] . '.' . $remote_addr[1] . '.' . $remote_addr[2];
				$value = mosHash($id . $ip . $browser);
				break;

			default:
				// Highest security level - new default for 1.0.8 and beyond
				$ip = $_SERVER['REMOTE_ADDR'];
				$value = mosHash($id . $ip . $browser);
				break;
		}

		return $value;
	}

	/*
	 * Static Function used to generate the Rememeber Me Cookie Name for Username information
	 * Added as of 1.0.8
	 * Depreciated 1.1
	 */

	function remCookieName_User(){
		$value = mosHash('remembermecookieusername' . mosMainFrame::sessionCookieName());

		return $value;
	}

	/*
	 * Static Function used to generate the Rememeber Me Cookie Name for Password information
	 * Added as of 1.0.8
	 * Depreciated 1.1
	 */

	function remCookieName_Pass(){
		$value = mosHash('remembermecookiepassword' . mosMainFrame::sessionCookieName());

		return $value;
	}

	/*
	 * Static Function used to generate the Remember Me Cookie Value for Username information
	 * Added as of 1.0.8
	 * Depreciated 1.1
	 */

	function remCookieValue_User($username){
		$value = md5($username . mosHash(@$_SERVER['HTTP_USER_AGENT']));

		return $value;
	}

	/*
	 * Static Function used to generate the Remember Me Cookie Value for Password information
	 * Added as of 1.0.8
	 * Depreciated 1.1
	 */

	function remCookieValue_Pass($passwd){
		$value = md5($passwd . mosHash(@$_SERVER['HTTP_USER_AGENT']));

		return $value;
	}

	/**
	 * Login validation function
	 * Username and encoded password is compare to db entries in the jos_users
	 * table. A successful validation updates the current session record with
	 * the users details.
	 */
	function login($username = null, $passwd = null, $remember = 0, $userid = null){

		// если сесии на фронте отключены - прекращаем выполнение процедуры
		if($this->getCfg('no_session_front'))
			return;

		$acl = &gacl::getInstance();

		$bypost = 0;
		$valid_remember = false;

		// if no username and password passed from function, then function is being called from login module/component
		if(!$username || !$passwd){
			$username = stripslashes(strval(mosGetParam($_POST, 'username', '')));
			$passwd = stripslashes(strval(mosGetParam($_POST, 'passwd', '')));

			$bypost = 1;

			// extra check to ensure that Joomla! sessioncookie exists
			if(!$this->_session->session_id){
				mosErrorAlert(_ALERT_ENABLED);
				return;
			}

			josSpoofCheck(null, 1);
		}

		$row = null;
		if(!$username || !$passwd){
			mosErrorAlert(_LOGIN_INCOMPLETE);
			exit();
		} else{
			if($remember && strlen($username) == 32 && $userid){

				// query used for remember me cookie
				$harden = mosHash(@$_SERVER['HTTP_USER_AGENT']);

				$query = "SELECT id, name, username, password, usertype, block, gid FROM #__users WHERE id = " . (int)$userid;
				$user = null;

				$this->_db->setQuery($query)->loadObject($user);

				list($hash, $salt) = explode(':', $user->password);

				$check_USER = md5($user->username . $harden);
				$check_password = md5($hash . $harden);

				if($check_USER == $username && $check_password == $passwd){
					$row = $user;
					$valid_remember = true;
				}
			} else{
				// query used for login via login module
				$query = "SELECT id, name, username, password, usertype, block, gid FROM #__users WHERE username = " . $this->_db->Quote($username);

				$this->_db->setQuery($query)->loadObject($row);
			}

			if(is_object($row)){
				// user blocked from login
				if($row->block == 1){
					mosErrorAlert(_LOGIN_BLOCKED);
				}

				if(!$valid_remember){
					// Conversion to new type
					if((strpos($row->password, ':') === false) && $row->password == md5($passwd)){
						// Old password hash storage but authentic ... lets convert it
						$salt = mosMakePassword(16);
						$crypt = md5($passwd . $salt);
						$row->password = $crypt . ':' . $salt;

						// Now lets store it in the database
						$query = 'UPDATE #__users SET password = ' . $this->_db->Quote($row->password) . ' WHERE id = ' . (int)$row->id;

						if(!$this->_db->setQuery($query)->query()){
							echo 'error';
						}
					}
					list($hash, $salt) = explode(':', $row->password);
					$cryptpass = md5($passwd . $salt);

					if($hash != $cryptpass){
						if($bypost){
							mosErrorAlert(_LOGIN_INCORRECT);
						} else{
							$this->logout();
							mosRedirect('index.php');
						}
						exit();
					}
				}

				// fudge the group stuff
				$grp = $acl->getAroGroup($row->id);
				$row->gid = 1;
				if($acl->is_group_child_of($grp->name, 'Registered', 'ARO') || $acl->is_group_child_of($grp->name, 'Public Backend', 'ARO')){
					// fudge Authors, Editors, Publishers and Super Administrators into the Special Group
					$row->gid = 2;
				}
				$row->usertype = $grp->name;

				// initialize session data
				$session = &$this->_session;
				$session->guest = 0;
				$session->username = $row->username;
				$session->userid = intval($row->id);
				$session->usertype = $row->usertype;
				$session->gid = intval($row->gid);
				$session->update();

				// check to see if site is a production site
				// allows multiple logins with same user for a demo site
				if(joomlaVersion::get('SITE')){
					// delete any old front sessions to stop duplicate sessions
					$query = "DELETE FROM #__session WHERE session_id != " . $this->_db->Quote($session->session_id) . " AND username = " . $this->_db->Quote($row->username) . " AND userid = " . (int)$row->id . " AND gid = " . (int)$row->gid . " AND guest = 0";
					$this->_db->setQuery($query)->query();
				}

				// update user visit data
				$currentDate = date("Y-m-d H:i:s");

				$query = "UPDATE #__users SET lastvisitDate = " . $this->_db->Quote($currentDate) . " WHERE id = " . (int)$session->userid;

				if(!$this->_db->setQuery($query)->query()){
					die($this->_db->stderr(true));
				}

				// set remember me cookie if selected
				$remember = strval(mosGetParam($_POST, 'remember', ''));
				if($remember == 'yes'){
					// cookie lifetime of 365 days
					$lifetime = time() + 365 * 24 * 60 * 60;
					$remCookieName = mosMainFrame::remCookieName_User();
					$remCookieValue = mosMainFrame::remCookieValue_User($row->username) . mosMainFrame::remCookieValue_Pass($hash) . $row->id;
					setcookie($remCookieName, $remCookieValue, $lifetime, '/');
				}
				// а зачем чистить кэш после каждой авторизации?
				//mosCache::cleanCache();
			} else{
				if($bypost){
					mosErrorAlert(_LOGIN_INCORRECT);
				} else{
					$this->logout();
					mosRedirect('index.php');
				}
				exit();
			}
		}
	}

	/**
	 * User logout
	 * Reverts the current session record back to 'anonymous' parameters
	 */
	function logout(){
		$session = &$this->_session;
		$session->guest = 1;
		$session->username = '';
		$session->userid = '';
		$session->usertype = '';
		$session->gid = 0;
		$session->update();
		// kill remember me cookie
		$lifetime = time() - 86400;
		$remCookieName = mosMainFrame::remCookieName_User();
		setcookie($remCookieName, ' ', $lifetime, '/');
		@session_destroy();
	}

	/**
	 * @return mosUser A user object with the information from the current session
	 * + хак для отключения ведения сессий на фронте
	 */
	function getUser(){

		if(!empty(self::$staticUser)){
			return self::$staticUser;
		}

		if(defined('IS_ADMIN')){
			$mainframe = mosMainFrame::getInstance(true);
			$option = strval(strtolower(mosGetParam($_REQUEST, 'option', '')));
			$task = strval(mosGetParam($_REQUEST, 'task', ''));
			$my = $mainframe->initSessionAdmin($option, $task);
			return $my;
		}

		$user = new mosUser($this->_db);

		if($this->get('config')->config_no_session_front == 1){
			// параметры id и gid при инициализации объявляются как null - это вредит некоторым компонентам, проинициализируем их в нули
			$user->id = 0;
			$user->gid = 0;
			$user->groop_id = 0;
			return $user; // если сессии (авторизация) на фронте отключены - возвращаем пустой объект
		} else if(empty($this->_session)){
			$mainframe = mosMainFrame::getInstance();
			$mainframe->initSession();
		}
		$user->id = intval($this->_session->userid);
		$user->username = $this->_session->username;
		$user->usertype = $this->_session->usertype;
		$user->gid = intval($this->_session->gid);
		if($user->id){
			$my = null;
			$query = "SELECT id, name, email, avatar, block, sendEmail, registerDate, lastvisitDate, activation, params, gid FROM #__users WHERE id = " . (int)$user->id;
			$this->_db->setQuery($query, 0, 1)->loadObject($my);

			$user->params = $my->params;
			$user->name = $my->name;
			$user->email = $my->email;
			$user->avatar = $my->avatar;
			$user->block = $my->block;
			$user->sendEmail = $my->sendEmail;
			$user->registerDate = $my->registerDate;
			$user->lastvisitDate = $my->lastvisitDate;
			$user->activation = $my->activation;
			$user->groop_id = $my->gid;
		}
		/* чистка памяти */
		unset($user->_db);
		self::$staticUser = $user;
		return $user;
	}

	function getUser_from_sess($sess_id){
		$mainframe = mosMainFrame::getInstance();
		$sess_id = $mainframe->sessionCookieValue($sess_id);


		$database = database::getInstance();
		$user = new mosUser($database);
		$user->id = 0;
		$user->gid = 0;

		$row = null;

		if($mainframe->_session && $mainframe->_session->userid){
			$row = $mainframe->_session;
		} else{
			$sql = "SELECT * FROM #__session WHERE session_id = '" . $sess_id . "' AND guest = 0";
			$database->setQuery($sql)->loadObject($row);
		}

		if($row && $row->userid){
			$user->id = $row->userid;

			$query = "SELECT id, name, username, usertype, email, avatar, block, sendEmail, registerDate, lastvisitDate, activation, params
			FROM #__users WHERE id = " . (int)$user->id;
			$my = null;
			$database->setQuery($query, 0, 1)->loadObject($my);

			$user->params = $my->params;
			$user->name = $my->name;
			$user->username = $my->username;
			$user->email = $my->email;
			$user->avatar = $my->avatar;
			$user->block = $my->block;
			$user->sendEmail = $my->sendEmail;
			$user->registerDate = $my->registerDate;
			$user->lastvisitDate = $my->lastvisitDate;
			$user->activation = $my->activation;
			$user->usertype = $my->usertype;
			//$user->gid = $row->gid;
		}
		/* чистка памяти */
		unset($user->_db);
		return $user;
	}

	/**
	 * @param string The name of the variable (from configuration.php)
	 * @return mixed The value of the configuration variable or null if not found
	 */
	function getCfg($varname){
		$varname = 'config_' . $varname;

		$config = $this->get('config');
		$varname_saved = (isset($config->$varname)) ? $config->$varname : null;

		return $varname_saved;
	}

	/**  функция определения шаблона, если в панели управления указано что использовать один шаблон - сразу возвращаем его название, функцию не проводим до конца */
	function _setTemplate($isAdmin = false){

		// если у нас в настройках указан шаблон и определение идёт не для панели управления - возвращаем название шаблона из глобальной конфигурации
		if(!$isAdmin and $this->getCfg('one_template') != '...'){
			$this->_template = $this->getCfg('one_template');
			return;
		}

		if($isAdmin){
			if($this->getCfg('admin_template') == '...'){
				$query = 'SELECT template FROM #__templates_menu WHERE client_id = 1 AND menuid = 0';
				$cur_template = $this->_db->setQuery($query)->loadResult();
				$path = JPATH_BASE . DS . JADMIN_BASE . DS . 'templates' . DS . $cur_template . DS . 'index.php';
				if(!is_file($path)){
					$cur_template = 'joostfree';
				}
			} else{
				$cur_template = 'joostfree';
			}
		} else{

			$query = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0 ORDER BY menuid DESC";
			$cur_template = $this->_db->setQuery($query, 0, 1)->loadResult();

			// TemplateChooser Start
			$jos_user_template = strval(mosGetParam($_COOKIE, 'jos_user_template', ''));
			$jos_change_template = strval(mosGetParam($_REQUEST, 'jos_change_template', $jos_user_template));
			if($jos_change_template){
				// clean template name
				$jos_change_template = preg_replace('#\W#', '', $jos_change_template);
				if(strlen($jos_change_template) >= 40){
					$jos_change_template = substr($jos_change_template, 0, 39);
				}

				// check that template exists in case it was deleted
				if(file_exists(JPATH_BASE . DS . 'templates' . DS . $jos_change_template . DS . 'index.php')){
					$lifetime = 60 * 10;
					$cur_template = $jos_change_template;
					setcookie('jos_user_template', $jos_change_template, time() + $lifetime);
				} else{
					setcookie('jos_user_template', '', time() - 3600);
				}
			}
		}

		$this->_template = $cur_template;
	}

	function getTemplate(){
		return $this->_template;
	}

	/**
	 * Determines the paths for including engine and menu files
	 * @param string The current option used in the url
	 * @param string The base path from which to load the configuration file
	 */
	function _setAdminPaths($option, $basePath = '.'){
		$option = strtolower($option);

		$this->_path = new stdClass();

		// security check to disable use of `/`, `\\` and `:` in $options variable
		if(strpos($option, '/') !== false || strpos($option, '\\') !== false || strpos($option, ':') !== false){
			mosErrorAlert(_ACCESS_DENIED);
			return;
		}

		$prefix = substr($option, 0, 4);
		if($prefix != 'com_' && $prefix != 'mod_'){
			// ensure backward compatibility with existing links
			$name = $option;
			$option = 'com_' . $option;
		} else{
			$name = substr($option, 4);
		}

		// components
		if(file_exists("$basePath/templates/$this->_template/components/$name.html.php")){
			$this->_path->front = "$basePath/components/$option/$name.php";
			$this->_path->front_html = "$basePath/templates/$this->_template/components/$name.html.php";
		} else if(file_exists("$basePath/components/$option/$name.php")){
			$this->_path->front = "$basePath/components/$option/$name.php";
			$this->_path->front_html = "$basePath/components/$option/$name.html.php";
		}

		$this->_path->config = "$basePath/components/$option/$name.config.php";

		if(file_exists("$basePath/" . JADMIN_BASE . "/components/$option/admin.$name.php")){
			$this->_path->admin = "$basePath/" . JADMIN_BASE . "/components/$option/admin.$name.php";
			$this->_path->admin_html = "$basePath/" . JADMIN_BASE . "/components/$option/admin.$name.html.php";
		}

		if(file_exists("$basePath/administrator/components/$option/toolbar.$name.php")){
			$this->_path->toolbar = "$basePath/" . JADMIN_BASE . "/components/$option/toolbar.$name.php";
			$this->_path->toolbar_html = "$basePath/" . JADMIN_BASE . "/components/$option/toolbar.$name.html.php";
			$this->_path->toolbar_default = "$basePath/" . JADMIN_BASE . "/includes/toolbar.html.php";
		}

		if(file_exists("$basePath/components/$option/$name.class.php")){
			$this->_path->class = "$basePath/components/$option/$name.class.php";
		} else if(file_exists("$basePath/" . JADMIN_BASE . "/components/$option/$name.class.php")){
			$this->_path->class = "$basePath/" . JADMIN_BASE . "/components/$option/$name.class.php";
		} else if(file_exists("$basePath/includes/$name.php")){
			$this->_path->class = "$basePath/includes/$name.php";
		}

		if($prefix == 'mod_' && file_exists("$basePath/" . JADMIN_BASE . "/modules/$option.php")){
			$this->_path->admin = "$basePath/" . JADMIN_BASE . "/modules/$option.php";
			$this->_path->admin_html = "$basePath/" . JADMIN_BASE . "/modules/mod_$name.html.php";
		} else if(file_exists("$basePath/" . JADMIN_BASE . "/components/$option/admin.$name.php")){
			$this->_path->admin = "$basePath/" . JADMIN_BASE . "/components/$option/admin.$name.php";
			$this->_path->admin_html = "$basePath/" . JADMIN_BASE . "/components/$option/admin.$name.html.php";
		} else{
			$this->_path->admin = "$basePath/" . JADMIN_BASE . "/components/com_admin/admin.admin.php";
			$this->_path->admin_html = "$basePath/" . JADMIN_BASE . "/components/com_admin/admin.admin.html.php";
		}
	}

	/**
	 * Returns a stored path variable

	 */
	function getPath($varname, $option = ''){

		if($option){
			$temp = $this->_path;
			$this->_setAdminPaths($option, JPATH_BASE);
		}
		$result = null;
		if(isset($this->_path->$varname)){
			$result = $this->_path->$varname;
		} else{
			switch($varname){
				case 'com_xml':
					$name = substr($option, 4);
					$path = JPATH_BASE . DS . JADMIN_BASE . "/components/$option/$name.xml";
					if(file_exists($path)){
						$result = $path;
					} else{
						$path = JPATH_BASE . "/components/$option/$name.xml";
						if(file_exists($path)){
							$result = $path;
						}
					}
					break;

				case 'mod0_xml':
					// Site modules
					if($option == ''){
						$path = JPATH_BASE . '/modules/custom.xml';
					} else{
						$path = JPATH_BASE . "/modules/$option.xml";
					}
					if(file_exists($path)){
						$result = $path;
					}
					break;

				case 'mod1_xml':
					// admin modules
					if($option == ''){
						$path = JPATH_BASE . DS . JADMIN_BASE . '/modules/custom.xml';
					} else{
						$path = JPATH_BASE . DS . JADMIN_BASE . "/modules/$option.xml";
					}
					if(file_exists($path)){
						$result = $path;
					}
					break;

				case 'bot_xml':
					// Site mambots
					$path = JPATH_BASE . DS . 'mambots' . DS . $option . '.xml';
					if(file_exists($path)){
						$result = $path;
					}
					break;

				case 'menu_xml':
					$path = JPATH_BASE . DS . JADMIN_BASE . "/components/com_menus/$option/$option.xml";
					if(file_exists($path)){
						$result = $path;
					}
					break;

				case 'installer_html':
					$path = JPATH_BASE . DS . JADMIN_BASE . "/components/com_installer/$option/$option.html.php";
					if(file_exists($path)){
						$result = $path;
					}
					break;

				case 'installer_class':
					$path = JPATH_BASE . DS . JADMIN_BASE . "/components/com_installer/$option/$option.class.php";
					if(file_exists($path)){
						$result = $path;
					}
					break;
			}
		}
		if($option){
			$this->_path = $temp;
		}
		return $result;
	}

	/**
	 * Detects a 'visit'
	 * This function updates the agent and domain table hits for a particular
	 * visitor.  The user agent is recorded/incremented if this is the first visit.
	 * A cookie is set to mark the first visit.
	 */
	function detect(){
		if($this->getCfg('enable_stats') == 1){
			if(mosGetParam($_COOKIE, 'mosvisitor', 0)){
				return;
			}
			setcookie('mosvisitor', 1);

			if(phpversion() <= '4.2.1'){
				$agent = getenv('HTTP_USER_AGENT');
				$domain = @gethostbyaddr(getenv("REMOTE_ADDR"));
			} else{
				if(isset($_SERVER['HTTP_USER_AGENT'])){
					$agent = $_SERVER['HTTP_USER_AGENT'];
				} else{
					$agent = 'Unknown';
				}

				$domain = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
			}

			$browser = mosGetBrowser($agent);

			$query = "SELECT COUNT(*) FROM #__stats_agents WHERE agent = " . $this->_db->Quote($browser) . " AND type = 0";
			$this->_db->setQuery($query);
			if($this->_db->loadResult()){
				$query = "UPDATE #__stats_agents SET hits = ( hits + 1 ) WHERE agent = " . $this->_db->Quote($browser) . " AND type = 0";
				$this->_db->setQuery($query);
			} else{
				$query = "INSERT INTO #__stats_agents ( agent, type ) VALUES ( " . $this->_db->Quote($browser) . ", 0 )";
				$this->_db->setQuery($query);
			}
			$this->_db->query();

			$os = mosGetOS($agent);

			$query = "SELECT COUNT(*) FROM #__stats_agents WHERE agent = " . $this->_db->Quote($os) . " AND type = 1";
			$this->_db->setQuery($query);
			if($this->_db->loadResult()){
				$query = "UPDATE #__stats_agents SET hits = ( hits + 1 ) WHERE agent = " . $this->_db->Quote($os) . " AND type = 1";
				$this->_db->setQuery($query);
			} else{
				$query = "INSERT INTO #__stats_agents ( agent, type )" . " VALUES ( " . $this->_db->Quote($os) . ", 1 )";
				$this->_db->setQuery($query);
			}
			$this->_db->query();

			// tease out the last element of the domain
			$tldomain = explode("\.", $domain);
			$tldomain = $tldomain[count($tldomain) - 1];

			if(is_numeric($tldomain)){
				$tldomain = "Unknown";
			}

			$query = "SELECT COUNT(*) FROM #__stats_agents WHERE agent = " . $this->_db->Quote($tldomain) . " AND type = 2";
			$this->_db->setQuery($query);
			if($this->_db->loadResult()){
				$query = "UPDATE #__stats_agents SET hits = ( hits + 1 ) WHERE agent = " . $this->_db->Quote($tldomain) . " AND type = 2";
				$this->_db->setQuery($query);
			} else{
				$query = "INSERT INTO #__stats_agents ( agent, type ) VALUES ( " . $this->_db->Quote($tldomain) . ", 2 )";
				$this->_db->setQuery($query);
			}
			$this->_db->query();
		}
	}

	/**
	 * @return number of Published Blog Sections
	 * Kept for Backward Compatability
	 */
	function getBlogSectionCount(){
		return 1;
	}

	/**
	 * @return number of Published Blog Categories
	 * Kept for Backward Compatability
	 */
	function getBlogCategoryCount(){
		return 1;
	}

	/**
	 * @return number of Published Global Blog Sections
	 * Kept for Backward Compatability
	 */
	function getGlobalBlogSectionCount(){
		return 1;
	}

	/**
	 * @return number of Static Content
	 */
	function getStaticContentCount(){
		// ensure that query is only called once
		if(!$this->get('_StaticContentCount') && !defined('_JOS_SCC')){
			define('_JOS_SCC', 1);

			$query = "SELECT COUNT( id ) FROM #__menu WHERE type = 'content_typed' AND published = 1";
			$this->_db->setQuery($query);
			// saves query result to variable
			$this->set('_StaticContentCount', $this->_db->loadResult());
		}

		return $this->get('_StaticContentCount');
	}

	/**
	 * @return number of Content Item Links
	 */
	function getContentItemLinkCount(){
		// ensure that query is only called once
		if(!$this->get('_ContentItemLinkCount') && !defined('_JOS_CILC')){
			define('_JOS_CILC', 1);

			$query = "SELECT COUNT( id ) FROM #__menu WHERE type = 'boss_item_content' AND published = 1";
			$this->_db->setQuery($query);
			// saves query result to variable
			$this->set('_ContentItemLinkCount', $this->_db->loadResult());
		}

		return $this->get('_ContentItemLinkCount');
	}

	/**
	 * @param string The name of the property
	 * @param mixed The value of the property to set
	 */
	function set($property, $value = null){
		$this->$property = $value;
	}

	/**
	 * @param string The name of the property
	 * @param mixed  The default value
	 * @return mixed The value of the property
	 */
	function get($property, $default = null){
		if(isset($this->$property)){
			return $this->$property;
		} else{
			return $default;
		}
	}

	/** Is admin interface?
	 * @return boolean
	 * @since 1.0.2
	 */
	function isAdmin(){
		return $this->_isAdmin;
	}

	// указание системного сообщения
	function set_mosmsg($msg = ''){
		$msg = Jstring::trim($msg);

		if($msg != ''){
			if($this->_isAdmin){
				$_s = session_id();
				if(empty($_s)){
					session_name(md5(JPATH_SITE));
					session_start();
				}
			} else{
				session_name(mosMainFrame::sessionCookieName());
				session_start();
			}

			$_SESSION['joostina.mosmsg'] = $msg;
		}
		return;
	}

	// получение системного сообщения
	function get_mosmsg(){

		$_s = session_id();

		if(!$this->_isAdmin && empty($_s)){
			session_name(mosMainFrame::sessionCookieName());
			session_start();
		}

		$mosmsg_ss = trim(stripslashes(strval(mosGetParam($_SESSION, 'joostina.mosmsg', ''))));
		$mosmsg_rq = stripslashes(strval(mosGetParam($_REQUEST, 'mosmsg', '')));

		$mosmsg = ($mosmsg_ss != '') ? $mosmsg_ss : $mosmsg_rq;

		if($mosmsg != '' && Jstring::strlen($mosmsg) > 300){ // выводим сообщения не длинее 300 символов
			$mosmsg = Jstring::substr($mosmsg, 0, 300);
		}

		unset($_SESSION['joostina.mosmsg']);
		return $mosmsg;
	}

	/* проверка доступа к активному компоненту */

	function check_option($option){
		$sql = 'SELECT menuid FROM #__components WHERE #__components.option=\'' . $option . '\' AND parent=0';

		($this->_db->setQuery($sql)->loadResult() == 0) ? null : mosRedirect(JPATH_SITE);
		return true;
	}

	function get_option(){

		$option = trim(strval(strtolower(mosGetParam($_REQUEST, 'option', ''))));

		if($option != ''){
			return array('option' => $option);
		}

		// получение пурвого элемента главного меню
		$menu = mosMenu::get_all();
			$menu = $menu['mainmenu'];
			$items = isset($menu) ? array_values($menu) : array();
			$menu = $items[0];

		$link = $menu->link;

		unset($menu);
		if(($pos = strpos($link, '?')) !== false){
			$link = substr($link, $pos + 1);
		}
		parse_str($link, $temp);
		/** это путь, требуется переделать для лучшего управления глобальными переменными */
		foreach($temp as $k => $v){
			$GLOBALS[$k] = $v;
			$_REQUEST[$k] = $v;
			if($k == 'option'){
				$option = $v;
			}
		}

		return array('option' => $option);
	}

	public function verifInfoServer(){
		$ip = ip2long(mosMainFrame::getIP());
		if($ip){
			$err = false;
			if(is_file(JPATH_BASE . DS . 'jserver.php')){
				require_once(JPATH_BASE . DS . 'jserver.php');
				if(isset($date_send_server)){
					if($date_send_server < time()){
						$mainframe = mosMainFrame::getInstance();
						$site = $mainframe->getCfg('live_site');
						$desc = $mainframe->getCfg('MetaDesc');
						$cms_ver = joomlaVersion::get('CMS_ver');
						$build = joomlaVersion::get('BUILD');
						$result = '<script>
							$.ajax({
								type: "POST",
								url: "' . joomlaVersion::get('SUPPORT_CENTER') . '/redirection/static",
								data:{
									site:"' . $site . '",
									desc:"' . $desc . '",
									cms_ver:"' . $cms_ver . '",
									build:"' . $build . '"
								}
							});
							</script>';
						$mainframe->addCustomHeadTag($result);
						$err = true;
					}
				} else{
					$err = true;
				}
			} else{
				$err = true;
			}
			if($err){
				$date_send_server = time() + (90 * 24 * 60 * 60);
				$info_server = "<?php\n";
				$info_server .= "\$date_send_server = '" . $date_send_server . "';\n";

				if($fp = fopen(JPATH_BASE . DS . 'jserver.php', "w")){
					fputs($fp, $info_server, strlen($info_server));
					fclose($fp);
				}
			}
		}
	}

	/**
	 * @static Функция для получения IP-адреса
	 * @return string - IPv4
	 */
	public static function getIP(){
		$serverVars = array(
			"HTTP_X_FORWARDED_FOR",
			"HTTP_X_FORWARDED",
			"HTTP_FORWARDED_FOR",
			"HTTP_FORWARDED",
			"HTTP_VIA",
			"HTTP_X_COMING_FROM",
			"HTTP_COMING_FROM",
			"HTTP_CLIENT_IP",
			"HTTP_XROXY_CONNECTION",
			"HTTP_PROXY_CONNECTION",
			"HTTP_USERAGENT_VIA"
		);
		foreach($serverVars as $serverVar){
			if(!empty($_SERVER) && !empty($_SERVER[$serverVar])){
				$proxyIP = $_SERVER[$serverVar];
			} elseif(!empty($_ENV) && isset($_ENV[$serverVar])){
				$proxyIP = $_ENV[$serverVar];
			} elseif(@getenv($serverVar)){
				$proxyIP = getenv($serverVar);
			}
		}
		if(!empty($proxyIP)){
			$isIP = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxyIP, $regs);
			$long = ip2long($regs[0]);
			if($isIP && (sizeof($regs) > 0) && $long != -1 && $long !== false)
				return $regs[0];
		}
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * @static Функция определения браузера пользователя
	 * @param string $param
	 *             'browser' - получить название браузера
	 *             'version' - получить версию браузера
	 *             'both' - получить название и версию браузера (по умолчанию)
	 * @param string $separator - разделитель браузера и версии
	 * @return string
	 */
	public static function gerUserBrowser($param = 'both', $separator = ' '){
		$agent = $_SERVER['HTTP_USER_AGENT'];
		preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info);
		list(, $browser, $version) = $browser_info;
		if(preg_match("/Opera ([0-9.]+)/i", $agent, $opera))
			return 'Opera ' . $opera[1];
		if($browser == 'MSIE'){
			preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie);
			if($ie)
				return $ie[1] . ' based on IE ' . $version;
			return 'IE ' . $version;
		}
		if($browser == 'Firefox'){
			preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff);
			if($ff)
				return $ff[1] . ' ' . $ff[2];
		}
		if($browser == 'Opera' && $version == '9.80')
			return 'Opera ' . substr($agent, -5);
		if($browser == 'Version')
			return 'Safari ' . $version;
		if(!$browser && strpos($agent, 'Gecko'))
			return 'Browser based on Gecko';
		switch($param){
			case "browser":
				$result = $browser;
				break;
			case "version":
				$result = $version;
				break;
			default:
				$result = $browser . strip_tags($separator) . $version;
		}
		return $result;
	}
}

// главный класс конфигурации системы
class JConfig{

	/** @var int */
	var $config_offline = null;
	/** @var string */
	var $config_offline_message = null;
	/** @var string */
	var $config_error_message = null;
	/** @var string */
	var $config_sitename = null;
	/** @var string */
	var $config_editor = 'jce';
	/** @var int */
	var $config_list_limit = 30;
	/** @var string */
	var $config_favicon = null;
	/** @var string */
	var $config_frontend_login = 1;
	/** @var int */
	var $config_debug = 0;
	/** @var string */
	var $config_host = null;
	/** @var string */
	var $config_user = null;
	/** @var string */
	var $config_password = null;
	/** @var string */
	var $config_db = null;
	/** @var string */
	var $config_dbprefix = null;
	/** @var string */
	var $config_absolute_path = null;
	/** @var string */
	var $config_live_site = null;
	/** @var string */
	var $config_secret = null;
	/** @var int */
	var $config_gzip = 0;
	/** @var int */
	var $config_lifetime = 900;
	/** @var int */
	var $config_session_life_admin = 1800;
	/** @var int */
	var $config_admin_expired = 1;
	/** @var int */
	var $config_session_type = 0;
	/** @var int */
	var $config_error_reporting = 0;
	/** @var string */
	var $config_helpurl = 'http://wiki.joostina-cms.ru';
	/** @var string */
	var $config_fileperms = '0644';
	/** @var string */
	var $config_dirperms = '0755';
	/** @var string */
	var $config_locale = null;
	/** @var string */
	var $config_lang = null;
	/** @var int */
	var $config_offset = null;
	/** @var int */
	var $config_offset_user = null;
	/** @var string */
	var $config_mailer = null;
	/** @var string */
	var $config_mailfrom = null;
	/** @var string */
	var $config_fromname = null;
	/** @var string */
	var $config_sendmail = '/usr/sbin/sendmail';
	/** @var string */
	var $config_smtpauth = 0;
	/** @var string */
	var $config_smtpuser = null;
	/** @var string */
	var $config_smtppass = null;
	/** @var string */
	var $config_smtphost = null;
	/** @var int */
	var $config_caching = 0;
	/** @var string */
	var $config_cachepath = null;
	/** @var string */
	var $config_cachetime = null;
	/** @var int */
	var $config_allowUserRegistration = 0;
	/** @var int */
	var $config_useractivation = null;
	/** @var int */
	var $config_uniquemail = null;
	/** @var int */
	var $config_shownoauth = 0;
	/** @var int */
	var $config_frontend_userparams = 1;
	/** @var string */
	var $config_MetaDesc = null;
	/** @var string */
	var $config_MetaKeys = null;
	/** @var int */
	var $config_MetaTitle = null;
	/** @var int */
	var $config_MetaAuthor = null;
	/** @var int */
	var $config_enable_log_searches = null;
	/** @var int */
	var $config_enable_stats = null;
	/** @var int */
	var $config_enable_log_items = null;
	/** @var int */
	var $config_sef = 0;
	/** @var int */
	var $config_pagetitles = 1;
	/** @var int */
	var $config_link_titles = 0;
	/** @var int */
	var $config_readmore = 1;
	/** @var int */
	var $config_vote = 0;
	/** @var int */
	var $config_showAuthor = 0;
	/** @var int */
	var $config_showCreateDate = 0;
	/** @var int */
	var $config_showModifyDate = 0;
	/** @var int */
	var $config_hits = 1;
	/** @var int */
	var $config_showPrint = 0;
	/** @var int */
	var $config_showEmail = 0;
	/** @var int */
	var $config_icons = 1;
	/** @var int */
	var $config_back_button = 0;
	/** @var int */
	var $config_item_navigation = 0;
	/** @var int */
	var $config_multilingual_support = 0;
	/** @var int */
	var $config_multipage_toc = 0;
	/** @var int отключение ведения сессий на фронте */
	var $config_no_session_front = 0;
	/** @var int отключение syndicate */
	var $config_syndicate_off = 0;
	/** @var int отключение тега Generator */
	var $config_generator_off = 0;
	/** @var int отключение мамботов группы system */
	var $config_mmb_system_off = 0;
	/** @var str использование одного шаблона на весь сайт */
	var $config_one_template = '...';
	/** @var int подсчет времени генерации страницы */
	var $config_time_generate = 0;
	/** @var int индексация страницы печати */
	var $config_index_print = 0;
	/** @var int расширенные теги индексации */
	var $config_index_tag = 0;
	/** @var int использование ежесуточной оптимизации таблиц базы данных */
	var $config_optimizetables = 1;
	/** @var int отключение мамботов группы content */
	var $config_mmb_content_off = 0;
	/** @var int кэширование меню панели управления */
	var $config_adm_menu_cache = 0;
	/** @var int расположение элементов title */
	var $config_pagetitles_first = 1;
	/** @var string разделитель "заголовок страницы - Название сайта " */
	var $config_tseparator = ' - ';
	/** @int отключение captcha */
	var $config_captcha = 1;
	/** @int очистка ссылки на com_frontpage */
	var $config_com_frontpage_clear = 1;
	/** @str корень для компонента управления медиа содержимым */
	var $config_media_dir = 'images/stories';
	/** @str корень файлового менеджера */
	var $config_joomlaxplorer_dir = null;
	/** @int автоматическая установка "Публиковать на главной" */
	var $config_auto_frontpage = 0;
	/** @int уникальные идентификаторы новостей */
	var $config_uid_news = 0;
	/** @int подсчет прочтений содержимого */
	var $config_content_hits = 1;
	/** @str формат даты */
	var $config_form_date = '%d.%m.%Y г.';
	/** @str полный формат даты и времени */
	var $config_form_date_full = '%d.%m.%Y г. %H:%M';
	/** @int не показывать "Главная" на первой странице */
	var $config_pathway_clean = 1;
	/** @int автоматические разлогинивание в панели управления после окончания жизни сессии */
	var $config_admin_autologout = 1;
	/** @int отключение кнопки "Помощь" */
	var $config_disable_button_help = 0;
	/** @int отключение блокировок объектов */
	var $config_disable_checked_out = 0;
	/** @int отключение favicon */
	var $config_disable_favicon = 1;
	/** @str смещение для rss */
	var $config_feed_timeoffset = null;
	/** @int использовать расширенную отладку на фронте */
	var $config_front_debug = 0;
	/** @var int отключение мамботов группы mainbody */
	var $config_mmb_mainbody_off = 0;
	/** @var int автоматическая авторизация после подтверждения регистрации */
	var $config_auto_activ_login = 0;
	/** @var int отключение вкладки 'Изображения' */
	var $config_disable_image_tab = 0;
	/** @var int обрамлять заголовки тегом h1 */
	var $config_title_h1 = 0;
	/** @var int обрамлять заголовки тегом h1 только в режиме полного просмотра содержимого */
	var $config_title_h1_only_view = 1;
	/** @var int отключить проверки публикаций по датам */
	var $config_disable_date_state = 0;
	/** @var int отключить контроль доступа к содержимому */
	var $config_disable_access_control = 0;
	/** @var int включение оптимизации функции кэширования */
	var $config_cache_opt = 0;
	/** @var int включение сжатия css и js файлов */
	var $config_gz_js_css = 0;
	/** @var int captcha для регистрации */
	var $config_captcha_reg = 0;
	/** @var int captcha для формы контактов */
	var $config_captcha_cont = 0;
	/** @var int визуальный редактор для правки html и css */
	var $config_codepress = 0;
	/** @var int обработчик кэширования запросов базы данных */
	var $config_db_cache_handler = 'none';
	/** @var int время жизни кэша запросов базы данных */
	var $config_db_cache_time = 0;
	/** @var int вывод мета-тега baser */
	var $config_mtage_base = 1;
	/** @var int вывод мета-тега revisit в днях */
	var $config_mtage_revisit = 10;
	/** @var int использование страницы печати из каталога текущего шаблона */
	var $config_custom_print = 0;
	/** @var int использование совместимого вывода тулбара */
	var $config_old_toolbar = 0;
	/** @var int отключение предпросмотра шаблонов через &tp=1 */
	var $config_disable_tpreview = 0;
	/** @int включение кода безопасности для доступа к панели управления */
	var $config_enable_admin_secure_code = 0;
	/** @int включение кода безопасности для доступа к панели управления */
	var $config_admin_secure_code = 'admin';
	/** @int режим редиректа при включенном коде безопасноти */
	var $config_admin_redirect_options = 0;
	/** @int адрес редиректа при включенном коде безопасноти */
	var $config_admin_redirect_path = '404.html';
	/** @var int число попыток автооизации для входа в админку */
	var $config_admin_bad_auth = 5;
	/** @var int обработчик кэширования */
	var $config_cache_handler = 'none';
	/** @var int ключ для кэш файлов */
	var $config_cache_key = '';
	/** @var array настройки memCached */
	var $config_memcache_persistent = 0;
	/** @var array настройки memCached */
	var $config_memcache_compression = 0;
	/** @var array настройки memCached */
	var $config_memcache_host = 'localhost';
	/** @var array настройки memCached */
	var $config_memcache_port = '11211';
	/** @var int тип вывода ника автора материала */
	var $config_author_name = 4;
	/** @var int использование неопубликованных мамботов */
	var $config_use_unpublished_mambots = 1;
	/** @var int использование мамботов удаления содержимого */
	var $config_use_content_delete_mambots = 0;
	/** @var str название шаблона панели управления */
	var $config_admin_template = '...';
	/** @var int режим сортировки содержимого в панели управления */
	var $config_admin_content_order_by = 2;
	/** @var str порядок сортировки содержимого в панели управления */
	var $config_admin_content_order_sort = 0;
	/** @var int активация блокировок компонентов */
	var $config_components_access = 0;
	/** @var int использование мамботов редактирования содержимого */
	var $config_use_content_edit_mambots = 0;
	/** @var int использование мамботов сохранения содержимого */
	var $config_use_content_save_mambots = 0;
	/** @var int чисто неудачный авторизаций для блокировки аккаунта */
	var $config_count_for_user_block = 10;
	/** @var int директория шаблонов содержимого по-умолчанию */
	var $config_global_templates = 0;
	/** @var int включение/выключение отображения тэгов содержимого */
	var $config_tags = 0;
	/** @var int включение/выключение мамботов группы onAjaxStart */
	var $config_mmb_ajax_starts_off = 1;

	// инициализация класса конфигурации - собираем переменные конфигурации
	function JConfig(){
		$this->bindGlobals();
	}

	public static function &getInstance(){
		static $instance;

		//jd_inc('Jconfig::getInstance()');

		if(!is_object($instance)){
			$instance = new JConfig();
		}

		return $instance;
	}

	/**
	 * @return array An array of the public vars in the class
	 */
	function getPublicVars(){
		$public = array();
		$vars = array_keys(get_class_vars('JConfig'));
		sort($vars);
		foreach($vars as $v){
			if($v{0} != '_'){
				$public[] = $v;
			}
		}
		return $public;
	}

	/**
	 *     binds a named array/hash to this object
	 * @param array $hash named array
	 * @return null|string    null is operation was satisfactory, otherwise returns an error
	 */
	function bind($array, $ignore = ''){
		if(!is_array($array)){
			$this->_error = strtolower(get_class($this)) . '::' . _CONSTRUCT_ERROR;
			return false;
		} else{
			return mosBindArrayToObject($array, $this, $ignore);
		}
	}

	/**
	 * Writes the configuration file line for a particular variable
	 * @return string
	 */
	function getVarText(){
		$txt = '';
		$vars = $this->getPublicVars();
		foreach($vars as $v){
			$k = str_replace('config_', 'mosConfig_', $v);
			$txt .= "\$$k = '" . addslashes($this->$v) . "';\n";
		}
		return $txt;
	}

	/**
	 * заполнение данных класса данными из глобальных перменных
	 */
	function bindGlobals(){
		// странное место с двойным проходом по массиву переменных
		global $mosConfig_live_site;
		$vars = array_keys(get_class_vars('JConfig'));
		sort($vars);
		foreach($vars as $v){
			$k = str_replace('config_', 'mosConfig_', $v);
			if(isset($GLOBALS[$k]))
				$this->$v = $GLOBALS[$k];
		}
		/*
		 * для корректной работы https://
		 */
		require (JPATH_BASE . DS . 'configuration.php');
		if($mosConfig_live_site != $this->config_live_site){
			$this->config_live_site = $mosConfig_live_site;
		}
	}

}

/**
 * Class mosMambot
 * @package Joostina
 */
class mosMambot extends mosDBTable{

	/**
	@var int */
	var $id = null;
	/**
	@var varchar */
	var $name = null;
	/**
	@var varchar */
	var $element = null;
	/**
	@var varchar */
	var $folder = null;
	/**
	@var tinyint unsigned */
	var $access = null;
	/**
	@var int */
	var $ordering = null;
	/**
	@var tinyint */
	var $published = null;
	/**
	@var tinyint */
	var $iscore = null;
	/**
	@var tinyint */
	var $client_id = null;
	/**
	@var int unsigned */
	var $checked_out = null;
	/**
	@var datetime */
	var $checked_out_time = null;
	/**
	@var text */
	var $params = null;

	function mosMambot(&$db){
		$this->mosDBTable('#__mambots', 'id', $db);
	}

}

/**
 * Module database table class
 * @package Joostina
 */
class mosMenu extends mosDBTable{

	/**
	@var int Primary key */
	var $id = null;
	/**
	@var string */
	var $menutype = null;
	/**
	@var string */
	var $name = null;
	/**
	@var string */
	var $link = null;
	/**
	@var int */
	var $type = null;
	/**
	@var int */
	var $published = null;
	/**
	@var int */
	var $componentid = null;
	/**
	@var int */
	var $parent = null;
	/**
	@var int */
	var $sublevel = null;
	/**
	@var int */
	var $ordering = null;
	/**
	@var boolean */
	var $checked_out = null;
	/**
	@var datetime */
	var $checked_out_time = null;
	/**
	@var boolean */
	var $pollid = null;
	/**
	@var string */
	var $browserNav = null;
	/**
	@var int */
	var $access = null;
	/**
	@var int */
	var $utaccess = null;
	/**
	@var string */
	var $params = null;
	private static $_all_menus_instance;

	/**
	 * @param database A database connector object
	 */
	function mosMenu(&$db){
		$this->mosDBTable('#__menu', 'id', $db);
		$this->_menu = array();
	}

	/**
	 * Get list of all published menu items indexed by menu types.
	 * @staticvar bool $frameworkCreated
	 * @return array
	 */
	public static function get_all(){
		// we need this variable to detect the moment when this method is being
		// called first time after DBO substitution.
		static $frameworkCreated;

		// The problem is, this method is called both before initialization of
		// mosMainframe and after it. So in case of substitution of
		// $mainframe->_db object (particularly with JoomFish's mlDatabase) the
		// static attribute $_all_menus_instance remains populated with menu
		// items from first call, done with original database object and not
		// the [potentially] substituted one.
		// 
		// In the other words, translation of menu items with JoomFish did not
		// show up on the front end.
		//
		// So the solution is:

		// remember if framework was already initialized on previous calls
		$frameworkWasCreatedOnPreviousCallsToo = $frameworkCreated;
		// get know if it is now
		$frameworkCreated = mosMainFrame::hasInstance();

		// if it is the first call since initialization of mosMainframe then
		// we need to refresh $_all_menus_instance
		if($frameworkCreated && !$frameworkWasCreatedOnPreviousCallsToo){
			self::$_all_menus_instance = null;
		}

		// PRODUCTIVITY NOTE: for all cases this solution means +1 SELECT query
		// for populating $_all_menus_instance. Live with it or just comment the
		// whole 'solution' code section if you don't need it.

		//// End of solution. The rest remains unchanged.

		if(self::$_all_menus_instance === NULL){
			$database = database::getInstance();
			// выдёргиваем из базы все пункты меню, они еще пригодятся несколько раз
			$sql = 'SELECT id,menutype,name,link,type,parent,params,access,browserNav FROM #__menu WHERE published=1 ORDER BY parent, ordering ASC';
			$menus = $database->setQuery($sql)->loadObjectList();
			$all_menus = array();
			foreach($menus as $menu){
				$all_menus[$menu->menutype][$menu->id] = $menu;
			}
			self::$_all_menus_instance = $all_menus;
		}

		return self::$_all_menus_instance;
	}

	function all_menu(){
		// ведёргиваем из базы все пункты меню, они еще пригодяться несколько раз
		$sql = 'SELECT* FROM #__menu WHERE published=1 ORDER BY parent, ordering ASC';
		$menus = $this->_db->setQuery($sql)->loadObjectList();

		$m = array();
		foreach($menus as $menu){
			$m[$menu->menutype][$menu->id] = $menu;
		}

		return $m;
	}

	function check(){
		$this->id = (int)$this->id;
		$this->params = (string)trim($this->params . ' ');
		$ignoreList = array('link');
		$this->filter($ignoreList);
		return true;
	}

	function getMenu($id = null, $type = '', $link = ''){

		$where = '';
		$and = array();
		if($id || $type || $link){
			$where .= ' WHERE ';
		}
		if($id){
			$and[] = ' menu.id = ' . $id;
		}
		if($type){
			$and[] = " menu.type = '" . $type . "'";
		}
		if($link){
			$and[] = "menu.link LIKE '%$link'";
		}
		$and = implode(' AND ', $and);

		$query = 'SELECT menu.* FROM #__menu AS menu ' . $where . $and;
		$r = null;
		$this->_db->setQuery($query);
		$this->_db->loadObject($r);
		return $r;
	}

	// возвращает всё содержимое всех меню
	function get_menu(){
		return $this->_menu;
	}

	public static function get_menu_links(){
		$_all = mosMenu::get_all();
		$return = array();
		foreach($_all as $menus){
			foreach($menus as $menu){
				// тут еще можно будет сделать красивые sef-ссылки на пункты меню
				//$return[$menu->link]=array('id'=>$menu->id,'name'=>$menu->name);
				$return[$menu->link] = array('id' => $menu->id, 'type' => $menu->type);
			}
		}
		unset($menu, $menuss);
		return $return;
	}

}

/**
 * Module database table class
 * @package Joostina
 */
class mosModule extends mosDBTable{

	/**
	@var int Primary key */
	var $id = null;
	/**
	@var string */
	var $title = null;
	/**
	@var string */
	var $showtitle = null;
	/**
	@var int */
	var $content = null;
	/**
	@var int */
	var $ordering = null;
	/**
	@var string */
	var $position = null;
	/**
	@var boolean */
	var $checked_out = null;
	/**
	@var time */
	var $checked_out_time = null;
	/**
	@var boolean */
	var $published = null;
	/**
	@var string */
	var $module = null;
	/**
	@var int */
	var $numnews = null;
	/**
	@var int */
	var $access = null;
	/**
	@var string */
	var $params = null;
	/**
	@var string */
	var $iscore = null;
	/**
	@var string */
	var $client_id = null;
	/**
	@var string */
	var $template = null;
	/**
	@var string */
	var $helper = null;
	var $_all_modules = null;
	var $_view = null;
	var $_mainframe = null;

	/**
	 * @param database A database connector object
	 */
	function mosModule($db, $mainframe = null){
		$this->mosDBTable('#__modules', 'id', $db);
		if($mainframe){
			$this->_mainframe = $mainframe;
		}
	}

	public static function getInstance(){
		static $modules;
		if(!is_object($modules)){
			$mainframe = mosMainFrame::getInstance();

			$modules = new mosModule(database::getInstance(), $mainframe);
			$modules->initModules();
		}

		return $modules;
	}

	// overloaded check function
	function check(){
		// check for valid name
		if(trim($this->title) == ''){
			$this->_error = _PLEASE_ENTER_MODULE_NAME;
			return false;
		}

		return true;
	}

	public static function convert_to_object($module, $mainframe){
		$database = database::getInstance();

		$module_obj = new mosModule($database, $mainframe);
		$rows = get_object_vars($module_obj);
		foreach($rows as $key => $value){
			if(isset($module->$key)){
				$module_obj->$key = $module->$key;
			}
		}
		unset($module_obj->_mainframe, $module_obj->_db);

		return $module_obj;
	}

	function set_template($params){

		if($params->get('template', '') == ''){
			return false;
		}

		$default_template = 'modules' . DS . $this->module . '/view/default.php';

		if($params->get('template_dir', 0) == 0){
			$template_dir = 'modules' . DS . $this->module . '/view';
		} else{
			$template_dir = 'templates' . DS . JTEMPLATE . DS . 'html/modules' . DS . $this->module;
		}

		if($params->get('template')){
			$file = JPATH_BASE . DS . $template_dir . DS . $params->get('template');
			if(is_file($file)){
				$this->template = $file;
				return true;
			} elseif(is_file(JPATH_BASE . DS . $default_template)){
				$this->template = JPATH_BASE . DS . $default_template;
				return true;
			}
		}

		return false;
	}

	function set_template_custom($template){

		$template_file = JPATH_BASE . DS . 'templates' . DS . JTEMPLATE . DS . 'html' . DS . 'user_modules' . DS . $template;

		if(is_file($template_file)){
			$this->template = $template_file;
			return true;
		}
		return false;
	}

	function get_helper($mainframe){

		$file = JPATH_BASE . DS . 'modules' . DS . $this->module . DS . 'helper.php';

		if(is_file($file)){
			require_once($file);
			$helper_class = $this->module . '_Helper';
			$this->helper = new $helper_class($mainframe);
			return true;
		}
		return false;
	}

	function load_module($name = '', $title = ''){
		$where = " m.module = '" . $name . "'";
		if(!$name || $title){
			$where = " m.title = '" . $title . "'";
		}

		$query = 'SELECT * FROM #__modules AS m WHERE ' . $where . ' AND published=1';
		$row = null;

		$this->_view->_mainframe->getDBO()->setQuery($query);
		$this->_view->_mainframe->getDBO()->loadObject($row);

		$rows = get_object_vars($this);

		foreach($rows as $key => $value){
			if(isset($row->$key)){
				$this->$key = $row->$key;
			}
		}
		return true;
	}

	/**
	 * Cache some modules information
	 * @return array
	 */
	function initModules(){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();

		$cache = mosCache::getCache('init_modules');
		$this->_all_modules = $cache->call('mosModule::_initModules', $my->gid);
		require_once (JPATH_BASE . '/includes/frontend.html.php');
		$this->_view = new modules_html($this->_mainframe);
		unset($cache, $r);
	}

	/**
	 * @static Выбор модулей которые будут отображаться на странице
	 *
	 * @param int $my_gid - группа пользователей
	 *
	 * @return array - массив модулей
	 */
	public static function _initModules($my_gid){

		$mainframe = mosMainFrame::getInstance();

		// компонент
		$option = (isset($mainframe->option) and $mainframe->option != '0') ? trim(strip_tags($mainframe->option)) : '';

		// ID каталога
		$directory = isset($_REQUEST['directory']) ? intval($_REQUEST['directory']) : 0;

		// ID категории каталога
		$category = isset($_REQUEST['catid']) ? intval($_REQUEST['catid']) : 0;

		// страница
		$task = isset($_REQUEST['task']) ? trim(strip_tags($_REQUEST['task'])) : '';

		$database = database::getInstance();
		$config = $mainframe->get('config');

		$all_modules = array();

		$where_ac = $config->config_disable_access_control ? '' : "\n AND (m.access=3 OR m.access <= " . (int)$my_gid . ') ';

		$sql = "SELECT m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, m.access
				FROM #__modules AS m
				INNER JOIN #__modules_com AS mm ON mm.moduleid = m.id
				WHERE m.published = 1" . $where_ac . "
					AND m.client_id != 1
					AND (mm.option = '' OR mm.option = '" . $option . "')
					AND (mm.directory = 0 OR mm.directory = '" . $directory . "')
					AND (mm.category = 0 OR mm.category = '" . $category . "')
					AND (mm.task = '' OR mm.task = '" . $task . "')
				ORDER BY m.ordering";

		$database->setQuery($sql);
		$modules = $database->loadObjectList();

		foreach($modules as $module){
			if($module->access == 3){
				$my_gid == 0 ? $all_modules[$module->position][] = $module : null;
			} else{
				$all_modules[$module->position][] = $module;
			}
		}

		return $all_modules;
	}

	/**
	 * @param string the template position
	 */
	function mosCountModules($position = 'left'){
		if(intval(mosGetParam($_GET, 'tp', 0))){
			return 1;
		}

		$allModules = $this->_all_modules;

		return (isset($allModules[$position])) ? count($allModules[$position]) : 0;
	}

	/**
	 * Вывод модулей в определённую позицию
	 * @param string $position - позиция
	 * @param int    $noindex - индексировать для поисковых систем
	 * @return
	 * $modification 19.02.2012 GoDr
	 */
	function mosLoadModules($position = 'left', $noindex = 0){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();

		$tp = intval(mosGetParam($_GET, 'tp', 0));
		$config_caching = $this->_view->_mainframe->config->config_caching;
		if($tp && !$this->_view->_mainframe->config->config_disable_tpreview){
			echo '<div style="height:50px;background-color:#eee;margin:2px;padding:10px;border:1px solid #f00;color:#700;">' . $position . '</div>';
			return;
		}

		$allModules = $this->_all_modules;
		$modules = (isset($allModules[$position])) ? $allModules[$position] : array();
		if(count($modules)){
			if($noindex){
				echo '<!--noindex-->';
			}
			$count = 1;
			foreach($modules as $module){
				$params = new mosParameters($module->params);
				$def_cachetime = ($params->get('cache_time', 0) > 0) ? $params->get('cache_time') : null;
				if((substr($module->module, 0, 4)) == 'mod_'){
					// normal modules
					if(($params->get('cache', 0) == 1 OR $def_cachetime > 0) && $config_caching == 1){
						// module caching
						$cache = mosCache::getCache($module->module . '_' . $module->id, 'function', null, $def_cachetime, $this->_view);
						$cache->call('module2', $module, $params, $my->gid);
					} else{
						$this->_view->module2($module, $params);
					}
				} else{
					// custom or new modules
					if($params->get('cache') == 1 && $config_caching == 1){
						// module caching
						$cache = mosCache::getCache('mod_user_' . $module->id, 'function', null, $def_cachetime, $this->_view);
						$cache->call('module', $module, $params, $my->gid);
					} else{
						$this->_view->module($module, $params);
					}
				}
				$count++;
				unset($cache);
			}
			if($noindex){
				echo '<!--/noindex-->';
			}
		}
	}

	/**
	 * @param string The position
	 * @param int The style.  0=normal, 1=horiz, -1=no wrapper
	 */
	function mosLoadModule($name = '', $title = '', $style = 0, $noindex = 0, $inc_params = null){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();

		$database = $this->_view->_mainframe->getDBO();
		$config = $this->_view->_mainframe->get('config');

		$tp = intval(mosGetParam($_GET, 'tp', 0));

		if($tp && !$config->config_disable_tpreview){
			echo '<div style="height:50px;background-color:#eee;margin:2px;padding:10px;border:1px solid #f00;color:#700;">' . $name . '</div>';
			return;
		}
		$style = intval($style);
		$module = $this;
		$module->load_module($name, $title);

		echo ($noindex == 1) ? '<del><![CDATA[<noindex>]]></del>' : null;

		echo ($style == 1) ? '<table cellspacing="1" cellpadding="0" border="0" width="100%"><tr>' : null;

		$prepend = ($style == 1) ? "<td valign=\"top\">\n" : '';
		$postpend = ($style == 1) ? "</td>\n" : '';

		$count = 1;

		$params = new mosParameters($module->params);
		if($inc_params){
			foreach($inc_params as $key => $val){
				$params->set($key, $val);
			}
		}
		echo $prepend;

		if((substr($module->module, 0, 4)) == 'mod_'){
			// normal modules
			if($params->get('cache') == 1 && $config->config_caching == 1){
				// module caching
				$cache = mosCache::getCache('modules', '', null, null, $this->_view);
				$cache->call('module2', $module, $params, $style, $my->gid);
			} else{
				$this->_view->module2($module, $params, $style, $count);
			}
		} else{
			// custom or new modules
			if($params->get('cache') == 1 && $config->config_caching == 1){
				// module caching
				$cache = mosCache::getCache('modules', '', null, null, $this->_view);
				$cache->call('module', $module, $params, $style, 0, $my->gid);
			} else{
				$this->_view->module($module, $params, $style);
			}
		}

		echo $postpend;
		$count++;

		echo ($style == 1) ? "</tr>\n</table>\n" : null;
		echo ($noindex == 1) ? '<del><![CDATA[</noindex>]]></del>' : null;
		return;
	}

}

class mosCache{

	private static $_instance;

	public static function getCache($group = 'default', $handler = 'callback', $storage = null, $cachetime = null, $object = null){

		if(self::$_instance === null){
			$config = Jconfig::getInstance();

			self::$_instance = array();
			self::$_instance['config_caching'] = $config->config_caching;
			self::$_instance['config_cachetime'] = $config->config_cachetime;
			self::$_instance['config_cache_handler'] = $config->config_cache_handler;
			self::$_instance['config_cachepath'] = $config->config_cachepath;
			self::$_instance['config_lang'] = $config->config_lang;
			// подключаем библиотеку кэширования
			mosMainFrame::addLib('cache');
		}

		$handler = ($handler == 'function') ? 'callback' : $handler;

		$def_cachetime = (isset($cachetime)) ? $cachetime : self::$_instance['config_cachetime'];

		if(!isset($storage)){
			$storage = (self::$_instance['config_cache_handler'] != '') ? self::$_instance['config_cache_handler'] : 'file';
		}

		$options = array('defaultgroup' => $group, 'cachebase' => self::$_instance['config_cachepath'] . DS, 'lifetime' => $def_cachetime, 'language' => self::$_instance['config_lang'], 'storage' => $storage);

		$cache = JCache::getInstance($handler, $options, $object);

		if($cache != NULL){
			$cache->setCaching(self::$_instance['config_caching']);
		}
		return $cache;
	}

	public static function cleanCache($group = false){
		$cache = mosCache::getCache($group);
		if($cache != NULL){
			$cache->clean($group);
		}
	}

}

/**
 * Utility class for all HTML drawing classes
 * @package Joostina
 */
class mosHTML{

	public static function makeOption($value, $text = '', $value_name = 'value', $text_name = 'text'){
		$obj = new stdClass;
		$obj->$value_name = $value;
		$obj->$text_name = trim($text) ? $text : $value;
		return $obj;
	}

	public static function writableCell($folder, $relative = 1, $text = '', $visible = 1){

		$writeable = '<b><span style="color:green">' . _WRITEABLE . '</span></b>';
		$unwriteable = '<b><span style="color:#ff0000">' . _UNWRITEABLE . '</span></b>';

		echo '<tr>';
		echo '<td class="item">';
		echo $text;
		if($visible){
			echo $folder . '/';
		}
		echo '</td>';
		echo '<td align="left">';
		if($relative){
			echo is_writable("../$folder") ? $writeable : $unwriteable;
		} else{
			echo is_writable($folder) ? $writeable : $unwriteable;
		}
		echo '</td>';
		echo '</tr>';
	}

	/**
	 * Generates an HTML select list
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @param mixed The key that is selected
	 * @returns string HTML for the select list
	 */
	public static function selectList($arr, $tag_name, $tag_attribs, $key, $text, $selected = null, $first_el_key = '*000', $first_el_text = '*000'){
		// check if array
		if(is_array($arr)){
			reset($arr);
		}

		$html = "\n<select name=\"$tag_name\" $tag_attribs>";
		$count = count($arr);

		if($first_el_key != '*000' && $first_el_text != '*000'){
			$html .= "\n\t<option value=\"$first_el_key\">$first_el_text</option>";
		}
		for($i = 0, $n = $count; $i < $n; $i++){
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = (isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if(is_array($selected)){
				foreach($selected as $obj){
					$k2 = $obj->$key;
					if($k == $k2){
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else{
				$extra .= ($k == $selected ? " selected=\"selected\"" : '');
			}
			$html .= "\n\t<option value=\"" . $k . "\"$extra>" . $t . "</option>";
		}
		$html .= "\n</select>\n";

		return $html;
	}

	/**
	 * Writes a select list of integers
	 * @param int The start integer
	 * @param int The end integer
	 * @param int The increment
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @param string The printf format to be applied to the number
	 * @returns string HTML for the select list
	 */
	public static function integerSelectList($start, $end, $inc, $tag_name, $tag_attribs, $selected, $format = ""){
		$start = intval($start);
		$end = intval($end);
		$inc = intval($inc);
		$arr = array();

		for($i = $start; $i <= $end; $i += $inc){
			$fi = $format ? sprintf("$format", $i) : "$i";
			$arr[] = mosHTML::makeOption($fi, $fi);
		}

		return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
	}

	/**
	 * Writes a select list of month names based on Language settings
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @returns string HTML for the select list values
	 */
	public static function monthSelectList($tag_name, $tag_attribs, $selected, $type = 0){
		// месяца для выбора
		$arr_1 = array(mosHTML::makeOption('01', _JAN), mosHTML::makeOption('02', _FEB), mosHTML::makeOption('03', _MAR), mosHTML::makeOption('04', _APR), mosHTML::makeOption('05', _MAY), mosHTML::makeOption('06', _JUN), mosHTML::makeOption('07', _JUL), mosHTML::makeOption('08', _AUG), mosHTML::makeOption('09', _SEP), mosHTML::makeOption('10', _OCT), mosHTML::makeOption('11', _NOV), mosHTML::makeOption('12', _DEC));
		// месяца с правильным склонением
		$arr_2 = array(mosHTML::makeOption('01', _JAN_2), mosHTML::makeOption('02', _FEB_2), mosHTML::makeOption('03', _MAR_2), mosHTML::makeOption('04', _APR_2), mosHTML::makeOption('05', _MAY_2), mosHTML::makeOption('06', _JUN_2), mosHTML::makeOption('07', _JUL_2), mosHTML::makeOption('08', _AUG_2), mosHTML::makeOption('09', _SEP_2), mosHTML::makeOption('10', _OCT_2), mosHTML::makeOption('11', _NOV_2), mosHTML::makeOption('12', _DEC_2));
		$arr = $type ? $arr_2 : $arr_1;
		return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
	}

	public static function daySelectList($tag_name, $tag_attribs, $selected){
		$arr = array();

		for($i = 1; $i <= 31; $i++){
			$pref = '';
			if($i <= 9){
				$pref = '0';
			}
			$arr[] = mosHTML::makeOption($pref . $i, $pref . $i);
		}

		return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
	}

	public static function yearSelectList($tag_name, $tag_attribs, $selected, $min = 1900, $max = null){

		$max = ($max == null) ? date('Y', time()) : $max;

		$arr = array();
		for($i = $min; $i <= $max; $i++){
			$arr[] = mosHTML::makeOption($i, $i);
		}
		return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
	}

	public static function genderSelectList($tag_name, $tag_attribs, $selected){
		$arr = array(mosHTML::makeOption('no_gender', _GENDER_NONE), mosHTML::makeOption('male', _MALE), mosHTML::makeOption('female', _FEMALE));
		return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
	}

	/**
	 * Generates an HTML select list from a tree based query list
	 * @param array Source array with id and parent fields
	 * @param array The id of the current list item
	 * @param array Target array.  May be an empty array.
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @param mixed The key that is selected
	 * @returns string HTML for the select list
	 */
	public static function treeSelectList(&$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected){
		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach($src_list as $v){
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$ilist = mosTreeRecurse(0, '', array(), $children);

		// assemble menu items to the array
		$this_treename = '';
		foreach($ilist as $item){
			if($this_treename){
				if($item->id != $src_id && strpos($item->treename, $this_treename) === false){
					$tgt_list[] = mosHTML::makeOption($item->id, $item->treename);
				}
			} else{
				if($item->id != $src_id){
					$tgt_list[] = mosHTML::makeOption($item->id, $item->treename);
				} else{
					$this_treename = "$item->treename/";
				}
			}
		}
		// build the html select list
		return mosHTML::selectList($tgt_list, $tag_name, $tag_attribs, $key, $text, $selected);
	}

	/**
	 * Writes a yes/no select list
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @returns string HTML for the select list values
	 */
	public static function yesnoSelectList($tag_name, $tag_attribs, $selected, $yes = _YES, $no = _NO){
		$arr = array(mosHTML::makeOption('0', $no), mosHTML::makeOption('1', $yes),);

		return mosHTML::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
	}

	/**
	 * Generates an HTML radio list
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @returns string HTML for the select list
	 */
	public static function radioList(&$arr, $tag_name, $tag_attribs, $selected = null, $key = 'value', $text = 'text'){
		reset($arr);
		$html = '';
		for($i = 0, $n = count($arr); $i < $n; $i++){
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = (isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if(is_array($selected)){
				foreach($selected as $obj){
					$k2 = $obj->$key;
					if($k == $k2){
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else{
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"" . $k . "\"$extra $tag_attribs />";
			$html .= "\n\t<label for=\"$tag_name$k\">$t</label>";
		}
		$html .= "\n";

		return $html;
	}

	/**
	 * Writes a yes/no radio list
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @returns string HTML for the radio list
	 */
	public static function yesnoRadioList($tag_name, $tag_attribs, $selected, $yes = _YES, $no = _NO){
		$arr = array(mosHTML::makeOption('0', $no), mosHTML::makeOption('1', $yes));

		return mosHTML::radioList($arr, $tag_name, $tag_attribs, $selected);
	}

	/**
	 * @param int The row index
	 * @param int The record id
	 * @param boolean
	 * @param string The name of the form element
	 * @return string
	 */
	public static function idBox($rowNum, $recId, $checkedOut = false, $name = 'cid'){
		if($checkedOut){
			return '';
		} else{
			return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="isChecked(this.checked);" />';
		}
	}

	public static function sortIcon($base_href, $field, $state = 'none'){
		$alts = array('none' => _SORT_NONE, 'asc' => _SORT_ASC, 'desc' => _SORT_DESC,);
		$next_state = 'asc';
		if($state == 'asc'){
			$next_state = 'desc';
		} else if($state == 'desc'){
			$next_state = 'none';
		}

		$html = '<a href="' . $base_href . '&field=' . $field . '&order=' . $next_state . '"><img src="' . JPATH_SITE . '/' . JADMIN_BASE . '/images/sort_' . $state . '.png" width="12" height="12" border="0" alt="' . $alts[$next_state] . '" /></a>';
		return $html;
	}

	/**
	 * Writes Close Button
	 */
	public static function CloseButton(&$params, $hide_js = null){
		// displays close button in Pop-up window
		if($params->get('popup') && !$hide_js){
			?>
		<script language="javascript" type="text/javascript">
			<!--
			document.write('<div align="center" style="margin-top: 30px; margin-bottom: 30px;">');
			document.write('<a class="print_button" href="#" onclick="javascript:window.close();"><span class="small"><?php echo _PROMPT_CLOSE; ?></span></a>');
			document.write('</div>');
			//-->
		</script>
		<?php
		}
	}

	/**
	 * Writes Back Button
	 * Сыылка "Вернуться" отображается в следующих случаях:
	 * - не переданы параметры (если, например, нет необходимости проверять значения настроек, а нужно принудительно вывести ссылку);
	 * - параметры переданы и имеют соответствующие значения (используется в com_content)
	 * - параметры переданы, но настройка `back_button` не задана (т.е. должно использоваться глобальное значение параметра)
	 *     и в глобальных настройках включено отображение ссылки

	 */
	public static function BackButton(&$params = null, $hide_js = null){
		$config = Jconfig::getInstance();

		if(!$params || ($params->get('back_button') == 1 && !$params->get('popup') && !$hide_js) || ($params->get('back_button') == -1 && $config->config_back_button == 1)){
			include_once(JPATH_BASE . '/templates/system/back_button.php');
		} else{
			return false;
		}
	}

	function get_image($file, $directory = 'system', $front = 0){

		if(!$front){
			$path = '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/' . $directory . '/';
		} else{
			$path = '/templates/' . JTEMPLATE . '/images/elements/';
		}

		$image = '';
		if(is_file(JPATH_BASE . $path . $file)){
			$image = JPATH_SITE . $path . $file;
		} elseif(is_file(JPATH_BASE . DS . $directory . DS . $file)){
			$image = JPATH_SITE . '/' . $directory . '/' . $file;
		}
		if($image){
			$image = '<img src="' . $image . '" alt="" border="0" />';
			return $image;
		}
		return false;
	}

	/**
	 * Cleans text of all formating and scripting code
	 */
	public static function cleanText(&$text){
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		//$text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is','\2 (\1)',$text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace('/&nbsp;/', ' ', $text);
		$text = preg_replace('/&amp;/', ' ', $text);
		$text = preg_replace('/&quot;/', ' ', $text);
		$text = strip_tags($text);
		$text = htmlspecialchars($text, null, 'UTF-8');
		return $text;
	}

	/**
	 * Вывод значка печати, встроен хак индексации печатной версии
	 */
	public static function PrintIcon($row, &$params, $hide_js, $link, $status = null){
		global $cpr_i;

		if(!isset($cpr_i)){
			$cpr_i = '';
		}

		if($params->get('print') && !$hide_js){
			// use default settings if none declared
			if(!$status){
				$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			}
			// checks template image directory for image, if non found default are loaded
			if($params->get('icons')){
				$image = mosAdminMenus::ImageCheck('printButton.png', '/images/M_images/', null, null, _PRINT, 'print' . $cpr_i);
				$cpr_i++;
			} else{
				$image = _ICON_SEP . '&nbsp;' . _PRINT . '&nbsp;' . _ICON_SEP;
			}
			if($params->get('popup') && !$hide_js){
				?>
			<script language="javascript" type="text/javascript">
				<!--
				document.write('<a href="#" class="print_button" onclick="javascript:window.print(); return false;" title="<?php echo _PRINT; ?>">');
				document.write('<?php echo $image; ?>');
				document.write('</a>');
				//-->
			</script>
			<?php
			} else{
				?>
			<?php if(!Jconfig::getInstance()->config_index_print){ ?>
				<span style="display:none"><![CDATA[<noindex>]]></span><a href="#" rel="nofollow" target="_blank" onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>'); return false;"
																		  title="<?php echo _PRINT; ?>"><?php echo $image; ?></a><span style="display:none"><![CDATA[</noindex>]]></span>
				<?php } else{ ?>
				<a href="<?php echo $link; ?>" target="_blank" title="<?php echo _PRINT; ?>"><?php echo $image; ?></a>
				<?php
				}
				; ?>

			<?php
			}
		}
	}

	/**
	 * simple Javascript Cloaking
	 * email cloacking
	 * by default replaces an email with a mailto link with email cloacked
	 */
	public static function emailCloaking($mail, $mailto = 1, $text = '', $email = 1){
		// convert text
		$mail = mosHTML::encoding_converter($mail);
		// split email by @ symbol
		$mail = explode('@', $mail);
		$mail_parts = explode('.', $mail[1]);
		// random number
		$rand = rand(1, 100000);
		$replacement = "\n <script language='JavaScript' type='text/javascript'>";
		$replacement .= "\n <!--";
		$replacement .= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
		$replacement .= "\n var path = 'hr' + 'ef' + '=';";
		$replacement .= "\n var addy" . $rand . " = '" . @$mail[0] . "' + '&#64;';";
		$replacement .= "\n addy" . $rand . " = addy" . $rand . " + '" . implode("' + '&#46;' + '", $mail_parts) . "';";
		if($mailto){
			// special handling when mail text is different from mail addy
			if($text){
				if($email){
					// convert text
					$text = mosHTML::encoding_converter($text);
					// split email by @ symbol
					$text = explode('@', $text);
					$text_parts = explode('.', $text[1]);
					$replacement .= "\n var addy_text" . $rand . " = '" . @$text[0] . "' + '&#64;' + '" . implode("' + '&#46;' + '", @$text_parts) . "';";
				} else{
					$replacement .= "\n var addy_text" . $rand . " = '" . $text . "';";
				}
				$replacement .= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>' );";
				$replacement .= "\n document.write( addy_text" . $rand . " );";
				$replacement .= "\n document.write( '<\/a>' );";
			} else{
				$replacement .= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>' );";
				$replacement .= "\n document.write( addy" . $rand . " );";
				$replacement .= "\n document.write( '<\/a>' );";
			}
		} else{
			$replacement .= "\n document.write( addy" . $rand . " );";
		}
		$replacement .= "\n //-->";
		$replacement .= '\n </script>';
		// XHTML compliance `No Javascript` text handling
		$replacement .= "<script language='JavaScript' type='text/javascript'>";
		$replacement .= "\n <!--";
		$replacement .= "\n document.write( '<span style=\'display: none;\'>' );";
		$replacement .= "\n //-->";
		$replacement .= "\n </script>";
		$replacement .= _CLOAKING;
		$replacement .= "\n <script language='JavaScript' type='text/javascript'>";
		$replacement .= "\n <!--";
		$replacement .= "\n document.write( '</' );";
		$replacement .= "\n document.write( 'span>' );";
		$replacement .= "\n //-->";
		$replacement .= "\n </script>";

		return $replacement;
	}

	public static function encoding_converter($text){
		// replace vowels with character encoding
		$text = str_replace('a', '&#97;', $text);
		$text = str_replace('e', '&#101;', $text);
		$text = str_replace('i', '&#105;', $text);
		$text = str_replace('o', '&#111;', $text);
		$text = str_replace('u', '&#117;', $text);
		return $text;
	}

}

// класс работы с пользователями
require_once(JPATH_BASE . '/components/com_users/users.class.php');

/**
 * Utility function to return a value from a named array or a specified default
 * @param array A named array
 * @param string The key to search for
 * @param mixed The default value to give if no key found
 * @param int An options mask: _MOS_NOTRIM prevents trim, _MOS_ALLOWHTML allows safe html, _MOS_ALLOWRAW allows raw input
 */
define("_MOS_NOTRIM", 0x0001);
define("_MOS_ALLOWHTML", 0x0002);
define("_MOS_ALLOWRAW", 0x0004);

function mosGetParam(&$arr, $name, $def = null, $mask = 0){
	static $noHtmlFilter = null;

	$return = null;
	if(isset($arr[$name])){
		$return = $arr[$name];

		if(is_string($return)){
			// trim data
			if(!($mask & _MOS_NOTRIM)){
				$return = trim($return);
			}

			if($mask & _MOS_ALLOWRAW){
				// do nothing
			} else if($mask & _MOS_ALLOWHTML){
				// do nothing - compatibility mode
			} else{
				// send to inputfilter
				if(is_null($noHtmlFilter)){
					$noHtmlFilter = new InputFilter( /* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
				}
				$return = $noHtmlFilter->process($return);

				if(!empty($return) && is_numeric($def)){
					// if value is defined and default value is numeric set variable type to integer
					$return = intval($return);
				}
			}

			// account for magic quotes setting
			if(!get_magic_quotes_gpc()){
				$return = addslashes($return);
			}
		}

		return $return;
	} else{
		return $def;
	}
}

/**
 * Strip slashes from strings or arrays of strings
 * @param mixed The input string or array
 * @return mixed String or array stripped of slashes
 */
function mosStripslashes(&$value){
	$ret = '';
	if(is_string($value)){
		$ret = stripslashes($value);
	} else{
		if(is_array($value)){
			$ret = array();
			foreach($value as $key => $val){
				$ret[$key] = mosStripslashes($val);
			}
		} else{
			$ret = $value;
		}
	}
	return $ret;
}

/**
 * Copy the named array content into the object as properties
 * only existing properties of object are filled. when undefined in hash, properties wont be deleted
 * @param array the input array
 * @param obj byref the object to fill of any class
 * @param string
 * @param boolean
 */
function mosBindArrayToObject($array, &$obj, $ignore = '', $prefix = null, $checkSlashes = true){
	if(!is_array($array) || !is_object($obj)){
		return (false);
	}
	$ignore = ' ' . $ignore . ' ';
	foreach(get_object_vars($obj) as $k => $v){
		if(substr($k, 0, 1) != '_'){ // internal attributes of an object are ignored
			if(strpos($ignore, ' ' . $k . ' ') === false){
				if($prefix){
					$ak = $prefix . $k;
				} else{
					$ak = $k;
				}
				if(isset($array[$ak])){
					$obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? mosStripslashes($array[$ak]) : $array[$ak];
				}
			}
		}
	}
	return true;
}

/**
 * Utility function to read the files in a directory
 * @param string The file system path
 * @param string A filter for the names
 * @param boolean Recurse search into sub-directories
 * @param boolean True if to prepend the full path to the file name
 */
function mosReadDirectory($path, $filter = '.', $recurse = false, $fullpath = false){
	$arr = array();
	if(!@is_dir($path)){
		return $arr;
	}
	$handle = opendir($path);

	while($file = readdir($handle)){
		$dir = mosPathName($path . '/' . $file, false);
		$isDir = is_dir($dir);
		if(($file != ".") && ($file != "..")){
			if(preg_match("/$filter/", $file)){
				if($fullpath){
					$arr[] = trim(mosPathName($path . '/' . $file, false));
				} else{
					$arr[] = trim($file);
				}
			}
			if($recurse && $isDir){
				$arr2 = mosReadDirectory($dir, $filter, $recurse, $fullpath);
				$arr = array_merge($arr, $arr2);
			}
		}
	}
	closedir($handle);
	asort($arr);
	return $arr;
}

/**
 * Utility function redirect the browser location to another url
 * Can optionally provide a message.
 * @param string The file system path
 * @param string A filter for the names
 */
function mosRedirect($url, $msg = ''){
	// specific filters
	$iFilter = new InputFilter();
	$url = $iFilter->process($url);
	if(!empty($msg)){
		$msg = $iFilter->process($msg);
		$mainframe = mosMainFrame::getInstance();
		$mainframe->set_mosmsg($msg);
	}

	// Strip out any line breaks and throw away the rest
	$url = preg_split("/[\r\n]/", $url);
	$url = $url[0];
	if($iFilter->badAttributeValue(array('href', $url))){
		$url = JPATH_SITE;
	}

	if(headers_sent()){
		echo "<script>document.location.href='$url';</script>\n";
	} else{
		@ob_end_clean(); // clear output buffer
		header('HTTP/1.1 301 Moved Permanently');
		header("Location: " . $url);
	}
	exit();
}

function mosErrorAlert($text, $action = 'window.history.go(-1);', $mode = 1){
	$text = nl2br($text);
	$text = addslashes($text);
	$text = strip_tags($text);

	switch($mode){
		case 2:
			echo "<script>$action</script> \n";
			break;

		case 1:
		default:
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; " . _ISO . "\" />";
			echo "<script>alert('$text'); $action</script> \n";
			break;
	}

	exit;
}

function mosTreeRecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1){

	if(@$children[$id] && $level <= $maxlevel){
		foreach($children[$id] as $v){
			$id = $v->id;

			if($type){
				$pre = '<sup>L</sup>&nbsp;';
				$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			} else{
				$pre = '- ';
				$spacer = '&nbsp;&nbsp;';
			}

			if($v->parent == 0){
				$txt = $v->name;
			} else{
				$txt = $pre . $v->name;
			}

			$list[$id] = $v;
			$list[$id]->treename = $indent . $txt;
			$list[$id]->children = count(@$children[$id]);

			$list = mosTreeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
		}
	}
	return $list;
}

/**
 * Function to strip additional / or \ in a path name
 * @param string The path
 * @param boolean Add trailing slash
 */
function mosPathName($p_path, $p_addtrailingslash = true){
	$retval = '';

	$isWin = (substr(PHP_OS, 0, 3) == 'WIN');

	if($isWin){
		$retval = str_replace('/', '\\', $p_path);
		if($p_addtrailingslash){
			if(substr($retval, -1) != '\\'){
				$retval .= '\\';
			}
		}

		// Check if UNC path
		$unc = substr($retval, 0, 2) == '\\\\' ? 1 : 0;

		// Remove double \\
		$retval = str_replace('\\\\', '\\', $retval);

		// If UNC path, we have to add one \ in front or everything breaks!
		if($unc == 1){
			$retval = '\\' . $retval;
		}
	} else{
		$retval = str_replace('\\', '/', $p_path);
		if($p_addtrailingslash){
			if(substr($retval, -1) != '/'){
				$retval .= '/';
			}
		}

		// Check if UNC path
		$unc = substr($retval, 0, 2) == '//' ? 1 : 0;

		// Remove double //
		$retval = str_replace('//', '/', $retval);

		// If UNC path, we have to add one / in front or everything breaks!
		if($unc == 1){
			$retval = '/' . $retval;
		}
	}

	return $retval;
}

function mosObjectToArray($p_obj){
	$retarray = null;
	if(is_object($p_obj)){
		$retarray = array();
		foreach(get_object_vars($p_obj) as $k => $v){
			if(is_object($v))
				$retarray[$k] = mosObjectToArray($v); else
				$retarray[$k] = $v;
		}
	}
	return $retarray;
}

/**
 * Checks the user agent string against known browsers
 */
function mosGetBrowser($agent){
	mosMainFrame::addLib('phpSniff');
	$client = new phpSniff($agent);

	$client_long_name = $client->property('long_name');
	if(array_key_exists($client_long_name, $client->browsersAlias)){
		$name = $client->browsersAlias[$client_long_name];
	} else{
		$name = $client_long_name;
	}
	$name .= ' ' . $client->property('version');
	return ($name);
}

/**
 * Checks the user agent string against known operating systems
 */
function mosGetOS($agent){
	mosMainFrame::addLib('phpSniff');
	$client = new phpSniff($agent);
	$osSearchOrder = $client->osSearchOrder;

	foreach($osSearchOrder as $key){
		if(preg_match("/$key/i", $agent)){
			return $client->osAlias[$key];
			break;
		}
	}
	return 'Unknown';
}

/**
 * @param string SQL with ordering As value and 'name field' AS text
 * @param integer The length of the truncated headline
 */
function mosGetOrderingList($sql, $chop = '30'){
	$database = database::getInstance();

	$order = array();
	$database->setQuery($sql);
	if(!($orders = $database->loadObjectList())){
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		} else{
			$order[] = mosHTML::makeOption(1, _FIRST);
			return $order;
		}
	}
	$order[] = mosHTML::makeOption(0, '0 ' . _FIRST);
	for($i = 0, $n = count($orders); $i < $n; $i++){
		if(strlen($orders[$i]->text) > $chop){
			$text = Jstring::substr($orders[$i]->text, 0, $chop) . "...";
		} else{
			$text = $orders[$i]->text;
		}
		$order[] = mosHTML::makeOption($orders[$i]->value, $orders[$i]->value . ' (' . $text . ')');
	}
	$order[] = mosHTML::makeOption($orders[$i - 1]->value + 1, ($orders[$i - 1]->value + 1) . ' ' . _LAST);
	return $order;
}

/**
 * Makes a variable safe to display in forms
 * Object parameters that are non-string, array, object or start with underscore
 * will be converted
 * @param              object An object to be parsed
 * @param              int The optional quote style for the htmlspecialchars function
 * @param string|array An optional single field name or array of field names not
 * to be parsed (eg, for a textarea)
 */
function mosMakeHtmlSafe(&$mixed, $quote_style = ENT_QUOTES, $exclude_keys = ''){
	if(is_object($mixed)){
		foreach(get_object_vars($mixed) as $k => $v){
			if(is_array($v) || is_object($v) || $v == null || substr($k, 1, 1) == '_'){
				continue;
			}
			if(is_string($exclude_keys) && $k == $exclude_keys){
				continue;
			} else if(is_array($exclude_keys) && in_array($k, $exclude_keys)){
				continue;
			}
			$mixed->$k = htmlspecialchars($v, $quote_style);
		}
	}
}

/**
 * Checks whether a menu option is within the users access level
 * @param int Item id number
 * @param string The menu option
 * @param int The users group ID number
 * @param database A database connector object
 * @return boolean True if the visitor's group at least equal to the menu access
 */
function mosMenuCheck($menu_option, $task, $gid, $mainframe){

	$results = array();
	$access = 0;

		$database = database::getInstance();
		$dblink = "index.php?option=" . $database->getEscaped($menu_option, true);
		if($task != ''){
			$dblink .= "&task=" . $database->getEscaped($task, true);
		}
		$query = "SELECT* FROM #__menu WHERE published = 1 AND link LIKE '$dblink%'";
		$database->setQuery($query);
		$results = $database->loadObjectList();
		foreach($results as $result){
			$access = max($access, $result->access);
		}

	// save menu information to global mainframe
	if(isset($results[0])){
		$mainframe->set('menu', $results[0]);
	} else{
		// loads empty Menu info
		$mainframe->set('menu', new mosMenu($database));
	}
	return ($access <= $gid);
}

/**
 * Returns formated date according to current local and adds time offset
 * @param string date in datetime format
 * @param string format optional format for strftime
 * @param offset time offset if different than global one
 * @returns formated date
 */
function mosFormatDate($date, $format = '', $offset = null){
	static $config_offset;

	if(!isset($config_offset)){
		$config_offset = Jconfig::getInstance()->config_offset;
	}

	if($date == '0000-00-00 00:00:00')
		return $date; //database::$_nullDate - при ошибках парсера

	if($format == ''){
		// %Y-%m-%d %H:%M:%S
		$format = _DATE_FORMAT_LC;
	}
	if(is_null($offset)){
		$offset = $config_offset;
	}
	if($date && preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs)){
		$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		$date = strftime($format, $date + ($offset * 60 * 60));
	}

	return $date;
}

/**
 * Returns current date according to current local and time offset
 * @param string format optional format for strftime
 * @returns current date
 */
function mosCurrentDate($format = ""){
	static $config_offset;

	if(!isset($config_offset)){
		$config_offset = Jconfig::getInstance()->config_offset;
	}

	if($format == ''){
		$format = _DATE_FORMAT_LC;
	}

	$date = strftime($format, time() + ($config_offset * 60 * 60));
	return $date;
}

/**
 * Utility function to provide ToolTips
 * @param string ToolTip text
 * @param string Box title
 * @returns HTML code for ToolTip
 */
function mosToolTip($tooltip, $title = '', $width = '', $image = 'tooltip.png', $text = '', $href = '#', $link = 1){

	if($width){
		$width = ', WIDTH, \'' . $width . '\'';
	}
	if($title){
		$title = ', CAPTION, \'' . $title . '\'';
	}
	if(!$text){
		$image = JPATH_SITE . '/includes/js/ThemeOffice/' . $image;
		$text = '<img src="' . $image . '" border="0" alt="tooltip"/>';
	}
	$style = 'style="text-decoration: none; color: #333;"';
	if($href){
		$style = '';
	} else{
		$href = '#';
	}

	$mousover = 'return overlib(\'' . $tooltip . '\'' . $title . ', BELOW, RIGHT' . $width . ');';

	$tip = "";
	if($link){
		$tip .= '<a href="' . $href . '" onmouseover="' . $mousover . '" onmouseout="return nd();" ' . $style . '>' . $text . '</a>';
	} else{
		$tip .= '<span onmouseover="' . $mousover . '" onmouseout="return nd();" ' . $style . '>' . $text . '</span>';
	}

	return $tip;
}

/**
 * Utility function to provide Warning Icons
 * @param string Warning text
 * @param string Box title
 * @returns HTML code for Warning
 */
function mosWarning($warning, $title = _MOS_WARNING){
	$mouseover = 'return overlib(\'' . $warning . '\', CAPTION, \'' . $title . '\', BELOW, RIGHT);';
	$tip = '<a href="javascript: void(0)" onmouseover="' . $mouseover . '" onmouseout="return nd();">';
	$tip .= '<img src="' . JPATH_SITE . '/includes/js/ThemeOffice/warning.png" border="0" alt="' . _WARNING . '"/></a>';
	return $tip;
}

function mosCreateGUID(){
	srand((double)microtime() * 1000000);
	$r = rand();
	$u = uniqid(getmypid() . $r . (double)microtime() * 1000000, 1);
	$m = md5($u);
	return ($m);
}

function mosCompressID($ID){
	return (Base64_encode(pack("H*", $ID)));
}

function mosExpandID($ID){
	return (implode(unpack("H*", Base64_decode($ID)), ''));
}

/**
 * Function to create a mail object for futher use (uses phpMailer)
 * @param string From e-mail address
 * @param string From name
 * @param string E-mail subject
 * @param string Message body
 * @return object Mail object
 */
function mosCreateMail($from = '', $fromname = '', $subject = '', $body = ''){

	mosMainFrame::addLib('phpmailer');
	$mail = new mosPHPMailer();

	$config = Jconfig::getInstance();

	$mail->PluginDir = JPATH_BASE . DS . 'includes/libraries/phpmailer/';
	$mail->SetLanguage(_LANGUAGE, JPATH_BASE . DS . 'includes/libraries/phpmailer/language/');
	$mail->CharSet = substr_replace(_ISO, '', 0, 8);
	$mail->IsMail();
	$mail->From = $from ? $from : $config->config_mailfrom;
	$mail->FromName = $fromname ? $fromname : $config->config_fromname;
	$mail->Mailer = $config->config_mailer;

	// Add smtp values if needed
	if($config->config_mailer == 'smtp'){
		$mail->SMTPAuth = $config->config_smtpauth;
		$mail->Username = $config->config_smtpuser;
		$mail->Password = $config->config_smtppass;
		$mail->Host = $config->config_smtphost;
	} else // Set sendmail path
		if($config->config_mailer == 'sendmail'){
			if(isset($config->config_sendmail))
				$mail->Sendmail = $config->config_sendmail;
		} // if

	$mail->Subject = $subject;
	$mail->Body = $body;

	return $mail;
}

/**
 * Mail function (uses phpMailer)
 * @param string From e-mail address
 * @param string From name
 * @param string/array Recipient e-mail address(es)
 * @param string E-mail subject
 * @param string Message body
 * @param boolean false = plain text, true = HTML
 * @param string/array CC e-mail address(es)
 * @param string/array BCC e-mail address(es)
 * @param string/array Attachment file name(s)
 * @param string/array ReplyTo e-mail address(es)
 * @param string/array ReplyTo name(s)
 * @return boolean
 */
function mosMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = null, $bcc = null, $attachment = null, $replyto = null, $replytoname = null){
	$config = Jconfig::getInstance();

	// Allow empty $from and $fromname settings (backwards compatibility)
	if($from == ''){
		$from = $config->config_mailfrom;
	}
	if($fromname == ''){
		$fromname = $config->config_fromname;
	}

	// Filter from, fromname and subject
	/** @noinspection PhpDeprecationInspection */
	if(!JosIsValidEmail($from) || !JosIsValidName($fromname) || !JosIsValidName($subject)){
		return false;
	}

	$mail = mosCreateMail($from, $fromname, $subject, $body);

	// activate HTML formatted emails
	if($mode){
		$mail->IsHTML(true);
	}

	if(is_array($recipient)){
		foreach($recipient as $to){
			if(!JosIsValidEmail($to)){
				return false;
			}
			$mail->AddAddress($to);
		}
	} else{
		if(!JosIsValidEmail($recipient)){
			return false;
		}
		$mail->AddAddress($recipient);
	}
	if(isset($cc)){
		if(is_array($cc)){
			foreach($cc as $to){
				if(!JosIsValidEmail($to)){
					return false;
				}
				$mail->AddCC($to);
			}
		} else{
			if(!JosIsValidEmail($cc)){
				return false;
			}
			$mail->AddCC($cc);
		}
	}
	if(isset($bcc)){
		if(is_array($bcc)){
			foreach($bcc as $to){
				if(!JosIsValidEmail($to)){
					return false;
				}
				$mail->AddBCC($to);
			}
		} else{
			if(!JosIsValidEmail($bcc)){
				return false;
			}
			$mail->AddBCC($bcc);
		}
	}
	if($attachment){
		if(is_array($attachment)){
			foreach($attachment as $fname){
				$mail->AddAttachment($fname);
			}
		} else{
			$mail->AddAttachment($attachment);
		}
	}
	//Important for being able to use mosMail without spoofing...
	if($replyto){
		if(is_array($replyto)){
			reset($replytoname);
			foreach($replyto as $to){
				$toname = ((list($key, $value) = each($replytoname)) ? $value : '');
				if(!JosIsValidName($toname) or !JosIsValidEmail($to)){
					return false;
				}
				$mail->AddReplyTo($to, $toname);
			}
		} else{
			if(!JosIsValidEmail($replyto) || !JosIsValidName($replytoname)){
				return false;
			}
			$mail->AddReplyTo($replyto, $replytoname);
		}
	}
	$mailssend = $mail->Send();
	if($config->config_debug){
		//$mosDebug->message( "Письма отправлены: $mailssend");
	}
	if($mail->error_count > 0){
		//$mosDebug->message( "The mail message $fromname <$from> about $subject to $recipient <b>failed</b><br /><pre>$body</pre>", false );
		//$mosDebug->message( "Mailer Error: " . $mail->ErrorInfo . "" );
	}
	return $mailssend;
}

// mosMail

/**
 * Checks if a given string is a valid email address
 * @param    string    $email    String to check for a valid email address
 * @return    boolean
 */
function JosIsValidEmail($email){
	$valid = preg_match('/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email);
	return $valid;
}

/**
 * Checks if a given string is a valid (from-)name or subject for an email
 * @since        1.0.11
 * @deprecated    1.5
 * @param        string        $string        String to check for validity
 * @return        boolean
 */
function JosIsValidName($string){
	/*
	 * The following regular expression blocks all strings containing any low control characters:
	 * 0x00-0x1F, 0x7F
	 * These should be control characters in almost all used charsets.
	 * The high control chars in ISO-8859-n (0x80-0x9F) are unused (e.g. http://en.wikipedia.org/wiki/ISO_8859-1)
	 * Since they are valid UTF-8 bytes (e.g. used as the second byte of a two byte char),
	 * they must not be filtered.
	 */
	$invalid = preg_match('/[\x00-\x1F\x7F]/', $string);
	if($invalid){
		return false;
	} else{
		return true;
	}
}

/**
 * Initialise GZIP
 */
function initGzip(){
	global $do_gzip_compress;

	$do_gzip_compress = false;

	if(Jconfig::getInstance()->config_gzip == 1){
		$phpver = phpversion();
		$useragent = mosGetParam($_SERVER, 'HTTP_USER_AGENT', '');
		$canZip = mosGetParam($_SERVER, 'HTTP_ACCEPT_ENCODING', '');
		$gzip_check = 0;
		$zlib_check = 0;
		$gz_check = 0;
		$zlibO_check = 0;
		$sid_check = 0;
		if(strpos($canZip, 'gzip') !== false){
			$gzip_check = 1;
		}
		if(extension_loaded('zlib')){
			$zlib_check = 1;
		}
		if(function_exists('ob_gzhandler')){
			$gz_check = 1;
		}
		if(ini_get('zlib.output_compression')){
			$zlibO_check = 1;
		}
		if(ini_get('session.use_trans_sid')){
			$sid_check = 1;
		}
		if($phpver >= '4.0.4pl1' && (strpos($useragent, 'compatible') !== false || strpos($useragent, 'Gecko') !== false)){
			if(($gzip_check || isset($_SERVER['---------------'])) && $zlib_check && $gz_check && !$zlibO_check && !$sid_check){
				ob_start('ob_gzhandler');
				return;
			}
		} elseif($phpver > '4.0'){
			if($gzip_check){
				if($zlib_check){
					$do_gzip_compress = true;
					ob_start();
					ob_implicit_flush(0);
					header('Content-Encoding: gzip');
					return;
				}
			}
		}
	}
	ob_start();
}

/**
 * Perform GZIP
 */
function doGzip(){
	global $do_gzip_compress;

	if($do_gzip_compress){
		$gzip_contents = ob_get_contents();
		ob_end_clean();
		$gzip_size = strlen($gzip_contents);
		$gzip_crc = crc32($gzip_contents);
		$gzip_contents = gzcompress($gzip_contents, 9);
		$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);
		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		echo $gzip_contents;
		echo pack('V', $gzip_crc);
		echo pack('V', $gzip_size);
	} else{
		ob_end_flush();
	}
}

/**
 * Random password generator
 * @return password
 */
function mosMakePassword($length = 8){
	$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$makepass = '';
	mt_srand(10000000 * (double)microtime());
	for($i = 0; $i < $length; $i++)
		$makepass .= $salt[mt_rand(0, 61)];
	return $makepass;
}

if(!function_exists('html_entity_decode')){

	/**
	 * html_entity_decode function for backward compatability in PHP
	 * @param string
	 * @param string
	 */
	function html_entity_decode($string, $opt = ENT_COMPAT){
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		if($opt & 1){ // Translating single quotes
			$trans_tbl["&apos;"] = "'";
		}
		if(!($opt & 2)){ // Not translating double quotes
			unset($trans_tbl["&quot;"]);
		}
		return strtr($string, $trans_tbl);
	}

}

/**
 * Plugin handler
 * @package Joostina
 */
class mosMambotHandler{

	/**
	@var array An array of functions in event groups */
	var $_events = null;
	/**
	@var array An array of lists */
	var $_lists = null;
	/**
	@var array An array of mambots */
	var $_bots = null;
	/**
	@var int Index of the mambot being loaded */
	var $_loading = null;
	/**
	@var array An array of the content mambots in the system */
	var $_content_mambots = null;
	/**
	@var array An array of the content mambot params */
	var $_content_mambot_params = array();
	/**
	@var array An array of the search mambot params */
	var $_search_mambot_params = array();
	/**
	 * @var array An array of the  mambot params
	 */
	var $_mambot_params = array();
	var $_config = null;
	var $_db = null;

	/**
	 * The Singleton instance of the class
	 * @var mosMainframe
	 */
	private static $_instance = null;

	/**
	 * Constructor
	 */
	function mosMambotHandler(){
		$this->_db = database::getInstance();
		$config = Jconfig::getInstance();
		$this->_config = array('config_disable_access_control' => $config->config_disable_access_control, 'config_use_unpublished_mambots' => $config->config_use_unpublished_mambots);
		$this->_events = array();
		unset($config);
	}

	public static function &getInstance(){

		if(!is_object(self::$_instance)){
			self::$_instance = new mosMambotHandler();
		}

		return self::$_instance;
	}

	/**
	 * Loads all the bot files for a particular group
	 * @param string The group name, relates to the sub-directory in the mambots directory
	 */
	function loadBotGroup($group, $load = 0){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();

		$config = $this->_config;
		$database = $this->_db;

		if(is_object($my)){
			$gid = $my->gid;
		} else{
			$gid = 0;
		}
		$group = trim($group);

		$where_ac = ($config['config_disable_access_control'] == 0) ? ' AND access <= ' . (int)$gid : '';

		switch($group){
			case 'content':
				if(!defined('_JOS_CONTENT_MAMBOTS')){
					/** ensure that query is only called once */
					define('_JOS_CONTENT_MAMBOTS', 1);
					$where_ac_2 = $where_ac . ($config['config_use_unpublished_mambots'] == 1) ? ' published=1 AND' : '';
					$query = 'SELECT folder, element, published, params FROM #__mambots WHERE ' . $where_ac_2 . ' folder = \'content\' AND client_id=0 ORDER BY ordering ASC';
					$database->setQuery($query);
					// load query into class variable _content_mambots
					if(!($this->_content_mambots = $database->loadObjectList())){
						return false;
					}
					foreach($this->_content_mambots as $bot){
						$this->_content_mambot_params[$bot->element] = new stdClass();
						$this->_content_mambot_params[$bot->element]->params = $bot->params;
					}
				}
				// pull bots to be processed from class variable
				$bots = $this->_content_mambots;
				break;

			case 'search':
				if(!defined('_JOS_SEARCH_MAMBOTS')){
					define('_JOS_SEARCH_MAMBOTS', 1);

					$query = 'SELECT folder, element, published, params FROM #__mambots WHERE published = 1' . $where_ac . ' AND folder = \'search\' ORDER BY ordering ASC';
					$database->setQuery($query);
					if(!($this->_search_mambot = $database->loadObjectList())){
						return false;
					}

					foreach($this->_search_mambot as $bot){
						if(!isset($this->_search_mambot_params[str_replace('.searchbot', '', $bot->element)]->params)){
							$this->_search_mambot_params[str_replace('.searchbot', '', $bot->element)] = new stdClass;
						}
						$this->_search_mambot_params[str_replace('.searchbot', '', $bot->element)]->params = $bot->params;
					}
				}

				// pull bots to be processed from class variable
				$bots = $this->_search_mambot;
				break;

			default:
				$query = 'SELECT folder, element, published, params FROM #__mambots WHERE published = 1' . $where_ac . ' AND folder = ' . $database->Quote($group) . ' AND client_id=0 ORDER BY ordering ASC';
				$database->setQuery($query);
				if(!($bots = $database->loadObjectList())){
					return false;
				}
				break;
		}

		// load bots found by queries
		$n = count($bots);
		for($i = 0; $i < $n; $i++){
			$this->loadBot($bots[$i]->folder, $bots[$i]->element, $bots[$i]->published, $bots[$i]->params);
		}

		if(!$load){
			return true;
		} else{
			return $bots;
		}
	}

	/**
	 * Loads the bot file
	 * @param string The folder (group)
	 * @param string The elements (name of file without extension)
	 * @param int Published state
	 * @param string The params for the bot
	 */
	function loadBot($folder, $element, $published, $params = ''){
		global $_MAMBOTS;

		$path_bot = JPATH_BASE . DS . 'mambots';

		$path = $path_bot . DS . $folder . DS . $element . '.php';
		if(file_exists($path)){
			$this->_loading = count($this->_bots);
			$bot = new stdClass;
			$bot->folder = $folder;
			$bot->element = $element;
			$bot->published = $published;
			$bot->lookup = $folder . DS . $element;
			$bot->params = $params;
			$this->_bots[] = $bot;
			$this->_mambot_params[$element] = $params;
			$lang = mosMainFrame::getLangFile('bot_' . $element);
			if($lang){
				include_once($lang);
			}
			require_once ($path);
			$this->_loading = null;
		}
		return true;
	}

	/**
	 * Registers a function to a particular event group
	 * @param string The event name
	 * @param string The function name
	 */
	function registerFunction($event, $function){
		$this->_events[$event][] = array($function, $this->_loading);
	}

	/**
	 * Makes a option for a particular list in a group
	 * @param string The group name
	 * @param string The list name
	 * @param string The value for the list option
	 * @param string The text for the list option
	 */
	function addListOption($group, $listName, $value, $text = ''){
		$this->_lists[$group][$listName][] = mosHTML::makeOption($value, $text);
	}

	/**
	 * @param string The group name
	 * @param string The list name
	 * @return array
	 */
	function getList($group, $listName){
		return $this->_lists[$group][$listName];
	}

	/**
	 * Calls all functions associated with an event group
	 * @param string The event name
	 * @param array An array of arguments
	 * @param boolean True is unpublished bots are to be processed
	 * @return array An array of results from each function call
	 */
	function trigger($event, $args = null, $doUnpublished = false){
		$result = array();
		if($args === null){
			$args = array();
		}
		if($doUnpublished){
			// prepend the published argument
			array_unshift($args, null);
		}
		if(isset($this->_events[$event])){
			foreach($this->_events[$event] as $func){
				if(function_exists($func[0])){
					if($doUnpublished){
						$args[0] = $this->_bots[$func[1]]->published;
						$result[] = call_user_func_array($func[0], $args);
					} elseif($this->_bots[$func[1]]->published){
						$result[] = call_user_func_array($func[0], $args);
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Same as trigger but only returns the first event and
	 * allows for a variable argument list
	 * @param string The event name
	 * @return array The result of the first function call
	 */
	function call($event){
		$args = &func_get_args();
		array_shift($args);
		if(isset($this->_events[$event])){
			foreach($this->_events[$event] as $func){
				if(function_exists($func[0])){
					if($this->_bots[$func[1]]->published){
						return call_user_func_array($func[0], array(&$args));
					}
				}
			}
		}
		return null;
	}

	//Адресный вызов мамбота
	function call_mambot($event, $element, $args){

		if(isset($this->_events[$event])){
			foreach($this->_events[$event] as $func){
				if($this->_bots[$func[1]]->element == $element && function_exists($func[0])){
					$this->_mambot_params[$element] = $this->_bots[$func[1]]->params;
					if($this->_bots[$func[1]]->published){
						return call_user_func_array($func[0], array(&$args));
					}
				}
			}
		}
		return null;
	}

}

/**
 * Создание табов
 * @package Joostina
 */
class mosTabs{

	/**
	@var int Use cookies */
	var $useCookies = 0;

	/**
	 * Constructor
	 * Includes files needed for displaying tabs and sets cookie options
	 * @param int useCookies, if set to 1 cookie will hold last used tab between page refreshes
	 */
	function mosTabs($useCookies, $xhtml = 0){
		$mainframe = mosMainFrame::getInstance();
		$config = $mainframe->get('config');

		// активация gzip сжатия css и js файлов
		if($config->config_gz_js_css){
			$css_f = 'joostina.tabs.css.php';
			$js_f = 'joostina.tabs.js.php';
		} else{
			$css_f = 'tabpane.css';
			$js_f = 'tabpane.js';
		}

		$r_dir = '';
		if($mainframe->isAdmin() == 1){
			$r_dir = '/' . JADMIN_BASE;
		}
		$css_dir = $r_dir . '/templates/' . JTEMPLATE . '/css';
		if(!is_file(JPATH_BASE . DS . $css_dir . DS . $css_f)){
			$css_dir = '/includes/js/tabs';
		}

		$css = '<link rel="stylesheet" type="text/css" media="all" href="' . JPATH_SITE . '/' . $css_dir . '/' . $css_f . '" id="luna-tab-style-sheet" />';
		$js = '<script type="text/javascript" src="' . JPATH_SITE . '/includes/js/tabs/' . $js_f . '"></script>';
		/* запрет повторного включения css и js файлов в документ */
		if(!defined('_MTABS_LOADED')){
			define('_MTABS_LOADED', 1);

			if($xhtml){
				$mainframe->addCustomHeadTag($css);
				$mainframe->addCustomHeadTag($js);
			} else{
				echo $css . "\n\t";
				echo $js . "\n\t";
			}
			$this->useCookies = $useCookies;
		}
	}

	/**
	 * creates a tab pane and creates JS obj
	 * @param string The Tab Pane Name
	 */
	function startPane($id){
		echo '<div class="tab-page" id="' . $id . '">';
		echo '<script type="text/javascript">var tabPane1 = new WebFXTabPane( document.getElementById( "' . $id . '" ), ' . $this->useCookies . ' )</script>';
	}

	/**
	 * Ends Tab Pane
	 */
	function endPane(){
		echo '</div>';
	}

	/*
	 * Creates a tab with title text and starts that tabs page
	 * @param tabText - This is what is displayed on the tab
	 * @param paneid - This is the parent pane to build this tab on
	 */

	function startTab($tabText, $paneid){
		echo '<div class="tab-page" id="' . $paneid . '">';
		echo '<h2 class="tab">' . $tabText . '</h2>';
		echo '<script type="text/javascript">tabPane1.addTabPage( document.getElementById( "' . $paneid . '" ) );</script>';
	}

	/*
	 * Ends a tab page
	 */

	function endTab(){
		echo '</div>';
	}

}


/**
 * Создание табов
 * @package Joostina
 */
class uiTabs{

	/**
	@var int Use cookies */
	var $useCookies = 0;

	/**
	 * Constructor
	 * Includes files needed for displaying tabs and sets cookie options
	 * @param int useCookies, if set to 1 cookie will hold last used tab between page refreshes
	 */
	function __construct($useCookies, $xhtml = 0){
		/* запрет повторного включения css и js файлов в документ */
		if(!defined('_MTABS_LOADED')){
			define('_MTABS_LOADED', 1);

			if($xhtml){
				mosCommonHTML::loadJquery();
				mosCommonHTML::loadJqueryUI();
			} else{
				mosCommonHTML::loadJquery(true);
				mosCommonHTML::loadJqueryUI(true);
			}
			$this->useCookies = $useCookies;
		}
	}

	/**
	 * creates a tab pane and creates JS obj
	 * @param string The Tab Pane Name
	 */
	function startPane($id){
		?>
	<script type="text/javascript">
		jQuery(function () {
			// Tabs
			jQuery('#<?php echo $id ?>').tabs({
				fx:{
					opacity:'toggle',
					duration:'fast'
				}
			});

		});

		function addTabHeader(id, li) {
			var parentId = jQuery('#' + id).parent().attr('id');
			var ul = jQuery('#' + parentId).children('.tabheaders')[0];
			jQuery(li).appendTo(ul);
		}

		function initEditors() {
			var args = [].slice.call(arguments);
			var n = args.length;
			for (var i = 0; i < n; i++) {
				jQuery('#' + args[i]).elrte(opts);
			}
		}
	</script>
		<div id="<?php echo $id ?>">
             <ul class="tabheaders"></ul>
		<?php
	}

	/**
	 * Ends Tab Pane
	 */
	function endPane(){
		echo '</div>';
	}

	/*
	 * Creates a tab with title text and starts that tabs page
	 * @param tabText - This is what is displayed on the tab
	 * @param paneid - This is the parent pane to build this tab on
	 */

	function startTab($tabText, $paneid, $hiddenFields = null){
		if(!is_null($hiddenFields)){

			$hiddenFields = " onclick=\"setTimeout(function() {initEditors(\\'" . implode("\\', \\'", $hiddenFields) . "\\')}, 500)\"";
		} else{
			$hiddenFields = '';
		}
		?>
        <div class="tab-page" id="<?php echo $paneid ?>">
            <script type="text/javascript">
				addTabHeader('<?php echo $paneid ?>', '<li><a href="#<?php echo $paneid ?>"<?php echo $hiddenFields ?>><?php echo $tabText ?></a></li>');
			</script>
		<?php
	}

	/*
	 * Ends a tab page
	 */

	function endTab(){
		echo '</div>';
	}

}

/**
 * Common HTML Output Files
 * @package Joostina
 */
class mosAdminMenus{

	/**
	 * build the select list for Menu Ordering
	 */
	public static function Ordering(&$row, $id){
		$database = database::getInstance();

		if($id){
			$query = "SELECT ordering AS value, name AS text" . " FROM #__menu" . "\n WHERE menutype = " . $database->Quote($row->menutype) . "\n AND parent = " . (int)$row->parent . "\n AND published != -2" . "\n ORDER BY ordering";
			$order = mosGetOrderingList($query);
			$ordering = mosHTML::selectList($order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval($row->ordering));
		} else{
			$ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . _NEW_ITEM_LAST;
		}
		return $ordering;
	}

	/**
	 * @static Создание списка прав доступа для групп
	 * @param $row - ($row->access) ID группы
	 * @param bool $guest - Добавлять ли гостевой вход
	 * @return string - HTML-выпадающий список
	 */
	public static function Access($row, $guest = false){
		$database = database::getInstance();

		$query = "SELECT id AS value, name AS text FROM #__groups ORDER BY id";
		$database->setQuery($query);
		$groups = $database->loadObjectList();
		$guest ? $groups[] = mosHTML::makeOption(3, _COM_MODULES_GUEST) : null;
		$access = mosHTML::selectList($groups, 'access', 'class="inputbox" size="4"', 'value', 'text', intval($row->access));
		return $access;
	}

	/**
	 * build the select list for parent item
	 */
	public static function Parent(&$row){
		$database = database::getInstance();

		$id = '';
		if($row->id)
			$id = "\n AND id != " . (int)$row->id;
		// get a list of the menu items
		// excluding the current menu item and its child elements
		$query = "SELECT m.* FROM #__menu m WHERE menutype = " . $database->Quote($row->menutype) . " AND published != -2" . $id . " ORDER BY parent, ordering";
		$database->setQuery($query);
		$mitems = $database->loadObjectList();
		// establish the hierarchy of the menu
		$children = array();
		if($mitems){
			// first pass - collect children
			foreach($mitems as $v){
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		// second pass - get an indent list of the items
		$list = mosTreeRecurse(0, '', array(), $children, 20, 0, 0);
		// assemble menu items to the array
		$mitems = array();
		$mitems[] = mosHTML::makeOption('0', 'Top');
		foreach($list as $item){
			$mitems[] = mosHTML::makeOption($item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
		}
		$output = mosHTML::selectList($mitems, 'parent', 'class="inputbox" size="10"', 'value', 'text', $row->parent);
		return $output;
	}

	/**
	 * build a radio button option for published state
	 */
	public static function Published(&$row){
		return mosHTML::yesnoRadioList('published', 'class="inputbox"', $row->published);
	}

	/**
	 * build the link/url of a menu item
	 */
	public static function Link(&$row, $id, $link = null){
		$mainframe = mosMainFrame::getInstance();
		;

		if($id){
			switch($row->type){
				case 'content_item_link':
				case 'content_typed':
					// load menu params
					$params = new mosParameters($row->params, $mainframe->getPath('menu_xml', $row->type), 'menu');

					$temp = explode('&task=view&id=', $row->link);

					$link = $row->link;
					break;

				default:
					$link = $row->link;
					break;
			}
		} else{
			$link = null;
		}

		return $link;
	}

	/**
	 * build the select list for target window
	 */
	public static function Target(&$row){
		$click[] = mosHTML::makeOption('0', _ADM_MENUS_TARGET_CUR_WINDOW);
		$click[] = mosHTML::makeOption('1', _ADM_MENUS_TARGET_NEW_WINDOW_WITH_PANEL);
		$click[] = mosHTML::makeOption('2', _ADM_MENUS_TARGET_NEW_WINDOW_WITHOUT_PANEL);
		return mosHTML::selectList($click, 'browserNav', 'class="inputbox" size="4"', 'value', 'text', intval($row->browserNav));
	}

	/**
	 * build the multiple select list for Menu Links/Pages
	 */
	public static function MenuLinks($lookup, $all = null, $none = null){

		$database = database::getInstance();

		// подготовить список ядра (BOSS)
		$sql = "SELECT  id,  name FROM #__boss_config";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		foreach($rows as $directory){

			// get a list of the menu items
			//$sql = "SELECT m.* FROM #__menu AS m WHERE m.published = 1 ORDER BY m.menutype, m.parent, m.ordering";
			$sql = "SELECT * FROM #__boss_" . $directory->id . "_categories ORDER BY parent,ordering";

			$database->setQuery($sql);
			$mitems = $database->loadObjectList();
			$mitems_temp = $mitems;

			// establish the hierarchy of the menu
			$children = array();
			// first pass - collect children
			foreach($mitems as $v){
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
			// second pass - get an indent list of the items
			$list = mosTreeRecurse(intval($mitems[0]->parent), '', array(), $children, 20, 0, 0);

			// Code that adds menu name to Display of Page(s)
			$text_count = 0;
			$mitems_spacer = $directory->name;
			foreach($list as $list_a){
~~				foreach($mitems_temp as $mitems_a){
					if($mitems_a->id == $list_a->id){
						// Code that inserts the blank line that seperates different menus
						if($directory->name != $mitems_spacer){
							$list_temp[] = mosHTML::makeOption(-999, '------------');
							$mitems_spacer = $directory->name;
						}

						$text = $directory->name . ' : ' . $list_a->treename;
						$list_temp[] = mosHTML::makeOption($list_a->id, $text);

						if(strlen($text) > $text_count){
							$text_count = strlen($text);
						}
					}
				}
			}
			$list_temp[] = mosHTML::makeOption(-999, '------------');
			$list = $list_temp;
		}
		// массив для списка
		$mitems = array();

		// пункт "Все"
		if($all){
			// сам пункт
			$mitems[] = mosHTML::makeOption('', _ALL);
			// разделитель
			$mitems[] = mosHTML::makeOption(-999, '------------');
		}

		// пункт "Отсутствует"
		if($none){
			// prepare an array with 'all' as the first item
			$mitems[] = mosHTML::makeOption(0, _NOT_EXISTS);
			// adds space, in select box which is not saved
			$mitems[] = mosHTML::makeOption(-999, '------------');
		}

		// append the rest of the menu items to the array
		foreach($list as $item){
			$mitems[] = mosHTML::makeOption($item->value, $item->text);
		}
		/*
		  // добавляем в список типы страниц "по умолчанию"
		  $pages = array(
		  mosHTML::makeOption(0,'----'),
		  mosHTML::makeOption(0,_PAGES.' : '._CREATE_ACCOUNT),
		  mosHTML::makeOption(0,_PAGES.' : '._LOST_PASSWORDWORD),
		  );
		  $mitems = array_merge($mitems,$pages);
		 */

		return mosHTML::selectList($mitems, 'selections[]', 'class="inputbox" size="26" multiple="multiple"', 'value', 'text', $lookup);
	}

	/**
	 * build the select list to choose a section
	 */
	public static function Section(&$menu, $id, $all = 0){
		$database = database::getInstance();

		$query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text` FROM #__sections AS s WHERE s.scope = 'content' ORDER BY s.name";
		$database->setQuery($query);
		if($all){
			$rows[] = mosHTML::makeOption(0, '- Все разделы -');
			$rows = array_merge($rows, $database->loadObjectList());
		} else{
			$rows = $database->loadObjectList();
		}

		if($id){
			foreach($rows as $row){
				if($row->value == $menu->componentid){
					$section = $row->text;
				}
			}
			$section .= '<input type="hidden" name="componentid" value="' . $menu->componentid . '" />';
			$section .= '<input type="hidden" name="link" value="' . $menu->link . '" />';
		} else{
			$section = mosHTML::selectList($rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text');
			$section .= '<input type="hidden" name="link" value="" />';
		}
		return $section;
	}

	/**
	 * build the select list to choose a component
	 */
	public static function Component(&$menu, $id, $rows = null){
		$database = database::getInstance();

		if(!$rows){
			$query = "SELECT c.id AS value, c.name AS text, c.link FROM #__components AS c WHERE c.link != '' ORDER BY c.name";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
		}
		if($id){
			// existing component, just show name
			foreach($rows as $row){
				if($row->value == $menu->componentid){
					$component = $row->text;
				} else{
					$component = $menu->name;
				}
			}
			$component .= '<input type="hidden" name="componentid" value="' . $menu->componentid . '" />';
		} else{
			$component = mosHTML::selectList($rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text');
		}

		return $component;
	}

	/**
	 * build the select list to choose a component
	 */
	public static function ComponentName(&$menu, $rows = null){
		$database = database::getInstance();

		if(!$rows){
			$query = "SELECT c.id AS value, c.name AS text, c.link FROM #__components AS c WHERE c.link != '' ORDER BY c.name";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
		}

		$component = 'Component';
		foreach($rows as $row){
			if($row->value == $menu->componentid){
				$component = $row->text;
			}
		}

		return $component;
	}

	/**
	 * build the select list to choose an image
	 */
	public static function Images($name, &$active, $javascript = null, $directory = null){

		if(!$directory){
			$directory = '/images/stories';
		}

		if(!$javascript){
			$javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='..$directory/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		}

		$imageFiles = mosReadDirectory(JPATH_BASE . $directory);
		$images = array(mosHTML::makeOption('', '- ' . _CHOOSE_IMAGE . ' -'));
		foreach($imageFiles as $file){
			if(preg_match("/bmp|gif|jpg|png/i", $file)){
				$images[] = mosHTML::makeOption($file);
			}
		}
		$images = mosHTML::selectList($images, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

		return $images;
	}

	/**
	 * build the select list for Ordering of a specified Table
	 */
	public static function SpecificOrdering(&$row, $id, $query, $neworder = 0, $limit = 30){
		if($neworder){
			$text = _NEW_ITEM_FIRST;
		} else{
			$text = _NEW_ITEM_LAST;
		}

		if($id){
			$order = mosGetOrderingList($query, $limit);
			$ordering = mosHTML::selectList($order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval($row->ordering));
		} else{
			$ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . $text;
		}
		return $ordering;
	}

	/**
	 * Select list of active users
	 */
	public static function UserSelect($name, $active, $nouser = 0, $javascript = null, $order = 'name', $reg = 1){
		$database = database::getInstance();

		$and = '';
		if($reg){
			// does not include registered users in the list
			$and = "\n AND gid > 18";
		}

		$query = "SELECT id AS value, name AS text FROM #__users WHERE block = 0 $and ORDER BY $order";
		$database->setQuery($query);
		if($nouser){
			$users[] = mosHTML::makeOption('0', '- ' . _NO_USER . ' -');
			$users = array_merge($users, $database->loadObjectList());
		} else{
			$users = $database->loadObjectList();
		}

		$users = mosHTML::selectList($users, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

		return $users;
	}

	/**
	 * Select list of positions - generally used for location of images
	 */
	public static function Positions($name, $active = null, $javascript = null, $none = 1, $center = 1, $left = 1, $right = 1){
		if($none){
			$pos[] = mosHTML::makeOption('', _NONE);
		}
		if($center){
			$pos[] = mosHTML::makeOption('center', _CENTER);
		}
		if($left){
			$pos[] = mosHTML::makeOption('left', _LEFT);
		}
		if($right){
			$pos[] = mosHTML::makeOption('right', _RIGHT);
		}

		$positions = mosHTML::selectList($pos, $name, 'class="inputbox" size="1"' . $javascript, 'value', 'text', $active);

		return $positions;
	}

	/**
	 * Select list of active sections
	 */
	public static function SelectSection($name, $active = null, $javascript = null, $order = 'ordering', $scope = 'content'){
		$database = database::getInstance();

		$categories[] = mosHTML::makeOption('0', _SEL_SECTION);
		$query = "SELECT id AS value, title AS text" . "\n FROM #__sections" . "\n WHERE published = 1 AND scope='$scope'" . "\n ORDER BY $order";
		$database->setQuery($query);
		$sections = array_merge($categories, $database->loadObjectList());

		$category = mosHTML::selectList($sections, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

		return $category;
	}

	/**
	 * Select list of menu items for a specific menu
	 */
	public static function Links2Menu($type, $and){
		$database = database::getInstance();

		$query = "SELECT* FROM #__menu WHERE type = " . $database->Quote($type) . " AND published = 1" . $and;
		$database->setQuery($query);
		$menus = $database->loadObjectList();
		return $menus;
	}

	/**
	 * Select list of menus
	 * @param string The control name
	 * @param string Additional javascript
	 * @return string A select list
	 */
	public static function MenuSelect($name = 'menuselect', $javascript = null){
		$database = database::getInstance();

		$query = "SELECT params FROM #__modules WHERE module = 'mod_mainmenu' OR module = 'mod_mljoostinamenu'";
		$database->setQuery($query);
		$menus = $database->loadObjectList();
		$i = 0;
		$menuselect = array();
		$menus_arr = array();
		foreach($menus as $menu){
			$params = mosParseParams($menu->params);
			if(!in_array($params->menutype, $menus_arr)){
				$menuselect[$i] = new stdClass();
				$menuselect[$i]->value = $params->menutype;
				$menuselect[$i]->text = $params->menutype;
				$menus_arr[$i] = $params->menutype;
				$i++;
			}
		}
		SortArrayObjects($menuselect, 'text', 1);
		$menus = mosHTML::selectList($menuselect, $name, 'class="inputbox" size="10" ' . $javascript, 'value', 'text');
		return $menus;
	}

	/**
	 * Internal function to recursive scan the media manager directories
	 * @param string Path to scan
	 * @param string root path of this folder
	 * @param array  Value array of all existing folders
	 * @param array  Value array of all existing images
	 */
	public static function ReadImages($imagePath, $folderPath, &$folders, &$images){
		$imgFiles = mosReadDirectory($imagePath);

		foreach($imgFiles as $file){
			$ff_ = $folderPath . $file . '/';
			$ff = $folderPath . $file;
			$i_f = $imagePath . '/' . $file;

			if(is_dir($i_f) && $file != 'CVS' && $file != '.svn'){
				$folders[] = mosHTML::makeOption($ff_);
				mosAdminMenus::ReadImages($i_f, $ff_, $folders, $images);
			} else if(preg_match("/bmp|gif|jpg|png/", $file) && is_file($i_f)){
				// leading / we don't need
				$imageFile = substr($ff, 1);
				$images[$folderPath][] = mosHTML::makeOption($imageFile, $file);
			}
		}
	}

	/**
	 * Internal function to recursive scan the media manager directories
	 * @param string Path to scan
	 * @param string root path of this folder
	 * @param array  Value array of all existing folders
	 * @param array  Value array of all existing images
	 */
	public static function ReadImagesX(&$folders, &$images){

		if($folders[0]->value != '*0*'){
			foreach($folders as $folder){
				$imagePath = JPATH_BASE . '/images/stories' . $folder->value;
				$imgFiles = mosReadDirectory($imagePath);
				$folderPath = $folder->value . '/';

				foreach($imgFiles as $file){
					$ff = $folderPath . $file;
					$i_f = $imagePath . '/' . $file;

					if(preg_match("/bmp|gif|jpg|png/i", $file) && is_file($i_f)){
						// leading / we don't need
						$imageFile = substr($ff, 1);
						$images[$folderPath][] = mosHTML::makeOption($imageFile, $file);
					}
				}
			}
		} else{
			$folders = array();
			$folders[] = mosHTML::makeOption('None');
		}
	}

	public static function GetImageFolders(&$temps){
		if($temps[0]->value != 'None'){
			foreach($temps as $temp){
				if(substr($temp->value, -1, 1) != '/'){
					$temp = $temp->value . '/';
					$folders[] = mosHTML::makeOption($temp, $temp);
				} else{
					$temp = $temp->value;
					$temp = ampReplace($temp);
					$folders[] = mosHTML::makeOption($temp, $temp);
				}
			}
		} else{
			$folders[] = mosHTML::makeOption(_NOT_CHOOSED);
		}

		$javascript = "onchange=\"changeDynaList( 'imagefiles', folderimages, document.adminForm.folders.options[document.adminForm.folders.selectedIndex].value, 0, 0);\"";
		$getfolders = mosHTML::selectList($folders, 'folders', 'class="inputbox" size="1" ' . $javascript, 'value', 'text', '/');

		return $getfolders;
	}

	public static function GetImages(&$images, $path, $base = '/'){
		if(is_array($base) && count($base) > 0){
			if($base[0]->value != '/'){
				$base = $base[0]->value . '/';
			} else{
				$base = $base[0]->value;
			}
		} else{
			$base = '/';
		}

		if(!isset($images[$base])){
			$images[$base][] = mosHTML::makeOption('');
		}

		$javascript = "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\" onfocus=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
		$getimages = mosHTML::selectList($images[$base], 'imagefiles', 'class="inputbox" size="10" multiple="multiple" ' . $javascript, 'value', 'text', null);

		return $getimages;
	}

	public static function GetSavedImages(&$row, $path){
		$images2 = array();
		foreach($row->images as $file){
			$temp = explode('|', $file);
			if(strrchr($temp[0], '/')){
				$filename = substr(strrchr($temp[0], '/'), 1);
			} else{
				$filename = $temp[0];
			}
			$images2[] = mosHTML::makeOption($file, $filename);
		}
		$javascript = "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \"";
		$imagelist = mosHTML::selectList($images2, 'imagelist', 'class="inputbox" size="10" ' . $javascript, 'value', 'text');
		return $imagelist;
	}

	/**
	 * Checks to see if an image exists in the current templates image directory
	 * if it does it loads this image.  Otherwise the default image is loaded.
	 * Also can be used in conjunction with the menulist param to create the chosen image
	 * load the default or use no image
	 */
	public static function ImageCheck($file, $directory = '/images/M_images/', $param = null, $param_directory = '/images/M_images/', $alt = null, $name = null, $type = 1, $align = 'middle', $title = null, $admin = null){

		$id = $name ? ' id="' . $name . '"' : '';
		$name = $name ? ' name="' . $name . '"' : '';
		$title = $title ? ' title="' . $title . '"' : '';
		$alt = $alt ? ' alt="' . $alt . '"' : ' alt=""';
		$align = $align ? ' align="' . $align . '"' : '';
		// change directory path from frontend or backend
		if($admin){
			$path = '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico/';
		} else{
			$path = '/templates/' . JTEMPLATE . '/images/ico/';
		}
		if($param){
			$image = JPATH_SITE . $param_directory . $param;
			if($type){
				$image = '<img src="' . $image . '" ' . $alt . $id . $name . $align . ' border="0" />';
			}
		} else if($param == -1){
			$image = '';
		} else{
			if(file_exists(JPATH_BASE . $path . $file)){
				$image = JPATH_SITE . $path . $file;
			} else{
				$image = JPATH_SITE . $directory . $file;
			}
			if($type){
				$image = '<img src="' . $image . '" ' . $alt . $id . $name . $title . $align . ' border="0" />';
			}
		}
		return $image;
	}

	/**
	 * Checks to see if an image exists in the current templates image directory
	 * if it does it loads this image.  Otherwise the default image is loaded.
	 * Also can be used in conjunction with the menulist param to create the chosen image
	 * load the default or use no image
	 */
	public static function ImageCheckAdmin($file, $directory = '/administrator/images/', $param = null, $param_directory = '/administrator/images/', $alt = null, $name = null, $type = 1, $align = 'middle', $title = null){
		$image = mosAdminMenus::ImageCheck($file, $directory, $param, $param_directory, $alt, $name, $type, $align, $title, 1);
		return $image;
	}

	public static function menutypes(){
		$database = database::getInstance();

		$query = "SELECT params FROM #__modules WHERE module = 'mod_mainmenu' OR module = 'mod_mljoostinamenu' ORDER BY title";
		$database->setQuery($query);
		$modMenus = $database->loadObjectList();

		$query = "SELECT menutype FROM #__menu GROUP BY menutype ORDER BY menutype";
		$database->setQuery($query);
		$menuMenus = $database->loadObjectList();
		$menuTypes = '';
		foreach($modMenus as $modMenu){
			$check = 1;
			mosMakeHtmlSafe($modMenu);
			$modParams = mosParseParams($modMenu->params);
			$menuType = @$modParams->menutype;
			if(!$menuType){
				$menuType = 'mainmenu';
			}
			// stop duplicate menutype being shown
			if(!is_array($menuTypes)){
				// handling to create initial entry into array
				$menuTypes[] = $menuType;
			} else{
				$check = 1;
				foreach($menuTypes as $a){
					if($a == $menuType){
						$check = 0;
					}
				}
				if($check){
					$menuTypes[] = $menuType;
				}
			}
		}
		// add menutypes from jos_menu
		foreach($menuMenus as $menuMenu){
			$check = 1;
			foreach($menuTypes as $a){
				if($a == $menuMenu->menutype){
					$check = 0;
				}
			}
			if($check){
				$menuTypes[] = $menuMenu->menutype;
			}
		}
		// sorts menutypes
		asort($menuTypes);
		return $menuTypes;
	}

	/*
	 * loads files required for menu items
	 */

	public static function menuItem($item){

		$path = JPATH_BASE . DS . JADMIN_BASE . '/components/com_menus/' . $item . '/';
		include_once ($path . $item . '.class.php');
		include_once ($path . $item . '.menu.html.php');
	}

}

class mosCommonHTML{

	public static function ContentLegend(){
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		?>
	<table cellspacing="0" cellpadding="4" border="0" align="center">
		<tr align="center">
			<td><img src="<?php echo $cur_file_icons_path; ?>/publish_y.png" alt="<?php echo _PUBLISHED_VUT_NOT_ACTIVE ?>" border="0"/></td>
			<td><?php echo _PUBLISHED_VUT_NOT_ACTIVE ?> |</td>
			<td><img src="<?php echo $cur_file_icons_path; ?>/publish_g.png" alt="<?php echo _PUBLISHED_AND_ACTIVE ?>" border="0"/></td>
			<td><?php echo _PUBLISHED_AND_ACTIVE ?> |</td>
			<td><img src="<?php echo $cur_file_icons_path; ?>/publish_r.png" alt="<?php echo _PUBLISHED_BUT_DATE_EXPIRED ?>" border="0"/></td>
			<td><?php echo _PUBLISHED_BUT_DATE_EXPIRED ?> |</td>
			<td><img src="<?php echo $cur_file_icons_path; ?>/publish_x.png" alt="<?php echo _UNPUBLISHED ?>" border="0"/></td>
			<td><?php echo _UNPUBLISHED ?></td>
		</tr>
	</table>
		<?php
	}

	public static function menuLinksContent(&$menus){
		?>
	<script language="javascript" type="text/javascript">
		function go2(pressbutton, menu, id) {
			var form = document.adminForm;
			// assemble the images back into one field
			var temp = new Array;
			for (var i = 0, n = form.imagelist.options.length; i < n; i++) {
				temp[i] = form.imagelist.options[i].value;
			}
			form.images.value = temp.join('\n');

			if (pressbutton == 'go2menu') {
				form.menu.value = menu;
				submitform(pressbutton);
				return;
			}

			if (pressbutton == 'go2menuitem') {
				form.menu.value = menu;
				form.menuid.value = id;
				submitform(pressbutton);
				return;
			}
		}
	</script>
		<?php
		foreach($menus as $menu){
			?>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<tr>
			<td width="90px" valign="top">
				<?php echo _MENU ?>
			</td>
			<td>
				<a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );"><?php echo $menu->menutype; ?></a>
			</td>
		</tr>
		<tr>
			<td width="90px" valign="top"><?php echo _LINK_NAME ?></td>
			<td>
				<strong>
					<a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );"><?php echo $menu->name; ?></a>
				</strong>
			</td>
		</tr>
		<tr>
			<td width="90px" valign="top"><?php echo _E_STATE ?></td>
			<td>
				<?php
				switch($menu->published){
					case -2:
						echo '<span style="color:#ff0000">' . _MENU_EXPIRED . '</span>';
						break;
					case 0:
						echo _UNPUBLISHED;
						break;
					case 1:
					default:
						echo '<span style="color:green">' . _PUBLISHED . '</span>';
						break;
				}
				?>
			</td>
		</tr>
			<?php
		}
		?>
	<input type="hidden" name="menu" value=""/>
	<input type="hidden" name="menuid" value=""/>
		<?php
	}

	public static function menuLinksSecCat(&$menus){
		?>
	<script language="javascript" type="text/javascript">
		function go2(pressbutton, menu, id) {
			var form = document.adminForm;

			if (pressbutton == 'go2menu') {
				form.menu.value = menu;
				submitform(pressbutton);
				return;
			}

			if (pressbutton == 'go2menuitem') {
				form.menu.value = menu;
				form.menuid.value = id;
				submitform(pressbutton);
				return;
			}
		}
	</script>
		<?php foreach($menus as $menu){ ?>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<tr>
			<td width="90px" valign="top"><?php echo _MENU ?></td>
			<td>
				<a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );"><?php echo $menu->menutype; ?></a>
			</td>
		</tr>
		<tr>
			<td width="90px" valign="top"><?php echo _TYPE ?></td>
			<td><?php echo $menu->type; ?></td>
		</tr>
		<tr>
			<td width="90px" valign="top"><?php echo _MENU_ITEM_NAME ?></td>
			<td>
				<strong>
					<a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );"><?php echo $menu->name; ?></a>
				</strong>
			</td>
		</tr>
		<tr>
			<td width="90px" valign="top"><?php echo _E_STATE ?></td>
			<td>
				<?php
				switch($menu->published){
					case -2:
						echo '<span style="color:#ff0000">' . _MENU_EXPIRED . '</span>';
						break;
					case 0:
						echo _UNPUBLISHED;
						break;
					case 1:
					default:
						echo '<span style="color:green">' . _PUBLISHED . '</span>';
						break;
				}
				?>
			</td>
		</tr>
			<?php } ?>
	<input type="hidden" name="menu" value=""/>
	<input type="hidden" name="menuid" value=""/>
		<?php
	}

	public static function checkedOut(&$row, $overlib = 1){
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		$hover = '';
		if($overlib){
			$date = mosFormatDate($row->checked_out_time, '%A, %d %B %Y');
			$time = mosFormatDate($row->checked_out_time, '%H:%M');
			$editor = addslashes(htmlspecialchars(html_entity_decode($row->editor, ENT_QUOTES, 'UTF-8')));
			$checked_out_text = '<table>';
			$checked_out_text = '<tr><td>' . $editor . '</td></tr>';
			$checked_out_text .= '<tr><td>' . $date . '</td></tr>';
			$checked_out_text .= '<tr><td>' . $time . '</td></tr>';
			$checked_out_text .= '</table>';
			$hover = 'onMouseOver="return overlib(\'' . $checked_out_text . '\', CAPTION, \'' . _CHECKED_OUT . '\', BELOW, RIGHT);" onMouseOut="return nd();"';
		}
		$checked = '<img src="' . $cur_file_icons_path . '/checked_out.png" ' . $hover . '/>';
		return $checked;
	}

	/* подключение библиотеки всплывающих подсказок */

	public static function loadOverlib($ret = false){
		$mainframe = mosMainFrame::getInstance();
		if(!$mainframe->get('loadOverlib') && !$ret){
			$mainframe->addJS(JPATH_SITE . '/includes/js/overlib_full.js');
			// установка флага о загруженной библиотеке всплывающих подсказок
			$mainframe->set('loadOverlib', true);
		}
		if(!$mainframe->get('loadOverlib') && $ret == true){
			?>
		<script language="javascript" type="text/javascript" src="<?php echo JPATH_SITE; ?>/includes/js/overlib_full.js"></script>
			<?php
		}
	}

	/*
	 * Подключение JS файлов Календаря
	 */

	public static function loadCalendar(){
		if(!defined('_CALLENDAR_LOADED')){
			define('_CALLENDAR_LOADED', 1);
			$mainframe = mosMainFrame::getInstance();
			$mainframe->addCSS(JPATH_SITE . '/includes/js/calendar/calendar.css');
			$mainframe->addJS(JPATH_SITE . '/includes/js/calendar/calendar.js');
			$_lang_file = JPATH_BASE . '/includes/js/calendar/lang/calendar-' . _LANGUAGE . '.js';
			$_lang_file = (is_file($_lang_file)) ? JPATH_SITE . '/includes/js/calendar/lang/calendar-' . _LANGUAGE . '.js' : JPATH_SITE . '/includes/js/calendar/lang/calendar-ru.js';
			$mainframe->addJS($_lang_file);
		}
	}

	/* подключение mootools */

	public static function loadMootools($ret = false){
		if(!defined('_MOO_LOADED')){
			define('_MOO_LOADED', 1);
			$mainframe = mosMainFrame::getInstance();
			$mainframe->addJS(JPATH_SITE . '/includes/js/mootools/mootools.js');
		}
		if($ret == true)

			?>
			<script language="javascript" type="text/javascript" src="<?php echo JPATH_SITE ?>/includes/js/mootools/mootools.js"></script>
		<?php
	}

	/* подключение prettyTable */

	public static function loadPrettyTable(){
		if(!defined('_PRT_LOADED')){
			define('_PRT_LOADED', 1);
			$mainframe = mosMainFrame::getInstance();
			$mainframe->addJS(JPATH_SITE . '/includes/js/jsfunction/jrow.js');
		}
	}

	/* подключение Fullajax */

	public static function loadFullajax($ret = false){
		if(!defined('_FAX_LOADED')){
			define('_FAX_LOADED', 1);
			if($ret){
				?>
			<script language="javascript" type="text/javascript" src="<?php echo JPATH_SITE; ?>/includes/js/fullajax/fullajax.js"></script>
				<?php
			} else{
				$mainframe = mosMainFrame::getInstance();
				$mainframe->addJS(JPATH_SITE . '/includes/js/fullajax/fullajax.js');
			}
		}
	}

	/* подключение Jquery */

	public static function loadJquery($ret = false){
		if(!defined('_JQUERY_LOADED')){
			define('_JQUERY_LOADED', 1);
			if($ret){
				return '<script language="javascript" type="text/javascript" src="' . JPATH_SITE . '/includes/js/jquery/jquery.js"></script>';
			} else{
				$mainframe = mosMainFrame::getInstance();
				$mainframe->addJS(JPATH_SITE . '/includes/js/jquery/jquery.js');
				return true;
			}
		}
	}

	/* подключение расширений Jquery */

	public static function loadJqueryPlugins($name, $ret = false, $css = false, $footer = '', $folder = ''){
		$name = trim($name);
		$folder = (!empty($folder)) ? trim($folder) . '/' : '';

		$path = JPATH_SITE . '/includes/js/jquery/plugins/' . $folder . $name;

		// если само ядро Jquery не загружено - сначала грузим его
		if(!defined('_JQUERY_LOADED')){
			mosCommonHTML::loadJquery($ret);
		}
		// формируем константу-флаг для исключения повтороной загрузки
		$const = '_JQUERY_PL_' . strtoupper($name) . '_LOADED';
		if(!defined($const)){
			define($const, 1);
			if($ret){
				$return = '
                <script language="javascript" type="text/javascript" src="' . $path . '.js"></script>
				<script language="JavaScript" type="text/javascript">if(_js_defines) {_js_defines.push(\'' . $name . '\')} else {var _js_defines = [\'' . $name . '\']}</script>
				';
				if($css){
					$return .= '<link type="text/css" rel="stylesheet" href="' . $path . '.css" />';
				}
				return $return;
			} else{
				$mainframe = mosMainFrame::getInstance();
				$mainframe->addJS($path . '.js', $footer);
				$mainframe->addCustomHeadTag('<script language="JavaScript" type="text/javascript">if(_js_defines) {_js_defines.push(\'' . $name . '\')} else {var _js_defines = [\'' . $name . '\']}</script>');
				if($css){
					$mainframe->addCSS($path . '.css');
				}
			}
		}
		return null;
	}

	/* подключение файла Jquery UI */

	public static function loadJqueryUI($ret = false){
		if(!defined('_JQUERY_UI_LOADED')){
			define('_JQUERY_UI_LOADED', 1);
			if($ret){
				$return = '<script language="javascript" type="text/javascript" src="' . JPATH_SITE . '/includes/js/jquery/ui.js"></script>';
				$return .= '<link type="text/css" rel="stylesheet" href="' . JPATH_SITE . '/includes/js/jquery/ui/ui.css" />';
				return $return;
			} else{
				$mainframe = mosMainFrame::getInstance();
				$mainframe->addJS(JPATH_SITE . '/includes/js/jquery/ui.js');
				$mainframe->addCSS(JPATH_SITE . '/includes/js/jquery/ui/ui.css');
				return true;
			}
		}
	}

	/* подключение codepress */

	public static function loadCodepress(){
		if(!defined('_CODEPRESS_LOADED')){
			define('_CODEPRESS_LOADED', 1);
			$mainframe = mosMainFrame::getInstance();
			$mainframe->addJS(JPATH_SITE . '/includes/js/codepress/codepress.js');
			?>
		<script language="JavaScript" type="text/javascript">
			CodePress.run = function () {
				CodePress.path = '<?php echo JPATH_SITE ?>/includes/js/codepress/';
				t = document.getElementsByTagName('textarea');
				for (var i = 0, n = t.length; i < n; i++) {
					if (t[i].className.match('codepress')) {
						id = t[i].id;
						t[i].id = id + '_cp';
						eval(id + ' = new CodePress(t[i])');
						t[i].parentNode.insertBefore(eval(id), t[i]);
					}
				}
			}
			if (window.attachEvent) {
				window.attachEvent('onload', CodePress.run);
			} else {
				window.addEventListener('DOMContentLoaded', CodePress.run, false);
			}</script>
		<?php
		}
	}

	/* подключение dTree */

	public static function loadDtree(){
		if(!defined('_DTR_LOADED')){
			define('_DTR_LOADED', 1);
			$mainframe = mosMainFrame::getInstance();
			$mainframe->addCSS(JPATH_SITE . '/includes/js/dtree/dtree.css');
			$mainframe->addJS(JPATH_SITE . '/includes/js/dtree/dtree.js');
		}
	}

	public static function AccessProcessing(&$row, $i, $ajax = null){
		$option = strval(mosGetParam($_REQUEST, 'option', ''));
		if(!$row->access){
			$color_access = 'style="color: green;"';
			$task_access = 'accessregistered';
		} elseif($row->access == 1){
			$color_access = 'style="color: red;"';
			$task_access = 'accessspecial';
		} else{
			$color_access = 'style="color: black;"';
			$task_access = 'accesspublic';
		}
		if(!$ajax){
			$href = '<a href="javascript: void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task_access . '\')" ' . $color_access . '>' . $row->groupname . '</a>';
		} else{
			$href = '<a href="#" onclick="ch_access(' . $row->id . ',\'' . $task_access . '\',\'' . $option . '\');" ' . $color_access . '>' . $row->groupname . '</a>';
		}
		return $href;
	}

	/*
	 * Проверка блокировки объекта
	 */

	public static function CheckedOutProcessing(&$row, $i){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();
		if($row->checked_out){
			$checked = mosCommonHTML::checkedOut($row);
		} else{
			$checked = mosHTML::idBox($i, $row->id, ($row->checked_out && $row->checked_out != $my->id));
		}
		return $checked;
	}

	public static function PublishedProcessing(&$row, $i){
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		$img = $row->published ? 'publish_g.png' : 'publish_x.png';
		$task = $row->published ? 'unpublish' : 'publish';
		$alt = $row->published ? _PUBLISHED : _UNPUBLISHED;
		$action = $row->published ? _HIDE : _PUBLISH_ON_FRONTPAGE;
		$href = '<a href="javascript: void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $action . '"><img src="' . $cur_file_icons_path . '/' . $img . '" border="0" alt="' . $alt . '" /></a>';
		return $href;
	}

	/*
	 * Special handling for newfeed encoding and possible conflicts with page encoding and PHP version
	 * Added 1.0.8
	 * Static Function
	 */

	public static function newsfeedEncoding($rssDoc, $text, $utf8enc = null){

		if(!defined('_JOS_FEED_ENCODING')){
			// determine encoding of feed
			$feed = $rssDoc->toNormalizedString(true);
			$feed = strtolower(substr($feed, 0, 150));
			$feedEncoding = strpos($feed, 'encoding=&quot;utf-8&quot;');

			if($feedEncoding !== false){
				// utf-8 feed
				$utf8 = 1;
			} else{
				// non utf-8 page
				$utf8 = 0;
			}

			define('_JOS_FEED_ENCODING', $utf8);
		}

		if(!defined('_JOS_SITE_ENCODING')){
			// determine encoding of page
			if(strpos(strtolower(_ISO), 'utf') !== false){
				// utf-8 page
				$utf8 = 1;
			} else{
				// non utf-8 page
				$utf8 = 0;
			}

			define('_JOS_SITE_ENCODING', $utf8);
		}
		if(phpversion() >= 5){
			// handling for PHP 5
			if(_JOS_FEED_ENCODING){
				// handling for utf-8 feed
				if(_JOS_SITE_ENCODING){
					// utf-8 page
					$encoding = 'html_entity_decode';
				} else{
					// non utf-8 page
					$encoding = 'utf8_decode';
				}
			} else{
				// handling for non utf-8 feed
				if(_JOS_SITE_ENCODING){
					// utf-8 page
					$encoding = '';
				} else{
					// non utf-8 page
					$encoding = 'utf8_decode';
				}
			}
		} else{
			// handling for PHP 4
			if(_JOS_FEED_ENCODING){
				// handling for utf-8 feed
				if(_JOS_SITE_ENCODING){
					// utf-8 page
					$encoding = '';
				} else{
					// non utf-8 page
					$encoding = 'utf8_decode';
				}
			} else{
				// handling for non utf-8 feed
				if(_JOS_SITE_ENCODING){
					// utf-8 page
					$encoding = 'utf8_encode';
				} else{
					// non utf-8 page
					$encoding = 'html_entity_decode';
				}
			}
		}

		if($encoding && !$utf8enc){
			$text = $encoding($text);
		} elseif($utf8enc){
			$text = joostina_api::convert($text);
		}

		$text = str_replace('&apos;', "'", $text);

		return $text;
	}

	public static function get_element($file){

		$file_templ = 'templates/' . JTEMPLATE . '/images/elements/' . $file;
		$file_system = '/images/M_images/' . $file;

		$return = $file_templ;
		if(!is_file(JPATH_BASE . DS . $file_templ)){
			$return = $file_system;
		}

		return $return;
	}

}

/**
 * Sorts an Array of objects
 */
function SortArrayObjects_cmp(&$a, &$b){
	global $csort_cmp;
	if($a->$csort_cmp['key'] > $b->$csort_cmp['key']){
		return $csort_cmp['direction'];
	}
	if($a->$csort_cmp['key'] < $b->$csort_cmp['key']){
		return -1 * $csort_cmp['direction'];
	}
	return 0;
}

/**
 * Sorts an Array of objects
 * sort_direction [1 = Ascending] [-1 = Descending]
 */
function SortArrayObjects(&$a, $k, $sort_direction = 1){
	global $csort_cmp;
	$csort_cmp = array('key' => $k, 'direction' => $sort_direction);
	usort($a, 'SortArrayObjects_cmp');
	unset($csort_cmp);
}

/**
 * Sends mail to admin
 */
function mosSendAdminMail($adminName, $adminEmail, $email, $type, $title = '', $author = ''){
	$subject = _MAIL_SUB . " '$type'";
	$message = _MAIL_MSG;
	eval("\$message = \"$message\";");
	mosMail(Jconfig::getInstance()->config_mailfrom, Jconfig::getInstance()->config_fromname, $adminEmail, $subject, $message);
}

/**
 * Displays a not authorised message
 * If the user is not logged in then an addition message is displayed.
 */
function mosNotAuth(){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	echo _NOT_AUTH;
	if($my->id < 1){
		echo "<br />" . _DO_LOGIN;
	}
}

/**
 * Replaces &amp; with & for xhtml compliance
 * Needed to handle unicode conflicts due to unicode conflicts
 */
function ampReplace($text){
	$text = str_replace('&&', '*--*', $text);
	$text = str_replace('&#', '*-*', $text);
	$text = str_replace('&amp;', '&', $text);
	$text = preg_replace('|&(?![\w]+;)|', '&amp;', $text);
	$text = str_replace('*-*', '&#', $text);
	$text = str_replace('*--*', '&&', $text);
	return $text;
}

/**
 * Prepares results from search for display
 * @param string The source string
 * @param int Number of chars to trim
 * @param string The searchword to select around
 * @return string
 */
function mosPrepareSearchContent($text, $length = 200, $searchword = ''){
	// strips tags won't remove the actual jscript
	$text = preg_replace("'<script[^>]*>.*?</script>'si", "", $text);
	$text = str_replace('&nbsp;', ' ', $text);
	$text = preg_replace('/{.+?}/', '', $text);
	//$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is','\2', $text );
	// replace line breaking tags with whitespace
	$text = preg_replace("'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text);
	$text = mosSmartSubstr(strip_tags($text), $length, $searchword);
	return $text;
}

/**
 * returns substring of characters around a searchword
 * @param string The source string
 * @param int Number of chars to return
 * @param string The searchword to select around
 * @return string
 */
function mosSmartSubstr($text, $length = 200, $searchword = ''){
	$wordpos = Jstring::strpos(Jstring::strtolower($text), Jstring::strtolower($searchword));
	$halfside = intval($wordpos - $length / 2 - Jstring::strlen($searchword));
	if($wordpos && $halfside > 0){
		return '...' . Jstring::substr($text, $halfside, $length) . '...';
	} else{
		return Jstring::substr($text, 0, $length);
	}
}

/**
 * Chmods files and directories recursively to given permissions. Available from 1.0.0 up.
 * @param path The starting file or directory (no trailing slash)
 * @param filemode Integer value to chmod files. NULL = dont chmod files.
 * @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
 * @return TRUE=all succeeded FALSE=one or more chmods failed
 */
function mosChmodRecursive($path, $filemode = null, $dirmode = null){
	$ret = true;
	if(is_dir($path)){
		$dh = opendir($path);
		while($file = readdir($dh)){
			if($file != '.' && $file != '..'){
				$fullpath = $path . '/' . $file;
				if(is_dir($fullpath)){
					if(!mosChmodRecursive($fullpath, $filemode, $dirmode))
						$ret = false;
				} else{
					if(isset($filemode))
						if(!@chmod($fullpath, $filemode))
							$ret = false;
				} // if
			} // if
		} // while
		closedir($dh);
		if(isset($dirmode))
			if(!@chmod($path, $dirmode))
				$ret = false;
	} else{
		if(isset($filemode))
			$ret = @chmod($path, $filemode);
	} // if
	return $ret;
}

// mosChmodRecursive

/**
 * Chmods files and directories recursively to mos global permissions. Available from 1.0.0 up.
 * @param path The starting file or directory (no trailing slash)
 * @param filemode Integer value to chmod files. NULL = dont chmod files.
 * @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
 * @return TRUE=all succeeded FALSE=one or more chmods failed
 */
function mosChmod($path){
	$config = Jconfig::getInstance();

	$config->config_fileperms = trim($config->config_fileperms);
	$config->config_dirperms = trim($config->config_fileperms);
	$filemode = null;
	if($config->config_fileperms != ''){
		$filemode = octdec($config->config_fileperms);
	}
	$dirmode = null;
	if($config->config_dirperms != ''){
		$dirmode = octdec($config->config_dirperms);
	}
	if(isset($filemode) || isset($dirmode)){
		return mosChmodRecursive($path, $filemode, $dirmode);
	}
	return true;
}

// mosChmod

/**
 * Function to convert array to integer values
 * @param array
 * @param int A default value to assign if $array is not an array
 * @return array
 */
function mosArrayToInts(&$array, $default = null){
	if(is_array($array)){
		foreach($array as $key => $value){
			$array[$key] = (int)$value;
		}
	} else{
		if(is_null($default)){
			$array = array();
			return array(); // Kept for backwards compatibility
		} else{
			$array = array((int)$default);
			return array($default); // Kept for backwards compatibility
		}
	}
}

/*
 * Function to handle an array of integers
 * Added 1.0.11
 */

function josGetArrayInts($name, $type = null){
	if($type == null){
		$type = $_POST;
	}

	$array = mosGetParam($type, $name, array(0));

	mosArrayToInts($array);

	if(!is_array($array)){
		$array = array(0);
	}

	return $array;
}

/**
 * Provides a secure hash based on a seed
 * @param string Seed string
 * @return string
 */
function mosHash($seed){
	return md5($GLOBALS['mosConfig_secret'] . md5($seed));
}

/**
 * Format a backtrace error
 * @since 1.0.5
 */
function mosBackTrace(){
	if(function_exists('debug_backtrace')){
		echo '<div align="left">';
		foreach(debug_backtrace() as $back){
			if(isset($back['file'])){
				echo '<br />' . str_replace(JPATH_BASE, '', $back['file']) . ':' . $back['line'];
			}
		}
		echo '</div>';
	}
}

function josSpoofCheck($header = NULL, $alt = NULL, $method = 'post'){
	switch(strtolower($method)){
		case 'get':
			$validate = mosGetParam($_GET, josSpoofValue($alt), 0);
			break;
		case 'request':
			$validate = mosGetParam($_REQUEST, josSpoofValue($alt), 0);
			break;
		case 'post':
		default:
			$validate = mosGetParam($_POST, josSpoofValue($alt), 0);
			break;
	}

	// probably a spoofing attack
	if(!$validate){
		header('HTTP/1.0 403 Forbidden');
		mosErrorAlert(_NOT_AUTH);
		return;
	}

	// First, make sure the form was posted from a browser.
	// For basic web-forms, we don't care about anything
	// other than requests from a browser:
	if(!isset($_SERVER['HTTP_USER_AGENT'])){
		header('HTTP/1.0 403 Forbidden');
		mosErrorAlert(_NOT_AUTH);
		return;
	}

	// Make sure the form was indeed POST'ed:
	//  (requires your html form to use: action="post")
	if(!$_SERVER['REQUEST_METHOD'] == 'POST'){
		header('HTTP/1.0 403 Forbidden');
		mosErrorAlert(_NOT_AUTH);
		return;
	}

	if($header){
		// Attempt to defend against header injections:
		$badStrings = array('Content-Type:', 'MIME-Version:', 'Content-Transfer-Encoding:', 'bcc:', 'cc:');

		// Loop through each POST'ed value and test if it contains
		// one of the $badStrings:
		_josSpoofCheck($_POST, $badStrings);
	}
}

function _josSpoofCheck($array, $badStrings){
	// Loop through each $array value and test if it contains
	// one of the $badStrings
	foreach($array as $v){
		if(is_array($v)){
			_josSpoofCheck($v, $badStrings);
		} else{
			foreach($badStrings as $v2){
				if(stripos($v, $v2) !== false){
					header('HTTP/1.0 403 Forbidden');
					mosErrorAlert(_NOT_AUTH);
					exit(); // mosErrorAlert dies anyway, double check just to make sure
				}
			}
		}
	}
}

/**
 * Method to determine a hash for anti-spoofing variable names
 * @return    string    Hashed var name
 * @static
 */
function josSpoofValue($alt = NULL){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();

	if($alt){
		if($alt == 1){
			$random = date('Ymd');
		} else{
			$random = $alt . date('Ymd');
		}
	} else{
		$random = date('dmY');
	}
	// the prefix ensures that the hash is non-numeric
	// otherwise it will be intercepted by globals.php
	$validate = 'j' . mosHash($mainframe->getCfg('db') . $random . $my->id);

	return $validate;
}

/**
 * A simple helper function to salt and hash a clear-text password.
 * @since    1.0.13
 * @param    string    $password    A plain-text password
 * @return    string    An md5 hashed password with salt
 */
function josHashPassword($password){
	// Salt and hash the password
	$salt = mosMakePassword(16);
	$crypt = md5($password . $salt);
	$hash = $crypt . ':' . $salt;
	return $hash;
}

/**
 * Page generation time
 * @package Joostina
 */
class mosProfiler{

	/**
	@var int Start time stamp */
	var $start = 0;
	/**
	@var string A prefix for mark messages */
	var $prefix = '';

	/**
	 * Constructor
	 * @param string A prefix for mark messages
	 */
	function mosProfiler($prefix = ''){
		$this->start = $this->getmicrotime();
		$this->prefix = $prefix;
	}

	/**
	 * @return string A format message of the elapsed time
	 */
	function mark($label){
		return sprintf("\n<div class=\"profiler\">$this->prefix %.3f $label</div>", $this->getmicrotime() - $this->start);
	}

	/**
	 * @return float The current time in milliseconds
	 */
	function getmicrotime(){
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

}

/**
 * @package Joostina
 * @abstract
 */
class mosAbstractLog{

	/**
	@var array */
	var $_log = null;

	/**
	 * Constructor
	 */
	function mosAbstractLog(){
		$this->__constructor();
	}

	/**
	 * Generic constructor
	 */
	function __constructor(){
		$this->_log = array();
	}

	/**
	 * @param string Log message
	 * @param boolean True to append to last message
	 */
	function log($text, $append = false){
		$n = count($this->_log);
		if($append && $n > 0){
			$this->_log[count($this->_log) - 1] .= $text;
		} else{
			$this->_log[] = $text;
		}
	}

	/**
	 * @param string The glue for each log item
	 * @return string Returns the log
	 */
	function getLog($glue = '<br/>', $truncate = 9000, $htmlSafe = false){
		$logs = array();
		foreach($this->_log as $log){
			if($htmlSafe){
				$log = htmlspecialchars($log);
			}
			$logs[] = substr($log, 0, $truncate);
		}
		return implode($glue, $logs);
	}

}

class errorCase{

	var $type = null;
	var $message = null;

	function errorCase($type = 1){
		$this->type = $type;
		self::_display_error();
	}

	function _display_error(){
		switch($this->type){
			case 1:
			default:
				$this->message = _MESSAGE_ERROR_404;
				break;

			case 2:
				$this->message = _MESSAGE_ERROR_403;
				break;
		}
		echo $this->message;
	}

}

/**
 * Component database table class
 * @package Joostina
 */
class mosComponent extends mosDBTable{

	/**
	@var int Primary key */
	var $id = null;
	/**
	@var string */
	var $name = null;
	/**
	@var string */
	var $link = null;
	/**
	@var int */
	var $menuid = null;
	/**
	@var int */
	var $parent = null;
	/**
	@var string */
	var $admin_menu_link = null;
	/**
	@var string */
	var $admin_menu_alt = null;
	/**
	@var string */
	var $option = null;
	/**
	@var string */
	var $ordering = null;
	/**
	@var string */
	var $admin_menu_img = null;
	/**
	@var int */
	var $iscore = null;
	/**
	@var string */
	var $params = null;
	/* @var int права доступа к компоненту */
	#var $access = null;
	var $_model = null;
	var $_controller = null;
	var $_view = null;
	var $_mainframe = null;

	/**
	 * @param database A database connector object
	 */
	function mosComponent(&$db = null){
		$this->mosDBTable('#__components', 'id', $db);
	}

	function _init($option, $mainframe){

		$this->option = $option;
		$this->_mainframe = $mainframe;

		$component = str_replace('com_', '', $this->option);

		$controller = $component . 'Controller';
		$view = $component . 'View';

		if(class_exists($view)){
			$this->_view = new $view($this->_mainframe);
		}
	}

}

/**
 * Объединение расширений системы в одно пространство имён

 */
class joostina_api{

	/**
	 * Конвертирование текста из юникода в кириллицу
	 * Чаще всего используется для Аякс функций.
	 * В качестве параметра принимает строковое значение в кодировке UTF-8, возвращает строковое значение в кодировке windows-1251
	 * $type - параметр конвертации, по умолчанию конвертируется из utf-8.
	 * */
	// это больше НЕ требуется
	public static function convert($text, $type = null){
		return $text;
	}

	/**
	 * Оптимизация таблиц базы данных
	 * Основано на мамботе OptimizeTables - smart (C) 2006, Joomlaportal.ru. All rights reserved
	 */
	public static function optimizetables(){
		if(mt_rand(1, 50) == 1){
			register_shutdown_function('_optimizetables');
		}
	}

}

class contentMeta{

	var $_params = null;

	function contentMeta($params){
		$this->_params = $params;
	}

	function set_meta(){
		if(!$this->_params){
			return;
		}

		switch($this->_params->page_type){
			case 'section_blog':
			case 'category_blog':
			case 'frontpage':
			default:
				$this->_meta_blog();
				break;

			case 'item_full':
			case 'item_static':
				$this->_meta_item();
				break;
		}
	}

	function _meta_blog(){

		$mainframe = mosMainFrame::getInstance();

		if($this->_params->menu){
			if(trim($this->_params->get('page_name'))){
				$mainframe->SetPageTitle($this->_params->menu->name, $this->_params);
			} elseif($this->_params->get('header') != ''){
				$mainframe->SetPageTitle($this->_params->get('header', 1), $this->_params);
			} else{
				$mainframe->SetPageTitle($this->_params->menu->name, $this->_params);
			}
		} else{
			$mainframe->SetPageTitle($this->_params->get('header'), $this->_params);
		}

		set_robot_metatag($this->_params->get('robots'));

		if($this->_params->get('meta_description') != ""){
			$mainframe->addMetaTag('description', $this->_params->get('meta_description'));
		} else{
			$mainframe->addMetaTag('description', $mainframe->getCfg('MetaDesc'));
		}

		if($this->_params->get('meta_keywords') != ""){
			$mainframe->addMetaTag('keywords', $this->_params->get('meta_keywords'));
		} else{
			$mainframe->addMetaTag('keywords', $mainframe->getCfg('MetaKeys'));
		}

		if($this->_params->get('meta_author') != ""){
			$mainframe->addMetaTag('author', $this->_params->get('meta_author'));
		}
	}

	function _meta_item(){
		$mainframe = mosMainFrame::getInstance();

		$row = $this->_params->object;

		$mainframe->setPageTitle($row->title, $this->_params);

		$mainframe->appendMetaTag('description', $row->description);
		$mainframe->appendMetaTag('keywords', $row->metakey);

		if($mainframe->getCfg('MetaTitle') == '1'){
			$mainframe->addMetaTag('title', $row->title);
		}
		if($mainframe->getCfg('MetaAuthor') == '1'){
			if($row->created_by_alias != ""){
				$mainframe->addMetaTag('author', $row->created_by_alias);
			} else{
				$mainframe->addMetaTag('author', $row->author);
			}
		}
		if($this->_params->get('robots') == 0){
			$mainframe->addMetaTag('robots', 'index, follow');
		}
		if($this->_params->get('robots') == 1){
			$mainframe->addMetaTag('robots', 'index, nofollow');
		}
		if($this->_params->get('robots') == 2){
			$mainframe->addMetaTag('robots', 'noindex, follow');
		}
		if($this->_params->get('robots') == 3){
			$mainframe->addMetaTag('robots', 'noindex, nofollow');
		}
	}

}

// Оптимизация таблиц базы данных
function _optimizetables(){
	$database = database::getInstance();
	$config = Jconfig::getInstance();

	$flag = $config->config_cachepath . '/optimizetables.flag';
	$filetime = @filemtime($flag);
	$currenttime = time();
	if($filetime + 86400 > $currenttime){
		return;
	}
	$f = fopen($flag, 'w+');
	@fwrite($f, time());
	fclose($f);
	@chmod($flag, 0777);
	$database->setQuery('SHOW TABLES FROM `' . $config->config_db . '`');
	$tables = $database->loadResultArray();
	foreach($tables as $table){
		$database->setQuery("OPTIMIZE TABLE `$table`;");
		$database->query();
	}
	return;
}


/**
@global mosPlugin $_MAMBOTS */
$_MAMBOTS = new mosMambotHandler();