<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       07.08.12
 * @time       10:50
 */
class Donation_Form_CreateCharity extends Engine_Form
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

  public function getPage()
  {
    if ($this->hasPage()) {
      return Engine_Api::_()->getItem('page', $this->getId());
    }
    return '';
  }

  public function hasPage()
  {
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page') &&
      isset($this->_id) &&
      !empty($this->_id) &&
      $this->_id != null &&
      Engine_Api::_()->getItem('page', $this->_id)
    ) {
      return true;
    }
    return false;
  }

  public function init()
  {
    $title = $this->_request->getActionName() == 'create' ? 'Create Charity' : 'Edit Charity';
    if ($this->hasPage()) {
      //$title .= '<a href="' . $this->getPage()->getHref() . '">' . $this->getPage()->getTitle() . "</a>";
    }
    $this->setTitle($title)
      ->setDescription('Compose your new donation entry below, then click "Post Donation Entry" to publish the entry to your donation entries.')
      ->setAttrib('name', 'donations_create')
      ->setAttrib('class', 'global_form group_form_upload')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('enctype', 'multipart/form-data');

    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'validator' => 'Alnum',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'autofocus' => 'autofocus',
    ));

    // Element: short description
    $this->addElement('Textarea', 'short_desc', array(
      'label' => 'Short Description',
      'maxlength' => '10000',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
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

    // Element: category
    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
    ));

    // Element : canSelect
    $this->addElement('Radio', 'can_choose_amount', array(
      'label' => 'Do you allow member to select the donation amount himself?',
      'multiOptions' => array(
        '0' => 'Yes, allow members to select the donation amount',
        '1' => 'No, select from predefined list ',
      ),
      'value' => '1',
      "onClick" => "switchSelectAmount()",
    ));

    $this->addElement('Text', 'predefine_list', array(
      'label' => 'List of predefined donation amounts',
      'value' => '5,10,20,50,100',
      'allowEmpty' => false
    ));


    // Element: Profile Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Profile Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    // Element: anonymous
    $this->addElement('Checkbox', 'allow_anonymous', array(
      'Label' => 'Allow anonymous donations? If donor select anonymous donation then his name and photo are hidden from public.',
      'value' => True
    ));

    // Element: Country
    $this->addElement('Text', 'country', array(
      'label' => 'Country',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: State
    $this->addElement('Text', 'state', array(
      'label' => 'State',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: City
    $this->addElement('Text', 'city', array(
      'label' => 'City',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: Street
    $this->addElement('Text', 'street', array(
      'label' => 'Street',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: Phone
    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    if ($this->hasPage()) {
      $page = $this->getPage();
      $this->phone->setValue($page->phone);
      $this->street->setValue($page->street);
      $this->city->setValue($page->city);
      $this->state->setValue($page->state);
      $this->country->setValue($page->country);
    }

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
      $this->submit->setLabel('Create Charity');
    } else {
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
