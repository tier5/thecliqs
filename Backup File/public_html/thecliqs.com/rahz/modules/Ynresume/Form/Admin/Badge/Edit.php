<?php
class Ynresume_Form_Admin_Badge_Edit extends Ynresume_Form_Admin_Badge_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Badge');
        $this->submit_btn->setLabel('Edit');
        $this->photo->setRequired(false);
    }
}