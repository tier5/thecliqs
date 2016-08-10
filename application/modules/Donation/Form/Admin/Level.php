<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 24.08.12
 * Time: 16:51
 * To change this template use File | Settings | File Templates.
 */
class Donation_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('DONATION_Member Level Settings')
      ->setDescription("DONATION_FORM_ADMIN_LEVEL_DESCRIPTION");

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);

    $levels_prepared = array();
    foreach ($user_levels as $user_level) {
      if ($user_level->type == 'public') {
        continue;
      }

      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }

    $this->addElement('Select', 'level_id', array(
      'label' => 'DONATION_Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));

    $this->addElement('Radio', 'create_charity', array(
      'label' => 'DONATION_Allow Creation of Charities?',
      'description' => 'DONATION_Do you want to let members create charities?',
      'multiOptions' => array(
        1 => 'DONATION_Yes, allow creation of charities.',
        0 => 'DONATION_No, do not allow charities to be created.'
      ),
    ));

    $this->addElement('Radio', 'create_project', array(
      'label' => 'DONATION_Allow Creation of Projects?',
      'description' => 'DONATION_Do you want to let members create projects?',
      'multiOptions' => array(
        1 => 'DONATION_Yes, allow creation of projects.',
        0 => 'DONATION_No, do not allow projects to be created.'
      ),
    ));

    $this->addElement('Radio', 'raise_money', array(
      'label' => 'DONATION_Allow Raise Money for Donations?',
      'description' => 'DONATION_Do you want to let members raise money for donations?',
      'multiOptions' => array(
        1 => 'DONATION_Yes, allow raise money for donations.',
        0 => 'DONATION_No, do not allow donations to be raised.'
      ),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
