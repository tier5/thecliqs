<?php

class Yncredit_Widget_BuyCreditController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        $packages = Engine_Api::_() -> getDbTable("packages", 'yncredit') -> getPackages(true);
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!$viewer -> getIdentity() || !count($packages))
		{
			$this -> setNoRender();
		}
		$this -> view -> form = new Yncredit_Form_BuyCredits();
    }
}