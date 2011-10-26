<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
* @package joomlaXplorer
* @copyright soeren 2007
* @author The joomlaXplorer project (http://joomlacode.org/gf/project/joomlaxplorer/)
* @author The  The QuiX project (http://quixplorer.sourceforge.net)
**/
defined('_VALID_MOS') or die();
function get_php_setting($val, $recommended = 1) {
	$value = ini_get($val);
	$r = ($value == $recommended?1:0);
	if(empty($value)) {
		$onoff = 1;
	} else {
		$onoff = 0;
	}
	return $r?'<span style="color: green;">'.$GLOBALS['messages']['sionoff'][$onoff].
		'</span>':'<span style="color: red;">'.$GLOBALS['messages']['sionoff'][$onoff].
		'</span>';
}
function get_server_software() {
	if(isset($_SERVER['SERVER_SOFTWARE'])) {
		return $_SERVER['SERVER_SOFTWARE'];
	} else
		if(($sf = getenv('SERVER_SOFTWARE'))) {
			return $sf;
		} else {
			return 'n/a';
		}
}
function system_info($version, $option) {
	global  $database;
	$version = joomlaVersion::get('CMS').' <strong style="color: red;">'.joomlaVersion::get('RELEASE').'.'.joomlaVersion::get('DEV_LEVEL').'</strong> '.joomlaVersion::get('DEV_STATUS').' [ '.joomlaVersion::get('CODENAME').' ] '.joomlaVersion::get('RELDATE').' '.joomlaVersion::get('RELTIME').' '.joomlaVersion::get('RELTZ');
	$width = 400;
	$tabs = new mosTabs(0);
?>
	<br />
	<?php
	$tabs->startPane('sysinfo');
	$tabs->startTab($GLOBALS['messages']['sisysteminfo'], 'system-page');
?>
	<table class="adminform">
	<tr>
		<td valign="top" width="250" style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['sibuilton']; ?>:
		</td>
		<td>
		<?php echo php_uname(); ?>
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['sidbversion']; ?>:
		</td>
		<td>
		<?php echo mysql_get_server_info(); ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['siphpversion']; ?>:
		</td>
		<td>
		<?php echo phpversion(); ?>
		&nbsp;
		<?php echo phpversion() >= '4.3'?'':$GLOBALS['messages']['siphpupdate']; ?>
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['siwebserver']; ?>:
		</td>
		<td>
		<?php echo get_server_software(); ?>
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['siwebsphpif']; ?>:
		</td>
		<td>
		<?php echo php_sapi_name(); ?>
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['simamboversion']; ?>:
		</td>
		<td>
		<?php echo $version; ?>
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['siuseragent']; ?>:
		</td>
		<td>
		<?php echo phpversion() <= "4.2.1"?getenv("HTTP_USER_AGENT"):$_SERVER['HTTP_USER_AGENT']; ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['sirelevantsettings']; ?>:
		</td>
		<td>
			<table cellspacing="1" cellpadding="1" border="0">
			<tr>
				<td valign="top">
					<?php echo $GLOBALS['messages']['sisafemode']; ?>:
				</td>
				<td>
				<?php echo get_php_setting('safe_mode', 0); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sibasedir']; ?>:
				</td>
				<td>
				<?php echo (($ob = ini_get('open_basedir'))?$ob:'none'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sidisplayerrors']; ?>:
				</td>
				<td>
				<?php echo get_php_setting('display_errors'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sishortopentags']; ?>:
				</td>
				<td>
				<?php echo get_php_setting('short_open_tag'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sifileuploads']; ?>:
				</td>
				<td>
				<?php echo get_php_setting('file_uploads'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['simagicquotes']; ?>:
				</td>
				<td>
				<?php echo get_php_setting('magic_quotes_gpc'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['siregglobals']; ?>:
				</td>
				<td>
				<?php echo get_php_setting('register_globals', 0); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sioutputbuf']; ?>:
				</td>
				<td>
				<?php echo get_php_setting('output_buffering', 0); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sisesssavepath']; ?>:
				</td>
				<td>
				<?php echo (($sp = ini_get('session.save_path'))?$sp:'none'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sisessautostart']; ?>:
				</td>
				<td>
				<?php echo intval(ini_get('session.auto_start')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sixmlenabled']; ?>:
				</td>
				<td>
					<?php echo extension_loaded('xml')?'<font style="color: green;">'.$GLOBALS['messages']['miscyesno'][0].
	'</font>':'<font style="color: red;">'.$GLOBALS['messages']['miscyesno'][1].
		'</font>'; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sizlibenabled']; ?>:
				</td>
				<td>
				<?php echo extension_loaded('zlib')?'<font style="color: green;">'.$GLOBALS['messages']['miscyesno'][0].
	'</font>':'<font style="color: red;">'.$GLOBALS['messages']['miscyesno'][1].
		'</font>'; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sidisabledfuncs']; ?>:
				</td>
				<td>
				<?php echo (($df = ini_get('disable_functions'))?$df:'none'); ?>
				</td>
			</tr>
			<?php
	$query = "SELECT name FROM #__mambots"."\nWHERE folder='editors' AND published='1'".
		"\nLIMIT 1";
	$database->setQuery($query);
	$editor = $database->loadResult();
?>
			<tr>
				<td>
					<?php echo $GLOBALS['messages']['sieditor']; ?>:
				</td>
				<td>
					<?php echo $editor; ?>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight:bold;">
			<?php echo $GLOBALS['messages']['siconfigfile']; ?>:
		</td>
		<td>
		<?php
	$cf = file(JPATH_BASE.'/configuration.php');
	foreach($cf as $k => $v) {
		if(preg_match('/mosConfig_host/i', $v)) {
			$cf[$k] = '$mosConfig_host = \'xxxxxx\'';
		} else
			if(preg_match('/mosConfig_user/i', $v)) {
				$cf[$k] = '$mosConfig_user = \'xxxxxx\'';
			} else
				if(preg_match('/mosConfig_password/i', $v)) {
					$cf[$k] = '$mosConfig_password = \'xxxxxx\'';
				} else
					if(preg_match('/mosConfig_db/i', $v)) {
						$cf[$k] = '$mosConfig_db = \'xxxxxx\'';
					} else
						if(preg_match('/<?php/i', $v)) {
							$cf[$k] = '&lt;?php';
						}
	}
	echo implode('<br>', $cf);
?>
		</td>
	</tr>
	</table>
	<?php
	$tabs->endTab();
	$tabs->startTab($GLOBALS['messages']['siphpinfo'], 'php-page');
?>
	<table class="adminform">
	<tr>
		<th colspan="2">
			<?php echo $GLOBALS['messages']['siphpinformation']; ?>:
		</th>
	</tr>
	<tr>
		<td>
		<?php
	ob_start();
	phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
	$phpinfo = ob_get_contents();
	ob_end_clean();
	preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
	$output = preg_replace('#<table#', '<table class="adminlist" align="center"', $output[1][0]);
	$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
	$output = preg_replace('#border="0" cellpadding="3" width="600"#',
		'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
	$output = preg_replace('#<hr />#', '', $output);
	echo $output;
?>
		</td>
	</tr>
	</table>
	<?php
	$tabs->endTab();
	$tabs->startTab($GLOBALS['messages']['sipermissions'], 'perms');
?>
	<table class="adminform">
	  <tr>
		<th colspan="2">&nbsp;<?php echo $GLOBALS['messages']['sidirperms']; ?>:</th>
	  </tr>
	  <tr>
		<td colspan="2">
			<span style="font-weight:bold;"><?php echo $GLOBALS['messages']['sidirpermsmess']; ?>:</span>
		</td>
	  </tr>
	  <tr>
	 	<td width="50%">
		<?php
	mosHTML::writableCell(JADMIN_BASE.'/backups');
	mosHTML::writableCell(JADMIN_BASE.'/components');
	mosHTML::writableCell(JADMIN_BASE.'/modules');
	mosHTML::writableCell(JADMIN_BASE.'/templates');
	mosHTML::writableCell('cache');
	mosHTML::writableCell('components');
	mosHTML::writableCell('images');
	mosHTML::writableCell('images/banners');
	mosHTML::writableCell('images/stories');
	mosHTML::writableCell('language');
	mosHTML::writableCell('mambots');
	mosHTML::writableCell('mambots/content');
	mosHTML::writableCell('mambots/search');
	mosHTML::writableCell('media');
	mosHTML::writableCell('modules');
	mosHTML::writableCell('templates');
?>

		</td>
	  </tr>
	</table>
	<?php
	$tabs->endTab();
	$tabs->endPane();
?>
	<?php
}
show_header($GLOBALS['messages']['simamsysinfo'].
	'&nbsp;&nbsp;<a href="index2.php?option='.$option.'">[ '.$GLOBALS['error_msg']['back'].
	']</a>');
system_info($version, $option);
?>
