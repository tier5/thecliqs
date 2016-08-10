<?php
class Yncredit_Plugin_Core
{
	/**
	 * Hook to earn credits for action user after create an item.
	 */
	public function onItemCreateAfter($event)
	{
		$payload = $event -> getPayload();
		if (!is_object($payload))
		{
			return;
		}
		if ($payload -> getType() == 'activity_action')
		{
			Engine_Api::_() -> yncredit() -> saveActionCredit($payload);
		}
		else
		{
			Engine_Api::_() -> yncredit() -> saveItemCredit($payload);
		}
	}
	
	/**
	 * Hook to earn credits when user update profile info
	 * 
	 */
	public function onUserUpdateBefore($event)
  	{
	    $user = $event->getPayload();
	    if (!($user instanceof User_Model_User)) {
	      return;
	    }
		$request = Zend_Controller_Front::getInstance()->getRequest();
	    if ($request && $request->getModuleName() == 'user' &&
	      $request->getControllerName() == 'edit' &&
	      ($request->getActionName() == 'profile' || $request->getActionName() == 'photo')
	    ) 
	    {
	      if($request->getActionName() != 'photo')
		  {
	      	 Engine_Api::_() -> yncredit() -> updateUserProfileCredits($user);
		  }
		 
		  // check profile completeness exist?
		  if (Engine_Api::_() -> hasModuleBootstrap('profile-completeness'))
		  {
		  	$emptyField = array();
            $filledField = array();

            $table = Engine_Api::_()->getDbtable('search', 'core');
            $db = $table->getAdapter();
            $select = $table->select()->setIntegrityCheck(false);
            $select->from(array('w' => 'engine4_profilecompleteness_weights'))
                    ->where('w.type_id = 0 AND w.field_id = 0');
            $row = $table->fetchRow($select);
            if ($user->photo_id != 0)
            {
                $filledField['photo'] = $row->weight;
            }
            else
            {
                $emptyField['photo'] = $row->weight;
            }

            $select = $table->select()->setIntegrityCheck(false);
            $select->from(array('v' => 'engine4_user_fields_values'))
                    ->where("v.item_id = ? AND v.field_id = 1", $user->getIdentity());
            $row = $table->fetchRow($select);
            $user_type = $row->value;

            $select = $table->select()->setIntegrityCheck(false);
            $select->from(array('w' => 'engine4_profilecompleteness_weights'))
                    ->where('w.type_id = ?', $user_type);
            $rows = $table->fetchAll($select);
            foreach ($rows as $row)
            {
                $select = $table->select()->setIntegrityCheck(false);
                $select->from(array('v' => 'engine4_user_fields_values'))
                        ->where('v.item_id = ?', $user->getIdentity())
                        ->where('v.field_id = ?', $row->field_id);
                $r = $table->fetchRow($select);
                $select = $table->select()->setIntegrityCheck(false);
                $select->from(array('map' => 'engine4_user_fields_maps'), array())
                        ->from(array('meta' => 'engine4_user_fields_meta'), array('meta.label'))
                        ->where('map.field_id = 1')
                        ->where('map.option_id = ?', $row->type_id)
                        ->where('map.child_id = ?', $row->field_id)
                        ->where('map.child_id = meta.field_id');
                $r1 = $table->fetchRow($select);

				if (is_object($r) && $r->value != '')
                {
                    $filledField[$r1->label] = $row->weight;
                }
                else if (is_object($r1))
                {
                    $emptyField[$r1->label] = $row->weight;
                }
            }

            $select = $table->select()->setIntegrityCheck(false);
            $select->from(array('s' => 'engine4_profilecompleteness_settings'));
            $row = $table->fetchRow($select);
            $percent_completed = $this->getPercentInfoProfileCompleted($filledField, $emptyField);

            if (empty($emptyField) || ($percent_completed == 100))
            {
            	// Earn credit when user have updated profile: 100%
                Engine_Api::_() -> yncredit() -> updateProfileCompletedCredits($user);
            }
		  }
	    }
  	}
	
