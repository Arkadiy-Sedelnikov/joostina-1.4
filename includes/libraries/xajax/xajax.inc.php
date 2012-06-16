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

defined('_VALID_MOS') or die();
if(!defined('XAJAX_DEFAULT_CHAR_ENCODING')){
	define('XAJAX_DEFAULT_CHAR_ENCODING', 'utf-8');
}
require_once (dirname(__file__) . "/xajaxResponse.inc.php");
if(!defined('XAJAX_GET')){
	define('XAJAX_GET', 0);
}
if(!defined('XAJAX_POST')){
	define('XAJAX_POST', 1);
}
class xajax{
	var $aFunctions;
	var $aObjects;
	var $aFunctionRequestTypes;
	var $aFunctionIncludeFiles;
	var $sCatchAllFunction;
	var $sPreFunction;
	var $sRequestURI;
	var $sWrapperPrefix;
	var $bDebug;
	var $bStatusMessages;
	var $bExitAllowed;
	var $bWaitCursor;
	var $bErrorHandler;
	var $sLogFile;
	var $bCleanBuffer;
	var $sEncoding;
	var $bDecodeUTF8Input;
	var $bOutputEntities;
	var $aObjArray;
	var $iPos;

	function xajax($sRequestURI = "", $sWrapperPrefix = "xajax_", $sEncoding =
	XAJAX_DEFAULT_CHAR_ENCODING, $bDebug = false){
		$this->aFunctions = array();
		$this->aObjects = array();
		$this->aFunctionIncludeFiles = array();
		$this->sRequestURI = $sRequestURI;
		if($this->sRequestURI == "")
			$this->sRequestURI = $this->_detectURI();
		$this->sWrapperPrefix = $sWrapperPrefix;
		$this->bDebug = $bDebug;
		$this->bStatusMessages = false;
		$this->bWaitCursor = true;
		$this->bExitAllowed = true;
		$this->bErrorHandler = false;
		$this->sLogFile = "";
		$this->bCleanBuffer = false;
		$this->setCharEncoding($sEncoding);
		$this->bDecodeUTF8Input = false;
		$this->bOutputEntities = false;
	}

	function setRequestURI($sRequestURI){
		$this->sRequestURI = $sRequestURI;
	}

	function setWrapperPrefix($sPrefix){
		$this->sWrapperPrefix = $sPrefix;
	}

	function debugOn(){
		$this->bDebug = true;
	}

	function debugOff(){
		$this->bDebug = false;
	}

	function statusMessagesOn(){
		$this->bStatusMessages = true;
	}

	function statusMessagesOff(){
		$this->bStatusMessages = false;
	}

	function waitCursorOn(){
		$this->bWaitCursor = true;
	}

	function waitCursorOff(){
		$this->bWaitCursor = false;
	}

	function exitAllowedOn(){
		$this->bExitAllowed = true;
	}

	function exitAllowedOff(){
		$this->bExitAllowed = false;
	}

	function errorHandlerOn(){
		$this->bErrorHandler = true;
	}

	function errorHandlerOff(){
		$this->bErrorHandler = false;
	}

	function setLogFile($sFilename){
		$this->sLogFile = $sFilename;
	}

	function cleanBufferOn(){
		$this->bCleanBuffer = true;
	}

	function cleanBufferOff(){
		$this->bCleanBuffer = false;
	}

	function setCharEncoding($sEncoding){
		$this->sEncoding = $sEncoding;
	}

	function decodeUTF8InputOn(){
		$this->bDecodeUTF8Input = true;
	}

	function decodeUTF8InputOff(){
		$this->bDecodeUTF8Input = false;
	}

	function outputEntitiesOn(){
		$this->bOutputEntities = true;
	}

	function outputEntitiesOff(){
		$this->bOutputEntities = false;
	}

