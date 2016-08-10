<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Page.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_Page extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';

  protected $_owner_type = 'user';

  protected $_content_info;

  public $owner;

  protected $_searchColumns = array('search', 'title', 'description');

  protected $_modifiedTriggers = array();

  protected $_packageEnabled;

  protected $_isStore;

  protected $_isDonation;

  protected $_isNew;

  public function init()
  {
    if (!empty($this->user_id)) {
      $this->owner = Engine_Api::_()->getItem('user', $this->user_id);
    }
    if (!empty($this->package_id)) {
      $this->_isNew = (boolean) ( $this->package_id )? false:true;
    }
    $this->_packageEnabled = (bool) Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.package.enabled', 0);
  }

  public function isNew()
  {
    return $this->_isNew;
  }

  public function isStore()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('store')) {
      $this->_isStore = false;
      return false;
    }

    /**
     * @var $select Zend_Db_Table_Select
     * @var $table Page_Model_DbTable_Pages
     * @var $api Store_Api_Page
     * @db Engine_Db_Table;
     */
    $api = Engine_Api::_()->getApi('page', 'store');
    $table = $this->getTable();
    $db = Engine_Db_Table::getDefaultAdapter();

    $select = $db
      ->select()
      ->from($table->info('name'), array($table->info('name').'.page_id'));

    $select = $api->setStoreIntegrity($select, false);
    $select->where($table->info('name') . '.page_id = ?', $this->page_id);

    if ( $db->fetchOne($select) ){
      $this->_isStore = true;
    } else {
      $this->_isStore = false;
    }

    return $this->_isStore;
  }

  public function isDonation()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('donation')) {
      $this->_isDonation = false;
      return false;
    }

    /**
     * @var $contentTbl Page_Model_DbTable_Content
     */
    $contentTbl = Engine_Api::_()->getDbTable('content', 'page');
    $select = $contentTbl->select()->where('page_id = ?', $this->getIdentity())->where("name = 'donation.page-profile-donations'");

    $this->_isDonation = $contentTbl->fetchAll($select)->count();

    return $this->_isDonation;
  }

  public function isPackageEnabled()
  {
    return (bool) $this->_packageEnabled;
  }

  public function isAllowLayout()
  {
    if ( $this->isPackageEnabled() ) {

      if (null != ($package = $this->getPackage())){
        return (bool)$package->isAllowLayout();
      }

      return false;
    }

    return (bool)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', Engine_Api::_()->user()->getViewer(), 'layout_editor');
  }

  public function isAllowPagecontact()
  {
    if ( !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('pagecontact') )
      return false;

    return (bool) in_array('pagecontact', $this->getAllowedFeatures());
  }

  public function isAllowPagefaq()
  {
    if ( !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('pagefaq') )
      return false;

    return (bool) in_array('pagefaq', $this->getAllowedFeatures());
  }

  public function isAllowStore()
  {
    if ( !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('store') )
      return false;

    if ( $this->isPackageEnabled() ){
      if (null == ($package = $this->getPackage())){
        return false;
      }

      $features = $package->modules;
    } else {
      $features = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->owner, 'auth_features');
    }

    if($features == '')
      $features = array();

    return (bool) in_array('store', $features);
  }

  public function getStorePrivacy()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowAdmins = $auth->isAllowed($this, $this->getTeamList(), 'store_posting');
    return ($isAllowAdmins || $this->isOwner($viewer)) ? true : false;
  }

  public function isAllowStyle()
  {
    if ($this->isPackageEnabled()) {
      if (null == ($package = $this->getPackage())) {
        return false;
      }
      return (bool)$package->style;
    }

    return (bool)Engine_Api::_()->authorization()->isAllowed('page', $this->owner, 'style');
  }

  public function isAllowDonation()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('donation'))
      return false;

    if ($this->isPackageEnabled()) {
      if (null == ($package = $this->getPackage())) {
        return false;
      }

      $features = $package->modules;
    } else {
      $features = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->owner, 'auth_features');
    }

    if ($features == '')
      $features = array();
    return (bool)in_array('donation', $features);
  }

  public function getDonationPrivacy($type)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowAdmins = $auth->isAllowed($this, $viewer, $type.'_posting');
    return ($isAllowAdmins || $this->isOwner($viewer)) ? true : false;
  }

  public function allowOffers()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('offers')) {
      return false;
    }

    if ($this->isPackageEnabled()) {
      if (null == ($package = $this->getPackage())) {
        return false;
      }

      $features = $package->modules;
    } else {
      $features = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->owner, 'auth_features');
    }

    if ($features == '') {
      $features = array();
    }
    return (bool)in_array('offers', $features);
  }

  public function isOffers()
  {
    if (!$this->allowOffers()) {
      return false;
    }
    /**
     * @var $contentTbl Page_Model_DbTable_Content
     */
    $contentTbl = Engine_Api::_()->getDbTable('content', 'page');
    $select = $contentTbl->select()->where('page_id = ?', $this->getIdentity())->where("name = 'offers.profile-offers'");

    return $contentTbl->fetchAll($select)->count();
  }

  public function isAllowCols()
  {
    if ( $this->isPackageEnabled() ){
      if (null == ($package = $this->getPackage())){
        return false;
      }
      return (bool)$package->edit_columns;
    }

    return (bool)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->owner, 'edit_cols');
  }

  public function getAllowedFeatures()
  {
    if ( $this->isPackageEnabled() ){
      if (null == ($package = $this->getPackage())){
        return array();
      }

      return (array) $package->modules;
    }

    return (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->getOwner(), 'auth_features');
  }

  public function isFeatureAllowed( $feature )
  {
    return (in_array($feature, $this->getAllowedFeatures())?true:false);
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function setContentInfo($content, $content_id)
  {
    $this->_content_info = array('content' => $content, 'content_id' => $content_id);
  }

  public function getContentInfo()
  {
    return $this->_content_info;
  }

  public function getDescription($truncate = false, $strip_tags = true, $nl2br = true, $truncate_count = 200)
  {
    $description = $this->description;
    if ($strip_tags) {
      $description = strip_tags($description);
    }

    if ($truncate) {
      $description = Engine_Api::_()->getApi('core', 'hecore')->truncate($description, $truncate_count);
    }

    if ($nl2br) {
      $description = nl2br($description);
    }

    return $description;
  }

  public function isTeamMember($viewer = null)
  {
    if ($viewer === null){
      $viewer = Engine_Api::_()->user()->getViewer();
    }elseif (is_numeric($viewer)){
      $viewer = Engine_Api::_()->getItem('user', $viewer);
    }

    if (!($viewer instanceof Core_Model_Item_Abstract )){
      return false;
    }

    return ($this->membership()->isMember($viewer, true) || $this->user_id == $viewer->getIdentity());
  }

  public function isExists($page_id = null)
  {
    if ($page_id == null) {
      $page_id = $this->page_id;
    }

    return (bool)Engine_Api::_()->getItem('page', (int)$page_id);
  }

  public function isEnabled($page_id = null)
  {
    /**
     * @var $settings Core_Api_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    if (!$settings->getSetting('page.package.enabled', 0)) {
      return true;
    }

    if ($page_id == null) {
      $page_id = $this->page_id;
    }
    $page = Engine_Api::_()->getItem('page', (int)$page_id);

    return (bool) $page->enabled;
  }

  public function getAddress()
  {
    $address = array();
    if ($this->country) {
      $address[] = $this->country;
    }

    if($this->state) {
      $address[] = $this->state;
    }

    if ($this->city) {
      $address[] = $this->city;
    }

    if ($this->street) {
      $address[] = $this->street;
    }

    return implode(", ", $address);
  }

  public function getAdmins($limit = 0)
  {
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $tableName = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false);

    $prefix = $table->getTablePrefix();

    $select
      ->from($tableName)
      ->joinLeft($prefix."page_membership", $prefix."page_membership.user_id = {$tableName}.user_id", array('title'))
      ->where($prefix."page_membership.resource_id = {$this->getIdentity()}")
      ->where($prefix."page_membership.type = 'ADMIN'")
      ->limit($limit);

    return Zend_Paginator::factory($select);
  }

  public function getEmployers($limit = 0)
  {
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $tableName = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false);

    $prefix = $table->getTablePrefix();

    $select
      ->from($tableName)
      ->joinLeft($prefix."page_membership", $prefix."page_membership.user_id = {$tableName}.user_id", array('title'))
      ->where($prefix."page_membership.resource_id = {$this->getIdentity()}")
      ->where($prefix."page_membership.type = 'EMPLOYER'")
      ->limit($limit);

    return Zend_Paginator::factory($select);
  }

  public function getTeam($limit = 0)
  {
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $tableName = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false);

    $prefix = $table->getTablePrefix();

    $select
      ->from($tableName)
      ->joinLeft($prefix."page_membership", $prefix."page_membership.user_id = {$tableName}.user_id", array('title', 'type'))
      ->where($prefix."page_membership.resource_id = {$this->getIdentity()}")
      ->order($prefix."page_membership.type ASC")
      ->limit($limit);

    return Zend_Paginator::factory($select);
  }

  public function getWebsite()
  {
    if (strpos($this->website, "http://") === false){
      $url = "http://".$this->website;
    }else{
      $url = $this->website;
    }

    return "<a href='".$url."' target='_blank'>".$this->website."</a>";
  }

  public function isAddress()
  {
    if ($this->country || $this->city || $this->street){
      return true;
    }

    return false;
  }

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'reset' => true,
      'page_id' => $this->url
    ), $params);

    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function removePhotos()
  {
    if (isset($this->photo_id) && $this->photo_id != 0){
      $storage = Engine_Api::_()->storage();
      $file = $storage->get($this->photo_id);
      if ($file !== null) $file->remove();
      $file = $storage->get($this->photo_id, 'thumb.normal');
      if ($file !== null) $file->remove();
      $file = $storage->get($this->photo_id, 'thumb.icon');
      if ($file !== null) $file->remove();
    }
  }

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
//            $file = $tmpRow->temporary();
      $file = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Event_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_id' => $this->getIdentity(),
      'parent_type'=>'page'
    );

    // Remove photos
    $this->removePhotos();

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $mainPath = $path . '/m_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
    //->resize(175, 200)
      ->resize(200, 400)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . '/in_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $iconPath = $path . '/is_' . $name;
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    try {
      $iMain = $storage->create($path.'/m_'.$name, $params);
      $iIconNormal = $storage->create($path.'/in_'.$name, $params);
      $iSquare = $storage->create($path.'/is_'.$name, $params);

      $iMain->bridge($iIconNormal, 'thumb.normal');
      $iMain->bridge($iSquare, 'thumb.icon');
    } catch( Exception $e ) {
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
      @unlink($iconPath);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);
    @unlink($iconPath);
    @unlink($file);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();

    return $this;
  }

  public function isAddressChanged($address)
  {
    if (!is_array($address) || empty($address)){
      return false;
    }

    return ( ($this->country != $address[0] || $this->city != $address[1] || $this->street != $address[2]) );
  }

  public function setPrivacy(array $values)
  {
    $auth = Engine_Api::_()->authorization()->context;

    $roles = array('team', 'likes', 'registered', 'everyone');
    $viewMax = array_search($values['auth_view'], $roles);
    foreach( $roles as $i => $role ){
      if( $role === 'team' ) {
        $role = $this->getTeamList();
      }
      elseif ( $role === 'likes' ) {
        $role = $this->getLikesList();
      }
      $auth->setAllowed($this, $role, 'view', (int)($i <= $viewMax));
    }

    $roles = array('team', 'likes', 'registered');
    $commentMax = array_search($values['auth_comment'], $roles);
    foreach( $roles as $i => $role ){
      if( $role === 'team' ) {
        $role = $this->getTeamList();
      }
      elseif ( $role === 'likes' ) {
        $role = $this->getLikesList();
      }
      $auth->setAllowed($this, $role, 'comment', (int)($i <= $commentMax) );
    }

    $roles = array('team', 'likes', 'registered');


    if ($values['auth_view'] == 'registered' || $values['auth_view'] == 'everyone'){
      $auth->setAllowed($this, $this->getLikesList(), 'view', 1);
    }

    if ($values['auth_comment'] == 'registered'){
      $auth->setAllowed($this, $this->getLikesList(), 'comment', 1);
    }

    $auth->setAllowed($this, $this->getTeamList(), 'view', 1);
    $auth->setAllowed($this, $this->getTeamList(), 'comment', 1);

    // Set page extensions privacy
    /**
     * @var $pageApi Page_Api_Core
     */
    $pageApi = Engine_Api::_()->page();
    $page_features = $this->getAllowedFeatures();

    if ($pageApi->isModuleExists('pagealbum') &&  in_array('pagealbum', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      $postingMax = array_search($values['auth_album_posting'], $roles);
      foreach( $roles as $i => $role ){
        if( $role === 'team' ) {
          $role = $this->getTeamList();
        }
        elseif ( $role === 'likes' ) {
          $role = $this->getLikesList();
        }

        $auth->setAllowed($this, $role, 'album_posting', (int)($i <= $postingMax) );
      }

      if ($values['auth_album_posting'] == 'registered'){
        $auth->setAllowed($this, $this->getLikesList(), 'album_posting', 1);
      }

      $auth->setAllowed($this, $this->getTeamList(), 'album_posting', 1);
    }

    if ($pageApi->isModuleExists('pageblog') &&  in_array('pageblog', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      $postingMax = array_search($values['auth_blog_posting'], $roles);
      foreach( $roles as $i => $role ){
        if( $role === 'team' ) {
          $role = $this->getTeamList();
        }
        elseif ( $role === 'likes' ) {
          $role = $this->getLikesList();
        }

        $auth->setAllowed($this, $role, 'blog_posting', (int)($i <= $postingMax) );
      }

      if ($values['auth_blog_posting'] == 'registered'){
        $auth->setAllowed($this, $this->getLikesList(), 'blog_posting', 1);
      }

      $auth->setAllowed($this, $this->getTeamList(), 'blog_posting', 1);
    }

    if ($pageApi->isModuleExists('pagediscussion') &&  in_array('pagediscussion', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      $postingMax = array_search($values['auth_disc_posting'], $roles);
      foreach( $roles as $i => $role ){
        if( $role === 'team' ) {
          $role = $this->getTeamList();
        }
        elseif ( $role === 'likes' ) {
          $role = $this->getLikesList();
        }

        $auth->setAllowed($this, $role, 'disc_posting', (int)($i <= $postingMax) );
      }

      if ($values['auth_disc_posting'] == 'registered'){
        $auth->setAllowed($this, $this->getLikesList(), 'disc_posting', 1);
      }

      $auth->setAllowed($this, $this->getTeamList(), 'disc_posting', 1);
    }

    if ($pageApi->isModuleExists('pagedocument') && in_array('pagedocument', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      $postingMax = array_search($values['auth_doc_posting'], $roles);
      foreach( $roles as $i => $role ){
        if( $role === 'team' ) {
          $role = $this->getTeamList();
        }
        elseif ( $role === 'likes' ) {
          $role = $this->getLikesList();
        }

        $auth->setAllowed($this, $role, 'doc_posting', (int)($i <= $postingMax) );
      }

      if ($values['auth_doc_posting'] == 'registered'){
        $auth->setAllowed($this, $this->getLikesList(), 'doc_posting', 1);
      }

      $auth->setAllowed($this, $this->getTeamList(), 'doc_posting', 1);
    }

    if ($pageApi->isModuleExists('pageevent') &&  in_array('pageevent', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      $postingMax = array_search($values['auth_event_posting'], $roles);
      foreach( $roles as $i => $role ){
        if( $role === 'team' ) {
          $role = $this->getTeamList();
        }
        elseif ( $role === 'likes' ) {
          $role = $this->getLikesList();
        }

        $auth->setAllowed($this, $role, 'event_posting', (int)($i <= $postingMax) );
      }

      if ($values['auth_event_posting'] == 'registered'){
        $auth->setAllowed($this, $this->getLikesList(), 'event_posting', 1);
      }

      $auth->setAllowed($this, $this->getTeamList(), 'event_posting', 1);
    }

    if ($pageApi->isModuleExists('pagemusic') &&  in_array('pagemusic', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      $postingMax = array_search($values['auth_music_posting'], $roles);
      foreach( $roles as $i => $role ){
        if( $role === 'team' ) {
          $role = $this->getTeamList();
        }
        elseif ( $role === 'likes' ) {
          $role = $this->getLikesList();
        }

        $auth->setAllowed($this, $role, 'music_posting', (int)($i <= $postingMax) );
      }

      if ($values['auth_music_posting'] == 'registered'){
        $auth->setAllowed($this, $this->getLikesList(), 'music_posting', 1);
      }

      $auth->setAllowed($this, $this->getTeamList(), 'music_posting', 1);
    }

    if ($pageApi->isModuleExists('pagevideo') &&  in_array('pagevideo', $page_features) ) {
      $roles = array('team', 'likes', 'registered');
      $postingMax = array_search($values['auth_video_posting'], $roles);
      foreach( $roles as $i => $role ){
        if( $role === 'team' ) {
          $role = $this->getTeamList();
        }
        elseif ( $role === 'likes' ) {
          $role = $this->getLikesList();
        }

        $auth->setAllowed($this, $role, 'video_posting', (int)($i <= $postingMax) );
      }

      if ($values['auth_video_posting'] == 'registered'){
        $auth->setAllowed($this, $this->getLikesList(), 'video_posting', 1);
      }

      $auth->setAllowed($this, $this->getTeamList(), 'video_posting', 1);
    }

    if ($pageApi->isModuleExists('store') &&  in_array('store', $page_features) ) {
      if( $values['auth_store_posting'] == 'team' )
        $auth->setAllowed($this, $this->getTeamList(), 'store_posting', 1);
      else
        $auth->setAllowed($this, $this->getTeamList(), 'store_posting', 0);
    }

    if($pageApi->isModuleExists('donation') && in_array('donation', $page_features)){
      if(isset($values['auth_charity_posting'])){
        if($values['auth_charity_posting'] == 'team'){
          $auth->setAllowed($this, $this->getTeamList(), 'charity_posting', 1);
        }
        else{
          $auth->setAllowed($this, $this->getTeamList(), 'charity_posting', 0);
        }
      }

      if(isset($values['auth_project_posting'])){
        if($values['auth_project_posting'] == 'team'){
          $auth->setAllowed($this, $this->getTeamList(), 'project_posting', 1);
        }
        else{
          $auth->setAllowed($this, $this->getTeamList(), 'project_posting', 0);
        }
      }
    }

  }

  public function getTeamList()
  {
    $table = Engine_Api::_()->getDbTable('lists', 'page');
    $select = $table->select()
      ->where('owner_id = ?', $this->getIdentity())
      ->where('title = ?', 'PAGE_TEAM')
      ->limit(1);

    $list = $table->fetchRow($select);

    if( null === $list ) {
      $list = $table->createRow();
      $list->setFromArray(array(
        'owner_id' => $this->getIdentity(),
        'title' => 'PAGE_TEAM',
      ));
      $list->save();
    }

    return $list;
  }

  public function getLikesList()
  {
    $table = Engine_Api::_()->getDbTable('lists', 'page');
    $select = $table->select()
      ->where('owner_id = ?', $this->getIdentity())
      ->where('title = ?', 'PAGE_LIKES')
      ->limit(1);

    $list = $table->fetchRow($select);

    if( null === $list ) {
      $list = $table->createRow();
      $list->setFromArray(array(
        'owner_id' => $this->getIdentity(),
        'title' => 'PAGE_LIKES',
      ));
      $list->save();
    }

    return $list;
  }

  public function membership()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbTable('membership', 'page'));
  }

  public function addMarker(Page_Model_Marker $marker)
  {
    $marker->getTable()->delete("page_id = {$this->page_id}");

    $marker->page_id = $this->page_id;
    $marker->save();
  }

  public function addMarkerByAddress($address)
  {
    $marker = Engine_Api::_()->getApi('gmap', 'page')->getMarker($address);
    if ($marker){
      $this->addMarker($marker);
    }
  }

  public function deleteMarker()
  {
    Engine_Api::_()->getApi('gmap', 'page')->deleteMarker($this);
  }

  protected function _reorderContentStructure($a, $b)
  {
    $sample = array('left', 'middle', 'right');
    $av = $a['name'];
    $bv = $b['name'];
    $ai = array_search($av, $sample);
    $bi = array_search($bv, $sample);
    if( $ai === false && $bi === false ) return 0;
    if( $ai === false ) return -1;
    if( $bi === false ) return 1;
    $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
    return $r;
  }

  public function createContent()
  {
    $pagesTable = Engine_Api::_()->getDbtable('pages', 'page');
    $pageDefault = $pagesTable->fetchRow($pagesTable->select()->where('name = ?', 'default'));
    $contentTable = Engine_Api::_()->getDbtable('content', 'page');
    $contentDefault = $contentTable->fetchAll($contentTable->select()->where('page_id=?', $pageDefault->getIdentity()));
    $contentStructure = $pagesTable->prepareContentArea($contentDefault);

    foreach( $contentStructure as &$info1 )
    {
      if (!in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container') {
        $error = true;
        break;
      }
      foreach( $info1['elements'] as &$info2 )
      {
        if (!in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container') {
          $error = true;
          break;
        }
      }
      // Re order second-level elements
      usort($info1['elements'], array($this, '_reorderContentStructure'));
    }

    // main, bottom
    foreach($contentStructure as $item)
    {
      $contentRow1 = $this->createContentItem(array('type' => $item['type'],
        'order' => $item['order'],
        'name' => $item['name'],
        'params' => $item['params'],
        'parent_content_id' => 0));
      if (count($item['elements']) > 0) {
        foreach($item['elements'] as $item2)
        {
          $contentRow2 = $this->createContentItem(array('type' => $item2['type'],
            'order' => $item2['order'],
            'name' => $item2['name'],
            'params' => $item2['params'],
            'parent_content_id' => $contentRow1->content_id));
          if (count($item2['elements']) > 0) {
            foreach($item2['elements'] as $item3)
            {
              $contentRow3 = $this->createContentItem(array('type' => $item3['type'],
                'order' => $item3['order'],
                'name' => $item3['name'],
                'params' => $item3['params'],
                'parent_content_id' => $contentRow2->content_id));
              if (count($item3['elements']) > 0) {
                foreach($item3['elements'] as $item4)
                {
                  $contentRow4 = $this->createContentItem(array('type' => $item4['type'],
                    'order' => $item4['order'],
                    'name' => $item4['name'],
                    'params' => $item4['params'],
                    'parent_content_id' => $contentRow3->content_id));
                }
              }
            }
          }
        }
      }
    }
  }

  public function createContentItem(Array $params)
  {
    if (empty($params)) {
      return false;
    }
    if (!isset($contentTable)) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'page');
    }

    $contentRow = $contentTable->createRow();
    foreach ($params as $key => $value) {
      if ($key == "") {
        continue;
      }
      if ($value === null) {
        $value = "";
      }
      $contentRow->$key = $value;
    }
    $contentRow->page_id = $this->page_id;
    $contentRow->save();

    return $contentRow;
  }

  protected function _delete()
  {
    $this->removePhotos();
    $page_id = $this->getIdentity();

    $table = Engine_Api::_()->fields()->getTable('page', 'search');
    $prefix = $table->getTablePrefix();

    $db = $table->getAdapter();
    $db->delete($prefix.'page_fields_search', "item_id = {$page_id}");
    $db->delete($prefix.'page_fields_values', "item_id = {$page_id}");
    $db->delete($prefix.'page_markers', "page_id = {$page_id}");
    $db->delete($prefix.'page_content', "page_id = {$page_id}");
    $db->delete($prefix.'page_views', "page_id = {$page_id}");

    $where = "object_id = {$page_id} AND object_type = 'page'";

    $db->delete($prefix.'activity_actions', $where);
    $db->delete($prefix.'activity_stream', $where);

    $where = "resource_type = 'page' AND resource_id = {$page_id}";

    $db->delete($prefix.'core_tagmaps', $where);
    $db->delete($prefix.'authorization_allow', $where);
    $db->delete($prefix.'core_likes', $where);

    $teamList = $this->getTeamList();
    $likesList = $this->getLikesList();

    $db->delete($prefix.'page_listitems', "list_id = {$teamList->getIdentity()}");
    $db->delete($prefix.'page_listitems', "list_id = {$likesList->getIdentity()}");
    $db->delete($prefix.'page_lists', "owner_id = {$this->page_id}");

    // favorites
    $db->delete($prefix.'page_favorites', "page_id = {$page_id} || page_fav_id = {$page_id}");

    // claim
    $db->delete($prefix.'page_claims', "page_id = {$page_id}");

    //Subscriptions
    $db->delete($prefix.'page_subscriptions', "page_id = {$page_id}");

    $this->membership()->removeAllMembers();
    Engine_Hooks_Dispatcher::getInstance()->callEvent('removePage', array('page' => $this));

    parent::_delete();
  }

  public function delete()
  {
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store') ) {
      $apiTable = Engine_Api::_()->getDbTable('apis', 'store');
      $productsTable = Engine_Api::_()->getDbTable('products', 'store');
      $apiTable->delete(array('page_id = ?' => $this->getIdentity()));
      $products = $productsTable->fetchAll($productsTable->select()->where('page_id = ?', $this->getIdentity()));
      foreach( $products as $product ) {
        $product->delete();
      }
    }
    parent::delete();
  }

  public function fields()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getApi('core', 'fields'));
  }

  public function approvedStatus($value)
  {
    if ($value != $this->approved) {
      $this->approved = $value;
      $this->search = $value;
      $this->save();
    }

    return $this;
  }

  public function sponsoredStatus($value)
  {
    if ($value != $this->sponsored) {
      $this->sponsored = $value;

      switch( $this->auto_set ){
        case 0:
          $this->auto_set = 2;
          break;
        case 1:
          $this->auto_set = 3;
          break;
        case 2:
          $this->auto_set = 2;
          break;
        case 3:
          $this->auto_set = 3;
          break;
      }

      $this->save();
    }

    return $this;
  }

  public function featuredStatus($value)
  {
    if ($value != $this->featured) {
      $this->featured = $value;

      switch( $this->auto_set ){
        case 0:
          $this->auto_set = 1;
          break;
        case 1:
          $this->auto_set = 1;
          break;
        case 2:
          $this->auto_set = 3;
          break;
        case 3:
          $this->auto_set = 3;
          break;
      }

      $this->save();
    }

    return $this;
  }

  public function setIdentity($id)
  {
    $this->_identity = $id;

    return $this;
  }

  public function viewPage()
  {
    $user_id = (int)Engine_Api::_()->user()->getViewer()->getIdentity();
    $today = Engine_Api::_()->page()->getToday();
    $locationsTable = Engine_Api::_()->getDbTable('locations', 'page');
    $table = Engine_Api::_()->getDbTable('views', 'page');

    $view = $table->createRow();

    if ($user_id == $this->user_id) {
      return ;
    }

    $db = $table->getAdapter();
    $prefix = $table->getTablePrefix();

    $select = $db->select();
    $select
      ->from($prefix.'page_views', array('view_id'))
      ->where('page_id = ?', $this->page_id)
      ->where('view_date >= ?', $today)
      ->where('user_id = ?', $user_id);

    $ip = ip2long($_SERVER['REMOTE_ADDR']);

    $select->where('ip = ?', $ip);

    $country = $locationsTable->getCountry($ip);

    $view->user_id = $user_id;
    $view->ip = $ip;
    $view->country = ($country && $country->name) ? $country->name : 'localhost';

    if (!((bool)$db->fetchOne($select))){
      $this->unique_views++;
    }

    $view->view_date = $today;
    $view->page_id = $this->page_id;
    $view->save();

    $this->view_count++;
    $this->save();

    // Run Page visit hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onPageVisit', $this);

  }

  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  public function getTotalVisitorsCount()
  {
    return $this->unique_views;
  }

  public function getTotalViewsCount()
  {
    return $this->view_count;
  }

  public function displayAddress()
  {
    $address = array();

    if (!empty($this->street)){
      $address[] = $this->street;
    }

    if (!empty($this->city)){
      $address[] = $this->city;
    }

    if(!empty($this->state)) {
      $address[] = $this->state;
    }

    if (!empty($this->country)){
      $address[] = $this->country;
    }

    return implode(', ', $address);
  }

  public function getViewStats($params)
  {
    $select = $this->getViewStatsSelect($params);
    return Zend_Paginator::factory($select);
  }

  public function getViewStatsCount($params)
  {
    $select = $this->getViewStatsSelect($params);
    $db = $this->getTable()->getAdapter();

    $query = "SELECT SUM(count) FROM (" . $select->__toString() . ") AS t1";
    return $db->fetchOne($query);
  }

  public function getViewStatsSelect($params)
  {
    $table = $this->getTable();
    $db = $table->getAdapter();

    $prefix = $table->getTablePrefix();

    $select = $db->select();

    $select
      ->from($prefix.'page_views', array('count' => 'COUNT(DISTINCT user_id, ip)', 'country'))
      ->where($prefix.'page_views.page_id = ?', $this->getIdentity())
      ->where($prefix.'page_views.country <> ?', 'localhost')
      ->group($prefix.'page_views.country')
      ->order('count DESC');

    return $select;
  }

  public function getLikesCount()
  {
    if (Engine_Api::_()->page()->isModuleExists('like')){
      $likeApi = Engine_Api::_()->getApi('core', 'like');
      return (int)$likeApi->getLikeCount($this);
    }

    return 0;
  }

  public function getParent()
  {
    return $this->getOwner();
  }

  public function getOwner()
  {
    return $this->owner ? $this->owner : Engine_Api::_()->getItem('user', $this->user_id);
  }

  /**
   * @param null $page_id
   * @return Core_Model_Item_Abstract|null|Page_Model_Package
   */
  public function getPackage( $page_id = null )
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = $this;
    if ($page_id != null) {
      $page = Engine_Api::_()->getItem('page', (int)$page_id);
    }

    if ( !$page ) {
      return null;
    }

    if ( $page->package_id ) {
      return Engine_Api::_()->getItem('page_package', $page->package_id);
    }

    /**
     * @var $subscription Page_Model_Subscription
     */
    if (null == ($subscription = Engine_Api::_()->getItemTable('page_subscription')->getSubscription($page->getIdentity()))){
      return null;
    }

    /**
     * @var $package Page_Model_Package
     */
    return Engine_Api::_()->getItem('page_package', $subscription->package_id);
  }

  public function getMarker($insert = false)
  {
    $markersTbl = Engine_Api::_()->getDbTable('markers', 'page');
    $select = $markersTbl->select()
      ->where('page_id = ?', $this->getIdentity());

    $marker = $markersTbl->fetchRow($select);

    if (!$marker && $insert) {
      $marker = $markersTbl->createRow(array(
        'page_id' => $this->getIdentity(),
        'latitude' => 0,
        'longitude' => 0
      ));
    }

    return $marker;
  }

  public function changeOwner($owner_id)
  {
    /**
     * @var $membershipTbl Page_Model_DbTable_Membership
     **/

    $membershipTbl = Engine_Api::_()->getDbTable('membership', 'page');
    $membershipTbl->updateRow($this, $owner_id);

    $this->user_id = $owner_id;
    $this->parent_id = $owner_id;
    $this->save();
    return $this;
  }

  /**
   * @param $viewer User_Model_User
   */
  public function isAdmin( $viewer = null )
  {
    if( $viewer == null )
      $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer || $viewer->getIdentity() == null ) return false;

    $superAdmins = Engine_Api::_()->user()->getSuperAdmins()->toArray();

    if(!empty($superAdmins))
    {
      foreach($superAdmins as $item)
      {
        if($item['user_id'] !== (int) $viewer->getIdentity() )
          continue;

        return true;
      }
    }

    $table = Engine_Api::_()->getDbTable('membership', 'page');
    $select = $table
      ->select()
      ->from($table->info('name'), array('count' => new Zend_Db_Expr('COUNT(*)')))
      ->where('resource_id = ?', $this->getIdentity())
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('type = ?', 'ADMIN');

    $result = $select
      ->query()
      ->fetchColumn(0);
    return (bool) $result;
  }

  public function setAdmin( $user )
  {
    if( $user->getIdentity() == null ) return;

    $row = $this->getMembershipRow($user);
    $row->type = 'ADMIN';
    $row->save();
  }

  public function setEmployer( $user )
  {
    if( $user->getIdentity() == null ) return;

    $row = $this->getMembershipRow($user);
    $row->type = 'EMPLOYER';
    $row->save();
  }

  public function getMembershipRow($user)
  {
    /**
     * @var $table Page_Model_DbTable_Membership
     */


    $table = Engine_Api::_()->getDbTable('membership', 'page');
    $select = $table->select()
      ->where('resource_id = ?', $this->getIdentity())
      ->where('user_id = ?', $user->getIdentity())
      ->limit(1);

    $row = $table->fetchRow($select);
    if( $row == null ) {
      $this->membership()->addMember($user)->setUserApproved($user)->setResourceApproved($user)->setUserTypeAdmin($user);
      $this->getTeamList()->add($user);
      $row = $table->fetchRow($select);
    }

    return $row;
  }

  public function isDefaultPackageEnabled()
  {
    /**
     *  @var $settings Core_Model_DbTable_Settings
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    if( !$settings->getSetting('page.package.enabled', 0) ) return true;
    if( !$this->enabled &&  !$settings->getSetting('default.package.enabled', 1)) return false;
    if( $this->package_id == 0 && $settings->getSetting('default.package.enabled', 1) ) return true;
    if( $this->package_id == 0 && !$settings->getSetting('default.package.enabled', 1) ) return false;
    if( !$this->getPackage()->isDefault() ) return true;
    if( $settings->getSetting('default.package.enabled', 1) ) return true;

    return false;
  }


  // New Timeline Page
  public function forceToTimeline()
  {
    if ($this->timeline_converted) {
      return true;
    }
    $page_id = $this->getIdentity();
    $content_table = Engine_Api::_()->getDbtable('content', 'page');
    $pages_table = Engine_Api::_()->getDbtable('pages', 'page');

    $main = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'container', 'order' => 2, 'name' => 'main', 'parent_content_id' => 0, 'is_timeline' => 1));
    $middle = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'container', 'order' => 6, 'name' => 'middle', 'parent_content_id' => $main->content_id, 'is_timeline' => 1));

    $timeline_header = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'widget', 'order' => 3, 'name' => 'timeline.page-header', 'parent_content_id' => $middle->content_id, 'is_timeline' => 1));
    $timeline_content = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'widget', 'order' => 4, 'name' => 'timeline.page-content', 'parent_content_id' => $middle->content_id, 'is_timeline' => 1));

    $core_tabs = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'widget', 'order' => 5, 'name' => 'core.container-tabs', 'parent_content_id' => $middle->content_id, 'is_timeline' => 1));

    // search for core.container-tabs
    $select = $content_table->select()
      ->where('page_id = ?', $this->page_id)
      ->where('name = ?', 'core.container-tabs')
      ->where('is_timeline = ?', false)
      ->order('order ASC');

    $row = $content_table->fetchRow($select);
    if ($row) {
      $select = $content_table->select()
        ->where('page_id = ?', $this->page_id)
        ->where('parent_content_id = ?', $row->content_id)
        ->order('order ASC');

      $tabs = $content_table->fetchAll($select);
      if ($tabs) {
        foreach ($tabs as $tab) {
          $tab = $tab->toArray();
          $tab['is_timeline'] = 1;
          $tab['parent_content_id'] = $core_tabs->content_id;
          unset($tab['content_id']);
          $tabs_content = $pages_table->createContentItem($tab);
        }
      }
    }
    return true;
  }

  public function isTimeline()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $usage = $settings->__get('timeline.usageonpage', false);

    if($usage && $usage == 'force') {
        $this->timeline_converted = $this->forceToTimeline();
        $this->save();
      return true;
    }

    if($this->is_timeline && $this->timeline_converted)
      return true;

    return false;
  }

  // New Timeline Page

  public function hasTimelinePhoto($type = 'cover')
  {
    $row_name = $type . '_id';
    return (boolean)$this->$row_name;
  }

  public function getTimelinePhoto($type = 'cover', $alt = "", $attribs = array())
  {
    $row_name = $type . '_id';
    if (
//                    !$this->isPhotoTypeSupported($type) ||
      !$this->$row_name
    ) {
      return '';
    }


    /**
     * @var $table Storage_Model_DbTable_Files
     * @var $file Storage_Model_File
     */
    $table = Engine_Api::_()->getDbTable('files', 'storage');
    $file = $table->getFile($this->$row_name);
    $src = $file->map();

    /**
     * @var $table User_Model_DbTable_Settings
     */
    $table = Engine_Api::_()->getDbTable('settings', 'user');
    $position = unserialize($table->getSetting($this->getOwner(), 'timeline-page-cover-position-' . $this->getIdentity()));

    if (!is_array($position) || !array_key_exists('top', $position) || !array_key_exists('left', $position)) {
      $position = array('top' => 0, 'left' => 0);
    }

    $attribs['style'] = 'top:' . $position['top'] . 'px;left:' . $position['left'] . 'px;';

    // User image
    $attribs = array_merge(array('id' => $type . '-photo'), $attribs);

    if ($src) {
      return Zend_Registry::get('Zend_View')->htmlImage($src, $alt, $attribs);
    }

    return '';
  }

  public function getTimelineAlbumPhoto($type = 'cover')
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
      return null;
    }

    /**
     * @var $table Timeline_Model_DbTable_Settings
     */
    $photo_id = null;
    $table = Engine_Api::_()->getDbTable('settings', 'hecore');
    $photo_id = $table->getSetting($this->owner, 'timeline-page-' . $type . '-photo-id');

    if ($photo_id == null) return null;

    return Engine_Api::_()->getItem('album_photo', $photo_id);
  }
// New Timeline Page
  public function setTimelinePhoto($photo, $type = 'cover')
  {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if ($photo instanceof Storage_Model_File) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setTimelinePhoto');
    }

    if (!$fileName) {
      $fileName = $file;
    }

    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->getOwner(),
      'name' => basename($fileName),
    );

    /**
     * Save
     *
     * @var $filesTable Storage_Model_DbTable_Files
     */
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
    //      ->resize(850, 315)
      ->write($mainPath)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    // Store
    $iMain = $filesTable->createFile($mainPath, $params);

    // Remove temp files
    @unlink($mainPath);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');

    $row_name = $type . '_id';
    $this->$row_name = $iMain->file_id;
    $this->save();
    return $this;
  }
// New Timeline Page

  public function getPhotoUrl($type = null)
  {
    if( empty($this->photo_id) ) {
      $view = Zend_Registry::get('Zend_View');
      return $view->layout()->staticBaseUrl . 'application/modules/Page/externals/images/nophoto_page_thumb_normal.png';
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function getMediaType()
  {
    return $this->getType();
  }
}