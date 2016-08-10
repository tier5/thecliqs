<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 08.02.13 10:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Photoviewer_IndexController extends Core_Controller_Action_Standard
{


  /**
   * The plugin support these Photo modules
   * - Album by SocialEngine
   * - Advanced Albums by Modules2Buy
   * - Page's Albums by Hire-experts
   * - Advanced Albums by SocialEngineAddons
   * - Photos in Advanced Groups by Modules2Buy
   */

  protected $_supportItems = array(
    'album_photo',
    'advalbum_photo',
    'pagealbumphoto',
    'advgroup_photo'
  );

  protected function _checkPhoto($photo)
  {
    if (!$photo){
      return false;
    }
    if (!($photo instanceof Core_Model_Item_Abstract)){
      return false;
    }
    if (!in_array($photo->getType(), $this->_supportItems)){
      return false;
    }
    return true;
  }


  public function getPhoto($photo_id, $isPage)
  {
    $photo = null;

    if ($isPage){

      // Page Album
      if (Engine_Api::_()->hasItemType('pagealbumphoto')){
        try {
          $photo = Engine_Api::_()->getItem('pagealbumphoto', $photo_id);
        } catch (Exception $e){

        }
      }

    } else {

      // Album
      if (Engine_Api::_()->hasItemType('album_photo')){
        try {
          $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
        } catch (Exception $e){

        }
      }
      // Advanced Albums by m2b
      if (!$photo && Engine_Api::_()->hasItemType('advalbum_photo')){
        try {
          $photo = Engine_Api::_()->getItem('advalbum_photo', $photo_id);
        } catch (Exception $e){

        }
      }
      // Photos in Advanced Groups by m2b
      if (!$photo && Engine_Api::_()->hasItemType('advgroup_photo')){
        try {
          $photo = Engine_Api::_()->getItem('advgroup_photo', $photo_id);
        } catch (Exception $e){

        }
      }

    }

    return $photo;

  }

  public function getAlbumByPhoto($subject)
  {
    $album_id = 0;
    $album = null;
    if (isset($subject['album_id'])){
      $album_id = $subject->album_id;
    }

    // Get Album
    if ($subject->getType() == 'album_photo') {
      $album = Engine_Api::_()->getItem('album', $album_id);
    } else if ($subject->getType() == 'advalbum_photo') {
      $album = Engine_Api::_()->getItem('advalbum_album', $album_id);
    } else if ($subject->getType() == 'pagealbumphoto') {
      $album = $subject->getCollection();
    } else if ($subject->getType() == 'advgroup_photo') {
      $album = $subject->getCollection();
    } else {
      throw new Exception('Album not found');
    }
    return $album;
  }

  public function indexAction()
  {
    $subject = $this->getPhoto($this->_getParam('photo_id'), $this->_getParam('isPage'));
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check Photo
    if (!$this->_checkPhoto($subject)){
      $this->view->message = 'Invalid Photo';
      $this->view->status = false;
      return ;
    }

    // Check privacy
    if ($subject->getType() == 'pagealbumphoto') {
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')) {
      $this->view->message = 'The photo is private';
      $this->view->status = false;
      return;
    }

    $table = $subject->getTable();


    // ID key of photo (must return photo_id, pagealbumphoto_id etc)
    $matches = $table->info('primary');
    $primary = array_pop($matches);


    // get album
    $album = $this->getAlbumByPhoto($subject);
    $owner = $album->getOwner();


    // ID key of album (must return album_id, pagealbump_id etc)
    $matches = $album->getTable()->info('primary');
    $album_primary = array_pop($matches);



    // get all photos by album id

    $select = $table->select();

    if (isset($subject->{$album_primary})){
      $select->where(''.$album_primary.' = ?', $subject->{$album_primary});
    } else if (isset($subject->collection_id)){
      $select->where('collection_id = ?', $subject->collection_id);
    }

    if (isset($subject['order'])){
      $select->order('order ASC');
    }
    $select->order($primary.' ASC');




    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(500);

    $photos = array();
    foreach ($paginator as $item){

      $owner = $item->getOwner();

      $photos[] = array(
        'photo_id' => $item->getIdentity(),
        'guid' => $item->getGuid(),
        'thumb' => $item->getPhotoUrl('thumb.normal'),
        'src' => $item->getPhotoUrl(),
        'active' => ($item->getIdentity() == $subject->getIdentity()),
        'title' => $item->getTitle(),
        'description' => $item->getTitle()
      );
    }


    $this->view->photos = $photos;
    $this->view->count = $paginator->getTotalItemCount();
    $this->view->album_title = $album->getTitle();
    $this->view->album_href = $album->getHref();
    $this->view->owner_title = $owner->getTitle();
    $this->view->owner_href = $owner->getHref();

  }

  public function commentsAction()
  {
    $subject = $this->getPhoto($this->_getParam('photo_id'), $this->_getParam('isPage'));
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->isPage = (int)$this->_getParam('isPage');

    // Check Photo
    if (!$this->_checkPhoto($subject)){
      $this->view->message = 'Invalid Photo';
      $this->view->status = false;
      return ;
    }

    // Check privacy
    if ($subject->getType() == 'pagealbumphoto') {
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')) {
      $this->view->message = 'The photo is private';
      $this->view->status = false;
      return;
    }

    $album = $this->getAlbumByPhoto($subject);

    Engine_Api::_()->core()->setSubject($subject);

    $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
    $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);
    $this->view->photo = $photo = $subject;

    if( !$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer) && isset($photo->view_count)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

    // Get tags
    $tags = array();
    foreach( $photo->tags()->getTagMaps() as $tagmap ) {
      $tags[] = array_merge($tagmap->toArray(), array(
        'id' => $tagmap->getIdentity(),
        'text' => $tagmap->getTitle(),
        'href' => $tagmap->getHref(),
        'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
      ));
    }
    $this->view->tags = $tags;



    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;



  }

  public function downloadAction()
  {
    $subject = $this->getPhoto($this->_getParam('photo_id'), $this->_getParam('isPage'));
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check Photo
    if (!$this->_checkPhoto($subject)){
      $this->view->message = 'Invalid Photo';
      $this->view->status = false;
      return ;
    }

    // Check privacy
    if ($subject->getType() == 'pagealbumphoto') {
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')) {
      $this->view->message = 'The photo is private';
      $this->view->status = false;
      return;
    }

    $album = $this->getAlbumByPhoto($subject);

    Engine_Api::_()->core()->setSubject($subject);

    $file = Engine_Api::_()->getItem('storage_file', $subject->file_id);
    if (!$file){
      throw new Exception('File is not available');
    }
    $file_patch = APPLICATION_PATH .'/'. $file->storage_path;

    if (file_exists($file_patch) && is_readable($file_patch)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file_patch));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_patch));
        ob_clean();
        flush();
        readfile($file_patch);
        exit;
    } else {
      throw new Exception('File is not exists');
    }

  }




}