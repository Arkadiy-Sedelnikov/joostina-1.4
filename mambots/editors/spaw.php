<?php
/**
 * @version $Id: Spaw.php,v 1.8 2004/09/27 10:52:56 stingrey Exp $
 * @package Mambo_4.5.1
 * @copyright (C) 2000 - 2004 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

/** ensure this file is being included by a parent file */
defined('_JLINDEX') or die('Direct Access to this location is not allowed.');

$_MAMBOTS->registerFunction('onInitEditor', 'botSpawEditorInit');
$_MAMBOTS->registerFunction('onGetEditorContents', 'botSpawEditorGetContents');
$_MAMBOTS->registerFunction('onEditorArea', 'botSpawEditorArea');

/**
 * Spaw WYSIWYG Editor - javascript initialisation
 */
function botSpawEditorInit(){
}

/**
 * Spaw WYSIWYG Editor - copy editor contents to form field
 * @param string The name of the editor area
 * @param string The name of the form field
 */
function botSpawEditorGetContents($editorArea, $hiddenField){
}

/**
 * Spaw WYSIWYG Editor - display the editor
 * @param string The name of the editor area
 * @param string The content of the field
 * @param string The name of the form field
 * @param string The width of the editor area
 * @param string The height of the editor area
 * @param int The number of columns for the editor area
 * @param int The number of rows for the editor area
 */
function botSpawEditorArea($name, $content, $hiddenField, $width, $height, $col, $row){
	require_once(dirname(__FILE__) . "/spaw/spaw.inc.php");
	$sw = new SpawEditor($hiddenField, html_entity_decode($content, ENT_QUOTES));
	$sw->show();
}

?>
