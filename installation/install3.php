<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

define("_VALID_MOS",1);

/** Подключение common.php*/
require_once ('common.php');

$DBhostname		= mosGetParam($_POST,'DBhostname','');
$DBuserName		= mosGetParam($_POST,'DBuserName','');
$DBpassword		= mosGetParam($_POST,'DBpassword','');
$DBname			= mosGetParam($_POST,'DBname','');
$DBPrefix		= mosGetParam($_POST,'DBPrefix','');
$sitename		= htmlspecialchars(stripslashes(mosGetParam($_POST,'sitename','')));
$adminEmail		= mosGetParam($_POST,'adminEmail','');
$filePerms		= mosGetParam($_POST,'filePerms','');
$dirPerms		= mosGetParam($_POST,'dirPerms','');
$adminLogin		= mosGetParam($_POST,'adminLogin','admin');
$adminPassword	= mosGetParam($_POST,'adminPassword','');
$configArray['siteUrl']			= trim(mosGetParam($_POST,'siteUrl',''));
$configArray['absolutePath']	= trim(mosGetParam($_POST,'absolutePath',''));
if(get_magic_quotes_gpc()) {
	$configArray['absolutePath'] = stripslashes(stripslashes($configArray['absolutePath']));
	$sitename = stripslashes(stripslashes($sitename));
}


if($sitename == '') {
	echo "<form name=\"stepBack\" method=\"post\" action=\"install2.php\">
                        <input type=\"hidden\" name=\"DBhostname\" value=\"$DBhostname\">
                        <input type=\"hidden\" name=\"DBuserName\" value=\"$DBuserName\">
                        <input type=\"hidden\" name=\"DBpassword\" value=\"$DBpassword\">
                        <input type=\"hidden\" name=\"DBname\" value=\"$DBname\">
                        <input type=\"hidden\" name=\"DBPrefix\" value=\"$DBPrefix\">
                        <input type=\"hidden\" name=\"DBcreated\" value=1>
                </form>";

	echo "<script>alert('Не введено название сайта'); document.stepBack.submit();</script>";
	return;
}
$url = "";
if($configArray['siteUrl'])
	$url = $configArray['siteUrl'];
else {
	$port = ($_SERVER['SERVER_PORT'] == 80)?'':":".$_SERVER['SERVER_PORT'];
	$root = $_SERVER['SERVER_NAME'].$port.$_SERVER['PHP_SELF'];
	$root = str_replace("installation/","",$root);
	$root = str_replace("/install3.php","",$root);
	$url = "http://".$root;
}
;
$abspath = "";
if($configArray['absolutePath'])
	$abspath = $configArray['absolutePath'];
else {
	$path = getcwd();
	if(preg_match("/\/installation/i","$path"))
		$abspath = str_replace('/installation',"",$path);
	else
		$abspath = str_replace('\installation',"",$path);
};
$mode = 0;
$flags = 0644;
if($filePerms != '') {
	$mode = 1;
	$flags = octdec($filePerms);
};

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Joostina - Web-установка. Шаг 3 - конфигурация сайта</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="../images/favicon.ico" />
		<link rel="stylesheet" href="install.css" type="text/css" />
<script type="text/javascript">
<!--
function check() {
        // form validation check
        var formValid = true;
        var f = document.form;
        if ( f.siteUrl.value == '' ) {
                alert('Введите URL сайта');
                f.siteUrl.focus();
                formValid = false;
        } else if ( f.absolutePath.value == '' ) {
                alert('Введите абсолютный путь до вашего сайта');
                f.absolutePath.focus();
                formValid = false;
        } else if ( f.adminEmail.value == '' ) {
                alert('Введите E-mail Администратора сайта для связи с ним');
                f.adminEmail.focus();
                formValid = false;
        } else if ( f.adminPassword.value == '' ) {
                alert('Введите пароль вашего Администратора');
                f.adminPassword.focus();
                formValid = false;
        }

        return formValid;
}

