<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Request.php 5/9/12 1:18 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Statistics_Request extends Engine_Form
{
  protected $_params;

  /**
   * @param array $params
   */
  public function setParams(array $params)
  {
    $this->_params = $params;
  }

  public function init()
  {
    parent::init();
    $this
      ->setAttrib('class', 'global_form_popup')
      ->loadDefaultDecorators();

    $this->addElement('text', 'request_amt', array(
      'Label'       => 'Request Amount',
      'required'    => true,
      'allowEmpty'  => false,
      'validators'  => array(
        'Between' => new Zend_Validate_Between((double)$this->getParam('allowed'), (double)$this->getParam('current')),
      )
    ));

    $this->addElement('textarea', 'request_message', array(
      'Label' => 'Request Message'
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label'      => 'Request',
      'type'       => 'submit',
      'ignore'     => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label'       => 'cancel',
      'link'        => true,
      'prependText' => ' or ',
      'href'        => '',
      'onclick'     => 'parent.Smoothbox.close();',
      'decorators'  => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function getParam($key)
  {
    if (isset($this->_params[$key]))
      return $this->_params[$key];

    return null;
  }
}
