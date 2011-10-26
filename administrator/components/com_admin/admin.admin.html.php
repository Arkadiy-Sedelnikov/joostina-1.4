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

/**
 * @package Joostina
 * @subpackage Admin
 */

class HTML_admin_misc {
	/**
	 * Control panel
	 */
	public static function controlPanel() {
		global $mainframe;
		$path = JPATH_BASE_ADMIN.'/templates/'.JTEMPLATE.'/html/cpanel.php';
		if(file_exists($path)) {
			require $path;
		} else {
			echo '<br />';
			mosLoadAdminModules('cpanel',1);
		}
	}

	public static function get_php_setting($val,$colour = 0,$yn = 1) {
		$r = (ini_get($val) == '1'?1:0);

		if($colour) {
			if($yn) {
				$r = $r?'<span style="color: green;">ON</span>':'<span style="color: red;">OFF</span>';
			} else {
				$r = $r?'<span style="color: red;">ON</span>':'<span style="color: green;">OFF</span>';
			}

			return $r;
		} else {
			return $r?'ON':'OFF';
		}
	}

	public static function get_server_software() {
		if(isset($_SERVER['SERVER_SOFTWARE'])) {
			return $_SERVER['SERVER_SOFTWARE'];
		} else
		if(($sf = phpversion() <= '4.2.1'?getenv('SERVER_SOFTWARE'):$_SERVER['SERVER_SOFTWARE'])) {
			return $sf;
		} else {
			return 'n/a';
		}
	}

