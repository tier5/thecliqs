<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Api_Core extends Core_Api_Abstract
{
  protected $_params = array();
  /**
   * @var $_subject Page_Model_Page
   */
  protected $_subject;

  /**
   * @var Zend_View
   */
  protected $_view;

  public function getHeight($img)
  {
    $file = '';

    if (is_file($img)) {
      $file = $img;
    } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . $img)) {
      $file = $_SERVER['DOCUMENT_ROOT'] . $img;
    } else {
      preg_match('/< *img[^>]*src *= *["\']?([^?"\']*)/i', $img, $matches);

      foreach ($matches as $match) {
        if (is_file($match)) {
          $file = $match;
          break;
        }
        elseif (is_file($_SERVER['DOCUMENT_ROOT'] . $match)) {
          $file = $_SERVER['DOCUMENT_ROOT'] . $match;
          break;
        }
      }
    }

    if (is_file($file)) {
      $size = getimagesize($file);
      if (array_key_exists(1, $size) && is_numeric($size[1])) return $size[1];
    }

    return 0;
  }

  public function getApplications($content)
  {
    // Don't render this if subject doesn't exist
    if (!Engine_Api::_()->core()->hasSubject()) {
      return null;
    }

    // Get subject
    $this->_subject = $subject = Engine_Api::_()->core()->getSubject('user');

    try {
      $allApplications = require APPLICATION_PATH_MOD . '/Timeline/settings/applications.php';
      if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') && Engine_Api::_()->touch()->isTouchMode())
        $allApplications = require APPLICATION_PATH_MOD . '/Touch/settings/tl_apps.php';
    } catch (Exception $e) {
      print_log($e);
      return array();
    }

    /**
     * @var $table Core_Model_DbTable_Modules
     */
    $mTable = Engine_Api::_()->getDbTable('modules', 'core');
    $applications = array();

    foreach ($content as $widget) {

      if (
        !array_key_exists($widget->name, $allApplications) ||
        !$mTable->isModuleEnabled($allApplications[$widget->name]['module'])
      ) continue;

      $application = $allApplications[$widget->name];

      try {
        $parts = explode('.', $widget->name);

        $applications[$widget->name] = $application;

        if (isset($allApplications[$widget->name]) &&
          array_key_exists('render', $allApplications[$widget->name]) &&
          !$allApplications[$widget->name]['render']
        ) continue;

        $parts = explode('-', $parts[1]);
        foreach ($parts as $key => $value) {
          if ($key == 0) continue;
          $parts[$key] = ucfirst($value);
        }

        $method = '_' . implode('', $parts);

        $this->_params = $widget->params;

        if (method_exists($this, $method)) {
          $applications[$widget->name]['items'] = $this->$method();
        }
      } catch (Exception $e) {
        print_log($e);
      }
    }

    return $applications;
  }

  public function getPageApplications($content)
  {
    // Don't render this if subject doesn't exist
    if (!Engine_Api::_()->core()->hasSubject()) {
      return null;
    }

    // Get subject
    $this->_subject = $subject = Engine_Api::_()->core()->getSubject('page');

    try {
      $allApplications = require APPLICATION_PATH_MOD . '/Timeline/settings/page_applications.php';
    } catch (Exception $e) {
      print_log($e);
      return array();
    }
    /**
     * @var $table Core_Model_DbTable_Modules
     */
    $mTable = Engine_Api::_()->getDbTable('modules', 'core');
    $applications = array();

    foreach ($content as $widget) {

      if (
        !array_key_exists($widget->name, $allApplications) ||
        !$mTable->isModuleEnabled($allApplications[$widget->name]['module'])
      ) {
        $test[] = $widget->name;
        continue;
      }

      $application = $allApplications[$widget->name];

      try {
        $parts = explode('.', $widget->name);

        $applications[$widget->name] = $application;

        if (
          array_key_exists('render', $allApplications[$widget->name]) &&
          !$allApplications[$widget->name]['render']
        ) continue;

        $parts = explode('-', $parts[1]);
        foreach ($parts as $key => $value) {
          if ($key == 0) continue;
          $parts[$key] = ucfirst($value);
        }

        $method = '_' . implode('', $parts);

        $this->_params = $widget->params;

        if (method_exists($this, $method)) {
          $applications[$widget->name]['items'] = $this->$method();
        }
      } catch (Exception $e) {
        print_log($e);
      }
    }

    return $applications;
  }

  /********************************* Timeline Page ***********************************/
