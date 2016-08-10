<?php
class Ynbusinesspages_Widget_BusinessesListingController extends Engine_Content_Widget_Abstract {
 	public function indexAction() {
 	    $table = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $params = $this -> _getAllParams();
        $mode_list = $mode_grid = $mode_pin = $mode_map = 1;
        $mode_enabled = array();
        $view_mode = 'list';
        
        if(isset($params['mode_list'])) {
            $mode_list = $params['mode_list'];
        }
        if($mode_list) {
            $mode_enabled[] = 'list';
        }
        
        if(isset($params['mode_grid'])) {
            $mode_grid = $params['mode_grid'];
        }
        if($mode_grid) {
            $mode_enabled[] = 'grid';
        }
        if(isset($params['mode_pin']))
        {
            $mode_pin = $params['mode_pin'];
        }
        if($mode_pin)
        {
            $mode_enabled[] = 'pin';
        }
        if(isset($params['mode_map'])) {
            $mode_map = $params['mode_map'];
        }
        if($mode_map) {
            $mode_enabled[] = 'map';
        }
        
        if(isset($params['view_mode'])) {
            $view_mode = $params['view_mode'];
        }
        if($mode_enabled && !in_array($view_mode, $mode_enabled)) {
            $view_mode = $mode_enabled[0];
        }
            
        $this -> view -> mode_enabled = $mode_enabled;
        $class_mode = "ynbusinesspages-business-listing-viewmode-list";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "ynbusinesspages-browse-business-viewmode-grid";
                break;
            case 'pin':
                $class_mode = "ynbusinesspages-browse-business-viewmode-pins";
                break;
            case 'map':
                $class_mode = "ynbusinesspages-browse-business-viewmode-maps";
                break;
            default:
                $class_mode = "ynbusinesspages-browse-business-viewmode-list";
                break;
        }
        
        $this->view->class_mode = $class_mode;
        
        $this->view->form = $form = new Ynbusinesspages_Form_Search(array(
            'type' => 'ynbusinesspages_business'
        ));
        
        $categories = Engine_Api::_() -> getItemTable('ynbusinesspages_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
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
                        'all'       => 'All',
                        'unclaimed' => 'Unclaimed', 
                        'claimed' => 'Claimed', 
                    );
                    $form -> status -> addMultiOptions($arr_status);
                    break;
                case 'manage':
                    $arr_status = array(
                        'all'       => 'All',
                        'draft' => 'Draft', 
                        'pending' => 'Pending', 
                        'published' => 'Published', 
                        'closed' => 'Closed', 
                        'denied' => 'Denied', 
                    );
                    $form -> status -> addMultiOptions($arr_status);
                    break;  
                default:
                    $form -> removeElement('status');
                    break;
            }
        }
        else
        {
            $form -> removeElement('status');
        }
        //Setup params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $originalOptions = $params;
        if ($form->isValid($params)) {
            $values = $form->getValues();
            $params = array_merge($params, $values);
        } else {
            //$params = array();
        }
        
        if (!isset($params['page']) || $params['page'] == '0') {
            $page = 1;
        }
        else {
            $page = (int)$params['page'];
        }
 		
        //Set curent page
        $this -> view -> paginator = $paginator = $table -> getBusinessesPaginator($params);
        
        $limit = $this->_getParam('itemCountPerPage', 10);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page );
        
        $businessIds = array();
        foreach ($paginator as $business){
            $businessIds[] = $business -> getIdentity();
        }
        $this->view->businessIds = implode("_", $businessIds);
        
        
        $this->view->totalBusinesses = $paginator->getTotalItemCount();
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = $originalOptions;
	}
}