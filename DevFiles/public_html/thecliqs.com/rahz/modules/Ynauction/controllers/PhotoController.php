<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: PhotoController.php 7244 2011-07-2- 01:49:53Z john $
 * @author     Minh Nguyen
 */
class Ynauction_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  { 
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('ynauction_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($auction_id = (int) $this->_getParam('auction')) &&
          null !== ($auction = Engine_Api::_()->getItem('ynauction_product', $auction_id)) )
      {
        Engine_Api::_()->core()->setSubject($auction);
      }
  }
  }
  public function listAction()
  {
    $this->view->auction = $auction = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $auction->getSingletonAlbum();

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $group->authorization()->isAllowed(null, 'photo.upload');
  }

  public function viewAction()
  {
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->group = $group = $photo->getGroup();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'photo.edit');
  }

  public function uploadAction()
  {
  	$this -> _helper -> content -> setEnabled();
    $auction = Engine_Api::_()->core()->getSubject();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $auction = Engine_Api::_()->getItem('ynauction_product', (int) $auction->getIdentity());
    if($auction->user_id == $viewer->getIdentity())
    {
        $this->view->canUpload = true;
    }
    $album = $auction->getSingletonAlbum();
    $this->view->auction = $auction->product_id;
    $this->view->form = $form = new Ynauction_Form_Photo_Upload();
    $form -> auction_id -> setValue($auction -> getIdentity());
	
	
	
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('ynauction_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {      
	  
	  $arr_photo_id = array();			
	  $values = $form -> getValues();
	  $arr_photo_id = explode(' ', trim($values['html5uploadfileids']));
		
	  if ($arr_photo_id)
	  {
		  $values['file'] = $arr_photo_id;
	  }
	
	  $params = array('auction' => $auction->getIdentity(), 'user_id' => $viewer->getIdentity(), );
	  

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $auction, 'ynauction_photo_upload', null, array('count' => count($values['file'])));

      // Do other stuff
      $count = 0;
      foreach( $values['file'] as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("ynauction_photo", $photo_id);

        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($auction->photo_id == 0) {
          $auction->photo_id = $photo->file_id;
          $auction->save();
        }

        if( $action instanceof Activity_Model_Action && $count < 8 )
        {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    if($auction->display_home == 0)
        return $this->_helper->redirector->gotoRoute(array('action' => 'display','auction'=>$auction->product_id), 'ynauction_general', true);
    else
        return $this->_helper->redirector->gotoRoute(array('action' => 'manageauction'), 'ynauction_general', true);
  }

  public function uploadPhotoAction()
  {
  	$this -> _helper -> layout() -> disableLayout();
	$this -> _helper -> viewRenderer -> setNoRender(true);
	if (!$this -> _helper -> requireUser() -> checkRequire()) {
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
	}
	if (!$this -> getRequest() -> isPost()) {
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
	}
	
	$auction = Engine_Api::_() -> getItem('ynauction_product', (int)$_REQUEST['auction_id']);	
    
    if (empty($_FILES['files'])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $error)))));
		}

    $name = $_FILES['files']['name'][0];
	$type = explode('/', $_FILES['files']['type'][0]);
	if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
	}

    // @todo check auth
    //$auction    

    $db = Engine_Api::_()->getDbtable('photos', 'ynauction')->getAdapter();
    $db->beginTransaction();
    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $auction->getSingletonAlbum();
      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),
        'product_id' => $auction->product_id,
        'user_id' => $viewer->getIdentity(),
      );
	  $temp_file = array('type' => $_FILES['files']['type'][0], 'tmp_name' => $_FILES['files']['tmp_name'][0], 'name' => $_FILES['files']['name'][0]);
	  
	  
      $photo_id = Engine_Api::_()->ynauction()->createPhoto($params, $temp_file)->photo_id;
      if(!$auction->photo_id){
        $auction->photo_id = $photo_id;
        $auction->save();
      }
      $db->commit();
	  $status = true;
	  $name = $_FILES['files']['name'][0];

	  return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $name, 'photo_id' => $photo_id)))));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      	$status = false;
		$name = $_FILES['files']['name'][0];
		$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
    }
  }

	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('ynauction_photo', $this -> getRequest() -> getParam('photo_id'));
		
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynauction') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}


  public function editAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();

    $this->view->form = $form = new YnAuction_Form_Photo_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'ynauction')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array('Changes saved'),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }

  public function removeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $photo_id= (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('ynauction_photo', $photo_id);

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
