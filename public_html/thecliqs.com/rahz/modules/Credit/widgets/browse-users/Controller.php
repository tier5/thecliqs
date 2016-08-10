<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 06.01.12 16:31 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_BrowseUsersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /**
     * @var $table Credit_Model_DbTable_Balances
     **/

    $page = $this->_getParam('page', 1);
    $table = Engine_Api::_()->getDbTable('balances', 'credit');
    $this->view->top_users = $top_users = Zend_Paginator::factory($table->getTopUsersSelect($page));
    $top_users->setCurrentPageNumber($page);
  }
}