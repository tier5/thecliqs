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

class Page_Form_Packages_Delete extends Engine_Form
{
	public function init()
  {
    $this
    	->setTitle('Delete Package')
      ->setAttrib('class', 'global_form_popup')
      ->setMethod('post')
    ;

    $this->addElement('hidden', 'package_id');
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onClick' => 'parent.Smoothbox.close()',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');
  }
}
