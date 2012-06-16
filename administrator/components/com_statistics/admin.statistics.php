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

require_once ($mainframe->getPath('admin_html'));

switch($task){
	case 'searches':
		showSearches($option, $task);
		break;

	case 'searchesresults':
		showSearches($option, $task, 1);
		break;

	case 'pageimp':
		showPageImpressions($option, $task);
		break;

	default:
		showSummary($option, $task);
		break;
}

function showSummary($option, $task){
	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance(true);

	// get sort field and check against allowable field names
	$field = strtolower(mosGetParam($_REQUEST, 'field', ''));
	if(!in_array($field, array('agent', 'hits'))){
		$field = '';
	}

	// get field ordering or set the default field to order
	$order = strtolower(mosGetParam($_REQUEST, 'order', 'asc'));
	if($order != 'asc' && $order != 'desc' && $order != 'none'){
		$order = 'asc';
	} else
		if($order == 'none'){
			$field = 'agent';
			$order = 'asc';
		}

	// browser stats
	$order_by = '';
	$sorts = array();
	$tab = mosGetParam($_REQUEST, 'tab', 'tab1');
	$sort_base = "index2.php?option=$option&task=$task";

	switch($field){
		case 'hits':
			$order_by = "hits $order";
			$sorts['b_agent'] = mosHTML::sortIcon("$sort_base&tab=tab1", "agent");
			$sorts['b_hits'] = mosHTML::sortIcon("$sort_base&tab=tab1", "hits", $order);
			$sorts['o_agent'] = mosHTML::sortIcon("$sort_base&tab=tab2", "agent");
			$sorts['o_hits'] = mosHTML::sortIcon("$sort_base&tab=tab2", "hits", $order);
			$sorts['d_agent'] = mosHTML::sortIcon("$sort_base&tab=tab3", "agent");
			$sorts['d_hits'] = mosHTML::sortIcon("$sort_base&tab=tab3", "hits", $order);
			break;

		case 'agent':
		default:
			$order_by = "agent $order";
			$sorts['b_agent'] = mosHTML::sortIcon("$sort_base&tab=tab1", "agent", $order);
			$sorts['b_hits'] = mosHTML::sortIcon("$sort_base&tab=tab1", "hits");
			$sorts['o_agent'] = mosHTML::sortIcon("$sort_base&tab=tab2", "agent", $order);
			$sorts['o_hits'] = mosHTML::sortIcon("$sort_base&tab=tab2", "hits");
			$sorts['d_agent'] = mosHTML::sortIcon("$sort_base&tab=tab3", "agent", $order);
			$sorts['d_hits'] = mosHTML::sortIcon("$sort_base&tab=tab3", "hits");
			break;
	}

	$query = "SELECT* FROM #__stats_agents WHERE type = 0 ORDER BY $order_by";
	$database->setQuery($query);
	$browsers = $database->loadObjectList();

	$query = "SELECT SUM( hits ) AS totalhits, MAX( hits ) AS maxhits FROM #__stats_agents WHERE type = 0";
	$database->setQuery($query);
	$bstats = null;
	$database->loadObject($bstats);

	// platform statistics
	$query = "SELECT* FROM #__stats_agents WHERE type = 1 ORDER BY hits DESC";
	$database->setQuery($query);
	$platforms = $database->loadObjectList();

	$query = "SELECT SUM( hits ) AS totalhits, MAX( hits ) AS maxhits FROM #__stats_agents WHERE type = 1";
	$database->setQuery($query);
	$pstats = null;
	$database->loadObject($pstats);

	// domain statistics
	$query = "SELECT* FROM #__stats_agents WHERE type = 2 ORDER BY hits DESC";
	$database->setQuery($query);
	$tldomains = $database->loadObjectList();

	$query = "SELECT SUM( hits ) AS totalhits, MAX( hits ) AS maxhits FROM #__stats_agents WHERE type = 2";
	$database->setQuery($query);
	$dstats = null;
	$database->loadObject($dstats);

	HTML_statistics::show($browsers, $platforms, $tldomains, $bstats, $pstats, $dstats, $sorts,
		$option);
}

function showPageImpressions($option, $task){
	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance(true);

	//определяем каталог выведенный на главную страницу
	require_once ($mainframe->getPath('class', 'com_frontpage'));
	$configObject = new frontpageConfig($database);
	$directory = $configObject->get('directory', 0);

	require_once (JPATH_BASE . '/' . JADMIN_BASE . '/includes/pageNavigation.php');

	if($directory == 0){
		$total = 0;
		$rows = array();
		$limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mainframe->getCfg('list_limit'));
		$limitstart = $mainframe->getUserStateFromRequest("view{$option}{$task}limitstart", 'limitstart', 0);
		$pageNav = new mosPageNav($total, $limitstart, $limit);
	} else{
		$query = "SELECT COUNT( * ) FROM #__boss_" . $directory . "_contents";
		$database->setQuery($query);
		$total = $database->loadResult();

		$limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mainframe->getCfg('list_limit'));
		$limitstart = $mainframe->getUserStateFromRequest("view{$option}{$task}limitstart", 'limitstart', 0);
		$pageNav = new mosPageNav($total, $limitstart, $limit);

		$query = "SELECT id, name AS title, date_created AS created, views AS hits FROM #__boss_" . $directory . "_contents ORDER BY views DESC";
		$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);

		$rows = $database->loadObjectList();
	}
	HTML_statistics::pageImpressions($rows, $pageNav, $option, $task);
}

function showSearches($option, $task, $showResults = null){
	$_MAMBOTS = mosMambotHandler::getInstance();

	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance(true);

	$limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mainframe->getCfg('list_limit'));
	$limitstart = $mainframe->getUserStateFromRequest("view{$option}{$task}limitstart", 'limitstart', 0);

	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__core_log_searches";
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once (JPATH_BASE . '/' . JADMIN_BASE . '/includes/pageNavigation.php');
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$query = "SELECT* FROM #__core_log_searches ORDER BY hits DESC";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);

	$rows = $database->loadObjectList();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	$_MAMBOTS->loadBotGroup('search');

	$total = count($rows);
	for($i = 0, $n = $total; $i < $n; $i++){
		// determine if number of results for search item should be calculated
		// by default it is `off` as it is highly query intensive
		if($showResults){
			$results = $_MAMBOTS->trigger('onSearch', array($rows[$i]->search_term));

			$count = 0;
			$total = count($results);
			for($j = 0, $n2 = $total; $j < $n2; $j++){
				$count += count($results[$j]);
			}

			$rows[$i]->returns = $count;
		} else{
			$rows[$i]->returns = null;
		}
	}

	HTML_statistics::showSearches($rows, $pageNav, $option, $task, $showResults);
}