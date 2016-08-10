<?php
class Ynbusinesspages_Widget_BusinessCreateLinkController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$businessId = $business_session -> businessId;
		// Must be logged-in
		if (!$viewer -> getIdentity() || !empty($businessId)) {
			return $this -> setNoRender();
		}
		if (!Engine_Api::_() -> authorization() -> isAllowed('ynbusinesspages_business', $viewer, 'create')) {
			return $this -> setNoRender();
		}
	}

}
