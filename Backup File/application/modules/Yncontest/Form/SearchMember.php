<?php

class Yncontest_Form_SearchMember extends Engine_Form
{
    public function init()
    {
        $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box',
            'method' => 'POST',
        ));

        //Search Title
        $this->addElement('Text', 'user_id', array(
            'label' => 'ID',
        ));
        //Search Owner
        $this->addElement('Text', 'user_name', array(
            'label' => 'Name',
        ));


        //Feature Filter
        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array(
                '' => '',
                'pending' => 'Pending',
                'approved' => 'Approved',
                'banned' => 'Banned',
                'denied' => 'Denied',
            ),
            'value' => '',
        ));

        $this->addElement('Select', 'gender', array(
            'label' => 'Gender',
            'multiOptions' => array(
                '' => '',
                '0' => 'Male',
                '1' => 'Female',
            ),
            'value' => '',
        ));
        // Element: order
        $this->addElement('Hidden', 'huser_id', array(
            'order' => 101,
            'value' => 'start_date'
        ));

        // Element: direction
        $this->addElement('Hidden', 'hcontest_id', array(
            'order' => 102,
            'value' => 'DESC',
        ));

        // Element: direction
        $this->addElement('Hidden', 'page', array(
            'order' => 103,
        ));

        // Buttons
        $this->addElement('Button', 'button', array(
            'label' => 'Search',
            'type' => 'submit',
        ));

        $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}