function changeFilePermsMode(mode)
{
        if(document.getElementById) {
                switch (mode) {
                        case 0:
                                document.getElementById('filePermsFlags').style.display = 'none';
                                break;
                        default:
                                document.getElementById('filePermsFlags').style.display = '';
                } // switch
        } // if
}

function changeDirPermsMode(mode)
{
        if(document.getElementById) {
                switch (mode) {
                        case 0:
                                document.getElementById('dirPermsFlags').style.display = 'none';
                                break;
                        default:
                                document.getElementById('dirPermsFlags').style.display = '';
                } // switch
        } // if
}
//-->
 </script>
</head>
<body onload="document.form.siteUrl.focus();">

 <div id="ctr" align="center">
  <form action="install4.php" method="post" name="form" id="form" onsubmit="return check();">
   <input type="hidden" name="DBhostname" value="<?php echo $DBhostname; ?>" />
   <input type="hidden" name="DBuserName" value="<?php echo $DBuserName; ?>" />
   <input type="hidden" name="DBpassword" value="<?php echo $DBpassword; ?>" />
   <input type="hidden" name="DBname" value="<?php echo $DBname; ?>" />
   <input type="hidden" name="DBPrefix" value="<?php echo $DBPrefix; ?>" />
   <input type="hidden" name="sitename" value="<?php echo $sitename; ?>" />
   <div class="install">
	<div id="header">
				<p><?php echo $version; ?></p>
				<p class="jst"><a href="http://www.joostina.ru">Joostina</a> - свободное программное обеспечение, распространяемое по лицензии GNU/GPL.</p>
			</div>	
			
			<div id="navigator">
				<big>Установка Joostina CMS</big>				
				<ul>
					<li class="step"><strong>1</strong><span>Проверка системы</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>2</strong><span>Лицензионное соглашение</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>3</strong><span>Конфигурация базы данных</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>4</strong><span>Название сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step  step-on"><strong>5</strong><span>Конфигурация сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>6</strong><span>Завершение установки</span></li>
				</ul>				
			</div>
			
				<div class="buttons">					
						<input class="button" type="submit" name="next" value="Далее &gt;&gt;"/>				
				</div>
    <div id="wrap">
     <div class="install-text">
      <p>Если вы не уверены в правильности настроек, оставьте значения по умолчанию.<br />
      Позже Вы сможете изменить эти настройки в глобальной конфигурации сайта.</p>
     </div>
     <div class="install-form">
      <div class="form-block">
      
      
	<table class="content2" width="100%">
		<tr class="trongate-1">
			<th>URL сайта</th>
			<td>	<input class="inputbox" type="text" name="siteUrl" value="<?php echo $url; ?>" size="40"/>	</td>
		</tr>
		
		<tr class="trongate-2">
			<th>Абсолютный путь</th>
				<td><input class="inputbox" type="text" name="absolutePath" value="<?php echo $abspath; ?>" size="40" /></td>
		</tr>
		
		<tr class="trongate-1">
			<th>Ваш логин</th>
				<td>
					<input class="inputbox" type="text" name="adminLogin" value="<?php echo $adminLogin; ?>" size="40" />
					Используется как логин для авторизации главного Администратора сайта
				</td>
		</tr>
		
		<tr class="trongate-1">
			<th>Ваш E-mail</th>
				<td>
					<input class="inputbox" type="text" name="adminEmail" value="<?php echo $adminEmail; ?>" size="40" />
				Используется как адрес главного Администратора сайта
			</td>
		</tr>
		
		<tr class="trongate-2">
			<th>Пароль Администратора</th>
				<td>
					<input class="inputbox" type="text" name="adminPassword" id="adminPassword" value="<?php if($adminPassword!='') echo $adminPassword; else mosMakePassword(8); ?>" size="40"/>
					Рекомендуется использовать пароль не короче <b>6</b> символов.
			</td>
		</tr>
	
		<tr class="trongate-1">
  			<th>Права доступа к файлам</th>
  			<td>
     			
				 <table cellpadding="1" cellspacing="1" border="0">
	            <tr>
	             <td>
	              <input type="radio" id="filePermsMode0" name="filePermsMode" value="0" onclick="changeFilePermsMode(0)"<?php if(!$mode) echo ' checked="checked"'; ?>/>
	             </td>
	             <td>
	              <label for="filePermsMode0">Не менять CHMOD (использовать умолчания сервера)</label>
	             </td>
	            </tr>
	            <tr>
	             <td>
	              <input type="radio" id="filePermsMode1" name="filePermsMode" value="1" onclick="changeFilePermsMode(1)"<?php if($mode) echo ' checked="checked"'; ?>/>
	             </td>
	             <td>
	              <label for="filePermsMode1"> CHMOD файлов:</label>
	             </td>
	            </tr>
	            <tr id="filePermsFlags"<?php if(!$mode) echo ' style="display:none"'; ?>>
	             <td>&nbsp;</td>
	             <td>
	              <table cellpadding="1" cellspacing="0" border="0">
	               <tr>
	                <td>Владелец:</td>
	                <td>
	                 <input type="checkbox" id="filePermsUserRead" name="filePermsUserRead" value="1"<?php if($flags &0400) echo ' checked="checked"'; ?>/>
	                </td>
	                <td>
	                 <label for="filePermsUserRead">чтение</label>
	                </td>
	                <td>
	                 <input type="checkbox" id="filePermsUserWrite" name="filePermsUserWrite" value="1"<?php if($flags &0200) echo ' checked="checked"'; ?>/>
	                </td>
	                <td>
	                 <label for="filePermsUserWrite">запись</label>
	                </td>
	                <td>
	                 <input type="checkbox" id="filePermsUserExecute" name="filePermsUserExecute" value="1"<?php if($flags &0100) echo ' checked="checked"'; ?>/>
	                </td>
	                <td width="100%">
	                 <label for="filePermsUserExecute">выполнение</label>
	                </td>
	               </tr>
	               <tr>
	                <td>Группа:</td>
						<td><input type="checkbox" id="filePermsGroupRead" name="filePermsGroupRead" value="1"<?php if($flags &040) echo ' checked="checked"'; ?>/></td>
						<td><label for="filePermsGroupRead">чтение</label></td>
						<td><input type="checkbox" id="filePermsGroupWrite" name="filePermsGroupWrite" value="1"<?php if($flags &020) echo ' checked="checked"'; ?>/></td>
						<td><label for="filePermsGroupWrite">запись</label></td>
						<td><input type="checkbox" id="filePermsGroupExecute" name="filePermsGroupExecute" value="1"<?php if($flags &010) echo ' checked="checked"'; ?>/></td>
						<td width="100%"><label for="filePermsGroupExecute">выполнение</label></td>
	               </tr>
	               <tr>
	                <td>Все:</td>
						<td><input type="checkbox" id="filePermsWorldRead" name="filePermsWorldRead" value="1"<?php if($flags &04) echo ' checked="checked"'; ?>/></td>
						<td><label for="filePermsWorldRead">чтение</label></td>
						<td><input type="checkbox" id="filePermsWorldWrite" name="filePermsWorldWrite" value="1"<?php if($flags &02) echo ' checked="checked"'; ?>/></td>
						<td><label for="filePermsWorldWrite">запись</label></td>
						<td><input type="checkbox" id="filePermsWorldExecute" name="filePermsWorldExecute" value="1"<?php if($flags &01) echo ' checked="checked"'; ?>/></td>
						<td width="100%"><label for="filePermsWorldExecute">выполнение</label></td>
	               </tr>
	              </table>
	             </td>
	            </tr>
	           </table>
	           
  			</td>
		</tr>

        <tr>
         <?php
