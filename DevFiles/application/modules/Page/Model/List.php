<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: List.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_List extends Core_Model_List
{
  protected $_owner_type = 'page';

  protected $_child_type = 'user';

  public $ignorePermCheck = true;

  public function getListItemTable()
  {
    return Engine_Api::_()->getItemTable('page_list_item');
  }
}