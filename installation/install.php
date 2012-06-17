<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

define("_VALID_MOS", 1);

if(file_exists('../configuration.php') && filesize('../configuration.php') > 10){
	header('Location: ../index.php');
	exit();
}
/** Include common.php*/
include_once ('common.php');

$lang = 'russian';

function writableCell($folder){
	echo "<tr>";
	echo "<td class=\"item\">" . $folder . "/</td>";
	echo "<td align=\"left\">";
	echo is_writable("../$folder") ? '<b><span style="color:green">Доступен для записи </span></b>' : '<b><span style="color:#ff0000">Недоступен для записи</span></b>' . "</td>";
	echo "</tr>";
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Joostina Lotos - Web-установка. Проверка системы</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
	<link rel="shortcut icon" href="../images/favicon.ico"/>
	<link rel="stylesheet" href="install.css" type="text/css"/>
</head>
<body>

<div id="ctr" align="center">
	<form action="install1.php" method="post" name="adminForm" id="adminForm">
		<div class="install">
			<div id="header">
				<p><?php echo $version; ?></p>
				<p class="jst"><a href="http://joostina-cms.ru">Joostina Lotos CMS</a> - свободное программное обеспечение (лицензия  GNU/GPL)</p>
			</div>

			<div id="navigator">
				<h1>Установка Joostina Lotos CMS</h1>
				<ul>
					<li class="step"><strong>1</strong><span>Проверка системы</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step step-on"><strong>2</strong><span>Лицензионное соглашение</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>3</strong><span>Конфигурация базы данных</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>4</strong><span>Название сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>5</strong><span>Конфигурация сайта</span></li>
					<li class="arrow">&nbsp;</li>
					<li class="step"><strong>6</strong><span>Завершение установки</span></li>
				</ul>
			</div>

			<div class="buttons">

				<input class="button" type="submit" name="next" value="Согласен &gt;&gt;"/>

			</div>

			<div id="wrap">
				<div class="clr"></div>
				<div class="install-text">
					Joostina - свободное программное обеспечение, распространяемое по лицензии GNU/GPL, для
					использования системы Вы должны полностью согласиться с предоставленной лицензией.
				</div>
				<div class="clr"></div>
				<div class="license-form">
					<div class="form-block" style="padding: 0px;">
						<iframe src="lang/<?php echo $lang;?>/license.php" class="license" frameborder="0"
								scrolling="auto"></iframe>
					</div>
				</div>
			</div>
			<div id="break"></div>
			<div class="clr"></div>
			<div class="ctr" id="footer"><a href="http://joostina-cms.ru" target="_blank">Joostina Lotos</a> - свободное программное обеспечение, распространяемое по лицензии GNU/GPL.</div>
		</div>
	</form>
</div>

</body>
</html>
