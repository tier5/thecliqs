<?php

class Yncontest_Form_Contest_Search extends Engine_Form
{
    public function init()
    {
        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box',
            'method' => 'POST',
        ));

        $this->addElement('Text', 'name', array(
            'label' => 'Keywords',
        ));

        $plugin = Engine_Api::_()->yncontest()->getPlugins();
        $contest_type = array_merge(array('0' => 'All'), $plugin);

        $this->addElement('Select', 'contest_type', array(
            'label' => "Contest's type",
            'multiOptions' => $contest_type,
        ));

        //Feature Filter
        $this->addElement('Select', 'contest_status', array(
            'label' => 'Contest Status',
            'multiOptions' => array(
                'all' => '',
                'draft' => 'Draft',
                'denied' => 'Denied',
                'waiting' => 'Waiting',
                'published' => 'Published',
                'close' => 'Closed',
            ),
            'value' => '',
        ));

        // Element: order
        $this->addElement('Hidden', 'orderby', array(
            'order' => 101,
            'value' => '',//'start_date'
        ));

        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
        ));

        // Element: direction
        $this->addElement('Hidden', 'page', array(
            'order' => 103,
        ));

        // Element: direction
        $this->addElement('Hidden', 'active_menu', array(
            'value' => 'mycontest',
        ));

        // Buttons
        $this->addElement('Button', 'button', array(
            'label' => 'Search',
            'type' => 'submit',
        ));
    }
}