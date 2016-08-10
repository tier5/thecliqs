<?php

class Ynbusinesspages_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl').'application/modules/Ynbusinesspages/externals/scripts/collapsible.js');
        $table = Engine_Api::_()->getDbTable('categories', 'ynbusinesspages');
        $categories = $table->getCategories();
        unset($categories[0]);
        $this->view->categories = $categories;
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
    }
}