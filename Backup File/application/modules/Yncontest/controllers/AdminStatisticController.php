<?php

class Yncontest_AdminStatisticController extends Core_Controller_Action_Admin{
	
	public function init() {
		Zend_Registry::set('admin_active_menu', 'yncontest_admin_main_statistic');
	}
	public function indexAction(){
		$statistic = Yncontest_Api_Statistic::getInstance();
		$this->view->totalContests = $statistic->getTotalContest();
		$this->view->approveContest = $statistic->getApprovedContest();
		$this->view->featuredContest = $statistic->getFeaturedContest();
		$this->view->premiumContest = $statistic->getPremiumContest();
		$this->view->endingsoonContest = $statistic->getEndingSoonContest();
		$this->view->followContest = $statistic->getFollowContest();
		$this->view->favoriteContest = $statistic->getFavoriteContest();
		$this->view->totalEntries = $statistic->getTotalEntries();
		$this->view->followEntries = $statistic->getFollowEntries();
		$this->view->favoriteEntries = $statistic->getFavoriteEntries();
		$this->view->publishedFee = $statistic->getPublishFee();
		$this->view->featuredFee = $statistic->getFeaturedFee();
		$this->view->premiumFee = $statistic->getPremiumFee();
		$this->view->endingSoonFee= $statistic->getEndingSoonFee();
	}
}
