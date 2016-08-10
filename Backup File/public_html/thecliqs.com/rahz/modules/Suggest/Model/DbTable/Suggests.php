<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Suggests.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Model_DbTable_Suggests extends Engine_Db_Table
{
  protected $_rowClass = 'Suggest_Model_Suggest';

  public function getSelect(array $params = array())
  {
    $select = $this->select();
    $name = $this->info('name');
    $userTable = Engine_Api::_()->getItemTable('user');
    $uname = $userTable->info('name');

    if (!empty($params['integrityCheck'])) {
      $select
        ->setIntegrityCheck($params['integrityCheck']);

      if (!empty($params['fields'])) {
        $select
          ->from($name, $params['fields']);
      } else {
        $select
          ->from($name);
      }
    }

    if (!empty($params['object_type'])) {
      $select
        ->where('object_type = ?', $params['object_type']);
    }

    if (!empty($params['suggest_id'])) {
      $select
        ->where('suggest_id = ?', $params['suggest_id']);
    }

    if (!empty($params['object_id'])) {
      $select
        ->where('object_id = ?', $params['object_id']);
    }

    if (!empty($params['from_id'])) {
      $select
        ->where('from_id = ?', $params['from_id']);
    }

    if (!empty($params['to_id'])) {
      $select
        ->where('to_id = ?', $params['to_id']);
    }

    if (!empty($params['group'])) {
      $select
        ->group($params['group']);
    }

    if (!empty($params['order'])) {
      $select
        ->order($params['order']);
    }

    if (!empty($params['limit'])) {
      $select
        ->limit($params['limit']);
    }

    return $select;
  }

  public function getPaginator($params = array())
	{
		$select = $this->getSelect($params);
		$paginator = Zend_Paginator::factory($select);

		if (!empty($params['ipp'])) {
			$params['ipp'] = (int)$params['ipp'];
			$paginator->setItemCountPerPage($params['ipp']);
		}

		if (!empty($params['page'])) {
			$params['page'] = (int)$params['page'];
			$paginator->setCurrentPageNumber($params['page']);
		}

		return $paginator;
	}
}