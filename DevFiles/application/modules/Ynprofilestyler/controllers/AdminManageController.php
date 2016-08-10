<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
class Ynprofilestyler_AdminManageController extends Core_Controller_Action_Admin {
	
    public function layoutAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynprofilestyler_admin_main', array(), 'ynprofilestyler_admin_main_managelayout');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $layout = Engine_Api::_()->ynprofilestyler()->getLayout($value);
                    if (is_object($layout)) {
                        $layout->delete();
                    }
                }
            }
        }

        $params = $this->_getAllParams();
        $this->view->form = $form = new Ynprofilestyler_Form_Search;
        $form->populate($params);
        $formValues = $form->getValues();
        if (isset($params['fieldOrder'])) {
            $formValues['fieldOrder'] = $params['fieldOrder'];
        }
        if (isset($params['order'])) {
            $formValues['order'] = $params['order'];
        }
        $this->view->params = $formValues;

        $layouts = new Ynprofilestyler_Model_DbTable_Layouts;
        $select = $layouts->select()->where('publish = ?', 1);
        if (!empty($params['title'])) {
            $select = $select->where('title LIKE ?', "%{$params['title']}%");
        }
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
        $this->view->paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(10);
        $page = $this->_getParam('page', 1);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function imageAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynprofilestyler_admin_main', array(), 'ynprofilestyler_admin_main_manageimage');

        $models = new Ynprofilestyler_Model_DbTable_Images;
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $ids = array();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    array_push($ids, $value);
                }
            }
            $models->delete(array('image_id in (?)' => $ids));
        }

        $select = $models->select();
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(10);
        $page = $this->_getParam('page', 1);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynprofilestyler_admin_main', array(), 'ynprofilestyler_admin_main_managelevel');

        // Get level id
        if (null !== ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $level_id = $id = $level->level_id;

        // Make form
        $this->view->form = $form = new Ynprofilestyler_Form_Admin_Level(array('public' => false));
        $form->level_id->setValue($id);

        // Populate values
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $form->populate($permissionsTable->getAllowed('theme', $id, array_keys($form->getValues())));

        // Check post
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Check validitiy
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process

        $values = $form->getValues();

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();

        try {
            // Set permissions
            $permissionsTable->setAllowed('theme', $id, $values);

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice('Your changes have been saved.');
    }

    public function deleteLayoutAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->layout_id = $id;
        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $layout = Engine_Api::_()->ynprofilestyler()->getLayout($id);
                $layout->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'layout' => 'default-simple',
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Delete successfully.'))
                    ));
        }

        // Output
        $this->_helper->layout->setLayout('default-simple');
        $this->renderScript('admin-manage/delete-layout.tpl');
    }

    public function deleteImageAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->image_id = $id;
        // Check post
        if ($this->getRequest()->isPost()) {
            $models = new Ynprofilestyler_Model_DbTable_Images;
            $models->delete(array('image_id = ?' => $id));

            return $this->_forward('success', 'utility', 'core', array(
                        'layout' => 'default-simple',
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Delete successfully.'))
                    ));
        }

        // Output
        $this->_helper->layout->setLayout('default-simple');
        $this->renderScript('admin-manage/delete-image.tpl');
    }

    public function editLayoutAction() {
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id');
        $layout = Engine_Api::_()->ynprofilestyler()->getLayout($id);

        if (is_object($layout)) {
            $this->view->form = $form = new Ynprofilestyler_Form_Admin_EditLayout(array('layout' => $layout));

            if (!$this->getRequest()->isPost()) {
                return;
            }
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            $values = $form->getValues();
            $layout->setFromArray($values);
            $layout->save();

            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Layout saved.')),
                        'layout' => 'default-simple',
                        'parentRefresh' => true,
                    ));
        }
    }

    public function uploadAction() {
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Ynprofilestyler_Form_Admin_Upload;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $fullFilePath = $this->_getUploadedFile();
        $values = $form->getValues();

        if (!empty($values['url']) && !Zend_Uri::check($values['url'])) {
        	$form->addError('You have just entered an invalid URL');
        	return;
        }
        
        if (empty($values['url']) && !empty($values['image'])) {
            $values['url'] = $fullFilePath;
        }
        $values['creation_date'] = date('Y-m-d H:i:s');
        
        if (empty($values['url'])) {
            $form->addError('The URL must be entered or an image must be uploaded');
            return;
        }

        $models = new Ynprofilestyler_Model_DbTable_Images;
        $row = $models->createRow($values);
        $row->save();

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Add successfully.')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
                ));
    }

    public function setActiveAction() {
        $id = $this->_getParam('layout_id', null);
        if ($id) {
            $layout = Engine_Api::_()->ynprofilestyler()->getLayout($id);
            if ($layout) {
                $layout->is_active = 1 - $layout->is_active;                
                $layout->save();
                $this->view->status = 1;
                $this->view->is_active = $layout->is_active;
            } else {
                $this->view->status = 0;
            }
        } else {
            $this->view->status = 0;
        }
    }
    
    public function settingsAction() {
    	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    		->getNavigation('ynprofilestyler_admin_main', array(), 'ynprofilestyler_admin_main_globalsettings');
    	
    	$this->view->form = $form = new Ynprofilestyler_Form_Admin_GlobalSettings();
    	$settings = Engine_Api::_()->getApi('settings', 'core');
    	$form->populate(array(
    		'slideTop' => $settings->getSetting('ynps_slide_top'),
    		'slideLeft' => $settings->getSetting('ynps_slide_left'),
    		'slideWidth' => $settings->getSetting('ynps_slide_width'),
    		'slideHeight' => $settings->getSetting('ynps_slide_height'),
    		'slideDistance' => $settings->getSetting('ynps_slide_distance'),
    		'slideInterval' => $settings->getSetting('ynps_slide_interval'),
    	));
    	
    	if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
    		$values = $form->getValues();
    		
    		$settings->setSetting('ynps_slide_top', $values['slideTop']);
    		$settings->setSetting('ynps_slide_left', $values['slideLeft']);
    		$settings->setSetting('ynps_slide_width', $values['slideWidth']);
    		$settings->setSetting('ynps_slide_height', $values['slideHeight']);
    		$settings->setSetting('ynps_slide_distance', $values['slideDistance']);
    		$settings->setSetting('ynps_slide_interval', $values['slideInterval']);
    		
    		$form->addNotice('Your changes have been saved.');
    	}
    }
    
    private function _getUploadedFile()
	{
		$fullFilePath = '';
		$upload = new Zend_File_Transfer_Adapter_Http();
		if ($upload->getFileName('image'))
		{
			$destination = "/public/ynprofilestyler";
			$fullPathDestination = APPLICATION_PATH . $destination;
			if (!is_dir($fullPathDestination))
			{
				mkdir($fullPathDestination);
			}
			$upload->setDestination($fullPathDestination);
			$fullFilePath = $destination . '/' . time() . '_' . $upload->getFileName('image', false);

			$image = Engine_Image::factory();
			$image->open($_FILES['image']['tmp_name'])->resize(256, 384)->write(APPLICATION_PATH . $fullFilePath);
		}
		return $this->view->baseUrl() . $fullFilePath;
	}
}