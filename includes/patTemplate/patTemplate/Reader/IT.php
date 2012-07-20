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
class patTemplate_Reader_IT extends patTemplate_Reader{
	var $_name = 'IT';
	var $_files = array();

	function parseString($string){
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
		if(!isset($this->_defaultAtts['autoload']))
			$this->_defaultAtts['autoload'] = 'on';
		$attributes = $this->_rootAtts;
		$attributes['name'] = '__global';
		$rootTemplate = $this->_initTemplate($attributes);
		array_push($this->_tmplStack, $rootTemplate);
		$patNamespace = strtolower($this->_tmpl->getNamespace());
		$regexp = '/(<!-- (BEGIN|END) ([a-zA-Z]+) -->)/m';
		$tokens = preg_split($regexp, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		if($tokens[0] != '')
			$this->_characterData($tokens[0]);
		$cnt = count($tokens);
		$i = 1;

		while($i < $cnt){
			$fullTag = $tokens[$i++];
			$closing = strtoupper($tokens[$i++]) == 'END' ? true : false;
			$tmplName = $tokens[$i++];
			$namespace = $patNamespace;
			$tagname = 'tmpl';
			$data = $tokens[$i++];
			if($closing === true){
				$result = $this->_endElement($namespace, $tagname);
				if(patErrorManager::isError($result)){
					return $result;
				}
				$this->_characterData($data);
				continue;
			}
			$attributes = array('name' => $tmplName);
			$result = $this->_startElement($namespace, $tagname, $attributes);
			if(patErrorManager::isError($result)){
				return $result;
			}
			$this->_characterData($data);
		}
		$rootTemplate = array_pop($this->_tmplStack);
		$this->_closeTemplate($rootTemplate, $this->_data[0]);
		if($this->_depth > 0){
			$el = array_pop($this->_elStack);
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_NO_CLOSING_TAG, $this->_createErrorMessage("No closing tag for {$el['ns']}:{$el['name']} found"));
		}
		return $this->_templates;
	}

	function readTemplates($input){
		$this->_currentInput = $input;
		$fullPath = $this->_resolveFullPath($input);
		if(patErrorManager::isError($fullPath))
			return $fullPath;
		$content = $this->_getFileContents($fullPath);
		if(patErrorManager::isError($content))
			return $content;
		$templates = $this->parseString($content);
		return $templates;
	}

	function loadTemplate($input){
		$fullPath = $this->_resolveFullPath($input);
		if(patErrorManager::isError($fullPath))
			return $fullPath;
		return $this->_getFileContents($fullPath);
	}

	function _resolveFullPath($filename){
		$baseDir = $this->getTemplateRoot();
		$fullPath = $baseDir . '/' . $filename;
		return $fullPath;
	}

	function _getFileContents($file){
		if(!file_exists($file) || !is_readable($file)){
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_NO_INPUT, "Could not load templates from $file.");
		}
		if(function_exists('file_get_contents'))
			$content = @file_get_contents($file);
		else
			$content = implode('', file($file));
		array_push($this->_files, $file);
		return $content;
	}
}

?>
