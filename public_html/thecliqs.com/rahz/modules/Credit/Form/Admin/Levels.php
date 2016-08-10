<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Levels.php 13.03.12 18:12 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Admin_Levels extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("Credit Level Settings");
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
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));

    // Element: view
    $this->addElement('Radio', 'view_credit_home', array(
      'label' => 'Allow Viewing of Credit Home?',
      'description' => 'Do you want to let members view Credit Home Page?',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of Credit Home, even private ones.',
        1 => 'Yes, allow viewing of Credit Home.',
        0 => 'No, do not allow Credit Home to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    // Element: view
    $this->addElement('Radio', 'view_credit_faq', array(
      'label' => 'Allow Viewing of Credit FAQ?',
      'description' => 'Do you want to let members view Credit FAQ Page?',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of Credit FAQ, even private ones.',
        1 => 'Yes, allow viewing of Credit FAQ.',
        0 => 'No, do not allow Credit FAQ to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    $this->addElement('Radio', 'credits', array(
      'label' => 'Allow Credits?',
      'description' => 'CREDIT_FORM_ADMIN_LEVEL_ALLOW_CREDIT_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow',
        1 => 'Yes, allow.',
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'transfer', array(
      'label' => 'Allow Transfer?',
      'description' => 'CREDIT_FORM_ADMIN_LEVEL_ALLOW_TRANSFER_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow',
        1 => 'Yes, allow.',
      ),
      'value' => 1,
    ));

    $this->addElement('Text', 'max_send', array(
      'label' => 'Max Credits Send',
      'description' => 'CREDIT_FORM_ADMIN_LEVEL_MAX_SEND_DESCRIPTION',
      'value' => 1500
    ));

    $this->addElement('Text', 'max_received', array(
      'label' => 'Max Credits Receive',
      'description' => 'CREDIT_FORM_ADMIN_LEVEL_MAX_RECEIVED_DESCRIPTION',
      'value' => 1500
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
