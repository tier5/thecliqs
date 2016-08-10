<?php

class Yncontest_Widget_StatictisContestController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $statistic = Yncontest_Api_Statistic::getInstance();
        $viewer = Engine_Api::_()->user()->getViewer();
        $id = $viewer->getIdentity();

        $this->view->totalContests = $statistic->getContestOwner($id);
        $this->view->totalPaticipants = $statistic->getMemberOwner($id);
        $this->view->totalEntries = $statistic->getEntriesOwner($id);
        $this->view->totalViews = $statistic->getViewOwner($id);
        $this->view->totalLikes = $statistic->getLikeOwner($id);

    }
}