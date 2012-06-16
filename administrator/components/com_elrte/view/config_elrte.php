<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 * Backend content viewer. Config-page.
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage    Admin
 * @filename config.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 **/

defined('_VALID_MOS') or die();

$tabs = new mosTabs(1, 1);
?>
<form action="index2.php" method="post" name="adminForm" id="adminForm">

	<table class="adminheading">
		<tr>
			<th class="config">elRTE - <?php echo _ELRTE_ADMIN_CONFIG ?> </th>
		</tr>
	</table>
	<?php $tabs->startPane("config"); ?>
	<?php $tabs->startTab(_ELRTE_ADMIN_MAIN_CONFIG, "config0"); ?>
	<fieldset class='adminform'>
		<legend><?php echo _ELRTE_PERMISSIONS ?></legend>
		<table class="adminlist" width="100%">
			<tr>
				<td>
					<?php echo _ELRTE_TOOLBAR_METOD; ?>
				</td>
				<td>
					<?php echo mosHTML::yesnoRadioList('toolbar_metod', 'class="inputbox"', @$toolbar_metod, _ELRTE_CREATE_TOOLBAR, _ELRTE_SELECT_TOOLBAR); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_TOOLBAR_SELECT; ?>
				</td>
				<td>
					<?php echo mosHTML::selectList($toolbar_objectList, 'select_toolbar', 'class="inputbox"', 'key', 'text', @$select_toolbar); ?>
				</td>
			</tr>
		</table>
		<table class="adminlist" width="100%">
			<tr>
				<th width="<?php echo $percent; ?>%"><?php echo _ELRTE_PANELS; ?></th>
				<?php foreach($groups as $group){ ?>
				<th width="<?php echo $percent; ?>%"><?php echo $group->name; ?></th>
				<?php } ?>
			</tr>

			<?php foreach($panels_array as $panel => $pan_name){ ?>
			<tr>
				<th width="<?php echo $percent; ?>%"><?php echo $pan_name; ?></th>
				<?php
				foreach($groups as $group){
					$selected = (strpos(@$panels[$group->group_id], $panel) !== false) ? 'checked' : '';
					?>
					<td align="center" width="<?php echo $percent; ?>%">
						<input type="checkbox" name="permissions[]"
							   value="<?php echo $panel . '_' . $group->group_id; ?>" <?php echo $selected; ?>>
					</td>
					<?php }    ?>
			</tr>
			<?php }    ?>

		</table>
	</fieldset>
	<?php $tabs->endTab(); ?>
	<?php $tabs->startTab(_ELRTE_EDITOR_CONFIG, "config3"); ?>
	<fieldset class='adminform'>
		<legend><?php echo _ELRTE_EDITOR_OPT ?></legend>
		<table class="adminlist" width="100%">
			<tr>
				<td>
					<?php echo _ELRTE_EDITOR_DOCTYPE ?>
				</td>
				<td>
					<input type="text" class="inputbox" name="doctype" value="<?php echo @$doctype; ?>"/>
				</td>
				<td>
					<?php echo _ELRTE_EDITOR_DOCTYPE_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_EDITOR_CSS ?>
				</td>
				<td>
					<input type="text" class="inputbox" name="css_class" value="<?php echo @$css_class; ?>"/>
				</td>
				<td>
					<?php echo _ELRTE_EDITOR_CSS_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_ADMIN_HEIGHT ?>
				</td>
				<td>
					<input type="text" class="inputbox" name="editor_height" value="<?php echo @$editor_height; ?>"/>
				</td>
				<td>
					<?php echo _ELRTE_ADMIN_HEIGHT_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_ADMIN_WIDTH ?>
				</td>
				<td>
					<input type="text" class="inputbox" name="editor_width" value="<?php echo @$editor_width; ?>"/>
				</td>
				<td>
					<?php echo _ELRTE_ADMIN_WIDTH_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_EDITOR_CSS_ARR ?>
				</td>
				<td>
					<textarea cols="50" rows="5" class="inputbox"
							  name="cssfiles"><?php echo stripslashes(@$cssfiles); ?></textarea>
				</td>
				<td>
					<?php echo _ELRTE_EDITOR_CSS_ARR_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_EDITOR_ABS_URLS ?>
				</td>
				<td>
					<?php echo mosHTML::yesnoRadioList('absolute_urls', 'class="inputbox"', @$absolute_urls); ?>
				</td>
				<td>
					<?php echo _ELRTE_EDITOR_ABS_URLS_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_EDITOR_ALLOW_HTML ?>
				</td>
				<td>
					<?php echo mosHTML::yesnoRadioList('allow_source', 'class="inputbox"', @$allow_source); ?>
				</td>
				<td>
					<?php echo _ELRTE_EDITOR_ALLOW_HTML_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_EDITOR_STYLE ?>
				</td>
				<td>
					<?php echo mosHTML::yesnoRadioList('style_with_css', 'class="inputbox"', @$style_with_css); ?>
				</td>
				<td>
					<?php echo _ELRTE_EDITOR_STYLE_DESC ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _ELRTE_EDITOR_ALLOW_FM ?>
				</td>
				<td>
					<table>
					<tr>
						<?php               $i = 0;
						foreach($groups as $group){
							$selected = (@in_array($group->group_id, @$fm_allow)) ? 'checked' : '';
							?>
							<td width="33%"><label><input type="checkbox" name="fm_allow[]"
														  value="<?php echo $group->group_id; ?>" <?php echo $selected; ?>><?php echo $group->name; ?>&nbsp;&nbsp;&nbsp;
							</label></td>
							<?php $i++;
							if($i == 3){
								$i = 0;
								?>
            </tr><tr>
    <?php }
						} ?>
					</tr>
					</table>
				</td>
				<td>
					<?php echo _ELRTE_EDITOR_ALLOW_FM_DESC ?>
				</td>
			</tr>
		</table>
	</fieldset>
	<?php $tabs->endTab(); ?>
	<?php $tabs->endPane();?>

	<input type="hidden" name="option" value="com_elrte"/>
	<input type="hidden" name="task" value="save_config_elrte"/>
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
</form>