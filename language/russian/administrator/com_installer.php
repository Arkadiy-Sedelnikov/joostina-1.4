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

DEFINE('_OTHER_COMPONENT_USE_DIR', 'Другой компонент уже использует каталог');
DEFINE('_CANNOT_CREATE_DIR', 'Невозможно создать каталог');
DEFINE('_SQL_ERROR', 'Ошибка выполнения SQL');
DEFINE('_ERROR_MESSAGE', 'Текст ошибки');
DEFINE('_CANNOT_COPY_PHP_INSTALL', 'Не могу скопировать PHP-файл установки');
DEFINE('_CANNOT_COPY_PHP_REMOVE', 'Не могу скопировать PHP-файл удаления');
DEFINE('_ERROR_DELETING', 'Ошибка удаления');
DEFINE('_IS_PART_OF_CMS', 'является элементом ядра Joostina и не может быть удален.<br />Вы должны снять его с публикации, если не хотите его использовать');
DEFINE('_DELETE_ERROR', 'Удаление - ошибка');
DEFINE('_UNINSTALL_ERROR', 'Ошибка деинсталляции');
DEFINE('_BAD_XML_FILE', 'Неправильный XML-файл');
DEFINE('_PARAM_FILED_EMPTY', 'Поле параметра пустое и невозможно удалить файлы');
DEFINE('_INSTALLED_COMPONENTS', 'Установленные компоненты');
DEFINE('_INSTALLED_COMPONENTS2', 'Здесь показаны только те расширения, которые Вы можете удалить. Части ядра Joostina удалить нельзя.');
DEFINE('_COMPONENT_NAME', 'Название компонента');
DEFINE('_COMPONENT_LINK', 'Ссылка меню компонента');
DEFINE('_COMPONENT_AUTHOR_URL', 'URL автора');
DEFINE('_OTHER_COMPONENTS_NOT_INSTALLED', 'Сторонние компоненты не установлены');
DEFINE('_COMPONENT_INSTALL', 'Установка компонента');
DEFINE('_CANNOT_DEL_LANG_ID', 'id языка пусто, поэтому невозможно удалить файлы');
DEFINE('_GOTO_LANG_MANAGEMENT', 'Перейти в Управление языками');
DEFINE('_INTALL_LANG', 'Установка языкового пакета сайта');
DEFINE('_NO_FILES_OF_MAMBOTS', 'Нет файлов, отмеченных как мамботы');
DEFINE('_WRONG_ID', 'Неправильный id объекта');
DEFINE('_BAD_DIR_NAME_EMPTY', 'Поле папки пустое, невозможно удалить файлы');
DEFINE('_INSTALLED_MAMBOTS', 'Установленные мамботы');
DEFINE('_OTHER_MAMBOTS', 'Это не мамботы ядра, а сторонние мамботы');
DEFINE('_INSTALL_MAMBOT', 'Установка мамбота');
DEFINE('_UNKNOWN_CLIENT', 'Неизвестный тип клиента');
DEFINE('_NO_FILES_MODULES', 'Файлы, отмеченные как модули, отсутствуют');
DEFINE('_ALREADY_EXISTS', 'уже существует');
DEFINE('_DELETING_XML_FILE', 'Удаление XML файла');
DEFINE('_INSTALL_MODULE', 'Установленные модули');
DEFINE('_NO_OTHER_MODULES', 'Сторонние модули не установлены');
DEFINE('_MODULE_INSTALL', 'Установка модуля');
DEFINE('_CANNOT_DEL_FILE_NO_DIR', 'Невозможно удалить файл, т.к. каталог не существует');
DEFINE('_CHOOSE_DIRECTORY_PLEASE', 'Пожалуйста, выберите каталог');
DEFINE('_ZIP_UPLOAD_AND_INSTALL', 'Загрузка архива расширения с последующей установкой');
DEFINE('_PACKAGE_FILE', 'Файл пакета');
DEFINE('_UPLOAD_AND_INSTALL', 'Загрузить и установить');
DEFINE('_INSTALL_FROM_DIR', 'Установка из каталога');
DEFINE('_INSTALLATION_DIRECTORY', 'Каталог установки');
DEFINE('_NO_INSTALLER', 'не найден инсталлятор');
DEFINE('_CANNOT_INSTALL', 'Установка [$element] невозможна');
DEFINE('_CANNOT_INSTALL_DISABLED_UPLOAD', 'Установка невозможна, пока запрещена загрузка файлов. Пожалуйста, используйте установку из каталога.');
DEFINE('_INSTALL_ERROR', 'Ошибка установки');
DEFINE('_CANNOT_INSTALL_NO_ZLIB', 'Установка невозможна, пока не установлена поддержка zlib');
DEFINE('_NO_FILE_CHOOSED', 'Файл не выбран');
DEFINE('_ERORR_UPLOADING_EXT', 'Ошибка загрузки нового модуля');
DEFINE('_UPLOADING_ERROR', 'Загрузка неудачна');
DEFINE('_SUCCESS', 'успешно');
DEFINE('_UNSUCCESS', 'неудачно');
DEFINE('_UPLOAD_OF_EXT', 'Загрузка нового элемента');
DEFINE('_DELETE_SUCCESS', 'Удаление успешно');
DEFINE('_CANNOT_CHMOD', 'Не могу изменить права доступа к закачанному файлу');
DEFINE('_CANNOT_WRITE_TO_MEDIA', 'Загрузка сорвана, так как каталог <code>/media</code> недоступен для записи.');
DEFINE('_CANNOT_INSTALL_NO_MEDIA', 'Загрузка сорвана, так как каталог <code>/media</code> не существует');
DEFINE('_ERROR_NO_XML_JOOMLA', 'ОШИБКА: В установочном пакете невозможно найти XML-файл установки Joostina.');
DEFINE('_ERROR_NO_XML_INSTALL', 'ОШИБКА: В установочном пакете невозможно найти XML-файл установки.');
DEFINE('_NO_NAME_DEFINED', 'Не определено имя файла');
DEFINE('_NOT_CORRECT_INSTALL_FILE_FOR_JOOMLA', 'не является корректным файлом установки Joostina!');
DEFINE('_CANNOT_RUN_INSTALL_METHOD', 'Метод "install" не может быть вызван классом');
DEFINE('_CANNOT_RUN_UNINSTALL_METHOD', 'Метод "uninstall" не может быть вызван классом');
DEFINE('_CANNOT_FIND_INSTALL_FILE', 'Установочный файл не найден');
DEFINE('_XML_NOT_FOR', 'Установочный XML-файл - не для');
DEFINE('_FILE_NOT_EXISTSS', 'Файл не существует');
DEFINE('_INSTALL_TWICE', 'Вы пытаетесь дважды установить одно и то же расширение');
DEFINE('_ERROR_COPYING_FILE', 'Ошибка копирования файла');
DEFINE('_INSTALL_MANAGER', 'Менеджер расширений');
DEFINE('_CHOOSE_DIRECTORY_NOT_ARCHIVE', 'Пожалуйста, выберите каталог, а не архив');
DEFINE('_CHOOSE_URL_PLEASE', 'Пожалуйста, введите адрес');
DEFINE('_INSTALL_FROM_URL', 'Установка по URL');
DEFINE('_INSTALLATION_URL', 'URL архива');
DEFINE('_UNKNOWN_EXTENSION_TYPE', 'Неизвестный тип расширения');
DEFINE('_DISABLE_ALLOW_URL_FOPEN', 'Установка через URL невозможна, настройка <a href="http://php.net/manual/en/features.remote-files.php" target="_blank">allow_url_fopen</a> сервера запрещена');
DEFINE('_CANNOT_CONNECT_SERVER', 'Не удалось установить связь с удаленным сервером');
DEFINE('_COM_INSTALLER_MAMBOT_EXIST', 'Мамбот %s уже существует');
DEFINE('_COM_INSTALLER_ACTIVE', 'Разрешен');
DEFINE('_COM_INSTALLER_NO_PREVIEW', 'Предпросмотр недоступен');
DEFINE('_COM_INSTALLER_TEMPLATE_PREVIEW', 'Предпросмотр шаблона');