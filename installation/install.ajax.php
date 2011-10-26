<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

// устанавливаем родительский флаг
define('_VALID_MOS',1);

// проверка файла конфигурации
if(!file_exists('../configuration.php')) {
	die('NON config file');
}

require_once ('../configuration.php');
// попытка удаления каталогу установки
if(!deldir('../installation/')) echo 'Error!';
	else
echo 'www.joostina.ru';


function deldir($dir) {
	$current_dir = opendir($dir);
	$old_umask = umask(0);
	while($entryname = readdir($current_dir)) {
		if($entryname != '.' and $entryname != '..') {
			if(is_dir($dir.$entryname)) {
				@deldir($dir.$entryname.'/');
			} else {
				@chmod($dir.$entryname,0777);
				@unlink($dir.$entryname);
			}
		}
	}
	@umask($old_umask);
	@closedir($current_dir);
	return @rmdir($dir);
}