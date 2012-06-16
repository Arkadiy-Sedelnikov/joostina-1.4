<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
//defined('_VALID_MOS') or die();

class BossAjaxSityPlugin{

	//имя типа поля в выпадающем списке в настройках поля
	var $name = 'Ajax Sity';

	//тип плагина для записи в таблицы
	var $type = 'BossAjaxSityPlugin';

	//отображение поля в категории
	function getListDisplay($directory, $content, $field, $field_values, $conf){
		return $this->getDetailsDisplay($directory, $content, $field, $field_values, $conf);
	}

	//отображение поля в контенте
	function getDetailsDisplay($directory, $content, $field, $field_values, $conf){
		//конфиг поля
		$conf = array();
		foreach($field_values as $fv){
			$conf[$fv->fieldtitle] = $fv->fieldvalue;
		}
		$conf['sep'] = (isset($conf['sep'])) ? $conf['sep'] : '{co}, {re}, {ci}';

		$return = '';
		if(!empty($field->text_before))
			$return .= '<div>' . $field->text_before . '</div>';
		if(!empty($field->tags_open))
			$return .= html_entity_decode($field->tags_open);

		$stroke = htmlspecialchars_decode(stripslashes($conf['sep']));
		$stroke = str_replace('{ci}', $content->content_city, $stroke);
		$stroke = str_replace('{co}', $content->content_country, $stroke);
		$stroke = str_replace('{re}', $content->content_region, $stroke);

		$return .= $stroke;
		if(!empty($field->tags_close))
			$return .= html_entity_decode($field->tags_close);
		if(!empty($field->text_after))
			$return .= '<div>' . $field->text_after . '</div>';

		return $return;
	}

	//функция вставки фрагмента ява-скрипта в скрипт
	//сохранения формы при редактировании контента с фронта.
	function addInWriteScript($field){

	}

	//отображение поля в админке в редактировании контента
	function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write"){

		$database = database::getInstance();

		//конфиг поля
		$conf = array();
		if(count($field_values[$field->fieldid]) > 0){
			foreach($field_values[$field->fieldid] as $fv){
				$conf[$fv->fieldtitle] = $fv->fieldvalue;
			}
		}
		$conf['title'] = (isset($conf['title'])) ? $conf['title'] : 'Country, Region, City, Other City';

		$tit = explode(',', $conf['title']);
		$tit[0] = (isset($tit[0])) ? $tit[0] : 'Country';
		$tit[1] = (isset($tit[1])) ? $tit[1] : 'Region';
		$tit[2] = (isset($tit[2])) ? $tit[2] : 'City';
		$tit[3] = (isset($tit[3])) ? $tit[3] : 'Other City';

		$list = $database->setQuery("SELECT * FROM  #__boss_country ORDER BY name ASC")->loadObjectList();
		$f = 0;
		$c = '';
		$n = '';
		foreach($list as $d){
			if($f == 0){
				$f = 1;
			} else{
				$c .= ',';
				$n .= ',';
			}
			$c .= $d->country_id;
			$n .= '"' . $d->name . '"';
		}
		$return = '<script type="text/javascript">
		function get_country(){
		    var JSONa={"country_id":[' . $c . '],"name":[' . $n . ']};
			var i = 0;
			var r = "";
			r = r+"<option value=\'\'>' . $tit[0] . '</option>";
			for(i=0;JSONa.name[i]!=null;i++){
			    r = r+"<option value=\'"+JSONa.country_id[i]+"\'>"+JSONa.name[i]+"</option>";
			}
			return r;
        }';

		$list = $database->setQuery("SELECT * FROM #__boss_region ORDER BY name ASC")->loadObjectList();
		$f = 0;
		$c = '';
		$n = '';
		$r = '';
		foreach($list as $d){
			if($f == 0){
				$f = 1;
			} else{
				$c .= ',';
				$n .= ',';
				$r .= ',';
			}
			$c .= $d->country_id;
			$n .= '"' . $d->name . '"';
			$r .= $d->region_id;
		}
		$return .= '
		function get_region(ci){
		    var cou = document.getElementById("country_");
            document.getElementById("content_country").value = cou.options[cou.selectedIndex].innerHTML;
		    var JSONa={"country_id":[' . $c . '], "region_id":[' . $r . '], "name": [' . $n . ']};
			var r = document.getElementById("region_");
			var i = 0;
			var res = "";
			for(i=0;JSONa.name[i]!=null;i++){
			    if(JSONa.country_id[i]==ci){
			        res = res+"<option value=\'"+JSONa.region_id[i]+"\'>"+JSONa.name[i]+"</option>";
				}
			}
			r.innerHTML = "<option> ' . $tit[1] . ' </option>"+res;
			r.disabled = false;
        }';

