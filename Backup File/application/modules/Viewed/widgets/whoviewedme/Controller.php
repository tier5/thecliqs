<?php
/**
 * Integration4us
 *
 * @category   Application_Widget
 * @package    Who Viewed Me Widget
 * @copyright  Copyright 2009-2010 Integration4us
 * @license    http://www.integration4us.com/terms
 * @author     Jomar
 */                        
class Viewed_Widget_WhoviewedmeController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  		$log = Zend_Registry::get('Zend_Log');
  		// get user
  		$viewer = Engine_Api::_()->user()->getViewer();
  		if(isset($viewer))
  		{
  		 $this->view->user=$viewer;
  		 $user_id = $viewer->getIdentity();
  		 $this->view->userlevel = $user_level = $viewer->level_id;
  		}
  		else 
  		{
  			return $this->setNoRender();
  		}
  		//get subject
  		if(Engine_Api::_()->core()->hasSubject())
  		{
  		   
  		   $subject = Engine_Api::_()->core()->getSubject('user');
  		   if(isset($subject))
  		   {
  		 
  		   	$sub_exist = true;
   		    $subject_id = $subject->getIdentity();
  		    $user = Engine_Api::_()->getItem('user', $subject_id);
  		    $subject_level = $user->level_id;
  		    $params=array("user_id" => $user_id,"subject_id"=>$subject_id,"widgetlimit" => $this->_getParam('widgetlimit'));
  		   }
  		   else {
  		   	$params=array("user_id" => $user_id,"widgetlimit" => $this->_getParam('widgetlimit'));
  		   	
  		   }
  		
  		}
  		else {
  			   $sub_exist = false;
  			   $params=array("user_id" => $user_id,"widgetlimit" => $this->_getParam('widgetlimit'));	  
  		}
  		
  		//check subject subscription
  		if(isset($subject))
  		{
  			$sub_subscription = Engine_Api::_()->getApi('core','viewed')->subscriptionStatus($subject_level,$subject_id);
  		}
  		//check package
  		$member_Exits = Engine_Api::_()->getApi('core','viewed')->subscriptionStatus($user_level,$user_id);
  		$this->view->userallow = $member_Exits;
  		$log->log("user allow:- ".$member_Exists,Zend_Log::DEBUG);
                $test_mode = 0;
  		$exclude_mode = 0;
  		//check test mode
  		$coresettings_table = Engine_Api::_()->getDbTable('settings','core');
  		$coresettings_select = $coresettings_table->select()
  		->where("name = 'testmode'");
  		// check exclusion
  		$corexclude_select = $coresettings_table->select()
  		->where("name = 'excludelevels'"); 
  		$coresettings_result = $coresettings_table->fetchRow($coresettings_select);
  		$coreexclude_result = $coresettings_table->fetchRow($corexclude_select);
  		if(isset($coresettings_result) && count($coresettings_result)>0)
  		{
  			$test_mode = $coresettings_result->value;
  			if(isset($test_mode) && $test_mode == 1 && $user_level !== 1)
  			{
  				 
  				if( $sub_exist && !$subject->isSelf($viewer) )
  				{
  					$save = Engine_Api::_()->getApi('core','viewed')->saveViewedProfile($params);
  				}
  				return $this->setNoRender();
  			}
  		}  	
  		
  		if(isset($coreexclude_result) && count($coreexclude_result)>0)
  		{
  			$excluded_Levels = $coreexclude_result->value;
  			$excluded_Levels = explode(',', $excluded_Levels);  			
  		}
  		
		
		// if not own profile
		if( $sub_exist &&  !$subject->isSelf($viewer))
		{
			
			if(isset($excluded_Levels) && !in_array($user_level,$excluded_Levels))
			{
				$save = Engine_Api::_()->getApi('core','viewed')->saveViewedProfile($params);
			}
			elseif(!isset($excluded_Levels))
			{
			    $save = Engine_Api::_()->getApi('core','viewed')->saveViewedProfile($params);
			}
			if(( !($test_mode == 1) && $sub_subscription && !(isset($excluded_Levels))) || $test_mode == 1 && $subject_level == 1 || (!($test_mode == 1) && !in_array($user_level,$excluded_Levels)&& $sub_subscription) || $subject_level == 1)
			{
				$test = 0;
			 if($test){	
			 //add notification
			 Engine_Api::_()->getDbtable('notifications', 'activity')
			 ->addNotification($user, $viewer, $user, 'profile_view');
			 }
			}
			return $this->setNoRender();
		}else{
			// get viewed members
			$membersView= Engine_Api::_()->getApi('core','viewed')->getWhoViewedMe($params,$user_level);
			
			$this->view->paginator = $paginator = $membersView;
				
		}
				
		  	  
  }
}
?>
