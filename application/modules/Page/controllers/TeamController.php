<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TeamController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_TeamController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( 0 !== ($page_id = (int) $this->_getParam('page_id')) && null !== ($page = Engine_Api::_()->getItem('page', $page_id)) ){
      Engine_Api::_()->core()->setSubject($page);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('page');


		if (!$page->isEnabled()){
			$this->_redirectCustom(array('route' => 'page_package_choose', 'page_id'=>$page_id));
      return ;
		}
  }
  
	public function removeAction()
  {
  	$page_id = $this->_getParam('page_id');
  	$user_id = $this->_getParam('admin_id');
    $page = Engine_Api::_()->core()->getSubject();
    $user = Engine_Api::_()->getItem('user', $user_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($page->user_id == $user_id || (!$page->isOwner($viewer) && $user_id != $viewer->getIdentity())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("You cannot delete this member");
      return 0;
    }
    
  	$this->view->form = new Page_Form_Team_Delete();
  	
  	if ($this->getRequest()->isPost()) {
	    $page->getTeamList()->remove($user);
	  	$page->membership()->removeMember($user);

      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      if ($user_id != $viewer->getIdentity()) {
        $notifyApi->addNotification($user, $viewer, $page, 'page_delete_admin', array(
          'label' => $page->getTitle()
        ));
      }

  		return $this->_forward('success' ,'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()
          ->assemble(array('action' => 'manage-admins', 'page_id' => $page_id),'page_team'),
        'messages' => Array(Zend_Registry::get('Zend_Translate')->_('Member has been deleted.'))
      ));
  	}
  }

  public function changeAction()
  {
    $page_id = $this->_getParam('page_id');
    $user_id = $this->_getParam('admin_id');
    $page = Engine_Api::_()->core()->getSubject();
    $user = Engine_Api::_()->getItem('user', $user_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = new Page_Form_Team_Change();

    if ($this->getRequest()->isPost()) {

      if( $this->_getParam('type') == 'ADMIN') {
        $page->getTeamList()->add($user);
        $page->setAdmin($user);

        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        if ($user_id != $viewer->getIdentity()) {
          $notifyApi->addNotification($user, $viewer, $page, 'page_add_admin', array(
            'label' => $page->getTitle()
          ));
        }
      }
      else {
        $page->getTeamList()->remove($user);
        $page->setEmployer($user);

        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        if ($user_id != $viewer->getIdentity()) {
          $notifyApi->addNotification($user, $viewer, $page, 'page_add_employer', array(
            'label' => $page->getTitle()
          ));
        }

      }

      return $this->_forward('success' ,'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()
          ->assemble(array('action' => 'manage-admins', 'page_id' => $page_id),'page_team'),
        'messages' => Array(Zend_Registry::get('Zend_Translate')->_('Member has been changed.'))
      ));
    }

  }

  public function ajaxAction() 
  {
  	$task = $this->_getParam('task');
  	$page = Engine_Api::_()->core()->getSubject();
  	$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $viewer = Engine_Api::_()->user()->getViewer();

  	if ($task == 'add_admins' || $task == 'add_employers') {
	  	$user_ids = $this->_getParam('user_ids');
	  	$list = $page->getTeamList();
			foreach ($user_ids as $user_id) {
				if (!$user_id) {
					continue ;
				}
				$user = Engine_Api::_()->getItem('user', $user_id);
				
      	$page->membership()->addMember($user)
	        ->setUserApproved($user)
	        ->setResourceApproved($user);


        if( $task == 'add_admins' ) {
          $list->add($user);
          $page->setAdmin($user);
          $notifyApi->addNotification($user, $viewer, $page, 'page_add_admin', array(
            'label' => $page->getTitle()
          ));

        } else {
          $page->setEmployer($user);
          $notifyApi->addNotification($user, $viewer, $page, 'page_add_employer', array(
            'label' => $page->getTitle()
          ));
        }

			}
  	} elseif ($task == 'change_title') {
  		$admin_id = $this->_getParam('admin_id');
	  	$title = $this->_getParam('title');
	  	
	  	$user = Engine_Api::_()->getItem('user', $admin_id);
			$memberInfo = $page->membership()->getMemberInfo($user);
			$memberInfo->title = $title;
      $memberInfo->save();
  	}
  }
  
}