<?php

class Minify_Form_Admin_Groups extends Engine_Form
{

    public function init()
    {

        $this->setAttrib('id','form_minify_admin_groups');
        
        $this->addElement('textarea','js1',array('label'=>'Group Javascript #1'));
        $this->addElement('textarea','js2',array('label'=>'Group Javascript #2'));
        $this->addElement('textarea','js3',array('label'=>'Group Javascript #3'));
        $this->addElement('textarea','js4',array('label'=>'Group Javascript #4'));
        $this->addElement('textarea','js5',array('label'=>'Group Javascript #5'));
        $this->addElement('textarea','css1',array('label'=>'Group CSS #1'));
        $this->addElement('textarea','css2',array('label'=>'Group CSS #2'));
        $this->addElement('textarea','css3',array('label'=>'Group CSS #3'));
        $this->addElement('textarea','css4',array('label'=>'Group CSS #4'));
        $this->addElement('textarea','css5',array('label'=>'Group CSS #5'));
        $this->addElement('textarea','css6',array('label'=>'Group CSS #6'));
        // Element: submit
        $this -> addElement('Button', '_submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'order' => 20,
        ));
        
        // Element: submit
         $this -> addElement('Button', '_refine', array(
             'label' => 'Refine Groups',
             'ignore' => true,
             'onclick'=>'refineGroups()',            
             'decorators' => array('ViewHelper'),
             'order' => 21,
       ));

       

        $this -> addDisplayGroup(array(
            '_submit',
            '_refine'
        ), 'buttons', array('order' => 22, ));
    }

}
