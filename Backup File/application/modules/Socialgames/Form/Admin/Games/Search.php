<?php

class Socialgames_Form_Admin_Games_Search extends Engine_Form
{
    public function init()
    {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' =>'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this->addElement('Text', 'search', array(
            'label' => 'Search',
        ));
        $this->addElement('Button', 'execute', array(
            'label'      => 'Search',
            'type'       => 'submit',
            'ignore'     => true,
        ));
    }
}