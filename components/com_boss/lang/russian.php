<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined( '_VALID_MOS' ) or die();

//General
DEFINE( "BOSS_COMPONENT", "JoiBOSS CCK" );
DEFINE( "BOSS_ROOT_TITLE", "Каталог" );
DEFINE( "BOSS_PAGE_TITLE", "Каталог - ");
DEFINE( "BOSS_CATALOGS", "Каталоги" );
DEFINE( "BOSS_BACK_TEXT", "Назад");

//Front
DEFINE( "BOSS_FRONT_TITLE","Контент");
DEFINE( "BOSS_LAST_CONTENTS", "Последний контент");

//Rules
DEFINE( "BOSS_RULES", "Правила");
DEFINE( "BOSS_RULESREAD", "<b>Сначала прочтите ПРАВИЛА!</b>");

// Device
DEFINE( "BOSS_DEVICE", "%s&nbsp;");

//Field Type
DEFINE( "BOSS_KINDOFALL", "Доставка / Транспортировка ");
DEFINE( "BOSS_KINDOF1", "Транспортировка");
DEFINE( "BOSS_KINDOF2", "Доставка");
DEFINE( "BOSS_KINDOF1_LONG", "Транспортировка");
DEFINE( "BOSS_KINDOF2_LONG", "Доставка");

//Define use in contents list
DEFINE( "BOSS_FROM", "от ");
DEFINE( "BOSS_TO", "до ");
DEFINE ("BOSS_CONTACT_NOT_LOGGED", ">>> Авторизуйтесь для полного доступа <<<");
DEFINE( "BOSS_CONTACT", "Контакты");
DEFINE( "BOSS_CLICKONIMAGE", "Нажмите для просмотра");

//Search
DEFINE( "BOSS_BACKLINK", "Backlink");
DEFINE( "BOSS_SEARCH", "Поиск");
DEFINE( "BOSS_SEARCH_TEXT", "Поиск...");
DEFINE( "BOSS_ORDER_BY_TEXT", "Сортировать");
DEFINE( "BOSS_ORDER_BY_DEFAULT", "Сортировка по умолчанию");
DEFINE( "BOSS_ORDER_BY_DEFAULT_LONG", "Сортировка контента по умолчанию");
//List Type
DEFINE( "BOSS_DATE", "Дата");
DEFINE( "BOSS_DATE_LAST_COMMENT", "Дата последнего комментария");
DEFINE( "BOSS_DATE_FORMAT", "Формат даты");
DEFINE( "BOSS_DATE_FORMAT_DESC", "Введите формат вывода даты на фронте.");
DEFINE( "BOSS_LIST_TEXT", "Весь контент");
DEFINE( "BOSS_LIST_USER_TEXT", "Контент от");

//Form Verification
DEFINE( "_REGWARN_EMAIL", "Введите правильный E-mail");
DEFINE( "_REGWARN_CONTENT_HEADLINE", "Необходимо ввести название");
DEFINE( "_REGWARN_CONTENT_TEXT", "Введите описание");
DEFINE( "_REGWARN_CONTENT_PRICE", "Цена только числом");
DEFINE( "BOSS_IMAGETOOBIG", "Картинка великовата");

//Profile
DEFINE( "BOSS_PROFILE_NAME", "Имя, отчество");
DEFINE( "BOSS_PROFILE_PASSWORD", "<br/><h4>Изменить пароль</h4> <i>Ничего не вводите, если смена пароля не требуется</i>");
DEFINE( "BOSS_PROFILE_CONTACT", "<h4>Контакты</h4>");
DEFINE( "BOSS_UPDATE_PROFILE_SUCCESSFULL", "Профиль обновлён");

//Warning
DEFINE( "BOSS_CONTENTD_NOTALLOWED", "Извините, доступ долько для авторизованных пользователей.");
DEFINE( "BOSS_DELETE_NOTALLOWED", "Извините, но действия только для авторизованных пользователей.");
DEFINE( "BOSS_CAUTION", "ВНИМАНИЕ! ");
DEFINE( "BOSS_CAUTION_DELETE1", ", Будут удалены: ");
DEFINE( "BOSS_CAUTION_DELETE2", "<br /> Вы уверены???");
DEFINE( "BOSS_YES_DELETE", "Да, удалить!");
DEFINE( "BOSS_NO_DELETE", "Нет, не надо");
DEFINE( "BOSS_DELETE_OK", "Удалено успешно");

//Form
DEFINE( "BOSS_CONTENT_EDIT", "Редактирование");
DEFINE( "BOSS_CONTENT_WRITE", "Добавить контент");
DEFINE( "BOSS_CONTENT_DELETE", "Удалить");
DEFINE( "BOSS_CONTENT_DELETE_IMAGE", "удалить");

DEFINE( "BOSS_FORM_NAME", "Имя, отчество");
DEFINE( "BOSS_FORM_SURNAME", "Никнейм");
DEFINE( "BOSS_FORM_STREET", "Улица");
DEFINE( "BOSS_FORM_ZIP", "Почтовый индекс");
DEFINE( "BOSS_FORM_CITY", "Город");
DEFINE( "BOSS_FORM_PHONE1", "Телефон");
DEFINE( "BOSS_FORM_EMAIL", "E-mail");
DEFINE( "BOSS_FORM_EMAIL_TEXT", "&nbsp;");
DEFINE( "BOSS_FORM_CONTENT_HEADLINE", "Название");
DEFINE( "BOSS_FORM_CONTENT_TEXT", "Описание");
DEFINE( "BOSS_FORM_KINDOF", "Тип доставки");
DEFINE( "BOSS_FORM_CONTENT_PRICE", "Цена");
DEFINE( "BOSS_FORM_CONTENT_PRICE_TEXT", "$ (optional)");

DEFINE( "BOSS_FORM_SUBMIT_TEXT", "Сохранить");
DEFINE( "BOSS_FORM_CANCEL_TEXT", "Отменить");

DEFINE( "BOSS_FORM_CONTENT_IMAGE_TEXT", "Можно добавить изображения в формате JPEG/PNG/GIf, но размер их не должен превышать 200 килобайт.<br/> В случае превышения изображения будут автоматоматически обрезаны до нужного размера.");

//State
DEFINE( "BOSS_FORM_STATE", "Состояние");
DEFINE( "BOSS_STATE_4", "Новое");
DEFINE( "BOSS_STATE_3", "Отличное");
DEFINE( "BOSS_STATE_2", "Хорошее");
DEFINE( "BOSS_STATE_1", "Нормальное");
DEFINE( "BOSS_STATE_0", "Б/у");

