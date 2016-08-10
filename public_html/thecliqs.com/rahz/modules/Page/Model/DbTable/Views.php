<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Views.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Views extends Engine_Db_Table
{
	protected $_rowClass = 'Page_Model_View';

  public function getOldRowsInfo()
  {
    $select = $this->select()
      ->from($this, array('count' => new Zend_Db_Expr('COUNT(*)'), 'min_id' => new Zend_Db_Expr('MIN(`view_id`)')))
      ->where(new Zend_Db_Expr('ISNULL(country)'));

    return $this->getAdapter()->fetchRow($select);
  }

  public function upgradeOldRows()
  {
    $params = $this->getOldRowsInfo();

    if (!$params['count']) {
      return;
    }

    $select = $this->select()
      ->from($this, array('view_id'))
      ->where(new Zend_Db_Expr('ISNULL(country)'))
      ->order('view_id ASC')
      ->limit(1);

    $start_id = $this->getAdapter()->fetchOne($select);

    if (!$start_id) {
      return;
    }

    $select = $this->select()
      ->from($this, array('view_id'))
      ->where(new Zend_Db_Expr('ISNULL(country)'))
      ->order('view_id ASC')
      ->limit(1, 100);

    $end_id = $this->getAdapter()->fetchOne($select);

    if (!$end_id) {
      $select = $this->select()
        ->from($this, array(new Zend_Db_Expr('MAX(view_id)')))
        ->where(new Zend_Db_Expr('ISNULL(country)'))
        ->order('view_id ASC');

      $end_id = $this->getAdapter()->fetchOne($select);
    }

    $locationsTbl = Engine_Api::_()->getDbTable('locations', 'page');
    $locName = $locationsTbl->info('name');
    $viewName = $this->info('name');

    $sql = "UPDATE `{$viewName}` "
      . "INNER JOIN `{$locName}` ON (ISNULL(`{$viewName}`.country) AND `{$viewName}`.view_id >= {$start_id}  AND `{$viewName}`.view_id <= {$end_id} AND `{$locName}`.begin_num <= `{$viewName}`.ip AND `{$locName}`.end_num >= `{$viewName}`.ip) "
      . "SET `{$viewName}`.country = `{$locName}`.name";

    $this->getAdapter()->query($sql);

    $sql = "UPDATE `{$viewName}` SET `{$viewName}`.country = 'localhost' "
      . "WHERE ISNULL(`{$viewName}`.country) AND `{$viewName}`.view_id >= {$start_id}  AND `{$viewName}`.view_id <= {$end_id}";

    $this->getAdapter()->query($sql);
  }
}