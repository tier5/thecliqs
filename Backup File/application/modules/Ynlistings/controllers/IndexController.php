<?php
class Ynlistings_IndexController extends Core_Controller_Action_Standard {
    public function init() {
    }
    
	public function indexAction() {
	//	Setting to use landing page.
       $this->_helper->content->setNoRender()->setEnabled();
	}
	
	public function followAction()
	{
		// Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$owner_id = $this->_getParam('owner_id');
		$followTable = Engine_Api::_() -> getItemTable('ynlistings_follow');
		$row = $followTable -> getRow($viewer->getIdentity(), $owner_id);
		if($row)
		{
			if($this->_getParam('status') == 1)
			{
				$row -> status = 1;
				$row -> save();
				echo Zend_Json::encode(array('json' => 'true'));
        		return true;
			}
			else 
			{
				$row -> status = 0;
				$row -> save();
				 echo Zend_Json::encode(array('json' => 'false'));
       			 return true;
			}
		}
		else 
		{
			$new_row = $followTable -> createRow();
			$new_row -> user_id = $viewer->getIdentity();
			$new_row -> owner_id = $owner_id;
			$new_row -> status = 1; 
			$new_row -> save();
            $owner = Engine_Api::_()->getItem('user', $owner_id);
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($owner, $viewer, $owner, 'ynlistings_listing_follow_owner');
        	echo Zend_Json::encode(array('json' => 'true'));
        	return true;
		}
	}
	
	public function mobileviewAction() {
		
	    if (!$this -> _helper -> requireAuth() -> setAuthParams('ynlistings_listing', null, 'view') -> isValid())
         {
           return $this -> _helper -> requireAuth() -> forward();
		 }
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$id = $this->_getParam('id');
	    if( null !== $id ){
	        $subject = Engine_Api::_()->getItem('ynlistings_listing', $this->_getParam('id'));
	        if( $subject && $subject->getIdentity() )
	        {
	          Engine_Api::_()->core()->setSubject($subject);
	        }
	        else return $this->_helper->requireSubject()->forward();
            if (!$subject->isViewable()) {
                return $this -> _helper -> requireAuth() -> forward();
            }
			if(($subject-> status !='open') || ($subject -> approved_status !='approved'))
			{
				if($subject -> user_id != $viewer -> getIdentity() && !($viewer->isAdmin()))
				{
					return $this -> _helper -> requireAuth() -> forward();
				}
			}
	    }
		else
		{
			$this->_helper->requireSubject()->forward();
		}
		
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
		Engine_Api::_()->ynlistings() ->checkAndUpdateStatus($listing);
		
		//get photos
		$this -> view -> album = $album = $subject -> getSingletonAlbum();
		$this -> view -> photos = $photos = $album -> getCollectiblesPaginator();
		$photos -> setCurrentPageNumber(1);
		$photos -> setItemCountPerPage(100);
		//get videos
		if(Engine_Api::_()->ynlistings()->checkYouNetPlugin('video') || Engine_Api::_()->ynlistings()->checkYouNetPlugin('ynvideo'))
		{
			$tableMappings = Engine_Api::_()->getItemTable('ynlistings_mapping');
			$params['listing_id'] = $subject -> getIdentity();
			$this -> view -> videos = $videos = $tableMappings -> getVideosPaginator($params);
			$videos -> setCurrentPageNumber(1);
			$videos -> setItemCountPerPage(100);
		}
        
		$this -> _helper -> content	-> setEnabled();
		$this -> view -> listing = $subject;
        
        if (!$subject->isOwner($viewer) && $subject->status == 'open' && $subject->approved_status == 'approved') {
            $now = new DateTime();
            $subject->view_time = $now->format('y-m-d H:i:s');
            $subject->view_count++;
            $subject->save();
        }
        
        $can_report = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'report') -> checkRequire();
        if ($subject->isOwner($viewer)) {
            $can_report = false;
        }
        $this->view->can_report = $can_report;
        