	function registerFunction($mFunction, $sRequestType = XAJAX_POST){
		if(is_array($mFunction)){
			$this->aFunctions[$mFunction[0]] = 1;
			$this->aFunctionRequestTypes[$mFunction[0]] = $sRequestType;
			$this->aObjects[$mFunction[0]] = array_slice($mFunction, 1);
		} else{
			$this->aFunctions[$mFunction] = 1;
			$this->aFunctionRequestTypes[$mFunction] = $sRequestType;
		}
	}

	function registerExternalFunction($mFunction, $sIncludeFile, $sRequestType =
	XAJAX_POST){
		$this->registerFunction($mFunction, $sRequestType);
		if(is_array($mFunction)){
			$this->aFunctionIncludeFiles[$mFunction[0]] = $sIncludeFile;
		} else{
			$this->aFunctionIncludeFiles[$mFunction] = $sIncludeFile;
		}
	}

	function registerCatchAllFunction($mFunction){
		if(is_array($mFunction)){
			$this->sCatchAllFunction = $mFunction[0];
			$this->aObjects[$mFunction[0]] = array_slice($mFunction, 1);
		} else{
			$this->sCatchAllFunction = $mFunction;
		}
	}

	function registerPreFunction($mFunction){
		if(is_array($mFunction)){
			$this->sPreFunction = $mFunction[0];
			$this->aObjects[$mFunction[0]] = array_slice($mFunction, 1);
		} else{
			$this->sPreFunction = $mFunction;
		}
	}

	function canProcessRequests(){
		if($this->getRequestMode() != -1)
			return true;
		return false;
	}

	function getRequestMode(){
		if(!empty($_GET["xajax"]))
			return XAJAX_GET;
		if(!empty($_POST["xajax"]))
			return XAJAX_POST;
		return -1;
	}

