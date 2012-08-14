<?php

/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
// запрет прямого доступа
defined('_JLINDEX') or die();

require_once ($mainframe->getPath('class', 'com_search'));
require_once ($mainframe->getPath('front_html'));
$mainframe->addLib('text');

$tag = urldecode(mosGetParam($_GET, 'tag', ''));

if($tag){
	search_by_tag($tag);
} else{
	$mainframe->setPageTitle(_SEARCH);
	viewSearch();
}

function search_by_tag($tag){
	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	$items = new contentTags($database);

	/**
	 * Формируем "поисковые группы" )
	 * Каждая группа - относится к какому-то конкретному компоненту
	 * >    'group_name' - имя группы, должно совпадать с названием компонента
	и названием типа объекта (который записывается в таблицу с тэгами)
	 * >     'group_title' - заголовок группы. Это значение может использоваться
	при выводе результатов поиска по тэгу. Так как результаты группируются на странице,
	логично  было бы указывать заголовки групп
	 * >    'table' - таблица, в которой хранятся записи
	 * >    'id' - ключевое поле/идентификатор записи
	 * >    'title' - поле, значение из которого будет использоваться для вывода заголовков результатов
	 * >    'text' -  поле, значение из которого будет использоваться для вывода текстов результатов
	 * >    'date' -  поле, значение из которого будет использоваться для вывода даты создания записи в списке результатов
	 *  В случае, если ссылка для перехода к найденному объекту выглядит просто
	(например, как в com_content: task=view&id=$item->id)
	никаких дополнительных манипуляций не требуется. Достаточно прописать значение 'task'
	 * >    'task' -  обращение к странице полного просмотра объекта поиска
	 * Если же специфика компонента предусматривает переход к записи по ссылке, содержащей дополнительные параметры,
	 * необходимо описать эти параметры в 'url_params'
	 * Допустим: нам необходимо сформировать ссылку на объект компонента com_content, указав дополнительно в параметрах
	 * ссылки id категории и раздела, к которым принадлежит найденный материал. А также нужно передать еще один просто параметр
	 * 'page_type' со значением, например, 'simple'
	 * Т.е. ссылка на переход к странице со статьёй должен выглядеть примерно так:
	index.php?option=com_content&task=view&id=[значение_id]&page_type=simple&category=[id_категории]&section=[id_раздела]
	 *  index.php? - будет подставлено автоматически
	 *  option=com_content - будет автоматически сформировано из типа объекта (obj_type в таблице с тэгами)
	 *  task=view   -  будет автоматически сформировано со значением поля 'task'
	 *  id=[значение_id]   - формируется автоматически
	 *  остальные параметры будем формировать вручную:
	---  'url_params'=>'page_type=simple&category=%category&section=%section'
	 *  >>>  знак '%' обозначает, что в качестве значения здесь будет одноименное свойство объекта $item,
	 *  >>>  т.е., вместо %category - будет подставлено $item->category, а вместо %section - $item->section
	 *  В таблице 'content' ID категории хранится в поле 'catid', а ID раздела в поле 'sectionid', следовательно
	 *  необходимо включить эти поля в выборку для поиска, присвои им соответствующие псевдонимы
	 * >    'select' - дополнительный текст для SQL-оператора SELECT
	 *  Итак, в нашем случае, параметр 'select' будет выглядеть следующим образом:
	---  'select'=>'item.catid AS category, item.sectionid AS section'

	 */
	$groups = array();
	$comcontent_params = array(
		'group_name'  => 'com_boss',
		'group_title' => _CONTENT,
		'table'       => 'content',
		'id'          => 'id',
		'title'       => 'title',
		'text'        => 'introtext',
		'date'        => 'created',
		'task'        => 'view',
		'url_params'  => '',
		'select'      => '',
		'join'        => '',
		'where'       => 'item.state=1',
		'order'       => 'id DESC'
	);
	$groups['com_boss'] = $comcontent_params;

	$items->items = array();
	foreach($groups as $v){
		$group_name = $v['group_name'];
		$items->items[$group_name] = $items->load_by_type_tag($v, $tag);
	}

	$items->tag = $tag;

	//Params
	$params = new searchByTagConfig();
	$mainframe->setPageTitle($params->title . ' - ' . $tag);

	search_by_tag_HTML::tag_page($items, $params, $groups);
}

