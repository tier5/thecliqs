<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 2012-03-31 13:34 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_Model_DbTable_Settings extends Engine_Db_Table
{

  public function getSettings($type)
  {
    if (is_string($type)) {
      $select = $this->select()->where('name LIKE ?', $type.'_%');
    } elseif (is_numeric($type)) {
      $select = $this->select()->where('type_id = ?', $type);
    } else {
      return 'Unknown type';
    }

    $items = $this->fetchAll($select);
    $settings = array();

    foreach($items as $item){
      $settings[$item['name']] = $item['value'];
    }

    return $settings;
  }

  public function getSetting($name)
  {
    return $this->fetchRow($this->select()->where('name = ?', $name));
  }

  public function setSettings($values)
  {
    foreach($values as $key => $value){
      if ($value == null) $value = 0;
      $this->update(array('value' => $value), array('name = ?' => $key));
    }
  }
}