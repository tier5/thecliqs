<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Messages.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Chat_Model_DbTable_Messages extends Engine_Db_Table
{
  protected $_rowClass = 'Chat_Model_Message';
}