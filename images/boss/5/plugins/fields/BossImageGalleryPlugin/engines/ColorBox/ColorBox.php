<?php

/**
 * @BOSS Шаблон подключения скрипта галереи плагина BossImageGalleryPlugin.
 * @tpl ColorBox
 * @file ColorBox.php
 * @author: Алексей Поздняков <mosgaz@list.ru>
 * @url: www.dev.woodell.ru
 * @package Joostina
 * @subpackage JoiBOSS CCK
 * @copyright (C) 2011 Woodell Web Works
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

function galleryAddInHead($path){
	// передаем в функцию addInHead скрипты и стили,
	// которые не должны кешироваться
	$params = array();
	$params['js']['galleryImg1'] = $path . '/js/jquery.colorbox-min.js';
	$params['css']['galleryImg1'] = $path . '/css/style_1/colorbox.css'; // style_1, style_2, style_3, style_4, style_5
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
		<a class="thumb_link" href="<?php echo $full; ?>" title="<?php echo $title; ?>" rel="thumb_set_<?php echo $content->id; ?>">
			<img class="thumb_img" src="<?php echo JPATH_SITE . $pic; ?>" alt="<?php echo htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES); ?>"/>
		</a>
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

	<?php //изображения галереи
	$i = 0;
	foreach($images as $image){
		$title = $image['signature'] ? $image['signature'] : $galleryTitle;

		if(!$i){
			$i++;
			continue;
		} // пропускаем первое изображение
		?>
		<div class="thumb_<?php echo $content->id; ?>" style="display:none;">
			<a class="thumb_link thumb_link<?php
				echo $content->id; ?>" href="/images/boss/<?php
				echo $directory; ?>/contents/gallery/full/<?php
				echo $image['file']; ?>" rel="thumb_set_<?php
				echo $content->id; ?>" title="<?php
				echo $title; ?>">

				<img alt="<?php
					echo $image['signature']; ?>" class="thumb_img thumb_img<?php
					echo $content->id; ?>" src="/images/boss/<?php
					echo $directory; ?>/contents/gallery/thumb/<?php echo $image['file']; ?>"/>
			</a>

			<div class="imgDesc">
				<?php echo $image['signature']; ?>
			</div>
		</div>

		<?php
		$i++;
	}
	?>
	<br clear="all"/>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {
		// инициализируем скрипт галереи
		jQuery("a.thumb_link").colorbox(
			{
				'current':'{current}<?php echo BOSS_PLG_GALLERY_TXT_OF; ?>{total}', // текст счетчика изображений
				'previous':'<?php echo BOSS_PLG_GALLERY_TXT_PREV; ?>', // текст следующего изображения
				'next':'<?php echo BOSS_PLG_GALLERY_TXT_NEXT; ?>', // текст предыдущего изображения
				'close':'<?php echo BOSS_PLG_GALLERY_TXT_CLOSE; ?>', // текст закрыть

				'maxWidth':galleryOptions['maxWidth'], // максимальная ширина "100%", 500, "500px"
				'maxHeight':galleryOptions['maxHeight'], // максимальная высота "100%", 500, "500px"
				'opacity':galleryOptions['overlayOpacity'], // прозрачность подложки

				'slideshow':galleryOptions['autoSlide'], // запускать режим слайд-шоу
				'slideshowSpeed':galleryOptions['slideTimer'], // скорость смены изображений
				'slideshowStart':'<?php echo BOSS_PLG_GALLERY_TXT_START; ?>', // текст запуска слайд-шоу
				'slideshowStop':'<?php echo BOSS_PLG_GALLERY_TXT_STOP; ?>', // текст остановки слайдшоу
			}
		);
	});
</script>
<?php
}


// Отображение в содержимом
function galleryDetailsDisplay($directory, $content, $images, $conf_fields, $conf){
	$galleryTitle = (empty($conf_fields['galleryTitle']))
		? htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES)
		: $conf_fields['galleryTitle'];

	//контейнер с изображениями галереи			  
	foreach($images as $image){
		$title = $image['signature'] ? $image['signature'] : $galleryTitle;
		?>

	<a class="thumb_link" href="/images/boss/<?php
		echo $directory; ?>/contents/gallery/full/<?php
		echo $image['file']; ?>" rel="thumb_set_<?php
		echo $content->id; ?>" title="<?php
		echo $title; ?>">

		<img alt="" class="thumb_img thumb_img<?php
			echo $content->id; ?>" src="/images/boss/<?php
			echo $directory; ?>/contents/gallery/thumb/<?php
			echo $image['file']; ?>"/>
	</a>

	<div class="thumb_desc">
		<?php echo Jstring::trim($image['signature']) ? $image['signature'] : '&nbsp;'; ?>
	</div>

	<?php
	}
	?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		// инициализируем скрипт галереи
		jQuery("a.thumb_link").colorbox(
			{
				'current':'{current}<?php echo BOSS_PLG_GALLERY_TXT_OF; ?>{total}', // текст счетчика изображений
				'previous':'<?php echo BOSS_PLG_GALLERY_TXT_PREV; ?>', // текст следующего изображения
				'next':'<?php echo BOSS_PLG_GALLERY_TXT_NEXT; ?>', // текст предыдущего изображения
				'close':'<?php echo BOSS_PLG_GALLERY_TXT_CLOSE; ?>', // текст закрыть

				'maxWidth':galleryOptions['maxWidth'], // максимальная ширина "100%", 500, "500px"
				'maxHeight':galleryOptions['maxHeight'], // максимальная высота "100%", 500, "500px"
				'opacity':galleryOptions['overlayOpacity'], // прозрачность подложки

				'slideshow':galleryOptions['autoSlide'], // запускать режим слайд-шоу
				'slideshowSpeed':galleryOptions['slideTimer'], // скорость смены изображений
				'slideshowStart':'<?php echo BOSS_PLG_GALLERY_TXT_START; ?>', // текст запуска слайд-шоу
				'slideshowStop':'<?php echo BOSS_PLG_GALLERY_TXT_STOP; ?>', // текст остановки слайдшоу
			}
		);
	});
</script>
<?php
}

?>