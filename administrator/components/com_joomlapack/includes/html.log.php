<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

global $JPConfiguration;
?>
<table class="adminheading">
	<tr>
		<th class="cpanel" nowrap rowspan="2"><?php echo _JP_ACTIONS_LOG?></th>
	</tr>
</table>
<div style="text-align: left; padding: 0.5em; background-color: #EEEEFE; border: thin solid black; margin: 0.5em;">
	<?php CJPLogger::VisualizeLogDirect(); ?>
</div>