<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminRecommendationsController.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_AdminRecommendationsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('suggest_admin_main', array(), 'suggest_admin_main_recs');

    $this->view->itemTypes = Engine_Api::_()->suggest()->getItemTypes();
    $this->view->activeType = $activeType = $this->_getParam('rectype', 'user');
    $this->view->onlyRec = $onlyRec = $this->_getParam('onlyrec', 0);
    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->itemTypes['user'] = 'Member';

    $table = Engine_Api::_()->getItemTable($activeType);
    $recTable = Engine_Api::_()->getDbTable('recommendations', 'suggest');
    $recName = $recTable->info('name');

    $name = $table->info('name');
    $primary = $table->info('primary');
    $primary = $primary[1];

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($name)
      ->joinLeft(
        $recName,
        $recName.'.object_type = "'.$activeType.'" AND '.$recName.'.object_id = '.$name.'.'.$primary,
        array(
          'rec' => 'IF ('.$recName.'.recommendation_id IS NULL, 0, 1)'
        )
      );

    if ($onlyRec) {
      $select
        ->where($recName.'.recommendation_id IS NOT NULL');
    }

    if ($activeType == 'page') {
      $select
        ->where('user_id <> 0');
    }

    $this->view->items = $items = Zend_Paginator::factory($select);
    $items->setItemCountPerPage(40);
    $items->setCurrentPageNumber($page);
  }

  public function recommendAction()
  {
    $object_type = $this->_getParam('object_type', '');
    $object_id = $this->_getParam('object_id', 0);
    $this->view->activeType = $activeType = $this->_getParam('rectype', 'user');
    $this->view->onlyRec = $onlyRec = $this->_getParam('onlyrec', 0);
    $this->view->page = $page = $this->_getParam('page', 1);

    if ($object_id && $object_type) {
      $recTable = Engine_Api::_()->getDbTable('recommendations', 'suggest');
      $recTable->insert(array('object_type' => $object_type, 'object_id' => $object_id, 'date' => date('Y-m-d H:i:s')));
    }


    return $this->_redirectCustom(array(
      'module' => 'suggest',
      'controller' => 'recommendations',
      'action' => 'index',
      'route' => 'admin_default',
      'onlyrec' => $onlyRec,
      'rectype' => $activeType,
      'page' => $page
    ),
    array(
      'reset' => true
    ));
  }

  public function unrecommendAction()
  {
    $object_type = $this->_getParam('object_type', '');
    $object_id = $this->_getParam('object_id', 0);
    $this->view->activeType = $activeType = $this->_getParam('rectype', 'user');
    $this->view->onlyRec = $onlyRec = $this->_getParam('onlyrec', 0);
    $this->view->page = $page = $this->_getParam('page', 1);

    if ($object_id && $object_type) {
      $recTable = Engine_Api::_()->getDbTable('recommendations', 'suggest');
      $recTable->delete(array('object_type = ?' => $object_type, 'object_id = ?' => $object_id));
    }

    return $this->_redirectCustom(array(
      'module' => 'suggest',
      'controller' => 'recommendations',
      'action' => 'index',
      'route' => 'admin_default',
      'onlyrec' => $onlyRec,
      'rectype' => $activeType,
      'page' => $page
    ),
    array(
      'reset' => true
    ));
  }

}