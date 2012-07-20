<?php

/**
 * @BOSS Шаблон подключения скрипта галереи плагина BossImageGalleryPlugin.
 * @tpl Pirobox
 * @file Pirobox.php
 * @author: Алексей Поздняков <mosgaz@list.ru>
 * @url: www.dev.woodell.ru
 * @package Joostina
 * @subpackage JoiBOSS CCK
 * @copyright (C) 2011 Woodell Web Works
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

// подключаем jQuery UI
mosCommonHTML::loadJqueryUI();

function galleryAddInHead($path){
	// передаем в функцию addInHead скрипты и стили,
	// которые не должны кешироваться
	$params = array();
	$params['js']['galleryImg1'] = $path . '/js/pirobox_extended_min.js';
	$params['css']['galleryImg1'] = $path . '/css/style_1/pirobox.css'; //style_1, style_2
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
		<a class="pirobox_gall" href="<?php echo $full; ?>" title="<?php echo $title; ?>" rel="gallery">
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
</div>

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
		<a class="pirobox_gall thumb_link<?php
			echo $content->id; ?>" href="/images/boss/<?php
			echo $directory; ?>/contents/gallery/full/<?php
			echo $image['file']; ?>" rel="gallery" title="<?php
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

<script type="text/javascript">
	jQuery(document).ready(function () {
		// инициализируем скрипт галереи
		jQuery().piroBox_ext(
			{
				'piro_speed':galleryOptions['slideTimer'], // скорость смены изображений
				'bg_alpha':galleryOptions['overlayOpacity'], // прозрачность подложки
				'piro_scroll':true                                 // центрирование
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

	<a class="thumb_link pirobox_gall" href="/images/boss/<?php
		echo $directory; ?>/contents/gallery/full/<?php
		echo $image['file']; ?>" rel="gallery" title="<?php
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
		jQuery().piroBox_ext(
			{
				'piro_speed':galleryOptions['slideTimer'], // скорость смены изображений
				'bg_alpha':galleryOptions['overlayOpacity'], // прозрачность подложки
				'piro_scroll':true                                 // центрирование
			}
		);
	});
</script>
<?php
}

?>