DEFINE( "BOSS_INSERT_SUCCESSFULL_PUBLISH","Поздравляем, контент размещен!");
DEFINE( "BOSS_INSERT_SUCCESSFULL_CONFIRM","Спасибо, администратор проверит и опубликует Вашу информацию");
DEFINE( "BOSS_INSERT_SUCCESSFULL", "Поздравляем, контент добавлен.");

DEFINE( "BOSS_HEADER1", "= обязательно");
DEFINE( "BOSS_HEADER2", "= необязательно");

DEFINE( "BOSS_SHOW_OTHERS", "Смотреть контент ");

DEFINE( "BOSS_MENU_HOME","Начало");
DEFINE( "BOSS_MENU_WRITE","Новый контент");
DEFINE( "BOSS_MENU_PROFILE","Мой профиль");
DEFINE( "BOSS_MENU_USER_CONTENTS","Мой контент");
DEFINE( "BOSS_MENU_RULES","Правила");
DEFINE( "BOSS_MENU_ALL_CONTENTS","Весь контент");
DEFINE( "BOSS_NOENTRIES", "Нет контента");
DEFINE( "BOSS_FORM_CATEGORY", "Категория");
DEFINE( "BOSS_FORM_CONTENT_HEADLINE_TEXT","&nbsp;");

DEFINE("BOSS_CONFIGURATION_SAVED","Конфигурация сохранена");
DEFINE("BOSS_CATEGORIES_REORDER","Категории пересортированы");
DEFINE("BOSS_ERROR_IN_URL","Ошибка в ссылке");
DEFINE("BOSS_CATEGORY_SAVED","Категория сохранена");
DEFINE("BOSS_CATEGORIES_DELETED","Категория(и) удалены");
DEFINE("BOSS_CONTENT_SAVED","Контент сохранен");
DEFINE("BOSS_CONTENTS_DELETED","Контент удален");
DEFINE("BOSS_MAIN_PAGE","главная страница");
DEFINE("BOSS_CONFIGURATION","Конфигурация");
DEFINE("BOSS_LIST_CATEGORIES","Категории");
DEFINE("BOSS_LIST_CONTENTS","Контент");
DEFINE("BOSS_CONFIGURATION_PANEL","Панель конфигурации");
DEFINE("BOSS_CONTENTS_PER_PAGE","Контента на страницу");
DEFINE("BOSS_CONTENTS_PER_PAGE_LONG","Контента на страницу");
DEFINE("BOSS_MAX_IMAGE_SIZE","Максимальный размер изображения");
DEFINE("BOSS_MAX_IMAGE_SIZE_LONG","Максимальный размер файла изображения в байтах");
DEFINE("BOSS_MAX_IMAGE_WIDTH","Максимальная ширина");
DEFINE("BOSS_MAX_IMAGE_WIDTH_LONG","Максимальная ширина");
DEFINE("BOSS_MAX_IMAGE_HEIGHT","Максимальная высота");
DEFINE("BOSS_MAX_IMAGE_HEIGHT_LONG","Максимальная высота");
DEFINE("BOSS_MAX_THUMBNAIL_WIDTH","Максимальная ширина миниатюры");
DEFINE("BOSS_MAX_THUMBNAIL_WIDTH_LONG","Максимальная ширина миниатюры");
DEFINE("BOSS_MAX_THUMBNAIL_HEIGHT","Максимальная высота миниатюры");
DEFINE("BOSS_MAX_THUMBNAIL_HEIGHT_LONG","Максимальная высота миниатюры");
DEFINE("BOSS_AUTO_PUBLISH","Автопубликация");
DEFINE("BOSS_AUTO_PUBLISH_LONG","Предварительная проверка модератором");
DEFINE("BOSS_IMAGE_TAG","Копирайт");
DEFINE("BOSS_IMAGE_TAG_LONG","Текст, который будет накладываться на картинку");
DEFINE("BOSS_FRONTPAGE","Вступительный текст");
DEFINE("BOSS_FRONTPAGE_LONG","Текст описания каталога на главной странице");
DEFINE("BOSS_CATEGORY_ITEMS","Контент категории: ");

DEFINE("BOSS_TH_TITLE","Название");
DEFINE("BOSS_TH_FRONTPAGE","На главной");
DEFINE("BOSS_TH_FEATURED","Выделить");
DEFINE("BOSS_TH_PUBLISH","Опубликовано");
DEFINE("BOSS_TH_USER","Пользователь");
DEFINE("BOSS_TH_USERS","Пользователи");
DEFINE("BOSS_TH_CATEGORY","Категория");
DEFINE("BOSS_TH_DATE","Дата");
DEFINE("BOSS_TH_IMAGE","Изображение");
DEFINE("BOSS_TH_CONTENTS","Контент");
DEFINE("BOSS_CONTENT_EDITION","Редактирование контента");
DEFINE("BOSS_PUBLISH","Опубликовано");
DEFINE("BOSS_NO_PUBLISH","Не опубликовано");
DEFINE("BOSS_DATE_CREATED","Написано");
DEFINE("BOSS_DELAYED","Просрочено");
DEFINE("BOSS_NOT_STARTED","Не начато");
DEFINE("BOSS_CATEGORY_EDITION","Редактирование категории");
DEFINE("BOSS_TH_PARENT","Родитель");
DEFINE("BOSS_ROOT","Корень");
DEFINE("BOSS_TH_DESCRIPTION","Описание");

DEFINE("BOSS_EMAIL_UPDATE","[Contents] Обновлено: ");
DEFINE("BOSS_EMAIL_NEW","[Contents] Создано: ");
DEFINE("BOSS_EMAIL_ON_NEW","Уведомлять о новых");
DEFINE("BOSS_EMAIL_ON_NEW_LONG","Отправлять E-mail администратору при добавлении записи");
DEFINE("BOSS_EMAIL_ON_UPDATE","Уведомлять о изменении");
DEFINE("BOSS_EMAIL_ON_UPDATE_LONG","Отправлять E-mail администратору при изменении записей");

//v1.0.2
DEFINE("BOSS_NB_IMAGES","Число изображений");
DEFINE("BOSS_NB_IMAGES_LONG","Максимум изображений для загрузки");
DEFINE( "BOSS_FORM_CONTENT_PICTURES", "Изображения");
DEFINE( "BOSS_FORM_CONTENT_PICTURE", "Изображение");

