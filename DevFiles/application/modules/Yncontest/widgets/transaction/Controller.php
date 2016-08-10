<?php
class Yncontest_Widget_TransactionController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		
		$view = Zend_Registry::get('Zend_View');

		$headScript = new Zend_View_Helper_HeadScript();
		$headLink = new Zend_View_Helper_HeadLink();
		
		$headScript -> appendFile($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/jquery-1.8.3.js');
		$headScript -> appendFile($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/jquery-ui.js');
		$headLink -> appendStylesheet($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/styles/jquery-ui.css');
		
		
		$request =  Zend_Controller_Front::getInstance()->getRequest();
		
		$page = $request->getParam('page',1);
		
		$this->view->form = $form = new Yncontest_Form_Transaction_Search();
		$values = array();  
		
		if ($form->isValid($this->_getAllParams())) {
			$values = $form->getValues();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if($viewer->getIdentity())
			$values['user_id'] = $viewer->getIdentity();
		if($request->getParam('contest_name')!="")
			$values['contest_name'] = $request->getParam('contest_name');
		if($request->getParam('from')!= "")
			$values['from'] = $request->getParam("from");
		if($request->getParam('to')!= "")
			$values['to'] = $request->getParam("to");
		if($request->getParam('order')!= "")
			$values['order'] = $request->getParam("order");
		if($request->getParam('direction')!= "")
			$values['direction'] = $request->getParam("direction");
		
		$form->populate($values);
		
		$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 10);
		//$values['limit'] = $limit;
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer = $viewer;
		$this->view->paginator = Yncontest_Api_Transaction::getInstance()->getTransactionsPaginator($values); 
		$this->view->paginator->setItemCountPerPage($limit);
		$this->view->paginator->setCurrentPageNumber($page);
		$this->view->formValues = $values; 
	}
}