	function processRequests(){
//		$requestMode = -1;
		$sFunctionName = "";
		$bFoundFunction = true;
		$bFunctionIsCatchAll = false;
		$sFunctionNameForSpecial = "";
		$aArgs = array();
		$sPreResponse = "";
		$bEndRequest = false;
		$sResponse = "";
		$requestMode = $this->getRequestMode();
		if($requestMode == -1)
			return;
		if($requestMode == XAJAX_POST){
			$sFunctionName = $_POST["xajax"];
			if(!empty($_POST["xajaxargs"]))
				$aArgs = $_POST["xajaxargs"];
		} else{
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			$sFunctionName = $_GET["xajax"];
			if(!empty($_GET["xajaxargs"]))
				$aArgs = $_GET["xajaxargs"];
		}
		if($this->bErrorHandler){
			$GLOBALS['xajaxErrorHandlerText'] = "";
			set_error_handler("xajaxErrorHandler");
		}
		if($this->sPreFunction){
			if(!$this->_isFunctionCallable($this->sPreFunction)){
				$bFoundFunction = false;
				$objResponse = new xajaxResponse();
				$objResponse->addAlert("Unknown Pre-Function " . $this->sPreFunction);
				$sResponse = $objResponse->getXML();
			}
		}
		if(array_key_exists($sFunctionName, $this->aFunctionIncludeFiles)){
			ob_start();
			include_once ($this->aFunctionIncludeFiles[$sFunctionName]);
			ob_end_clean();
		}
		if($bFoundFunction){
			$sFunctionNameForSpecial = $sFunctionName;
			if(!array_key_exists($sFunctionName, $this->aFunctions)){
				if($this->sCatchAllFunction){
					$sFunctionName = $this->sCatchAllFunction;
					$bFunctionIsCatchAll = true;
				} else{
					$bFoundFunction = false;
					$objResponse = new xajaxResponse();
					$objResponse->addAlert("Unknown Function $sFunctionName.");
					$sResponse = $objResponse->getXML();
				}
			} else
				if($this->aFunctionRequestTypes[$sFunctionName] != $requestMode){
					$bFoundFunction = false;
					$objResponse = new xajaxResponse();
					$objResponse->addAlert("Incorrect Request Type.");
					$sResponse = $objResponse->getXML();
				}
		}
		if($bFoundFunction){
			for($i = 0; $i < sizeof($aArgs); $i++){
				if(get_magic_quotes_gpc() == 1 && is_string($aArgs[$i])){
					$aArgs[$i] = stripslashes($aArgs[$i]);
				}
				if(stristr($aArgs[$i], "<xjxobj>") != false){
					$aArgs[$i] = $this->_xmlToArray("xjxobj", $aArgs[$i]);
				} else
					if(stristr($aArgs[$i], "<xjxquery>") != false){
						$aArgs[$i] = $this->_xmlToArray("xjxquery", $aArgs[$i]);
					} else
						if($this->bDecodeUTF8Input){
							$aArgs[$i] = $this->_decodeUTF8Data($aArgs[$i]);
						}
			}
			if($this->sPreFunction){
				$mPreResponse = $this->_callFunction($this->sPreFunction, array($sFunctionNameForSpecial,
					$aArgs));
				if(is_array($mPreResponse) && $mPreResponse[0] === false){
					$bEndRequest = true;
					$sPreResponse = $mPreResponse[1];
				} else{
					$sPreResponse = $mPreResponse;
				}
				if($sPreResponse instanceof  xajaxResponse){
					$sPreResponse = $sPreResponse->getXML();
				}
				if($bEndRequest)
					$sResponse = $sPreResponse;
			}
			if(!$bEndRequest){
				if(!$this->_isFunctionCallable($sFunctionName)){
					$objResponse = new xajaxResponse();
					$objResponse->addAlert("The Registered Function $sFunctionName Could Not Be Found.");
					$sResponse = $objResponse->getXML();
				} else{
					if($bFunctionIsCatchAll){
						$aArgs = array($sFunctionNameForSpecial, $aArgs);
					}
					$sResponse = $this->_callFunction($sFunctionName, $aArgs);
				}
				if($sResponse instanceof xajaxResponse){
					$sResponse = $sResponse->getXML();
				}
				if(!is_string($sResponse) || strpos($sResponse, "<xjx>") === false){
					$objResponse = new xajaxResponse();
					$objResponse->addAlert("No XML Response Was Returned By Function $sFunctionName.");
					$sResponse = $objResponse->getXML();
				} else
					if($sPreResponse != ""){
						$sNewResponse = new xajaxResponse($this->sEncoding, $this->bOutputEntities);
						$sNewResponse->loadXML($sPreResponse);
						$sNewResponse->loadXML($sResponse);
						$sResponse = $sNewResponse->getXML();
					}
			}
		}
		$sContentHeader = "Content-type: text/xml;";
		if($this->sEncoding && strlen(trim($this->sEncoding)) > 0)
			$sContentHeader .= " charset=" . $this->sEncoding;
		header($sContentHeader);
		if($this->bErrorHandler && !empty($GLOBALS['xajaxErrorHandlerText'])){
			$sErrorResponse = new xajaxResponse();
			$sErrorResponse->addAlert("** PHP Error Messages:**" . $GLOBALS['xajaxErrorHandlerText']);
			if($this->sLogFile){
				$fH = @fopen($this->sLogFile, "a");
				if(!$fH){
					$sErrorResponse->addAlert("** Logging Error**\n\nxajax was unable to write to the error log file:\n" .
						$this->sLogFile);
				} else{
					fwrite($fH, "** xajax Error Log - " . strftime("%b %e %Y %I:%M:%S %p") . "**" . $GLOBALS['xajaxErrorHandlerText'] .
						"\n\n\n");
					fclose($fH);
				}
			}
			$sErrorResponse->loadXML($sResponse);
			$sResponse = $sErrorResponse->getXML();
		}
		if($this->bCleanBuffer)
			while(@ob_end_clean())
				;
		print $sResponse;
		if($this->bErrorHandler)
			restore_error_handler();
		if($this->bExitAllowed)
			exit();
	}

	function printJavascript($sJsURI = "", $sJsFile = null){
		print $this->getJavascript($sJsURI, $sJsFile);
	}

	function getJavascript($sJsURI = "", $sJsFile = null){
		$html = $this->getJavascriptConfig();
		$html .= $this->getJavascriptInclude($sJsURI, $sJsFile);
		return $html;
	}