		$list = $database->setQuery("SELECT * FROM #__boss_city ORDER BY name ASC")->loadObjectList();
		$f = 0;
		$c = '';
		$n = '';
		$r = '';
		foreach($list as $d){
			if($f == 0){
				$f = 1;
			} else{
				$c .= ',';
				$n .= ',';
				$r .= ',';
			}
			$c .= $d->city_id;
			$n .= '"' . $d->name . '"';
			$r .= $d->region_id;
		}
		$return .= '
		function get_cities(id){
		    var cou = document.getElementById("region_");
            document.getElementById("content_region").value = cou.options[cou.selectedIndex].innerHTML;
		    var JSONa={ "city_id":[' . $c . '], "region_id" :[' . $r . '], "name":[' . $n . ']};
			var c = document.getElementById("city_");
			var i = 0;
			var res = "";
			for(i=0;JSONa.name[i]!=null;i++){
			    if(JSONa.region_id[i]==id){
			        res = res+"<option value=\'"+JSONa.city_id[i]+"\'>"+JSONa.name[i]+"</option>";
				}
			}
			c.innerHTML = "<option> ' . $tit[3] . ' </option>"+res;
			c.disabled = false;
        }		';
		$return .= '
		function get_nones(id){
		    var cou = document.getElementById("city_");
            document.getElementById("content_city").value = cou.options[cou.selectedIndex].innerHTML;
			document.getElementById("city_").disabled = false;
			document.getElementById("othercity").disabled = true;
			document.getElementById("selcit1").checked = true;
			document.getElementById("selcit2").checked = false;
        }	
        function ser_cit(v){
		    document.getElementById("citys").value = v;
		}
		function ch(t){
		    t.checked=true;
			if(t.value==1){
			    get_nones("");
			}else{
			    document.getElementById("content_city").value = document.getElementById("othercity").value;
				document.getElementById("city_").disabled = true;
				document.getElementById("othercity").disabled = false;
			}
		}
        </script>';
		$return .= '
		<tr><td>' . $tit[0] . '</td><td><select id="country_" name="country_" onChange="get_region(this.value);" mosReq="1" class="boss_required" mosLabel="' . $tit[0] . '"><option value="">' . $tit[0] . ' </option></select></td></tr>
		<tr><td>' . $tit[1] . '</td><td><select id="region_" name="region_" onChange="get_cities(this.value);" disabled mosReq="1" class="boss_required" mosLabel="' . $tit[1] . '"><option value=""> ' . $tit[1] . ' </option></select></td></tr>
		<tr><td>' . $tit[2] . '</td><td><input type="radio" name="selcit" id="selcit1" value="1" onClick="ch(this)" checked><select id="city_" name="city_" onChange="get_nones(this.value);" disabled><option value=""> ' . $tit[2] . ' </option></select></td></tr>
		<tr><td>' . $tit[3] . '</td><td><input type="radio" name="selcit" value="2" id="selcit2" onClick="ch(this);"><input type="text" name="othercity" id="othercity" onkeypress="ch(2);" onChange="ch(2);" value=""/>
		<script type="text/javascript">
		var c = document.getElementById("country_");
		c.innerHTML = get_country();
        window.onload= function () {
            var city = document.getElementById(\'content_city\').setAttribute(\'readonly\', \'\');
		    document.getElementById(\'content_region\').setAttribute(\'readonly\', \'\');
		    document.getElementById(\'content_country\').setAttribute(\'readonly\', \'\');
        };
        </script>
		';
		return $return;
	}

	//действия при сохранении контента
	function onFormSave($directory, $contentid, $field, $isUpdateMode){
		return 1;
	}

	//действия при удалении контента
	function onDelete($directory, $content){
		return;
	}

	//отображение поля в админке в настройках поля
	function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues){
		$return = "<div id='divAjaxcityOptions'>";
		$return .= "<table class='adminform'>";
		$return .= "<tr>";
		$return .= "<td width='20%'>Заголовки формы выбора</td>";
		$return .= "<td width='20%' align=left><input type='text' id='title_' name='title_' mosReq=1 mosLabel=' ' class='inputbox' value='" . @$fieldvalues['title']->fieldvalue . "' /></td>";
		$return .= "<td>Пример: Страна, Регион, Город, Другой город</td>";
		$return .= "</tr>";
		$return .= "<tr>";
		$return .= "<tr>";
		$return .= "<td width='20%'>Порядок и вид отображения</td>";
		$return .= "<td width='20%' align=left><input type='text' id='separator_' name='separator_' mosReq=1 mosLabel=' ' class='inputbox' value='" . @$fieldvalues['sep']->fieldvalue . "' /></td>";
		$return .= "<td>Пример: Страна - {co}, регион - {re}, город - {ci}<br/>Где {co} - страна, {re} - регион, {ci} - город</td>";
		$return .= "</tr>";
		$return .= "</table>";
		$return .= "</div>";
		return $return;
	}

	//действия при сохранении настроек поля
	function saveFieldOptions($directory, $field){
		$database = database::getInstance();
		$fieldid = $field->fieldid;
		$title = htmlspecialchars(stripslashes(mosGetParam($_POST, "title_", '')));
		$sep = htmlspecialchars(stripslashes(mosGetParam($_POST, "separator_", '')));

		$database->setQuery("DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = '" . $fieldid . "' ");
		$database->query();
		$database->setQuery("INSERT INTO #__boss_" . $directory . "_field_values
    		                    (fieldid, fieldtitle, fieldvalue, ordering, sys)
    		                    VALUES
    		                    ($fieldid,'title',  '$title',   1,0),
    		                    ($fieldid,'sep',    '$sep',     2,0)
    		                    ");
		$database->query();
		//если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
		//иначе true
		return false;
	}

	//расположение иконки плагина начиная со слеша от корня сайта
	function getFieldIcon($directory){
		return "/images/boss/$directory/plugins/fields/" . __CLASS__ . "/images/slide.png";
	}

	//действия при установке плагина
	function install($directory){
		$database = database::getInstance();
		//Step 0
		$database->setQuery("ALTER TABLE  `#__boss_" . $directory . "_contents` ADD  `content_city` VARCHAR( 255 ) NOT NULL")->query();
		$database->setQuery("ALTER TABLE  `#__boss_" . $directory . "_contents` ADD  `content_region` VARCHAR( 255 ) NOT NULL")->query();
		$database->setQuery("ALTER TABLE  `#__boss_" . $directory . "_contents` ADD  `content_country` VARCHAR( 255 ) NOT NULL")->query();
		//Step 1
		$database->setQuery("INSERT INTO #__boss_" . $directory . "_fields (name,title,type,searchable,published,catsid) VALUES ('content_city','City','text','1','1',',-1,')")->query();
		$database->setQuery("INSERT INTO #__boss_" . $directory . "_fields (name,title,type,searchable,published,catsid) VALUES ('content_region','Region','text','1','1',',-1,')")->query();
		$database->setQuery("INSERT INTO #__boss_" . $directory . "_fields (name,title,type,searchable,published,catsid) VALUES ('content_country','Country','text','1','1',',-1,')")->query();
	}

	function uninstall($directory = 0){
		$database = database::getInstance();
		$database->setQuery("DELETE FROM `#__boss_" . $directory . "_fields` WHERE `name` = 'content_city'")->query();
		$database->setQuery("DELETE FROM `#__boss_" . $directory . "_fields` WHERE `name` = 'content_region'")->query();
		$database->setQuery("DELETE FROM `#__boss_" . $directory . "_fields` WHERE `name` = 'content_city'")->query();
		$database->setQuery("ALTER TABLE `#__boss_" . $directory . "_contents` DROP `content_city`")->query();
		$database->setQuery("ALTER TABLE `#__boss_" . $directory . "_contents` DROP `content_region`")->query();
		$database->setQuery("ALTER TABLE `#__boss_" . $directory . "_contents` DROP `content_country`")->query();
	}

	//действия при поиске
	function search($directory, $fieldName){
		$search = '';
		$content_country = mosGetParam($_REQUEST, 'content_country', '');
		$content_region = mosGetParam($_REQUEST, 'content_region', '');
		$content_city = mosGetParam($_REQUEST, 'content_city', '');
		if(!empty($content_country))
			$search .= " AND a.content_country = '" . $content_country . "'";
		if(!empty($content_region))
			$search .= " AND a.content_region = '" . $content_region . "'";
		if(!empty($content_city))
			$search .= " AND a.content_city = '" . $content_city . "'";
		return $search;
	}
}

?>