<?php

class Ynjobposting_Widget_ListIndustriesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 
                'application/modules/Ynjobposting/externals/scripts/collapsible.js');
        
        $table = Engine_Api::_()->getDbTable('industries', 'ynjobposting');
        $industries = $table->getIndustries();
        unset($industries[0]);
        $this->view->industries = $industries;
        if (count($industries) == 0) {
            $this->setNoRender(true);
        }
        $type = 'job';
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('controller') == 'company') {
            $type = 'company';
        }
        $this->view->type = $type;
    }
}