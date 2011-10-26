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
define('DOMIT_RSS_VERSION','0.51');
define('DOMIT_RSS_ELEMENT_LANGUAGE','language');
define('DOMIT_RSS_ELEMENT_COPYRIGHT','copyright');
define('DOMIT_RSS_ELEMENT_MANAGINGEDITOR','managingeditor');
define('DOMIT_RSS_ELEMENT_WEBMASTER','webmaster');
define('DOMIT_RSS_ELEMENT_PUBDATE','pubdate');
define('DOMIT_RSS_ELEMENT_LASTBUILDDATE','lastbuilddate');
define('DOMIT_RSS_ELEMENT_CATEGORY','category');
define('DOMIT_RSS_ELEMENT_GENERATOR','generator');
define('DOMIT_RSS_ELEMENT_DOCS','docs');
define('DOMIT_RSS_ELEMENT_CLOUD','cloud');
define('DOMIT_RSS_ELEMENT_TTL','ttl');
define('DOMIT_RSS_ELEMENT_IMAGE','image');
define('DOMIT_RSS_ELEMENT_RATING','rating');
define('DOMIT_RSS_ELEMENT_TEXTINPUT','textinput');
define('DOMIT_RSS_ELEMENT_SKIPHOURS','skiphours');
define('DOMIT_RSS_ELEMENT_SKIPDAYS','skipdays');
define('DOMIT_RSS_ELEMENT_URL','url');
define('DOMIT_RSS_ELEMENT_WIDTH','width');
define('DOMIT_RSS_ELEMENT_HEIGHT','height');
define('DOMIT_RSS_ELEMENT_GUID','guid');
define('DOMIT_RSS_ELEMENT_ENCLOSURE','enclosure');
define('DOMIT_RSS_ELEMENT_COMMENTS','comments');
define('DOMIT_RSS_ELEMENT_SOURCE','source');
define('DOMIT_RSS_ELEMENT_NAME','name');
define('DOMIT_RSS_ELEMENT_AUTHOR','author');
define('DOMIT_RSS_ATTR_DOMAIN','domain');
define('DOMIT_RSS_ATTR_PORT','port');
define('DOMIT_RSS_ATTR_PATH','path');
define('DOMIT_RSS_ATTR_REGISTERPROCEDURE','registerProcedure');
define('DOMIT_RSS_ATTR_PROTOCOL','protocol');
define('DOMIT_RSS_ATTR_URL','url');
define('DOMIT_RSS_ATTR_LENGTH','length');
define('DOMIT_RSS_ATTR_TYPE','type');
define('DOMIT_RSS_ATTR_ISPERMALINK','isPermaLink');
require_once (DOMIT_RSS_INCLUDE_PATH.'xml_domit_rss_shared.php');
class xml_domit_rss_document extends xml_domit_rss_base_document {
	function xml_domit_rss_document($url = '',$cacheDir = './',$cacheTime = '3600') {
		$this->parser = 'DOMIT_RSS';
		$this->xml_domit_rss_base_document($url,$cacheDir,$cacheTime);
	}

	function _init() {
		$total = $this->node->documentElement->childCount;
		$itemCounter = 0;
		$channelCounter = 0;
		$categoryCounter = 0;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$this->node->documentElement->childNodes[$i];
			$tagName = strtolower($currNode->nodeName);
			switch($tagName) {
				case DOMIT_RSS_ELEMENT_ITEM:
					$this->domit_rss_items[$itemCounter] = new xml_domit_rss_item($currNode);
					$itemCounter++;
					break;
				case DOMIT_RSS_ELEMENT_CHANNEL:
					$this->domit_rss_channels[$channelCounter] = new xml_domit_rss_channel($currNode);
					$channelCounter++;
					break;
				case DOMIT_RSS_ELEMENT_CATEGORY:
					$this->domit_rss_categories[$categoryCounter] = new xml_domit_rss_category($currNode);
					$categoryCounter++;
					break;
				case DOMIT_RSS_ELEMENT_IMAGE:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_image($currNode);
					break;
				case DOMIT_RSS_ELEMENT_CLOUD:
					$this->indexer[$tagName] = new xml_domit_rss_cloud($currNode);
					break;
				case DOMIT_RSS_ELEMENT_TEXTINPUT:
					$this->indexer[$tagName] = new xml_domit_rss_textinput($currNode);
					break;
				case DOMIT_RSS_ELEMENT_TITLE:
				case DOMIT_RSS_ELEMENT_LINK:
				case DOMIT_RSS_ELEMENT_DESCRIPTION:
				case DOMIT_RSS_ELEMENT_LANGUAGE:
				case DOMIT_RSS_ELEMENT_COPYRIGHT:
				case DOMIT_RSS_ELEMENT_MANAGINGEDITOR:
				case DOMIT_RSS_ELEMENT_WEBMASTER:
				case DOMIT_RSS_ELEMENT_PUBDATE:
				case DOMIT_RSS_ELEMENT_LASTBUILDDATE:
				case DOMIT_RSS_ELEMENT_GENERATOR:
				case DOMIT_RSS_ELEMENT_DOCS:
				case DOMIT_RSS_ELEMENT_TTL:
				case DOMIT_RSS_ELEMENT_RATING:
				case DOMIT_RSS_ELEMENT_SKIPHOURS:
				case DOMIT_RSS_ELEMENT_SKIPDAYS:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
				default:
					$this->addIndexedElement($currNode);

			}
		}
		if($itemCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_ITEMS] = &$this->domit_rss_items;
		}
		if($channelCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_CHANNELS] = &$this->domit_rss_channels;
		}
		if($categoryCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_CATEGORIES] = &$this->domit_rss_categories;
		}
		$this->handleChannelElementsEmbedded();
	}

	function getVersion() {
		return DOMIT_RSS_VERSION;
	}

}

