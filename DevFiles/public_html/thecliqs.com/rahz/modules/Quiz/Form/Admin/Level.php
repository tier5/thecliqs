<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("Quiz level settings.");
      
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);

    foreach ($user_levels as $user_level) {
      $levels_prepared[$user_level->level_id] = $user_level->getTitle();
    }

    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));
    
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Quizzes?',
      'description' => 'QUIZ_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow quizzes to be viewed.',
        1 => 'Yes, allow viewing of quizzes.',
        2 => 'Yes, allow viewing of all quizzes, even private ones.'
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'create', array(
      'label' => 'Allow Creation of Quizzes?',
      'description' => 'Do you want to allow members to create quizzes?',
      'multiOptions' => array(
        1 => 'Yes, allow this member level to create quizzes',
        0 => 'No, do not allow this member level to create quizzes',
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'take', array(
      'label' => 'Allow Taking of Quizzes?',
      'description' => 'Do you want to allow members to take quizzes?',
      'multiOptions' => array(
        1 => 'Yes, allow this member level to take quizzes',
        0 => 'No, do not allow this member level to take quizzes',
      ),
      'value' => 1,
    ));

    // PRIVACY ELEMENTS
    $this->addElement('MultiCheckbox', 'auth_view', array(
      'label' => 'Quiz Privacy',
      'description' => 'QUIZ_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
      'multiOptions' => array(
        'everyone' => 'Everyone',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me'
      ),
      'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
    ));

    $this->addElement('MultiCheckbox', 'auth_comment', array(
      'label' => 'Quiz Comment Options',
      'description' => 'QUIZ_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
      'multiOptions' => array(
        'everyone' => 'Everyone',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me'
      ),
      'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
    ));

    $this->addElement('Text', 'auth_html', array(
      'label' => 'HTML in Quizzes?',
      'description' => 'quiz_If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: b, img, a, embed, font',
      'value'=> 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}