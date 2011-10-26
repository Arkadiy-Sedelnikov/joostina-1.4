<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 *
 * Backend lang-file
 *
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage	JoiEditor
 * @filename com_joieditor.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 *
 **/

defined('_VALID_MOS') or die();
//editor page
DEFINE('_ELRTE_ADMIN_CONFIG','Конфигурация');
DEFINE('_ELRTE_NO_SUPERADMIN_REDIRECT','Только пользователи с правами супер-администратора могут изменять настройки редактора и файлового менеджера.');
DEFINE('_ELRTE_ADMIN_MAIN_CONFIG','Разрешения на панели редактирования');
DEFINE('_ELRTE_EDITOR_CONFIG','Настройки редактора');
DEFINE('_ELRTE_ADMIN_USERGROUP','Группа пользователей');
DEFINE('_ELRTE_ADMIN_WIDTH','Ширина');
DEFINE('_ELRTE_ADMIN_WIDTH_DESC','Ширина редактора');
DEFINE('_ELRTE_ADMIN_HEIGHT','Высота');
DEFINE('_ELRTE_ADMIN_HEIGHT_DESC','Высота редактора');
DEFINE('_ELRTE_ADMIN_COMCONTENT','Содержимое сайта');
DEFINE('_ELRTE_SAVE','Сохранение');
DEFINE('_ELRTE_COPYPASTE','Копировать/вставить');
DEFINE('_ELRTE_UNDOREDO','Отменить/Возобновить');
DEFINE('_ELRTE_STYLE','Стили текста');
DEFINE('_ELRTE_COLORS','Цвета');
DEFINE('_ELRTE_ALIGNMENT','Выравнивание');
DEFINE('_ELRTE_INDENT','Отступ');
DEFINE('_ELRTE_FORMAT','Форматирование');
DEFINE('_ELRTE_LISTS','Списки');
DEFINE('_ELRTE_ELEMENTS','Разные элементы');
DEFINE('_ELRTE_DIRECTION','Направление текста');
DEFINE('_ELRTE_LINKS','Ссылки');
DEFINE('_ELRTE_IMAGES','Изображения');
DEFINE('_ELRTE_MEDIA','Медиа');
DEFINE('_ELRTE_TABLES','Таблицы');
DEFINE('_ELRTE_ELFINDER','Менеджер файлов');
DEFINE('_ELRTE_FULLSCREEN','На весь экран');
DEFINE('_ELRTE_PERMISSIONS','Разрешения');
DEFINE('_ELRTE_EDITOR_OPT','Другие настройки редактора');
DEFINE('_ELRTE_EDITOR_DOCTYPE','DocType окна редактора (iframe).');
DEFINE('_ELRTE_EDITOR_DOCTYPE_DESC','По умолчанию - HTML 4.01 Transitional');
DEFINE('_ELRTE_EDITOR_CSS','CSS Класс для редактора');
DEFINE('_ELRTE_EDITOR_CSS_DESC','CSS Класс для редактора');
DEFINE('_ELRTE_EDITOR_CSS_ARR','Пути до css файлов, подключаемых в окно редактора (iframe)');
DEFINE('_ELRTE_EDITOR_CSS_ARR_DESC','По одному на строку, начиная со слеша (/) от корня сайта.');
DEFINE('_ELRTE_EDITOR_ABS_URLS','Приводить адреса ссылок и изображений к абсолютным');
DEFINE('_ELRTE_EDITOR_ABS_URLS_DESC','Ссылки включают названия сайта или нет.');
DEFINE('_ELRTE_EDITOR_ALLOW_HTML','Разрешает редактирование HTML');
DEFINE('_ELRTE_EDITOR_ALLOW_HTML_DESC','Показывает вкладку HTML в редакторе.');
DEFINE('_ELRTE_EDITOR_STYLE','Оформление стилями');
DEFINE('_ELRTE_EDITOR_STYLE_DESC','Если да - текст будет оформляться тегами span с аттрибутом style, в противном случае - семантическими тегами strong, em и тд');
DEFINE('_ELRTE_EDITOR_ALLOW_FM','Разрешить использование файлового менеджера');
DEFINE('_ELRTE_EDITOR_ALLOW_FM_DESC','Разрешить использование файлового менеджера');
DEFINE('_ELRTE_PANELS','Панели инструментов');
DEFINE('_ELRTE_SELECT_TOOLBAR','Выбрать тулбар из списка');
DEFINE('_ELRTE_CREATE_TOOLBAR','Создать тулбары для групп пользователей');
DEFINE('_ELRTE_TOOLBAR_METOD','Выберите способ создания тулбара');
DEFINE('_ELRTE_TOOLBAR_SELECT','Выберите тулбар из списка');
DEFINE('_ELRTE_TOOLBAR_TINY','Маленький');
DEFINE('_ELRTE_TOOLBAR_COMPACT','Компактный');
DEFINE('_ELRTE_TOOLBAR_NORMAL','Нормальный');
DEFINE('_ELRTE_TOOLBAR_COMPLETE','Полный');
DEFINE('_ELRTE_TOOLBAR_MAXI','Максимальный');
//info page
DEFINE('_ELRTE_INFO_VER_ELRTE','Версия elRTE');
DEFINE('_ELRTE_INFO_VER_ELFINDER','Версия elFinder');
DEFINE('_ELRTE_INFO_VER','Версия компонента');
DEFINE('_ELRTE_INFO_DEV','Разработчик');
DEFINE('_ELRTE_INFO_DESC','Визуальный редактор и файловый менеджер для Joostina CMS. В основе компонента - javascript Jquery Jquery.ui редактор elRTE и файловый менеджер elFinder');
//filemanager page
DEFINE('_ELRTE_ADMIN_IM_PHP_CONFIG','Настройка PHP коннектора');
DEFINE('_ELRTE_ADMIN_IM_CONFIG','Настройка менеджера файлов');
DEFINE('_ELRTE_ADMIN_IM_ROOT_DIR','Корневая директория менеджера, начиная со слеша (/) от корня сайта.');
DEFINE('_ELRTE_ADMIN_IM_DOWNLOAD_IMG','Разрешить загрузку изображений');
DEFINE('_ELRTE_ADMIN_IM_DEL_IMG','Разрешить удаление изображений');
DEFINE('_ELRTE_ADMIN_IM_RENAME_IMG','Разрешить переименование изображений');
DEFINE('_ELRTE_ADMIN_IM_CREATE_DIR','Разрешить создание директорий');
DEFINE('_ELRTE_ADMIN_IM_DEL_DIR','Разрешить удаление директорий');
DEFINE('_ELRTE_ADMIN_IM_CROP','Уменьшать большие изображения до:');
DEFINE('_ELRTE_ADMIN_IM_OWN_DIR','Использовать директории пользователей');
DEFINE('_ELRTE_ADMIN_IM_DISABLED_COMAND','Список запрещенных команд');
DEFINE('_ELRTE_ADMIN_IM_ROOT_ALIAS','Название корневой директории, отображаемое в файловом менеджере');
DEFINE('_ELRTE_ADMIN_IM_COMAND_OPEN','Открыть директорию или вывести содержимое файла в браузер');
DEFINE('_ELRTE_ADMIN_IM_COMAND_MKDIR','Создать директорию');
DEFINE('_ELRTE_ADMIN_IM_COMAND_MKFILE','Создать текстовый файл');
DEFINE('_ELRTE_ADMIN_IM_COMAND_RENAME','Переименовать директорию или файл');
DEFINE('_ELRTE_ADMIN_IM_COMAND_UPLOAD','Загрузить файлы');
DEFINE('_ELRTE_ADMIN_IM_COMAND_PING','ping - служебная команда. необходима для Safari (загрузка файлов)');
DEFINE('_ELRTE_ADMIN_IM_COMAND_PASTE','Скопировать или переместить файлы/директории в указанную директорию');
DEFINE('_ELRTE_ADMIN_IM_COMAND_RM','Удалить директорию/файлы');
DEFINE('_ELRTE_ADMIN_IM_COMAND_DUPLICATE','Дублировать директорию/файл');
DEFINE('_ELRTE_ADMIN_IM_COMAND_READ','Читать содержимое текстового файла');
DEFINE('_ELRTE_ADMIN_IM_COMAND_EDIT','Редактировать текстовый файл');
DEFINE('_ELRTE_ADMIN_IM_COMAND_EXTRACT','Распаковать архив');
DEFINE('_ELRTE_ADMIN_IM_COMAND_ARCHIVE','Сжать директории/файлы в архив');
DEFINE('_ELRTE_ADMIN_IM_COMAND_TMB','Создать миниатюрки для картинок, не имеющих их');
DEFINE('_ELRTE_ADMIN_IM_COMAND_RESIZE','Изменить размер изображения');
DEFINE('_ELRTE_ADMIN_IM_COMAND','Команды');
DEFINE('_ELRTE_ADMIN_IM_DOT_FILES','Показывать файлы, начинающиеся с точки');
DEFINE('_ELRTE_ADMIN_IM_DIR_SIZE','Подсчитывать размер директорий');
DEFINE('_ELRTE_ADMIN_IM_FILE_MODE','mode для новых файлов (0644)');
DEFINE('_ELRTE_ADMIN_IM_DIR_MODE','mode для новых директорий (0755)');
DEFINE('_ELRTE_ADMIN_IM_ALLOWED_FILES','Список разрешенных для загрузки файлов');
DEFINE('_ELRTE_ADMIN_IM_MIMES','Типы файлов');
DEFINE('_ELRTE_ADMIN_IM_IMG_LIB','Биилиотека для обработки изображений');
DEFINE('_ELRTE_ADMIN_IM_TMB_DIR','Директория для превьюшек. Если не заданна - превьюшки не будут создаваться');
DEFINE('_ELRTE_ADMIN_IM_TMB_CLEAN','Частота очистки директории с превьюшками. Варианты - от 0 до 200. 0 - не очищать, 200 - при каждом запросе инициализации файлового менеджера');
DEFINE('_ELRTE_ADMIN_IM_TMB_AT_ONCE','Сколько миниатюрок создавать в одном фоновом запросе. По умолчанию: 5');
DEFINE('_ELRTE_ADMIN_IM_TMB_SIZE','Размер миниатюрок в пикселях');
DEFINE('_ELRTE_ADMIN_IM_FILE_URL','Отдавать URL файлов клиенту');
DEFINE('_ELRTE_ADMIN_IM_CLIENT','Настройка клиента');
DEFINE('_ELRTE_ADMIN_IM_PLACES','Название папки "избранное". Укажите пустую строку "", чтобы отключить "избранное"');
DEFINE('_ELRTE_ADMIN_IM_PLACE_FIRST','Поместить "избранное" перед деревом файлов в панели навигации?');
DEFINE('_ELRTE_ADMIN_VIEW','Внешний вид текущей директории по умолчанию');
DEFINE('_ELRTE_ADMIN_IM_REMEMBER_LAST_DIR','Открывать последнюю посещенную директорию после перезагрузки страницы или закрытии/открытии браузера');
DEFINE('_ELRTE_ADMIN_IM_MIME_ALL','ВСЕ');
DEFINE('_ELRTE_ADMIN_IM_MIME_AUDIO','Аудио файлы');
DEFINE('_ELRTE_ADMIN_IM_MIME_IMAGE','Изображения');
DEFINE('_ELRTE_ADMIN_IM_MIME_TEXT','Текстовые файлы');
DEFINE('_ELRTE_ADMIN_IM_MIME_VIDEO','Видео файлы');
DEFINE('_ELRTE_ADMIN_IM_MIME_PDF','Файлы PDF');
DEFINE('_ELRTE_ADMIN_IM_MIME_XML','Файлы XML');
DEFINE('_ELRTE_ADMIN_IM_MIME_FLASH','Флеш-муви');
DEFINE('_ELRTE_ADMIN_IM_MIME_ZIP','Архивы ZIP');
DEFINE('_ELRTE_ADMIN_IM_MIME_RAR','Архивы RAR');
DEFINE('_ELRTE_ADMIN_IM_MIME_TAR','Архивы TAR');
?>