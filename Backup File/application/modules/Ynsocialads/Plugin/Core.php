<?php
class Ynsocialads_Plugin_Core
{
    public function addActivity($event) {
        $payload = $event->getPayload();
        $subject = $payload['subject'];
        $object = $payload['object'];
        
        // Only for object=event
        if( $object instanceof Ynsocialads_Model_Ad) {
            
            $event->addResponse(array(
                'type' => 'ynsocialads_ad',
                'identity' => $object->getIdentity()
            ));
        }
    }
    
	public function onRenderLayoutDefault($event)
	{
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$limit = $settings->getSetting('ynsocialads_noadsshown');
		if(!$limit)
		{
			return;
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$view = $event -> getPayload();
		$params = Zend_Controller_Front::getInstance() -> getRequest() -> getParams();
		$tableHiddens = Engine_Api::_() -> getItemTable('ynsocialads_hidden');
		if($params['module'] == 'user' && $params['controller'] == 'index' && $params['action'] == 'home')
		{
			//check hidden
			
			$arr_owner_id = array();
			$arr_ad_id = array();
			$hiddenTable = Engine_Api::_()->getItemTable('ynsocialads_hidden');
			$select = $hiddenTable->select()->where('user_id = ?', $viewer->getIdentity());
			$rows = $hiddenTable->fetchAll($select);
			foreach ($rows as $row) {
				if($row['type'] == 'owner')
				{
					$arr_owner_id[] = $row['id'];
				}
				elseif($row['type'] == 'ad')
				{
					$arr_ad_id[] = $row['id'];
				}
			}
			
			$pos = $settings->getSetting('ynsocialads_posfeedads');
			$tableAds = Engine_Api::_()->getItemTable('ynsocialads_ad');
			$select = $tableAds -> select();
			$select -> where("ad_type = 'feed'");
			$select -> where("status = 'running'");
			
			if(!empty($arr_owner_id))
			{
				$select -> where('user_id NOT IN (?)', $arr_owner_id);
			}
			if(!empty($arr_ad_id))
			{
				$select -> where('ad_id NOT IN (?)', $arr_ad_id);
			}
			$ads_arr = $tableAds -> fetchAll($select);
			$arr = array();
			
			foreach($ads_arr as $item)
			{
				if($item -> isAudience($viewer->getIdentity()))
				{
					$package = $item -> getPackage();
					$base_order = 0;
					switch ($package->benefit_type) {
						case 'click':
								$base_order = ($item -> click_count / $item->benefit_total);
							break;
						case 'impression':
								$base_order = ($item -> impressions_count / $item->benefit_total);
							break;
						case 'day':
								$start_date = new DateTime($item -> start_date);
								$now   = new DateTime;
								$diff = date_diff($start_date, $now);
								$base_order = ($diff->format('%a') / $item->benefit_total);
						break;
					}
					$user_id = $item -> user_id;
					$ad_id = $item -> getIdentity();
					$id = $item->ad_id;
					$arr[$id] =  $base_order ;
				}
			}
				
			asort($arr);
			$arr_ads = array();
			$count = 0;
			foreach($arr as $key => $value)
			{
				if($count == $limit)
				{
					break;
				}
				$item = Engine_Api::_()->getItem('ynsocialads_ad',$key);
				$arr_ads[] = $item;
				
				//update view
				$tableStatisticTable = Engine_Api::_() -> getItemTable('ynsocialads_statistic');
				$tableTrackTable =  Engine_Api::_() -> getItemTable('ynsocialads_track');
				
				$date = new DateTime();
				$item -> last_view = $date->getTimestamp();
				
				$today = date("Y-m-d"); 
				//check if user login
				if($viewer->getIdentity())
				{
					// check if user has not view ad yet -> add reach count
					if(!($tableStatisticTable->checkUniqueViewByUserId($viewer->getIdentity(), $key, 'impression')))
					{
						$item -> reaches_count = $item -> reaches_count + 1;
						$item -> impressions_count = $item -> impressions_count + 1;
						
						if($track = $tableTrackTable->checkExistTrack($today, $key)){
							$track -> reaches = $track -> reaches + 1;
							$track -> impressions = $track -> impressions + 1;
							$track -> save();
						}
						else{
							$track = $tableTrackTable -> createRow();
							$track -> date = $today;
							$track -> ad_id = $key;
							$track -> reaches = 1;
							$track -> impressions = 1;
							$track -> save();
						}
					}	
					else {
						$item -> impressions_count = $item -> impressions_count + 1;
						
						if($track = $tableTrackTable->checkExistTrack($today, $key)){
							$track -> impressions = $track -> impressions + 1;
							$track -> save();
						}
						else{
							$track = $tableTrackTable -> createRow();
							$track -> date = $today;
							$track -> ad_id = $key;
							$track -> impressions = 1;
							$track -> save();
						}
					}
					//update view statistic
					$stats = $tableStatisticTable -> createRow();
					$stats -> user_id = $viewer->getIdentity();
					$stats -> timestamp = date('Y-m-d H:i:s');
					$stats -> type = 'impression';
					$stats -> ad_id = $key;
					$stats -> save();
				}
				$item -> save();
				$count++;
			}
			$this-> view -> ads = $arr_ads;
			$script = $view -> partial('_feedRender.tpl', 'ynsocialads', array('ads_arr' => $arr_ads,'pos'=>$pos));
			$view -> headScript() -> appendScript($script);
		}	
	}
	
}
