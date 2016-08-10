<?php
class Ynlistings_Api_Core extends  Core_Api_Abstract {
	
	public function buyListing($listing_id)
	{
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $listing_id);
		if($listing)
		{
			$auto_approve = !(Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynlistings_listing', null, 'approve') -> checkRequire());
			if($auto_approve)
			{
				$listing -> approved_date = date("Y-m-d H:i:s");
				$listing -> approved_status = 'approved';
			}
			$listing -> status = 'open';
			$listing -> save();
			
			if ($listing->approved_status == 'approved' && $listing->status == 'open') {
				//send notification to follower
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$owner = $listing -> getOwner();
				// get follower
				$tableFollow = Engine_Api::_() -> getItemTable('ynlistings_follow');
				$select = $tableFollow -> select() -> where('owner_id = ?', $owner -> getIdentity()) -> where('status = 1');
				$follower = $tableFollow -> fetchAll($select);
				foreach($follower as $row)
				{
					$person = Engine_Api::_()->getItem('user', $row -> user_id);
					$notifyApi -> addNotification($person, $owner, $listing, 'ynlistings_listing_follow');
				}
				
				//send notifications end add activity on feed
	     		$viewer = Engine_Api::_()->user()->getViewer();
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($owner, $owner, $listing, 'ynlistings_listing_approve');
				
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($owner, $listing, 'ynlistings_listing_create');
				if($action) {
					$activityApi->attachActivity($action, $listing);
				}
			}
		}	
	}
	
	public function featureListing($listing_id, $feature_day_number)
	{
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $listing_id);
		if($listing)
		{
			if($feature_day_number == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$now =  date("Y-m-d H:i:s");
			//set feature expire if listing approved by admin
			if ($listing->approved_status == 'approved')
			{
				$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($feature_day_number." ".$type));
				$listing -> feature_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			}
			$listing -> featured = true;
			$listing -> feature_day_number = $feature_day_number;
			$listing -> save();
		}
	}
	
	public function checkAndUpdateStatus($item)
	{
		//check end date
		$end_date = strtotime($item -> end_date);
		$current_date = strtotime(date("Y-m-d H:i:s"));
		if($end_date > 0)
		{
			if($current_date > $end_date)
			{
				$item -> status = 'expired';
				$item -> save();
			}
		}
		//check feature
		if($item -> featured == 1)
		{
			$expiration_date = strtotime($item -> feature_expiration_date);
			//check end date
			if($expiration_date > 0)
			{
				if($current_date > $expiration_date)
				{
					$item -> featured = 0;
					$item -> save();
				}
			}
		}
	}
	
	public function typeCreate($label) {
		$field = Engine_Api::_() -> fields() -> getField('1', 'ynlistings_listing');
		// Create new blank option
		$option = Engine_Api::_() -> fields() -> createOption('ynlistings_listing', $field, array('field_id' => $field -> field_id, 'label' => $label, ));
		// Get data
		$mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynlistings_listing');
		$metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynlistings_listing');
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynlistings_listing');
		// Flush cache
		$mapData -> getTable() -> flushCache();
		$metaData -> getTable() -> flushCache();
		$optionData -> getTable() -> flushCache();

		return $option -> option_id;
	}
	
	public function getGateway($gateway_id)
	{
		return $this -> getPlugin($gateway_id) -> getGateway();
	}
	
	public function getPlugin($gateway_id)
	{
		if (null === $this -> _plugin)
		{
			if (null == ($gateway = Engine_Api::_() -> getItem('payment_gateway', $gateway_id)))
			{
				return null;
			}
			Engine_Loader::loadClass($gateway -> plugin);
			if (!class_exists($gateway -> plugin))
			{
				return null;
			}
			$class = str_replace('Payment', 'Ynlistings', $gateway -> plugin);

			Engine_Loader::loadClass($class);
			if (!class_exists($class))
			{
				return null;
			}

			$plugin = new $class($gateway);
			if (!($plugin instanceof Engine_Payment_Plugin_Abstract))
			{
				throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
			}
			$this -> _plugin = $plugin;
		}
		return $this -> _plugin;
	}
	
	public function checkYouNetPlugin($name) {
		$table = Engine_Api::_ ()->getDbTable ( 'modules', 'core' );
		$select = $table->select ()->where ( 'name = ?', $name )->where ( 'enabled  = 1' );
		$result = $table->fetchRow ( $select );
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
    
    public function subPhrase($string, $length = 0) {
        if (strlen ( $string ) <= $length)
            return $string;
        $pos = $length;
        for($i = $length - 1; $i >= 0; $i --) {
            if ($string [$i] == " ") {
                $pos = $i + 1;
                break;
            }
        }
        return substr ( $string, 0, $pos ) . "...";
    }
}
