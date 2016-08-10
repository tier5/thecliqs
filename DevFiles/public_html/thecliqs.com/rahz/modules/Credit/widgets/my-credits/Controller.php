<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 06.01.12 18:32 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_MyCreditsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (Engine_Api::_()->core()->hasSubject('user')) {
      $viewer_id = Engine_Api::_()->core()->getSubject('user')->getIdentity();
    } else {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    if (!$viewer_id) {
      return $this->setNoRender();
    }
    $this->view->credits = Engine_Api::_()->getItem('credit_balance', $viewer_id);

    /**
     * @var $table Credit_Model_DbTable_Balances
     */
    $table = Engine_Api::_()->getDbTable('balances', 'credit');
    $users = $table->fetchAll($table->getTopUsersSelect());

    $place = null;
    foreach ($users as $user) {
      if ($user->balance_id == $viewer_id) {
        $place = $user->place;
        break;
      }
    }

    $all_users = count($users);
    $point = (double)$all_users/5.0;
    $icon = 5;

    if ($all_users < 5) {
      $icon = $place;
    } else {
      for ($i = 0; $i < 5; $i ++) {
        $first = (double)$i*$point;
        $second = (double)($i+1)*$point;
        if ($place > $first && $place <= $second) {
          $icon = $i+1;
          break;
        }
      }
    }

    $this->view->place = $place;
    $this->view->icon = $icon;
  }
}