class xml_domit_rss_channel extends xml_domit_rss_elementindexer {
	var $domit_rss_items = array();
	var $domit_rss_categories = array();
	function xml_domit_rss_channel(&$channel) {
		$this->node = &$channel;
		$this->rssDefinedElements = array('title','link','description','language',
			'copyright','managingEditor','webmaster','pubDate','lastBuildDate','generator',
			'docs','cloud','ttl','image','rating','textInput','skipHours','skipDays',
			'domit_rss_channels','domit_rss_items','domit_rss_categories');
		$this->_init();
	}

	function _init() {
		$total = $this->node->childCount;
		$itemCounter = 0;
		$categoryCounter = 0;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$this->node->childNodes[$i];
			$tagName = strtolower($currNode->nodeName);
			switch($tagName) {
				case DOMIT_RSS_ELEMENT_ITEM:
					$this->domit_rss_items[$itemCounter] = new xml_domit_rss_item($currNode);
					$itemCounter++;
					break;
				case DOMIT_RSS_ELEMENT_CATEGORY:
					$this->domit_rss_categories[$categoryCounter] = new xml_domit_rss_category($currNode);
					$categoryCounter++;
					break;
				case DOMIT_RSS_ELEMENT_IMAGE:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_image($currNode);
					break;
				case DOMIT_RSS_ELEMENT_CLOUD:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_cloud($currNode);
					break;
				case DOMIT_RSS_ELEMENT_TEXTINPUT:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_textinput($currNode);
					break;
				case DOMIT_RSS_ELEMENT_SKIPHOURS:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_skiphours($currNode);
					break;
				case DOMIT_RSS_ELEMENT_SKIPDAYS:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_skipdays($currNode);
					break;
				case DOMIT_RSS_ELEMENT_TITLE:
				case DOMIT_RSS_ELEMENT_LINK:
				case DOMIT_RSS_ELEMENT_DESCRIPTION:
				case DOMIT_RSS_ELEMENT_LANGUAGE:
				case DOMIT_RSS_ELEMENT_COPYRIGHT:
				case DOMIT_RSS_ELEMENT_MANAGINGEDITOR:
				case DOMIT_RSS_ELEMENT_WEBMASTER:
				case DOMIT_RSS_ELEMENT_PUBDATE:
				case DOMIT_RSS_ELEMENT_LASTBUILDDATE:
				case DOMIT_RSS_ELEMENT_GENERATOR:
				case DOMIT_RSS_ELEMENT_DOCS:
				case DOMIT_RSS_ELEMENT_TTL:
				case DOMIT_RSS_ELEMENT_RATING:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
				default:
					$this->addIndexedElement($currNode);

			}
		}
		if($itemCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_ITEMS] = &$this->domit_rss_items;
		}
		if($categoryCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_CATEGORIES] = &$this->domit_rss_categories;
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

	function getLanguage() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_LANGUAGE);
	}

	function getCopyright() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_COPYRIGHT);
	}

	function getManagingEditor() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_MANAGINGEDITOR);
	}

	function getWebMaster() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_WEBMASTER);
	}

	function getPubDate() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_PUBDATE);
	}

	function getLastBuildDate() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_LASTBUILDDATE);
	}

	function getGenerator() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_GENERATOR);
	}

	function getDocs() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_DOCS);
	}

	function getCloud() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_CLOUD)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_CLOUD);
		}
		return null;
	}

	function getTTL() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_TTL);
	}

	function getImage() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_IMAGE)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_IMAGE);
		}
		return null;
	}

	function getRating() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_RATING);
	}

	function getTextInput() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_TEXTINPUT)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_TEXTINPUT);
		}
		return null;
	}

	function getSkipDays() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_SKIPDAYS)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_SKIPDAYS);
		}
		return null;
	}

	function getSkipHours() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_SKIPHOURS)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_SKIPHOURS);
		}
		return null;
	}

	function getItemCount() {
		return count($this->domit_rss_items);
	}

	function &getItem($index) {
		return $this->domit_rss_items[$index];
	}

	function getCategoryCount() {
		return count($this->domit_rss_categories);
	}

	function &getCategory($index) {
		return $this->domit_rss_categories[$index];
	}

}