	/**
	 * Hook to earn credit when user login to site
	 * 
	 */
	public function onUserLoginAfter($event)
	{
		$user = $event -> getPayload();
		Engine_Api::_() -> yncredit() -> saveItemCredit($user);
	}
	
	/**
	 * Hook to add "pay with credits" to Subscription, Publish Deal, Buy Deal, Publish contest.
	 * 
	 */
	public function onRenderLayoutDefault($event)
	{
		$view = $event -> getPayload();
		if (!($view instanceof Zend_View_Interface))
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$session = new Zend_Session_Namespace('Payment_Subscription');
		// Check viewer and user
		if (!$viewer || !$viewer -> getIdentity()) 
		{
			if (!empty($session -> user_id)) 
			{
				$viewer = Engine_Api::_() -> getItem('user', $session -> user_id);
			}
			// If no user, redirect to home?
			if (!$viewer || !$viewer -> getIdentity()) 
			{
				return;
			}
		}
		if( !Engine_Api::_()->authorization()->isAllowed('yncredit', $viewer, 'use_credit') )
	    {
	      return;
	    }
		$params = Zend_Controller_Front::getInstance() -> getRequest() -> getParams();
		$action_type = "";
		$item_id = 0;
		$id = 0;
		if (!empty($params['module']) && in_array($params['module'],array('payment', 'ynpayment')) 
			&& !empty($params['controller']) && $params['controller'] == 'subscription' 
			&& !empty($params['action']) && $params['action'] == 'gateway')
		{
			$action_type = 'upgrade_subscription';
		}
		if (!empty($params['module']) && $params['module'] == 'groupbuy' 
			&& !empty($params['controller']) && $params['controller'] == 'index' 
			&& !empty($params['action']) && $params['action'] == 'publish')
		{
			$deal = Engine_Api::_()->getItem('deal', $params['deal']);
			if($deal && $deal -> total_fee > 0)
			{
				$action_type = 'publish_deal';
				$item_id = $deal -> getIdentity();
			}
		}
		if (!empty($params['module']) && $params['module'] == 'yncontest' 
			&& !empty($params['controller']) && $params['controller'] == 'payment' 
			&& !empty($params['action']) && $params['action'] == 'method')
		{
			$contest = Engine_Api::_()->getItem('yncontest_contest', $params['contestId']);
			$security = $params['id'];
			if(!$security || !$contest)
			{
				return;	
			}
			$action_type = 'publish_contest';
			$item_id = $contest -> getIdentity();
			$id = $security;
		}
		if (!empty($params['module']) && $params['module'] == 'groupbuy' 
			&& !empty($params['controller']) && $params['controller'] == 'index' 
			&& !empty($params['action']) && $params['action'] == 'buy-deal')
		{
			$deal = Engine_Api::_()->getItem('deal', $params['deal']);
			$maxbought = $deal->getMaxBought($viewer);
			
			$settings = Engine_Api::_()->getDbTable('settings', 'core');
    		$defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
			$credits = ceil($deal->price * $defaultPrice * 1);
			$balance = Engine_Api::_()->getItem('yncredit_balance', $viewer -> getIdentity());
			$currentBalance = 0;
			if($balance)
			{
				$currentBalance = $balance->current_credit;
			}
			if($deal && $deal->price > 0 && $deal->status == 30 && $deal->published == 20 && ($maxbought > 0 || $deal -> max_bought == 0) && $currentBalance >= $credits)
			{
				$action_type = 'buy_deal';
				$item_id = $deal -> getIdentity();
			}
		}
		if($action_type)
		{
			// check permission
			$options = Engine_Api::_()->authorization()->getPermission($viewer -> level_id, 'yncredit', 'spend');
			if(!in_array($action_type, Zend_Json::decode($options)))
			{
				return;
			}
			$script = $view -> partial('_creditButtonJS.tpl', 'yncredit', array('action_type' => $action_type, 'item_id' => $item_id, 'id' => $id));
			$view -> headScript() -> appendScript($script);
		}
	}
	
