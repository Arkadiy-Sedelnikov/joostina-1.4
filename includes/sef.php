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

global $mosConfig_sef;

// редиректить ли с не-sef адресов
DEFINE('_SEF_REDIRECT', true);
// удалять из ссылок парамтер ItemId
DEFINE('_SEF_DELETE_ITEMID', false);

if ($mosConfig_sef) {

	if (_SEF_REDIRECT == true) {
		// перебрасываем на корректный адрес
		if (ltrim(strpos($_SERVER['REQUEST_URI'], 'index.php'), '/') == 1 && $_SERVER['REQUEST_METHOD'] == 'GET') { //Проверка SEF ли урл, т.е. вначале стоит index.php
			$bSefGoto = true; //Флаг перехода
			//Проверка компонентов
			$sSef_option = mosGetParam($_GET, 'option', ''); // Получение опции (компонента)
			$sSef_task = mosGetParam($_GET, 'task', '');   // Получение задачи
			$sSef_tp = mosGetParam($_GET, 'tp', 0);   // Предпросмотр
			//Режим предпросмотра и компонент поиска
			$bSefGoto = ($sSef_tp == '1' || $sSef_option == 'com_search' ) ? false : true;

			//Компонент контекста (статьи, новости)
			if ($sSef_option == 'com_content') {
				$aConTask = array('edit', 'new', 'mycontent'); //Задачи компонента, если надо добавить свою задачу, добавьте в массив
				if (in_array($sSef_task, $aConTask)) { //Если текущая задача в списке запрещенных, то пропускаем ее
					$bSefGoto = false;
				}
			}
			if ($bSefGoto == true) { //Переход
				$url = sefRelToAbs('index.php?' . $_SERVER['QUERY_STRING']); //Преобразование урл
				header("Location: " . $url, TRUE, 301); //Формирование заголовка с перенаправлением
				exit(301); //Завершение работы, с отдачей кода завершения
			}
		}
	}

	$QUERY_STRING = '';

	$url_array = explode('/', $_SERVER['REQUEST_URI']);

	// обрабатываем некоторые красивые ссыцлки
	if (in_array('tag', $url_array)) {
		$_GET['option'] = 'com_search';
		$_REQUEST['option'] = 'com_search';
		$pos = array_search('tag', $url_array);
		$_GET['tag'] = $url_array[$pos + 1];

		$QUERY_STRING = 'option=com_search&tag=' . $_GET['tag'];
	} elseif (in_array('register', $url_array)) {
		$_GET['option'] = 'com_users';
		$_REQUEST['option'] = 'com_users';
		$_GET['task'] = 'register';
		$_REQUEST['task'] = 'register';

		$QUERY_STRING = 'option=com_users&task=register';
	} elseif (in_array('lostpassword', $url_array)) {
		$_GET['option'] = 'com_users';
		$_REQUEST['option'] = 'com_users';
		$_GET['task'] = 'lostPassword';
		$_REQUEST['task'] = 'lostPassword';

		$QUERY_STRING = 'option=com_users&task=lostPassword';
	} elseif (strpos($_SERVER['REQUEST_URI'], 'sitemap.xml')) {
		$_GET['option'] = 'com_xmap';
		$_REQUEST['option'] = 'com_xmap';
		$_GET['sitemap'] = '1';
		$_REQUEST['sitemap'] = '1';
		$_GET['view'] = 'xml';
		$_REQUEST['view'] = 'xml';
		$_GET['no_html'] = '1';
		$_REQUEST['no_html'] = '1';

		$QUERY_STRING = 'option=com_xmap&sitemap=1&view=xml&no_html=1';
	} elseif (in_array('content', $url_array)) { // обработка компонента com_content

		/**
		 * Content
		 * http://www.domain.com/$option/$task/$sectionid/$id/$Itemid/$limit/$limitstart
		 */
		$uri = explode('content/', $_SERVER['REQUEST_URI']);
		$option = 'com_content';
		$_GET['option'] = $option;
		$_REQUEST['option'] = $option;
		$pos = array_search('content', $url_array);

		// TODO проверить правильно формирования и работы языком
		$lang = $url_array[$pos - 1];
		if (!strcasecmp(substr($lang, 0, 5), 'lang,')) {
			$temp = explode(',', $lang);
			if (isset($temp[0]) && $temp[0] != '' && isset($temp[1]) && $temp[1] != '') {
				$_GET['lang'] = $temp[1];
				$_REQUEST['lang'] = $temp[1];
				$lang = $temp[1];
			}
			unset($url_array[$pos - 1]);
		}

		if (isset($url_array[$pos + 8]) && $url_array[$pos + 8] != '' && in_array('category', $url_array) && (strpos($url_array[$pos + 5], 'order,') !== false) && (strpos($url_array[$pos + 6], 'filter,') !== false)) {
			// $option/$task/$sectionid/$id/$Itemid/$order/$filter/$limit/$limitstart
			$task = $url_array[$pos + 1];
			$sectionid = $url_array[$pos + 2];
			$id = $url_array[$pos + 3];
			$Itemid = $url_array[$pos + 4];
			$order = str_replace('order,', '', $url_array[$pos + 5]);
			$filter = urldecode(str_replace('filter,', '', $url_array[$pos + 6]));
			$limit = $url_array[$pos + 7];
			$limitstart = $url_array[$pos + 8];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['sectionid'] = $sectionid;
			$_REQUEST['sectionid'] = $sectionid;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['Itemid'] = $Itemid;
			$_REQUEST['Itemid'] = $Itemid;
			$_GET['order'] = $order;
			$_REQUEST['order'] = $order;
			$_GET['filter'] = $filter;
			$_REQUEST['filter'] = $filter;
			$_GET['limit'] = $limit;
			$_REQUEST['limit'] = $limit;
			$_GET['limitstart'] = $limitstart;
			$_REQUEST['limitstart'] = $limitstart;

			$QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&order=$order&filter=$filter&limit=$limit&limitstart=$limitstart";
		} elseif (isset($url_array[$pos + 7]) && $url_array[$pos + 7] != '' && $url_array[$pos + 5] > 1000 && (in_array('archivecategory', $url_array) || in_array('archivesection', $url_array))) {
			// $option/$task/$id/$limit/$limitstart/year/month/module
			$task = $url_array[$pos + 1];
			$id = $url_array[$pos + 2];
			$limit = $url_array[$pos + 3];
			$limitstart = $url_array[$pos + 4];
			$year = $url_array[$pos + 5];
			$month = $url_array[$pos + 6];
			$module = $url_array[$pos + 7];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['limit'] = $limit;
			$_REQUEST['limit'] = $limit;
			$_GET['limitstart'] = $limitstart;
			$_REQUEST['limitstart'] = $limitstart;
			$_GET['year'] = $year;
			$_REQUEST['year'] = $year;
			$_GET['month'] = $month;
			$_REQUEST['month'] = $month;
			$_GET['module'] = $module;
			$_REQUEST['module'] = $module;

			$QUERY_STRING = "option=com_content&task=$task&id=$id&limit=$limit&limitstart=$limitstart&year=$year&month=$month&module=$module";
		} elseif (isset($url_array[$pos + 7]) && $url_array[$pos + 7] != '' && $url_array[$pos + 6] > 1000 && (in_array('archivecategory', $url_array) || in_array('archivesection', $url_array))) {
			// $option/$task/$id/$Itemid/$limit/$limitstart/year/month
			$task = $url_array[$pos + 1];
			$id = $url_array[$pos + 2];
			$Itemid = $url_array[$pos + 3];
			$limit = $url_array[$pos + 4];
			$limitstart = $url_array[$pos + 5];
			$year = $url_array[$pos + 6];
			$month = $url_array[$pos + 7];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['Itemid'] = $Itemid;
			$_REQUEST['Itemid'] = $Itemid;
			$_GET['limit'] = $limit;
			$_REQUEST['limit'] = $limit;
			$_GET['limitstart'] = $limitstart;
			$_REQUEST['limitstart'] = $limitstart;
			$_GET['year'] = $year;
			$_REQUEST['year'] = $year;
			$_GET['month'] = $month;
			$_REQUEST['month'] = $month;

			$QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart&year=$year&month=$month";
		} elseif (isset($url_array[$pos + 7]) && $url_array[$pos + 7] != '' && in_array('category', $url_array) && (strpos($url_array[$pos + 5], 'order,') !== false)) {
			// $option/$task/$sectionid/$id/$Itemid/$order/$limit/$limitstart
			$task = $url_array[$pos + 1];
			$sectionid = $url_array[$pos + 2];
			$id = $url_array[$pos + 3];
			$Itemid = $url_array[$pos + 4];
			$order = str_replace('order,', '', $url_array[$pos + 5]);
			$limit = $url_array[$pos + 6];
			$limitstart = $url_array[$pos + 7];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['sectionid'] = $sectionid;
			$_REQUEST['sectionid'] = $sectionid;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['Itemid'] = $Itemid;
			$_REQUEST['Itemid'] = $Itemid;
			$_GET['order'] = $order;
			$_REQUEST['order'] = $order;
			$_GET['limit'] = $limit;
			$_REQUEST['limit'] = $limit;
			$_GET['limitstart'] = $limitstart;
			$_REQUEST['limitstart'] = $limitstart;

			$QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&order=$order&limit=$limit&limitstart=$limitstart";
		} elseif (isset($url_array[$pos + 6]) && $url_array[$pos + 6] != '') {
			// $option/$task/$sectionid/$id/$Itemid/$limit/$limitstart
			$task = $url_array[$pos + 1];
			$sectionid = $url_array[$pos + 2];
			$id = $url_array[$pos + 3];
			$Itemid = $url_array[$pos + 4];
			$limit = $url_array[$pos + 5];
			$limitstart = $url_array[$pos + 6];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['sectionid'] = $sectionid;
			$_REQUEST['sectionid'] = $sectionid;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['Itemid'] = $Itemid;
			$_REQUEST['Itemid'] = $Itemid;
			$_GET['limit'] = $limit;
			$_REQUEST['limit'] = $limit;
			$_GET['limitstart'] = $limitstart;
			$_REQUEST['limitstart'] = $limitstart;

			$QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart";
		} elseif (isset($url_array[$pos + 5]) && $url_array[$pos + 5] != '') {
			// $option/$task/$id/$Itemid/$limit/$limitstart
			$task = $url_array[$pos + 1];
			$id = $url_array[$pos + 2];
			$Itemid = $url_array[$pos + 3];
			$limit = $url_array[$pos + 4];
			$limitstart = $url_array[$pos + 5];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['Itemid'] = $Itemid;
			$_REQUEST['Itemid'] = $Itemid;
			$_GET['limit'] = $limit;
			$_REQUEST['limit'] = $limit;
			$_GET['limitstart'] = $limitstart;
			$_REQUEST['limitstart'] = $limitstart;

			$QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart";
		} elseif (isset($url_array[$pos + 4]) && $url_array[$pos + 4] != '' && (in_array('archivecategory', $url_array) || in_array('archivesection', $url_array))) {
			// $option/$task/$year/$month/$module
			$task = $url_array[$pos + 1];
			$year = $url_array[$pos + 2];
			$month = $url_array[$pos + 3];
			$module = $url_array[$pos + 4];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['year'] = $year;
			$_REQUEST['year'] = $year;
			$_GET['month'] = $month;
			$_REQUEST['month'] = $month;
			$_GET['module'] = $module;
			$_REQUEST['module'] = $module;

			$QUERY_STRING = "option=com_content&task=$task&year=$year&month=$month&module=$module";
		} elseif (!(isset($url_array[$pos + 5]) && $url_array[$pos + 5] != '') && isset($url_array[$pos + 4]) && $url_array[$pos + 4] != '') {
			// $option/$task/$sectionid/$id/$Itemid
			$task = $url_array[$pos + 1];
			$sectionid = $url_array[$pos + 2];
			$id = $url_array[$pos + 3];
			$Itemid = $url_array[$pos + 4];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['sectionid'] = $sectionid;
			$_REQUEST['sectionid'] = $sectionid;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['Itemid'] = $Itemid;
			$_REQUEST['Itemid'] = $Itemid;

			$QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid";
		} elseif (!(isset($url_array[$pos + 4]) && $url_array[$pos + 4] != '') && (isset($url_array[$pos + 3]) && $url_array[$pos + 3] != '')) {
			// $option/$task/$id/$Itemid
			$task = $url_array[$pos + 1];
			$id = $url_array[$pos + 2];
			$Itemid = $url_array[$pos + 3];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;
			$_GET['Itemid'] = $Itemid;
			$_REQUEST['Itemid'] = $Itemid;

			$QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid";
		} elseif (!(isset($url_array[$pos + 3]) && $url_array[$pos + 3] != '') && (isset($url_array[$pos + 2]) && $url_array[$pos + 2] != '')) {
			// $option/$task/$id
			$task = $url_array[$pos + 1];
			$id = $url_array[$pos + 2];

			// pass data onto global variables
			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;
			$_GET['id'] = $id;
			$_REQUEST['id'] = $id;

			$QUERY_STRING = "option=com_content&task=$task&id=$id";
		} elseif (!(isset($url_array[$pos + 2]) && $url_array[$pos + 2] != '') && (isset($url_array[$pos + 1]) && $url_array[$pos + 1] != '')) {
			// $option/$task
			$task = $url_array[$pos + 1];

			$_GET['task'] = $task;
			$_REQUEST['task'] = $task;

			$QUERY_STRING = 'option=com_content&task=' . $task;
		}

		$QUERY_STRING = ($lang == '') ? $QUERY_STRING : $QUERY_STRING .= '&amp;lang=' . $lang;

		$_SERVER['QUERY_STRING'] = $QUERY_STRING;
		$REQUEST_URI = $uri[0] . 'index.php?' . $QUERY_STRING;
		$_SERVER['REQUEST_URI'] = $REQUEST_URI;
	} elseif (in_array('component', $url_array)) {

		/*
		 * Components
		 * http://www.domain.com/component/$name,$value
		 */
		$uri = explode('component/', $_SERVER['REQUEST_URI']);
		$uri_array = explode('/', $uri[1]);
		$QUERY_STRING = '';

		foreach ($uri_array as $value) {
			$temp = explode(',', $value);
			if (isset($temp[0]) && $temp[0] != '' && isset($temp[1]) && $temp[1] != '') {
				$_GET[$temp[0]] = $temp[1];
				$_REQUEST[$temp[0]] = $temp[1];

				// проверка на сущестрование каталога запрашиваемого компонента
				if ($temp[0] == 'option') {
					if (!is_dir(JPATH_BASE . '/components/' . $temp[1])) {
						header('HTTP/1.0 404 Not Found');
						require_once(JPATH_BASE . '/templates/system/404.php');
						exit(404);
					}
				}

				if ($QUERY_STRING == '') {
					$QUERY_STRING .= "$temp[0]=$temp[1]";
				} else {
					$QUERY_STRING .= "&$temp[0]=$temp[1]";
				}
			}
		}

		$_SERVER['QUERY_STRING'] = $QUERY_STRING;
		$REQUEST_URI = $uri[0] . 'index.php?' . $QUERY_STRING;
		$_SERVER['REQUEST_URI'] = $REQUEST_URI;
	} else {
		/*
		 * Unknown content
		 * http://www.domain.com/unknown
		 */
		$jdir = str_replace('index.php', '', $_SERVER['PHP_SELF']);
		$juri = str_replace($jdir, '', $_SERVER['REQUEST_URI']);
//  TODO раскомментировать при ошибках с SEF
//			if($juri != '' && $juri != '/' && !eregi("index\.php",$_SERVER['REQUEST_URI']) && !eregi("index2\.php",$_SERVER['REQUEST_URI']) && !eregi("/\?",$_SERVER['REQUEST_URI']) && $_SERVER['QUERY_STRING'] == '') {
		if ($juri != '' && $juri != '/' && !preg_match("/index.php/i", $_SERVER['REQUEST_URI']) && !preg_match("/index2.php/i", $_SERVER['REQUEST_URI']) && !preg_match("/\?/i", $_SERVER['REQUEST_URI']) && $_SERVER['QUERY_STRING'] == '') {
			header('HTTP/1.0 404 Not Found');
			require_once(JPATH_BASE . '/templates/system/404.php');
			exit(404);
		}
	}
}

