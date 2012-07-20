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

/**
 * @package Joostina
 * @subpackage Users
 */
class loginHTML{

	function loginpage($params, $image){
		global $mosConfig_lang;

		// used for spoof hardening
		$validate = josSpoofValue(1);

		$return = $params->get('login');
		?>
	<form action="<?php echo JSef::getUrlToSef('index.php?option=login'); ?>" method="post" name="login" id="login">
		<table width="100%" border="0" align="center" cellpadding="4" cellspacing="0" class="contentpane<?php echo
		$params->get('pageclass_sfx'); ?>">
			<tr>
				<td colspan="2">
					<?php
					if($params->get('page_title')){
						?>
						<div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
							<?php echo $params->get('header_login'); ?>
						</div>
						<?php
					}
					?>
					<div>
						<?php echo $image; ?>
						<?php
						if($params->get('description_login')){
							?>
							<?php echo $params->get('description_login_text'); ?>
							<br/><br/>
							<?php
						}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" width="50%">
					<br/>
					<table>
						<tr>
							<td align="center">
								<?php echo _USER; ?>
								<br/>
							</td>
							<td align="center">
								<?php echo _PASSWORDWORD; ?>
								<br/>
							</td>
						</tr>
						<tr>
							<td align="center">
								<input name="username" type="text" class="inputbox" size="20"/>
							</td>
							<td align="center">
								<input name="passwd" type="password" class="inputbox" size="20"/>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="2">
								<br/>
								<?php echo _REMEMBER_ME; ?>
								<input type="checkbox" name="remember" class="inputbox" value="yes"/>
								<br/>
								<a href="<?php echo JSef::getUrlToSef('index.php?option=com_users&amp;task=lostPassword'); ?>">
									<?php echo _LOST_PASSWORDWORD; ?>
								</a>
								<?php
								if($params->get('registration')){
									?>
									<br/>
									<?php echo _NO_ACCOUNT; ?>
									<a href="<?php echo JSef::getUrlToSef('index.php?option=com_users&amp;task=register'); ?>">
										<?php echo _CREATE_ACCOUNT; ?>
									</a>
									<?php
								}
								?>
								<br/><br/><br/>
							</td>
						</tr>
					</table>
				</td>
				<td>
					<div align="center">
						<input type="submit" name="submit" class="button" value="<?php echo
						_BUTTON_LOGIN; ?>"/>
					</div>

				</td>
			</tr>
			<tr>
				<td colspan="2">
					<noscript>
						<?php echo _JAVASCRIPT; ?>
					</noscript>
				</td>
			</tr>
		</table>
		<?php
		// displays back button
		mosHTML::BackButton($params);
		?>

		<input type="hidden" name="op2" value="login"/>
		<input type="hidden" name="return" value="<?php echo JSef::getUrlToSef($return); ?>"/>
		<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>"/>
		<input type="hidden" name="message" value="<?php echo $params->get('login_message'); ?>"/>
		<input type="hidden" name="<?php echo $validate; ?>" value="1"/>
	</form>
	<?php
	}

	function logoutpage($params, $image){
		global $mosConfig_lang;

		$return = $params->get('logout');
		?>
	<form action="<?php echo JSef::getUrlToSef('index.php?option=logout'); ?>" method="post" name="login" id="login">
		<table width="100%" border="0" align="center" cellpadding="4" cellspacing="0" class="contentpane<?php echo
		$params->get('pageclass_sfx'); ?>">
			<tr>
				<td valign="top">
					<?php
					if($params->get('page_title')){
						?>
						<div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
							<?php echo $params->get('header_logout'); ?>
						</div>
						<?php
					}
					?>
					<div>
						<?php
						echo $image;

						if($params->get('description_logout')){
							echo $params->get('description_logout_text');
							?>
							<br/><br/>
							<?php
						}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center">
					<div align="center">
						<input type="submit" name="Submit" class="button" value="<?php echo
						_BUTTON_LOGOUT; ?>"/>
					</div>
				</td>
			</tr>
		</table>
		<?php mosHTML::BackButton($params); ?>
		<input type="hidden" name="op2" value="logout"/>
		<input type="hidden" name="return" value="<?php echo JSef::getUrlToSef($return); ?>"/>
		<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>"/>
		<input type="hidden" name="message" value="<?php echo $params->get('logout_message'); ?>"/>
	</form>
	<?php
	}
}