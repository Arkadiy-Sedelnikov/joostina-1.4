<?php

	/**
	 * mbGallery
	 * Ссылки:
	 * 1. http://pupunzi.open-lab.com/mb-jquery-components/mb-gallery/
	 * 2. https://github.com/pupunzi/jquery.mb.gallery/wiki/

	 */

// Parameters:
'galleryTitle'		: galleryOptions['galleryTitle'],		// заголовок галереи											// 
'overlayBackground'	: galleryOptions['overlayBackground'],	// цвет слоя подложки
'overlayOpacity'	: galleryOptions['overlayOpacity'],		// прозрачность слоя подложки					

'minWidth' 			: 300,									// минимальная ширина отображения
'minHeight' 		: 300,									// минимальная высота отображения
'maxWidth' 			: galleryOptions['maxWidth'],			// максимальная ширина (0 - без ограничений)
'maxHeight' 		: galleryOptions['maxHeight'],			// максимальная высота (0 - без ограничений)
'fullScreen'		: true,									// разрешить просмотр большого изображения
															// или ограничивать до minWidth minHeight

'fadeTime'			: 300,									// длительность эффекта растворения 
															// при смене изображений

'slideTimer'		: galleryOptions['slideTimer'],			// пауза между сменой изображений  	
'autoSlide'			: galleryOptions['autoSlide'], 			// автозапуск слайд-шоу 		
'startFrom'			: 1,									// номер стартового изображения слайдшоу 
															// или 'random' для случайного выбора					
					
'addRaster'			: false,								// добавлять слой растрирования 
'printOutThumbs'	: true,									// отображать миниатюры 					
'exifData'			: false, 								// просмотр exif-информации фотографии

'skin'				: 'white',								// палитра оформления 'white' или 'black'  
'cssURL'			: 'css/',								// директория css-файлов
'containment'		: 'body',								// 
'thumbnailSelector'	: '.imgThumb',							// селектор миниатюры изображения
'titleSelector'		: '.imgTitle',							// селектор заголовка изображения
'descSelector'		: '.imgDesc',							// селектор описания изображения					

// Callbacks:
'onOpen'			: function(){
},	// действия при открытии галереи
'onBeforeClose'		: function(){
},	// действия перед закрытием галереи
'onClose'			: function(){
},	// действия при закрытии галереи
'onChangePhoto'		: function(){
}	// действия при смене изображения

// Methods:
$.fn . mb_galleryNext()	// go to next image.
$.fn . mb_galleryPrev()	// go to previous image.
$.fn . mb_gotoIDX(int) // go to specific image.
$.fn . mb_startSlide()	// start the slideshow.
$.fn . mb_stopSlide()		// stop the slideshow.
$.fn . mb_showThumbs()	// show the thumbs panel.
$.fn . mb_hideThumbs()	// hide the thumbs panel.
$.fn . mb_closeGallery()	// close the gallery.
