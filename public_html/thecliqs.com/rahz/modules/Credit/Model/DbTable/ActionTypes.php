<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ActionTypes.php 03.01.12 16:09 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_DbTable_ActionTypes extends Engine_Db_Table
{
  public function getSelect()
  {
    /**
     * @var $modulesTbl Core_Model_DbTable_Modules
     */

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('a' => $this->info('name')))
      ->joinLeft(array('m' => $modulesTbl->info('name')), 'a.action_module = m.name', array())
      ->where('m.enabled = 1')
    ;

    return $select;
  }

  public function getActionTypes($params = array())
  {
    $select = $this->getSelect()->where('m.name <> ?', 'offers');

    if (!empty($params['credit'])) {
      $select->where('credit <> 0');
    }

    if (!empty($params['action_module'])) {
      $select->order('action_module ' . $params['action_module'])
        ->order('credit DESC');
    }

    return $this->fetchAll($select);
  }

  public function getActionType($type)
  {
    if (is_array($type)) {
      $action_type = $type['type'];
    } else if (is_object($type)) {
      $action_type = $type->type;
    } else {
      $action_type = $type;
    }

    $select = $this->select()->where('action_type = ?', $action_type);
    $temp_type = $this->fetchRow($select);

    if ($temp_type === null) {
      return $temp_type;
    }

    if ($temp_type->action_module == null) {
      return $temp_type;
    } else {
      $select = $this->getSelect()->where('action_type = ?', $action_type)->where('credit <> 0');
      return $this->fetchRow($select);
    }
  }

  public function getAllActionTypes($params = array())
  {
    $select = $this->select();
    if (!empty($params['group_type'])) {
      $select->where('group_type = ?', $params['group_type']);
    }

    $action_types = $this->fetchAll($select);
    $types = array(0 => ' ');
    foreach ($action_types as $type) {
      if ($type->action_type == 'transfer_to') {
        $types[$type->action_id] = 'Transfer to someone';
      } elseif ($type->action_type == 'transfer_from') {
        $types[$type->action_id] = 'Transfer from someone';
      } else {
        $types[$type->action_id] = '_CREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type->action_type), '_'));
      }
    }
    return $types;
  }

  public function getGroupTypes()
  {
    $select = $this->select()
      ->group('group_type');

    $types = $this->fetchAll($select);
    $group_types = array(0 => ' ');
    foreach ($types as $type) {
      $group_types[$type->group_type] = '_CREDIT_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type->group_type), '_'));
    }

    return $group_types;
  }

  public function getActionTypesByGroupType($type)
  {
    return $this->getAllActionTypes(array('group_type' => $type));
  }
}