class xml_domit_rss_item extends xml_domit_rss_elementindexer {
	var $domit_rss_categories = array();
	function xml_domit_rss_item(&$item) {
		$this->node = &$item;
		$this->rssDefinedElements = array('title','link','description','author',
			'comments','enclosure','guid','pubDate','source','domit_rss_categories');
		$this->_init();
	}

	function _init() {
		$total = $this->node->childCount;
		$categoryCounter = 0;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$this->node->childNodes[$i];
			$tagName = strtolower($currNode->nodeName);
			switch($tagName) {
				case DOMIT_RSS_ELEMENT_CATEGORY:
					$this->categories[$categoryCounter] = new xml_domit_rss_category($currNode);
					$categoryCounter++;
					break;
				case DOMIT_RSS_ELEMENT_ENCLOSURE:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_enclosure($currNode);
					break;
				case DOMIT_RSS_ELEMENT_SOURCE:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_source($currNode);
					break;
				case DOMIT_RSS_ELEMENT_GUID:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_guid($currNode);
					break;
				case DOMIT_RSS_ELEMENT_TITLE:
				case DOMIT_RSS_ELEMENT_LINK:
				case DOMIT_RSS_ELEMENT_DESCRIPTION:
				case DOMIT_RSS_ELEMENT_AUTHOR:
				case DOMIT_RSS_ELEMENT_COMMENTS:
				case DOMIT_RSS_ELEMENT_PUBDATE:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
				default:
					$this->addIndexedElement($currNode);

			}
		}
		if($categoryCounter != 0) {
			$this->DOMIT_RSS_indexer[DOMIT_RSS_ARRAY_CATEGORIES] = &$this->domit_rss_categories;
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

	function getAuthor() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_AUTHOR);
	}

	function getComments() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_COMMENTS);
	}

	function getEnclosure() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_ENCLOSURE)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_ENCLOSURE);
		}
		return null;
	}

	function getGUID() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_GUID)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_GUID);
		}
		return null;
	}

	function getPubDate() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_PUBDATE);
	}

	function getSource() {
		if($this->hasElement(DOMIT_RSS_ELEMENT_SOURCE)) {
			return $this->getElement(DOMIT_RSS_ELEMENT_SOURCE);
		}
		return null;
	}

	function getCategoryCount() {
		return count($this->domit_rss_categories);
	}

	function &getCategory($index) {
		return $this->domit_rss_categories[$index];
	}

}

class xml_domit_rss_category extends xml_domit_rss_elementindexer {
	function xml_domit_rss_category(&$category) {
		$this->node = &$category;
		$this->_init();
	}

	function getCategory() {
		return $this->node->firstChild->toString();
	}

	function getDomain() {
		return $this->getAttribute(DOMIT_RSS_ATTR_DOMAIN);
	}

}

