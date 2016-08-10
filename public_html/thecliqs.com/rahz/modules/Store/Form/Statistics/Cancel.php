<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Delete.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Statistics_Cancel extends Engine_Form
{
  /**
   * @var $_request Store_Model_Request
   */
  protected $_request;

  public function setRequest( Store_Model_Request $request)
  {
    $this->_request = $request;
  }

  public function init()
  {
    $this->setTitle('Cancel Request')
      ->setDescription('Are you sure you want to cancel this request?')
      ->setAttrib('class', 'global_form_popup')
      ->setMethod('POST');
      ;

    $this->addElement('hidden', 'request_id', array(
      'value' => $this->_request->getIdentity(),
      'required' => true,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Cancel Request',
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
    $button_group = $this->getDisplayGroup('buttons');
  }
}