/* _profileAlbum */
  protected function _profileAlbum()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $select = Engine_Api::_()->getItemTable('pagealbum')->select()
      ->where('page_id = ?', $this->_subject->getIdentity())
      ->order('RAND()');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  /* _profileDocument */
  protected function _profileDocument()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $select = Engine_Api::_()->getItemTable('pagedocument')->select()
      ->where('page_id = ?', $this->_subject->getIdentity())
      ->order('RAND()');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  /* _profileDiscussion */
  protected function _profileEvent()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $select = Engine_Api::_()->getItemTable('pageevent')->select()
      ->where('page_id = ?', $this->_subject->getIdentity())
      ->order('RAND()');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  /* _profileVideo */
  protected function _profileVideo()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $select = Engine_Api::_()->getItemTable('pagevideo')->select()
      ->where('page_id = ?', $this->_subject->getIdentity())
      ->order('RAND()');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

/* _profileBlog */
//    protected function _profileBlog()
//    {
//        /**
//         * Get paginator
//         *
//         * @var $paginator Zend_Paginator
//         */
//        $select = Engine_Api::_()->getItemTable('pageblog')->select()
//            ->where('page_id = ?', $this->_subject->getIdentity())
//            ->order('RAND()');
//
//        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
//
//        // Set item count per page and current page number
//        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
//        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//
//        // Do not render if nothing to show
//        if ($paginator->getTotalItemCount() <= 0) {
//            return null;
//        }
//
//        return $paginator;
//    }
/* _profileContact */
//    protected function _profileContact()
//    {
//        /**
//         * Get paginator
//         *
//         * @var $paginator Zend_Paginator
//         */
//        $select = Engine_Api::_()->getItemTable('pagecontact')->select()
//            ->where('page_id = ?', $this->_subject->getIdentity())
//            ->order('RAND()');
//
//        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
//
//        // Set item count per page and current page number
//        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
//        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//
//        // Do not render if nothing to show
//        if ($paginator->getTotalItemCount() <= 0) {
//            return null;
//        }
//
//        return $paginator;
//    }

  // Timeline Page
