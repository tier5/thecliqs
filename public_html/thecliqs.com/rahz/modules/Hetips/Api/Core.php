<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-03-31 17:01 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_Api_Core extends Core_Api_Abstract
{
  public function getTipsMeta($type, $option_id = 1)
  {
    $tipsMeta = array();
    if (isset($type) && is_string($type) && !empty($type)) {
      if ($type != 'user') {
        $metaData = Engine_Api::_()->getDbTable('meta', 'hetips')->getTipsMeta($type);

        foreach($metaData as $meta){
            $tipsMeta[$meta['tip_id']] = $meta['label'];
        }
      } else {
        $db = Engine_Db_Table::getDefaultAdapter();
        $select = $db->select()
                      ->from(array('ufm' => 'engine4_user_fields_meta'))
                      ->joinInner(array('um' => 'engine4_user_fields_maps'), 'um.child_id = ufm.field_id AND um.option_id = '.$option_id, array());

        $metaData = $db->fetchAll($select);

        foreach($metaData as $meta){
          if ($meta['type'] == 'profile_type' || $meta['type'] == 'heading') continue;
          $tipsMeta[$meta['field_id']] = $meta['label'];
        }
      }

      return $tipsMeta;
    }
    else{
      return $this->translate('Unknown type');
    }
  }

  public function getTipsMap($type, $option_id = 1)
  {
    return Engine_Api::_()->getDbTable('maps', 'hetips')->getTipsMap($type, $option_id);
  }

  public function getTipsTypes()
  {
    return Engine_Api::_()->getDbTable('types', 'hetips')->getListTypes();
  }

  public function getTipsSubject($subject){
    if ($subject && $subject->getType()) {
      $subject_type = $subject->getType();
      $subject_id = $subject->getIdentity();

      //Option tips
      if (!isset($subject->category_id)) {
        $option_id = @Engine_Api::_()->fields()->getFieldsSearch($subject)->profile_type;
      } else {
        $option_id = $subject->category_id;
      }

      if (!$option_id) {
        return 0;
      }

      $tipsMap = $this->getTipsMap($subject_type, $option_id);

      if ($tipsMap->count() == 0) {
        return 0;
      }

      $tips_ids = Array();

      //Get ids tips
      foreach($tipsMap as $tip){
        $tips_ids[$tip->id] = $tip->tip_id;
      }

      if (count($tips_ids) == 0) {
        return 0;
      }

      $tableMeta = Engine_Api::_()->getDbTable('meta', 'hetips');
      $tips = $tableMeta->getTipsByIds($subject_type, $tips_ids);
      $types = $tableMeta->fetchAll($tips);
      $list_types = Array();
      $i = 0;

      foreach($types as $item){
        $list_types[$i++] = $item->type;
      }
      $db = Engine_Db_Table::getDefaultAdapter();
      //Select tips
      if ($subject_type == 'user') {
        $select = $db->select()
                      ->from(array('hm' => 'engine4_hetips_maps'), array())
                      ->joinLeft(array('ufv' => 'engine4_user_fields_values'), 'hm.tip_type = \'user\' AND ufv.field_id = hm.tip_id', array('value'))
                      ->joinLeft(array('ufo' => 'engine4_user_fields_options'), 'ufo.option_id = ufv.value', array('caption' => 'ufo.label'))
                      ->joinInner(array('ufm' => 'engine4_user_fields_meta'), 'ufm.field_id = ufv.field_id', array('label'))
                      ->where('ufv.item_id = ?', $subject_id)
                      ->where('hm.option_id = ?', $option_id)
                      ->order('hm.order');

        $tips_arr = array();
        $items = Engine_Db_Table::getDefaultAdapter()->fetchAll($select);

        foreach($items as $item){
          $tips_arr[$item['label']] = ($item['caption'] == null) ? $item['value'] : $item['caption'];
        }

        return $tips_arr;
      } else {
        $tableName = 'engine4_'.$subject_type.'_'.$subject_type.'s';
        $select = $db->select()
                  ->from($tableName, $list_types)
                  ->where($subject_type.'_id = ?', $subject_id);

        $rows = $db->fetchRow($select);
        $tips_arr = array();
        $i = 0;

        foreach($rows as $value){
          if (empty($value)) {
            $tips_arr[$i++] = NULL;
            continue;
          }
          $tips_arr[$i++] = $value;
        }

        $select_labels = $tableMeta->select()
          ->setIntegrityCheck(false)
          ->from(array('htm' => $tableMeta->info('name')), array('label'))
          ->joinInner(array('hm' => 'engine4_hetips_maps'), 'hm.tip_id = htm.tip_id', array())
          ->where('hm.tip_type = ?', $subject_type)
          ->where('htm.tip_id IN (?)', $tips_ids)
          ->group('htm.label')
          ->order('hm.order');

        $labels_arr = $tableMeta->getDefaultAdapter()->fetchCol($select_labels);

        return array_combine((array)$labels_arr, (array)$tips_arr);
      }
    }
    else {
      return 0;
    }
  }
  public function getSetting($name)
  {
    return Engine_Api::_()->getDbTable('settings', 'hetips')->getSetting($name);
  }

  public function getFriendsGroupPaginator($user_id, $group_id){
    $db_users = Engine_Api::_()->getDbTable('users', 'user');

    $select = $db_users->select()->from(array('u' => $db_users->info('name')))
        ->setIntegrityCheck(false)
        ->joinInner(array('um' => 'engine4_user_membership'), 'um.user_id = u.user_id')
        ->joinInner(array('gm' => 'engine4_group_membership'), 'gm.user_id = um.user_id')
        ->where('um.resource_id = ?', $user_id)
        ->where('um.active = ?', 1)
        ->where('gm.resource_id = ?', $group_id);

    return Zend_Paginator::factory($select);
  }
}