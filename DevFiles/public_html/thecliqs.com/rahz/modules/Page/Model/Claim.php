<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Claim.php 19.12.11 16:29 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_Claim extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'page';
  protected $_owner_type = 'page';

  public function changeStatus($status)
  {
    $this->status = $status . 'd';
    $this->save();
    return $this;
  }
}
