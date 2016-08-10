<?php
class Ynbusinesspages_Api_Core extends  Core_Api_Abstract {
	
	public function typeCreate($label) {
		$field = Engine_Api::_() -> fields() -> getField('1', 'ynbusinesspages_business');
		// Create new blank option
		$option = Engine_Api::_() -> fields() -> createOption('ynbusinesspages_business', $field, array('field_id' => $field -> field_id, 'label' => $label, ));
		// Get data
		$mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynbusinesspages_business');
		$metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynbusinesspages_business');
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynbusinesspages_business');
		// Flush cache
		$mapData -> getTable() -> flushCache();
		$metaData -> getTable() -> flushCache();
		$optionData -> getTable() -> flushCache();

		return $option -> option_id;
	}
	
	function getPhotoSpan($item, $type = null)
  	{
  		if (!is_null($type))
  		{
  			$photoUrl = $item->getPhotoUrl($type);
  		}
  		else
  		{
  			$photoUrl = $item->getPhotoUrl();
			if (!$photoUrl)
			{
				$photoUrl = $item->getPhotoUrl('thumb.profile');
			}
  		}

  		// set default photo
  		if (!$photoUrl)
		{
			$view = Zend_Registry::get("Zend_View");
			$photoUrl = $view->layout()->staticBaseUrl.'application/modules/Ynbusinesspages/externals/images/nophoto_business_thumb_profile.png';
		}

  		return '<a href = "'.$item -> getHref().'" title = "'.$item -> getTitle().'"><span class="ynbusinesspages-item-photo-cover" style="background-image:url('.$photoUrl.');"></span></a>';
  	}

    function getFeaturedSpan($item, $type = null)
    {
        $cover = $item -> getFirstCover();
        if (is_null($cover))
        {
        	$view = Zend_Registry::get("Zend_View");
            $photoUrl = $view->layout()->staticBaseUrl.'application/modules/Ynbusinesspages/externals/images/default_bussiness_featured.png';
        }
        else 
        {
        	$photoUrl = $cover -> getPhotoUrl();
        }
        return '<a href = "'.$item -> getHref().'" title = "'.$item -> getTitle().'"><span class="ynbusinesspages-item-photo-cover" style="background-image:url('.$photoUrl.');"></span></a>';
    }
	
	public function buyBusiness($business_id, $package_id)
	{
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
		$business -> last_payment_date = date("Y-m-d H:i:s");
		$business -> package_id = $package_id;
		$business -> status = 'pending';
		if(Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynbusinesspages_business', null, 'autoapprove') -> checkRequire())
		{
			//get package
			$package = Engine_Api::_() -> getItem('ynbusinesspages_package', $business -> package_id);
			if($package -> getIdentity())
			{
				if ($package->valid_amount == 0) {
					$business -> expiration_date = NULL;
					$business -> never_expire = 1;
				}
				else {
					if($package->valid_amount == 1)
					{
						$type = 'day';
					}
					else 
					{
						$type = 'days';
					}
					$now =  date("Y-m-d H:i:s");
					$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($package->valid_amount." ".$type));
					$business -> approved_date = $now;
					$business -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				}
			}	
			
			if(!$business -> approved)
			{
				//add activity
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($business -> getOwner(), $business, 'ynbusinesspages_business_create');
				if($action) {
					$activityApi->attachActivity($action, $business);
				}
			}
			$business -> approved = true;
			$business -> status = 'published';
		}
		$business -> save();  
	}
	
