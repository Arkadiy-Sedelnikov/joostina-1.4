<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

// запрет прямого доступа
defined( '_VALID_MOS' ) or die( 'Прямой вызов файла запрещен' );
/*------------------------------------------------------------------------------
     The contents of this file are subject to the Mozilla Public License
     Version 1.1 (the "License"); you may not use this file except in
     compliance with the License. You may obtain a copy of the License at
     http://www.mozilla.org/MPL/

     Software distributed under the License is distributed on an "AS IS"
     basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
     License for the specific language governing rights and limitations
     under the License.

     The Original Code is fun_down.php, released on 2003-01-25.

     The Initial Developer of the Original Code is The QuiX project.

     Alternatively, the contents of this file may be used under the terms
     of the GNU General Public License Version 2 or later (the "GPL"), in
     which case the provisions of the GPL are applicable instead of
     those above. If you wish to allow use of your version of this file only
     under the terms of the GPL and not to allow others to use
     your version of this file under the MPL, indicate your decision by
     deleting  the provisions above and replace  them with the notice and
     other provisions required by the GPL.  If you do not delete
     the provisions above, a recipient may use your version of this file
     under either the MPL or the GPL."
------------------------------------------------------------------------------*/
/**
* @author soeren
* @copyright soeren (C) 2006
* 
* This file handles ftp authentication
*/

function ftp_authentication( $ftp_login='', $ftp_pass='') {
	global $dir;
	
	if( $ftp_login != '' || $ftp_pass != '' ) {
		while( @ob_end_clean() );
			
		@header("Status: 200 OK");
		$ftp_host = mosGetParam( $_POST, 'ftp_host', 'localhost:21' );
		$url = @parse_url( 'ftp://' . $ftp_host);
		if( empty( $url )) {			
			echo jx_alertBox( 'Unable to parse the specified Host Name. Please use a hostname in this format: hostname:21' );
			echo jx_scriptTag('', '$(\'loadingindicator\').innerHTML = \'\';' );
			echo 'Unable to parse the specified Host Name. Please use a hostname in this format: hostname:21';
			exit;
		}
		$port = empty($url['port']) ? 21 : $url['port'];
		$ftp = new Net_FTP( $url['host'], $port, 20 );
		
		$res = $ftp->connect();
		if( PEAR::isError( $res )) {
			echo jx_alertBox( $GLOBALS['messages']['ftp_connection_failed'] );
			echo jx_scriptTag('', '$(\'loadingindicator\').innerHTML = \'\';' );
			echo $GLOBALS['messages']['ftp_connection_failed'].'<br />['.$res->getMessage().']';
			exit;
		}
		else {
			$res = $ftp->login( $ftp_login, $ftp_pass );
			$ftp->disconnect();
			if( PEAR::isError( $res )) {
				echo jx_alertBox( $GLOBALS['messages']['ftp_login_failed'] );
				echo jx_scriptTag('', '$(\'loadingindicator\').innerHTML = \'\';' );
				echo $GLOBALS['messages']['ftp_login_failed'].'<br />['.$res->getMessage().']';
				exit;
			}
			echo jx_alertBox('Вход выполнен успешно!');
			$_SESSION['ftp_login'] = $ftp_login;
			$_SESSION['ftp_pass'] = $ftp_pass;
			$_SESSION['ftp_host'] = $_POST['ftp_host'];
			
			session_write_close();
			
			echo jx_docLocation( str_replace( 'index3.php', 'index2.php', make_link('list', '' ).'&file_mode=ftp' ));
			exit;
		}
		
	}
	else {
		?>
		<script type="text/javascript" src="components/com_joomlaxplorer/scripts/mootools.ajax.js"></script>
		<script type="text/javascript" src="components/com_joomlaxplorer/scripts/functions.js"></script>
		<script type="text/javascript">
		function showLoadingIndicator( el, replaceContent ) {
			if( !el ) return;
			var loadingimg = 'components/com_joomlaxplorer/images/indicator.gif';
			var imgtag = '<img src="'+ loadingimg + '" alt="Загрузка..." border="0" name="Loading" align="absmiddle" />';

			if( replaceContent ) {
				el.innerHTML = imgtag;
			}
			else {
				el.innerHTML += imgtag;
			}
		}
		function checkFTPAuth( url ) {
			showLoadingIndicator( $('loadingindicator'), true );
			$('loadingindicator').innerHTML += ' <strong><?php echo $GLOBALS['messages']['ftp_login_check'] ?></strong>';
			
			var controller = new ajax( url, {	postBody: $('adminform'),
												evalScripts: true,
												update: 'statustext' 
												} 
									);
			controller.request();
			return false;
		}
		</script>
		
		<?php
	show_header(null);
	?><br />
	
	<form name="ftp_auth_form" method="post" action="<?php echo JPATH_SITE ?>/<?php echo JADMIN_BASE?>/index3.php" onsubmit="return checkFTPAuth('<?php echo JPATH_SITE ?>/<?php echo JADMIN_BASE?>/index3.php');" id="adminform">
	
	<input type="hidden" name="no_html" value="1" />
	<table class="adminform" style="width:400px;">
		<tr><th colspan="3"><?php echo $GLOBALS["messages"]["ftp_login_lbl"] ?></th></tr>
		
		<tr><td colspan="3" style="text-align:center;" id="loadingindicator"></td></tr>
		<tr><td colspan="3" style="font-weight:bold;text-align:center" id="statustext">&nbsp;</td></tr>
		
		<tr>
			<td width="50" style="text-align:center;" rowspan="3"><img align="absmiddle" src="images/security_f2.png" alt="Login!" /></td>
			<td><?php echo $GLOBALS["messages"]["ftp_login_name"] ?>:</td>
			<td align="left">
				<input type="text" name="ftp_login_name" size="25" title="<?php echo $GLOBALS["messages"]["ftp_login_name"] ?>" />
			</td>
		</tr>		
		<tr>
			<td><?php echo $GLOBALS["messages"]["ftp_login_pass"] ?>:</td>
			<td align="left">
				<input type="password" name="ftp_login_pass" size="25" title="<?php echo $GLOBALS["messages"]["ftp_login_pass"] ?>" />
			</td>
		</tr>		
		<tr>
			<td><?php echo $GLOBALS["messages"]["ftp_hostname_port"] ?>:</td>
			<td align="left">
				<input type="text" name="ftp_host" size="25" title="<?php echo $GLOBALS["messages"]["ftp_hostname"] ?>" value="<?php echo mosGetParam($_SESSION,'ftp_host', 'localhost:21') ?>" />
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td style="text-align:center;" colspan="3">
			<input type="hidden" name="action" value="ftp_authentication" />
			<input type="hidden" name="option" value="com_joomlaxplorer" />
			<input type="submit" name="submit" value="<?php echo $GLOBALS['messages']['btnlogin'] ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" name="cancel" value="<?php echo $GLOBALS['messages']['btncancel'] ?>" onclick="javascript:document.location='<?php echo make_link('list', $dir ) ?>';" />
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
	</table>
	</form>
	<?php	
	}
}
function ftp_logout() {
	unset($_SESSION['ftp_login']);
	unset($_SESSION['ftp_pass']);
	unset($_SESSION['ftp_host']);
	session_write_close();
	mosRedirect('index2.php?option=com_joomlaxplorer&file_mode=file');
}
