<?php
class Ynmusic_AdminSettingsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_settings_global');
		
		$settings = Engine_Api::_()->getApi('settings', 'core');
    	$this->view->form = $form = new Ynmusic_Form_Admin_Settings_Global();
     	if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
        	$values = $form->getValues();
        	foreach ($values as $key => $value) {
	        	if($value != ''){
	            	$settings->setSetting($key, $value);
				}	
        	}
			$settings->setSetting("ynmusic_x_value", $this ->_getParam('x_value', 0));
			$settings->setSetting("ynmusic_y_value", $this ->_getParam('y_value', 0));
	        $form->addNotice('Your changes have been saved.'); 
    	}
	}
	
    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_settings_level');

       	// Get level id
		if (null !== ($id = $this -> _getParam('level_id'))) {
			$level = Engine_Api::_() -> getItem('authorization_level', $id);
		} else {
			$level = Engine_Api::_() -> getItemTable('authorization_level') -> getDefaultLevel();
		}
		if (!$level instanceof Authorization_Model_Level) {
			throw new Engine_Exception('missing level');
		}
		$id = $level -> level_id;
		// Make form
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Settings_Level( array('public' => ( in_array($level -> type, array('public'))), 'moderator' => ( in_array($level -> type, array('admin', 'moderator'))), ));
		$form -> level_id -> setValue($id);
		$permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
 		
		$elements = array(
			'album' => array('create', 'edit', 'delete', 'view', 'comment', 'max_songs', 'auth_view', 'auth_comment', 'auth_download'),
			'playlist' => array('create', 'edit', 'delete', 'view', 'comment', 'max_songs', 'auth_view', 'auth_comment'),
			'song' => array('create', 'edit', 'delete', 'view', 'comment', 'max_filesize', 'max_storage', 'auth_view', 'auth_comment', 'auth_download')
		);
		foreach ($elements as $key => $arr) {
			foreach ($arr as $ele) {
				$eleKey = 	$key.'_'.$ele;
				if ($form->getElement($eleKey))
					$form->getElement($eleKey)->setValue($permissionsTable->getAllowed('ynmusic_'.$key, $id, $ele));
			}
		}
		// get max allow
		if ($level->type != 'public') {
			
			$numberElements = array(
				'album' => array('max_songs'),
				'playlist' => array('max_songs'),
				'song' => array('max_filesize', 'max_storage')
			);
			
			foreach ($numberElements as $key => $numberFieldArr) {
				foreach ($numberFieldArr as $numberField) {
	                if ($permissionsTable->getAllowed('ynmusic_'.$key, $id, $numberField) == null) {
	                    $row = $permissionsTable->fetchRow($permissionsTable->select()
	                    ->where('level_id = ?', $id)
	                    ->where('type = ?', 'ynmusic_'.$key)
	                    ->where('name = ?', $numberField));
	                    if ($row) {
	                    	$eleKey = 	$key.'_'.$numberField;
	                        $form->$eleKey->setValue($row->value);
	                    }
	                }
	            }
			}
			
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
                $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit"); 
				
				$creditElements = array('first_amount', 'first_credit', 'credit', 'max_credit', 'period');
				$type = array();
				$credit = array();
				foreach ($elements as $key=>$value) {
	                $select = $typeTbl->select()->where('module = ?', 'ynmusic')->where('action_type = ?', 'ynmusic_'.$key)->limit(1);
	                $type[$key] = $typeTbl -> fetchRow($select);
	                
	                if(empty($type[$key])) {
	                    $type[$key] = $typeTbl->createRow();
	                    $type[$key]->module = 'ynmusic';
	                    $type[$key]->action_type = 'ynmusic_'.$key;
	                    $type[$key]->group = 'earn';
	                    $type[$key]->content = 'Creation %s '.$key;
	                    $type[$key]->credit_default = 5;
						if (in_array($key, array('album', 'song'))) {
							$type[$key]->link_params = '{"route":"ynmusic_song","action":"upload"}';
						}
	                    $type[$key]->save();
	                }
	                         
	                $select = $creditTbl->select()
	                    ->where("level_id = ? ", $id)
	                    ->where("type_id = ?", $type[$key]->type_id)
	                    ->limit(1);
	                $credit[$key] = $creditTbl->fetchRow($select);
	                if(empty($credit[$key])) {
	                    $credit[$key] = $creditTbl->createRow();
	                }
	                else {
	                	foreach ($creditElements as $ele) {
	                		$form->getElement($key.'_'.$ele)->setValue($credit[$key]->$ele);
	                	}
	                }
                }
            }
		}
		$this -> view -> form = $form;
		// Check post
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		// Check validitiy
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		// Process
		$values = $form -> getValues();
		$db = $permissionsTable -> getAdapter();
		$db -> beginTransaction();
		if ($level->type != 'public') {
			try {
				if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
	                $creditValues['song'] = array_slice($values, 0, 5);
	                $creditValues['album'] = array_slice($values, 5, 5);
					$creditValues['playlist'] = array_slice($values, 10, 5);
	                $permissionValues = array_slice($values, 15);
	                
					foreach ($elements as $key=>$value) {
						$credit[$key]->level_id = $id;
						$credit[$key]->type_id = $type[$key]->type_id;
						foreach ($creditValues[$key] as $index=>$value) {
							$index = explode('_', $index, 2);
							if ($index[0] == $key)
								$credit[$key]->$index[1] = $value;
						}
						$credit[$key]->save();
					}
	            }
	            else {
	                $permissionValues = $values;
	            }
				// Set permissions
				foreach ($permissionValues as $key => $value) {
					$key = explode('_', $key, 2);
					$permissionsTable->setAllowed('ynmusic_'.$key[0], $id, $key[1], $value);
					
				}
				// Commit
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
		}
		else {
			try {
                foreach ($values as $key => $value) {
					$key = explode('_', $key, 2);
					$permissionsTable->setAllowed('ynmusic_'.$key[0], $id, $key[1], $value);
					
				}
                 // Commit
                $db->commit();
            }
            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
		} 
		$form->addNotice($this->view->translate('Your changes have been saved.'));
    }
}