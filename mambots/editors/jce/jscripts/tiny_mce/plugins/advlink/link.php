<?php
/**
* @version $Id: link.php 2006-07-30 10:33:32 Ryan Demmer $
* @package JCE
* @copyright Copyright (C) 2006 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_VALID_MOS' ) or die( 'Restricted Access.' );

$version = "1.1.3";

global $database;

require_once( JPATH_BASE . '/mambots/editors/jce/jscripts/tiny_mce/libraries/classes/jce.class.php' );

$jce = new JCE();
$jce->setPlugin('advlink');

require_once( $jce->getPluginPath() . '/advlink.php' );

//Setup languages
include_once( $jce->getLibPath() . '/langs/' . $jce->getLanguage() . '.php' );
include_once(  $jce->getPluginPath() . '/langs/' . $jce->getPluginLanguage() . '.php' );

//Load Plugin Parameters
$params = $jce->getPluginParams();

$jce->setAjax( 'getByType' );
$jce->processAjax();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $jce->translate('iso');?>" />
	<title><?php echo $jce->translate('title');?> : <?php echo $version;?></title>
	<?php 
	echo $jce->printLibJs( 'tiny_mce_utils' );
	// загрузка скриптов mootols
	mosCommonHTML::loadMootools(1);
	echo $jce->printLibJs( 'utils' );
	echo $jce->printPluginJs( 'functions' );
	echo $jce->printLibCss( 'common' );
	echo $jce->printPluginCss( 'advlink' );
	?>
	<script type="text/javascript">
		jce.setPlugin('advlink');
		jce.set('target', "<?php echo $params->get('target', '_self');?>");
	</script>
</head>
<body lang="<?php echo $jce->getPluginLanguage();?>" id="advlink" onLoad="tinyMCEPopup.executeOnLoad('init();');" style="display: none">
	<form name="advlink_form" onSubmit="insertAction();return false;" action="#">
	<div class="tabs">
		<ul>
			<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onMouseDown="return false;"><?php echo $jce->translate('general_tab');?></a></span></li>
			<li id="events_tab"><span><a href="javascript:mcTabs.displayTab('events_tab','events_panel');" onMouseDown="return false;"><?php echo $jce->translate('events_tab');?></a></span></li>
			<li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onMouseDown="return false;"><?php echo $jce->translate('advanced_tab');?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper" style="border-bottom:0px;">
		<div id="general_panel" class="panel current">
			<fieldset>
				<legend><?php echo $jce->translate('general_tab');?></legend>
				<table border="0" cellpadding="4" cellspacing="0">
					<tr>
						<td nowrap="nowrap"><label id="hreflabel" for="href"><?php echo $jce->translate('url');?></label></td>
						<td>
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td colspan="2"><input id="href" name="href" type="text" value="" size="200" /></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="column1"><label for="anchorlist"><?php echo $jce->translate('anchors');?></label></td>
						<td colspan="2" id="anchorlistcontainer">&nbsp;</td>
					</tr>
					<tr>
						<td><label id="targetlistlabel" for="targetlist"><?php echo $jce->translate('target');?></label></td>
						<td><select id="targetlist" name="targetlist">
								<option value="_self"><?php echo $jce->translate('self');?></option>
								<option value="_blank"><?php echo $jce->translate('blank');?></option>
								<option value="_parent"><?php echo $jce->translate('parent');?></option>
								<option value="_top"><?php echo $jce->translate('top');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap"><label id="titlelabel" for="title"><?php echo $jce->translate('title');?></label></td>
						<td><input id="title" name="title" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="classlabel" for="classlist"><?php echo $jce->translate('class');?></label></td>
						<td>
						<select id="classlist" name="classlist" onChange="changeClass();">
							<option value="" selected><?php echo $jce->translate('not_set');?></option>
							<option value="jcebox">jcebox</option>
						</select>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><strong><?php echo $jce->translate('email');?></strong></legend>
                    <table border="0" cellpadding="4" cellspacing="0">
				        <tr id="emailaddressrow">
							<td class="column1"><label for="emailadd"><?php echo $jce->translate('address');?></label></td>
							<td><input id="emailadd" name="emailadd" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="emailsub"><?php echo $jce->translate('subject');?></label></td>
							<td><input id="emailsub" name="emailsub" type="text" value="" /></td>
						</tr>
						<tr>
							<td colspan="2"><input id="emailcreate" class="button" type="button" onClick="buildAddress();" value="<?php echo $jce->translate('create');?>" /></td>
						</tr>
                    </table>
                </fieldset>
			</div>
			<div id="advanced_panel" class="panel">
			<fieldset>
					<legend><?php echo $jce->translate('advanced_tab');?></legend>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td class="column1"><label id="idlabel" for="id"><?php echo $jce->translate('id');?></label></td>
							<td><input id="id" name="id" type="text" value="" /></td> 
						</tr>

						<tr>
							<td><label id="stylelabel" for="style"><?php echo $jce->translate('style');?></label></td>
							<td><input type="text" id="style" name="style" value="" /></td>
						</tr>

						<tr>
							<td><label id="classeslabel" for="classes"><?php echo $jce->translate('class');?></label></td>
							<td><input type="text" id="classes" name="classes" value="" onChange="selectByValue('classlist',this.value,true);" /></td>
						</tr>

						<tr>
							<td><label id="targetlabel" for="target"><?php echo $jce->translate('advanced_target_name');?></label></td>
							<td><input type="text" id="target" name="target" value="" onChange="selectByValue('targetlist',this.value,true);" /></td>
						</tr>

						<tr>
							<td class="column1"><label id="dirlabel" for="dir"><?php echo $jce->translate('lang_dir');?></label></td>
							<td>
								<select id="dir" name="dir"> 
										<option value=""><?php echo $jce->translate('not_set');?></option>
										<option value="ltr"><?php echo $jce->translate('ltr');?></option>
										<option value="rtl"><?php echo $jce->translate('rtl');?></option>
								</select>
							</td> 
						</tr>

						<tr>
							<td><label id="hreflanglabel" for="hreflang"><?php echo $jce->translate('advanced_target_langcode');?></label></td>
							<td><input type="text" id="hreflang" name="hreflang" value="" /></td>
						</tr>

						<tr>
							<td class="column1"><label id="langlabel" for="lang"><?php echo $jce->translate('advanced_langcode');?></label></td>
							<td>
								<input id="lang" name="lang" type="text" value="" />
							</td> 
						</tr>

						<tr>
							<td><label id="charsetlabel" for="charset"><?php echo $jce->translate('advanced_encoding');?></label></td>
							<td><input type="text" id="charset" name="charset" value="" /></td>
						</tr>

						<tr>
							<td><label id="typelabel" for="type"><?php echo $jce->translate('advanced_mime');?></label></td>
							<td><input type="text" id="type" name="type" value="" /></td>
						</tr>

						<tr>
							<td><label id="rellabel" for="rel"><?php echo $jce->translate('advanced_rel');?></label></td>
							<td><select id="rel" name="rel" class="mceEditableSelect"> 
									<option value=""><?php echo $jce->translate('not_set');?></option>
									<option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option> 
								</select> 
							</td>
						</tr>

						<tr>
							<td><label id="revlabel" for="rev"><?php echo $jce->translate('advanced_rev');?></label></td>
							<td><select id="rev" name="rev"> 
									<option value=""><?php echo $jce->translate('not_set');?></option>
									<option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option> 
								</select> 
							</td>
						</tr>

						<tr>
							<td><label id="tabindexlabel" for="tabindex"><?php echo $jce->translate('advanced_tabindex');?></label></td>
							<td><input type="text" id="tabindex" name="tabindex" value="" /></td>
						</tr>

						<tr>
							<td><label id="accesskeylabel" for="accesskey"><?php echo $jce->translate('advanced_accesskey');?></label></td>
							<td><input type="text" id="accesskey" name="accesskey" value="" /></td>
						</tr>
					</table>
				</fieldset>
			</div>

			<div id="events_panel" class="panel">
			<fieldset>
					<legend><?php echo $jce->translate('events_tab');?></legend>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td class="column1"><label for="onfocus">onfocus</label></td> 
							<td><input id="onfocus" name="onfocus" type="text" value="" /></td> 
						</tr>
						<tr>
							<td class="column1"><label for="onblur">onblur</label></td>
							<td><input id="onblur" name="onblur" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="onclick">onclick</label></td> 
							<td><input id="onclick" name="onclick" type="text" value="" /></td> 
						</tr>
						<tr>
							<td class="column1"><label for="ondblclick">ondblclick</label></td>
							<td><input id="ondblclick" name="ondblclick" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="onmousedown">onmousedown</label></td> 
							<td><input id="onmousedown" name="onmousedown" type="text" value="" /></td> 
						</tr>
						<tr>
							<td class="column1"><label for="onmouseup">onmouseup</label></td>
							<td><input id="onmouseup" name="onmouseup" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="onmouseover">onmouseover</label></td> 
							<td><input id="onmouseover" name="onmouseover" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="onmousemove">onmousemove</label></td>
							<td><input id="onmousemove" name="onmousemove" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="onmouseout">onmouseout</label></td> 
							<td><input id="onmouseout" name="onmouseout" type="text" value="" /></td> 
						</tr>
						<tr>
							 <td class="column1"><label for="onkeypress">onkeypress</label></td>
							<td><input id="onkeypress" name="onkeypress" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="onkeydown">onkeydown</label></td> 
							<td><input id="onkeydown" name="onkeydown" type="text" value="" /></td>
						</tr>
						<tr>
							<td class="column1"><label for="onkeyup">onkeyup</label></td>
							<td><input id="onkeyup" name="onkeyup" type="text" value="" /></td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>
		<div class="panel_wrapper" style="border-top:0px;">
		<fieldset>
			<legend><strong><?php echo $jce->translate('content');?></strong></legend>
            <table border="0" style="height:100px; text-align:center;">
				  <tr>
					<td><img id="loader" src="<?php echo $jce->getLibImg('spacer.gif');?>" width="16" height="16" /></td>
					<td class="label" valign="middle" nowrap><?php echo $jce->translate('select_link_type');?></td>
					<td><select class="link_select" name="list_type" onChange="if(this.value!=''){clearLists();loadType(this.value,'');}">
							<?php echo getListOptions();?>
						</select>
					</td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td id="list_level_1_label" class="label" valign="middle" nowrap>&nbsp;</td>
					<td colspan="2"><div id="list_level_1">&nbsp;</div></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td id="list_level_2_label" class="label" valign="middle" nowrap>&nbsp;</td>
					<td><div id="list_level_2">&nbsp;</div></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td id="list_level_3_label" class="label" valign="middle" nowrap>&nbsp;</td>
					<td><div id="list_level_3">&nbsp;</div></td>
				  </tr>
			</table>
            </fieldset>
        </div>
		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" id="insert" name="insert" value="{$lang_insert}" onClick="insertAction();" />
			</div>

			<div style="float: right">
				<input type="button" class="button" id="help" name="help" value="<?php echo $jce->translate('help');?>" onClick="openHelp();" />
				<input type="button" id="cancel" name="cancel" value="<?php echo $jce->translate('cancel');?>" onClick="tinyMCEPopup.close();" />
			</div>
		</div>
    </form>
</body>
</html>