	public static function system_info($version) {
		global $mosConfig_cachepath;

		$mainframe = mosMainFrame::getInstance();
		$database = $mainframe->getDBO();

		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

		$width = 400; // width of 100%
		$tabs = new mosTabs(0);
		?>
<table class="adminheading">
	<tr>
		<th class="info"><?php echo _INFO?></th>
	</tr>
</table>
		<?php
		$tabs->startPane("sysinfo");
		$tabs->startTab(_ABOUT_JOOSTINA,"joostina-page");
		?>
<table class="adminform">
	<tr>
		<td>
			<pre>
						<?php
						include(JPATH_BASE.'/help/copyright.php');
						?>
			</pre>
		</td>
	</tr>
</table>
		<?php
		$tabs->endTab();
		$tabs->startTab(_ABOUT_SYSTEM,"system-page");
		?>
<table class="adminform">
	<tr>
		<td colspan="2">
					<?php
					// show security setting check
					josSecurityCheck();
					?>
		</td>
	</tr>
	<tr>
		<td valign="top" width="250"><strong><?php echo _SYSTEM_OS?>:</strong></td>
		<td><?php echo php_uname(); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo _DB_VERSION?>:</strong></td>
		<td><?php echo $database->getVersion(); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo _PHP_VERSION?>:</strong></td>
		<td><?php echo phpversion(); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo _APACHE_VERSION?>:</strong></td>
		<td><?php echo HTML_admin_misc::get_server_software(); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo _PHP_APACHE_INTERFACE?>:</strong></td>
		<td><?php echo php_sapi_name(); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo _JOOSTINA_VERSION?>:</strong></td>
		<td><?php echo $version->CMS.' '.$version->CMS_ver.'.'.$version->DEV_STATUS.' [ '.$version->CODENAME.' ] '.$version->RELDATE.' '.$version->RELTIME.' '.$version->RELTZ.'<br />'.$version->SUPPORT; ?></td>
	</tr>
	<tr>
		<td><strong><?php echo _BROWSER?>:</strong></td>
		<td><?php echo phpversion() <= '4.2.1'?getenv('HTTP_USER_AGENT'):$_SERVER['HTTP_USER_AGENT']; ?></td>
	</tr>
	<tr>
		<td colspan="2" style="height: 10px;">&nbsp;</td>
	</tr>
	<tr>
		<td valign="top">
			<strong><?php echo _PHP_SETTINGS?>:</strong>
		</td>
		<td>
			<table cellspacing="1" cellpadding="1" border="0">
				<tr>
					<td><?php echo _REGISTER_GLOBALS?>:</td>
					<td style="font-weight: bold;"><?php echo HTML_admin_misc::get_php_setting('register_globals',1,0); ?></td>
					<td>
								<?php $img = ((ini_get('register_globals'))?'publish_x.png':'tick.png'); ?>
						<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" />
					</td>
				</tr>
				<tr>
					<td><?php echo _MAGIC_QUOTES?>:</td>
					<td style="font-weight: bold;">
								<?php echo HTML_admin_misc::get_php_setting('magic_quotes_gpc',1,1); ?>
					</td>
					<td>
								<?php $img = (!(ini_get('magic_quotes_gpc'))?'publish_x.png':'tick.png'); ?>
						<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" />
					</td>
				</tr>
				<tr>
					<td><?php echo _SAFE_MODE?>:</td>
					<td style="font-weight: bold;">
								<?php echo HTML_admin_misc::get_php_setting('safe_mode',1,0); ?>
					</td>
					<td>
								<?php $img = ((ini_get('safe_mode'))?'publish_x.png':'tick.png'); ?>
						<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" />
					</td>
				</tr>
				<tr>
					<td><?php echo _FILE_UPLOAD?>:</td>
					<td style="font-weight: bold;">
								<?php echo HTML_admin_misc::get_php_setting('file_uploads',1,1); ?>
					</td>
					<td>
								<?php $img = ((!ini_get('file_uploads'))?'publish_x.png':'tick.png'); ?>
						<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" />
					</td>
				</tr>
				<tr>
					<td><?php echo _SESSION_HANDLING?>:</td>
					<td style="font-weight: bold;">
								<?php echo HTML_admin_misc::get_php_setting('session.auto_start',1,0); ?>
					</td>
					<td>
								<?php $img = ((ini_get('session.auto_start'))?'publish_x.png':'tick.png'); ?>
						<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" />
					</td>
				</tr>
				<tr>
					<td><?php echo _SESS_SAVE_PATH?>:</td>
					<td style="font-weight: bold;" colspan="2">
								<?php echo (($sp = ini_get('session.save_path'))?$sp:'none'); ?>
					</td>
				</tr>
				<tr>
					<td><?php echo _PHP_TAGS?>:</td>
					<td style="font-weight: bold;">
								<?php echo HTML_admin_misc::get_php_setting('short_open_tag'); ?>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td><?php echo _BUFFERING?>:</td>
					<td style="font-weight: bold;">
								<?php echo HTML_admin_misc::get_php_setting('output_buffering'); ?>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td><?php echo _OPEN_BASEDIR?>:</td>
					<td style="font-weight: bold;" colspan="2">
								<?php echo (($ob = ini_get('open_basedir'))?$ob:'none'); ?>
					</td>
				</tr>
				<tr>
					<td><?php echo _ERROR_REPORTING?>:</td>
					<td style="font-weight: bold;" colspan="2">
								<?php echo HTML_admin_misc::get_php_setting('display_errors'); ?>
					</td>
				</tr>
				<tr>
					<td><?php echo _XML_SUPPORT?>:</td>
					<td style="font-weight: bold;" colspan="2">
								<?php echo extension_loaded('xml')?'Yes':'No'; ?>
					</td>
				</tr>
				<tr>
					<td><?php echo _ZLIB_SUPPORT?>:</td>
					<td style="font-weight: bold;" colspan="2">
								<?php echo extension_loaded('zlib')?'Yes':'No'; ?>
					</td>
				</tr>
				<tr>
					<td><?php echo _DISABLED_FUNCTIONS?>:</td>
					<td style="font-weight: bold;" colspan="2">
								<?php echo (($df = ini_get('disable_functions'))?$df:'none'); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="height: 10px;">&nbsp;</td>
	</tr>
	<tr>
		<td valign="top"><strong><?php echo _CONFIGURATION_FILE?>:</strong></td>
		<td>
					<?php
					$cf = file(JPATH_BASE.'/configuration.php');
					foreach($cf as $k => $v) {
						if(preg_match('/mosConfig_host/i',$v)) {
							$cf[$k] = '$mosConfig_host = \'xxxxxx\'';
						} elseif(preg_match('/mosConfig_user/i',$v)) {
							$cf[$k] = '$mosConfig_user = \'xxxxxx\'';
						} elseif(preg_match('/mosConfig_password/i',$v)) {
							$cf[$k] = '$mosConfig_password = \'xxxxxx\'';
						} elseif(preg_match('/mosConfig_db /i',$v)) {
							$cf[$k] = '$mosConfig_db = \'xxxxxx\'';
						}
					}
					foreach($cf as $k => $v) {
						$k = htmlspecialchars($k);
						$v = htmlspecialchars($v);
						$cf[$k] = $v;
					}
					echo implode("<br />",$cf);
					?>
		</td>
	</tr>
</table>
		<?php
		$tabs->endTab();
		$tabs->startTab("PHP Info","php-page");
		?>
<table class="adminform">
	<tr>
		<td>
					<?php
					ob_start();
					phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
					$phpinfo = ob_get_contents();
					ob_end_clean();
					preg_match_all('#<body[^>]*>(.*)</body>#siU',$phpinfo,$output);
					$output = preg_replace('#<table#','<table class="adminlist" align="center"',$output[1][0]);
					$output = preg_replace('#(\w),(\w)#','\1, \2',$output);
					$output = preg_replace('#border="0" cellpadding="3" width="600"#','border="0" cellspacing="1" cellpadding="4" width="95%"',$output);
					$output = preg_replace('#<hr />#','',$output);
					echo $output;
					?>
		</td>
	</tr>
</table>
		<?php
		$tabs->endTab();
		$tabs->startTab(_ACCESS_RIGHTS,'perms');
		?>
<table class="adminform">
	<tr>
		<td>
			<strong><?php echo _DIRS_WITH_RIGHTS?>:</strong><br />
					<?php
					$sp = ini_get('session.save_path');

					mosHTML::writableCell(JADMIN_BASE.'/backups');
					mosHTML::writableCell(JADMIN_BASE.'/components');
					mosHTML::writableCell(JADMIN_BASE.'/modules');
					mosHTML::writableCell(JADMIN_BASE.'/templates');
					mosHTML::writableCell('components');
					mosHTML::writableCell('images');
					mosHTML::writableCell('images/show');
					mosHTML::writableCell('images/stories');
					mosHTML::writableCell('language');
					mosHTML::writableCell('mambots');
					mosHTML::writableCell('mambots/content');
					mosHTML::writableCell('mambots/editors');
					mosHTML::writableCell('mambots/editors-xtd');
					mosHTML::writableCell('mambots/search');
					mosHTML::writableCell('mambots/system');
					mosHTML::writableCell('media');
					mosHTML::writableCell('modules');
					mosHTML::writableCell('templates');
					mosHTML::writableCell($mosConfig_cachepath,0,'<strong>'._CACHE_DIR.'</strong> ');
					mosHTML::writableCell($sp,0,'<strong>'._SESSION_DIRECTORY.'</strong> ');
					?>
		</td>
	</tr>
</table>
		<?php
		$tabs->endTab();
		$tabs->startTab(_DATABASE,'db');
		?>
<table class="adminform">
	<tr>
		<th><?php echo _TABLE_NAME?>:</th>
		<th><?php echo _DB_CHARSET?>:</th>
		<th><?php echo _DB_NUM_RECORDS?>:</th>
		<th><?php echo _DB_SIZE?>:</th>
	</tr>
			<?php
			$db_info = HTML_admin_misc::db_info();
			$k = 0;
			foreach($db_info as $table) {
				if($table->Collation != 'utf8_general_ci') $table->Collation ='<font color="red"><b>'.$table->Collation.'</b></font>';
				echo '<tr class="row'.$k.'"><td><b>'.$table->Name.'</b></td><td>'.$table->Collation.'</td><td>'.$table->Rows.'</td><td>'.$table->Data_length.'</td></tr>';
				$k = 1 - $k;
			}
			?>

</table>
		<?php
		$tabs->endTab();
		$tabs->endPane();
		?>
		<?php
	}
	// получение информации о базе данных
	public static function db_info() {
		global $database,$mosConfig_db;
		$sql = 'SHOW TABLE STATUS FROM '.$mosConfig_db;
		$database->setQuery($sql);
		return $database->loadObjectList();
	}