	public function featureBusiness($business_id, $feature_day_number)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$featureTable = Engine_Api::_() -> getDbTable('features', 'ynbusinesspages');
		$featureRow = $featureTable -> getFeatureRowByBusinessId($business_id);
		if($feature_day_number == 1)
		{
			$type = 'day';
		}
		else 
		{
			$type = 'days';
		}
		$now =  date("Y-m-d H:i:s");
		$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($feature_day_number." ".$type));
		if(!empty($featureRow)) //used to feature business
		{
			if($featureRow -> active == 1)
			{
				$expiration_date = date_add(date_create($featureRow->expiration_date),date_interval_create_from_date_string($feature_day_number." ".$type));
			}
			$featureRow -> modified_date = $now;
			$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			$featureRow -> active = 1;
			$featureRow -> save();  
		}
		else //first time
		{
			$featureRow = $featureTable -> createRow();
			$featureRow -> user_id = $viewer -> getIdentity();
			$featureRow -> business_id = $business_id;
			$featureRow -> creation_date = $now;
			$featureRow -> modified_date = $now;
			$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			$featureRow -> active = 1;
			$featureRow -> save();  
		}
		
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
		$business -> last_payment_date = date("Y-m-d H:i:s");
		$business -> featured = true;
		$business -> save();
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
			$class = str_replace('Payment', 'Ynbusinesspages', $gateway -> plugin);

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
    
    public function isAllowed($business, $action,  $user = null, $object = null)
    {
    	
    	if (is_null($business))
    	{
    		return false;
    	}
    	if (is_null($user))
    	{
    		$user = Engine_Api::_()->user()->getViewer();
    	}
    	
    	if (!is_object($user))
    	{
    		print_r($action); exit;
    	}
    	
    	//CHECKING NON-REGISTERED USERS
    	if (!$user->getIdentity())
    	{
			$list = $business -> getNonRegisteredList();
			$privacy = $list->privacy;
	    	if (in_array($action, array_keys($privacy)) && $privacy[$action] == '1')
	  		{
	  			return true;
	  		}
	  		else
	  		{
	  			return false;
	  		}		
    	}
    	if($business -> is_claimed)
		{
			return true;
		}
    	//CHECKING OWNER
	    if( ($user instanceof Core_Model_Item_Abstract && method_exists($business, 'isOwner') && $business->isOwner($user)) || $user === 'owner' )
	    {
			return true;
	    }

	    //CHECKING PERMISSION BELONG SPECIFIC LIST
	    $flag = true;
    	$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
    	$listTblName = $listTbl->info('name');
    	$itemTbl = Engine_Api::_()->getDbTable('listItems', 'ynbusinesspages');
    	$itemTblName = $itemTbl->info('name');
    	
    	$select = $listTbl -> select() -> setIntegrityCheck(false)
    	->from ($listTblName)
    	->join ($itemTblName, "$listTblName.list_id = $itemTblName.list_id")
    	->where ("$listTblName.owner_id = ? ", $business->getIdentity())
    	->where ("$itemTblName.child_id = ? ", $user->getIdentity())
    	->limit(1)
    	;
    	
    	$list = $listTbl->fetchRow($select);
    	if ($list)
    	{
    		
	    	if ( !$list->privacy || is_null($list->privacy))
	  		{
	  			$flag = false;
	  		}
	  		
	  		$privacy = $list->privacy;
	  		if (in_array($action, array_keys($privacy)))
	  		{
	  			if ($privacy[$action] == '2')
	  			{
	  				return true;
	  			}
	  			else if ($privacy[$action] == '1')
	  			{
	  				if (is_null($object))
	  				{
	  					return true;
	  				}
	  				else 
	  				{
	  					if( ($object instanceof Core_Model_Item_Abstract 
	  					&& method_exists($object, 'isOwner') 
	  					&& $object->isOwner($user)))
	  					{
	  						return true;
	  					}
	  					else 
	  					{
	  						$flag = false;
	  					}
	  				}
	  			}
	  			else 
	  			{
	  				$flag = false;
	  			}
	  		}
	  		else
	  		{
	  			$flag = false;
	  		}
		}
		else {
			$flag = false;
		}
  		
  		//CHECKING REGISTER USERS
  		if (!$flag && $user->getIdentity())
  		{
  		    
    		$list = $business -> getRegisteredList();
			$privacy = $list->privacy;
	    	if (in_array($action, array_keys($privacy)) && $privacy[$action] == '1')
	  		{
	  			return true;
	  		}
	  		else
	  		{
	  			return false;
	  		}
  		}
    }
    
    public function getAvailableCategories() {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        
        $compare_list = $myNamespace->compare_list;
		if(empty($compare_list))
			return array();
        $categories = array_keys($compare_list);
        $categories_str = implode(',', $categories);
        if (empty($categories)) return array();
        $categoryTbl = Engine_Api::_()->getItemTable('ynbusinesspages_category');
        $select = $categoryTbl -> select();
        $select -> where('category_id IN (?)', $categories);
        $select -> order(new Zend_Db_Expr("FIELD(category_id, $categories_str)"));
        $result = $categoryTbl->fetchAll($select);
        return $result;
    }
    
    public function getComparebusinessesOfCategory($category_id) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        $compare_busineses = $myNamespace->compare_list[$category_id];
        if (empty($myNamespace->compare_list[$category_id])) return array();
        $compare_busineses_arr = explode(',', $compare_busineses);
        if (empty($compare_busineses_arr)) return array();
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table -> select();
        $select -> where('business_id IN (?)', $compare_busineses_arr)->order(new Zend_Db_Expr("FIELD(business_id, $compare_busineses)"));
        $result = $table->fetchAll($select);
        return $result;
    }
    
    public function countComparebusinessesOfCategory($category_id) {
        return count($this->getComparebusinessesOfCategory($category_id));
    }
    
    public function removeComparebusiness($id, $category_id) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        $compare_list = $myNamespace->compare_list;
        if (is_null($category_id)) {
            foreach ($compare_list as $key => $value) {
                $compare_busineses = explode(',', $value);
                $pos = array_search($id, $compare_busineses);
                if (false !== $pos) {
                    unset($compare_busineses[$pos]);
                    if (!count($compare_busineses)) {
                        unset($myNamespace->compare_list[$key]);
                    }
                    else {
                        $compare_busineses_str = implode(',', $compare_busineses);
                        $myNamespace->compare_list[$key] = $compare_busineses_str;
                    }
                    return count($compare_busineses);
                }
            }
            return false;
        }
        else {
            $compare_busineses = $compare_list[$category_id];
            $compare_busineses = explode(',', $compare_busineses);
            $key = array_search($id, $compare_busineses);
            if (false !== $key) {
                unset($compare_busineses[$key]);
                if (empty($compare_busineses)) {
                    unset($myNamespace->compare_list[$category_id]);
                }
                else {
                    $compare_busineses_str = implode(',', $compare_busineses);
                    $myNamespace->compare_list[$category_id] = $compare_busineses_str;
                }
                return count($compare_busineses);
            }
            else return false;
        }
    }
    
    public function checkBusinessInCompare($id) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        $compare_list = $myNamespace->compare_list;
		if(!empty($compare_list))
		{
	        foreach ($compare_list as $value) {
	            $valueArr = explode(',', $value);
	            if (in_array($id, $valueArr)) return true;
	        }
		}
        return false;
    }
    
    public function addBusinessToCompare($id) {
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) return false;
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        $compare_list = $myNamespace->compare_list;
        $category_id = $business->getMainCategoryId();
        $count = 0;
        if (isset($compare_list[$category_id])) {
            $compare_busineses = $myNamespace->compare_list[$category_id];
            $compare_busineses_arr = explode(',', $compare_busineses);
            if (in_array($id, $compare_busineses_arr)) return false;
            else {
                $myNamespace->compare_list[$category_id] = $compare_busineses.','.$id;
                $count = count($compare_busineses_arr) + 1;
            }
        }
        else {
            $myNamespace->compare_list[$category_id] = $id;
            $count = 1;
        }
        return $count;
    }
    
    public function removeCompareCategory($id) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        unset($myNamespace->compare_list[$id]);
        return count($myNamespace->compare_list);
    }
    
    public function addCompareCategory($id) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        if(!isset($myNamespace->compare_list[$id])) {
            $myNamespace->compare_list[$id] = '';
        }
    }
    
    public function updateCompareCategory($category_id, $newArr) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');
        $myNamespace->compare_list[$category_id] = implode(',', $newArr);
    }
    
    public function getPrevCategory($category_id) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');       
        $compare_list = $myNamespace->compare_list;
        $categories = array_keys($compare_list);
        $index = array_search($category_id, $categories);
        if (!$index) {
            return false;
        }
        return $categories[$index-1];
    }
    
    public function getNextCategory($category_id) {
        $myNamespace = new Zend_Session_Namespace('ynbusinesspages_compare');       
        $compare_list = $myNamespace->compare_list;
        $categories = array_keys($compare_list);
        $index = array_search($category_id, $categories);
        if (($index === false) || ($index == (count($categories)-1))) {
            return false;
        }
        return $categories[$index+1];
    }
    
    public function renderBusinessPhone($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->phones = $business->phone;
        return $view -> render('render-phone.tpl');
    }
    
    public function renderBusinessWebsite($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->websites = $business->web_address;
        return $view -> render('render-website.tpl');
    }
    
    public function renderBusinessLocation($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->locations = $business->getAllLocations();
        return $view -> render('render-location.tpl');
    }
    
    public function renderBusinessRating($id, $li = true) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
		return $view -> partial('render-rating.tpl', 'ynbusinesspages', array('rating' => $business->getRating(), 'li' => $li ));
    }
    
    public function renderBusinessMemberCount($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->memberCount = $business->getMemberCount();
        return $view -> render('render-member-count.tpl');
    }
    
    public function renderBusinessFollowerCount($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->followerCount = $business->getFollowerCount();
        return $view -> render('render-follower-count.tpl');
    }
    
    public function renderBusinessReview($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->reviewCount = $business->getReviewCount();
        $view->latestReview = $business->getLatestReview();
        return $view -> render('render-review.tpl');
    }
    
    public function renderBusinessContact($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->business = $business;
        return $view -> render('render-contact.tpl');
    }
    
    public function renderBusinessAddress($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->locations = $business->getAllLocations();
        return $view -> render('render-address.tpl');
    }
    
    public function renderBusinessOperatingHour($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->operatingHours = $business->getOperatingHours();
        return $view -> render('render-operating-hour.tpl');
    }
    
    public function renderBusinessCustomField($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->business = $business;
        $view->category = $business->getMainCategory();
        return $view -> render('render-custom-field.tpl');
    }
    
    public function renderBusinessShortDescription($id) {
        $view = Zend_Registry::get('Zend_View');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
        if (!$business) {
            return;
        }
        $view->shortDescription = $business->getDescription();
        return $view -> render('render-short-description.tpl');
    }

    public function getSingers($album_id) {
        $as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
        $as_name = $as_table -> info('name');
        $s_table = Engine_Api::_() -> getDbTable('singers', 'mp3music');
        $s_name = $s_table -> info('name');
        
        $select = $as_table -> select()  -> where("$as_name.album_id = ?", $album_id) -> order('order ASC') ->limit(1) ;
        
        $albumSongs = $as_table -> fetchAll($select);
        foreach ($albumSongs as $albumSong)
        {
            $singer_id =  $albumSong ->singer_id;
            $other_singer = $albumSong -> other_singer; 
        }
        
        if($singer_id == 0)
        {
            if($other_singer != null)
              return $other_singer;
            else {
                return false;
            }
        }
        
        $select1 = $s_table ->  select() -> where("$s_name.singer_id = ?", $singer_id) ;
        
        $singers = $s_table -> fetchAll($select1);
        
        foreach ($singers as $singer)
        {
            return $title =  $singer ->title;       
        }
    }

	function isMobile()
	{
	    if (isset($_SERVER['HTTP_USER_AGENT']))
	    {
	    	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	      	if (preg_match('/(android|iphone|ipad|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent))
	      	{
	      		return true;
	      	}
			return false;
	     }
	     else
	     {
	        return false;
	     }
	}

    function isMobile2()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/(android|iphone|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|psp|treo)/i', $user_agent))
            {
                return true;
            }
            return false;
         }
         else
         {
            return false;
         }
    }
	
	function getCurrentHost()
	{
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$parts = parse_url($currentUrl);
		// use port if non default
		$port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';
		$path = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'default');
		$path = str_replace("index.php/", "", $path);
		$currentHostSite = $protocol . $parts['host'] . $port;
		return $currentHostSite;
	}

    public function getMyCompanies($user_id = null) {
        if (!$user_id) {
            $user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
        }
        if (!Engine_Api::_()->hasItemType('ynjobposting_company')) {
            return array();
        }
        $table = Engine_Api::_() -> getItemTable('ynjobposting_company');
        $select = $table -> select() -> where('user_id = ?', $user_id) -> where('status = ?', 'published') -> where('deleted = ?', 0);
        $rawData = $table -> fetchAll($select);
        $result = array();
        foreach ($rawData as $company) {
            $result[$company -> getIdentity()] = $company -> getTitle();
        }
        return $result;
    }
    
    public function getJobsByCompany($company_id = null, $business_id = null) {
        $jobs = array();
        if (is_null($company_id)) {
            return $jobs;
        }
        $company = Engine_Api::_()->getItem('ynjobposting_company', $company_id);
        if (!$company) {
            return $jobs;
        }
        $tableJob = Engine_Api::_() -> getItemTable('ynjobposting_job');
        $select = $tableJob -> select() -> where('company_id = ?', $company -> getIdentity())->where('status = ?', 'published');
        if (!is_null($business_id)) {
            $existJobs = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages')->getItemIdsMapping('ynjobposting_job', array('business_id' => $business_id));
            if (!empty($existJobs)) {
                $select->where('job_id NOT IN (?)', $existJobs);
            }
        }
        $result = $tableJob->fetchAll($select);
        foreach ($result as $job) {
            $jobs[$job->getIdentity()] = $job->getTitle();
        }
        return $jobs;
    }
	public function getFolderHref($p = array(), $folder) 
	{
		$params = array(
			'route' => 'ynbusinesspages_view_folder',
			'reset' => true,
			'business_id' => $p['parent_id'],
			'folder_id' => $folder->getIdentity(),
			'slug' => $folder->getSlug(),
		);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}

    public function isLogAsBusiness() {
        $business_session = new Zend_Session_Namespace('ynbusinesspages_business');
        $business_id = $business_session -> businessId;
        return ($business_id) ? true : false;
    }
}