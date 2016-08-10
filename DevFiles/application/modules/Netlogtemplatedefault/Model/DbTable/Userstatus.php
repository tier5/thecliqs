<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplatedefault
 * @copyright  Copyright 2010-2012 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     altrego aka Vadim ( provadim@gmail.com )
 */

class Netlogtemplatedefault_Model_DbTable_Userstatus extends Engine_Db_Table {

  public function getStatus($user_id) {

	return $this->select()
		->from($this, 'status')
		->where('user_id = ?', $user_id)
		->query()
		->fetchColumn();

  }

}