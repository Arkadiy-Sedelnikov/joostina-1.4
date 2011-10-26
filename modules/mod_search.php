<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

$moduleclass_sfx= $params->get( 'moduleclass_sfx' );
$button_vis		= $params->get( 'button', 1 );
$button_pos		= $params->get( 'button_pos', 'right' );
$button_text	= $params->get( 'button_text', _SEARCH );
$width			= intval( $params->get( 'width', 20 ) );
$text			= $params->get( 'text', _SEARCH_BOX );
$text_pos		= $params->get( 'text_pos', 'inside' );
$set_Itemid		= intval( $params->get( 'set_itemid', 0 ) );

$params->set('template',$params->get('template','default.php'));

switch ($text_pos) {
	case 'iside':
	default:
		$output = '<input name="searchword" id="mod_search_searchword" maxlength="100" alt="search" class="inputbox'. $moduleclass_sfx .'" type="text" size="'. $width .'" value="'. $text .'"  onblur="if(this.value==\'\') this.value=\''. $text .'\';" onfocus="if(this.value==\''. $text .'\') this.value=\'\';" />';
		break;

	case 'left':
		$output = '<strong>'.$text.'</strong>&nbsp;<input name="searchword" id="mod_search_searchword" maxlength="100" alt="search" class="inputbox'. $moduleclass_sfx .'" type="text" size="'. $width .'" value=""  />';
		break;

	case 'top':
		$output = '<strong>'.$text.'</strong><br /><input name="searchword" id="mod_search_searchword" maxlength="100" alt="search" class="inputbox'. $moduleclass_sfx .'" type="text" size="'. $width .'" value=""  />';
		break;

	case 'hidden':
		$output = '<input name="searchword" id="mod_search_searchword" maxlength="100" alt="search" class="inputbox'. $moduleclass_sfx .'" type="text" size="'. $width .'" value=""  />';
		break;
}

$button = $button_vis ? '<input type="submit" value="'. $button_text .'" class="button'. $moduleclass_sfx .'"/>' : '';

switch ( $button_pos ) {
	case 'top':
		$button = $button .'<br/>';
		$output = $button . $output;
		break;

	case 'bottom':
		$button =  '<br/>'. $button;
		$output = $output . $button;
		break;

	case 'right':
		$output = $output . $button;
		break;

	case 'left':
	default:
		$output = $button . $output;
		break;
}

// указываем Itemid
if ( $set_Itemid ) {
	$_Itemid	= $set_Itemid;
	$link		= JPATH_SITE . '/index.php?option=com_search&amp;Itemid='. $set_Itemid;
} else {
	$query = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_search' AND published = 1";
	$database->setQuery( $query,0,1 );
	$s_itemid = $database->loadResult();

	// try to auto detect search component Itemid
	if ( $s_itemid ) {
		$_Itemid	= $s_itemid;
		$link		= JPATH_SITE . '/index.php?Itemid='. $_Itemid;
	} else {
		// Assign no Itemid
		$_Itemid	= '';
		$link		= JPATH_SITE . '/index.php';
	}
}

//Подключаем шаблон
if($module->set_template($params)) {
	require($module->template);
}