<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Topics.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Forum_Model_DbTable_Topics extends Engine_Db_Table
{
  protected $_rowClass = 'Forum_Model_Topic';

  public function getChildrenSelectOfForum($forum, $params)
  {
    $select = $this->select()->where('forum_id = ?', $forum->forum_id);
    return $select;
  }
}