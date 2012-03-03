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

// корень Медиа - менеджера из глобальной конфигурации
global $mosConfig_media_dir,$mosConfig_cachepath;
$jwmmxtd_browsepath = $mosConfig_media_dir;

define('JWMMXTD_STARTABSPATH',JPATH_BASE.DS.$jwmmxtd_browsepath);
define('JWMMXTD_STARTURLPATH',JPATH_SITE.'/'.$jwmmxtd_browsepath);

require_once ($mainframe->getPath('admin_html'));


function makeSafe($file) {
	return str_replace('..','',urldecode($file));
}

$subtask		= mosGetParam($_REQUEST,'subtask','');
$curdirectory	= makeSafe(mosGetParam($_REQUEST,'curdirectory',''));
$img			= mosGetParam($_REQUEST,'img','');
$selectedfile	= mosGetParam($_REQUEST,'selectedfile','');
$curfile		= mosGetParam($_REQUEST,'curfile','');
$newfile		= mosGetParam($_REQUEST,'newfilename','');
$folder_name	= mosGetParam($_POST,'createfolder','');
$delFile		= makeSafe(mosGetParam($_REQUEST,'delFile',''));
$delFolder		= mosGetParam($_REQUEST,'delFolder','');
$dirtocopy		= makeSafe(mosGetParam($_REQUEST,'dirtocopy','/'));
$dirtomove		= makeSafe(mosGetParam($_REQUEST,'dirtomove','/'));

if(is_int(strpos($curdirectory,".."))) {
	mosRedirect('index2.php',_JWMM_HACK_ATTEMPT);
}

// очистка каталога кэша
$tmpimage = mosGetParam($_REQUEST,'tmpimage','');
if($tmpimage != "") {
	@unlink($mosConfig_cachepath.DS.$tmpimage);
}

