<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_JLINDEX') or die();

/*
interface bossUI {

	// All Screens
	public function displayPathway();
	public function displaySubCats();
	
	public function displayItemsShortList();						
	public function displayShortListing();
		
	public function displayWriteLink();
	public function displayAllContentsLink();
	public function displayProfileLink();
	public function displayUserContentsLink();
	public function displayRulesLink();
	
	// Content Form + Content Display Screen
	public function displayCaptchaImage();
	public function displayCaptchaInput(); 
	
	public function displayErrorMsg(); 
	
	// Content Form  Screen
	public function displayWarningNoAccount();
	public function isContentCaptchaActivated();
	
	public function displayCategoriesSelectSearch(); 
	public function displayCategoriesSelect(); 
	public function isCategorySelected();
	
	public function displayFormFields($mode = "write");
	public function displayFormType(); 
	public function displayFormBegin($mode = "write");
	public function displayFormEnd($mode = "write");
	
	public function isAccountCreation(); 
	public function displayAccountCreationFields();
	
	// Contents List + Content Display  Screen
	public function displayContentTitle($content,$link = true);
	
	public function isRatingAllowed();
	public function displayNumVotes($content);
	public function displayVoteResult($content,$prefix = "");
	
	public function isReviewAllowed();
	public function displayNumReviews($content);
	
	public function displayContentEditDelete($content);
	public function displayContentHits($content);
	
	public function displayCategoryImage($content);
	public function displayCategoryTitle($content,$type);
	
	public function countFieldsInGroup($groupname); 
	public function loadFieldsInGroup($content,$groupname,$betweenfields=null,$fieldheader=null,$fieldfooter=null,$private=0); 
	
	// Contents List Screen
	public function displayAdvancedSearchLink();
	public function displaySearch();

	public function displayContentLinkMore($content,$text = null);
	
	public function displayCatImage(); 
	public function displayCatTitle(); 
	public function displayCatDescription(); 
	
	public function displayOrderOption();
	
	public function displayPagesCounter();
	public function displayPagesLinks();
	
	public function displayContents();
	
	// Content Display Screen
	public function displayReviewTitle($review);
	public function displayReviewContent($review);
	public function displayReviewUser($review);
	public function displayReviewDate($review);
	public function isReviewCaptchaActivated(); 
	public function displayReviews(); 
	public function displayAddReview($content);
	public function displayNumReviews($content);
	
	public function displayVoteForm($content);
	public function displayVoteResult($content,$prefix = "");
	
	public function PdfIcon( &$row, &$params, $hide_js ); {
	public function PrintIcon($content,$text=null);
	public function EmailIcon( $content,$text=null ); 
	
	public function displayDeleteShortList($content,$text =null);
	public function displayShortList($content,$add = null,$shortlisted = null);
	
	public function displayShowOther($content);
	public function displayPms($content,$private = 0);
	public function displayContent($content,$unique = 1);
	
	//Login Screen
	public function displayLostPasswordLink();
	public function displayCreateAccountLink();
	
	// Profile Screen
	public function displayProfileField($name); 
	public function displayCustomProfileFields(); 
	
	// Rules
	function displayRulesText();
	
	// Front
	public function displayCategories();
	public function displayLastContents();	
	public function displayFrontText();
}
*/