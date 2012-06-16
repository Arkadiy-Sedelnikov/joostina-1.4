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

// Language File for Russian
DEFINE('_BNR_CLIENT_NAME', 'Вы должны ввести имя клиента.');
DEFINE('_ABP_CL_MSCF', 'Вы должны ввести контактное имя клиента.');
DEFINE('_ABP_CL_MSEF', 'Вы должны ввести правильный адрес эл. почты клиента.');
DEFINE('_ABP_BN_MSC', 'Вы должны выбрать клиента.');
DEFINE('_ABP_BN_MSCA', 'Вы должны выбрать категорию.');
DEFINE('_ABP_BN_MSNB', 'Вы должны ввести название баннера.');
DEFINE('_ABP_BN_MSIB', 'Вы должны выбрать изображение для баннера');
DEFINE('_ABP_BN_MSUB', 'Вы должны заполнить поля URL баннера или вставить свой код.');
DEFINE('_ABP_BN_DATE', 'Дата начала публикации должна быть меньшей или равной дате окончания!');
DEFINE('_ABP_BN_REC', 'Вы должны установить дни по которым баннер должен быть видимым');
DEFINE('_ABP_SELECT_CLIENT', 'Выберите клиента');
DEFINE('_ABP_FAILED_TO_COPY', 'Ошибка при копировании %s');
DEFINE('_ABP_SELECT_ITEM_TO', 'Выберите объект для %s');
DEFINE('_ABP_CICBEBAA', 'Клиент %s в текущее время редактируется другим администратором');
DEFINE('_ABP_CDCATTATHABSR', 'Не могу удалить клиента %s, т.к. в настоящие время у него запущены баннеры.');
DEFINE('_ABP_L_PUBLISH', 'Допустить');
DEFINE('_BANNERS_MANAGEMENT', 'Управление баннерами');
DEFINE('_ABP_BANNER_NAME', 'Название баннера');
DEFINE('_ABP_CATEGORY', 'Категория');
DEFINE('_ABP_ALLCAT', '- Все категории -');
DEFINE('_ABP_IMPMADE', 'Показано');
DEFINE('_ABP_IMPLEFT', 'Осталось показов');
DEFINE('_ABP_CLICKS', 'Всего нажатий');
DEFINE('_ABP_PRCLICKS', '% Всего нажатий');
DEFINE('_ABP_CHECKED_OUT', 'Изменяется');
DEFINE('_ABP_EDIT_BANNER', 'Изменить баннер');
DEFINE('_ABP_ADD_BANNER', 'Добавить баннер');
DEFINE('_ABP_E_BANNER_NAME', 'Название баннера:');
DEFINE('_ABP_E_IMP_PURCHASED', 'Куплено показов:');
DEFINE('_ABP_E_BANNER_URL', 'Изображение:');
DEFINE('_ABP_E_CLICK_URL', 'URL для перехода:');
DEFINE('_ABP_E_CUSTOM_BANNER_CODE', 'Свой код:');
DEFINE('_ABP_E_BANNER_IMAGE', 'Изображение баннера:');
DEFINE('_ABP_YCMHAN', 'Категория должна иметь название');
DEFINE('_ABP_TIACAWTHPTA', 'Категория с таким названием уже существует, попробуйте ещё раз.');
DEFINE('_ABP_SELECT_CATEGORY', 'Выберите категорию');
DEFINE('_ABP_NEVER', ' - никогда');
DEFINE('_ABP_TCICBEBAA', 'Категория %s в настоящее время редактируется другим администратором');
DEFINE('_CHOOSE_CATEGORY_TO_REMOVE', 'Выберите категорию для удаления');
DEFINE('_ABP_CCBRATCR', 'Категория: %s не может быть удалена т.к. содержит данные');
DEFINE('_ABP_SACT', 'Выберите категорию для %s');
DEFINE('_ABP_SACT_PUB', 'опубликования');
DEFINE('_ABP_SACT_UNPUB', 'скрытия');
DEFINE('_ABP_REPEAT_TYPE', 'Тип показа');
DEFINE('_PUBLISH_INFO', 'Информация о публикации');
DEFINE('_ABP_PUB_BIC', 'Опубликован, но <u>не видим</u>');
DEFINE('_ABP_PUB_AIC', 'Опубликован и <u>видим</u>');
DEFINE('_ABP_PUB_BHF', 'Опубликован, но <u>просрочен</u>');
DEFINE('_ABP_OUB_NOT', 'Не опубликован');
DEFINE('_ABP_COITTS', 'Нажмите на иконку для изменения состояния');
DEFINE('_ABP_YMPABN', 'Вы должны ввести имя баннера');
DEFINE('_ABP_PSACLI', 'Выберите клиента.');
DEFINE('_ABP_PSACAT', 'Выберите категорию.');
DEFINE('_ABP_PSANIMG', 'Выберите рисунок.');
DEFINE('_ABP_PFITUOCCFTB', 'Заполните поля URL баннера или Свой код.');
DEFINE('_ABP_UNPUBLISHED', 'Неопубликована');
DEFINE('_ABP_ALL_DAY', 'Весь день');
DEFINE('_ABP_BANNER_CLIENT_MANAGER', 'Управление клиентами');
DEFINE('_ABP_DISPLAY_TURMA', 'На странице #');
DEFINE('_ABP_CLIENT_NAME', 'Имя клиента');
DEFINE('_ABP_CONTACT', 'Контактная имя');
DEFINE('_NONE_OF_ACTIVE_BANNERS', 'Кол-во активных баннеров');
DEFINE('_ABP_BANNERS_ATT', 'Активные');
DEFINE('_ABP_BANNERS_TER', 'Срок истек');
DEFINE('_ABP_BANNERS_NO_PUB', 'Скрытые');
DEFINE('_ABP_BANNERS_IN_ATT', 'Не активные');
DEFINE('_NONE_OF_BANNERS', 'активных');
DEFINE('_ABP_PFITCN1', 'Пожалуйста заполните  имя клиента');
DEFINE('_ABP_PFITCN2', 'Пожалуйста заполните  имя контакта');
DEFINE('_ABP_PFITCE', 'Пожалуйста заполните  контактный адрес эл. почты');
DEFINE('_ABP_EDIT_BANNER_CLIENT', '  Изменить клиента');
DEFINE('_ABP_ADD_BANNER_CLIENT', '  Добавить клиента');
DEFINE('_ABP_E_CLIENT_NAME', 'Имя клиента:');
DEFINE('_ABP_E_CONTACT_NAME', 'Контактное имя:');
DEFINE('_ABP_E_EMAIL', 'Контактный адрес эл. почты:');
DEFINE('_ABP_E_EXTRA_INFO', 'Дополнительная информация:');
DEFINE('_ABP_BANNER_CATEGORY_MANAGER', 'Управление категориями');
DEFINE('_ABP_C_CATEGORY_NAME', 'Название категории');
DEFINE('_ABP_C_NUM_OF_RECORDS', '# кол-во&nbsp;баннеров');
DEFINE('_ABP_CATEGORY_MUST_HAVE_A_NAME', 'Введите название категории');
DEFINE('_ABP_ADD_CATEGORY', 'Добавить категорию');
DEFINE('_ABP_CATEGORY_NAME', 'Название категории:');
DEFINE('_ABP_UNLIMITED', 'Неограниченно');
DEFINE('_ABP_TARGET', 'Открывать в');
DEFINE('_ABP_BORDER_VALUE', 'Значение границы');
DEFINE('_ABP_BORDER_VALUE_DESCRIPTION', 'Значение по умолчанию - Без границ');
DEFINE('_ABP_BORDER_STYLE', 'Стиль границы');
DEFINE('_ABP_BORDER_COLOR', 'Цвет границы');
DEFINE('_ABP_BORDER_COLOR_DESCRIPTION', 'Вы можете использовать название цвета или шестнадцатеричное значение (напр. black (черный) или #000000)');
DEFINE('_ABP_TOTAL_PRICE', 'Единиц / Всего');
DEFINE('_ABP_VALUE_CLICK', 'Стоимость за нажатие');
DEFINE('_ABP_CURRENCY', 'Руб.');
DEFINE('_ABP_IMP_VALUE', 'Стоимость за показ');

