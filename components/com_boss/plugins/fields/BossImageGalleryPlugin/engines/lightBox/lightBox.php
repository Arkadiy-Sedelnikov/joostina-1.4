<?php

/**
 * @BOSS Шаблон подключения скрипта галереи плагина BossImageGalleryPlugin.
 * @tpl lightBox
 * @file lightBox.php
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

	$params['js']['galleryImg1'] = $path . '/js/jquery.lightbox-0.6.js';
	$params['css']['galleryImg1'] = $path . '/css/lightbox.css';
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
		<a class="thumb_link" href="<?php
			echo $full; ?>" title="<?php
			echo $title; ?>" rel="lightBox_<?php
			echo $content->id; ?>">
			<img class="thumb_img" src="<?php
				echo JPATH_SITE . $pic; ?>" alt="<?php
				echo htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES); ?>"/>
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
				echo $image['file']; ?>" rel="lightBox_<?php
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
		jQuery("a[rel^='lightbox_<?php echo $content->id; ?>']").lightBox(
			{
				'overlayBgColor':galleryOptions['overlayBackground'], // цвет заливки фона подложки
				'overlayOpacity':galleryOptions['overlayOpacity'], // прозрачность слоя подложки

				'fixedNavigation':false, // фиксированные элементы навигации

				'containerBorderSize':10, // внутренние отступы, #lightbox-container-image-box
				'containerResizeSpeed':400, // скорость анимации смены изображений

				'txtImage':'<?php //echo BOSS_PLG_GALLERY_TXT_IMG; ?>', // текст "Image"
				'txtOf':'/<?php //echo BOSS_PLG_GALLERY_TXT_OF; ?>', // текст "of"
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
		echo $image['file']; ?>" rel="lightBox_<?php
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
		jQuery("a[rel^='lightbox_<?php echo $content->id; ?>']").lightBox(
			{
				'overlayBgColor':galleryOptions['overlayBackground'], // цвет заливки фона подложки
				'overlayOpacity':galleryOptions['overlayOpacity'], // прозрачность слоя подложки

				'fixedNavigation':false, // фиксированные элементы навигации

				'containerBorderSize':10, // внутренние отступы, #lightbox-container-image-box
				'containerResizeSpeed':400, // скорость анимации смены изображений

				'txtImage':'<?php //echo BOSS_PLG_GALLERY_TXT_IMG; ?>', // текст "Image"
				'txtOf':'/<?php //echo BOSS_PLG_GALLERY_TXT_OF; ?>', // текст "of"
			}
		);
	});
</script>
<?php
}

?>