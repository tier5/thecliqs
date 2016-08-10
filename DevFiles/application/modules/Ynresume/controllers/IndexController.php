<?php

class Ynresume_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		$this -> _helper -> content -> setEnabled();
	}
  	
	public function composeMessageAction()
	{
		// Make form
		$this->view->form = $form = new Messages_Form_Compose();

		// Get params
		$multi = $this->_getParam('multi');
		$to = $this->_getParam('to');
		$viewer = Engine_Api::_()->user()->getViewer();
		$toObject = null;

		// Build
		$isPopulated = false;
		if( !empty($to) && (empty($multi) || $multi == 'user') ) {
			$multi = null;
			// Prepopulate user
			$toUser = Engine_Api::_()->getItem('user', $to);
			$isMsgable = true;
			if( $toUser instanceof User_Model_User &&
			(!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
			isset($toUser->user_id) &&
			$isMsgable ) {
				$this->view->toObject = $toObject = $toUser;
				$form->toValues->setValue($toUser->getGuid());
				$isPopulated = true;
			} else {
				$multi = null;
				$to = null;
			}
		} else if( !empty($to) && !empty($multi) ) {
			// Prepopulate group/event/etc
			$item = Engine_Api::_()->getItem($multi, $to);
			// Potential point of failure if primary key column is something other
			// than $multi . '_id'
			if( $item instanceof Core_Model_Item_Abstract &&
			$item->getIdentity() && (
			$item->isOwner($viewer) ||
			$item->authorization()->isAllowed($viewer, 'edit')
			)) {
				$this->view->toObject = $toObject = $item;
				$form->toValues->setValue($item->getGuid());
				$isPopulated = true;
			} else {
				$multi = null;
				$to = null;
			}
		}
		$this->view->isPopulated = $isPopulated;

		// Build normal
		if( !$isPopulated ) {
		}

		// Assign the composing stuff
		$composePartials = array();
		foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
			if( empty($data['composer']) ) {
				continue;
			}
			foreach( $data['composer'] as $type => $config ) {
				// is the current user has "create" privileges for the current plugin
				$isAllowed = Engine_Api::_()
				->authorization()
				->isAllowed($config['auth'][0], null, $config['auth'][1]);

				if( !empty($config['auth']) && !$isAllowed ) {
					continue;
				}
				$composePartials[] = $config['script'];
			}
		}
		$this->view->composePartials = $composePartials;

		// Get config
		$this->view->maxRecipients = $maxRecipients = 10;


		// Check method/data
		if( !$this->getRequest()->isPost() ) {
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}

		// Process
		$db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
		$db->beginTransaction();

		try {
			// Try attachment getting stuff
			$attachment = null;
			$attachmentData = $this->getRequest()->getParam('attachment');
			if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
				$type = $attachmentData['type'];
				$config = null;
				foreach( Zend_Registry::get('Engine_Manifest') as $data )
				{
					if( !empty($data['composer'][$type]) )
					{
						$config = $data['composer'][$type];
					}
				}
				if( $config ) {
					$plugin = Engine_Api::_()->loadClass($config['plugin']);
					$method = 'onAttach'.ucfirst($type);
					$attachment = $plugin->$method($attachmentData);
					$parent = $attachment->getParent();
					if($parent->getType() === 'user'){
						$attachment->search = 0;
						$attachment->save();
					}
					else {
						$parent->search = 0;
						$parent->save();
					}
				}
			}

			$viewer = Engine_Api::_()->user()->getViewer();
			$values = $form->getValues();

			// Prepopulated
			if( $toObject instanceof User_Model_User ) {
				$recipientsUsers = array($toObject);
				$recipients = $toObject;
				// Validate friends
				/*
				if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
					if( !$viewer->membership()->isMember($recipients) ) {
						return $form->addError('One of the members specified is not in your friends list.');
					}
				}
				*/
			} else if( $toObject instanceof Core_Model_Item_Abstract &&
			method_exists($toObject, 'membership') ) {
				$recipientsUsers = $toObject->membership()->getMembers();
				//        $recipients = array();
				//        foreach( $recipientsUsers as $recipientsUser ) {
				//          $recipients[] = $recipientsUser->getIdentity();
				//        }
					$recipients = $toObject;
			}
			// Normal
			else {
				$recipients = preg_split('/[,. ]+/', $values['toValues']);
				// clean the recipients for repeating ids
				// this can happen if recipient is selected and then a friend list is selected
				$recipients = array_unique($recipients);
				// Slice down to 10
				$recipients = array_slice($recipients, 0, $maxRecipients);
				// Get user objects
				$recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
				// Validate friends
				if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
					foreach( $recipientsUsers as &$recipientUser ) {
						if( !$viewer->membership()->isMember($recipientUser) ) {
							return $form->addError('One of the members specified is not in your friends list.');
						}
					}
				}
			}

			// Create conversation
			$conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
			$viewer,
			$recipients,
			$values['title'],
			$values['body'],
			$attachment
			);

			// Send notifications
			foreach( $recipientsUsers as $user ) {
				if( $user->getIdentity() == $viewer->getIdentity() ) {
					continue;
				}
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
				$user,
				$viewer,
				$conversation,
          'message_new'
          );
			}

			// Increment messages counter
			Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

			// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		if( $this->getRequest()->getParam('format') == 'smoothbox' ) {
			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
		        'smoothboxClose' => true,
			));
		} else {
			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
		        'redirect' => $conversation->getHref(), //$this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
			));
		}
	}
	
    public function myFavouriteAction() {
  		$this -> _helper -> content -> setEnabled();
        // Return if guest try to access to create link.
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        
        //Setup params
        $params = $this->_getAllParams();
        $originalOptions = $params;
        if (!isset($params['page']) || $params['page'] == '0') {
            $page = 1;
        }
        else {
            $page = (int)$params['page'];
        }
        
        $params['favouriter_id'] = $viewer -> getIdentity();
        $params['favourite'] = 1;
        
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynresume_resume');
        $this -> view -> paginator = $paginator = $table -> getResumesPaginator($params);
        $paginator->setCurrentPageNumber($page );
        $this->view->total = $paginator->getTotalItemCount();
        
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
    }
  
    public function mySavedAction() {
        $this -> _helper -> content -> setEnabled();
        // Return if guest try to access to create link.
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        
        //Setup params
        $params = $this->_getAllParams();
        $originalOptions = $params;
        if (!isset($params['page']) || $params['page'] == '0') {
            $page = 1;
        }
        else {
            $page = (int)$params['page'];
        }
        
        $params['saver_id'] = $viewer -> getIdentity();
        $params['save'] = 1;
        
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynresume_resume');
        $this -> view -> paginator = $paginator = $table -> getResumesPaginator($params);
        $paginator->setCurrentPageNumber($page );
        $this->view->total = $paginator->getTotalItemCount();
        
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
    }
  
  public function listingAction()
  {
        $this -> _helper -> content -> setEnabled();
		$this -> _helper -> viewRenderer -> setNoRender(true);
  }
  
  public function favouriteAction()
  {
  		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$view = Zend_Registry::get('Zend_View');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$id = $this ->_getParam('id');
		$resume = Engine_Api::_() -> getItem('ynresume_resume', $id);
		$tableFavourite = Engine_Api::_() -> getDbTable('favourites', 'ynresume');
		$favouriteRow = $tableFavourite -> getFavouriteResume($resume -> getIdentity(), $viewer -> getIdentity());
		if(!empty($favouriteRow))
		{
			//unsave action
			$favouriteRow -> delete();
			
			$resume -> favourite_count -= 1;
			$resume -> save();
			
			echo Zend_Json::encode(array('save' => 0));
			exit ;
		}
		else 
		{
			//save action
			$favouriteRow = $tableFavourite -> createRow();
			$favouriteRow -> user_id = $viewer -> getIdentity();
			$favouriteRow -> resume_id = $resume -> getIdentity();
			$favouriteRow -> creation_date = $now =  date("Y-m-d H:i:s");
			$favouriteRow -> save();
			
			$resume -> favourite_count += 1;
			$resume -> save();
			
			echo Zend_Json::encode(array('save' => 1));
			exit ;
		}
  }
  
  public function saveAction()
  {
  		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$view = Zend_Registry::get('Zend_View');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if($viewer -> getIdentity())
		{
			$id = $this ->_getParam('id');
			$resume = Engine_Api::_() -> getItem('ynresume_resume', $id);
			$tableSave = Engine_Api::_() -> getDbTable('saves', 'ynresume');
			$saveRow = $tableSave -> getSaveRow($viewer -> getIdentity(), $resume -> getIdentity());
			if(!empty($saveRow))
			{
				//unsave action
				$saveRow -> delete();
				echo Zend_Json::encode(array('save' => 0));
				exit ;
			}
			else 
			{
				//save action
				$saveRow = $tableSave -> createRow();
				$saveRow -> user_id = $viewer -> getIdentity();
				$saveRow -> resume_id = $resume -> getIdentity();
				$saveRow -> creation_date = $now =  date("Y-m-d H:i:s");
				$saveRow -> save();
				
				echo Zend_Json::encode(array('save' => 1));
				exit ;
			}
		}
  }
  
  public function whoViewedMeAction()
  {
  		$this -> _helper -> content -> setEnabled();
        
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynresume_resume', null, 'service') -> isValid())
            return;
        
		$viewTable = Engine_Api::_() -> getDbTable('views', 'ynresume');
		$this -> view -> resume = $resume = Engine_Api::_() -> ynresume() -> getUserResume();
		$this -> view -> error = false;
		if(empty($resume))
		{
			$this -> view -> error = true;
			return;
		}
		$page = $this ->_getParam('page', 1);
		$this -> view -> paginator = $paginator = $viewTable -> getViewersPaginator($resume, false);
 		$paginator->setItemCountPerPage(12);
		$paginator->setCurrentPageNumber($page);
		$this -> view -> total = $total = $viewTable -> getCountViewer($resume);
  }
  
  public function getMyLocationAction()
  {
		$latitude = $this -> _getParam('latitude');
		$longitude = $this -> _getParam('longitude');
		$values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
		echo $values;
		die ;
  }
    
    public function getPhotoAction() {
        $this -> _helper -> layout -> disableLayout();
        $id = $this ->_getParam('id');
        $this -> view -> resume = $resume = Engine_Api::_() -> getItem('ynresume_resume', $id);
    }
    
    public function manageAction() {
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        ini_set('error_reporting', -1);
        $this -> _helper -> content -> setEnabled();
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $myResume = Engine_Api::_()->ynresume()->getUserResume();
        if (!$myResume) {
            if (!$this -> _helper -> requireAuth() -> setAuthParams('ynresume_resume', null, 'create') -> isValid())
                return;
            $this->view->selectTheme = true;
            if ($this -> getRequest() -> isPost()) {
                $params = $this -> getRequest() -> getPost();
                $theme = $params['theme'];
                $table = Engine_Api::_()->getItemTable('ynresume_resume');
                $viewer = Engine_Api::_()->user()->getViewer();
                
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {
                    $resume = $table->createRow();
                    $resume->user_id = $viewer->getIdentity();
                    $resume->photo_id = ($viewer->photo_id) ? $viewer->photo_id : 0;
                    $resume->name = $viewer->getTitle();
                    $resume->theme = $theme;
                    $resume->save();
                    $db->commit();
                    $this->_helper->getHelper('Redirector')->gotoRoute(array('action' => 'manage'), 'ynresume_general');
                }
                catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
        }
        else {
            Engine_Api::_() -> core() -> setSubject($myResume);
            $this -> _helper -> viewRenderer -> setNoRender(true);
        }
    }
  
  public function editResumeAction()
  {
  		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$view = Zend_Registry::get('Zend_View');
		
		//get values
		$values = $this ->_getAllParams();
		$values['name'] = strip_tags($values['name']);
		$values['user_id'] = $viewer -> getIdentity();
		$values['location'] = $values['location_address'];
		$values['headline'] = strip_tags($values['title']);
		$values['title'] = strip_tags($values['title']);
		if(!empty($values['company']))
		{
			$values['company'] = strip_tags($values['company']);
			$values['headline'] .= ' '.$view -> translate('at').' '.strip_tags($values['company']);
		}
		$values['latitude'] = $values['lat'];
		$values['longitude'] = $values['long'];
		$values['search'] = $values['search'];
		$isEdit = true;
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			
			//check & get value if exists resume (not create new)
			$resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
			$resume = $resumeTable -> getResume($viewer -> getIdentity());
			if(!$resume -> active)
			{
				$isEdit = false;
				$resume -> active = true;
			}
			//save values
      		$resume->setFromArray($values);
			$resume -> save();
			
			//save displayname
			$viewer -> displayname = $values['name'];
			$viewer -> save(); 
			
			if(!$isEdit)
			{
				// Set auth
				$auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                $sections = Engine_Api::_()->ynresume()->getAllSections();
                $auth_arr = array_keys($sections);
                $auth_arr[] = 'view';
                if (isset($auth_arr['photo'])) unset($auth_arr['photo']);
                foreach ($auth_arr as $elem) {
                    $auth_role = 'everyone';
                    if ($auth_role) {
                        $roleMax = array_search($auth_role, $roles);
                        foreach ($roles as $i=>$role) {
                           $auth->setAllowed($resume, $role, $elem, ($i <= $roleMax));
                        }
                    }    
                }
				
				//add activity
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($resume -> getOwner(), $resume, 'ynresume_resume_create');
				if($action) {
					$activityApi->attachActivity($action, $resume);
				}
				
				//add credits
				if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
		        {
		        	$user = $resume -> getOwner();
					if($user -> getIdentity())
		            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynresume_new', $user);
				}
			}
			
			$db -> commit();
			echo Zend_Json::encode(array('error_code' => 0));

		} catch (Exception $e) {
			$db -> rollBack();
			echo Zend_Json::encode(array('error_code' => 1));
			
		}
  }
  
  public function getCustomGroupAction()
  {
		// Disable layout and viewrenderer
		$this -> _helper -> layout -> disableLayout();
		$isEdit = $this ->_getParam('edit', false);
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynresume_resume');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array(
				'topLevelId' => $profileTypeField -> field_id, 
				'topLevelValue' => 1,
				'heading' => $this ->_getParam('field_id'),
			);
		}
		$this -> view -> resume_id = $this ->_getParam('resume_id');
		$resume = Engine_Api::_() -> getItem('ynresume_resume', $this ->_getParam('resume_id'));
	   	if($isEdit)
		{
			$this -> view -> form = $form = new Ynresume_Form_Custom_Create( array(
				'formArgs' => $formArgs,
				'item' => $resume,
			));
		}
		else 
		{
			$this -> view -> form = $form = new Ynresume_Form_Custom_Create( array(
				'formArgs' => $formArgs,
			));
		}
  }
  
  public function removeGroupAction()
  {
  		// Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$resume = Engine_Api::_() -> getItem('ynresume_resume', $this ->_getParam('resume_id'));
		$valuesTable = Engine_Api::_() -> getDbTable('values', 'ynresume');
		
		$fieldIds = Engine_Api::_()->getApi('fields','ynresume')->getFieldIdsFullHeading($resume, $this ->_getParam('field_id'), 1, 1);
		if(count($fieldIds) > 0)
		{
			$select = $valuesTable -> select() -> where('field_id IN (?)', $fieldIds);
			foreach($valuesTable -> fetchAll($select) as $value) {
				$value -> delete();
			}
		}
		echo Zend_Json::encode(array('error_code' => 0));
		exit();
  }
  
  public function saveGroupAction()
  {
  		// Disable layout and viewrenderer
		$this -> _helper -> layout -> disableLayout();
		
		$resume = Engine_Api::_() -> getItem('ynresume_resume', $this ->_getParam('resume_id'));
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynresume_resume');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array(
				'topLevelId' => $profileTypeField -> field_id, 
				'topLevelValue' => 1,
				'heading' => $this ->_getParam('field_id'),
			);
		}
		
	   	 $form = new Ynresume_Form_Custom_Create( array(
			'formArgs' => $formArgs,
		 ));
		 
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$form -> isValid($posts)) {
			$form_messages = $form->getMessages();
			echo Zend_Json::encode(array('error_code' => 1, 'message' => $form_messages));
			exit ;
		}
		
		//save custom field values
		$customdefaultfieldform = $form -> getSubForm('fields');
		$customdefaultfieldform -> setItem($resume);
		$customdefaultfieldform -> saveValues();
		echo Zend_Json::encode(array('error_code' => 0));
		exit();
    }
  
    public function displayMapViewAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $resumeIds = $this->_getParam('ids', '');
        if ($resumeIds != '') {
            $resumeIds = explode("_", $resumeIds);
        }
        $table = Engine_Api::_()->getItemTable('ynresume_resume');
        $select = $table -> select();
        
        if (is_array($resumeIds) && count($resumeIds)) {
            $select -> where ("resume_id IN (?)", $resumeIds);
        }
        else {
            $select -> where ("resume_id IN (0)");
        }
        $resumes = $table->fetchAll($select);
            
        $datas = array();
        $contents = array();
        $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ;
        $view = Zend_Registry::get("Zend_View");
        $locationArr = array();
        foreach($resumes as $resume) {
            if($resume -> latitude) {               
                $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynresume/externals/images/maker.png';
                $key = "{$resume -> latitude},{$resume -> longitude}";
                $locationArr[$key][] = $resume;
            }
        }
        
        foreach ($locationArr as $resumeList) {
            if (count($resumeList) == 1) {
                $resume = $resumeList[0];
                $datas[] = array(   
                        'resume_id' => $resume -> getIdentity(),              
                        'latitude' => $resume -> latitude,
                        'longitude' => $resume -> longitude,
                        'icon' => $icon
                );
                $contents[] = '
                    <div class="ynresume-maps-main" style="overflow: hidden;">  
                        <div class="ynresume-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">                          
                            <div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden">
                                '. $view->itemPhoto($resume, "thumb.icon") .'
                            </div>                              
                            <a href="'.$resume->getHref().'" class="ynresume-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
                                '.$resume->getTitle().'
                            </a>
                        </div>
                    </div>
                ';
            }
            else if (count($resumeList) > 1) {
                $resume = $resumeList[0];
                $datas[] = array(   
                        'business_id' => $resume -> getIdentity(),              
                        'latitude' => $resume -> latitude,
                        'longitude' => $resume -> longitude,
                        'icon' => $icon
                );
                $str = '<div>' . count($resumeList) . $view->translate(" resumes") . '</div>';
                foreach ($resumeList as $resume){
                    $str .= '
                        <div class="ynresume-maps-main" style="overflow: hidden;">  
                            <div class="ynresume-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">                          
                                <div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden">
                                    '. $view->itemPhoto($resume, "thumb.icon") .'
                                </div>                              
                                <a href="'.$resume->getHref().'" class="ynresume-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
                                    '.$resume->getTitle().'
                                </a>
                            </div>
                        </div>
                    ';
                }
                $contents[] = $str;
            }
        }
        
        echo $this ->view -> partial('_map_view.tpl', 'ynresume', array('datas'=>Zend_Json::encode($datas), 'contents' => Zend_Json::encode($contents)));
        exit();
    }
  
  public function placeOrderAction() 
    {
    	$settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> resume = $resume = Engine_Api::_() -> getItem('ynresume_resume', $this ->_getParam('id'));
       	$this -> view -> service_day_number = $service_day_number = $this ->_getParam('service_day_number');
		$this -> view -> feature_day_number = $feature_day_number = $this ->_getParam('feature_day_number');
	   
        if($resume->user_id != $viewer->getIdentity())
        {
            $message = $this -> view -> translate('You do not have permission to do this.');
            return $this -> _redirector($message);
        }

        if (!$service_day_number && !$feature_day_number) {
            $message = $this -> view -> translate('Please set service or feature day.');
            return $this -> _redirector($message);
        }
		
		//check if service resume
		if($service_day_number)
		{
			if($service_day_number <= 0)
			{
				$message = $this -> view -> translate('Invalid service day.');
            	return $this -> _redirector($message);
			}
		}
		//check if feature resume
		if($feature_day_number)
		{
			if($feature_day_number <= 0)
			{
				$message = $this -> view -> translate('Invalid service day.');
            	return $this -> _redirector($message);
			}
		}
		
		//Credit
        //check permission
        // Get level id
        $id = $viewer->level_id;
    	$action_type = "";
        if ($this -> _helper -> requireAuth() -> setAuthParams('ynresume_resume', null, 'use_credit') -> checkRequire()) {
            $allowPayCredit = 0;
            $credit_enable = Engine_Api::_() -> hasModuleBootstrap('yncredit');
            if ($credit_enable)
            {
            	if($service_day_number){
					$action_type = 'ynresume_service';
				}
				elseif($feature_day_number) {
					$action_type = 'ynresume_feature';
				}
                $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
                $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
			    $type_spend = $typeTbl -> fetchRow($select);
				if($type_spend)
				{
					$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
					$select = $creditTbl->select()
		                ->where("level_id = ? ", $id)
		                ->where("type_id = ?", $type_spend -> type_id)
		                ->limit(1 );
		            $spend_credit = $creditTbl->fetchRow($select);
					if($spend_credit)
					{
		               $allowPayCredit = 1;
		            }
				}
			}
            $this -> view -> allowPayCredit = $allowPayCredit;
        };
		
		if($service_day_number){
			$this -> view -> service_fee = $service_fee = $settings->getSetting('ynresume_fee_service', 0);
			$this -> view -> total_pay = $total_pay = $service_day_number * $service_fee;
		}
		elseif($feature_day_number) {
			$this -> view -> feature_fee = $feature_fee = $settings->getSetting('ynresume_fee_feature', 0);
			$this -> view -> total_pay = $total_pay = $feature_day_number * $feature_fee;
		}
		
	   
	   //if free
	   if($total_pay == 0)
	   {
			//core - service resume
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try 
			{
				if($service_day_number) {
					Engine_Api::_() -> ynresume() -> serviceResume($resume->getIdentity(), $service_day_number);
				}
				elseif($feature_day_number) {
					Engine_Api::_() -> ynresume() -> featureResume($resume->getIdentity(), $feature_day_number);
				}
				$db -> commit();
			} 
			catch (Exception $e) {
		      $db->rollBack();
		      throw $e;
		    }
		    
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'resume_id' => $resume -> getIdentity(),
					'slug' => $resume -> getSlug(),
				), 'ynresume_specific', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Success...'))
			 ));
		}  
	   
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit)) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
		
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynresume');
		
        if ($row = $ordersTable -> getLastPendingOrder()) {
           $row -> delete();
        }
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
        try 
        {
        	if($service_day_number){
        		$ordersTable -> insert(array(
	            	'user_id' => $viewer -> getIdentity(), 
		            'creation_date' => new Zend_Db_Expr('NOW()'), 
					'service_day_number' => $service_day_number,
		            'item_id' => $resume -> getIdentity(),
		            'price' => $total_pay, 
		            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				));
			}
			elseif($feature_day_number){
				$ordersTable -> insert(array(
	            	'user_id' => $viewer -> getIdentity(), 
		            'creation_date' => new Zend_Db_Expr('NOW()'), 
					'feature_day_number' => $feature_day_number,
		            'item_id' => $resume -> getIdentity(),
		            'price' => $total_pay, 
		            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				));
			}
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
		
        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
    }

    public function updateOrderAction() 
    {
        $type = $this ->_getParam('type');
        $id = $this ->_getParam('id');
        if(isset($type))
        {
            switch ($type) {
                
                case 'paycredit':
					$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynresume');
					$order = $ordersTable -> getLastPendingOrder();
                    return $this -> _forward('success', 'utility', 'core', 
                        array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                        array(
	                        'action' => 'pay-credit', 
	                        'item_id' => $id,
							'order_id' => $order -> getIdentity()
						), 'ynresume_general', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                default:
                    
                    break;
            }
        }

        $resume = Engine_Api::_() -> getItem('ynresume_resume', $id);
            
        $gateway_id = $this -> _getParam('gateway_id', 0);
        if (!$gateway_id) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynresume');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'ynresume', 'cancel_route' => 'ynresume_transaction', 'return_route' => 'ynresume_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'transaction', 'action' => 'process', 'order_id' => $order -> getIdentity(), ), 'ynresume_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
	public function payCreditAction()
    {
    	$credit_enable = Engine_Api::_() -> hasModuleBootstrap('yncredit');
        if (!$credit_enable)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
		$order = Engine_Api::_()->getItem('ynresume_order', $this->_getParam('order_id'));
		if(!$order)
        {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
		$action_type = "";
		if($order -> service_day_number)
		{
			$action_type = 'ynresume_service';
		}
		else
		{
			$action_type = 'ynresume_feature';
		}
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
		$this-> view -> item = $resume = Engine_Api::_() -> getItem('ynresume_resume', $item_id);
	    $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
		if($order -> service_day_number)
		{
	        $cancel_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(
		          array(
		            'action' => 'place-order',
		            'id' => $item_id,
		            'service_day_number' => $order -> service_day_number,
		          ), 'ynresume_general', true);
			}
		elseif($order -> feature_day_number)
		{
			$cancel_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(
		          array(
		            'action' => 'place-order',
		            'id' => $item_id,
		            'feature_day_number' => $order -> feature_day_number,
		          ), 'ynresume_general', true);
		}	  
	    //publish fee
        $this -> view -> total_pay = $total_pay =  $order -> price ;    
        $credits = ceil(($total_pay * $defaultPrice * $numbers));
        $this -> view -> cancel_url = $cancel_url;
        $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
        if (!$balance) 
        {
          $currentBalance = 0;
        } else 
        {
          $currentBalance = $balance->current_credit;
        }
        $this->view->currentBalance = $currentBalance;
        $this->view->credits = $credits;
        $this->view->enoughCredits = $this->_checkEnoughCredits($credits);
    
        // Check method
        if (!$this->getRequest()->isPost()) 
        {
          return;
        }
    	$service_day_number = $order -> service_day_number;
		$feature_day_number = $order -> feature_day_number;
    	
        // Insert member transaction
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynresume');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
			$description = "";
			if($service_day_number){
				Engine_Api::_() -> ynresume() -> serviceResume($resume->getIdentity(), $order -> service_day_number);
				$description = $this ->view ->translate(array("Register \"Who Viewed Me\" service for %s day", "Register \"Who Viewed Me\" service for %s days" , $order -> service_day_number), $order -> service_day_number);
			}
			elseif($feature_day_number) {
				Engine_Api::_() -> ynresume() -> featureResume($resume->getIdentity(), $order -> feature_day_number);
				$description = $this ->view ->translate(array("Feature resume for %s day", "Feature resume for %s days" , $order -> feature_day_number), $order -> feature_day_number);
			}
			
			//save transaction
	     	$transactionsTable->insert(array(
		     	'creation_date' => date("Y-m-d"),
		     	'status' => 'completed',
		     	'gateway_id' => '-3',
		     	'amount' => $order->price,
		     	'currency' => $order->currency,
		     	'user_id' => $order->user_id,
		     	'item_id' => $order->item_id,
		     	'description' => $description,
			 ));
			 
	      $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
        Engine_Api::_()->yncredit()-> spendCredits($viewer, (-1) * $credits, $viewer->getTitle(), $action_type, $viewer);
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('resume_id' => $resume->getIdentity(), 'slug' => $resume -> getSlug()), 'ynresume_specific', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }
  	
	protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynresume_general', true), 'messages' => array($message)));
	}
	
	protected function _checkEnoughCredits($credits)
	{
		$balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
		if (!$balance) {
			return false;
		}
		$currentBalance = $balance->current_credit;
		if ($currentBalance < $credits) {
			return false;
		}
		return true;
	}
	
	public function importAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
        $this -> view -> canImport = Engine_Api::_()->hasModuleBootstrap('socialbridge');

		// Render
		$this -> _helper -> content	-> setEnabled();
	}

    public function unsaveAction() {
        $this->_helper->layout->setLayout('admin-simple');
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $id = $this ->_getParam('resume_id');
        $resume = Engine_Api::_() -> getItem('ynresume_resume', $id);
        $tableSave = Engine_Api::_() -> getDbTable('saves', 'ynresume');
        $saveRow = $tableSave -> getSaveRow($viewer -> getIdentity(), $resume -> getIdentity());
        if(empty($saveRow))
            return $this->_helper->requireSubject()->forward();
        $this->view->resume_id = $id;
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $saveRow -> delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This resume has been removed from your saved resumes list.')
            ));
        }
    }
}
