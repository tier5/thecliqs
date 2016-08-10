<?php
class Ynsocialads_Widget_AdsContentController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$params = array();
		$params['content_id'] = $content_id =  $this->getElement()->getIdentity();
		$this -> view -> content_id = $content_id;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$tableHiddens = Engine_Api::_() -> getItemTable('ynsocialads_hidden');
		$tableAdBlock = Engine_Api::_() -> getItemTable('ynsocialads_adblock');
		$adBlock = $tableAdBlock->fetchRow($tableAdBlock->select()->where('content_id = ?',$content_id));
		$ads_limit = $adBlock -> ads_limit;
		$this -> view -> is_ajax = $is_ajax = $adBlock -> ajax;
		if($viewer->getIdentity())
		{
			$items = Engine_Api::_() -> getItemTable('ynsocialads_ad') -> getAdsRender($params, $viewer->getIdentity(), 'yes');
		}
		else 
		{
			// Get ip address
		    $db = Engine_Db_Table::getDefaultAdapter();
		    $ipObj = new Engine_IP();
		    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
			$items = Engine_Api::_() -> getItemTable('ynsocialads_ad') -> getAdsRender($params, $ipExpr, 'no');
		}
		$arr = array();
		if(!count($items))
		{
			return $this->setNoRender();
		}
		if(!$is_ajax)
		{
			foreach($items as $item)
			{
				if($item->isAudience($viewer -> getIdentity()))
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
				if($count >= $ads_limit)
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
				//guest
				else 
				{
					// Get ip address
				    $db = Engine_Db_Table::getDefaultAdapter();
				    $ipObj = new Engine_IP();
				    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
					
					if(!($tableStatisticTable->checkUniqueViewByIP($ipExpr, $key, 'impression')))
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
					$stats -> IP = $ipExpr;
					$stats -> timestamp = date('Y-m-d H:i:s');
					$stats -> type = 'impression';
					$stats -> ad_id = $key;
					$stats -> save();
				}	
				$item -> save();
				$count++;
			}
			$this-> view -> ads = $arr_ads;
		}
	}
}
