<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: ItemDelete.php minhnc $
 * @author     MinhNC
 */
class Ynmobile_Form_Admin_Menu_ItemDelete extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Delete Menu Item')
      ->setAttrib('class', 'global_form_popup')
      ;

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Menu Item',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}