        $can_review = false;
        if (!$subject->isOwner($viewer)) {
            $can_review = $can_rate = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'rate') -> checkRequire();
            if ($can_review) {
                $reviewTable = Engine_Api::_()->getItemTable('ynlistings_review');
                $reviewSelect = $reviewTable->select()
                ->where('listing_id = ?', $subject->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity());  
                $my_review = $reviewTable->fetchRow($reviewSelect);
                if ($my_review) {
                    $this->view->has_review = true;
                    $can_review = false;
                }
            }
        }
        $this->view->can_review = $can_review;
	}

	public function viewAction() {
		
		
	    if (!$this -> _helper -> requireAuth() -> setAuthParams('ynlistings_listing', null, 'view') -> isValid())
         {
           return $this -> _helper -> requireAuth() -> forward();
		 }
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$id = $this->_getParam('id');
	    if( null !== $id ){
	        $subject = Engine_Api::_()->getItem('ynlistings_listing', $this->_getParam('id'));
	        if( $subject && $subject->getIdentity() )
	        {
	          Engine_Api::_()->core()->setSubject($subject);
	        }
	        else return $this->_helper->requireSubject()->forward();
            if (!$subject->isViewable()) {
                return $this -> _helper -> requireAuth() -> forward();
            }
			if(($subject-> status !='open') || ($subject -> approved_status !='approved'))
			{
				if($subject -> user_id != $viewer -> getIdentity() && !($viewer->isAdmin()))
				{
					return $this -> _helper -> requireAuth() -> forward();
				}
			}
            
	    }
		else
		{
			$this->_helper->requireSubject()->forward();
		}
		
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
		Engine_Api::_()->ynlistings() ->checkAndUpdateStatus($listing);
		
		//get photos
		$this -> view -> album = $album = $subject -> getSingletonAlbum();
		$this -> view -> photos = $photos = $album -> getCollectiblesPaginator();
		$photos -> setCurrentPageNumber(1);
		$photos -> setItemCountPerPage(100);
		//get videos
		if(Engine_Api::_()->ynlistings()->checkYouNetPlugin('video') || Engine_Api::_()->ynlistings()->checkYouNetPlugin('ynvideo'))
		{
			$tableMappings = Engine_Api::_()->getItemTable('ynlistings_mapping');
			$params['listing_id'] = $subject -> getIdentity();
			$this -> view -> videos = $videos = $tableMappings -> getVideosPaginator($params);
			$videos -> setCurrentPageNumber(1);
			$videos -> setItemCountPerPage(100);
		}
        
		$this -> _helper -> content	-> setEnabled();
		$this -> view -> listing = $subject;
        
        if (!$subject->isOwner($viewer) && $subject->status == 'open' && $subject->approved_status == 'approved') {
            $now = new DateTime();
            $subject->view_time = $now->format('y-m-d H:i:s');
            $subject->view_count++;
            $subject->save();
        }
        
        $can_report = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'report') -> checkRequire();
        if ($subject->isOwner($viewer)) {
            $can_report = false;
        }
        $this->view->can_report = $can_report;
        
        $can_review = false;
        if (!$subject->isOwner($viewer)) {
            $can_review = $can_rate = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'rate') -> checkRequire();
            if ($can_review) {
                $reviewTable = Engine_Api::_()->getItemTable('ynlistings_review');
                $reviewSelect = $reviewTable->select()
                ->where('listing_id = ?', $subject->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity());  
                $my_review = $reviewTable->fetchRow($reviewSelect);
                if ($my_review) {
                    $this->view->has_review = true;
                    $can_review = false;
                }
            }
        }
        $this->view->can_review = $can_review;
	}

	public function exportAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> form = $form = new Ynlistings_Form_Export();
		// Check method and data validity.
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$tableListings = Engine_Api::_()->getItemTable('ynlistings_listing');
		$select = $tableListings -> select() -> where('user_id = ?', $viewer->getIdentity());
		$listings = $tableListings -> fetchAll($select);
		if(count($listings) == 0)
		{
			return;
		}
		//export to file
        $filename = "/tmp/csv-" . date( "m-d-Y" ) . ".csv";
        $realPath = realpath( $filename );
        if ( false === $realPath )
        {
            touch( $filename );
            chmod( $filename, 0777 );
        }
        $filename = realpath( $filename );
        $handle = fopen( $filename, "w" );
        
        foreach ( $listings as $item )
        {
            
        	//Populate Tag
			$tagStr = '';
			foreach ($item->tags()->getTagMaps() as $tagMap)
			{
				$tag = $tagMap -> getTag();
				if (!isset($tag -> text))
					continue;
				if ('' !== $tagStr)
					$tagStr .= ', ';
				$tagStr .= $tag -> text;
			}
			
			$end_date = strtotime($item->end_date);
			if($end_date <= 0)
			{
				$end_date = "";
			}
			else 
			{
				$oldTz = date_default_timezone_get();
			    date_default_timezone_set($viewer->timezone);
			    $end_date = date('Y-m-d H:i:s', $end_date);
			    date_default_timezone_set($oldTz);
			}
            $finalData[] = array(
                $item -> title,
                $tagStr,
                $item -> short_description, 
                $item -> description,
                $item -> about_us,
                $item -> price,
                $item -> location,
                $item -> category_id,
                $end_date,
                $item->getOwner()->email,
            );
        }
		$type_export = $this ->_getParam('type_export');
		if($type_export == 'xls')
		{
			//Export to xls file
			$xls = new Ynlistings_Api_ExcelExport('UTF-8', false, 'mylistings');
			$xls->addArray($finalData);
			$xls->generateXML('mylistings');
		}
		elseif($type_export == 'csv')
		{
			//Export to csv file
			foreach ( $finalData as $finalRow )
	        {
	            fputcsv( $handle, $finalRow);
	        }
	        fclose($handle);
	        $this->_helper->layout->disableLayout();
	        $this->_helper->viewRenderer->setNoRender();
	        $csvname = 'mylistings.csv';
	        $this->getResponse()->setRawHeader( "Content-Type: application/csv; charset=UTF-8" )
	            ->setRawHeader( "Content-Disposition: attachment; filename=".$csvname )
	            ->setRawHeader( "Content-Transfer-Encoding: binary" )
	            ->setRawHeader( "Expires: 0" )
	            ->setRawHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0" )
	            ->setRawHeader( "Pragma: public" )
	            ->setRawHeader( "Content-Length: " . filesize( $filename ) )
	            ->sendResponse();
	        // fix for print out data
	        readfile($filename);
	        unlink($filename);
		}
		$this -> view -> status = TRUE;
		exit();
	}
	
	public function importAction() {
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $form = new Ynlistings_Form_Import();
        
        //get max import listings settings
        $this->view->max_import = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynlistings_max_listings', 100);
		if(!Engine_Api::_()->hasItemType('video'))
		{
			$form -> removeElement('upload_videos');
		}
		$this->view->has_video = Engine_Api::_()->hasItemType('video');
	}
	
	public function manageAction() {
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> _helper -> content
			-> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
		$this -> view -> viewer = $viewer;
		$params = $this -> _getAllParams();
		$this -> view -> formValues = $params;
		$params['user_id'] = $viewer -> getIdentity();
        $params['direction'] = 'DESC';

		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynlistings_listing') -> getListingsPaginator($params);
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		$paginator -> setItemCountPerPage(10);
        
        $this->view->can_import = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'import') -> checkRequire();
        $this->view->can_export = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'export') -> checkRequire();
        $this->view->can_select_theme = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'select_theme') -> checkRequire();
	}
    
    public function browseAction() {
        // Setting to use landing page.
        $this->_helper->content->setNoRender()->setEnabled();
    }
    
	public function placeOrderAction()
	{
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $this ->_getParam('id'));
		$category = Engine_Api::_() -> getItem('ynlistings_category', $listing -> category_id);
		if($listing -> category_id == 0 || (!$category))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'edit',
					'id' => $listing -> getIdentity(),
				), 'ynlistings_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please choose category before publishing listing...'))
			));
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($listing -> status != 'draft')
		{
			return $this -> _redirector();
		}
		if ($listing -> user_id != $viewer -> getIdentity())
		{
			return $this -> _redirector();
		}
		
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
		
		$publish_fee = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynlistings_listing', 'publish_fee');
	    if ($publish_fee== null) {
			$row = $permissionsTable->fetchRow($permissionsTable->select()
			->where('level_id = ?', $viewer->level_id)
			->where('type = ?', 'ynlistings_listing')
			->where('name = ?', 'publish_fee'));
			if ($row) {
			$publish_fee= $row->value;
			}
		}
		
		$feature_fee = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynlistings_listing', 'feature_fee');
	    if ($feature_fee== null) {
			$row = $permissionsTable->fetchRow($permissionsTable->select()
			->where('level_id = ?', $viewer->level_id)
			->where('type = ?', 'ynlistings_listing')
			->where('name = ?', 'feature_fee'));
			if ($row) {
			$feature_fee= $row->value;
			}
		}
		
		$feature_period = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynlistings_listing', 'feature_period');
	    if ($feature_period == null) {
			$row = $permissionsTable->fetchRow($permissionsTable->select()
			->where('level_id = ?', $viewer->level_id)
			->where('type = ?', 'ynlistings_listing')
			->where('name = ?', 'feature_period'));
			if ($row) {
				$feature_period= $row->value;
			}
		}
		
		if($publish_fee == 0 && $feature_fee == 0)
		{
			
			Engine_Api::_() -> ynlistings() -> buyListing($listing -> getIdentity());
			Engine_Api::_() -> ynlistings() -> featureListing($listing -> getIdentity(), $feature_period);
			
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'view',
					'id' => $listing -> getIdentity()
				), 'ynlistings_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Publish listing successfully...'))
			));
		}
		
		//Credit
		//check permission
		// Get level id
		$id = $viewer -> level_id;
		if ($this -> _helper -> requireAuth() -> setAuthParams('ynlistings_listing', null, 'publish_credit') -> checkRequire())
		{
			$allowPayCredit = 0;
			$credit_enable = Engine_Api::_() -> ynlistings() -> checkYouNetPlugin('yncredit');
			if ($credit_enable)
			{
				$typeTbl = Engine_Api::_() -> getDbTable("types", "yncredit");
				$select = $typeTbl -> select() -> where("module = 'yncredit'") -> where("action_type = 'publish_listings'") -> limit(1);
				$type_spend = $typeTbl -> fetchRow($select);
				if ($type_spend)
				{
					$creditTbl = Engine_Api::_() -> getDbTable("credits", "yncredit");
					$select = $creditTbl -> select() -> where("level_id = ? ", $id) -> where("type_id = ?", $type_spend -> type_id) -> limit(1);
					$spend_credit = $creditTbl -> fetchRow($select);
					if ($spend_credit)
					{
						$allowPayCredit = 1;
					}
				}
			}
			$this -> view -> allowPayCredit = $allowPayCredit;
		};
		$this -> view -> listing = $listing;
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		if ((!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit))
		{
			return $this -> _redirector();
		}
		$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynlistings');
		if ($row = $ordersTable -> getLastPendingOrder())
		{
			$row -> delete();
		}
		$db = $ordersTable -> getAdapter();
		$db -> beginTransaction();
		
		$this->view->publish_fee = $publish_fee;
		$this->view->feature_fee = $feature_fee;
		$this->view->feature_period = $feature_period;
		$this->view->currency =  $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
		try
		{
			$ordersTable -> insert(array(
				'user_id' => $viewer -> getIdentity(),
				'creation_date' => new Zend_Db_Expr('NOW()'),
				'listing_id' => $listing -> getIdentity(),
				'price' => $publish_fee,
				'currency' => $currency
			));
			// Commit
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		// Gateways
		$gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
		$gateways = $gatewayTable -> fetchAll($gatewaySelect);

		$gatewayPlugins = array();
		foreach ($gateways as $gateway)
		{
			$gatewayPlugins[] = array(
				'gateway' => $gateway,
				'plugin' => $gateway -> getGateway()
			);
		}
		$this -> view -> gateways = $gatewayPlugins;
	}

	public function updateOrderAction()
	{
		$feature = $this -> _getParam('feature');
		$type = $this -> _getParam('type');
		$id = $this -> _getParam('id');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//get listing
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $id);
		if(!$listing)
		{
			return $this -> _redirector();
		}
		
		//get order
		$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynlistings');
		$order = $ordersTable -> getLastPendingOrder();
		if (!$order)
		{
			return $this -> _redirector();
		}
		
		//if publish fee = 0 && user do not check feature
		if($order -> price == 0 && !$feature)
		{
			Engine_Api::_() -> ynlistings() -> buyListing($listing -> getIdentity());
			
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'view',
					'id' => $listing -> getIdentity()
				), 'ynlistings_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Publish listing successfully...'))
			));
		}
		
		
		if (isset($type))
		{
			switch ($type)
			{
				case 'paycredit' :
					
					if(isset($feature))
					{
						$order -> price +=  $feature;
						$order -> featured = 1;
					}
					$order -> save();
					
					return $this -> _forward('success', 'utility', 'core', array(
						'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
							'controller' => 'index',
							'action' => 'pay-credit',
							'item_id' => $id,
							'order_id' => $order -> getIdentity(),
						), 'ynlistings_general', true),
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
					));
					break;
				default :
					break;
			}
		}
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $id);

		$gateway_id = $this -> _getParam('gateway_id', 0);
		if (!$gateway_id)
		{
			return $this -> _redirector();
		}

		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
		$gateway = $gatewayTable -> fetchRow($gatewaySelect);
		if (!$gateway)
		{
			return $this -> _redirector();
		}
		
		if(isset($feature))
		{
			$feature_period = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynlistings_listing', 'feature_period');
		    if ($feature_period == null) {
				$row = $permissionsTable->fetchRow($permissionsTable->select()
				->where('level_id = ?', $viewer->level_id)
				->where('type = ?', 'ynlistings_listing')
				->where('name = ?', 'feature_period'));
				if ($row) {
					$feature_period= $row->value;
				}
			}
			$order -> feature_day_number = $feature_period;
			$order -> price +=  $feature;
			$order -> featured = 1;
		}
		$order -> gateway_id = $gateway -> getIdentity();
		$order -> save();

		$this -> view -> status = true;
		if (!in_array($gateway -> title, array(
			'2Checkout',
			'PayPal'
		)))
		{
			$this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'action' => 'process-advanced',
					'order_id' => $order -> getIdentity(),
					'm' => 'ynlistings',
					'cancel_route' => 'ynlistings_transaction',
					'return_route' => 'ynlistings_transaction',
				), 'ynpayment_paypackage', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		else
		{
			$this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'transaction',
					'action' => 'process',
					'order_id' => $order -> getIdentity(),
				), 'ynlistings_extended', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
	}
	
	public function payCreditAction()
    {
        $credit_enable = Engine_Api::_() -> ynlistings() -> checkYouNetPlugin('yncredit');
        if (!$credit_enable)
        {
            return $this -> _redirector();
        }
        $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'publish_listings'")->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            return $this -> _redirector();
        }
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
        $listing = Engine_Api::_() -> getItem('ynlistings_listing', $item_id);
        $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
        $item = array();
        $item = Engine_Api::_() -> getItem('ynlistings_listing', $item_id);
        if($listing->status !='draft')
        {
            return $this -> _redirector();
        }
        if($listing->user_id != $viewer->getIdentity())
        {
            return $this -> _redirector();
        }
        // Check if it exists
        if (!$item) 
        {
          $this-> view -> message = Zend_Registry::get('Zend_View')->translate('Please choose one now below.');
          return;
        }
        $cancel_url = Zend_Controller_Front::getInstance()->getRouter()
                ->assemble(
                  array(
                    'action' => 'place-order',
                    'id' => $item -> getIdentity()
                  ), 'ynlistings_general', true);
	    //publish fee
		$order = Engine_Api::_()->getItem('ynlistings_order', $this->_getParam('order_id'));
		if(!$order)
        {
            return $this -> _redirector();
        }
        $this -> view -> total_pay = $total_pay =  $order -> price ;    
        $credits = ceil(($total_pay * $defaultPrice * $numbers));
        $this -> view -> item = $item;
        $this -> view -> cancel_url = $cancel_url;
        $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
        if (!$balance) 
        {
          $currentBalance = 0;
        } else 
        {
          $currentBalance = $balance->current_credit;
        }
        $this->view->currentBalance = $currentBalance;
        $this->view->credits = $credits;
        $this->view->enoughCredits = $this->_checkEnoughCredits($credits);
    
        // Check method
        if (!$this->getRequest()->isPost()) 
        {
          return;
        }
    	
		//get listing
        $listing = Engine_Api::_() -> getItem('ynlistings_listing', $item_id);
        
		//buy listing
		Engine_Api::_() -> ynlistings() -> buyListing($listing -> getIdentity());
				
		//check if feature
		if($order->featured == 1)
		{
			Engine_Api::_() -> ynlistings() -> featureListing($listing -> getIdentity(), $order -> feature_day_number);
		}
		
		//insert transaction
        $transactionTable = Engine_Api::_() -> getDbTable('transactions', 'ynlistings');
        $transAd = $transactionTable -> createRow();
        $transAd -> status = 'completed';
        $transAd -> gateway_id = '-3';
        $transAd -> amount = $total_pay;
        $transAd -> currency =  Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $transAd -> listing_id = $listing -> getIdentity();
        $transAd -> user_id = $listing -> user_id;
		$transAd -> creation_date = date("Y-m-d");
		$transAd -> description = 'Publish Listing';
        $transAd -> save();
		
		//spend credit
		Engine_Api::_()->yncredit()-> spendCredits($listing->getOwner(), (-1) * $credits, $listing->getTitle(), 'publish_listings', $listing);
			
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'view', 'id' => $listing -> getIdentity()), 'ynlistings_general', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }
	
	public function editAction()
	{
	    
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		$this -> _helper -> content
		-> setEnabled();
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
        
		// Check authorization to edit listing.
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynlistings_listing', null, 'edit') -> isValid())
			return;
        
		$listing_id = $this->_getParam('id');
		if(empty($listing_id))
		{
			return;
		}
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $listing_id);
        
        if (!$listing->isEditable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to edit this listing.';
            return;    
        }
        
	    $tableCategory = Engine_Api::_()->getItemTable('ynlistings_category');
		$category_id = $this -> _getParam('category_id', $listing->category_id);
		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynlistings_category', $category_id);
		if(!$category)
		{
			$categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
			unset($categories[0]);
			$category = $categories[1];
		}
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynlistings_listing');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
		{
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array(
				'topLevelId' => $profileTypeField -> field_id,
				'topLevelValue' => $category -> option_id,
			);
			if($category -> use_parent_category == 1)
			{
				$main_parent_category = Engine_Api::_()->getItem('ynlistings_category', $category -> getIdentity()) -> getParentCategoryLevel1();
				$category_parent = Engine_Api::_() -> getItem('ynlistings_category', $main_parent_category -> getIdentity());
				$formArgsParent = array(
					'topLevelId' => $profileTypeField -> field_id,
					'topLevelValue' => $category_parent -> option_id,
				);
			}
		}
        $can_select_theme = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'select_theme') -> checkRequire();
		if($category -> use_parent_category == 1)
		{
			$this -> view -> form = $form = new Ynlistings_Form_Edit( array(
				'category' => $category,
				'formArgs' => $formArgs,
				'formArgsParent' => $formArgsParent,
				'theme' =>  $listing->theme,
				'item' => $listing,
				'canSelectTheme' => $can_select_theme
			));
			if($listing -> category_id != $category_id)
			{
				$this -> view -> switchCategory = '1';
			}
			$this -> view -> theme = $listing->theme;
		}
		else {
			$this -> view -> form = $form = new Ynlistings_Form_Edit( array(
				'category' => $category,
				'formArgs' => $formArgs,
				'theme' =>  $listing->theme,
				'item' => $listing,
				'canSelectTheme' => $can_select_theme
			));
			if($listing -> category_id != $category_id)
			{
				$this -> view -> switchCategory = '1';
			}
			$this -> view -> theme = $listing->theme;
		}
		if(!Engine_Api::_()->hasItemType('video'))
		{
			$form -> removeElement('upload_videos');
			$form -> removeElement('to');
		}
		
		// Populate category list.
		$categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item)
		{
			$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
		}
		if (count($form -> category_id -> getMultiOptions()) < 1)
		{
			$form -> removeElement('category_id');
		}
		if($listing -> status != 'draft')
		{
			$form -> removeElement('submit_button');
		}
		
		//populate end date?
		$end_date = $listing -> end_date;
		$end_date = strtotime($end_date);
        
		if($end_date > 0)
		{
			$form -> is_end -> setValue(true);
			$this -> view -> is_end = true;
		}
		else{
			$form -> is_end -> setValue(false);
			$this -> view -> is_end = false;
		}
		
		//populate location
		$form -> populate(array('location_address' => $listing->location));
		$form -> populate(array('lat' => $listing->latitude));
		$form -> populate(array('long' => $listing->longitude));
		
		//Populate Tag
		$tagStr = '';
		foreach ($listing->tags()->getTagMaps() as $tagMap)
		{
			$tag = $tagMap -> getTag();
			if (!isset($tag -> text))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag -> text;
		}
		$form -> populate(array('tags' => $tagStr, ));
		
		//populate currency
		$supportedCurrencies = array();
		$gateways = array();
		$gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		foreach ($gatewaysTable->fetchAll(/*array('enabled = ?' => 1)*/) as $gateway)
		{
			$gateways[$gateway -> gateway_id] = $gateway -> title;
			$gatewayObject = $gateway -> getGateway();
			$currencies = $gatewayObject -> getSupportedCurrencies();
			if (empty($currencies))
			{
				continue;
			}
			$supportedCurrencyIndex[$gateway -> title] = $currencies;
			if (empty($fullySupportedCurrencies))
			{
				$fullySupportedCurrencies = $currencies;
			}
			else
			{
				$fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
			}
			$supportedCurrencies = array_merge($supportedCurrencies, $currencies);
		}
		$supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

		$translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
		$fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
		$supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
		
		$form -> getElement('currency') -> setMultiOptions(array(
			'Please select one' => array_merge($fullySupportedCurrencies, $supportedCurrencies)
		));
        
		$form -> populate($listing -> toArray());
		//populate date
		if($end_date > 0)
		{
		    $end = strtotime($listing->end_date);
		    $oldTz = date_default_timezone_get();
		    date_default_timezone_set($viewer->timezone);
		    $end = date('Y-m-d H:i:s', $end);
		    date_default_timezone_set($oldTz);
			$form -> end_date -> setvalue($end);
		}
        
		$form -> category_id -> setValue($category_id);
		$submit_button = $this -> _getParam('submit_button');
		$edit_button = $this -> _getParam('edit_button');
		
        //populate auth
        
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $auth_arr = array('view', 'comment', 'share', 'upload_photos', 'discussion', 'print');
        if(Engine_Api::_()->hasItemType('video')) {
            array_push($auth_arr, 'upload_videos'); 
        }
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($listing, $role, $elem)) {
                    if ($form->$elem)
                    $form->$elem->setValue($role);
                }
            }    
        }
        
		if (!isset($submit_button))
		{
			if (!isset($edit_button))
			{
				//Check if it edit category
				if($listing -> category_id != $category_id)
				{
					$form->addError('Please note that all the informations of the existing category will be cleared when switching to another category.');
				}
				return;
			}	
		}
        
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if ($posts['is_end'] != '1')
		{
			unset($posts['end_date']);		
		}
		
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			$values = $form -> getValues();
			if ($values['is_end'] == '1')
			{
				$form -> is_end -> setValue(true);
				$this -> view -> is_end = true;
			}
			return;
		}
		// Process
		$values = $form -> getValues();
		$values['location'] = $values['location_address'];
		$values['latitude'] = $values['lat'];
		$values['longitude'] = $values['long'];

		if ($values['is_end'] == '1')
		{
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$end = strtotime($values['end_date']);
			date_default_timezone_set($oldTz);
			$values['end_date'] = date('Y-m-d H:i:s', $end);
			$now = date('Y-m-d H:i:s');
			if (strtotime($now) > strtotime($values['end_date']))
			{
				$form -> addError('End date must be greater than today!');
				return;
			}
		}
		$db = Engine_Api::_() -> getDbtable('listings', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			//Check if it edit category
			if($listing -> category_id != $category_id)
			{
				$old_category_id = $listing -> category_id;
				$isEditCategory = true;
			}
			// Edit listing
			$listing -> setFromArray($values);
			if(!empty($values['toValues']))
			{
				$listing -> video_id = $values['toValues'];
			}
			if ($values['is_end'] == '1')
			{
				$listing -> end_date = $values['end_date'];
			}
			else{
				$listing -> end_date = NULL;
			}
			$listing -> save();

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$listing -> tags() -> addTagMaps($viewer, $tags);

			$search_table = Engine_Api::_() -> getDbTable('search', 'core');
			$select = $search_table -> select() -> where('type = ?', 'ynlistings_listing') -> where('id = ?', $listing -> getIdentity());
			$row = $search_table -> fetchRow($select);
			if ($row)
			{
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			else
			{
				$row = $search_table -> createRow();
				$row -> type = 'ynlistings_listing';
				$row -> id = $listing -> getIdentity();
				$row -> title = $listing -> title;
				$row -> description = $listing -> description;
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			
			// Set photo
			if (!empty($values['photo']))
			{
				$listing -> setPhoto($form -> photo);
			}
			//Set video
			if(!empty($values['toValues']))
			{
				$tableMappings = Engine_Api::_() -> getDbTable('mappings', 'ynlistings');
				$hasItem = $tableMappings -> checkHasItem($listing -> getIdentity(), $values['toValues'], 'profile_video');
				if(!$hasItem)
				{
					$row = $tableMappings -> createRow();
				    $row -> setFromArray(array(
				       'listing_id' => $listing -> getIdentity(),
				       'item_id' => $values['toValues'],
				       'user_id' => $viewer->getIdentity(),				       
				       'type' => 'profile_video',
				       'creation_date' => date('Y-m-d H:i:s'),
				       'modified_date' => date('Y-m-d H:i:s'),
				       ));
				    $row -> save();
			    }
			}
			
			//Add parent fields
			if($category -> use_parent_category == 1)
		    {
		    	$customfieldformParent = $form -> getSubForm('fieldsParent');
				$customfieldformParent -> setItem($listing);
				$customfieldformParent -> saveValues();
			}
			// Add fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($listing);
			$customfieldform -> saveValues();
			
			// Remove old data custom fields if edit category
			if($isEditCategory)
			{
				$old_category = Engine_Api::_()->getItem('ynlistings_category', $old_category_id);
				$tableMaps = Engine_Api::_() -> getDbTable('maps','ynlistings');
				$tableValues = Engine_Api::_() -> getDbTable('values','ynlistings');
				$tableSearch = Engine_Api::_() -> getDbTable('search','ynlistings');
				if($old_category)
				{
					$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_category->option_id));
					$arr_ids = array();
					if(count($fieldIds) > 0)
					{
						//clear values in search table
						$searchItem  = $tableSearch->fetchRow($tableSearch -> select() -> where('item_id = ?', $listing->getIdentity()) -> limit(1));
						foreach($fieldIds as $id)
						{
							try{
								$column_name = 'field_'.$id -> child_id;
								$searchItem -> $column_name = NULL;
								$arr_ids[] = $id -> child_id;
							}
							catch(exception $e)
							{
								continue;
							}
						}
						$searchItem -> save();
						//delele in values table
						if(count($arr_ids) > 0)
						{
							$valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $listing->getIdentity()) -> where('field_id IN (?)', $arr_ids));
							foreach($valueItems as $item)
							{
								$item -> delete();
							}
						}
					}
				}
			}
			$db -> commit();
            
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment', 'share', 'upload_photos', 'discussion', 'print');
            if(Engine_Api::_()->hasItemType('video')) {
                array_push($auth_arr, 'upload_videos'); 
            }
            foreach ($auth_arr as $elem) {
                $auth_role = $values[$elem];
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }

		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		if(isset($edit_button))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'manage',
				), 'ynlistings_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		if (isset($submit_button))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'place-order',
					'id' => $listing -> getIdentity()
				), 'ynlistings_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
	}
	
	public function createAction()
	{
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		$this -> _helper -> content
					-> setEnabled();
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// Check authorization to post listing.
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynlistings_listing', null, 'create') -> isValid())
			return;
        
	    $tableCategory = Engine_Api::_()->getItemTable('ynlistings_category');
        
        // Check max of listings can be add.
        $table = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $count_listings = count($table->fetchAll($select));
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_listings_auth = $permissionsTable->getAllowed('ynlistings_listing', $viewer->level_id, 'max_listings');
        if ($max_listings_auth == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynlistings_listing')
                ->where('name = ?', 'max_listings'));
            if ($row) {
                $max_listings_auth = $row->value;
            }
        }
        $categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
 		$firstCategory = $categories[1];
		$category_id = $this -> _getParam('category_id', $firstCategory->category_id);

		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynlistings_category', $category_id);
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynlistings_listing');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
		{
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array(
				'topLevelId' => $profileTypeField -> field_id,
				'topLevelValue' => $category -> option_id,
			);
			if($category -> use_parent_category == 1)
			{
				$main_parent_category = Engine_Api::_()->getItem('ynlistings_category', $category -> getIdentity()) -> getParentCategoryLevel1();
				$category_parent = Engine_Api::_() -> getItem('ynlistings_category', $main_parent_category -> getIdentity());
				$formArgsParent = array(
					'topLevelId' => $profileTypeField -> field_id,
					'topLevelValue' => $category_parent -> option_id,
				);
			}
		}
        
        $can_select_theme = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'select_theme') -> checkRequire();
		if($category -> use_parent_category == 1)
		{
			$this -> view -> form = $form = new Ynlistings_Form_Create( array(
				'category' => $category,
				'formArgs' => $formArgs,
				'formArgsParent' => $formArgsParent,
				'canSelectTheme' => $can_select_theme
			));
		}
		else {
			$this -> view -> form = $form = new Ynlistings_Form_Create( array(
				'category' => $category,
				'formArgs' => $formArgs,
				'canSelectTheme' => $can_select_theme
			));
		}
       
        //check max of listings can be add
        if ($max_listings_auth > 0 && $count_listings >= $max_listings_auth) {
            $this->view->error = true;
            $this->view->message = 'Number of your listings is maximum. Please delete some listings for creating new.';
            return;    
        }
        
		if(!Engine_Api::_()->hasItemType('video'))
		{
			$form -> removeElement('upload_videos');
			$form -> removeElement('to');
		}
		// Populate category list.
		$categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item)
		{
			$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
		}

		if (count($form -> category_id -> getMultiOptions()) < 1)
		{
			$form -> removeElement('category_id');
		}
		//populate category
		if($category_id)
		{
			$form -> category_id -> setValue($category_id);
		}
		else
		{
			$form->addError('Create listing require at least one category. Please contact admin for more details.');
		}
		//populate currency
		$supportedCurrencies = array();
		$gateways = array();
		$gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		foreach ($gatewaysTable->fetchAll(/*array('enabled = ?' => 1)*/) as $gateway)
		{
			$gateways[$gateway -> gateway_id] = $gateway -> title;
			$gatewayObject = $gateway -> getGateway();
			$currencies = $gatewayObject -> getSupportedCurrencies();
			if (empty($currencies))
			{
				continue;
			}
			$supportedCurrencyIndex[$gateway -> title] = $currencies;
			if (empty($fullySupportedCurrencies))
			{
				$fullySupportedCurrencies = $currencies;
			}
			else
			{
				$fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
			}
			$supportedCurrencies = array_merge($supportedCurrencies, $currencies);
		}
		$supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

		$translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
		$fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
		$supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
		
		$form -> getElement('currency') -> setMultiOptions(array(
			'Please select one' => array_merge($fullySupportedCurrencies, $supportedCurrencies)
		));
		$submit_button = $this -> _getParam('submit_button');
		$save_draft = $this -> _getParam('save_draft');
		if (!isset($submit_button))
		{
			if (!isset($save_draft))
				return;
		}
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if ($posts['is_end'] != '1')
		{
			unset($posts['end_date']);		
		}
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			$values = $form -> getValues();
			if ($values['is_end'] == '1')
			{
				$form -> is_end -> setValue(true);
				$this -> view -> is_end = true;
			}
			return;
		}
		// Process
		$values = $form -> getValues();
		$values['location'] = $values['location_address'];
		$values['latitude'] = $values['lat'];
		$values['longitude'] = $values['long'];
		$values['user_id'] = $viewer -> getIdentity();

		if ($values['is_end'] == '1')
		{
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$end = strtotime($values['end_date']);
			date_default_timezone_set($oldTz);
			$values['end_date'] = date('Y-m-d H:i:s', $end);
			$now = date('Y-m-d H:i:s');
			if (strtotime($now) > strtotime($values['end_date']))
			{
				$form -> addError('End date must be greater than today!');
				return;
			}
		}
		$db = Engine_Api::_() -> getDbtable('listings', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Create listing
			$table = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
			$listing = $table -> createRow();
			$listing -> setFromArray($values);
			$listing -> status = 'draft';
			$listing -> video_id = $values['toValues'];
			$listing -> approved_status = 'pending';
			if ($values['is_end'] == '1')
			{
				$listing -> end_date = $values['end_date'];
			}
			$listing -> save();

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$listing -> tags() -> addTagMaps($viewer, $tags);

			$search_table = Engine_Api::_() -> getDbTable('search', 'core');
			$select = $search_table -> select() -> where('type = ?', 'ynlistings_listing') -> where('id = ?', $listing -> getIdentity());
			$row = $search_table -> fetchRow($select);
			if ($row)
			{
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			else
			{
				$row = $search_table -> createRow();
				$row -> type = 'ynlistings_listing';
				$row -> id = $listing -> getIdentity();
				$row -> title = $listing -> title;
				$row -> description = $listing -> description;
				$row -> keywords = $values['tags'];
				$row -> save();
			}

			// Set photo
			if (!empty($values['photo']))
			{
				$listing -> setPhoto($form -> photo);
			}
			//Set video
			if(!empty($values['toValues']))
			{
				$tableMappings = Engine_Api::_() -> getDbTable('mappings', 'ynlistings');
				$row = $tableMappings -> createRow();
			    $row -> setFromArray(array(
			       'listing_id' => $listing -> getIdentity(),
			       'item_id' => $values['toValues'],
			       'user_id' => $viewer->getIdentity(),				       
			       'type' => 'profile_video',
			       'creation_date' => date('Y-m-d H:i:s'),
			       'modified_date' => date('Y-m-d H:i:s'),
			       ));
			    $row -> save();
			}
			//Add parent fields
			if($category -> use_parent_category == 1)
		    {
		    	$customfieldformParent = $form -> getSubForm('fieldsParent');
				$customfieldformParent -> setItem($listing);
				$customfieldformParent -> saveValues();
			}
			// Add fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($listing);
			$customfieldform -> saveValues();
			
			$db -> commit();

		    $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment', 'share', 'upload_photos', 'discussion', 'print');
            if(Engine_Api::_()->hasItemType('video')) {
                array_push($auth_arr, 'upload_videos'); 
            }
            foreach ($auth_arr as $elem) {
                $auth_role = $values[$elem];
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
            {
                Engine_Api::_()->yncredit()-> hookCustomEarnCredits($listing -> getOwner(), $listing -> title, 'ynlistings_new', $listing);
			}
        }
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		if (isset($save_draft))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'view',
					'id' => $listing -> getIdentity()
				), 'ynlistings_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		if (isset($submit_button))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'place-order',
					'id' => $listing -> getIdentity()
				), 'ynlistings_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
	}
	
	public function selectThemeAction()
	{
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $this->_getParam('listing_id'));
		$this->view->listing = $listing;
        $this->view->can_select_theme = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'select_theme') -> checkRequire();
        
		// Check method and data validity.
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$listing -> theme = $this->_getParam('theme');	
		$listing -> save();
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Done.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}
	
	public function getMyLocationAction()
	{
		$latitude = $this -> _getParam('latitude');
		$longitude = $this -> _getParam('longitude');
		$values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
		echo $values;
		die ;
	}

	protected function _redirector()
	{
		$this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynlistings_general', true),
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Error!'))
		));
	}

	public function deleteAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $this -> getRequest() -> getParam('id'));
		
		//check authorization for deleting listing.
		if (!$this -> _helper -> requireAuth() -> setAuthParams($listing, null, 'delete') -> isValid())
		{
			return;
		}
        
        if (!$listing->isDeletable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to delete this listing.';
            return;    
        }
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynlistings_Form_Delete();

		if (!$listing)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$db = $listing -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			if ($listing -> photo_id)
			{
				Engine_Api::_() -> getItem('storage_file', $listing -> photo_id) -> remove();
			}
			$listing -> delete();
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This listing has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynlistings_general', true),
			'messages' => Array($this -> view -> message)
		));
	}

	public function closeAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $this -> getRequest() -> getParam('id'));
		if (!$listing -> isOwner($viewer))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("You don't have permission to close this listing.");
			return;
		}
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynlistings_Form_Close();

		if (!$listing)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists.");
			return;
		}

		if ($listing -> status != 'open' || $listing -> approved_status != 'approved')
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Invalid Listing.");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$listing -> status = 'closed';
		$listing -> save();

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Listing has been closed.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynlistings_general', true),
			'messages' => Array($this -> view -> message)
		));
	}

	public function reOpenAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $this -> getRequest() -> getParam('id'));
		if (!$listing -> isOwner($viewer))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("You don't have permission to re-open this listing.");
			return;
		}
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynlistings_Form_Reopen();

		if (!$listing) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists.");
			return;
		}

		if (($listing -> status != 'closed' && $listing -> status != 'expired') || $listing -> approved_status != 'approved') {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Invalid Listing.");
			return;
		}
        
        if (!$listing->expired()) {
            $form->removeElement('end_date');
        }
        else {
            $end_date = $listing->end_date;
            if ($end_date) {
                $end_date = strtotime($end_date);
                $end_date = new Zend_Date($end_date);
                $end_date->setTimezone($timezone);
                $form->end_date->setValue($end_date->get('yyyy-MM-dd HH:mm:ss'));
            }
        }
        
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
        
        if (!$listing->expired()) {
            $listing -> status = 'open';
        }
        else {
            $new_end_date = $this->getRequest()->getPost('end_date');
            if ($new_end_date) {
                $sysTimezone = date_default_timezone_get();
                date_default_timezone_set($timezone);
                $new_end_date = strtotime($new_end_date);
                $new_end_date = new Zend_Date($new_end_date);
                $new_end_date->setTimezone($sysTimezone);
                date_default_timezone_set($sysTimezone);
                $now = new Zend_Date();
                if ($new_end_date > $now) {
                    $listing -> status = 'open';
                    $listing -> end_date = $new_end_date->get('yyyy-MM-dd HH:mm:ss');
                }
                else {
                    $this -> view -> status = false;
                    $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('New end date must greater than current time.');
                    return;
                }
            }
        }
		$listing -> save();

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Listing has been re-opened.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynlistings_general', true),
			'messages' => Array($this -> view -> message)
		));
	}
	
	 protected function _checkEnoughCredits($credits)
	  {
	    $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
	    if (!$balance) {
	      return false;
	    }
	    $currentBalance = $balance->current_credit;
	    if ($currentBalance < $credits) {
	      return false;
	    }
	    return true;
	  }
      
    public function displayMapViewAction() {
        
        $tab = $this->_getParam('tab','tab_listings_recent');
        $itemCount = $this->_getParam('itemCount');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $table = Engine_Api::_()->getItemTable('ynlistings_listing');
        switch ($tab) {
            case 'tab_listings_recent':
                
                $recentType = $this->_getParam('recentType', 'creation');
                if( !in_array($recentType, array('creation', 'modified')) ) {
                  $recentType = 'approved';
                }
                $this->view->recentType = $recentType;
                $this->view->recentCol = $recentCol = $recentType . '_date';
                
                // Get paginator
                $table = Engine_Api::_()->getItemTable('ynlistings_listing');
                $select = $table->select()
                ->where('search = ?', 1)
                ->where('status = ?', 'open')
                ->where('approved_status = ?', 'approved')
                  ;
                 
                if( $recentType == 'creation' ) {
                  // using primary should be much faster, so use that for creation
                  $select->order('listing_id DESC');
                } else {
                  $select->order($recentCol . ' DESC');
                }
                
                if ($itemCount) {
                    $select->limit($itemCount);
                }
                $listings = $table->fetchAll($select);
                break;
            
            case 'tab_listings_popular':
                        
                $popularType = $this->_getParam('popularType', 'view');
                if( !in_array($popularType, array('view', 'member')) ) {
                  $popularType = 'view';
                }
                $this->view->popularType = $popularType;
                $this->view->popularCol = $popularCol = $popularType . '_count';
                
                // Get paginator
                $table = Engine_Api::_()->getItemTable('ynlistings_listing');
                $select = $table->select()
                ->where('search = ?', 1)
                ->where('status = ?', 'open')
                ->where('approved_status = ?', 'approved')
                ->order($popularCol . ' DESC');
                if ($itemCount) {
                    $select->limit($itemCount);
                }
                $listings = $table->fetchAll($select);                
                break;
                
            case 'tab_browse_listings':
                $params = $this->getRequest()->getParams();       
                $listings = Engine_Api::_() -> getItemTable('ynlistings_listing') -> getListingsPaginator($params);                
                break;
        
        }
        
        $datas = array();
        $contents = array();
        $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ;
        $icon_clock = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/ynlistings-maps-time.png';
        $icon_persion = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/ynlistings-maps-person.png';
        $icon_star = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/ynlistings-maps-close-black.png';
        $icon_home = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/ynlistings-maps-location.png';
        $icon_new = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/icon-New.png';
        $icon_guest = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/ynlistings-maps-person.png';
        
        foreach($listings as $listing) {           
            if($listing -> latitude) {               
                $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/maker.png';
                
                if($listing->featured) {
                    $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/feature_maker.png';
                }
                else
                {
                    if(!$listing->isNew()) {
                        $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynlistings/externals/images/old-maker.png';
                    }
                }
                $datas[] = array(   
                        'listings_id' => $listing -> getIdentity(),              
                        'latitude' => $listing -> latitude,
                        'longitude' => $listing -> longitude,
                        'icon' => $icon
                    );
                if($listing->isNew())
                {
                    $new = "<img src='".$icon_new."' style='float: left; margin-right: 10px;'/>";
                }else{
                    $new = "";
                }
                $memicon = "<img src='".$icon_guest."' />";
                $contents[] = '
                    <div class="ynlistings-maps-main" style="width: auto;">   
                        <div class="ynlistings-maps-content" style="overflow: hidden; line-height: 20px;">
                            '.$new.'
                            <div style="overflow:hidden; float: left;">
                                <a href="'.$listing->getHref().'" class="ynlistings-maps-title" style="color: #679ac0; font-weight: bold; font-size: 14px; text-decoration: none; float: left; clear: both;" target="_parent">
                                    '.$listing->title.'
                                </a>                      
                            </div>
                        </div>
                    </div>
                ';
            }
        }

        echo $this ->view -> partial('_map_view.tpl', 'ynlistings',array('datas'=>Zend_Json::encode($datas), 'listings'=>$listings , 'contents' => Zend_Json::encode($contents)));
        exit();
    }
    
    public function importOneByOneAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $can_import = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'import') -> checkRequire();
        if (!$viewer->getIdentity() || !$can_import) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'You don\'t have permission to do this.'));  
            return;
        }
        
        // If not post or form not valid, return
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'The request is invalid.'));  
            return;
        }

        $data = json_decode($this->_getParam('listing'));
        $auth_listing = json_decode($this->_getParam('auth'));
        
        if(isset($data[0]))
            $title = strip_tags($data[0]);
        if(isset($data[1]))
             $tag = strip_tags($data[1]);
        if(isset($data[2]))
            $short_description = $data[2];
        if(isset($data[3]))
            $description = $data[3];
        if(isset($data[4]))
            $about_us = $data[4];
        if(isset($data[5]))
            $price = strip_tags($data[5]);
        if(isset($data[6])) {
            $location = strip_tags($data[6]);
        }
        if(isset($data[7]))
            $category_id = strip_tags($data[7]);
        if(isset($data[8]))
            $end_date = strip_tags($data[8]);
        if(empty($title) || empty($short_description)){
            echo true;
            return;
        }
        //$cancelSetting = 'ynlistings_cancel_import_user'.$viewer->getIdentity();
        // Check max of listings can be add.
        $table = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $count_listings = count($table->fetchAll($select));
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_listings_auth = $permissionsTable->getAllowed('ynlistings_listing', $viewer->level_id, 'max_listings');
        if ($max_listings_auth == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynlistings_listing')
                ->where('name = ?', 'max_listings'));
            if ($row) {
                $max_listings_auth = $row->value;
            }
        }
        
        if ($max_listings_auth > 0 && $count_listings > $max_listings_auth) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'Your listings is maximum.'));  
            return;
        }
        else {
            $db = Engine_Api::_()->getDbtable('listings', 'ynlistings')->getAdapter();
            $db->beginTransaction();
            try {
                $listing = $table->createRow();
                $listing -> title = $title;
                $listing -> short_description = $short_description;
                if(!empty($description))
                    $listing -> description = $description;
                if(!empty($about_us))
                    $listing -> about_us = $about_us;
                if(!empty($price))
                    $listing -> price = $price;
                if(!empty($location))
                    $listing -> location = $location;
                
                $categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
                unset($categories[0]);
                if(!empty($category_id)) {
                    $category = Engine_Api::_()->getItem('ynlistings_category', $category_id);
                    if ($category) {
                        $listing->category_id = $category_id;
                        if ($category->level > 1)
                            $listing->theme = $category->getParentCategoryLevel1()->themes[0];
                        else
                            $listing->theme = $category->themes[0]; 
                    }
                    else {
                        $listing->category_id = $categories[1]->getIdentity();
                        $listing->theme = $categories[1]->themes[0];
                    }
                }
                else {
                    $listing->category_id = $categories[1]->getIdentity();
                    $listing->theme = $categories[1]->themes[0];
                }
            
                if(!empty($end_date)) {
                    $oldTz = date_default_timezone_get();
                    date_default_timezone_set($viewer->timezone);
                    $end = strtotime($end_date);
                    date_default_timezone_set($oldTz);
                    $end_date = date('Y-m-d H:i:s', $end);
                    $now = date('Y-m-d H:i:s');
                    if (strtotime($now) > strtotime($end_date))
                        $listing -> end_date = NULL;
                    else 
                        $listing -> end_date = $end_date;
                }
                $listing -> user_id = $viewer -> getIdentity();
                $listing -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
                $listing -> approved_status = 'pending';
                $listing -> status = 'draft';
                $listing -> save();
                if(!empty($tag)) {
                    $tags = preg_split('/[,]+/', $tag);
                    $listing -> tags() -> addTagMaps($viewer, $tags);
                }
                
                //set authorization
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $auth_arr = array('view', 'comment', 'share', 'upload_photos', 'discussion', 'print');
                if(Engine_Api::_()->hasItemType('video')) {
                    array_push($auth_arr, 'upload_videos'); 
                }
                
                foreach ($auth_arr as $elem) {
                    $auth_role = $auth_listing->$elem;
                    if ($auth_role) {
                        $roleMax = array_search($auth_role, $roles);
                        foreach ($roles as $i=>$role) {
                           $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                        }
                    }    
                }
            }
            catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }       
    
            $db->commit();
            
            echo Zend_Json::encode(array('status' => true, 'message' => '', 'id' => $listing->getIdentity()));  
            return;
        } 
    }

    public function rollbackImportAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $can_import = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'import') -> checkRequire();
        if (!$viewer->getIdentity() || !$can_import) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'You don\'t have permission to do this.'));  
            return;
        }
        
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'The request is invalid.'));  
            return;
        }

        $listings = json_decode($this->_getParam('listings'));
        foreach($listings as $listing_id) {
            $listing = Engine_Api::_()->getItem('ynlistings_listing', $listing_id);
            if (!$listing || !$listing->isDeletable()) {
                continue;
            }
            else {
                $listing->delete();
            }
        }
        echo Zend_Json::encode(array('status' => true, 'message' => ''));  
            return;
    }

    public function historyImportAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $can_import = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'import') -> checkRequire();
        if (!$viewer->getIdentity() || !$can_import) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'You don\'t have permission to do this.'));  
            return;
        }
        
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'The request is invalid.'));  
            return;
        }

        $listings = json_decode($this->_getParam('listings'));
        $db = Engine_Api::_()->getDbtable('imports', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('imports', 'ynlistings');
            $history = $table->createRow();
            $history -> file_name = $this->_getParam('filename');
            $history -> number_listings = count($listings);
            $history -> list_listings = $listings;
            $history -> save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
    }

    public function printAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $id = $this->_getParam('id');
        if( null !== $id ){
            $subject = Engine_Api::_()->getItem('ynlistings_listing', $this->_getParam('id'));
            if( !$subject || !$subject->getIdentity() )
                return $this->_helper->requireSubject()->forward();
            
            if (!$subject->canPrint())
             {
               return $this -> _helper -> requireAuth() -> forward();
             }
             if( $subject && $subject->getIdentity() )
            {
              Engine_Api::_()->core()->setSubject($subject);
            }
            if(($subject-> status !='open') || ($subject -> approved_status !='approved'))
            {
                if($subject -> user_id != $viewer -> getIdentity())
                {
                    return $this -> _helper -> requireAuth() -> forward();
                }
            }
        }
        //get photos
        $this -> view -> album = $album = $subject -> getSingletonAlbum();
        $this -> view -> photos = $photos = $album -> getCollectiblesPaginator();
        $photos -> setCurrentPageNumber(1);
        $photos -> setItemCountPerPage(100);
        //get videos
        if(Engine_Api::_()->ynlistings()->checkYouNetPlugin('video') || Engine_Api::_()->ynlistings()->checkYouNetPlugin('ynvideo'))
        {
            $tableMappings = Engine_Api::_()->getItemTable('ynlistings_mapping');
            $params['listing_id'] = $subject -> getIdentity();
            $this -> view -> videos = $videos = $tableMappings -> getVideosPaginator($params);
            $videos -> setCurrentPageNumber(1);
            $videos -> setItemCountPerPage(100);
        }
        $this -> _helper -> content -> setEnabled();
        $this -> view -> listing = $subject;
    }
}
