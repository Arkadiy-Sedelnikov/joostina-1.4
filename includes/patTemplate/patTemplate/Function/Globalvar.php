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
class patTemplate_Function_Globalvar extends patTemplate_Function{
	var $_name = 'Globalvar';
	var $_tmpl;

	function setTemplateReference(&$tmpl){
		$this->_tmpl = &$tmpl;
	}

	function call($params, $content){
		if(isset($params['default'])){
			$this->_tmpl->addGlobalVar($params['name'], $params['default']);
		}
		if(!isset($params['hidden'])){
			$params['hidden'] = 'no';
		}
		if($params['hidden'] != 'yes')
			return $this->_tmpl->getOption('startTag') . strtoupper($params['name']) . $this->_tmpl->getOption('endTag');
		return '';
	}
}

?>
