<?php
/**
 * Field Plug for AdsManager
 * Author: Thomas PAPIN
 * URL:  http://www.joomprod.com
 * mail: webmaster@joomprod.com
 **/
defined('_VALID_MOS') or die();

    class BossDirectoryMultiHrefPlugin {
                      
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'Directory Href Multi';
        
        //тип плагина для записи в таблицы
        var $type = 'BossDirectoryMultiHrefPlugin';
	
        //отображение поля в категории
        function getListDisplay($directory, $content, $field, $field_values, $itemid) {
            return BossDirectoryHrefPlugin::getDetailsDisplay($directory, $content, $field, $field_values, $itemid);
        }
        //отображение поля в контенте
        function getDetailsDisplay($directory, $content, $field, $field_values, $itemid) {

            $fieldName = $field->name;
            $values = $content->$fieldName;
            $return = "";
            $dataArray = array();
            if ($values != "") {
                $values = explode('|', $values);

                if(!empty($field->text_before))
                    $return .= '<span>'.$field->text_before.'</span>';
                if(!empty($field->tags_open))
                    $return .= html_entity_decode($field->tags_open);

                foreach($values as $value){
                    $dataArray[] = '<div class="directory_multihref">' . stripslashes($value) . '</div>';
                }

                $return .= implode( html_entity_decode($field->tags_separator), $dataArray);

                if(!empty($field->tags_close))
                    $return .= html_entity_decode($field->tags_close);
                if(!empty($field->text_after))
                    $return .= '<span>'.$field->text_after.'</span>';
            }
            return $return;
        }

        //функция вставки фрагмента ява-скрипта в скрипт
        //сохранения формы при редактировании контента с фронта.
        function addInWriteScript($field){

        }

        //отображение поля в админке в редактировании контента
        function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write") {
            global $mainframe;
            $return  = '';
            $fieldname = $field->name;
            $values = (isset ($content->$fieldname)) ? stripslashes($content->$fieldname) : '';
            $values = explode('|', $values);

            $strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');
            if (($mode == "write") && ($field->editable == 0))
                $readonly = "readonly=true";
            else
                $readonly = "";

            if (($mode == "write") && ($field->required == 1)) {
                $class = 'boss_required';
                $mosReq= "mosReq='1'";
            }
            else {
                $class = 'boss';
                $mosReq= "";
            }

            if($mainframe->isAdmin()!=1  && $field->editable == 0){
                if(count($values)>0) {
                    foreach($values as $value){
                        $return .= "<div class = 'dxcontainer'>";
                        $return .= "<input type='text' class='".$class."' $mosReq id='" . $fieldname . "_href' test='DirHref' name='" . $fieldname . "_href[]' mosLabel='" . $strtitle . "' size='$field->size' maxlength='$field->maxlength' $readonly value='$value' />\n";
                        $return .= "</div>";
                    }
                }
                return $return;
            }
            ?>
            <script type="text/javascript">
                jQuery.noConflict();
                function loadFunc(func, fieldname){
                    var url     = 'http://'+location.hostname;
                    url = url+'/administrator/ajax.index.php?option=com_boss&act=plugins&task=run_plugins_func&directory=<?php echo $directory;?>&class=BossDirectoryMultiHrefPlugin&function='+func+'&fieldname='+fieldname;

                    if(func == 'loadCategory'){
                        jQuery('#'+fieldname+'_content').html('');
                        jQuery('#'+fieldname+'_category').html('');
                    }

                    if(func == 'loadContent'){
                        jQuery('#'+fieldname+'_content').html('');
                    }

                    if(jQuery("select").is('#'+fieldname+'_directory')){
                        var sel_dir = jQuery('#'+fieldname+'_directory').val()
                        url = url+'&sel_dir='+sel_dir;
                        url = url+'&id_cat='+fieldname+'_category_sel';
                    }

                    if(jQuery("select").is('#'+fieldname+'_category_sel')){
                        var sel_cat = jQuery('#'+fieldname+'_category_sel').val()
                        url = url+'&sel_cat='+sel_cat;
                        url = url+'&id_cont='+fieldname+'_content_sel';
                    }

                    if(jQuery("select").is('#'+fieldname+'_content_sel')){
                        var sel_cont = jQuery('#'+fieldname+'_content_sel').val()
                        url = url+'&sel_cont='+sel_cont;
                        url = url+'&id_href='+fieldname+'_href';
                    }

                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'HTML',
                        success: function (data){
                            if(jQuery("select").is('#'+fieldname+'_content_sel')){
                                insertRow(data, fieldname);
                            }
                            else if(jQuery("select").is('#'+fieldname+'_category_sel')){
                                jQuery('#'+fieldname+'_content').html(data);
                            }
                            else if(jQuery("select").is('#'+fieldname+'_directory')){
                                jQuery('#'+fieldname+'_category').html(data);
                            }
                        }
                    });

                }

                function insertRow(data, fieldname) {
                    var quantity = jQuery(".dxcontainer").length;
                    var newId = fieldname+"_href"+quantity;
                    var newInp = "<input type='text' class='<?php echo $class; ?>' style='border-color:#dc143c;' <?php echo $mosReq; ?> id='"+newId+"' test='DirHref' name='<?php echo $fieldname . "_href[]"; ?>' mosLabel='<?php echo $strtitle; ?>' size='<?php echo $field->size; ?>' maxlength='<?php echo $field->maxlength; ?>' <?php echo $readonly; ?> value='"+data+"' />";
                    newInp += " <input type='button' value='X' onclick='deleteRow('inpdiv"+quantity+"');'>";

                    var newDiv = "<div class = 'dxcontainer' id='inpdiv"+quantity+"'></div>";

                    jQuery("#dx_first").prepend(newDiv);
                    jQuery("#inpdiv"+quantity).html(newInp);
                }

                function deleteRow(id) {
                    jQuery("#"+id).remove();
                }
                jQuery.noConflict();
            </script>

            <?php

            $return .= "<table><tr><td>";
            $return .= "<select class='boss' style='width: 200px;' name='" . $fieldname . "_directory' id='" . $fieldname . "_directory'". 'onchange="loadFunc(\'loadCategory\', \''.$fieldname.'\')"' ."/>\n";
            $return .= $this->loadDirectories();
            $return .= "</select>";
            $return .= "</td><td>";
            $return .= "<div id='" . $fieldname . "_category'></div>";
            $return .= "</td><td>";
            $return .= "<div id='" . $fieldname . "_content'></div>";
            $return .= "</td></tr></table>";
            $i=0;
            if(count($values)>0) {
                foreach($values as $value){
                    $return .= "<div class = 'dxcontainer' id='inpdiv".$i."'>";
                    $return .= "<input type='text' class='".$class."' $mosReq id='" . $fieldname . "_href' test='DirHref' name='" . $fieldname . "_href[]' mosLabel='" . $strtitle . "' size='$field->size' maxlength='$field->maxlength' $readonly value='$value' />\n";
                    $return .= "<input type='button' value='X' onclick='deleteRow(\"inpdiv".$i."\");'>";
                    $return .= "</div>";
                    $i++;
                }
            }
            $return .= "<div id='dx_first'></div>";

            return $return;
        }
        //действия при сохранении контента
        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $return = mosGetParam($_POST, $field->name."_href", '', _MOS_ALLOWHTML);
            $return = addslashes(implode('|', $return));
            return $return;
        }
        //действия при удалении контента
        function onDelete($directory, $contentid = -1) {
             return;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues) {
            $return = "";
            return $return;
        }

        //действия при сохранении настроек поля
        //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
        //иначе true
        function saveFieldOptions($directory, $field) {
            return false;
        }

        //расположение иконки плагина начиная со слеша от корня сайта
        function getFieldIcon($directory) {
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/folder_image.png";
        }
        //действия при установке плагина
        function install() {
            return;
        }
        //действия при удалении плагина
        function uninstall() {
            return;
        }

        //действия при поиске
        function search($directory,$fieldName) {
            return;
        }

        /************************/
        /******AJAX функции******/
        /************************/
        function loadDirectories() {
        	$database = database::getInstance();
        	$directories = $database->setQuery("SELECT id,name FROM #__boss_config")->loadObjectList("id");

            $return =  "<option value=''>".BOSS_DIRECTORY_SEL."</option>";
            foreach ($directories as $d) {
                $return .=  "<option value='". $d->id . "'>" . $d->name . "&nbsp;(" . $d->id . ")</option>";
            }
            return $return;
        }

        function loadCategory(){
            $directory  = mosGetParam($_REQUEST, 'sel_dir', 0);
            if($directory  == 0) return;
            $id_cat  = mosGetParam($_REQUEST, 'id_cat', 'directory_htef_category');
            $fieldname  = mosGetParam($_REQUEST, 'fieldname', '');
            $database = database::getInstance();

            require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_boss'.DS.'admin.boss.html.php');

		    $rows = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_categories ORDER BY parent, ordering")->loadObjectList();

            // establish the hierarchy of the menu
	        $children = array();
	        // first pass - collect children
	        foreach ($rows as $v) {
	        	$pt = $v->parent;
	        	$list = isset($children[$pt]) ? $children[$pt] : array();
	        	array_push($list, $v);
	        	$children[$pt] = $list;
	        }
            //выводим селект выбора категорий
            echo '<select name="'.$id_cat.'" id="'.$id_cat.'" class="boss" style="width: 200px;" onchange=\'loadFunc("loadContent", "'.$fieldname.'")\'>';
            echo "<option value=''>".BOSS_SELECT_CATEGORY."</option>";
            HTML_boss::selectCategories(0, "Корень >> ", $children);
            echo '</select>';
        }

        function loadContent(){
            $sel_dir  = mosGetParam($_REQUEST, 'sel_dir', 0);
            if($sel_dir  == 0) return;
            $sel_cat  = mosGetParam($_REQUEST, 'sel_cat', 0);
            if($sel_cat == 0) return;
            $id_cont  = mosGetParam($_REQUEST, 'id_cont', 'directory_htef_content');
            $fieldname  = mosGetParam($_REQUEST, 'fieldname', '');
            $database = database::getInstance();

            $q = "SELECT c.id, c.name FROM"
            ." #__boss_" . $sel_dir . "_contents AS c,"
            ." #__boss_" . $sel_dir . "_content_category_href AS cch"
            ." WHERE c.id = cch.content_id AND cch.category_id = $sel_cat"
            ." ORDER BY c.name";

		    $rows = $database->setQuery($q)->loadObjectList();

            //выводим селект выбора категорий
            echo '<select name="'.$id_cont.'" id="'.$id_cont.'" class="boss" style="width: 200px;" onchange=\'loadFunc("loadHref", "'.$fieldname.'")\'>';
            echo "<option value=''>".BOSS_SELECT_CONTENT."</option>";
            foreach($rows as $row){
                 echo "<option value='".$row->id."'>$row->name</option>";
            }
            echo '</select>';
        }

        function loadHref(){
            $sel_dir  = mosGetParam($_REQUEST, 'sel_dir', 0);
            if($sel_dir  == 0) return;
            $sel_cat  = mosGetParam($_REQUEST, 'sel_cat', 0);
            if($sel_cat == 0) return;
            $sel_cont  = mosGetParam($_REQUEST, 'sel_cont', 0);
            if($sel_cont == 0) return;
            $id_href = mosGetParam($_REQUEST, 'id_href', 'directory_htef_href');
            $database = database::getInstance();

            $q = "SELECT c.id, c.name FROM"
            ." #__boss_" . $sel_dir . "_contents AS c"
            ." WHERE c.id = ".$sel_cont." LIMIT 1";
            $row = null;
		    $database->setQuery($q)->loadObject($row);
            $itemid = getBossItemid($sel_dir, $sel_cat);
            echo '<a href="'.JPATH_SITE.'/index.php?option=com_boss&task=show_content&contentid='.$sel_cont.'&catid='.$sel_cat.'&directory='.$sel_dir.'&Itemid='.$itemid.'">'.$row->name.'</a>';
        }
    }
?>
