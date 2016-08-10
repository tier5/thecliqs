<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Maps.php 2012-03-31 13:34 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_Model_DbTable_Maps extends Engine_Db_Table
{
  public function getSelectTipsMap($type)
  {
    $select = $this->select()->setIntegrityCheck(false)
            ->from(array('hm' => $this->info('name')));

    if ($type == 'user') {
        $select->joinInner(array('u' => 'engine4_user_fields_meta'), 'u.field_id = hm.tip_id', array('type' ,'label'));
    }
    else {
      $select->joinInner(array('htm' => 'engine4_hetips_meta'), 'htm.tip_id = hm.tip_id');
    }

    $select->order('hm.order');

    return $select;
  }

  public function getTipsMap($type, $option_id)
  {
    $select = $this->getSelectTipsMap($type);

    $select->where('hm.tip_type = ?', $type)
            ->where('hm.option_id = ?', $option_id);

    return $this->fetchAll($select);
  }

  public function addTip($tipsData)
  {
    $data = array(
      'tip_id' => $tipsData['tip_id'],
      'option_id' => $tipsData['option_id'],
      'tip_type' => $tipsData['tip_type']
    );

    $insert = $this->createRow($data);
    $newTipId = $insert->save();

    $select = $this->getSelectTipsMap($tipsData['tip_type']);
    $select->where('id = ?', $newTipId);

    return $this->fetchRow($select);
  }

  public function orderTips($tips_ids)
  {
    $i = 0;
    foreach($tips_ids as $id){ 
      $this->update(array('order' => ++$i), array('id = ?' => $id));
    }
  }

  public function deleteTip($tip_id)
  {
    $this->delete(array('id = ?' => $tip_id));
  }
}