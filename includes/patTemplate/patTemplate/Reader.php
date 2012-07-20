<?php
/**
 * @package Joostina
 * @copyright ��������� ����� (C) 2008 Joostina team. ��� ����� ��������.
 * @license �������� http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, ��� help/license.php
 * Joostina! - ��������� ����������� ����������� ���������������� �� �������� �������� GNU/GPL
 * ��� ��������� ���������� � ������������ ����������� � ��������� �� ��������� �����, �������� ���� help/copyright.php.
 * @version        3.1.0
 * @package        patTemplate
 * @author        Stephan Schmidt <schst@php.net>
 * @license        LGPL
 * @link        http://www.php-tools.net
 */
// ������ ������� �������
defined('_JLINDEX') or die();


define('PATTEMPLATE_READER_ERROR_NO_INPUT', 6000);
define('PATTEMPLATE_READER_ERROR_UNKNOWN_TAG', 6001);
define('PATTEMPLATE_READER_ERROR_INVALID_TAG', 6002);
define('PATTEMPLATE_READER_ERROR_NO_CLOSING_TAG', 6003);
define('PATTEMPLATE_READER_ERROR_INVALID_CLOSING_TAG', 6004);
define('PATTEMPLATE_READER_ERROR_INVALID_CONDITION', 6005);
define('PATTEMPLATE_READER_ERROR_NO_NAME_SPECIFIED', 6010);
define('PATTEMPLATE_READER_NOTICE_INVALID_CDATA_SECTION', 6050);
define('PATTEMPLATE_READER_NOTICE_TEMPLATE_EXISTS', 6051);
class patTemplate_Reader extends patTemplate_Module{
	var $_tmpl;
	var $_elStack;
	var $_tmplStack;
	var $_data;
	var $_depth;
	var $_templates = array();
	var $_path = array();
	var $_startTag;
	var $_endTag;
	var $_defaultAtts = array();
	var $_rootAtts = array();
	var $_inheritAtts = array();
	var $_root = null;
	var $_processedData = null;
	var $_currentInput = null;
	var $_functions = array();
	var $_funcAliases = array();
	var $_options = array();
	var $_inUse = false;

	function setTemplateReference(&$tmpl){
		$this->_tmpl = &$tmpl;
	}

	function readTemplates($input, $options = array()){
		return array();
	}

	function loadTemplate($input, $options = array()){
		return $input;
	}

	function setOptions($options){
		$this->_startTag = $options['startTag'];
		$this->_endTag = $options['endTag'];
		$this->_options = $options;
		if(isset($options['functionAliases'])){
			$this->_funcAliases = $options['functionAliases'];
		}
		array_map('strtolower', $this->_funcAliases);
	}

	function addFunctionAlias($alias, $function){
		$this->_funcAliases[strtolower($alias)] = $function;
	}

	function setRootAttributes($attributes){
		$this->_rootAtts = $attributes;
	}

