<?php
class Ynfeed_AdminWelcomeController extends Core_Controller_Action_Admin {
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfeed_admin_main', array(), 'ynfeed_admin_main_welcome');
	}

	public function indexAction() {
		if ($this -> getRequest() -> isPost()) {
			$values = $this -> getRequest() -> getPost();
			if (isset($values['ids']) && $values['ids']) {
				$ids = explode(',', $values['ids']);
				foreach ($ids as $id) {
					$content = Engine_Api::_() -> getItem('ynfeed_welcome', $id);
					if ($content)
						$content -> delete();
				}
			}
		}
		$this -> view -> contents = Engine_Api::_() -> getDbtable('welcomes', 'ynfeed') -> getWelcomes();
		$this -> view -> contentsShow = Engine_Api::_() -> getDbtable('welcomes', 'ynfeed') -> getWelcomes(array('show' => 1));
	}

	public function sortAction() {
		$contents = Engine_Api::_() -> getDbtable('welcomes', 'ynfeed') -> getWelcomes();
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $value) {
			$content_id = substr($value, strrpos($value, '_') + 1);
			foreach ($contents as $item) {
				if ($item -> welcome_id == $content_id) {
					$item -> order = $i;
					$item -> save();
				}
			}
		}
	}

	public function showAction() {
		$id = $this -> getRequest() -> getParam('id', 0);
		$value = $this -> getRequest() -> getParam('value');
		if ($id && $content = Engine_Api::_() -> getItem('ynfeed_welcome', $id)) {
			$content -> show = $value;
			$content -> save();
		}
	}

	public function showMultiAction() {
		$contents = Engine_Api::_() -> getDbtable('welcomes', 'ynfeed') -> getWelcomes();
		$value = $this -> getRequest() -> getParam('value');
		foreach ($contents as $item) 
		{
			$item -> show = $value;
			$item -> save();
		}
	}

	public function deleteContentAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$content_id = $this -> _getParam('content_id');
		$this -> view -> content_id = $content_id;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try {
				$content = Engine_Api::_() -> getItem('ynfeed_welcome', $content_id);
				if ($content)
					$content -> delete();
				$db -> commit();
			} catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		// Output
		$this -> renderScript('admin-welcome/delete.tpl');
	}

	public function addContentAction() {
		$this -> view -> form = $form = new Ynfeed_Form_Admin_Welcome();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$params = $form -> getValues();
		$params['user_id'] = Engine_Api::_() -> user() -> getViewer() -> getIdentity();

		// Serialize Arrays in to JSON arrays
		$params['networks'] = json_encode($params['networks']);
		$params['member_levels'] = json_encode($params['member_levels']);

		$content = Engine_Api::_() -> getDbtable('welcomes', 'ynfeed') -> createRow();
		$content -> setFromArray($params);
		$content -> save();
		
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
	}

	public function editContentAction() {
		$content_id = $this -> _getParam('content_id');
		$content = Engine_Api::_() -> getItem('ynfeed_welcome', $content_id);
		if(!$content)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		// Convert JSON to Arrays in Order to Prepopulate
	    $content['networks'] = json_decode($content['networks']);
	    $content['member_levels'] = json_decode($content['member_levels']);
		$this -> view -> form = $form = new Ynfeed_Form_Admin_Welcome();
		$form -> setTitle('Edit Welcome Content');
		if (!$this -> getRequest() -> isPost()) {
			$form -> populate($content -> toarray());
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		$params = $form->getValues();
      
      // Serialize Arrays in to JSON arrays
      $params['networks'] = json_encode($params['networks']);
      $params['member_levels'] = json_encode($params['member_levels']);
      
      $content->setFromArray($params);
      $content->save();
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
	}

}