	public static function ListComponents() {
		global $database;

		$query = "SELECT params FROM #__modules WHERE module = 'mod_components'";
		$database->setQuery($query);
		$row = $database->loadResult();
		$params = new mosParameters($row);

		mosLoadAdminModule('components',$params);
	}

	/**
	 * Display Help Page
	 */
	public static function help() {
		$helpurl = strval(mosGetParam($GLOBALS,'mosConfig_helpurl',''));

		if($helpurl == 'http://help.mamboserver.com') {
			$helpurl = 'http://help.joomla.org';
		}

		$fullhelpurl = $helpurl.'/index2.php?option=com_content&amp;task=findkey&pop=1&keyref=';

		$helpsearch = strval(mosGetParam($_REQUEST,'helpsearch',''));
		$helpsearch = addslashes(htmlspecialchars($helpsearch));

		$page = strval(mosGetParam($_REQUEST,'page','joomla.whatsnew100.html'));
		$toc = getHelpToc($helpsearch);
		if(!preg_match('/\.html$/',$page)) {
			$page .= '.xml';
		}

		echo $helpsearch;
		?>
<style type="text/css">
	.helpIndex {
		border: 0px;
		width: 95%;
		height: 100%;
		padding: 0px 5px 0px 10px;
		overflow: auto;
	}
	.helpFrame {
		border-left: 0px solid #222;
		border-right: none;
		border-top: none;
		border-bottom: none;
		width: 100%;
		height: 700px;
		padding: 0px 5px 0px 10px;
	}
</style>
<form name="adminForm">
	<table class="adminform" border="1">
		<tr>
			<th colspan="2" class="title"><?php echo _HELP; ?></th>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%">
					<tr>
						<td>
							<strong><?php echo _SEARCH?>:</strong>
							<input class="text_area" type="hidden" name="option" value="com_admin" />
							<input type="text" name="helpsearch" value="<?php echo $helpsearch; ?>" class="inputbox" />
							<input type="submit" value="<?php echo _FIND?>" class="button" />
							<input type="button" value="<?php echo _CLEAR?>" class="button" onclick="f=document.adminForm;f.helpsearch.value='';f.submit()" />
						</td>
						<td style="text-align:right">
									<?php
									if($helpurl) {
										?>
							<a href="<?php echo $fullhelpurl; ?>joomla.glossary" target="helpFrame"><?php echo _GLOSSARY?></a>
							|
							<a href="<?php echo $fullhelpurl; ?>joomla.credits" target="helpFrame"><?php echo _DEVELOPERS?></a>
							|
							<a href="<?php echo $fullhelpurl; ?>joomla.support" target="helpFrame"><?php echo _SUPPORT?></a>
										<?php
									} else {
										?>
							<a href="<?php echo JPATH_SITE; ?>/help/joomla.glossary.html" target="helpFrame"><?php echo _GLOSSARY?></a>
							|
							<a href="<?php echo JPATH_SITE; ?>/help/joomla.credits.html" target="helpFrame"><?php echo _DEVELOPERS?></a>
							|
							<a href="<?php echo JPATH_SITE; ?>/help/joomla.support.html" target="helpFrame"><?php echo _SUPPORT?></a>
										<?php
									}
									?>
							|
							<a href="http://www.gnu.org/licenses/gpl-2.0.htm" target="helpFrame"><?php echo _LICENSE?></a>
							|
							<a href="http://help.joomla.org" target="_blank">help.joomla.org</a>
							|
							<a href="http://Joom.Ru" target="_blank">Joom.Ru</a>
							<br />
							<a href="<?php echo JPATH_SITE; ?>/<?php echo JADMIN_BASE?>/index3.php?option=com_admin&task=changelog" target="helpFrame"><?php echo _CHANGELOG?></a>
							|
							<a href="<?php echo JPATH_SITE; ?>/<?php echo JADMIN_BASE?>/index3.php?option=com_admin&task=sysinfo" target="helpFrame"><?php echo _ABOUT_SYSTEM?></a>
							|
							<a href="http://www.joostina.ru/" target="_blank"><?php echo _CHECK_VERSION ?></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<td width="20%" valign="top">
				<strong><?php echo _TOC_JUMPTO?></strong>
				<div class="helpIndex">
							<?php
							foreach($toc as $k => $v) {
								if($helpurl) {
									echo '<br /><a href="'.$fullhelpurl.urlencode($k).'" target="helpFrame">'.$v.
											'</a>';
								} else {
									echo '<br /><a href="'.JPATH_SITE.'/help/'.$k.'" target="helpFrame">'.
											$v.'</a>';
								}
							}
							?>
				</div>
			</td>
			<td valign="top">
				<iframe name="helpFrame" src="<?php echo JPATH_SITE.'/help/'.$page; ?>" class="helpFrame" frameborder="0" ></iframe>
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="help" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}

