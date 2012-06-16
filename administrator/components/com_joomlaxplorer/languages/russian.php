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
/*******************************************************************************
 ** The Russian language file for joomlaXplorer until further notice
 ** Created by AllXXX & boston from Russian Joomla! Team
 ** (c) 2006 Joom.Ru - Russian home of Joomla!
 ** Encoding: Win-1251
 *******************************************************************************/

$_VERSION = new joomlaVersion();

$GLOBALS['charset'] = 'utf-8';
$GLOBALS['text_dir'] = 'ltr'; // ('ltr' для слева направо, 'rtl' для справа налево)
$GLOBALS['date_fmt'] = 'Y/m/d H:i';
$GLOBALS['error_msg'] = array( // ошибки
	'error'                                                                     => 'ОШИБКА(И)', 'back' => 'Вернуться', // корневой каталог
	'home'                                                                      => 'Корневой каталог не существует! Проверьте настройки.', 'abovehome' =>
	'Текущий каталог не может находиться выше корневого каталога.',
	'targetabovehome'                                                           =>
	'Запрашиваемый каталог не может находиться выше корневого каталога.',
	// существовать
	'direxist'                                                                  => 'Каталог не существует', 'fileexist' =>
	'Такого файла не существует', 'itemdoesexist'                               => 'Такой объект уже существует',
	'itemexist'                                                                 => 'Такого объекта существует', 'targetexist' =>
	'Назначенного каталога не существует', 'targetdoesexist'                    =>
	'Назначенного объекта не существует', // открыть
	'opendir'                                                                   => 'Невозможно открыть каталог', 'readdir' =>
	'Невозможно прочитать каталог', // доступ
	'accessdir'                                                                 => 'Вам запрещено заходить в этот каталог', 'accessfile' =>
	'Вам запрещено использовать этот файл', 'accessitem'                        =>
	'Вам запрещено использовать этот объект', 'accessfunc'                      =>
	'Вам запрещено использовать эту функцию', 'accesstarget'                    =>
	'Вам запрещено входить в этот каталог', // действие
	'permread'                                                                  => 'Ошибка при получении прав доступа', 'permchange' =>
	'Ошибка при смене прав доступа', 'openfile'                                 => 'Ошибка при открытии файла',
	'savefile'                                                                  => 'Ошибка при сохранении файла', 'createfile' =>
	'Ошибка при создании файла', 'createdir'                                    => 'Ошибка при создании каталога',
	'uploadfile'                                                                => 'Ошибка при загрузке файла', 'copyitem' =>
	'Ошибка при копировании', 'moveitem'                                        => 'Ошибка при переименовании', 'delitem' =>
	'Ошибка при удалении', 'chpass'                                             => 'Ошибка при смене пароля', 'deluser' =>
	'Ошибка при удалении пользователя', 'adduser'                               =>
	'Ошибка при добавлении пользователя', 'saveuser'                            =>
	'Ошибка при сохранении пользователя', 'searchnothing'                       =>
	'Строка поиска не должна быть пустой', // вспомогательные команды
	'miscnofunc'                                                                => 'Функция недоступна', 'miscfilesize' =>
	'Файл превышает максимальный размер', 'miscfilepart'                        =>
	'Файл был загружен частично', 'miscnoname'                                  => 'Вы должны задать имя',
	'miscselitems'                                                              => 'Вы не выбрали объект(ы)', 'miscdelitems' =>
	"Вы уверены, что хотите удалить \"+num+\" объект(ов)?", 'miscdeluser'       =>
	"Вы уверены, что хотите удалить пользователя \'+user+\'?", 'miscnopassdiff' =>
	'Новый пароль не отличается от текущего', 'miscnopassmatch'                 =>
	'Пароли не совпадают', 'miscfieldmissed'                                    => 'Вы пропустили важное поле',
	'miscnouserpass'                                                            => 'Имя пользователя или пароль не правильны', 'miscselfremove' =>
	'Вы не можете удалить самого себя', 'miscuserexist'                         =>
	'Такой пользователь уже существует', 'miscnofinduser'                       =>
	'Невозможно найти пользователя', 'extract_noarchive'                        =>
	'Этот архив не распаковывается', 'extract_unknowntype'                      =>
	'Этот архив не поддерживается');
