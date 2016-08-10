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

class Netlogtemplatedefault_Widget_NetlogNetworkStatisticController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		// Get total users
	$table = Engine_Api::_()->getItemTable('user');
	$info = $table->select()
		->from($table, array('COUNT(user_id) AS count'))
		->where('enabled = ?', true)
		->query()->fetch();
	$this->view->member_count = $info['count'];
	
		// Get online users
	$table = Engine_Api::_()->getDbtable('online', 'user');
	$info = $table->select()
		->from($table, array('COUNT(user_id) as count'))
		->where('user_id > ?', 0)
		->where('active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'))
		->query()->fetch();
	$this->view->online_count = $info['count'];

  }

}