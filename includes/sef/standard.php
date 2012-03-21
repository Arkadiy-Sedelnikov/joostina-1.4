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
	}
    elseif (in_array('component', $url_array)) {

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
			if (isset($parts['option']) && $parts['option'] == 'com_search' && isset($parts['tag'])) {
				$string = 'tag/' . $parts['tag'];
			} elseif (isset($parts['option']) && $parts['option'] == 'com_users' && isset($parts['task']) && $parts['task'] == 'register') {
				$string = 'register/';
			} elseif (isset($parts['option']) && $parts['option'] == 'com_users' && isset($parts['task']) && $parts['task'] == 'lostPassword') {
				$string = 'lostpassword/';
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