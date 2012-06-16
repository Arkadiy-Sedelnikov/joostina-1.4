<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

/**
 * @package Joostina
 * @subpackage Wrapper
 */
class HTML_wrapper{
	public static function displayWrap(&$row, &$params){
		?>
	<script language="javascript" type="text/javascript">
		function iFrameHeight() {
			var h = 0;
			if (!document.all) {
				h = document.getElementById('blockrandom').contentDocument.height;
				document.getElementById('blockrandom').style.height = h + 60 + 'px';
			} else if (document.all) {
				h = document.frames('blockrandom').document.body.scrollHeight;
				document.all.blockrandom.style.height = h + 20 + 'px';
			}
		}
	</script>
	<div class="contentpane<?php echo $params->get('pageclass_sfx'); ?>">
		<?php
		if($params->get('page_title')){
			?>
			<div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>"><?php echo $params->get('header'); ?></div>
			<?php
		}
		?>
		<div class="com_wrapper">
			<iframe
				<?php echo $row->load; ?>
				id="blockrandom"
				name="iframe"
				src="<?php echo $row->url; ?>"
				width="<?php echo $params->get('width'); ?>"
				height="<?php echo $params->get('height'); ?>"
				scrolling="<?php echo $params->get('scrolling'); ?>"
				align="top"
				frameborder="0"
				class="wrapper<?php echo $params->get('pageclass_sfx'); ?>">
				<?php echo _IFRAMES; ?>
			</iframe>
		</div>
	</div>
	<?php
		// displays back button
		mosHTML::BackButton($params);
	}
}