$GLOBALS['messages'] = array( // ссылки
	'permlink'                                                                      => 'Смена прав доступа', 'editlink' => 'Редактировать', 'downlink' =>
	'Скачать', 'uplink'                                                             => 'Вверх', 'homelink' => 'Корень', 'reloadlink' => 'Обновить',
	'copylink'                                                                      => 'Копировать', 'movelink' => 'Переместить', 'dellink' => 'Удалить',
	'comprlink'                                                                     => 'Архивировать', 'adminlink' => 'Администрирование', 'logoutlink' =>
	'Выйти', 'uploadlink'                                                           => 'Загрузить', 'searchlink' => 'Поиск', 'extractlink' =>
	'Разархивировать', 'chmodlink'                                                  => 'Смена прав (каталога(ов)/файла(ов))',
	'mossysinfolink'                                                                => $_VERSION->CMS . ' Системная информация (' . $_VERSION->CMS .
		', Сервер, PHP, mySQL)', 'logolink'                                         =>
	'открыть веб-сайт joomlaXplorer в новом окне', // список
	'nameheader'                                                                    => 'Файл', 'sizeheader' => 'Размер', 'typeheader' => 'Тип',
	'modifheader'                                                                   => 'Изменен', 'permheader' => 'Права', 'actionheader' => 'Действия',
	'pathheader'                                                                    => 'Путь', // buttons
	'btncancel'                                                                     => 'Отменить', 'btnsave' => 'Сохранить', 'btnchange' => 'Изменить',
	'btnreset'                                                                      => 'Очистить', 'btnclose' => 'Закрыть', 'btncreate' => 'Создать',
	'btnsearch'                                                                     => 'Поиск', 'btnupload' => 'Закачать', 'btncopy' => 'Копировать',
	'btnmove'                                                                       => 'Переместить', 'btnlogin' => 'Войти', 'btnlogout' => 'Выйти',
	'btnadd'                                                                        => 'Добавить', 'btnedit' => 'Редактировать', 'btnremove' => 'Удалить',
	// Сообщения, joomlaXplorer 1.3.0 и выше
	'renamelink'                                                                    => 'Переименовать', 'confirm_delete_file' =>
	'Вы уверены, что хотите удалить файл? \\n%s', 'success_delete_file'             =>
	'Удалено успешно.', 'success_rename_file'                                       =>
	'Каталог/файл %s переименован в %s.', // actions
	'actdir'                                                                        => 'Каталог', 'actperms' => 'Смена прав доступа', 'actedit' =>
	'Редактирование файла', 'actsearchresults'                                      => 'Поиск', 'actcopyitems' =>
	'Копировать объект(ы)', 'actcopyfrom'                                           => 'Копировать из /%s в /%s ',
	'actmoveitems'                                                                  => 'Переместить объект(ы)', 'actmovefrom' =>
	'Переместить из /%s в /%s ', 'actlogin'                                         => 'Войти', 'actloginheader' =>
	'Войти, чтобы начать использовать QuiXplorer', 'actadmin'                       => 'Администрирование',
	'actchpwd'                                                                      => 'Сменить пароль', 'actusers' => 'Пользователи', 'actarchive' =>
	'Архивировать объект(ы)', 'actupload'                                           => 'Закачать файл(ы)', // misc
	'miscitems'                                                                     => 'объект(ов)', 'miscfree' => 'Свободно', 'miscusername' =>
	'Пользователь', 'miscpassword'                                                  => 'Пароль', 'miscoldpass' => 'Старый пароль',
	'miscnewpass'                                                                   => 'Новый пароль', 'miscconfpass' => 'Подтвердите пароль',
	'miscconfnewpass'                                                               => 'Подтвердите новый пароль', 'miscchpass' =>
	'Поменять пароль', 'mischomedir'                                                => 'Корневой каталог', 'mischomeurl' =>
	'Домашний URL', 'miscshowhidden'                                                => 'Показывать спрятанные объекты',
	'mischidepattern'                                                               => 'Прятать файлы', 'miscperms' => 'Права', 'miscuseritems' =>
	'(имя, домашняя директория, показывать спрятанные объекты, права доступа, активен)',
	'miscadduser'                                                                   => 'добавить пользователя', 'miscedituser' =>
	'редактировать пользователя "%s"', 'miscactive'                                 => 'Активен', 'misclang' => 'Язык',
	'miscnoresult'                                                                  => 'Нет результатов', 'miscsubdirs' => 'Искать в подкаталогах',
	'miscpermnames'                                                                 => array('Только просмотр', 'Редактирование', 'Смена пароля',
		'Правка и смена пароля', 'Администратор'), 'miscyesno'                      => array('Да', 'Нет', 'Д', 'Н'), 'miscchmod' => array('Владелец', 'Группа', 'Интернет'),
	// from here all new by mic
	'miscowner'                                                                     => 'Владелец', 'miscownerdesc' =>
	'<strong>Description:</strong><br />User (UID) /<br />Group (GID)<br />Current rights:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',
	// sysinfo (new by mic)
	'simamsysinfo'                                                                  => $_VERSION->CMS . ' Информация', 'sisysteminfo' => 'О системе',
	'sibuilton'                                                                     => 'Система', 'sidbversion' => 'Версия базы данных', 'siphpversion' =>
	'Версия PHP', 'siphpupdate'                                                     =>
	'INFORMATION: <span style="color: red;">The PHP version you use is <strong>not</strong> actual!</span><br />To guarantee all functions and features of ' .
		$_VERSION->CMS . ' and addons,<br />you should use as minimum <strong>PHP.Version 4.3</strong>!',
	'siwebserver'                                                                   => 'Веб-сервер', 'siwebsphpif' =>
	'Интерфейс между веб-сервером и PHP', 'simamboversion'                          => 'Версия ' . $_VERSION->CMS,
	'siuseragent'                                                                   => 'Браузер (User Agent)', 'sirelevantsettings' =>
	'Важные настройки PHP', 'sisafemode'                                            => 'Эмуляция Joomla! Register Globals',
	'sibasedir'                                                                     => 'Open basedir', 'sidisplayerrors' => 'PHP Errors',
	'sishortopentags'                                                               => 'Short Open Tags', 'sifileuploads' => 'Datei Uploads',
	'simagicquotes'                                                                 => 'Magic Quotes', 'siregglobals' => 'Register Globals',
	'sioutputbuf'                                                                   => 'Output Buffer', 'sisesssavepath' => 'Session Savepath',
	'sisessautostart'                                                               => 'Session auto start', 'sixmlenabled' => 'XML enabled',
	'sizlibenabled'                                                                 => 'ZLIB enabled', 'sidisabledfuncs' => 'Non enabled functions',
	'sieditor'                                                                      => 'WYSIWYG Editor', 'siconfigfile' => 'Конфигурационный файл',
	'siphpinfo'                                                                     => 'PHP Info', 'siphpinformation' => 'Информация о PHP',
	'sipermissions'                                                                 => 'Разрешения', 'sidirperms' => 'Права доступа на каталоги',
	'sidirpermsmess'                                                                => 'Для работы ВСЕХ функций и возможностей ' . $_VERSION->CMS .
		' , ВСЕ указанные ниже каталоги должны быть доступны для записи', 'sionoff' =>
	array('Вкл.', 'Выкл.'), 'extract_warning'                                       =>
	'Вы действительно хотите разархивировать этот файл?\\n Существующие файлы будут перезаписаны!',
	'extract_success'                                                               => 'Успешно разархивировано', 'extract_failure' =>
	'Ошибка при разархивировании', 'overwrite_files'                                => 'Перезаписать файл(ы)?',
	'viewlink'                                                                      => 'Просмотр', 'actview' => 'Просмотр файла',
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'                                                               => 'Применить для подкаталогов',
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'                                                                 => 'Проверить последнюю версию',
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'                                                                   => 'Переименование каталога или файла', 'newname' => 'Новое имя',
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'                                                                     => 'Вернуться в каталог после сохранения', 'line' => 'Строка',
	'column'                                                                        => 'Столбец', 'wordwrap' =>
	'Автоматический перенос строк: (только в IE)', 'copyfile'                       =>
	'Скопировать файла в:', // Bookmarks
	'quick_jump'                                                                    => 'Быстрый переход', 'already_bookmarked' =>
	'Каталог уже существует в закладках', 'bookmark_was_added'                      =>
	'Каталог был добавлен в закладки', 'not_a_bookmark'                             =>
	'Каталог is not a bookmark.', 'bookmark_was_removed'                            =>
	'Каталог был удален из закладок', 'bookmarkfile_not_writable'                   => 'Ошибка  %s \n файл закладок "%s" \n не перезаписывается.',
	'lbl_add_bookmark'                                                              => 'Добавить каталог в закладки', 'lbl_remove_bookmark' =>
	'Удалить каталог из закладок', 'enter_alias_name'                               =>
	'Пожалуйста, введите имя закладки', 'normal_compression'                        => 'нормальное сжатие',
	'good_compression'                                                              => 'хорошее сжатие', 'best_compression' => 'лучшее сжатие',
	'no_compression'                                                                => 'без сжатия', 'creating_archive' => 'Архивирование...',
	'processed_x_files'                                                             => 'Обработано %s %s Файлы', 'ftp_login_lbl' =>
	'Введите имя и пароль для подключения к FTP серверу', 'ftp_login_name'          =>
	'Имя пользователя FTP', 'ftp_login_pass'                                        => 'Пароль FTP', 'ftp_hostname_port' =>
	'Имя хоста FTP сервера и порт <br />(Порт необязательно)', 'ftp_login_check'    =>
	'Подключение FTP серверу...', 'ftp_connection_failed'                           =>
	"Ошибка: Невозможно соединиться с FTP сервером.\n Пожалуйста проверьте поддерживает ли FTP ваш хостер.",
	'ftp_login_failed'                                                              =>
	'Ошибка подключения к FTP серверу. Пожалуйста, проверьте имя и пароль пользователя и попытайтесь снова.',
	'switch_file_mode'                                                              =>
	'Режим работы: <strong>%s</strong>. Вы можете переключиться в режим %s.',
	'symlink_target'                                                                => 'Задание символической связи', 'archive_name' =>
	'Имя файла архива', 'archive_saveToDir'                                         => 'Архив сохранён в каталог',
	'editor_simple'                                                                 => 'Простой редактор', 'editor_syntaxhighlight' =>
	'Режим редактирования с подсветкой');