<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ItemCover.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Timeline_View_Helper_ItemCover extends Engine_View_Helper_HtmlImage
{
  public function itemCover($item, $alt = "", $attribs = array())
  {
    // Whoops
    if (!($item instanceof Core_Model_Item_Abstract)) {
      throw new Zend_View_Exception("Item must be a valid item");
    }

    $type = '';
    // Get url

    /**
     * @var $table Storage_Model_DbTable_Files
     * @var $file Storage_Model_File
     */
    $table = Engine_Api::_()->getDbTable('files', 'storage');
    $file = $table->getFile($item->cover_id);
    $src = $file->map();

    // User image
    if ($src) {
      // Add auto class and generate
      return $this->htmlImage($src, $alt, array('id' => 'cover-photo'), $attribs);
    }

    return '';
  }
}