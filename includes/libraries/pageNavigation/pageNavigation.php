<?php

/**
 * @package   Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или LICENSE.php
 *            Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
 *
 * Модификация 12.07.2012, Gold Dragon
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Page navigation support class
 * @package Joostina
 */
class mosPageNav{

	/**
	@var int The record number to start dislpaying from */
	public $limitstart = null;
	/**
	@var int Number of rows to display per page */
	public $limit = null;
	/**
	@var int Total number of rows */
	public $total = null;

	public $prev_exist = 0;

	public $next_exist = 0;

	public function __construct($total, $limitstart, $limit){
		$this->total = (int)$total;
		$this->limitstart = (int)max($limitstart, 0);
		$this->limit = (int)max($limit, 1);
		if($this->limit > $this->total){
			$this->limitstart = 0;
		}
		if(($this->limit - 1) * $this->limitstart > $this->total){
			$this->limitstart -= $this->limitstart % $this->limit;
		}
	}


	/**
	 * Returns the html limit # input box
	 *
	 * @param string The basic link to include in the href
	 *
	 * @return string
	 */
	function getLimitBox($link = null){
		$limits = array();
		for($i = 5; $i <= 30; $i += 5){
			$limits[] = mosHTML::makeOption($i);
		}
		$limits[] = mosHTML::makeOption('50');
		$limits[] = mosHTML::makeOption('100');
		$limits[] = mosHTML::makeOption('150');
		$limits[] = mosHTML::makeOption('5000', _PN_ALL);

		if($link){
			$link = $link . "&amp;limit=[selector]&amp;limitstart=" . $this->limitstart;
			$link = JSef::getUrlToSef($link);
			$link = str_replace('[selector]', "' + this.options[selectedIndex].value + '", $link);

			return mosHTML::selectList($limits, 'limit', 'class="inputbox" size="1" onchange="document.location.href=\'' . $link . '\';"', 'value', 'text', $this->limit);
		} else{
			$html = mosHTML::selectList($limits, 'limit', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $this->limit);
			$html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"$this->limitstart\" />";
			return $html;
		}
	}


	/**
	 * Writes the html limit # input box
	 *
	 * @param string The basic link to include in the href
	 */
	function writeLimitBox($link = null){
		echo mosPageNav::getLimitBox($link);
	}

