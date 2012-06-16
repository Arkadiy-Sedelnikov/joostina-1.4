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
			<th class="config">elFinder - <?php echo _ELRTE_ADMIN_CONFIG ?> </th>
		</tr>
	</table>
	<?php $tabs->startPane("config"); ?>
	<?php $tabs->startTab(_ELRTE_ADMIN_IM_PHP_CONFIG, "config1"); ?>

	<table class="adminlist" width="100%">
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_ROOT_DIR;?></td>
			<td><input size="100" class="inputbox" type="text" name="file_manager_dir" value="<?php echo @$file_manager_dir; ?>"/></td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_OWN_DIR;?></td>
			<td>
				<?php echo mosHTML::yesnoRadioList('file_manager_owndir', 'class="inputbox"', @$file_manager_owndir); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_ROOT_ALIAS;?></td>
			<td>
				<input size="50" class="inputbox" type="text" name="root_alias" value="<?php echo @$root_alias; ?>"/>
			</td>
		</tr>

		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_DOT_FILES;?></td>
			<td>
				<?php echo mosHTML::yesnoRadioList('dot_files', 'class="inputbox"', @$dot_files); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_DIR_SIZE;?></td>
			<td>
				<?php echo mosHTML::yesnoRadioList('dir_size', 'class="inputbox"', @$dir_size); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_FILE_MODE;?></td>
			<td>
				<input size="5" class="inputbox" type="text" name="file_mode" value="<?php echo $file_mode; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_DIR_MODE;?></td>
			<td>
				<input size="5" class="inputbox" type="text" name="dir_mode" value="<?php echo $dir_mode; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_IMG_LIB;?></td>
			<td>
				<?php echo mosHTML::selectList($img_lib_obList, 'img_lib', 'class="inputbox"', 'key', 'text', @$img_lib); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_TMB_DIR;?></td>
			<td>
				<input size="100" class="inputbox" type="text" name="tmb_dir" value="<?php echo @$tmb_dir; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_TMB_CLEAN;?></td>
			<td>
				<input size="5" class="inputbox" type="text" name="tmb_clean_prob" value="<?php echo @$tmb_clean_prob; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_TMB_AT_ONCE;?></td>
			<td>
				<input size="5" class="inputbox" type="text" name="tmb_at_once" value="<?php echo @$tmb_at_once; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_TMB_SIZE;?></td>
			<td>
				<input size="5" class="inputbox" type="text" name="tmb_size" value="<?php echo @$tmb_size; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_FILE_URL;?></td>
			<td>
				<?php echo mosHTML::yesnoRadioList('file_url', 'class="inputbox"', @$file_url); ?>
			</td>
		</tr>
	</table>


	<?php $tabs->endTab(); ?>
	<?php $tabs->startTab(_ELRTE_ADMIN_IM_DISABLED_COMAND, "config2"); ?>
	<table class="adminlist" width="100%">
		<tr>
			<th width="<?php echo $percent; ?>%"><?php echo _ELRTE_ADMIN_IM_COMAND ?></th>
			<?php foreach($groups as $group){ ?>
			<th width="<?php echo $percent; ?>%"><?php echo $group->name; ?></th>
			<?php } ?>
		</tr>

		<?php foreach($commands as $command => $command_name){ ?>
		<tr>
			<th width="20%"><?php echo $command_name; ?></th>
			<?php
			foreach($groups as $group){
				$selected = (@in_array($command, @$disabled_command[$group->group_id])) ? 'checked' : '';
				?>
				<td align="center" width="<?php echo $percent; ?>%">
					<input type="checkbox" name="disabled_command[]"
						   value="<?php echo $command . '_' . $group->group_id; ?>" <?php echo $selected; ?>>
				</td>
				<?php }    ?>
		</tr>
		<?php }    ?>
	</table>
	<?php $tabs->endTab(); ?>
	<?php $tabs->startTab(_ELRTE_ADMIN_IM_ALLOWED_FILES, "config3"); ?>
	<table class="adminlist" width="100%">
		<tr>
			<th width="<?php echo $percent; ?>%"><?php echo _ELRTE_ADMIN_IM_MIMES ?></th>
			<?php foreach($groups as $group){ ?>
			<th width="<?php echo $percent; ?>%"><?php echo $group->name; ?></th>
			<?php } ?>
		</tr>

		<?php foreach($mimetypes as $command => $command_name){ ?>
		<tr>
			<th width="20%"><?php echo $command_name; ?></th>
			<?php
			foreach($groups as $group){
				$selected = (@in_array($command, @$upload_allow[$group->group_id])) ? 'checked' : '';
				?>
				<td align="center" width="<?php echo $percent; ?>%">
					<input type="checkbox" name="upload_allow[]"
						   value="<?php echo $command . '_' . $group->group_id; ?>" <?php echo $selected; ?>>
				</td>
				<?php }    ?>
		</tr>
		<?php }    ?>
	</table>
	<?php $tabs->endTab(); ?>
	<?php $tabs->startTab(_ELRTE_ADMIN_IM_CLIENT, "config4"); ?>
	<table class="adminlist" width="100%">
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_PLACES;?></td>
			<td>
				<input size="50" class="inputbox" type="text" name="places" value="<?php echo @$places; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_PLACE_FIRST;?></td>
			<td>
				<?php echo mosHTML::yesnoRadioList('places_first', 'class="inputbox"', @$places_first); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_VIEW;?></td>
			<td>
				<?php echo mosHTML::selectList($view_obList, 'view', 'class="inputbox"', 'key', 'text', @$view); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo _ELRTE_ADMIN_IM_REMEMBER_LAST_DIR;?></td>
			<td>
				<?php echo mosHTML::yesnoRadioList('remember_last_dir', 'class="inputbox"', @$remember_last_dir); ?>
			</td>
		</tr>
	</table>
	<?php $tabs->endTab(); ?>
	<?php $tabs->endPane(); ?>

	<input type="hidden" name="option" value="com_elrte"/>
	<input type="hidden" name="task" value="save_config_elfinder"/>
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
</form>