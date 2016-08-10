<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Search extends Engine_Form {

    public function init() {
        $this->setAttribs(array('class' => 'global_form_box', 'id' => 'search_form'))->setMethod('GET');

        $this->addElement('Text', 'title', array(
            'label' => 'Theme Title',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('StringLength', true, array(1, 50)),
            ),
        ));

        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
