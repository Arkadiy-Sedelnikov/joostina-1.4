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
class patTemplate_OutputFilter_PdfLatex extends patTemplate_OutputFilter{
	var $_name = 'PdfLatex';
	var $_params = array('cacheFolder' => './');

	function apply($data){
		$cacheFolder = $this->getParam('cacheFolder');
		$texFile = tempnam($cacheFolder, 'pt_tex_');
		$fp = fopen($texFile, 'w');
		fwrite($fp, $data);
		fclose($fp);
		$command = 'pdflatex ' . $texFile;
		exec($command);
		exec($command);
		$pdf = $texFile . '.pdf';
		$pdf = file_get_contents($pdf);
		return $pdf;
	}
}

?>
