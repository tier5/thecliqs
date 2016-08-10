<?php

class Headvmessages_Api_Core extends Core_Api_Abstract
{

  public function allowSmiles() {
    //return Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('heemoticon');
    return true;
  }

  public function getComposers()
  {
    $composePartials = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $data) {
      if (empty($data['composer'])) {
        continue;
      }

      foreach ($data['composer'] as $type => $config) {
        // is the current user has "create" privileges for the current plugin
        if (isset($config['auth'], $config['auth'][0], $config['auth'][1])) {
          $isAllowed = Engine_Api::_()
            ->authorization()
            ->isAllowed($config['auth'][0], null, $config['auth'][1]);

          if (!empty($config['auth']) && !$isAllowed) {
            continue;
          }
        }
        $composePartials[] = $config['script'];
      }
    }

    return $composePartials;
  }

  public function getConversations()
  {
    /**
     * @var $table Messages_Model_DbTable_Conversations
     * @var $paginstor Zend_Paginator
     */
    $user = Engine_Api::_()->user()->getViewer();
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');

    $table = Engine_Api::_()->getDbtable('conversations', 'messages');
    $cName = $table->info('name');
    $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    $select = $table->select()
      ->from($cName)
      ->joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` = ?", $user->getIdentity())
      /*->where("`{$rName}`.`inbox_deleted` = ?", 0)*/
      ->where("`{$cName}`.`resource_type` IS NULL or `{$cName}`.`resource_type` ='' or `{$cName}`.`resource_type` IN (?)", $enabledModules)
      ->order(new Zend_Db_Expr('inbox_updated desc'))
    ;

    $paginstor = Zend_Paginator::factory($select);

    return $paginstor;
  }

  public function getLastMessage($item)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $table = Engine_Api::_()->getItemTable('messages_message');
    $select = $table->select()
      ->where('conversation_id = ?', $item->getIdentity())
      ->order('message_id asc')
      ->limit(1)
    ;

    return $table->fetchRow($select);
  }

  public function removeConversation($id = 0) {
    /**
     * @var $cTable Messages_Model_DbTable_Conversations
     * @var $mTable Messages_Model_DbTable_Messages
     * @var $rTable Messages_Model_DbTable_Recipients
     */

    if(!$id) {
      return;
    }

    $cTable = Engine_Api::_()->getDbTable('conversations', 'messages');
    $mTable = Engine_Api::_()->getDbTable('messages', 'messages');
    $rTable = Engine_Api::_()->getDbTable('recipients', 'messages');

    $conv = $cTable->findRow($id);

    if(!$conv) {
      return;
    }

    $cTable->delete(array('conversation_id=?' => $id));
    $mTable->delete(array('conversation_id=?' => $id));
    $rTable->delete(array('conversation_id=?' => $id));
  }
}
