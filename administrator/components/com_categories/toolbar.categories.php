<?php
/**
 * @package Joostina
 * @copyright ��������� ����� (C) 2008-2010 Joostina team. ��� ����� ��������.
 * @license �������� http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, ��� help/license.php
 * Joostina! - ��������� ����������� ����������� ���������������� �� �������� �������� GNU/GPL
 * ��� ��������� ���������� � ������������ ����������� � ��������� �� ��������� �����, �������� ���� help/copyright.php.
 */

// ������ ������� �������
defined('_VALID_MOS') or die();

require_once ($mainframe->getPath('toolbar_html'));

switch($task) {
	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_categories::_EDIT();
		break;

	case 'moveselect':
		TOOLBAR_categories::_MOVE();
		break;

	case 'copyselect':
		TOOLBAR_categories::_COPY();
		break;

	default:
		TOOLBAR_categories::_DEFAULT();
		break;
}