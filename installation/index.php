<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
define('_VALID_MOS', 1);


if (file_exists('../configuration.php') && filesize('../configuration.php') > 10) {
	header("Location: ../index.php");
	exit();
}
require ('../includes/globals.php');

/** подключаем common.php */
include_once ('common.php');

$sp = ini_get('session.save_path');

echo '<?xml version="1.0" encoding="utf-8"?' . '>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Joostina - Web-установка. Проверка системы</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		<link rel="shortcut icon" href="../images/favicon.ico" />
		<link rel="stylesheet" href="install.css" type="text/css" />
	</head>
	<body>

		<div id="ctr" align="center">
			<div class="install">

				<div id="header">
					<p><?php echo $version; ?></p>
					<p class="jst"><a href="http://www.joostina.ru">Joostina</a> - свободное программное обеспечение, распространяемое по лицензии GNU/GPL.</p>
				</div>

				<div id="navigator">
					<big>Установка Joostina CMS</big>
					<ul>
						<li class="step step-on"><strong>1</strong><span>Проверка системы</span></li>
						<li class="arrow">&nbsp;</li>
						<li class="step"><strong>2</strong><span>Лицензионное соглашение</span></li>
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
					<input type="button" class="button small" value="Проверить снова" onclick="window.location=window.location" />
					<input name="Button2" type="submit" class="button" value="Далее >>" onclick="window.location='install.php';" />
				</div>

				<div id="wrap">

					<h1>Проверка настроек сервера: </h1>
					<div class="install-text">
						Если на сервере имеются настройки, способные привести к ошибкам во время установки или работы Joostina, то на этой странице они будут отмечены <b>
							<font color="red">красным цветом</font></b>.
						Для полноценной и беспроблемной работы системы рекомендуем исправить все необходимые настройки.
						<div class="ctr"></div>
					</div>
					<div class="install-form">
						<div class="form-block">
							<table class="content">
								<tr>
									<td class="item">Версия PHP >= 5.0.0</td>
									<td align="left">
										<?php echo phpversion() < '5.0' ? '<b><font color="red">Нет</font></b>' : '<b><font color="green">Да</font></b>'; ?>
									</td>
								</tr>
								<tr>
									<td>&nbsp; - поддержка zlib-сжатия</td>
									<td align="left">
										<?php echo extension_loaded('zlib') ? '<b><font color="green">Доступна</font></b>' : '<b><font color="red">Недоступна</font></b>'; ?>    </td>
								</tr>
								<tr>    <td>    &nbsp; - поддержка XML     </td>
									<td align="left">
										<?php echo extension_loaded('xml') ? '<b><font color="green">Доступна</font></b>' : '<b><font color="red">Недоступна</font></b>'; ?>    </td>
								</tr>
								<tr>    <td>    &nbsp; - поддержка MySQL     </td>
									<td align="left">
										<?php echo function_exists('mysql_connect') ? '<b><font color="green">Доступна</font></b>' : '<b><font color="red">Недоступна</font></b>'; ?>    </td>
								</tr>
								<tr>
									<td valign="top" class="item">     Файл <strong>configuration.php</strong>    </td>
									<td align="left">
										<?php
										if (@file_exists('../configuration.php') && @is_writable('../configuration.php')) {
											echo '<b><font color="green">Доступен для записи</font></b>';
										} else
										if (is_writable('..')) {
											echo '<b><font color="green">Доступен для записи</font></b>';
										} else {
											echo '<b><font color="red">Недоступен для записи</font></b><br /><span class="small">Вы можете продолжать установку, значения файла конфигурации будут показаны в конце. ОБЯЗАТЕЛЬНО СОХРАНИТЕ ЕГО: скопируйте/вставьте содержимое в созданный вами файл configuration.php и загрузите на сервер!</span>';
										}
										?>    </td>
								</tr>
								<tr>
									<td class="item">Каталог для записи сессий</td>
									<td align="left" valign="top">
										<?php echo is_writable($sp) ? '<b><font color="green">Доступен для записи</font></b>' : '<b><font color="red">Недоступен для записи</font></b>'; ?>	</td>
								</tr>
								<tr>
									<td class="item" colspan="2">
										<b>
											<?php echo $sp ? $sp : 'Не установлен'; ?>
										</b>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="clr"></div>
					<?php
					$wrongSettingsTexts = array();

					if (ini_get('magic_quotes_gpc') != '1') {
						$wrongSettingsTexts[] = 'Параметр PHP magic_quotes_gpc - `OFF` вместо `ON`';
					}
					if (ini_get('register_globals') == '1') {
						$wrongSettingsTexts[] = 'Параметр PHP register_globals - `ON` вместо `OFF`';
					}

					if (count($wrongSettingsTexts)) {
						?>
						<h1>
								Проверка безопасности:
						</h1>

						<div class="install-text">
							<p>
									Следующие параметры PHP являются неоптимальными для <strong>Безопасности</strong> и их рекомендуется изменить:
							</p>
							<p>
									Пожалуйста, за дополнительной информацией обращайтесь на <a href="http://www.joostina.ru" target="_blank">официальный сайт Joostina</a>.
							</p>
							<div class="ctr"></div>
						</div>

						<div class="install-form">
							<div class="form-block" style=" border: 1px solid #cc0000; background: #ffffcc;">
								<table class="content" style=" width:355px">
									<tr>
										<td class="item">
											<ul style="margin: 0px; padding: 0px; padding-left: 5px; text-align: left; padding-bottom: 0px; list-style: none;">
												<?php
												foreach ($wrongSettingsTexts as $txt) {
													?>
													<li style="min-height: 25px; padding-bottom: 5px; padding-left: 25px; color: red; font-weight: bold; background-image: url(../includes/js/ThemeOffice/warning.png); background-repeat: no-repeat; background-position: 0px 2px;" >
														<?php
														echo $txt;
														?>
													</li>
													<?php
												}
												?>
											</ul>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="clr"></div>
						<?php
					}
					?>
					<h1>
						Рекомендуемые параметры PHP:
					</h1>

					<div class="install-text">
						&nbsp;&nbsp;Эти параметры PHP рекомендуются для полной
						совместимости с Joostina.
						<br />
						Однако, Joostina будет работать, если эти параметры не в
						полной мере соответствуют рекомендуемым.
						<div class="ctr"></div>
					</div>

					<div class="install-form">
						<div class="form-block">

							<table class="content">
								<tr>
									<td class="toggle">Директива</td>
									<td class="toggle">Рекомендовано</td>
									<td class="toggle">Установлено</td>
								</tr>
								<?php
								$php_recommended_settings = array(
									array('Safe Mode', 'safe_mode', 'OFF'),
									array('Display Errors', 'display_errors', 'ON'),
									array('File Uploads', 'file_uploads', 'ON'),
									array('Magic Quotes GPC', 'magic_quotes_gpc', 'ON'),
									array('Magic Quotes Runtime', 'magic_quotes_runtime', 'OFF'),
									array('Register Globals', 'register_globals', 'OFF'),
									array('Output Buffering', 'output_buffering', 'OFF'),
									array('Session auto start', 'session.auto_start', 'OFF')
										,);
								foreach ($php_recommended_settings as $phprec) {
									?>
									<tr>
										<td class="item">
											<?php echo $phprec[0]; ?>
										</td>
										<td class="toggle">
											<?php echo $phprec[2]; ?>
										</td>
										<td>
											<b>
												<?php
												if (get_php_setting($phprec[1]) == $phprec[2]) {
													?>
													<font color="green">
														<?php
													} else {
														?>
														<font color="red">
															<?php
														}
														echo get_php_setting($phprec[1]);
														?>
													</font>
											</b>
										</td>
									</tr>
								<?php } ?>
								<tr>
									<td class="item">PCRE UTF-8</td>
									<td class="toggle">ON</td>
									<?php if (!@preg_match('/^.$/u', 'ñ')): $failed = TRUE ?>
										<td colspan="2"><b><font color="red"><a href="http://php.net/pcre">PCRE</a> не поддерживает работу с UTF-8.</font></b></td>
									<?php elseif (!@preg_match('/^\pL$/u', 'ñ')): $failed = TRUE ?>
										<td colspan="2"><b><font color="red"><a href="http://php.net/pcre">PCRE</a> не поддерживает работу с Юникодом.</font></b></td>
									<?php else: ?>
										<td><b><font color="green">ON</font></b></td>
									<?php endif ?>
								</tr>
								<tr>
									<td class="item">mbstring</td>
									<td class="toggle">установлено</td>
									<?php if (!extension_loaded('mbstring')): ?>
										<td colspan="2"><b><font color="red"><a href="http://php.net/mbstring" target="_blank">mbstring</a> не установлено</font></b></td>
									<?php else: ?>
										<td><b><font color="green">установлено</font></b></td>
									<?php endif ?>
								</tr>
								<tr>
									<td class="item">iconv</td>
									<td class="toggle">установлено</td>
									<?php if (!extension_loaded('iconv')): ?>
										<td colspan="2"><b><font color="red"><a href="http://php.net/iconv" target="_blank">iconv</a> не установлено</font></b></td>
									<?php else: ?>
										<td><b><font color="green">установлено</font></b></td>
									<?php endif ?>
								</tr>
							</table>
						</div>
					</div>
					<div class="clr"></div>
					<!-- первая рекламная позиция // -->
					<h1>Помощь в установке:</h1>
					<div class="install-text">
								Бывает так, что при установке Joostina возникают разного рода ошибки. Ошибки бывают как системные - например красные пункты выше и ниже этого текста, так и человеческие - отсутствие опыта или просто лень. Ссылки ниже могут помочь справиться с любыми возникшими пробелами.
						<ul>
							<li><a href="http://joostinadev.ru/" target="_blank"><b>Официльный сайт проекта Joostina</b></a></li>
							<li><a href="http://joostinadev.ru/index.php?option=com_yarbbforum&Itemid=22" target="_blank"><b>Форум поддержки Joostina</b></a></li>
							<li><a href="http://wiki.joostinadev.ru/" target="_blank"><b>Техническая документация по CMS Joostina</b></a></li>
						</ul>
					</div>
					<!-- // первая рекламная позиция -->
					<div class="clr"></div>
					<h1>Расширенные характеристики сервера</h1>
					<div class="install-text">Указанные параметры сервера не являются критичными для работы, но соответствие указанным значениям придадут работе с Joostina максимальное удобство и безопасность.
					</div>
					<div class="install-form">
						<div class="form-block">
							<table class="content">
								<tr>
									<td class="toggle">Директива</td>
									<td class="toggle">Рекомендовано</td>
									<td class="toggle">Установлено</td>
								</tr>
								<?php
								$php_recommended_settings = array(
									array('allow_url_fopen', 'allow_url_fopen', 'OFF'),
									array('short_open_tag', 'short_open_tag', 'OFF'),
									array('post_max_size', 'post_max_size', '8M'),
									array('upload_max_filesize', 'upload_max_filesize', '2M'),
									array('default_socket_timeout (in sec.)', 'default_socket_timeout', '30'),
									array('max_execution_time (in sec.)', 'max_execution_time', '30'),
								);
								foreach ($php_recommended_settings as $phprec) {
									?>
									<tr>
										<td class="item"><?php echo $phprec[0]; ?></td>
										<td class="toggle"><?php echo $phprec[2]; ?></td>
										<td>
											<?php
											$act_val = ini_get($phprec[1]);
											if ($act_val == '1' || $act_val == '2' || $act_val == '') {
												if (get_php_setting($phprec[1]) == $phprec[2]) {
													?>
													<strong><font color="green">
														<?php } else { ?>
															<strong><font color="red">
																	<?php
																}
															}
															if ($act_val == '1') {
																echo 'ON';
															} elseif ($act_val == '2' || $act_val == '') {
																echo 'OFF';
															} else
																echo '<strong><font color="green">' . $act_val . '</font>';
															?>
														</font></strong>
													</td>
													</tr>
													<?php
												} // end foreach
												?>
												</table>
												</div>
												<div class="clr"></div>
												</div>
												<div id="dir_info" style="display:none;"><h1>Права доступа к файлам и каталогам:</h1>
													<div class="install-text">Для нормальной работы Joostina необходимо, чтобы на определенные файлы и каталоги были установлены права записи. Если вы видите <b><font color="red">Недоступен для записи</font></b> для некоторых файлов и каталогов, то необходимо установить на них права доступа, позволяющие перезаписывать их.</div>
												</div>
												<div class="install-form">
													<div class="form-block">
														<div class="button2" id="cr" style="width: 98%;" onclick="document.getElementById('cool_dirs').style.display=''; document.getElementById('dir_info').style.display=''; document.getElementById('cr').style.display='none';">
			Проверить права доступа к системным каталогам
														</div>
														<div class="clr">&nbsp;</div>
														<?php
