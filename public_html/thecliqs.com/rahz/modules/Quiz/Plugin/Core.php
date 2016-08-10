<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Plugin_Core
{
  public function onStatistics($event)
  {
    $table = Engine_Api::_()->getDbTable('quizs', 'quiz');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('COUNT(*) AS count'))
      ->where('published = 1');
    
    $count = $table->getAdapter()->fetchOne($select);
    $event->addResponse($count, 'quiz');
  }

  public function onUserDeleteBefore($quiz)
  {
    $payload = $quiz->getPayload();

    if ($payload instanceof User_Model_User) {
      // Delete quizzes
      $quizTable = Engine_Api::_()->getDbtable('quizs', 'quiz');
      $quizSelect = $quizTable->select()->where('user_id = ?', $payload->getIdentity());

      foreach ($quizTable->fetchAll($quizSelect) as $quiz) {
        $quiz->delete();
      }

      // Delete results
      $takeTable = Engine_Api::_()->getDbtable('takes', 'quiz');
      $takeSelect = $takeTable->select()->where('user_id = ?', $payload->getIdentity());

      foreach ($takeTable->fetchAll($takeSelect) as $took) {
        $took->delete();
      }      
    }
  }
}