DEFINE("BOSS_SHOW_CONTACT","Показать контакты");
DEFINE("BOSS_SHOW_CONTACT_LONG","Посмотреть подробные контактные данные");
DEFINE("BOSS_SHOW_CONTACT_LOGGED_ONLY","Только для авторизованных");
DEFINE("BOSS_SHOW_CONTACT_ALL","Для всех");
DEFINE("BOSS_NO_SHOW_CONTACT","Не показывать");

DEFINE("BOSS_ROOT_SUBMIT","Разрешить контент в родительских категориях");
DEFINE("BOSS_ROOT_SUBMIT_LONG","Пользователь может публиковать контент в родительских категориях (категории, которые содержат подкатегории)");
DEFINE("BOSS_ROOT_SUBMIT_ALLOWED","Разрешено");
DEFINE("BOSS_ROOT_SUBMIT_NOT_ALLOWED","Запрещено");

DEFINE("BOSS_EMPTY_CAT","Разрешить показ пустых категорий");
DEFINE("BOSS_EMPTY_CAT_ALLOW","Разрешено");
DEFINE("BOSS_EMPTY_CAT_NOT_ALLOW","Запрещено");
DEFINE("BOSS_EMPTY_CAT_LONG","Разрешает показ категорий, в которых нет контента");

//v1.2.1
DEFINE('BOSS_SEND_EMAIL_BUTTON','Отправить');
DEFINE('BOSS_EMAIL_FORM','Отправить E-mail');
DEFINE('BOSS_FORM_TITLE','Заголовок письма');
DEFINE('BOSS_FORM_EMAIL_BODY','Текст письма');
DEFINE('BOSS_EMAIL_TITLE',"Ответ : ");
DEFINE('BOSS_EMAIL_BODY','Content : ');
DEFINE('BOSS_WRITE_EMAIL','Форма E-mail');
DEFINE('BOSS_EMAIL_DISPLAY','Отображение E-mail');
DEFINE('BOSS_EMAIL_DISPLAY_LONG','Выберите как отображать E-mail для посетителей');
DEFINE('BOSS_EMAIL_DISPLAY_FORM','Форма');
DEFINE('BOSS_EMAIL_DISPLAY_IMAGE','Изображение');
DEFINE('BOSS_EMAIL_DISPLAY_LINK','Ссылка');
DEFINE('BOSS_EMAIL_SENT','Отправить письмо');

//v2
DEFINE('BOSS_FIELDS','Поля');
DEFINE('BOSS_GROUP_FIELDS','Поля в позиции');
DEFINE('BOSS_SELECT_GROUP_FIELDS','Выберите позицию для редактирования');
DEFINE('BOSS_FIELDS_REORDER','Пересортировать');
DEFINE('BOSS_FIELDS_LIST','Список полей');
DEFINE('BOSS_FIELDS_NEW','Создать поле');
DEFINE('BOSS_COLUMNS','Столббцы');
DEFINE('BOSS_CONTENT_DISPLAY','Отображение контента');
DEFINE('BOSS_EDIT_FIELD','Редактировать поля');
DEFINE('BOSS_REGWARN_NUMBER','К сожалению только цифры могут быть введены в этой области');
DEFINE('BOSS_REGWARN_ERROR','Заполните это поле');
DEFINE('BOSS_ORDER','Порядок');
DEFINE("BOSS_UPDATE_SUCCESSFULL","Обновление завершено");
DEFINE("BOSS_SELECT_ITEM_TO_BE_DELETED","Выберите пункт для удаления");
DEFINE("BOSS_ERROR_SYSTEM_FIELD","Вы не можете удалить системные поля");

DEFINE("BOSS_FIELD_TYPE","Тип: ");
DEFINE("BOSS_FIELD_TYPE_PARAMS","Дополнительные параметры поля");
DEFINE("BOSS_FIELD_NAME","Имя поля в БД (a-z): ");
DEFINE("BOSS_FIELD_TITLE","Название: ");
DEFINE("BOSS_FIELD_REQUIRED","Обязательное ");
DEFINE("BOSS_FIELD_COLUMN","Столбец?:");
DEFINE("BOSS_FIELD_COLUMN_ORDER","Порядок колонок: ");
DEFINE("BOSS_FIELD_POSITION_DISPLAY","Отображение позиции?:");
DEFINE("BOSS_FIELD_POSITION_ORDER","Сортировка позиции:");
DEFINE("BOSS_FIELD_PROFILE","Использовать в качестве поля профиля пользователя?");
DEFINE("BOSS_FIELD_SORT_OPTION","Контент может быть сортирован по этому полю?:");
DEFINE("BOSS_FIELD_SORT_DIRECTION","Направление сортировки?:");
DEFINE("BOSS_FIELD_SIZE","Размер:");
DEFINE("BOSS_FIELD_PUBLISHED","Публикация");
DEFINE("BOSS_FIELD_MAX_LENGTH","Максимальная длина:");
DEFINE("BOSS_FIELD_COLS","Столбцов:");
DEFINE("BOSS_FIELD_ROWS","Строк:");
DEFINE("BOSS_FIELD_VALUES_EXPLANATION","Используйте таблицу ниже, чтобы добавить новые значения.<br />");

DEFINE("BOSS_FIELD_ADD_VALUES","Добавить значения");
DEFINE("BOSS_FIELD_UPLOAD_FILE","Загрузить изображение");

DEFINE("BOSS_FIELD_VALUE_NAME","Название");
DEFINE("BOSS_FIELD_VALUE_VALUE","Значение");
DEFINE("BOSS_FIELD_VALUE_IMAGE","Изображение");

DEFINE("BOSS_FIELD_TEXT_BEFORE","Предварительный текст");
DEFINE("BOSS_FIELD_TEXT_BEFORE_LONG","Если вам нужно написать вводный текст, выводящийся перед полем, напишите его здесь.");
DEFINE("BOSS_FIELD_TEXT_AFTER","Заключительный текст");
DEFINE("BOSS_FIELD_TEXT_AFTER_LONG","Если вам нужно написать текст, выводящийся после вывода поля, напишите его здесь.");
DEFINE("BOSS_FIELD_TAGS_OPEN","Открывающий тег");
DEFINE("BOSS_FIELD_TAGS_OPEN_LONG","Если хотите отформатировать поле особыми тегами, то напишите здесь открывающий (открывающие) теги");
DEFINE("BOSS_FIELD_TAGS_SEPARATOR","Разделяющий тег");
DEFINE("BOSS_FIELD_TAGS_SEPARATOR_LONG","Если поле может иметь несколько значений (напимер мультичекбокс или мультиселект) напишите здесь теги, которые отделят одно значение от другого при выводе поля на фронт.");
DEFINE("BOSS_FIELD_TAGS_CLOSE","Закрывающий тег");
DEFINE("BOSS_FIELD_TAGS_CLOSE_LONG","Если вы заполнили поле Открывающий тег, то введите здесь закрывающий.");


