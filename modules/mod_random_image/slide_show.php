<?php
/**
 * jstslideshow.php. Используется для подключения сконфигурированного js-скрипта
 *
 * @package     Joostina
 * @subpackage  modules
 * @copyright   Copyright (c) 2007-2009 Joostina Team. All rights reserved.
 * @license     GNU/GPL, see help/license.php
 * @version     $Id: jstslideshow.php 2009-01-29 11:05 ZaiSL $;
 * @link        http://www.joostina.ru/API/subpackage/modules/mod_random_image
 * @since       File available since Joostina 1.2.0
 *
 * Joostina! - свободное программное обеспечение распространяемое
 * по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и
 * замечаний об авторском праве, смотрите файл help/copyright.php.
 */
// запрет прямого доступа
defined('_VALID_MOS') or die();
?>
<script type="text/javascript">
    var site_url = "<?php echo JPATH_SITE . '/';?>";
	var simpleGallery_navpanel={
		panel: {
			height:'<?php echo $panel_height;?>',
			opacity: <?php echo $panel_opacity;?>,
			paddingTop:'<?php echo $panel_padding;?>',
			fontStyle:'<?php echo $panel_font;?>'
		},
		images: [
			site_url +'modules/mod_random_image/images/left.gif',
			site_url +'modules/mod_random_image/images/play.gif',
			site_url +'modules/mod_random_image/images/right.gif',
			site_url +'modules/mod_random_image/images/pause.gif'
		],
		imageSpacing: {
			offsetTop:[-4, 0, -4],
			spacing:10
		},
		slideduration: 500
    }

    var gallery_<?php echo $slideshow_name;?>=new simpleGallery({
        wrapperid: "<?php echo $slideshow_name;?>", //ID of main gallery container,
		dimensions: [<?php echo $width;?>, <?php echo $height;?>], //width/height
		imagearray: [<?php echo $pics_str;?> ],
		autoplay: <?php echo $s_autoplay;?>,
        persist: false,
		pause: <?php echo $s_pause;?>, //pause between slides (milliseconds)
		fadeduration: <?php echo $s_fadeduration;?> //transition duration (milliseconds)
	})
</script>