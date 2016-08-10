<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:25 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Quiz_Api_Core extends Core_Api_Abstract
{  
  /**
   * Gets a paginator for quizzes
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getQuizzesPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getQuizzesSelect($params));

    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    
    return $paginator;
  }

  /**
   * Gets a select object for the user's quizzes
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getQuizzesSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('quizs', 'quiz');
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');

    $select = $table->select()
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : 'creation_date DESC' );
      
    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($rName.'.user_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($rName.'.user_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = is_array($params['users'])
        ? "'" . join("', '", $params['users']) . "'"
        : (string)$params['users'];

      $select->where($rName . '.user_id in (?)', new Zend_Db_Expr($str));
    }

    if (!empty($params['tag'])) {
      $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.quiz_id")
        ->where($tmName . '.resource_type = ?', 'quiz')
        ->where($tmName . '.quiz_id = ?', $params['tag']);
    }

    if (!empty($params['category'])) {
      $select->where($rName . '.category_id = ?', $params['category']);
    }

    if (isset($params['publish']) && is_numeric($params['publish'])) {
      $select->where($rName . '.published = ?', $params['publish']);
    }

    if (isset($params['approved']) && is_numeric($params['approved'])) {
      $select->where($rName . '.approved = ?', $params['approved']);
    }

    // Could we use the search indexer for this?
    if (!empty($params['search'])) {
      $select->where($rName . ".title LIKE ? OR " . $rName . ".description LIKE ?", '%' . $params['search'] . '%');
    }

    if (!empty($params['start_date'])) {
      $select->where($rName . ".creation_date > ?", date('Y-m-d', $params['start_date']));
    }

    if (!empty($params['end_date'])) {
      $select->where($rName . ".creation_date < ?", date('Y-m-d', $params['end_date']));
    }

    return $select;
  }

  /**
   * Returns a collection of all the categories in the quiz plugin
   *
   * @return Zend_Db_Table_Select
   */
  public function getCategories()
  {
    return Engine_Api::_()->getDbtable('categories', 'quiz')->fetchAll();
  }

  /**
   * Returns a category item
   *
   * @param Int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategory($category_id)
  {
    return Engine_Api::_()->getDbtable('categories', 'quiz')->find( $category_id)->current();
  }

  public function getQuizTakers($parameters)
  {
    $table = Engine_Api::_()->getDbTable('takes', 'quiz');
    $userTable = Engine_Api::_()->getItemTable('user');

    $takersTable = $table->info('name');
    $usersTable = $userTable->info('name');

    if (isset($parameters['list_type']) && $parameters['list_type'] == 'mutual') {

      $viewer = Engine_Api::_()->user()->getViewer();
      $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
      $membershipsTable = $membershipTable->info('name');

      $viewer = Engine_Api::_()->user()->getViewer();

      $select = $userTable->select()
        ->setIntegrityCheck(false)
        ->from($usersTable)
        ->join($takersTable, "$takersTable.user_id = $usersTable.user_id", array())
        ->joinLeft($membershipsTable, "$takersTable.user_id = $membershipsTable.user_id", array())
        ->where("$takersTable.quiz_id = ?", $parameters['quiz_id'], 'INTEGER')
        ->where("$membershipsTable.resource_id = ?", $viewer->getIdentity())
        ->where("$membershipsTable.resource_approved = 1")
        ->where("$membershipsTable.user_approved = 1");

    } else {

      $select = $userTable->select()
        ->setIntegrityCheck(false)
        ->from($usersTable)
        ->join($takersTable, "$takersTable.user_id = $usersTable.user_id", array())
        ->where("$takersTable.quiz_id = ?", $parameters['quiz_id'], 'INTEGER');

    }

    if (isset($parameters['result_id']) && $parameters['result_id']) {
      $select->where("$takersTable.result_id = ?", $parameters['result_id'], 'INTEGER');
    }

    if (isset($parameters['exclude_id']) && $parameters['exclude_id']) {
      $select->where("$usersTable.user_id != ?", $parameters['exclude_id'], 'INTEGER');
    }

    if (isset($parameters['keyword']) && $parameters['keyword']) {
      $select->where("$usersTable.displayname LIKE ?", '%'. $parameters['keyword'] .'%', 'STRING');
    }

    return Zend_Paginator::factory($select);
  }

  public function getCurrentTheme()
  {
    // todo theme compatibility
    $themes = Zend_Registry::get('Themes');
    $theme_name = 'default';
    
    if (is_array($themes)) {
      foreach ($themes as $key => $value) {
        $theme_name = $key;
      }
    }
    
    return $theme_name;
  }
}