<?php

class Yncontest_Form_SearchContest extends Engine_Form
{
    protected $_location;
    public function setLocation($location)
    {
        $this -> _location = $location;
    }
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
                        'action' => 'listing',
                    ), 'yncontest_general', true)
            );

        //Search Title
        $this->addElement('Text', 'contest_name', array(
            'label' => 'Contest name',
        ));


        $plugin = Engine_Api::_()->yncontest()->getPlugins();

        $contest_type = array_merge(array('0' => 'All'), $plugin);
        $this->addElement('Select', 'contest_type', array(
            'label' => "Contest's type",
            'multiOptions' => $contest_type,
        ));

        $this->addElement('ContestMultiLevel', 'category_id', array(
            'label' => 'Category',
            'required' => false,
            'model' => 'Yncontest_Model_DbTable_Categories',
            'onchange' => "en4.yncontest.changeCategory($(this),'category_id','Yncontest_Model_DbTable_Categories')",
            'title' => '',
            'value' => 0
        ));

        $this->addElement('Select', 'browseby', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'all' => '',
                'featured_contest' => 'Featured Contests',
                'premium_contest' => 'Premium Contests',
                'endingsoon_contest' => 'Ending Soon',
            ),
        ));
        $this->addElement('Select', 'contestsocial', array(
            'label' => 'Filter By',
            'multiOptions' => array(
                'all_contest' => 'All Contests',
                'friend_contest' => 'Friend\'s Contests',
            ),
        ));

        $this->addElement('Select', 'contest_status', array(
            'label' => 'Status',
            'multiOptions' => array(
                'all' => '',
                'published' => 'Published',
                'close' => 'Closed',
            ),
            'value' => '',
            //'onchange' => 'this.form.submit();',
        ));

        $this->addElement('Text', 'location', array(
            'label' => 'Location',
            'decorators' => array(array(
                'ViewScript',
                array(
                    'viewScript' => '_location_search.tpl',
                    'viewModule' => 'yncontest',
                    'label' => "Location",
                    'location_address' => $this->_location
                )
            )),
        ));

        $this->addElement('Text', 'within', array(
            'label' => 'Radius (mile)',
            'placeholder' => Zend_Registry::get('Zend_Translate')->_('Radius (mile)'),
            'maxlength' => '60',
            'required' => false,
            'style' => "display: block",
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
            ),
        ));

        $this->addElement('hidden', 'lat', array(
            'value' => '0',
            'order' => '98'
        ));

        $this->addElement('hidden', 'long', array(
            'value' => '0',
            'order' => '99'
        ));

        $this->addElement('Hidden', 'page', array(
            'order' => '100'
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