<?php

class Ynvideochannel_Form_SendToFriends extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Send to friends');
        $onfocus = "$(this).store('over', this.value);this.value = '';";
        $onblur = "this.value = $(this).retrieve('over');";
        $this->addElement('Text', 'search', array(
            'label' => '',
            'id'=>'friends_search',
            'value'=>'Search friends',
            'onfocus'=> $onfocus,
            'onblur' => $onblur,
        ));

        $this -> addElement('Checkbox', 'all', array(
            'id' => 'selectall',
            'label' => 'Choose All Friends',
            'ignore' => true
        ));

        $this -> addElement('dummy', 'friendlist', array(
            'label'     => '',
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_friend_list_container.tpl',
                )
            )),
        ));

        $this -> addElement('Button', 'submit', array(
            'label' => 'Send',
            'type' => 'submit',
            'id' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper', ),
        ));
        $onclick = 'parent.Smoothbox.close();';
        $session = new Zend_Session_Namespace('mobile');
        if ($session -> mobile)
        {
            $onclick = '';
        }
        $this -> addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => $onclick,
            'decorators' => array('ViewHelper', ),
        ));

        $this -> addDisplayGroup(array(
            'submit',
            'cancel'
        ), 'buttons');
    }

    public function isValid($data)
    {
        $result = parent::isValid($data);

        return $result;
    }

}
