<?php
class Ynresume_Plugin_Menus {
	
	public function onMenuInitialize_YnresumeMainCreateResume() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity()) {
            return false;
        }
        
        $resume = Engine_Api::_()->ynresume()->getResumeByUserId($viewer->getIdentity());
        if (!$resume) {
            $can_create = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'create')->checkRequire();
            return ($can_create) ? true : false;
        }
		 $can_edit = $resume->isEditable();
         return ($can_edit) ? true : false;
	}
	
	public function onMenuInitialize_YnresumeMainWhoViewedMe()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$resume = Engine_Api::_() -> ynresume() -> getUserResume();
		if(!$resume)
		{
			return false;
		}
        $can_service = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'service')->checkRequire();
            return ($can_service) ? true : false;
	}
	
	public function onMenuInitialize_YnresumeMainRecommendations() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity()) {
            return false;
        }
		return true;
	}
	
	public function onMenuInitialize_YnresumeMainImportResume()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		return true;
	}
	
	public function onMenuInitialize_YnresumeMainManageFavourite()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		return true;
	}
	
	public function onMenuInitialize_YnresumeMainSavedResume()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		return true;
	}
	
    public function onMenuInitialize_UserProfileResume($row) {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = Engine_Api::_() -> core() -> getSubject();
        $view = Zend_Registry::get('Zend_View');
        $label = $view->translate('View Resume');
        $resume = Engine_Api::_()->ynresume()->getResumeByUserId($subject->getIdentity());
        if (!$resume || !$resume->isViewable())
            return false;
        return array(
            'label' => $label,
            'icon' => 'application/modules/Ynresume/externals/images/resume_icon.png',
            'route' => 'ynresume_specific',
            'params' => array(
                'resume_id' => $resume -> getIdentity(),
            )
        );
    }
    
    public function onMenuInitialize_YnresumeRecommendationReceived() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity()) {
            return false;
        }
        return true;
    }
    
    public function onMenuInitialize_YnresumeRecommendationGiven() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity()) {
            return false;
        }
        return true;
    }
    
    public function onMenuInitialize_YnresumeRecommendationAsk() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity()) {
            return false;
        }
        return true;
    }
    
    public function onMenuInitialize_YnresumeRecommendationGive() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity()) {
            return false;
        }
        $can_recommend = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'recommend')->checkRequire();        
        return ($can_recommend) ? true : false;
    }
}
