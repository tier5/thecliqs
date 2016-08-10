<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2010-07-02 19:44 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Quizzes',
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'quiz_Browse By',
      'multiOptions' => array(
        'creation_date' => 'quiz_Most Recent',
        'view_count' => 'quiz_Most Viewed',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'show', array(
      'label' => 'quiz_Show',
      'multiOptions' => array(
        '1' => 'Everyone\'s Quizzes',
        '2' => 'Only My Friends\' Quizzes',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'publish', array(
      'label' => 'quiz_Published',
      'multiOptions' => array(
        '' => 'All Quizzes',
        '1' => 'quiz_Published',
        '0' => 'quiz_UnPublished',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'category', array(
      'label' => 'quiz_Category',
      'multiOptions' => array(
        '0' => 'quiz_All Categories',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 1
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => 2
    ));

    $this->addElement('Hidden', 'start_date', array(
      'order' => 3
    ));

    $this->addElement('Hidden', 'end_date', array(
      'order' => 4
    ));
  }
}