<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Album.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'classified';

  protected $_owner_type = 'classified';

  protected $_children_types = array('classified_photo');

  protected $_collectible_type = 'classified_photo';

  public function getHref($params = array())
  {
    return $this->getClassified()->getHref($params);
  }

  public function getClassified()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('classified');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('classified_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $classifiedPhoto ) {
      $classifiedPhoto->delete();
    }

    parent::_delete();
  }
}