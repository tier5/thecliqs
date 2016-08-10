<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:52 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Widget_MostTakenController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $element = $this->getElement();
    $element->setTitle('');

    $table = Engine_Api::_()->getDbtable('quizs', 'quiz');
    $select = $table->select()
      ->where('published = ?', 1)
      ->where('approved = ?', 1)
      ->order('take_count DESC')->limit(5);

    $this->view->quizes = $table->fetchAll($select);

    if (!$this->view->quizes || $this->view->quizes->count() == 0) {
      return $this->setNoRender();
    }
  }
}