DEFINE("BOSS_LIST_COLUMNS","Список столбцов");
DEFINE("BOSS_POSITIONS","Отображение контента");

DEFINE("BOSS_TH_NAME","Имя поля в БД");
DEFINE("BOSS_TH_TYPE","Тип");
DEFINE("BOSS_TH_REORDER","Type");
DEFINE("BOSS_TH_REQUIRED","Обязательное");

DEFINE("BOSS_NO_DISPLAY","Не отображать - Скрытое (hidden)");
DEFINE("BOSS_NOT_USED","Не использовать");

//add nopic.gif
DEFINE("BOSS_NOPIC","nopic.gif");

//JDirectory v2.1.0
DEFINE("BOSS_CONTENT_RESUBMIT","Ваш контент был повторно отправлен");
DEFINE("BOSS_VIEWS","Просмотров: %s");
DEFINE("BOSS_ORDER_HITS","Просмотров");
DEFINE("BOSS_FIELD_EDITABLE","Редактируемое с фронта?");
DEFINE("BOSS_FIELD_SEARCHABLE","Участвует в поиске?");
DEFINE("BOSS_LOGIN","Логин");
DEFINE("BOSS_LOGIN_DESCRIPTION","Для управления контентом Вы должны быть зарегистрированы.<br /> <br /> Пожалуйста, введите имя пользователя и пароль, чтобы войти в систему, или зарегистрируйтесь для получения новой учетной записи.");
DEFINE("BOSS_ADVANCED_SEARCH","Расширенный поиск");
DEFINE("BOSS_SUBMIT_BUTTON","Передать");

DEFINE("BOSS_SEF_SHOW_SEARCH","search");
DEFINE("BOSS_SEF_SHOW_RESULT","result");
DEFINE("BOSS_SEF_CONTENT","content");
DEFINE("BOSS_SEF_PROFILE","profile");
DEFINE("BOSS_SEF_EDIT","edit");
DEFINE("BOSS_SEF_SAVE","save");
DEFINE("BOSS_SEF_USER","user");
DEFINE("BOSS_SEF_MY_CONTENT","my_content");
DEFINE("BOSS_SEF_WA","write_content");
DEFINE("BOSS_SEF_UPDATE","update");
DEFINE("BOSS_SEF_SAVE_CONTENT","save_content");
DEFINE("BOSS_SEF_DELETE_CONTENT","delete_content");
DEFINE("BOSS_SEF_DELETE","delete");
DEFINE("BOSS_SEF_ALL_CONTENT","all_content");
DEFINE("EXPIRATIONS","expirations");

//JDirectory v2.1.2
DEFINE("BOSS_WAIT","Подождите ...");
DEFINE("BOSS_FIELD_DESCRIPTION","Описание");

//JDirectory v2.1.4
DEFINE("BOSS_FIELD_DISPLAY_TITLE","Отображать название");
DEFINE("BOSS_DISPLAY_DETAILS","Отображать при полном просмотре");
DEFINE("BOSS_DISPLAY_LIST","Отображать в списке");
DEFINE("BOSS_DISPLAY_LIST_AND_DETAILS","Отображать везде");
DEFINE("BOSS_YES","Да");
DEFINE("BOSS_NO","Нет");

//JDirectory v2.1.5
DEFINE("BOSS_INSTALL_SUCCESSFULL","Установка завершена");
DEFINE("BOSS_ERROR_INSTALL","Ошибка при установке");
DEFINE("BOSS_ALREADY_INSTALL","Уже установлено");
DEFINE("BOSS_INSTALL_JOOMFISH","Установить файлы расширения для Joomfish");
DEFINE("BOSS_REORDER","Пересортировать");
DEFINE("BOSS_IMAGE_DISPLAY","Отображение изображений");
DEFINE("BOSS_IMAGE_DISPLAY_DEFAULT","Открыть изображение в новом окне");
DEFINE("BOSS_IMAGE_DISPLAY_FANCY","Открыть изображение в Fancybox");
DEFINE("BOSS_IMAGE_DISPLAY_POPUP","Открыть изображение во всплывающем окне");
DEFINE("BOSS_IMAGE_DISPLAY_GALLERY","Показать изображения в галерее");
DEFINE("BOSS_MAX_CATIMAGE_WIDTH","Максимальная ширина изображений категории");
DEFINE("BOSS_MAX_CATIMAGE_WIDTH_LONG","Максимальная ширина изображений категории");
DEFINE("BOSS_MAX_CATIMAGE_HEIGHT","Максимальная высота изображений категории");
DEFINE("BOSS_MAX_CATIMAGE_HEIGHT_LONG","Максимальная высота изображений категории");
DEFINE("BOSS_MAX_CATTHUMBNAIL_WIDTH","Максимальная ширина миниатюр категории");
DEFINE("BOSS_MAX_CATTHUMBNAIL_WIDTH_LONG","Максимальная ширина миниатюр категории");
DEFINE("BOSS_MAX_CATTHUMBNAIL_HEIGHT","Максимальная высота миниатюр категории");
DEFINE("BOSS_MAX_CATTHUMBNAIL_HEIGHT_LONG","Максимальная высота миниатюр категории");

DEFINE("BOSS_TAB_GENERAL","Главное");
DEFINE("BOSS_TAB_IMAGE","Изображения");
DEFINE("BOSS_TAB_RIGHTS","Права пользователей");
DEFINE("BOSS_TAB_TEXT","Текст");
DEFINE("BOSS_CONTENT_DISPLAY_EXPLANATION","Чтобы изменить отображение контента, вам придется изменить поля и выбрать для каждого поля, где вы хотите отобразить эту информацию");

