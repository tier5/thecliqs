<?php

class Yncredit_FaqController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
  	if( !$this->_helper->requireAuth()->setAuthParams('yncredit', null, 'use_credit')->isValid() ) return;
  	if( !$this->_helper->requireAuth()->setAuthParams('yncredit', null, 'faq')->isValid() ) return;
     // Landing page mode
	$this->_helper->content->setEnabled();
	$table = Engine_Api::_()->getDbTable('faqs', 'yncredit');
	$select = $table->select() -> where("status = 'show'");
	$select->order('ordering asc');
	$paginator = $this->view->paginator = Zend_Paginator::factory($select);
	$paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }
}
