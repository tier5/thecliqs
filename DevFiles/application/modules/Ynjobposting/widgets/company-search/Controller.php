<?php
class Ynjobposting_Widget_CompanySearchController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  	{
		$viewer = Engine_Api::_()->user()->getViewer();
	    $this->view->form = $form = new Ynjobposting_Form_Company_Search();
	    $request = Zend_Controller_Front::getInstance() -> getRequest();
	    $params = $request->getParams();
	    $form->populate($params);
    }
}