	function getJavascriptConfig(){
		$html = "\t<script type=\"text/javascript\">\n";
		$html .= "var xajaxRequestUri=\"" . $this->sRequestURI . "\";\n";
		$html .= "var xajaxDebug=" . ($this->bDebug ? "true" : "false") . ";\n";
		$html .= "var xajaxStatusMessages=" . ($this->bStatusMessages ? "true" : "false") . ";\n";
		$html .= "var xajaxWaitCursor=" . ($this->bWaitCursor ? "true" : "false") . ";\n";
		$html .= "var xajaxDefinedGet=" . XAJAX_GET . ";\n";
		$html .= "var xajaxDefinedPost=" . XAJAX_POST . ";\n";
		$html .= "var xajaxLoaded=false;\n";
		foreach($this->aFunctions as $sFunction => $bExists){
			$html .= $this->_wrap($sFunction, $this->aFunctionRequestTypes[$sFunction]);
		}
		$html .= "\t</script>\n";
		return $html;
	}

	function getJavascriptInclude($sJsURI = "", $sJsFile = null){
		if($sJsFile == null)
			$sJsFile = "xajax_js/xajax.js";
		if($sJsURI != "" && substr($sJsURI, -1) != "/")
			$sJsURI .= "/";
		$html = "\t<script type=\"text/javascript\" src=\"" . $sJsURI . $sJsFile . "\"></script>\n";
		$html .= "\t<script type=\"text/javascript\">\n";
		$html .= "window.setTimeout(function () { if (!xajaxLoaded) { alert('Error: the xajax Javascript file could not be included. Perhaps the URL is incorrect?\\nURL: {$sJsURI}{$sJsFile}'); } }, 6000);\n";
		$html .= "\t</script>\n";
		return $html;
	}

	function autoCompressJavascript($sJsFullFilename = null){
		$sJsFile = "xajax_js/xajax.js";
		if($sJsFullFilename){
			$realJsFile = $sJsFullFilename;
		} else{
			$realPath = realpath(dirname(__file__));
			$realJsFile = $realPath . "/" . $sJsFile;
		}
		if(!file_exists($realJsFile)){
			$srcFile = str_replace(".js", "_uncompressed.js", $realJsFile);
			if(!file_exists($srcFile)){
				trigger_error("The xajax uncompressed Javascript file could not be found in the <b>" .
					dirname($realJsFile) . "</b> folder. Error ", E_USER_ERROR);
			}
			require (dirname(__file__) . "/xajaxCompress.php");
			$javaScript = implode('', file($srcFile));
			$compressedScript = xajaxCompressJavascript($javaScript);
			$fH = @fopen($realJsFile, "w");
			if(!$fH){
				trigger_error("The xajax compressed javascript file could not be written in the <b>" .
					dirname($realJsFile) . "</b> folder. Error ", E_USER_ERROR);
			} else{
				fwrite($fH, $compressedScript);
				fclose($fH);
			}
		}
	}

