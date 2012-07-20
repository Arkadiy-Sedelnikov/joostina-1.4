<?php

/**
 * @BOSS Шаблон подключения скрипта галереи плагина BossImageGalleryPlugin.
 * @tpl FancyBox
 * @file FancyBox.php
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
	// инициализируем скрипт галереи
	galleryScriptInit();

	// передаем в функцию addInHead скрипты и стили, 
	// которые не должны кешироваться
	$params = array();
	$params['js']['galleryImg1'] = '/includes/js/jquery/plugins/fancybox/jquery.fancybox.js';
	$params['css']['galleryImg1'] = '/includes/js/jquery/plugins/fancybox/jquery.fancybox.css';
	$params['css']['galleryImg2'] = $path . '/css/fancybox.custom.css';
	return $params;
}

// инициализация скрипта галереи
function galleryScriptInit(){
	//mosCommonHTML::loadJqueryPlugins('fancybox/jquery.fancybox', false, true);
	//loadJqueryPlugins($name, $ret = false, $css = false, $footer = '', $folder='')
	?>

<script type="text/javascript">
	// параметры инициализации
	jQuery(document).ready(function () {
		// настраиваем слайдшоу
		galleryOptions['cyclic'] = false;
		galleryOptions['onStart'] = function () {
		};

		if (galleryOptions['autoSlide'] == true) {
			galleryOptions['cyclic'] = true;
			galleryOptions['onStart'] = function () {
				setInterval(jQuery.fancybox.next, galleryOptions['slideTimer'])
			};
		}

		// отображаем описание изображения при наведении
		galleryOptions['onComplete'] = function () {
			jQuery("#fancybox-wrap").hover(
				function () { //alert('hover');
					jQuery("#fancybox-title").show();
				},
				function () { //alert('blur');
					jQuery("#fancybox-title").hide();
				});
		};

		jQuery("a.thumb_link").fancybox(
			{
				'padding':3, // внутренние отступы (px)
				'margin':10, // внешние отступы (px)
				'opacity':false,
				'modal':false,
				'cyclic':galleryOptions['cyclic'], // цикличный просмотр изображений
				'scrolling':'auto', //'auto', 'yes', или 'no'
				'width':galleryOptions['maxWidth'], //
				'height':galleryOptions['maxHeight'], //
				'autoScale':true, //
				'autoDimensions':true, //
				'centerOnScroll':true, //
				'hideOnOverlayClick':true, // закрывать окно при клике по подложке
				'hideOnContentClick':false, // закрывать окно при клике по изображению
				'overlayShow':true, //
				'overlayOpacity':galleryOptions['overlayOpacity'], // прозрачность подложки
				'overlayColor':galleryOptions['overlayBackground'], // цвет подложки
				'titleShow':true, //
				'titlePosition':'over', //'outside', 'inside' или 'over'
				'transitionIn':'fade', //'elastic', 'fade' или 'none'
				'transitionOut':'fade', //'elastic', 'fade' или 'none'
				'speedIn':300, //
				'speedOut':300, //
				'changeSpeed':300, //
				'changeFade':'fast', //
				'easingIn':'swing',
				'easingOut':'swing',
				'showCloseButton':true, // кнопка закрытия модального окна
				'showNavArrows':true, // кнопки навигации
				'enableEscapeButton':true, // закрывать окно при нажатии Esc

				'onStart':galleryOptions['onStart'],
				'onComplete':galleryOptions['onComplete']
			}
		).animate({opacity:1.0}, 1500);
	});
</script>

<?php
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
			<img src="<?php echo JPATH_SITE . $pic; ?>" alt="<?php echo htmlspecialchars(stripslashes(cutLongWord($content->name)), ENT_QUOTES); ?>"/>
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

	<?php //ссылка на просмотр изображений
	?>
	<br clear="all"/>
	<a class="thumb_link galleryView" href="/images/boss/<?php
		echo $directory; ?>/contents/gallery/full/<?php
		echo $images[0]['file']; ?>" rel="thumb_set_<?php
		echo $content->id; ?>" title="<?php
		echo $images[0]['signature']; ?>"><?php
		echo BOSS_PLG_GALLERY_VIEW; ?>
	</a>
	<br clear="all"/>
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
</div>
<br clear="all"/>
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
}

?>