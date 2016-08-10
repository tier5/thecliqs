<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminManageController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_manage');

    $page = $this->_getParam('page',1);
    $this->view->form = $form = new Ynauction_Form_Admin_Search();   
    $values = array();  
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      if( empty($values['order']) ) {
        $values['order'] = 'product_id';
        }
        if( empty($values['direction']) ) {
        $values['direction'] = 'DESC';
        }
        $this->view->filterValues = $values;
        $this->view->order = $values['order'];
        $this->view->direction = $values['direction'];
      $table = Engine_Api::_()->getDbTable('products', 'ynauction');
      $ynauctions = $table->fetchAll(Engine_Api::_()->ynauction()->getProductsSelect($values))->toArray();
      $this->view->count = count($ynauctions);
    } 
    $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 10);
    $values['limit'] = $limit;
    $this->view->paginator = Engine_Api::_()->ynauction()->getProductsPaginator($values); 
    $this->view->paginator->setCurrentPageNumber($page);
    $this->view->formValues = $values; 
    $viewer = $this->_helper->api()->user()->getViewer();
    if(Engine_Api::_()->ynauction()->checkBecome($viewer->getIdentity()))
    {
        $this->view->canCreate = true;
    }
  }
  public function deleteselectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));
     $viewer = $this->_helper->api()->user()->getViewer();
    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $ynauction = Engine_Api::_()->getItem('ynauction_product', $id);
        if( $ynauction )
        { 
            $ynauction->is_delete = 1;
             $ynauction->save();

			 //delete actions and attachments
            $streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
            $streamTbl->delete('(`object_id` = '.$ynauction -> getIdentity().' AND `object_type` = "ynauction_product")');
			$activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
            $activityTbl->delete('(`object_id` = '.$ynauction -> getIdentity().' AND `object_type` = "ynauction_product")');
            $attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
            $attachmentTbl->delete('(`id` = '.$ynauction -> getIdentity().' AND `type` = "ynauction_product")');
			
             Engine_Api::_()->getApi('search', 'core')->unindex($ynauction);   
               //notification 
                $notify =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 0);
                if($notify == 1)
                {
                     //send notify 
                     //Send sell
                     $productOwner = $ynauction->getOwner();
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     if($ynauction->user_id != $viewer->getIdentity())
                     {
                        $notifyApi->addNotification($productOwner, $viewer, $ynauction, 'ynauction_deleted_bidded', array(
          'label' => $auction->title
        ));
                     }
                     //send users
                   $userBids =  Engine_Api::_()->ynauction()->getUserBid($ynauction->product_id,$ynauction->user_id);
                   foreach($userBids as $bid)
                   {
                       if($bid->ynauction_user_id != $viewer->getIdentity() && $bid->ynauction_user_id != $productOwner->getIdentity())
                        {
                          $userBid = Engine_Api::_()->getItem('user', $bid->ynauction_user_id);
                          $notifyApi->addNotification($userBid, $viewer, $ynauction, 'ynauction_deleted_bidded', array(
                            'label' => $auction->title
                          ));
                        }
                   }   
                }
        }
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }
  public function featuredAction()
  {
      $pro_id = $this->_getParam('product_id'); 
      $pro_good = $this->_getParam('good');
      $ynauction = Engine_Api::_()->getItem('ynauction_product', $pro_id); 
      if($ynauction)
      {
          $ynauction->featured = $pro_good;
          $ynauction->save(); 
      } 
  }
  public function winAction()
  {
      $pro_id = $this->_getParam('product_id'); 
      $pro_win = $this->_getParam('win');
      $ynauction = Engine_Api::_()->getItem('ynauction_product', $pro_id); 
      if($ynauction)
      {
            $ynauction->status = $pro_win;
            $ynauction->save(); 
      } 
  }
	public function stopAction()
  {
      $pro_id = $this->_getParam('product_id'); 
      $pro_stop = $this->_getParam('stop');
      $viewer = $this->_helper->api()->user()->getViewer();
      $ynauction = Engine_Api::_()->getItem('ynauction_product', $pro_id); 
      //if( !$this->_helper->requireAuth()->setAuthParams($ynauction, $viewer, 'edit')->isValid() ) 
      //  return;
      if($ynauction)
      {
          $ynauction->stop = $pro_stop;
          $ynauction->save(); 
          if($pro_stop == 1)
          {
              //notification 
                $notify =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 0);
                if($notify == 1)
                {
                     //send notify 
                     //Send sell
                     $productOwner = $ynauction->getOwner();
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     if($ynauction->user_id != $viewer->getIdentity())
                     {
                        $notifyApi->addNotification($productOwner, $viewer, $ynauction, 'ynauction_stopped_bidded', array(
          'label' => $auction->title
        ));
                     }
                     //send users
                   $userBids =  Engine_Api::_()->ynauction()->getUserBid($ynauction->product_id,$ynauction->user_id);
                   foreach($userBids as $bid)
                   {
                       if($bid->ynauction_user_id != $viewer->getIdentity() && $bid->ynauction_user_id != $productOwner->getIdentity())
                        {
                          $userBid = Engine_Api::_()->getItem('user', $bid->ynauction_user_id);
                          $notifyApi->addNotification($userBid, $viewer, $ynauction, 'ynauction_stopped_bidded', array(
                            'label' => $auction->title
                          ));
                        }
                   }   
                }
          }
      } 
  }
  public function disAction()
  {
      $pro_id = $this->_getParam('product_id'); 
      $pro_stop = $this->_getParam('dis');
      $ynauction = Engine_Api::_()->getItem('ynauction_product', $pro_id); 
      if($ynauction)
      {
          $ynauction->display_home = $pro_stop;
          $ynauction->save(); 
      } 
  }
