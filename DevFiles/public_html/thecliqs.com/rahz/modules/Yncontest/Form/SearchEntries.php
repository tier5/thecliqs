<?php

class Yncontest_Form_SearchEntries extends Engine_Form
{
    public function init()
    {
        $this
            ->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator')
            ->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element')
            ->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator');

        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box',
        ))
            ->setAction(
                Zend_Controller_Front::getInstance()->getRouter()->assemble(
                    array(
                        'module' => 'yncontest',
                        'controller' => 'index',
                        'action' => 'entries',
                    ), 'default', true)
            );

        //Search Title
        $this->addElement('Text', 'entry_name', array(
            'label' => 'Entry name',
        ));

        $contest_type[] = "";
        $contest_type = array_merge($contest_type, Engine_Api::_()->yncontest()->arrPlugins);//getPlugins();
        $this->addElement('Select', 'entry_type', array(
            'label' => "Entry's type",
            'multiOptions' => $contest_type,
        ));

        $this->addElement('Select', 'browseby', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'all' => '',
                'view_count' => 'Most view',
                'vote_count' => 'Most voted',
                'like_count' => 'Most liked',
            ),
        ));

        $this->addElement('Select', 'award', array(
            'label' => 'Award',
            'multiOptions' => array(
                'all' => 'All',
                'yes' => 'Yes',
                'no' => 'No',
            ),
        ));

        $this->addElement('Hidden', 'page', array(
            'order' => 100
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'submit',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }
}