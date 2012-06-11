<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

define("_VALID_MOS", 1);

// Include common.php
require_once ('common.php');
// используем оригинальный класс работы с базой данных - без кэширования
require_once ('../includes/libraries/database/database.php');

$DBhostname = mosGetParam($_POST, 'DBhostname', '');
$DBuserName = mosGetParam($_POST, 'DBuserName', '');
$DBpassword = mosGetParam($_POST, 'DBpassword', '');
$DBname = mosGetParam($_POST, 'DBname', '');
$DBPrefix = mosGetParam($_POST, 'DBPrefix', '');
$sitename = htmlspecialchars(stripslashes(mosGetParam($_POST, 'sitename', '')));
$adminEmail = mosGetParam($_POST, 'adminEmail', '');
$siteUrl = mosGetParam($_POST, 'siteUrl', '');
$absolutePath = mosGetParam($_POST, 'absolutePath', '');
$adminPassword = mosGetParam($_POST, 'adminPassword', '');
$adminLogin = mosGetParam($_POST, 'adminLogin', '');
$filePerms = '';

if(get_magic_quotes_gpc()){
	$sitename = stripslashes(stripslashes($sitename));
}

if(mosGetParam($_POST, 'filePermsMode', 0))
	$filePerms = '0' . (mosGetParam($_POST, 'filePermsUserRead', 0) * 4 + mosGetParam($_POST, 'filePermsUserWrite', 0) * 2 + mosGetParam($_POST, 'filePermsUserExecute', 0)) . (mosGetParam($_POST, 'filePermsGroupRead', 0) * 4 + mosGetParam($_POST, 'filePermsGroupWrite', 0) * 2 + mosGetParam($_POST, 'filePermsGroupExecute', 0)) . (mosGetParam($_POST, 'filePermsWorldRead', 0) * 4 + mosGetParam($_POST, 'filePermsWorldWrite', 0) * 2 + mosGetParam($_POST, 'filePermsWorldExecute', 0));

$dirPerms = '';
if(mosGetParam($_POST, 'dirPermsMode', 0))
	$dirPerms = '0' . (mosGetParam($_POST, 'dirPermsUserRead', 0) * 4 + mosGetParam($_POST, 'dirPermsUserWrite', 0) * 2 + mosGetParam($_POST, 'dirPermsUserSearch', 0)) . (mosGetParam($_POST, 'dirPermsGroupRead', 0) * 4 + mosGetParam($_POST, 'dirPermsGroupWrite', 0) * 2 + mosGetParam($_POST, 'dirPermsGroupSearch', 0)) . (mosGetParam($_POST, 'dirPermsWorldRead', 0) * 4 + mosGetParam($_POST, 'dirPermsWorldWrite', 0) * 2 + mosGetParam($_POST, 'dirPermsWorldSearch', 0));

if((trim($adminEmail == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $adminEmail) == false)){
	echo "<head></head><body><form name=\"stepBack\" method=\"post\" action=\"install3.php\" id=\"stepBack\">
                <input type=\"hidden\" name=\"DBhostname\" value=\"$DBhostname\" />
                <input type=\"hidden\" name=\"DBuserName\" value=\"$DBuserName\" />
                <input type=\"hidden\" name=\"DBpassword\" value=\"$DBpassword\" />
                <input type=\"hidden\" name=\"DBname\" value=\"$DBname\" />
                <input type=\"hidden\" name=\"DBPrefix\" value=\"$DBPrefix\" />
                <input type=\"hidden\" name=\"DBcreated\" value=\"1\" />
                <input type=\"hidden\" name=\"sitename\" value=\"$sitename\" />
                <input type=\"hidden\" name=\"adminEmail\" value=\"$adminEmail\" />
                <input type=\"hidden\" name=\"siteUrl\" value=\"$siteUrl\" />
                <input type=\"hidden\" name=\"absolutePath\" value=\"$absolutePath\" />
                <input type=\"hidden\" name=\"filePerms\" value=\"$filePerms\" />
                <input type=\"hidden\" name=\"dirPerms\" value=\"$dirPerms\" />
                <input type=\"hidden\" name=\"adminPassword\" value=\"$adminPassword\" />
                <input type=\"hidden\" name=\"adminLogin\" value=\"$adminLogin\" />
                </form>";
	echo "<script>alert('Вы должны указать правильный адрес e-mail Администратора!.'); document.stepBack.submit(); </script></body>";
	exit();
	return;
}

