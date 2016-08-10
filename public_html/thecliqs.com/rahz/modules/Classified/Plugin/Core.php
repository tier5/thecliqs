<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('classifieds', 'classified');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'classified');
  }


  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete classifieds
      $classifiedTable = Engine_Api::_()->getDbtable('classifieds', 'classified');
      $classifiedSelect = $classifiedTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach( $classifiedTable->fetchAll($classifiedSelect) as $classified ) {
        $classified->delete();
      }
      // delete images and albums as well
    }
  }
}