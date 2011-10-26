<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

// load the html drawing class
require_once ($mainframe->getPath('front_html'));
require_once ($mainframe->getPath('class'));

$mainframe->setPageTitle(_CONTACT_TITLE);

//Load Vars
$op = strval(mosGetParam($_REQUEST,'op',''));
$con_id = intval(mosGetParam($_REQUEST,'con_id',0));
$contact_id = intval(mosGetParam($_REQUEST,'contact_id',0));
$catid = intval(mosGetParam($_REQUEST,'catid',0));

switch($op) {
	case 'sendmail':
		sendmail($con_id,$option);
		break;
}

switch($task) {
	case 'view':
		contactpage($contact_id);
		break;

	case 'vcard':
		vCard($contact_id);
		break;

	default:
		listContacts($option,$catid);
		break;
}


function listContacts($option,$catid) {
	global $my,$Itemid;

	$mainframe = mosMainFrame::getInstance();
	$config = &$mainframe->config;
	$database = $mainframe->getDBO();

	/* Query to retrieve all categories that belong under the contacts section and that are published.*/
	$query = "SELECT*, COUNT( a.id ) AS numlinks FROM #__categories AS cc"
			."\n LEFT JOIN #__contact_details AS a ON a.catid = cc.id"
			."\n WHERE a.published = 1 AND cc.section = 'com_contact_details'"
			."\n AND cc.published = 1"
			."\n AND a.access <= "
			.(int)$my->gid."\n AND cc.access <= ".(int)$my->gid
			."\n GROUP BY cc.id"
			."\n ORDER BY cc.ordering";
	$database->setQuery($query);
	$categories = $database->loadObjectList();

	$count = count($categories);

	if(($count < 2) && (@$categories[0]->numlinks == 1)) {
		// if only one record exists loads that record, instead of displying category list
		contactpage($option,0);
	} else {
		$rows = array();
		$currentcat = null;

		// Parameters
		$menu = $mainframe->get('menu');
		$params = new mosParameters($menu->params);

		if($params->get('header') == "") {
			$mainframe->SetPageTitle($menu->name,$params);
		} else {
			$mainframe->SetPageTitle($params->get('header'),$params);
		}
		if($params->get('robots') == 0) {
			$mainframe->addMetaTag('robots','index, follow');
		}
		if($params->get('robots') == 1) {
			$mainframe->addMetaTag('robots','index, nofollow');
		}
		if($params->get('robots') == 2) {
			$mainframe->addMetaTag('robots','noindex, follow');
		}
		if($params->get('robots') == 3) {
			$mainframe->addMetaTag('robots','noindex, nofollow');
		}
		if($params->get('meta_description') != "") {
			$mainframe->addMetaTag('description',$params->get('meta_description'));
		} else {
			$mainframe->addMetaTag('description',$config->config_MetaDesc);
		}
		if($params->get('meta_keywords') != "") {
			$mainframe->addMetaTag('keywords',$params->get('meta_keywords'));
		} else {
			$mainframe->addMetaTag('keywords',$config->config_MetaKeys);
		}
		if($params->get('meta_author') != "") {
			$mainframe->addMetaTag('author',$params->get('meta_author'));
		}

		//$params->def('page_title',1);
		$params->def('header',$menu->name);
		$params->def('pageclass_sfx','');
		$params->def('headings',1);
		$params->def('back_button',$mainframe->getCfg('back_button'));
		$params->def('description_text',_CONTACTS_DESC);
		$params->def('image',-1);
		$params->def('image_align','right');
		$params->def('other_cat_section',1);
		// Category List Display control
		$params->def('other_cat',1);
		$params->def('cat_description',1);
		$params->def('cat_items',1);
		// Table Display control
		$params->def('headings',1);
		$params->def('position',1);
		$params->def('email',0);
		$params->def('phone',1);
		$params->def('fax',1);
		$params->def('telephone',1);

		if($catid == 0) {
			$catid = $params->get('catid',0);
		}

		if($catid) {
			$params->set('type','category');
		} else {
			$params->set('type','section');
		}

		if($catid) {
			// url links info for category
			$query = "SELECT* FROM #__contact_details WHERE catid = ".(int)$catid." AND published =1 AND access <= ".(int)$my->gid." ORDER BY ordering";
			$database->setQuery($query);
			$rows = $database->loadObjectList();

			// current category info
			$query = "SELECT id, name, description, image, image_position FROM #__categories WHERE id = ".(int)$catid." AND published = 1 AND access <= ".(int)$my->gid;
			$database->setQuery($query);
			$database->loadObject($currentcat);

			/*
			* Check if the category is published or if access level allows access
			*/
			if(!$currentcat->name) {
				mosNotAuth();
				return;
			}
		}

		// page description
		$currentcat->descrip = '';
		if(isset($currentcat->description) && ($currentcat->description != '')) {
			$currentcat->descrip = $currentcat->description;
		} else
		if(!$catid) {
			// show description
			if($params->get('description')) {
				$currentcat->descrip = $params->get('description_text');
			}
		}

		// page image
		$currentcat->img = '';
		$path = JPATH_SITE.'/images/stories/';
		if(isset($currentcat->image) && ($currentcat->image != '')) {
			$currentcat->img = $path.$currentcat->image;
			$currentcat->align = $currentcat->image_position;
		} else
		if(!$catid) {
			if($params->get('image') != -1) {
				$currentcat->img = $path.$params->get('image');
				$currentcat->align = $params->get('image_align');
			}
		}

		// page header
		$currentcat->header = '';
		if(isset($currentcat->name) && ($currentcat->name != '')) {
			$currentcat->header = $params->get('header').' - '.$currentcat->name;
		} else {
			$currentcat->header = $params->get('header');
		}

		// used to show table rows in alternating colours
		$tabclass = array('sectiontableentry1','sectiontableentry2');

		HTML_contact::displaylist($categories,$rows,$catid,$currentcat,$params,$tabclass);
	}
}


