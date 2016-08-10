<?php
require_once APPLICATION_PATH . '/application/modules/Yncredit/Plugin/Constants.php';

class Yncredit_Api_Core extends Core_Api_Abstract 
{
	protected $_plugin;
	public function inputCreditData($levelId)
	{
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
		$types = $typeTbl->fetchAll($typeTbl->select());
		$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
		
		foreach ($types as $type)
		{
			$row = $creditTbl->createRow();
			$row->setFromArray(array(
					'level_id' => $levelId,
					'type_id' => $type->type_id,
					'credit' => $type->credit_default,
					'max_credit' => 100,
					'period' => 1,
			));
			$row->save();
		}
	}
	
	public function saveActionCredit($payload)
	{
		/*
		 * CHECK USER
		 */
		$user = Engine_Api::_()->getItem('user', $payload->subject_id);
		if (!$user->getIdentity())
		{
			return false;
		}
		
		/*
		 * CHECK SETTINGS - USER CAN USE CREDIT OR NOT
		 */
		$iCanUseCredit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'yncredit', 'use_credit');
		if (!$iCanUseCredit || $iCanUseCredit != '1')
		{
			return false;
		}
		
		/*
		 * GETTING CREDIT BY ACTION TYPE AND LEVEL ID
		 */
		$creditTbl = Engine_Api::_()->getDbTable('credits', 'yncredit');
		$credit = $creditTbl->getCreditByActionType($payload->type, $user);
		if (!is_object($credit))
		{
			return false;
		}
		
		/*
		 * CHECKING CREDIT IN PERIOD
		 */
		$logTbl = Engine_Api::_()->getDbTable('logs', 'yncredit');
		if (!$logTbl->checkCreditInPeriod($credit, $user))
		{
			return false;
		}
		if($credit -> action_type == 'signup')
		{
			// CHECKING LOG SIGNUP EXISTS
			if ($logTbl->checkSignupAction($credit, $user)) 
			{
				return false;
			}
		}
		
		/*
		 * GETTING FIRST ACTION REMAINING
		 */
		if ($credit->first_amount == '0')
		{
			$firstRemaining = 0;
		}
		else
		{
			$creditAmount = $logTbl->select()
				-> from($logTbl, new Zend_Db_Expr('COUNT(log_id)'))
				-> where('user_id = ?', $user->getIdentity())
				-> where('credit_id = ?', $credit->credit_id)
				-> query()
				-> fetchColumn();
			if ($creditAmount < $credit->first_amount)
			{
				$firstRemaining = $credit->first_amount - $creditAmount;
			}
			else
			{
				$firstRemaining = 0;
			}
		}
		
		/*
		 * GETTING REMAINING CREDITS
		 */
		$remaining = $logTbl->getRemainingCredits($credit, $user);
		$itemCount = 1;
		if ($remaining <= 0)
		{
			$creditPoints = 0;
		}
		else
		{
			if(is_array($payload->params))
			{
				$params = $payload->params;
			}
			else 
			{
				$params = Zend_Json::decode($payload->params);
			}
			
			if (!empty($params) && isset($params['count']) && $params['count'])
			{
				$itemCount = $params['count'];
			}
			//BE CAREFUL WITH FIRST ACTIONS
			if ($firstRemaining != 0)
			{
				if($itemCount > $firstRemaining)
				{
					$totalCredits = ($credit->first_credit * $firstRemaining)  + ($credit->credit * ($itemCount - $firstRemaining));
				}
				else 
				{
					$totalCredits = $credit->first_credit * $itemCount;
				}
				
			}
			else
			{
				$totalCredits = $credit->credit * $itemCount;
			}
			$creditPoints = min($totalCredits, $remaining);
		}
		/*
		 * CHECKING JOIN
		 */
		if (in_array($credit->action_type, unserialize(YNCREDIT_JOIN_ACTION_TYPE)))
		{
			if ($logTbl->checkJoinAction($credit, $payload)) {
				return false;
			}
		}
		/*
		 * ADDING LOG
		 */
		$log = $logTbl->createRow();
		$log->setFromArray(array(
				'user_id' => $user->getIdentity(),
				'credit_id' => $credit->credit_id,
				'type_id' => $credit->type_id,
				'credit' => $creditPoints,
				'object_type' => $payload->object_type,
				'object_id' => $payload->object_id,
				'item_count' => $itemCount,
				'body' => '',
				'creation_date' => new Zend_Db_Expr('NOW()'),
		));
		$log->save();
		