public function createAction()
  {
                          // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    //if( !$this->_helper->requireAuth()->setAuthParams('ynauction_product', null, 'create')->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_manage');
    $this->view->form = $form = new Ynauction_Form_Create();
    $supportedCurrencyIndex = array();
    $fullySupportedCurrencies = array();
    $supportedCurrencies = array();
    $gateways = array();
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    foreach( $gatewaysTable->fetchAll() as $gateway ) {
      $gateways[$gateway->gateway_id] = $gateway->title;
      $gatewayObject = $gateway->getGateway();
      $currencies = $gatewayObject->getSupportedCurrencies();
      if( empty($currencies) ) {
        continue;
      }
      $supportedCurrencyIndex[$gateway->title] = $currencies;
      if( empty($fullySupportedCurrencies) ) {
        $fullySupportedCurrencies = $currencies;
      } else {
        $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
      }
      $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
    }
    $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
    
    $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
    $fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
    $supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
    $form->currency_symbol->setMultiOptions(array(
      'Fully Supported' => $fullySupportedCurrencies,
      'Partially Supported' => $supportedCurrencies,
    ));     
   // If not post or form not valid, return
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    $post = $this->getRequest()->getPost();
    $cat1_id = $post['cat1_id'];
    $post['cat1_id'] = "";
    if(!$form->isValid($post))
        return;
  
    $viewer = Engine_Api::_()->user()->getViewer();

    // Process
    $table = Engine_Api::_()->getItemTable('ynauction_product');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create Auction
      $values = array_merge($form->getValues(), array(
        'user_id' => $viewer->getIdentity(),
      ));
      $product = $table->createRow();
      $product->setFromArray($values);
      if($cat1_id > 0)
          $product->cat_id = $cat1_id;
       $flag = 0; 
      //check price
      if(!is_numeric($values['price']) || $values['price'] < 0)
      {
                $form->getElement('price')->addError('The price number is invalid! (Ex: 2000.25)');
                $flag = 1;
      }
       if(!is_numeric($values['starting_bidprice']) || $values['starting_bidprice'] < 0)
      {
                $form->getElement('starting_bidprice')->addError('The price number is invalid! (Ex: 2000.25)');
                $flag = 1;  
      }
      if(!is_numeric($values['minimum_increment']) || $values['minimum_increment'] < 0)
      {
                $form->getElement('minimum_increment')->addError('The minimum increment number is invalid! (Ex: 2000.25)');
                $flag = 1;  
      }
      if(!is_numeric($values['maximum_increment']) || $values['maximum_increment'] < 0)
      {
                $form->getElement('maximum_increment')->addError('The maximum increment number is invalid! (Ex: 2000.25)');
                $flag = 1;  
      }
      //check start time and end time
      $oldTz = date_default_timezone_get();   
      date_default_timezone_set($viewer->timezone);
      $start_time = strtotime($values['start_time']);
      $end_time =  strtotime($values['end_time']);
      $now = date('Y-m-d H:i:s');
      date_default_timezone_set($oldTz); 
      
      $product->start_time = date('Y-m-d H:i:s', $start_time);
      $product->end_time = date('Y-m-d H:i:s', $end_time);
       if($values['start_time'] < $now)
      {
          $form->getElement('start_time')->addError('Start Time should be equal or greater than Current Time!');
          $flag = 1;   
      } 
      if($values['start_time'] >= $values['end_time'])
      {
          $form->getElement('end_time')->addError('End Time should be greater than Start Time!');
          $flag = 1;   
      }
      if($values['minimum_increment'] > $values['maximum_increment'])
      {
          $form->getElement('maximum_increment')->addError('Maximum increment should be greater than minimum increment!');
          $flag = 1;   
      }  
      //check image
      if( !empty($values['thumbnail']) ) {
          $file = $form->thumbnail->getFileName();
          $info = getimagesize($file);
          if($info[2] > 3 || $info[2] == "")
          {
            $form->getElement('thumbnail')->addError('The uploaded file is not supported or is corrupt.');  
            $flag = 1;
          }                
      }
      if($flag == 1)
      {
          return false;
      } 
      $product->price = round($product->price,2); 
      $product->starting_bidprice = round($product->starting_bidprice,2); 
      $product->bid_price = round($product->starting_bidprice,2); 
      $product->bid_time = round($product->bid_time);
       //Set Fee
      $publish_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $viewer, 'publish_fee');
      $featured_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $viewer, 'featured_fee');
      $total_fee = $publish_fee;
      if($values['featured'] == 1)
        $total_fee = $total_fee + $featured_fee;
      if(!$total_fee)
        $total_fee = 0;
      $product->total_fee = $total_fee; 
      $product->creation_ip = ip2long($_SERVER['REMOTE_ADDR']); 
      $product->save();
      // Set photo
      if( !empty($values['thumbnail']) ) {
        $product->setPhoto($form->thumbnail);
      }
      // Add fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($product);
      $customfieldform->saveValues();
      // Set privacy
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network','registered', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = array("everyone");
      }
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = array("everyone");
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($product, $role, 'view',    ($i <= $viewMax));
        $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
      }

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    // Redirect
   
    return $this->_helper->redirector->gotoRoute(array('module' => 'ynauction', 'controller' => 'manage','action' => 'index'), 'admin_default', true);
    
  }