if($DBhostname && $DBuserName && $DBname){
	$configArray['DBhostname'] = $DBhostname;
	$configArray['DBuserName'] = $DBuserName;
	$configArray['DBpassword'] = $DBpassword;
	$configArray['DBname'] = $DBname;
	$configArray['DBPrefix'] = $DBPrefix;
} else{
	echo "<form name=\"stepBack\" method=\"post\" action=\"install3.php\">
                <input type=\"hidden\" name=\"DBhostname\" value=\"$DBhostname\" />
                <input type=\"hidden\" name=\"DBuserName\" value=\"$DBuserName\" />
                <input type=\"hidden\" name=\"DBpassword\" value=\"$DBpassword\" />
                <input type=\"hidden\" name=\"DBname\" value=\"$DBname\" />
                <input type=\"hidden\" name=\"DBPrefix\" value=\"$DBPrefix\" />
                <input type=\"hidden\" name=\"DBcreated\" value=\"1\" />
                <input type=\"hidden\" name=\"sitename\" value=\"$sitename\" />
                <input type=\"hidden\" name=\"adminEmail\" value=\"$adminEmail\" />
                <input type=\"hidden\" name=\"siteUrl\" value=\"$siteUrl\" />
                <input type=\"hidden\" name=\"absolutePath\" value=\"$absolutePath\" />
                <input type=\"hidden\" name=\"filePerms\" value=\"$filePerms\" />
                <input type=\"hidden\" name=\"dirPerms\" value=\"$dirPerms\" />
                <input type=\"hidden\" name=\"adminPassword\" value=\"$adminPassword\" />
                <input type=\"hidden\" name=\"adminLogin\" value=\"$adminLogin\" />
                </form>";

	echo "<script>alert('Указанные значения для БД неверны и/или пусты'); document.stepBack.submit(); </script>";
	return;
}

if($sitename){
	if(!get_magic_quotes_gpc()){
		$configArray['sitename'] = addslashes($sitename);
	} else{
		$configArray['sitename'] = $sitename;
	}
} else{
	echo "<form name=\"stepBack\" method=\"post\" action=\"install3.php\">
                <input type=\"hidden\" name=\"DBhostname\" value=\"$DBhostname\" />
                <input type=\"hidden\" name=\"DBuserName\" value=\"$DBuserName\" />
                <input type=\"hidden\" name=\"DBpassword\" value=\"$DBpassword\" />
                <input type=\"hidden\" name=\"DBname\" value=\"$DBname\" />
                <input type=\"hidden\" name=\"DBPrefix\" value=\"$DBPrefix\" />
                <input type=\"hidden\" name=\"DBcreated\" value=\"1\" />
                <input type=\"hidden\" name=\"sitename\" value=\"$sitename\" />
                <input type=\"hidden\" name=\"adminEmail\" value=\"$adminEmail\" />
                <input type=\"hidden\" name=\"siteUrl\" value=\"$siteUrl\" />
                <input type=\"hidden\" name=\"absolutePath\" value=\"$absolutePath\" />
                <input type=\"hidden\" name=\"filePerms\" value=\"$filePerms\" />
                <input type=\"hidden\" name=\"dirPerms\" value=\"$dirPerms\" />
                <input type=\"hidden\" name=\"adminPassword\" value=\"$adminPassword\" />
                <input type=\"hidden\" name=\"adminLogin\" value=\"$adminLogin\" />
                </form>";

	echo "<script>alert('Вами не указано название сайта! '); document.stepBack2.submit();</script>";
	return;
}

if(file_exists('../configuration.php')){
	$canWrite = is_writable('../configuration.php');
} else{
	$canWrite = is_writable('..');
}

