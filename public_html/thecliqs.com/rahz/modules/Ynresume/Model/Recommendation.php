<?php
class Ynresume_Model_Recommendation extends Core_Model_Item_Abstract {
    protected $_type = 'ynresume_recommendation';
    protected $_searchTriggers = false;
    
    public function getGiver() {
        $user = Engine_Api::_()->user()->getUser($this->giver_id);
        $resume = Engine_Api::_()->ynresume()->getResumeByUserId($this->giver_id);
        return ($resume) ? $resume : $user;
    }
    
    public function getReceiver() {
        return Engine_Api::_()->ynresume()->getResumeByUserId($this->receiver_id);
    }
    
    public function getGivenDate() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $givenDate = new Zend_Date(strtotime($this->given_date));
        $givenDate->setTimezone($timezone);
        return $givenDate;
    }
    
    public function isViewable() {
        return $this->authorization()->isAllowed(null, 'view'); 
    }
    
    public function getOwner($recurseType = null){
        return Engine_Api::_()->user()->getUser($this->giver_id);
    }
    
    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynresume_recommend',
            'action' => 'give',
            'reset' => true
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
    }
}
