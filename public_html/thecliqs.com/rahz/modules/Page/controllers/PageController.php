<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_PageController extends Core_Controller_Action_Standard
{
	public function init()
	{
    if( !$this->_helper->requireUser()->isValid() ) return;
		$page_id = (int)$this->_getParam('page_id');

    $this->view->page = $page = Engine_Api::_()->getItem('page', $page_id);
    if( !$page ) {
      $this->_redirectCustom(array('route' => 'page_browse'));
    }
    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($page);
    }
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
		if ($page == null) {
			$this->_redirectCustom(array('route' => 'page_browse'));
		}

		if ( !$this->_helper->requireUser()->isValid() || !($page->isAdmin() || $this->_getParam('action') == 'add-favorites')) {
			$this->_redirectCustom(array('route' => 'page_browse'));
  	}

    /**
     *  @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $isOwner = $page->isOwner($viewer);

    if($this->_getParam('action') != 'delete' && !$page->isDefaultPackageEnabled() ) {
      if ( $isOwner ) {
        $this->_redirectCustom(array('route' => 'page_package_choose', 'page_id' => $page->page_id));
      } else {
        $this->_redirectCustom(array('route' => 'page_browse'));
      }
    }


    if ($settings->getSetting('page.package.enabled', 0)) {
      $this->view->packageEnabled = true;
      $this->view->isOwner = $isOwner;
      $this->view->package =  $package = $page->getPackage();
      $this->view->currency = $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
      if ( null != ($subscription = Engine_Api::_()->getItemTable('page_subscription')->getSubscription($page->getIdentity(), true))) {
        $this->view->subscription_expired = $subscription->expiration_date;
      }
      if( !$package ) {
        $package = Engine_Api::_()->getDbTable('packages', 'page')->getDefaultPackage();
      }
      $this->view->isDefaultPackage = $package->isDefault();
    }

    $this->view->isAllowLayout = $page->isAllowLayout();
    $this->view->isAllowPagecontact = $page->isAllowPagecontact();
    $this->view->isAllowPagefaq = $page->isAllowPagefaq();
    $this->view->isAllowStore = $page->isAllowStore();
    $this->view->isAllowDonation = $page->isAllowDonation();
    $this->view->isAllowStyle = $page->isAllowStyle();
    $this->view->isAllowInvite = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('inviter');
    $this->view->isAllowedBadge = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hebadge');
    $this->view->action = $this->_getParam('action');

	}
	
  public function deleteAction()
  {
		/**
		 * @var $page Page_Model_Page
		 */
  	$page_id = $this->_getParam('page_id');
 		$page = $this->view->page;
 		
  	$this->view->form = $form = new Page_Form_Delete();
  	
  	$form->setAction($this->view->url(array('action' => 'delete', 'page_id' => $page_id), 'page_team'));
  	$description = sprintf(Zend_Registry::get('Zend_Translate')
  	  ->_('PAGE_DELETE_DESC'), $this->view->htmlLink($page->getHref(), $page->getTitle()));
  	  
  	$form->setDescription($description);
  	
  	if (!$this->getRequest()->isPost()) {
  		return;
  	}

	  $db = Engine_Api::_()->getDbtable('pages', 'page')->getAdapter();
    $db->beginTransaction();
    
    try {
      if (null != ($subs = Engine_Api::_()->getItemTable('page_subscription')->getSubscription($page_id))) {
        $subs->delete();
      }

      $page->delete();
    	$db->commit();
    } catch (Exception $e) {
    	$db->rollBack();
    	throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'page_manage'));
  }
    
  public function deletePhotoAction()
  {
    // Get form
    $this->view->form = $form = new Page_Form_DeletePhoto();

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $page = Engine_Api::_()->core()->getSubject();
    $page->removePhotos();
    $page->photo_id = 0;
    $page->save();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your photo has been deleted.');

    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => $this->view->url(array('action' => 'edit-photo', 'page_id' => $page->getIdentity()), 'page_team', true),
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photo has been deleted.'))
    ));
  }
  
  public function editAction()
  {
		/**
		 * @var $page Page_Model_Page
		 */

 		$page = $this->view->page;
    $coordinates = $this->_getParam('coordinates', false);

    if (!$page->approved) {
      $page->search = $page->approved;
    }

    $this->view->form = $form = new Page_Form_Edit(array('item' => $page));
    $this->view->setInfoJSON = json_encode($form->getSetInfo());
    $this->view->isMultiMode = count($form->getSetInfo()) > 1;
    $this->view->form->getSubForm('fields')->getElement('0_0_1')->setLabel('Category');
    $form->getDecorator('description')->setOption('escape', false);

    // add map element
    $mapJs = Engine_Api::_()->getApi('gmap', 'page')->getMapJS();
    $pageMarker = Engine_Api::_()->getApi('gmap', 'page')->getPageMarker($page);
    if ($coordinates) {
      $coordinate_arr = explode(';', $coordinates);
      $pageMarker['lat'] = $coordinate_arr[0];
      $pageMarker['lng'] = $coordinate_arr[1];
    }

    $markers = array($pageMarker);
    $bounds = Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers);
    $markers = Zend_Json_Encoder::encode($markers);
    $bounds = Zend_Json_Encoder::encode($bounds);
    $form->addMapElement($mapJs, $markers, $bounds);

    $customfieldform = $form->getSubForm('fields');
    $this->view->topLevelId = $customfieldform->getTopLevelId();
    $this->view->topLevelValue = $customfieldform->getTopLevelValue();

    $form->populate($page->toArray());

    $tags = $page->tags()->getTagMaps();

    $tagString = '';
    foreach ( $tags as $tagmap ) {
      if( $tagString !== '' ) $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }

    $form->tags->setValue($tagString);

  	if (!$this->getRequest()->isPost()) {
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return ;
    }

    $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      /**
       * @var $customfieldform Page_Form_Custom_Fields
       **/
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($page);
      $customfieldform->saveValues();
      $customfieldform->removeElement('submit');

      $address = array($values['country'], $values['state'], $values['city'], $values['street']);

      if ($address[0] == '' && $address[1] == '' && $address[2] == '' && $address[3] == '' && !$coordinates) {
        $page->deleteMarker();
      } elseif ($page->isAddressChanged($address) && !$coordinates) {
        if(!$address[0]) {
          unset($address[0]);
        }
        if(!$address[1]) {
          unset($address[1]);
        }
        if(!$address[2]) {
          unset($address[2]);
        }
        if(!$address[3]) {
          unset($address[3]);
        }
        $page->addMarkerByAddress($address);
      }

      if ($coordinates) {
        $coordinate_arr = explode(';', $coordinates);
        $pageMarker = $page->getMarker(true);
        $pageMarker->latitude = $coordinate_arr[0];
        $pageMarker->longitude = $coordinate_arr[1];
        $pageMarker->save();
      }

      $raw_tags = preg_split('/[,]+/', $values['tags']);
      $tags = array();
      foreach ($raw_tags as $tag) {
        $tag = trim(strip_tags($tag));
        if ($tag == "") {
          continue ;
        }
        $tags[] = $tag;
      }
      $page->tags()->setTagMaps($this->view->viewer, $tags);

      $misTypes = array('http//', 'htp://', 'http://');
      $values['website'] = str_replace($misTypes, '', trim($values['website']));

      if (function_exists('mb_convert_encoding')) {
        $values['description'] = mb_convert_encoding($values['description'], 'UTF-8');
        $values['title'] = mb_convert_encoding(strip_tags( $values['title'] ), 'UTF-8');
      } else {
        $values['title'] = Engine_String::strip_tags($values['title']);
      }

      $page->setFromArray($values);
      $page->displayname = $page->title;
      $page->keywords = $values['tags'];
      $page->set_id = $values['category'];
      $page->modified_date = date('Y-m-d H:i:s');

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Changes were successfully saved.'));

      $page->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $pageMarker = Engine_Api::_()->getApi('gmap', 'page')->getPageMarker($page);
    $markers = array($pageMarker);
    $bounds = Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers);
    $markers = Zend_Json_Encoder::encode($markers);
    $bounds = Zend_Json_Encoder::encode($bounds);
    $form->addMapElement($mapJs, $markers, $bounds);
  }

  public function badgesAction()
  {

  }

  public function privacyAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    /**
     * @var $page Page_Model_Page
     */
    $page = $this->view->page;
    $this->view->form = $form = new Page_Form_Privacy(array('page' => $page));

    $auth = Engine_Api::_()->authorization()->context;

    $roles = array('team', 'likes', 'registered', 'everyone');
    foreach ($roles as $roleString) {
      $role = $roleString;

      if( $role === 'team' ) {
        $role = $page->getTeamList();
      } elseif( $role === 'likes' ) {
        $role = $page->getLikesList();
      }

      if ( 1 === $auth->isAllowed($page, $role, 'view') && !empty($form->auth_view) ) {
        $form->auth_view->setValue($roleString);
      }
    }

    $roles = array('team', 'likes', 'registered');
    foreach ($roles as $roleString) {
      $role = $roleString;

      if( $role === 'team' ) {
        $role = $page->getTeamList();
      } elseif( $role === 'likes' ) {
        $role = $page->getLikesList();
      }

      if ( 1 === $auth->isAllowed($page, $role, 'comment') && !empty($form->auth_comment) ) {
        $form->auth_comment->setValue($roleString);
      }
    }

    $pageApi = Engine_Api::_()->page();
    $page_features = $page->getAllowedFeatures();

    if( $pageApi->isModuleExists('pagealbum') &&  in_array('pagealbum', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        } elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'album_posting') && !empty($form->auth_album_posting) ) {
          $form->auth_album_posting->setValue($roleString);
        }
      }
    }

    if( $pageApi->isModuleExists('pageblog') &&  in_array('pageblog', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        } elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'blog_posting') && !empty($form->auth_blog_posting) ) {
          $form->auth_blog_posting->setValue($roleString);
        }
      }
    }

    if( $pageApi->isModuleExists('pagediscussion') &&  in_array('pagediscussion', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        } elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'disc_posting') && !empty($form->auth_disc_posting) ) {
          $form->auth_disc_posting->setValue($roleString);
        }
      }
    }

    if( $pageApi->isModuleExists('pagedocument') &&  in_array('pagedocument', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        } elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'doc_posting') && !empty($form->auth_doc_posting) ) {
          $form->auth_doc_posting->setValue($roleString);
        }
      }
    }

    if( $pageApi->isModuleExists('pageevent') &&  in_array('pageevent', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        } elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'event_posting') && !empty($form->auth_event_posting) ) {
          $form->auth_event_posting->setValue($roleString);
        }
      }
    }

    if( $pageApi->isModuleExists('pagemusic') &&  in_array('pagemusic', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        } elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'music_posting') && !empty($form->auth_music_posting) ) {
          $form->auth_music_posting->setValue($roleString);
        }
      }
    }

    if( $pageApi->isModuleExists('pagevideo') &&  in_array('pagevideo', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        } elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'video_posting') && !empty($form->auth_video_posting) ) {
          $form->auth_video_posting->setValue($roleString);
        }
      }
    }

    if( $pageApi->isModuleExists('store') &&  in_array('store', $page_features) ) {
      $roles = array('owner', 'team');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        }

        if ( 1 === $auth->isAllowed($page, $role, 'store_posting') && !empty($form->auth_store_posting) ) {
          $form->auth_store_posting->setValue($roleString);
        }
      }
    }
    if($pageApi->isModuleExists('donation') && in_array('donation', $page_features)){
      $roles = array('owner','team');
      foreach($roles as $roleString){
        $role = $roleString;

        if($role == 'team'){
          $role = $page->getTeamList();
        }

        if(1 === $auth->isAllowed($page, $role, 'charity_posting') && !empty($form->auth_charity_posting)){
          $form->auth_charity_posting->setValue($roleString);
        }

        if(1 === $auth->isAllowed($page, $role, 'project_posting') && !empty($form->auth_project_posting)){
          $form->auth_project_posting->setValue($roleString);
        }
      }
    }

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return ;
    }

    $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $page->setPrivacy($values);
      $page->search = $values['search'];

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));

      $page->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

  }

  public function editPhotoAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return 0;

    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->core()->getSubject();
    $this->view->form = $form = new Page_Form_Photo();

    if( empty($page->photo_id) ) {
      $form->removeElement('remove');
    }

    if (!$this->getRequest()->isPost()) {
      return 0;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return 0;
    }

    if( $form->Filedata->getValue() !== null ) {
      $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
      $db->beginTransaction();

      try {
        $fileElement = $form->Filedata;

        $page->setPhoto($fileElement);

        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Image was successfully proccessed.'));

        $page->save();
        $db->commit();
      } catch( Engine_Image_Adapter_Exception $e ) {
        $db->rollBack();
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    } else if( $form->getValue('coordinates') !== '' ) {
        $storage = Engine_Api::_()->storage();

        $iProfile = $storage->get($page->photo_id, 'thumb.profile');
        $iSquare = $storage->get($page->photo_id, 'thumb.icon');

        // Read into tmp file
        $pName = $iProfile->getStorageService()->temporary($iProfile);
        $iName = dirname($pName) . '/nis_' . basename($pName);

        list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));

        $image = Engine_Image::factory();
        $image->open($pName)
          ->resample($x+.1, $y+.1, $w-.1, $h-.1, 48, 48)
          ->write($iName)
          ->destroy();

        $iSquare->store($iName);

        // Remove temp files
        @unlink($iName);
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'edit-photo', 'page_id' => $page->getIdentity()), 'page_team', true);
  }

	public function postNoteAction()
	{
		$note = $this->_getParam('note');
		$page = $this->view->page;

		$page->note = trim(strip_tags($note));
		$page->save();
		$this->view->note = nl2br($page->note);
		$this->view->result = 1;
		$this->view->message = "Ok!";
	}

  public function addFavoritesAction()
  {
    $page_id = (int)$this->_getParam('page_id');
    $favorites = $this->_getParam('favorites');
    $page = Engine_Api::_()->getItem('page', $page_id);

    if (!$page_id) {
      $this->view->message = $this->view->translate('Wrong parameter set.');
      $this->view->type = 'error';
			return ;
    }
    
		if (!$page) {
      $this->view->message = $this->view->translate('Pages does not exists.');
      $this->view->type = 'error';
			return ;
		}
    		
    $table = Engine_Api::_()->getDbTable('favorites', 'page');
    $db = $table->getAdapter();
    $db->beginTransaction();
    
    try {
      foreach ($favorites as $page_fav_id) {
        $table->insert(array(
          'page_id' => $page_id,
          'page_fav_id' => $page_fav_id
        ));
      }
      $db->commit();
      $this->view->message = $this->view->translate('Pages were successfully added.');
      $this->view->type = 'text';
    } catch (Exception $e) {
      $this->view->message = $this->view->translate($e->getMessage());
      $this->view->type = 'error';
      $db->rollBack();
      throw $e;
    }
    
  }

  public function deleteFavoritesAction()
  {
    $page_id = (int)$this->_getParam('page_id', 0);
    $favorites = $this->_getParam('favorites');
    $page = Engine_Api::_()->getItem('page', $page_id);

    if (!$page_id) {
      $this->view->message = $this->view->translate('Wrong parameter set.');
      $this->view->type = 'error';
			return ;
    }

		if (!$page) {
      $this->view->message = $this->view->translate('Pages does not exists.');
      $this->view->type = 'error';
			return ;
		}

		if (!$page->isAdmin()) {
      $this->view->message = $this->view->translate('You have no permission to do this.');
      $this->view->type = 'error';
  		return ;
  	}

    $table = Engine_Api::_()->getDbTable('favorites', 'page');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      foreach ($favorites as $page_fav_id) {
        $table->delete(array(
          'page_id = ?' => $page_fav_id,
          'page_fav_id = ?' => $page_id
        ));
      }
      $db->commit();
      $this->view->message = $this->view->translate('Pages were successfully deleted.');
      $this->view->type = 'text';
    } catch (Exception $e) {
      $this->view->message = $this->view->translate($e->getMessage());
      $this->view->type = 'error';
      $db->rollBack();
      throw $e;
    }

  }

  public function manageAdminsAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    /**
     * @var $page Page_Model_Page
     */
    $page = $this->view->page;
    $this->view->is_super_admin = (bool) $page->isOwner($this->view->viewer);
    $this->view->admins = $page->getAdmins();
    $this->view->employers = $page->getEmployers();
  }

  public function appsAction()
  {
    $page = $this->view->page;
    $this->view->sub_menu = $this->_getParam('sub-menu', 'contact');

    if ($this->view->isAllowStore && $page->getStorePrivacy()) {
      $path = Engine_Api::_()->page()->getModuleDirectory('store');
      $this->view->addScriptPath($path . '/views/scripts');
      $this->view->isGatewayEnabled = (boolean)Engine_Api::_()->getDbTable('apis', 'store')->getEnabledGateways($page->getIdentity());
      $this->view->page_id = $page->getIdentity();
    }

    if($this->view->sub_menu == 'donation'){
      return $this->_helper->redirector->gotoRoute(array('controller' => 'page','action' => 'index', 'page_id' => $page->getIdentity()),'donation_extended',true);
    }
    if ($this->view->isAllowInvite) {
      $this->view->fb_settings = Engine_Api::_()->inviter()->getFacebookSettings($this->view, $page);
      $providers = Engine_Api::_()->inviter()->getProviders2(false, 15);
      $this->view->providers = Engine_Api::_()->inviter()->getIntegratedProviders();
      $this->view->count = count($providers);

      $session = new Zend_Session_Namespace('contacts');
      $session->unsetAll();

      $form = new Inviter_Form_Widget_PageImport();
      $form->page_id->setValue($page->getIdentity());

      if( !$page ) {
        $this->_redirect('browse-pages');
      }

      $this->view->form = $form;

      if ($this->view->viewer->getIdentity()) {
        $form_write = new Inviter_Form_Widget_PageWrite();
        $form_write->page_id->setValue($page->getIdentity());
        $this->view->form_write = $form_write;
      }
    }
  }

  public function getStartedAction()
  {

  }

  public function styleAction()
  {
    $page = $this->view->page;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->view->isAllowStyle) {
      $this->_redirectCustom(array('route' => 'page_team', 'action'=>'edit', 'page_id'=>$page->getIdentity()));
    }

    // Get form
    $this->view->form = $form = new User_Form_Edit_Style();
    $form->getElement('style')->setDescription('Add your own CSS code above to give your page profile a more personalized look.');

    // Get current row
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $page->getType())
      ->where('id = ?', $page->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    // Not posting, populate
    if (!$this->getRequest()->isPost()) {
      $form->populate(array(
        'style' => (null === $row ? '' : $row->style)
      ));
      return;
    }

    // Whoops, form was not valid
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Cool! Process
    $style = $form->getValue('style');

    // Process
    $style = strip_tags($style);

    $forbiddenStuff = array(
      '-moz-binding',
      'expression',
      'javascript:',
      'behaviour:',
      'vbscript:',
      'mocha:',
      'livescript:',
    );

    $style = str_replace($forbiddenStuff, '', $style);

    // Save
    if (null == $row) {
      $row = $table->createRow();
      $row->type = $page->getType();
      $row->id = $page->getIdentity();
    }

    $row->style = $style;
    $row->save();

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
  }
}