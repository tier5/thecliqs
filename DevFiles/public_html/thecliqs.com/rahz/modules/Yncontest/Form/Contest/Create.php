<?php

class Yncontest_Form_Contest_Create extends Engine_Form
{
    protected $_location;
    protected $_plugin;
    public function setPlugin($plugin)
    {
        $this->_plugin = $plugin;
    }

    protected $_contest;

    public function setContest($contest)
    {
        $this->_contest = $contest;
    }

    public function getContest()
    {
        return $this->_contest;
    }

    public function setLocation($location)
    {
        $this -> _location = $location;
    }

    public function init()
    {
        $this
            ->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element');
        $this
            ->setTitle('Basic Information');

        $this->addElement('Text', 'contest_name', array(
            'label' => 'Contest Name*',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(1, 64)),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
        $this->addElement('TinyMce', 'description', array(
            'label' => 'Description*',
            'editorOptions' => array(
                'bbcode' => 1,
                'html' => 1,
                'theme_advanced_buttons1' => array(
                    'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
                    'media', 'image', 'link', 'unlink', 'fullscreen', 'preview', 'emotions'
                ),
                'theme_advanced_buttons2' => array(
                    'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
                    'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
                    'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
                ),
            ),
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags' => $allowed_html)))
        ));


        $this->addElement('Text', 'tags', array(
            'label' => 'Tags',
            'autocomplete' => 'off',
            'description' => 'Tips: separate tags with commas.',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
        ));
        $this->tags->getDecorator("Description")->setOption("placement", "append");


        $this->addElement('Radio', 'contest_type', array(
            'label' => 'Contest Type*',
            'required' => true,
            'multiOptions' => $this->_plugin,
            'value' => key($this->_plugin),
        ));

        $this->addElement('ContestMultiLevel', 'category_id', array(
            'label' => 'Category*',
            'required' => true,
            'allowEmpty' => false,
            'model' => 'Yncontest_Model_DbTable_Categories',
            'onchange' => "en4.yncontest.changeCategory($(this),'category_id','Yncontest_Model_DbTable_Categories')",
            'title' => '',
            'value' => ''
        ));

        $this->addElement('File', 'photo', array(
            'label' => 'Contest Image*',
            'required' => true,
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this -> addElement('Dummy', 'location_map', array(
            'label' => 'Location',
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_location_search.tpl',
                    'viewModule' => 'yncontest',
                    'class' => 'form element',
                    'label' => "Location",
                    'location_address' => $this -> _location
                )
            )),
        ));

        $this -> addElement('hidden', 'location_address', array(
            'value' => '0',
            'order' => '97'
        ));

        $this -> addElement('hidden', 'lat', array(
            'value' => '0',
            'order' => '98'
        ));

        $this -> addElement('hidden', 'long', array(
            'value' => '0',
            'order' => '99'
        ));

        $AllowEdit = true;
        if (!empty($this->_contest)) {
            if ($this->_contest->contest_status != 'draft' || $this->_contest->approve_status == 'pending') {
                $AllowEdit = false;
            }
        }
        if ($AllowEdit) {
            $start = new Engine_Form_Element_CalendarDateTime('start_date');
            $start->setLabel("Start Date Contest Period*");
            $start->setAllowEmpty(false);
            $start->setRequired(true);
            $this->addElement($start);

            $end = new Engine_Form_Element_CalendarDateTime('end_date');
            $end->setLabel("End Date Contest Period*");
            $end->setAllowEmpty(false);
            $end->setRequired(true);
            $this->addElement($end);

            $start = new Engine_Form_Element_CalendarDateTime('start_date_submit_entries');
            $start->setLabel("Start Date Submit Entries Period*");
            $start->setAllowEmpty(false);
            $start->setRequired(true);
            $this->addElement($start);

            $end = new Engine_Form_Element_CalendarDateTime('end_date_submit_entries');
            $end->setLabel("End Date Submit Entries Period*");
            $end->setAllowEmpty(false);
            $end->setRequired(true);
            $this->addElement($end);

            $start = new Engine_Form_Element_CalendarDateTime('start_date_vote_entries');
            $start->setLabel("Start Date Vote Entries Period*");
            $start->setAllowEmpty(false);
            $start->setRequired(true);
            $this->addElement($start);

            $end = new Engine_Form_Element_CalendarDateTime('end_date_vote_entries');
            $end->setLabel("End Date Vote Entries Period*");
            $end->setAllowEmpty(false);
            $end->setRequired(true);
            $this->addElement($end);
        }


        $this->addElement('TinyMce', 'award', array(
            'label' => 'Award*',
            'editorOptions' => array(
                'bbcode' => 1,
                'html' => 1,
                'theme_advanced_buttons1' => array(
                    'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
                    'media', 'image', 'link', 'unlink', 'fullscreen', 'preview', 'emotions'
                ),
                'theme_advanced_buttons2' => array(
                    'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
                    'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
                    'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
                ),
            ),
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags' => $allowed_html)))
        ));
        $this->addElement('TinyMce', 'condition', array(
            'label' => 'Terms and Conditions*',
            'description' => 'Tips: This is Terms and Conditions applied for contest.',
            'editorOptions' => array(
                'bbcode' => 1,
                'html' => 1,
                'theme_advanced_buttons1' => array(
                    'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
                    'media', 'image', 'link', 'unlink', 'fullscreen', 'preview', 'emotions'
                ),
                'theme_advanced_buttons2' => array(
                    'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
                    'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
                    'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
                ),
            ),
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags' => $allowed_html)))
        ));
        $this->condition->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('TinyMce', 'winner_desc', array(
            'label' => 'Winners Congratulation',
            'editorOptions' => array(
                'bbcode' => 1,
                'html' => 1,
                'theme_advanced_buttons1' => array(
                    'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
                    'media', 'image', 'link', 'unlink', 'fullscreen', 'preview', 'emotions'
                ),
                'theme_advanced_buttons2' => array(
                    'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
                    'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
                    'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
                ),
            ),
            'required' => false,
            'allowEmpty' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags' => $allowed_html)))
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Save & Continue',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'yncontest_general', true),
            'onclick' => '',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }
}

