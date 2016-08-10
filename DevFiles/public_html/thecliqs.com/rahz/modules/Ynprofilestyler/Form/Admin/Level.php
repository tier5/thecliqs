<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        parent::init();

        // My stuff
        $this->setTitle('Member Level Settings')->setDescription('YNPROFILESTYLER_FORM_ADMIN_LEVEL_DESCRIPTION');

        // Element: view
        $this->addElement('Radio', 'edit', array(
            'label' => 'Allow Editing Of Theme?',
            'description' => 'YNPROFILESTYLE_FORM_ADMIN_LEVEL_EDITING_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => 'Yes, allow users to edit their themes.',
                0 => 'No, do not allow users to edit their themes.',
            ),
        ));
        
        if (!$this->_public) {
            $publicLevel = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel();
            unset($this->getElement('level_id')->options[$publicLevel->level_id]);
        }
    }
}