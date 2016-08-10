<?php
class Ynlistings_Widget_HighlightListingController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        $table = Engine_Api::_()->getItemTable('ynlistings_listing');
        $hightlight_listing = $table->fetchRow($table->select()
        ->where('highlight = ?', 1)
        ->where('status = ?', 'open')
        ->where('approved_status = ?', 'approved'));
        if ($hightlight_listing) {
            $this->view->listing = $hightlight_listing;
        }
        else {
            $this->setNoRender();
        }
    }
}
