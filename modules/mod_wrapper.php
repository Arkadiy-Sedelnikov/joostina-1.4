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

global $mod_wrapper_count;

$params->def( 'url', '' );
$params->def( 'scrolling', 'auto' );
$params->def( 'height', '200' );
$params->def( 'height_auto', '0' );
$params->def( 'width', '100%' );
$params->def( 'add', '1' );

$url = $params->get( 'url' );
if ( $params->get( 'add' ) ) {
	// Добавить 'http://' , если отсутствует
	if ( substr( $url, 0, 1 ) == '/' ) {
		// relative url in component. use server http_host.
		$url = 'http://'. $_SERVER['HTTP_HOST'] . $url;
	} elseif ( !strstr( $url, 'http' ) && !strstr( $url, 'https' ) ) {
		$url = 'http://'. $url;
	}
}

// Create a unique ID for the IFrame, output the javascript function only once
if (!isset( $mod_wrapper_count )) {
	$mod_wrapper_count = 0;
	?>
<script language="javascript" type="text/javascript">
	function iFrameHeightX( iFrameId ) {
		var h = 0;
		if ( !document.all ) {
			h = document.getElementById(iFrameId).contentDocument.height;
			document.getElementById(iFrameId).style.height = h + 60 + 'px';
		} else if( document.all ) {
			h = document.frames(iFrameId).document.body.scrollHeight;
			document.all[iFrameId].style.height = h + 20 + 'px';
		}
	}
</script>
	<?php
}

// автоматический контроль высоты
if ( $params->def( 'height_auto' ) ) {
	$load = 'onload="iFrameHeightX(\'blockrandom' . $mod_wrapper_count . '\')" ';
} else {
	$load = '';
}
?>
<iframe
<?php echo $load; ?>
	id="blockrandom<?php echo $mod_wrapper_count++; ?>"
	src="<?php echo $url; ?>"
	width="<?php echo $params->get( 'width' ); ?>"
	height="<?php echo $params->get( 'height' ); ?>"
	scrolling="<?php echo $params->get( 'scrolling' ); ?>"
	align="top"
	frameborder="0"
	class="wrapper<?php echo $params->get( 'moduleclass_sfx' ); ?>">
		<?php echo _IFRAMES; ?>
</iframe>