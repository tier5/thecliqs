<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Audios.php 09.09.11 17:03 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Audios extends Engine_Db_Table
{
  protected $_rowClass = 'Store_Model_Audio';

  public function getAudios($product_id)
  {
    $select = $this->select()
      ->where('product_id = ?', $product_id);

    return $this->fetchAll($select);
  }


}