<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Imports.php 19.12.11 16:32 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Imports extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_Import';

  public function getAllImportsPaginator($params)
  {
    $select = $this->getAllImportsSelect($params);
    $paginator = Zend_Paginator::factory($select);

    if( !empty($params['ipp']) ) {
      $paginator->setItemCountPerPage($params['ipp']);
    }

    if( !empty($params['page']) ) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    return $paginator;
  }

  public function getAllImportsSelect($params)
  {
    $select = $this->select();

    if( !empty($params['file_name']) ) {
      $select->where('file_name LIKE ?', '%' . $params['file_name'] . '%');
    }

    if( !empty($params['status']) ) {
      $select->where('status = ?', $params['status']);
    }

    if( !empty($params['import_id']) ) {
      $select->where('import_id = ?', $params['import_id']);
    }

    if( !empty($params['status']) ) {
      $select->where('status = ?', $params['status']);
    }

    if( !empty($params['order']) ) {
      $select->order($params['order']);
    }

    return $select;
  }
}
