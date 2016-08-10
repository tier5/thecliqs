<?php
class Ynresume_Widget_RecommendationRequestController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            $this->setNoRender();
            return;
        }
        
        $can_recommend = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'recommend')->checkRequire();
        if (!$can_recommend) {
            $this->setNoRender();
        }
        
        $params = $this->_getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        
        $this->view->pendingRecommendations = $pendingRecommendations = Engine_Api::_()->getDbTable('recommendations', 'ynresume')->getPendingRecommendaions($viewer->getIdentity());
	}
}