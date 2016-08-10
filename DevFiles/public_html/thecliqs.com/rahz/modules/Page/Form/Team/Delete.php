<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Delete.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Team_Delete extends Engine_Form
{
	public function init()
  {
    $this
    	->setTitle('Delete Page Admin')
      ->setMethod('post')
      ->setDescription('Are you sure you want to delete page team?')
      ->setAttrib('class', 'global_form_popup');

    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
    ));
    
    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons', array(
      'decorators' => array(
        'FormElements'
      )
    ));
  }
}