//JDirectory v2.1.6
DEFINE("BOSS_SUBMISSION_TYPE","Тип отправки");
DEFINE("BOSS_SUBMISION_WITH_ACCOUNT_CREATION","Создавать учетную запись (если необходимо) при размещении контента");
DEFINE("BOSS_SUBMISSION_ALLOWED_ONLY_FOR_REGISTERS","Учетная запись необходима для размещения контента");
DEFINE("BOSS_SUBMISSION_ALLOWED_FOR_VISITORS","Посетители могут размещать контент без создания учетных записей");
DEFINE("BOSS_WARNING_NEW_CONTENT_NO_ACCOUNT","<b>ПРЕДУПРЕЖДЕНИЕ: Вы не авторизованы!</b><br/> Вы можете опубликовать контент без входа в систему, но вы не сможете изменить или удалить его.<br/> Пожалуйста, зарегистрируйтесь и войдите в систему для полной функциональности.<br/>");
DEFINE("BOSS_SELECT_CATEGORY","-- Выберите категорию --");
DEFINE("BOSS_SELECT_CONTENT","-- Выберите контент --");
DEFINE("BOSS_SELECT","-- Выбрать --");
DEFINE("BOSS_AUTOMATIC_ACCOUNT","Используйте вашу учетную информацию или<br />введите  логин/пароль для создания новой учетной записи");
DEFINE("BOSS_BAD_PASSWORD","<b>Ошибка:</b> Имя пользователя уже существует или Вы ввели неправильный пароль для этого имени пользователя");
DEFINE("BOSS_EMAIL_ALREADY_USED","<b>Error:</b>На этот E-mail уже зарегистрирована учетная запись. Вы должны использовать другой E-mail или воспользоваться имеющейся учетной записью.");
DEFINE("BOSS_NB_CONTENTS_BY_USER","Записей от пользователя");
DEFINE("BOSS_NB_CONTENTS_BY_USER_LONG","Максимум записей от пользователя ( -1  = Неограниченно ) ");
DEFINE("BOSS_MAX_NUM_CONTENTS_REACHED","Больше добавлять нельзя. Максимум для пользователя = %s");
DEFINE("BOSS_ATTACH_FILE","Файл");

DEFINE("BOSS_TAB_CONTACT","Контакты");
DEFINE("BOSS_MESSAGE_SENT","Сообщение отправлено");
DEFINE("BOSS_MESSAGE_NOT_SENT","Ошибка отправки сообщения");
DEFINE("BOSS_CONTACT_BY_PMS","Приватные сообщения");
DEFINE("BOSS_CONTACT_BY_PMS_LONG","Позволяет связаться с автором через систему приватных сообщений, необходимо установить  компонент приватных сообщений, например JIM, Missus и проассоциировать contentsmanager мамбот");
DEFINE("BOSS_ALLOW_ATTACHMENT","Разрешить вложения");
DEFINE("BOSS_ALLOW_ATTACHMENT_LONG","Разрешить вложения в E-mail сообщениях");
DEFINE("BOSS_PMS_FORM","Связаться с <b>%s</b>");
DEFINE("BOSS_APPLY","Применить");

DEFINE('BOSS_FORM_MESSAGE_TITLE','Заголовок');
DEFINE('BOSS_FORM_MESSAGE_BODY','Сообщение');
DEFINE('BOSS_FORM_MESSAGE_WRITE','Форма написания');

//JDirectory 2.1.7
DEFINE('BOSS_PROFILE','Профиль');
DEFINE('BOSS_FULL','Полностью');
DEFINE('BOSS_DATE_FORMAT_LC',"%d-%m-%Y");

//JDirectory 2.1.9
DEFINE('BOSS_FNAME','Имя');
DEFINE('BOSS_MNAME','Фамилия');

DEFINE('BOSS_READ_MORE','Подробнее');
DEFINE('BOSS_CONFIRMATION','Подтверждение');

DEFINE('BOSS_BY',' от ');
DEFINE('BOSS_REVIEWS','Комментарии');
DEFINE('BOSS_REVIEWS_SYS','Система комментариев');
DEFINE('BOSS_REVIEWS_SYS_IN','Встроенная');
DEFINE('BOSS_REVIEWS_SYS_OUT','jComments');
DEFINE('BOSS_ADD_REVIEWS','Добавить свой комментарий');
DEFINE('BOSS_SUBMIT','Передать');
DEFINE('BOSS_GALLERY','Изображения');
DEFINE('BOSS_NUM_VOTES','голосов - %s');
DEFINE('BOSS_NUM_REVIEWS','Комментариев - %s');
DEFINE('BOSS_SUBMIT_VOTE','Проголосовать');

DEFINE('BOSS_LINK_TEXT','Текст ссылки');
DEFINE('BOSS_LINK_IMAGE','Картинка ссылки');

DEFINE('BOSS_LIST_GROUPS','Позиции шаблонов');
DEFINE('BOSS_GROUPS','Позиции шаблонов');

DEFINE('BOSS_TEMPLATE','Шаблон');
DEFINE('BOSS_TEMPLATE_LONG','Шаблон');
DEFINE('BOSS_TEMPLATE_SELECT','Выбрать шаблон');
DEFINE('BOSS_TH_REVIEW','Описание');
DEFINE('BOSS_TH_CAT_TMPL','Категория');
DEFINE('BOSS_TH_CONTENT_TMPL','Контент');
DEFINE('BOSS_TH_EDIT_FIELDS_TMPL','Поля шаблона');
DEFINE('BOSS_TH_EDIT_SOURCE_TMPL','Исходный код');
DEFINE('BOSS_TH_EDIT_SOURCE_TMPL_LONG','Редактировать исходный код файлов шаблона');
DEFINE('BOSS_EDIT_CAT_TMPL','Редактировать шаблон категории');
DEFINE('BOSS_EDIT_CONTENT_TMPL','Редактировать шаблон контента');
DEFINE('BOSS_EDIT_TMPL_FIELDS','Редактировать поля шаблона');
DEFINE('BOSS_EDIT','Редактировать');
DEFINE('BOSS_CONTENTS','Контент');
DEFINE('BOSS_GROUP_EDITION','Редактировать позицию');
DEFINE('BOSS_TH_ORDER','Порядок');

DEFINE('BOSS_VOTE_LOGIN_REQUIRED','Пожалуйста, авторизуйтесь, чтобы проголосовать');
DEFINE('BOSS_REVIEW_LOGIN_REQUIRED','Пожалуйста, авторизуйтесь, чтобы добавить комментарий');
DEFINE('BOSS_THANKS_FOR_YOUR_VOTE','Спасибо за ваш голос!');
DEFINE('BOSS_ALREADY_VOTE','Извините, вы уже оценили эту запись');
DEFINE('BOSS_BAD_CAPTCHA','Введен неправильный код безопасности');

