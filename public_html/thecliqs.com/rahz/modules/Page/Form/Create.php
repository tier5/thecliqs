<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Create extends Engine_Form
{
  private $set = array();

  public function init()
  {
    $this
      ->setTitle('PAGE_CREATE_TITLE')
      ->setDescription('PAGE_CREATE_DESC')
      ->setAttrib('enctype','multipart/form-data')
      ;

    $this->addElement('Text', 'title', array(
      'label' => 'Title *',
      'allowEmpty' => false,
      'required' => true,
      'order' => -4,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $host_url = $_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_id' => 'pagename'), 'page_view');
    $description = sprintf( Zend_Registry::get('Zend_Translate')->_("PAGE_CREATE_URL_DESC"), $host_url);

    $this->addElement('Text', 'url', array(
      'label' => 'URL *',
      'required' => true,
      'order' => -3,
      'description' => $description,
      'filters' => array(
        array('PregReplace', array('/[^a-z0-9-]/i', '-')),
        array('PregReplace', array('/-+/', '-')),
      ),
    ));

    $this->url->getDecorator('Description')->setOption('placement', 'append');
    $this->url->getDecorator('Description')->setOption('escape', false);

    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
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

    $this->addElement('File', 'photo', array(
      'label' => 'Photo',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'order' => -1,
      'validators' => array(
        array('Count', false, 1),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      )
    ));

    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->getElement('tags')->getDecorator("Description")->setOption("placement", "append");

    $this->photo->addValidator('Extension', false, 'jpg,png,gif,bmp');

    // Add subforms
    if( !$this->_item ) {
      $customFields = new Page_Form_Custom_Fields();
    } else {
      $customFields = new Page_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Page_Form_Create' ) {
      $customFields->setIsCreation(true);
    }

		$customFields->removeElement('submit');

    $customFields->getElement('0_0_1')->setValue(1);
    $setInfo = Engine_Api::_()->page()->getSetInfo();
    $set = array();
    $setOptions = array(0=>'Select');
    foreach ($setInfo as $cSet) {
      if (!isset($set[$cSet['set_id']])) {
        $set[$cSet['set_id']] = array(
          'info' => array('id'=>$cSet['set_id'], 'caption' =>$cSet['caption']),
          'items' => array()
        );
        $setOptions[$cSet['set_id']] = $cSet['caption'];
      }
      if($cSet['cat_id'] == 1)
        continue;
      $set[$cSet['set_id']]['items'][$cSet['cat_id']] = array('id'=>$cSet['cat_id'], 'caption'=>$cSet['cat_caption']);
    }
    $this->set = $set;
    $this->addElement('select', 'category', array(
      'id' => 'category',
      'label'=>'Category Set',
      'required' => true) //todo: 8aa: page add lang
    );

    $this->getElement('category')->addFilter('Int')
      ->addValidator('NotEmpty',true, array('integer','zero'));
    $this->getElement('category')->setMultiOptions($setOptions);
    $this->addSubForms(array(
      'fields' => $customFields
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'page_manage', true),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'execute',
        'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }

  /**
   * Return Category Set info
   * @return array
   */
  public function getSetInfo(){
    return $this->set;
  }

}