	/**
	 * Writes the html for the pages counter, eg, Results 1-10 of x
	 */
	function writePagesCounter(){
		$html = '';
		$from_result = $this->limitstart + 1;
		if($this->limitstart + $this->limit < $this->total){
			$to_result = $this->limitstart + $this->limit;
		} else{
			$to_result = $this->total;
		}
		if($this->total > 0){
			$html .= "\n" . _NAV_SHOW . " " . $from_result . " - " . $to_result . " " . _NAV_SHOW_FROM . " " . $this->total;
		} else{
			$html .= "\n" . _NO_ITEMS;
		}
		return $html;
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
	 *
	 * @param string The basic link to include in the href
	 */
	function writePagesLinks($link = null){
		if($link){
			$mainframe = mosMainFrame::getInstance();
			$txt = '<div class="pagenavigation"><ul>';

			$displayed_pages = 10;
			$total_pages = $this->limit ? ceil($this->total / $this->limit) : 0;
			// скрываем навигатор по страницам если их меньше 2х.
			if($total_pages < 2) return;

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
			if(_PN_LT || _PN_RT) $pnSpace = "&nbsp;";

			if($this_page > 1){

				$page = ($this_page - 2) * $this->limit;

				if(!$this->prev_exist){
					$mainframe->addCustomHeadTag('<link rel="prev" href="' . JSef::getUrlToSef($link . '&amp;limitstart=' . $page) . '" />');
					$this->prev_exist = 1;
				}

				$txt .= '<li class="first_page"><a href="' . JSef::getUrlToSef("$link&amp;limitstart=0") . '" class="pagenav" title="' . _PN_START . '">' . _PN_START . '</a></li> ';
				$txt .= '<li class="back"><a href="' . JSef::getUrlToSef("$link&amp;limitstart=$page") . '" class="pagenav" title="' . _PN_PREVIOUS . '">' . _PN_LT . $pnSpace . _PN_PREVIOUS . '</a></li> ';
			} else{
				$txt .= '<li class="first_page"><span class="pagenav">' . _PN_START . '</span></li> ';
				$txt .= '<li class="back"><span class="pagenav">' . _PN_LT . $pnSpace . _PN_PREVIOUS . '</span></li> ';
			}

			for($i = $start_loop; $i <= $stop_loop; $i++){
				$page = ($i - 1) * $this->limit;
				if($i == $this_page){
					$txt .= '<li><span class="pagenav">' . $i . '</span></li>';
				} else{
					$txt .= '<li><a href="' . JSef::getUrlToSef($link . '&amp;limitstart=' . $page) . '" class="pagenav"><strong>' . $i . '</strong></a></li>';
				}
			}

			if($this_page < $total_pages){

				$page = $this_page * $this->limit;
				$end_page = ($total_pages - 1) * $this->limit;

				if(!$this->next_exist){
					$mainframe->addCustomHeadTag("<link rel='next' href='" . JSef::getUrlToSef($link . '&amp;limitstart=' . $page) . "' />");
					$this->next_exist = 1;
				}

				$txt .= '<li class="next"><a href="' . JSef::getUrlToSef($link . '&amp;limitstart=' . $page) . ' " class="pagenav" title="' . _PN_NEXT . '">' . _PN_NEXT . $pnSpace . _PN_RT . '</a> </li>';
				$txt .= '<li class="last_page"><a href="' . JSef::getUrlToSef($link . '&amp;limitstart=' . $end_page) . ' " class="pagenav" title="' . _PN_END . '">' . _PN_END . '</a></li>';
			} else{
				$txt .= '<li class="next"><span class="pagenav">' . _PN_NEXT . $pnSpace . _PN_RT . '</span></li> ';
				$txt .= '<li class="last_page"><span class="pagenav">' . _PN_END . $pnSpace . _PN_RT . '</span></li>';
			}
			return $txt . '</ul></div>';
		} else{
			$html = '';
			$displayed_pages = 10;
			$total_pages = ceil($this->total / $this->limit);
			// скрываем навигатор по страницам если их меньше 2х.
			if($total_pages < 2) return;
			$this_page = ceil(($this->limitstart + 1) / $this->limit);
			$start_loop = (floor(($this_page - 1) / $displayed_pages)) * $displayed_pages + 1;
			if($start_loop + $displayed_pages - 1 < $total_pages){
				$stop_loop = $start_loop + $displayed_pages - 1;
			} else{
				$stop_loop = $total_pages;
			}

			if($this_page > 1){
				$page = ($this_page - 2) * $this->limit;
				$html .= "\n<a href=\"#beg\" class=\"pagenav\" onclick=\"javascript: document.adminForm.limitstart.value=0; document.adminForm.submit();return false;\">&lt;&lt;&nbsp;" . _PN_START . "</a>";
				$html .= "\n<a href=\"#prev\" class=\"pagenav\" onclick=\"javascript: document.adminForm.limitstart.value=$page; document.adminForm.submit();return false;\">&lt;&nbsp;" . _PN_PREVIOUS . "</a>";
			} else{
				$html .= "\n<span class=\"pagenav\">&lt;&lt;&nbsp;" . _PN_START . "</span>";
				$html .= "\n<span class=\"pagenav\">&lt;&nbsp;" . _PN_PREVIOUS . "</span>";
			}

			for($i = $start_loop; $i <= $stop_loop; $i++){
				$page = ($i - 1) * $this->limit;
				if($i == $this_page){
					$html .= "\n<span class=\"pagenav\"> $i </span>";
				} else{
					$html .= "\n<a href=\"#$i\" class=\"pagenav\" onclick=\"javascript: document.adminForm.limitstart.value=$page; document.adminForm.submit();return false;\"><strong>$i</strong></a>";
				}
			}

			if($this_page < $total_pages){
				$page = $this_page * $this->limit;
				$end_page = ($total_pages - 1) * $this->limit;
				$html .= "\n<a href=\"#next\" class=\"pagenav\" onclick=\"javascript: document.adminForm.limitstart.value=$page; document.adminForm.submit();return false;\"> " . _PN_NEXT . "&nbsp;&gt;</a>";
				$html .= "\n<a href=\"#end\" class=\"pagenav\" onclick=\"javascript: document.adminForm.limitstart.value=$end_page; document.adminForm.submit();return false;\"> " . _PN_END . "&nbsp;&gt;&gt;</a>";
			} else{
				$html .= "\n<span class=\"pagenav\">" . _PN_NEXT . "&nbsp;&gt;</span>";
				$html .= "\n<span class=\"pagenav\">" . _PN_END . "&nbsp;&gt;&gt;</span>";
			}
			return $html;
		}
	}

	function getListFooter(){
		$html = '<table class="adminlist"><tr><td align="center">';
		$html .= $this->writePagesCounter() . $this->writePagesLinks() . ' ' . _PN_DISPLAY_NR . ' # ' . $this->getLimitBox() . '</td>';
		$html .= '</tr></table>';
		return $html;
	}

	/**
	 * Sets the vars {PAGE_LINKS}, {PAGE_LIST_OPTIONS} and {PAGE_COUNTER} for the page navigation template
	 *
	 * @param object The patTemplate object
	 * @param string The full link to be used in the nav link, eg index.php?option=com_content
	 * @param string The name of the template to add the variables
	 */
	function setTemplateVars($tmpl, $link = '', $name = 'admin-list-footer'){
		$tmpl->addVar($name, 'PAGE_LINKS', $this->writePagesLinks($link));
		$tmpl->addVar($name, 'PAGE_LIST_OPTIONS', $this->getLimitBox($link));
		$tmpl->addVar($name, 'PAGE_COUNTER', $this->writePagesCounter());
	}

	/**
	 * @param int The row index
	 *
	 * @return int
	 */
	function rowNumber($i){
		return $i + 1 + $this->limitstart;
	}

	/**
	 * @param int    The row index
	 * @param string The task to fire
	 * @param string The alt text for the icon
	 *
	 * @return string
	 */
	function orderUpIcon($i, $condition = true, $task = 'orderup', $alt = _PN_MOVE_TOP){
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		if(($i > 0 || ($i + $this->limitstart > 0)) && $condition){
			return '<a href="#reorder" onClick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $alt . '"><img src="' . $cur_file_icons_path . '/uparrow.png" width="12" height="12" border="0" alt="' . $alt . '" /></a>';
		} else{
			return '&nbsp;';
		}
	}

	/**
	 * @param int    The row index
	 * @param int    The number of items in the list
	 * @param string The task to fire
	 * @param string The alt text for the icon
	 *
	 * @return string
	 */
	function orderDownIcon($i, $n, $condition = true, $task = 'orderdown', $alt = _PN_MOVE_DOWN){
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		if(($i < $n - 1 || $i + $this->limitstart < $this->total - 1) && $condition){
			return '<a href="#reorder" onClick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $alt . '"><img src="' . $cur_file_icons_path . '/downarrow.png" width="12" height="12" border="0" alt="' . $alt . '" /></a>';
		} else{
			return '&nbsp;';
		}
	}

	/**
	 * @param int    The row index
	 * @param string The task to fire
	 * @param string The alt text for the icon
	 *
	 * @return string
	 */
	function orderUpIcon2($id, $order){
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		if($order == 0){
			$img = 'uparrow.png';
			$show = true;
		} else if($order < 0){
			$img = 'uparrow.png';
			$show = true;
		} else{
			$img = 'uparrow.png';
			$show = true;
		}
		;
		if($show){
			$output = '<a href="#ordering" onClick="listItemTask(\'cb' . $id . '\',\'orderup\')" title="' . _NAV_ORDER_UP . '">';
			$output .= '<img src="' . $cur_file_icons_path . '/' . $img . '" width="12" height="12" border="0" alt="' . _NAV_ORDER_UP . '" title="' . _NAV_ORDER_UP . '" /></a>';

			return $output;
		} else{
			return '&nbsp;';
		}
	}

	/**
	 * @param int    The row index
	 * @param int    The number of items in the list
	 * @param string The task to fire
	 * @param string The alt text for the icon
	 *
	 * @return string
	 */
	function orderDownIcon2($id, $order){
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';

		if($order == 0){
			$img = 'downarrow.png';
			$show = true;
		} else if($order < 0){
			$img = 'downarrow.png';
			$show = true;
		} else{
			$img = 'downarrow.png';
			$show = true;
		}
		;
		if($show){
			$output = '<a href="#ordering" onClick="listItemTask(\'cb' . $id . '\',\'orderdown\')" title="' . _NAV_ORDER_DOWN . '">';
			$output .= '<img src="' . $cur_file_icons_path . '/' . $img . '" width="12" height="12" border="0" alt="' . _NAV_ORDER_DOWN . '" title="' . _NAV_ORDER_DOWN . '" /></a>';

			return $output;
		} else{
			return '&nbsp;';
		}
	}
}