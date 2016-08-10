<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Create.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Chat_Form_Admin_Room_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create Chat Room')
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    // Add submit
    $this->addElement('Button', 'execute', array(
      'label' => 'Create Room',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
      'order' => 10000,
    ));

    // Add cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'onclick' => 'parent.Smoothbox.close();',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
      'order' => 10001,
    ));
  }
}