unset($url_array, $jdir, $juri);

/**
 * Converts an absolute URL to SEF format
 * @param string The URL
 * @return string
 */
function sefRelToAbs($string) {
	global $mosConfig_sef, $mosConfig_multilingual_support;
	global $mosConfig_com_frontpage_clear;

	//multilingual code url support
	if ($mosConfig_sef && $mosConfig_multilingual_support && $string != 'index.php' && !preg_match("/^(([^:\/\?#]+):)/i", $string) && !strcasecmp(substr($string, 0, 9), 'index.php') && !preg_match('/lang=/', $string)) {
		$string .= '&amp;lang=' . _LANGUAGE;
	}

	// если ссылка идёт на компонент главной страницы - очистим её
	if ($mosConfig_sef && $mosConfig_com_frontpage_clear && strpos($string, 'option=com_frontpage') > 0 && !(strpos($string, 'limit'))) {
		$string = '';
	}

	// SEF URL Handling
	if ($mosConfig_sef && !preg_match("/^(([^:\/\?#]+):)/i", $string) && !strcasecmp(substr($string, 0, 9), 'index.php')) {
		$string = str_replace('&amp;', '&', $string);

		// Home index.php
		if ($string == 'index.php') {
			$string = '';
		}

		// break link into url component parts
		$url = parse_url($string);

		// check if link contained fragment identifiers (ex. #foo)
		$fragment = '';
		if (isset($url['fragment'])) {
			// ensure fragment identifiers are compatible with HTML4
			if (preg_match('@^[A-Za-z][A-Za-z0-9:_.-]*$@', $url['fragment'])) {
				$fragment = '#' . $url['fragment'];
			}
		}

		// check if link contained a query component
		if (isset($url['query'])) {
			// special handling for javascript
			$url['query'] = stripslashes(str_replace('+', '%2b', $url['query']));
			// clean possible xss attacks
			$url['query'] = preg_replace("'%3Cscript[^%3E]*%3E.*?%3C/script%3E'si", '', $url['query']);

			// break url into component parts
			parse_str($url['query'], $parts);

			// TODO удаляем Itemid
			if (_SEF_DELETE_ITEMID == true) {
				unset($parts['Itemid'], $parts['ItemId']);
			}
			$sefstring = '';

			// Component com_content urls
			if (((isset($parts['option']) && ($parts['option'] == 'com_content' || $parts['option'] == 'content'))) && ($parts['task'] != 'new') && ($parts['task'] != 'edit') && ($parts['task'] != 'mycontent')) {
				// index.php?option=com_content [&task=$task] [&sectionid=$sectionid] [&id=$id] [&Itemid=$Itemid] [&limit=$limit] [&limitstart=$limitstart] [&year=$year] [&month=$month] [&module=$module]
				$sefstring .= 'content/';

				// task
				if (isset($parts['task'])) {
					$sefstring .= $parts['task'] . '/';
				}
				// sectionid
				if (isset($parts['sectionid'])) {
					$sefstring .= $parts['sectionid'] . '/';
				}
				// id
				if (isset($parts['id'])) {
					$sefstring .= $parts['id'] . '/';
				}
				// Itemid
				if (isset($parts['Itemid'])) {
					//only add Itemid value if it does not correspond with the 'unassigned' Itemid value
					if ($parts['Itemid'] != 99999999 && $parts['Itemid'] != 0) {
						$sefstring .= $parts['Itemid'] . '/';
					}
				}
				// order
				if (isset($parts['order'])) {
					$sefstring .= 'order,' . $parts['order'] . '/';
				}
				// filter
				if (isset($parts['filter'])) {
					$sefstring .= 'filter,' . $parts['filter'] . '/';
				}
				// limit
				if (isset($parts['limit'])) {
					$sefstring .= $parts['limit'] . '/';
				}
				// limitstart
				if (isset($parts['limitstart'])) {
					$sefstring .= $parts['limitstart'] . '/';
				}
				// year
				if (isset($parts['year'])) {
					$sefstring .= $parts['year'] . '/';
				}
				// month
				if (isset($parts['month'])) {
					$sefstring .= $parts['month'] . '/';
				}
				// module
				if (isset($parts['module'])) {
					$sefstring .= $parts['module'] . '/';
				}
				// lang
				if (isset($parts['lang'])) {
					$sefstring .= 'lang,' . $parts['lang'] . '/';
				}
				// user
				if (isset($parts['user'])) {
					$sefstring .= 'user,' . $parts['user'] . '/';
				}

				$string = $sefstring;

				// all other components
				// index.php?option=com_xxxx &...
			} elseif (isset($parts['option']) && $parts['option'] == 'com_search' && isset($parts['tag'])) {
				$string = 'tag/' . $parts['tag'];
			} elseif (isset($parts['option']) && $parts['option'] == 'com_users' && isset($parts['task']) && $parts['task'] == 'register') {
				$string = 'register/';
			} elseif (isset($parts['option']) && $parts['option'] == 'com_users' && isset($parts['task']) && $parts['task'] == 'lostPassword') {
				$string = 'lostpassword/';
			} elseif (isset($parts['option']) && (strpos($parts['option'], 'com_') !== false)) {
				// do not SEF where com_content - `edit` or `new` task link
				if (!(($parts['option'] == 'com_content') && ((isset($parts['task']) == 'new') || (isset($parts['task']) == 'edit')))) {
					$sefstring = 'component/';
					foreach ($parts as $key => $value) {
						// special handling for javascript
						$parts[$key] = (strpos($value, '+') !== false) ? stripslashes(str_replace('%2b', '+', $value)) : $parts[$key];

						// remove slashes automatically added by parse_str
						$value = stripslashes($value);
						$sefstring .= $key . ',' . $value . '/';
					}

					$string = str_replace('=', ',', $sefstring);
				}
			}
			// no query given. Empty $string to get only the fragment
			// index.php#anchor or index.php?#anchor
		} else {
			$string = '';
		}

		// allows SEF without mod_rewrite
		// comment line below if you dont have mod_rewrite
		return JPATH_SITE . '/' . $string . $fragment;

		// allows SEF without mod_rewrite
		// uncomment Line 512 and comment out Line 514
		// uncomment line below if you dont have mod_rewrite
		// return $mosConfig_live_site .'/index.php/'. $string . $fragment;
		// If the above doesnt work - try uncommenting this line instead
		// return $mosConfig_live_site .'/index.php?/'. $string . $fragment;
	} else {

		if (_SEF_DELETE_ITEMID) {
			$string = str_replace('&amp;', '&', $string);
			$string = parse_url($string);

			if (isset($string['host'])) {
				return isset($string['scheme']) ? $string['scheme'] . '://' . $string['host'] : $string['host'];
			}

			isset($string['query']) ? parse_str($string['query'], $q) : ( $q = array() );
			// TODO удаляем Itemid !!!
			unset($q['Itemid'], $q['ItemId']);

			$string = isset($string['path']) ? $string['path'] : '';
			$string .= '?' . http_build_query($q);
		}


		// Handling for when SEF is not activated
		// Relative link handling
		if ((strpos($string, JPATH_SITE) !== 0)) {
			// if URI starts with a "/", means URL is at the root of the host...
			if (strncmp($string, '/', 1) == 0) {
				$live_site_parts = array();
				preg_match("/^(https?:[\/]+[^\/]+)(.*$)/i", JPATH_SITE, $live_site_parts);

				$string = $live_site_parts[1] . $string;
			} else {
				$check = 1;

				// array list of non http/https	URL schemes
				$url_schemes = explode(', ', _URL_SCHEMES);
				$url_schemes[] = 'http:';
				$url_schemes[] = 'https:';

				foreach ($url_schemes as $url) {
					if (strpos($string, $url) === 0) {
						$check = 0;
					}
				}

				if ($check) {
					$string = JPATH_SITE . '/' . $string;
				}
			}
		}

		return $string;
	}
}
