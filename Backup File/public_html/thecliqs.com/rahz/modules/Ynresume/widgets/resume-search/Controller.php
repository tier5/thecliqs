<?php
class Ynresume_Widget_ResumeSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
		$view = Zend_Registry::get('Zend_View');
        $this->view->form = $form = new Ynresume_Form_Search();
        $industries = Engine_Api::_() -> getDbTable('industries', 'ynresume') -> getIndustries();
        unset($industries[0]);
        if (count($industries) > 0) {
            foreach ($industries as $industry) {
                $form->industry_id->addMultiOption($industry['industry_id'], str_repeat("-- ", $industry['level'] - 1).$industry['title']);
            }
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynresume') {
            if ($controller == 'index' && (in_array($action, array('listing' ,'my-saved', 'my-favourite')))) {
                $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => $action), 'ynresume_general', true));	
                $forwardListing = false;
            }
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'listing'), 'ynresume_general', true));
        }
		
        // Process form
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if(!isset($params['within']) || empty($params['within'])) {
            $params['within'] = 50;
        }
        $form->populate($params);
        $values = $values = $form->getValues();
        $this->view->formValues = $values;
	}
}
