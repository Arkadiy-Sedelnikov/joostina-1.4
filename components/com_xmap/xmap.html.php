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

/** Wraps HTML output */
class XmapHtml extends Xmap{
	var $level = -1;
	var $_openList = '';
	var $_closeList = '';
	var $_closeItem = '';
	var $_childs;
	var $_width;

	function XmapHtml($config, $sitemap){
		$this->view = 'html';
		Xmap::Xmap($config, $sitemap);
	}

	/**
	 * Print one node of the sitemap
	 */
	function printNode($node){
		$out = '';

		$out .= $this->_closeItem;
		$out .= $this->_openList;
		$this->_openList = "";

			$out .= '<li>';

		$link = Xmap::getItemLink($node);

		if(!isset($node->browserNav))
			$node->browserNav = 0;

		$node->name = htmlspecialchars($node->name);
		switch($node->browserNav){
			case 1: // open url in new window
				$ext_image = '';
				if($this->sitemap->exlinks){
					$ext_image = '&nbsp;<img src="' . JPATH_SITE . '/components/com_xmap/images/' . $this->sitemap->ext_image . '" alt="' . _XMAP_SHOW_AS_EXTERN_ALT . '" title="' . _XMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
				}
				$out .= '<a href="' . $link . '" title="' . $node->name . '" target="_blank">' . $node->name . $ext_image . '</a>';
				break;

			case 2: // open url in javascript popup window
				$ext_image = '';
				if($this->sitemap->exlinks){
					$ext_image = '&nbsp;<img src="' . JPATH_SITE . '/components/com_xmap/images/' . $this->sitemap->ext_image . '" alt="' . _XMAP_SHOW_AS_EXTERN_ALT . '" title="' . _XMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
				}
				$out .= '<a href="' . $link . '" title="' . $node->name . '" target="_blank" ' . "onClick=\"javascript: window.open('" . $link . "', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false;\">" . $node->name . $ext_image . "</a>";
				break;

			case 3: // no link
				$out .= '<span>' . $node->name . '</span>';
				break;

			default: // open url in parent window
				$out .= '<a href="' . $link . '" title="' . $node->name . '">' . $node->name . '</a>';
				break;
		}
		if(isset($node->end_line)){
			$this->_closeItem = $node->end_line . "</li>\n";
		} else{
			$this->_closeItem = "</li>\n";
		}

		$this->_childs[$this->level]++;
		echo $out;
		$this->count++;
	}

	/**
	 * Moves sitemap level up or down
	 */
	function changeLevel($level){
		if($level > 0){
			# We do not print start ul here to avoid empty list, it's printed at the first child
			$this->level += $level;
			$this->_childs[$this->level] = 0;
			$this->_openList = "\n<ul class=\"level_" . $this->level . "\">\n";
			$this->_closeItem = '';
		} else{
			if($this->_childs[$this->level]){
				echo $this->_closeItem . "</ul>\n";
			}
			$this->_closeItem = '</li>';
			$this->_openList = '';
			$this->level += $level;
		}
	}

	/** Print component heading, etc. Then call getHtmlList() to print list */
	function startOutput($menus, $config, $title){

		$sitemap = &$this->sitemap;

		$exlink[0] = $sitemap->exlinks; // image to mark popup links
		$exlink[1] = $sitemap->ext_image;

		if($sitemap->columns > 1){ // calculate column widths
			$total = count($menus);
			$columns = $total < $sitemap->columns ? $total : $sitemap->columns;
			$this->_width = (100 / $columns) - 1;
		}
		echo '<div class="' . $sitemap->classname . '">';
		echo '<div class="componentheading"><h1>' . $title . '</h1></div>';
		echo '<div class="contentpaneopen"' . ($sitemap->columns > 1 ? ' style="float:left;width:100%;"' : '') . '>';


	}

	/** Print component heading, etc. Then call getHtmlList() to print list */
	function endOutput($menus){
		$sitemap = &$this->sitemap;
		echo '<div style="clear:left"></div>';
		echo '</div>';
		echo "</div>\n";
	}

	function startMenu($menu){
		$sitemap =& $this->sitemap;
		if($sitemap->columns > 1) // use columns
			echo '<div style="float:left;width:' . $this->_width . '%;">';
		if($sitemap->show_menutitle) // show menu titles
			echo '<h2 class="menutitle">' . $menu->name . '</h2>';
	}

	function endMenu($menu){
		$sitemap =& $this->sitemap;
		$this->_closeItem = '';
		if($sitemap->show_menutitle || $sitemap->columns > 1){ // each menu gets a separate list
			if($sitemap->columns > 1){
				echo "</div>\n";
			}

		}
	}
}
