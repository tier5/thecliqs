<?php

class Ynsocialads_AdsController extends Core_Controller_Action_Standard 
{
    protected $_periods = array(Zend_Date::DAY, //dd
    Zend_Date::WEEK, //ww
    Zend_Date::MONTH, //MM
    Zend_Date::YEAR, //y
    );
    protected $_allPeriods = array(Zend_Date::SECOND, Zend_Date::MINUTE, Zend_Date::HOUR, Zend_Date::DAY, Zend_Date::WEEK, Zend_Date::MONTH, Zend_Date::YEAR, );
    protected $_periodMap = array(Zend_Date::DAY => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, ), Zend_Date::WEEK => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::WEEKDAY_8601 => 1, ), Zend_Date::MONTH => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::DAY => 1, ), Zend_Date::YEAR => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::DAY => 1, Zend_Date::MONTH => 1, ), );

    public function indexAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;
        $this -> view -> form = $form = new Ynsocialads_Form_Ads_Search();
        $form -> isValid($this -> _getAllParams());
        $params = $form -> getValues();
        $page = $this -> _getParam('page', 1);
        $campaign_id = $this -> _getParam('campaign_id');
        if (!empty($campaign_id)) 
        {
            $params['campaign_id'] = $campaign_id;
            $campaign = Engine_Api::_() -> getItem('ynsocialads_campaign', $campaign_id);
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> user_id = $params['user_id'] = $viewer -> getIdentity();
        $this -> view -> formValues = $params;
        $this -> view -> paginator = Engine_Api::_() -> getItemTable('ynsocialads_ad') -> getAdsPaginator($params);
        $this -> view -> paginator -> setItemCountPerPage(10);
        $this -> view -> paginator -> setCurrentPageNumber($page);
        $this -> _helper -> content -> setEnabled();
        $this -> view -> formStatistic = $formStatistic = new Ynsocialads_Form_Ads_SearchStatistics();
    }
    
    public function updateStatsAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $item = Engine_Api::_() -> getItem('ynsocialads_ad', $this->_getParam('id'));
        $id = $item -> getIdentity();
        $tableStatisticTable = Engine_Api::_() -> getItemTable('ynsocialads_statistic');
        $tableTrackTable = Engine_Api::_() -> getItemTable('ynsocialads_track');
        $preview = $this->_getParam('preview',0);
        if(!$preview)
        {
            $today = date("Y-m-d"); 
            //check if user login
            if($viewer->getIdentity())
            {
                // check if user has not view ad yet -> add reach count
                if(!($tableStatisticTable->checkUniqueViewByUserId($viewer->getIdentity(), $id, 'click')))
                {
                    $item -> unique_click_count = $item -> unique_click_count + 1;
                    $item -> click_count = $item -> click_count + 1;
                    
                    if($track = $tableTrackTable->checkExistTrack($today, $id)){
                        $track -> unique_clicks = $track -> unique_clicks + 1;
                        $track -> clicks = $track -> clicks + 1;
                        $track -> save();
                    }
                    else{
                        $track = $tableTrackTable -> createRow();
                        $track -> date = $today;
                        $track -> ad_id = $id;
                        $track -> unique_clicks = 1;
                        $track -> clicks = 1;
                        $track -> save();
                    }
                }   
                else {
                    $item -> click_count = $item -> click_count + 1;
                    
                    if($track = $tableTrackTable->checkExistTrack($today, $id)){
                        $track -> clicks = $track -> clicks + 1;
                        $track -> save();
                    }
                    else{
                        $track = $tableTrackTable -> createRow();
                        $track -> date = $today;
                        $track -> ad_id = $id;
                        $track -> clicks = 1;
                        $track -> save();
                    }
                }
                
                //update view statistic
                $stats = $tableStatisticTable -> createRow();
                $stats -> user_id = $viewer->getIdentity();
                $stats -> timestamp = date('Y-m-d H:i:s');
                $stats -> type = 'click';
                $stats -> ad_id = $id;
                $stats -> save();
                
            }
            //guest
            else 
            {
                // Get ip address
                $db = Engine_Db_Table::getDefaultAdapter();
                $ipObj = new Engine_IP();
                $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
                
                if(!($tableStatisticTable->checkUniqueViewByIP($ipExpr, $id, 'click')))
                {
                    $item -> unique_click_count = $item -> unique_click_count + 1;
                    $item -> click_count = $item -> click_count + 1;
                    
                    if($track = $tableTrackTable->checkExistTrack($today, $id)){
                        
                        $track -> unique_clicks = $track -> unique_clicks + 1;
                        $track -> clicks = $track -> clicks + 1;
                        $track -> save();
                    }
                    else{
                        $track = $tableTrackTable -> createRow();
                        $track -> date = $today;
                        $track -> ad_id = $id;
                        $track -> unique_clicks = 1;
                        $track -> clicks = 1;
                        $track -> save();
                    }
                }   
                else {
                    $item -> click_count = $item -> click_count + 1;
                    
                    if($track = $tableTrackTable->checkExistTrack($today, $id)){
                        $track -> clicks = $track -> clicks + 1;
                        $track -> save();
                    }
                    else{
                        $track = $tableTrackTable -> createRow();
                        $track -> date = $today;
                        $track -> ad_id = $id;
                        $track -> clicks = 1;
                        $track -> save();
                    }
                }
                
                //update view statistic
                $stats = $tableStatisticTable -> createRow();
                $stats -> IP = $ipExpr;
                $stats -> timestamp = date('Y-m-d H:i:s');
                $stats -> type = 'click';
                $stats -> ad_id = $id;
                $stats -> save();
            }   
            $item -> save();
        }
        //handle redirect
        if(!empty($item -> url))
            $this -> _redirect($item -> url);
        if(!empty($item -> item_id))
        {
            $module = Engine_Api::_()->getItem('ynsocialads_module',$item -> module_id);
            $table_item = $module -> table_item;
            $spec_item = Engine_Api::_()->getItem($table_item, $item -> item_id);
            // Prepare host info
            $schema = 'http://';
            if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]))
            {
                $schema = 'https://';
            }
            $host = $_SERVER['HTTP_HOST'];
            $this->_helper->redirector->gotoUrl($schema . $host .$spec_item->getHref());
        }
    }
    
    public function hiddenAction()
    {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if($viewer->getIdentity()){
            $params = $this->_getAllParams();
            $hiddenTable = Engine_Api::_() -> getItemTable('ynsocialads_hidden');
            switch ($params['type']) {
                case 'ad':
                        $hidden = $hiddenTable -> createRow();
                        $hidden -> user_id = $viewer -> getIdentity();
                        $hidden -> id = $params['id'];
                        $hidden -> type = 'ad';
                        $hidden -> save();
                    break;
                case 'owner':
                        $ad = Engine_Api::_()->getItem('ynsocialads_ad', $params['id']);
                        $hidden = $hiddenTable -> createRow();
                        $hidden -> user_id = $viewer -> getIdentity();
                        $hidden -> id = $ad->user_id;
                        $hidden -> type = 'owner';
                        $hidden -> save();
                    break;
                default:
                    break;
            }
        }
        else{
            $db = Engine_Db_Table::getDefaultAdapter();
            $ipObj = new Engine_IP();
            $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
            $params = $this->_getAllParams();
            $hiddenTable = Engine_Api::_() -> getItemTable('ynsocialads_hidden');
            switch ($params['type']) {
                case 'ad':
                        $hidden = $hiddenTable -> createRow();
                        $hidden -> IP = $ipExpr;
                        $hidden -> id = $params['id'];
                        $hidden -> type = 'ad';
                        $hidden -> save();
                    break;
                case 'owner':
                        $ad = Engine_Api::_()->getItem('ynsocialads_ad', $params['id']);
                        $hidden = $hiddenTable -> createRow();
                        $hidden -> IP = $ipExpr;
                        $hidden -> id = $ad->user_id;
                        $hidden -> type = 'owner';
                        $hidden -> save();
                    break;
                default:
                    break;
            }
        }
        echo Zend_Json::encode(array('json' => 'true'));
        return true;
    }
        
    public function viewPackageAction() {
        $this -> view -> package = $package = Engine_Api::_() -> getItem('ynsocialads_package', $this -> _getParam('id'));
    }

    public function updateStatusAction() {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');
        $status = $this -> _getParam('status');
        $this -> view -> ads_id = $id;
        $this -> view -> status = $status;
        // Check post
        if ($this -> getRequest() -> isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();
            try {
                $ads = Engine_Api::_() -> getItem('ynsocialads_ad', $id);
                if ($ads) {
                    if ($status == "Delete") {
                        $ads -> status = 'deleted';
                        $ads -> deleted = true;
                    }
                    if ($status == "Pause") {
                        $ads -> status = 'paused';
                    }
                    if ($status == "Resume") {
                        $ads -> status = 'running';
                    }
                    if ($status == "Publish") {
                        $ads -> status = 'pending';
                        if(!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'approve') -> checkRequire() && $ads->getPackage()->price == 0)
                        {
                            $ads->approve();
                        }
                    }
                    $ads -> save();
                }
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
            }

            $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 100, 'parentRefresh' => 100, 'messages' => array('')));
        }

        // Output
        $this -> renderScript('ads/update-status.tpl');
    }

    public function deleteSelectedAction() {
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true) {
            //Process delete
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();
            try {
                $ids_array = explode(",", $ids);
                foreach ($ids_array as $id) {
                    $ads = Engine_Api::_() -> getItem('ynsocialads_ad', $id);
                    if ($ads) {
                        $ads -> status = 'deleted';
                        $ads -> deleted = true;
                        $ads -> save();
                    }
                }
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
            }

            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }

    public function editAction() {

      $this -> _helper -> content
      // -> setNoRender()
      -> setEnabled();
      
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'edit') -> isValid())
            return;

        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $this -> _getParam('id', 0));
        
        //check permission on item
        if (!$ad -> isEditable())
            return;
        
        $this->view->ad = $ad;
        $package_id = $ad->package_id;

        $package = Engine_Api::_()->getItem('ynsocialads_package', $package_id);
        $this->view->package = $package;
        $this->view->modules = $modules = $package->getAllModules();
        
        $placements = $package->getAllPlacements();
        $this->view->placements = $placements;
        $placement = key($placements);
        $pages = $package->getAllPages($placement);
        $this->view->pages = $pages;
        $temp = explode('_', $placement);
        $preview_layout_src = 'application/modules/Ynsocialads/externals/images/widgets/'.$temp[0].'.png';
        $this->view->preview_layout_src = $preview_layout_src;
        
        $this->view->selectedAdblocks = $selectedAdblocks = $ad->getSelectAdBlocks();
        if ($ad->ad_type != 'feed') {
            $this->view->selectedPlacements = $selectedPlacements = $ad->getSelectPlacements();
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $campaignTbl = Engine_Api::_()->getItemTable('ynsocialads_campaign');
        $campaigns = $campaignTbl->fetchAll($campaignTbl->select()->where('status = ?', 'active')->where('user_id = ?', $viewer->getIdentity()));
        $this->view->campaigns = $campaigns;
        
        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $targetAvailable = $searchTable->info('cols');
        $this->view->targetAvailable = $targetAvailable;
        $target = $ad->getTarget();

        $this->view->target = $target;
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
        $this->view->countries = $territories;
        
        $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();
        $this->view->networks = $networks;
        
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('user', 'profile_type');
        if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;
        $profileTypeField = $profileTypeFields['profile_type'];
        
        $options = $profileTypeField->getOptions();
        $this->view->profileTypes = $options;
        
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        
        $this->view->timezone = $timezone;
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            
            $values['name'] = strip_tags($values['name']);
            if ($values['description']) $values['description'] = strip_tags($values['description']);
            if ($values['url']) $values['url'] = strip_tags($values['url']);
            if ($values['campaign_name']) $values['campaign_name'] = strip_tags($values['campaign_name']);
            if ($values['cities']) $values['cities'] = strip_tags($values['cities']);
            if ($values['interests']) $values['interests'] = strip_tags($values['interests']);
            
            if($values['item_type'] == 'internal') {
                $values['url'] = '';
                $values['module_id'] = $values['module'];
                $values['item_id'] = $values['item'];
            }
            else {
                $values['module_id'] = null;
                $values['item_id'] = null;
            }
            unset($values['item_type']);           
       
            if ($values['campaign_id'] == '0' && $values['campaign_name'] != null) {
                $values['campaign_id'] = $ad->createCampaign($values['campaign_name']);
            }
            
            if ($values['schedule'] == 'specify') {
                    $oldTz = date_default_timezone_get();
                    date_default_timezone_set($viewer->timezone);
                    $start = strtotime($values['start_date']);
                    $end = strtotime($values['end_date']);
                    date_default_timezone_set($oldTz);
                    $values['start_date'] = date('Y-m-d H:i:s', $start);
                    $values['end_date'] = date('Y-m-d H:i:s', $end);
                }
                else {
                    $values['start_date'] = null;
                    $values['end_date'] = null;
                }
             
            $ad->setFromArray($values);
            $ad->save();
            
            if (!$values['birthday']) {
                $values['birthday'] = 0;
            }
            if (!$values['public']) {
                $values['public'] = 0;
            }
            $targetTbl = Engine_Api::_()->getItemTable('ynsocialads_adtarget');
            $ad_target = $targetTbl->fetchRow($targetTbl->select()->where('ad_id = ?', $ad->getIdentity()));
            if (!$values['countries']) $values['countries'] = null;
            if (!$values['networks']) $values['networks'] = null;
            $ad_target->setFromArray($values);
            $ad_target->save();
            
            $mappingTbl = Engine_Api::_()->getItemTable('ynsocialads_mapping');
            $db = $mappingTbl->getAdapter();
            $db->delete($mappingTbl->info('name'), 'ad_id = '.$ad->getIdentity());
            foreach ($values['block_id'] as $adblock_id) {
                $ad_block = Engine_Api::_()->getItem('ynsocialads_adblock', $adblock_id);
                $mapping = $mappingTbl->createRow();
                $mapping->ad_id = $ad->ad_id;
                $mapping->adblock_id = $ad_block->getIdentity();
                $mapping->content_id = $ad_block->content_id;
                $mapping->save();
            }
            if (isset($values['placement'])) {
                if (in_array('footer', $values['placement'])) {
                    $adblockTbl = Engine_Api::_()->getItemTable('ynsocialads_adblock');
                    $ad_block = $adblockTbl->fetchRow($adblockTbl->select()->where('page_id = ?', '2') -> where('deleted = ?', 0));
                    $mapping = $mappingTbl->createRow();
                    $mapping->ad_id = $ad->ad_id;
                    $mapping->adblock_id = $ad_block->getIdentity();
                    $mapping->content_id = $ad_block->content_id;
                    $mapping->save();
                } 
            }
                            
            if( !empty($_FILES['photo']) ) {
                $ad->setPhoto($_FILES['photo']);
            }
            if ($values['place_order'] == '1') {
                if ($package->price != 0) {
                    $this->_redirect('socialads/ads/place-order/id/'.$ad->ad_id);
                }
                else {
                    $ad->status = 'pending';
                    if(!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'approve') -> checkRequire())
                    {
                        $ad->approve();
                    }
                    $ad->save();
                }
            }
            $this->_redirect('socialads/ads/view/id/'.$ad->ad_id);
            }
    }

    public function chartDataAction() 
    {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        // Get params
        $start = $this -> _getParam('start');
        $offset = $this -> _getParam('offset', 0);
        $type = $this -> _getParam('type', 'all');
        $mode = $this -> _getParam('mode');
        $chunk = $this -> _getParam('chunk');
        $period = $this -> _getParam('period');
        $periodCount = $this -> _getParam('periodCount', 1);
        
        $ad_ids = $this -> _getParam('ad_ids', '');

        // Validate chunk/period
        if (!$chunk || !in_array($chunk, $this -> _periods)) {
            $chunk = Zend_Date::DAY;
        }
        if (!$period || !in_array($period, $this -> _periods)) {
            $period = Zend_Date::MONTH;
        }
        if (array_search($chunk, $this -> _periods) >= array_search($period, $this -> _periods)) {
            die('whoops');
            return;
        }

        // Validate start
        if ($start && !is_numeric($start)) {
            $start = strtotime($start);
        }
        if (!$start) {
            $start = time();
        }

        // Fixes issues with month view
        Zend_Date::setOptions(array('extend_month' => true, ));

        // Get timezone
        $timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core_locale_timezone', 'GMT');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer && $viewer -> getIdentity() && !empty($viewer -> timezone)) {
            $timezone = $viewer -> timezone;
        }

        // Make start fit to period?
        $startObject = new Zend_Date($start);
        $startObject -> setTimezone($timezone);

        $partMaps = $this -> _periodMap[$period];
        foreach ($partMaps as $partType => $partValue) {
            $startObject -> set($partValue, $partType);
        }

        // Do offset
        if ($offset != 0) {
            $startObject -> add($offset, $period);
        }

        // Get end time
        $endObject = new Zend_Date($startObject -> getTimestamp());
        $endObject -> setTimezone($timezone);
        $endObject -> add($periodCount, $period);
        $endObject -> sub(1, Zend_Date::SECOND);
        // Subtract one second
        $staTable = Engine_Api::_() -> getDbtable('statistics', 'ynsocialads');
        $staName = $staTable -> info('name');
        $arr_ads = array();
        if($ad_ids)
        {
            $arr_ads = explode(',', $ad_ids);
        }
        else {
        }
        if ($type != "all")
        {
            // Get data
            $select = $staTable -> select();
            $select -> where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('timestamp ASC');
            $select -> where('type = ?', $type) -> where('ad_id IN (?)', $arr_ads);
            $rawData = $staTable -> fetchAll($select);

            // Now create data structure
            $currentObject = clone $startObject;
            $nextObject = clone $startObject;
            $data = array();
            $dataLabels = array();
            $cumulative = 0;
            $previous = 0;
    
            do {
                $nextObject -> add(1, $chunk);
                $currentObjectTimestamp = $currentObject -> getTimestamp();
                $nextObjectTimestamp = $nextObject -> getTimestamp();
                $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;
    
                // Get everything that matches
                $currentPeriodCount = 0;
                foreach ($rawData as $rawDatum) {
                    $rawDatumDate = strtotime($rawDatum -> timestamp);
                    if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
                        $currentPeriodCount += 1;
                    }
                }
    
                // Now do stuff with it
                switch( $mode ) {
                    default :
                    case 'normal' :
                        $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount;
                        break;
                    case 'cumulative' :
                        $cumulative += $currentPeriodCount;
                        $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;
                        break;
                    case 'delta' :
                        $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount - $previous;
                        $previous = $currentPeriodCount;
                        break;
                }
    
                $currentObject -> add(1, $chunk);
            } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );
    
            // Remove some grid lines if there are too many
            $xsteps = 1;
            if (count($data) > 100) {
                $xsteps = ceil(count($data) / 100);
            }
            $title = $this -> view -> locale() -> toDate($startObject) . ' to ' . $this -> view -> locale() -> toDate($endObject);
            echo Zend_Json::encode(array('json' => $data, 'title' => $title));
        }
        else
        {
            // Get data
            $selectClick = $staTable -> select();
            $selectClick -> where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('timestamp ASC');
            $selectClick -> where("type = 'click'");
            $selectClick -> where('ad_id IN (?)', $arr_ads);
            $clickData = $staTable -> fetchAll($selectClick);
            
            $selectImpression = $staTable -> select();
            $selectImpression -> where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('timestamp ASC');
            $selectImpression -> where("type = 'impression'");
            $selectImpression -> where('ad_id IN (?)', $arr_ads);
            $impressionData = $staTable -> fetchAll($selectImpression);

            // Now create data structure
            $currentObject = clone $startObject;
            $nextObject = clone $startObject;
            $dataClick = array();
            $dataImpression = array();
            $dataLabels = array();
            $cumulativeClick = 0;
            $cumulativeIm = 0;
            $previousClick = 0;
            $previousIm = 0;
    
            do {
                $nextObject -> add(1, $chunk);
                $currentObjectTimestamp = $currentObject -> getTimestamp();
                $nextObjectTimestamp = $nextObject -> getTimestamp();
                
                $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeClick;
                $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeIm;
    
                // Get everything that matches
                $currentPeriodCountClick = 0;
                foreach ($clickData as $rawDatum) 
                {
                    $rawDatumDate = strtotime($rawDatum -> timestamp);
                    if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) 
                    {
                        $currentPeriodCountClick += 1;
                    }
                }
                
                $currentPeriodCountIm = 0;
                foreach ($impressionData as $rawDatum) 
                {
                    $rawDatumDate = strtotime($rawDatum -> timestamp);
                    if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) 
                    {
                        $currentPeriodCountIm += 1;
                    }
                }
    
                // Now do stuff with it
                switch( $mode ) {
                    default :
                    case 'normal' :
                        $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountClick;
                        $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountIm;
                        break;
                    case 'cumulative' :
                        $cumulativeClick += $currentPeriodCountClick;
                        $cumulativeIm += $currentPeriodCountIm;
                        $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeClick;
                        $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeIm;
                        break;
                    case 'delta' :
                        $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountClick - $previousClick;
                        $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountIm - $previousIm;
                        $previousClick = $currentPeriodCountClick;
                        $previousIm = $currentPeriodCountIm;
                        break;
                }
    
                $currentObject -> add(1, $chunk);
            } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );
    
            // Remove some grid lines if there are too many
            $xsteps = 1;
            if (count($dataClick) > 100) 
            {
                $xsteps = ceil(count($dataClick) / 100);
            }
            $title = $this -> view -> locale() -> toDate($startObject) . ' to ' . $this -> view -> locale() -> toDate($endObject);
            echo Zend_Json::encode(array('json' => $dataClick, 'title' => $title, 'json2' => $dataImpression));
        }
        return true;
    }

    public function placeOrderAction() 
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $this ->_getParam('id'));
        if ($ad -> status != 'draft' && $ad -> status != 'unpaid') {
            return $this -> _redirector();
        }
        if($ad->user_id != $viewer->getIdentity())
        {
            return $this -> _redirector();
        }
        
		//Credit
        //check permission
        // Get level id
        $id = $viewer->level_id;
    
        if ($this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'pay_credit') -> checkRequire()) {
            //TODO add implement code here
            $allowPayCredit = 0;
            $credit_enable = Engine_Api::_() -> ynsocialads() -> checkYouNetPlugin('yncredit');
            if ($credit_enable)
            {
                $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
                $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'publish_ads'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
				if($type_spend)
				{
					$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
					$select = $creditTbl->select()
		                ->where("level_id = ? ", $id)
		                ->where("type_id = ?", $type_spend -> type_id)
		                ->limit(1);
		            $spend_credit = $creditTbl->fetchRow($select);
					if($spend_credit)
					{
		               $allowPayCredit = 1;
		            }
				}
			}
            $this -> view -> allowPayCredit = $allowPayCredit;
        };
		
        $package = $ad -> getPackage();
        $this -> view -> ad = $ad;
        $this -> view -> package = $package;
        $this -> view -> total_pay = $total_pay = ($ad -> benefit_total*($package -> price / $package -> benefit_amount));
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit) || !$package) {
            return $this -> _redirector();
        }
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynsocialads');
        if ($row = $ordersTable -> getLastPendingOrder()) {
            $row -> delete();
        }

        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();

        try 
        {
            $ordersTable -> insert(array('user_id' => $viewer -> getIdentity(), 'creation_date' => new Zend_Db_Expr('NOW()'), 'package_id' => $package -> getIdentity(), 'ad_id' => $ad -> getIdentity(), 'price' => $total_pay, 'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), ));
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;

        //Pay Later
        //check permission
        if ($this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'pay_later') -> checkRequire()) {
            //TODO add implement code here
            $allowPayLater = 0;
            $settings = Engine_Api::_() -> getApi('settings', 'core');
            $paylaterexpiretime = $settings -> getSetting('ynsocialads_paylaterexpiretime');
            $start = new DateTime($ad -> start_date);
            $create = new DateTime($ad -> creation_date);
            $interval = $create -> diff($start);
            if ($interval -> format('%R%a') >= $paylaterexpiretime) 
            {
                $allowPayLater = 1;
            }
            $this -> view -> allowPayLater = $allowPayLater;
        };

        //Virtual Money
        //check permission
        if ($this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'virtual_money') -> checkRequire()) {
            //TODO add implement code here
            $allowPayVirtual = 0;
            $row = Engine_Api::_() -> getItemTable('ynsocialads_virtual') -> GetRowByUser($viewer -> getIdentity());
            if ($row->remain >= $ad -> getPackage() -> price) {
                $allowPayVirtual = 1;
            }
            $this -> view -> allowPayVirtual = $allowPayVirtual;
        };

        
    }

    public function updateOrderAction() 
    {
        $type = $this ->_getParam('type');
        $id = $this ->_getParam('id');
        if(isset($type))
        {
            switch ($type) {
                
                case 'paylater':
                    return $this -> _forward('success', 'utility', 'core', 
                    array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                    array(
                        'controller'=>'ads',
                        'action' => 'pay-later', 
                        'id' => $id), 
                        'ynsocialads_extended', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                case 'payvirtual':
                    return $this -> _forward('success', 'utility', 'core', 
                        array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                        array(
                        'controller'=>'ads',
                        'action' => 'pay-virtual', 
                        'id' => $id), 
                        'ynsocialads_extended', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                case 'paycredit':
                    return $this -> _forward('success', 'utility', 'core', 
                        array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                        array(
                        'controller'=>'ads',
                        'action' => 'pay-credit', 
                        'item_id' => $id), 
                        'ynsocialads_extended', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                default:
                    
                    break;
            }
        }

        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $id);
        $ad -> status = 'unpaid';
        $ad -> save();
            
        $gateway_id = $this -> _getParam('gateway_id', 0);
        if (!$gateway_id) {
            return $this -> _redirector();
        }

        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            return $this -> _redirector();
        }

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynsocialads');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            return $this -> _redirector();
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'ynsocialads', 'cancel_route' => 'ynsocialads_transaction', 'return_route' => 'ynsocialads_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'transaction', 'action' => 'process', 'order_id' => $order -> getIdentity(), ), 'ynsocialads_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }

    ///HoangND
    public function viewAction() 
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'view') -> isValid())
            return;

        if (!$this -> _hasParam('id')) {
            $this -> _helper -> content -> setNoRender();
            return;
        }
        if (null == ($ad_id = $this -> _getParam('id')))
            return;
        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $ad_id);

        if (!$ad) {
            $this -> _helper -> content -> setNoRender();
        }
        
        $this -> view -> ad = $ad;
        $this -> view -> viewer = $viewer;
        Engine_Api::_()->ynsocialads()->checkAndUpdateStatus($ad);
        $this -> view -> campaign = $campaign = Engine_Api::_() -> getItem('ynsocialads_campaign', $ad -> campaign_id);
        $this -> view -> formStatistic = $formStatistic = new Ynsocialads_Form_Ads_ViewStatistics();
        //calculate remaining
        $this -> view -> remain = $ad -> getRemain();
        
        $this -> _helper -> content
        -> setEnabled();
        $this -> statisticsAction($ad_id);
    }

    private function statisticsAction($ad_id) {
        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $ad_id);
        // Get timezoney
        $sysTimezone = date_default_timezone_get();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        } 
        $this->view->timezone = $timezone;
        
        if (($ad -> running_date)) { 
            $runningDate = new Zend_Date(strtotime($ad -> running_date));
            $runningDate->setTimezone($sysTimezone);
            $todate = new Zend_Date();
            
            if ($ad->end_date) {
                $endAdDate = new Zend_Date(strtotime($ad -> end_date));
                $endAdDate->setTimezone($sysTimezone);
                if ($endAdDate < $todate)
                    $endObject = $endAdDate;
                else
                    $endObject = $todate;
                }
            else {
                $endObject = $todate;
            }
            // Make start fit to period?
            $chunk = 'dd';
            $startObject = new Zend_Date($endObject -> getTimestamp());
            $startObject -> sub(6, $chunk);
    
            if ($startObject < $runningDate) {
                $startObject = $runningDate;
            }
            $startObject -> setHour(0);
            $startObject -> setMinute(0);
            $startObject -> setSecond(0);
            
            $staTable = Engine_Api::_()->getDbtable('tracks', 'ynsocialads');
            $staName = $staTable ->info('name');
            $select = $staTable->select();
            $select
            -> where('ad_id = ?', $ad_id)
            -> where('date >= ?', $startObject->get('yyyy-MM-dd') )
            -> where('date <= ?', $endObject->get('yyyy-MM-dd'));
            
            $data = $staTable->fetchAll($select);      
        }
        else {
            $data = array();
        } 
        // Remove some grid lines if there are too many
        $this -> view -> data = ($data);
    }

    public function createSimilarAction() {
        //check create permissions
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'create') -> isValid())
            return;
        $this -> _helper -> layout -> setLayout('admin-simple');
        if (!$this -> _hasParam('id')) {
            $this -> _helper -> content -> setNoRender();
            return;
        }
        $ad_id = $this -> _getParam('id');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $ad_id);
        if ($ad->user_id != $viewer->getIdentity()) {
            return;
        }
        
        if ($this -> getRequest() -> isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $table = Engine_Api::_() -> getDbTable('ads', 'ynsocialads');
            $targetTbl = Engine_Api::_() -> getItemTable('ynsocialads_adtarget');
            $db -> beginTransaction();
            try {           
                if ($ad) 
                {
                    $similar_ad = $table -> createRow();
                    $arr = $ad -> toArray();
                    unset($arr['ad_id']);
                    unset($arr['creation_date']);
                    unset($arr['modified_date']);
                    unset($arr['click_count']);
                    unset($arr['impressions_count']);
                    unset($arr['unique_click_count']);
                    unset($arr['reaches_count']);
                    $arr['status'] = 'draft';
                    $arr['approved'] = false;
                    $arr['deleted'] = false;
                    $similar_ad -> setFromArray($arr);
                    $similar_ad -> save();
                    $new_ad = $similar_ad->ad_id;
                    
                    $target = $ad->getTarget();
                    $target = $target->toArray();
                    $cloneTarget = $targetTbl->createRow();
                    unset($target['adtarget_id']);
                    $cloneTarget -> setFromArray($target);
                    $cloneTarget -> ad_id = $new_ad;
                    $cloneTarget -> save();
                    
                    $mappingTbl = Engine_Api::_()->getItemTable('ynsocialads_mapping');
                    $adblock_ids = $mappingTbl->fetchAll($mappingTbl->select()->where('ad_id = ?', $ad->ad_id));
                    foreach ($adblock_ids as $adblock_id) 
                    {
                        $mapping = $mappingTbl->createRow();
                        $mapping->ad_id = $similar_ad->ad_id;
                        $mapping->adblock_id = $adblock_id->adblock_id;
                        $mapping->content_id = $adblock_id->content_id;
                        $mapping->save();
                    }
                    if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
                    {
                        Engine_Api::_()->yncredit()-> hookCustomEarnCredits($similar_ad -> getOwner(), $similar_ad -> name, 'ynsocialads_new', $similar_ad);
                    }
                }
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
            }
            
            $new = Engine_Api::_()->getItem('ynsocialads_ad', $new_ad);
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $new->getHref(),
                'format' => 'smoothbox',
                'messages' => array($this->view->translate("Sending request!"))
            ));
        }
    }

    public function chartAdAction() {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        if (!$this -> _hasParam('id')) {
            $this -> _helper -> content -> setNoRender();
            return;
        }
        if (null == ($ad_id = $this -> _getParam('id')))
            return;

        $viewer = Engine_Api::_() -> user() -> getViewer();

        // Get params
        $start = $this -> _getParam('start');
        $offset = $this -> _getParam('offset', 0);
        $type = $this -> _getParam('type', 'all');
        $mode = $this -> _getParam('mode');
        $chunk = $this -> _getParam('chunk');
        $period = $this -> _getParam('period');
        $periodCount = $this -> _getParam('periodCount', 1);

        // Validate chunk/period
        if (!$chunk || !in_array($chunk, $this -> _periods)) {
            $chunk = Zend_Date::DAY;
        }
        if (!$period || !in_array($period, $this -> _periods)) {
            $period = Zend_Date::MONTH;
        }
        if (array_search($chunk, $this -> _periods) >= array_search($period, $this -> _periods)) {
            die('whoops');
            return;
        }

        // Validate start
        if ($start && !is_numeric($start)) {
            $start = strtotime($start);
        }
        if (!$start) {
            $start = time();
        }

        // Fixes issues with month view
        Zend_Date::setOptions(array('extend_month' => true, ));

        // Get timezone
        $timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core_locale_timezone', 'GMT');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer && $viewer -> getIdentity() && !empty($viewer -> timezone)) {
            $timezone = $viewer -> timezone;
        }

        // Make start fit to period?
        $startObject = new Zend_Date($start);
        $startObject -> setTimezone($timezone);

        $partMaps = $this -> _periodMap[$period];
        foreach ($partMaps as $partType => $partValue) {
            $startObject -> set($partValue, $partType);
        }

        // Do offset
        if ($offset != 0) {
            $startObject -> add($offset, $period);
        }

        // Get end time
        $endObject = new Zend_Date($startObject -> getTimestamp());
        $endObject -> setTimezone($timezone);
        $endObject -> add($periodCount, $period);
        $endObject -> sub(1, Zend_Date::SECOND);
        // Subtract one second
        $staTable = Engine_Api::_() -> getDbtable('statistics', 'ynsocialads');
        $staName = $staTable -> info('name');
        if ($type != "all")
        {
            // Get data
            $select = $staTable -> select();
            $select -> where('ad_id = ?', $ad_id) -> where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('timestamp ASC');
            $select -> where('type = ?', $type);
            $rawData = $staTable -> fetchAll($select);

            // Now create data structure
            $currentObject = clone $startObject;
            $nextObject = clone $startObject;
            $data = array();
            $dataLabels = array();
            $cumulative = 0;
            $previous = 0;
    
            do {
                $nextObject -> add(1, $chunk);
                $currentObjectTimestamp = $currentObject -> getTimestamp();
                $nextObjectTimestamp = $nextObject -> getTimestamp();
                $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;
    
                // Get everything that matches
                $currentPeriodCount = 0;
                foreach ($rawData as $rawDatum) {
                    $rawDatumDate = strtotime($rawDatum -> timestamp);
                    if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
                        $currentPeriodCount += 1;
                    }
                }
    
                // Now do stuff with it
                switch( $mode ) {
                    default :
                    case 'normal' :
                        $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount;
                        break;
                    case 'cumulative' :
                        $cumulative += $currentPeriodCount;
                        $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;
                        break;
                    case 'delta' :
                        $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount - $previous;
                        $previous = $currentPeriodCount;
                        break;
                }
    
                $currentObject -> add(1, $chunk);
            } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );
    
            // Remove some grid lines if there are too many
            $xsteps = 1;
            if (count($data) > 100) {
                $xsteps = ceil(count($data) / 100);
            }
            $title = $this -> view -> locale() -> toDate($startObject) . ' to ' . $this -> view -> locale() -> toDate($endObject);
            echo Zend_Json::encode(array('json' => $data, 'title' => $title));
        }
        else
        {
            // Get data
            $selectClick = $staTable -> select();
            $selectClick -> where('ad_id = ?', $ad_id) -> where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('timestamp ASC');
            $selectClick -> where("type = 'click'");
            $clickData = $staTable -> fetchAll($selectClick);
            
            $selectImpression = $staTable -> select();
            $selectImpression -> where('ad_id = ?', $ad_id) -> where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('timestamp ASC');
            $selectImpression -> where("type = 'impression'");
            $impressionData = $staTable -> fetchAll($selectImpression);

            // Now create data structure
            $currentObject = clone $startObject;
            $nextObject = clone $startObject;
            $dataClick = array();
            $dataImpression = array();
            $dataLabels = array();
            $cumulativeClick = 0;
            $cumulativeIm = 0;
            $previousClick = 0;
            $previousIm = 0;
    
            do {
                $nextObject -> add(1, $chunk);
                $currentObjectTimestamp = $currentObject -> getTimestamp();
                $nextObjectTimestamp = $nextObject -> getTimestamp();
                
                $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeClick;
                $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeIm;
    
                // Get everything that matches
                $currentPeriodCountClick = 0;
                foreach ($clickData as $rawDatum) 
                {
                    $rawDatumDate = strtotime($rawDatum -> timestamp);
                    if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) 
                    {
                        $currentPeriodCountClick += 1;
                    }
                }
                
                $currentPeriodCountIm = 0;
                foreach ($impressionData as $rawDatum) 
                {
                    $rawDatumDate = strtotime($rawDatum -> timestamp);
                    if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) 
                    {
                        $currentPeriodCountIm += 1;
                    }
                }
    
                // Now do stuff with it
                switch( $mode ) {
                    default :
                    case 'normal' :
                        $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountClick;
                        $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountIm;
                        break;
                    case 'cumulative' :
                        $cumulativeClick += $currentPeriodCountClick;
                        $cumulativeIm += $currentPeriodCountIm;
                        $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeClick;
                        $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulativeIm;
                        break;
                    case 'delta' :
                        $dataClick[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountClick - $previousClick;
                        $dataImpression[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCountIm - $previousIm;
                        $previousClick = $currentPeriodCountClick;
                        $previousIm = $currentPeriodCountIm;
                        break;
                }
    
                $currentObject -> add(1, $chunk);
            } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );
    
            // Remove some grid lines if there are too many
            $xsteps = 1;
            if (count($dataClick) > 100) 
            {
                $xsteps = ceil(count($dataClick) / 100);
            }
            $title = $this -> view -> locale() -> toDate($startObject) . ' to ' . $this -> view -> locale() -> toDate($endObject);
            echo Zend_Json::encode(array('json' => $dataClick, 'title' => $title, 'json2' => $dataImpression));
        }   
        return true;
    }

    public function createChoosePackageAction() {
        //TODO
        //add page
        //check create permissions
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'create') -> isValid())
            return;

        $this -> view -> level = $viewer_level = Engine_Api::_() -> user() -> getViewer() -> level_id;
        $table = Engine_Api::_() -> getItemTable('ynsocialads_package');
        $select = $table->select()->where('`show` = 1')->where('`deleted` = 0')->order('order ASC');
        $packages = $table -> fetchAll($select);
        $this -> view -> packages = $packages;
        
     //   TODO un-install for use
        $this -> _helper -> content
        -> setEnabled();
    }
    
    public function createStepOneAction() 
    {
        $this -> _helper -> content
        -> setEnabled();
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'create') -> isValid())
            return;

        $this -> view -> level = $viewer_level = Engine_Api::_() -> user() -> getViewer() -> level_id;
        
        if (null == ($package_id = $this->_getParam('package_id'))) {
            return;
        }
        $package = Engine_Api::_()->getItem('ynsocialads_package', $package_id);
        if(!$package || !$package->isViewable() || !$package->show) return;
        $this->view->package = $package;
        $this->view->modules = $modules = $package->getAllModules();
        $placements = $package->getAllPlacements();
        $this->view->placements = $placements;
        $placement = key($placements);
        $pages = $package->getAllPages($placement);
        $this->view->pages = $pages;
        $temp = explode('_', $placement);
        $preview_layout_src = 'application/modules/Ynsocialads/externals/images/widgets/'.$temp[0].'.png';
        $this->view->preview_layout_src = $preview_layout_src;
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $campaignTbl = Engine_Api::_()->getItemTable('ynsocialads_campaign');
        $campaigns = $campaignTbl->fetchAll($campaignTbl->select()->where('status = ?', 'active')->where('user_id = ?', $viewer->getIdentity()));
        $this->view->campaigns = $campaigns;
        
        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $targetAvailable = $searchTable->info('cols');
        $this->view->targetAvailable = $targetAvailable;
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
        $this->view->countries = $territories;
        
        $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();
        $this->view->networks = $networks;
        
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('user', 'profile_type');
        if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;
        $profileTypeField = $profileTypeFields['profile_type'];
        
        $options = $profileTypeField->getOptions();
        $this->view->profileTypes = $options;
        
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            
            $values['name'] = strip_tags($values['name']);
            if ($values['description']) $values['description'] = strip_tags($values['description']);
            if ($values['url']) $values['url'] = strip_tags($values['url']);
            if ($values['campaign_name']) $values['campaign_name'] = strip_tags($values['campaign_name']);
            if ($values['cities']) $values['cities'] = strip_tags($values['cities']);
            if ($values['interests']) $values['interests'] = strip_tags($values['interests']);
            
            $values['package_id'] = $package->package_id;
            $values['user_id'] = $viewer->getIdentity();
            if($values['item_type'] == 'internal') {
                $values['url'] = '';
                $values['module_id'] = $values['module'];
                $values['item_id'] = $values['item'];
            }
            else {
                $values['module_id'] = null;
                $values['item_id'] = null;
            }
            unset($values['item_type']);
                       
            if ($package->price == 0) {
                $values['benefit_total'] = $package->benefit_amount;
            }
            $db = Engine_Api::_()->getItemTable('ynsocialads_ad')->getAdapter();
            $db->beginTransaction();
            try {
                $table = Engine_Api::_()->getItemTable('ynsocialads_ad');
                $ad = $table->createRow();
                
                if ($values['campaign_id'] == '0' && $values['campaign_name'] != null) {
                    $values['campaign_id'] = $ad->createCampaign($values['campaign_name']);
                }
                
                if ($values['schedule'] == 'specify') {
                    $oldTz = date_default_timezone_get();
                    date_default_timezone_set($timezone);
                    $start = strtotime($values['start_date']);
                    $end = strtotime($values['end_date']);
                    date_default_timezone_set($oldTz);
                    $values['start_date'] = date('Y-m-d H:i:s', $start);
                    $values['end_date'] = date('Y-m-d H:i:s', $end);
                }
                else 
                {
                    $values['start_date'] = null;
                    $values['end_date'] = null;
                }
                
                $ad->setFromArray($values);
                $ad->save();
                
                if ($package->price == 0 && $values['draft'] == '0') {
                    $ad->status = 'pending';
                    if(!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'approve') -> checkRequire())
                    {
                        $ad->approve();
                    }
                    $ad->save();
                }
                
                if (!$values['birthday']) {
                    $values['birthday'] = 0;
                }
                if (!$values['public']) {
                    $values['public'] = 0;
                }
                $targetTbl = Engine_Api::_()->getItemTable('ynsocialads_adtarget');
                $ad_target = $targetTbl->createRow();
                $ad_target->ad_id = $ad->ad_id;
                $ad_target->setFromArray($values);
                $ad_target->save();
                
                $mappingTbl = Engine_Api::_()->getItemTable('ynsocialads_mapping');
                $values['block_id'] = array_unique($values['block_id']);
                foreach ($values['block_id'] as $adblock_id) 
                {
                    $ad_block = Engine_Api::_()->getItem('ynsocialads_adblock', $adblock_id);
                    $mapping = $mappingTbl->createRow();
                    $mapping->ad_id = $ad->ad_id;
                    $mapping->adblock_id = $ad_block->getIdentity();
                    $mapping->content_id = $ad_block->content_id;
                    $mapping->save();
                }
                if (isset($values['placement'])) 
                {
                    if (in_array('footer', $values['placement'])) 
                    {
                        $adblockTbl = Engine_Api::_()->getItemTable('ynsocialads_adblock');
                        $ad_block = $adblockTbl->fetchRow($adblockTbl->select()->where('page_id = ?', '2') -> where('deleted = ?', 0));
                        $mapping = $mappingTbl->createRow();
                        $mapping->ad_id = $ad->ad_id;
                        $mapping->adblock_id = $ad_block->getIdentity();
                        $mapping->content_id = $ad_block->content_id;
                        $mapping->save();
                    }
                }
                                
                if( !empty($_FILES['photo']) ) {
                    $ad->setPhoto($_FILES['photo']);
                }
                
                if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
                {
                    Engine_Api::_()->yncredit()-> hookCustomEarnCredits($ad -> getOwner(), $ad -> name, 'ynsocialads_new', $ad);
                }
            }
            catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }       
    
            $db->commit();
            
            if ($package->price == 0 || $values['draft'] == '1') {
                $this->_redirect('socialads/ads/view/id/'.$ad->ad_id);
            }
            $this->_redirect('socialads/ads/place-order/id/'.$ad->ad_id);
        }
    }
    
    public function getItemsAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if (null == ($module_id = $this->_getParam('module_id'))) {
            return false;
        }
        $modulesTbl = Engine_Api::_()->getItemTable('ynsocialads_module');
        $modulesTblName = $modulesTbl->info('name');
        $select = $modulesTbl->select()->from($modulesTblName, array('table_item', 'title_field', 'owner_field'))->where('module_id = ?', $module_id);
        $item = $modulesTbl->fetchRow($select);
        $allItems = array();
        $tempTbl = Engine_Api::_()->getItemTable($item->table_item);
        $rawData = $tempTbl->fetchAll($tempTbl->select()->where($item->owner_field.' = ?',$viewer->getIdentity() ));
        foreach ($rawData as $datum) {
            $str = html_entity_decode($datum[$item->title_field], ENT_COMPAT, "UTF-8");
            if ($str == '') $str = $this->view->translate('untitled').'-'.$datum->getIdentity();
            $allItems[$datum->getIdentity()] = $str;
        }
        
        echo Zend_Json::encode(array('json' => $allItems));
        return true;
    }
    
    public function getPagesAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if (null == ($package_id = $this->_getParam('package_id'))) {
            return false;
        }
        if (null == ($placement = $this->_getParam('placement'))) {
            return false;
        }
        $package = Engine_Api::_()->getItem('ynsocialads_package', $package_id);
        if (!$package) return;
        $pages = $package->getAllPages($placement);    
        echo Zend_Json::encode(array('json' => $pages));
        return true;
    }
    
    public function countAudiencesAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $success = true;
        if (!$this->getRequest()->isPost())
            return;
        
        $data = $this->getRequest()->getPost('json');
        $options = json_decode(($data));
        $options =  get_object_vars($options);
        
        foreach ($options as &$option) {
            if (is_object($option)) {
                $option = get_object_vars($option);
            }
        }
        
        $result = Engine_Api::_()->ynsocialads()->getAudiences($options);
        
        echo count($result);
    }

    public function payLaterAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'pay_later') -> isValid()) {
            return $this -> _redirector();
        }
        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $this -> _getParam('id'));
        if($ad->status !='draft')
        {
            return $this -> _redirector();
        }
        
        if($ad->user_id != $viewer->getIdentity())
        {
            return $this -> _redirector();
        }
        
        $transactionTable = Engine_Api::_() -> getDbTable('transactions', 'ynsocialads');
        $db = $transactionTable -> getAdapter();
        $db -> beginTransaction();
        $package = $ad->getPackage();
        try {
            $transAd = $transactionTable -> createRow();
            $transAd -> start_date = $ad -> start_date;
            $transAd -> status = 'initialized';
            $transAd -> gateway_id = '-2';
            $transAd -> amount = ($ad -> benefit_total*($package -> price / $package -> benefit_amount));
            $transAd -> currency = $package -> currency;
            $transAd -> ad_id = $ad -> getIdentity();
            $transAd -> user_id = $ad -> user_id;
            $transAd -> save();
            
            $ad->status = 'unpaid';
            $ad->save();
            // Commit
            $db -> commit();
        } catch( Exception $e ) {
            $db -> rollBack();
            throw $e;
        }
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'view', 'id' => $ad -> getIdentity()), 'ynsocialads_ads', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay later!'))));
    }

    public function payVirtualAction() {
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynsocialads_ad', null, 'virtual_money') -> isValid()) {
            return $this -> _redirector();
        }
        $this->view->item = $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $this -> _getParam('id'));
        if($ad->status !='draft' && $ad->status !='unpaid')
        {
            return $this -> _redirector();
        }
        if($ad->user_id != $viewer->getIdentity())
        {
            return $this -> _redirector();
        }
        
        $virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
        $row = $virtualTable -> GetRowByUser($ad -> user_id);
        $this->view->currentVirtualBalance = $row -> remain;
        $this->view->cancel_url = $cancel_url = Zend_Controller_Front::getInstance()->getRouter()
                ->assemble(
                  array(
                    'action' => 'place-order',
                    'id' => $ad -> getIdentity()
                  ), 'ynsocialads_ads', true);
                  
        $transactionTable = Engine_Api::_() -> getDbTable('transactions', 'ynsocialads');
        $select = $transactionTable -> select() -> where('ad_id = ?', $this -> _getParam('id')) -> limit(1);
        $item = $transactionTable -> fetchRow($select);
        $this -> view -> package = $package =$ad -> getPackage();
        $this -> view -> total_pay = $total_pay = ($ad -> benefit_total)*($package -> price / $package -> benefit_amount);
        if (!$this->getRequest()->isPost()) 
        {
          return;
        }
        if (!isset($item)) 
        {
            $db = $transactionTable -> getAdapter();
            $db -> beginTransaction();
            try 
            {
                $transAd = $transactionTable -> createRow();
                $transAd -> start_date = $ad -> start_date;
                $transAd -> status = 'completed';
                $transAd -> gateway_id = '-1';
                $transAd -> amount = $total_pay;
                $transAd -> currency = $package -> currency;
                $transAd -> ad_id = $ad -> getIdentity();
                $transAd -> user_id = $ad -> user_id;
                $transAd -> save();

                $virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
                $row = $virtualTable -> GetRowByUser($ad -> user_id);
                $row -> total = $row -> total - $total_pay;
                $row -> remain = $row -> remain - $total_pay;
                $row -> save();

                $ad -> status = 'pending';
                $ad -> save();
                // Commit
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
            }
        }
        // already paylater
        else {
            $item -> status = 'completed';
            $item -> gateway_id = '-1';
            $item -> save();
            $ad -> status = 'pending';
            $ad -> save();
            $virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
            $row = $virtualTable -> GetRowByUser($ad -> user_id);
            $row -> total = $row -> total - $total_pay;
            $row -> remain = $row -> remain - $total_pay;
            $row -> save();
        }
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'view', 'id' => $ad -> getIdentity()), 'ynsocialads_ads', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Virtual Money!'))));
    }


    public function payCreditAction()
    {
        $credit_enable = Engine_Api::_() -> ynsocialads() -> checkYouNetPlugin('yncredit');
        if (!$credit_enable)
        {
            return $this -> _redirector();
        }
        $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'publish_ads'")->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            return $this -> _redirector();
        }
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $item_id);
        $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
        $item = array();
        $item = Engine_Api::_() -> getItem('ynsocialads_ad', $item_id);
        if($ad->status !='draft' && $ad->status !='unpaid')
        {
            return $this -> _redirector();
        }
        if($ad->user_id != $viewer->getIdentity())
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
                  ), 'ynsocialads_ads', true);
        $this -> view -> package = $package =$ad -> getPackage();
        $this -> view -> total_pay = $total_pay = ($ad -> benefit_total)*($package -> price / $package -> benefit_amount);        
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
    
        $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $item_id);
        $transactionTable = Engine_Api::_() -> getDbTable('transactions', 'ynsocialads');
        $select = $transactionTable -> select() -> where('ad_id = ?', $item_id) -> limit(1);
        $item = $transactionTable -> fetchRow($select);
        $package = $ad -> getPackage();
        if (!isset($item)) {
            $db = $transactionTable -> getAdapter();
            $db -> beginTransaction();

            try {

                $transAd = $transactionTable -> createRow();
                $transAd -> start_date = $ad -> start_date;
                $transAd -> status = 'completed';
                $transAd -> gateway_id = '-3';
                $transAd -> amount = $total_pay;
                $transAd -> currency = $package -> currency;
                $transAd -> ad_id = $ad -> getIdentity();
                $transAd -> user_id = $ad -> user_id;
                $transAd -> save();
                $ad -> status = 'pending';
                $ad -> save();
                // Commit
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
            }
        }
        // already paylater
        else {
            $item -> status = 'completed';
            $item -> gateway_id = '-3';
            $item -> save();
            $ad -> status = 'pending';
            $ad -> save();
        }
        Engine_Api::_()->yncredit()-> spendCredits($ad->getOwner(), (-1) * $credits, $ad->getTitle(), 'publish_ads', $ad);
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'view', 'id' => $ad -> getIdentity()), 'ynsocialads_ads', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }

    public function ajaxRenderAdsAction()
    {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        
        $params = array();
        $params['content_id'] = $content_id = $this->_getParam('content_id');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $tableHiddens = Engine_Api::_() -> getItemTable('ynsocialads_hidden');
        $tableAdBlock = Engine_Api::_() -> getItemTable('ynsocialads_adblock');
        $adBlock = $tableAdBlock->fetchRow($tableAdBlock->select()->where('content_id = ?',$content_id));
        $ads_limit = $adBlock -> ads_limit;
        
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
        foreach($items as $item)
        {
            if($item -> isAudience($viewer->getIdentity()))
            {
                $package = $item -> getPackage();
                $base_order = 0;
                switch ($package->benefit_type) 
                {
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

        echo $this -> view -> partial(Ynsocialads_Api_Core::partialViewFullPath('_blockRenderView.tpl'), array(
            'ads' => $arr_ads,
            'content_id' => $content_id,
            'viewer' => $viewer, 
        ));
    }
    
    protected function _redirector() {
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynsocialads_ads', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Error!'))));
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
  
}
