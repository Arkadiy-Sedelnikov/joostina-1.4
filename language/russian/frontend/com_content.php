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



DEFINE('_LAST_UPDATED','Последнее обновление');
DEFINE('_LEGEND','История');
DEFINE('_HEADER_AUTHOR','Автор');
DEFINE('_HEADER_SUBMITTED','Написан');
DEFINE('_HEADER_HITS','Просмотров');
DEFINE('_E_WARNUSER','Пожалуйста, нажмите кнопку "Отмена" или "Сохранить", чтобы покинуть эту страницу');
DEFINE('_E_WARNTITLE','Содержимое должно иметь заголовок');
DEFINE('_E_WARNTEXT','Содержимое должно иметь вводный текст');
DEFINE('_E_TITLE','Заголовок');
DEFINE('_E_INTRO','Вводный текст');
DEFINE('_E_MAIN','Основной текст');
DEFINE('_E_MOSIMAGE','Вставить тег {mosimage}');
DEFINE('_E_GALLERY_IMAGES','Галерея изображений');
DEFINE('_EDIT_IMAGE','Параметры изображения');
DEFINE('_E_NO_IMAGE','Без изображения');
DEFINE('_E_INSERT','Вставить');
DEFINE('_E_SOURCE','Название файла:');
DEFINE('_E_ALIGN','Расположение:');
DEFINE('_E_ALT','Альтернативный текст:');
DEFINE('_E_BORDER','Рамка:');
DEFINE('_E_APPLY','Применить');
DEFINE('_E_AUTHOR_ALIAS','Псевдоним автора:');
DEFINE('_E_ACCESS_LEVEL','Уровень доступа:');
DEFINE('_E_ORDERING','Порядок:');
DEFINE('_E_START_PUB','Дата:');
DEFINE('_E_FINISH_PUB','Дата окончания публикации:');
DEFINE('_E_SHOW_FP','Показывать на главной странице:');
DEFINE('_E_HIDE_TITLE','Скрыть заголовок:');
DEFINE('_E_METADATA','Мета-тэги');
DEFINE('_DESC','Описание:');
DEFINE('_E_M_KEY','Ключевые слова:');
DEFINE('_E_SUBJECT','Тема:');
DEFINE('_E_EXPIRES','Дата истечения:');
DEFINE('_E_ABOUT','Об объекте:');
DEFINE('_E_LAST_MOD','Последнее изменение:');
DEFINE('_E_REGISTERED','Только для зарегистрированных пользователей');
DEFINE('_KEY_NOT_FOUND','Ключ не найден');
DEFINE('_SECTION_ARCHIVE_EMPTY','В этом разделе архива сейчас нет объектов. Пожалуйста, зайдите позже');
DEFINE('_CATEGORY_ARCHIVE_EMPTY','В этой категории архива сейчас нет объектов. Пожалуйста, зайдите позже');
DEFINE('_HEADER_SECTION_ARCHIVE','Архив разделов');
DEFINE('_HEADER_CATEGORY_ARCHIVE','Архив категорий');
DEFINE('_ARCHIVE_SEARCH_FAILURE','Не найдено архивных записей для %s %s'); // значения месяца, а затем года
DEFINE('_ARCHIVE_SEARCH_SUCCESS','Найдены архивные записи для %s %s'); // значения месяца, а затем года
DEFINE('_ORDER_DROPDOWN_DA','Дата (по возрастанию)');
DEFINE('_ORDER_DROPDOWN_DD','Дата (по убыванию)');
DEFINE('_ORDER_DROPDOWN_TA','Название (по возрастанию)');
DEFINE('_ORDER_DROPDOWN_TD','Название (по убыванию)');
DEFINE('_ORDER_DROPDOWN_HA','Просмотры (по возрастанию)');
DEFINE('_ORDER_DROPDOWN_HD','Просмотры (по убыванию)');
DEFINE('_ORDER_DROPDOWN_AUA','Автор (по возрастанию)');
DEFINE('_ORDER_DROPDOWN_AUD','Автор (по убыванию)');
DEFINE('_ORDER_DROPDOWN_O','По порядку');
DEFINE('_ORDER_DROPDOWN_S_C_ASC','Раздел / Категория по возрастанию');
DEFINE('_ORDER_DROPDOWN_S_C_DESC','Раздел / Категория по убыванию');
DEFINE('_YOU_HAVE_NO_CONTENT','Нет добавленного Вами содержимого');
DEFINE('_CONTENT_IS_BEING_EDITED_BY_OTHER_PEOPLE','Содержимое сейчас редактируется другим человеком');
DEFINE('_EMPTY_BLOG','Нет объектов для отображения!');
DEFINE('_USER_NOT_FOUND','Извините, пользователь не найден');
DEFINE('_COM_CONTENT_USERCONTENT_NOT_FOUND','Извините, материалы не найдены');
DEFINE('_COM_CONTENT_NEW_ITEM','Новый объект');
DEFINE('_COM_CONTENT_ITEM_SAVED','Изменения сохранены. Здесь (ссылка) страница для предпросмотра');
DEFINE('_COM_CONTENT_ITEM_ADDED','Материал был успешно добавлен и будет доступен для общего просмотра после проверки модератором. А пока повпечатляйтесь версией для предпросмотра.');
DEFINE('_COM_CONTENT_ITEM_ADDED_THANK','Спасибо и все такое');
DEFINE('_COM_CONTENT_ITEM_CHANGES_SAVED','Изменения сохранены.');
DEFINE('_COM_CONTENT_ITEM_ALL_CHANGES_SAVED','Все изменения были успешно сохранены');
DEFINE('_COM_CONTENT_ITEM_ADDED_THANK_2','Внимание! Это версия для предпросмотра. Материал еще не был опубликован на сайте, вероятно, ожидается проверка модератором.');
/** письмо другу*/
DEFINE('_EMAIL_TITLE','Отправить e-mail другу');
DEFINE('_EMAIL_FRIEND','Отправить ссылку страницы на e-mail:');
DEFINE('_EMAIL_FRIEND_ADDR','E-Mail друга:');
DEFINE('_EMAIL_YOUR_NAME','Ваше имя:');
DEFINE('_EMAIL_YOUR_MAIL','Ваш e-mail:');
DEFINE('_SUBJECT_PROMPT',' Тема сообщения:');
DEFINE('_BUTTON_SUBMIT_MAIL','Отправить');
DEFINE('_EMAIL_ERR_NOINFO','Вы должны правильно ввести свой e-mail и e-mail получателя этого письма.');
DEFINE('_EMAIL_MSG',' Здравствуйте! Следующую ссылку на страницу сайта "%s" отправил Вам %s ( %s ).

Вы сможете просмотреть её по этой ссылке:
%s');
DEFINE('_THANK_SUB_PUB','Спасибо за Ваш материал.');
DEFINE('_EMAIL_INFO','Письмо отправил');
DEFINE('_EMAIL_SENT','Ссылка на эту страницу отправлена для');
DEFINE('_ON_NEW_CONTENT',"Пользователь [ %s ] добавил новый объект [ %s ]. Раздел: [ %s ]. Категория: [ %s ]");
DEFINE('_TOC_JUMPTO','Оглавление');