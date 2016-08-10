<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Claims.php 19.12.11 16:32 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Claims extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_Claim';

  public function checkClaim($page_id)
  {
    if (!$page_id) {
      return false;
    }
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    return $this->fetchRow(
      $this->select()
        ->where('page_id = ?', $page_id)
        ->where('user_id = ?', $user_id)
    );
  }
}