// список каталогов которые необхоимо проверять на возможность записи в них
														$dirs = array(
															'administrator/backups',
															'administrator/components',
															'administrator/modules',
															'administrator/templates',
															'cache',
															'components',
															'images',
															'images/show',
															'images/stories',
															'language',
															'mambots',
															'mambots/content',
															'mambots/editors',
															'mambots/editors-xtd',
															'mambots/search',
															'mambots/system',
															'media',
															'modules',
															'templates');
														$cool_dirs = '';
														$bad_dirs = '';
														foreach ($dirs as $dir) {
															if (writableCell($dir)) {
																// каталоги в которые запись разрешена
																$cool_dirs .= '<tr><td class="item">' . $dir . '/</td><td align="right"><b><font color="green">Доступен для записи</font></b></tr>';
															} else {
																// каталоги в которые запись запрещена
																$bad_dirs .= '<tr><td class="item">' . $dir . '/</td><td align="right"><b><font color="red">Недоступен для записи</font></b></tr>';
															}
														}

														if ($bad_dirs != '') {
															echo '<table class="content">' . $bad_dirs . '</table>';
														};
														echo '<table id="cool_dirs" class="content" style="display:none;">' . $cool_dirs . '</table>';
														?>
													</div>
													<div class="clr"></div>
												</div>
												<div class="clr"></div>
												</div>
												<div class="clr"></div>
												</div>
												</div>

												</body>
												</html>
												<?php

												function get_php_setting($val) {
													$r = (ini_get($val) == '1' ? 1 : 0);
													return $r ? 'ON' : 'OFF';
												}

												function writableCell($folder, $relative = 1) {
													if ($relative) {
														return is_writable("../$folder") ? 1 : 0;
													} else {
														return is_writable("$folder") ? 1 : 0;
													}
												}

												function writableCell_old($folder, $relative = 1, $text = '') {
													$writeable = '<b><font color="green">Доступен для записи</font></b>';
													$unwriteable = '<b><font color="red">Недоступен для записи</font></b>';

													echo '<tr>';
													echo '<td class="item">' . $folder . '/</td>';
													echo '<td align="right">';
													if ($relative) {
														echo is_writable("../$folder") ? $writeable : $unwriteable;
													} else {
														echo is_writable("$folder") ? $writeable : $unwriteable;
													}
													echo '</tr>';
												}
												?>