public function editAction()
  {
         if( !$this->_helper->requireUser()->isValid() ) return;
     // Get navigation
       $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_manage');
    $viewer = $this->_helper->api()->user()->getViewer();
    $product = Engine_Api::_()->getItem('ynauction_product', $this->_getParam('auction'));
    $category = Engine_Api::_()->getItem('ynauction_category', $product->cat_id);
    if( !Engine_Api::_()->core()->hasSubject('product') ) {
          Engine_Api::_()->core()->setSubject($product);
    }
    // Check auth
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
   // if( !$this->_helper->requireAuth()->setAuthParams($product, $viewer, 'edit')->isValid() ) 
    //    return;
    // Prepare form
    $this->view->form = $form = new Ynauction_Form_Edit(array(
      'item' => $product
    ));
    $form->removeElement('check');
    $form->removeElement('thumbnail');
    $form->removeElement('featured'); 
    if($product->photo_id > 0)
        if(!$product->getPhoto($product->photo_id)){
          $product->addPhoto($product->photo_id);
        }
        
    $category = Engine_Api::_()->getItem('ynauction_category', $product->cat_id);
    // prepare subcategories
    if($category)
    {
        if($category->parent > 0)
        {
            $subcategories = Engine_Api::_()->ynauction()->getCategories($category->parent);
            $subSelect = $category->category_id;
        }
        else {
            $subcategories = Engine_Api::_()->ynauction()->getCategories($category->category_id);
            $subSelect = $category->category_id;
        }
    }
    if (count($subcategories)!=0){
      $form->cat1_id->addMultiOption(0,""); 
      foreach ($subcategories as $subcategory){
        $form->cat1_id->addMultiOption($subcategory->category_id,$subcategory->title); 
      }
      $form->cat1_id->setValue($subSelect);
    }
    $this->view->album = $album = $product->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(100);
    
    foreach( $paginator as $photo )
    {
      $subform = new Ynauction_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->removeElement('title');
      if($photo->file_id == $product->photo_id)
        $subform->removeElement('delete');
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }
    $this->view->product = $product;
    // Populate form
    $supportedCurrencyIndex = array();
    $fullySupportedCurrencies = array();
    $supportedCurrencies = array();
    $gateways = array();
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    foreach( $gatewaysTable->fetchAll() as $gateway ) {
      $gateways[$gateway->gateway_id] = $gateway->title;
      $gatewayObject = $gateway->getGateway();
      $currencies = $gatewayObject->getSupportedCurrencies();
      if( empty($currencies) ) {
        continue;
      }
      $supportedCurrencyIndex[$gateway->title] = $currencies;
      if( empty($fullySupportedCurrencies) ) {
        $fullySupportedCurrencies = $currencies;
      } else {
        $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
      }
      $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
    }
    $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
    
    $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
    $fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
    $supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
    $form->currency_symbol->setMultiOptions(array(
      'Fully Supported' => $fullySupportedCurrencies,
      'Partially Supported' => $supportedCurrencies,
    ));     
    $array = $product->toArray();     
    if($category->parent > 0)
    {
        $array['cat_id'] = $category->parent;
    }  
    $options = array();
    $options['format'] = 'Y-M-d H:m:s';
    $array['start_time'] = date('Y-m-d H:i:s',strtotime($this->view->locale()->toDateTime($array['start_time'], $options)));
    $array['end_time'] = date('Y-m-d H:i:s',strtotime($this->view->locale()->toDateTime($array['end_time'], $options)));
    $form->populate($array);
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network','registered', 'everyone');

    foreach( $roles as $role ) {
      if( $auth->isAllowed($product, $role, 'view') ) {
        $form->auth_view->setValue($role);
      }
      if( $auth->isAllowed($product, $role, 'comment') ) {
        $form->auth_comment->setValue($role);
      }
    }
    // Check post/form
    if( !$this->getRequest()->isPost() ) {
          return;
    }
        
    $post = $this->getRequest()->getPost();
    $cat1_id = $post['cat1_id'];
    $post['cat1_id'] = "";
    if(!$form->isValid($post))
      return;
    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      $values = $form->getValues();

      $product->setFromArray($values);
      if($cat1_id > 0)
          $product->cat_id = $cat1_id;
      $product->modified_date = date('Y-m-d H:i:s');
       $flag = 0; 
      //check price
      if(!is_numeric($values['price']) || $values['price'] < 0)
      {
                $form->getElement('price')->addError('The price number is invalid! (Ex: 2000.25)');
                $flag = 1;
      }
       if(!is_numeric($values['starting_bidprice']) || $values['starting_bidprice'] < 0)
      {
                $form->getElement('starting_bidprice')->addError('The price number is invalid! (Ex: 2000.25)');
                $flag = 1;  
      }
      if(!is_numeric($values['minimum_increment']) || $values['minimum_increment'] < 0)
      {
                $form->getElement('minimum_increment')->addError('The minimum increment number is invalid! (Ex: 2000.25)');
                $flag = 1;  
      }
      if(!is_numeric($values['maximum_increment']) || $values['maximum_increment'] < 0)
      {
                $form->getElement('maximum_increment')->addError('The maximum increment number is invalid! (Ex: 2000.25)');
                $flag = 1;  
      }
      //check start time and end time
      $oldTz = date_default_timezone_get();   
      date_default_timezone_set($viewer->timezone);
      $start_time = strtotime($values['start_time']);
      $end_time =  strtotime($values['end_time']);
      $now = date('Y-m-d H:i:s');
      date_default_timezone_set($oldTz); 
      
      $product->start_time = date('Y-m-d H:i:s', $start_time);
      $product->end_time = date('Y-m-d H:i:s', $end_time);
      if($values['start_time'] < $now)
      {
          $form->getElement('start_time')->addError('Start Time should be equal or greater than Current Time!');
          $flag = 1;   
      } 
      if($values['start_time'] >= $values['end_time'])
      {
          $form->getElement('end_time')->addError('End Time should be greater than Start Time!');
          $flag = 1;   
      }
      if($values['minimum_increment'] > $values['maximum_increment'])
      {
          $form->getElement('maximum_increment')->addError('Maximum increment should be greater than minimum increment!');
          $flag = 1;   
      }  
      if($flag == 1)
      {
          return false;
      } 
      $product->price = round($product->price,2); 
      $product->starting_bidprice = round($product->starting_bidprice,2); 
      $product->bid_price = round($product->starting_bidprice,2); 
      $product->bid_time = round($product->bid_time);
       //Set Fee
      //$publish_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $viewer, 'publish_fee');
      //$featured_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $viewer, 'featured_fee');
      //$total_fee = $publish_fee;
      //if($values['featured'] == 1)
      //  $total_fee = $total_fee + $featured_fee;
      //$product->total_fee = $total_fee; 
      $product->creation_ip = ip2long($_SERVER['REMOTE_ADDR']); 
      $product->save(); 
      $cover = $values['cover'];
       // Process
      foreach( $paginator as $photo )
      {
        $subform = $form->getSubForm($photo->getGuid());
        $subValues = $subform->getValues();
        $subValues = $subValues[$photo->getGuid()];
        unset($subValues['photo_id']);

        if( isset($cover) && $cover == $photo->photo_id) {
          $product->photo_id = $photo->file_id;
          $product->save();
        }

        if( isset($subValues['delete']) && $subValues['delete'] == '1' )
        {
          if( $product->photo_id == $photo->file_id ){
            $product->photo_id = 0;
            $product->save();
          }
          $photo->delete();
        }
        else
        {
          $photo->setFromArray($subValues);
          $photo->save();
        }
      }
    // Save custom fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($product);
      $customfieldform->saveValues();
      // Auth
      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($product, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($product) as $action ) {
        @$actionTable->resetActivityBindings($action);
      }
      $db->commit();

    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'ynauction', 'controller' => 'manage','action' => 'index'), 'admin_default', true);  
  }
   public function deleteAction()
  {
    $ynauction = Engine_Api::_()->getItem('ynauction_product', $this->_getParam('auction'));
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->product_id = $ynauction->getIdentity();
    // This is a smoothbox by default
    if( null === $this->_helper->ajaxContext->getCurrentContext() )
      $this->_helper->layout->setLayout('default-simple');
    else // Otherwise no layout
      $this->_helper->layout->disableLayout(true);
    if (!$this->getRequest()->isPost())
      return;
    $db = Engine_Api::_()->getDbtable('products', 'ynauction')->getAdapter();
    $db->beginTransaction();
    try {
      $ynauction->is_delete = 1;
      $ynauction->save();
	  
	   //delete actions and attachments
		$streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
		$streamTbl->delete('(`object_id` = '.$ynauction -> getIdentity().' AND `object_type` = "ynauction_product")');
		$activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
		$activityTbl->delete('(`object_id` = '.$ynauction -> getIdentity().' AND `object_type` = "ynauction_product")');
		$attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
		$attachmentTbl->delete('(`id` = '.$ynauction -> getIdentity().' AND `type` = "ynauction_product")');
			
      Engine_Api::_()->getApi('search', 'core')->unindex($ynauction);   
       //notification 
        $notify =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 0);
        if($notify == 1)
        {
             //send notify 
             //Send sell
             $productOwner = $ynauction->getOwner();
             $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
             if($ynauction->user_id != $viewer->getIdentity())
             {
                $notifyApi->addNotification($productOwner, $viewer, $ynauction, 'ynauction_deleted_bidded', array(
  'label' => $auction->title
));
             }
             //send users
           $userBids =  Engine_Api::_()->ynauction()->getUserBid($ynauction->product_id,$ynauction->user_id);
           foreach($userBids as $bid)
           {
               if($bid->ynauction_user_id != $viewer->getIdentity() && $bid->ynauction_user_id != $productOwner->getIdentity())
                {
                  $userBid = Engine_Api::_()->getItem('user', $bid->ynauction_user_id);
                  $notifyApi->addNotification($userBid, $viewer, $ynauction, 'ynauction_deleted_bidded', array(
                    'label' => $auction->title
                  ));
                }
           }   
        }
      $db->commit();
      $this->view->success = true;
      $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Delete Auction successfully.'))
                  ));
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      throw $e;
    }
  }
}
