<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Cleanup.php 02.03.12 12:51 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $gift Hegift_Model_Gift
     */

    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $gifts = $table->getGifts(array('sent_count' => true, 'owner_id' => true, 'ipp' => 100));

    foreach ($gifts as $gift) {
      if ($gift->getRemovingDate(false) - time() < 0) {
        if ($gift->type == 3) {
          if ($gift->getStatus()) {
            Engine_Api::_()->getItem('credit_balance', $gift->owner_id)->returnCredits($gift->credits);
            $gift->deleteRecipients();
          }
        }
        $gift->delete();
      }
    }

    $this->_setWasIdle();
  }
}
