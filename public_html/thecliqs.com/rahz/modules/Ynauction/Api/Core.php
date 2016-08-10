<?php
 /**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Core.php
 * @author     Minh Nguyen
 */
class Ynauction_Api_Core extends Core_Api_Abstract
{
	function getFinanceAccount($user_id = null,$payment_type = null)
	{
	    $Table =  new Ynauction_Model_DbTable_PaymentAccounts;
	    $select = $Table->select();
	    
	    if($user_id)
	    {
	        $select->where('user_id=?', $user_id);
	    }
	    if($payment_type)
	    {
	        $select->where('payment_type=?', $payment_type);
	    }
	        
	    $account =  $Table->fetchRow($select);
	    
	    // check is there finnance account
	    if(!is_object($account)){
	        throw new Exception("payment account does not exists");
	    }
	    return $account;
	}
	
	function saveTrackingPayIn($bill, $order)
	{
		 // Get gateway
    	$gateway = Engine_Api::_()->getDbtable('gateways', 'payment')
	      ->find($order -> gateway_id)
	      ->current();
		
	    // buyer account
	    $account = $this -> getFinanceAccount($bill->user_id,2);
	    // seller account.  
	    $table  =  new Ynauction_Model_DbTable_TransactionTrackings;
	    $item =    $table->fetchNew();
	    // them transaction tracking
	    $item->transaction_date =   $bill->date_bill;
	    $item->user_seller = $bill->owner_id;
	    $item->user_buyer  = $bill->user_id;
	    $item->item_id     = $bill->item_id;
	    $item->amount      = $bill->amount;
	    $item->account_seller_id = 'admin';
	    if($gateway)
	    	$item->method = $gateway -> title;
	    $item->account_buyer_id  = $account->paymentaccount_id;
	    $item->transaction_status = 1;
	    $item->type = 0;
	    $item->params   = sprintf('fee');
	    $item->save();
	    return $item;
	}
	
