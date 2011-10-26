<?php

defined('_VALID_MOS') or die();

function com_uninstall() {

    $database = &database::getInstance();
    $q = array();
    //запрашиваем иды каталогов
    $directories = $database->setQuery( "SELECT `id` FROM `#__boss_config`" )->loadResultArray();

    foreach($directories as $directory){
        $q[]="DROP TABLE `#__boss_".$directory."_categories`";
        $q[]="DROP TABLE `#__boss_".$directory."_contents`";
        $q[]="DROP TABLE `#__boss_".$directory."_content_category_href`";
        $q[]="DROP TABLE `#__boss_".$directory."_content_types`";
        $q[]="DROP TABLE `#__boss_".$directory."_fields`";
        $q[]="DROP TABLE `#__boss_".$directory."_field_values`";
        $q[]="DROP TABLE `#__boss_".$directory."_groupfields`";
        $q[]="DROP TABLE `#__boss_".$directory."_groups`";
        $q[]="DROP TABLE `#__boss_".$directory."_profile`";
        $q[]="DROP TABLE `#__boss_".$directory."_rating`";
        $q[]="DROP TABLE `#__boss_".$directory."_reviews`";
		$q[]="DROP TABLE `#__boss_plug_config`";

    }
    $q[]="DROP TABLE `#__boss_config`";
	foreach ($q as $key=>$query) {
		$database->setquery($query)->query();
	}
    joi_rmdir(JPATH_BASE."/images/boss");
    joi_rmdir(JPATH_BASE."/templates/com_boss");
}//function com_uninstall



function joi_rmdir($dirName) {

	if ($handle = opendir($dirName)) {

		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					joi_rmdir($dirName . '/' . $file);
					@rmdir($dirName . '/' . $file);
				} elseif (is_file($dirName . '/' . $file)) {
					@unlink($dirName . '/' . $file);
				}
			}
		}

		closedir($handle);
	}

	@rmdir($dirName);
}
?>