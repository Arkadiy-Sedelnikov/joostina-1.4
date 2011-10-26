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
define('DOM_XMLRPC_TYPE_ITEM','item');
define('DOM_XMLRPC_TYPE_INT','int');
define('DOM_XMLRPC_TYPE_I4','i4');
define('DOM_XMLRPC_TYPE_DOUBLE','double');
define('DOM_XMLRPC_TYPE_BOOLEAN','boolean');
define('DOM_XMLRPC_TYPE_STRING','string');
define('DOM_XMLRPC_TYPE_DATETIME','dateTime.iso8601');
define('DOM_XMLRPC_TYPE_BASE64','base64');
define('DOM_XMLRPC_TYPE_METHODCALL','methodCall');
define('DOM_XMLRPC_TYPE_METHODNAME','methodName');
define('DOM_XMLRPC_TYPE_PARAMS','params');
define('DOM_XMLRPC_TYPE_PARAM','param');
define('DOM_XMLRPC_TYPE_VALUE','value');
define('DOM_XMLRPC_TYPE_STRUCT','struct');
define('DOM_XMLRPC_TYPE_MEMBER','member');
define('DOM_XMLRPC_TYPE_NAME','name');
define('DOM_XMLRPC_TYPE_ARRAY','array');
define('DOM_XMLRPC_TYPE_DATA','data');
define('DOM_XMLRPC_TYPE_METHODRESPONSE','methodResponse');
define('DOM_XMLRPC_TYPE_FAULT','fault');
define('DOM_XMLRPC_TYPE_SCALAR','scalar');

define('DOM_XMLRPC_NODEVALUE_FAULTCODE','faultCode');
define('DOM_XMLRPC_NODEVALUE_FAULTSTRING','faultString');
define('DOM_XMLRPC_RESPONSE_TYPE_STRING','string');
define('DOM_XMLRPC_RESPONSE_TYPE_ARRAY','array');
define('DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT','xml_domit');
define('DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT_LITE','xml_domit_lite');
define('DOM_XMLRPC_RESPONSE_TYPE_XML_DOMXML','xml_domxml');
define('DOM_XMLRPC_OBJECT_MARSHALLING_ANONYMOUS','anonymous');
define('DOM_XMLRPC_OBJECT_MARSHALLING_NAMED','named');
define('DOM_XMLRPC_OBJECT_MARSHALLING_SERIALIZED','serialized');
define('DOM_XMLRPC_PHPOBJECT','__phpobject__');
define('DOM_XMLRPC_SERIALIZED','__serialized__');
?>
