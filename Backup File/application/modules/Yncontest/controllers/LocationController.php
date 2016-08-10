<?php
class Yncontest_LocationController extends Core_Controller_Action_Standard
{
	public function indecAction(){
		
	}
	
	public function suggestAction(){
	   
	    $location = $this->_getParam('location');
        		
		$viewer = Engine_Api::_()->user()->getViewer();
    	if( !$viewer->getIdentity() ) 
    	{
	      $data = null;
	    } 
	    else 
	    {
	      $data = array();
	      $table = Engine_Api::_()->getItemTable('yncontest_location');
	      $select = $table->select();
		  
	      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
	        $select->limit($limit);
	      }
	
	      if( null !== ($text = $this->_getParam('location', $this->_getParam('location'))) ) {
	        $select->where('`'.$table->info('name').'`.`name` LIKE ?', '%'. $text .'%');
	      }
	      	      
	      foreach($table->fetchAll($select) as $location ) 
	      {	      	
		        $data[] = array(		         
		          'id'=> $location->getIdentity(),	           
		          'label' => $location->getName(),		          
		        );		 
	      }
	    }
	   	
	   $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       $data = Zend_Json::encode($data);
       $this->getResponse()->setBody($data);
	}
}
