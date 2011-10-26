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
if(!defined('DOM_XMLRPC_INCLUDE_PATH')) {
	define('DOM_XMLRPC_INCLUDE_PATH',(dirname(__file__)."/"));
}
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
class dom_xmlrpc_parser {
	var $arrayDocument = null;
	var $charContainer = '';
	var $lastArrayType = array();
	var $lastArray = array();
	var $lastStructName = array();
	function parseXML($xmlText,$useSAXY = true) {
		$xmlText = trim($xmlText);
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_array_document.php');
		$this->arrayDocument = new dom_xmlrpc_array_document();
		if($xmlText != '') {
			if($useSAXY || (!function_exists('xml_parser_create'))) {

				return $this->parseSAXY($xmlText);
			} else {

				return $this->parse($xmlText);
			}
		}
		return false;
	}

	function parse($xmlText) {

		$parser = xml_parser_create();

		xml_set_object($parser,$this);
		xml_set_element_handler($parser,'startElement','endElement');
		xml_set_character_data_handler($parser,array(&$this,'dataElement'));
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);


		$xmlText = preg_replace('/>'."[[:space:]]+".'</iu','><',$xmlText);
		$success = xml_parse($parser,$xmlText);
		xml_parser_free($parser);
		return $success;
	}

	function parseSAXY($xmlText) {

		require_once (DOM_XMLRPC_INCLUDE_PATH.'xml_saxy_parser.php');
		$parser = new SAXY_Lite_Parser();
		$parser->xml_set_element_handler(array(&$this,'startElement'),array(&$this,'endElement'));
		$parser->xml_set_character_data_handler(array(&$this,'dataElement'));
		$success = $parser->parse($xmlText);
		return $success;
	}

	function &getArrayDocument() {
		return $this->arrayDocument;
	}

	function startElement($parser,$name,$attrs) {

	}

	function endElement($parser,$name) {

	}

	function addValue($value) {

	}

	function dataElement($parser,$data) {
		$this->charContainer .= $data;
	}

}




?>