	function parseString($string){
		$this->_inUse = true;
		$string = $this->_tmpl->applyInputFilters($string);
		$this->_inheritAtts = array();
		$this->_elStack = array();
		$this->_data = array('');
		$this->_tmplStack = array();
		$this->_depth = 0;
		$this->_templates = array();
		$this->_path = array();
		$this->_processedData = '';
		$this->_defaultAtts = $this->_tmpl->getDefaultAttributes();
		if(!isset($this->_defaultAtts['autoload'])){
			$this->_defaultAtts['autoload'] = 'on';
		}
		$attributes = $this->_rootAtts;
		$attributes['name'] = '__ptroot';
		$rootTemplate = $this->_initTemplate($attributes);
		$this->_root = null;
		unset($rootTemplate['isRoot']);
		array_push($this->_tmplStack, $rootTemplate);
		$patNamespace = $this->_tmpl->getNamespace();
		if(is_array($patNamespace)){
			$patNamespace = array_map('strtolower', $patNamespace);
		} else{
			$patNamespace = array(strtolower($patNamespace));
		}
		$regexp = '/(<(\/?)([[:alnum:]]+):([[:alnum:]]+)[[:space:]]*([^>]*)>)/im';
		$tokens = preg_split($regexp, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		if($tokens[0] != ''){
			$this->_characterData($tokens[0]);
		}
		$cnt = count($tokens);
		$i = 1;

		while($i < $cnt){
			$fullTag = $tokens[$i++];
			$closing = $tokens[$i++];
			$namespace = strtolower($tokens[$i++]);
			$tagname = strtolower($tokens[$i++]);
			$attString = $tokens[$i++];
			$empty = substr($attString, -1);
			$data = $tokens[$i++];
			if(!in_array($namespace, $patNamespace)){
				$this->_characterData($fullTag);
				$this->_characterData($data);
				continue;
			}
			if($closing === '/'){
				$result = $this->_endElement($namespace, $tagname);
				if(patErrorManager::isError($result)){
					return $result;
				}
				$this->_characterData($data);
				continue;
			}
			if($empty === '/'){
				$attString = substr($attString, 0, -1);
			}
			$attributes = $this->_parseAttributes($attString);
			$result = $this->_startElement($namespace, $tagname, $attributes);
			if(patErrorManager::isError($result)){
				return $result;
			}
			if($empty === '/'){
				$result = $this->_endElement($namespace, $tagname);
				if(patErrorManager::isError($result)){
					return $result;
				}
			}
			$this->_characterData($data);
		}
		$rootTemplate = array_pop($this->_tmplStack);
		$this->_closeTemplate($rootTemplate, $this->_data[0]);
		if($this->_depth > 0){
			$el = array_pop($this->_elStack);
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_NO_CLOSING_TAG, $this->_createErrorMessage("No closing tag for {$el['ns']}:{$el['name']} found"));
		}
		$this->_inUse = false;
		return $this->_templates;
	}

	function _parseAttributes($string){
		static $entities = array('&lt;' => '<', '&gt;' => '>', '&amp;' => '&', '&quot;' => '"', '&apos;' => '\'');
		$attributes = array();
		$match = array();
		preg_match_all('/([a-zA-Z_0-9]+)="((?:\\\.|[^"\\\])*)"/U', $string, $match);
		for($i = 0; $i < count($match[1]); $i++){
			$attributes[strtolower($match[1][$i])] = strtr((string )$match[2][$i], $entities);
		}
		return $attributes;
	}

