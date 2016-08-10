<?php
class Ynjobposting_Widget_JobProfileOptionController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject('ynjobposting_job'))
		{
			return $this->setNoRender();
		}
		$this->view->job = $job = Engine_Api::_()->core()->getSubject('ynjobposting_job');
        if ($job->isDeleted()) {
            return $this->setNoRender();
        }
	    $this->view->canApply = $canApply = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynjobposting_job', null, 'apply')->checkRequire(); 
        $this->view->canShare = $canShare = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynjobposting_job', null, 'share')->checkRequire();
        $this->view->canPrint = $canPrint = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynjobposting_job', null, 'print')->checkRequire();
        $this->view->canReport = $canReport = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynjobposting_job', null, 'report')->checkRequire();
    }
}
