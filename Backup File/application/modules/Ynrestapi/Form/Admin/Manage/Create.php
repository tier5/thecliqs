<?php

class Ynrestapi_Form_Admin_Manage_Create extends Engine_Form
{
    public function init()
    {
        $this->setTitle('Create Client');

        // Element: title
        $this->addElement('Text', 'title', array(
            'label' => 'Client Name',
            'required' => true,
            'allowEmpty' => false,
            'maxlength' => '255',
            'filters' => array(
                'StringTrim',
                'stripTags',
            ),
        ));

        $this->addElement('Text', 'redirect_uri', array(
            'label' => 'Redirect URI',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('MultiCheckbox', 'grant_types', array(
            'label' => 'Grant Types',
            'description' => 'When there are no grant types configured, the client is able to use all grant types available within the authorization server.',
            'multiOptions' => array(
                'authorization_code' => 'Authorization Code',
                'password' => 'User Credentials',
                'client_credentials' => 'Client Credentials',
                'refresh_token' => 'Refresh Token',
            ),
        ));

        $this->addElement('MultiCheckbox', 'scope', array(
            'label' => 'Scope',
            'description' => 'When there are no scopes configured, the client is able to use all scopes available within the authorization server.',
        ));

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Create Client',
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
        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }
}
