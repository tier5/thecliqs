<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2012-03-04 17:01 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_IndexController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
    if ($this->getRequest()->isGet()) {
      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
      } else {
        $type = $this->_getParam('type');
        $id = $this->_getParam('id');
        if ($type == 'page') {
          $subject = Engine_Api::_()->page()->getPageByUrl($id);
        } else {
          $subject = Engine_Api::_()->getItem($type, $id);
        }
      }
    }

    $tipsArray = Engine_Api::_()->hetips()->getTipsSubject($subject);
    $tipsSettings = Engine_Api::_()->getDbTable('settings', 'hetips')->getSettings($subject->getType());

    $this->view->settings = $tipsSettings;
    $this->view->subject = $subject;
    $this->view->tips = $tipsArray;
  }

  public function showMatchesAction(){
    $id = (int)array_pop(explode('_', $this->_getParam('id', '')));

    if (!$id){
      $this->view->html = false;
      return ;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $api = Engine_Api::_()->like();

    $this->view->nophotoItems = array('blog', 'pageblog', 'classified', 'poll');
    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
    $this->view->userTips = Engine_Api::_()->hetips()->getTipsSubject($user);
    $this->view->showInterests = $showInterests = $user->authorization()->isAllowed($viewer, 'interest');
    $this->view->isSelf = $user->isSelf($viewer);
    $this->view->viewer = $viewer;
    $this->view->settings = Engine_Api::_()->getDbTable('settings', 'hetips')->getSettings('user');

    if ($showInterests){
      $this->view->paginator = $api->getMatchedItems($viewer, $id);
      $this->view->paginator->setItemCountPerPage(5);
    }else{
      $this->view->paginator = Zend_Paginator::factory(array());
    }

    $this->view->html = $this->view->render('_composeMatchHint.tpl');
  }

}