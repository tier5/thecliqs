<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-08-31 16:05 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Admin_Package_Create extends Engine_Form {

  public function init()
  {
    $this
      ->setTitle('PACKAGE_EDITCREATE_FORM_TITLE')
      ->setDescription('PACKAGE_EDITCREATE_FORM_DESCRIPTION');

    // Element: title
    $this->addElement('Text', 'name', array(
      'label' => 'PACKAGE_EDITCREATE_FORM_NAME_TITLE',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        'StringTrim',
      ),
    ));

    // Element: description
    $this->addElement('Textarea', 'description', array(
      'label' => 'PACKAGE_EDITCREATE_FORM_DESCRITPION_TITLE',
      'validators' => array(
        array('StringLength', true, array(0, 250)),
      )
    ));

    // Element: price
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency');
    $this->addElement('Text', 'price', array(
      'label' => 'PACKAGE_EDITCREATE_FORM_PRICE_TITLE',
      'description' => 'PACKAGE_EDITCREATE_FORM_PRICE_DESCRIPTION',
      'allowEmpty' => false,
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0),
      ),
      'value' => '0.00',
    ));

    // Element: recurrence
    $this->addElement('Duration', 'recurrence', array(
      'label' => 'Billing Cycle',
      'description' => 'How often should pages in this package be billed?',
      'required' => true,
      'allowEmpty' => false,
      'value' => array('0', 'forever'),
    ));

    // Element: duration
    $this->addElement('Duration', 'duration', array(
      'label' => 'Billing Duration',
      'description' => 'When should this plan expire? For one-time ' .
        'plans, the plan will expire after the period of time set here. For ' .
        'recurring plans, the page will be billed at the above billing cycle ' .
        'for the period of time specified here.',
      'required' => true,
      'allowEmpty' => false,
      'value' => array('0', 'forever'),
    ));

    // auto aprove
    $this->addElement('Checkbox', 'autoapprove', array(
      'description' => "PACKAGE_EDITCREATE_FORM_AUTOAPPROVED_TITLE",
      'label' => 'PACKAGE_EDITCREATE_FORM_AUTOAPPROVED_DESCRIPTION',
      'value' => 0,
    ));

    // Element:sponsored
    $this->addElement('Checkbox', 'sponsored', array(
      'description' => "PACKAGE_EDITCREATE_FORM_SPONSORED_TITLE",
      'label' => 'PACKAGE_EDITCREATE_FORM_SPONSORED_DESCRIPTION',
      'value' => 0,
    ));

    // Element:featured
    $this->addElement('Checkbox', 'featured', array(
      'description' => "PACKAGE_EDITCREATE_FORM_FEATURED_TITLE",
      'label' => 'PACKAGE_EDITCREATE_FORM_FEATURED_DESCRIPTION',
      'value' => 0,
    ));


    $this->addElement('Radio', 'edit_columns', array(
      'label' => 'PAGE_ALLOW_COLS_EDIT_FORM_TITLE',
      'description' => 'PAGE_ALLOW_COLS_EDIT_FORM_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow edit columns.',
        1 => 'Yes, allow edit columns.'
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'edit_layout', array(
      'label' => 'PAGE_ALLOW_LAYOUT_FORM_TITLE',
      'description' => 'PAGE_ALLOW_LAYOUT_FORM_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow layout editor.',
        1 => 'Yes, allow layout editor.'
      ),
      'value' => 1,
    ));

    // Element:modules
    $multiOptions = Engine_Api::_()->getDbtable('modules', 'page')->getAvailableModules();

    $this->addElement('MultiCheckbox', 'modules', array(
      'label' => 'Page Features/Apps',
      'description' => 'Your members can choose from any of the features/apps checked below when they decide who can use its on their pages.',
      'multiOptions' => $multiOptions,
      'value' => array('pagealbum', 'pageblog', 'pagediscussion', 'pagedocument', 'pageevent', 'pagemusic', 'pagevideo', 'rate', 'pagecontact', 'store', 'pagefaq', 'donation', 'offers')
    ));

    $this->addElement('MultiCheckbox', 'auth_view', array(
      'label' => 'Page Privacy',
      'description' => 'Your members can choose from any of the options checked below when they decide who can see their pages.',
      'multiOptions' => array(
        'everyone' => 'Everyone',
        'registered' => 'Registered',
        'likes' => 'Fans',
        'team' => 'Team'
      ),
      'value' => array('everyone', 'registered', 'likes', 'team')
    ));

    $this->addElement('MultiCheckbox', 'auth_comment', array(
      'label' => 'Page Comment Options',
      'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their pages.',
      'multiOptions' => array(
        'registered' => 'Registered',
        'likes' => 'Fans',
        'team' => 'Team'
      ),
      'value' => array('registered', 'likes', 'team')
    ));

    $this->addElement('MultiCheckbox', 'auth_posting', array(
      'label' => 'Page Posting Options',
      'description' => 'Your members can choose from any of the options checked below when they decide who can post content on their pages.',
      'multiOptions' => array(
        'registered' => 'Registered',
        'likes' => 'Fans',
        'team' => 'Team'
      ),
      'value' => array('registered', 'likes', 'team')
    ));

    // Element: style
    $this->addElement('Radio', 'style', array(
      'label' => 'Allow Custom CSS Styles?',
      'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their pages by altering their CSS styles.',
      'multiOptions' => array(
        1 => 'Yes, enable custom CSS styles.',
        0 => 'No, disable custom CSS styles.',
      ),
      'value' => 1,
    ));

    // Element: execute
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Package',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'package_id' => null)),
      'decorators' => array('ViewHelper'),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      )
    ));
  }

  public function onShow()
  {
    // Get supported billing cycles
    $gateways = array();
    $supportedBillingCycles = array();
    $partiallySupportedBillingCycles = array();
    $fullySupportedBillingCycles = null;
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    foreach ($gatewaysTable->fetchAll() as $gateway) {
      $gateways[$gateway->gateway_id] = $gateway;
      $supportedBillingCycles[$gateway->gateway_id] = $gateway->getGateway()->getSupportedBillingCycles();
      $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
      if (null == $fullySupportedBillingCycles) {
        $fullySupportedBillingCycles = $supportedBillingCycles[$gateway->gateway_id];
      } else {
        $fullySupportedBillingCycles = array_intersect($fullySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
      }
    }

    $multiOptions = array_combine(array_map('strtolower', $fullySupportedBillingCycles), $fullySupportedBillingCycles);

    $this->getElement('recurrence')->setMultiOptions($multiOptions);
    $this->getElement('recurrence')->options['forever'] = 'One-time';
  }

	/**
	 * @param array $values
	 * @return array
	 */
	public function onEdit($values = array())
	{
		//Recurrence
		$values['recurrence'] = array($values['recurrence'], $values['recurrence_type']);

		//Duration
    $values['duration'] = array($values['duration'] , $values['duration_type']);

		return $values;
	}

	/**
	 * @param array $values
	 * @return array
	 */
  public function onSave($values = array())
  {
    //Recurrence
    if (!empty($values['recurrence'])) {
      $tmp = $values['recurrence'];
      unset($values['recurrence']);
      if (empty($tmp) || !is_array($tmp)) {
        $tmp = array(null, null);
      }
      $values['recurrence'] = (int)$tmp[0];
      $values['recurrence_type'] = $tmp[1];
    }

    //Duration
    if (!empty($values['duration'])) {
      $tmp = $values['duration'];
      unset($values['duration']);
      if (empty($tmp) || !is_array($tmp)) {
        $tmp = array(null, null);
      }
      $values['duration'] = (int)$tmp[0];
      $values['duration_type'] = $tmp[1];
    }

    $values['title'] = $values['name'];
    return $values;
  }
}