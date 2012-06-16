<?php

/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или LICENSE.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
 */
// запрет прямого доступа
defined('_VALID_MOS') or die();

/**
 * Page navigation support class
 * @package Joostina
 */
class mosPageNav{

	/**
	@var int The record number to start dislpaying from */
	var $limitstart = null;
	/**
	@var int Number of rows to display per page */
	var $limit = null;
	/**
	@var int Total number of rows */
	var $total = null;
	var $prev_exist = 0;
	var $next_exist = 0;

	function mosPageNav($total, $limitstart, $limit){
		$this->total = (int)$total;
		$this->limitstart = (int)max($limitstart, 0);
		$this->limit = (int)max($limit, 0);
	}

	/**
	 * Returns the html limit # input box
	 * @param string The basic link to include in the href
	 * @return string
	 */
	function getLimitBox($link){
		$limits = array();
		for($i = 5; $i <= 30; $i += 5){
			$limits[] = mosHTML::makeOption($i);
		}
		$limits[] = mosHTML::makeOption('50');
		$limits[] = mosHTML::makeOption('100');
		$limits[] = mosHTML::makeOption('150');
		$limits[] = mosHTML::makeOption('5000', _PN_ALL);

		$link = $link . "&amp;limit=[selector]&amp;limitstart=" . $this->limitstart;
		$link = sefRelToAbs($link);
		$link = str_replace('[selector]', "' + this.options[selectedIndex].value + '", $link);

		return mosHTML::selectList($limits, 'limit', 'class="inputbox" size="1" onchange="document.location.href=\'' . $link . '\';"', 'value', 'text', $this->limit);
	}

	/**
	 * Writes the html limit # input box
	 * @param string The basic link to include in the href
	 */
	function writeLimitBox($link){
		echo mosPageNav::getLimitBox($link);
	}

	/**
	 * Writes the html for the pages counter, eg, Results 1-10 of x
	 */
	function writePagesCounter(){
		$txt = '';
		$from_result = $this->limitstart + 1;
		if($this->limitstart + $this->limit < $this->total){
			$to_result = $this->limitstart + $this->limit;
		} else{
			$to_result = $this->total;
		}
		if($this->total > 0){
			$txt .= _PN_RESULTS . " $from_result - $to_result " . _PN_OF . " $this->total";
		}
		return $to_result ? $txt : '';
	}

	/**
	 * Writes the html for the leafs counter, eg, Page 1 of x
	 */
	function writeLeafsCounter(){
		$txt = '';
		$page = ceil(($this->limitstart + 1) / $this->limit);
		if($this->total > 0){
			$total_pages = ceil($this->total / $this->limit);
			$txt .= _PN_PAGE . " $page " . _PN_OF . " $total_pages";
		}
		return $txt;
	}

	/**
	 * Writes the html links for pages, eg, previous, next, 1 2 3 ... x
	 * @param string The basic link to include in the href
	 */
	function writePagesLinks($link){

		$mainframe = mosMainFrame::getInstance();
		;
		$txt = '<div class="pagenavigation"><ul>';

		$displayed_pages = 10;
		$total_pages = $this->limit ? ceil($this->total / $this->limit) : 0;
		// скрываем навигатор по страницам если их меньше 2х.
		if($total_pages < 2)
			return;

		$this_page = $this->limit ? ceil(($this->limitstart + 1) / $this->limit) : 1;
		$start_loop = (floor(($this_page - 1) / $displayed_pages)) * $displayed_pages + 1;
		if($start_loop + $displayed_pages - 1 < $total_pages){
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else{
			$stop_loop = $total_pages;
		}

		$link .= '&amp;limit=' . $this->limit;

		if(!defined('_PN_LT') || !defined('_PN_RT')){
			DEFINE('_PN_LT', '&larr;');
			DEFINE('_PN_RT', '&rarr;');
		}

		$pnSpace = '';
		if(_PN_LT || _PN_RT)
			$pnSpace = "&nbsp;";

		if($this_page > 1){

			$page = ($this_page - 2) * $this->limit;

			if(!$this->prev_exist){
				$mainframe->addCustomHeadTag('<link rel="prev" href="' . sefRelToAbs($link . '&amp;limitstart=' . $page) . '" />');
				$this->prev_exist = 1;
			}

			$txt .= '<li class="first_page"><a href="' . sefRelToAbs("$link&amp;limitstart=0") . '" class="pagenav" title="' . _PN_START . '">' . _PN_START . '</a></li> ';
			$txt .= '<li class="back"><a href="' . sefRelToAbs("$link&amp;limitstart=$page") . '" class="pagenav" title="' . _PN_PREVIOUS . '">' . _PN_LT . $pnSpace . _PN_PREVIOUS . '</a></li> ';
		} else{
			$txt .= '<li class="first_page"><span class="pagenav">' . _PN_START . '</span></li> ';
			$txt .= '<li class="back"><span class="pagenav">' . _PN_LT . $pnSpace . _PN_PREVIOUS . '</span></li> ';
		}

		for($i = $start_loop; $i <= $stop_loop; $i++){
			$page = ($i - 1) * $this->limit;
			if($i == $this_page){
				$txt .= '<li><span class="pagenav">' . $i . '</span></li>';
			} else{
				$txt .= '<li><a href="' . sefRelToAbs($link . '&amp;limitstart=' . $page) . '" class="pagenav"><strong>' . $i . '</strong></a></li>';
			}
		}

		if($this_page < $total_pages){

			$page = $this_page * $this->limit;
			$end_page = ($total_pages - 1) * $this->limit;

			if(!$this->next_exist){
				$mainframe->addCustomHeadTag("<link rel='next' href='" . sefRelToAbs($link . '&amp;limitstart=' . $page) . "' />");
				$this->next_exist = 1;
			}

			$txt .= '<li class="next"><a href="' . sefRelToAbs($link . '&amp;limitstart=' . $page) . ' " class="pagenav" title="' . _PN_NEXT . '">' . _PN_NEXT . $pnSpace . _PN_RT . '</a> </li>';
			$txt .= '<li class="last_page"><a href="' . sefRelToAbs($link . '&amp;limitstart=' . $end_page) . ' " class="pagenav" title="' . _PN_END . '">' . _PN_END . '</a></li>';
		} else{
			$txt .= '<li class="next"><span class="pagenav">' . _PN_NEXT . $pnSpace . _PN_RT . '</span></li> ';
			$txt .= '<li class="last_page"><span class="pagenav">' . _PN_END . $pnSpace . _PN_RT . '</span></li>';
		}
		return $txt . '</ul></div>';
	}

	/**
	 * Sets the vars {PAGE_LINKS}, {PAGE_LIST_OPTIONS} and {PAGE_COUNTER} for the page navigation template
	 * @param object The patTemplate object
	 * @param string The full link to be used in the nav link, eg index.php?option=com_content
	 * @param string The name of the template to add the variables
	 */
	function setTemplateVars(&$tmpl, $link = '', $name = 'admin-list-footer'){
		$tmpl->addVar($name, 'PAGE_LINKS', $this->writePagesLinks($link));
		$tmpl->addVar($name, 'PAGE_LIST_OPTIONS', $this->getLimitBox($link));
		$tmpl->addVar($name, 'PAGE_COUNTER', $this->writePagesCounter());
	}

}