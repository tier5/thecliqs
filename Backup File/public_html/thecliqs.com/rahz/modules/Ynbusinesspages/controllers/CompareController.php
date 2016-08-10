<?php

class Ynbusinesspages_CompareController extends Core_Controller_Action_Standard {
    public function indexAction() {
        $this -> _helper -> content -> setEnabled();
        if (Engine_Api::_()->ynbusinesspages()->isMobile2()) {
            $this -> _helper -> content -> setNoRender();
        }
        $category_id = $this->_getParam('category_id');
        if (is_null($category_id)) {
            return $this->_helper->requireSubject()->forward();
        }
        $category = Engine_Api::_()->getItem('ynbusinesspages_category', $category_id);
        if (!$category) {
            return $this->_helper->requireSubject()->forward();
        }
        Engine_Api::_()->ynbusinesspages()->addCompareCategory($category_id);
        $this->view->category = $category;
        $availableCompareFields = Engine_Api::_()->getDbTable('comparisonfields', 'ynbusinesspages')->getAvailableComparisonFields();
        $this->view->availableCompareFields = $availableCompareFields;
        $this->view->businesses = Engine_Api::_()->ynbusinesspages()->getComparebusinessesOfCategory($category_id);
        $this->view->prevCategory = Engine_Api::_()->ynbusinesspages()->getPrevCategory($category_id);
        $this->view->nextCategory = Engine_Api::_()->ynbusinesspages()->getNextCategory($category_id);
        $this -> view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    }
    
    public function removeBusinessAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        $category_id = $this->_getParam('category_id');
        if (!$id) {
            echo Zend_Json::encode(array('status' => false, 'count' => 0));
            return true;
        }
        $count = Engine_Api::_()->ynbusinesspages()->removeComparebusiness($id, $category_id);
        if ($count === false) {
            echo Zend_Json::encode(array('status' => false, 'count' => 0));
            return true;
        }
        else {
            echo Zend_Json::encode(array('status' => true, 'count' => $count));
            return true;
        }
    }
    
    public function removeCategoryAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if (!$id) {
            echo Zend_Json::encode(array('status' => false, 'count' => 0));
            return true;
        }
        $count = Engine_Api::_()->ynbusinesspages()->removeCompareCategory($id);
        echo Zend_Json::encode(array('status' => true, 'count' => $count));
        return true;
    }
    
    public function sortAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $table = Engine_Api::_()->getDbTable('comparisonfields', 'ynbusinesspages');
        $comparisonfields = $table->fetchAll();
        $order = explode(',', $this->getRequest()->getParam('order'));
        $category_id = $this->getRequest()->getParam('category_id');
        if (!$category_id) return false;
        $newArr = array();
        foreach( $order as $i => $item ) {
            $field_id = substr($item, strrpos($item, '_') + 1);
            if (!empty($field_id))
                array_push($newArr, $field_id);
        }
        Engine_Api::_()->ynbusinesspages()->updateCompareCategory($category_id, $newArr);
        return true;
    }
}