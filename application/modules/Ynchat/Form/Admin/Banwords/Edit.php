<?php
class Ynchat_Form_Admin_Banwords_Edit extends Ynchat_Form_Admin_Banwords_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Ban Word');
    //    $this->setDescription('YNCHAT_BANWORDS_EDIT_DESCRIPTION');
        $this->submit->setLabel('Edit');
    }
}