	function _startElement($ns, $name, $attributes){
		array_push($this->_elStack, array('ns' => $ns, 'name' => $name, 'attributes' => $attributes,));
		$this->_depth++;
		$this->_data[$this->_depth] = '';
		switch($name){
			case 'tmpl':
				$result = $this->_initTemplate($attributes);
				break;
			case 'sub':
				$result = $this->_initSubTemplate($attributes);
				break;
			case 'link':
				$result = $this->_initLink($attributes);
				break;
			case 'var':
				$result = false;
				break;
			case 'instance':
			case 'comment':
				$result = false;
				break;
			default:
				if(isset($this->_funcAliases[strtolower($name)])){
					$name = $this->_funcAliases[strtolower($name)];
				}
				$name = ucfirst($name);
				if(!$this->_tmpl->moduleExists('Function', $name)){
					if(isset($this->_options['defaultFunction']) && !empty($this->_options['defaultFunction'])){
						$attributes['_originalTag'] = $name;
						$name = ucfirst($this->_options['defaultFunction']);
					} else{
						return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_UNKNOWN_TAG, $this->_createErrorMessage("Unknown tag {$ns}:{$name}."));
					}
				}
				$result = array('type' => 'custom', 'function' => $name, 'attributes' => $attributes);
				break;
		}
		if(patErrorManager::isError($result)){
			return $result;
		}
		array_push($this->_tmplStack, $result);
		return true;
	}

	function _endElement($ns, $name){
		$el = array_pop($this->_elStack);
		$data = $this->_getCData();
		$this->_depth--;
		if($el['name'] != $name || $el['ns'] != $ns){
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_CLOSING_TAG, $this->_createErrorMessage("Invalid closing tag {$ns}:{$name}, {$el['ns']}:{$el['name']} expected"));
		}
		$tmpl = array_pop($this->_tmplStack);
		switch($name){
			case 'tmpl':
				$this->_closeTemplate($tmpl, $data);
				break;
			case 'sub':
				$this->_closeSubTemplate($tmpl, $data);
				break;
			case 'link':
				$this->_closeLink($tmpl);
				break;
			case 'var':
				$this->_handleVariable($el['attributes'], $data);
				break;
			case 'instance':
				break;
			case 'comment':
				$this->_handleComment($el['attributes'], $data);
				break;
			default:
				$name = ucfirst($tmpl['function']);
				if(!isset($this->_functions[$name])){
					$this->_functions[$name] = $this->_tmpl->loadModule('Function', $name);
					$this->_functions[$name]->setReader($this);
				}
				$result = $this->_functions[$name]->call($tmpl['attributes'], $data);
				if(patErrorManager::isError($result)){
					return $result;
				}
				if(is_string($result)){
					$this->_characterData($result, false);
				}
				break;
		}
		return true;
	}

	function _characterData($data, $readFromTemplate = true){
		$this->_data[$this->_depth] .= $data;
		if($readFromTemplate){
			$this->_processedData .= $data;
		}
		return true;
	}

	function _initLink($attributes){
		if(!isset($attributes['src'])){
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_TAG, $this->_createErrorMessage("Attribute 'src' missing for link"));
		}
		$tmpl = array('type' => 'link', 'src' => $attributes['src'],);
		return $tmpl;
	}

	function _closeLink($tmpl){
		if(!empty($this->_tmplStack)){
			$this->_addToParentTag('dependencies', strtolower($tmpl['src']));
			$this->_characterData(sprintf("%sTMPL:%s%s", $this->_startTag, strtoupper($tmpl['src']), $this->_endTag));
		}
		return true;
	}

	function _initTemplate($attributes){
		if(!isset($attributes['name'])){
			$name = $this->_buildTemplateName();
		} else{
			$name = strtolower($attributes['name']);
			unset($attributes['name']);
		}
		if(isset($this->_templates[$name]) || $this->_tmpl->exists($name)){
			patErrorManager::raiseNotice(PATTEMPLATE_READER_NOTICE_TEMPLATE_EXISTS, $this->_createErrorMessage("Template $name already exists"), $name);
		}
		array_push($this->_path, $name);
		if(isset($attributes['maxloop'])){
			if(!isset($attributes['parent'])){
				$attributes['parent'] = $this->_getFromParentTemplate('name');
			}
		}
		$attributes = $this->_prepareTmplAttributes($attributes, $name);
		array_push($this->_inheritAtts, array('whitespace' => $attributes['whitespace'], 'unusedvars' => $attributes['unusedvars'], 'autoclear' => $attributes['autoclear']));
		$tmpl = array('type' => 'tmpl', 'name' => $name, 'attributes' => $attributes, 'content' => '', 'dependencies' => array(), 'varspecs' => array(), 'comments' => array(), 'loaded' => false, 'parsed' => false, 'input' => $this->_name . '://' . $this->_currentInput);
		if($this->_root == null){
			$this->_root = $name;
			$tmpl['isRoot'] = true;
		}
		switch($attributes['type']){
			case 'condition':
			case 'modulo':
				$tmpl['subtemplates'] = array();
				break;
		}
		return $tmpl;
	}

	function _prepareTmplAttributes($attributes, $templatename){
		if(isset($attributes['__prepared']) && $attributes['__prepared'] === true){
			return $attributes;
		}
		$attributes = $this->_inheritAttributes($attributes);
		$attributes = array_merge($this->_tmpl->getDefaultAttributes(), $attributes);
		$attributes['type'] = strtolower($attributes['type']);
		if(!isset($attributes['rowoffset'])){
			$attributes['rowoffset'] = 1;
		}
		if(!isset($attributes['addsystemvars'])){
			$attributes['addsystemvars'] = false;
		} else{
			switch($attributes['addsystemvars']){
				case 'on':
				case 'boolean':
					$attributes['addsystemvars'] = 'boolean';
					break;
				case 'int':
				case 'integer':
					$attributes['addsystemvars'] = 'integer';
					break;
				case 'off':
					$attributes['addsystemvars'] = false;
					break;
			}
		}
		if(isset($attributes['src'])){
			if(!isset($attributes['parse']))
				$attributes['parse'] = 'on';
			if(!isset($attributes['reader']))
				$attributes['reader'] = $this->getName();
			if(!isset($attributes['autoload']))
				$attributes['autoload'] = $this->_defaultAtts['autoload'];
			if(isset($attributes['relative']) && strtolower($attributes['relative'] === 'yes')){
				$attributes['relative'] = $this->getCurrentInput();
			} else{
				$attributes['relative'] = false;
			}
		}
		if(isset($attributes['varscope'])){
			if($attributes['varscope'] === '__parent'){
				$attributes['varscope'] = $this->_getFromParentTemplate('name');
			}
			$attributes['varscope'] = strtolower($attributes['varscope']);
			if(strstr($attributes['varscope'], ',')){
				$attributes['varscope'] = array_map('trim', explode(',', $attributes['varscope']));
			}
		}
		switch($attributes['type']){
			case 'condition':
				if(!isset($attributes['conditionvar'])){
					return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_TAG, $this->_createErrorMessage("Attribute 'conditionvar' missing for $templatename"));
				}
				$attributes['conditionvar'] = strtoupper($attributes['conditionvar']);
				if(strstr($attributes['conditionvar'], '.')){
					list($attributes['conditiontmpl'], $attributes['conditionvar']) = explode('.', $attributes['conditionvar']);
					$attributes['conditiontmpl'] = strtolower($attributes['conditiontmpl']);
				}
				$attributes['autoclear'] = 'yes';
				if(!isset($attributes['useglobals'])){
					$attributes['useglobals'] = 'no';
				}
				break;
			case 'simplecondition':
				if(!isset($attributes['requiredvars'])){
					return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_TAG, $this->_createErrorMessage("Attribute 'requiredvars' missing for $templatename"));
				}
				$tmp = array_map('trim', explode(',', $attributes['requiredvars']));
				$attributes['requiredvars'] = array();
				foreach($tmp as $var){
					$pos = strpos($var, '=');
					if($pos !== false){
						$val = trim(substr($var, $pos + 1));
						$var = trim(substr($var, 0, $pos));
					} else{
						$val = null;
					}
					$var = strtoupper($var);
					$pos = strpos($var, '.');
					if($pos === false){
						array_push($attributes['requiredvars'], array($templatename, $var, $val));
					} else{
						array_push($attributes['requiredvars'], array(strtolower(substr($var, 0, $pos)), substr($var, $pos + 1), $val));
					}
				}
				$attributes['autoclear'] = 'yes';
				break;
			case 'oddeven':
				$attributes['type'] = 'modulo';
				$attributes['modulo'] = 2;
				$attributes['autoclear'] = 'yes';
				break;
			case 'modulo':
				if(!isset($attributes['modulo'])){
					return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_TAG, $this->_createErrorMessage("Attribute 'modulo' missing for $templatename"));
				}
				$attributes['autoclear'] = 'yes';
				break;
			case 'standard':
				break;
			default:
				return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_TAG, $this->_createErrorMessage("Unknown value for attribute type: {$attributes['type']}"));
				break;
		}
		$attributes['__prepared'] = true;
		return $attributes;
	}

	function _buildTemplateName(){
		return strtolower(uniqid('tmpl'));
	}

	function _closeTemplate($tmpl, $data){
		$name = array_pop($this->_path);
		$data = $this->_adjustWhitespace($data, $tmpl['attributes']['whitespace']);
		array_pop($this->_inheritAtts);
		switch($tmpl['attributes']['type']){
			case 'condition':
			case 'modulo':
				if(trim($data) != ''){
					patErrorManager::raiseNotice(PATTEMPLATE_READER_NOTICE_INVALID_CDATA_SECTION, $this->_createErrorMessage(sprintf('No cdata is allowed inside a template of type %s (cdata was found in %s)', $tmpl['attributes']['type'], $tmpl['name'])));
				}
				$data = null;
				break;
		}
		$tmpl['content'] = $data;
		if(!isset($tmpl['attributes']['src'])){
			$tmpl['loaded'] = true;
		}
		if(!empty($this->_tmplStack)){
			$this->_addToParentTag('dependencies', $name);
			if(isset($tmpl['attributes']['placeholder'])){

				if($this->shouldMaintainBc() && $tmpl['attributes']['placeholder'] === 'none'){
					$tmpl['attributes']['placeholder'] = '__none';
				}
				if($tmpl['attributes']['placeholder'] !== '__none'){
					$this->_characterData($this->_startTag . (strtoupper($tmpl['attributes']['placeholder'])) . $this->_endTag);
				}
			} else{
				$this->_characterData(sprintf("%sTMPL:%s%s", $this->_startTag, strtoupper($name), $this->_endTag));
			}
		}
		unset($tmpl['name']);
		unset($tmpl['tag']);
		$this->_templates[$name] = $tmpl;
		return true;
	}

	function _initSubTemplate($attributes){
		if(!$this->_parentTagIs('tmpl')){
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_TAG, $this->_createErrorMessage('A subtemplate is only allowed in a TMPL tag'));
		}
		if(!isset($attributes['condition'])){
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_NO_CONDITION_SPECIFIED, $this->_createErrorMessage('Missing \'condition\' attribute for subtemplate'));
		}
		$matches = array();
		$regexp = '/^' . $this->_startTag . '([^a-z]+[^\\\])' . $this->_endTag . '$/U';
		if(preg_match($regexp, $attributes['condition'], $matches)){
			$attributes['var'] = $matches[1];
		}
		if($this->shouldMaintainBc() && in_array($attributes['condition'], array('default', 'empty', 'odd', 'even'))){
			$attributes['condition'] = '__' . $attributes['condition'];
		}
		if($attributes['condition'] == '__odd'){
			$attributes['condition'] = 1;
		} elseif($attributes['condition'] == '__even'){
			$attributes['condition'] = 0;
		}
		$parent = array_pop($this->_tmplStack);
		array_push($this->_tmplStack, $parent);
		if($parent['attributes']['type'] == 'modulo'){
			if(preg_match('/^\d$/', $attributes['condition'])){
				if((integer)$attributes['condition'] >= $parent['attributes']['modulo']){
					return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_INVALID_CONDITION, $this->_createErrorMessage('Condition may only be between 0 and ' . ($parent['attributes']['modulo'] - 1)));
				}
			}
		}
		$attributes = $this->_inheritAttributes($attributes);
		$condition = $attributes['condition'];
		unset($attributes['condition']);
		$subTmpl = array('type' => 'sub', 'condition' => $condition, 'data' => '', 'attributes' => $attributes, 'comments' => array(), 'dependencies' => array());
		return $subTmpl;
	}

	function _closeSubTemplate($subTmpl, $data){
		$data = $this->_adjustWhitespace($data, $subTmpl['attributes']['whitespace']);
		$subTmpl['data'] = $data;
		$condition = $subTmpl['condition'];
		unset($subTmpl['condition']);
		$this->_addToParentTemplate('subtemplates', $subTmpl, $condition);
		return true;
	}

	function _handleVariable($attributes, $data){
		if(!isset($attributes['name'])){
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_NO_NAME_SPECIFIED, $this->_createErrorMessage('Variable needs a name attribute'));
		}
		$specs = array();
		$name = strtoupper($attributes['name']);
		unset($attributes['name']);
		$specs['name'] = $name;
		if(isset($attributes['default'])){
			$data = $attributes['default'];
			$specs['default'] = $data;
			unset($attributes['default']);
		} elseif(!empty($data)){
			$specs['default'] = $data;
		}
		if(!isset($attributes['hidden']) || $attributes['hidden'] == 'no'){
			$this->_characterData($this->_startTag . strtoupper($name) . $this->_endTag);
		}
		if(isset($attributes['hidden'])){
			unset($attributes['hidden']);
		}
		if(isset($attributes['copyfrom'])){
			$specs['copyfrom'] = strtoupper($attributes['copyfrom']);
			if(strstr($specs['copyfrom'], '.')){
				$specs['copyfrom'] = explode('.', $specs['copyfrom']);
				$specs['copyfrom'][0] = strtolower($specs['copyfrom'][0]);
			}
			unset($attributes['copyfrom']);
		}
		if(isset($attributes['modifier'])){
			$modifier = $attributes['modifier'];
			unset($attributes['modifier']);
			$type = isset($attributes['modifiertype']) ? $attributes['modifiertype'] : 'auto';
			if(isset($attributes['modifiertype']))
				unset($attributes['modifiertype']);
			$specs['modifier'] = array('mod' => $modifier, 'type' => $type, 'params' => $attributes);
		}
		if(!empty($specs)){
			$this->_addToParentTemplate('varspecs', $specs, $name);
		}
		return true;
	}

	function _handleComment($attributes, $data){
		$this->_addToParentTag('comments', $data);
	}

	function _getCData(){
		if($this->_depth == 0){
			return '';
		}
		return $this->_data[$this->_depth];
	}

	function _addToParentTemplate($property, $value, $key = null){
		$cnt = count($this->_tmplStack);
		if($cnt === 0){
			return false;
		}
		$pos = $cnt - 1;
		while($pos >= 0){
			if($this->_tmplStack[$pos]['type'] != 'tmpl'){
				$pos--;
				continue;
			}
			if($key === null){
				if(!in_array($value, $this->_tmplStack[$pos][$property])){
					array_push($this->_tmplStack[$pos][$property], $value);
				}
			} else{
				$this->_tmplStack[$pos][$property][$key] = $value;
			}
			return true;
		}
		return false;
	}

	function _getFromParentTemplate($property){
		$cnt = count($this->_tmplStack);
		if($cnt === 0){
			return false;
		}
		$pos = $cnt - 1;
		while($pos >= 0){
			if($this->_tmplStack[$pos]['type'] != 'tmpl'){
				$pos--;
				continue;
			}
			if(isset($this->_tmplStack[$pos][$property])){
				return $this->_tmplStack[$pos][$property];
			}
			return false;
		}
		return false;
	}

	function _addToParentTag($property, $value, $key = null){
		$cnt = count($this->_tmplStack);
		if($cnt === 0){
			return false;
		}
		$pos = $cnt - 1;
		if($key === null){
			if(!in_array($value, $this->_tmplStack[$pos][$property])){
				array_push($this->_tmplStack[$pos][$property], $value);
			}
		} else{
			$this->_tmplStack[$pos][$property][$key] = $value;
		}
		return true;
	}

	function _adjustWhitespace($data, $behaviour){
		switch($behaviour){
			case 'trim':
				$data = str_replace('\n', ' ', $data);
				$data = preg_replace('/\s\s+/', ' ', $data);
				$data = trim($data);
				break;
		}
		return $data;
	}

	function _inheritAttributes($attributes){
		if(!empty($this->_inheritAtts)){
			$parent = end($this->_inheritAtts);
		} else{
			$parent = array('whitespace' => $this->_defaultAtts['whitespace'], 'unusedvars' => $this->_defaultAtts['unusedvars'], 'autoclear' => $this->_defaultAtts['autoclear']);
		}
		$attributes = array_merge($parent, $attributes);
		return $attributes;
	}

	function _parentTagIs($type){
		$parent = array_pop($this->_tmplStack);
		if($parent === null){
			return false;
		}
		array_push($this->_tmplStack, $parent);
		if($parent['type'] == $type){
			return true;
		}
		return false;
	}

	function _getCurrentLine(){
		$line = count(explode("\n", $this->_processedData));
		return $line;
	}

	function _createErrorMessage($msg){
		return sprintf('%s in %s on line %d', $msg, $this->getCurrentInput(), $this->_getCurrentLine());
	}

	function getCurrentInput(){
		return $this->_currentInput;
	}

	function shouldMaintainBc(){
		if(!isset($this->_options['maintainBc'])){
			return false;
		}
		return $this->_options['maintainBc'];
	}

	function isInUse(){
		return $this->_inUse;
	}

	function getTemplateRoot(){
		if(!isset($this->_options['root'])){
			return null;
		}
		if(isset($this->_options['root'][$this->_name])){
			return $this->_options['root'][$this->_name];
		}
		if(isset($this->_options['root']['__default'])){
			return $this->_options['root']['__default'];
		}
		return null;
	}
}

?>
