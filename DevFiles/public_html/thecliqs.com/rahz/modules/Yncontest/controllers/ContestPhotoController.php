<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: PhotoController.php 7244 2011-07-2- 01:49:53Z john $
 * @author     Luan Nguyen
 * @editor	   MinhNC
 */
class Yncontest_ContestPhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
  	if (!$this -> _helper -> requireUser() -> isValid())
		return; 
	Zend_Registry::set('active_menu','yncontest_main_mycontests');
  	$viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('contestId')) &&
          null !== ($photo = Engine_Api::_()->getItem('yncontest_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($contest_id = (int) $this->_getParam('contestId')) &&
          null !== ($contest = Engine_Api::_()->getItem('yncontest_contest', $contest_id)) )
      {
        
      	Engine_Api::_()->core()->setSubject($contest);
      }
  	}
  }
  public function listPhotoAction()
	{
	  	$contest = Engine_Api::_()->core()->getSubject();
	  	if (!is_object($contest)) {
	  		return;
	  	}
	  	$viewer = Engine_Api::_()->user()->getViewer();
	  	
	  	//check contest owner
	  	if (($contest->user_id == null) || $contest->user_id != $viewer->getIdentity()) {
	  		return $this->_forward('requireauth', 'error', 'core');
	  	}
	  	$this->view->contest_id = $contest->contest_id;
	  	$this->view->photo_id = $contest->photo_id;
	  	$this->view->form = $form = new Yncontest_Form_Photo_Manage();
	  	
		$this->view->album = $album = $contest->getSingletonAlbum();

	    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
	    
	    
	    $paginator->setCurrentPageNumber($this->_getParam('page'));
	    $paginator->setItemCountPerPage(100);

	    foreach( $paginator as $photo )
	    {
	      $subform = new Yncontest_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
	      $subform->removeElement('label');
	      $subform->removeElement('photo_id');
	      if($photo->file_id == $contest->photo_id){
	      	$subform->removeElement('delete');
	      	$subform->removeElement('slideshow');
	      }
	      $subform->populate($photo->toArray());
	      $form->addSubForm($subform, $photo->getGuid());
	      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
	    }
	    if( !$this->getRequest()->isPost() )
	    {
	      return;
	    }
	
	    if( !$form->isValid($this->getRequest()->getPost()) )
	    {
	      return;
	    }
	
	    // Process
	    $table = Engine_Api::_()->getDbTable('photos','yncontest');
	    $db = $table->getAdapter();
	    $db->beginTransaction();
	
	    try
	    {
	      $values = $form->getValues();
	     
	      $cover = $values['cover'];
	      // Process
	      $count = 0;
	      
	      foreach( $paginator as $photo )
	      {
	      	$subform = $form->getSubForm($photo->getGuid());
	        $subValues = $subform->getValues();
	        $subValues = $subValues[$photo->getGuid()];
	       
	        unset($subValues['photo_id']);
			
	        if( isset($cover) && $cover == $photo->photo_id) {
	       	  $contest->photo_id = $photo->file_id;
	       	  $photo->slideshow = 0;
	       	  $photo->save();
	          $contest->save();
	        }
	
	        if( isset($subValues['delete']) && $subValues['delete'] == '1' )
	        {
	          if( $contest->photo_id == $photo->file_id ){
	            $contest->photo_id = 0;
	            $contest->save();
	          }
	          $photo->delete();
	        }
	        else
	        {
	          $photo->setFromArray($subValues);
	          $photo->save();
	        }
	      }
	
	      $db->commit();
	    }
	
	    catch( Exception $e )
	    {
	      $db->rollBack();
	      throw $e;
	    }
	     return $this->_helper->redirector->gotoRoute(array("action"=>"index"),'yncontest_mycontest',true);
	  	
	}
  public function uploadAction()
	{
		$contest = Engine_Api::_()->core()->getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		if($contest->user_id == $viewer->getIdentity())
		{
			$this->view->canUpload = true;
		}
		$album = $contest->getSingletonAlbum();
		$this->view->form = $form = new Yncontest_Form_Photo_Upload();
		$form -> contest_id -> setValue($contest -> getIdentity());
		if( !$this->getRequest()->isPost() )
		{
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) )
		{
			return;
		}
		// Process
		$table = Engine_Api::_() -> getItemTable('yncontest_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$params = array(
				'contest_id' => $contest -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
				'collection_id' => $album -> getIdentity(),
				'album_id' => $album -> getIdentity()
			);
			$values = $form -> getValues();
			$arr_photo_id = explode(' ', trim($values['html5uploadfileids']));
			if ($arr_photo_id)
			{
				$values['file'] = $arr_photo_id;
			}
			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id)
			{
				$photo = Engine_Api::_() -> getItem("yncontest_photo", $photo_id);
				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				$photo -> collection_id = $album -> album_id;
				$photo -> album_id = $album -> album_id;
				$photo -> save();
			}
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}	
   		return $this->_helper->redirector->gotoRoute(array('action' => 'list-photo','contestId' => $contest->getIdentity()), 'yncontest_photo', true);
	}
  public function uploadPhotoAction()
  {
  	$this -> _helper -> layout() -> disableLayout();
	$this -> _helper -> viewRenderer -> setNoRender(true);    
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    $contest = Engine_Api::_() -> getItem('yncontest_contest', $_POST['contest_id']);
    // @todo check auth
    //$deal

    if (empty($_FILES['files']))
	{
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('No file');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
					'status' => $status,
					'name' => $error
				)))));
	}
	$name = $_FILES['files']['name'][0];
	$type = explode('/', $_FILES['files']['type'][0]);
	if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
	{
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
					'status' => $status,
					'error' => $error,
					'name' => $name
				)))));
	}


    $db = Engine_Api::_()->getItemTable('yncontest_photo')->getAdapter();
    $db->beginTransaction();
    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $contest->getSingletonAlbum();
      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),
        'contest_id' => $contest->getIdentity(),
        'user_id' => $viewer->getIdentity(), 
      	'slideshow' => 1,
      );
      $temp_file = array(
			'type' => $_FILES['files']['type'][0],
			'tmp_name' => $_FILES['files']['tmp_name'][0],
			'name' => $_FILES['files']['name'][0]
		);
      $photo = Engine_Api::_()->getApi('core','yncontest')->createPhoto($params, $temp_file);
	  $photo_id = $photo -> getIdentity();
      if(!$contest->photo_id)
      {
        $contest->photo_id = $photo_id;
        $contest->save();
      }
	 $db->commit();
      $status = true;
		$name = $_FILES['files']['name'][0];
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
					'status' => $status,
					'name' => $name,
					'photo_id' => $photo_id
				)))));
    }
    catch( Exception $e )
    {
      $db->rollBack();
      $status = false;
		$name = $_FILES['files']['name'][0];
		$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
					'status' => $status,
					'error' => $error,
					'name' => $name
				)))));
    }
  }
  public function removeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $photo_id= (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('yncontest_photo', $photo_id);

    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
}
