<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminIndexController.php 03.02.12 12:17 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hegift_admin_main');

    $this->view->menu = $this->_getParam('action');

    if( $gift_type = $this->getRequest()->getQuery('gift_type', false) ) {
      return $this->_forward('upload-'.$gift_type, null, null, array('format' => 'json'));
    }
  }

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hegift_admin_main', array(), 'hegift_admin_main_index');
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     */

    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $this->view->formFilter = $formFilter = new Hegift_Form_Admin_Filter();

    $page = $this->_getParam('page', 1);

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'gift_id',
      'order_direction' => 'DESC',
      'page' => $page
    ), $values);

    $this->view->assign($values);
    $valuesCopy = array_filter($values);

    $this->view->paginator = $paginator = $table->getGifts($values);
    $this->view->formValues = $valuesCopy;
  }

  public function createAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $gift Hegift_Model_Gift
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $this->view->form = $form = new Hegift_Form_Admin_Create();
    $form->getDecorator('Description')->setOption('escape', false);

    if (!$this->getRequest()->isPost()) {
      return 0;
    }

    $values = $this->getRequest()->getPost();

    if (!$form->isValid($values)) {
      $values['starttime'] = '';
      $values['endtime'] = '';
      $form->populate($values);
      return 0;
    }

    if ($values['type'] == 1) {
      $gift_type = 'photo';
    } elseif ($values['type'] == 2) {
      $gift_type = 'audio';
    } elseif ($values['type'] == 3) {
      $gift_type = 'video';
    }

    if (empty($values['fancyupload'.$gift_type])) {
      $values['starttime'] = '';
      $values['endtime'] = '';
      $form->populate($values);
      $form->addError('You have not upload a(an) '.$gift_type);
      return 0;
    }

    $dates = $form->getValues();
    if (
      ($dates['starttime'] == '0000-00-00' && $dates['endtime'] != '0000-00-00') ||
      ($dates['starttime'] != '0000-00-00' && $dates['endtime'] == '0000-00-00')
    ) {
      $values['starttime'] = '';
      $values['endtime'] = '';
      $form->populate($values);
      $form->addError('Or fill out the two dates, or leave both empty');
      return 0;
    }

    if ($dates['starttime'] != '0000-00-00' && $dates['endtime'] != '0000-00-00') {
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $start = strtotime($dates['starttime']);
      $end = strtotime($dates['endtime']);

      if ($start > $end) {
        $values['starttime'] = '';
        $values['endtime'] = '';
        $form->populate($values);
        $form->addError('End date is less than the start date');
        return 0;
      }

      date_default_timezone_set($oldTz);
      $values['starttime'] = date('Y-m-d H:i:s', $start);
      $values['endtime'] = date('Y-m-d H:i:s', $end);
    } else {
      unset($values['starttime']);
      unset($values['endtime']);
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    $gift = $table->createRow();

    try {
      if ($values['limit'] == 0) {
        unset($values['limit']);
        unset($values['amount']);
      } else {
        unset($values['limit']);
      }
      $gift->setFromArray($values);
      $gift->creation_date = new Zend_Db_Expr('NOW()');
      $gift->save();

      if ($gift_type == 'photo') {
        $gift->setPhoto((int)$values['fancyuploadphoto']);
      } elseif ($gift_type == 'audio') {
        $gift->setAudio((int)$values['fancyuploadaudio']);
      } elseif ($gift_type == 'video') {
        $gift->setVideo((int)$values['fancyuploadvideo']);
      }

      $db->commit();
      $form->addNotice('Gift successfully created');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirector(array('action' => 'manage-photo', 'gift_id' => $gift->getIdentity()));
  }

  public function editAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $gift Hegift_Model_Gift
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $gift_id = $this->_getParam('gift_id', 0);
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $gift_id);
    $this->view->form = $form = new Hegift_Form_Admin_Edit();
    $values = array_merge($gift->toArray(), array('limit' => ($gift->amount) ? 1 : 0));

    if (!$this->getRequest()->isPost()) {
      $form->populate($values);
      return ;
    }

    if (!$form->isValid($values = $this->getRequest()->getPost())) {
      $values['starttime'] = '';
      $values['endtime'] = '';
      $form->populate($values);
      return ;
    }

    $dates = $form->getValues();
    if (
      ($dates['starttime'] == '0000-00-00' && $dates['endtime'] != '0000-00-00') ||
      ($dates['starttime'] != '0000-00-00' && $dates['endtime'] == '0000-00-00')
    ) {
      $values['starttime'] = '';
      $values['endtime'] = '';
      $form->populate($values);
      $form->addError('Or fill out the two dates, or leave both empty');
      return 0;
    }

    if ($dates['starttime'] != '0000-00-00' && $dates['endtime'] != '0000-00-00') {
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $start = strtotime($dates['starttime']);
      $end = strtotime($dates['endtime']);

      if ($start > $end) {
        $values['starttime'] = '';
        $values['endtime'] = '';
        $form->populate($values);
        $form->addError('End date is less than the start date');
        return 0;
      }

      date_default_timezone_set($oldTz);
      $values['starttime'] = date('Y-m-d H:i:s', $start);
      $values['endtime'] = date('Y-m-d H:i:s', $end);
    } else {
      unset($values['starttime']);
      unset($values['endtime']);
    }

    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      if ($values['limit'] == 0) {
        unset($values['limit']);
        unset($values['amount']);
        $gift->amount = null;
      } else {
        unset($values['limit']);
      }
      $gift->setFromArray($values);
      $gift->save();

      $db->commit();
      $form->addNotice('Changes successfully saved');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function enableAction()
  {
    $gift_id = $this->_getParam('gift_id', 0);
    $value = $this->_getParam('value', 1);
    $gift = Engine_Api::_()->getItem('gift', $gift_id);

    if( !$gift || _ENGINE_ADMIN_NEUTER) {
      $this->redirector();
    } else {
      $gift->enabled = $value;
      $gift->save();
      $this->redirector();
    }
  }

  public function managePhotoAction()
  {
    /**
     * @var $gift Hegift_Model_Gift
     */
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    if (!$gift) {
      $this->redirector();
    }

    Engine_Api::_()->core()->setSubject($gift);

    $this->view->form = $form = new Hegift_Form_Admin_Gift_Photo();
    if (!$this->getRequest()->isPost()) {
      return ;
    }

    // Uploading a new photo
    if( $form->Filedata->getValue() !== null ) {

      if( !$form->isValid($this->getRequest()->getPost()) ) {
        return;
      }

      $db = $gift->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $fileElement = $form->Filedata;

        $gift->setPhoto($fileElement);

        // Insert activity
        $db->commit();
      }

      // If an exception occurred within the image adapter, it's probably an invalid image
      catch( Engine_Image_Adapter_Exception $e )
      {
        $db->rollBack();
        $form->addError('The uploaded file is not supported or is corrupt.');
      }

      // Otherwise it's probably a problem with the database or the storage system (just throw it)
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }

    // Resizing a photo
    else if( $this->getRequest()->getParam('coordinates', '') !== '' ) {
      $storage = Engine_Api::_()->storage();

      $iProfile = $storage->get($gift->photo_id, 'thumb.profile');
      $iSquare = $storage->get($gift->photo_id, 'thumb.icon');

      // Read into tmp file
      $pName = $iProfile->getStorageService()->temporary($iProfile);
      $iName = dirname($pName) . '/nis_' . basename($pName);
      list($x, $y, $w, $h) = explode(':', $this->getRequest()->getParam('coordinates', ''));

      $image = Engine_Image::factory();
      $image->open($pName)
        ->resample($x+.1, $y+.1, $w-.1, $h-.1, 48, 48)
        ->write($iName)
        ->destroy();

      $iSquare->store($iName);

      // Remove temp files
      @unlink($iName);
    }
  }

  public function manageAudioAction()
  {
    $this->view->storage = Engine_Api::_()->storage();
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));

    if (!$gift || $gift->type != 2) {
      $this->redirector();
    }

    $this->view->form = $form = new Hegift_Form_Admin_Gift_Audio();
    $this->view->exist = true;

    if (!$this->view->storage->get($gift->file_id)) {
      $this->view->exist = false;
      $form->addError('HEGIFT_There is no audio.');
    }

    if (!$this->getRequest()->isPost()) {
      return 0;
    }

    $values = $this->getRequest()->getPost();
    if( !$form->isValid($values) ) {
      return 0;
    }

    if (empty($values['fancyuploadaudio'])) {
      $form->populate($values);
      $form->addError('You have not upload an audio');
      return 0;
    }

    $db = $gift->getTable()->getAdapter();
    $db->beginTransaction();
    $file_id = $gift->file_id;
    try {
      $gift->setAudio($values['fancyuploadaudio']);
      $db->commit();
      $form->addNotice('Audio Gift successfully changed');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if ($file_id && (null != ($file = Engine_Api::_()->getItem('storage_file', $file_id)))) {
      $file->remove();
    }
  }

  public function manageVideoAction()
  {
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    if (!$gift || $gift->type != 3) {
      $this->redirector();
    }

    $this->view->form = $form = new Hegift_Form_Admin_Gift_Video();

    $file_id = $gift->file_id;

    if (!empty($file_id)) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if ($storage_file) {
        $this->view->video_location = $storage_file->map();
      } else {
        $form->addError('HEGIFT_There is no video.');
      }
    }

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    $values = $this->getRequest()->getPost();

    if( !$form->isValid($values) ) {
      return;
    }

    $gift->status = 0;


    $db = $gift->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $gift->setVideo($values['fancyuploadvideo']);
      $db->commit();
      $form->addNotice('Video Gift successfully changed');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if ($file_id && (null != ($file = Engine_Api::_()->getItem('storage_file', $file_id)))) {
      $file->remove();
    }
  }

  public function duplicateAction()
  {
    /**
     * @var $duplicated Hegift_Model_Gift
     */
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    if (!$gift || $gift->type != 1) {
      $this->redirector();
    }

    $values = array(
      'title' => $gift->title,
      'type' => $gift->type,
      'credits' => $gift->credits,
      'amount' => $gift->amount,
      'category_id' => $gift->category_id,
      'starttime' => $gift->starttime,
      'endtime' => $gift->endtime
    );

    $this->view->form = $form = new Hegift_Form_Admin_Duplicate();
    if (!$this->getRequest()->isPost()) {
      $form->populate($values);
      return ;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $form->populate($values);
      return ;
    }

    // Uploading a new photo
    if( $form->photo->getValue() !== null ) {
      $db = $gift->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $duplicated = $gift->getTable()->createRow();
        $duplicated->setFromArray($values);
        $duplicated->save();
        $fileElement = $form->photo;

        $duplicated->setPhoto($fileElement);

        // Insert activity
        $db->commit();
      }

      // If an exception occurred within the image adapter, it's probably an invalid image
      catch( Engine_Image_Adapter_Exception $e ) {
        $db->rollBack();
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
      }

      // Otherwise it's probably a problem with the database or the storage system (just throw it)
      catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    }

    $this->redirector(array('action' => 'edit', 'gift_id' => $duplicated->getIdentity()));
  }

  public function uploadPhotoAction()
  {
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');

      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    /**
     * @var $api Hegift_Api_Core
     */
    try {
      $api = Engine_Api::_()->hegift();
      $photo_id = $api->setPhoto($_FILES['Filedata']);
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;
      return;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function uploadAudioAction()
  {
    // only members can upload audio
    if( !$this->_helper->requireUser()->checkRequire() ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Max file size limit exceeded or session expired.');
      return;
    }

    // Check method
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid request method');
      return;
    }

    // Check file
    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) || empty($_FILES['Filedata']) ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('No file');
      return;
    }

    /**
     * @var $api Hegift_Api_Core
     */
    try {
      $api = Engine_Api::_()->hegift();
      $file = $api->setAudio($_FILES['Filedata']);
      $this->view->status   = true;
      $this->view->file     = $file;
      $this->view->file_id  = $file->getIdentity();
      return;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function uploadVideoAction()
  {
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload').print_r($_FILES, true);
      return;
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if( in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    try
    {
      $video = Engine_Api::_()->hegift()->setVideo($_FILES['Filedata']);
      $this->view->status   = true;
      $this->view->file_id = $video->file_id;
    }

    catch( Exception $e )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.').$e;
      // throw $e;
      return;
    }
  }

  public function removeAction()
  {
    $file_id = $this->_getParam('file_id', 0);
    if ($file_id && (null != ($file = Engine_Api::_()->getItem('storage_file', $file_id)))) {
      $file->remove();
    }
  }

  public function changeAction()
  {
    /**
     * @var $gift Hegift_Model_Gift
     */
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    if (!$gift) {
      $this->redirector();
    }

    $this->view->form = $form = new Hegift_Form_Admin_Change();

    if (!$this->getRequest()->isPost()) {
      $form->populate(array('type' => $gift->type));
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $form->populate(array('type' => $gift->type));
      return ;
    }

    $old_type = $gift->type;
    $new_type = $form->getValue('type');

    if (!$new_type) {
      $form->populate(array('type' => $gift->type));
      $form->addError('Invalid type');
      return ;
    }

    if ($new_type == $old_type) {
      $form->populate(array('type' => $gift->type));
      $form->addNotice('Sorry, you chose same type, type is not changed');
      return ;
    }

    //deleting file if type was video or audio

    if ($gift->file_id && $gift->type != 1) {
      $file = Engine_Api::_()->getItem('storage_file', $gift->file_id);
      if ($file) {
        $file->remove();
      }
    }

    $gift->type = $new_type;
    $gift->save();

    $this->redirector(array('action' => 'manage-'.$gift->getTypeName(), 'gift_id' => $gift->getIdentity()));
  }

  protected function redirector($params = array())
  {
    $params = array_merge(array(
      'module' => 'hegift',
      'controller' => 'index',
      'action' => 'index'
    ), $params);

    $this->_redirectCustom($this->view->url($params, 'admin_default', true));
  }
}