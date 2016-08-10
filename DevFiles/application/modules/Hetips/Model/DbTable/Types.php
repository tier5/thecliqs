<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Types.php 2012-03-31 13:34 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_Model_DbTable_Types extends Engine_Db_Table
{

  public function getListTypes()
  {
    return $this->fetchAll($this->select());
  }

  public function getIdType($type)
  {
    return $this->getAdapter()->fetchOne($this->select()->where('type = ?', $type));
  }

}