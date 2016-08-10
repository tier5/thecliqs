<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminClaimController.php 16.12.11 16:23 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_AdminClaimController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_claim');

    $this->view->menu = $this->_getParam('action', 'index');
  }

  public function indexAction()
  {
    $page = $this->_getParam('page', 1);
    $this->view->form = $form = $this->getForm();

    /**
     * @var $table User_Model_DbTable_Users
     * @var $usersTbl User_Model_DbTable_Settings
     **/

    $usersTbl = Engine_Api::_()->getDbTable('settings', 'user');
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('u' => $table->info('name')))
      ->joinLeft(array('c' => $usersTbl->info('name')), 'u.user_id = c.user_id', array())
      ->where('c.name = ?', 'claimable_page_creator');

    $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($values = $this->getRequest()->getPost())) {
      return;
    }

    $usernames = preg_split('/[,]+/', $values['username']);
    $users = array();
    foreach ($usernames as $user){
      $user = trim(strip_tags($user));
      if ($user == "") {
        continue ;
      }
      $users[] = "'".$user."'";
    }

    $select = $table->select()
      ->where('username IN('.join(',', $users).')');

    $users = $table->fetchAll($select);
    foreach ($users as $user) {
      if (!$usersTbl->getSetting($user, 'claimable_page_creator')) {
        $row = $usersTbl->createRow();
        $row->user_id = $user->user_id;
        $row->name = 'claimable_page_creator';
        $row->value = 1;
        $row->save();
      }
    }
  }

  public function multiModifyAction()
  {
    $table = Engine_Api::_()->getDbTable('settings', 'user');
    $claimsTbl = Engine_Api::_()->getDbTable('claims', 'page');

    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      if ($values['submit_button'] == 'delete-creators') {
        foreach ($values as $key=>$value) {
          if( $key == 'delete_' . $value ) {
            $select = $table->select()
              ->where('user_id = ?', $value)
              ->where('name = ?', 'claimable_page_creator');
            $user = $table->fetchRow($select);
            if ($user !== null) {
              $user->delete();
            }
          }
        }
      } elseif ($values['submit_button'] == 'delete-claims') {
        foreach ($values as $key=>$value) {
          if( $key == 'remove_' . $value ) {
            $select = $claimsTbl->select()
              ->where('claim_id = ?', $value);
            $claim = $claimsTbl->fetchRow($select);
            if ($claim !== null) {
              $claim->delete();
            }
          }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'process'));
      }
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function processAction()
  {
    $page = $this->_getParam('page', 1);

    /**
     * @var $claimTable Page_Model_DbTable_Claims
     **/

    $claimTable = Engine_Api::_()->getDbTable('claims', 'page');
    $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
    $userTable = Engine_Api::_()->getDbTable('users', 'user');

    $claimTblName = $claimTable->info('name');
    $pageTblName = $pageTable->info('name');
    $userTblName = $userTable->info('name');

    $select = $claimTable->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $claimTblName))
      ->joinLeft(array('p' => $pageTblName), 'p.page_id = c.page_id', array('p.title'))
      ->joinLeft(array('u' => $userTblName), 'u.user_id = c.user_id', array('u.displayname'))
      ->order('c.claim_id DESC');

    $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

  public function addAction()
  {
    $users = $this->getUsersByText($this->_getParam('text'), $this->_getParam('limit', 40));
    $data = array();
    $mode = $this->_getParam('struct');

    if( $mode == 'text' ) {
      foreach( $users as $user ) {
        $data[] = $user->username;
      }
    } else {
      foreach( $users as $user ) {
        $data[] = array(
          'id' => $user->user_id,
          'label' => $user->username,
          'photo' => $this->view->itemPhoto($user, 'thumb.icon')
        );
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  public function takeActionAction()
  {
    $claim_id = $this->_getParam('claim_id', 0);
    $this->view->claim = $claim = Engine_Api::_()->getDbTable('claims', 'page')->findRow($claim_id);
    $this->view->page = $page = Engine_Api::_()->getItem('page', $claim->page_id);

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    $params = $this->_getAllParams();
    $action = $params['take_action'];

    $translate = Zend_Registry::get('Zend_Translate');

    if ($action == 'approve') {
      $page->changeOwner($claim->user_id);
      $message = $translate->_('PAGE_CLAIM_APPROVED');
    } elseif ($action == 'decline') {
      $message = $translate->_('PAGE_CLAIM_DECLINED');
    } else {
      return ;
    }
    $claim->changeStatus($action);
    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      $claim->claimer_email,
      'page_claim',
      array(
        'page' => $this->view->htmlLink($page->getHref(), $page->getTitle(), array('target' => '_blank')),
        'claimer_name' => $claim->claimer_name,
        'message' => $message
      )
    );

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 1500,
      'parentRefresh' => false,
      'messages' => array($translate->_('Your action has been submitted and email successfully sent to the claimer.'))
    ));
  }

  private function getUsersByText($text = null, $limit = 10)
  {
    /**
     * @var $table User_Model_DbTable_Users
     **/
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
      ->order('username ASC')
      ->limit($limit);

    if( $text )
    {
      $select->where('username LIKE ? OR displayname LIKE ?', '%'.$text.'%');
    }

    return $table->fetchAll($select);
  }

  private function getForm()
  {
    $form = new Engine_Form();
    $form->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('FormErrors')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'admin-claim'));
    $form->setAttrib('enctype','multipart/form-data');
    $form
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));
    $form->addElement('Text', 'username',array(
      'label'=>'Username',
      'autocomplete' => 'off',
      'description' => 'Separate users with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ))->getElement('username')->getDecorator("Description")->setOption("placement", "append");

    $form->addElement('Button', 'execute', array(
      'label' => 'Add Member',
      'type' => 'submit',
      'ignore' => true,
      'class' => 'buttons',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    return $form;
  }
}
