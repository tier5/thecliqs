<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Form_Admin_Global extends Engine_Form
{
    public function init()
    {
        // Form information
        $this -> setTitle('Global Settings') -> setDescription('These settings affect all members in your community. Please enter a number for each field. Leave 0 for unlimited.');

        $this->addElement('Radio', 'ynfilesharing_apiviewer', array(
	        'label' => 'API Viewer',
	        'description' => 'What is the Api viewer will be used to view document? ',
	        'multiOptions' => array(
	          1 => 'Scribd Viewer',
	          0 => 'Google Viewer '
	        ),
	        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfilesharing.apiviewer', 1),
	     )); 
        
        // Max total size per folder
        $this -> addElement('Text', 'ynfilesharing_foldertotal', array(
            'label' => 'Max total size per folder',
            'description' => 'What is the total file size (Kb) that a folder can contain?',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfilesharing.foldertotal', 0),
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
                //array('Between', true, array(0, 1000, true)),
            ),
        ));
        
        // Max total size per group
        $this -> addElement('Text', 'ynfilesharing_grouptotal', array(
            'label' => 'Max total size per group',
            'description' => 'What is the total file size (Kb) that a group can contain?',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfilesharing.grouptotal', 0),
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
                //array('Between', true, array(0, 1000, true)),
            ),
        ));
		
		// Max total size per event
        $this -> addElement('Text', 'ynfilesharing_eventtotal', array(
            'label' => 'Max total size per event',
            'description' => 'What is the total file size (Kb) that a event can contain?',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfilesharing.eventtotal', 0),
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        // Max total size per business
        $this -> addElement('Text', 'ynfilesharing_businesstotal', array(
            'label' => 'Max total size per business',
            'description' => 'What is the total file size (Kb) that a business can contain?',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfilesharing.businesstotal', 0),
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
            ),
        ));

        // Submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
