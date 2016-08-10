<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Products_Create extends Engine_Form
{
  public function init()
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $href = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'store', 'controller'=>'products'), 'admin_default', true);

    // Init form
    $this
      ->setTitle('STORE_Add New Product')
      ->setDescription('You can add new product here, or <a href="'.$href.'">back</a> to the list of products.')
      ->setAttrib('enctype','multipart/form-data')
      ->setAttrib('id','form-upload');

    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'STORE_Product Title',
      'required' => true,
      'allowEmpty' => false,
    ));

    // Element tags
    $this->addElement('Text', 'tags', array(
      'label' => 'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    // Element type
		$this->addElement('Select', 'type', array(
			'label' => 'STORE_Product Type',
			'description' => 'STORE_PRODUCT_TYPE_DESCRIPTION',
			'multiOptions' => array(
				'simple'=>'STORE_Tangible',
				'digital'=>'STORE_Digital',
			),
      "onchange" => "switchAmount()"
		));
		$this->type->getDecorator('Description')->setOptions(array('placement' => 'append'));

    // Element: quantity
    $this->addElement('Text', 'quantity', array(
      'label' => 'STORE_Quantity',
			'description' => 'STORE_Amount of product for sell',
      'required' => true,
      'allowEmpty' => false,
			'value' => 1,
			'validators' => array(
        array('Digits'),
			)
    ));
		$this->quantity->getValidator('Digits')->setMessage('STORE_Please enter a valid digits.', 'digitsInvalid');
		$this->quantity->getDecorator("Description")->setOption("placement", "append");

    // Element price_type
		$this->addElement('Select', 'price_type', array(
			'label' => 'STORE_Price Type',
			'description' => 'STORE_PRICE_TYPE_DESCRIPTION',
			'multiOptions' => array(
				'simple'=>'STORE_Simple',
				'discount'=>'STORE_Discount'
			),
			"onchange" => "switchType()"
		));
		$this->price_type->getDecorator('Description')->setOptions(array('placement' => 'append'));

    // Element: price
    $this->addElement('Text', 'price', array(
      'label' => 'STORE_Product Price',
      'required' => true,
      'allowEmpty' => false,
			'validators' => array(
        array('Float', true),
				new Engine_Validate_AtLeast($settings->getSetting('store.minimum.price', 0.15)),
			),
	    'value' => $settings->getSetting('store.minimum.price', 0.15),
    ));


		// Element: list_price
    $this->addElement('Text', 'list_price', array(
      'label' => 'STORE_Product List Price',
			'description' => 'STORE_LIST_PRICE_DESCRIPTION',
      'required' => true,
      'allowEmpty' => false,
			'validators' => array(
        array('Float', true),
				new Engine_Validate_AtLeast($settings->getSetting('store.minimum.price', 0.15)),
			),
	    'value' => (float)($settings->getSetting('store.minimum.price', 0.15) + 0.01),
    ));

    // Discount expiry date
    $discount_expiry_date = new Engine_Form_Element_CalendarDateTime('discount_expiry_date');
    $discount_expiry_date->setLabel("Discount expiry date");
    $discount_expiry_date->setAllowEmpty(true);
    $discount_expiry_date->setDescription('STORE_DISCOUNT_EXPIRY_DATE_DESC');
    $this->addElement($discount_expiry_date);
    $discount_expiry_date->getDecorator('Description')->setOption('escape', false);

    if (Engine_Api::_()->store()->isCreditEnabled()) {
      $this->addElement('Checkbox', 'via_credits', array(
        'label' => 'Selling with Credits',
        'description' => 'STORE_Select checkbox if you want to sell product with credits, but this doesn\'t mean that you cannot sell with default currency ($ etc), they will work together',
      ));
    }

    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
    ));

    $params = array(
      'mode' => 'exact',
	    'elements' => 'description',
	    'width' => '500px',
	    'height' => '225px',
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

//    $this->addElement('FancyUpload', 'file');
    $fancyUpload = new Engine_Form_Element_FancyUpload('file', array(
      'label' => 'Photos'
    ));
    $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                  'viewScript' => '_FancyUpload.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');

    // Add subforms
    if( !$this->_item ) {
      $customFields = new Store_Form_Admin_Custom_Fields();
    } else {
      $customFields = new Store_Form_Admin_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Store_Form_Admin_Products_Create' ) {
      $customFields->setIsCreation(true);
    }

		$customFields->removeElement('submit');

    $this->addSubForms(array(
      'fields' => $customFields
    ));

    /**
     * @var $category Fields_Form_Element_ProfileType
     * @var $multiOptions Array
     */
    $category = $this->getSubForm('fields')->getElement('0_0_1');
    $multiOptions = $category->getMultiOptions();
    foreach($multiOptions as $key=>$value){
      if ( $key == '') unset($multiOptions[$key]);
    }
    $category->setMultiOptions($multiOptions);
    $category->setValue(1);

    /**
     * @var $table Store_Model_DbTable_Taxes
     */
    $table = Engine_Api::_()->getDbTable('taxes', 'store');
    $taxes = $table->getTaxesArray();

    // Element type
		$this->addElement('Select', 'tax_id', array(
			'label' => 'Tax',
			'description' => 'STORE_TAX_DESCRIPTION',
			'multiOptions' => $taxes
		));
		$this->tax_id->getDecorator('Description')->setOptions(array('placement' => 'append'));

    // Element: params
    $path = Engine_Api::_()->getModuleBootstrap('store')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');

    $additionalParams = new Store_Form_Element_AdditionalParams('additional_params', array(
      'label' => 'Additional Params',
      'description' => 'STORE_ADDITIONAL_PARAMS_DESCRIPTION'
    ));
    $additionalParams->clearDecorators()
                ->addDecorator('FormAdditionalParams')
                ->addDecorator('viewScript', array(
                  'viewScript' => '_AdditionalParams.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($additionalParams);
    $this->addElement($additionalParams);
    $additionalParams->getDecorator('Description')->setOption('escape', false);

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'STORE_Add Product',
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
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'store', 'controller'=>'products'), 'admin_default', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
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


  public function createAlbum($product)
  {
    $set_cover = true;
    $values = $this->getValues();

    $params = Array();
    if( (!isset($values['album'])) || ($values['album'] == 0) )
    {
      $params['title'] = $values['title'];
      if (empty($params['title'])) {
        $params['title'] = "Untitled Album";
      }
      $params['description'] = $values['description'];

      if( $product->product_id )
      $params['product_id'] = $product->product_id;

      $album = Engine_Api::_()->getDbtable('albums', 'store')->createRow();
      $album->setFromArray($params);
      $album->save();

      $set_cover = true;
    }
    else
    {
      $album = Engine_Api::_()->getItem('store_album', $values['album']);
    }

    $fileids = explode(' ', $values['fancyuploadfileids'] );
    if(count($fileids) <= 1)
      return $album;

    // Do other stuff
    $count = 0;
    foreach( $fileids  as $photo_id )
    {
      $photo = Engine_Api::_()->getItem("store_photo", $photo_id);
      if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

      if( $set_cover )
      {
        $album->photo_id = $photo_id;
        $product->photo_id = $photo_id;
        $product->save();
        $album->save();
        $set_cover = false;
      }
      $photo->collection_id = $album->album_id;
      $photo->album_id = $album->album_id;
      $photo->save();

      $count++;
    }

    return $album;
  }

  public function isValid($data){
    /**
     * @var $atLeast Engine_Validate_AtLeast
     */

    if ( $data['price_type']=='discount' ){
      $element = $this->getElement('list_price');
      $atLeast = $element->getValidator('AtLeast');
      $minimum_price = (float)($data['price'] + 0.01);
      $atLeast->setMin($minimum_price);
    }

    return parent::isValid($data);
  }
}
