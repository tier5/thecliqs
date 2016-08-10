<?php
class Viewed_Api_Core extends Core_Api_Abstract
{
	public function saveViewedProfile($params)
	{
		$log = Zend_Registry::get('Zend_Log');
		$count = 0;
		$user_id = $params['user_id'];
		$owner_id = $params['subject_id'];
		
		$datetime=date("Y-m-d H:i:s");
		// insert tracking
		$viewme_table = Engine_Api::_()->getDbTable('viewmes', 'viewed');
		$viewmeInfo = $viewme_table->info('name');
		
		$select = $viewme_table->select()
		->where('user_id = ? ',$user_id)
		->where('profile_id = ? ',$owner_id);
		
		$arr =$viewme_table->fetchRow($select);
		if($arr)
		{
		   $count = $arr->count;
		}
		$values = array("user_id" => $user_id,"profile_id"=>$owner_id,"datetime"=>$datetime,"ip" =>$_SERVER['REMOTE_ADDR'],"count"=>$count+1,"flag"=>0);
		
		//$log->log('value of count>>>'.$count,Zend_Log::DEBUG);
		if ( !$arr['user_id'])
		{
			$viewme_table->insert($values);
		}
		else
		{
			$viewme_table->update(array(
					'datetime'=>new Zend_Db_Expr('NOW()'),
					'ip'=>$_SERVER['REMOTE_ADDR'],
					'count'=>$count+1,
					'flag'=>0,
		
			),array("user_id = ?"=>$user_id,"profile_id = ?"=>$owner_id));
		}
	}
	
	public function getWhoViewedMe($params,$user_level)
	{
		$log = Zend_Registry::get('Zend_Log');
		$user_id=$params['user_id'];
		$widgetlimit = isset($params['widgetlimit']) ? $params['widgetlimit'] : 10;
		
		$membercount_table = Engine_Api::_()->getDbTable('membercounts','viewed');
		$select = $membercount_table->select()
									->where('level_id = ?',$user_level);
		$memberResult = $membercount_table->fetchRow($select);
		$membercount = $memberResult['view_count']; 
		
		// my sql
		$viewme_table = Engine_Api::_()->getDbTable('viewmes', 'viewed');
		$user_table = Engine_Api::_()->getDbtable('users', 'user');
		$select = $viewme_table->select()
		->setIntegrityCheck(false)
		->from(array('v' => $viewme_table->info('name')),array('user_id','profile_id','datetime','count'))
		->join(array('u' => $user_table->info('name')),'v.user_id = u.user_id',array('username'))
		->where('v.profile_id = ?',$user_id)
		->order('v.datetime DESC');
		 if(!empty($membercount) && count($membercount) > 0)
		{
		  $select->limit($membercount);
		} 
		else
		{
			return array();
			 
		}
		$members = $viewme_table->fetchAll($select);
		return $members;
	}
	
	
	public function getWhoViewedMeAll($user_id)
	{
		$log = Zend_Registry::get('Zend_Log');
		$widgetlimit = isset($params['widgetlimit']) ? $params['widgetlimit'] : 10;
		// my sql
		$viewme_table = Engine_Api::_()->getDbTable('viewmes', 'viewed');
		$user_table = Engine_Api::_()->getDbtable('users', 'user');
		$select = $viewme_table->select()
		->setIntegrityCheck(false)
		->from(array('v' => $viewme_table->info('name')),array('user_id','profile_id','datetime','count'))
		->join(array('u' => $user_table->info('name')),'v.user_id = u.user_id',array('username'))
		->where('v.profile_id = ?',$user_id)
		->order('v.datetime DESC');
		$members = $viewme_table->fetchAll($select);
		return $members;
	}
	
	public function subscriptionStatus($user_level,$user_id)
	{
		$log = Zend_Registry::get('Zend_Log');
		if(isset($user_level))
		{
			$membertable = Engine_Api::_()->getDbTable('membercounts','viewed');
			$memberselect = $membertable->select()
			->where('level_id = ?',$user_level);
			$memberResult = $membertable->fetchRow($memberselect);
			$memberPackage = $memberResult->package_id;
		}
		
		if(isset($memberPackage) && !$memberPackage == 0)
		{
			$package_tabel = Engine_Api::_()->getDbTable('packages','payment');
			$package_select = $package_tabel->select()
				         ->where('package_id = ?',$memberPackage);
											//->where('level_id = ?',$user_level);
			$package_result = $package_tabel->fetchRow($package_select);
			if(isset($package_result) &&  count($package_result)  > 0)
			{ 
			  // check subscription
			  $defaultLevel = $package_result->level_id;
			  if($user_level == $defaultLevel)	
			  {
			  	return true;
			  }
			  else {
			  	return false;
			  }
		         /*$subscription_table = Engine_Api::_()->getDbTable('subscriptions','payment');
			$subscription_select = $subscription_table->select()
			->where('package_id = ?',$memberPackage)
			->where('user_id = ?', $user_id)
			->where('active = 1')
			->where("expiration_date != 'null'");
			$memberExits = $subscription_table->fetchRow($subscription_select);
			if(isset($memberExits) && count($memberExits)>0)
			{
				return true;
			}
			else {
				return false;
			}*/
		   } 
		   else {
		   	 return true;
		   }
		}
		else if($memberPackage == 0)
		{
			return true;
		}
		
	}
	
	
}
