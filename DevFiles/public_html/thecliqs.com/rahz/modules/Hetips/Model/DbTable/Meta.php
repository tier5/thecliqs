<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Meta.php 2012-03-31 13:34 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_Model_DbTable_Meta extends Engine_Db_Table
{
  public function getTipsMeta($type)
  {
    $select = $this->select()->where('tip_type = ?', $type);
    return $this->getAdapter()->fetchAssoc($select);
  }

  public function getTipsByIds($type, $ids)
  {
    $select = $this->select()
                   ->from(array('htm' => 'engine4_hetips_meta'))
                  ->setIntegrityCheck(false)
                  ->joinInner(array('hm' => 'engine4_hetips_maps'), 'hm.tip_id = htm.tip_id', array())
                  ->where('hm.tip_type = ?', $type)
                  ->where('htm.tip_id IN (?)', $ids)
                  ->group('hm.tip_id')
                  ->order('hm.order');
    return $select;
  }
}