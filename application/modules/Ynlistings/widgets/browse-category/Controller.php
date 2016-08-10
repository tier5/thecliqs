<?php

class Ynlistings_Widget_BrowseCategoryController extends Engine_Content_Widget_Abstract {
    
    public function indexAction() {
        
        $table = Engine_Api::_()->getItemTable('ynlistings_category');
        
        $select = $table->select()->where('level = ?', 1);

        $categories = $table->fetchAll($select);
        
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }

        $this->view->categories = $categories;
    }
}