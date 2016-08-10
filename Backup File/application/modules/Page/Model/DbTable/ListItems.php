<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ListItems.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_ListItems extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_ListItem';

  public function getLikePageIds($user)
  {
    if (!($user instanceof User_Model_User)){
      return array();
    }

    $list = Engine_Api::_()->getDbTable('lists', 'page');
    $auth = Engine_Api::_()->getDbTable('allow', 'authorization');

    $select = $this->select()
        ->from(array('item' => $this->info('name')), new Zend_Db_Expr('list.owner_id AS page_id'))
        ->join(array('list' => $list->info('name')), 'list.title = "PAGE_LIKES"', array())
        ->join(array('auth' => $auth->info('name')), 'list.list_id = auth.role_id AND auth.action = "view" AND auth.resource_type = "page" AND auth.resource_id = list.owner_id', array())
        ->where('item.child_id = ?', $user->getIdentity())
        ->where('list.list_id = item.list_id');

    $data = $this->fetchAll($select);
    $page_ids = array();

    foreach ($data as $list){
      $page_ids[] = $list->page_id;
    }

    return $page_ids;

  }

}