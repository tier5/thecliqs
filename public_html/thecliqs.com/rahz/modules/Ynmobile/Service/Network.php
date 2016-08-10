<?php

class Ynmobile_Service_Network extends Ynmobile_Service_Base{

    protected $module = 'network';
    
    protected $mainItemType  =  'network';
    
    
    function fetch($aData){
        
        extract($aData);
        
        $sSearch = isset($sSearch)?$sSearch: '';
        $iPage = isset($iPage)?$iPage: 1;
        $iLimit  = isset($iLimit)?intval($iLimit):10;
            
        $form = array();
        $formData = array();
        
        $viewer = Engine_Api::_()->user()->getViewer();
    
        /*$select = Engine_Api::_()->getDbtable('membership', 'network')
            ->getMembershipsOfSelect($viewer)
            ->order('engine4_network_networks.title ASC');
          
        $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);*/
        
        $select_all_networks = Engine_Api::_()->getDbtable('networks', 'network')->select();
		if($sSearch)
		{
			$select_all_networks -> where("engine4_network_networks.title LIKE ?", $sSearch);
		}
        
        //$fields  = explode(',',$this->listingFields);
        
		return Ynmobile_AppMeta::_exports_by_page($select_all_networks, $iPage, $iLimit, $fields = array('listing'));
        //return Ynmobile_AppMeta::_export_all($select_all_networks, array('listing'));
    }


    

    function join_network($aData){
        
        extract($aData);
        
        $iNetworkId  = isset($iNetworkId)?intval($iNetworkId): 0;
        
        $viewer = Engine_Api::_()->user()->getViewer();
        
        $network = Engine_Api::_()->getItem('network', $iNetworkId);
        
        $view = Zend_Registry::get('Zend_View');
        
        if(!$network ||$network->assignment != 0){
            return array(
            'error_code'=>1,
            'error_message'=> 'Invalid network');
        }
        
        try{
             $network->membership()->addMember($viewer)
              ->setUserApproved($viewer)
              ->setResourceApproved($viewer);    
        }catch(Exception $ex){
            return array(
                'error_code'=>1,
                'error_message'=>$ex->getMessage(),
            );
        }
        
        try{
            if (!$network->hide){
          // Activity feed item
              Engine_Api::_()->getDbtable('actions', 'activity')
              ->addActivity($viewer, $network, 'network_join');
            }
        }catch(Exception $ex){
            
        }
        
        return array(
            'message'=>'Joined successful',
            );
    }
    
    function leave_network($aData){
        extract($aData);
        
        $iNetworkId  = isset($iNetworkId)?intval($iNetworkId): 0;
        
        $viewer = Engine_Api::_()->user()->getViewer();
        
        $network = Engine_Api::_()->getItem('network', $iNetworkId);
        
        $view = Zend_Registry::get('Zend_View');
        
        if(!$network){
            return array(
            'error_code'=>1,
            'error_message'=> $view->translate('Invalid network'));
        }
        
        if($network->assignment != 0){
            return array(
                'error_code'=>1,
                'error_message'=> $view->translate('Invalid network'),
            );
        }
        
        try{
             $network->membership()->removeMember($viewer);
        }catch(Exception $ex){
            return array(
                'error_code'=>1,
                'error_message'=>$ex->getMessage(),
            );
        }
         
        return array(
            'message'=>'Leave successful',
        );
    }
}

