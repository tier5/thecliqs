<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Result.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Model_Result extends Core_Model_Item_Abstract
{
  public function getTable ()
  {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('results', 'quiz');
    }
    return $this->_table;
  }
  
  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Quiz_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'quiz',
      'parent_id' => $this->quiz_id
    );
    
    // Delete old images
    $this->deletePhoto();
    
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
      ->resize(140, 140)
      ->write($path . '/in_' . $name)
      ->destroy();

    // Store
    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iIconNormal = $storage->create($path . '/in_' . $name, $params);

    $iMain->bridge($iIconNormal, 'thumb.normal');

    // Remove temp files
    @unlink($path . '/m_' . $name);
    @unlink($path . '/in_' . $name);

    // Update row
    $this->photo_id = $iMain->file_id;
    $this->save();

    return $this;
  }
  
  public function deletePhoto() 
  {
    if (!$this->photo_id) {
      return;
    }
    
    $storage = Engine_Api::_()->storage();
    
    $file = $storage->get($this->photo_id);
    $file->remove();
    $file = $storage->get($this->photo_id, 'thumb.normal');
    $file->remove();
  }
  
  public function deleteAnswers() 
  {
    $table = Engine_Api::_()->getDbtable('answers', 'quiz');
    $select = $table->select()
      ->where('result_id = ?', $this->getIdentity());

    $answers = $table->fetchAll($select);
    
    foreach ($answers as $answer) {
      $answer->delete();
    }
  }

  public function deleteTakes()
  {
    $table = Engine_Api::_()->getDbtable('takes', 'quiz');
    $select = $table->select()
      ->where('result_id = ?', $this->getIdentity());

    $takes = $table->fetchAll($select);

    foreach ($takes as $take) {
      $take->delete();
    }
  }
  
  public function delete() 
  {
    // Delete photo
    $this->deletePhoto();
    // Delete answers
    $this->deleteAnswers();
    // Delete takes
    $this->deleteTakes();
    
    parent::delete();
  } 
}