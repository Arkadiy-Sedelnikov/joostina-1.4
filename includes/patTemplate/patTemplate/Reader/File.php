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
defined('_VALID_MOS') or die();
class patTemplate_Reader_File extends patTemplate_Reader{
	var $_name = 'File';
	var $_isRemote = false;
	var $_files = array();

	function readTemplates($input){
		if(isset($this->_rootAtts['relative'])){
			$relative = $this->_rootAtts['relative'];
		} else{
			$relative = false;
		}
		if($relative === false){
			$this->_currentInput = $input;
		} else{
			$this->_currentInput = dirname($relative) . DIRECTORY_SEPARATOR . $input;
		}
		$fullPath = $this->_resolveFullPath($input, $relative);
		if(patErrorManager::isError($fullPath)){
			return $fullPath;
		}
		$content = $this->_getFileContents($fullPath);
		if(patErrorManager::isError($content)){
			return $content;
		}
		$templates = $this->parseString($content);
		return $templates;
	}

	function loadTemplate($input){
		if(isset($this->_rootAtts['relative'])){
			$relative = $this->_rootAtts['relative'];
		} else{
			$relative = false;
		}
		$fullPath = $this->_resolveFullPath($input, $relative);
		if(patErrorManager::isError($fullPath))
			return $fullPath;
		return $this->_getFileContents($fullPath);
	}

	function _resolveFullPath($filename, $relativeTo = false){
		if(preg_match('/^[a-z]+:\/\//', $filename)){
			$this->_isRemote = true;
			return $filename;
		} else{
			$rootFolders = $this->getTemplateRoot();
			if(!is_array($rootFolders)){
				$rootFolders = array($rootFolders);
			}
			foreach($rootFolders as $root){
				if($relativeTo === false){
					$baseDir = $root;
				} else{
					$baseDir = $root . DIRECTORY_SEPARATOR . dirname($relativeTo);
				}
				$fullPath = $baseDir . DIRECTORY_SEPARATOR . $filename;
				if(file_exists($fullPath)){
					return $fullPath;
				}
			}
		}
		return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_NO_INPUT, "Could not load templates from $filename.");
	}

	function _getFileContents($file){
		if(!$this->_isRemote && (!file_exists($file) || !is_readable($file))){
			return patErrorManager::raiseError(PATTEMPLATE_READER_ERROR_NO_INPUT, "Could not load templates from $file.");
		}
		if(function_exists('file_get_contents')){
			$content = @file_get_contents($file);
		} else{
			$content = implode('', file($file));
		}
		array_push($this->_files, $file);
		return $content;
	}
}

?>
