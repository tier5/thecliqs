<?php
class Ynresume_AdminResumesController extends Core_Controller_Action_Admin
{
	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresume_admin_main', array(), 'ynresume_admin_main_resumes');
	}
	
	public function featureselectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		if(!empty($ids))
		{
			$this -> view -> ids = $ids_array = explode(",", $ids);
		}
		$this->view->form = $form = new Ynresume_Form_Admin_Resume_Feature();
		
		$posts = $this->getRequest()->getPost();
		if(!empty($posts['toValues']))
		{
			$this -> view -> ids  = $ids_array =  explode(",", $posts['toValues']);
		}
		//return if not click submit or save draft
		$submit_button = $this -> _getParam('submit_button');
		if (!isset($submit_button))
		{
			return;
		}
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
        	$form -> toValues -> setValue($posts['toValues']);
            return;
        }
		
		$values = $form -> getValues();
		$toValues = $values['toValues'];
		$toValues_array = explode(",", $toValues);
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			foreach ($ids_array as $id) {
				Engine_Api::_() -> ynresume() -> featureResume($id, $values['number_feature_day']);
			}
			$db -> commit();
			
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		
		$this->_helper->redirector->gotoRoute(array('module'=>'ynresume','controller'=>'resumes', 'action' => 'index'), 'admin_default', true);
	}
	
	public function serviceselectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		if(!empty($ids))
		{
			$this -> view -> ids = $ids_array = explode(",", $ids);
		}
		$this->view->form = $form = new Ynresume_Form_Admin_Resume_Service();
		
		$posts = $this->getRequest()->getPost();
		if(!empty($posts['toValues']))
		{
			$this -> view -> ids  = $ids_array =  explode(",", $posts['toValues']);
		}
		//return if not click submit or save draft
		$submit_button = $this -> _getParam('submit_button');
		if (!isset($submit_button))
		{
			return;
		}
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
        	$form -> toValues -> setValue($posts['toValues']);
            return;
        }
		
		$values = $form -> getValues();
		$toValues = $values['toValues'];
		$toValues_array = explode(",", $toValues);
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			foreach ($ids_array as $id) {
				Engine_Api::_() -> ynresume() -> serviceResume($id, $values['number_service_day']);
			}
			$db -> commit();
			
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		
		$this->_helper->redirector->gotoRoute(array('module'=>'ynresume','controller'=>'resumes', 'action' => 'index'), 'admin_default', true);
	}
	
	public function multiselectedAction() {
		$action = $this -> _getParam('select_action', 'Delete');
		$this -> view -> action = $action;
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			switch ($action) {
				case 'Delete' :
					foreach ($ids_array as $id) {
						$resume = Engine_Api::_() -> getItem('ynresume_resume', $id);
						if (isset($resume)) {
							$resume -> delete();
						}
					}
					break;
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}
	
	public function featureAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$resumeID = $this -> _getParam('id');
		$value = $this -> _getParam('value');
        $resume = Engine_Api::_()->getItem('ynresume_resume', $resumeID);
        if (!$resume) {
            echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not find the feedback.")));
            exit ;
        }
		if ($resume) {
			
			$resume -> featured = $value;
			$resume -> modified_date = new Zend_Db_Expr("NOW()");
			$resume -> feature_expiration_date = NULL;
			$resume -> save();
			
			echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => ($value) ? Zend_Registry::get("Zend_Translate") -> _("Set feature successfully!") : Zend_Registry::get("Zend_Translate") -> _("Unset feature successfully!")));
			exit ;
		} else {
			echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not set feature this resume")));
			exit ;
		}
	}
	
	public function deleteAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$resume = Engine_Api::_() -> getItem('ynresume_resume', $this -> getRequest() -> getParam('id'));
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynresume_Form_Resume_Delete();
		if (!$resume)
		{
			return $this->_helper->requireSubject()->forward();
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$db = $resume -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$resume -> delete();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$message = Zend_Registry::get('Zend_Translate') -> _('The selected resume has been deleted.');

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array($message),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
		
	}
	
	public function getOwnerAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$ids = $this -> _getParam('ids', null);
		$ids_array = explode(",", $ids);
		$authorTable = Engine_Api::_() -> getDbTable('authors', 'ynresume');
		$arr_authors = array();
		
		foreach( $ids_array as $id )
		{
			$resume = Engine_Api::_() -> getItem('ynresume_resume', $id);
			if(!empty($resume))
			{
				$authors = $authorTable -> getAuthorsByIdeaId($resume -> getIdentity());
				foreach($authors as $author)
				{
					$user = Engine_Api::_() -> getItem('user', $author -> user_id);
					if(!empty($user) && !in_array($author -> user_id, $arr_authors))
					{
						$arr_authors[$author -> user_id] = $user -> getTitle();
					}
				}
	        	if($resume -> user_id != 0 && !in_array($resume -> user_id, $arr_authors))
				{
					$arr_authors[$resume -> getOwner() -> getIdentity()] = $resume -> getOwner() -> getTitle();
				}
			}
        }
		if(!empty($arr_authors))
		{
			echo Zend_Json::encode(array('error' => 0, 'author' => json_encode($arr_authors)));
		}
		else
		{
			echo Zend_Json::encode(array('error' => 1));
		}
	}
	public function getIdeaAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
        $resume = Engine_Api::_()->getItem('ynresume_resume', $this ->_getParam('id'));
		if(!$resume)
		{
			echo Zend_Json::encode(array('error' => 1));
		}
		else
		{
			echo Zend_Json::encode(array('error' => 0, 'title' => $resume -> getTitle(), 'description' => $resume -> getDescription()));
		}
	}
	
	public function suggestAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('ynresume_resume');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = $table->select();
    
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`name` LIKE ?', '%'. $text .'%');
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $resume ){
        	
			$data[] = array(
                'id' => $resume->getIdentity(),
                'label' => $resume->getTitle(), 
                'title' => $resume->getTitle(),
                'url' => $resume->getHref(),
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
	
	
	
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynresume_Form_Admin_Search();
		$params = $this ->_getAllParams();
		unset($params['module']);
		unset($params['controller']);
		unset($params['action']);
		unset($params['rewrite']);
		
		if(empty($params['order']))
		{
			$params['order'] = 'resume.name';
		}
		if(empty($params['direction'])) 
		{
			$params['direction'] = 'DESC';
		}
		
		$params['search'] = true;
		$this -> view -> formValues = $params;
		$form -> populate($params);
		// Get Ideas Paginator
		$resumeTbl = Engine_Api::_()->getItemTable('ynresume_resume');
		$this -> view -> paginator = $resumeTbl -> getResumesPaginator($params);
		$this -> view -> paginator->setItemCountPerPage(20);
		if(isset($params['page']))
		{
			$this->view->paginator->setCurrentPageNumber($params['page']);
		} 

	}
	
}