function contactpage($contact_id) {
	global $my,$Itemid;

	$mainframe = mosMainFrame::getInstance();
	$config = &$mainframe->config;
	$database = $mainframe->getDBO();

	$query = "SELECT a.id AS value, CONCAT_WS( ' - ', a.name, a.con_position ) AS text, a.catid, cc.access AS cat_access"
			."\n FROM #__contact_details AS a"
			."\n LEFT JOIN #__categories AS cc ON cc.id = a.catid WHERE a.published = 1"
			."\n AND cc.published = 1"
			."\n AND a.access <= "
			.(int)$my->gid
			."\n ORDER BY a.default_con DESC, a.ordering ASC";
	$database->setQuery($query);
	$checks = $database->loadObjectList();

	$count = count($checks);
	if($count) {
		if($contact_id < 1) {
			$contact_id = $checks[0]->value;
		}

		$query = "SELECT a.*, cc.access AS cat_access"
				."\n FROM #__contact_details AS a"
				."\n LEFT JOIN #__categories AS cc ON cc.id = a.catid"
				."\n WHERE a.published = 1"
				."\n AND a.id = ".(int)$contact_id
				."\n AND a.access <= ".(int)$my->gid;
		$database->SetQuery($query);
		$contacts = $database->LoadObjectList();

		if(!$contacts) {
			echo _NOT_AUTH;
			return;
		}
		$contact = $contacts[0];

		/*
		* check whether category access level allows access
		*/
		if($contact->cat_access > $my->gid) {
			mosNotAuth();
			return;
		}

		$list = array();
		foreach($checks as $check) {
			if($check->catid == $contact->catid) {
				$list[] = $check;
			}
		}
		// creates dropdown select list
		$contact->select = mosHTML::selectList($list,'contact_id','class="inputbox" onchange="ViewCrossReference(this);"','value','text',$contact_id);

		$menu = $mainframe->get('menu');
		$params = new mosParameters($menu->params);

		if($params->get('header') == "") {
			$mainframe->SetPageTitle($menu->name,$params);
		} else {
			$mainframe->SetPageTitle($params->get('header'),$params);
		}
		if($params->get('robots') == 0) {
			$mainframe->addMetaTag('robots','index, follow');
		}
		if($params->get('robots') == 1) {
			$mainframe->addMetaTag('robots','index, nofollow');
		}
		if($params->get('robots') == 2) {
			$mainframe->addMetaTag('robots','noindex, follow');
		}
		if($params->get('robots') == 3) {
			$mainframe->addMetaTag('robots','noindex, nofollow');
		}
		if($params->get('meta_description') != "") {
			$mainframe->addMetaTag('description',$params->get('meta_description'));
		} else {
			$mainframe->addMetaTag('description',$config->config_MetaDesc);
		}
		if($params->get('meta_keywords') != "") {
			$mainframe->addMetaTag('keywords',$params->get('meta_keywords'));
		} else {
			$mainframe->addMetaTag('keywords',$config->config_MetaKeys);
		}
		if($params->get('meta_author') != "") {
			$mainframe->addMetaTag('author',$params->get('meta_author'));
		}

		// Adds parameter handling
		$params = new mosParameters($contact->params);

		$params->set('page_title',0);
		$params->def('pageclass_sfx','');
		$params->def('back_button',$mainframe->getCfg('back_button'));
		$params->def('print',$mainframe->getCfg('showPrint'));
		$params->def('name',1);
		$params->def('email',0);
		$params->def('street_address',1);
		$params->def('suburb',1);
		$params->def('state',1);
		$params->def('country',1);
		$params->def('postcode',1);
		$params->def('telephone',1);
		$params->def('fax',1);
		$params->def('misc',1);
		$params->def('image',1);
		$params->def('email_description',1);
		$params->def('email_description_text',_EMAIL_DESCRIPTION);
		$params->def('email_form',1);
		$params->def('email_copy',0);
		// global pront|email
		$params->def('icons',$mainframe->getCfg('icons'));
		// contact only icons
		$params->def('contact_icons',0);
		$params->def('icon_address','');
		$params->def('icon_email','');
		$params->def('icon_telephone','');
		$params->def('icon_fax','');
		$params->def('icon_misc','');
		$params->def('drop_down',0);
		$params->def('vcard',0);


		if($contact->email_to && $params->get('email')) {
			// email cloacking
			$contact->email = mosHTML::emailCloaking($contact->email_to);
		}

		// loads current template for the pop-up window
		$pop = intval(mosGetParam($_REQUEST,'pop',0));
		if($pop) {
			$params->set('popup',1);
			$params->set('back_button',0);
		}

		if($params->get('email_description')) {
			$params->set('email_description',$params->get('email_description_text'));
		} else {
			$params->set('email_description','');
		}

		// needed to control the display of the Address marker
		$temp = $params->get('street_address').$params->get('suburb').$params->get('state').
				$params->get('country').$params->get('postcode');
		$params->set('address_check',$temp);

		// determines whether to use Text, Images or nothing to highlight the different info groups
		switch($params->get('contact_icons')) {
			case 1:
			// text
				$params->set('marker_address',_CONTACT_ADDRESS);
				$params->set('marker_email',_EMAIL);
				$params->set('marker_telephone',_CONTACT_TELEPHONE);
				$params->set('marker_fax',_CONTACT_FAX);
				$params->set('marker_misc',_CONTACT_MISC);
				$params->set('column_width','100');
				break;
			case 2:
			// none
				$params->set('marker_address','');
				$params->set('marker_email','');
				$params->set('marker_telephone','');
				$params->set('marker_fax','');
				$params->set('marker_misc','');
				$params->set('column_width','0');
				break;
			default:
			// icons
				$image1 = mosAdminMenus::ImageCheck('con_address.png','/images/M_images/',$params->get('icon_address'),'/images/M_images/',_CONTACT_ADDRESS,'adress');
				$image2 = mosAdminMenus::ImageCheck('emailButton.png','/images/M_images/',$params->get('icon_email'),'/images/M_images/',_EMAIL,'email');
				$image3 = mosAdminMenus::ImageCheck('con_tel.png','/images/M_images/',$params->get('icon_telephone'),'/images/M_images/',_CONTACT_TELEPHONE,'phone');
				$image4 = mosAdminMenus::ImageCheck('con_fax.png','/images/M_images/',$params->get('icon_fax'),'/images/M_images/',_CONTACT_FAX,'fax');
				$image5 = mosAdminMenus::ImageCheck('con_info.png','/images/M_images/',$params->get('icon_misc'),'/images/M_images/',_CONTACT_MISC,'more');
				$params->set('marker_address',$image1);
				$params->set('marker_email',$image2);
				$params->set('marker_telephone',$image3);
				$params->set('marker_fax',$image4);
				$params->set('marker_misc',$image5);
				$params->set('column_width','40');
				break;
		}

		// params from menu item
		$menu = $mainframe->get('menu');
		$menu_params = new mosParameters($menu->params);

		$menu_params->def('page_title',1);
		$menu_params->def('header',$menu->name);
		$menu_params->def('pageclass_sfx','');

		HTML_contact::viewcontact($contact,$params,$count,$list,$menu_params);
	} else {
		$params = new mosParameters('');
		$params->def('back_button',$mainframe->getCfg('back_button'));
		HTML_contact::nocontact($params);
	}
}


