<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2007 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/copyleft/gpl.html GNU/GPL, смотрите LICENSE.php
* Joostina! - свободное программное обеспечение. Эта версия может быть изменена
* в соответствии с Генеральной Общественной Лицензией GNU, поэтому возможно
* её дальнейшее распространение в составе результата работы, лицензированного
* согласно Генеральной Общественной Лицензией GNU или других лицензий свободных
* программ или программ с открытым исходным кодом.
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
*/

// запрет прямого доступа
defined( '_VALID_MOS' ) or die( 'Прямой вызов файла запрещен' );

DEFINE('_XMAP_CFG_COM_TITLE',		'Карты сайта');
DEFINE('_XMAP_CFG_OPTIONS',			'Отображение');
DEFINE('_XMAP_CFG_CSS_CLASSNAME',	'Стили CSS');
DEFINE('_XMAP_CFG_EXPAND_CATEGORIES','Расширять категории содержимого');
DEFINE('_XMAP_CFG_EXPAND_SECTIONS',	'Расширять разделы содержимого');
DEFINE('_XMAP_CFG_SHOW_MENU_TITLES','Показывать названия меню');
DEFINE('_XMAP_CFG_NUMBER_COLUMNS',	'Число столбцов');
DEFINE('_XMAP_EX_LINK',				'Отмечать внешние ссылки');
DEFINE('_XMAP_CFG_CLICK_HERE', 		'Нажмите сюда');
DEFINE('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
DEFINE('_XMAP_EXCLUDE_MENU',		'Исключать ID меню');
DEFINE('_XMAP_TAB_DISPLAY',			'Отображение');
DEFINE('_XMAP_CFG_WRITEABLE',		'доступен');
DEFINE('_XMAP_CFG_UNWRITEABLE',		'не доступен');
DEFINE('_XMAP_MSG_MAKE_UNWRITEABLE','Запретить редактирование после сохранения');
DEFINE('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Игнорировать защиту при записи');
DEFINE('_XMAP_CFG_INCLUDE_LINK',	'Скрывать ссылку на автора');
DEFINE('_XMAP_EXCLUDE_MENU_TIP',	'Идентификаторы меню исключаемые из карты.<br /><strong>ВНИМАНИЕ</strong><br />Идентификаторы разделять запятыми!');
DEFINE('_XMAP_CFG_SET_ORDER',		'Сортировка меню');
DEFINE('_XMAP_CFG_MENU_SHOW',		'Показать');
DEFINE('_XMAP_CFG_MENU_REORDER',	'Пересортировать');
DEFINE('_XMAP_CFG_MENU_ORDER',		'Положение');
DEFINE('_XMAP_CFG_MENU_NAME',		'Название меню');
DEFINE('_DISABLE',			'Отключить');
DEFINE('_XMAP_CFG_ENABLE',			'Включить');
DEFINE('_XMAP_SHOW',				'Показать');
DEFINE('_XMAP_NO_SHOW',				'Не показывать');
DEFINE('_XMAP_TOOLBAR_CANCEL', 		'Выход');
DEFINE('_XMAP_ERR_NO_LANG',			'Языковой файл [ %s ] не найден, используется язык по умолчанию<br />');
DEFINE('_XMAP_ERR_CONF_SAVE',		 'ОШИБКА: Невозможно сохранить конфигурацию.');
DEFINE('_XMAP_ERR_NO_CREATE',		 'ОШИБКА: Отсутствует таблица настроек');
DEFINE('_XMAP_ERR_NO_DEFAULT_SET',	'ОШИБКА: Отсутствует таблица базовых данных');
DEFINE('_XMAP_ERR_NO_PREV_BU',		'ПРЕДУПРЕЖДЕНИЕ: Невозможно удалить копию');
DEFINE('_XMAP_ERR_NO_BACKUP',		 'ОШИБКА: Невозможно создать копию');
DEFINE('_XMAP_ERR_NO_DROP_DB',		'ОШИБКА: Невозможно удалить настройки');
DEFINE('_XMAP_ERR_NO_SETTINGS',		'ОШИБКА: Невозможно загрузить настройки: <a href="%s">Создать таблицу настроек</a>');
DEFINE('_XMAP_MSG_SET_RESTORED',	'Настройки восстановлены');
DEFINE('_XMAP_MSG_SET_BACKEDUP',	'Настройки сохранены');
DEFINE('_XMAP_MSG_SET_DB_CREATED',	'Таблица настроек создана');
DEFINE('_XMAP_MSG_SET_DEF_INSERT',	'Базовые данные внесены');
DEFINE('_XMAP_MSG_SET_DB_DROPPED',	'Таблицы Xmap\'s сохранены!');
DEFINE('_XMAP_CSS',		'Xmap CSS');
DEFINE('_XMAP_CSS_EDIT','Редактирование CSS карт'); // Edit template
DEFINE('_XMAP_SHOW_AS_EXTERN_ALT',	'Ссылка откроется в новом окне');
DEFINE('_XMAP_CFG_MENU_SHOW_HTML',		'Показывать на сайте');
DEFINE('_XMAP_CFG_MENU_SHOW_XML',		'Показывать в XML карте');
DEFINE('_XMAP_CFG_MENU_PRIORITY',		'Приоритет');
DEFINE('_XMAP_CFG_MENU_CHANGEFREQ',		'Создавать');
DEFINE('_XMAP_CFG_CHANGEFREQ_ALWAYS',	'Постоянно');
DEFINE('_XMAP_CFG_CHANGEFREQ_HOURLY',	'Ежечасно');
DEFINE('_XMAP_CFG_CHANGEFREQ_DAILY',	'Ежедневно');
DEFINE('_XMAP_CFG_CHANGEFREQ_WEEKLY',	'Еженедельно');
DEFINE('_XMAP_CFG_CHANGEFREQ_MONTHLY',	'Ежемесячно');
DEFINE('_XMAP_CFG_CHANGEFREQ_YEARLY',	'Ежегодно');
DEFINE('_XMAP_TIT_SETTINGS_OF',			'Настройки %s');
DEFINE('_XMAP_TAB_SITEMAPS',			'Карта');
DEFINE('_XMAP_MSG_NO_SITEMAPS',			'Нет созданных карт');
DEFINE('_XMAP_MSG_LOADING_SETTINGS',	'Загрузка настроек...');
DEFINE('_XMAP_MSG_ERROR_LOADING_SITEMAP','Ошибка. Невозможно загрузить карту');
DEFINE('_XMAP_MSG_ERROR_SAVE_PROPERTY',	'Ошибка. Невозможно сохранить приоритет.');
DEFINE('_XMAP_MSG_ERROR_CLEAN_CACHE',	'Ошибка. Невозможно очистить кэш карты.');
DEFINE('_XMAP_ERROR_DELETE_DEFAULT',	'Невозможно удалить карту, используемую по умолчанию!');
DEFINE('_XMAP_MSG_CACHE_CLEANED',		'Кэш карты очищен!');
DEFINE('_XMAP_CHARSET',					'utf-8');
DEFINE('_XMAP_SITEMAP_ID',				'Идентификатор карты');
DEFINE('_SAVE_SITEMAP',				'Создать новую карту сайта');
DEFINE('_XMAP_NAME_NEW_SITEMAP',		'Новая карта');
DEFINE('_XMAP_SITEMAP_SET_DEFAULT',		'По умолчанию');
DEFINE('_SETTINGS',				'Настройки');
DEFINE('_XMAP_CLEAR_CACHE',				'Очистить кэш');
DEFINE('_XMAP_MOVEUP_MENU',		'Выше');
DEFINE('_XMAP_MOVEDOWN_MENU',	'Ниже');
DEFINE('_SAVE_MENU',		'Добавить меню');
DEFINE('_XMAP_COPY_OF',			'Копия %s');
DEFINE('_XMAP_INFO_LAST_VISIT',	'Последнее посещение');
DEFINE('_XMAP_INFO_COUNT_VIEWS','Число посещений');
DEFINE('_XMAP_INFO_TOTAL_LINKS','Число ссылок');
DEFINE('_XMAP_CFG_URLS',		'URL карты');
DEFINE('_XMAP_XML_LINK_TIP',	'Скопируйте эту ссылку для использования в Google и Yahoo');
DEFINE('_XMAP_HTML_LINK_TIP',	'Это полный адрес карты.');
DEFINE('_XMAP_CFG_XML_MAP',		'XML  карта');
DEFINE('_XMAP_CFG_HTML_MAP',	'HTML карта');
DEFINE('_XMAP_XML_LINK',		'Googlelink');
DEFINE('_XMAP_CFG_XML_MAP_TIP',	'XML файл создаётся для поисковых систем');
DEFINE('_XMAP_LOADING', 'Загрузка...');
DEFINE('_XMAP_CACHE', 'Кэширование');
DEFINE('_XMAP_USE_CACHE', 'Использовать');
DEFINE('_CACHE_TIME', 'Время жизни кэша');
DEFINE('_XMAP_PLUGINS','Расширения');
DEFINE('_INSTALL_NEW_PLUGIN', 'Установить новое расширение');
DEFINE('_XMAP_UNKNOWN_AUTHOR','Автор неизвестен');
DEFINE('_XMAP_PLUGIN_VERSION','Версия %s');
DEFINE('_XMAP_TAB_EXTENSIONS','Расширения');
DEFINE('_XMAP_TAB_INSTALLED_EXTENSIONS','Установленные расширения');
DEFINE('_XMAP_NO_PLUGINS_INSTALLED','Расширения не установлены');
DEFINE('_HEADER_AUTHOR','Автор');
DEFINE('_XMAP_CONFIRM_DELETE_SITEMAP','Вы уверены что хотите удалить эту карту?');
DEFINE('_XMAP_CONFIRM_UNINSTALL_PLUGIN','Вы уверены что хотите удалить это расширение?');
DEFINE('_XMAP_EXT_PUBLISHED','Опубликовать');
DEFINE('_XMAP_PLUGIN_OPTIONS','Options');
DEFINE('_XMAP_EXT_INSTALLED_MSG','Расширение установлено, пожалуйста проверьте его параметры и опубликуйте.');
DEFINE('_XMAP_CONTINUE','Продолжить...');
DEFINE('_XMAP_MSG_EXCLUDE_CSS_SITEMAP','Не использовать CSS в карте');
DEFINE('_XMAP_MSG_EXCLUDE_XSL_SITEMAP','Использовать классическую XML карту');
DEFINE('_XMAP_MSG_SELECT_FOLDER','Пожалуйста, выберите каталог');
DEFINE('_XMAP_UPLOAD_PKG_FILE','Загрузка пакета расширения');
DEFINE('_UPLOAD_AND_INSTALL','Загрузить и установить');
DEFINE('_INSTALL_F_DIRECTORY','Установка из каталога');
DEFINE('_INSTALLATION_DIRECTORY','Каталог установки');
DEFINE('_XMAP_WRITEABLE','Доступен');
DEFINE('_XMAP_UNWRITEABLE','Не доступен');
DEFINE('_XMAP_INVALID_SID','Ошибка идентификатора карты');
DEFINE('_EXTENSION_NAME', 'Название расширения');
DEFINE('_PUBLICATION', 'Публикация');
DEFINE('_XMAP_PAGE',' страница');
DEFINE('_XMAP_PLUGIN_SET','Настройки расширения: ');