/* _profileDiscussion */
//    protected function _profileDiscussion()
//    {
//        /**
//         * Get paginator
//         *
//         * @var $paginator Zend_Paginator
//         */
//        $select = Engine_Api::_()->getItemTable('pagediscussion_pagetopic')->select()
//            ->where('page_id = ?', $this->_subject->getIdentity())
//            ->order('RAND()');
//
//        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
//
//        // Set item count per page and current page number
//        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
//        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//
//        // Do not render if nothing to show
//        if ($paginator->getTotalItemCount() <= 0) {
//            return null;
//        }
//
//        return $paginator;
//    }

  /********************************* Timeline Page ***********************************/

  protected function _profileFriends()
  {
    // Multiple friend mode
    $select = $this->_subject->membership()->getMembersOfSelect();
    $friends = $paginator = Zend_Paginator::factory($select);

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    // Get stuff
    $ids = array();
    foreach ($friends as $friend) {
      $ids[] = $friend->resource_id;
    }

    $table = Engine_Api::_()->getItemTable('user');
    $select = $table
      ->select()
      ->where('user_id IN( ' . implode(',', $ids) . ')')
      ->order('RAND()');
    $paginator = Zend_Paginator::factory($select);

    $paginator->setItemCountPerPage(6);
    $paginator->setCurrentPageNumber(1);

    return $paginator;
  }

  protected function _profileFriendsFollowers()
  {

    // Multiple friend mode
    $select = $this->_subject->membership()->getMembersSelect();
    $friends = $paginator = Zend_Paginator::factory($select);


    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    // Get stuff
    $ids = array();
    foreach ($friends as $friend) {
      $ids[] = $friend->user_id;
    }

    $table = Engine_Api::_()->getItemTable('user');
    $select = $table
      ->select()
      ->where('user_id IN( ' . implode(',', $ids) . ')')
      ->order('RAND()');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(6);
    $paginator->setCurrentPageNumber(1);

    return $paginator;
  }

  protected function _profileFriendsFollowing()
  {

    // Multiple friend mode
    $select = $this->_subject->membership()->getMembersOfSelect();
    $friends = $paginator = Zend_Paginator::factory($select);


    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    // Get stuff
    $ids = array();
    foreach ($friends as $friend) {
      $ids[] = $friend->resource_id;
    }

    $table = Engine_Api::_()->getItemTable('user');
    $select = $table
      ->select()
      ->where('user_id IN( ' . implode(',', $ids) . ')')
      ->order('RAND()');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(6);
    $paginator->setCurrentPageNumber(1);

    return $paginator;
  }

  protected function _profileAlbums()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $select = Engine_Api::_()->getItemTable('album')->select()
      ->where('owner_type = ?', $this->_subject->getType())
      ->where('owner_id = ?', $this->_subject->getIdentity())
      ->order('RAND()');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  protected function _profileEvents()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $membership = Engine_Api::_()->getDbtable('membership', 'event');
    $this->view->paginator = $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($this->_subject)->order('rand()'));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  protected function _profileGroups()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $membership = Engine_Api::_()->getDbtable('membership', 'group');
    $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($this->_subject)->order('rand()'));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  protected function _profilePages()
  {
    /**
     * Get paginator
     *
     * @var $paginator Zend_Paginator
     */
    $table = Engine_Api::_()->getDbtable('membership', 'page');
    $itemTable = Engine_Api::_()->getDbTable('pages', 'page');

    $itName = $itemTable->info('name');
    $mtName = $table->info('name');
    $col = current($itemTable->info('primary'));

    $select = $itemTable->select()
      ->setIntegrityCheck(false)
      ->from($itName)
      ->joinLeft($mtName, "`{$mtName}`.`resource_id` = `{$itName}`.`{$col}`", array('admin_title' => "{$mtName}.title"))
      ->where("`{$mtName}`.`user_id` = ?", $this->_subject->getIdentity())
      ->where("`{$mtName}`.`active` = 1")
      ->where("`{$itName}`.`approved` = 1")
      ->order('RAND()');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(2);

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  protected function _profileClassifieds()
  {
    /**
     * Get paginator
     *
     * @var $table Classified_Model_DbTable_Classifieds
     * @var $paginator Zend_Paginator
     */
    $table = Engine_Api::_()->getItemTable('classified');
    $paginator = $table->getClassifiedsPaginator(array(
      'user_id' => $this->_subject->getIdentity(),
      'orderby' => 'creation_date',
    ));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  protected function _profileLikes()
  {
    /**
     * Get paginator
     *
     * @var $api Like_Api_Core
     * @var $table Core_Model_DbTable_Likes
     */
    $api = Engine_Api::_()->like();
    $itemTypes = array_keys($api->getSupportedModulesLabels());
    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $select = $table->select()
      ->where('poster_type = ?', $this->_subject->getType())
      ->where('poster_id = ?', $this->_subject->getIdentity())
      ->where('resource_type IN ("' . implode('","', $itemTypes) . '")')
      ->order('RAND()')
      ->limit(4);
    $items = array();
    foreach ($table->fetchAll($select) as $row)
    {
      if (null == ($item = Engine_Api::_()->getItem($row->resource_type, $row->resource_id))) continue;
      $items[] = $item;

      if (count($items) == 2) break;
    }

    return $items;
  }

  protected function _profileVideos()
  {
    /**
     * Get paginator
     *
     * @var $api Video_Api_Core
     * @var $paginator Zend_Paginator
     */
    $api = Engine_Api::_()->getApi('core', 'video');
    $paginator = $api->getVideosPaginator(array(
      'user_id' => $this->_subject->getIdentity(),
      'status' => 1,
      'search' => 1
    ));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return null;
    }

    return $paginator;
  }

  /*for std*/
  /*for touch {*/
  protected function _userProfileFriends()
  {
    return $this->_profileFriends();
  }

  protected function _albumProfileAlbums()
  {
    return $this->_profileAlbums();
  }

  protected function _eventProfileEvents()
  {
    return $this->_profileEvents();
  }

  protected function _groupProfileGroups()
  {
    return $this->_profileGroups();
  }

  protected function _pagesProfilePages()
  {
    return $this->_profilePages();
  }

  protected function _classifiedsProfileClassifieds()
  {
    return $this->_profileClassifieds();
  }

  protected function _likeProfileLikes()
  {
    return $this->_profileLikes();
  }

  protected function _videoProfileVideos()
  {
    return $this->_profileVideos();
  }

  /*} for touch */

  protected function _getParam($name, $default = null)
  {
    if (array_key_exists($name, $this->_params))
      return $this->_params[$name];

    return $default;
  }

  public function getSupportedItems()
  {
    return array(
      'user'
    );
  }

  public function timelineDates(Timeline_Model_User $subject)
  {
    /**
     * Timeline Dates
     *
     * @var $actionsTb Wall_Model_DbTable_Actions
     */
    $actionsTb = Engine_Api::_()->getDbTable('actions', 'wall');

    $birthdate = $subject->getBirthdate();

    $select = $actionsTb
      ->select()
      ->setIntegrityCheck(false)
      ->from(
      $actionsTb->info('name'),
      array(
        'year' => 'DATE_FORMAT(`date`, \'%Y\')',
        'month' => 'DATE_FORMAT(`date`, \'%m\')',
        'UNIX_TIMESTAMP(MAX(date)) AS date',
        'max_id' => 'MAX(action_id)',
        'min_id' => 'MIN(action_id)'
      ))
      ->where('subject_type = ?', 'user')
      ->where('subject_id = ?', $subject->getIdentity());

    if ($birthdate) {
      $select->where('date >= ?', $birthdate);
    }

    $select
      ->orWhere('object_type = ?', 'user')
      ->where('object_id = ?', $subject->getIdentity());

    if ($birthdate) {
      $select->where('date >= ?', $birthdate);
    }

    $select
      ->group('DATE_FORMAT(`date`, \'%Y%m\')')
      ->order('date DESC');

    $dates = $actionsTb->fetchAll($select);

    return $this->_reorderDates($dates->toArray(), $birthdate);
  }

  public function timelinePageDates(Page_Model_Page $subject)
  {
    /**
     * Timeline Dates
     *
     * @var $actionsTb Wall_Model_DbTable_Actions
     */
    $actionsTb = Engine_Api::_()->getDbTable('actions', 'wall');

    $birthdate = $subject->creation_date;

    $select = $actionsTb
      ->select()
      ->setIntegrityCheck(false)
      ->from(
      $actionsTb->info('name'),
      array(
        'year' => 'DATE_FORMAT(`date`, \'%Y\')',
        'month' => 'DATE_FORMAT(`date`, \'%m\')',
        'UNIX_TIMESTAMP(MAX(date)) AS date',
        'max_id' => 'MAX(action_id)',
        'min_id' => 'MIN(action_id)'
      ))
      ->where('subject_type = ?', 'page')
      ->where('subject_id = ?', $subject->getIdentity());

    if ($birthdate) {
      $select->where('date >= ?', $birthdate);
    }

    $select
      ->orWhere('object_type = ?', 'page')
      ->where('object_id = ?', $subject->getIdentity());

    if ($birthdate) {
      $select->where('date >= ?', $birthdate);
    }

    $select
      ->group('DATE_FORMAT(`date`, \'%Y%m\')')
      ->order('date DESC');

    $dates = $actionsTb->fetchAll($select);
    return $this->_reorderDates($dates->toArray(), $birthdate);
  }

  protected function _reorderDates(array $dates_array, $birthdate = null)
  {
    $dates = array();
    $year = date('Y', time());
    $month = date('m', time());
    $translate = Zend_Registry::get('Zend_Translate');

    foreach ($dates_array as $key => $date)
    {
      $date['name'] = $translate->_(date('F', strtotime($date['year'] . '-' . $date['month'] . '-01')));
      $date['title'] = date('M', strtotime($date['year'] . '-' . $date['month'] . '-01'));
      $date['key'] = $date['year'] . '-' . $date['month'];

      if ($year == $date['year'] && $month == $date['month']) {
        $date['title'] = 'Now';
        $dates['now'] = $date;
        continue;
      }

      if (!isset($dates['last_month'])) {
        $date['title'] = $date['name'];
        $dates['last_month'] = $date;
        continue;
      }

      $dates['years']['y' . $date['year']]['m' . $date['month']] = $date;
    }

    if ($birthdate) {
      $b_arr = explode('-', $birthdate);
      $dates['born']['year'] = (int)$b_arr[0];
      $dates['born']['month'] = ($b_arr[1] < 9) ? '0' . $b_arr[1] : $b_arr[1];
      $dates['born']['day'] = ($b_arr[2] < 9) ? '0' . $b_arr[2] : $b_arr[2];
    }
    return $dates;
  }

  public function get_age($birthday)
  {
    list($by, $bm, $bd) = explode('-', $birthday);
    list($cd, $cm, $cy) = explode('-', date('d-m-Y'));
    $cd -= $bd;
    $cm -= $bm;
    $cy -= $by;
    if ($cd < 0) $cm--;
    if ($cm < 0) $cy--;
    return $cy;
  }
}
