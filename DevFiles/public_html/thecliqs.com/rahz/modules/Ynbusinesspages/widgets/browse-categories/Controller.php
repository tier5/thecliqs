<?php

class Ynbusinesspages_Widget_BrowseCategoriesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_category');
        $select = $table->select()->where('level = ?', 1)->order('order ASC');
        $categories = $table->fetchAll($select);
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
        $this->view->categories = $categories;
    }
}