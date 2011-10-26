<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*
* dom_xmlrpc_array_document wraps a PHP array with the DOM XML-RPC API
* @package dom-xmlrpc
* @copyright (C) 2004 John Heinstein. All rights reserved
* @license http://www.gnu.org/copyleft/lesser.html LGPL License
* @author John Heinstein <johnkarl@nbnet.nb.ca>
* @link http://www.engageinteractive.com/dom_xmlrpc/ DOM XML-RPC Home Page
* DOM XML-RPC is Free Software
**/

defined('_VALID_MOS') or die();
if(!defined('DOMIT_RSS_INCLUDE_PATH')) {
	define('DOMIT_RSS_INCLUDE_PATH',(dirname(__file__)."/"));
}
define('DOMIT_RSS_LITE_VERSION','0.51');
require_once (DOMIT_RSS_INCLUDE_PATH.'xml_domit_rss_shared.php');
class xml_domit_rss_document_lite extends xml_domit_rss_base_document {
	function xml_domit_rss_document_lite($url = '',$cacheDir = './',$cacheTime = 3600) {
		$this->parser = 'DOMIT_RSS_LITE';
		$this->xml_domit_rss_base_document($url,$cacheDir,$cacheTime);
	}

	function _init() {
		$total = $this->node->documentElement->childCount;
		$itemCounter = 0;
		$channelCounter = 0;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$this->node->documentElement->childNodes[$i];
			$tagName = strtolower($currNode->nodeName);
			switch($tagName) {
				case DOMIT_RSS_ELEMENT_ITEM:
					$this->domit_rss_items[$itemCounter] = new xml_domit_rss_item_lite($currNode);
					$itemCounter++;
					break;
				case DOMIT_RSS_ELEMENT_CHANNEL:
					$this->domit_rss_channels[$channelCounter] = new xml_domit_rss_channel_lite($currNode);
					$channelCounter++;
					break;
				case DOMIT_RSS_ELEMENT_TITLE:
				case DOMIT_RSS_ELEMENT_LINK:
				case DOMIT_RSS_ELEMENT_DESCRIPTION:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
			}
		}
		if($itemCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_ITEMS] = &$this->domit_rss_items;
		}
		if($channelCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_CHANNELS] = &$this->domit_rss_channels;
		}
		$this->handleChannelElementsEmbedded();
	}

	function getVersion() {
		return DOMIT_RSS_LITE_VERSION;
	}

}

class xml_domit_rss_channel_lite extends xml_domit_rss_elementindexer {
	var $domit_rss_items = array();
	function xml_domit_rss_channel_lite(&$channel) {
		$this->node = &$channel;
		$this->_init();
	}

	function _init() {
		$total = $this->node->childCount;
		$itemCounter = 0;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$this->node->childNodes[$i];
			$tagName = strtolower($currNode->nodeName);
			switch($tagName) {
				case DOMIT_RSS_ELEMENT_ITEM:
					$this->domit_rss_items[$itemCounter] = new xml_domit_rss_item_lite($currNode);
					$itemCounter++;
					break;
				case DOMIT_RSS_ELEMENT_TITLE:
				case DOMIT_RSS_ELEMENT_LINK:
				case DOMIT_RSS_ELEMENT_DESCRIPTION:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
			}
		}
		if($itemCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_ITEMS] = &$this->domit_rss_items;
		}
	}

	function getTitle() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_TITLE);
	}

	function getLink() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_LINK);
	}

	function getDescription() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_DESCRIPTION);
	}

	function getItemCount() {
		return count($this->domit_rss_items);
	}

	function &getItem($index) {
		return $this->domit_rss_items[$index];
	}

}

class xml_domit_rss_item_lite extends xml_domit_rss_elementindexer {
	function xml_domit_rss_item_lite(&$item) {
		$this->node = &$item;
		$this->_init();
	}

	function _init() {
		$total = $this->node->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$this->node->childNodes[$i];
			$tagName = strtolower($currNode->nodeName);
			switch($tagName) {
				case DOMIT_RSS_ELEMENT_TITLE:
				case DOMIT_RSS_ELEMENT_LINK:
				case DOMIT_RSS_ELEMENT_DESCRIPTION:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
			}
		}
	}

	function getTitle() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_TITLE);
	}

	function getLink() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_LINK);
	}

	function getDescription() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_DESCRIPTION);
	}

}




?>
