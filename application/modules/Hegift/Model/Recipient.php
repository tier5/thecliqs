<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Recipient.php 13.02.12 13:17 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Model_Recipient extends Core_Model_Item_Abstract
{
  public function getUser($action_name)
  {
    if ($action_name == 'received') {
      return Engine_Api::_()->getItem('user', $this->subject_id);
    }
    return Engine_Api::_()->getItem('user', $this->object_id);
  }

  public function getGift()
  {
    return Engine_Api::_()->getItem('gift', $this->gift_id);
  }

  public function getMessage()
  {
    if (isset($this->message)) {
      return $this->message;
    }
    return '';
  }

  public function getPrivacy()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if ($this->privacy) {
      return $translate->_('HEGIFT_public');
    } else {
      return $translate->_('HEGIFT_private');
    }
  }

  public function getPrivacyForUser(User_Model_User $user)
  {
    if ($this->privacy) {
      return true;
    } elseif ($user->getIdentity() == $this->object_id || $user->getIdentity() == $this->subject_id) {
      return true;
    } else {
      return false;
    }
  }
}
