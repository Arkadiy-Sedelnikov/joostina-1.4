<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

class defaultFilter{
	var $directory = null;
	var $fields_searchable = null;
	var $category = null;
	var $field_values = null;
	var $conf = null;
	var $show_name = null;

	//имя типа поля в выпадающем списке в настройках поля
	var $name = 'Default Filter';

	//тип плагина для записи в таблицы
	var $type = 'defaultFilter';

	public function displaySettingsForm($directory){

		?>
	<form action="index2.php" name="adminform" method="post">
		<?php echo 'Hello!'; ?>
	</form>
	<?php
	}

	public function displayFilter(){
		$action = sefRelToAbs('index.php?option=com_boss&directory=' . $this->directory . '&task=show_result&catid=' . $this->category->id);
		?>
	<form action="<?php echo $action; ?>" name="adminform" method="post">
		<?php include('filter.php'); ?>
	</form>
	<?php
	}

	private function displayFilterFields(){
		$directory = $this->directory;
		$fields_searchable = $this->fields_searchable;

		$catid = (!empty($this->category->id)) ? $this->category->id : 0;
		$field_values = $this->field_values;
		$plugins = BossPlugins::get_plugins($directory, 'fields');

		if($this->show_name){
			$title = BOSS_NAME_DIR;
			$input = '<input class="boss_required" mosreq="1" id="name_search" type="text" moslabel="Название" name="name_search" size="10" maxlength="20" value="">';
			$id = 'name_div';
			include('filter_field.php');
		}

		foreach($fields_searchable as $key => $fsearch){
			//var_dump($fsearch);
			//if (($catid == 0)||(strpos($fsearch->catsid, ",$catid,") !== false)||(strpos($fsearch->catsid, ",-1,") !== false)) {
			$return = jDirectoryField::getFieldForm($fsearch, null, null, $field_values, $directory, $plugins, "search");
			$title = $return->title;
			$input = $return->input;
			$id = $key . '_div';
			include('filter_field.php');
			//}
		}
	}

	//действия при установке плагина
	function install($directory){
	}

	function uninstall(){
	}
}

?>