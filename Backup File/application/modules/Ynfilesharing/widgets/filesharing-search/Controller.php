<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
class Ynfilesharing_Widget_FilesharingSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Data preload
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$params = array ();

		// Get search form
		$this->view->form = $form = new Ynfilesharing_Form_Search ();

		//$form->setAction ( $this->view->url ( array (), 'default' ) . "filesharing/" );

		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$params = $request->getParams ();
		$form->populate ( $params );
	}
}