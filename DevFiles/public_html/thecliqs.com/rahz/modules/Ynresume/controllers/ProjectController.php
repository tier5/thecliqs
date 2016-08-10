<?php
class Ynresume_ProjectController extends Core_Controller_Action_Standard
{
    public function suggestAction()
    {
    	$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('user');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('value', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = Engine_Api::_()->getItemTable('user')->select()->where('search = ?', 1);
    
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $friend ){
            $data[] = array(
                'id' => $friend->getIdentity(),
                'label' => $friend->getTitle(), // We should recode this to use title instead of label
                'title' => $friend->getTitle(),
                'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                'type' => 'user', 
                'url' => $friend->getHref(),
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
}