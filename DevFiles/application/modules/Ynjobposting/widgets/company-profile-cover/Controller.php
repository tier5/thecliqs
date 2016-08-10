<?php

class Ynjobposting_Widget_CompanyProfileCoverController extends Engine_Content_Widget_Abstract 
{
     public function indexAction() 
     {
     	
          // Don't render this if not authorized
          $viewer = Engine_Api::_()->user()->getViewer();
          if (!Engine_Api::_()->core()->hasSubject()) 
          {
               return $this->setNoRender();
          }
          
          // Get subject and check auth
          $company = $subject = Engine_Api::_()->core()->getSubject('ynjobposting_company');
          $view = $this->view;
          $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
          $this->view->jobs = $jobs = $company -> getJobs(true);
          $this->view->number_of_jobs = count($jobs); 
          $this->view->company = $subject;
          $this->view->user = $user = Engine_Api::_()->user()->getUser($company->user_id);
          $this->view->canComment = $canComment = $company->authorization()->isAllowed($viewer, 'comment');
          $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($company);
          
		  $menu = new Ynjobposting_Plugin_Company_Menus();
		 
		  $aSponsorButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanySponsor();
          $this -> view -> aSponsorButton = $aSponsorButton;
		  
		  $aEditButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyEdit();
          $this -> view -> aEditButton = $aEditButton;
		  
		  $aEditSubmissionFormButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyEditSubmissionForm();
          $this -> view -> aEditSubmissionFormButton = $aEditSubmissionFormButton;
		  
		  $aManagePostedJobButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyManagePostedJob();
          $this -> view -> aManagePostedJobButton = $aManagePostedJobButton;
		  
          $aViewApplicationsButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyViewApplications();
          $this -> view -> aViewApplicationsButton = $aViewApplicationsButton;
          
		  $aCloseButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyClose();
          $this -> view -> aCloseButton = $aCloseButton;
		  
		  $aDeleteButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyDelete();
          $this -> view -> aDeleteButton = $aDeleteButton;
		  
		  $aShareButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyShare();
          $this -> view -> aShareButton = $aShareButton;
		  
		  $aReportButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyReport();
          $this -> view -> aReportButton = $aReportButton;
		  
		  $aFollowButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyFollow();
          $this -> view -> aFollowButton = $aFollowButton;
		  
		  $aContactButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyContact();
          $this -> view -> aContactButton = $aContactButton;
		  
     }
}