<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 14.02.12 18:30 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Widget_ProfilePhotoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject('user') ) {
      Engine_Api::_()->core()->setSubject($viewer);
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');

    $this->view->user = $subject;

    /**
     * @var $settings User_Model_DbTable_Settings
     * @var $recipient Hegift_Model_Recipient
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'user');
    $this->view->recipient_id = $recipient_id = $settings->getSetting($subject, 'active_gift');
    $this->view->recipient = $recipient = Engine_Api::_()->getItem('hegift_recipient', $recipient_id);

    if ($recipient !== null) {
      $this->view->from = Engine_Api::_()->getItem('user', $recipient->subject_id);
      $this->view->message = nl2br($recipient->message);
      $this->view->privacy = $recipient->getPrivacyForUser($viewer);
      $this->view->gift = Engine_Api::_()->getItem('gift', $recipient->gift_id);
    }

    $this->view->storage = Engine_Api::_()->storage();
  }
}