// Short day names
DEFINE('_ABP_SUN', 'Вс');
DEFINE('_ABP_MON', 'Пн');
DEFINE('_ABP_TUE', 'Вт');
DEFINE('_ABP_WED', 'Ср');
DEFINE('_ABP_THU', 'Чт');
DEFINE('_ABP_FRI', 'Пт');
DEFINE('_ABP_SAT', 'Сб');

// Days
DEFINE('_ABP_SUNDAY', 'Воскресенье');
DEFINE('_ABP_MONDAY', 'Понедельник');
DEFINE('_ABP_TUESDAY', 'Вторник');
DEFINE('_ABP_WEDNESDAY', 'Среда');
DEFINE('_ABP_THURSDAY', 'Четверг');
DEFINE('_ABP_FRIDAY', 'Пятница');
DEFINE('_ABP_SATURDAY', 'Суббота');

// Repeat type
DEFINE('_ABP_ALLDAYS', 'Каждый день');
DEFINE('_ABP_EACHWEEK', 'Каждую неделю');
DEFINE('_ABP_ONLYDAYS', 'Только выбранные дни');

// Repeat days
DEFINE('_MAIL_FROM', 'От');
DEFINE('_ABP_TO', 'До');

DEFINE('_ABP_EVENT_STARTDATE', 'Начальная дата');
DEFINE('_ABP_EVENT_ENDDATE', 'Конечная дата');
DEFINE('_ABP_EVENT_STARTHOURS', 'Начальное время');
DEFINE('_ABP_EVENT_ENDHOURS', 'Конечное время');
DEFINE('_ABP_EVENT_CHOOSE_WEEKDAYS', 'Дни недели');
DEFINE('_ABP_EVENT_ACCESSLEVEL', 'Уровень доступа');
DEFINE('_ABP_ERROR_IMP', 'Оценена, чтобы это было число визуализации, какая бесконечная визуализация.');
DEFINE('_ABP_ERROR_DAYS_REC', 'Установите дни, в которые баннер будет повторяться');
DEFINE('_ABP_ERROR_TIME', 'Установить расписание визуализации');
DEFINE('_ABP_ERROR_PWD', 'Установить пароль');
DEFINE('_ABP_FORM_SEND_CLIENT', 'Сообщить пароль клиенту:');
DEFINE('_ABP_OPZ_IMP', 'Опции показа');
DEFINE('_DATE_PUB', 'Даты публикации');
DEFINE('_ABP_COSTS', 'Цены');
DEFINE('_ABP_PREVIEW', 'Предпросмотр баннера');
DEFINE('_ABP_PREVIEW_NOT_DISP', 'Предпросмотр баннера, не доступен.');
DEFINE('_ABP_OTHER', 'Другое ');
DEFINE('_ABP_CATEGORY_UNPUBLISH', 'категория скрыта');
DEFINE('_ABP_CLIENT_UNPUBLISH', 'клиент скрыт');
DEFINE('_ABP_PRICE_IMPRESSION', 'Цена показа (Ед./Всего)');
DEFINE('_ABP_ALERT_BANNER_FLASH', 'Вы выбрали флеш-баннер.\\n' . 'Для того чтобы работал счетчик нажатий, Вы должны ' . 'установить ссылку к файлу .swf, соответсвующую с \\n' . '$mosConfig_live_site/index.php?option=com_artbannersplus&task=clk&id=');
DEFINE('_ABP_BANNER_IN_USE', 'Баннер %s в настоящее время изменяется другим администратором');
DEFINE('_ABP_BANNER_STATE_TOOL_TIP', 'Да: <u>активный</u> баннер<br>Нет: <u>не активный</u> баннер.');
DEFINE('_ABP_TOT', 'Всего&nbsp;Руб.');
DEFINE('_ABP_TOT_IMP_CLIC', 'Всего ( показов + нажатий )');
DEFINE('_ABP_PARZ_DAL', '( Кол-во ');
DEFINE('_ABP_RESET_CLIC_PARZ', 'Сбросить количество нажатий');
DEFINE('_ABP_SUBJECT_MAIL', 'Поместить / изменить баннер');
DEFINE('_ABP_BODY_MAIL', 'Для информации нажмите здесь: ');
DEFINE('_ABP_OPZ', 'Опции');
DEFINE('_ABP_ALLCLI', '- Все клиенты -');
DEFINE('_ABP_CONTROL_PANEL', 'Панель управления');
DEFINE('_TASK_UPLOAD_FILE', 'Загрузить файл');
DEFINE('_ABP_SELECT_FILE', 'Выберите файл для загрузки');
DEFINE('_ABP_ERROR_FILENAME', 'Файл с таким именем уже существует.');
DEFINE('_ABP_ERROR_NOT_XML_FILE', 'Файл не - xml');
DEFINE('_ABP_ERROR_LOAD_FILE', 'Не удается загрузить файл: ');
DEFINE('_NONET_EXIST_BANNER_RESTORE', 'Файл не является баннером');
DEFINE('_ABP_ERROR_NOT_EXIST_CLIENTS', 'Ошибка: в файле нет клиентов');
DEFINE('_ABP_ERROR_NOT_EXIST_CATEGORIES', 'Ошибка: в файле нет категорий');
DEFINE('_ABP_RESTORE_OK', 'Восстановите успешно завершено');
DEFINE('_ABP_IMPORT_OK', 'Импорт успешно завершен');
DEFINE('_ABP_ARCHIVE_BANNERS', 'Архивировать баннеры');
DEFINE('_ABP_RESTORE_BANNERS', 'Восстановить баннеры');
DEFINE('_ABP_ERROR_SEND_MAIL', 'Ошибка отправки сообщения с паролем по почте');
DEFINE('_ABP_LANGUAGE_MANAGER', 'Управление языком');
DEFINE('_ABP_FOLDER_BANNER', 'Баннеры');
DEFINE('_ABP_FOLDER_CLIENTS', 'Клиенты');
DEFINE('_ABP_TOTAL', 'Всего');
DEFINE('_ABP_ALT', 'Альтер. текст');
/*Перенесено из общего языкового файла*/
DEFINE('_EDIT_BANNER', 'Редактирование баннера');
DEFINE('_NEW_BANNER', 'Создание баннера');
DEFINE('_IN_CURRENT_WINDOW', 'Том же окне');
DEFINE('_IN_PARENT_WINDOW', 'Текущем окне');
DEFINE('_IN_MAIN_FRAME', 'Главном фрейме');
DEFINE('_BANNER_CLIENTS', 'Клиенты баннеров');
DEFINE('_BANNER_CATEGORIES', 'Категории баннеров');
DEFINE('_NO_BANNERS', 'Банеры не обнаружены');
DEFINE('_BANNER_COUNTER_RESETTED', 'Счётчик показа баннеров обнулён');
DEFINE('_CHECK_PUBLISH_DATE', 'Проверьте правильность ввода даты публикации');
DEFINE('_CHECK_START_PUBLICATION_DATE', 'Проверьта дату начала публикации');
DEFINE('_CHECK_END_PUBLICATION_DATE', 'Проверьта дату окончания публикации');
DEFINE('_BANNERS_PANEL', 'Панель баннеров');
DEFINE('_BANNERS_DIRECTORY_DOESNOT_EXISTS', 'Папка banners не существует');
DEFINE('_CHOOSE_BANNER_IMAGE', 'Выберите изображение для загрузки');
DEFINE('_BAD_FILENAME', 'Файл должен содержать алфавитно-числовые символы без пробелов.');
DEFINE('_FILE_ALREADY_EXISTS', 'Файл #FILENAME# уже существует в базе данных.');
DEFINE('_BANNER_UPLOAD_ERROR', 'Загрузка #FILENAME# неудачна');
DEFINE('_BANNER_UPLOAD_SUCCESS', 'Загрузка #FILENAME# в #DIRNAME# успешно завешена');
DEFINE('_UPLOAD_BANNER_FILE', 'Загрузить файл баннера');
DEFINE('_BNR_CONTACT', 'Вы должны выбрать контакт для клиента.');
DEFINE('_BNR_VALID_EMAIL', 'Адрес электронной почты клиента должен быть правильным.');
DEFINE('_BNR_CLIENT', 'Вы должны выбрать клиента,');
DEFINE('_BNR_NAME', 'Введите имя баннера.');
DEFINE('_BNR_IMAGE', 'Выберите изображения баннера.');
DEFINE('_BNR_URL', 'Вы должны ввести URL/Код баннера.');