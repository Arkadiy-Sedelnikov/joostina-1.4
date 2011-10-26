<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

define("_VALID_MOS",1);

require_once ('../includes/auth.php');

$mainframe = mosMainFrame::getInstance(true);
$mainframe->set('lang', $mosConfig_lang);
include_once($mainframe->getLangFile());

// limit access to functionality
$option = strval(mosGetParam($_SESSION,'option',''));
$task = strval(mosGetParam($_SESSION,'task',''));

switch($option) {
	case 'com_content':
	case 'com_typedcontent':
		if($task != 'edit' && $task != 'editA' && $task != 'new') {
			echo _NOT_AUTH;
			return;
		}
		break;

	default:
		echo _NOT_AUTH;
		return;
		break;
}

$css = mosGetParam($_REQUEST,'t','');

// css file handling
// check to see if template exists
if($css != '' && is_file(JPATH_BASE.DS.'templates'.DS.$css.'/css/template_css.css')) {
	$css = $css;
} else {
	if($css == '') {
		$css = 'newline';
	}
}

$iso = explode('=',_ISO);
// xml prolog
echo '<?xml version="1.0" encoding="'.$iso[1].'"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<base href="<?php echo (JPATH_SITE); ?>/" />
	<head>
		<title><?php echo _CONTENT_PREVIEW?></title>
		<link rel="stylesheet" href="templates/<?php echo $css; ?>/css/template_css.css" type="text/css" />
		<script>
			var form = window.opener.document.adminForm
			var title = form.title.value;

			var alltext = form.introtext.value;
			if (form.fulltext) {
				alltext += form.fulltext.value;
			}

			// do the images
			var temp = new Array();
			for (var i=0, n=form.imagelist.options.length; i < n; i++) {
				value = form.imagelist.options[i].value;
				parts = value.split( '|' );

				temp[i] = '<img src="images/stories/' + parts[0] + '" align="' + parts[1] + '" border="' + parts[3] + '" alt="' + parts[2] + '" hspace="6" />';
			}

			var temp2 = alltext.split( '{mosimage}' );

			var alltext = temp2[0];

			for (var i=0, n=temp2.length-1; i < n; i++) {
				alltext += temp[i] + temp2[i+1];
			}
		</script>
		<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
	</head>
	<body style="background-color:#FFFFFF">
		<table align="center" width="90%" cellspacing="2" cellpadding="2" border="0">
			<tr>
				<td class="contentheading" colspan="2"><script>document.write(title);</script></td>
			</tr>
			<tr>
				<script>document.write("<td valign=\"top\" height=\"90%\" colspan=\"2\">" + alltext + "</td>");</script>
			</tr>
			<tr>
				<td align="right"><a href="#" onClick="window.close()"><?php echo _CLOSE?></a></td>
				<td align="left"><a href="javascript:;" onClick="window.print(); return false"><?php echo _PRINT?></a></td>
			</tr>
		</table>
	</body>
</html>
