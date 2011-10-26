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

if (!defined( '_JOS_RSSFEED_MODULE' )) {
	/** ensure that functions are declared only once*/
	define( '_JOS_RSSFEED_MODULE', 1 );

	function output_rssfeed( $link, $img_default, $img_file, $img_alt, $img_name  ) {
		$img = mosAdminMenus::ImageCheck( $img_default, '/images/M_images/', $img_file, '/images/M_images/', $img_alt, $img_name );
		?><a class="<?php echo $img_name;?>" href="<?php echo sefRelToAbs( $link ); ?>" title="<?php echo $img_alt;?>"><?php echo $img ?></a><?php
	}
}

$text				= $params->get( 'text','');
$moduleclass_sfx	= $params->get( 'moduleclass_sfx', '' );
$rss091				= $params->get( 'rss091', 1 );
$rss10				= $params->get( 'rss10', 1 );
$rss20				= $params->get( 'rss20', 1 );
$atom03				= $params->get( 'atom', 1 );
$opml				= $params->get( 'opml', 1 );
$yandex 			= $params->get( 'yandex', 1 );
$rss091_image		= $params->get( 'rss091_image', '' );
$rss10_image		= $params->get( 'rss10_image', '' );
$rss20_image		= $params->get( 'rss20_image', '' );
$atom_image			= $params->get( 'atom_image', '' );
$opml_image			= $params->get( 'opml_image', '' );
$yandex_image		= $params->get( 'yandex_image', '' );
$t_path				= JPATH_SITE .'/templates/'. JTEMPLATE .'/images/';
$d_path				= JPATH_SITE .'/images/M_images/';

// needed to reduce query
if ( isset( $GLOBALS['syndicateParams'] ) ) {
// load value stored in GLOBALS
	$syndicateParams = $GLOBALS['syndicateParams'];
} else {
// query to oull syndication component params
	$query = "SELECT a.*"
			. "\n FROM #__components AS a"
			. "\n WHERE ( a.admin_menu_link = 'option=com_syndicate' OR a.admin_menu_link = 'option=com_syndicate&hidemainmenu=1' )"
			. "\n AND a.option = 'com_syndicate'";
	$database->setQuery( $query );
	$database->loadObject( $row );

	// get params definitions
	$syndicateParams = new mosParameters( $row->params, $mainframe->getPath( 'com_xml', $row->option ), 'component' );
}

// check for disabling/enabling of selected feed types
if ( !$syndicateParams->get( 'rss091', 1 ) ) {
	$rss091 = 0;
}
if ( !$syndicateParams->get( 'rss10', 1 ) ) {
	$rss10 = 0;
}
if ( !$syndicateParams->get( 'rss20', 1 ) ) {
	$rss20 = 0;
}
if ( !$syndicateParams->get( 'atom03', 1 ) ) {
	$atom03 = 0;
}
if ( !$syndicateParams->get( 'opml', 1 ) ) {
	$opml = 0;
}
if ( !$syndicateParams->get( 'yandex', 1 ) ) {
	$yandex = 0;
}
?>
<div class="syndicate<?php echo $moduleclass_sfx;?>">
	<?php
	// текст
	if ( $text ) {
		?><div align="center" class="syndicate_text<?php echo $moduleclass_sfx;?>"><?php echo $text;?></div><?php
	}
	// ссылка стандарта Yandex
	if ( $yandex ) {
		$link = 'index.php?option=com_rss&amp;feed=Yandex&amp;no_html=1';
		output_rssfeed( $link, 'yandex_rss.png', $yandex_image, 'Yandex RSS', 'Yandex' );
	}
	// ссылка стандарта rss091
	if ( $rss091 ) {
		$link = 'index.php?option=com_rss&amp;feed=RSS0.91&amp;no_html=1';
		output_rssfeed( $link, 'rss091.gif', $rss091_image, 'RSS 0.91', 'RSS091' );
	}
	// ссылка стандарта rss10
	if ( $rss10 ) {
		$link = 'index.php?option=com_rss&amp;feed=RSS1.0&amp;no_html=1';
		output_rssfeed( $link, 'rss10.gif', $rss10_image, 'RSS 1.0', 'RSS10' );
	}
	// ссылка стандарта rss20
	if ( $rss20 ) {
		$link = 'index.php?option=com_rss&amp;feed=RSS2.0&amp;no_html=1';
		output_rssfeed( $link, 'rss20.gif', $rss20_image, 'RSS 2.0', 'RSS20' );
	}
	// ссылка стандарта atom
	if ( $atom03 ) {
		$link = 'index.php?option=com_rss&amp;feed=ATOM0.3&amp;no_html=1';
		output_rssfeed( $link, 'atom03.gif', $atom_image, 'ATOM 0.3', 'ATOM03' );
	}
	// ссылка opml
	if ( $opml ) {
		$link = 'index.php?option=com_rss&amp;feed=OPML&amp;no_html=1';
		output_rssfeed( $link, 'opml.png', $opml_image, 'OPML', 'OPML'  );
	}
	?>
</div>