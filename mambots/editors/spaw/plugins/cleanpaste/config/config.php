<?php 
/**
* @package Joostina
* @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

// Clean paste config
// When to apply cleaning to the pasted content
// "selective" -  when content matches (javascript) regular expression 
//                pattern specified in the cofiguration
// "always"
// "never"
SpawConfig::setStaticConfigItem("PG_CLEANPASTE_CLEAN", 'always', SPAW_CFG_TRANSFER_JS);
// pattern to determine that content should be cleaned in "selective" mode
SpawConfig::setStaticConfigItem("PG_CLEANPASTE_PATTERN", 
  '(urn:schemas-microsoft-com:office)'
  .'|(<([^>]*)style([\s]*)=([\s]*)([\"]*)mso)'
  .'|(<([^>]*)class([\s]*)=([\s]*)([\"]*)mso)'
  .'|(<o:)'
  , 
  SPAW_CFG_TRANSFER_JS);
?>
