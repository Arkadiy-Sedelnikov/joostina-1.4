<?php

class viewSelector{

	public function displaySettingsForm($directory){
		$database = database::getInstance();
		$fieldList = array();
		$options = $this->getOptions($directory);
		$database->setQuery("SELECT `title`, `name` " .
			" FROM #__boss_" . $directory . "_fields WHERE `published`= '1' ORDER BY `title`");
		$fields = $database->loadObjectList();
		foreach($fields as $field){
			$fieldList[] = mosHTML::makeOption($field->name, $field->title);
		}
		?>
	<form action="index2.php" name="adminForm" method="post">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<td><?php echo BOSS_PLUG_PUB;?></td>
				<td><input type="checkbox" name="public" value="1" <?php if(@$options['public'] == 1) echo 'checked';?> /></td>
				<td><?php echo BOSS_PLUG_PUB_DESC;?></td>
			</tr>
			<tr>
				<td><?php echo BOSS_PLUG_SEL_FIELD;?></td>
				<td><?php echo mosHTML::selectList($fieldList, 'field', 'class="inputbox" size="1"', 'value', 'text', @$options['field']);?></td>
				<td><?php echo BOSS_PLUG_SEL_FIELD_DESC;?></td>
			</tr>
			<tr>
				<td><?php echo BOSS_PLUG_SEL_FIELD_VAL;?></td>
				<td><input type="text" name="fieldvalue" value="<?php echo @$options['fieldvalue'];?>"/></td>
				<td><?php echo BOSS_PLUG_SEL_FIELD_VAL_DESC;?></td>
			</tr>
		</table>
		<input type="hidden" name="directory" value="<?php echo $directory;?>"/>
		<input type="hidden" name="option" value="com_boss"/>
		<input type="hidden" name="act" value="plugins"/>
		<input type="hidden" name="task" value="save"/>
		<input type="hidden" name="folder" value="views"/>
		<input type="hidden" name="plugin" value="viewSelector"/>
	</form>
	<?php
	}

	public function getOptions($directory){
		$database = database::getInstance();
		$options = array();
		$database->setQuery("SELECT `title`, `value` " .
			" FROM #__boss_plug_config WHERE `directory`= '$directory' AND `plug_name`= 'viewSelector'");
		$opts = $database->loadObjectList();
		if(count($opts) > 0){
			foreach($opts as $opt){
				$options[$opt->title] = $opt->value;
			}
		}
		return $options;
	}

	public function saveOptions($directory){
		$database = database::getInstance();

		$public = mosGetParam($_REQUEST, 'public', 0);
		$field = mosGetParam($_REQUEST, 'field', '');
		$fieldvalue = mosGetParam($_REQUEST, 'fieldvalue', '');

		$q = "DELETE FROM #__boss_plug_config WHERE `directory`= '$directory' AND `plug_name`= 'viewSelector'";
		$database->setQuery($q)->query();
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}

		$q = "INSERT INTO #__boss_plug_config "
			. "(`directory`, `plug_type`, `plug_name`, `title`, `value`) "
			. "VALUES "
			. "($directory, 'views', 'viewSelector', 'public', '$public')";
		$database->setQuery($q)->query();
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}

		$q = "INSERT INTO #__boss_plug_config "
			. "(`directory`, `plug_type`, `plug_name`, `title`, `value`) "
			. "VALUES "
			. "($directory, 'views', 'viewSelector', 'field', '$field')";
		$database->setQuery($q)->query();
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}

		$q = "INSERT INTO #__boss_plug_config "
			. "(`directory`, `plug_type`, `plug_name`, `title`, `value`) "
			. "VALUES "
			. "($directory, 'views', 'viewSelector', 'fieldvalue', '$fieldvalue')";
		$database->setQuery($q)->query();
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}
		mosRedirect(JPATH_SITE . '/administrator/index2.php?option=com_boss&act=plugins&directory=' . $directory, BOSS_PLUG_SAVE_OK);
	}

	public function contentViews($content, $options){
		if(count($options) == 0 || @$options['public'] == 0){
			return 'list_item.php';
		} else if($content->$options['field'] == $options['fieldvalue']){
			return 'featured_content.php';
		} else{
			return 'list_item.php';
		}

	}
}

?>
