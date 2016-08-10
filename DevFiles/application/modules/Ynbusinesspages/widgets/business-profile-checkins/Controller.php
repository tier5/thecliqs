<?php
class Ynbusinesspages_Widget_BusinessProfileCheckinsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }
        
        $this->view->business = $business = Engine_Api::_()->core()->getSubject('ynbusinesspages_business');
        if(!$business->isViewable()) {
            return $this->setNoRender();
        }
        
        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');
        
        $table = Engine_Api::_()->getDbTable('checkin', 'ynbusinesspages');
        $select = $table->select()->where('business_id = ?', $business->getIdentity());
        $rows = $table->fetchAll($select);
        $ids = array();
        foreach ($rows as $row) {
            array_push($ids, $row->user_id);    
        }
        if (empty($ids)) {
            $this->setNoRender();
            return;
        }
        
        $users = Engine_Api::_()->user()->getUserMulti($ids);
        $this -> view -> paginator = $paginator = Zend_Paginator::factory($users);
        $limit = $this -> _getParam('itemCountPerPage', 20);
        if (!$limit) {
            $limit = 20;
        }
        $this->view->limit = $limit;
        $paginator -> setItemCountPerPage($limit);
        $paginator -> setCurrentPageNumber(1);
    }
}