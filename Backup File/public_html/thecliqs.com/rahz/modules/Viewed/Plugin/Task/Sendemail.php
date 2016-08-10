<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Cleanup.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Viewed_Plugin_Task_Sendemail extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
  	$viewme_table = Engine_Api::_()->getDbTable('viewmes', 'viewed');
  	$viewmeInfo = $viewme_table->info('name');
  	
  	$select = $viewme_table->select()
  	->where('flag = ? ',0);
  	$tempUsers = $viewme_table->fetchAll($select);
  	foreach ($tempUsers as $tempuser){
  		$profile_id = $tempuser->profile_id;
  		$user_id = $tempuser->user_id;
  		$user = Engine_Api::_()->getItem('user', $profile_id);
  		$viewer = Engine_Api::_()->getItem('user',$user_id );
  		Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'profile_view');
  		
  		$viewme_table->update(array(
  				'flag'=>1,
  		),array("user_id = ?"=>$user_id,"profile_id = ?"=>$profile_id));
  	}
  }
}