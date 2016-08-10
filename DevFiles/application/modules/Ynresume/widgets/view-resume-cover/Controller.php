<?php
class Ynresume_Widget_ViewResumeCoverController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		if(isset($params['endorse']))
		{
			$this -> view -> endorse = true;
		}
		else
		{
			$this -> view -> endorse = false;
		}
	}
}
