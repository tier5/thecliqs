<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Content.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Content extends Engine_Db_Table
{
	protected $_serializedColumns = array('params');

  public function getEnabledExistAddOns($page_id, $module = '')
  {
    if (!$page_id) {
      return 0;
    }

    $table = Engine_Api::_()->getDbTable('modules', 'core');
    $db = $table->getAdapter();

    $prefix = $table->getTablePrefix();

    $select = $db->select()
      ->from($prefix.'page_modules')
      ->joinLeft($prefix.'page_content', $prefix.'page_content.name = '.$prefix.'page_modules.widget', array())
      ->joinLeft($prefix.'core_modules', $prefix.'core_modules.name = '.$prefix.'page_modules.name', array())
      ->where($prefix.'page_content.page_id = ?', $page_id)
      ->where($prefix.'page_modules.name <> ?', 'pagecontact')
      ->where($prefix.'page_modules.name <> ?', 'pagefaq')
      ->where($prefix.'page_modules.name <> ?', 'weather')
      ->where($prefix.'page_modules.name <> ?', 'inviter')
      ->where($prefix.'core_modules.enabled = ?', 1);

    if (!empty($module)) {
      $select->where($prefix.'core_modules.name = ?', $module);
      return $db->fetchRow($select);
    }

    return $db->fetchAll($select);
  }
}