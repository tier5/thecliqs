<?php
class Ynmusic_Form_Admin_Genres_Edit extends Ynmusic_Form_Admin_Genres_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Genre');
        $this->submit_btn->setLabel('Edit Genre');
    }
}