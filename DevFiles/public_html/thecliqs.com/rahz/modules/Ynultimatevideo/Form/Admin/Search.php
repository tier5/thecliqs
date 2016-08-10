<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Form_Admin_Search extends Engine_Form {

    public function init() {
        $this->clearDecorators()
                ->addDecorator('FormElements')
                ->addDecorator('Form')
                ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
                ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this->setAttribs(array('id' => 'filter_form', 'class' => 'global_form_box'))->setMethod('get');

        //Search Title
        $this->addElement('Text', 'keyword', array(
            'label' => 'Title',
        ));

        //Search Owner
        $this->addElement('Text', 'owner', array(
            'label' => 'Owner',
        ));

        //Feature
        $this->addElement('Select', 'featured', array(
            'label' => 'Featured',
            'multiOptions' => array(
                '' => '',
                '0' => 'Only Featured Videos',
                '1' => 'Only Not Featured Videos',
            ),
            'value' => '',
        ));

        //Feature
        $this->addElement('Select', 'featured', array(
            'label' => 'Featured',
            'multiOptions' => array(
                '' => '',
                '1' => 'Only Featured Videos',
                '0' => 'Only Not Featured Videos',
            ),
            'value' => '',
        ));

        $videoTypes[''] = '';
        foreach (Ynultimatevideo_Plugin_Factory::getAllSupportTypes() as $key => $type) {
            $videoTypes[$key] = $type;
        }

        //Feature
        $this->addElement('Select', 'type', array(
            'label' => 'Type',
            'multiOptions' => $videoTypes,
            'value' => '',
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