if($siteUrl){
	$configArray['siteUrl'] = $siteUrl;
	// Fix for Windows
	$absolutePath = str_replace("\\\\", "/", $absolutePath);
	$configArray['absolutePath'] = $absolutePath;
	$configArray['filePerms'] = $filePerms;
	$configArray['dirPerms'] = $dirPerms;

	$config = "<?php\n";
	$config .= "\$mosConfig_offline = '0';\n";
	$config .= "\$mosConfig_host = '{$configArray['DBhostname']}';\n";
	$config .= "\$mosConfig_user = '{$configArray['DBuserName']}';\n";
	$config .= "\$mosConfig_password = '{$configArray['DBpassword']}';\n";
	$config .= "\$mosConfig_db = '{$configArray['DBname']}';\n";
	$config .= "\$mosConfig_dbprefix = '{$configArray['DBPrefix']}';\n";
	$config .= "\$mosConfig_lang = 'russian';\n";
	$config .= "\$mosConfig_live_site = '{$configArray['siteUrl']}';\n";
	$config .= "\$mosConfig_sitename = '{$configArray['sitename']}';\n";
	$config .= "\$mosConfig_shownoauth = '0';\n";
	$config .= "\$mosConfig_useractivation = '1';\n";
	$config .= "\$mosConfig_uniquemail = '1';\n";
	$config .= "\$mosConfig_offline_message = 'Сайт временно закрыт.<br />Приносим свои извинения! Пожалуйста, зайдите позже.';\n";
	$config .= "\$mosConfig_error_message = 'Сайт недоступен.<br />Пожалуйста, сообщите об этом Администратору';\n";
	$config .= "\$mosConfig_debug = '0';\n";
	$config .= "\$mosConfig_lifetime = '900';\n";
	$config .= "\$mosConfig_session_life_admin = '1800';\n";
	$config .= "\$mosConfig_session_type = '0';\n";
	$config .= "\$mosConfig_MetaDesc = 'Joostina - современная система управления содержимым динамичных сайтов и мощная система управления порталами';\n";
	$config .= "\$mosConfig_MetaKeys = 'Joostina, joostina';\n";
	$config .= "\$mosConfig_MetaTitle = '1';\n";
	$config .= "\$mosConfig_MetaAuthor = '1';\n";
	$config .= "\$mosConfig_locale = 'ru_RU.utf8';\n";
	$config .= "\$mosConfig_offset = '0';\n";
	$config .= "\$mosConfig_offset_user = '0';\n";
	$config .= "\$mosConfig_showAuthor = '1';\n";
	$config .= "\$mosConfig_showCreateDate = '1';\n";
	$config .= "\$mosConfig_showModifyDate = '0';\n";
	$config .= "\$mosConfig_tags = '0';\n";
	$config .= "\$mosConfig_global_templates = '0';\n";
	$config .= "\$mosConfig_showPrint = '1';\n";
	$config .= "\$mosConfig_showEmail = '1';\n";
	$config .= "\$mosConfig_enable_log_items = '0';\n";
	$config .= "\$mosConfig_enable_log_searches = '0';\n";
	$config .= "\$mosConfig_enable_stats = '0';\n";
	$config .= "\$mosConfig_sef = '0';\n";
	$config .= "\$mosConfig_vote = '1';\n";
	$config .= "\$mosConfig_gzip = '0';\n";
	$config .= "\$mosConfig_multipage_toc = '1';\n";
	$config .= "\$mosConfig_allowUserRegistration = '1';\n";
	$config .= "\$mosConfig_link_titles = '0';\n";
	$config .= "\$mosConfig_error_reporting = '6143';\n";
	$config .= "\$mosConfig_list_limit = '30';\n";
	$config .= "\$mosConfig_caching = '0';\n";
	$config .= "\$mosConfig_cachepath = '{$configArray['absolutePath']}/cache';\n";
	$config .= "\$mosConfig_cachetime = '900';\n";
	$config .= "\$mosConfig_mailer = 'mail';\n";
	$config .= "\$mosConfig_mailfrom = '$adminEmail';\n";
	$config .= "\$mosConfig_fromname = '{$configArray['sitename']}';\n";
	$config .= "\$mosConfig_sendmail = '/usr/sbin/sendmail';\n";
	$config .= "\$mosConfig_smtpauth = '0';\n";
	$config .= "\$mosConfig_smtpuser = '';\n";
	// boston, отключение ведения сессий на фронте
	$config .= "\$mosConfig_no_session_front = '0';\n";
	// boston, отключение RSS
	$config .= "\$mosConfig_syndicate_off = '0';\n";
	// boston, отключение тега Generetor
	$config .= "\$mosConfig_generator_off = '0';\n";
	// boston, отключение мамботов группы system
	$config .= "\$mosConfig_mmb_system_off = '0';\n";
	// boston, использование одного шаблона на весь сайт
	$config .= "\$mosConfig_one_template = '...';\n";
	// boston, отображение времени генерации страницы
	$config .= "\$mosConfig_time_generate = '0';\n";
	// boston, индексация печатной версии
	$config .= "\$mosConfig_index_print = '0';\n";
	//boston, расширенные теги индексации
	$config .= "\$mosConfig_index_tag = '0';\n";
	// boston, оптимизация таблиц бд
	$config .= "\$mosConfig_optimizetables = '0';\n";
	// boston, отключение мамботов группы content
	$config .= "\$mosConfig_mmb_content_off = '0';\n";
	// boston, использование captcha для авторизации в панели управления
	$config .= "\$mosConfig_captcha = '0';\n";
	// boston, кэширование меню панели управления
	$config .= "\$mosConfig_adm_menu_cache = '0';\n";
	// boston, расположение элементов title ( Заголовок страницы - Название сайта )
	$config .= "\$mosConfig_pagetitles_first = '1';\n";
	// boston, очистка ссылки на компонент главной страницы
	$config .= "\$mosConfig_com_frontpage_clear = '1';\n";
	// boston, корень медиа менеджера
	$config .= "\$mosConfig_media_dir = 'images/stories';\n";
	// boston, корень файлового менеджера
	$config .= "\$mosConfig_joomlaplorer_dir = null;\n";
	// boston, автоматическая публикация новостей на главной
	$config .= "\$mosConfig_auto_frontpage = '0';\n";
	// boston, уникальные идентификаторы новостей
	$config .= "\$mosConfig_uid_news = '0';\n";
	// boston, счетчик просмотров содержимого
	$config .= "\$mosConfig_content_hits = '1';\n";
	// формат даты
	$config .= "\$mosConfig_form_date = '%d.%m.%Y г.';\n";
	// формат даты и времени
	$config .= "\$mosConfig_form_date_full = '%d.%m.%Y г. %H:%M';\n";
	// разделитель для заголовка страницы
	$config .= "\$mosConfig_tseparator = ' - ';\n";
	// не удалять сессии после окончания срока существования
	$config .= "\$mosConfig_adm_session_del = '0';\n";
	// отключить favicon для значка сайта в браузере
	$config .= "\$mosConfig_disable_favicon = '0';\n";
	// часовой пояс для rss
	$config .= "\$mosConfig_feed_timeoffset = '00:00';\n";
	// использование расширенного отладчика на фронте сайта
	$config .= "\$mosConfig_front_debug = '0';\n";
	// отключение мамботов группы mainbody
	$config .= "\$mosConfig_mmb_mainbody_off = '1';\n";
	// отключение блокировок объекта
	$config .= "\$mosConfig_disable_checked_out = '0';\n";
	// отключение кнопки Помощь
	$config .= "\$mosConfig_disable_button_help = '1';\n";
	// авторизовать пользователя после подтверждения регистрации
	$config .= "\$mosConfig_auto_activ_login = '1';\n";
	// отключение условия публикации с учетом дат
	$config .= "\$mosConfig_disable_date_state = '0';\n";
	// отключение контроля уровня доступа к содержимому
	$config .= "\$mosConfig_disable_access_control = '0';\n";
	// сжатие css и js файлов
	$config .= "\$mosConfig_gz_js_css = '0';\n";
	// визуальный редактор для css и html
	$config .= "\$mosConfig_codepress = '0';\n";
	// использование страницы печати из каталога текущего шаблона
	$config .= "\$mosConfig_custom_print = '0';\n";

	$config .= "\$mosConfig_smtppass = '';\n";
	$config .= "\$mosConfig_smtphost = 'localhost';\n";
	$config .= "\$mosConfig_back_button = '1';\n";
	$config .= "\$mosConfig_item_navigation = '1';\n";
	$config .= "\$mosConfig_secret = '" . mosMakePassword(16) . "';\n";
	$config .= "\$mosConfig_pagetitles = '1';\n";
	$config .= "\$mosConfig_readmore = '1';\n";
	$config .= "\$mosConfig_hits = '1';\n";
	$config .= "\$mosConfig_icons = '1';\n";
	$config .= "\$mosConfig_favicon = 'favicon.ico';\n";
	$config .= "\$mosConfig_fileperms = '" . $configArray['filePerms'] . "';\n";
	$config .= "\$mosConfig_dirperms = '" . $configArray['dirPerms'] . "';\n";
	$config .= "\$mosConfig_helpurl = 'http://wiki.joostinadev.ru/';\n";
	$config .= "\$mosConfig_multilingual_support = '0';\n";
	$config .= "\$mosConfig_editor = 'elrte';\n";
	$config .= "\$mosConfig_admin_expired = '1';\n";
	$config .= "\$mosConfig_frontend_login = '1';\n";
	$config .= "\$mosConfig_frontend_userparams = '1';\n";

	//Joostina ver. 1.3
	$config .= "\$mosConfig_admin_redirect_options = '0';\n";
	$config .= "\$mosConfig_admin_redirect_path = '404.html';\n";
	$config .= "\$mosConfig_admin_secure_code = 'admin';\n";
	$config .= "\$mosConfig_admin_bad_auth = '5';\n";
	$config .= "\$mosConfig_cache_handler = 'file';\n";
	$config .= "\$mosConfig_cache_key = '" . time() . "';\n";
	$config .= "\$mosConfig_enable_admin_secure_code = '0';\n";
	$config .= "\$mosConfig_author_name = '4';\n";
	$config .= "\$mosConfig_mmb_ajax_starts_off = '0';\n";

	$config .= "setlocale (LC_TIME, \$mosConfig_locale);\n";
	$config .= "?>";

	if($canWrite && ($fp = fopen("../configuration.php", "w"))){
		fputs($fp, $config, strlen($config));
		fclose($fp);
	} else{
		$canWrite = false;
	} // if

	//Joostina ver. 1.4
	// Отправка информации о сайте на центральный сервер
	$date_send_server = time() + (30 * 24 * 60 * 60);
	$info_server = "<?php\n";
	$info_server .= "\$date_send_server = '" . $date_send_server . "';\n";

	if($fp = fopen("../jserver.php", "w")){
		fputs($fp, $info_server, strlen($info_server));
		fclose($fp);
	}

	$salt = mosMakePassword(16);
	$crypt = md5($adminPassword . $salt);
	$cryptpass = $crypt . ':' . $salt;

	$database = new database($DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix);
	$nullDate = $database->getNullDate();

	// создание администратора
	$installdate = date('Y-m-d H:i:s');
	$adminLogin = $database->getEscaped($adminLogin);
	$query = "INSERT INTO `#__users` VALUES (62, 'Administrator', '$adminLogin', '$adminEmail', '$cryptpass', 'Super Administrator', 0, 1, 25, '$installdate', '$nullDate', '', '',0, '')";
	$database->setQuery($query);
	$database->query();
	// добавить ARO (Access Request Object)
	$query = "INSERT INTO `#__core_acl_aro` VALUES (10,'users','62',0,'Administrator',0)";
	$database->setQuery($query);
	$database->query();
	// add the map between the ARO and the Group
	$query = "INSERT INTO `#__core_acl_groups_aro_map` VALUES (25,'',10)";
	$database->setQuery($query);
	$database->query();

	// chmod files and directories if desired
	$chmod_report = "Права доступа к файлам и каталогам не изменены.";
	if($filePerms != '' || $dirPerms != ''){
		$mosrootfiles = array('administrator', 'cache', 'components', 'images', 'language', 'mambots', 'media', 'modules', 'templates', 'configuration.php');
		$filemode = null;
		if($filePerms != '')
			$filemode = octdec($filePerms);
		$dirmode = null;
		if($dirPerms != '')
			$dirmode = octdec($dirPerms);
		$chmodOk = true;
		foreach($mosrootfiles as $file){
			if(!mosChmodRecursive($absolutePath . '/' . $file, $filemode, $dirmode)){
				$chmodOk = false;
			}
		}
		if($chmodOk){
			$chmod_report = 'Права доступа к файлам и каталогам успешно изменены.';
		} else{
			$chmod_report = 'Права доступа к файлам и каталогам не могут быть изменены.<br />Пожалуйста, установите CHMOD каталогов и файлов Joostina вручную.';
		}
	} // if chmod wanted
} else{
	?>
<form action="install3.php" method="post" name="stepBack3" id="stepBack3">
	<input type="hidden" name="DBhostname" value="<?php echo $DBhostname; ?>"/>
	<input type="hidden" name="DBusername" value="<?php echo $DBuserName; ?>"/>
	<input type="hidden" name="DBpassword" value="<?php echo $DBpassword; ?>"/>
	<input type="hidden" name="DBname" value="<?php echo $DBname; ?>"/>
	<input type="hidden" name="DBPrefix" value="<?php echo $DBPrefix; ?>"/>
	<input type="hidden" name="DBcreated" value="1"/>
	<input type="hidden" name="sitename" value="<?php echo $sitename; ?>"/>
	<input type="hidden" name="adminEmail" value="$adminEmail"/>
	<input type="hidden" name="siteUrl" value="$siteUrl"/>
	<input type="hidden" name="absolutePath" value="$absolutePath"/>
	<input type="hidden" name="filePerms" value="$filePerms"/>
	<input type="hidden" name="dirPerms" value="$dirPerms"/>
</form>
<script>alert('URL сайта не введен');
document.stepBack3.submit();</script>
<?php
}
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?" . ">";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Joostina Lotos - Web-установка. Установка завершена.</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" href="../images/favicon.ico"/>
	<link rel="stylesheet" href="install.css" type="text/css"/>
	<?php echo '<script language="JavaScript" src="' . $siteUrl . '/includes/js/jquery/jquery.js" type="text/javascript"></script>'; ?>
