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


DEFINE('_RENAME','Переименовать');
DEFINE('_JWMM_DIRECTORIES','Каталогов');
DEFINE('_JWMM_FILES','Файлов');
DEFINE('_JWMM_DELETE_FILE_CONFIRM','Удалить файл');
DEFINE('_CLICK_TO_PREVIEW','Нажмите для просмотра');
DEFINE('_WIDTH','Ширина');
DEFINE('_HEIGHT','Высота');
DEFINE('_UNPACK','Распаковать');
DEFINE('_JWMM_VIDEO_FILE','Видео файл');
DEFINE('_JWMM_HACK_ATTEMPT','Попытка взлома...');
DEFINE('_JWMM_DIRECTORY_NOT_EMPTY','Каталог не пустой. Пожалуйста, удалите сначала содержимое внутри каталога!');
DEFINE('_JWMM_DELETE_CATALOG','Удалить каталог');
DEFINE('_JWMM_SAFE_MODE_WARNING','При активированном параметре SAFE MODE возможны проблемы с созданием каталогов');
DEFINE('_JWMM_CATALOG_CREATED','Создан каталог');
DEFINE('_JWMM_CATALOG_NOT_CREATED','Создан не каталог');
DEFINE('_JWMM_FILE_NOT_DELETED','Файл не удалён');
DEFINE('_JWMM_DIRECTORY_DELETED','Каталог удалён');
DEFINE('_JWMM_DIRECTORY_NOT_DELETED','Каталог не удалён');
DEFINE('_JWMM_RENAMED','Переименовано');
DEFINE('_JWMM_NOT_RENAMED','Не переименовано');
DEFINE('_JWMM_COPIED','Скопировано');
DEFINE('_JWMM_NOT_COPIED','Не скопировано');
DEFINE('_JWMM_FILE_MOVED','Файл перемещён');
DEFINE('_JWMM_FILE_NOT_MOVED','Файл не перемещён');
DEFINE('_TMP_DIR_CLEANED','Временный каталог полностью очищен');
DEFINE('_TMP_DIR_NOT_CLEANED','Временный каталог не очищен');
DEFINE('_FILES_UNPACKED','файл(ов) распакованы');
DEFINE('_ERROR_WHEN_UNPACKING','Ошибка распаковки');
DEFINE('_FILE_IS_NOT_A_ZIP','Файл не является корректным zip архивом');
DEFINE('_FILE_NOT_EXISTS','Файл не существует');
DEFINE('_IMAGE_SAVED_AS','Отредактированное изображение сохранено как');
DEFINE('_IMAGE_NOT_SAVED','Изображение НЕ сохранено');
DEFINE('_FILES_NOT_UPLOADED','Файл(ы) НЕ загружены на сервер');
DEFINE('_FILES_UPLOADED','Файлы загружены');
DEFINE('_DIRECTORIES','Каталоги');
DEFINE('_JWMM_FILE_NAME_WARNING','Пожалуйста, не используйте в названиях пробелы и спецсимволы');
DEFINE('_MEDIA_MANAGER','Медиа менеджер');
DEFINE('_JWMM_CREATE_DIRECTORY','Создать каталог');
DEFINE('_UPLOAD_FILE','Загрузить файл');
DEFINE('_FILE_PATH','Местоположение');
DEFINE('_JWMM_UP_TO_DIRECTORY','Перейти на каталог выше');
DEFINE('_JWMM_RENAMING','Переименование');
DEFINE('_JWMM_NEW_NAME','Новое имя (включая расширение!)');
DEFINE('_CHOOSE_DIR_TO_COPY','Выберите каталог для копирования');
DEFINE('_JWMM_COPY_TO','Копировать в');
DEFINE('_CHOOSE_DIR_TO_MOVE','Выберите каталог для перемещения');
DEFINE('_MOVE_TO','Переместить в');
DEFINE('_CHOOSE_DIR_TO_UNPACK','Выберите каталог для распаковки');
DEFINE('_DERICTORY_TO_UNPACK','Каталог распаковки');
DEFINE('_NUMBER_OF_IMAGES_IN_TMP_DIR','Число изображений во временном каталоге');
DEFINE('_CLEAR_DIRECTORY','Очистить каталог');
DEFINE('_JWMM_ERROR_EDIT_FILE','Ошибка при обработке файла');
DEFINE('_JWMM_EDIT_IMAGE','Редактирование изображения');
DEFINE('_JWMM_IMAGE_RESIZE','Расширение изображения');
DEFINE('_JWMM_IMAGE_CROP','Обрезать');
DEFINE('_JWMM_IMAGE_SIZE','Размеры');
DEFINE('_JWMM_X_Y_POSITION','X и Y координаты');
DEFINE('_VERICAL','вертикали');
DEFINE('_HORIZONTAL','горизонтали');
DEFINE('_JWMM_CROP_TOP','Сверху');
DEFINE('_JWMM_BOTTOM','Снизу');
DEFINE('_JWMM_ROTATION','Поворот');
DEFINE('_JWMM_CHOOSE','- выбор -');
DEFINE('_JWMM_MIRROR','Отражение');
DEFINE('_JWMM_GRADIENT_BORDER','Градиентная рамка');
DEFINE('_JWMM_SIZE_PX','Размер px');
DEFINE('_JWMM_TOP_LEFT','Сверху-Слева');
DEFINE('_JWMM_PRESS_TO_CHOOSE_COLOR','Нажмите для выбора цвета');
DEFINE('_JWMM_BOTTOM_RIGHT','Справа-Снизу');
DEFINE('_JWMM_BORDER','Бордюр');
DEFINE('_COLOR','Цвет');
DEFINE('_JWMM_ALL_BORDERS','Все бордюры');
DEFINE('_JWMM_TOP','Сверху');
DEFINE('_JWMM_LEFT','Слева');
DEFINE('_JWMM_BRIGHTNESS','Яркость');
DEFINE('_JWMM_CONTRAST','Контраст');
DEFINE('_JWMM_ADDITIONAL_ACTIONS','Дополнительные действия');
DEFINE('_JWMM_GRAY_SCALE','Градиент серого');
DEFINE('_JWMM_NEGATIVE','Негатив');
DEFINE('_JWMM_ADD_TEXT','Добавить текст');
DEFINE('_JWMM_TEXT','Текст');
DEFINE('_JWMM_TEXT_COLOR','Цвет текста');
DEFINE('_JWMM_TEXT_FONT','Шрифт текста');
DEFINE('_JWMM_TEXT_SIZE','Размер текста');
DEFINE('_JWMM_ORIENTATION','Ориентация');
DEFINE('_JWMM_BG_COLOR','Цвет фона');
DEFINE('_JWMM_XY_POSITION','Расположение по X и Y');
DEFINE('_JWMM_XY_PADDING','Отступы по X и Y');
DEFINE('_JWMM_SECOND','Вторая');
DEFINE('_JWMM_THIRDTH','Третья...');
DEFINE('_JWMM_CANCEL_ALL','Отменить всё');
DEFINE('_JWMM_IMAGE_LINK','Путь к файлу:');
DEFINE('_JWMM_IMAGE_HREF','Тэг-ссылка на файл:');
DEFINE('_JWMM_IMAGE_TAG','Тэг вставки изображения:');
DEFINE('_JWMM_CLICK_TO_URL','Нажмите для получения ссылок');
DEFINE('_JWMM_FILE','Файлы');