function viewSearch(){
	global $mosConfig_lang, $mosConfig_list_limit;
	$mainframe = mosMainFrame::getInstance();
	$_MAMBOTS = mosMambotHandler::getInstance();
	$database = database::getInstance();
	$restriction = 0;

	$params = new mosParameters('');
	$params->def('page_title', 1);
	$params->def('pageclass_sfx', '');
	$params->def('header', _SEARCH);
	$params->def('back_button', $mainframe->getCfg('back_button'));

	// html output
	search_html::openhtml($params);

	$searchphrase = mosGetParam($_REQUEST, 'searchphrase', 'any');
	$searchphrase = preg_replace('/[^a-z]/u', '', strtolower($searchphrase));

	$searchword = strval(mosGetParam($_REQUEST, 'searchword', ''));
	$searchword = trim(stripslashes($searchword));

	// boston, воспользуемся хаком smart'a, увеличим число символов для поиска до 100
	if(Jstring::strlen($searchword) > 100){
		$searchword = Jstring::substr($searchword, 0, 99);
		$restriction = 1;
	}

	// searchword must contain a minimum of 3 characters
	if($searchword && Jstring::strlen($searchword) < 3){
		$searchword = '';
		$restriction = 1;
	}

	if($searchphrase != 'exact'){
		$aterms = explode(' ', Jstring::strtolower($searchword));
		$search_ignore = array();
		// filter out search terms that are too small
		foreach($aterms AS $aterm){
			if(Jstring::strlen($aterm) < 3){
				$search_ignore[] = $aterm;
			}
		}
		$pruned = array_diff($aterms, $search_ignore);
		$pruned = array_unique($pruned);
		$searchword = implode(' ', $pruned);
		if(trim($searchword) == ''){
			$restriction = 1;
		}
	}
	include JPATH_BASE . DS . 'language' . DS . $mosConfig_lang . DS . 'ignore.php';

	$orders = array();
	$orders[] = mosHTML::makeOption('newest', _SEARCH_NEWEST);
	$orders[] = mosHTML::makeOption('oldest', _SEARCH_OLDEST);
	$orders[] = mosHTML::makeOption('popular', _SEARCH_POPULAR);
	$orders[] = mosHTML::makeOption('alpha', _SEARCH_ALPHABETICAL);
	$orders[] = mosHTML::makeOption('category', _SEARCH_CATEGORY);
	$ordering = mosGetParam($_REQUEST, 'ordering', 'newest');
	$ordering = preg_replace('/[^a-z]/u', '', strtolower($ordering));
	$lists = array();
	$lists['ordering'] = mosHTML::selectList($orders, 'ordering', 'id="search_ordering" class="inputbox" onchange="this.form.submit()"', 'value', 'text', $ordering);

	$searchphrases = array();

	$phrase = new stdClass();
	$phrase->value = 'any';
	$phrase->text = _SEARCH_ANYWORDS;
	$searchphrases[] = $phrase;

	$phrase = new stdClass();
	$phrase->value = 'all';
	$phrase->text = _SEARCH_ALLWORDS;
	$searchphrases[] = $phrase;

	$phrase = new stdClass();
	$phrase->value = 'exact';
	$phrase->text = _SEARCH_PHRASE;
	$searchphrases[] = $phrase;

	$lists['searchphrase'] = mosHTML::radioList($searchphrases, 'searchphrase', '', $searchphrase);

	// html output
	search_html::searchbox(htmlspecialchars($searchword), $lists, $params);

	if(!$searchword){
		if(count($_POST)){
			// html output
			// no matches found
			search_html::message(_NOKEYWORD, $params);
		} else
			if($restriction){
				// html output
				search_html::message(_SEARCH_MESSAGE, $params);
			}
	} elseif(in_array($searchword, $search_ignore)){
		// html output
		search_html::message(_IGNOREKEYWORD, $params);
	} else{
		// html output

		if($restriction){
			// html output
			search_html::message(_SEARCH_MESSAGE, $params);
		}

		$searchword_clean = htmlspecialchars($searchword);

		search_html::searchintro($searchword_clean, $params);

		mosLogSearch($searchword);

		$_MAMBOTS->loadBotGroup('search');
		$results = $_MAMBOTS->trigger('onSearch', array($database->getEscaped($searchword, true), $searchphrase, $ordering));
		$totalRows = 0;

		$rows = array();
		$_n = count($results);
		for($i = 0, $n = $_n; $i < $n; $i++){
			$rows = array_merge((array)$rows, (array)$results[$i]);
		}

		$totalRows = count($rows);

		for($i = 0; $i < $totalRows; $i++){
			$text = &$rows[$i]->text;

			if($searchphrase == 'exact'){
				$searchwords = array($searchword);
				$needle = $searchword;
			} else{
				$searchwords = explode(' ', $searchword);
				$needle = $searchwords[0];
			}

			$text = mosPrepareSearchContent($text, 200, $needle);

			foreach($searchwords as $k => $hlword){
				$searchwords[$k] = htmlspecialchars(stripslashes($hlword), ENT_QUOTES, 'UTF-8');
			}
			$searchRegex = implode('|', $searchwords);
			$text = preg_replace('/' . $searchRegex . '/iu', '<span class="highlight">\0</span>', $text);

			if(strpos($rows[$i]->href, 'http') == false){
				$url = parse_url($rows[$i]->href);
				parse_str(@$url['query'], $link);
			}
		}

		$mainframe->setPageTitle(_SEARCH);

		$total = $totalRows;
		$limit = intval(mosGetParam($_GET, 'limit', $mosConfig_list_limit));
		$limit = ($limit ? $limit : $mosConfig_list_limit);
		$limitstart = intval(mosGetParam($_GET, 'limitstart', 0));

		// prepares searchword for proper display in url
		$searchword_clean = urlencode($searchword_clean);

		// html output
		mosMainFrame::addLib('pagenavigation');
		$pageNav = new mosPageNav($total, $limitstart, $limit);

		if($n){
			search_html::display($rows, $params, $pageNav, $limitstart, $limit, $total, $totalRows, $searchword_clean);
		} else{
			// html output
			search_html::displaynoresult();
		}

		// html output
		search_html::conclusion($searchword_clean, $pageNav);
	}

	// displays back button
	echo '<br/>';
	mosHTML::BackButton($params, 0);
}

function mosLogSearch($search_term){
	$database = database::getInstance();
	global $mosConfig_enable_log_searches;

	if(@$mosConfig_enable_log_searches){
		$query = "SELECT hits FROM #__core_log_searches WHERE LOWER( search_term ) = " .
			$database->Quote($search_term);
		$database->setQuery($query);
		$hits = intval($database->loadResult());
		if($hits){
			$query = "UPDATE #__core_log_searches SET hits = ( hits + 1 ) WHERE LOWER( search_term ) = " .
				$database->Quote($search_term);
			$database->setQuery($query);
			$database->query();
		} else{
			$query = "INSERT INTO #__core_log_searches VALUES ( " . $database->Quote($search_term) . ", 1 )";
			$database->setQuery($query);
			$database->query();
		}
	}
}

?>