class xml_domit_rss_image extends xml_domit_rss_elementindexer {
	function xml_domit_rss_image(&$image) {
		$this->node = &$image;
		$this->rssDefinedElements = array('title','link','description','url','width',
			'height');
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
				case DOMIT_RSS_ELEMENT_URL:
				case DOMIT_RSS_ELEMENT_WIDTH:
				case DOMIT_RSS_ELEMENT_HEIGHT:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
				default:
					$this->addIndexedElement($currNode);

			}
		}
	}

	function getTitle() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_TITLE);
	}

	function getLink() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_LINK);
	}

	function getUrl() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_URL);
	}

	function getWidth() {
		$myWidth = $this->getElementText(DOMIT_RSS_ELEMENT_WIDTH);
		if($myWidth == '') {
			$myWidth = '88';
		} else
			if(intval($myWidth) > 144) {
				$myWidth = '144';
			}
		return $myWidth;
	}

	function getHeight() {
		$myHeight = $this->getElementText(DOMIT_RSS_ELEMENT_HEIGHT);
		if($myHeight == '') {
			$myHeight = '31';
		} else
			if(intval($myHeight) > 400) {
				$myHeight = '400';
			}
		return $myHeight;
	}

	function getDescription() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_DESCRIPTION);
	}

}

class xml_domit_rss_textinput extends xml_domit_rss_elementindexer {
	function xml_domit_rss_textinput(&$textinput) {
		$this->node = &$textinput;
		$this->rssDefinedElements = array('title','link','description','name');
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
				case DOMIT_RSS_ELEMENT_NAME:
					$this->DOMIT_RSS_indexer[$tagName] = new xml_domit_rss_simpleelement($currNode);
					break;
				default:
					$this->addIndexedElement($currNode);

			}
		}
	}

	function getTitle() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_TITLE);
	}

	function getDescription() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_DESCRIPTION);
	}

	function getName() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_NAME);
	}

	function getLink() {
		return $this->getElementText(DOMIT_RSS_ELEMENT_LINK);
	}

}

class xml_domit_rss_cloud extends xml_domit_rss_elementindexer {
	function xml_domit_rss_cloud(&$cloud) {
		$this->node = &$cloud;
		$this->_init();
	}

	function getDomain() {
		return $this->getAttribute(DOMIT_RSS_ATTR_DOMAIN);
	}

	function getPort() {
		return $this->getAttribute(DOMIT_RSS_ATTR_PORT);
	}

	function getPath() {
		return $this->getAttribute(DOMIT_RSS_ATTR_PATH);
	}

	function getRegisterProcedure() {
		return $this->getAttribute(DOMIT_RSS_ATTR_REGISTERPROCEDURE);
	}

	function getProtocol() {
		return $this->getAttribute(DOMIT_RSS_ATTR_PROTOCOL);
	}

}

class xml_domit_rss_enclosure extends xml_domit_rss_elementindexer {
	function xml_domit_rss_enclosure(&$enclosure) {
		$this->node = &$enclosure;
		$this->_init();
	}

	function getUrl() {
		return $this->getAttribute(DOMIT_RSS_ATTR_URL);
	}

	function getLength() {
		return $this->getAttribute(DOMIT_RSS_ATTR_LENGTH);
	}

	function getType() {
		return $this->getAttribute(DOMIT_RSS_ATTR_TYPE);
	}

}

class xml_domit_rss_guid extends xml_domit_rss_elementindexer {
	function xml_domit_rss_guid(&$guid) {
		$this->node = &$guid;
		$this->_init();
	}

	function getGuid() {
		return $this->node->getText();
	}

	function isPermaLink() {
		if(!$this->node->hasAttribute(DOMIT_RSS_ATTR_ISPERMALINK)) {
			return true;
		} else {
			return (strtolower($this->node->getAttribute(DOMIT_RSS_ATTR_ISPERMALINK)) ==
				"true");
		}
	}

}

class xml_domit_rss_source extends xml_domit_rss_elementindexer {
	function xml_domit_rss_source(&$source) {
		$this->node = &$source;
		$this->_init();
	}

	function getSource() {
		return $this->node->getText();
	}

	function getUrl() {
		return $this->getAttribute(DOMIT_RSS_ATTR_URL);
	}

}

class xml_domit_rss_skipdays extends xml_domit_rss_elementindexer {
	function xml_domit_rss_skipdays(&$skipdays) {
		$this->node = &$skipdays;
		$this->_init();
	}

	function getSkipDayCount() {
		return $this->node->childCount;
	}

	function getSkipDay($index) {
		return $this->node->childNodes[$index]->getText();
	}

}

class xml_domit_rss_skiphours extends xml_domit_rss_elementindexer {
	function xml_domit_rss_skiphours(&$skiphours) {
		$this->node = &$skiphours;
		$this->_init();
	}

	function getSkipHourCount() {
		return $this->node->childCount;
	}

	function getSkipHour($index) {
		return $this->node->childNodes[$index]->getText();
	}

}




?>
