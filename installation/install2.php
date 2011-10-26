<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/


// Установка флага родительского файла
define("_VALID_MOS",1);

// Подключение common.php
require_once ('common.php');
require_once ('../includes/libraries/database/database.php');

$DBhostname	= trim(mosGetParam($_POST,'DBhostname',''));
$DBuserName	= trim(mosGetParam($_POST,'DBuserName',''));
$DBpassword	= trim(mosGetParam($_POST,'DBpassword',''));
$DBname		= trim(mosGetParam($_POST,'DBname',''));
$DBPrefix	= trim(mosGetParam($_POST,'DBPrefix',''));
$DBDel		= intval(mosGetParam($_POST,'DBDel',0));
$DBBackup	= intval(mosGetParam($_POST,'DBBackup',0));
$DBSample	= intval(mosGetParam($_POST,'DBSample',0));
$DBcreated	= intval(mosGetParam($_POST,'DBcreated',0));
$DBexp		= intval(mosGetParam($_POST,'DBexp',0));
$create_db	= intval(mosGetParam($_POST,'create_db',0));
$BUPrefix	= 'old_';
$configArray['sitename'] = trim(mosGetParam($_POST,'sitename',''));
$database	= null;

$lang = 'russian';

$errors = array();
if(!$DBcreated) {
	if(!$DBhostname || !$DBuserName || !$DBname) {
		db_err('stepBack3','Вами введены неверные данные о БД MySQL или не заполнены необходимые поля формы.');
	}

	if($DBPrefix == '') {
		db_err('stepBack','Необходимо ввести префикс базы данных.');
	}

	$database = new database($DBhostname,$DBuserName,$DBpassword,'','',false);
	$test = $database->getErrorMsg();

	if(!$database->getResource() ) {
		db_err('stepBack2','Введены неверные имя пользователя и пароль.');
	}

	// Does this code actually do anything???
	$configArray['DBhostname'] = $DBhostname;
	$configArray['DBuserName'] = $DBuserName;
	$configArray['DBpassword'] = $DBpassword;
	$configArray['DBname'] = $DBname;
	$configArray['DBPrefix'] = $DBPrefix;

	//Если не выбрано создание базы, пробуем соединиться с указанной
	if(mysql_select_db($DBname,$database->getResource() )){
		$sql = "USE `$DBname` ";
		$database->setQuery($sql);
		$database->query();
	}elseif($create_db==1) {
		// обработка разных версий MySQL
		$sql = "CREATE DATABASE IF NOT EXISTS `$DBname` CHARACTER SET utf8 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT COLLATE utf8_general_ci";
		$database->setQuery($sql);
		$database->query();
	}else{
		db_err('stepBack3','Подключение к указанной базе невозможно.');
	}

	$test = $database->getErrorNum();


	if($test != 0 && $test != 1007) {
		db_err('stepBack','Ошибка создания базы данных: '.$database->getErrorMsg());
	}

	unset($database);

	// создание новых параметров БД и замена существующих
	$database = new database($DBhostname,$DBuserName,$DBpassword,$DBname,$DBPrefix);

	// удаление существующих таблиц (если задано)
	if($DBDel) {
		$query = "SHOW TABLES FROM `$DBname`";
		$database->setQuery($query);
		$errors = array();
		if($tables = $database->loadResultArray()) {
			foreach($tables as $table) {
				if(strpos($table,$DBPrefix) === 0) {
					if($DBBackup) {
						$butable = str_replace($DBPrefix,$BUPrefix,$table);
						$query = "DROP TABLE IF EXISTS `$butable`";
						$database->setQuery($query);
						$database->query();
						if($database->getErrorNum()) {
							$errors[$database->getQuery()] = $database->getErrorMsg();
						}
						$query = "RENAME TABLE `$table` TO `$butable`";
						$database->setQuery($query);
						$database->query();
						if($database->getErrorNum()) {
							$errors[$database->getQuery()] = $database->getErrorMsg();
						}
					}
					$query = "DROP TABLE IF EXISTS `$table`";
					$database->setQuery($query);
					$database->query();
					if($database->getErrorNum()) {
						$errors[$database->getQuery()] = $database->getErrorMsg();
					}
				}
			}
		}
	}
	populate_db($database,'sql/joostina.sql');

	if($DBSample) {
		populate_db($database,'lang/'.$lang.'/sample_data.sql');
	}
	$DBcreated = 1;
}

function db_err($step,$alert) {
	global $DBhostname,$DBuserName,$DBpassword,$DBDel,$DBname;
	echo "<form name=\"$step\" method=\"post\" action=\"install1.php\">
	<input type=\"hidden\" name=\"DBhostname\" value=\"$DBhostname\">
	<input type=\"hidden\" name=\"DBuserName\" value=\"$DBuserName\">
	<input type=\"hidden\" name=\"DBpassword\" value=\"$DBpassword\">
	<input type=\"hidden\" name=\"DBDel\" value=\"$DBDel\">
	<input type=\"hidden\" name=\"DBname\" value=\"$DBname\">
	</form>\n";
	echo "<script>alert(\"$alert\"); document.location.href='install1.php';</script>";
	exit();
}