		/*
		 * UPDATE BALANCE
		 */
		$balance = Engine_Api::_()->getItem('yncredit_balance', $user->getIdentity());
		if (!$balance) {
			$balance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
			$balance->user_id = $user->getIdentity();
			$balance->save();
		}
		$balance->saveCredits($creditPoints);
	}
	
	public function saveItemCredit($payload)
	{
		$user = Engine_Api::_()->user()->getViewer();
		if($payload->getType() == 'user')
		{
			if($user-> getIdentity())
			{
				$itemType = 'user_login';
			}
			else 
			{
				$user = $payload;
				$itemType = 'signup';
			}
		}
		elseif($payload->getType() == 'contactimporter_joined')
		{
			$itemType = $payload->getType();
			$user = Engine_Api::_() -> getItem('user', $payload -> inviter_id);
		}
		elseif($payload->getType() == 'activity_notification' && $payload -> type == 'new_participant' && $payload -> object_type == 'contest')
		{
			$itemType = 'yncontest_join';
			$user = Engine_Api::_() -> getItem('user', $payload -> subject_id);
		}
		elseif($payload->getType() == 'activity_notification' && $payload -> type == 'ynauction_won' && $payload -> object_type == 'ynauction_product')
		{
			$itemType = 'ynauction_won';
			$user = Engine_Api::_() -> getItem('user', $payload -> subject_id);
		}
		else
		{
			$itemType = $payload->getType();
		}
		/*
		 * CHECK SETTINGS - USER CAN USE CREDIT OR NOT
		 */
		if(!$user-> getIdentity())
		{
			return;
		}
		$iCanUseCredit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'yncredit', 'use_credit');
		if (!$iCanUseCredit || $iCanUseCredit != '1')
		{
			return false;
		}
		
		/*
		 * GETTING CREDIT BY ITEM TYPE AND LEVEL ID
		*/
		$creditTbl = Engine_Api::_()->getDbTable('credits', 'yncredit');
		$credit = $creditTbl->getCreditByActionType($itemType, $user);
		if (!is_object($credit))
		{
			return false;
		}
		
		/*
		 * CHECKING CREDIT IN PERIOD
		*/
		$logTbl = Engine_Api::_()->getDbTable('logs', 'yncredit');
		if (!$logTbl->checkCreditInPeriod($credit, $user))
		{
			return false;
		}
		
		/*
		 * GETTING FIRST ACTION REMAINING
		*/
		if ($credit->first_amount == '0')
		{
			$firstRemaining = 0;
		}
		else
		{
			$creditAmount = $logTbl->select()
				-> from($logTbl, new Zend_Db_Expr('COUNT(log_id)'))
				-> where('user_id = ?', $user->getIdentity())
				-> where('credit_id = ?', $credit->credit_id)
				-> query()
				-> fetchColumn();
			if ($creditAmount < $credit->first_amount)
			{
				$firstRemaining = $credit->first_amount - $creditAmount;
			}
			else
			{
				$firstRemaining = 0;
			}
		}
		
		/*
		 * GETTING REMAINING CREDITS
		*/
		$remaining = $logTbl->getRemainingCredits($credit, $user);
		$itemCount = 1;
		if ($remaining <= 0)
		{
			$creditPoints = 0;
		}
		else
		{
			//BE CAREFUL WITH FIRST ACTIONS
			if ($firstRemaining != 0)
			{
				if($itemCount > $firstRemaining)
				{
					$totalCredits = ($credit->first_credit * $firstRemaining)  + ($credit->credit * ($itemCount - $firstRemaining));
				}
				else 
				{
					$totalCredits = $credit->first_credit * $itemCount;
				}
			}
			else
			{
				$totalCredits = $credit->credit * $itemCount;
			}
				
			$creditPoints = min($totalCredits, $remaining);
		}
		
		/*
		 * ADDING LOG
		*/
		list($objectType, $objectId) = $this->getActionObject($credit, $payload);
		if ($objectType === '' && $objectId === 0)
		{
			return false;
		}
		$log = $logTbl->createRow();
		$log->setFromArray(array(
				'user_id' => $user->getIdentity(),
				'credit_id' => $credit->credit_id,
				'type_id' => $credit->type_id,
				'credit' => $creditPoints,
				'object_type' => $objectType,
				'object_id' => $objectId,
				'body' => '',
				'creation_date' => new Zend_Db_Expr('NOW()'),
		));
		$log->save();
		
		/*
		 * UPDATE BALANCE
		*/
		$balance = Engine_Api::_()->getItem('yncredit_balance', $user->getIdentity());
		if (!$balance) {
			$balance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
			$balance->user_id = $user->getIdentity();
			$balance->save();
		}
		$balance->saveCredits($creditPoints);
	}
	
	/**
	 * Check credit type to add object default
	 * 
	 */
	public function getActionObject($credit, $payload)
	{
		$logTbl = Engine_Api::_()->getDbTable('logs', 'yncredit');
		if ($credit->action_type == 'signup') 
		{
			if ($logTbl->checkSignupAction($credit, $payload)) 
			{
				return array('', 0 );
			}
			$object_type = 'user';
			$object_id = $payload->getIdentity();
		} 
		elseif ($credit->action_type == 'music_playlist_song') 
		{
			$object_type = 'music_playlist';
			$object_id = $payload->playlist_id;
		} 
		elseif ($credit->action_type == 'mp3music_album_song') 
		{
			$object_type = 'mp3music_album';
			$object_id = $payload->album_id;
		} 
		elseif ($credit->action_type == 'contactimporter_joined') 
		{
			$object_type = 'user';
			$object_id = $payload->recipient_id;
		} 
		elseif ($credit->action_type == 'core_link' || $credit->action_type == 'user_login') 
		{
			$object_type = $payload->getType();
			$object_id = $payload->getIdentity();
		} 
		elseif ($credit->action_type == 'core_like' || $credit->action_type == 'core_comment') 
		{
			if ($logTbl->checkLikeAction($credit, $payload)) 
			{
				return array('', 0 );
			}
			$object_type = $payload->resource_type;
			$object_id = $payload->resource_id;
		} 
		elseif ($credit->action_type == 'activity_like' || $credit->action_type == 'activity_comment') 
		{
			if ($logTbl->checkLikeAction($credit, $payload)) 
			{
				return array('', 0 );
			}
			$object_type = 'activity_action';
			$object_id = $payload->resource_id;
		} 
		elseif ($credit->action_type == 'rate' || $credit->action_type == 'suggest') 
		{
			$object_type = $payload->object_type;
			$object_id = $payload->object_id;
		} 
		elseif ($credit->action_type == 'checkin_check') 
		{
			$object_type = 'activity_action';
			$object_id = $payload->action_id;
		} 
		elseif ($credit->action_type == 'video') 
		{
			$object_type = 'video';
			$object_id = $payload->video_id;
		} 
		elseif ($credit->action_type == 'file') 
		{
			$object_type = 'file';
			$object_id = $payload->getIdentity();
		}
		elseif ($credit->action_type == 'ynauction_won') 
		{
			$object_type = 'ynauction_product';
			$object_id = $payload->object_id;
		}
		elseif ($credit->action_type == 'yncontest_entry') 
		{
			$object_type = 'yncontest_entry';
			$object_id = $payload->getIdentity();
		}
		elseif ($credit->action_type == 'yncontest_join') 
		{
			if ($logTbl->checkJoinAction($credit, $payload)) 
			{
				return array('', 0 );
			}
			$object_type = 'contest';
			$object_id = $payload->object_id;
		} 
		else 
		{
			return array('', 0 );
		}
		return array($object_type, $object_id);
	}

	public function updateUserProfileCredits(User_Model_User $user)
  	{
	    if (!$user->getIdentity()) {
	      return 0;
	    }
		$iCanUseCredit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'yncredit', 'use_credit');
		if (!$iCanUseCredit || $iCanUseCredit != '1')
		{
			return false;
		}
		
		/*
		 * GETTING CREDIT BY ITEM TYPE AND LEVEL ID
		*/
		$creditTbl = Engine_Api::_()->getDbTable('credits', 'yncredit');
		$credit = $creditTbl->getCreditByActionType('user_profile_edit', $user);
		if (!is_object($credit))
		{
			return false;
		}
		/*
		 * CHECKING CREDIT IN PERIOD
		*/
		$logTbl = Engine_Api::_()->getDbTable('logs', 'yncredit');
		if (!$logTbl->checkCreditInPeriod($credit, $user))
		{
			return false;
		}
		
		/*
		 * GETTING FIRST ACTION REMAINING
		*/
		if ($credit->first_amount == '0')
		{
			$firstRemaining = 0;
		}
		else
		{
			$creditAmount = $logTbl->select()
				-> from($logTbl, new Zend_Db_Expr('COUNT(log_id)'))
				-> where('user_id = ?', $user->getIdentity())
				-> where('credit_id = ?', $credit->credit_id)
				-> query()
				-> fetchColumn();
			if ($creditAmount < $credit->first_amount)
			{
				$firstRemaining = $credit->first_amount - $creditAmount;
			}
			else
			{
				$firstRemaining = 0;
			}
		}
		
		/*
		 * GETTING REMAINING CREDITS
		*/
		$remaining = $logTbl->getRemainingCredits($credit, $user);
		$itemCount = 1;
		if ($remaining <= 0)
		{
			$creditPoints = 0;
		}
		else
		{
			//BE CAREFUL WITH FIRST ACTIONS
			if ($firstRemaining != 0)
			{
				if($itemCount > $firstRemaining)
				{
					$totalCredits = ($credit->first_credit * $firstRemaining)  + ($credit->credit * ($itemCount - $firstRemaining));
				}
				else 
				{
					$totalCredits = $credit->first_credit * $itemCount;
				}
			}
			else
			{
				$totalCredits = $credit->credit * $itemCount;
			}
				
			$creditPoints = min($totalCredits, $remaining);
		}
		
		$log = $logTbl->createRow();
		$log->setFromArray(array(
				'user_id' => $user->getIdentity(),
				'credit_id' => $credit->credit_id,
				'type_id' => $credit->type_id,
				'credit' => $creditPoints,
				'object_type' => $user -> getType(),
				'object_id' => $user -> getIdentity(),
				'body' => '',
				'creation_date' => new Zend_Db_Expr('NOW()'),
		));
		$log->save();
		/*
		 * UPDATE BALANCE
		*/
		$balance = Engine_Api::_()->getItem('yncredit_balance', $user->getIdentity());
		if (!$balance) {
			$balance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
			$balance->user_id = $user->getIdentity();
			$balance->save();
		}
		$balance->saveCredits($creditPoints);
  	}
	
	public function updateProfileCompletedCredits(User_Model_User $user)
  	{
	    if (!$user->getIdentity()) {
	      return 0;
	    }
		$iCanUseCredit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'yncredit', 'use_credit');
		if (!$iCanUseCredit || $iCanUseCredit != '1')
		{
			return false;
		}
		
		/*
		 * GETTING CREDIT BY ITEM TYPE AND LEVEL ID
		*/
		$creditTbl = Engine_Api::_()->getDbTable('credits', 'yncredit');
		$credit = $creditTbl->getCreditByActionType('profile_completed', $user);
		if (!is_object($credit))
		{
			return false;
		}
		
		/*
		 * CHECKING CREDIT IN PERIOD
		*/
		$logTbl = Engine_Api::_()->getDbTable('logs', 'yncredit');
		if (!$logTbl->checkCreditInPeriod($credit, $user))
		{
			return false;
		}
		
		// Check log exist
		if ($logTbl->checkProfileCompleted($credit, $user)) 
		{
			return false;
		}
		/*
		 * GETTING FIRST ACTION REMAINING
		*/
		if ($credit->first_amount == '0')
		{
			$firstRemaining = 0;
		}
		else
		{
			$creditAmount = $logTbl->select()
				-> from($logTbl, new Zend_Db_Expr('COUNT(log_id)'))
				-> where('user_id = ?', $user->getIdentity())
				-> where('credit_id = ?', $credit->credit_id)
				-> query()
				-> fetchColumn();
			if ($creditAmount < $credit->first_amount)
			{
				$firstRemaining = $credit->first_amount - $creditAmount;
			}
			else
			{
				$firstRemaining = 0;
			}
		}
		
		/*
		 * GETTING REMAINING CREDITS
		*/
		$remaining = $logTbl->getRemainingCredits($credit, $user);
		$itemCount = 1;
		if ($remaining <= 0)
		{
			$creditPoints = 0;
		}
		else
		{
			//BE CAREFUL WITH FIRST ACTIONS
			if ($firstRemaining != 0)
			{
				if($itemCount > $firstRemaining)
				{
					$totalCredits = ($credit->first_credit * $firstRemaining)  + ($credit->credit * ($itemCount - $firstRemaining));
				}
				else 
				{
					$totalCredits = $credit->first_credit * $itemCount;
				}
			}
			else
			{
				$totalCredits = $credit->credit * $itemCount;
			}
				
			$creditPoints = min($totalCredits, $remaining);
		}
		
		$log = $logTbl->createRow();
		$log->setFromArray(array(
				'user_id' => $user->getIdentity(),
				'credit_id' => $credit->credit_id,
				'type_id' => $credit->type_id,
				'credit' => $creditPoints,
				'object_type' => $user -> getType(),
				'object_id' => $user -> getIdentity(),
				'body' => '',
				'creation_date' => new Zend_Db_Expr('NOW()'),
		));
		$log->save();
		/*
		 * UPDATE BALANCE
		*/
		$balance = Engine_Api::_()->getItem('yncredit_balance', $user->getIdentity());
		if (!$balance) {
			$balance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
			$balance->user_id = $user->getIdentity();
			$balance->save();
		}
		$balance->saveCredits($creditPoints);
  	}
	
	public function buyCredits($buyer, $credits, $service)
  	{
	    $table = Engine_Api::_()->getDbTable('logs', 'yncredit');
	    $tableCredits = Engine_Api::_()->getDbTable('credits', 'yncredit');
	
	    $buyerBalance = Engine_Api::_()->getItem('yncredit_balance', $buyer->getIdentity());
	    if (!$buyerBalance) {
	      $buyerBalance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
	      $buyerBalance->user_id = $buyer->getIdentity();
	      $buyerBalance->save();
	    }
	
	    $credit = $tableCredits -> getCreditByActionType('buy_credits', $buyer);
		
	    $row = $table->createRow();
	    $row->user_id = $buyer->getIdentity();
	    $row->credit_id = $credit -> credit_id;
	    $row->type_id = $credit -> type_id;
	    $row->credit = $credits;
	    $row->object_type = '';
	    $row->object_id = 0;
	    $row->body = $service;
	    $row->creation_date = new Zend_Db_Expr('NOW()');
		$row->save();
	    $buyerBalance->saveCredits($credits, 'buy');
  	}
	
	public function sendCredits($sender, $receiver, $credits = 0, $type = 'user')
  	{
	    $table = Engine_Api::_()->getDbTable('logs', 'yncredit');
	    $tableCredits = Engine_Api::_()->getDbTable('credits', 'yncredit');
	    $receiverBalance = Engine_Api::_()->getItem('yncredit_balance', $receiver->getIdentity());
	    if (!$receiverBalance) 
	    {
	      $receiverBalance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
	      $receiverBalance-> user_id = $receiver->getIdentity();
	      $receiverBalance->save();
	    }
	    $credit_receive = $tableCredits -> getCreditByActionType('receive_credits', $receiver);
		if($type == 'user')
		{
			$senderBalance = Engine_Api::_()->getItem('yncredit_balance', $sender->getIdentity());
			$credit_send = $tableCredits -> getCreditByActionType('send_credits', $sender);
		    $row = $table->createRow();
		    $row->user_id = $sender->getIdentity();
		    $row->credit_id = $credit_send -> credit_id;
		    $row->type_id = $credit_send -> type_id;
		    $row->credit = $credits*(-1);
		    $row->object_type = 'user';
		    $row->object_id = $receiver -> getIdentity();
		    $row->body = "Send Credits";
		    $row->creation_date = new Zend_Db_Expr('NOW()');
			$row->save();
			$senderBalance->saveCredits($credits*(-1), 'send');
		}
		
		$row = $table->createRow();
	    $row->user_id = $receiver->getIdentity();
	    $row->credit_id = $credit_receive -> credit_id;
	    $row->type_id = $credit_receive -> type_id;
	    $row->credit = $credits;
	    $row->object_type = 'user';
	    $row->object_id = $sender->getIdentity();
	    $row->body = "Receive Credits";
	    $row->creation_date = new Zend_Db_Expr('NOW()');
		$row->save();
	    $receiverBalance->saveCredits($credits, 'receive');
		
		// add notification
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		if($sender -> getIdentity() != $receiver -> getIdentity())
			$notifyApi -> addNotification($receiver, $sender, $receiverBalance, 'yncredit_receive', array('credits' => $credits));
  	}
	
	public function debitCredits($sender, $receiver, $credits = 0)
  	{
	    $table = Engine_Api::_()->getDbTable('logs', 'yncredit');
	    $tableCredits = Engine_Api::_()->getDbTable('credits', 'yncredit');
	    $receiverBalance = Engine_Api::_()->getItem('yncredit_balance', $receiver->getIdentity());
	    if (!$receiverBalance) 
	    {
	      $receiverBalance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
	      $receiverBalance->user_id = $receiver->getIdentity();
	      $receiverBalance->save();
	    }
	    $credit_receive = $tableCredits -> getCreditByActionType('debit_credits', $receiver);
		
		$row = $table->createRow();
	    $row->user_id = $receiver->getIdentity();
	    $row->credit_id = $credit_receive -> credit_id;
	    $row->type_id = $credit_receive -> type_id;
	    $row->credit = $credits*(-1);
	    $row->object_type = 'user';
	    $row->object_id = $sender->getIdentity();
	    $row->body = "Debit Credits";
	    $row->creation_date = new Zend_Db_Expr('NOW()');
		$row->save();
	    $receiverBalance->saveCredits($credits*(-1), 'spend');
		
		// add notification
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$view = Zend_Registry::get("Zend_View");
		$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $view->translate('_SITE_TITLE'));
		if($sender -> getIdentity() != $receiver -> getIdentity())
			$notifyApi -> addNotification($receiver, $sender, $receiverBalance, 'yncredit_debit', array('credits' => $credits, 'site_name' => $title));
  	}
  	
  	public function getPlugin($gateway_id)
  	{
	    if (null === $this->_plugin) 
	    {
	      if (null == ($gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id))) 
	      {
	        return null;
	      }
	      Engine_Loader::loadClass($gateway->plugin);
	      if (!class_exists($gateway->plugin)) {
	        return null;
	      }
		  $class = str_replace('Payment', 'Yncredit', $gateway->plugin);
	      
	      Engine_Loader::loadClass($class);
	      if (!class_exists($class)) {
	        return null;
	      }
		  
	      $plugin = new $class($gateway);
	      if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
	        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
	          'implement Engine_Payment_Plugin_Abstract', $class));
	      }
	      $this->_plugin = $plugin;
	    }
    	return $this->_plugin;
  	}
  	
  	public function getGateway($gateway_id)
  	{
    	return $this->getPlugin($gateway_id)->getGateway();
  	}
  	
  	public function getService($gateway_id)
  	{
    	return $this->getPlugin($gateway_id)->getService();
  	}

	public function checkSendCredit($viewer, $user)
	{
		$view = Zend_Registry::get("Zend_View");
		if(!$user || !$viewer -> getIdentity())
		{
			return Zend_Json::encode(array('fail' => 1, 'message' => $view -> translate('Please select one friend to send credits.')));
		}
	  	$can_send = Engine_Api::_() -> authorization() -> isAllowed('yncredit', $viewer, 'send');
		$can_receive = Engine_Api::_() -> authorization() -> isAllowed('yncredit', $user, 'receive');
		$balance = Engine_Api::_()->getItem('yncredit_balance', $viewer -> getIdentity());
		if(!$can_send || !$balance)
		{
			return Zend_Json::encode(array('fail' => 1, 'message' => $view -> translate('You can not send credits to')));
		}
		
		if(!$can_receive)
		{
			return Zend_Json::encode(array('fail' => 1, 'message' => $view -> translate('This friend can not receive credits.')));
		}
		
		$max_send = Engine_Api::_()->authorization()->getPermission($viewer -> level_id, 'yncredit', 'max_send');
		$max_receive = Engine_Api::_()->authorization()->getPermission($user -> level_id, 'yncredit', 'max_receive');
		
		$current_credit = $balance -> current_credit;
		
		if($max_receive == 0 && $max_send == 0)
		{
			return Zend_Json::encode(array('fail' => 0, 'max' => $current_credit, 'message' => $view -> translate(array("You can send up to %s credit to", "You can send up to %s credits to", $view ->locale()->toNumber($current_credit)), $view ->locale()->toNumber($current_credit))));
		}
		
		$logsTable = Engine_Api::_() -> getDbTable('logs', 'yncredit');
		$max_can_send = 0;
		if($max_send != 0)
		{
			$max_can_send = $logsTable -> getRemaining($viewer, 'send');
			if($max_can_send <= 0)
			{
				return Zend_Json::encode(array('fail' => 1, 'message' => $view -> translate('You cannot send credits to your friend because you meet the limitation of sending credits.')));
			}
		}
		if($max_receive != 0)
		{
			$remaining_recieve = $logsTable -> getRemaining($user, 'receive');
			
			if($remaining_recieve <= 0)
			{
				return Zend_Json::encode(array('fail' => 1, 'message' => $view -> translate('You cannot send credits to your friend because he/she meets the limitation of receiving credits.')));
			}
			if($remaining_recieve < $max_can_send)
			{
				$max_can_send = $remaining_recieve;
			}
		}
		if($max_can_send > 0)
		{
			if($current_credit < $max_can_send)
			{
				$max_can_send = $current_credit;
			}
			return Zend_Json::encode(array('fail' => 0, 'max' => $max_can_send, 'message' => $view -> translate(array("You can send up to %s credit to", "You can send up to %s credits to", $view ->locale()->toNumber($max_can_send)), $view ->locale()->toNumber($max_can_send))));
		}
	}
	
	public function getUsersByLevels($levelIds = array())
	{
		$userTable = Engine_Api::_() -> getItemTable('user');
		$select = $userTable -> select();
		$select -> where("level_id IN (?)", $levelIds)
			-> where("enabled = 1")
			-> where("verified = 1")
			-> where("approved = 1");
		return $userTable -> fetchAll($select);
	}
	
	public function getPackageDescription($package)
   {
	    $translate = Zend_Registry::get('Zend_Translate');
	    $settings = Engine_Api::_()->getDbTable('settings', 'core');
	    $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
	    $priceStr = '<span class="payment_credit_icon">';
	    $priceStr .= '<span class="payment-credit-price">' . ceil($package->price * $defaultPrice) . ' ' . $translate->translate('Credits') . '</span></span>';
	
	    // Plan is free
	    if ($package->price == 0) {
	      $str = $translate->translate('Free');
	    } // Plan is recurring
	    else if ($package->recurrence > 0 && $package->recurrence_type != 'forever') {
	
	      // Make full string
	      if ($package->recurrence == 1) { // (Week|Month|Year)ly
	        if ($package->recurrence_type == 'day') {
	          $typeStr = $translate->translate('daily');
	        } else {
	          $typeStr = $translate->translate($package->recurrence_type . 'ly');
	        }
	        $str = sprintf($translate->translate('%1$s %2$s'), $priceStr, $typeStr);
	      } else { // per x (Week|Month|Year)s
	        $typeStr = $translate->translate(array($this->recurrence_type, $package->recurrence_type . 's', $package->recurrence));
	        $str = sprintf($translate->translate('%1$s per %2$s %3$s'), $priceStr,
	          $package->recurrence, $typeStr); // @todo currency
	      }
	    } // Plan is one-time
	    else {
	      $str = sprintf($translate->translate('One-time fee of %1$s'), $priceStr);
	    }
	
	    // Add duration, if not forever
	    if ($package->duration > 0 && $package->duration_type != 'forever') {
	      $typeStr = $translate->translate(array($package->duration_type, $package->duration_type . 's', $package->duration));
	      $str = sprintf($translate->translate('%1$s for %2$s %3$s'), $str, $package->duration, $typeStr);
	    }
	    return $str;
  	}
	
	public function hookCustomEarnCredits(User_Model_User $user, $body, $action_type, $item = null)
  	{
  		$table = Engine_Api::_()->getDbTable('logs', 'yncredit');
	    $tableCredits = Engine_Api::_()->getDbTable('credits', 'yncredit');
	
	    $uBalance = Engine_Api::_()->getItem('yncredit_balance', $user->getIdentity());
	    if (!$uBalance) {
	      $uBalance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
	      $uBalance->user_id = $user->getIdentity();
	      $uBalance->save();
	    }
	
	    $credit = $tableCredits -> getCreditByActionType($action_type, $user);
		if (!is_object($credit))
		{
			return false;
		}
		$creditPoints = 0;
		/*
		 * CHECKING CREDIT IN PERIOD
		*/
		if (!$table->checkCreditInPeriod($credit, $user))
		{
			return false;
		}
		/*
		 * GETTING FIRST ACTION REMAINING
		*/
		if ($credit->first_amount == '0')
		{
			$firstRemaining = 0;
		}
		else
		{
			$creditAmount = $table->select()
				-> from($table, new Zend_Db_Expr('COUNT(log_id)'))
				-> where('user_id = ?', $user->getIdentity())
				-> where('credit_id = ?', $credit->credit_id)
				-> query()
				-> fetchColumn();
			if ($creditAmount < $credit -> first_amount)
			{
				$firstRemaining = $credit->first_amount - $creditAmount;
			}
			else
			{
				$firstRemaining = 0;
			}
		}
		/*
		 * GETTING REMAINING CREDITS
		*/
		$remaining = $table->getRemainingCredits($credit, $user);
		$itemCount = 1;
		if ($remaining <= 0)
		{
			$creditPoints = 0;
		}
		else
		{
			//BE CAREFUL WITH FIRST ACTIONS
			if ($firstRemaining != 0)
			{
				if($itemCount > $firstRemaining)
				{
					$totalCredits = ($credit->first_credit * $firstRemaining)  + ($credit->credit * ($itemCount - $firstRemaining));
				}
				else 
				{
					$totalCredits = $credit->first_credit * $itemCount;
				}
			}
			else
			{
				$totalCredits = $credit->credit;
			}
			$creditPoints = min($totalCredits, $remaining);
		}
	    $row = $table->createRow();
	    $row->user_id = $user -> getIdentity();
	    $row->credit_id = $credit -> credit_id;
	    $row->type_id = $credit -> type_id;
	    $row->credit = $creditPoints;
		if($item)
		{
			$row->object_type = $item -> getType();
	    	$row->object_id = $item -> getIdentity();
		}
		else 
		{
			$row->object_type = '';
	    	$row->object_id = 0;
		}
	    $row->body = $body;
	    $row->creation_date = new Zend_Db_Expr('NOW()');
	
	    $uBalance->saveCredits($creditPoints);
	    $row->save();
	}

	public function spendCredits(User_Model_User $buyer, $credits, $body, $type_spend, $item = null)
  	{
  		$db = Engine_Api::_() -> getItemTable('yncredit_balance') -> getAdapter();
		$db -> beginTransaction();
		try
		{
		    $table = Engine_Api::_()->getDbTable('logs', 'yncredit');
		    $tableCredits = Engine_Api::_()->getDbTable('credits', 'yncredit');
		
		    $buyerBalance = Engine_Api::_()->getItem('yncredit_balance', $buyer->getIdentity());
		    if (!$buyerBalance) {
		      $buyerBalance = Engine_Api::_()->getItemTable('yncredit_balance')->createRow();
		      $buyerBalance->user_id = $buyer->getIdentity();
		      $buyerBalance->save();
		    }
			if($buyerBalance -> current_credit >= abs($credits))
			{
			    $credit = $tableCredits -> getCreditByActionType($type_spend, $buyer);
			
			    $row = $table->createRow();
			    $row->user_id = $buyer->getIdentity();
			    $row->credit_id = $credit -> credit_id;
			    $row->type_id = $credit -> type_id;
			    $row->credit = $credits;
				if($item)
				{
					$row->object_type = $item -> getType();
			    	$row->object_id = $item -> getIdentity();
				}
				else 
				{
					$row->object_type = '';
			    	$row->object_id = 0;
				}
			    $row->body = $body;
			    $row->creation_date = new Zend_Db_Expr('NOW()');
			
			    $buyerBalance->saveCredits($credits, 'spend');
			    $row->save();
				// Commit
				$db -> commit();
			}
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
  	}
	
}
?>
