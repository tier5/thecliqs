<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Membership.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Membership extends Core_Model_DbTable_Membership
{
  protected $_type = 'page';


  // Configuration

  /**
   * Does membership require approval of the resource?
   *
   * @param Core_Model_Item_Abstract $resource
   * @return bool
   */
  public function isResourceApprovalRequired(Core_Model_Item_Abstract $resource)
  {
    return false;
  }

  public function updateRow(Page_Model_Page $page, $owner_id)
  {
    $select = $this->select()
      ->where('user_id = ?', $owner_id)
      ->where('resource_id = ?', $page->getIdentity())
      ->limit(1)
    ;

    if( $this->fetchRow($select) ) {
      $this->delete(array(
        'user_id' => $page->user_id,
        'resource_id' => $page->getIdentity()
      ));
      return;
    }

    $select = $this->select()
      ->where('user_id = ?', $page->user_id)
      ->where('resource_id = ?', $page->getIdentity())
      ->limit(1)
    ;

    $row = $this->fetchRow($select);
    $row->user_id = $owner_id;
    $row->save();
  }

  public function getMembershipsOfIds(User_Model_User $user, $active = true, $subject = null)
  {
    $ids = array();
    $rows = $this->getMembershipsOfInfo($user, $active, $subject);
    foreach( $rows as $row )
    {
      $ids[] = $row->resource_id;
    }
    return $ids;
  }

  public function getMembershipsOfInfo(User_Model_User $user, $active = true, $subject = null)
  {
    $table = $this->getTable();

    if( $subject ){
      $select = $table
        ->select()
        ->from(array('member' => $table->info('name')))
        ->where('user_id = ?', $user->getIdentity())
        ->joinLeft(array('list' => 'engine4_page_lists'), "list.list_id = member.resource_id", array())
        ->where('list.owner_id = ?', $subject->getIdentity());
    } else {
      $select = $table
        ->select()
        ->where('user_id = ?', $user->getIdentity())->limit(100);
    }

    if( $active !== null )
    {
      $select->where('active = ?', (bool) $active);
    }

    return $table->fetchAll($select);
  }

  public function setUserTypeAdmin(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    $this->_isSupportedType($resource);
    $row = $this->getRow($resource, $user);

    if( null === $row )
    {
      throw new Core_Model_Exception("Membership does not exist");
    }

    if( !$row->user_approved )
    {
      $row->type = 'ADMIN';

      $this->_checkActive($resource, $user);
      $row->save();
    }

    return $this;
  }

}