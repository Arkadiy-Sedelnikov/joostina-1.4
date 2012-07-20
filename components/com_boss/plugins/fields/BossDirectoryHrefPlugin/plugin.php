<?php
/**
 * Field Plug for AdsManager
 * Author: Thomas PAPIN
 * URL:  http://www.joomprod.com
 * mail: webmaster@joomprod.com
 **/
defined('_JLINDEX') or die();

class BossDirectoryHrefPlugin{

	//имя типа поля в выпадающем списке в настройках поля
	var $name = 'Directory Href';

	//тип плагина для записи в таблицы
	var $type = 'BossDirectoryHrefPlugin';

	//отображение поля в категории
	function getListDisplay($directory, $content, $field, $field_values){
		return BossDirectoryHrefPlugin::getDetailsDisplay($directory, $content, $field, $field_values);
	}

	//отображение поля в контенте
	function getDetailsDisplay($directory, $content, $field, $field_values){

		$fieldName = $field->name;
		$value = $content->$fieldName;
		$return = "";
		if($value != ""){

			if(!empty($field->text_before))
				$return .= '<span>' . $field->text_before . '</span>';
			if(!empty($field->tags_open))
				$return .= html_entity_decode($field->tags_open);

			$return .= '<div class="directory_href">' . stripslashes($value) . '</div>';

			if(!empty($field->tags_close))
				$return .= html_entity_decode($field->tags_close);
			if(!empty($field->text_after))
				$return .= '<span>' . $field->text_after . '</span>';
		}
		return $return;
	}

	//функция вставки фрагмента ява-скрипта в скрипт
	//сохранения формы при редактировании контента с фронта.
	function addInWriteScript($field){

	}

	//отображение поля в админке в редактировании контента
	function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write"){
		$mainframe = mosMainFrame::getInstance();
		;
		if($mainframe->isAdmin() != 1) return;
		?>
	<script type="text/javascript">
		jQuery.noConflict();
		function loadFunc(func) {
			var url = 'http://' + location.hostname;
			url = url + '/administrator/ajax.index.php?option=com_boss&act=plugins&task=run_plugins_func&directory=<?php echo $directory;?>&class=BossDirectoryHrefPlugin&function=' + func;

			if (func == 'loadCategory') {
				jQuery('#<?php echo $field->name;?>_content').html('');
				jQuery('#<?php echo $field->name;?>_category').html('');
			}

			if (func == 'loadContent') {
				jQuery('#<?php echo $field->name;?>_content').html('');
			}

			if (jQuery("select").is("#<?php echo $field->name;?>_directory")) {
				var sel_dir = jQuery('#<?php echo $field->name;?>_directory').val()
				url = url + '&sel_dir=' + sel_dir;
				url = url + '&id_cat=<?php echo $field->name;?>_category_sel';
			}

			if (jQuery("select").is("#<?php echo $field->name;?>_category_sel")) {
				var sel_cat = jQuery('#<?php echo $field->name;?>_category_sel').val()
				url = url + '&sel_cat=' + sel_cat;
				url = url + '&id_cont=<?php echo $field->name;?>_content_sel';
			}

			if (jQuery("select").is("#<?php echo $field->name;?>_content_sel")) {
				var sel_cont = jQuery('#<?php echo $field->name;?>_content_sel').val()
				url = url + '&sel_cont=' + sel_cont;
				url = url + '&id_href=<?php echo $field->name;?>_href';
			}

			jQuery.ajax({
				type:"POST",
				url:url,
				dataType:'HTML',
				success:function (data) {
					if (jQuery("select").is("#<?php echo $field->name;?>_content_sel")) {
						jQuery('#<?php echo $field->name;?>_href').attr('value', data);
					}
					else if (jQuery("select").is("#<?php echo $field->name;?>_category_sel")) {
						jQuery('#<?php echo $field->name;?>_content').html(data);
					}
					else if (jQuery("select").is("#<?php echo $field->name;?>_directory")) {
						jQuery('#<?php echo $field->name;?>_category').html(data);
					}
				}
			});
		}
		jQuery.noConflict();
	</script>
	<?php
		$fieldname = $field->name;
		$value = (isset ($content->$fieldname)) ? stripslashes($content->$fieldname) : '';
		$strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');
		if(($mode == "write") && ($field->editable == 0))
			$readonly = "readonly=true";
		else
			$readonly = "";

		if(($mode == "write") && ($field->required == 1)){
			$class = 'boss_required';
			$mosReq = "mosReq='1'";
		} else{
			$class = 'boss';
			$mosReq = "";
		}
		$return = '';
		$return .= "<table><tr><td>";
		$return .= "<input type='text' class='" . $class . "' $mosReq id='" . $fieldname . "_href' test='DirHref' name='" . $fieldname . "_href' mosLabel='" . $strtitle . "' size='$field->size' maxlength='$field->maxlength' $readonly value='$value' />\n";
		$return .= "</td><td>";
		$return .= "<select class='boss' style='width: 200px;' name='" . $field->name . "_directory' id='" . $field->name . "_directory' onchange='loadFunc(\"loadCategory\")' />\n";
		$return .= $this->loadDirectories();
		$return .= "</select>";
		$return .= "</td><td>";
		$return .= "<div id='" . $field->name . "_category'></div>";
		$return .= "</td><td>";
		$return .= "<div id='" . $field->name . "_content'></div>";
		$return .= "</td></tr></table>";

