<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvmessages_Form_Admin_Levels extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Member Levels Settings')
      ->setDescription('HEADVMESSAGES_Form Admin Levels Description');

    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);
    
    foreach ($user_levels as $user_level){
      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }
    
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));

    $this->addElement('Radio', 'use', array(
      'label' => 'HEADVMESSAGES_Allow using advanced messaging?',
      /*'description' => 'HEADVMESSAGES_Allow messaging description',*/
      'multiOptions' => array(
        0 => 'HEADVMESSAGES_No, do not allow use advanced messages.',
        1 => 'HEADVMESSAGES_Yes, allow use advanced messages.',
      ),
      'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));

  }
}