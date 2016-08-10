<?php
class Ynbusinesspages_Form_Admin_Comparison_Edit extends Ynbusinesspages_Form_Admin_Comparison_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Header');
        $this->setDescription('');
        $this->submit_btn->setLabel('Edit Header');
    }
}