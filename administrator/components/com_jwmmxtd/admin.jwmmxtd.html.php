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

global $mainframe,$task;

class HTML_mmxtd {
// отображения подкаталога текущего каталога
	function show_dir($path,$dir,$listdir) {
		if($listdir) {
			$link = 'index2.php?option=com_jwmmxtd&amp;curdirectory='.$listdir."/".$path;
			$count = HTML_mmxtd::num_files($listdir."/".$path);
		} else {
			$link = 'index2.php?option=com_jwmmxtd&amp;curdirectory='."/".$path;
			$count = HTML_mmxtd::num_files('/'.$path);
		}
		$num_files = $count[0];
		$num_dir = $count[1];
		$mainframe = mosMainFrame::getInstance(true);
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico/';
		?>
<div class="folder_style">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td class="filename" colspan="2"><h2><?php echo substr($dir,0,20).(strlen($dir) > 20?'...':''); ?></h2></td>
		</tr>
		<tr>
			<td class="fileinfo">
						<?php echo _JWMM_DIRECTORIES?>: <?php echo $num_dir; ?><br />
						<?php echo _JWMM_FILES?>: <?php echo $num_files; ?>
			</td>
			<td class="fileactions">
				<a href="javascript:void(null)" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $path ?>';document.adminForm.subtask.value='renamefile';document.adminForm.submit( );" title="<?php echo _RENAME?>">
					<img src="<?php echo $cur_file_icons_path?>rename.png" alt="<?php echo _RENAME?>" title="<?php echo _RENAME?>" /></a>
				<a href="javascript:void(null)" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $path ?>';document.adminForm.subtask.value='copyfile';document.adminForm.submit( );" title="<?php echo _COPY?>">
					<img src="<?php echo $cur_file_icons_path?>copy.png" alt="<?php echo _COPY?>" title="<?php echo _COPY?>" /></a>
				<a href="javascript:void(null)" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $path ?>';document.adminForm.subtask.value='movefile';document.adminForm.submit( );" title="<?php echo _MOVE?>">
					<img src="<?php echo $cur_file_icons_path?>cut.png" alt="<?php echo _MOVE?>" title="<?php echo _MOVE?>" /></a>
				<a href="index2.php?option=com_jwmmxtd&amp;task=deletefolder&amp;delFolder=<?php echo $path; ?>&amp;curdirectory=<?php echo $listdir; ?>" onclick="return deleteFolder('<?php echo $dir; ?>', <?php echo $num_files; ?>);" title="<?php echo _DELETE?>">
					<img src="<?php echo $cur_file_icons_path?>delete.png" alt="<?php echo _DELETE?>" title="<?php echo _DELETE?>" /></a>
			</td>
		</tr>
	</table>
	<div style="text-align:center;margin:2px auto;"> <a href="<?php echo $link; ?>"><img src="components/com_jwmmxtd/images/folder.gif" /></a> </div>
</div>
		<?php
	}
// подсчет размера
	function parse_size($size) {
		if($size < 1024) {
			return $size.' '._JWMM_BYTES;
		} else
		if($size >= 1024 && $size < 1024* 1024) {
			return sprintf('%01.2f',$size / 1024.0).' '._JWMM_KBYTES;
		} else {
			return sprintf('%01.2f',$size / (1024.0* 1024)).' '._JWMM_MBYTES;
		}
	}
// вывод изображения
	function show_image($img,$file,$info,$size,$listdir) {
		$img_file = basename($img);
		$img_url_link = JWMMXTD_STARTURLPATH.$listdir.'/'.rawurlencode($img_file);
		$cur = $listdir;
		$filesize = HTML_mmxtd::parse_size($size);
		if(($info[0] > 200) || ($info[1] > 200)) {
			$img_dimensions = HTML_mmxtd::imageResize($info[0],$info[1],200);
		} else {
			$img_dimensions = 'style="width:'.$info[0].'px;height:'.$info[1].'px; margin:4px auto;display:block;"';
		}

		$mainframe = mosMainFrame::getInstance(true);
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico/';
		?>
<div class="image_style">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td class="filename" colspan="2">
				<h2><a href="<?php echo $img_url_link; ?>" title="<?php echo $file; ?>" rel="lightbox[jwmmxtd-title]">
								<?php echo htmlspecialchars(substr($file,0,20).(strlen($file) > 20?'...':''),ENT_QUOTES); ?></a></h2>
			</td>
		</tr>
		<tr>
			<td class="fileactions">
				<a href="index2.php?option=com_jwmmxtd&task=edit&curdirectory=<?php echo $cur; ?>&img=<?php echo $img_file; ?>" title="<?php echo _EDIT?>">
					<img src="<?php echo $cur_file_icons_path ?>picture_edit.png" alt="<?php echo _EDIT?>" title="<?php echo _EDIT?>" /></a>
				<a href="#" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $file ?>';document.adminForm.subtask.value='renamefile';document.adminForm.submit( );" title="<?php echo _RENAME?>">
					<img src="<?php echo $cur_file_icons_path ?>rename.png" alt="<?php echo _RENAME?>" title="<?php echo _RENAME?>" /></a>
				<a href="#" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $file ?>';document.adminForm.subtask.value='copyfile';document.adminForm.submit( );" title="<?php echo _COPY?>">
					<img src="<?php echo $cur_file_icons_path ?>copy.png" alt="<?php echo _COPY?>" title="<?php echo _COPY?>" /></a>
				<a href="#" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $file ?>';document.adminForm.subtask.value='movefile';document.adminForm.submit( );" title="<?php echo _MOVE?>">
					<img src="<?php echo $cur_file_icons_path ?>cut.png" alt="<?php echo _MOVE?>" title="<?php echo _MOVE?>" /></a>
				<a href="index2.php?option=com_jwmmxtd&amp;task=delete&amp;delFile=<?php echo $file; ?>&amp;curdirectory=<?php echo $cur; ?>" onclick="javascript:if(confirm('<?php echo _JWMM_DELETE_FILE_CONFIRM?>:<?php echo $file; ?>')) return true; return false;" title="<?php echo _DELETE?>">
					<img src="<?php echo $cur_file_icons_path ?>delete.png" alt="<?php echo _DELETE?>" title="<?php echo _DELETE?>" /></a>
			</td>
		</tr>
	</table>
	<div class="fileimage" onclick="get_image('<?php echo $img_url_link; ?>','<?php echo $file?>',<?php echo $info[0] ?>,<?php echo $info[1] ?>);">
		<img src="<?php echo $img_url_link; ?>" <?php echo $img_dimensions; ?> alt="<?php echo _JWMM_CLICK_TO_URL?>" title="<?php echo _CLICK_TO_PREVIEW?>" />
	</div>
			<?php echo _JWMM_FILESIZE?>: <?php echo $filesize; ?><br />
			<?php echo _WIDTH?>: <?php echo $info[0]; ?>px, <?php echo _HEIGHT?>: <?php echo $info[1]; ?>px
</div>
		<?php
	}
// подсчет числа файлов
	function num_files($dir) {
		$total_file = 0;
		$total_dir = 0;
		$dir = JWMMXTD_STARTABSPATH.$dir;
		if(is_dir($dir)) {
			$d = dir($dir);
			while(false !== ($entry = $d->read())) {
				if(substr($entry,0,1) != '.' && is_file($dir.DS.$entry) && strpos($entry,'.html') === false && strpos($entry,'.php') === false) {
					$total_file++;
				}
				if(substr($entry,0,1) != '.' && is_dir($dir.DS.$entry)) {
					$total_dir++;
				}
			}
			$d->close();
		}

		return array($total_file,$total_dir);
	}
// отображение документов
	function show_doc($doc,$size,$listdir,$icon) {
		$size = HTML_mmxtd::parse_size($size);
		$doc_url_link = JWMMXTD_STARTURLPATH.$listdir.'/'.rawurlencode($doc);
		$cur = $listdir;
		$mainframe = mosMainFrame::getInstance(true);
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico/';

		?>
<div class="file_style">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td class="filename" colspan="2"><h2><?php echo htmlspecialchars(substr($doc,0,14).(strlen($doc) > 14?'...':''),ENT_QUOTES); ?></h2></td>
		</tr>
		<tr>
			<td class="fileinfo"><?php echo $size; ?></td>
			<td class="fileactions">
						<?php
// архив
						if($icon == $cur_file_icons_path.'zip.png') { ?>
				<a href="#" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $doc ?>';document.adminForm.subtask.value='unzipfile';document.adminForm.submit( );" title="<?php echo _UNPACK?>">
					<img src="<?php echo $cur_file_icons_path ?>compress.png" alt="<?php echo _UNPACK?>" title="<?php echo _UNPACK?>" /></a>
							<?php } ?>
				<a href="#" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $doc ?>';document.adminForm.subtask.value='renamefile';document.adminForm.submit( );" title="<?php echo _RENAME?>">
					<img src="<?php echo $cur_file_icons_path ?>rename.png" alt="<?php echo _RENAME?>" title="<?php echo _RENAME?>" /></a>
				<a href="#" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $doc ?>';document.adminForm.subtask.value='copyfile';document.adminForm.submit( );" title="<?php echo _COPY?>">
					<img src="<?php echo $cur_file_icons_path ?>copy.png" alt="<?php echo _COPY?>" title="<?php echo _COPY?>" /></a>
				<a href="#" onclick="javascript:document.adminForm.selectedfile.value='<?php echo $doc ?>';document.adminForm.subtask.value='movefile';document.adminForm.submit( );" title="<?php echo _MOVE?>">
					<img src="<?php echo $cur_file_icons_path ?>cut.png" alt="<?php echo _MOVE?>" title="<?php echo _MOVE?>" /></a>
				<a href="index2.php?option=com_jwmmxtd&amp;task=delete&amp;delFile=<?php echo $doc; ?>&amp;curdirectory=<?php echo $cur; ?>" onclick="javascript:if(confirm('<?php echo _JWMM_DELETE_FILE_CONFIRM?>: <?php echo $doc; ?>')) return true; return false;" title="<?php echo _DELETE?>">
					<img src="<?php echo $cur_file_icons_path ?>delete.png" alt="<?php echo _DELETE?>" title="<?php echo _DELETE?>" /></a>
			</td>
		</tr>
	</table>
	<div class="fileimage" onclick="get_file('<?php echo $doc_url_link; ?>','<?php echo $doc?>');">
				<?php
				// флеш - файл flv
				if($icon == $cur_file_icons_path.'flv.png') {
					?>
		<a href="components/com_jwmmxtd/js/flvplayer.swf?file=<?php echo $doc_url_link; ?>&amp;autostart=true&amp;allowfullscreen=true" target="_blank" title="<?php echo _JWMM_VIDEO_FILE?>:<br /><?php echo $doc; ?>" alt="<?php echo _CLICK_TO_PREVIEW?>">
			<img src="<?php echo $icon ?>" alt="<?php echo $doc; ?>" title="<?php echo _CLICK_TO_PREVIEW?>" /></a>
					<?php
					// флеш - файл swf
				} elseif($icon == $cur_file_icons_path.'swf.png') {
					$swfinfo = @getimagesize($doc_url_link);
					?>
		<a href="<?php echo $doc_url_link; ?>" rel="vidbox <?php echo $swfinfo[0]; ?> <?php echo $swfinfo[1]; ?>" title="<?php echo _FILE?>:</b><br /><?php echo $doc; ?>" alt="<?php echo _CLICK_TO_PREVIEW?>">
			<img src="<?php echo $icon ?>" alt="<?php echo $doc; ?>" title="<?php echo _CLICK_TO_PREVIEW?>" /></a>
					<?php
				} else {
					?>
		<img src="<?php echo $icon ?>" alt="<?php echo $doc; ?>" />
					<?php
				}
				?>
	</div>
</div>
		<?php
	}

// расчет и отображение размера изображения
	function imageResize($width,$height,$target) {
		if($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}
		$width = round($width* $percentage);
		$height = round($height* $percentage);
		return 'width="'.$width.'" height="'.$height.'"';
	}
}