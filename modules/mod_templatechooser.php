<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

global $cur_template;

// длина заголовка, установленная в параметрах модуля
$titlelength	= $params->get( 'title_length', 20 );
$preview_height = $params->get( 'preview_height', 90 );
$preview_width	= $params->get( 'preview_width', 140 );
$show_preview	= $params->get( 'show_preview', 0 );

// Чтение файлов из каталога шаблона
$template_path	= JPATH_BASE.'/templates';
$templatefolder = @dir( $template_path );
$darray = array();

if ($templatefolder) {
	while ($templatefile = $templatefolder->read()) {
		if ($templatefile != "." && $templatefile != ".." && $templatefile != "system" && $templatefile != ".svn" && $templatefile != "css" && is_dir( $template_path.'/'.$templatefile )  ) {
			if(strlen($templatefile) > $titlelength) {
				$templatename = substr( $templatefile, 0, $titlelength-3 );
				$templatename .= "...";
			} else {
				$templatename = $templatefile;
			}
			$darray[] = mosHTML::makeOption( $templatefile, $templatename );
		}
	}
	$templatefolder->close();
}

sort( $darray );

// Показ изображения предпросмотра
// Установки JavaScript для текущего предпросмотра
$onchange = '';
if ($show_preview) {
	$onchange = "showimage()";
	?>
<img src="<?php echo "templates/$cur_template/template_thumbnail.png";?>" name="preview" border="1" width="<?php echo $preview_width;?>" height="<?php echo $preview_height;?>" alt="<?php echo $cur_template; ?>" />
<script language='JavaScript1.2' type='text/javascript'>
	<!--
	function showimage() {
		document.images.preview.src = 'templates/' + getSelectedValue( 'templateform', 'jos_change_template' ) + '/template_thumbnail.png';
	}
	function getSelectedValue( frmName, srcListName ) {
		var form = eval( 'document.' + frmName );
		var srcList = eval( 'form.' + srcListName );
		i = srcList.selectedIndex;
		if (i != null && i > -1) {
			return srcList.options[i].value;
		} else {
			return null;
		}
	}
	-->
</script>
	<?php
}
?>
<form action="index.php" name="templateform" method="post">
	<?php echo mosHTML::selectList( $darray, 'jos_change_template', "id=\"mod_templatechooser_jos_change_template\" class=\"button\" onchange=\"$onchange\"",'value', 'text', $cur_template ); ?>
	<input class="button" type="submit" value="<?php echo _SELECT;?>" />
</form>