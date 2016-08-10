<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Take.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Model_Take extends Core_Model_Item_Abstract
{
  public function getTable()
  {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('takes', 'quiz');
    }

    return $this->_table;
  }
}