<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

    class BossGmapPlugin {
        
        //имя типа поля в выпадающем списке в настройках поля
        var $name = 'GMap Field';
        
        //тип плагина для записи в таблицы
        var $type = 'BossGmapPlugin';
        
        //отображение поля в категории
        function getListDisplay($directory, $content, $field, $field_values, $itemid, $conf) {
            return $this->getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf);
        }

        //отображение поля в контенте
        function getDetailsDisplay($directory, $content, $field, $field_values, $itemid, $conf) {

            $fieldname = $field->name;
            $data = $content->$fieldname;
            $data = explode('|', $data);
            $lat = (!empty($data[0])) ? $data[0] : '';
            $lng = (!empty($data[1])) ? $data[1] : '';
            $return = '';
            if (!empty($lng)) {
                $database = database::getInstance();
                $fieldid = $field->fieldid;

                $database->setQuery("SELECT `fieldtitle`, `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = '$fieldid'");
                $conf = $database->loadObjectList('fieldtitle');
                $map_width = $conf['gmap_map_width']->fieldvalue; //500;
                $map_height = $conf['gmap_map_height']->fieldvalue; //300;
                $google_key = $conf['gmap_google_key']->fieldvalue;


                if(!empty($field->text_before))
                    $return .= '<span>'.$field->text_before.'</span>';
                if(!empty($field->tags_open))
                    $return .= html_entity_decode($field->tags_open);

                $return .= '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=' . $google_key . '" type="text/javascript"></script>';
                $return .= '<script type="text/javascript">';
                $return .= 'function initialize() {';
                $return .= '  if (GBrowserIsCompatible()) {';
                $return .= '    var map = new GMap2(document.getElementById("map_canvas' . $fieldid . '_' . $content->id . '"));';
                $return .= '    map.setCenter(new GLatLng(' . $lat . ', ' . $lng . '), 13);';
                $return .= '	var center = new GLatLng(' . $lat . ', ' . $lng . ');';
                $return .= '    var marker = new GMarker(center); map.addOverlay(marker);';
                $return .= '	map.addControl(new GSmallMapControl());';
                $return .= '    map.addControl(new GMapTypeControl());';
                $return .= '  }';
                $return .= '}';
                $return .= '</script>';
                $return .= '<div id="map_canvas' . $fieldid . '_' . $content->id . '" style="width: ' . $map_width . 'px; height: ' . $map_height . 'px"></div>';
                $return .= '<script type="text/javascript">initialize();</script>';

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
            $database = database::getInstance();
            $fieldid = $field->fieldid;
            $fieldname = $field->name;
            $database->setQuery("SELECT `fieldtitle`, `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = '$fieldid'");
            $conf = $database->loadObjectList('fieldtitle');

            @$data = $content->$fieldname;
            $data = explode('|', $data);

            $lat = (!empty($data[0])) ? $data[0] : $conf['gmap_lat']->fieldvalue;
            $lng = (!empty($data[1])) ? $data[1] : $conf['gmap_lng']->fieldvalue;

            $map_width = $conf['gmap_map_width']->fieldvalue; //500;
            $map_height = $conf['gmap_map_height']->fieldvalue; //300;
            $google_key = $conf['gmap_google_key']->fieldvalue; //ABQIAAAAbgp4ITpmNUShfIO_dNHv_BR3Tz62YPXwBIaKJWeQ0jDUesttEhTdqyqafAWvPNs2HRK7lWBo2Yemww

            $return = '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=' . $google_key . '" type="text/javascript"></script>';
            $return .= '<script type="text/javascript">';

            $return .= 'var map = null;';
            $return .= 'var geocoder = null;';
            $return .= 'var marker = null;';

            $return .= 'function initialize() {';
            $return .= '  if (GBrowserIsCompatible()) {';
            $return .= '    map = new GMap2(document.getElementById("map_canvas' . $fieldid . '"));';
            $return .= '    map.setCenter(new GLatLng(' . $lat . ', ' . $lng . '), 13);';
            $return .= '	var center = new GLatLng(' . $lat . ', ' . $lng . ');';
            $return .= '    marker = new GMarker(center, {draggable: true}); map.addOverlay(marker);';
            $return .= '	  GEvent.addListener(marker, "dragstart", function() {';
            $return .= '        });';

            $return .= '        GEvent.addListener(marker, "dragend", function() {';
            $return .= '		  document.getElementById("gmap_lat' . $fieldid . '").value = marker.getLatLng().lat();';
            $return .= '		  document.getElementById("gmap_lng' . $fieldid . '").value = marker.getLatLng().lng();';
            $return .= '        });';
            $return .= '	map.addControl(new GSmallMapControl());';
            $return .= '    map.addControl(new GMapTypeControl());';
            $return .= '    geocoder = new GClientGeocoder();';
            $return .= '  }';
            $return .= '}';

            $return .= 'function showAddress(address) {';
            $return .= 'if (geocoder) {';
            $return .= 'geocoder.getLatLng(';
            $return .= '  address,';
            $return .= '  function(point) {';
            $return .= '    if (!point) {';
            $return .= '      alert(address + " not found");';
            $return .= '   } else {';
            $return .= '      map.setCenter(point, 13);';
            $return .= '	  delete marker;';
            $return .= '	  map.clearOverlays();';
            $return .= '      marker = new GMarker(point, {draggable: true});	';
            $return .= '	  document.getElementById("gmap_lat' . $fieldid . '").value = marker.getLatLng().lat();';
            $return .= '	  document.getElementById("gmap_lng' . $fieldid . '").value = marker.getLatLng().lng();';
            $return .= '	  GEvent.addListener(marker, "dragstart", function() {';
            $return .= '        });';

            $return .= '        GEvent.addListener(marker, "dragend", function() {';
            $return .= '		  document.getElementById("gmap_lat' . $fieldid . '").value = marker.getLatLng().lat();';
            $return .= '		  document.getElementById("gmap_lng' . $fieldid . '").value = marker.getLatLng().lng();';
            $return .= '        });';
            $return .= '      map.addOverlay(marker);';
            $return .= '    }';
            $return .= '  }';
            $return .= ');';
            $return .= '}';
            $return .= '}';
            $return .= '</script>';
            $return .= '</script>';
            $return .= '<div>';
            $return .= '<input type="text" size="60" name="gmap_address' . $fieldid . '" value="Enter an address to search on the map" />';
            $return .= '<input type="button" value="Go!" onClick="showAddress(' . $nameform . '.gmap_address' . $fieldid . '.value);" />';
            $return .= '<div id="map_canvas' . $fieldid . '" style="width: ' . $map_width . 'px; height: ' . $map_height . 'px"></div>';
            $return .= '<input type="hidden" id="gmap_lat' . $fieldid . '" name="gmap_lat' . $fieldid . '" value="' . $lat . '"/>';
            $return .= '<input type="hidden" id="gmap_lng' . $fieldid . '" name="gmap_lng' . $fieldid . '" value="' . $lng . '"/>';
            $return .= '<script type="text/javascript">initialize();</script>';
            $return .= 'If GoogleMap doesn\'t find correctly your address, you can drag the marker to the correct position';
            $return .= '</div>';

            return $return;
        }

        function onFormSave($directory, $contentid, $field, $isUpdateMode, $itemid) {
            $lat = mosGetParam($_POST, "gmap_lat$field->fieldid", '');
            $lng = mosGetParam($_POST, "gmap_lng$field->fieldid", '');
            $return = $lat . '|' . $lng;
            return $return;
        }

        function onDelete($directory, $contentid = -1) {
            return;
        }

        //отображение поля в админке в настройках поля
        function getEditFieldOptions($row, $directory,$fieldimages,$fieldvalues)
        {
            $return = "<div id='divGMapOptions'>\n";
            $return .= "<table class='adminform'>\n";
            $return .= "<tr>\n";
            $return .= "<td width='20%'>Map Width</td>\n";
            $return .= "<td width='20%' align=left><input type='text' id='gmap_map_width' name='gmap_map_width' mosReq=1 mosLabel='Map Width' class='inputbox' value='" . @$fieldvalues['gmap_map_width']->fieldvalue . "' /></td>\n";
            $return .= "<td>&nbsp;</td>\n";
            $return .= "</tr>\n";
            $return .= "<tr>\n";
            $return .= "<td width='20%'>Map Height</td>\n";
            $return .= "<td width='20%' align=left><input type='text' id='gmap_map_height' name='gmap_map_height' mosReq=1 mosLabel='Map Height' class='inputbox' value='" . @$fieldvalues['gmap_map_height']->fieldvalue . "' /></td>\n";
            $return .= "<td>&nbsp;</td>\n";
            $return .= "</tr>\n";
            $return .= "<tr>\n";
            $return .= "<td width='20%'>Default Lat</td>\n";
            $return .= "<td width='20%' align=left><input type='text' id='gmap_lat' name='gmap_lat' mosReq=1 mosLabel='Default Lat' class='inputbox' value='" . @$fieldvalues['gmap_lat']->fieldvalue . "' /></td>\n";
            $return .= "<td>&nbsp;</td>\n";
            $return .= "</tr>\n";
            $return .= "<tr>\n";
            $return .= "<td width='20%'>Default Lng</td>\n";
            $return .= "<td width='20%' align=left><input type='text' id='gmap_lng' name='gmap_lng' mosReq=1 mosLabel='Default Lng' class='inputbox' value='" . @$fieldvalues['gmap_lng']->fieldvalue . "' /></td>\n";
            $return .= "<td>&nbsp;</td>\n"; 
            $return .= "</tr>\n";
            $return .= "<tr>\n";
            $return .= "<td width='20%'>Google Key</td>\n";
            $return .= "<td width='20%' align=left><input type='text' id='gmap_google_key' name='gmap_google_key' mosReq=1 mosLabel='Google Key' class='inputbox' value='" . @$fieldvalues['gmap_google_key']->fieldvalue . "' /></td>\n";
            $return .= "<td>&nbsp;</td>\n";
            $return .= "</tr>\n";
            $return .= "</table>\n";
            $return .= "</div>\n";
            
            return $return;
        }

        //действия при сохранении настроек поля
        function saveFieldOptions($directory, $field) {
            $fieldid = $field->fieldid;
            $fieldname = $field->name;
            $database = database::getInstance();
            $gmap_map_width = mosGetParam($_POST, "gmap_map_width", 0);
            $gmap_map_height = mosGetParam($_POST, "gmap_map_height", 0);
            $gmap_lat = mosGetParam($_POST, "gmap_lat", '');
            $gmap_lng = mosGetParam($_POST, "gmap_lng", '');
            $gmap_google_key = mosGetParam($_POST, "gmap_google_key", '');
            $database->setQuery("DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = '" . $fieldid . "' ");
            $database->query();
            $database->setQuery("INSERT INTO #__boss_" . $directory . "_field_values
    		                    (fieldid, fieldtitle, fieldvalue, ordering, sys)
    		                    VALUES
    		                    ($fieldid,'gmap_map_width',     '$gmap_map_width',  1,0),
    		                    ($fieldid,'gmap_map_height',    '$gmap_map_height', 2,0),
    		                    ($fieldid,'gmap_lat',           '$gmap_lat',        3,0),
    		                    ($fieldid,'gmap_lng',           '$gmap_lng',        4,0),
    		                    ($fieldid,'gmap_google_key',    '$gmap_google_key', 5,0)
    		                    ");
            $database->query();      
            //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
            //иначе true
            return false;
        }

        //расположение иконки плагина начиная со слеша от корня сайта
        function getFieldIcon($directory) {
            return "/images/boss/$directory/plugins/fields/".__CLASS__."/images/map.png";
        }

        //действия при установке плагина
        function install($directory) {
            return;
        }

        //действия при удалении плагина
        function uninstall($directory) {
            return;
        }

        //действия при поиске
        function search($directory,$fieldName) {
            return;
        }
    }
?>