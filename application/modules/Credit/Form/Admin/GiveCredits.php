<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GiveCredits.php 11.01.12 18:02 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Admin_GiveCredits extends Engine_Form
{
  public function init()
  {
    $this->setAttribs(
      array(
        'class' => 'global_form_box credit_settings_form'
      )
    );

    $this->setTitle('Give Mass Credits')
      ->setDescription('CREDIT_GIVE_MASS_CREDITS_DESC');

    // all users, members levels, networks, specific users (may be one user)
    $give_to = array(
      '' => ' ',
      'all_users' => 'All Users',
      'levels' => 'by Level',
      'networks' => 'by Network',
      'spec_users' => 'Specific Users'
    );

    $this->addElement('Select', 'users', array(
      'label' => 'Present to: ',
      'multiOptions' => $give_to,
      'onchange' => 'javascript:switchType(this.value)',
      'allowEmpty' => false,
      'required' => true
    ));


    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);
    $levels_prepared = array('' => 'All Levels');
    foreach ($user_levels as $user_level) {
      if ($user_level->type == 'public') {
        continue;
      }

      $levels_prepared[$user_level->level_id] = $user_level->getTitle();
    }

    $this->addElement('Select', 'levels', array(
      'label' => 'Member Levels:',
      'multiOptions' => $levels_prepared
    ));


    // prepare user networks
    $table = Engine_Api::_()->getDbTable('networks', 'network');
    $select = $table->select();
    $user_networks = $table->fetchAll($select);
    $networks_prepared = array('' => 'All Networks');
    foreach ($user_networks as $user_network) {
      $networks_prepared[$user_network->network_id] = $user_network->getTitle();
    }

    $this->addElement('Select', 'networks', array(
      'label' => 'Networks:',
      'multiOptions' => $networks_prepared
    ));

    // prepare specific users
    $this->addElement('Text', 'spec_users', array(
      'label' => 'Specific Users (or user)',
      'description' => 'start typing...',
      'autocomplete' => 'off',
      'filters' => array(
        new Engine_Filter_Censor(),
      )
    ))->getElement('spec_users')->getDecorator("Description")->setOption("placement", "append");
    $this->addElement('Hidden', 'user_ids');

    // amount of credits
    $this->addElement('Text', 'credit', array(
      'label' => 'Credit',
      'description' => 'How much do you want to give this users',
      'allowEmpty' => false,
      'required' => true,
      'class' => 'admin_give_credits_amount_input',
      'validators' => array(
        array('Int', true),
      ),
    ))->getElement('credit')->getDecorator("Description")->setOption("placement", "append");

    // set fixed points to users
    $this->addElement('Checkbox', 'set_default', array(
      'label' => 'Set default credits to users witch chose'
    ));

    $this->addDisplayGroup(
      array('users', 'levels', 'networks', 'spec_users', 'credit', 'set_default'),
      'giving',
      array('class' => 'he_setting_fieldset', 'legend' => 'Giving Credits to Users')
    );


    $this->addElement('Radio', 'send', array(
      'label' => 'Send What?',
      'description' => 'You can send message or send notification or none of them',
      'multiOptions' => array(
        2 => 'Message',
        1 => 'Notification',
        0 => 'None'
      ),
      'value' => 0,
      'onclick' => 'javascript:showOrNot(this.value)'
    ))->getElement('send')->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Text', 'subject', array(
      'label' => 'Subject',
    ));

    $this->addElement('Textarea', 'message', array(
      'label' => 'Message',
      'value' => Zend_Registry::get('Zend_Translate')->_('CREDIT_Send or set credits message')
    ));

    $this->addDisplayGroup(
      array('send', 'admins', 'subject', 'message'),
      'send_message',
      array('class' => 'he_setting_fieldset', 'legend' => 'Sending mass message to chose users')
    );

    $this->addElement('Button', 'submit', array(
      'label' => 'Send Credits',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
