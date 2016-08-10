<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Form_Admin_Scribd extends Engine_Form {
	public function init() {
		// Form information
		$this->setTitle('Scribd Settings')
		->setDescription ('These settings affect document preview page and the way scribd document is embedded.');

		$settings = Engine_Api::_()->getApi('settings', 'core');

		// api key
		$this->addElement ( 'Text', 'ynfilesharing_apikey', array (
			'label' => 'Scribd Api Key',
			'description' => 'Insert your Scribd Api key',
			'allowEmpty' => false,
			'required' => true,
			'value' => $settings->getSetting ('ynfilesharing.apikey'),
			'validators'  => array(),
		));
		
		// api secret
		$this->addElement ( 'Text', 'ynfilesharing_apisecret', array (
			'label' => 'Scribd Api Secret',
			'description' => 'Insert your Scribd Api secret',
			'allowEmpty' => false,
			'required' => true,
			'value' => $settings->getSetting ( 'ynfilesharing.apisecret'),
			'validators'  => array(),
		));
		
		// Element: level_id
// 		$this->addElement('Select', 'level_id', array(
// 				'label' => 'Member Level',
// 				'multiOptions' => $levelOptions,
// 				'onchange' => 'javascript:fetchLevelSettings(this.value);',
// 				'ignore' => true,
// 		));
		// View Settings
		$this->addElement('Radio', 'ynfilesharing_mode', array(
			'label'        => 'View mode',
			'description'  => 'Set the default view mode for the document',
			'multiOptions' => array(
					'list' => 'List',
					'book' => 'Book',
					'slideshow' => 'Slide show',
			),
			'value' => $settings->getSetting ( 'ynfilesharing.mode', 'list')
		));
		// width
		$this->addElement ( 'Text', 'ynfilesharing_width', array (
				'label' => 'Width',
				'description' => "The width of embedded document, in pixels. If this parameter is not sepecified, the embedded document will attempt to size itself correctly for the page it's embedded in.",
				'value' => $settings->getSetting ( 'ynfilesharing.width'),
				'validators'  => array(
						array('Int', true),
						new Engine_Validate_AtLeast(0),
				),
		) );
		// height
		$this->addElement ( 'Text', 'ynfilesharing_height', array (
				'label' => 'Height',
				'description' => "The height of embedded document, in pixels. If this parameter is not sepecified, the embedded document will attempt to size itself correctly for the page it's embedded in.",
				'value' => $settings->getSetting ( 'ynfilesharing.height'),
				'validators'  => array(
						array('Int', true),
						new Engine_Validate_AtLeast(0),
				),
		) );
		// embed format
// 		$this->addElement('Radio', 'ynfilesharing_embedformat', array(
// 				'label' => 'Default Embed Format',
// 				'description' => 'The default format of the Scribd Reader',
// 				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfilesharing_embedformat', 'html5'),
// 				'multiOptions' => array(
// 						'html5' => 'HTML5',
// 						'flash' => 'flash'
// 				)
// 		)
//		);

		// Submit button
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Save Changes',
				'type' => 'submit',
				'ignore' => true
		) );
	}
}