		return $return;
	}

	//действия при сохранении контента
	function onFormSave($directory, $contentid, $field, $isUpdateMode){
		$return = addslashes(mosGetParam($_POST, $field->name . "_href", '', _MOS_ALLOWHTML));
		return $return;
	}

	//действия при удалении контента
	function onDelete($directory, $content){
		return;
	}

	//отображение поля в админке в настройках поля
	function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues){
		$return = "";
		return $return;
	}

	//действия при сохранении настроек поля
	//если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
	//иначе true
	function saveFieldOptions($directory, $field){
		return false;
	}

	//расположение иконки плагина начиная со слеша от корня сайта
	function getFieldIcon($directory){
		return "/images/boss/$directory/plugins/fields/" . __CLASS__ . "/images/folder.png";
	}

	//действия при установке плагина
	function install(){
		return;
	}

	//действия при удалении плагина
	function uninstall(){
		return;
	}

	//действия при поиске
	function search($directory, $fieldName){
		return;
	}

	/************************/
	/******AJAX функции******/
	/************************/
	function loadDirectories(){
		$database = database::getInstance();
		$directories = $database->setQuery("SELECT id,name FROM #__boss_config")->loadObjectList("id");

		$return = "<option value=''>" . BOSS_DIRECTORY_SEL . "</option>";
		foreach($directories as $d){
			$return .= "<option value='" . $d->id . "'>" . $d->name . "&nbsp;(" . $d->id . ")</option>";
		}
		return $return;
	}

	function loadCategory(){
		$directory = mosGetParam($_REQUEST, 'sel_dir', 0);
		if($directory == 0) return;
		$id_cat = mosGetParam($_REQUEST, 'id_cat', 'directory_htef_category');
		$database = database::getInstance();

		require_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_boss' . DS . 'admin.boss.html.php');

		$rows = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_categories ORDER BY parent, ordering")->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach($rows as $v){
			$pt = $v->parent;
			$list = isset($children[$pt]) ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		//выводим селект выбора категорий
		echo '<select name="' . $id_cat . '" id="' . $id_cat . '" class="boss" style="width: 200px;" onchange=\'loadFunc("loadContent")\'>';
		echo "<option value=''>" . BOSS_SELECT_CATEGORY . "</option>";
		HTML_boss::selectCategories(0, "Корень >> ", $children);
		echo '</select>';
	}

	function loadContent(){
		$sel_dir = mosGetParam($_REQUEST, 'sel_dir', 0);
		if($sel_dir == 0) return;
		$sel_cat = mosGetParam($_REQUEST, 'sel_cat', 0);
		if($sel_cat == 0) return;
		$id_cont = mosGetParam($_REQUEST, 'id_cont', 'directory_htef_content');
		$database = database::getInstance();

		$q = "SELECT c.id, c.name FROM"
			. " #__boss_" . $sel_dir . "_contents AS c,"
			. " #__boss_" . $sel_dir . "_content_category_href AS cch"
			. " WHERE c.id = cch.content_id AND cch.category_id = $sel_cat"
			. " ORDER BY c.name";

		$rows = $database->setQuery($q)->loadObjectList();

		//выводим селект выбора категорий
		echo '<select name="' . $id_cont . '" id="' . $id_cont . '" class="boss" style="width: 200px;" onchange=\'loadFunc("loadHref")\'>';
		echo "<option value=''>" . BOSS_SELECT_CONTENT . "</option>";
		foreach($rows as $row){
			echo "<option value='" . $row->id . "'>$row->name</option>";
		}
		echo '</select>';
	}

	function loadHref(){
		$sel_dir = mosGetParam($_REQUEST, 'sel_dir', 0);
		if($sel_dir == 0) return;
		$sel_cat = mosGetParam($_REQUEST, 'sel_cat', 0);
		if($sel_cat == 0) return;
		$sel_cont = mosGetParam($_REQUEST, 'sel_cont', 0);
		if($sel_cont == 0) return;
		$id_href = mosGetParam($_REQUEST, 'id_href', 'directory_htef_href');
		$database = database::getInstance();

		$q = "SELECT c.id, c.name FROM"
			. " #__boss_" . $sel_dir . "_contents AS c"
			. " WHERE c.id = " . $sel_cont . " LIMIT 1";
		$row = null;
		$database->setQuery($q)->loadObject($row);
		echo '<a href="' . JPATH_SITE . '/index.php?option=com_boss&task=show_content&contentid=' . $sel_cont . '&catid=' . $sel_cat . '&directory=' . $sel_dir . '">' . $row->name . '</a>';

	}
}

?>
