<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Import.php 19.12.11 16:29 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_Import extends Core_Model_Item_Abstract
{
  public function delete()
  {
    $this->getFile()->delete();
    parent::delete();
  }

  public function getFile()
  {
    return Engine_Api::_()->getItem('storage_file', $this->file_id);
  }
}
