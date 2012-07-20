<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 * Backend uninstall handler
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage    Installer
 * @filename uninstall.joieditor.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 **/

defined('_JLINDEX') or die();


function com_uninstall(){
	$database = &database::getInstance();

	@unlink(JPATH_BASE . '/mambots/editors/elrte.xml');
	@unlink(JPATH_BASE . '/mambots/editors/elrte.php');
	rmdir_rf(JPATH_BASE . '/mambots/editors/elrte');

	$query = "DELETE FROM #__mambots WHERE folder = 'editors' AND element = 'elrte'";
	$database->setQuery($query);
	$database->query();
}

function rmdir_rf($dirName){

	if($handle = opendir($dirName)){

		while(false !== ($file = readdir($handle))){
			if($file != '.' && $file != '..'){
				if(is_dir($dirName . '/' . $file)){
					rmdir_rf($dirName . '/' . $file);
					@rmdir($dirName . '/' . $file);
				} elseif(is_file($dirName . '/' . $file)){
					@unlink($dirName . '/' . $file);
				}
			}
		}

		closedir($handle);
	}

	@rmdir($dirName);
}