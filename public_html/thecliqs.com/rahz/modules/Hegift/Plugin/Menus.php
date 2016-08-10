<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Menus.php 11.02.12 17:46 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Plugin_Menus
{
  public function onMenuInitialize_HegiftMainManage($row)
  {
    if ( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_HegiftMainOwn($row)
  {
    if ( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_HegiftMainTemp($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if ( !$viewer->getIdentity() ) {
      return false;
    }

    /**
     * @var $table Hegift_Model_DbTable_Gifts
     */
    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $gifts = $table->getGifts(array('owner_id' => $viewer->getIdentity(), 'sent_count' => true));

    if (!$gifts->getTotalItemCount()) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_HegiftProfileGift($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = Engine_Api::_()->core()->getSubject('user');
    $row = $user->membership()->getRow($viewer);

    if (!$viewer->getIdentity() ||
      $viewer->getIdentity() == $user->getIdentity() ||
      $row == null ||
      $row->user_id != $viewer->getIdentity())
    {
      return false;
    }

    return array(
      'label' => 'HEGIFT_Send Gift',
      'icon' => 'application/modules/Hegift/externals/images/send_gift.png',
      'route' => 'hegift_general',
      'params' => array(
        'user' => $user->getIdentity()
      )
    );
  }
}
