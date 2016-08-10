<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Edit extends Engine_Form
{
  protected $_item;

  private $set = array();

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }

	public function init()
  {

    $this->setTitle('Edit Basic Information')
      ->setDescription('Edit your Page title, description, location and other information.')
    ;

    $this->addElement('Text', 'title', array(
      'label' => 'Title *',
      'allowEmpty' => false,
      'required' => true,
    	'order' => -100,
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

    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
//      'editorOptions' => array(
//        'html' => true,
//      ),
    ));

    $params = array(
      'elements' => 'description',
      'mode' => 'exact',
      'width' => '500px',
      'height' => '300px',
      'theme_advanced_buttons1' => array(
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
        'bullist', 'numlist', '|',
        'cut', 'copy', 'paste', 'undo', 'redo', '|',
        'sub', 'sup'
      ),
      'theme_advanced_buttons2' => array(
        'newdocument', 'code', 'image', 'media', 'preview', 'fullscreen', '|',
        'link', 'unlink', 'anchor', 'charmap', 'cleanup', 'hr', 'removeformat', 'blockquote', 'separator', 'outdent', 'indent', '|',
        'selectall', 'advimage'),
      'theme_advanced_buttons3' => array('formatselect', 'fontselect', 'fontsizeselect', 'styleselectchar', '|', 'table'),
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Text', 'country', array(
      'label' => 'Country',
    	'order' => -98,
    	'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    
    $this->addElement('Text', 'state', array(
      'label' => 'State',
    	'order' => -97,
    	'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    
    $this->addElement('Text', 'city', array(
      'label' => 'City',
    	'order' => -96,
    	'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    
    $this->addElement('Text', 'street', array(
      'label' => 'Address',
    	'order' => -95,
    	'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    
    $this->addElement('Text', 'website', array(
      'label' => 'Website',
    	'order' => -94
    ));
    
    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'order' => -93,
    ));

    // Add subforms
    if( !$this->_item ) {
      $customFields = new Page_Form_Custom_Fields();
    } else {
      $customFields = new Page_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Page_Form_Edit' ) {
      $customFields->setIsCreation(true);
    }

		$customFields->removeElement('submit');

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
    $this->addElement('select', 'category',
      array('id' => 'category', 'label'=>'Category Set', 'required' => true) //todo: 8aa: page add lang
    );
    $this->getElement('category')->addFilter('Int')
      ->addValidator('NotEmpty',true, array('integer','zero'));
    $this->getElement('category')->setMultiOptions($setOptions);
    if(intval($this->getItem()->set_id) > 0) {
      $this->getElement('category')->setValue($this->getItem()->set_id);
    }
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

  public function addMapElement($mapJs, $markers, $bounds)
  {
    $this->addElement('Text', 'map', array(
      'label' => 'Map',
      'ignore' => true,
      'order' => -92,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formEditMap.tpl',
        'class'      => 'form element',
        'markers'      => $markers,
        'bounds'      => $bounds,
        'mapJs'      => $mapJs,
      )))
    ));

    Engine_Form::addDefaultDecorators($this->map);

    $this->addElement('Hidden', 'coordinates', array(
      'order' => -91
    ));
  }

  /**
   * @return array
   */
  public function getSetInfo(){
    return $this->set;
  }
}
