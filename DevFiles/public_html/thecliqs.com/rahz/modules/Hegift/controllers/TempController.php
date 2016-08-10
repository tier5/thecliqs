<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: TempController.php 01.03.12 11:15 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_TempController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hegift_main', array(), 'hegift_main_temp');

    $task = Engine_Api::_()->hegift()->getTask();
    $class = $task->plugin;
    $manualHook = new $class($task);
    $manualHook->execute();
  }

  public function indexAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $viewer User_Model_User
     */
    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $viewer = Engine_Api::_()->user()->getViewer();
    $page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = $table->getGifts(array('owner_id' => $viewer->getIdentity(), 'page' => $page, 'sent_count' => true));
    if (!$paginator->getTotalItemCount()) {
      $this->_redirectCustom($this->view->url(array(), 'hegift_own', true));
    }
  }

  public function deleteAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $this->_helper->layout->setLayout('default-simple');
    $gift = Engine_Api::_()->getItem('gift', $this->getRequest()->getParam('gift_id'));
    $this->view->form = $form = new Hegift_Form_Delete();
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$gift ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Gift doesn't exist or not authorized to delete");
      return;
    }

    if ($gift->owner_id) {
      if ($viewer->getIdentity() != $gift->owner_id) {
        $this->view->message = $translate->_('HEGIFT_This is not your Gift');
        return ;
      }
      if ($gift->isSent()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent. You cannot to delete!');
        return ;
      }
      if ($gift->getStatus()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent from you. You cannot to delete! Please reload this page.');
        return ;
      }
    }

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if ($gift->owner_id != $viewer->getIdentity()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $gift->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $gift->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your gift has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'hegift_temp', true),
      'messages' => Array($this->view->message)
    ));
  }
}
