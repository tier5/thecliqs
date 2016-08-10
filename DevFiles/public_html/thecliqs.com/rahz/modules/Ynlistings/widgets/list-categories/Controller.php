<?php

class Ynlistings_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 
                'application/modules/Ynlistings/externals/scripts/collapsible.js');
        
        $categoryTable = Engine_Api::_()->getDbTable('categories', 'ynlistings');
        $categories = $categoryTable->getCategories();
        unset($categories[0]);
        $this->view->categories = $categories;
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
    }
}