<?php
/**
* @version $Id: manager.php 2005-12-27 09:23:43Z Ryan Demmer $
* @package JCE
* @copyright Copyright (C) 2005 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_VALID_MOS' ) or die( 'Restricted Access.' );

$version = "1.1.3";

require_once( JPATH_BASE . '/mambots/editors/jce/jscripts/tiny_mce/libraries/classes/jce.class.php' );
require_once( JPATH_BASE . '/mambots/editors/jce/jscripts/tiny_mce/libraries/classes/jce.utils.class.php' );

$jce = new JCE();
$jce->setPlugin('imgmanager');

require_once( $jce->getPluginPath() . '/classes/manager.class.php' );
//Setup languages
include_once( $jce->getLibPath() . '/langs/' . $jce->getLanguage() . '.php' );
include_once(  $jce->getPluginPath() . '/langs/' .$jce->getPluginLanguage() . '.php' );

//Load Plugin Parameters
$params = $jce->getPluginParams();

$base_dir = $jce->getBaseDir( true );
$base_url = $base_dir;

$manager = new imageManager( $base_dir, $base_url );

$jce->setAjax( array( 'getProperties', &$manager, 'getProperties' ) );
$jce->setAjax( array( 'getDimensions', &$manager, 'getDimensions' ) );

$jce->processAjax();

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $jce->translate('iso');?>" />
	<title><?php echo $jce->translate('desc');?></title>
<?php
	echo $jce->printLibJs( 'tiny_mce_utils' );
	// загрузка скриптов mootols
	mosCommonHTML::loadMootools(1);
	echo $jce->printLibJs( 'utils' );
	echo $jce->printLibJs( 'window' );
	echo $jce->printLibJs( 'manager' );
	echo $jce->printPluginJs( 'functions' );
	echo $jce->printLibJs( 'dtree' ); 
	echo $jce->printLibCss( 'common', true );
	echo $jce->printPluginCss( 'manager' );
	echo $jce->printLibCss( 'dtree' );
?>
	<script type="text/javascript">
		jce.setPlugin('imgmanager');
		jce.set("base_url", "<?php echo $base_url; ?>");
		jce.set("align", "<?php echo $params->get( 'align', 'left' );?>");
		jce.set("border", "<?php echo $params->get( 'border', '0' );?>");
		jce.set("border_width", "<?php echo $params->get( 'border_width', '1' );?>");
		jce.set("border_style", "<?php echo $params->get( 'border_style', 'solid' );?>");
		jce.set("border_color", "<?php echo $params->get( 'border_color', '#000000' );?>");
		jce.set("hspace", "<?php echo $params->get( 'hspace', '5' );?>");
		jce.set("vspace", "<?php echo $params->get( 'vspace', '5' );?>");
	</script>
</head>
<body lang="<?php echo $jce->getPluginLanguage(); ?>" id="imgmanager" onLoad="tinyMCEPopup.executeOnLoad('init();');" style="display: none">
	<form action="<?php echo $jce->getPluginFile('files.php');?>" target="manager" name="uploadForm" id="uploadForm" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="itemsList" id="itemsList" />
	<input type="hidden" name="clipboard" id="clipboard" />
	<div class="tabs">
			<ul>
				<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onMouseDown="return false;"><?php echo $jce->translate('article_image');?></a></span></li>
				<li id="swap_tab"><span><a href="javascript:mcTabs.displayTab('swap_tab','swap_panel');" onMouseDown="return false;"><?php echo $jce->translate('swap_image');?></a></span></li>
				<li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onMouseDown="return false;"><?php echo $jce->translate('advanced');?></a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
			<div id="general_panel" class="panel current">
				<fieldset>
						<legend><?php echo $jce->translate('article_image');?></legend>
						<table class="properties" border="0">
							<tr>
								<td class="column1"><label id="srclabel" for="src"><?php echo $jce->translate('url');?></label></td>
								<td colspan="3"><table border="0" cellspacing="0" cellpadding="0">
									<tr><td><input name="src" type="text" class="large_input" id="src" value="" /></td></tr>
								  </table></td>
								<td rowspan="7" valign="top">
								<div class="alignPreview">
										<img id="alignSampleImg" src="<?php echo $jce->getPluginImg('sample.jpg');?>" alt="sample" />
										Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
								</div>
								</td>
							</tr>
							<tr>
								<td class="column1"><label id="altlabel" for="alt"><?php echo $jce->translate('alt');?></label></td>
								<td colspan="3"><input id="alt" class="large_input" name="alt" type="text" value="" /></td>
							</tr>
							<tr>
								<td class="column1"><label id="titlelabel" for="title"><?php echo $jce->translate('title');?></label></td>
								<td colspan="3"><input id="title" class="large_input" name="title" type="text" value="" /></td>
							</tr>
							<tr>
							<td class="column1"><label id="widthlabel" for="width"><?php echo $jce->translate('dimensions');?></label></td>
							<td class="jtd_nowrap" colspan="3">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="text" id="width" name="width" value="" onChange="changeHeight();updateStyle();" /> x <input type="text" id="height" name="height" value="" onChange="changeWidth();updateStyle();" /></td>
										<input name="tmp_width" type="hidden" id="tmp_width" value=""  />
										<input name="tmp_height" type="hidden" id="tmp_height" value="" />
										<td>&nbsp;&nbsp;<input id="constrain" type="checkbox" name="constrain" class="checkbox" checked="checked" /></td>
										<td><label id="constrainlabel" for="constrain"><?php echo $jce->translate('constrain');?></label></td>
										<td><div id="dim_loader"></div></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="column1"><label id="vspacelabel" for="vspace"><?php echo $jce->translate('vspace');?></label></td>
							<td style="width:10%"><input name="vspace" type="text" id="vspace" value="" size="3" maxlength="3" onChange="changeAppearance();updateStyle();" /></td>	
							<td class="column1" style="width:10%"><label id="hspacelabel" for="hspace"><?php echo $jce->translate('hspace');?></label></td>
							<td><input name="hspace" type="text" id="hspace" value="" size="3" maxlength="3" onChange="changeAppearance();updateStyle();" /></td>
						</tr>
						<tr>
							<td class="column1"><label id="alignlabel" for="align"><?php echo $jce->translate('align');?></label></td>
							<td colspan="3">
								<select name="align" id="align" onChange="changeAppearance();updateStyle();">
									<option value=""><?php echo $jce->translate('align_default');?></option>
									<option value="top"><?php echo $jce->translate('align_top');?></option>
									<option value="middle"><?php echo $jce->translate('align_middle');?></option>
									<option value="bottom"><?php echo $jce->translate('align_bottom');?></option>
									<option value="left"><?php echo $jce->translate('align_left');?></option>
									<option value="right"><?php echo $jce->translate('align_right');?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><label><?php echo $jce->translate('border');?></label></td>
							<td colspan="3">
							<table cellspacing="0">
								<tr>
									<td><input type="checkbox" id="border" name="border" onClick="setBorder();"></td>
									<td><label for="border_width"><?php echo $jce->translate('border_width');?></label></td>
									<td>
									<select id="border_width" name="border_width" onChange="changeAppearance();updateStyle();">
										<option value="0">0</option>
										<option value="1" selected="selected">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="thin">thin</option>
										<option value="medium">medium</option>
										<option value="thick">thick</option>
									</select>
									</td>
									<td><label for="border_style"><?php echo $jce->translate('border_style');?></label></td>
									<td>
										<select id="border_style" name="border_style" onChange="changeAppearance();updateStyle();">
											<option value="none">none</option>
											<option value="solid" selected="selected">solid</option>
											<option value="dashed">dashed</option>
											<option value="dotted">dotted</option>
											<option value="double">double</option>
											<option value="groove">groove</option>
											<option value="inset">inset</option>
											<option value="outset">outset</option>
											<option value="ridge">ridge</option>
										</select>
									</td>
									<td><label for="border_color"><?php echo $jce->translate('border_color');?></label></td>
									<td><input id="border_color" name="border_color" type="text" value="#000000" size="9" onChange="updateColor('border_color_pick','border_color');changeAppearance();updateStyle();" /></td>
									<td id="border_color_pickcontainer">&nbsp;</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="swap_panel" class="panel">
				<fieldset>
					<legend><?php echo $jce->translate('swap_image');?></legend>
					<input type="checkbox" id="onmousemovecheck" name="onmousemovecheck" class="checkbox" onClick="changeMouseMove();" />
					<label id="onmousemovechecklabel" for="onmousemovecheck"><?php echo $jce->translate('swap_image');?></label>
					<table border="0" cellpadding="4" cellspacing="0" width="100%">
							<tr>
								<td class="column1"><label id="onmouseoversrclabel" for="onmouseoversrc"><?php echo $jce->translate('mouseover');?></label></td>
								<td><table border="0" cellspacing="0" cellpadding="0"><tr><td><input id="onmouseoversrc" class="large_input" name="onmouseoversrc" type="text" value="" /></td></tr></table></td>
							</tr>
							<tr>
								<td class="column1"><label id="onmouseoutsrclabel" for="onmouseoutsrc"><?php echo $jce->translate('mouseout');?></label></td>
								<td class="column2"><table border="0" cellspacing="0" cellpadding="0"><tr><td><input id="onmouseoutsrc" class="large_input" name="onmouseoutsrc" type="text" value="" /></td></tr></table></td>
							</tr>
					</table>
				</fieldset>
			</div>
			<div id="advanced_panel" class="panel">
				<fieldset>
					<legend><?php echo $jce->translate('advanced');?></legend>
					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td class="column1"><label id="stylelabel" for="style"><?php echo $jce->translate('style');?></label></td>
							<td><input id="style" name="style" class="large_input" type="text" value="" /></td>
						</tr>
						<tr>
							<td><label id="classlabel" for="classlist"><?php echo $jce->translate('class_list');?></label></td>
							<td><select id="classlist" name="classlist" class="mceEditableSelect">
									<option value="" selected><?php echo $jce->translate('not_set');?></option>
									<option value="jce_tooltip">JCE Tooltip</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="column1"><label id="idlabel" for="id"><?php echo $jce->translate('id');?></label></td>
							<td><input id="id" name="id" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label id="dirlabel" for="dir"><?php echo $jce->translate('lang_dir');?></label></td>
							<td>
								<select id="dir" name="dir" onChange="changeAppearance();">
									<option value=""><?php echo $jce->translate('not_set');?></option>
									<option value="ltr"><?php echo $jce->translate('ltr');?></option>
									<option value="rtl"><?php echo $jce->translate('rtl');?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="column1"><label id="langlabel" for="lang"><?php echo $jce->translate('lang_code');?></label></td>
							<td><input id="lang" name="lang" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label id="usemaplabel" for="usemap"><?php echo $jce->translate('image_map');?></label></td>
							<td><input id="usemap" name="usemap" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label id="longdesclabel" for="longdesc"><?php echo $jce->translate('long_desc');?></label></td>
							<td><table border="0" cellspacing="1" cellpadding="0">
									<tr><td><input id="longdesc" name="longdesc" type="text" value="" /></td></tr>
								</table></td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>
	<fieldset>
	<legend><?php echo $jce->translate('browse');?></legend>
	<table class="properties" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="5" style="vertical-align:top">
				<div id="msgIcon"><img id="imgMsgContainer" src="<?php echo  $jce->getLibImg('spacer.gif');?>" width="16" height="16" border="0" alt="Message" title="Message" /></div>
				<div id="msgDiv"><span id="msgContainer" style="vertical-align:top;"></span></div>
			</td>
		</tr>
		<tr>
			<td colspan="5" style="vertical-align:top">
				<div id="dirListBlock">
					<label for="dirlistcontainer" style="vertical-align:middle;"><?php echo $jce->translate('dir');?></label>&nbsp;
					<div id="dirlistcontainer" style="vertical-align:middle;"></div>
				</div>
				<div id="dirImg" style="display: inline;"><a href="javascript:void(0)" onClick="goUpDir();" title="<?php echo $jce->translate('dir_up');?>" class="toolbar"><img src="<?php echo $jce->getLibImg('dir_up.gif');?>" width="20" height="20" border="0" alt="<?php echo $jce->translate('dir_up');?>" /></a></div>
<?php if( $jce->getAuthOption( 'folder_new', '18' ) ){?>
					<div id="folderImg" style="display: inline;"><a href="javascript:void(0)" class="toolbar" onClick="newFolder();" title="<?php echo $jce->translate('new_dir');?>"><img src="<?php echo $jce->getLibImg('new_folder.gif');?>" width="20" height="20" alt="<?php echo $jce->translate('new_dir');?>" /></a></div>
<?php } if( $jce->getAuthOption( 'upload', '18' ) ){?>
					<div id="upImg" style="display: inline;"><a href="javascript:void(0)" onClick="uploadFile();" class="toolbar"><img src="<?php echo $jce->getLibImg('upload.gif');?>" border="0" alt="<?php echo $jce->translate('upload');?>" width="20" height="20" title="<?php echo $jce->translate('upload');?>" /></a></div>
<?php }?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top"><div id="spacerDiv"></div></td>
			<td style="vertical-align:top"><?php echo $jce->sortType();?></td>
			<td style="vertical-align:top"><?php echo $jce->sortName();?></td>
			<td colspan="2" style="vertical-align:top"><?php echo $jce->searchDiv();?></td>
		</tr>
		<tr>
			<td style="vertical-align:top">
				<div id="treeBlock">
					<div id="treeTitle"><?php echo $jce->translate('folders');?></div>
					<div id="treeDetails" class="tree"></div>
				</div>
			</td>
			<td colspan="2" style="vertical-align:top"><div id="fileContainer"></div></td>
			<td style="vertical-align:top">
				<div id="infoBlock">
					<div id="infoTitle"><?php echo $jce->translate('details');?></div>
					<div id="fileDetails"></div>
				</div>
			</td>
			<td style="vertical-align:top">
				<div id="toolsList">
					<?php echo $jce->editTools();?>
					<div id="viewIcon" class="editIcon"><a href="javascript:void(0)" id="viewLink" title="<?php echo $jce->translate('view') ?>" onClick="viewImage();" class="tools"><img src="<?php echo $jce->getLibImg('view.gif');?>" id="viewIcon" height="20" width="20" border="0" alt="<?php echo $jce->translate('view');?>" /></a> </div>
				</div>
			</td>
		</tr>
	</table>
	</fieldset>
	<div class="mceActionPanel">
		<div style="float: right">
			<input type="button" class="upload2 "id="upImg" name="refresh" value="<?php echo $jce->translate('upload_img');?>"  onClick="uploadFile();"/>
			<input type="button" class="button "id="refresh" name="refresh" value="<?php echo $jce->translate('refresh');?>" onClick="refreshAction();" />
			<input type="button" id="insert" name="insert" value="{$lang_insert}" onClick="insertAction();" />
			<input type="button" id="cancel" name="cancel" value="<?php echo $jce->translate('cancel');?>" onClick="tinyMCEPopup.close();" />
		</div>
	</div>
	</form>
</body> 
</html>
