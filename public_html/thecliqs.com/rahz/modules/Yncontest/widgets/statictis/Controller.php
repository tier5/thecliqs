<?php
class Yncontest_Widget_StatictisController extends Engine_Content_Widget_Abstract
{
		public function indexAction(){
		   $statistic = Yncontest_Api_Statistic::getInstance();
			$this->view->totalContests = $statistic->getTotalContest();
			$this->view->contestAlbum = $statistic->getContestAlbum();
			$this->view->contestVideo = $statistic->getContestVideo();
			$this->view->contestBlog = $statistic->getContestBlog();
			$this->view->totalWinner = $statistic->getTotalWinner();
		}
} 