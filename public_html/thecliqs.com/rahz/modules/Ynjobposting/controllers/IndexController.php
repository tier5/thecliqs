<?php

class Ynjobposting_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		$this->_helper->content->setNoRender()->setEnabled();
	}
	public function getMyLocationAction()
	{
		$latitude = $this -> _getParam('latitude');
		$longitude = $this -> _getParam('longitude');
		$values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
		echo $values;
		die ;
	}
	
	public function displayMapViewAction()
	{
		$type = $this->_getParam('type', '');
		if ($type == 'company')
		{
			$this->displayCompanyMap();
		}
		else if ($type == 'job')
		{
			$this->displayJobMap();
		} 
	}

	public function displayJobMap()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
	    $jobIds = $this->_getParam('ids', '');
	    if ($jobIds != '')
	    {
	    	$jobIds = explode("_", $jobIds);
	    }
	    $jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
	    $select = $jobTbl -> select();
	    
		if (is_array($jobIds) && count($jobIds))
		{
			$select -> where ("job_id IN (?)", $jobIds);
		}
		else 
		{
			$select -> where ("0 = 1");
		}
		$jobs = $jobTbl->fetchAll($select);
			
		$datas = array();
		$contents = array();
		$http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
		$view = Zend_Registry::get("Zend_View");
		$jobArr = array();
		foreach($jobs as $job)
		{			
			if($job -> latitude)	
			{				
				$icon = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/maker.png';
				$key = "{$job -> latitude},{$job -> longitude}";
				$jobArr[$key][] = $job;
			}
		}
		
		foreach ($jobArr as $jobList)
		{
			if (count($jobList) == 1)
			{
				$job = $jobList[0];
				$datas[] = array(	
						'job_id' => $job -> getIdentity(),				
						'latitude' => $job -> latitude,
						'longitude' => $job -> longitude,
						'icon' => $icon
				);
				$contents[] = '
					<div class="ynjobposting-maps-main" style="overflow: hidden;">	
	      				<div class="ynjobposting-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">	      					
							<div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden">
								'. $view->itemPhoto($job, "thumb.icon") .'
							</div>								
							<a href="'.$job->getHref().'" class="ynjobposting-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
								'.$job->title.'
							</a>
			      		</div>
					</div>
				';
			}
			else if (count($jobList) > 1)
			{
				$job = $jobList[0];
				$datas[] = array(	
						'job_id' => $job -> getIdentity(),				
						'latitude' => $job -> latitude,
						'longitude' => $job -> longitude,
						'icon' => $icon
				);
				$str = '<div>' . count($jobList) . $view->translate(" jobs") . '</div>';
				foreach ($jobList as $job){
					$str .= '
						<div class="ynjobposting-maps-main" style="overflow: hidden;">	
		      				<div class="ynjobposting-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">	      					
								<div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden">
									'. $view->itemPhoto($job, "thumb.icon") .'
								</div>								
								<a href="'.$job->getHref().'" class="ynjobposting-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
									'.$job->title.'
								</a>
				      		</div>
						</div>
					';
				}
				$contents[] = $str;
			}
		}
		
		echo $this ->view -> partial('_map_view.tpl', 'ynjobposting',array('datas'=>Zend_Json::encode($datas), 'contents' => Zend_Json::encode($contents)));
		exit();
	}
	
	public function displayCompanyMap()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
	    $companyIds = $this->_getParam('ids', '');
	    if ($companyIds != '')
	    {
	    	$companyIds = explode("_", $companyIds);
	    }
	    $companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
	    $select = $companyTbl -> select();
	    
		if (is_array($companyIds) && count($companyIds))
		{
			$select -> where ("company_id IN (?)", $companyIds);
		}
		else 
		{
			$select -> where ("0 = 1");
		}
		$companies = $companyTbl->fetchAll($select);
			
		$datas = array();
		$contents = array();
		$http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
		/*
		$icon_clock = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/ynjobposting-maps-time.png';
		$icon_persion = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/ynjobposting-maps-person.png';
		$icon_star = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/ynjobposting-maps-close-black.png';
		$icon_home = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/ynjobposting-maps-location.png';
		$icon_new = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/icon-New.png';
		$icon_guest = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/ynjobposting-maps-person.png';
		*/
		$view = Zend_Registry::get("Zend_View");
		$companyArr = array();
		foreach($companies as $company)
		{			
			if($company -> latitude)	
			{				
				$icon = $http.$_SERVER['SERVER_NAME'].$this->view->layout()->staticBaseUrl.'application/modules/Ynjobposting/externals/images/maker.png';
				$key = "{$company -> latitude},{$company -> longitude}";
				$companyArr[$key][] = $company;
			}
		}
		
		foreach ($companyArr as $companyList)
		{
			if (count($companyList) == 1)
			{
				$company = $companyList[0];
				$datas[] = array(	
						'company_id' => $company -> getIdentity(),				
						'latitude' => $company -> latitude,
						'longitude' => $company -> longitude,
						'icon' => $icon
				);
				$jobCount = $company->countJobWithStatus('published');
				$jobCountStr = $view->translate(array("%s job", "%s jobs", $jobCount), $jobCount);
				$contents[] = '
					<div class="ynjobposting-maps-main" style="overflow: hidden;">	
	      				<div class="ynjobposting-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">	      					
							<div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden;">
								'. $view->itemPhoto($company, "thumb.icon") .'
							</div>								
							<a href="'.$company->getHref().'" class="ynjobposting-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
								'.$company->name.'
							</a>
							'.$jobCountStr.'								
			      		</div>
					</div>
				';
			}
			else if (count($companyList) > 1)
			{
				$company = $companyList[0];
				$datas[] = array(	
						'company_id' => $company -> getIdentity(),				
						'latitude' => $company -> latitude,
						'longitude' => $company -> longitude,
						'icon' => $icon
				);
				$str = '<div>' . count($companyList) . $view->translate(" companies") . '</div>';
				foreach ($companyList as $company){
					$jobCount = $company->countJobWithStatus('published');
					$jobCountStr = $view->translate(array("%s job", "%s jobs", $jobCount), $jobCount);
					$str .= '
						<div class="ynjobposting-maps-main" style="overflow: hidden;">	
		      				<div class="ynjobposting-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">	      					
								<div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden;">
								'. $view->itemPhoto($company, "thumb.icon") .'
							</div>									
								<a href="'.$company->getHref().'" class="ynjobposting-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
									'.$company->name.'
								</a>
								'.$jobCountStr.'								
				      		</div>
						</div>
					';
				}
				$contents[] = $str;
			}
		}
		
		echo $this ->view -> partial('_map_view.tpl', 'ynjobposting',array('datas'=>Zend_Json::encode($datas), 'contents' => Zend_Json::encode($contents)));
		exit();
	}
	
	public function deleteNoteAction()
	{
		$this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$noteId = $this->_getParam('note_id', null);
		if (is_null($noteId))
		{
			return;
		}
		$note = Engine_Api::_()->getItem('ynjobposting_applynote', $noteId);
		if (is_null($note)){
			return;
		}
		$note -> delete();
		echo Zend_Json::encode(array(
			'result' => Zend_Registry::get("Zend_Translate")-> _("Deleted note successfully")
		));
		exit;
	}
	
}