DEFINE('BOSS_IMAGE_FIELD_VALUES_EXPLANATION',"Используйте таблицу ниже для выбора полей-картинок.<br />(Добавьте сначала поля-картинки в 'images/boss/directory_number/fields')<br/>");
DEFINE('BOSS_CHOOSE_DIRECTORY','Используется каталог ');
DEFINE('BOSS_MANAGER','Управление каталогами');
DEFINE('BOSS_LIST_DIRECTORIES','Список каталогов');
DEFINE('BOSS_DIRECTORY_NAME',"Название каталога");
DEFINE('BOSS_DIRECTORY_ID',"ID каталога");
DEFINE('BOSS_DIRECTORY_SEL',"-- Выбрать каталог --");
DEFINE('BOSS_SECURE_NEW_CONTENT','Использовать CAPTCHA при добавлении контента');
DEFINE('BOSS_CONTENT_MAMBOT','Разрешить использование мамботов');

DEFINE('BOSS_ALLOW_RATINGS','Разрешить рейтинг');
DEFINE('BOSS_RATING','Выберите рейтинг');
DEFINE('BOSS_ALLOW_COMMENTS','Разрешить комментировать');
DEFINE('BOSS_ALLOW_UNREG_COMMENTS','Разрешить комментарии и рейтинги незарегистрированным пользователям');
DEFINE('BOSS_SECURE_COMMENT','Использовать CAPTCHA');
DEFINE('BOSS_SECURE_COMMENT_LONG',"&nbsp;");
DEFINE('BOSS_SECURE_NEW_CONTENT_LONG',"&nbsp;");
DEFINE('BOSS_CONTENT_MAMBOT_LONG',"&nbsp;");
DEFINE('BOSS_TAB_RATINGS_COMMENTS','Комментарии и рейтинг');
DEFINE('BOSS_FORM_USER','Пользователь');
DEFINE('BOSS_FORM_USER_DELETED','Информация пользователя удалена');
DEFINE('BOSS_FORM_USER_SAVED','Информация пользователя сохранена');

DEFINE('BOSS_FORM_SEND_TEXT','Отправить');
DEFINE('BOSS_CMN_SORT_DESC','По возрастанию');
DEFINE('BOSS_CMN_SORT_ASC','По убыванию');
DEFINE('BOSS_REGISTER_PASS','Пароль:');
DEFINE('BOSS_REGISTER_VPASS','Подтвердите пароль:');
DEFINE('BOSS_EMAIL','E-mail');
DEFINE('BOSS_UNAME','Имя пользователя:');
DEFINE('BOSS_PASS','Пароль:');
DEFINE('BOSS_VPASS','Подтверждение пароля:');
DEFINE('BOSS_NAME','Имя:');
DEFINE('BOSS_PROMPT_UNAME','Имя пользователя:');
DEFINE('BOSS_PROMPT_EMAIL','Адрес E-mail:');
DEFINE('BOSS_VALID_AZ09',"Пожалуйста, введите корректный %s.  Без пробелов, не более %d символов и содержащий 0-9,a-z,A-Z");
DEFINE('BOSS_REGWARN_NAME','Пожалуйста, введите ваше имя.');
DEFINE('BOSS_REGWARN_UNAME','Пожалуйста, введите имя пользователя.');
DEFINE('BOSS_REGWARN_MAIL','Пожалуйста, введите корректный e-mail.');
DEFINE('BOSS_REGWARN_PASS','Пожалуйста, введите корректный пароль.  Без пробелов, не более 6 символов и содержащий 0-9,a-z,A-Z');
DEFINE('BOSS_REGWARN_VPASS1','Пожалуйста, подтвердите пароль.');
DEFINE('BOSS_REGWARN_VPASS2','Пароль и подтверждение не совпадают, попробуйте еще раз.');
DEFINE("BOSS_USERNAME",'Имя пользователя');
DEFINE("BOSS_PASSWORD",'Пароль');
DEFINE("BOSS_REMEMBER_ME",'Запомнить');
DEFINE("BOSS_LOST_PASSWORD",'Забыли пароль?');
DEFINE("BOSS_NO_ACCOUNT",'Вы не зарегистрированы?');
DEFINE("BOSS_CREATE_ACCOUNT",'Регистрация');
DEFINE("BOSS_BUTTON_LOGIN",'Авторизация');
DEFINE('BOSS_EDIT_PROFILE','Редактировать профиль');

DEFINE('BOSS_DISPLAY_FULLNAME','Отображать имя полностью?');
DEFINE('BOSS_DISPLAY_FULLNAME_LONG','Отображать Имя пользователя как Имя / Фамилия');
DEFINE("BOSS_SHOW_RSS","Показывать RSS ссылку");
DEFINE("BOSS_SHOW_RSS_LONG",""); 
DEFINE("BOSS_DOWNLOAD_FILE","Скачать файл");
DEFINE("BOSS_FILE_TOO_BIG",'Размер файла слишком велик');

DEFINE('BOSS_REGISTER_EMAIL','E-mail:');

