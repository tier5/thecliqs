<?php

class Ynrestapi_Form_Admin_Manage_Edit extends Ynrestapi_Form_Admin_Manage_Create
{
    public function init()
    {
        parent::init();

        $this->setTitle('Edit Client');

        // Change the submit label
        $this->getElement('execute')->setLabel('Edit Client');
    }
}
