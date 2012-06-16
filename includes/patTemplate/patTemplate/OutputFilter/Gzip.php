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
class patTemplate_OutputFilter_Gzip extends patTemplate_OutputFilter{
	var $_name = 'Gzip';

	function apply($data){
		if(!$this->_clientSupportsGzip()){
			return $data;
		}
		$size = strlen($data);
		$crc = crc32($data);
		$data = gzcompress($data, 9);
		$data = substr($data, 0, strlen($data) - 4);
		$data .= $this->_gfc($crc);
		$data .= $this->_gfc($size);
		header('Content-Encoding: gzip');
		$data = "\x1f\x8b\x08\x00\x00\x00\x00\x00" . $data;
		return $data;
	}

	function _clientSupportsGzip(){
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING'])){
			return false;
		}
		if(false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
			return true;
		}
		return false;
	}

	function _gfc($value){
		$str = '';
		for($i = 0; $i < 4; $i++){
			$str .= chr($value % 256);
			$value = floor($value / 256);
		}
		return $str;
	}
}

?>
