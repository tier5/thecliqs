<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       13.08.12
 * @time       16:22
 */
class Donation_Form_CreateFundraise extends Engine_Form
{
  protected $_id;
  protected $_request;

  public function setRequest($request)
  {
    $this->_request = $request;
    return $this;
  }

  public function getRequest()
  {
    return $this->_request;
  }

  public function setId($id)
  {
    $this->_id = $id;
    return $this;
  }

  public function getId()
  {
    return $this->_id;
  }


  public function init()
  {
    $title = $this->_request->getActionName() == 'create' ? 'Create Fundraising' : 'Edit Fundraising';
    $donation = Engine_Api::_()->getItem('donation', $this->getId());
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->setTitle($title)
      ->setDescription('You\'re in process of fundraising page creation for <a href="'. $donation->getHref(). '">' . $donation->getTitle() . '</a> ' . $donation->type .
      ".<br/>It is to create your fudraising page - just type title? your story and upload photos(optional):")
      ->setAttrib('name', 'donations_create')
      ->setAttrib('class', 'global_form group_form_upload')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('enctype', 'multipart/form-data');


    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'autofocus' => 'autofocus',
    ));

    // Element: description (HTML)
    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'allowEmpty' => false,
      'required' => true,
    ));

    $params = array(
      'mode' => 'exact',
      'elements' => 'description',
      'width' => '500px',
      'height' => '250px',
      'theme_advanced_buttons1' => array(
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
        'bullist', 'numlist', '|',
        'undo', 'redo', '|',
        'sub', 'sup', '|',
        'forecolor', 'forecolorpicker', 'backcolor', 'backcolorpicker', '|'
      ),
      'theme_advanced_buttons2' => array(
        'newdocument', 'code', 'image', 'media', 'preview', 'fullscreen', '|',
        'link', 'unlink', 'anchor', 'charmap', 'cleanup', 'hr', 'removeformat', 'blockquote', 'separator', 'outdent', 'indent', '|',
        'selectall', 'advimage'),
      'theme_advanced_buttons3' => array('formatselect', 'fontselect', 'fontsizeselect', 'styleselectchar', '|', 'table', '|'),
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

    // expiry_date
    $expiry_date = new Engine_Form_Element_CalendarDateTime('expiry_date');
    $expiry_date->setLabel("DONATION_expiry_date")
      ->setAllowEmpty(true)
      ->setDescription('DONATION_EXPIRY_DATE_DESC');
    $this->addElement($expiry_date);
    $expiry_date->getDecorator('Description')->setOption('escape', false);

    // Element: target
    $this->addElement('Text', 'target_sum', array(
      'label' => 'DONATION_target_sum',
      'validators' => array(
        array('Float', true),
      ),
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
      ),
      'value' => 0
    ));

    // Element: min amount
    $this->addElement('Text', 'min_amount', array(
      'label' => 'Minimal Amount',
      'allowEmpty' => false,
      'value' => $settings->getSetting('donation.minimal.amount', 0.15),
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast($settings->getSetting('donation.minimal.amount', 0.15)),
      ),
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        //new Zend_Validate_Callback('is_numeric')
      ),
    ));

    // Element : canSelect
    $this->addElement('Radio', 'can_choose_amount', array(
      'label' => 'Do you allow member to select the donation amount himself?',
      'multiOptions' => array(
        '0' => 'Yes, allow members to select the donation amount',
        '1' => 'No, select from predefined list',
      ),
      'value' => '1',
      "onClick" => "switchSelectAmount()",
    ));

    $this->addElement('Text', 'predefine_list', array(
      'label' => 'List of predefined donation amounts',
      'value' => '5,10,20,50,100'
    ));

    // Element: anonymous
    $this->addElement('Checkbox', 'allow_anonymous', array(
      'Label' => 'Allow anonymous donations? If donor select anonymous donation then his name and photo are hidden from public.',
      'value' => True
    ));

    // Element : Fancy Upload
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->setLabel('Photos')
      ->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
      'viewScript' => '_FancyUpload.tpl',
      'placement' => '',
    ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    if ($this->_request->getActionName() == 'create') {
      $this->submit->setLabel('Create Fundraising');
    } else
    {
      $this->submit->setLabel('Save Changes');
    }

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }

}
