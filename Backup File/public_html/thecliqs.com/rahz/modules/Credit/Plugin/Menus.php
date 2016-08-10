<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 06.01.12 16:19 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Plugin_Menus
{
  public function onMenuInitialize_CreditMainCredit($row)
  {
    return true;
  }

  public function onMenuInitialize_CreditMainManage($row)
  {
    if ( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_UserProfileSendCredits($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($viewer->user_id == $subject->user_id) {
      return false;
    }
    $label = "CREDIT_Send Credits";

    return array(
      'label' => $label,
      'icon' => 'application/modules/Credit/externals/images/current.png',
      'route' => 'credit_general',
      'class' => 'smoothbox',
      'params' => array(
        'controller' => 'index',
        'action' => 'send',
        'user_id' => ( $viewer->getGuid(false) == $subject->getGuid(false) ? null : $subject->getIdentity()),
      )
    );
  }
}