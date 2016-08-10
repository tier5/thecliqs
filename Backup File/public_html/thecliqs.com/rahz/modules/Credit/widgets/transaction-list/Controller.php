<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 07.01.12 12:49 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_TransactionListController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $page = $this->_getParam('page', 1);
    $user = Engine_Api::_()->user()->getViewer();
    if (!$user->getIdentity()) {
      return $this->setNoRender();
    }
    $this->view->paginator = Engine_Api::_()->getDbTable('logs', 'credit')
      ->getTransaction(
        array(
          'page' => $page,
          'user_id' => $user->getIdentity()
        )
      );
  }
}