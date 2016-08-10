<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Answer.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Model_Answer extends Core_Model_Item_Abstract
{
  // Properties
  // General
  public function getTable ()
  {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('answers', 'quiz');
    }
    return $this->_table;
  }
}