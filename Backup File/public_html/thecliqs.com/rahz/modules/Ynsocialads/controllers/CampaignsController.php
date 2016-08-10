<?php

class Ynsocialads_CampaignsController extends Core_Controller_Action_Standard {
    protected $_periods = array(
        Zend_Date::DAY, //dd
        Zend_Date::WEEK, //ww
        Zend_Date::MONTH, //MM
        Zend_Date::YEAR, //y
    );
    
    protected $_allPeriods = array(
        Zend_Date::SECOND,
        Zend_Date::MINUTE,
        Zend_Date::HOUR,
        Zend_Date::DAY,
        Zend_Date::WEEK,
        Zend_Date::MONTH,
        Zend_Date::YEAR,
    );
    
    protected $_periodMap = array(
        Zend_Date::DAY => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
        ),
        Zend_Date::WEEK => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
            Zend_Date::WEEKDAY_8601 => 1,
        ),
        Zend_Date::MONTH => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
            Zend_Date::DAY => 1,
        ),
        Zend_Date::YEAR => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
            Zend_Date::DAY => 1,
            Zend_Date::MONTH => 1,
        ),
    );
    
    public function indexAction() {
        if( !$this->_helper->requireUser->isValid()) return;
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $table = Engine_Api::_()->getItemTable('ynsocialads_campaign');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
    
        $this->view->form = $form = new Ynsocialads_Form_Campaigns_Search();
        
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $this->view->formValues = $values;
        if ($values['status'] == 'All') {
            $statusArr = array('active', 'deleted');
        }
        else {
            $statusArr = array($values['status']);
        }
        if ($values['title'] != null) {
            $select = $select->where('title LIKE ?', '%'.$values['title'].'%');
        }
        $select = $select->where('status IN (?)', $statusArr);              
        $campaigns = $table->fetchAll($select);
        
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($campaigns);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
        
        $this->view->formStatistic = $formStatistic = new Ynsocialads_Form_Campaigns_Statistics();
        
        $this->_helper->content
        //->setNoRender()
        ->setEnabled();
    }
    
    public function editAction() {
        if(!$this->_helper->requireAuth()->setAuthParams('ynsocialads_campaign', null, 'edit')->isValid()) return;
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $id);
        
        if (!$campaign->isEditable()) return;
        
        $this->view->campaign_id=$id;
        $this->view->campaign_name = $campaign->title;
        // Check post
        if( $this->getRequest()->isPost()) {
            $newTitle = $this->getRequest()->getPost('newTitle');
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $campaign->title = $newTitle;
                $campaign->save();
                
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }
    }
    
    public function deleteAction() {
        if(!$this->_helper->requireAuth()->setAuthParams('ynsocialads_campaign', null, 'delete')->isValid()) return;
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $id);
        
        if (!$campaign->isDeletable()) return;
        
        $this->view->campaign_id=$id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $campaign->deleteAllAds();
                $campaign->status = "deleted";
                $campaign->save();
                
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }
    }

    public function chartAction() {
        // Disable layout and viewrenderer
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    
        $viewer = Engine_Api::_()->user()->getViewer();
        
        // Get params
        $start  = $this->_getParam('start');
        $offset = $this->_getParam('offset', 0);
        $type   = $this->_getParam('type', 'all');
        $mode   = $this->_getParam('mode');
        $chunk  = $this->_getParam('chunk');
        $period = $this->_getParam('period');
        $periodCount = $this->_getParam('periodCount', 1);
        
        $campaign_ids = $this->_getParam('campaign_ids');
        
        // Validate chunk/period
        if( !$chunk || !in_array($chunk, $this->_periods) ) {
          $chunk = Zend_Date::DAY;
        }
        if( !$period || !in_array($period, $this->_periods) ) {
          $period = Zend_Date::MONTH;
        }
        if( array_search($chunk, $this->_periods) >= array_search($period, $this->_periods) ) {
          die('whoops');
          return;
        }
    
        // Validate start
        if( $start && !is_numeric($start) ) {
          $start = strtotime($start);
        }
        if( !$start ) {
          $start = time();
        }
    
        // Fixes issues with month view
        Zend_Date::setOptions(array(
          'extend_month' => true,
        ));
    
        // Get timezone
        $timezone = Engine_Api::_()->getApi('settings', 'core')
            ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
          $timezone = $viewer->timezone;
        }
    
        // Make start fit to period?
        $startObject = new Zend_Date($start);
        $startObject->setTimezone($timezone);
        
        $partMaps = $this->_periodMap[$period];
        foreach( $partMaps as $partType => $partValue ) {
          $startObject->set($partValue, $partType);
        }
    
        // Do offset
        if( $offset != 0 ) {
          $startObject->add($offset, $period);
        }
        
        $arr_campaigns = array();
        if($campaign_ids)
        {
            $arr_campaigns = explode(',', $campaign_ids);
        }
        
        // Get end time
        $endObject = new Zend_Date($startObject->getTimestamp());
        $endObject->setTimezone($timezone);
        $endObject->add($periodCount, $period);
        $endObject->sub(1, Zend_Date::SECOND); // Subtract one second
        
        $adTbl = Engine_Api::_()->getDbtable('ads', 'ynsocialads');
        if (count($arr_campaigns)) {
            $adList = $adTbl -> fetchAll($adTbl->select() -> from($adTbl -> info("name"), 'ad_id')->where('campaign_id IN (?)', $arr_campaigns));
            $adList = $adList -> toArray();
        }
        else {
            $adList = array();
        }
        // Get data
        $staTable = Engine_Api::_()->getDbtable('statistics', 'ynsocialads');
        $staName = $staTable ->info('name');
        
        if ($type != "all")
        {
            $select = $staTable -> select();
            // Get data
            if (count($adList)) 
            {
                $select
                  ->where('ad_id IN (?)', $adList)
                  ->where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
                  ->where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
                  ->order('timestamp ASC');
            
                if($type != "all")
                {
                    $select -> where('type = ?', $type);
                }
                $rawData = $staTable->fetchAll($select);
            }
            else $rawData = array();

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
            $selectClick = $staTable -> select();
            // Get data
            if (count($adList)) 
            {
                $selectClick
                  ->where('ad_id IN (?)', $adList)
                  ->where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
                  ->where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
                  ->order('timestamp ASC');
                $selectClick -> where("type = 'click'");
                $clickData = $staTable->fetchAll($selectClick);
            }
            else $clickData = array();
            
            $selectImpression = $staTable -> select();
            // Get data
            if (count($adList)) 
            {
                $selectImpression
                  ->where('ad_id IN (?)', $adList)
                  ->where('timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
                  ->where('timestamp < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
                  ->order('timestamp ASC');
                $selectImpression -> where("type = 'impression'");
                $impressionData = $staTable->fetchAll($selectImpression);
            }
            else $impressionData = array();
            
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
}
