<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

class defaultComment{
	private function displayReviewTitle($review){
		echo $review->title;
	}

	private function displayReviewContent($review){
		echo $review->description;
	}

	private function displayReviewUser($review){
		echo $review->user;
	}

	private function displayReviewDate($review){
		echo $review->date;
	}

	private function isContentCaptchaActivated($conf){
		return $conf->secure_new_content;
	}

	private function isReviewCaptchaActivated($conf){
		return $conf->secure_comment;
	}

	public function displayReviews($content, $directory, $conf, $reviews){

		if(($conf->allow_comments == 1) && (isset($reviews))){
			foreach($reviews as $review){
				include(JPATH_BASE . '/images/boss/' . $directory . '/plugins/comments/defaultComment/template/review.php');
			}
		}
	}

	private function displayCaptchaImage(){
		?>
	<img id="captchaimg" alt="<?php echo _PRESS_HERE_TO_RELOAD_CAPTCHA?>"
		 onclick="document.saveForm.captchaimg.src='<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>&' + new String(Math.random())"
		 src="<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>"/>
	<?php

	}

	private function displayCaptchaInput(){
		echo '<input class="boss_required" moslabel="' . BOSS_SECURITY . '" type="text" name="captcha" id="captcha" mosreq="1" value="" size="20" />';
	}

	public function displayAddReview($directory, $content, $conf){
		global $my;

		$name = '';
		if($my->id != 0)
			$name = $my->name;

		if($conf->allow_comments == 0){
			return;
		} else if($my->id == 0 && $conf->allow_unregisered_comment == 0){
			$link = sefRelToAbs("index.php?option=com_boss&amp;task=login&amp;directory=$directory");
			echo sprintf(BOSS_REVIEW_LOGIN_REQUIRED, $link);
		} else{
			$target = sefRelToAbs("index.php?option=com_boss&amp;task=save_review&amp;directory=$directory");
			?>
		<form action="<?php echo $target;?>" method="post" name="saveForm">
			<?php include(JPATH_BASE . '/images/boss/' . $directory . '/plugins/comments/defaultComment/template/addreview.php');?>
			<input type="hidden" name="contentid" value="<?php echo $content->id; ?>"/>
		</form>
		<?php
		}
	}

	public function displayNumReviews($content, $reviews, $conf){
		if($conf->allow_comments == 0)
			echo "";
		else{
			if(isset($content->num_reviews))
				$nb = $content->num_reviews;
			else
				$nb = count($reviews);

			echo sprintf(BOSS_NUM_REVIEWS, $nb);
		}

	}

	public function save_review($directory){
		global $my;
		$database = database::getInstance();
		//get configuration
		$conf = getConfig($directory);

		if($conf->allow_comments == 1){
			$row = new jDirectoryReview($database, $directory);
			$catid = (int)mosGetParam($_REQUEST, 'catid', 0);
			$contentid = (int)mosGetParam($_POST, 'contentid', 0);
			// bind it to the table
			if(!$row->bind($_POST)){
				echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}

			if($conf->secure_comment == 1 && $my->id == 0){
				session_name(mosMainFrame::sessionCookieName());
				session_start();
				$captcha = strval(mosGetParam($_POST, 'captcha', null));
				$captcha_keystring = mosGetParam($_SESSION, 'captcha_keystring');
				if($captcha_keystring !== $captcha){
					$link = sefRelToAbs("index.php?option=com_boss&amp;task=show_content&amp;&contentid=" . $row->contentid . "&amp;directory=$directory");
					mosRedirect($link, _BAD_CAPTCHA_STRING);
					unset($_SESSION['captcha_keystring']);
					exit;
				}
				session_unset();
				session_write_close();
			}

			if(($my->id == "0" && $conf->allow_unregisered_comment == 0)){
				mosRedirect(sefRelToAbs("index.php?option=com_boss&amp;task=show_content&amp;contentid=" . $row->contentid . "&amp;directory=$directory&amp;catid=$catid"), "");
				return;
			}

			$row->userid = $my->id;
			$row->published = 1;
			$row->date = date("Y-m-d");

			// store it in the db
			if(!$row->store()){
				echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			} else{
				//переписываем дату последнего комментария в контенте, нужно для сортировки
				$sql = "UPDATE #__boss_" . $directory . "_contents ";
				$sql .= "SET `date_last_сomment` = '" . date("Y-m-d h:i:s") . "' ";
				$sql .= "WHERE `id` = '" . $contentid . "'";
				//echo $sql;
				$database->setQuery($sql)->query();
				if($database->getErrorNum()){
					echo "<script> alert('" . $database->stderr() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}
		mosRedirect(sefRelToAbs("index.php?option=com_boss&amp;task=show_content&amp;&contentid=" . $row->contentid . "&amp;directory=$directory&amp;catid=$catid"), "");
	}

	//функция для вставки таблиц и полей в запрос категории
	public function queryStringList($directory, $conf){
		$query = array();
		if($conf->allow_comments == 1){
			$query['tables'] = " LEFT JOIN #__boss_" . $directory . "_reviews as rev ON a.id = rev.contentid \n";
			$query['fields'] = " count(DISTINCT rev.id) as num_reviews, \n";
			$query['wheres'] = '';
		} else{
			$query['tables'] = '';
			$query['fields'] = '';
			$query['wheres'] = '';
		}
		return $query;
	}

	//функция для вставки таблиц и полей в запрос контента
	public function queryStringContent($directory, $conf, $id){
		$database = database::getInstance();
		$reviews = array();

		if($conf->allow_comments == 1){
			$database->setQuery("SELECT r.*,u.username as user FROM #__boss_" . $directory . "_reviews AS r " .
				"LEFT JOIN #__users as u ON u.id = r.userid " .
				"WHERE r.published = 1 AND r.contentid = " . $id . " ORDER by r.date ASC, r.id ASC");
			$reviews = $database->loadObjectList();
			if($database->getErrorNum()){
				echo $database->stderr();
				return false;
			}
		}
		return $reviews;
	}

	//действия при установке плагина
	public function install($directory){
		$database = database::getInstance();
		$query = "CREATE TABLE IF NOT EXISTS `#__boss_" . $directory . "_reviews` ( " .
			"`id` int(10) unsigned NOT NULL auto_increment, " .
			"`contentid` int(10) unsigned default NULL, " .
			"`userid` int(10) unsigned default NULL, " .
			"`title` varchar(255) CHARACTER SET utf8 default NULL, " .
			"`description`  text CHARACTER SET utf8 default NULL, " .
			"`date` date default NULL, " .
			"`published` tinyint(1) default '1', " .
			"PRIMARY KEY  (`id`) " .
			") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ";
		$database->setQuery($query);
		$database->query();
	}

	public function uninstall($directory){
		$database = database::getInstance();
		$query = "DROP TABLE IF EXISTS `#__boss_" . $directory . "_reviews`";
		$database->setQuery($query);
		$database->query();
	}
}

class jDirectoryReview extends mosDBTable{

	var $id = null;
	var $contentid = null;
	var $userid = null;
	var $title = null;
	var $description = null;
	var $published = null;

	function __construct(&$db, $directory){
		$this->mosDBTable('#__boss_' . $directory . '_reviews', 'id', $db);
	}

}