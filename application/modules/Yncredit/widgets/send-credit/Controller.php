<?php
class Yncredit_Widget_SendCreditController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        $this -> view -> form = new Yncredit_Form_SendCredits();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$balance = Engine_Api::_()->getItem('yncredit_balance', $viewer -> getIdentity());
		if (!Engine_Api::_() -> authorization() -> isAllowed('yncredit', $viewer, 'send') || !$balance) 
		{
			$this -> setNoRender();
		}
    }
}