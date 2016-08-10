<?php

class Ynmobile_Service_Search extends Ynmobile_Service_Base {
    
    protected $module = 'none';
    
    function available_types(){
        
        $availableTypes  = Engine_Api::_()->getApi('search','core')->getAvailableTypes();
        
        $view  = Zend_Registry::get('Zend_View');
        
        // require to reorder by alphabet ascendent
        $maps = array();
        $options  = array();
        
        
        foreach($availableTypes as $type){
            $maps[$type] = $view->translate(strtoupper('ITEM_TYPE_' . $type)); 
        }
        asort($maps, SORT_LOCALE_STRING);
        
        foreach($maps as $id=>$title){
            $options[] =array(
                'id'=>$id,
                'title'=>$title,
            );
        } 
        
        // sort($options);
        
        return $options;
    }
    
    /**
     *
     */
    function fetch($aData) {
        extract($aData);
        
        $fields = array('id','title','desc','type');
        
        $sSearch = isset($sSearch) ? $sSearch : '';
        $iPage = isset($iPage) ? $iPage : 1;
        $iLimit =  isset($iLimit)?$iLimit:10;
        $sType =  isset($sFilterBy)?$sFilterBy: 'user';

        if (empty($sSearch)) {
            return array();
        }

        $searchApi = Engine_Api::_() -> getApi('search', 'core');

        // Get available types
        $availableTypes = $searchApi -> getAvailableTypes();


        $paginator = $searchApi -> getPaginator($sSearch, $sType);
        
        $paginator -> setCurrentPageNumber($iPage);
        $paginator -> setItemCountPerPage($iLimit);
        
        if ($iPage > $paginator -> count()) {
            return array();
        }

        $return = array();

        $view = Zend_Registry::get('Zend_View');
        
        // $appMeta = Ynmobile_AppMeta::getInstance();
        
        // $fields = array('simple_array');

        foreach ($paginator as $entry) {
            
            $item = Engine_Api::_() -> getItem($entry -> type, $entry -> id);

            if (!$item){
                continue;
            }   

            try {
                $img = $view->itemPhoto($item, 'thumb.icon');
                $sImage = '';
                if(preg_match("#src=\"([^\"]+)\"#", $img, $match)){
                    $sImage  = Engine_Api::_()->ynmobile()->finalizeUrl($match[1]);
                }
                
                // $result =  $appMeta->getModelHelper($item)->toArray($fields);;
                // $result['imgIcon'] =  $sImage;
                
                $return[] = array(
                    'id'=> $item->getIdentity(),
                    'type'=>$item->getType(),
                    'title'=>$item->getTitle(),
                    'description'=>$item->getDescription(),
                    'img'=>$sImage,
                );

            } catch(Exception $ex) {
                return array('error_code'=>1,'error_message'=>$ex->getMessage());
            }
        }

        return $return;
    }

}
