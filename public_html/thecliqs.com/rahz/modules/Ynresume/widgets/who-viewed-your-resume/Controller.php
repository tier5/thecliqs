<?php
class Ynresume_Widget_WhoViewedYourResumeController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
	    $viewer = Engine_Api::_()->user()->getViewer();
     	if(!$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}
        
        $can_service = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'service')->checkRequire();
        if (!$can_service) $this->setNoRender();
            
		$resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
		$this -> view -> resume = $resume = $resumeTable -> getResume($viewer -> getIdentity());
		if(empty($resume))
		{
			return $this -> setNoRender();
		}
		$viewTable = Engine_Api::_() -> getDbTable('views', 'ynresume');
		$this -> view -> viewers = $viewers = $viewTable -> getViewersPaginator($resume, true);
		$this -> view -> total = $total = $viewTable -> getCountViewer($resume);
	}
}
