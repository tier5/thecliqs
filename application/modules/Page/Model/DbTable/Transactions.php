<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Transactions.php 27.07.11 15:15 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Transactions extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_Transaction';

  public function getBenefitStatus(User_Model_User $user = null)
  {
    // Get benefit setting
    $benefitSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.benefit');
    if( !in_array($benefitSetting, array('all', 'some', 'none')) ) {
      $benefitSetting = 'all';
    }

    switch( $benefitSetting ) {
      default:
      case 'all':
        return true;
        break;

      case 'some':
        if( !$user ) {
          return false;
        }
        return (bool) $this->select()
          ->from($this, new Zend_Db_Expr('TRUE'))
          ->where('user_id = ?', $user->getIdentity())
          ->where('type = ?', 'page')
          ->where('status = ?', 'okay')
          ->limit(1);
        break;

      case 'none':
        return false;
        break;
    }

    return false;
  }
}