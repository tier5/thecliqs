<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       06.08.12
 * @time       16:23
 */
class Donation_PhotoController extends Core_Controller_Action_Standard
{

  public function init()
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int)$this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('donation_photo', $photo_id))
      ) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 !== ($donation_id = (int)$this->_getParam('donation_id')) &&
        null !== ($donation = Engine_Api::_()->getItem('donation', $donation_id))
      ) {
        Engine_Api::_()->core()->setSubject($donation);
      }
    }

  }

  public function listAction()
  {
    /**
     * @var $album Group_Model_Album
     */
    $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $donation->getSingletonAlbum();

    if( !$this->_helper->requireAuth()->setAuthParams($donation, null, 'view')->isValid() ) {
      return;
    }

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $donation->authorization()->isAllowed(null, 'photo');
  }

  public function uploadphotosAction()
  {
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');

      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'donation')->getAdapter();
    $db->beginTransaction();

    try {
      /**
       * @var $viewer     User_Model_User
       * @var $photoTable Donation_Model_DbTable_Donations
       * @var $photo      Donation_Model_Photo
       */
      $viewer = Engine_Api::_()->user()->getViewer();
      $photoTable = Engine_Api::_()->getDbtable('photos', 'donation');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'user_id' => $viewer->getIdentity()
      ));
      $photo->save();

      $photo->setPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->photo_id;

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }

  public function removephotoAction()
  {
    $photo_id = (int)$this->_getParam('photo_id');
    if ($photo_id && !_ENGINE_ADMIN_NEUTER) {
      $photo = Engine_Api::_()->getItem('donation', $photo_id);
      $db = $photo->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $storage = Engine_Api::_()->getItemTable('storage_file');
        $select = $storage->select()
          ->where('parent_file_id = ?', $photo->file_id);

        if (($file = $storage->fetchRow($select)) !== null) {
          $file->delete();
        }
        Engine_Api::_()->getApi('core', 'donation')->deleteFile($photo->file_id);
        $photo->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->donation = $donation = $photo->getDonation();
    $this->view->canEdit = $photo->canEdit(Engine_Api::_()->user()->getViewer());


    $page = $donation->getPage();

    if($page && $page->getDonationPrivacy($donation->type)){
      $this->view->subject = $page;
      $api = Engine_Api::_()->getApi('page', 'donation');
      $this->view->navigation = $api->getNavigation($page);
    }

  }

  public function editAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();

//    if( !$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid() ) {
//      return;
//    }

    $this->view->form = $form = new Donation_Form_Photo_Edit();

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
    $db = Engine_Api::_()->getDbtable('photos', 'donation')->getAdapter();
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
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }

  public function deleteAction()
  {

    $photo = Engine_Api::_()->core()->getSubject();
    $donation = $photo->getParent('donation');

//    if( !$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid() ) {
//      return;
//    }

    $this->view->form = $form = new Donation_Form_Photo_Delete();

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
    $db = Engine_Api::_()->getDbtable('photos', 'donation')->getAdapter();
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

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
      'layout' => 'default-simple',
      'parentRedirect' => $donation->getHref(),
      'closeSmoothbox' => true,
    ));
  }
}
