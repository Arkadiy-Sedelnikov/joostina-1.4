<?php

defined('_VALID_MOS') or die();

function com_install() {

  $mainframe = &mosMainFrame::getInstance();
  $database = &database::getInstance();

  //РАСПАКОВКА АРХИВОВ  
  //Файлы фронта
  $zipfile = JPATH_BASE.DS.'components'.DS.'com_boss'.DS.'front.zip';
  $unzip_dir = JPATH_BASE.DS.'components'.DS.'com_boss';
  if(!joi_unzip($zipfile, $unzip_dir)) return false;
  joi_chmod ($unzip_dir);

  //Файлы админки
  $zipfile = JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_boss'.DS.'admin.zip';
  $unzip_dir = JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_boss';
  if(!joi_unzip($zipfile, $unzip_dir)) return false;
  joi_chmod ($unzip_dir);

  //Файлы шаблонов
  $zipfile = JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_boss'.DS.'templates.zip';
  $unzip_dir = JPATH_BASE.DS.'templates'.DS.'com_boss';
  if(!is_dir($unzip_dir)) mkdir($unzip_dir);
  if(!joi_unzip($zipfile, $unzip_dir)) return false;
  joi_chmod ($unzip_dir);

  //создание таблицы конфигурации компонента
  $database->setQuery("
            CREATE TABLE IF NOT EXISTS `#__boss_config` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` text NOT NULL,
            `slug` varchar(200) NOT NULL,
            `meta_title` varchar(60) NOT NULL,
            `meta_desc` varchar(200) NOT NULL,
            `meta_keys` varchar(200) NOT NULL,
            `default_order_by` varchar(20) NOT NULL,
            `contents_per_page` int(10) unsigned NOT NULL DEFAULT '20',
            `root_allowed` tinyint(4) NOT NULL DEFAULT '1',
            `show_contact` tinyint(4) NOT NULL DEFAULT '1',
            `send_email_on_new` tinyint(4) NOT NULL DEFAULT '1',
            `send_email_on_update` tinyint(4) NOT NULL DEFAULT '1',
            `auto_publish` tinyint(4) NOT NULL DEFAULT '1',
            `fronttext` text NOT NULL,
            `email_display` tinyint(4) NOT NULL DEFAULT '0',
            `display_fullname` tinyint(4) NOT NULL DEFAULT '2',
            `rules_text` text NOT NULL,
            `expiration` tinyint(1) NOT NULL DEFAULT '0',
            `content_duration` int(4) NOT NULL default '30',
            `recall` tinyint(1) NOT NULL default '1',
            `recall_time` int(4) NOT NULL default '7',
            `recall_text` text NOT NULL,
            `empty_cat` tinyint(1) NOT NULL DEFAULT '1',
            `cat_max_width` int(4) NOT NULL DEFAULT '150',
            `cat_max_height` int(4) NOT NULL DEFAULT '150',
            `cat_max_width_t` int(4) NOT NULL DEFAULT '30',
            `cat_max_height_t` int(4) NOT NULL DEFAULT '30',
            `submission_type` int(4) NOT NULL DEFAULT '30',
            `nb_contents_by_user` int(4) NOT NULL DEFAULT '-1',
            `allow_attachement` tinyint(1) NOT NULL DEFAULT '0',
            `allow_contact_by_pms` tinyint(1) NOT NULL DEFAULT '0',
            `allow_comments` tinyint(1) NOT NULL DEFAULT '0',
            `rating` varchar(50) NOT NULL,
            `secure_comment` tinyint(1) NOT NULL DEFAULT '0',
            `comment_sys` tinyint(1) NOT NULL,
            `allow_unregisered_comment` tinyint(1) NOT NULL,
            `allow_ratings` tinyint(1) NOT NULL,
            `secure_new_content` tinyint(1) NOT NULL DEFAULT '0',
            `use_content_mambot` tinyint(1) NOT NULL DEFAULT '0',
            `show_rss` tinyint(1) NOT NULL DEFAULT '0',
            `filter` varchar(50) NOT NULL DEFAULT 'no',
            `template` varchar(255) NOT NULL DEFAULT 'default',
            `allow_rights` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
            `rights` TEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ");
  $database->query();

  //создание таблицы конфигурации плагинов
  $database->setQuery("
            CREATE TABLE IF NOT EXISTS `#__boss_plug_config` (
            `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `directory` INT( 11 ) NOT NULL ,
            `plug_type` VARCHAR( 11 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            `plug_name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            `title` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            `value` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            INDEX (  `directory` ,  `plug_type` ,  `plug_name` )
            ) ENGINE = MYISAM ;
        ");
  $database->query();

  // Установка иконок меню
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/component.png'
                        WHERE admin_menu_link='option=com_boss'");
  $iconresult[0] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/manager.png'
                        WHERE admin_menu_link='option=com_boss&act=manager'");
  $iconresult[1] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/configuration.png'
                        WHERE admin_menu_link='option=com_boss&act=configuration'");
  $iconresult[2] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/fields.png'
                        WHERE admin_menu_link='option=com_boss&act=fields'");
  $iconresult[3] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/categories.png'
                        WHERE admin_menu_link='option=com_boss&act=categories'");
  $iconresult[4] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/contents.png'
                        WHERE admin_menu_link='option=com_boss&act=contents'");
  $iconresult[5] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/templates.png'
                        WHERE admin_menu_link='option=com_boss&act=templates'");
  $iconresult[6] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/plugins.png'
                        WHERE admin_menu_link='option=com_boss&act=plugins'");
  $iconresult[7] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/fieldimage.png'
                        WHERE admin_menu_link='option=com_boss&act=fieldimage'");
  $iconresult[8] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/export_import.png'
                        WHERE admin_menu_link='option=com_boss&act=export_import'"); 
  $iconresult[9] = $database->query();
  $database->setQuery("UPDATE #__components
                          SET admin_menu_img='../administrator/components/com_boss/images/16x16/user.png'
                        WHERE admin_menu_link='option=com_boss&act=users'");
  $iconresult[10] = $database->query();

  if(!$database->query()) echo $database->getErrorMsg().'<br />';

  foreach ($iconresult as $i=>$icresult) {
          if (!$icresult) echo '<font color="red">Ошибка установки: </font> иконка '.$i
                              .' не была скопирована<br />';

  }//foreach

}//function com_install


function joi_chmod ($dir) {
  $filemode = 0644;
  $dirmode  = 0755;
  mosChmodRecursive($dir, $filemode, $dirmode);
  return true;
}

function joi_unzip($zip, $unzip_dir) {
  $mainframe = &mosMainFrame::getInstance();

  require_once (JPATH_BASE.'/administrator/includes/pcl/pclzip.lib.php');
  require_once (JPATH_BASE.'/administrator/includes/pcl/pclerror.lib.php');

  $zipfile = new PclZip($zip);

  $ret = $zipfile->extract(PCLZIP_OPT_PATH, $unzip_dir);
  if ($ret == 0) {
          echo "Неисправимая ошибка: " . $zipfile->errorName(true).'<br />';
          return false;
  }
  unlink($zip);
  return true;
}
?>