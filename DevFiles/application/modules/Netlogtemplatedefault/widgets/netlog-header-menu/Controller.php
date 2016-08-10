<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplatedefault
 * @copyright  Copyright 2010-2012 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     altrego aka Vadim ( provadim@gmail.com )
 */

class Netlogtemplatedefault_Widget_NetlogheadermenuController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

	if( $this->view->viewer->getIdentity() ) {
		$tblSettings = Engine_Api::_()->getDbtable('userstatus', 'Netlogtemplatedefault');

		$user_status = $tblSettings->getStatus($viewer->getIdentity());

		if ( !empty($user_status) ) {
			if( $user_status=='online' ) {
				$status_class = 'online';
			} elseif ( $user_status=='away' || $user_status=='out to lunch' ) {
				$status_class = 'away';
			} elseif ( $user_status=='busy' || $user_status=='unavailable' ) {
				$status_class = 'busy';
			} else {
				$status_class = 'offline';
			}
			$this->view->user_status = array('status'=>$user_status, 'class'=>$status_class);
		} else {
			$this->view->user_status = array('status'=>'online', 'class'=>'online');
		}

		if ( isset($_GET['action']) && $_GET['action']=='setUserStatus' ) {
			$status_array = array('online','away','busy','out to lunch','unavailable','invisible');
			$new_status = $_GET['status'];
	
			if ( !in_array($new_status, $status_array) )
				exit();
	
			if( empty($user_status) ) {
				$tblSettings->insert(array(
					'user_id' => $viewer->getIdentity(),
					'status' => $new_status,
				));
			} else {
				$tblSettings->update(array('status' => $new_status), array(
					'user_id = ?'=>$viewer->getIdentity(),
				));
			}
			exit();
		}

		$this->view->navigation = $navigation = Engine_Api::_()
			->getApi('menus', 'core')
			->getNavigation('netlogtemplate_usermenu');

	} else {

		$form = $this->view->form = new User_Form_Login();;
		$form->setTitle(null)->setDescription(null);
		$form->removeElement('forgot');

		$form->getElement('email')->setOptions(array('tabindex'=>3));
		$form->getElement('password')->setOptions(array('tabindex'=>4));

			// Facebook login
		if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable )
			$form->removeElement('facebook');
	}

  }

}