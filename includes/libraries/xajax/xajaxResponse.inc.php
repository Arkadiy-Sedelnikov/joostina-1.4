<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * xajax version 0.2.4
 * copyright (c) 2005 by Jared White & J. Max Wilson
 * http://www.xajaxproject.org
 **/

defined('_JLINDEX') or die();
class xajaxResponse{
	var $xml;
	var $sEncoding;
	var $bOutputEntities;

	function xajaxResponse($sEncoding = XAJAX_DEFAULT_CHAR_ENCODING, $bOutputEntities = false){
		$this->setCharEncoding($sEncoding);
		$this->bOutputEntities = $bOutputEntities;
	}

	function setCharEncoding($sEncoding){
		$this->sEncoding = $sEncoding;
	}

	function outputEntitiesOn(){
		$this->bOutputEntities = true;
	}

	function outputEntitiesOff(){
		$this->bOutputEntities = false;
	}

	function addConfirmCommands($iCmdNumber, $sMessage){
		$this->xml .= $this->_cmdXML(array("n" => "cc", "t" => $iCmdNumber), $sMessage);
	}

	function addAssign($sTarget, $sAttribute, $sData){
		$this->xml .= $this->_cmdXML(array("n" => "as", "t" => $sTarget, "p" => $sAttribute),
			$sData);
	}

	function addAppend($sTarget, $sAttribute, $sData){
		$this->xml .= $this->_cmdXML(array("n" => "ap", "t" => $sTarget, "p" => $sAttribute),
			$sData);
	}

	function addPrepend($sTarget, $sAttribute, $sData){
		$this->xml .= $this->_cmdXML(array("n" => "pp", "t" => $sTarget, "p" => $sAttribute),
			$sData);
	}

	function addReplace($sTarget, $sAttribute, $sSearch, $sData){
		$sDta = "<s><![CDATA[$sSearch]]></s><r><![CDATA[$sData]]></r>";
		$this->xml .= $this->_cmdXML(array("n" => "rp", "t" => $sTarget, "p" => $sAttribute),
			$sDta);
	}

	function addClear($sTarget, $sAttribute){
		$this->addAssign($sTarget, $sAttribute, '');
	}

	function addAlert($sMsg){
		$this->xml .= $this->_cmdXML(array("n" => "al"), $sMsg);
	}

	function addRedirect($sURL){
		$queryStart = strpos($sURL, '?', strrpos($sURL, '/'));
		if($queryStart !== false){
			$queryStart++;
			$queryEnd = strpos($sURL, '#', $queryStart);
			if($queryEnd === false)
				$queryEnd = strlen($sURL);
			$queryPart = substr($sURL, $queryStart, $queryEnd - $queryStart);
			parse_str($queryPart, $queryParts);
			$newQueryPart = "";
			foreach($queryParts as $key => $value){
				$newQueryPart .= rawurlencode($key) . '=' . rawurlencode($value) . ini_get('arg_separator.output');
			}
			$sURL = str_replace($queryPart, $newQueryPart, $sURL);
		}
		$this->addScript('window.location = "' . $sURL . '";');
	}

	function addScript($sJS){
		$this->xml .= $this->_cmdXML(array("n" => "js"), $sJS);
	}

	function addScriptCall(){
		$arguments = func_get_args();
		$sFunc = array_shift($arguments);
		$sData = $this->_buildObjXml($arguments);
		$this->xml .= $this->_cmdXML(array("n" => "jc", "t" => $sFunc), $sData);
	}

	function addRemove($sTarget){
		$this->xml .= $this->_cmdXML(array("n" => "rm", "t" => $sTarget), '');
	}

	function addCreate($sParent, $sTag, $sId, $sType = ""){
		if($sType){
			trigger_error("The \$sType parameter of addCreate has been deprecated.  Use the addCreateInput() method instead.",
				E_USER_WARNING);
			return;
		}
		$this->xml .= $this->_cmdXML(array("n" => "ce", "t" => $sParent, "p" => $sId), $sTag);
	}