function sendmail($con_id,$option) {
	global $Itemid;

	// simple spoof check security
	josSpoofCheck(1);

	$mainframe = mosMainFrame::getInstance();
	$config = &$mainframe->config;
	$database = $mainframe->getDBO();

	$query = "SELECT* FROM #__contact_details WHERE id = ".(int)$con_id;
	$database->setQuery($query);
	$contact = $database->loadObjectList();

	if(count($contact) > 0) {
		$default = $config->config_sitename.' '._ENQUIRY;
		$email = strval(mosGetParam($_POST,'email',''));
		$text = strval(mosGetParam($_POST,'text',''));
		$name = strval(mosGetParam($_POST,'name',''));
		$subject = strval(mosGetParam($_POST,'subject',$default));
		$email_copy = strval(mosGetParam($_POST,'email_copy',0));

		$menu = $mainframe->get('menu');
		$mparams = new mosParameters($menu->params);
		$bannedEmail = $mparams->get('bannedEmail','');
		$bannedSubject = $mparams->get('bannedSubject','');
		$bannedText = $mparams->get('bannedText','');
		$sessionCheck = $mparams->get('sessionCheck',1);

		if($config->config_captcha_cont) {
			session_name(mosMainFrame::sessionCookieName());
			session_start();
			$captcha = strval(mosGetParam($_POST, 'captcha', null));
			$captcha_keystring =mosGetParam($_SESSION,'captcha_keystring');
			if($captcha_keystring!== $captcha) {
				$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
				mosRedirect($link,_BAD_CAPTCHA_STRING);
				unset($_SESSION['captcha_keystring']);
				exit;
			}
			session_unset();
			session_write_close();
		}

		// check for session cookie

		if(!$config->config_no_session_front && $sessionCheck) {
			// Session Cookie `name`
			$sessionCookieName = mosMainFrame::sessionCookieName();
			// Get Session Cookie `value`
			$sessioncookie = mosGetParam($_COOKIE,$sessionCookieName,null);

			if(!(strlen($sessioncookie) == 32 || $sessioncookie == '-')) {
				$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
				mosRedirect($link,_NOT_AUTH);
			}
		}

		// Prevent form submission if one of the banned text is discovered in the email field
		if($bannedEmail) {
			$bannedEmail = explode(';',$bannedEmail);
			foreach($bannedEmail as $value) {
				if(stristr($email,$value)) {
					$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
					mosRedirect($link,_NOT_AUTH);
				}
			}
		}
		// Prevent form submission if one of the banned text is discovered in the subject field
		if($bannedSubject) {
			$bannedSubject = explode(';',$bannedSubject);
			foreach($bannedSubject as $value) {
				if(stristr($subject,$value)) {
					$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
					mosRedirect($link,_NOT_AUTH);
				}
			}
		}
		// Prevent form submission if one of the banned text is discovered in the text field
		if($bannedText) {
			$bannedText = explode(';',$bannedText);
			foreach($bannedText as $value) {
				if(stristr($text,$value)) {
					$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
					mosRedirect($link,_NOT_AUTH);
				}
			}
		}

		// test to ensure that only one email address is entered
		$check = explode('@',$email);
		if(strpos($email,';') || strpos($email,',') || strpos($email,' ') || count($check) >2) {
			$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
			mosRedirect($link,_CONTACT_MORE_THAN);
		}

		if(!$email || !$text || (JosIsValidEmail($email) == false)) {
			$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
			mosRedirect($link,_CONTACT_FORM_NC);
		}
		$prefix = sprintf(_ENQUIRY_TEXT,JPATH_SITE);
		$text = $prefix."\n".$name.' <'.$email.'>'."\n\n".stripslashes($text);

		$success = mosMail($email,$name,$contact[0]->email_to,$config->config_fromname.': '.$subject,$text);
		if(!$success) {
			$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
			mosRedirect($link,_FAILID_MESSAGE);
		}

		// parameter check
		$params = new mosParameters($contact[0]->params);
		$emailcopyCheck = $params->get('email_copy',0);

		// check whether email copy function activated
		if($email_copy && $emailcopyCheck) {
			$copy_text = sprintf(_COPY_TEXT,$contact[0]->name,$config->config_sitename);
			$copy_text = $copy_text."\n\n".$text.'';
			$copy_subject = _COPY_SUBJECT.$subject;

			$success = mosMail($config->config_mailfrom,$config->config_fromname,$email,$copy_subject,$copy_text);
			if(!$success) {
				$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);
				mosRedirect($link,_CONTACT_FORM_NC);
			}
		}

		$link = sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$contact[0]->id.'&Itemid='.$Itemid);

		mosRedirect($link,_THANK_MESSAGE);
	}
}

