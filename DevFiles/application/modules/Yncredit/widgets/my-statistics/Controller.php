<?php
class Yncredit_Widget_MyStatisticsController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        $viewer = Engine_Api::_()->user()->getViewer();
		if(!$viewer -> getIdentity())
		{
			$this -> setNoRender();
		}
		// get my balance
		$this -> view -> balance = $balance = Engine_Api::_()->getItem('yncredit_balance', $viewer->getIdentity());
		if(!$balance)
		{
			$this -> setNoRender();
		}
    }
}