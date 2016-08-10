<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Suggest.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Model_Recommendation extends Core_Model_Item_Abstract
{
  protected $_type = 'suggest_recommendation';
  public $owner;

  public function getOwner()
  {
    return Engine_Api::_()->getItem('suggest', $this->recommendation_id);
    //return $this->owner ? $this->owner : Engine_Api::_()->getItem('user', $this->user_id);
  }
}