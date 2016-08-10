<?php

class Ynsocialads_ReportController extends Core_Controller_Action_Standard {
    public function indexAction() {
        if( !$this->_helper->requireUser->isValid()) return;
        
        $viewer = Engine_Api::_()->user()->getViewer();
        
        $sysTimezone = date_default_timezone_get();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
        
        if (null == ($campaign_id = $this->_getParam('campaign_id')))
            $campaign_id = 0;
        
        if (null == ($ad_id = $this->_getParam('ad_id')))
            $ad_id = 0;
        
        if (null == ($export = $this->_getParam('export')))
            $export = 0;
        if (null == ($export_type = $this->_getParam('export_type')))
            $export_type = 'xls';
        
        $this->view->form = $form = new Ynsocialads_Form_Report_Filter();
        
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $this->view->formValues = $values;
        
        if ($this->_hasParam('end_date')) {
            $end = $this->_getParam('end_date');
            if( $end && !is_numeric($end) ) {
                $end_date = strtotime($end);
            }
            else {
                if ($end) $end_date = $end;
            }
            $end_date = new Zend_Date($end_date);    
        }
        else {
            $end_date = new Zend_Date();
        }
        
        if ($this->_hasParam('start_date')) {
            $start = $this->_getParam('start_date');
            if( $start && !is_numeric($start) ) {
                $start_date = strtotime($start);
            }
            else {
                if ($start) $start_date = $start;
            }
            $start_date = new Zend_Date($start_date);
        }
        else {
            $start_date = new Zend_Date($end_date->getTimestamp());
            $start_date->sub(1, 'dd');       
        }    
        
        $form->start_date->setValue($start_date->setLocale()->get('MM/dd/yyyy'));
        $form->end_date->setValue($end_date->setLocale()->get('MM/dd/yyyy'));

        $end_date->setTimezone($sysTimezone);
        $start_date->setTimezone($sysTimezone);    

        $campaignTbl = Engine_Api::_()->getItemTable('ynsocialads_campaign');
        $select = $campaignTbl->select()->where('user_id = ?', $viewer->getIdentity());
        $campaigns = $campaignTbl->fetchAll($select);
        foreach ($campaigns as $campaign) {
            $form->campaign_id->addMultiOption($campaign['campaign_id'], $campaign['title']);
        }
        $form->campaign_id->setValue($campaign_id);
        
        $adTbl = Engine_Api::_()->getDbtable('ads', 'ynsocialads');
        $adTotal = array();
          
        if ($campaign_id == 0) {
            $form->ad_id->setAttrib('disabled', 'disabled');
            foreach ($campaigns as $campaign) {
                $adList = $adTbl->fetchAll($adTbl->select()->where('campaign_id = ?', $campaign['campaign_id']));
                foreach($adList as $ad) {
                    $adTotal[] = $ad->ad_id;
                }
            }
        }
        else {
            $ads = $adTbl->fetchAll($adTbl->select('ad_id')->where('campaign_id = ?', $campaign_id));
            foreach ($ads as $ad) {
                $form->ad_id->addMultiOption($ad['ad_id'], $ad['name']);
                if ($ad_id == 0) {
                    array_push($adTotal, $ad['ad_id']);
                }
            }
            if ($ad_id != 0)
                $adTotal = array($ad_id);
            $form->ad_id->setValue($ad_id);
        } 

        $staTable = Engine_Api::_()->getDbtable('tracks', 'ynsocialads');
        $staName = $staTable ->info('name');
        $select = $staTable->select();
        $select
            ->where('ad_id IN (?)', $adTotal)
            ->where('date >= ?', $start_date->get('yyyy-MM-dd') )
            ->where('date <= ?', $end_date->get('yyyy-MM-dd'))
            ->order('ad_id ASC');
            if (count($adTotal)) {
                $tracks = $staTable->fetchAll($select);
            }
            else {
                $tracks = array();
            }
         
        if ($export == 0) { 
            $page = $this->_getParam('page',1);
            $this->view->paginator = $paginator = Zend_Paginator::factory($tracks);
            $this->view->paginator->setItemCountPerPage(10);
            $this->view->paginator->setCurrentPageNumber($page);
            
            $this->_helper->content
            ->setEnabled();
        }
        else 
        {
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
            $finalData[] = array(
                utf8_decode( $this->view->translate('Date')),
                utf8_decode( $this->view->translate('Ad')),
                utf8_decode( $this->view->translate('Campaign')),
                utf8_decode( $this->view->translate('Start Date')),
                utf8_decode( $this->view->translate('End Date')),
                utf8_decode( $this->view->translate('Running Date')),
                utf8_decode( $this->view->translate('Reaches')), 
                utf8_decode( $this->view->translate('Impressions')),
                utf8_decode( $this->view->translate('Clicks')),
                utf8_decode( $this->view->translate('Unique Clicks')),
            );
            foreach ( $tracks as $item )
            {
                $ad = Engine_Api::_()->getItem('ynsocialads_ad', $item['ad_id']);
                $date =  new Zend_Date(strtotime($item['date']));
                $finalData[] = array(
                    $date->setLocale()->get(Zend_Date::DATE_LONG),
                    $ad->getTitle(),
                    $ad->getCampaign()->getTitle(), 
                    ($ad->start_date) ? utf8_decode( $ad->getStartDate()->setLocale()->get(Zend_Date::DATE_LONG)) : '',
                    ($ad->end_date) ? utf8_decode( $ad->getEndDate()->setLocale()->get(Zend_Date::DATE_LONG)) : '',
                    $ad->getRunningDate()->setLocale()->get(Zend_Date::DATE_LONG),
                    $item['reaches'],
                    $item['impressions'],
                    $item['clicks'],
                    $item['unique_clicks'],
                );
            }
			if ($export_type == 'xls') 
			{
                $xls = new Ynsocialads_Api_ExcelExport('UTF-8', false, $start_date->setLocale()->get('MM_dd_YYYY').'_'.$end_date->setLocale()->get('MM_dd_YYYY'));
				$xls->addArray($finalData);
				$xls->generateXML('campaign'.$campaign_id.'_ad'.$ad_id.'_'.$start_date->setLocale()->get('MM_dd_YYYY').'_'.$end_date->setLocale()->get('MM_dd_YYYY'));
				exit();
			}
			else 
			{
				foreach ( $finalData as $finalRow )
	            {
	                fputcsv( $handle, $finalRow);
	            }
	            fclose($handle);
	            $this->_helper->layout->disableLayout();
	            $this->_helper->viewRenderer->setNoRender();
	            $csvname = 'campaign'.$campaign_id.'_ad'.$ad_id.'_'.$start_date->setLocale()->get('MM_dd_YYYY').'_'.$end_date->setLocale()->get('MM_dd_YYYY').'.csv';
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
	            exit();
			}
        }        
    }
}
