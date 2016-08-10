<?php
class Yncontest_Widget_ProfileRuleController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {
  	
  
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    
    // Get subject and check auth
    $contest = Engine_Api::_()->core()->getSubject();
  
  	
    
  	$this->view->contest = $contest;  	
  	$this->view->viewer = $viewer;
  	$this->view->rules = Engine_Api::_()->getDbtable('rules', 'yncontest')->getRuleByContest($contest->contest_id);
    
  	

  	
  	
  	
    
  }

  
}