DEFINE("BOSS_SEND_TO_FRIEND","Отправить контент другу");
DEFINE("BOSS_CMN_EMAIL","Отправить контент другу");
DEFINE("BOSS_EMAIL_FRIEND","Отправить контент другу");
DEFINE("BOSS_EMAIL_FRIEND_ADDR","E-mail друга");
DEFINE("BOSS_EMAIL_YOUR_NAME","Ваше имя");
DEFINE("BOSS_EMAIL_YOUR_MAIL","Ваш E-mail");
DEFINE("BOSS_SUBJECT_PROMPT","Тема");
DEFINE("BOSS_BUTTON_SUBMIT_MAIL","Отправить");
DEFINE("BOSS_BUTTON_CANCEL","Отмена");
DEFINE('BOSS_EMAIL_ERR_NOINFO','Вы должны указать свой адрес электронной почты и адрес электронной почты друга, чтобы отправить сообщение.');
DEFINE('BOSS_EMAIL_MSG','На следующей странице в "%s" веб-сайт был отправлен на ваш %s ( %s ).
Ознакомиться с ним можно по адресу:
%s');

DEFINE('BOSS_LIST_FIELDIMAGES','Поля-картинки');
DEFINE("BOSS_RADIOIMAGE", "Изображения");

DEFINE("BOSS_FORM_INFORMATION","Информация");
DEFINE("BOSS_SECURITY","КАПЧА");
DEFINE("BOSS_FORM_SECURITY_CODE_VERIFY","Код проверки");

DEFINE("BOSS_LIST_PLUGINS","Расширения");
DEFINE("BOSS_PLUGINS","Расширения");

DEFINE("BOSS_GROUPLIST1","GroupList1");
DEFINE("BOSS_GROUPLIST2","GroupList2");
DEFINE("BOSS_GROUPLIST3","GroupList3");
DEFINE("BOSS_GROUPLIST4","GroupList4");
//new
DEFINE( "BOSS_FORM_FIELD_CATEGORY", "Разрешить редактировать в категориях (фронт):");
DEFINE("BOSS_NAME_DIR","Название");
DEFINE("BOSS_NAME_DIR_FULL","Название каталога");
DEFINE("BOSS_NAME_ALIAS","Псевдоним");
DEFINE("BOSS_NAME_ALIAS_FULL","псевдоним для формирования ссылки");
DEFINE("BOSS_NEED_CREATE","Сначала необходимо создать хотя бы один каталог во вкладке Управление каталогами.");
DEFINE("BOSS_PARAMS","Параметры");
DEFINE( "BOSS_FORM_CATEGORY_GROUPS", "Отображать позицию в категориях:");
DEFINE( "BOSS_FORM_ALERT", "Предупреждение");
DEFINE( "BOSS_FIELDIMAGES_INSTALL", "Установить новую картинку");
DEFINE( "BOSS_FIELDIMAGES_UPLOAD", "Загрузить картинку");
DEFINE( "BOSS_FIELDIMAGES_FILE", "Файл картинки:");
DEFINE( "BOSS_FIELDIMAGES_BUTTON", "Загрузить и установить");
DEFINE('BOSS_LIST_TEMPLATES','Шаблоны');
DEFINE('BOSS_EDIT_TEMPLATE_FIELD','Редактирование позиций шаблона');
DEFINE( "BOSS_GROUP_IMG", "Расположение позиций в шаблоне");

DEFINE( "BOSS_META", "Meta + Тэги");
DEFINE( "BOSS_META_TITLE", "Meta-title");
DEFINE( "BOSS_META_TITLE_LONG", "Заголовок страницы, не более 80 символов");
DEFINE( "BOSS_META_DESC", "Meta-description");
DEFINE( "BOSS_META_DESC_LONG", "Описание страницы, не более 200 символов");
DEFINE( "BOSS_META_KEYS", "Meta-keys");
DEFINE( "BOSS_META_KEYS_LONG", "Ключевые слова через запятую");
DEFINE( "BOSS_TAGS", "Теги");
DEFINE( "BOSS_TAGS_NO", "не определены.");
DEFINE( "BOSS_TAGS_LONG", "Теги через запятую");
DEFINE( "BOSS_TAGS_HEADER", "Контент, содержащий тег");
DEFINE( "BOSS_ALPHA_HEADER", "Контент, начинающийся с");
DEFINE( "BOSS_UNREGISTERED", "Незарегистрированный пользователь");
DEFINE( "BOSS_INSTALL_NEW_PLUGIN", "Установить новый плагин");
DEFINE( "BOSS_UPLOAD_PACAGE", "Загрузка дистрибутивов");
DEFINE( "BOSS_PACAGE", "Дистрибутив: ");
DEFINE( "BOSS_UPLOAD_INSTALL", "Загрузить и установить");

DEFINE( "BOSS_CSV_HEADER", "Загрузка контента");
DEFINE( "BOSS_CSV_CONTINNUE_LONG", "Для добавления новых данных выберите загружаемый файл, категорию и нажмите &quot;Продолжить&quot;:");
DEFINE( "BOSS_CSV_CONTINNUE", "Продолжить");
DEFINE( "BOSS_CSV_FILE","Файл отсутствует");
DEFINE( "BOSS_CSV_CAT","Не выбрана категория");
DEFINE( "BOSS_CSV_REZULT", "успешно");
DEFINE( "BOSS_FILES", "Файлы");
DEFINE( "BOSS_EX_IM_HEADER", "Импорт/экспорт");
DEFINE( "BOSS_EX_HEADER", "Экспорт");
DEFINE( "BOSS_IM_HEADER", "Импорт");
DEFINE( "BOSS_IM_JOOS_HEADER", "Импорт содержимого из Джустины");
DEFINE( "BOSS_IM_JOOS_CATS", "Импортировать разделы/категории");
DEFINE( "BOSS_IM_JOOS_CONTENT", "Импортировать содержимое");
DEFINE( "BOSS_IM_JOOS_INTRO", "Имя поля BOSS куда вставлять интротекст");
DEFINE( "BOSS_IM_JOOS_FULL", "Имя поля BOSS куда вставлять полное описание");
DEFINE( "BOSS_IM_ALARM", "Внимание! Если не указать ID каталога, то создастся новый каталог со следующим по порядку ID, Если указать ID каталога, то контент этого каталога будет заменен на контент из архива.");
DEFINE( "BOSS_IM_SELECT_ARCHIV", "Нужно выбрать архив.");
DEFINE( "BOSS_IM_NOT_ARCH", "Не разархивировалось");

DEFINE( "BOSS_EX_TABLES", "Экспортировать таблицы базы данных?");
DEFINE( "BOSS_EX_CONTENT", "Экспортировать содержимое?");
DEFINE( "BOSS_EX_TMPL", "Экспортировать шаблоны?");
DEFINE( "BOSS_EX_PLUG", "Экспортировать плагины?");
DEFINE( "BOSS_EX_NAME", "Название архива");
DEFINE( "BOSS_EXPORT", "Экспорт");

DEFINE("BOSS_SELECT_CONTENT_TO_BE_DELETED","Выберите содержимое для удаления");
DEFINE("BOSS_SELECT_CONTENT_TO_BE_PUBLISH","Выберите содержимое для публикации");
DEFINE("BOSS_SELECT_TEMPLATE_TO_BE_DELETED","Выберите шаблон для удаления");
DEFINE("BOSS_ZLIB_NOT_FOUND","Библиотека zlib не найдена, установка невозможна");
DEFINE("BOSS_SELECT_PLUGIN_TO_BE_DELETED","Выберите расширение для удаления");
DEFINE("BOSS_DELETE_CATEGORY_SELECT_CHIDLS","Для удаления необходимо выбрать и все подкатегории");
DEFINE("BOSS_AUTOR","Автор");
DEFINE("BOSS_ALL","Все");
DEFINE("BOSS_USED","Используемые");
DEFINE("BOSS_NOTUSED","Неиспользуемые");
DEFINE("BOSS_USE","Использование");
//userList
DEFINE( "BOSS_DATE_LAST_VIZIT", "Дата последнего визита");
DEFINE( "BOSS_DATE_REGISTER", "Дата регистрации");
DEFINE( "BOSS_NAME_USER", "Имя пользователя");
DEFINE( "BOSS_EXTRA_FIELDS", "Дополнительные поля пользователя");
DEFINE( "BOSS_ROLE", "Группа");

DEFINE( "BOSS_FILTER", "Выберите фильтр");
DEFINE( "BOSS_FILTER_LONG", "Выберите фильтр, который будет выводиться над содержимым категорий и фильтровать это содержимое");
DEFINE( "BOSS_FILTER_ALLOW", "Участвует в фильтре содержимого?");
DEFINE( "BOSS_LAYOUT_EDIT", "Режим редактирования");
DEFINE( "BOSS_LAYOUT_MANAGE", "Режим управления");
DEFINE( "BOSS_LAYOUT_FULL", "Полный режим");

DEFINE( "BOSS_TAB_EXPIRATION", "Срок жизни контента");
DEFINE( "BOSS_EXPIRATION", "Активировать срок жизни контента");
DEFINE( "BOSS_CONTENT_DURATION", "Продолжительность жизни контента (дней)");
DEFINE( "BOSS_RECALL", "Отправить по Email напоминание о истечении срока");
DEFINE( "BOSS_RECALL_TIME", "Количество дней между началом напоминаний и истечением срока действия");
DEFINE( "BOSS_RECALL_TEXT", "Текст напоминания");
DEFINE( "BOSS_EMAIL_EXPIRATION", "Заканчивается срок публикации вашего контента - ");
DEFINE( "BOSS_EMPTY_FILENAME", "Выберите файл для установки.");
DEFINE( "BOSS_EMPTY_DIRS", "Выберите каталоги для установки.");
DEFINE( "BOSS_SELECT_PLUG_DIR", "Выберите каталоги в которые устанавливать плагин");
DEFINE('BOSS_RENEW_CONTENT_QUESTION','Ваш контент "%s" будет просрочен и удален %s');
DEFINE('BOSS_RENEW_CONTENT','Продлить публикацию');
DEFINE('BOSS_RENEW_CONTENT_MAIL','Ваш контент  "%s" скоро будет просрочен, чтобы продлить его публикацию пройдите по ссылке %s');

DEFINE( "BOSS_ACCOUNT_CREATE", "Аккаунт создан");
DEFINE( "BOSS_SETTINGS", "Настройки");
DEFINE( "BOSS_FIELDS_OPTIONS", "Выберите поле или создайте новое чтобы вывести его настойки сюда");
DEFINE( "BOSS_CH_TYPE_TIP", "<h3>Изменить тип поля.</h3><p>Чтобы изменить тип поля щелкните мышкой здесь, затем выберите новый тип поля из списка справа и, после вывода настроек поля справа вверху, сохраните их.</p>");
DEFINE( "BOSS_DELETE", "Удалить");
DEFINE( "BOSS_MOVE", "Переместить");
DEFINE( "BOSS_FIELD_GROUP_HREF", "Изменить привязку поля к позициям шаблона");
DEFINE( "BOSS_WHERE", "Где");
DEFINE( "BOSS_POZ", "Позиция");
DEFINE( "BOSS_FIELD", "Поле");
DEFINE( "BOSS_SELECT_TEMPLATE", "Выберите шаблон");
DEFINE( "BOSS_SELECT_TYPE_TEMPLATE", "Выберите шаблон категории/контента");
DEFINE( "BOSS_GROUPFIELDS_SAVED", "Привязка поля к позициям шаблона сохранена");
DEFINE( "BOSS_SELECT_TEMPLATE_TYPETPL", "Выберите шаблон и категорию или контент");
DEFINE( "BOSS_TYPETPL", "Тип шаблона");
DEFINE( "BOSS_SAVE_FIELD_ORDER", "Сохранить порядок полей");
DEFINE( "BOSS_SAVE_FIELD_POZ", "Сохранить");
DEFINE( "BOSS_SAVED_FIELD_POZ", "Привязка поля сохранена");
DEFINE( "BOSS_CONTENT_TYPES", "Типы контента");
DEFINE( "BOSS_CONTENT_TYPES_EDIT", "Редактирование типов контента");
DEFINE("BOSS_CONTENT_TYPE_SAVED","Тип контента сохранен");
DEFINE("BOSS_CONTENT_TYPE_HREF","Привязка к типам контента");
DEFINE("BOSS_ALL_CONTENT_TYPE","Все типы контента");
DEFINE("BOSS_SELECT_CONTENT_TYPE","Выберите тип создаваемого контента");
DEFINE("BOSS_SELECT_CONTENT_TYPE2","Выберите тип контента");
DEFINE("BOSS_CATEGORY_CONTENT_TYPE","Выберите предпочтительный тип контента, выводимый в категории");
DEFINE("BOSS_CATEGORIES","Категории");
DEFINE("BOSS_TPL_FIELDS_DESC","Поставьте галку лоя привязки поля к позиции шаблона, если к одной позиции шаблона привязано несколько полей, то в поле рядом с галками этой позиции введите числа для сортировки.");

DEFINE("BOSS_PLUG_PUB","Разрешить");
DEFINE("BOSS_PLUG_PUB_DESC","Разрешить использование плагина");
DEFINE("BOSS_PLUG_SEL_FIELD","Поле");
DEFINE("BOSS_PLUG_SEL_FIELD_DESC","Выберите поле по которому будет выбираться шаблон (должно быть создано заранее)");
DEFINE("BOSS_PLUG_SEL_FIELD_VAL","Значение");
DEFINE("BOSS_PLUG_SEL_FIELD_VAL_DESC","Введите значение поля при котором будет выводиться выделенный шаблон");
DEFINE("BOSS_PLUG_SAVE_OK","Настройки плагина сохранены");

DEFINE("BOSS_ALLOW_RIGHTS","Разрешить управление правами пользователей");
DEFINE("BOSS_RIGHTS","Права пользователей");
DEFINE("BOSS_RIGHTS_ADMIN","Права пользователей в панели администрирования");
DEFINE("BOSS_RIGHTS_FRONT","Права пользователей на фронте сайта");