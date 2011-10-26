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
 * @subpackage Weblinks
 */
class HTML_weblinks {

	public static function displaylist(&$categories,&$rows,$catid,$currentcat = null,$params,$tabclass) {
		global $hide_js;

		?>
<div class="weblinks_page <?php echo $params->get('pageclass_sfx'); ?>">
	<form action="index.php" method="post" name="adminForm">
				<?php if($params->get('page_title')) { ?>
		<div class="componentheading"><h1><?php echo $currentcat->header; ?></h1></div>
					<?php } ?>

				<?php if ($currentcat->descrip || $currentcat->img) {?>
		<div class="contentdescription">
						<?php if($currentcat->img) { ?>
			<img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" hspace="6" alt="<?php echo _LINKS; ?>" />
							<?php } ?>

						<?php echo $currentcat->descrip; ?>
		</div>
					<?php } ?>

				<?php if(count($rows)) { ?>
		<div class="weblinks_list">
						<?php HTML_weblinks::showTable($params,$rows,$catid,$tabclass); ?>
		</div>
					<?php } ?>

		<div class="weblinks_cats">
					<?php if(($params->get('type') == 'category') && $params->get('other_cat')) {
						HTML_weblinks::showCategories($params,$categories,$catid);
					} else
					if(($params->get('type') == 'section') && $params->get('other_cat_section')) {
						HTML_weblinks::showCategories($params,$categories,$catid);
					}
					?>
		</div>
	</form>

			<?php mosHTML::BackButton($params,$hide_js); ?>
</div>
		<?php
	}

	/**
	 * Display Table of items
	 */
	public static function showTable(&$params,&$rows,$catid,$tabclass) {
		global $cwl_i;
		if(!isset($cwl_i)) $cwl_i = '';

		?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">

			<?php if($params->get('headings')) { ?>
	<tr>

					<?php if($params->get('weblink_icons') != -1) { ?>
		<th>&nbsp;</th>
						<?php } ?>

		<th width="90%"><?php echo _WEBLINK; ?></th>

					<?php if($params->get('hits')) { ?>
		<th width="30"><?php echo _COM_LINKS_HEADER_HITS; ?></th>
						<?php } ?>

	</tr>
				<?php } ?>

			<?php
			$k = 0;
			foreach($rows as $row) {
				// icon in table display
				if($params->get('weblink_icons') != -1) {
					$img = mosAdminMenus::ImageCheck('weblink.png','/images/M_images/',$params->get
							('weblink_icons'),'/images/M_images/','Link','Link'.$cwl_i);
					$cwl_i++;
				} else {
					$img = null;
				}
				$iparams = new mosParameters($row->params);

				$link = sefRelToAbs('index.php?option=com_weblinks&task=view&catid='.$catid.'&id='.$row->id);
				$link = ampReplace($link);

				$menuclass = 'category'.$params->get('pageclass_sfx');

				switch($iparams->get('target')) {
					// cases are slightly different
					case 1:
					// open in a new window
						$txt = '<a href="'.$link.'" target="_blank" class="'.$menuclass.'">'.$row->title.'</a>';
						break;

					case 2:
					// open in a popup window
						$txt = "<a href=\"#\" onclick=\"javascript: window.open('".$link."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\" class=\"$menuclass\">".$row->title."</a>\n";
						break;

					default: // formerly case 2
					// open in parent window
						$txt = '<a href="'.$link.'" class="'.$menuclass.'">'.$row->title.'</a>';
						break;
				}
				?>


	<tr class="<?php echo $tabclass[$k]; ?>">

					<?php if($img) { ?>
		<td width="100" height="20" align="center">&nbsp;&nbsp;<?php echo $img; ?>&nbsp;&nbsp;</td>
						<?php } ?>

		<td height="20">
						<?php echo $txt;
						if($params->get('item_description')) { ?>
			<br />
							<?php echo $row->description;
						} ?>
		</td>

					<?php if($params->get('hits')) { ?>
		<td align="center"><?php echo $row->hits; ?></td>
						<?php } ?>

	</tr>

				<?php $k = 1 - $k;
			}
			?>

</table>
		<?php
	}

	/**
	 * Display links to categories
	 */
	public static function showCategories(&$params,&$categories,$catid) {
		global $Itemid;
		?>
<ul>
			<?php
			foreach($categories as $cat) {
				if($catid == $cat->catid) {
					?>
	<li>
		<b><?php echo stripslashes($cat->name); ?></b>
		&nbsp;
		<span class="small">
					(<?php echo $cat->numlinks; ?>)
		</span>
	</li>
					<?php
				} else {
					$link = 'index.php?option=com_weblinks&amp;catid='.$cat->catid.'&amp;Itemid='.$Itemid;
					?>
	<li>
		<a href="<?php echo sefRelToAbs($link); ?>" class="category<?php echo $params->get('pageclass_sfx'); ?>">
							<?php echo stripslashes($cat->name); ?>
		</a>
		&nbsp;
		<span class="small">
					(<?php echo $cat->numlinks; ?>)
		</span>
	</li>
					<?php
				}
			}
			?>
</ul>
		<?php
	}

	/**
	 * Writes the edit form for new and existing record (FRONTEND)
	 *
	 * A new record is defined when <var>$row</var> is passed with the <var>id</var>
	 * property set to 0.
	 * @param mosWeblink The weblink object
	 * @param string The html for the categories select list
	 */
	public static function editWeblink($option,&$row,&$lists) {

		$Returnid = intval(mosGetParam($_REQUEST,'Returnid',0));

		// used for spoof hardening
		$validate = josSpoofValue();
		?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.title.value == ""){
			alert( "<?php echo _COM_LINKS_NONE_TITLE?>" );
		} else if (getSelectedValue('adminForm','catid') < 1) {
			alert( "<?php echo _COM_LINKS_NONE_CAT?>" );
		} else if (form.url.value == ""){
			alert( "<?php echo _COM_LINKS_NONE_URL?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>

<div class="weblinks_add">

	<div class="componentheading"><h1><?php echo _SUBMIT_LINK; ?></h1></div>

	<br />
	<form action="<?php echo sefRelToAbs("index.php"); ?>" method="post" name="adminForm" id="adminForm">

		<table cellpadding="4" cellspacing="1" border="0" width="100%">
			<tr>
				<td width="20%" align="right"><?php echo _NAME; ?></td>
				<td width="80%">
					<input class="inputbox" type="text" name="title" size="50" maxlength="250" value="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo _SECTION; ?></td>
				<td><?php echo $lists['catid']; ?></td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo _URL; ?></td>
				<td>
					<input class="inputbox" type="text" name="url" value="<?php echo $row->url; ?>" size="50" maxlength="250" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo _DESC; ?></td>
				<td>
					<textarea class="inputbox" cols="30" rows="6" name="description" style="width:300px" width="300"><?php echo htmlspecialchars($row->description,ENT_QUOTES); ?></textarea>
				</td>
			</tr>
		</table>

		<div class="buttons">
			<span class="button">
				<a class="button" href="javascript:submitbutton('save');" ><?php echo _SAVE?></a>
			</span>
			<span class="button">
				<a class="button" href="javascript:submitbutton('cancel');" ><?php echo _CANCEL?></a>
			</span>
		</div>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
		<input type="hidden" name="approved" value="<?php echo $row->approved; ?>" />
		<input type="hidden" name="Returnid" value="<?php echo $Returnid; ?>" />
		<input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
		<input type="hidden" name="<?php echo $validate; ?>" value="1" />
	</form>
</div>
		<?php
	}
}