<?php

class Ynmobile_Helper_Classified extends Ynmobile_Helper_Base{
    
    
    
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('classified','ynmobile');
    }
    
    function field_id(){
        return $this->data['iListingId'] =  $this->entry->getIdentity();
    }
    
    public function field_photos(){
        
        $limit = defined('LIMIT_FIELD_PHOTOS') ?LIMIT_FIELD_PHOTOS: 10;
        
        $engine = Engine_Api::_();
        
        $table = $this->getWorkingTable('photos','classified');
        
        if(!$table){
            $this->data['iTotalPhotos'] = 0;
            $this->data['aPhotos'] = array();
            return ;
        }

        $select = $table->select()
            ->where("classified_id = ?", $this->entry->getIdentity())
           ;
            
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        
        $total = $paginator->getTotalItemCount();
        
        $items = array();
        
        $appMeta  = Ynmobile_AppMeta::getInstance();
        
        $fields = array('as_attachment');
        foreach($paginator as $item){
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        
        $this->data['iTotalPhoto'] =  $total;
        $this->data['aPhotos'] =  $items;
    }
    
    function field_custom(){
            
        $view =  Zend_Registry::get('Zend_View');
        
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynmobile/View/Helper', 'Ynmobile_View_Helper');
        
        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($this->entry);
        
        $view =  Zend_Registry::get('Zend_View');
        
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynmobile/View/Helper', 'Ynmobile_View_Helper');
        
        $this->data['sCustomContent'] = $view->fieldValueLoop($this->entry, $fieldStructure);
        
        foreach ($fieldStructure as $index => $map)
        {
            $field = $map -> getChild();
            $value = $field -> getValue($this->entry);
            if ($field -> type == 'location')
            {
                $this->data['sLocation'] = ($value->value) ? $value->value : '';
            }
            else if ($field -> type == 'currency')
            {
                $this->data['fPrice'] = ($value->value) ? $value->value : '0.00';
                if ($value->value != ''){
                    $this->data['sFullPrice'] = $view->locale()->toCurrency($value->value);
                }
                    
                else{
                    $this->data['sFullPrice'] = $view->locale()->toCurrency(0);
                } 
                    
            }
        }
    }

    public function field_auth(){
        $classified  = $this->entry;
        $auth = Engine_Api::_() -> authorization() -> context;
        $roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );
        foreach ($roles as $role)
        {
            if (1 === $auth -> isAllowed($classified, $role, 'view'))
            {
                $sViewPrivacy = $role;
            }
            if (1 === $auth -> isAllowed($classified, $role, 'comment'))
            {
                $sCommentPrivacy = $role;
            }
        }
        $this->data['auth']['view'] = $sViewPrivacy;
        $this->data['auth']['comment'] = $sCommentPrivacy;
        
    }

    function field_listing(){
         $this->field_id();
         $this->field_type();
         $this->field_desc();
         $this->field_title();
         $this->field_stats();
         $this->field_photos();
         $this->field_custom();
         $this->field_user();
         $this->field_imgNormal();
         $this->field_imgFull();
         $this->field_closed();
     }
    
    function field_closed(){
        $this->data['bIsClosed'] = $this->entry->closed?1:0;
    }
     
     function field_detail(){
         
         $this->field_auth();
         $this->field_category();
         $this->field_listing();
         $this->field_totalPhoto();
         $this->field_likes();
     }
}