</head>
<body>

<div id="ctr" align="center">
	<form action="dummy" name="form" id="form">
		<div class="install">
			<div id="header">
				<p><?php echo $version; ?></p>

				<p class="jst"><a href="http://www.joostina.ru">Joostina Lotos CMS</a> - свободное программное обеспечение (лицензия GNU/GPL)</p>
			</div>

			<div id="navigator">
				<h1>Установка Joostina Lotos CMS</h1>
				<ul>
					<li class="step"><strong>1</strong><span>Проверка системы</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>2</strong><span>Лицензионное соглашение</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>3</strong><span>Конфигурация базы данных</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>4</strong><span>Название сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>5</strong><span>Конфигурация сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step  step-on"><strong>6</strong><span>Завершение установки</span></li>
				</ul>
			</div>
			<div id="wrap">

				<div class="install-form">
					<div class="form-block">
						<div class="install-text">ПОЖАЛУЙСТА, <b> УДАЛИТЕ КАТАЛОГ 'INSTALLATION'</b>, ИНАЧЕ ВАШ САЙТ НЕ ЗАГРУЗИТСЯ</div>


						<input class="button small" type="button" name="runSite" value="Просмотр сайта"
							<?php
							if($siteUrl){
								echo "onClick=\"window.location.href='$siteUrl/' \"";
							} else{
								echo "onClick=\"window.location.href='" . $configArray['siteURL'] . "/index.php' \"";
							}
							?>/>
						&nbsp;<input class="button small" type="button" name="Admin" value="Панель управления"
						<?php
						if($siteUrl){
							echo "onClick=\"window.location.href='$siteUrl/administrator/index.php' \"";
						} else{
							echo "onClick=\"window.location.href='" . $configArray['siteURL'] . "/administrator/index.php' \"";
						}
						?>/>
						<?php
						$url = $siteUrl . '/installation/install.ajax.php?task=rminstalldir';
						$clk = 'onclick=\'$.ajax({url: "' . $url . '", beforeSend: function(response){$("#status").show("normal")}, success: function(response){$("#delbutton").val(response); $("#delbutton").click(function(){if(response == "www.joostina-cms.ru") window.location.href="http://www.joostina-cms.ru"}); $("#alert_mess").hide("fast")}, dataType: "html"}); return false;\'';
						$delbutton = '&nbsp;<input class="button small" ' . $clk . ' type="button" id="delbutton" name="delbutton" value="Удалить installation" />';
						echo $delbutton;
						?>
						<div id="status" style="display:none;"></div>
						<h2>Данные для авторизации Главного Администратора сайта:</h2>
						Логин: <b><?php echo $adminLogin;?></b> Пароль: <b><?php echo $adminPassword; ?></b>
						<?php if(!$canWrite){ ?>
						<div class="install-text">
							Ваш конфигурационный файл или нужный каталог недоступны для записи,
							или есть какая-то проблема с созданием основного конфигурационного файла.
							Вам придется загрузить этот код вручную.<br/>
							ОБЯЗАТЕЛЬНО выделите и скопируйте весь следующий код:
						</div>


						<textarea rows="5" cols="60" name="configcode" onclick="javascript:this.form.configcode.focus();this.form.configcode.select();"><?php echo htmlspecialchars($config); ?></textarea>

						<?php } ?>
						<div><?php /*echo $chmod_report*/; ?></div>

					</div>
				</div>
				<div id="break"></div>
			</div>
			<div class="clr"></div>
		</div>
	</form>
</div>
<div class="clr"></div>
<div class="ctr" id="footer"><a href="http://www.joostina-cms.ru" target="_blank">Joostina Lotos</a> - свободное программное обеспечение, распространяемое по лицензии GNU/GPL.</div>

</body>
</html>
