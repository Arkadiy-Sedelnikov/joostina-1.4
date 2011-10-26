<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 *
 * Backend content viewer. Info page.
 *
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage	Admin
 * @filename info.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 *
 **/

defined('_VALID_MOS') or die();

$mainframe	= &mosMainFrame::getInstance(true);

?>

<h2>ElFinder + ElRTE</h2>
<p><a target="_blank" href="http://elrte.org/"><?php echo _ELRTE_INFO_DESC; ?> </a></p>

<table  width="100%" class="paramlist">
	<tr><th class="key"><?php echo _ELRTE_INFO_VER; ?>: </th><td>1.0.0a</td></tr>
	<tr><th class="key"><?php echo _ELRTE_INFO_VER_ELRTE; ?>: </th><td>1.2</td></tr>
	<tr><th class="key"><?php echo _ELRTE_INFO_VER_ELFINDER; ?>: </th><td>1.1</td></tr>
	<tr><th class="key"><?php echo _ELRTE_INFO_DEV; ?>: </th><td>Arkadiy Sedelnikov (Joostina Team, <a target="_blank" href="http://www.joostina.ru">www.joostina.ru</a>)</td></tr>
	<tr><th class="key">GoogleCode:</th><td><a target="_blank" href="#">http://code.google.com/p/###/</a></td></tr>
</table>