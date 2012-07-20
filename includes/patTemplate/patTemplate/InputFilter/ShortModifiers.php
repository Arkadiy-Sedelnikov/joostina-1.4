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
class patTemplate_InputFilter_ShortModifiers extends patTemplate_InputFilter{
	var $_name = 'ShortModifiers';
	var $_params = array('copyVars' => true);
	var $_ns = null;
	var $_tmpl = null;

	function setTemplateReference(&$tmpl){
		$this->_tmpl = &$tmpl;
	}

	function _generateReplace($matches){
		if($this->getParam('copyVars') === true){
			$newName = $matches[2] . '_' . $matches[3];
			$replace = $matches[1] . '<' . $this->_ns . ':var copyFrom="' . $matches[2] . '" name="' . $newName . '" modifier="' . $matches[3] . '"';
		} else{
			$replace = $matches[1] . '<' . $this->_ns . ':var name="' . $matches[2] . '" modifier="' . $matches[3] . '"';
		}
		for($i = 4; $i < count($matches) - 1; $i++){
			$replace .= ' ' . $matches[++$i] . '="' . $matches[++$i] . '"';
		}
		$replace .= '/>';
		return $replace;
	}

	function apply($data){
		$startTag = $this->_tmpl->getStartTag();
		$endTag = $this->_tmpl->getEndTag();
		$this->_ns = $this->_tmpl->getNamespace();
		if(is_array($this->_ns)){
			$this->_ns = array_shift($this->_ns);
		}
		$regex = chr(1) . "([^\\\])" . $startTag . "([^a-z]+)\|(.+[^\\\])(\|(.+):(.+[^\\\]))*" . $endTag . chr(1) . "U";
		$data = preg_replace_callback($regex, array($this, '_generateReplace'), $data);
		return $data;
	}
}

?>
