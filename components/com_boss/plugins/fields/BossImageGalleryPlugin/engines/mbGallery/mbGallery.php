<?php

/**
 * @BOSS Шаблон подключения скрипта галереи плагина BossImageGalleryPlugin.
 * @tpl mbGallery
 * @file mbGallery.php
 * @author: Алексей Поздняков <mosgaz@list.ru>
 * @url: www.dev.woodell.ru
 * @package Joostina
 * @subpackage JoiBOSS CCK
 * @copyright (C) 2011 Woodell Web Works
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();


function galleryAddInHead($path){
	// передаем в функцию addInHead скрипты и стили, 
	// которые не должны кешироваться
	$params = array();

	$params['js']['galleryImg2'] = $path . '/js/jquery.exif.js';
	$params['js']['galleryImg1'] = $path . '/js/mbGallery.js';

	$params['css']['galleryImg1'] = $path . '/css/mbgallery.css';

	return $params;
}


// Отображение в категории
function galleryListDisplay($directory, $content, $images, $conf_fields, $conf){
	$galleryTitle = (empty($conf_fields['galleryTitle']))
		? htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES)
		: $conf_fields['galleryTitle'];

	$pic = "/images/boss/" . $directory . "/contents/gallery/thumb/" . $images[0]['file'];
	$full = "/images/boss/" . $directory . "/contents/gallery/full/" . $images[0]['file'];
	$title = $images[0]['signature'] ? $images[0]['signature'] : $galleryTitle;
	?>

<div class="thumbsContainer">
	<?php
	if(file_exists(JPATH_BASE . $pic)){
		?>
		<img src="<?php
			echo JPATH_SITE . $pic; ?>" alt="<?php
			echo htmlspecialchars(stripslashes(cutLongWord($content->name)),
				ENT_QUOTES); ?>"/>
		<?php
	} else{
		$nopic_dir = "/templates/com_boss/" . $conf->template . "/images/";
		if((BOSS_NOPIC != "") && (file_exists(JPATH_BASE . $nopic_dir . BOSS_NOPIC))){
			?>
			<img src="<?php echo JPATH_SITE . $nopic_dir . BOSS_NOPIC; ?>" alt="nopic"/>
			<?php
		} else{
			?>
			<img src="<?php echo JPATH_SITE . $nopic_dir . nopic . gif; ?>" alt="nopic"/>
			<?php
		}
	}
	?>

	<?php //ссылка на просмотр изображений
	$opts = "
			galleryTitle		: galleryOptions['galleryTitle'],							
			overlayBackground	: galleryOptions['overlayBackground'],
			overlayOpacity		: galleryOptions['overlayOpacity'],					
			
			minWidth 			: 300,									
			minHeight 			: 300,									
			maxWidth 			: galleryOptions['maxWidth'],		
			maxHeight 			: galleryOptions['maxHeight'],			
								
			slideTimer			: galleryOptions['slideTimer'],			
			autoSlide			: galleryOptions['autoSlide'], 		
			
			printOutThumbs		: false,										
			skin				: 'white'
			";

	?>
	<br clear="all"/>

	<a class="galleryView" onclick="$('#gallery_<?php
		echo $content->id; ?>').mbGallery({<?php
		echo $opts; ?>});" title="<?php
		echo $title; ?>">
		<?php echo BOSS_PLG_GALLERY_VIEW; ?>
	</a>

	<div id="gallery_<?php echo $content->id; ?>" class="galleryCont" style="display: none;">
		<?php //изображения галереи

		foreach($images as $image){
			$title = $image['signature'] ? $image['signature'] : $galleryTitle;
			?>

			<a class="imgThumb" href="/images/boss/<?php
				echo $directory; ?>/contents/gallery/thumb/<?php
				echo $image['file']; ?>" title="<?php
				echo $title; ?>"></a>

			<a class="imgFull" href="/images/boss/<?php
				echo $directory; ?>/contents/gallery/full/<?php
				echo $image['file']; ?>" title="<?php
				echo $title; ?>"></a>

			<div class="imgDesc">
				<?php echo Jstring::trim($image['signature']) ? $image['signature'] : '&nbsp;'; ?>
			</div>

			<?php
		}
		?>
	</div>
	<br clear="all"/>
</div>

<?php
}

// Отображение в содержимом
function galleryDetailsDisplay($directory, $content, $images, $conf_fields, $conf){
	$galleryTitle = (empty($conf_fields['galleryTitle']))
		? htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES)
		: $conf_fields['galleryTitle'];
	?>

<div id="gallery_<?php echo $content->id; ?>" class="galleryCont" style="display: none;">
	<?php
	//контейнер с изображениями галереи
	foreach($images as $image){
		$title = $image['signature'] ? $image['signature'] : $galleryTitle;
		?>

		<a class="imgThumb" href="/images/boss/<?php
			echo $directory; ?>/contents/gallery/thumb/<?php
			echo $image['file']; ?>" title="<?php
			echo $title; ?>"></a>

		<a class="imgFull" href="/images/boss/<?php
			echo $directory; ?>/contents/gallery/full/<?php
			echo $image['file']; ?>" title="<?php
			echo $title; ?>"></a>

		<div class="imgDesc">
			<?php echo Jstring::trim($image['signature']) ? $image['signature'] : '&nbsp;'; ?>
		</div>

		<?php
	}
	?>
</div>


<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery(".galleryCont").mbGallery(
			{
				'galleryTitle':galleryOptions['galleryTitle'], // заголовок галереи											//
				'overlayBackground':galleryOptions['overlayBackground'], // цвет слоя подложки
				'overlayOpacity':galleryOptions['overlayOpacity'], // прозрачность слоя подложки

				'minWidth':300, // минимальная ширина отображения
				'minHeight':300, // минимальная высота отображения
				'maxWidth':galleryOptions['maxWidth'], // максимальная ширина (0 - без ограничений)
				'maxHeight':galleryOptions['maxHeight'], // максимальная высота (0 - без ограничений)

				'slideTimer':galleryOptions['slideTimer'], // пауза между сменой изображений
				'autoSlide':galleryOptions['autoSlide'], // автозапуск слайд-шоу

				'printOutThumbs':true, // отображать миниатюры
				'skin':'white'                                // палитра оформления 'white' или 'black'
			}
		);
	});
</script>
<?php
}

?>