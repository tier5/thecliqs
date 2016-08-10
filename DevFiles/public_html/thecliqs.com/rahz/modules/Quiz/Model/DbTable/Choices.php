<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Choices.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Model_DbTable_Choices extends Engine_Db_Table
{
  protected $_rowClass = "Quiz_Model_Choice";

  public function deleteUserChoices($quiz_id, $user_id)
  {
    $select = $this->select()
      ->where('quiz_id = ?', $quiz_id)
      ->where('user_id = ?', $user_id);

    $choices = $this->fetchAll($select);

    foreach ($choices as $choice) {
      $choice->delete();
    }
  }
}