	function addInsert($sBefore, $sTag, $sId){
		$this->xml .= $this->_cmdXML(array("n" => "ie", "t" => $sBefore, "p" => $sId), $sTag);
	}

	function addInsertAfter($sAfter, $sTag, $sId){
		$this->xml .= $this->_cmdXML(array("n" => "ia", "t" => $sAfter, "p" => $sId), $sTag);
	}

	function addCreateInput($sParent, $sType, $sName, $sId){
		$this->xml .= $this->_cmdXML(array("n" => "ci", "t" => $sParent, "p" => $sId, "c" =>
		$sType), $sName);
	}

	function addInsertInput($sBefore, $sType, $sName, $sId){
		$this->xml .= $this->_cmdXML(array("n" => "ii", "t" => $sBefore, "p" => $sId, "c" =>
		$sType), $sName);
	}

	function addInsertInputAfter($sAfter, $sType, $sName, $sId){
		$this->xml .= $this->_cmdXML(array("n" => "iia", "t" => $sAfter, "p" => $sId, "c" =>
		$sType), $sName);
	}

	function addEvent($sTarget, $sEvent, $sScript){
		$this->xml .= $this->_cmdXML(array("n" => "ev", "t" => $sTarget, "p" => $sEvent),
			$sScript);
	}

	function addHandler($sTarget, $sEvent, $sHandler){
		$this->xml .= $this->_cmdXML(array("n" => "ah", "t" => $sTarget, "p" => $sEvent),
			$sHandler);
	}

	function addRemoveHandler($sTarget, $sEvent, $sHandler){
		$this->xml .= $this->_cmdXML(array("n" => "rh", "t" => $sTarget, "p" => $sEvent),
			$sHandler);
	}

	function addIncludeScript($sFileName){
		$this->xml .= $this->_cmdXML(array("n" => "in"), $sFileName);
	}

	function getXML(){
		$sXML = "<?xml version=\"1.0\"";
		if($this->sEncoding && strlen(trim($this->sEncoding)) > 0)
			$sXML .= " encoding=\"" . $this->sEncoding . "\"";
		$sXML .= " ?" . "><xjx>" . $this->xml . "</xjx>";
		return $sXML;
	}

	function loadXML($mXML){
		if($mXML instanceof  xajaxResponse){
			$mXML = $mXML->getXML();
		}
		$sNewXML = "";
		$iStartPos = strpos($mXML, "<xjx>") + 5;
		$sNewXML = substr($mXML, $iStartPos);
		$iEndPos = strpos($sNewXML, "</xjx>");
		$sNewXML = substr($sNewXML, 0, $iEndPos);
		$this->xml .= $sNewXML;
	}

	function _cmdXML($aAttributes, $sData){
		if($this->bOutputEntities){
			if(function_exists('mb_convert_encoding')){
				$sData = call_user_func_array('mb_convert_encoding', array(&$sData,
					'HTML-ENTITIES', $this->sEncoding));
			} else{
				trigger_error("The xajax XML response output could not be converted to HTML entities because the mb_convert_encoding function is not available",
					E_USER_NOTICE);
			}
		}
		$xml = "<cmd";
		foreach($aAttributes as $sAttribute => $sValue)
			$xml .= " $sAttribute=\"$sValue\"";
		if($sData !== null && !stristr($sData, '<![CDATA['))
			$xml .= "><![CDATA[$sData]]></cmd>";
		else
			if($sData !== null)
				$xml .= ">$sData</cmd>";
			else
				$xml .= "></cmd>";
		return $xml;
	}

	function _buildObjXml($var){
		if(gettype($var) == "object")
			$var = get_object_vars($var);
		if(!is_array($var)){
			return "<![CDATA[$var]]>";
		} else{
			$data = "<xjxobj>";
			foreach($var as $key => $value){
				$data .= "<e>";
				$data .= "<k>" . htmlspecialchars($key) . "</k>";
				$data .= "<v>" . $this->_buildObjXml($value) . "</v>";
				$data .= "</e>";
			}
			$data .= "</xjxobj>";
			return $data;
		}
	}
}


?>