/**
* @param object
* @param string File name
*/
function populate_db(&$database,$sqlfile = 'joostina.sql') {
	global $errors;
	// переводим в 'правильное русло'
	$database->setQuery("SET NAMES 'utf8'");

	$database->query();
	$mqr = @get_magic_quotes_runtime();
	ini_set("magic_quotes_runtime", 0);
	$query = fread(fopen($sqlfile,'r'),filesize($sqlfile));
	ini_set("magic_quotes_runtime", $mqr);
	$pieces = split_sql($query);

	for($i = 0; $i < count($pieces); $i++) {
		$pieces[$i] = trim($pieces[$i]);
		if(!empty($pieces[$i]) && $pieces[$i] != "#") {
			$database->setQuery($pieces[$i]);
			if(!$database->query()) {
				$errors[] = array($database->getErrorMsg(),$pieces[$i]);
			}
		}
	}
}

/**
* @param string
*/
function split_sql($sql) {
	$sql = trim($sql);
	$sql = preg_replace("/\n#[^\n]*\n/","\n",$sql);

	$buffer = array();
	$ret = array();
	$in_string = false;

	for($i = 0; $i < strlen($sql) - 1; $i++) {
		if($sql[$i] == ";" && !$in_string) {
			$ret[] = substr($sql,0,$i);
			$sql = substr($sql,$i + 1);
			$i = 0;
		}

		if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
			$in_string = false;
		} elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) ||
		$buffer[0] != "\\")) {
			$in_string = $sql[$i];
		}
		if(isset($buffer[1])) {
			$buffer[0] = $buffer[1];
		}
		$buffer[1] = $sql[$i];
	}

	if(!empty($sql)) {
		$ret[] = $sql;
	}
	return ($ret);
}

$isErr = intval(count($errors));

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Joostina - Web-установка. Шаг 2 - название вашего сайта.</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="../images/favicon.ico" />
		<link rel="stylesheet" href="install.css" type="text/css" />
 <script type="text/javascript">
<!--
function check() {
	// проверка правильности заполнения формы
	var formValid = true;
	var f = document.form;
	if ( f.sitename.value == '' ) {
		alert('Введите название Вашего сайта');
		f.sitename.focus();
		formValid = false
	}
	return formValid;
}
//-->
 </script>
</head>
<body onload="document.form.sitename.focus();">

 <div id="ctr" align="center">
  <form action="install3.php" method="post" name="form" id="form" onsubmit="return check();">
   <input type="hidden" name="DBhostname" value="<?php echo $DBhostname; ?>" />
   <input type="hidden" name="DBuserName" value="<?php echo $DBuserName; ?>" />
   <input type="hidden" name="DBpassword" value="<?php echo $DBpassword; ?>" />
   <input type="hidden" name="DBname" value="<?php echo $DBname; ?>" />
   <input type="hidden" name="DBPrefix" value="<?php echo $DBPrefix; ?>" />
   <input type="hidden" name="DBcreated" value="<?php echo $DBcreated; ?>" />
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
					<li class="step step-on"><strong>4</strong><span>Название сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>5</strong><span>Конфигурация сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>6</strong><span>Завершение установки</span></li>
				</ul>				
			</div>
			
				<div class="buttons">
				<?php if(!$isErr) { ?>					
						<input class="button" type="submit" name="next" value="Далее &gt;&gt;"/>
						<?php } ?>				
				</div>

    <div id="wrap">
     <div class="far-right">
     </div>
     <div class="install-text">
      <?php if($isErr) { ?>
      Произошли ошибки при конфигурировании базы данных!<br />
      Продолжение установки НЕВОЗМОЖНО!
      <?php } else { ?>
      Название сайт используется при автоматической отправке сообщений по электронной почте и может отображаться в заголовке сайта.
      <?php } ?>
     </div>
     <div class="install-form">
      <div class="form-block">
	<?php
if($isErr) {
	echo '<tr><td colspan="2">';
	echo '<b></b>';
	echo "<br/><br /><b><font color=\"red\">Ошибки:</font></b><br />\n";
	echo '<textarea rows="20" cols="140">';
	foreach($errors as $error) {
		echo "SQL=$error[0]:\n- - - - - - - - - -\n$error[1]\n= = = = = = = = = =\n\n";
	}
	echo '</textarea>';
	echo "</td></tr>\n";
} else {
?>
       <table class="content2">
	<tr>
	 <td width="100">Название сайта</td>
	 <td align="center"><input class="inputbox" type="text" name="sitename" size="40" value="<?php echo $configArray['sitename']; ?>" /></td>
	</tr>
	<tr>
	 <td width="100">&nbsp;</td>
	 <td align="center" class="small">Например: Мой новый сайт!</td>
	</tr>
       </table>
       <?php
} // if

?>
      </div>
     </div>
     <div class="clr"></div>
     <div id="break"></div>
    </div>
    <div class="clr"></div>
  </form>
</div>
  <div class="clr"></div>
 <div class="ctr" id="footer"><a href="http://www.joostina.ru" target="_blank">Joostina</a> - свободное программное обеспечение, распространяемое по лицензии GNU/GPL.</div>
</body>
</html>
