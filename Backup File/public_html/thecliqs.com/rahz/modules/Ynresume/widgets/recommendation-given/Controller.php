<?php
class Ynresume_Widget_RecommendationGivenController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            $this->setNoRender();
            return;
        }
        $params = $this->_getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        
        $this->view->recommendations = $recommendations = Engine_Api::_()->getDbTable('recommendations', 'ynresume')->getGivenRecommendations($viewer->getIdentity());
        $this->view->can_recommend = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'recommend')->checkRequire();	   
    }
}