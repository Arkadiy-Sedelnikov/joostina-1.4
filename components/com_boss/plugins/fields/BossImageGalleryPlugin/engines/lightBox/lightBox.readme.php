<?php
/**
* 
* lightBox
* 
* Ссылки:
* 1. http://leandrovieira.com/projects/jquery/lightbox/
*
* Лицензия:
* 1. CCAttribution-ShareAlike 2.5 Brazil (http://creativecommons.org/licenses/by-sa/2.5/br/deed.en_US)
* 
*/

//Использование
/* <script type="text/javascript">
$(function() {
	// Use this example, or...
	$('a[@rel*=lightbox]').lightBox(); // Select all links that contains lightbox in the attribute rel
	// This, or...
	$('#gallery a').lightBox(); // Select all links in object with gallery ID
	// This, or...
	$('a.lightbox').lightBox(); // Select all links with lightbox class
	// This, or...
	$('a').lightBox(); // Select all links in the page
	// ... The possibility are many. Use your creative or choose one in the examples above
});
</script> */

// Configuration related to overlay
overlayBgColor: 		'#000',		// (string) цвет заливки фона подложки
overlayOpacity:			0.8,		// (integer) прозрачность слоя подложки

// Configuration related to navigation
fixedNavigation:		false,		// (boolean) фиксированные элементы навигации.

// Configuration related to container image box
containerBorderSize:	10,			// (integer) внутренние отступы, #lightbox-container-image-box
containerResizeSpeed:	400,		// (integer) скорость анимации смены изображений. 400 is default.

// Configuration related to texts in caption. For example: Image 2 of 8. You can alter either "Image" and "of" texts.
txtImage:				'Image',	// (string) текст "Image"
txtOf:					'of',		// (string) текст "of"

// Configuration related to keyboard navigation
keyToClose:				'c',		// (string) (c = close) Letter to close the jQuery lightBox interface. Beyond this letter, the letter X and the ESCAPE key is used to.
keyToPrev:				'p',		// (string) (p = previous) Letter to show the previous image
keyToNext:				'n',		// (string) (n = next) Letter to show the next image.

// Donґt alter these variables in any way
imageArray:				[],
activeImage:			0

?>