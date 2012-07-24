<?php
/**
 * @package   Joostina Lotos
 * @copyright Авторские права (C) 2011-2012 Joostina Lotos. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 *            Joostina Lotos! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @autor     Gold Dragon (http://gd.joostina-cms.ru)
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Основной класс для обработки SEF
 */
class JSef{

	/** разделитель параметр-значение */
	const separator = '-';

	/** @var object библиотека JLIni */
	private static $_lib_ini;

	/** @var object библиотека JLUrl */
	private static $_lib_url;

	/** @var object библиотека JLText */
	private static $_lib_text;

	/** @var array Хранит запросы к базе */
	private static $_sef_url = array();

	/** @var int разрешён ли SEF */
	public static $cfg_sef;

	/** @var int очищать ссылку на главную */
	public static $cfg_frontpage;

	/** @var string имя компонента */
	public static $option;

	/** @var array массив sef-файлов компонентов */
	public static $sef_files;


	/**
	 * @static Подключение класса
	 *
	 * @param int $cfg_sef
	 * @param int $cfg_frontpage
	 *
	 * @return object JSef
	 */
	public static function getInstance($cfg_sef = 0, $cfg_frontpage = 0){
		//используется ли SEF
		if($cfg_sef){
			// запоминаем настройки
			self::$cfg_sef = (int)$cfg_sef;
			self::$cfg_frontpage = (int)$cfg_frontpage;

			// Подключаем необходимые библиотеки
			JCore::getLib('url');
			self::$_lib_url = JLUrl::getInstance();

			JCore::getLib('ini');
			self::$_lib_ini = JLIni::getInstance();

			JCore::getLib('text');
			self::$_lib_text = JLText::getInstance();

			// имя компонента в адресной строке
			self::$option = self::getOption();

			// список sef-файлов
			$sef_com = scandir(_JLPATH_SEF . DS);
			foreach($sef_com as $value){
				if(preg_match('#\.sef\.ini$#i', $value, $tmp)){
					self::$sef_files[] = $value;
				}
			}

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
				} else{
					self::getSefToUrl();
				}
			}
		}
	}

	private static function checkINI($option_ini){
		$option_ini = $option_ini . '.sef.ini';
		// проверяем есть ли отработчик для компонента и включен ли он
		if(in_array($option_ini, self::$sef_files)){
			// подключаем библиотеку для обработки INI-файлов
			self::$_lib_ini->parse(_JLPATH_SEF . DS . $option_ini);
			$result = (int)self::$_lib_ini->checkKey('sef');
		} else{ // Стандартный обработчик
			$result = null;
		}
		return $result;
	}

	/**
	 * @static Получение имени компонета
	 *
	 * @param string $link - нормальная ссылка
	 *
	 * @return string - имя компонента
	 */
	private static function getOption($link = null){
		if(!$link){
			$option = (isset($_REQUEST['option'])) ? trim(strval(strtolower($_REQUEST['option']))) : '';
			if($option == ''){
				$link = $_SERVER['REQUEST_URI'];
				$link = explode("/", preg_replace('#(^\/)|(\/$)#', '', $link));
				$option = (isset($link[0])) ? $link[0] : '';
			}
		} else{
			self::$_lib_url->parse($link);
			$option = self::$_lib_url->getVar('option', '');
		}
		return $option;
	}

	/**
	 * @static преобразует нормальную ссылку в Sef-ссылку
	 *
	 * @param $link - нормальная ссылка
	 *
	 * @return mixed - sef-ссылка
	 */
	public static function getUrlToSef($link){
		if(self::$cfg_sef){

			$link_url = $link;
			// Парсим URL
			self::$_lib_url->parse($link);
			if(self::$_lib_url->_path != 'ajax.index.php'){
				// Проверяем, существует ли sef-файл и включен ли он
				if(self::checkINI(self::getOption($link))){
					$url_vars = self::$_lib_url->_vars;

					$database = database::getInstance();

					self::$_lib_ini->parse(_JLPATH_LIBRARIES . DS . self::getOption($link));

					/////////////////////////////////////////////////////////////
					// получаем имя компонента
					$option_show = (int)self::$_lib_ini->getValue('option_show');
					$option_name = self::$_lib_ini->getValue('option_name');
					$option_sql = self::$_lib_ini->getValue('option_sql');

					if($option_show == 1 and $option_name != ''){
						$option = $option_name;
					} elseif($option_show == 2 and $option_sql != ''){

						// получаем из запроса данные для замены
						$tmp1 = preg_match_all("#\[\[([a-z0-9-_]*)\]\]#i", $option_sql, $sql_arr);

						// Производим замену
						if($tmp1){
							foreach($sql_arr[1] as $tmp2){
								$tmp_search[] = "[[" . $tmp2 . "]]";
								$tmp_replace[] = self::$_lib_url->getVar($tmp2);
							}
							$option_sql = str_replace($tmp_search, $tmp_replace, $option_sql);
						}
						$option = self::_checkSefUrl($option_sql);
					} else{
						$option = self::getOption($link);
					}
					// Транслитерация
					$option = '/' . self::$_lib_text->text_to_url($option);

					// Удаляем из зараметров option
					if(isset($url_vars['option'])){
						unset($url_vars['option']);
					}

					/////////////////////////////////////////////////////////////
					// Получаем task
					$task_prm_arr = self::$_lib_ini->getValue('task_prm');
					$task_val_arr = self::$_lib_ini->getValue('task_val');
					$task_sql_arr = self::$_lib_ini->getValue('task_sql');

					$tmp_key = array_search(self::$_lib_url->getVar('task'), $task_prm_arr);
					if($tmp_key){
						if(isset($task_val_arr[$tmp_key]) and $task_val_arr[$tmp_key] != ''){
							$task = $task_val_arr[$tmp_key];
						} elseif(isset($task_sql_arr[$tmp_key]) and $task_sql_arr[$tmp_key] != ''){
							// получаем из запроса данные для замены
							$tmp1 = preg_match_all("#\[\[([a-z0-9-_]*)\]\]#i", $task_sql_arr[$tmp_key], $sql_arr);
							// Производим замену
							if($tmp1){
								foreach($sql_arr[1] as $tmp2){
									$tmp_search[] = "[[" . $tmp2 . "]]";
									$tmp_replace[] = self::$_lib_url->getVar($tmp2);
								}
								$task_sql_arr[$tmp_key] = str_replace($tmp_search, $tmp_replace, $task_sql_arr[$tmp_key]);
							}
							$task = self::_checkSefUrl($task_sql_arr[$tmp_key]);
						} else{
							$task = $task_prm_arr[$tmp_key];
						}
					}

					// Транслитерация
					$task = (isset($task)) ? '/' . self::$_lib_text->text_to_url($task) : '';

					// Удаляем из зараметров option
					if(isset($url_vars['task'])){
						unset($url_vars['task']);
					}

					/////////////////////////////////////////////////////////////
					// Получаем params

					$params = self::$_lib_ini->getValue('param');

					// Сортировка порядка отображения параметров
					$orders = self::$_lib_ini->getValue('order');
					$tmp1 = array();
					foreach($orders as $order){
						if(array_key_exists($order, $url_vars)){
							$tmp1[$order] = $url_vars[$order];
							unset($url_vars[$order]);
						}
					}
					$url_vars = array_merge($tmp1, $url_vars);
					unset($tmp1);

					// Добавляем параметры
					$param = '';
					foreach($url_vars as $key => $var){
						if(array_search($key, $params) === false){
							$param .= '/' . self::$_lib_text->text_to_url($var);
						}
					}

					// Добавляем окончание
					$tmp_key = array_search(self::$_lib_url->getVar('task'), self::$_lib_ini->getValue('task_html'));
					$param .= ($tmp_key === false) ? '/' : '.html';

					// Добавляем "Якорь"
					$fragment = (self::$_lib_url->_fragment) ? '#' . self::$_lib_text->text_to_url(self::$_lib_url->_fragment) : '';

					// Формируем ссылку для базы
					$link = $option . $task . $param . $fragment;

					$sql = "SELECT `url` FROM `#__sef_link` WHERE `sef`='" . $link . "' ";

					$link_sef = self::_checkSefUrl($sql);

					if($link_sef === null){
						$sql = "INSERT INTO `#__sef_link` (`url`, `sef`)
							VALUES (
							'" . $link_url . "',
							'" . $link . "'
							);";
						$database->setQuery($sql);
						$database->query();
					} elseif($link_sef and (self::$_lib_url->compareUrls($link_sef, $link_url) == false)){
						$sql = "INSERT INTO `#__sef_duplicate` (`id`, `url`, `sef`)
							VALUES (
							'',
							'" . $link_url . "',
							'" . $link . "'
							);";
						$database->setQuery($sql);
						$database->query();
					}

					// формируем окончательно ссылку
					$link = JPATH_SITE . $link;
				} else{
					$option = '';
					$fragment = '';
					// если ссылка идёт на компонент главной страницы - очистим её
					if((JSef::$cfg_frontpage AND stripos($link, 'option=com_frontpage') > 0 AND !(stripos($link, 'limit'))) OR $link == 'index.php' OR $link == 'index.php?'){
						$link = JPATH_SITE . '/';
					} else{
						// Оснавная обработка
						$link = str_replace('&amp;', '&', $link);

						// Разбирает URL и возвращает его компоненты
						$url = parse_url($link);

						// проверяем часть fragment (после знака диеза #)
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
								if($key != 'option'){
									$link .= $key . self::separator . $value . '/';
								} else{
									$option = $value . '/';
								}
							}
						}
						$link = JPATH_SITE . '/' . $option . $link . $fragment;
					}
				}
			}else{
				$link = JPATH_SITE . '/' . $link;
			}
		}
		return $link;
	}

	/**
	 * @static Загрузка параметров из SEF-url в глобальные
	 * @return mixed
	 */
	public static function getSefToUrl(){
		$database = database::getInstance();
		// получаем URL
		$link = $_SERVER['REQUEST_URI'];

		// Проверяем существует ли обратная ссылка в базе
		$sql = "SELECT `url` FROM `#__sef_link` WHERE `sef`='" . $link . "' ";
		$result = self::_checkSefUrl($sql);
		if(!is_null($result)){
			self::$_lib_url->parse($result);
			$url_vars = self::$_lib_url->_vars;
			foreach($url_vars as $key => $value){
				$_GET[$key] = $value;
				$_REQUEST[$key] = $value;
			}
		} else{
			// получаем массив с параметрами
			$url = explode("/", $link);

			// присваиваем значения глобальным переменным
			$option = false;
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

	/**
	 * @static Проверяет есть ли уже в массиве значение из базы, если нет, то получает из базы
	 *
	 * @param string $sql - SQL-запрос, который получает одно значение
	 *
	 * @return string - результат
	 */
	private static function _checkSefUrl($sql){
		//получаем ключ из SQL-запроса
		$key = md5($sql);

		if(array_key_exists($key, self::$_sef_url)){ // если такое значение уже есть
			return self::$_sef_url[$key];
		} else{ // если нет , то получаем из базы
			$database = database::getInstance();
			$database->setQuery($sql);
			$result = $database->loadResult();

			// записываем в массив результат
			self::$_sef_url[$key] = $result;
			return $result;
		}
	}

}