$mode = 0;
$flags = 0755;
if($dirPerms != '') {
	$mode = 1;
	$flags = octdec($dirPerms);
} // if

?>
         <th> Права доступа к каталогам</th>
         <td>
          
          
           <table cellpadding="1" cellspacing="1" border="0">
            <tr>
										<td><input type="radio" id="dirPermsMode0" name="dirPermsMode" value="0" onclick="changeDirPermsMode(0)"<?php if(!$mode) echo ' checked="checked"'; ?>/></td>
              <td><label for="dirPermsMode0">Не менять CHMOD (использовать умолчания сервера)</label></td>
            </tr>
            <tr>
             <td>
              <input type="radio" id="dirPermsMode1" name="dirPermsMode" value="1" onclick="changeDirPermsMode(1)"<?php if($mode) echo ' checked="checked"'; ?>/>
             </td>
             <td>
              <label for="dirPermsMode1"> CHMOD каталогов:</label>
             </td>
            </tr>
            <tr id="dirPermsFlags"<?php if(!$mode) echo ' style="display:none"'; ?>>
             <td>&nbsp;</td>
             <td>
              <table cellpadding="1" cellspacing="0" border="0">
               <tr>
                <td>Владелец:</td>
					<td><input type="checkbox" id="dirPermsUserRead" name="dirPermsUserRead" value="1"<?php if($flags &0400) echo ' checked="checked"'; ?>/></td>
					<td><label for="dirPermsUserRead">чтение</label></td>
					<td><input type="checkbox" id="dirPermsUserWrite" name="dirPermsUserWrite" value="1"<?php if($flags &0200) echo ' checked="checked"'; ?>/></td>
					<td><label for="dirPermsUserWrite">запись</label></td>
					<td><input type="checkbox" id="dirPermsUserSearch" name="dirPermsUserSearch" value="1"<?php if($flags &0100) echo ' checked="checked"'; ?>/></td>
					<td width="100%"><label for="dirPermsUserSearch">поиск</label></td>
               </tr>
               <tr>
                <td>Группа:</td>
                <td>
                 <input type="checkbox" id="dirPermsGroupRead" name="dirPermsGroupRead" value="1"<?php if($flags &040) echo ' checked="checked"'; ?>/>
                </td>
                <td>
                 <label for="dirPermsGroupRead">чтение</label>
                </td>
                <td>
                 <input type="checkbox" id="dirPermsGroupWrite" name="dirPermsGroupWrite" value="1"<?php if($flags &020) echo ' checked="checked"'; ?>/>
                </td>
                <td>
                 <label for="dirPermsGroupWrite">запись</label>
                </td>
                <td>
                 <input type="checkbox" id="dirPermsGroupSearch" name="dirPermsGroupSearch" value="1"<?php if($flags &010) echo ' checked="checked"'; ?>/>
                </td>
                <td width="100%">
                 <label for="dirPermsGroupSearch">поиск</label>
                </td>
               </tr>
               <tr>
                <td>Все:</td>
                <td>
                 <input type="checkbox" id="dirPermsWorldRead" name="dirPermsWorldRead" value="1"<?php if($flags &04) echo ' checked="checked"'; ?>/>
                </td>
                <td>
                 <label for="dirPermsWorldRead">чтение</label>
                </td>
                <td>
                 <input type="checkbox" id="dirPermsWorldWrite" name="dirPermsWorldWrite" value="1"<?php if($flags &02) echo ' checked="checked"'; ?>/>
                </td>
                <td>
                 <label for="dirPermsWorldWrite">запись</label>
                </td>
                <td>
                 <input type="checkbox" id="dirPermsWorldSearch" name="dirPermsWorldSearch" value="1"<?php if($flags &01) echo ' checked="checked"'; ?>/>
                </td>
                <td width="100%">
                 <label for="dirPermsWorldSearch">поиск</label>
                </td>
               </tr>
              </table>
             </td>
            </tr>
           </table>
           
          </td>

        </tr>
       </table>
      </div>
     </div>
     <div id="break"></div>
    </div>
    <div class="clr"></div>
   </div>
  </form>
 </div>
 <div class="clr"></div>
</body>
</html>