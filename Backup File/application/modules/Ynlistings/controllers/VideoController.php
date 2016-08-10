<?php
class Ynlistings_VideoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($listing_id = (int)$this -> _getParam('listing_id')) && null !== ($listing = Engine_Api::_() -> getItem('ynlistings_listing', $listing_id)))
			{
				Engine_Api::_() -> core() -> setSubject($listing);
			}
		}
	}
	
	public function manageAction()
	{
		//Checking Video Plugin - Viewer required -View privacy
		if(!Engine_Api::_()->hasItemType('video'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($listing, null, 'view') -> isValid())
		{
			return;
		}
		//Get viewer, listing, search form
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $form = new Ynlistings_Form_Video_Search();

		// Check create video authorization
		$this -> view -> canCreate = $canCreate = $listing->canUploadVideos();

		//Prepare data filer
		$params = array();
		$params = $this -> _getAllParams();
		$params['listing_id'] = $listing -> getIdentity();
		$params['user_id'] = $viewer -> getIdentity();
		$form -> populate($params);
		$this -> view -> formValues = $formValues = $form -> getValues();
		$params['title'] = $formValues['title'];
		$params['owner'] = $formValues['owner'];
		//Get data
		$tableMappings = Engine_Api::_()->getItemTable('ynlistings_mapping');
		$this -> view -> paginator = $paginator = $tableMappings -> getWidgetVideosPaginator($params);
		 // Set item count per page and current page number
   		$paginator -> setItemCountPerPage(6);
        $paginator -> setCurrentPageNumber($this->_getParam('page'), 1);
	}
	
	public function listAction()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		//Check viewer and subject requirement
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity())
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		//Checking Ynvideo Plugin - View privacy
		$ynvideo_enable = Engine_Api::_() -> ynlistings() -> checkYouNetPlugin('ynvideo');
		$video_enable = Engine_Api::_() -> ynlistings() -> checkYouNetPlugin('video');
		$this->view->form = $form = new Ynlistings_Form_Photo_Manage();
		if (!$video_enable && !$ynvideo_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		$this->view->can_select_theme = $can_select_theme = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'select_theme') -> checkRequire();	
		// Check create video authorization

		if ($viewer -> getIdentity() == $listing->user_id)
		{
			$this -> view -> canCreate = true;
		}
		else
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		//Get data
		$tableMapping = Engine_Api::_()->getItemTable('ynlistings_mapping');
		$params['listing_id'] = $this->_getParam('listing_id');
		$this -> view -> paginator = $paginator = $tableMapping->getVideosPaginator($params);
		
		foreach( $paginator as $video )
	    {
	      $subform = new Ynlistings_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));
	      if($video->video_id == $listing->video_id)
	        $subform->removeElement('delete');
	      $subform->populate($video->toArray());
	      $form->addSubForm($subform, $video->getGuid());
	    }
		
		// Check post/form
	    if( !$this->getRequest()->isPost() ) {
	          return;
	    }
	        
	    $post = $this->getRequest()->getPost();
	    if(!$form->isValid($post))
	      return;
		$cover = $this->_getParam('cover');
	    // Process
	     foreach( $paginator as $video )
	      {
	        $subform = $form->getSubForm($video->getGuid());
	        $subValues = $subform->getValues();
	        $subValues = $subValues[$video->getGuid()];
	        unset($subValues['video_id']);
	
	        if( isset($cover) && $cover == $video->video_id) {
	          $listing->video_id = $video->video_id;
	          $listing->save();
	        }
	
	        if( isset($subValues['delete']) && $subValues['delete'] == '1' )
	        {
	          if( $listing->video_id == $video->video_id ){
	            $listing->video_id = 0;
	            $listing->save();
	          }
			 $select =  $tableMapping -> select() -> where('listing_id = ?', $listing->getIdentity()) -> where('item_id=?',$video->video_id) -> limit(1);
	          $mapping_row = $tableMapping->fetchRow($select);
			  $mapping_row->delete();
	          $video->delete();
	        }
	        else
	        {
	          $video->setFromArray($subValues);
	          $video->save();
	        }
	      }
	  return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'ynlistings_general', true);
	}

		
	public function suggestAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		//Checking Ynvideo Plugin - View privacy
		$video_enable = Engine_Api::_() -> ynlistings() -> checkYouNetPlugin('video');
		$ynvideo_enable = Engine_Api::_() -> ynlistings() -> checkYouNetPlugin('ynvideo');
		if (!$video_enable && !$ynvideo_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$table = Engine_Api::_() -> getItemTable('video');
		$select = $table -> select() ->order('title ASC');
	    if( $this -> _getParam('text'))
	    {
	      $text = $this -> _getParam('text');
	      $select->where('title LIKE ?', '%'.$text.'%');
		  $select->where('owner_id = ?', $viewer->getIdentity());
	    }
		$tags =  $table->fetchAll($select);
		$data = array();
		foreach ($tags as $tag) {
			$data[] = array('id' => $tag -> video_id, 'label' => $tag -> title);
		}
		if ($this -> _getParam('sendNow', true)) {
			return $this -> _helper -> json($data);
		} else {
			$this -> _helper -> viewRenderer -> setNoRender(true);
			$data = Zend_Json::encode($data);
			$this -> getResponse() -> setBody($data);
		}
	}
}
?>
