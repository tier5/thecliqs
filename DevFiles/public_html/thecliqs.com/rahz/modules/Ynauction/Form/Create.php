<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Create.php
 * @author     Minh Nguyen
 */
class Ynauction_Form_Create extends Engine_Form
{
	public $_error = array();

	public function init()
	{
		$this -> setTitle('Post New Auction') -> setDescription("Compose your new auction below, then click 'Create Auction' to publish auction.") -> setAttrib('name', 'ynauctions_create');
		$user = Engine_Api::_() -> user() -> getViewer();
		$user_level = Engine_Api::_() -> user() -> getViewer() -> level_id;
		$translate = Zend_Registry::get('Zend_Translate');
		// prepare categories
		$categories = Engine_Api::_() -> ynauction() -> getCategories(0);
		if (count($categories) != 0)
		{
			$categories_prepared[0]= $translate -> translate("Select category");
			foreach ($categories as $category)
			{
				$categories_prepared[$category -> category_id] = Zend_View_Helper_Translate::translate($category -> title);
			}

			// category field
			$this -> addElement('Select', 'cat_id', array(
				'label' => 'Category*',
				'multiOptions' => $categories_prepared,
				'title' => $translate -> translate('Category which product belongs to'),
				'required' => true,
				'onchange' => "subCategories()",
				'value' => 0,
				'validators' => array(
			        array('Int', true),
			        new Engine_Validate_AtLeast(1)
			      )
			));
		}
		// subCategory field
		$subcategories_prepared[0] = "";
		$this -> addElement('Select', 'cat1_id', array(
			'label' => 'Sub category',
			'title' => $translate -> translate('Sub category which product belongs to'),
			'multiOptions' => $subcategories_prepared,
		));

		$this -> addElement('Text', 'title', array(
			'label' => 'Auction Name*',
			'required' => true,
			'title' => $translate -> translate('Name of bidded product'),
			'description' => 'Name of bidded product',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength( array('max' => '63'))
			)
		));
		$this -> title -> getDecorator("Description") -> setOption("placement", "append");
		$feature_fee = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynauction_product', $user, 'feature_fee');
		$mtable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
		$fsselect = $mtable -> select() -> where("type = 'ynauction_product'") -> where("level_id = ?", $user_level) -> where("name = 'feature_fee'");
		$mallow_f = $mtable -> fetchRow($fsselect);
		if (!empty($mallow_f) && $feature_fee == "")
			$feature_fee = $mallow_f['value'];
		// Init featured checkbox
		$this -> addElement('Checkbox', 'featured', array(
			'label' => $translate -> translate("Featured Auction")." (" . $feature_fee . " " . Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.currency', 'USD') . ") ",
			'value' => 1,
			'checked' => true,
		));
		// location_id field
		$this -> addElement('Select', 'location_id', array(
			'label' => 'Auction Location*',
			'required' => true,
			'multiOptions' => Engine_Api::_() -> getDbTable('locations', 'ynauction') -> getMultiOptions('..'),
			'value' => 0
		));
		// Element: currency
		$this -> addElement('Select', 'currency_symbol', array(
			'label' => 'Currency*',
			'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.currency', 'USD'),
			'description' => '',
		));
		$this -> getElement('currency_symbol') -> getDecorator('Description') -> setOption('placement', 'APPEND');

		$this -> addElement('Text', 'price', array(
			'label' => 'Buy Now price*',
			'title' => $translate -> translate('Product price is offered in MarketPlace'),
			'description' => '',
			'required' => true,
			'filters' => array(new Engine_Filter_Censor(), ),
			'value' => '0.00',
		));
		$this -> price -> getDecorator("Description") -> setOption("placement", "append");

		$this -> addElement('Text', 'starting_bidprice', array(
			'label' => 'Starting Bid price*',
			'required' => true,
			'filters' => array(new Engine_Filter_Censor(), ),
			'value' => '0.00',
		));

		$this -> addElement('Text', 'minimum_increment', array(
			'label' => 'Minimun Increment',
			'title' => '',
			'description' => '',
			'required' => false,
			'filters' => array(new Engine_Filter_Censor(), ),
			'value' => '0.00',
		));
		$this -> addElement('Text', 'maximum_increment', array(
			'label' => 'Maximum Increment',
			'title' => '',
			'description' => '',
			'required' => false,
			'filters' => array(new Engine_Filter_Censor(), ),
			'value' => '0.00',
		));

		$this -> addElement('File', 'thumbnail', array(
			'label' => 'Thumbnail*',
			'title' => $translate -> translate('Main image of product'),
			'required' => true,
			'description' => 'Main image of product (jpg, png, gif, jpeg)',
		));
		$this -> thumbnail -> getDecorator("Description") -> setOption("placement", "append");
		$this -> thumbnail -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		// Start time
		$start = new Engine_Form_Element_CalendarDateTime('start_time');
		$start -> setLabel("Start Time*");
		$start -> setTitle = $translate -> translate('Time to start auction');
		$start -> setAllowEmpty(false);
		$this -> addElement($start);
		$this -> start_time -> getDecorator("Description") -> setOption("placement", "append");
		// End time
		$end = new Engine_Form_Element_CalendarDateTime('end_time');
		$end -> setLabel("End Time*");
		$end -> setAllowEmpty(false);
		$this -> addElement($end);

		// Add subforms
		if (!$this -> _item)
		{
			$customFields = new Ynauction_Form_Custom_Fields();
		}
		else
		{
			$customFields = new Ynauction_Form_Custom_Fields( array('item' => $this -> getItem()));
		}
		if (get_class($this) == 'Ynauction_Form_Create')
		{
			$customFields -> setIsCreation(true);
		}

		$this -> addSubForms(array('fields' => $customFields));

		$allowed_html = Engine_Api::_() -> authorization() -> getPermission($user_level, 'ynauction_product', 'auth_html');
		$this -> addElement('TinyMce', 'description', array(
			'label' => 'Product Introduction',
			'required' => false,
			'editorOptions' => array(
				'bbcode' => 1,
				'html' => 1
			),
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_Html( array('AllowedTags' => $allowed_html))
			),
		));

		$this -> addElement('TinyMce', 'description1', array(
			'label' => 'Product Description',
			'required' => false,
			'editorOptions' => array(
				'bbcode' => 1,
				'html' => 1
			),
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_Html( array('AllowedTags' => $allowed_html))
			),
		));

		// View
		$availableLabels = array(
			'everyone' => 'Everyone',
			'registered' => 'All Registered Members',
			'owner_network' => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member' => 'Friends Only',
			'owner' => 'Just Me',
		);

		$options = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynauction_product', $user, 'auth_view');
		$options = array_intersect_key($availableLabels, array_flip($options));

		$this -> addElement('Select', 'auth_view', array(
			'label' => 'Privacy',
			'description' => 'Who may view and bid this product?',
			'multiOptions' => $options,
			'value' => 'everyone',
		));
		$this -> auth_view -> getDecorator('Description') -> setOption('placement', 'append');

		$options = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynauction_product', $user, 'auth_comment');
		$options = array_intersect_key($availableLabels, array_flip($options));

		// Comment
		$this -> addElement('Select', 'auth_comment', array(
			'label' => 'Comment Privacy',
			'description' => 'Who may post comments on this product?',
			'multiOptions' => $options,
			'value' => 'everyone',
		));
		$this -> auth_comment -> getDecorator('Description') -> setOption('placement', 'append');
		$this -> addElement('TinyMce', 'shipping_delivery', array(
			'label' => 'Shipping & Delivery',
			'editorOptions' => array(
				'bbcode' => 1,
				'html' => 1
			),
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_Html( array('AllowedTags' => $allowed_html))
			),
		));
		$this -> addElement('Checkbox', 'local_only', array(
			'label' => 'Local Only',
			'value' => 0,
			'checked' => false,
		));
		$this -> addElement('Checkbox', 'international', array(
			'label' => 'International',
			'value' => 1,
			'checked' => true,
		));

		$this -> addElement('TinyMce', 'payment_method', array(
			'label' => 'Payment Method',
			'editorOptions' => array(
				'bbcode' => 1,
				'html' => 1
			),
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_Html( array('AllowedTags' => $allowed_html))
			),
		));

		$this -> addElement('Checkbox', 'check', array(
			'label' => 'I agrees to the ',
			'value' => 0,
			//'required' => true,
			'checked' => false,
		));
		$this -> addElement('Cancel', 'link', array(
			'label' => 'Term of Use and Privacy Statement',
			'link' => true,
			'onclick' => 'goto()',
			'decorators' => array('ViewHelper')
		));
		$this -> addDisplayGroup(array(
			'check',
			'link'
		), 'buttons', array('decorators' => array(
				'FormElements',
				'DivDivDivWrapper'
			)));
		$this -> addElement('Button', 'submit', array(
			'label' => 'Create Auction',
			'type' => 'submit',
			'onclick' => 'removeSubmit()',
		));
	}

}