$mainframe->addCSS(JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/css/jw_mmxtd.css');


if($task == 'edit') {
	$mainframe->addJS(JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/js/jw_mmxtd_edit.php');
} else {
	$jw_mmxtd_head = '
	<script type="text/javascript">
	<!--
		function updateDir(){
			var allPaths = window.top.document.forms[0].dirPath.options;
			for(i=0; i<allPaths.length; i++) {
				allPaths.item(i).selected = false;
				if((allPaths.item(i).value)== "';
	if(strlen($curdirectory) > 0) {
		$jw_mmxtd_head .= $curdirectory;
	} else {
		$jw_mmxtd_head .= '/';
	}
	$jw_mmxtd_head .= '") {
					allPaths.item(i).selected = true;
				}
			}
		}
		function deleteFolder(folder, numFiles) {
			if(numFiles > 0) {
				alert("'._JWMM_DIRECTORY_NOT_EMPTY.'");
				return false;
			}
			if(confirm("'._JWMM_DELETE_CATALOG.' \""+folder+"\"?")) return true; return false;
		}
		function get_image(file,name,width,height){
			get_file(file,name);
			id("file_url").value=\'<img width="\'+width+\'" height="\'+height+\'" src="\'+file+\'" alt="\'+name+\'" />\';
		}
		function get_file(file,name){
			id("file_href").value=\'<a href="\'+file+\'">\'+name+\'</a>\';
			id("file_link").value=file;
			id("file_url").value = \'\';
		}
	-->
	</script>';
	$mainframe->addCustomHeadTag($jw_mmxtd_head);
	mosCommonHTML::loadJqueryPlugins('multiple-file-upload/jquery.MultiFile');
}


switch($task) {
	case 'edit':
		editImage($img,$curdirectory);
		break;

	case 'unzipfile':
		$mosmsg = unzipzipfile($curdirectory,$curfile,$dirtocopy);
		viewMediaManager($dirtocopy,$mosmsg);
		break;

	case 'createfolder':
		if(ini_get('safe_mode') == 'On') {
			mosRedirect("index2.php?option=com_jwmmxtd&curdirectory=".$curdirectory,_JWMM_SAFE_MODE_WARNING);
		} else {
			if(create_folder($curdirectory,$folder_name))
				$mosmsg = _JWMM_CATALOG_CREATED.' '.$folder_name;
			else
				$mosmsg = _JWMM_CATALOG_NOT_CREATED.' '.$folder_name;
		}
		viewMediaManager($curdirectory,$mosmsg);
		break;

	case 'delete':
		if(delete_file($curdirectory,$delFile))
			$mosmsg = _JWMM_FILE_DELETED.' '.$delFile;
		else
			$mosmsg = _JWMM_FILE_NOT_DELETED.' '.$delFile;
		viewMediaManager($curdirectory,$mosmsg);
		break;

	case 'deletefolder':
		if(delete_folder($curdirectory,$delFolder))
			$mosmsg = _JWMM_DIRECTORY_DELETED.' '.$delFolder;
		else
			$mosmsg = _JWMM_DIRECTORY_NOT_DELETED.' '.$delFolder;
		viewMediaManager($curdirectory,$mosmsg);
		break;

	case 'uploadimages':
		$mosmsg = uploadImages($curdirectory);
		viewMediaManager($curdirectory,$mosmsg);
		break;

	case 'alterfilename':
		if(newFileName($curdirectory,$curfile,$newfile))
			$mosmsg = _JWMM_RENAMED;
		else
			$mosmsg = _JWMM_NOT_RENAMED;
		viewMediaManager($curdirectory,$mosmsg);
		break;

	case 'copyfile':
		if(copyFile($curdirectory,$curfile,$dirtocopy))
			$mosmsg = _JWMM_COPIED;
		else
			$mosmsg = _JWMM_NOT_COPIED;
		viewMediaManager($dirtocopy,$mosmsg);
		break;

	case 'movefile':
		if(moveFile($curdirectory,$curfile,$dirtomove))
			$mosmsg = _JWMM_FILE_MOVED.' '.$curfile.' - '.$dirtomove;
		else
			$mosmsg = _JWMM_FILE_NOT_MOVED.' '.$curfile;
		viewMediaManager($dirtomove,$mosmsg);
		break;

	case 'emptytmp':
		if(emptyTmp())
			$mosmsg = _TMP_DIR_CLEADNED;
		else
			$mosmsg = _TMP_DIR_NOT_CLEANED;
		viewMediaManager($curdirectory,$mosmsg);
		break;

	case 'saveimage':
		$mosmsg = saveImage($curdirectory);
		viewMediaManager($curdirectory,$mosmsg);
		break;

	case 'returnfromedit':
		returnFromEdit($curdirectory);
		viewMediaManager($curdirectory);
		break;

	default:
		viewMediaManager($curdirectory,"",$selectedfile);
		break;
}

// распаковка ZIP архивов
function unzipzipfile($curdirpath,$curfile,$destindir) {
	include_once (JPATH_BASE_ADMIN.'/includes/pcl/pclzip.lib.php');

	$path = JWMMXTD_STARTABSPATH.$curdirpath.DS.$curfile;// файл для распаковки
	$path2 = JWMMXTD_STARTABSPATH.$destindir.DS; // каталог для распаковки

	if(is_file($path)) {
		if(preg_match("/.zip/i",$path)) {
			$zip = new PclZip($path);
			$list = $zip->extract($path2);
			if($list > 0) {
				$msg = count($list).' '._FILES_UNPACKED;
				return $msg;
			} else $msg = _ERROR_WHEN_UNPACKING.': '.$curfile;
		} else {
			$msg = $curfile.' '._FILE_IS_NOT_A_ZIP;
			return $msg;
		}
	} else $msg = $curfile.' '._FILE_NOT_EXISTS;
	return $msg;
}

// загрузка изображения
function saveImage($cur) {
	require_once ('class.upload.php');

	$cur = JWMMXTD_STARTABSPATH.$cur.DS;

	$primage = mosGetParam($_REQUEST,'primage','');
	$orimage = mosGetParam($_REQUEST,'originalimage','');

	$tmp = explode("/",$orimage);
	$ornamewithext = end($tmp);
	$orname = str_replace(substr($ornamewithext,-4),"",$ornamewithext);

	if($orname) {
		$pic = new upload(JPATH_BASE.DS.'media'.DS.$primage);
		if($pic->uploaded) {
			$pic->file_src_name_body = $orname."_edit".rand(100,999);
			$pic->Process($cur);
			@unlink(JPATH_BASE.DS.'media'.DS.$primage);
			$ok = true;
		} else $ok = false;
	} else $ok = false;
	if($ok)
		$msg = _IMAGE_SAVED_AS.' '.$pic->file_dst_name;
	else
		$msg = _IMAGE_NOT_SAVED;
	return $msg;
}

function returnFromEdit() {
	require_once ('class.upload.php');
	$primage = mosGetParam($_REQUEST,'primage','');
	@unlink(JPATH_BASE.DS.'media'.DS.$primage);
}

function emptyTmp() {
	$dir = JPATH_BASE.DS.'media';
	if(is_dir($dir)) {
		$d = dir($dir);
		while(false !== ($entry = $d->read())) {
			if(substr($entry,-4) == ".jpg" || substr($entry,-4) == ".gif" || substr($entry,-4) == ".png") {
				@unlink($dir."/".$entry);
			}
		}
		$d->close();
	}
	$total_file = 0;
	if(is_dir($dir)) {
		$d = dir($dir);
		while(false !== ($entry = $d->read())) {
			if(substr($entry,-4) == ".jpg" || substr($entry,-4) == ".gif" || substr($entry,-4) == ".png") {
				$total_file++;
			}
		}
		$d->close();
	}
	if($total_file == 0) $ok = true;
	else $ok = false;
	return $ok;
}

function newFileName($curdirectory,$curfile,$newfile) {
	if($curfile == "" || $newfile == "") return false;
	$path = JWMMXTD_STARTABSPATH.$curdirectory.DS.$curfile;
	$path2 = JWMMXTD_STARTABSPATH.$curdirectory.DS.$newfile;
	if(file_exists($path2)) return false;
	if(rename($path,$path2))
		$ok = true;
	else
		$ok = false;
	return $ok;
}

function copyFile($curdirectory,$curfile,$dirtocopy) {
	if($curfile == "") return false;
	$path = JWMMXTD_STARTABSPATH.$curdirectory.DS.$curfile;
	$path2 = JWMMXTD_STARTABSPATH.$dirtocopy.DS.$curfile;
	if(file_exists($path2)) return false;
	if(!copy($path,$path2))
		$ok = false;
	else
		$ok = true;
	return $ok;
}

function moveFile($curdirectory,$curfile,$dirtomove) {
	if($curfile == "") return false;
	$path = JWMMXTD_STARTABSPATH.$curdirectory.DS.$curfile;
	$path2 = JWMMXTD_STARTABSPATH.$dirtomove.DS.$curfile;
	if(file_exists($path2)) return false;
	if(!rename($path,$path2))
		$ok = false;
	else
		$ok = true;
	return $ok;
}

function uploadImages($curdirectory) {
	include ('class.upload.php');
	$files = array();
	foreach($_FILES['upimage'] as $k => $l) {
		foreach($l as $i => $v) {
			if(!array_key_exists($i,$files)) $files[$i] = array();
			$files[$i][$k] = $v;
		}
	}
	$mosmsg = _FILES_NOT_UPLOADED;
	foreach($files as $file) {
		$handle = new Upload($file);
		if($handle->uploaded) {
			$updirectory = JWMMXTD_STARTABSPATH.$curdirectory.DS;
			$handle->Process($updirectory);
			if($handle->processed) {
				$mosmsg = _FILES_UPLOADED;
			} else {
				$mosmsg = _FILES_NOT_UPLOADED;
			}
		} else {
			//$mosmsg = 'Файлы не загружены на сервер!';
		}
	}
	return $mosmsg;
}

function delete_folder($listdir,$delFolder) {
	$del_html = JWMMXTD_STARTABSPATH.$listdir.DS.$delFolder.DS.'index.html';
	$del_folder = JWMMXTD_STARTABSPATH.$listdir.DS.$delFolder;
	$entry_count = 0;
	$dir = opendir($del_folder);
	while($entry = readdir($dir)) {
		if($entry != "." & $entry != ".." & strtolower($entry) != "index.html") $entry_count++;
	}
	closedir($dir);
	if($entry_count < 1) {
		@unlink($del_html);
		if(rmdir($del_folder))
			$ok = true;
		else
			$ok = false;
	} else {
		$ok = false;
	}
	return $ok;
}

function delete_file($listdir,$delFile) {
	$fullPath = JWMMXTD_STARTABSPATH.$listdir.DS.stripslashes($delFile);
	if(file_exists($fullPath)) {
		if(unlink($fullPath)) return true;
	}
	return false;
}

function listofImages($listdir) {
	$listdir = JWMMXTD_STARTABSPATH.$listdir;
	$d = @dir($listdir);

	if($d) {
		$images = array();
		$folders = array();
		$docs = array();
		// к изображениям относятся только файлы перечисленного типа
		$allowable = '/xcf|odg|gif|jpg|png|bmp/i';
		while(false !== ($entry = $d->read())) {
			$img_file = $entry;
			if(is_file($listdir.'/'.$img_file) && substr($entry,0,1) != '.' && strtolower($entry)
					!== 'index.html') {
				if(preg_match($allowable,$img_file)) {
					$image_info = @getimagesize($listdir.'/'.$img_file);
					$file_details['file'] = $listdir."/".$img_file;
					$file_details['img_info'] = $image_info;
					$file_details['size'] = filesize($listdir."/".$img_file);
					$images[$entry] = $file_details;
				} else {
					$file_details['size'] = filesize($listdir."/".$img_file);
					$file_details['file'] = $listdir."/".$img_file;
					$docs[$entry] = $file_details;
				}
			} else
			if(is_dir($listdir.'/'.$img_file) && substr($entry,0,1) != '.' && strtolower($entry)!== 'cvs') {
				$folders[$entry] = $img_file;
			}
		}
		$d->close();
		if(count($images) > 0 || count($folders) > 0 || count($docs) > 0) {
			// сортировка файлов и каталогов по имени
			ksort($images);
			ksort($folders);
			ksort($docs);

			// подкаталоги
			if(count($folders) > 0) {
				echo '<fieldset><legend>'._DIRECTORIES.'</legend>';
				for($i = 0; $i < count($folders); $i++) {
					$folder_name = key($folders);
					HTML_mmxtd::show_dir($folders[$folder_name],$folder_name,str_replace(JWMMXTD_STARTABSPATH,"",$listdir));
					next($folders);
				}
				echo '</fieldset>';
			}

			// изображения
			if(count($images) > 0) {
				echo '<fieldset><legend>'._IMAGES.'</legend>';
				for($i = 0; $i < count($images); $i++) {
					$image_name = key($images);
					HTML_mmxtd::show_image($images[$image_name]['file'],$image_name,$images[$image_name]['img_info'],$images[$image_name]['size'],str_replace(JWMMXTD_STARTABSPATH,"",$listdir));
					next($images);
				}
				echo "</fieldset>";
			}
			// разные файлы
			if(count($docs) > 0) {
				echo '<fieldset><legend>'._JWMM_FILE.'</legend>';
				for($i = 0; $i < count($docs); $i++) {
					$doc_name = key($docs);
					//$iconfile = JPATH_BASE.'/images/icons/'.substr($doc_name,-3).'.png';

					$mainframe = mosMainFrame::getInstance(true);
					$iconfile = JPATH_BASE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico/'.substr($doc_name,-3).'.png';

					if(file_exists($iconfile)) {
						$icon = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico/'.substr($doc_name,-3).'.png';
					} else {
						$icon = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico/file.png';
					}
					$icon = strtolower($icon);
					HTML_mmxtd::show_doc($doc_name,$docs[$doc_name]['size'],str_replace(JWMMXTD_STARTABSPATH,'',$listdir),$icon);
					next($docs);
				}
				echo '</fieldset>';
			}
		} else {
		}
	} else {
	}
}

function listImagesBak($dirname = '.') {
	return glob($dirname.'*.{jpg,png,jpeg,gif}',GLOB_BRACE);
}
// создание каталога
function create_folder($curdirectory,$folder_name) {
	$folder_name = str_replace(" ","_",$folder_name);
	if(strlen($folder_name) > 0) {
		if(preg_match("/[^0-9a-zA-Z_]/i",$folder_name)) {
			mosRedirect("index2.php?option=com_jwmmxtd&curdirectory=".$curdirectory,_JWMM_FILE_NAME_WARNING);
		}
		$folder = JWMMXTD_STARTABSPATH.$curdirectory.DS.$folder_name;
		if(!is_dir($folder) && !is_file($folder)) {
			$suc = mosMakePath($folder);
			$fp = fopen($folder."/index.html","w");
			fwrite($fp,"<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
			fclose($fp);
			mosChmod($folder."/index.html");
			return $suc;
		}
	}
}
// список подкаталогов
function listofdirectories($base) {
	static $filelist = array();
	static $dirlist = array();
	if(is_dir($base)) {
		$dh = opendir($base);
		while(false !== ($dir = readdir($dh))) {
			if(is_dir($base.'/'.$dir) && $dir !== '.' && $dir !== '..' && strtolower($dir)!== 'cvs' && strtolower($dir) !== '.svn') {
				$subbase = $base.'/'.$dir;
				$dirlist[] = $subbase;
				$subdirlist = listofdirectories($subbase);
			}
		}
		closedir($dh);
	}
	return $dirlist;
}

// отображение медиа-менеджера
function viewMediaManager($curdirectory = "",$mosmsg = "",$selectedfile = "") {
	global $subtask;
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();

	$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico';

	$imgFiles = listofdirectories(JWMMXTD_STARTABSPATH);
	$folders = array();
	$folders[] = mosHTML::makeOption("","/");
	$len = strlen(JWMMXTD_STARTABSPATH);
	foreach($imgFiles as $file) {
		$folders[] = mosHTML::makeOption(substr($file,$len));
	}
	if(is_array($folders)) {
		sort($folders);
	}
	$dirPath = mosHTML::selectList($folders,'curdirectory',"class=\"inputbox\" size=\"5\" style=\"width:95%;\" onchange=\"document.adminForm.task.value='';document.adminForm.submit( );\" ",'value','text',$curdirectory);
	if($curdirectory == '') {
		$upcategory = '';
	}else {
		$tmp = explode('/',$curdirectory);
		end($tmp);
		unset($tmp[key($tmp)]);
		$upcategory = implode('/',$tmp);
	}
	// сообщения о ошибках, уведомления
	if($mosmsg) {
		echo '<div class="message">'.$mosmsg.'</div>';
	}
	?>
<div id="jwmmxtd">
	<form action="index2.php" name="adminForm" method="POST" enctype="multipart/form-data">
		<table cellpadding="0" cellspacing="0" style="width:100%;" id="upper" class="adminheading">
			<tr>
				<th class="media"><?php echo _MEDIA_MANAGER?></th>
				<td id="browse"><table cellpadding="0" cellspacing="4" align="right">
						<tr>
							<td><?php echo _JWMM_CREATE_DIRECTORY?>:</td>
							<td><input style="width:200px;" class="inputbox" type="text" name="createfolder" id="createfolder" /></td>
							<td>
								<input type="button" class="button" onclick="javascript:document.adminForm.task.value='createfolder';document.adminForm.submit( );" value="<?php echo _NEW?>" />
							</td>
						</tr>
						<tr>
							<td><?php echo _UPLOAD_FILE?>:</td>
							<td><input type="file" class="inputbox multi" name="upimage[]" maxlength="8" /></td>
							<td>
								<input type="button" class="button" onclick="javascript:document.adminForm.task.value='uploadimages';document.adminForm.submit( );" value="<?php echo _TASK_UPLOAD?>" />
							</td>
						</tr>
					</table></td>
			</tr>
		</table>
		<table style="width:100%;" cellpadding="0" cellspacing="0">
			<tr>
				<td><?php echo _JWMM_IMAGE_LINK ?></td><td><input onfocus="this.select()" type="text" id="file_link" name="file_link" class="inputbox" size="100"/></td>
				<td rowspan="3" width="50%"><?php echo _FILE_PATH?>:<a href="index2.php?option=com_jwmmxtd&amp;curdirectory=<?php echo $upcategory; ?>"><img src="<?php echo $cur_file_icons_path;?>/uparrow.png" alt="<?php echo _JWMM_UP_TO_DIRECTORY?>" /></a><br /><?php echo $dirPath; ?></td>
			</tr>
			<tr><td><?php echo _JWMM_IMAGE_HREF ?></td><td><input onfocus="this.select()" type="text" id="file_href" name="file_href" class="inputbox" size="100"/></td></tr>
			<tr><td><?php echo _JWMM_IMAGE_TAG ?></td><td><input onfocus="this.select()" type="text" id="file_url" name="file_url" class="inputbox" size="100"/></td></tr>
		</table>
		<div id="actions">
				<?php if($selectedfile != "" && $subtask == "renamefile") { ?>
			<fieldset class="block">
				<legend><?php echo _JWMM_RENAMING?>: <span><?php echo $selectedfile; ?></span></legend>
				<input type="hidden" name="curfile" value="<?php echo $selectedfile; ?>"><?php echo _JWMM_NEW_NAME?>:
				<input type="text" name="newfilename" id="newfilename">
				<input type="button" onclick="javascript:document.adminForm.task.value='alterfilename';document.adminForm.submit( );" class="button" value="<?php echo _RENAME?>" />
			</fieldset>
					<?php } ?>

				<?php if($selectedfile != "" && $subtask == "copyfile") { ?>
			<fieldset class="block">
				<legend><?php echo _CHOOSE_DIR_TO_COPY?>:<span><?php $selectedfile; ?></span></legend>
				<input type="hidden" name="curfile" value="<?php echo $selectedfile; ?>">
						<?php echo _JWMM_COPY_TO?>: <?php echo mosHTML::selectList($folders,'dirtocopy',"class=\"inputbox\" size=\"1\" ",'value','text',$curdirectory); ?>
				<input type="button" onclick="javascript:document.adminForm.task.value='copyfile';document.adminForm.submit( );" class="button" value="<?php echo _COPY?>" />
			</fieldset>
					<?php }if
	($selectedfile != "" && $subtask == "movefile") { ?>
			<fieldset class="block">
				<legend><?php echo _CHOOSE_DIR_TO_MOVE?>:<span><?php echo $selectedfile; ?></span></legend>
				<input type="hidden" name="curfile" value="<?php echo $selectedfile; ?>">
		<?php echo _MOVE_TO?>: <?php echo mosHTML::selectList($folders,'dirtomove','class="inputbox" size="1" ','value','text',$curdirectory); ?>
				<input type="button" onclick="javascript:document.adminForm.task.value='movefile';document.adminForm.submit( );" class="button" value="<?php echo _MOVE?>" />
			</fieldset>
		<?php }if
	($selectedfile != "" && $subtask == "unzipfile") {?>
			<fieldset class="block">
				<legend><?php echo _CHOOSE_DIR_TO_UNPACK?>:<span><?php echo $selectedfile; ?></span></legend>
				<input type="hidden" name="curfile" value="<?php echo $selectedfile; ?>" />
		<?php echo _DERICTORY_TO_UNPACK?>:<?php echo mosHTML::selectList($folders,'dirtocopy',"class=\"inputbox\" size=\"1\" ",'value','text',$curdirectory); ?>
				<input type="button" onclick="javascript:document.adminForm.task.value='unzipfile';document.adminForm.submit( );" class="button" value="<?php echo _UNPACK?>" />
			</fieldset>
		<?php } ?>

		</div>
		<input type="hidden" name="selectedfile" value="">
		<input type="hidden" name="subtask" value="">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="option" value="com_jwmmxtd">
	</form>
	<div class="jwmmxtd_clr"></div>
	<?php echo listofImages($curdirectory); ?>
	<div class="jwmmxtd_clr"></div>
	<div id="jwmmxtd_tmp">
			<?php if($my->gid == 25 || $my->gid == 24) {
				echo _NUMBER_OF_IMAGES_IN_TMP_DIR.': ';
				$dir = JPATH_BASE.'/media/';
				$total_file = 0;
				if(is_dir($dir)) {
					$d = dir($dir);
					while(false !== ($entry = $d->read())) {
						if(substr($entry,-4) == ".jpg" || substr($entry,-4) == ".gif" || substr($entry,-4) == ".png") {
							$total_file++;
						}
					}
					$d->close();
				}
		echo $total_file;
				?>
		<input type="button" class="button" onclick="javascript:document.adminForm.task.value='emptytmp';document.adminForm.submit( );" value="<?php echo _CLEAR_DIRECTORY?>" />
		<?php } ?>
	</div>
</div>
	<?php
}
// отмена всех действий по редактированию изображения
function OriginalImage($aFormValues) {
	require_once ('class.upload.php');
	$primage		= $aFormValues['primage'];
	$orimage		= $aFormValues['originalimage'];
	$curdirectory	= $aFormValues['curdirectory'];
	@unlink(JPATH_BASE.DS.'media'.DS.$primage);
	$objResponse	= new xajaxResponse();
	$objResponse->addAssign("mmxtd","innerHTML","<img name=\"mainimage\" id=\"mainimage\" src='".JWMMXTD_STARTURLPATH.$curdirectory."/".$orimage."'>");
	$objResponse->addAssign("imagepath","value",JWMMXTD_STARTURLPATH.$curdirectory."/".$orimage);
	return $objResponse;
}

function UpdateImage($aFormValues) {
	require_once ('class.upload.php');

	$imagepath	= $aFormValues['imagepath'];
	$imagepath	= str_replace(JWMMXTD_STARTURLPATH,JWMMXTD_STARTABSPATH,$imagepath);

	$width		= intval($aFormValues['width']);
	$height		= intval($aFormValues['height']);
	$convert	= trim($aFormValues['convert']);
	$crop		= trim($aFormValues['crop']);
	$cropv		= trim($aFormValues['cropv']);
	$cropo		= trim($aFormValues['cropo']);
	$cropt		= trim($aFormValues['cropt']);
	$cropr		= trim($aFormValues['cropr']);
	$cropb		= trim($aFormValues['cropb']);
	$cropl		= trim($aFormValues['cropl']);
	$rotation	= intval($aFormValues['rotation']);
	$flip		= trim($aFormValues['flip']);
	$bevelpx	= intval($aFormValues['bevelpx']);
	$beveltl	= trim($aFormValues['beveltl']);
	$bevelrb	= trim($aFormValues['bevelrb']);
	$borderw	= trim($aFormValues['borderw']);
	$borderc	= trim($aFormValues['borderc']);
	$bordert	= trim($aFormValues['bordert']);
	$borderr	= trim($aFormValues['borderr']);
	$borderb	= trim($aFormValues['borderb']);
	$borderl	= trim($aFormValues['borderl']);
	$borderc2	= trim($aFormValues['borderc2']);
	$tint		= trim($aFormValues['tint']);
	$overlayp	= trim($aFormValues['overlayp']);
	$overlayc	= trim($aFormValues['overlayc']);
	$brightness	= intval($aFormValues['brightness']);
	$contrast	= intval($aFormValues['contrast']);
	$threshold	= intval($aFormValues['threshold']);
	$bgcolor	= trim($aFormValues['bgcolor']);
	$bgpercent	= intval($aFormValues['bgpercent']);
	$text		= trim($aFormValues['text']);
	$textcolor	= trim($aFormValues['textcolor']);
	$textfont	= trim($aFormValues['textfont']);

	if(isset($aFormValues['primage']))
		$primage = $aFormValues['primage'];
	else
		$primage = 0;
	if(isset($aFormValues['greyscale']))
		$greyscale = $aFormValues['greyscale'];
	else
		$greyscale = 0;
	if(isset($aFormValues['negative']))
		$negative = $aFormValues['negative'];
	else
		$negative = 0;

	$textpercent	= intval($aFormValues['textpercent']);
	$textdirection	= trim($aFormValues['textdirection']);
	$textposition	= trim($aFormValues['textposition']);
	$textpaddingx	= intval($aFormValues['textpaddingx']);
	$textpaddingy	= intval($aFormValues['textpaddingy']);
	$textabsolutex	= intval($aFormValues['textabsolutex']);
	$textabsolutey	= intval($aFormValues['textabsolutey']);

	$pic = new upload($imagepath);
	if($pic->uploaded) {
		$pic->file_new_name_body = md5(uniqid("mmxtd"));
		if($width > 0 || $height > 0) {
			$pic->image_resize = true;
		}
		if($width > 0 && $height > 0) {
			$pic->image_x = $width;
			$pic->image_y = $height;
		}
		if($width > 0 && $height == 0) {
			$pic->image_x = $width;
			$pic->image_ratio_y = true;
		}
		if($height > 0 && $width == 0) {
			$pic->image_y = $height;
			$pic->image_ratio_x = true;
		}
		if($crop != "") {
			$pic->image_crop = $crop;
		} elseif($cropv != "" && $cropo != "") {
			$pic->image_crop = $cropv." ".$cropo;
		} elseif($cropt != "" && $cropr != "" && $cropb != "" && $cropl != "") {
			$pic->image_crop = $cropt." ".$cropr." ".$cropb." ".$cropl;
		}
		if($rotation > 0) {
			$pic->image_rotate = $rotation;
		}
		if($flip != "none") {
			$pic->image_flip = $flip;
		}
		if($convert != "none") {
			$pic->image_convert = $convert;
		}
		if($bevelpx > 0 && $beveltl != "" && $bevelrb != "") {
			$pic->image_bevel = $bevelpx;
			$pic->image_bevel_color1 = $beveltl;
			$pic->image_bevel_color2 = $bevelrb;
		}
		if($borderw != "" && $borderc != "") {
			$pic->image_border = $borderw;
			$pic->image_border_color = $borderc;
		} elseif($bordert != "" && $borderr != "" && $borderb != "" && $borderl != "" &&
				$borderc2 != "") {
			$pic->image_border = $bordert." ".$borderr." ".$borderb." ".$borderl;
			$pic->image_border_color = $borderc2;
		}
		if($tint != "") {
			$pic->image_tint_color = $tint;
		}
		if($overlayp != "" && $overlayc != "") {
			$pic->image_overlay_percent = $overlayp;
			$pic->image_overlay_color = $overlayc;
		}
		if($brightness != 0) {
			$pic->image_brightness = $brightness;
		}
		if($contrast != 0) {
			$pic->image_contrast = $contrast;
		}
		if($threshold != 0) {
			$pic->image_threshold = $threshold;
		}
		if($greyscale) {
			$pic->image_greyscale = true;
		}
		if($negative) {
			$pic->image_negative = true;
		}
		if($text != "") {
			$pic->image_text = $text;
			if($textcolor != "") {
				$pic->image_text_color = "$textcolor";
			}
			if($textfont != "") {
				$pic->image_text_font = $textfont;
			}
			if($textpercent != 0) {
				$pic->image_text_percent = $textpercent;
			}
			if($textdirection != "") {
				$pic->image_text_direction = $textdirection;
			}
			if($textposition != "") {
				$pic->image_text_position = $textposition;
			}
			if($bgcolor != "") {
				$pic->image_text_background = $bgcolor;
			}
			if($bgpercent != 0) {
				$pic->image_text_background_percent = $bgpercent;
			}
			if($textpaddingx != 0) {
				$pic->image_text_padding_x = $textpaddingx;
			}
			if($textpaddingy != 0) {
				$pic->image_text_padding_y = $textpaddingy;
			}
			if($textabsolutex != 0) {
				$pic->image_text_x = $textabsolutex;
			}
			if($textabsolutey != 0) {
				$pic->image_text_y = $textabsolutey;
			}
		}
		$pic->Process(JPATH_BASE.'/media/');
		if($pic->processed) {
			$img2out = '<img name="mainimage" id="mainimage" src="'.JPATH_SITE.'/media/'.$pic->file_dst_name.'" />';
			@unlink(JPATH_BASE.'/media/'.$primage);
			$primage = $pic->file_dst_name;
		}
	} else $img2out = _JWMM_ERROR_EDIT_FILE." ".$imagepath;

	$objResponse = new xajaxResponse();
	//$objResponse->addAssign("mymsg","innerHTML",$imagepath."--".$primage);
	$objResponse->addAssign("tb-apply","className",'tb-apply'); // скрываем слой с индикатором выполнения процесса
	$objResponse->addClear("mainimage","src");
	$objResponse->addAssign("loading_placeholder","innerHTML",'');
	$objResponse->addAssign("mmxtd","innerHTML",$img2out);
	$objResponse->addAssign("primage","innerHTML","<input type=\"hidden\" name=\"primage\" id=\"primage\" value=\"".$primage."\">");
	$objResponse->addAssign("imagepath","value",JPATH_BASE.'/media/'.$primage);
	$objResponse->addAssign("width","value","");
	$objResponse->addAssign("height","value","");
	$objResponse->addAssign("rotation","value","0");
	$objResponse->addAssign("flip","value","none");
	$objResponse->addAssign("convert","value","none");
	$objResponse->addAssign("bevelpx","value","");
	$objResponse->addAssign("beveltl","value","");
	$objResponse->addAssign("bevelrb","value","");
	$objResponse->addAssign("borderw","value","");
	$objResponse->addAssign("borderc","value","");
	$objResponse->addAssign("bordert","value","");
	$objResponse->addAssign("borderr","value","");
	$objResponse->addAssign("borderb","value","");
	$objResponse->addAssign("borderl","value","");
	$objResponse->addAssign("borderc2","value","");
	$objResponse->addAssign("tint","value","");
	$objResponse->addAssign("overlayp","value","");
	$objResponse->addAssign("overlayc","value","");
	$objResponse->addAssign("brightness","value","");
	$objResponse->addAssign("contrast","value","");
	$objResponse->addAssign("threshold","value","");
	$objResponse->addAssign("greyscale","checked",false);
	$objResponse->addAssign("negative","checked",false);
	$objResponse->addAssign("text","value","");
	$objResponse->addAssign("textcolor","value","");
	$objResponse->addAssign("textfont","value","");
	$objResponse->addAssign("textpercent","value","");
	$objResponse->addAssign("textdirection","value","none");
	$objResponse->addAssign("textposition","value","none");
	$objResponse->addAssign("bgcolor","value","");
	$objResponse->addAssign("bgpercent","value","");
	$objResponse->addAssign("textpaddingx","value","");
	$objResponse->addAssign("textpaddingy","value","");
	$objResponse->addAssign("textabsolutex","value","");
	$objResponse->addAssign("textabsolutey","value","");
	return $objResponse;
}

function editImage($img,$cur) {
	global $option;
	require_once (JPATH_BASE.'/includes/libraries/xajax/xajax.inc.php');
	$path = JWMMXTD_STARTURLPATH.$cur.'/'.$img;
	$xajax = new xajax();
	//$xajax->debugOn();
	$xajax->registerFunction('UpdateImage');
	$xajax->registerFunction('OriginalImage');
	$xajax->registerFunction('MoveImage');
	$xajax->processRequests();
	$xajax->printJavascript(JPATH_SITE.'/includes/libraries/xajax');
	?>
<script type="text/javascript">
	function UpdateImg(value){
		SRAX.get('tb-apply').className='tb-load';
		xajax_UpdateImage(value);
	}
</script>
<div id="loading_placeholder"></div>
<div id="jimgedit">
	<table class="adminheading">
		<tr><th class="media"><?php echo _JWMM_EDIT_IMAGE?></th></tr>
	</table>
	<div id="mymsg"></div>
	<div id="show_image_path"><?php echo _FILE?>:<b><?php echo $path; ?></b></div>
	<div id="jwmmxtd_editpage">
		<div id="jwmmxtd_image">
			<div id="mmxtd"><img name="mainimage" id="mainimage" src="<?php echo $path; ?>" /></div>
		</div>
		<div id="jwmmxtd_panel">
			<form method="POST" id="adminForm" name="adminForm" enctype="multipart/form-data" onSubmit="return false;">
				<fieldset><legend><?php echo _HEIGHT?> x <?php echo _WIDTH?></legend>
						<?php echo _WIDTH?><input id="width" name="width" type="text" size="4" />
			x<input id="height" name="height" type="text" size="4" />
	<?php echo _HEIGHT?>
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_IMAGE_RESIZE?></legend>
	<?php echo _JWMM_IMAGE_RESIZE?>
					<select id="convert" name="convert">
						<option value="none"><?php echo _SEL_TYPE?></option>
						<option value="jpg">jpg</option>
						<option value="gif">gif</option>
						<option value="png">png</option>
					</select>
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_IMAGE_CROP?></legend>
					<fieldset>
						<legend><?php echo _JWMM_IMAGE_SIZE?></legend>
	<?php echo _JWMM_IMAGE_SIZE?>
						<input id="crop" name="crop" type="text" size="4" />
					</fieldset>
					<fieldset>
						<legend><?php echo _JWMM_X_Y_POSITION?></legend>
	<?php echo _VERICAL?>:<input id="cropv" name="cropv" type="text" size="4" />
	<?php echo _HORIZONTAL?>:<input id="cropo" name="cropo" type="text" size="4" />
					</fieldset>
					<fieldset>
						<legend><?php echo _JWMM_IMAGE_CROP?></legend>
						<table cellpadding="0" cellspacing="0" style="text-align:center;">
							<tr>
								<td><?php echo _JWMM_CROP_TOP?><br />
									<input id="cropt" name="cropt" type="text" size="4" />
								</td>
							</tr>
							<tr>
								<td>
	<?php echo _LEFT?><input id="cropl" name="cropl" type="text" size="4" />
									&nbsp;
									<input id="cropr" name="cropr" type="text" size="4" />
	<?php echo _RIGHT?>
								</td>
							</tr>
							<tr>
								<td><input id="cropb" name="cropb" type="text" size="4" />
									<br /><?php echo _JWMM_BOTTOM?>
								</td>
							</tr>
						</table>
					</fieldset>
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_ROTATION?></legend>
	<?php echo _JWMM_ROTATION?>
					<select id="rotation" name="rotation">
						<option value="0"><?php echo _JWMM_CHOOSE?></option>
						<option value="90">90</option>
						<option value="180">180</option>
						<option value="270">270</option>
					</select>
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_MIRROR?></legend>
	<?php echo _JWMM_MIRROR?>
					<select id="flip" name="flip">
						<option value="none"><?php echo _JWMM_CHOOSE?></option>
						<option value="H"><?php echo _VERICAL?></option>
						<option value="V"><?php echo _HORIZONTAL?></option>
					</select>
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_GRADIENT_BORDER?></legend>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td><?php echo _JWMM_SIZE_PX?></td>
							<td><input id="bevelpx" name="bevelpx" type="text" /></td>
						</tr>
						<tr>
							<td><?php echo _JWMM_TOP_LEFT?></td>
							<td><input id="beveltl" name="beveltl" type="text" />
								<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.beveltl)">
									<img width="16" height="16" border="0" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>">
								</a>
							</td>
						</tr>
						<tr>
							<td><?php echo _JWMM_BOTTOM_RIGHT?></td>
							<td><input id="bevelrb" name="bevelrb" type="text" />
								<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.bevelrb)"><img width="16" height="16" border="0" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>"></a></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_BORDER?></legend>
					<fieldset>
						<legend><?php echo _JWMM_BORDER?></legend>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td><php echo _WIDTH></td>
								<td><input id="borderw" name="borderw" type="text" /></td>
								</tr>
								<tr>
									<td><?php echo _COLOR?></td>
									<td><input id="borderc" name="borderc" type="text" />
										<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.borderc)"><img width="16" height="16" border="0" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>"> </a></td>
								</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo _JWMM_ALL_BORDERS?></legend>
						<table cellpadding="0" cellspacing="0" style="text-align:center;">
							<tr>
								<td><?php echo _JWMM_TOP?><br /><input id="bordert" name="bordert" type="text" size="4" /></td>
							</tr>
							<tr>
								<td><?php echo _JWMM_LEFT?><input id="borderl" name="borderl" type="text" size="4" />&nbsp;
									<input id="borderr" name="borderr" type="text" size="4" />
	<?php echo _RIGHT?></td>
							</tr>
							<tr>
								<td><input id="borderb" name="borderb" type="text" size="4" />
									<br />
	<?php echo _JWMM_BOTTOM?><br />
	<?php echo _COLOR?>
									<input id="borderc2" name="borderc2" type="text" />
									<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.borderc2)"><img width="16" height="16" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>"></a> </td>
							</tr>
						</table>
					</fieldset>
				</fieldset>
				<fieldset>
					<legend>Tint Color</legend>
	<?php echo _COLOR?>
					<input id="tint" name="tint" type="text" />
					<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.tint)"> <img width="16" height="16" border="0" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>"> </a>
				</fieldset>
				<fieldset>
					<legend>Overlay</legend>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>Percent</td>
							<td><input id="overlayp" name="overlayp" type="text" size="4" /></td>
						</tr>
						<tr>
							<td><?php echo _COLOR?></td>
							<td><input id="overlayc" name="overlayc" type="text" />
								<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.overlayc)"> <img width="16" height="16" border="0" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>"> </a></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_BRIGHTNESS?></legend>
					<input id="brightness" name="brightness" type="text" />
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_CONTRAST?></legend>
					<input id="contrast" name="contrast" type="text" />
				</fieldset>
				<fieldset>
					<legend>Threshold filter</legend>
					<input id="threshold" name="threshold" type="text" />
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_ADDITIONAL_ACTIONS?></legend>
	<?php echo _JWMM_GRAY_SCALE?><input type="checkbox" name="greyscale" id="greyscale">
	<?php echo _JWMM_NEGATIVE?><input type="checkbox" name="negative" id="negative">
				</fieldset>
				<fieldset>
					<legend><?php echo _JWMM_ADD_TEXT?></legend>
					<table cellpadding="0" cellspacing="2">
						<tr>
							<td><?php echo _JWMM_TEXT?></td>
							<td><input type="text" name="text" id="text">
							</td>
						</tr>
						<tr>
							<td><?php echo _JWMM_TEXT_COLOR?></td>
							<td><input type="text" name="textcolor" id="textcolor">
								<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.textcolor)"> <img width="16" height="16" border="0" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>"> </a> </td>
						</tr>
						<tr>
							<td><?php echo _JWMM_TEXT_FONT?></td>
							<td><input type="text" name="textfont" id="textfont">
							</td>
						</tr>
						<tr>
							<td><?php echo _JWMM_TEXT_SIZE?></td>
							<td><input type="text" name="textpercent" id="textpercent"></td>
						</tr>
						<tr>
							<td><?php echo _JWMM_ORIENTATION?></td>
							<td><select name="textdirection" id="textdirection">
									<option value="none"><?php echo _JWMM_CHOOSE?></option>
									<option value="h"><?php echo _HORIZONTAL?></option>
									<option value="v"><?php echo _VERICAL?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo _POSITION?></td>
							<td><select name="textposition" id="textposition">
									<option value="none"><?php echo _JWMM_CHOOSE?></option>
									<option value="TL">Top - Left</option>
									<option value="T">Top</option>
									<option value="TR">Top - Right</option>
									<option value="L">Left</option>
									<option value="R">Right</option>
									<option value="BL">Bottom - Left</option>
									<option value="B">Bottom</option>
									<option value="BR">Bottom - Right</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Bg Percent</td>
							<td><input type="text" name="bgpercent" id="bgpercent">
							</td>
						</tr>
						<tr>
							<td><?php echo _JWMM_BG_COLOR?></td>
							<td><input type="text" name="bgcolor" id="bgcolor">
								<a style="cursor:pointer;" onClick="showColorPicker(this,document.adminForm.bgcolor)">
									<img width="16" height="16" border="0" alt="<?php echo _JWMM_PRESS_TO_CHOOSE_COLOR?>" src="<?php echo JPATH_SITE.'/'.JADMIN_BASE.'/components/com_jwmmxtd/images/color_wheel.png'; ?>"> </a> </td>
						</tr>
						<tr>
							<td><?php echo _JWMM_XY_POSITION?></td>
							<td>
					X:<input type="text" name="textpaddingx" id="textpaddingx" size="4">
					Y:<input type="text" name="textpaddingy" id="textpaddingy" size="4">
							</td>
						</tr>
						<tr>
							<td><?php echo _JWMM_XY_PADDING?></td>
							<td>
					X:<input type="text" name="textabsolutex" id="textabsolutex" size="4">
					Y:<input type="text" name="textabsolutey" id="textabsolutey" size="4">
							</td>
						</tr>
					</table>
				</fieldset>
				<input type="hidden" name="imagepath" id="imagepath" value="<?php echo $path; ?>">
				<input type="hidden" name="originalimage" id="originalimage" value="<?php echo $img; ?>">
				<input type="hidden" name="curdirectory" id="curdirectory" value="<?php echo $cur; ?>">
				<input type="hidden" name="option" id="option" value="<?php echo $option; ?>">
				<input type="hidden" name="task" id="task" value="">
				<div id="primage"></div>
			</form>
		</div>
		<div class="jwmmxtd_clr"></div>
		<script type="text/javascript">
			initFloatingWindowWithTabs('editor_panel',Array(_PN_START,_JWMM_SECOND,_JWMM_THIRDTH),100,450,80,60,true,false,false,true);
		</script>
	</div>
</div>
	<?php } ?>