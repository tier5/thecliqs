<?php
class Ynbusinesspages_Widget_BusinessSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
		$view = Zend_Registry::get('Zend_View');
        $this->view->form = $form = new Ynbusinesspages_Form_Search(array(
            'type' => 'ynbusinesspages_business'
        ));
        $categories = Engine_Api::_() -> getItemTable('ynbusinesspages_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1).$view->translate($category['title']));
            }
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynbusinesspages') {
            if ($controller == 'index' && (in_array($action, array('claim', 'manage', 'listing', 'manage-claim', 'manage-favourite', 'manage-follow')))) {
                $forwardListing = false;
            }
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'listing'), 'ynbusinesspages_general', true));
        }
		
		$arr_status_claim = array(
			'all'       => $view -> translate('All'),
			'0' => $view -> translate('Verified'), 
			'1' => $view -> translate('UnClaimed'), 
		);
		$form -> status_claimed -> addMultiOptions($arr_status_claim);
		
		if(in_array($action, array('manage', 'manage-claim', 'manage-favourite', 'manage-follow')))
		{
			$form -> removeElement('lat');
			$form -> removeElement('long');
			$form -> removeElement('location');
			$form -> removeElement('within');
			$form -> removeElement('category');
			
			switch ($action) {
				case 'manage-claim':
					$arr_status = array(
						'all'       => $view -> translate('All'),
						'unclaimed' => $view -> translate('Unclaimed'), 
						'claimed' => $view -> translate('Claimed'), 
					);
					$form -> status -> addMultiOptions($arr_status);
					$form -> removeElement('status_claimed');
					break;
				case 'manage':
					$arr_status = array(
						'all'       => $view -> translate('All'),
						'draft' => $view -> translate('Draft'), 
						'pending' => $view -> translate('Pending'), 
						'published' => $view -> translate('Published'), 
						'closed' => $view -> translate('Closed'), 
						'denied' => $view -> translate('Denied'), 
					);
					$form -> status -> addMultiOptions($arr_status);
					$form -> removeElement('status_claimed');
					break;	
				default:
					$form -> removeElement('status');
					$form -> removeElement('status_claimed');
					break;
			}
		}
		else
		{
			if(in_array($action, array('index', 'listing')))
			{
				$form -> removeElement('status');
			}
			else
			{
				$form -> removeElement('status');
				$form -> removeElement('status_claimed');
			}
		}
		
        // Process form
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if ($form->isValid($p)) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = $values;
        $this->view->topLevelId = $form->getTopLevelId();
        $this->view->topLevelValue = $form->getTopLevelValue();
	}
}