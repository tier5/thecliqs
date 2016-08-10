<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Delete.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Quiz')
      ->setDescription('Are you sure you want to delete this quiz?');

    $this->addElement('Hash', 'token');

    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Quiz',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      )
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons', array(
      'decorators' => array(
        'FormElements'
      )
    ));

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
  }
}