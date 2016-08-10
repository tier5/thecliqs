<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListTopMembersController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$signaturesTable = Engine_Api::_ ()->getDbtable ( 'signatures', 'ynultimatevideo' );
		$numberOfMember = $this->_getParam ( 'numberOfMembers', 5 );

		$signaturesSelect = $signaturesTable->select ()->where ( 'user_id != 0' )->where ( 'video_count > 0' )->order ( 'video_count DESC' )->limit ( $numberOfMember );

		// do not show members have 0 video
		$videoSignatures = $signaturesTable->fetchAll ( $signaturesSelect );
		if (count ( $videoSignatures ) == 0) {
			return $this->setNoRender ();
		}
		$this->view->videoSignatures = $videoSignatures;
	}
}