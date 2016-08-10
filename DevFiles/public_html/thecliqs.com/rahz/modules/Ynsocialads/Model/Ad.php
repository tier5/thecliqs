<?php
 
class Ynsocialads_Model_Ad extends Core_Model_Item_Abstract
{
    protected $_type = 'ynsocialads_ad';
    protected $_owner_type = 'user';
    protected $_parent_type = 'user';
    
    function isViewable()  { return $this->authorization()->isAllowed(null, 'view'); }
    function isEditable()  { return $this->authorization()->isAllowed(null, 'edit'); }
    function isDeletable() { return $this->authorization()->isAllowed(null, 'delete'); }
    
    public function likes()
    {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
    }
    
    public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
    }
    
    
    public function isPayLater()
    {
        $isPayLater = 0;
        //check if ad is paid later
        $transactionTable = Engine_Api::_() -> getDbTable('transactions', 'ynsocialads');
        $transAd = $transactionTable -> fetchRow($transactionTable -> select() -> where('ad_id = ?', $this -> getIdentity()) -> limit(1));
        if (($transAd -> status == 'initialized') && ($transAd -> gateway_id == '-2'))
        {
            $isPayLater = 1;
        }
        return $isPayLater;
    }
    
    public function getLinkUpdateStats()
    {
        $params = array(
            'route' => 'ynsocialads_ads',
            'controller' => 'ads',
            'action' => 'update-stats',
            'id' => $this->ad_id,
        );
         $route = $params['route'];
         unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, true);
    }
    
    public function getHref($params = array()) {
    $params = array_merge(array(
        'route' => 'ynsocialads_ads',
        'controller' => 'ads',
        'action' => 'view',
        'id' => $this->ad_id,
    ), 
    $params);
    $route = $params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, true);
    }
    
    public function getPackage()
    {
        $packageTable =  Engine_Api::_() -> getDbTable('packages', 'ynsocialads');
        $select = $packageTable -> select();
        $select -> where('package_id = ?', $this->package_id);
        return $packageTable->fetchRow($select);
    }
    public function getCampaignName()
    {
          $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $this->campaign_id);
          if ($campaign) return $campaign->title;
    }
    public function getCampaign() {
        $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $this->campaign_id);
        return $campaign;
    }
    
    public function getStartDate() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $start_date = new Zend_Date(strtotime($this->start_date));
        $start_date->setTimezone($timezone);
        return $start_date;
    }
    
    public function getEndDate() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $end_date = new Zend_Date(strtotime($this->end_date));
        $end_date->setTimezone($timezone);
        return $end_date;
    }
    
    public function getRunningDate() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $running_date = new Zend_Date(strtotime($this->running_date));
        $running_date->setTimezone($timezone);
        return $running_date;
    }
    
    public function getTitle() {
        return $this->name;
    }
    
    public function getTotalTarget()
    {
        $package = $this-> getPackage();
        $amount = $package -> benefit_amount;
        $price = $package -> price;
        $remain = $this->getRemain();
        $virtual_money = ($price/$amount) * $remain;
        return $virtual_money;
    }
    
    public function getRemain() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $package = Engine_Api::_()->getItem('ynsocialads_package', $this->package_id);
        $remain = 0;
        if ($package) {
            $benefit_type = $package->benefit_type;
            switch ($benefit_type) {
                case 'click':
                    $remain = intval($this->benefit_total) - intval($this->click_count);
                    break;
                case 'impression':
                    $remain = intval($this->benefit_total) - intval($this->impressions_count);
                    break;
                case 'day':
                    // Get timezone
                    $timezone = Engine_Api::_()->getApi('settings', 'core')
                    ->getSetting('core_locale_timezone', 'GMT');
                    
                    if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
                        $timezone = $viewer->timezone;
                    }
                    if ($this->running_date == null) {
                        $remain = intval($this->benefit_total);
                    }
                    else {
                        $runningDate = new DateTime($this->running_date);
                        $now = new DateTime;
                        $diff = date_diff($runningDate, $now);
                        $measure = ($diff->format('%a'));
                        $remain = ($measure < $this->benefit_total) ? ($this->benefit_total - $measure) : 0;

                    }
                    break;
            }
        }
        return ($remain < 0)?0:$remain;
    }
    
     public function getUserLike()
    {
        $aUserLike = array();
        // friend of mine in the list of people like
        
        if ($this -> likes() -> getLikeCount() > 0)
        {
            $table = Engine_Api::_() -> getDbTable('likes', 'core');
            $select = $table -> select();
            $select -> from($table -> info('name'), array(
                'poster_type',
                'poster_id'
            ));
                $select -> where('resource_type = ?', $this -> getType());
            $select -> where('resource_id = ?', $this -> getIdentity());
            
            //get all Friends
            $oViewer = Engine_Api::_() -> user() -> getViewer();
            $friends = $oViewer -> membership() -> getMembersInfo(true);
            if (count($friends) > 0)
            {
                $ids = array();
                foreach ($friends as $row)
                {
                    $ids[] = $row -> user_id;
                }
                $select -> where("poster_id IN (?)", $ids);
            
                $select -> limit(1);
                $userLike = $table -> fetchRow($select);
                $oUser = Engine_Api::_() -> user() -> getUser($userLike -> poster_id);
                if ($oUser -> getIdentity())
                {
                    $aUserLike[] = array(
                        'iUserId' => $oUser -> getIdentity(),
                        'sDisplayName' => $oUser -> getTitle()
                    );
                }
            }
        }
        return $aUserLike;
    }

    public function setPhoto($photo) {
        if( is_array($photo) && !empty($photo['tmp_name']) ) {
            $file = $photo['tmp_name'];
        } 
        else {
            return;
        }
        
        $name = $photo['name'];
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'ynsocialads_ad',
            'parent_id' => $this->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (outsize)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(184, 138)
            ->write($path.'/o_'.$name)
            ->destroy();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
         //   ->resize(584, 200)
            ->write($path.'/m_'.$name)
            ->destroy();

        // Resize image (footer)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(1064, 200)
            ->write($path.'/f_'.$name)
            ->destroy();
            
        // Store
        $iMain = $storage->create($path.'/m_'.$name, $params);
        $iOutsize = $storage->create($path.'/o_'.$name, $params);
        $iFooter = $storage->create($path.'/f_'.$name, $params);

        $iMain->bridge($iOutsize, 'thumb.outsize');
        $iMain->bridge($iFooter, 'thumb.footer');

        // Remove temp files
        @unlink($path.'/o_'.$name);
        @unlink($path.'/m_'.$name);
        @unlink($path.'/f_'.$name);
        // Update row
        $this->photo_id = $iMain->file_id;
        $this->save();

        return $this;
    }
    
    public function getModuleName() {
        $module = Engine_Api::_()->getItem('ynsocialads_module', $this->module_id);
        if ($module) return $module->module_name;        
    }
    
    public function getItem() {
        $module = Engine_Api::_()->getItem('ynsocialads_module', $this->module_id);
        if ($module) {
            return ($module->table_item.'_'.$this->item_id);
        }    
            
    }
    
    public function getSelectAdBlocks() {
        $mappingTbl = Engine_Api::_()->getItemTable('ynsocialads_mapping');
        $select = $mappingTbl->select()->where('ad_id = ?', $this->ad_id);
        $rawData = $mappingTbl->fetchAll($select);
        $adblockArr = array();
        foreach ($rawData as $rawDatum) {
            $adblockArr[] = $rawDatum->adblock_id;
        }
        return $adblockArr;
    }

    public function getSelectPlacements() {
        $adblockArr = $this->getSelectAdBlocks();
        $adblocksTbl = Engine_Api::_()->getItemTable('ynsocialads_adblock');
        $select = $adblocksTbl->select()->where('adblock_id IN (?)', $adblockArr);
        $adblocks = $adblocksTbl->fetchAll($select);
        $placements = array();
        foreach ($adblocks as $adblock) {
            if ($adblock->page_id == '2') {
                $placements[] = 'footer';
            }
            else {
                $placements[] = $adblock->placement;
            }
        }
        return array_unique($placements);
    }
    
    public function getTarget() {
        $targetTbl = Engine_Api::_()->getItemTable('ynsocialads_adtarget');
        $select = $targetTbl->select()->where('ad_id = ?', $this->ad_id);
        $target = $targetTbl->fetchRow($select);
        if ($target) return $target;
        else return null;
    }
    
    public function isAudience($user_id) {
        $target = $this->getTarget();
        $target = $target->toArray();
        $target['birthdate'] = array(
            'min' => $target['age_from'],
            'max' => $target['age_to']
        );

        if ($user_id == '0') {
            if ($target['public'])
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        $audiences = Engine_Api::_()->ynsocialads()->getAudiences($target);
        foreach ($audiences as $audience) {
            if ($audience['user_id'] == $user_id) {
                return true;
            } 
        }
        return false;
    }
    
    public function createCampaign($campaign_name) {
        $table = Engine_Api::_()->getItemTable('ynsocialads_campaign');
        $viewer = Engine_Api::_()->user()->getViewer();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $campaign = $table->createRow();
            $campaign->title = $campaign_name;
            $campaign->user_id = $viewer->getIdentity();
            $campaign->save();
            $success = TRUE;                
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
        $db->commit();
        if ($success) {
            return $campaign->getIdentity();
        }
        else {
            return 0;
        }
    }
    
    public function approve() {
        
        $this->status = 'approved';
        $this->approved = 1;
        $user = Engine_Api::_() -> user() -> getUser($this -> user_id);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
        $notifyApi -> addNotification($user, $viewer, $this, 'ynsocialads_admin_ad_approve');
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
        $viewMax = array_search('everyone', $roles);
        foreach( $roles as $i => $role ) {
            $auth->setAllowed($this, $role, 'view', ($i <= $viewMax));
        }
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($this -> getOwner(), $this, 'ynsocialads_ad_create');
        if($action) 
        {
            $activityApi->attachActivity($action, $this);
        }

        $this->save();
        Engine_Api::_()->ynsocialads()->checkAndUpdateStatus($this);
        
    }
}