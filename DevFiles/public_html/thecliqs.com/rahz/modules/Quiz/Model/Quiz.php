<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Quiz.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Model_Quiz extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';

  protected $_owner_type = 'user';

  protected $_searchColumns = array('title', 'description');

  // General
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this->getTitle()))), '-');

    $params = array_merge(array('route' => 'quiz_view', 'reset' => true, 'quiz_id' => $this->quiz_id, 'slug' => $slug), $params);

    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }
    
  public function getDescription($truncate = false, $length = 50)
  {
    // @todo decide how we want to handle multibyte string functions
    $tmpDescription = Engine_String::strip_tags($this->description);

    if ($truncate) {
      return $this->truncate($tmpDescription, $length, "...");
    }

    return (Engine_String::strlen($tmpDescription) > 255
      ? Engine_String::substr($tmpDescription, 0, 255) . '...'
      : $tmpDescription);
  }

  public function getKeywords($separator = ' ')
  {
    $keywords = array();

    foreach ($this->tags()->getTagMaps() as $tagmap) {
      $tag = $tagmap->getTag();
      $keywords[] = $tag->getTitle();
    }

    if (null === $separator) {
      return $keywords;
    }

    return join($separator, $keywords);
  }

  // Interfaces
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  
  public function getParent($type = 'user')
  {
    return $this->getOwner($type);
  }
    
  public function setPhoto($photo)
  {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    } else {
      throw new Quiz_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'quiz',
      'parent_id' => $this->getIdentity()
    );
    
    // Save
    $storage = Engine_Api::_()->storage();
    
    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path . '/m_' . $name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path . '/in_' . $name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path . '/p_' . $name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path . '/is_' . $name)
      ->destroy();

    // Store
    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iProfile = $storage->create($path. '/p_' . $name, $params);
    $iIconNormal = $storage->create($path . '/in_' . $name, $params);
    $iSquare = $storage->create($path . '/is_' . $name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($path . '/p_' . $name);
    @unlink($path . '/m_' . $name);
    @unlink($path . '/in_' . $name);
    @unlink($path . '/is_' . $name);

    // Update row
    $this->modified_date = new Zend_Db_Expr('NOW()');
    $this->photo_id = $iMain->file_id;
    $this->save();

    return $this;
  }

  public function getResultList()
  {
    $table = Engine_Api::_()->getDbtable('results', 'quiz');
    $select = $table->select()
      ->where('quiz_id = ?', $this->getIdentity())
      ->order('result_id ASC');

    return $table->fetchAll($select);
  }

  public function getQuestionList($fetch_answers = false)
  {
    $table = Engine_Api::_()->getDbtable('questions', 'quiz');
    $select = $table->select()
      ->where('quiz_id = ?', $this->getIdentity())
      ->order('question_id ASC');
    
    $questions = $table->fetchAll($select);

    if ($fetch_answers == false) {
      return $questions;
    }

    // array of pointers
    $answer_list = array();
    
    $question_ids = array();
    
    foreach ($questions as $question) {
      $question_id = $question->getIdentity();
      $question_ids[] = $question_id;
      // collect pointers
      $answer_list[$question_id] = &$question->answers;
    }

    if (empty($question_ids)) {
      return $questions;
    }
    
    $table = Engine_Api::_()->getDbtable('answers', 'quiz');
    $select = $table->select()
      ->where('question_id IN (?)', $question_ids)
      ->order('answer_id ASC');

    $answers = $table->fetchAll($select);

    foreach ($answers as $answer) {
      $answer_list[$answer->question_id][] = $answer;
    }
    
    return $questions;
  }

  public function deletePhoto() 
  {
    if (!$this->photo_id) {
      return;
    }
    
    $storage = Engine_Api::_()->storage();
    
    $file = $storage->get($this->photo_id);
    $file->remove();
    $file = $storage->get($this->photo_id, 'thumb.profile');
    $file->remove();
    $file = $storage->get($this->photo_id, 'thumb.normal');
    $file->remove();
    $file = $storage->get($this->photo_id, 'thumb.icon');
    $file->remove();
  }
  
  public function deleteResults() 
  {
    $table = Engine_Api::_()->getDbtable('results', 'quiz');
    $select = $table->select()
      ->where('quiz_id = ?', $this->getIdentity());

    $results = $table->fetchAll($select);
    
    foreach ($results as $result) {
      $result->delete();
    }
  }
  
  public function deleteQuestions() 
  {
    $table = Engine_Api::_()->getDbtable('questions', 'quiz');
    $select = $table->select()
      ->where('quiz_id = ?', $this->getIdentity());

    $questions = $table->fetchAll($select);
    
    foreach ($questions as $question) {
      $question->delete();
    }
  }
  
  public function _delete()
  {
    // Delete photo
    $this->deletePhoto();
    // Delete results
    $this->deleteResults();
    // Delete questions
    $this->deleteQuestions();
    
    parent::_delete();
  }
  
  public function getQuizStatus() 
  {
    $quiz_info = array('result_count' => 0, 'question_count' => 0, 'answer_count' => 0);
        
    $resultTable = Engine_Api::_()->getDbtable('results', 'quiz');
    $select = $resultTable->select()
      ->from($resultTable->info('name'), array('result_id'))
      ->where("quiz_id = ?", $this->getIdentity());

    $result_ids = $resultTable->getAdapter()->fetchCol($select);
    $quiz_info['result_count'] = count($result_ids);

    if ($quiz_info['result_count'] == 0) {
      return $quiz_info;
    }

    $questionTable = Engine_Api::_()->getDbtable('questions', 'quiz');
    $select = $questionTable->select()
      ->from($questionTable->info('name'), array('question_id'))
      ->where("quiz_id = ?", $this->getIdentity());
    
    $question_ids = $questionTable->getAdapter()->fetchCol($select);
    $quiz_info['question_count'] = count($question_ids);
    
    if ($quiz_info['question_count'] == 0) {
      return $quiz_info;
    }

    $table = Engine_Api::_()->getDbtable('answers', 'quiz');
    $select = $table->select()
      ->from($table->info('name'), array('answer_count' => new Zend_Db_Expr('COUNT(answer_id)')))
      ->where("result_id IN (?)", $result_ids, 'INTEGER')
      ->where("question_id IN (?)", $question_ids, 'INTEGER');
       
    $quiz_info['answer_count'] = $table->getAdapter()->fetchOne($select);
    
    return $quiz_info;
  }
  
  public function fetchTakeResult($answer_ids) 
  {
    if (!$answer_ids) {
      return 0;
    }
    
    $table = Engine_Api::_()->getDbtable('answers', 'quiz');
    $select = $table->select()
      ->from($table->info('name'), array('result_id' => 'result_id', 'result_count' => new Zend_Db_Expr('COUNT(result_id)')))
      ->where("answer_id in (?)", $answer_ids, 'INTEGER')
      ->group("result_id")
      ->order(array("result_count DESC"))
      ->limit(1);

    return $table->getAdapter()->fetchOne($select);
  }
  
  public function getUserResult($user_id) 
  {
    $table = Engine_Api::_()->getDbtable('takes', 'quiz');
    $select = $table->select()
      ->where("quiz_id = ?", $this->getIdentity(), 'INTEGER')
      ->where("user_id = ?", $user_id, 'INTEGER');

    return $table->fetchRow($select);
  }

  public function getTakerList()
  {
    $table = Engine_Api::_()->getDbtable('takes', 'quiz');
    $select = $table->select()
      ->where("quiz_id = ?", $this->getIdentity(), 'INTEGER');

    return $table->fetchAll($select);
  }

  public function getCategory()
  {
    return Engine_Api::_()->getDbtable('categories', 'quiz')->find($this->category_id)->current();
  }

  public function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
  {
    if ($length == 0) {
      return '';
    }

    if (Engine_String::strlen($string) > $length) {
      $length -= Engine_String::strlen($etc);

      if (!$break_words && !$middle) {
        $string = preg_replace('/\s+?(\S+)?$/', '', Engine_String::substr($string, 0, $length+1));
      }

      if (!$middle) {
        return Engine_String::substr($string, 0, $length).$etc;
      } else {
        return Engine_String::substr($string, 0, $length/2) . $etc . Engine_String::substr($string, -$length/2);
      }
    } else {
      return $string;
    }
  }

  public function userMatches($user_id, $level = 1)
  {
    $choiceTable = Engine_Api::_()->getDbtable('choices', 'quiz');
    $userTable = Engine_Api::_()->getItemTable('user');
    $choicesTable = $choiceTable->info('name');
    $usersTable = $userTable->info('name');

    $select = $choiceTable->select()
      ->from($choicesTable, array('answer_id'))
      ->where('quiz_id = ?', $this->getIdentity())
      ->where('user_id = ?', $user_id);

    $answer_ids = $choiceTable->getAdapter()->fetchCol($select);
    
    $select = $userTable->select()
      ->setIntegrityCheck(false)
      ->from($usersTable, array('user_id', 'email', 'username', 'displayname', 'photo_id'))
      ->join($choicesTable, "$choicesTable.user_id = $usersTable.user_id", array('choice_count' => new Zend_Db_Expr('COUNT(*)')))
      ->where("$choicesTable.quiz_id = ?", $this->getIdentity())
      ->where("$choicesTable.user_id != ?", $user_id)
      ->where("$choicesTable.answer_id IN (?)", $answer_ids)
      ->group("$choicesTable.user_id")
      ->order("choice_count");

    if ($level == 1) {
      $select->having('choice_count > ?', round(count($answer_ids)/2));
    } else {
      $select->having('choice_count >= 1 AND choice_count <= ?', round(count($answer_ids)/2));
    }

    $users = $userTable->fetchAll($select);

    $user_list = array();
    foreach ($users as $user) {
      $user_list[] = array(
        'username' => $user->getTitle(),
        'url' => $user->getHref(),
        'photo' => $user->getPhotoUrl('thumb.icon'),
        'choice_count' => $user->choice_count
      );
    }

    return array('count' => $users->count(), 'users' => $user_list);
  }

  public function isCompleted()
  {
    $quizStatus = $this->getQuizStatus();

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $minResultCount = $settings->getSetting('quizzes.min.result.count', 2);
    $minQuestionCount = $settings->getSetting('quizzes.min.question.count', 1);

    if ($quizStatus['result_count'] < $minResultCount) {
      return false;
    } elseif ($quizStatus['question_count'] < $minQuestionCount) {
      return false;
    } elseif ($quizStatus['result_count'] * $quizStatus['question_count'] > $quizStatus['answer_count']) {
      return false;
    }

    return true;
  }
}