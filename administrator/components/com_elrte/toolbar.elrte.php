<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 * Backend toolbar handler
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage    Admin
 * @filename toolbar.joieditor.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 **/

defined('_JLINDEX') or die();

switch($task){

	case 'config_elrte':
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::save('save_config_elrte');
		mosMenuBar::endTable();
		break;

	case 'config_elfinder':
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::save('save_config_elfinder');
		mosMenuBar::endTable();
		break;

	case 'info':
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::endTable();
		break;

	default:
		break;
}