	function _detectURI(){
		$aURL = array();
		if(!empty($_SERVER['REQUEST_URI'])){
			$aURL = parse_url($_SERVER['REQUEST_URI']);
		}
		if(empty($aURL['scheme'])){
			if(!empty($_SERVER['HTTP_SCHEME'])){
				$aURL['scheme'] = $_SERVER['HTTP_SCHEME'];
			} else{
				$aURL['scheme'] = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !=
					'off') ? 'https' : 'http';
			}
		}
		if(empty($aURL['host'])){
			if(!empty($_SERVER['HTTP_HOST'])){
				if(strpos($_SERVER['HTTP_HOST'], ':') > 0){
					list($aURL['host'], $aURL['port']) = explode(':', $_SERVER['HTTP_HOST']);
				} else{
					$aURL['host'] = $_SERVER['HTTP_HOST'];
				}
			} else
				if(!empty($_SERVER['SERVER_NAME'])){
					$aURL['host'] = $_SERVER['SERVER_NAME'];
				} else{
					print "xajax Error: xajax failed to automatically identify your Request URI.";
					print "Please set the Request URI explicitly when you instantiate the xajax object.";
					exit();
				}
		}
		if(empty($aURL['port']) && !empty($_SERVER['SERVER_PORT'])){
			$aURL['port'] = $_SERVER['SERVER_PORT'];
		}
		if(empty($aURL['path'])){
			if(!empty($_SERVER['PATH_INFO'])){
				$sPath = parse_url($_SERVER['PATH_INFO']);
			} else{
				$sPath = parse_url($_SERVER['PHP_SELF']);
			}
			$aURL['path'] = $sPath['path'];
			unset($sPath);
		}
		if(!empty($aURL['query'])){
			$aURL['query'] = '?' . $aURL['query'];
		}
		$sURL = $aURL['scheme'] . '://';
		if(!empty($aURL['user'])){
			$sURL .= $aURL['user'];
			if(!empty($aURL['pass'])){
				$sURL .= ':' . $aURL['pass'];
			}
			$sURL .= '@';
		}
		$sURL .= $aURL['host'];
		if(!empty($aURL['port']) && (($aURL['scheme'] == 'http' && $aURL['port'] != 80) ||
			($aURL['scheme'] == 'https' && $aURL['port'] != 443))
		){
			$sURL .= ':' . $aURL['port'];
		}
		$sURL .= $aURL['path'] . @$aURL['query'];
		unset($aURL);
		return $sURL;
	}

	function _isObjectCallback($sFunction){
		if(array_key_exists($sFunction, $this->aObjects))
			return true;
		return false;
	}

	function _isFunctionCallable($sFunction){
		if($this->_isObjectCallback($sFunction)){
			if(is_object($this->aObjects[$sFunction][0])){
				return method_exists($this->aObjects[$sFunction][0], $this->aObjects[$sFunction][1]);
			} else{
				return is_callable($this->aObjects[$sFunction]);
			}
		} else{
			return function_exists($sFunction);
		}
	}

	function _callFunction($sFunction, $aArgs){
		if($this->_isObjectCallback($sFunction)){
			$mReturn = call_user_func_array($this->aObjects[$sFunction], $aArgs);
		} else{
			$mReturn = call_user_func_array($sFunction, $aArgs);
		}
		return $mReturn;
	}

	function _wrap($sFunction, $sRequestType = XAJAX_POST){
		$js = "function " . $this->sWrapperPrefix . "$sFunction(){return xajax.call(\"$sFunction\", arguments, " .
			$sRequestType . ");}\n";
		return $js;
	}

	function _xmlToArray($rootTag, $sXml){
		$aArray = array();
		$sXml = str_replace("<$rootTag>", "<$rootTag>|~|", $sXml);
		$sXml = str_replace("</$rootTag>", "</$rootTag>|~|", $sXml);
		$sXml = str_replace("<e>", "<e>|~|", $sXml);
		$sXml = str_replace("</e>", "</e>|~|", $sXml);
		$sXml = str_replace("<k>", "<k>|~|", $sXml);
		$sXml = str_replace("</k>", "|~|</k>|~|", $sXml);
		$sXml = str_replace("<v>", "<v>|~|", $sXml);
		$sXml = str_replace("</v>", "|~|</v>|~|", $sXml);
		$sXml = str_replace("<q>", "<q>|~|", $sXml);
		$sXml = str_replace("</q>", "|~|</q>|~|", $sXml);
		$this->aObjArray = explode("|~|", $sXml);
		$this->iPos = 0;
		$aArray = $this->_parseObjXml($rootTag);
		return $aArray;
	}

	function _parseObjXml($rootTag){
		$aArray = array();
		if($rootTag == "xjxobj"){
			while(!stristr($this->aObjArray[$this->iPos], "</xjxobj>")){
				$this->iPos++;
				if(stristr($this->aObjArray[$this->iPos], "<e>")){
					$key = "";
					$value = null;
					$this->iPos++;
					while(!stristr($this->aObjArray[$this->iPos], "</e>")){
						if(stristr($this->aObjArray[$this->iPos], "<k>")){
							$this->iPos++;
							while(!stristr($this->aObjArray[$this->iPos], "</k>")){
								$key .= $this->aObjArray[$this->iPos];
								$this->iPos++;
							}
						}
						if(stristr($this->aObjArray[$this->iPos], "<v>")){
							$this->iPos++;
							while(!stristr($this->aObjArray[$this->iPos], "</v>")){
								if(stristr($this->aObjArray[$this->iPos], "<xjxobj>")){
									$value = $this->_parseObjXml("xjxobj");
									$this->iPos++;
								} else{
									$value .= $this->aObjArray[$this->iPos];
									if($this->bDecodeUTF8Input){
										$value = $this->_decodeUTF8Data($value);
									}
								}
								$this->iPos++;
							}
						}
						$this->iPos++;
					}
					$aArray[$key] = $value;
				}
			}
		}
		if($rootTag == "xjxquery"){
			$sQuery = "";
			$this->iPos++;
			while(!stristr($this->aObjArray[$this->iPos], "</xjxquery>")){
				if(stristr($this->aObjArray[$this->iPos], "<q>") || stristr($this->aObjArray[$this->iPos],
					"</q>")
				){
					$this->iPos++;
					continue;
				}
				$sQuery .= $this->aObjArray[$this->iPos];
				$this->iPos++;
			}
			parse_str($sQuery, $aArray);
			if($this->bDecodeUTF8Input){
				foreach($aArray as $key => $value){
					$aArray[$key] = $this->_decodeUTF8Data($value);
				}
			}
			if(get_magic_quotes_gpc() == 1){
				$newArray = array();
				foreach($aArray as $sKey => $sValue){
					if(is_string($sValue))
						$newArray[$sKey] = stripslashes($sValue);
					else
						$newArray[$sKey] = $sValue;
				}
				$aArray = $newArray;
			}
		}
		return $aArray;
	}

	function _decodeUTF8Data($sData){
		$sValue = $sData;
		if($this->bDecodeUTF8Input){
			$sFuncToUse = null;
			if(function_exists('iconv')){
				$sFuncToUse = "iconv";
			} else
				if(function_exists('mb_convert_encoding')){
					$sFuncToUse = "mb_convert_encoding";
				} else
					if($this->sEncoding == "ISO-8859-1"){
						$sFuncToUse = "utf8_decode";
					} else{
						trigger_error("The incoming xajax data could not be converted from UTF-8",
							E_USER_NOTICE);
					}
			if($sFuncToUse){
				if(is_string($sValue)){
					if($sFuncToUse == "iconv"){
						$sValue = iconv("UTF-8", $this->sEncoding . '//TRANSLIT', $sValue);
					} else
						if($sFuncToUse == "mb_convert_encoding"){
							$sValue = mb_convert_encoding($sValue, $this->sEncoding, "UTF-8");
						} else{
							$sValue = utf8_decode($sValue);
						}
				}
			}
		}
		return $sValue;
	}
}

function xajaxErrorHandler($errno, $errstr, $errfile, $errline){
	$errorReporting = error_reporting();
	if(($errno & $errorReporting) == 0)
		return;
	if($errno == E_NOTICE){
		$errTypeStr = "NOTICE";
	} else
		if($errno == E_WARNING){
			$errTypeStr = "WARNING";
		} else
			if($errno == E_USER_NOTICE){
				$errTypeStr = "USER NOTICE";
			} else
				if($errno == E_USER_WARNING){
					$errTypeStr = "USER WARNING";
				} else
					if($errno == E_USER_ERROR){
						$errTypeStr = "USER FATAL ERROR";
					} else
						if($errno == E_STRICT){
							return;
						} else{
							$errTypeStr = "UNKNOWN: $errno";
						}
	$GLOBALS['xajaxErrorHandlerText'] .= "\n----\n[$errTypeStr] $errstr\nerror in line $errline of file $errfile";
}

?>