function vCard($id) {
	$database = database::getInstance();
	$config = Jconfig::getInstance();

	$contact = new mosContact($database);
	$contact->load((int)$id);
	$params = new mosParameters($contact->params);

	$show = $params->get('vcard',0);
	if($show) {
		// check to see if VCard option hsa been activated
		$name = explode(' ',$contact->name);
		$count = count($name);

		// handles conversion of name entry into firstname, surname, middlename distinction
		$surname = '';
		$middlename = '';

		switch($count) {
			case 1:
				$firstname = $name[0];
				break;

			case 2:
				$firstname = $name[0];
				$surname = $name[1];
				break;

			default:
				$firstname = $name[0];
				$surname = $name[$count - 1];
				for($i = 1; $i < $count - 1; $i++) {
					$middlename .= $name[$i].' ';
				}
				break;
		}
		$middlename = trim($middlename);

		$v = new MambovCard();

		$v->setPhoneNumber($contact->telephone,'PREF;WORK;VOICE');
		$v->setPhoneNumber($contact->fax,'WORK;FAX');
		$v->setName($surname,$firstname,$middlename,'');
		$v->setAddress('','',$contact->address,$contact->suburb,$contact->state,$contact->postcode,$contact->country,'WORK;POSTAL');
		$v->setEmail($contact->email_to);
		$v->setNote($contact->misc);
		$v->setURL(JPATH_SITE,'WORK');
		$v->setTitle($contact->con_position);
		$v->setOrg($config->config_sitename);

		$filename = str_replace(' ','_',$contact->name);
		$v->setFilename($filename);

		$output = $v->getVCard($config->config_sitename);
		$filename = $v->getFileName();

		// header info for page
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-Length: '.strlen($output));
		header('Connection: close');
		header('Content-Type: text/x-vCard; name='.$filename);
		header('Cache-Control: store, cache');
		header('Pragma: cache');

		echo $output;
	} else {
		mosNotAuth();
		return;
	}
}