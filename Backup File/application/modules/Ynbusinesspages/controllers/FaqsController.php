<?php

class Ynbusinesspages_FaqsController extends Core_Controller_Action_Standard {
    public function indexAction() {
        $table = Engine_Api::_()->getDbTable('faqs', 'ynbusinesspages');
        $select = $table->select()->where("status = 'show'")->order('order ASC');
        $paginator = $this->view->paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $paginator->setItemCountPerPage(10);
        $this->_helper->content->setEnabled();
    }
}