	/**
	 * Preview site
	 */
	public static function preview($tp = 0) {
		$tp = intval($tp);
		?>
<style type="text/css">
	.previewFrame {
		border: none;
		width: 95%;
		height: 600px;
		padding: 0px 5px 0px 10px;
	}
</style>
<table class="adminform">
	<tr>
		<th width="50%" class="title"><?php echo _PREVIEW_SITE?></th>
		<th width="50%" style="text-align:right">
			<a href="<?php echo JPATH_SITE.'/index.php?tp='.$tp; ?>" target="_blank"><?php echo _IN_NEW_WINDOW?></a>
		</th>
	</tr>
	<tr>
		<td width="100%" valign="top" colspan="2">
			<iframe name="previewFrame" src="<?php echo JPATH_SITE.'/index.php?tp='.$tp; ?>" class="previewFrame" ></iframe>
		</td>
	</tr>
</table>
		<?php
	}

	/*
	* Displays contents of Changelog.php file
	*/
	public static function changelog() {
		?>
<pre>
			<?php
			readfile(JPATH_BASE.'/changeslog');
			?>
</pre>
		<?php
	}
}

/**
 * Compiles the help table of contents
 * @param string A specific keyword on which to filter the resulting list
 */
function getHelpTOC($helpsearch) {
	$helpurl = strval(mosGetParam($GLOBALS,'mosConfig_helpurl',''));
	$files = mosReadDirectory(JPATH_BASE.'/help/','\.xml$|\.html$');

	require_once (JPATH_BASE.'/includes/domit/xml_domit_lite_include.php');

	$toc = array();
	foreach($files as $file) {
		$buffer = file_get_contents(JPATH_BASE.'/help/'.$file);
		if(preg_match('#<title>(.*?)</title>#',$buffer,$m)) {
			$title = trim($m[1]);
			if($title) {
				if($helpurl) {
					// strip the extension
					$file = preg_replace('#\.xml$|\.html$#','',$file);
				}
				if($helpsearch) {
					if(strpos(strip_tags($buffer),$helpsearch) !== false) {
						$toc[$file] = $title;
					}
				} else {
					$toc[$file] = $title;
				}
			}
		}
	}
	asort($toc);
	return $toc;
}