	/**
	 * Hook to earn credits for affiliate user after payment subscription
	 * 
	 */
	public function onPaymentSubscriptionUpdateAfter($event)
	{
		try
		{
			$subs = $event -> getPayload();
			if (!($subs instanceof Payment_Model_Subscription))
			{
				return;
			}
			if ($subs -> status == 'active')
			{
				$gateway_id = $subs -> gateway_profile_id;
				$Orders = new Payment_Model_DbTable_Orders;
				$select = $Orders -> select() -> where("gateway_order_id = ?", $gateway_id) -> where("user_id = ?", $subs -> user_id);
				$result = $Orders -> fetchRow($select);
				if (($result) && $result -> state == 'complete')
				{
					$new_user_id = $subs -> user_id;
					$user = Engine_Api::_() -> ynaffiliate() -> getAssocId($new_user_id);
					if ($user)
					{
						$user = Engine_Api::_() -> getItem('user', $user -> user_id);
						$new_user = Engine_Api::_() -> getItem('user', $new_user_id);
						Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $subs->getTitle(), 'ynaffiliate_subscription', $new_user);
					}
					else
					{
						return;
					}
				}
				else
				{
					return;
				}
			}

		}
		catch(Exception $e)
		{
		}
	}
	
	/**
	 * Hook to earn credits for affiliate user
	 * modules support: Group Buy, Auction, Subscription, Store.
	 */
	public function onPaymentAfter($event)
	{
		$params = $event -> getPayload();
		$new_user_id = $params['user_id'];
		$item = Engine_Api::_() -> getItem('user', $new_user_id);
		$user = Engine_Api::_() -> ynaffiliate() -> getAssocId($new_user_id);
		if ($user)
		{
			$user = Engine_Api::_() -> getItem('user', $user -> user_id);
			$action_type = $params['rule_name'];
			
			// check rule enabled
			/*$Rules = new Ynaffiliate_Model_DbTable_Rules;
			$rule = $Rules -> getRuleByName($action_type);
			if ($rule)
			{
				$rule_id = $rule -> rule_id;
			}
			else
			{
				return;
			}
			$RulemapDetails = new Ynaffiliate_Model_DbTable_Rulemapdetails;
			$rulemaps = $RulemapDetails -> getRuleMapDetail('', $user -> user_id, $rule_id);
			if (count($rulemaps) > 0)
			{*/
				Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $item->getTitle(), 'ynaffiliate_'.$action_type, $item);
			/*}*/
		}
		else
		{
			return;
		}
	}

	/**
	 * Hook to earn credits for buyer when purchase successfully
	 * module support: Auction, Mp3 Music selling, Store, Group Buy
	 */
	public function onPurchaseItemAfter($event)
	{
		$item = null;
		$params = $event -> getPayload();
		$user = Engine_Api::_() -> user() -> getViewer();
		if(!empty($params['item_id']))
		{
			$item = Engine_Api::_() -> getItem($params['item_type'], $params['item_id']);
		}
		Engine_Api::_() -> yncredit() -> hookCustomEarnCredits($user, '', $params['rule_name'], $item);
	}
	
	/**
	 * Hook to earn credits when publish item successfully
	 * module support: Social Publisher
	 */
	public function onPublishItemAfter($event)
	{
		$item = null;
		$params = $event -> getPayload();
		$user = Engine_Api::_() -> getItem('user', $params['user_id']);
		if(!empty($params['item_id']))
		{
			$item = Engine_Api::_() -> getItem($params['item_type'], $params['item_id']);
		}
		Engine_Api::_() -> yncredit() -> hookCustomEarnCredits($user, '', $params['rule_name'], $item);
	}
	
	private function sum($filled, $empty)
    {
        $sum = 0;
        foreach ($filled as $key => $value)
        {
            $sum += $value;
        }
        foreach ($empty as $key => $value)
        {
            $sum += $value;
        }
        return $sum;
    }
	
	/**
	 * Get Sum percent complete update profile
	 * 
	 */
    private function getPercentInfoProfileCompleted($filled, $empty)
    {
        if (empty($empty))
        {
            return 100;
        }
        if (empty($filled))
        {
            return 0;
        }

        $sum = $this->sum($filled, $empty);
        $per = 0;

        foreach ($filled as $key => $value)
        {
            $per += $value / $sum;
        }
        return round($per * 100);
    }
}
