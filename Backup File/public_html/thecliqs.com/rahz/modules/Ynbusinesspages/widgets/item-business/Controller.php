<?php
class Ynbusinesspages_Widget_ItemBusinessController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }
        $item = Engine_Api::_()->core()->getSubject();
        $businesses = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages')->getBusinesses($item);
        if (!count($businesses)) {
            return $this->setNoRender();
        }
        if (count($businesses) > 10) {
            $businesses = array_rand($businesses, 10);
        }
        $this->view->businesses = $businesses;
        $this->view->short_type = $item->getShortType();
        
    }
}