	public function updateBillStatus($bill,$status)
	{
	    $bill->bill_status = $status;
	    $bill->save();
	}
	public function updateDisplay($bill)
	{ 
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $auction =  Engine_Api::_()->getItem('ynauction_product',$bill->item_id);
	    if(!is_object($auction)){
	        throw new Exception ("the auction does not found!");
	    }
	    $auction->display_home = 1; 
	    if($bill->auto_approve == 1)
	    {
	        $auction->approved = 1;
	        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $auction, 'ynauction_new');
	        if( $action != null ) {
	            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $auction);
	        }  
	    }
	    $auction->save();
	    return $auction;
	}
	
	public function processPayment($order)
	{
		$sercurity = $order -> security_code;
		$invoice = $order -> invoice_code;
		 //get bill
        $Bills  =  new Ynauction_Model_DbTable_Bills;
        $select =  $Bills->select()->where('sercurity=?', $sercurity)->where('invoice=?', $invoice);
        $bill =  $Bills->fetchRow($select);
		
		if($bill){
	         //update status of bill
	        $this -> updateBillStatus($bill,1);
	        $this -> updateDisplay($bill);
	        $bill->bill_status = 1;
	         //saveTracking
	        $this -> saveTrackingPayIn($bill, $order); 
	        /**
	         * Call Event from Affiliate
	         */
	        
	        if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))    {
	            $params['module'] = 'ynauction';
	            $params['user_id'] = $bill->user_id;
	            $params['rule_name'] = 'publish_ynauction';
	            $params['currency'] = $bill->currency;
	            $params['total_amount'] = number_format($bill->amount,2);
	            Zend_Registry::get('Zend_Log')->log(print_r($params,true),Zend_Log::DEBUG);
	            Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
	        }
	        
	        /**
	         * End Call Event from Affiliate
	         */  
		}               
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
			$class = str_replace('Payment', 'Ynauction', $gateway -> plugin);

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
	
  public function getProductsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getProductsSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }
  public function getBoughtsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getBoughtsSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }
  public function getBecomePaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getBecomeSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }
  public function getBecomeSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('becomes', 'ynauction');
    $rName = $table->info('name');
    $select = $table->select()->from($rName)->setIntegrityCheck(false);
    $select->joinLeft('engine4_users', "$rName.user_id = engine4_users.user_id", 'engine4_users.user_id');
    if( !empty($params['title']) )
    {
      $select->where("engine4_users.username LIKE ? ", '%'.$params['title'].'%');
    }
    if( isset($params['approved']) && $params['approved'] != ' ')
    {
      $select->where($rName.".approved = ? ",$params['approved']);
    }
    if(isset($params['order']) && $params['order'])
     {
        $select->order($params['order'].' '.$params['direction']);
     }
    return $select;
  }
  public function getProductsSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('products', 'ynauction');
    $rName = $table->info('name');
    $select = $table->select()->from($rName)->setIntegrityCheck(false);
    // by search
    if( !empty($params['search']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$params['search'].'%');
    }
   if( !empty($params['title']) )
    {
      $select->where($rName.".title LIKE ? ", '%'.$params['title'].'%');
    }
    if( isset($params['featured']) && $params['featured'] != ' ')
    {
      $select->where($rName.".featured = ? ",$params['featured']);
    }
    if( isset($params['stop']) && $params['stop'] != ' ')
    {
      $select->where($rName.".stop = ? ",$params['stop']);
    }
    if( isset($params['online']) && $params['online'] != ' ')
    {
      $select->where($rName.".display_home = ? ",$params['online']);
    }
    // by where
    if(isset($params['where']) && $params['where'] != "")
    	$select->where($params['where']);
    // by User
    if(!empty($params['user_id']) && is_numeric($params['user_id']))
    	$select->where("$rName.user_id = ?",$params['user_id']);
    // by Bider
    if(!empty($params['bider_id']) && is_numeric($params['bider_id']))
        $select->where("$rName.bider_id = ?",$params['bider_id']);
    // by Category
    if(!empty($params['category']) && $params['category'] > 0 && $params['subcategory'] <= 0 )
    {
    	$sudcategory =  Engine_Api::_()->ynauction()->getCategories($params['category']);
        $arr = array();
        $arr[] = $params['category'];
        foreach($sudcategory as $sub)
        {
           $arr[] =  $sub->category_id;
        }
        $strIds = implode(',', $arr);
        $select->having("$rName.cat_id IN ($strIds)");
       
    }
    elseif(!empty($params['subcategory']) && $params['subcategory'] > 0)
    {
    	$select->where("$rName.cat_id = ?",$params['subcategory']);
    }
    // get  participate
    if(isset($params['participate']) && $params['participate'] != '')
    {
        $ynauctions =  Engine_Api::_()->ynauction()->getBidUser($params['participate']);
        $arr = array();
        $strIds = "-1";
        foreach($ynauctions as $ynauction)
        {
           $arr[] =  $ynauction->product_id;
        }
        if($arr)
            $strIds = implode(',', $arr);
        $select->having("$rName.product_id IN ($strIds)"); 
    }
    // by status
    if(isset($params['status']) && $params['status'] != ' ' && $params['status'] != '')
    {
        $now = date('Y-m-d H:i:s');
        $status = $params['status'];    
        if($status == 0)
        {
    	    $select->where("$rName.status = 0 AND $rName.display_home = 0");
        }
        elseif($status == 1)
        {
            $select->where("$rName.status = 0 AND $rName.display_home = 1 AND $rName.approved = 0");
        }
        elseif($status == 2)
        {
            $select->where("$rName.status = 0 AND $rName.display_home = 1 AND $rName.start_time >  '$now' AND $rName.approved = 1");
        }
        elseif($status == 3)
        {
            $select->where("$rName.status = 0 AND $rName.display_home = 1 AND $rName.start_time <= '$now' AND $rName.approved = 1");
        }
        elseif($status == 4)
        {
            $select->where("$rName.status = 1");
        }
        elseif($status == 5)
        {
            $select->where("$rName.status = 2");
        }
        elseif($status == 6)
        {
            $select->where("$rName.status = 3");
        }
        
    } 
     if(isset($params['order']) && $params['order'])
     {
        if($params['order'] == 'cat_title')
        {
            $select->joinLeft('engine4_ynauction_categories', "$rName.cat_id = engine4_ynauction_categories.category_id", 'engine4_ynauction_categories.title as cat_title');
        }
        if($params['order'] == 'username')
        {
            $select->joinLeft('engine4_users', "$rName.user_id = engine4_users.user_id", 'engine4_users.user_id');
        }  
        $select->order($params['order'].' '.$params['direction']);
     }
     else
     {
        // order
        if(isset($params['orderby']) && $params['orderby'])
        {
            $select->order($params['orderby'].' DESC');
            if($params['orderby'] == 'currency_symbol')
                $select->order('starting_bidprice DESC');   
        }
        else
        {
            $select->order("$rName.status ASC");    
            $select->order("$rName.Featured DESC");    
    	    $select->order("$rName.creation_date DESC");
        }
     }
    $select->where("$rName.is_delete = 0");	
    return $select;
  }
  public function getBoughtsSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('products', 'ynauction');
    $rName = $table->info('name');
    $select = $table->select()->from($rName)->setIntegrityCheck(false);
    // by search
    if( !empty($params['search']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$params['search'].'%');
    }
    // by Bidder
    $select->joinLeft("engine4_ynauction_proposals","engine4_ynauction_proposals.product_id = $rName.product_id"
    			, array('proposal_price', 'proposal_id'));
    $select->where("engine4_ynauction_proposals.ynauction_user_id = ?",$params['bider_id']);
    // by Category
    if(!empty($params['category']) && $params['category'] > 0 && $params['subcategory'] <= 0 )
    {
        $sudcategory =  Engine_Api::_()->ynauction()->getCategories($params['category']);
        $arr = array();
        $arr[] = $params['category'];
        foreach($sudcategory as $sub)
        {
           $arr[] =  $sub->category_id;
        }
        $strIds = implode(',', $arr);
        $select->having("$rName.cat_id IN ($strIds)");
       
    }
    elseif(!empty($params['subcategory']) && $params['subcategory'] > 0)
    {
        $select->where("$rName.cat_id = ?",$params['subcategory']);
    }
    // by status
    if(isset($params['status']) && $params['status'] != ' ' && $params['status'] != '')
    {
        $now = date('Y-m-d H:i:s');
        $status = $params['status'];    
        if($status == 0)
        {
            $select->where("$rName.status = 0 AND $rName.display_home = 0");
        }
        elseif($status == 1)
        {
            $select->where("$rName.status = 0 AND $rName.display_home = 1 AND $rName.approved = 0");
        }
        elseif($status == 2)
        {
            $select->where("$rName.status = 0 AND $rName.display_home = 1 AND $rName.start_time >  '$now' AND $rName.approved = 1");
        }
        elseif($status == 3)
        {
            $select->where("$rName.status = 0 AND $rName.display_home = 1 AND $rName.start_time <= '$now' AND $rName.approved = 1");
        }
        elseif($status == 4)
        {
            $select->where("$rName.status = 1");
        }
        elseif($status == 5)
        {
            $select->where("$rName.status = 2");
        }
        elseif($status == 6)
        {
            $select->where("$rName.status = 3");
        }
        
    } 
     if(isset($params['order']) && $params['order'])
     {
        if($params['order'] == 'cat_title')
        {
            $select->joinLeft('engine4_ynauction_categories', "$rName.cat_id = engine4_ynauction_categories.category_id", 'engine4_ynauction_categories.title as cat_title');
        }
        if($params['order'] == 'username')
        {
            $select->joinLeft('engine4_users', "$rName.user_id = engine4_users.user_id", 'engine4_users.user_id');
        }  
        $select->order($params['order'].' '.$params['direction']);
     }
     else
     {
        // order
        if(isset($params['orderby']) && $params['orderby'])
        {
            $select->order($params['orderby'].' DESC');
        }
        else
        {
            $select->order("$rName.product_id DESC");    
            $select->order("engine4_ynauction_proposals.proposal_price DESC");    
        }
     }
    $select->where("$rName.is_delete = 0");  
    return $select;
  }
  public function getCategories($parent = 0)
  {
    $table = Engine_Api::_()->getDbTable('categories', 'ynauction');
    $select = $table->select()->order('title ASC')->where('parent = ?',$parent);
    return $table->fetchAll($select);
  }
  public function getYnauctionTypes()
  {
    $table = Engine_Api::_()->getDbTable('types', 'ynauction');
    return $table->fetchAll($table->select()->order('title ASC'));
  }
  public function getAVGrate($ynauction_id)
  {
        $rateTable = Engine_Api::_()->getDbtable('rates', 'ynauction');
        $select = $rateTable->select()
        ->from($rateTable->info('name'), 'AVG(rate_number) as rates')
        ->group("ynauction_id")
        ->where('ynauction_id = ?', $ynauction_id);
        $row = $rateTable->fetchRow($select);
        return ((count($row) > 0)) ? $row->rates : 0;
    }
  public function getBid($ynauction_id)
  {
        $bidTable = Engine_Api::_()->getDbtable('bids', 'ynauction');
        $select = $bidTable->select()
        ->from($bidTable->info('name'))
        ->where('product_id = ?', $ynauction_id)->order('bid_time DESC')->limit(1);
        $row = $bidTable->fetchRow($select);
        return $row;
    }
    public function getUserBid($ynauction_id,$seller_id)
   {
        $bidTable = Engine_Api::_()->getDbtable('bids', 'ynauction');
        $select = $bidTable->select()
        ->from($bidTable->info('name'),"distinct(ynauction_user_id)")
        ->where('product_id = ?', $ynauction_id)
        ->where('ynauction_user_id != ?', $seller_id)
        ->order('ynauction_user_id DESC');
        $users = $bidTable->fetchAll($select);
        return $users;
    }
   
   public function getLatestUserBid($ynauction_id,$seller_id,$bid_price,$bider_id) {
        $bidTable = Engine_Api::_()->getDbtable('bids', 'ynauction');
		$select = $bidTable->select()->where('product_price <= ?', $bid_price)->where('ynauction_user_id != ?',$bider_id)->order('product_price DESC');
        $row = $bidTable->fetchRow($select);
		if (!$row) return array();
		$price = $row->product_price;
        $select = $bidTable->select()
        ->from($bidTable->info('name'),"distinct(ynauction_user_id)")
        ->where('product_id = ?', $ynauction_id)
        ->where('ynauction_user_id != ?', $seller_id)
		->where('product_price = ?', $price)
        ->order('ynauction_user_id DESC');
        $users = $bidTable->fetchAll($select);
        return $users;
    }
    public function getBidHis($ynauction_id,$limit=NULL)
   {
        $bidTable = Engine_Api::_()->getDbtable('bids', 'ynauction');
        $select = $bidTable->select()
        ->from($bidTable->info('name'))
        ->where('product_id = ?', $ynauction_id)->order('bid_time DESC');
        if($limit) $select->limit($limit);
        return $bidTable->fetchAll($select);
    }
    public function getBidUser($bidder_id)
   {
        $bidTable = Engine_Api::_()->getDbtable('bids', 'ynauction');
        $select = $bidTable->select()
        ->from($bidTable->info('name'))
        ->where('ynauction_user_id = ?', $bidder_id);
        return $bidTable->fetchAll($select);
    }
    public function checkConfirm($bidder_id)
    {
        $confirmTable = Engine_Api::_()->getDbtable('confirms', 'ynauction');
        $select = $confirmTable->select()
        ->from($confirmTable->info('name'))
        ->where('user_id = ?', $bidder_id);
        $users = $confirmTable->fetchAll($select);
        if(count($users) > 0)
            return true;
        else
            return false; 
    }
    public function checkBought($bidder_id)
    {
        $confirmTable = Engine_Api::_()->getDbtable('proposals', 'ynauction');
        $select = $confirmTable->select()
        ->from($confirmTable->info('name'))
        ->where('ynauction_user_id = ?', $bidder_id);
        $users = $confirmTable->fetchAll($select);
        if(count($users) > 0)
            return true;
        else
            return false; 
    }
    public function checkBoughtPrice($product_id,$price)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $bidder_id = $viewer->getIdentity();
        $confirmTable = Engine_Api::_()->getDbtable('proposals', 'ynauction');
        $select = $confirmTable->select()
        ->from($confirmTable->info('name'))
        ->where('ynauction_user_id = ?', $bidder_id)
        ->where('product_id = ?', $product_id)
        ->where('proposal_price = ?', $price)
        ;
        $users = $confirmTable->fetchAll($select);
        if(count($users) > 0)
            return true;
        else
            return false; 
    }
    public function checkBuy($product_id,$amount)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $bidder_id = $viewer->getIdentity();
        $confirmTable = Engine_Api::_()->getDbtable('transactionTrackings', 'ynauction');
        $select = $confirmTable->select()
        ->from($confirmTable->info('name'))
        ->where('user_buyer = ?', $bidder_id)
        ->where('item_id = ?', $product_id)
        ->where('amount = ?', $amount)
        ;
        $users = $confirmTable->fetchAll($select);
        if(count($users) > 0)
            return true;
        else
            return false; 
    }
    public function checkBecome($seller_id)
    {
        $becomeTable = Engine_Api::_()->getDbtable('becomes', 'ynauction');
        $select = $becomeTable->select()
        ->from($becomeTable->info('name'))
        ->where('approved = 1')
        ->where('user_id = ?', $seller_id);
        $users = $becomeTable->fetchAll($select);
        if(count($users) > 0)
            return true;
        else
            return false; 
    }
    public function getBecome($seller_id)
    {
        $becomeTable = Engine_Api::_()->getDbtable('becomes', 'ynauction');
        $select = $becomeTable->select()
        ->from($becomeTable->info('name'))
        ->where('user_id = ?', $seller_id)->limit(1);
        $users = $becomeTable->fetchRow($select);
        return $users; 
    }
    public function getBidUserHis($ynauction_id,$bidder_id)
   {
        $bidTable = Engine_Api::_()->getDbtable('bids', 'ynauction');
        $select = $bidTable->select()
        ->from($bidTable->info('name'))
        ->where('product_id = ?', $ynauction_id)->where('ynauction_user_id = ?',$bidder_id);
        return $bidTable->fetchAll($select);
    }
    public function canRate($ynauction,$user_id)
    {
          if ($ynauction->user_id == $user_id)
            return 0;
            $rateTable = Engine_Api::_()->getDbtable('rates', 'ynauction');
            $select = $rateTable->select()
        ->where('ynauction_id = ?', $ynauction->getIdentity())
        ->where('poster_id = ?', $user_id);

        return (count($rateTable->fetchAll($select)) > 0)?0:1;   
    }
    public function getParentAction($user_id,$ynauction) 
    {
        $table = Engine_Api::_()->getDbtable('actions', 'activity');
        $select = $table->select()
        ->from($table->info('name'))
        ->where('subject_id  = ?', $user_id)
        ->where('object_id  = ?', $ynauction)
        ->where("type  = 'ynauction_new'")->limit(1);
        return $table->fetchRow($select);
    }
    public function getBlocks()
    {
        $table = Engine_Api::_()->getDbTable('blocks', 'ynauction');
        return $table->fetchAll($table->select()->order('price ASC'));
    }
    public function getBlock($id)
    {
        $table = Engine_Api::_()->getDbTable('blocks', 'ynauction');
        return $table->fetchRow($table->select()->where('block_id = ?',$id)->limit(1));
    }
    public function createPhoto($params, $file)
   {
    if( $file instanceof Storage_Model_File )
    {
      $params['file_id'] = $file->getIdentity();
    }

    else
    {
      // Get image info and resize
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName = $path.'/m_'.$name . '.' . $extension;
      $profileName = $path.'/p_'.$name . '.' . $extension;
      $thumbName = $path.'/t_'.$name . '.' . $extension;
      $thumbName1 = $path.'/t1_'.$name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(720, 720) 
          ->write($mainName)
          ->destroy();
       // Resize image (profile)
       $image = Engine_Image::factory();
       $image->open($file['tmp_name'])
          ->resize(155, 155) 
          ->write($profileName)
          ->destroy();
      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(100,100)
          ->write($thumbName1)
          ->destroy();
      
      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(48, 48)
          ->write($thumbName)
          ->destroy();

      // Store photos
      $photo_params = array(
        'parent_id' => $params['product_id'],
        'parent_type' => 'product',
      );
      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
      $profileFile = Engine_Api::_()->storage()->create($profileName, $photo_params);
      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
      $thumbFile1 = Engine_Api::_()->storage()->create($thumbName1, $photo_params);
      $photoFile->bridge($profileFile, 'thumb.profile');
      $photoFile->bridge($thumbFile, 'thumb.icon');
      $photoFile->bridge($thumbFile1, 'thumb.normal');
      $params['file_id'] = $photoFile->file_id; // This might be wrong
      $params['photo_id'] = $photoFile->file_id;

      // Remove temp files
      @unlink($mainName);
      @unlink($profileName);
      @unlink($thumbName);
      @unlink($thumbName1);
      
    }
    $row = Engine_Api::_()->getDbtable('photos', 'ynauction')->createRow();
    $row->setFromArray($params);
    $row->save();
    return $row;
  }
}
