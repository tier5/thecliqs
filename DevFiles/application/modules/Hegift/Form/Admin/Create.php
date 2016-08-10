<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Create.php 04.02.12 12:51 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create New Gift')
      ->setDescription('HEGIFT_NEW_GIFT_DESC')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('class', 'global_form_box hegift_create_gift_form')
      ->setAttrib('name', 'gift_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Text', 'title', array(
      'label' => 'Gift Title',
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Text', 'credits', array(
      'label' => 'Credit',
      'description' => 'HEGIFT_Gift will be free if you types zero',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(-1)),
      ),
      'value' => 0
    ))->getElement('credits')->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Select', 'limit', array(
      'label' => 'Gift Limit',
      'description' => 'HEGIFT_Choose what kind of gift want to create: limitly or ...?',
      'multiOptions' => array(
        0 => 'HEGIFT_unlimit',
        1 => 'HEGIFT_limit'
      ),
      'onchange' => "updateAmountField()",
      'value' => 0
    ));

    $this->addElement('Text', 'amount', array(
      'description' => 'HEGIFT_Maximum amount is 100 and minimum is 1, you can leave empty',
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
        array('LessThan', true, 101)
      )
    ))->getElement('amount')->getDecorator("Description")->setOption("placement", "append");

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(true);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(true);
    $this->addElement($end);

    /**
     * @var $table Hegift_Model_DbTable_Categories
     */
    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $categories = $table->getCategoriesArray();

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => $categories,
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Select', 'type', array(
      'label' => 'Type of Gift',
      'multiOptions' => array('0' => ' '),
      'allowEmpty' => false,
      'required' => true,
      'onchange' => "updateTextFields()",
    ));

    $types = array(
      '1' => 'Photo Gift',
      '2' => 'Audio Gift'
    );

    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    if (!empty($ffmpeg_path)) {
      $types['3'] = 'Video Gift';
    }
    $this->type->addMultiOptions($types);

    // Create Photo Gift
    $fancyUpload = new Engine_Form_Element_FancyUpload('photo');
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => '_FancyUploadPhoto.tpl',
        'placement'  => '',
      ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Create Audio Gift
    $fancyUpload = new Engine_Form_Element_FancyUpload('audio');
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => '_FancyUploadAudio.tpl',
        'placement'  => '',
      ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Create Video Gift
    $fancyUpload = new Engine_Form_Element_FancyUpload('video');
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => '_FancyUploadVideo.tpl',
        'placement'  => '',
      ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Element: execute
    $this->addElement('Button', 'upload', array(
      'label' => 'Create Gift',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
