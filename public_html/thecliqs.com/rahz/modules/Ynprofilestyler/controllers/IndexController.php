<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_IndexController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (!$this->_helper->requireUser()->isValid())
		{
			return;
		}

		$this->_helper->layout->disableLayout();
	}

	public function indexAction()
	{
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		$ruleGroups = new Ynprofilestyler_Model_DbTable_Rulegroups;
		$select = $ruleGroups->select()->where('enabled = ?', 1)->where('published = ?', 1)->order('ordering')->order('title');
		$this->view->groups = $ruleGroups->fetchAll($select);

		$users = new Ynprofilestyler_Model_DbTable_Users;
		$select = $users->select()->where('user_id = ?', $viewer->getIdentity())->where('enabled = ?', 1);
		$user = $users->fetchRow($select);
		$isAllowed = 0;
		if (is_object($user))
		{
			$isAllowed = $user->is_allowed;
		}
		$this->view->isAllowed = $isAllowed;
	}

	public function addAction()
	{
		$this->_helper->layout->setLayout('default-simple');
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form = $form = new Ynprofilestyler_Form_Custom_Add;
		if ($this->getRequest()->isPost())
		{
			if (!$form->isValid($this->getRequest()->getPost()))
			{
				return;
			}
			$layouts = new Ynprofilestyler_Model_DbTable_Layouts;
			$layout = $layouts->createRow();
			$tempLayout = Engine_Api::_()->ynprofilestyler()->getTempLayout();
				
			$fullFilePath = $this->_getUploadedFile();
			$values = $form->getValues();
			$values['user_id'] = $viewer->getIdentity();
			$values['publish'] = 1;
			$values['creation_date'] = date('Y-m-d H:i:s');
			if (!empty($values['thumbnail']))
			{
				$values['thumbnail'] = $fullFilePath;
			}
			$layout->setFromArray($values);
			$layout->save();
			$layout->saveRulesFromLayout($tempLayout);

			return $this->_forward('success', 'utility', 'core', array(
					'messages'      => array(Zend_Registry::get('Zend_Translate')->_('Add successfully.')),
					'layout'        => 'default-simple',
					'parentRefresh' => true,
			));
		}
	}

	public function themesAction()
	{
		$model = new Ynprofilestyler_Model_DbTable_Layouts;
		$select = $model->select()->where('publish = ?', 1)->where('is_active = ?', 1);
		$this->view->themes = $model->fetchAll($select);
	}

	public function slideshowAction()
	{
		$slideshow = Engine_Api::_()->ynprofilestyler()->getViewerSlideshow();
		$this->view->form = $form = new Ynprofilestyler_Form_Custom_SlideshowConfigure();

		$slideshowCfg = Zend_Json_Decoder::decode($slideshow->configure);
		if ($slideshowCfg != NULL) 
		{
			$form->populate($slideshowCfg);
		} 
		else 
		{
			$settings = Engine_Api::_()->getApi('settings', 'core');
			$form->populate(array(
				'slideTop' => $settings->getSetting('ynps.slide_top'),
				'slideLeft' => $settings->getSetting('ynps.slide_left'),
				'slideWidth' => $settings->getSetting('ynps.slide_width'),
				'slideHeight' => $settings->getSetting('ynps.slide_height'),
				'slideDistance' => $settings->getSetting('ynps.slide_distance'),
				'slideInterval' => $settings->getSetting('ynps.slide_interval'),
			));	
		}
		
		$this->view->slides = $slides = $slideshow->getSlides();
	}

	public function customAction()
	{
		$model = new Ynprofilestyler_Model_DbTable_Rulegroups;
		$select = $model->select()->where('enabled = ?', 1)->where('published = ?', 1)->order('ordering')->order('title');
		$this->view->groups = $groups = $model->fetchAll($select);
	}

	public function customBackgroundAction()
	{
		$this->view->form = $form = new Ynprofilestyler_Form_Custom_Background;
		$this->view->formUpload = new Ynprofilestyler_Form_Custom_Upload;
	}

	public function customHeaderAction()
	{
		$this->view->form = $form = new Ynprofilestyler_Form_Custom_Header;
	}

	public function customWidgetAction()
	{
		$this->view->formFrame = $formFrame = new Ynprofilestyler_Form_Custom_Widget_Frame;
		$this->view->formHeader = $formHeader = new Ynprofilestyler_Form_Custom_Widget_Header;
		$this->view->formText = $formText = new Ynprofilestyler_Form_Custom_Widget_Text;
		$this->view->formLink = $formLink = new Ynprofilestyler_Form_Custom_Widget_Link;
	}

	public function customMenuBarAction()
	{
		$this->view->form = $form = new Ynprofilestyler_Form_Custom_MenuBar;
	}

	public function customTextAction()
	{
		$this->view->formContent = $formContent = new Ynprofilestyler_Form_Custom_Text_Content;
		$this->view->formLink = $formLink = new Ynprofilestyler_Form_Custom_Text_Link;
		$this->view->formUsername = $formUsername = new Ynprofilestyler_Form_Custom_Text_Username;
	}

	public function customLinkAction()
	{
		$this->view->form = $form = new Ynprofilestyler_Form_Custom_Link;
	}

	public function customTabBarAction()
	{
		$this->view->form = $form = new Ynprofilestyler_Form_Custom_TabBar;
	}

	public function getRulesAction()
	{
		$rules = Engine_Api::_()->ynprofilestyler()->getAllRuleSettings();

		$viewer = Engine_Api::_()->user()->getViewer();
		$layoutId = $this->_getParam('layout_id', null);
		if ($layoutId == null) {
			$layout = Engine_Api::_()->ynprofilestyler()->getLayoutFromUser($viewer->getIdentity());
		} else {
			$layout = Engine_Api::_()->ynprofilestyler()->getLayout($layoutId);
		}

		if (is_object($layout))
		{
			foreach ($layout->getRules() as $rule)
			{
				foreach ($rules as $index => $r)
				{
					if ($r['rule_id'] == $rule->rule_id)
					{
						$rules[$index]['value'] = $rule->value;
						break;
					}
				}
			}
		}

		return $this->_helper->json($rules);
	}

	public function getAllRulesAction()
	{
		$rules = Engine_Api::_()->ynprofilestyler()->getAllRuleSettings();
		return $this->_helper->json($rules);
	}

	public function getDomPathAction()
	{
		$ruleIds = $this->_getParam('ruleIds');
		$model = new Ynprofilestyler_Model_DbTable_Rules;
		$select = $model->select()->where('rule_id in (?)', $ruleIds);
		$domPaths = array();
		foreach ($model->fetchAll($select) as $rule)
		{
			array_push($domPaths, $rule->dompath);
		}

		return $this->_helper->json($domPaths);
	}

	public function saveTempAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->isAdmin()) {
			$layout = Engine_Api::_()->ynprofilestyler()->getTempLayout();
			$rules = $this->_getParam('rules', null);
			$layout->saveRules($rules);
			$this->_helper->content->setNoRender();
		}
	}

	public function saveAction()
	{
		if (Engine_Api::_()->authorization()->isAllowed('theme', null, 'edit'))
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$db = Engine_Db_Table::getDefaultAdapter();

			$db->beginTransaction();
			try
			{
				// save rules belonging to a layout
				$layout = Engine_Api::_()->ynprofilestyler()->getViewerLayout();
				$rules = $this->_getParam('rules', null);
				$layout->saveRules($rules);

				// save the layout
				$users = new Ynprofilestyler_Model_DbTable_Users;
				$user = $users->fetchRow(array('user_id = ?' => $viewer->getIdentity()));
				if (!$user)
				{
					$data = array(
							'user_id'    => $viewer->getIdentity(),
							'layout_id'  => $layout->getIdentity(),
							'is_allowed' => $this->_getParam('allowOtherUse', 0)
					);

					$user = $users->createRow($data);
					$user->save();
				}
				else
				{
					$user->is_allowed = $this->_getParam('allowOtherUse', 0);
					$user->save();
				}

				// Get current row
				$this->_saveStylesToProfile($rules);

				// Save slideshow configuration
				$slideshowCfg = $this->_getParam('slideshowCfg', NULL);
				if ($slideshowCfg != NULL) {
					$slideshow = Engine_Api::_()->ynprofilestyler()->getViewerSlideshow();
					$cfg = array();
					foreach($slideshowCfg as $config) {
						$cfg[$config['name']] = $config['value'];
					}
					$slideshow->configure = json_encode($cfg);
					$slideshow->save();
					
					// loop slides in slideshow, set transition
					
				}

				$db->commit();

				$data = array(
						'result'  => 1,
						'message' => Zend_Registry::get('Zend_Translate')->_('Save successfully.')
				);
			}
			catch (Exception $e)
			{
				$db->rollBack();
				$data = array(
						'status'  => 0,
						'message' => $e->getMessage()
				);
				return $this->_helper->json($data);
			}
			return $this->_helper->json($data);
		}
	}

	public function uploadAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();

		$this->view->form = $form = new Ynprofilestyler_Form_Custom_Upload;

		if (!$this->getRequest()->isPost())
		{
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost()))
		{
			return;
		}

		$values = $form->getValues();

		if (!empty($values['background_file']))
		{
			$file = $form->background_file->getFileName();
			$name = basename($file);
			$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
			$params = array(
					'parent_id'   => $viewer->getIdentity(),
					'parent_type' => $viewer->getType()
			);
			// Save
			$storage = Engine_Api::_()->storage();

			// Resize image (main)
			$image = Engine_Image::factory();
			$image->open($file)->write($path . '/m_' . $name)->destroy();

			// Store
			$iMain = $storage->create($path . '/m_' . $name, $params);

			// Remove temp files
			@unlink($path . '/m_' . $name);

			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Save successfully.');
			$this->view->url = $iMain->map();
			
		}
	}

	public function useUserLayoutAction()
	{
		if (Engine_Api::_()->authorization()->isAllowed('theme', null, 'edit'))
		{
			$userId = $this->_getParam('user_id');
			if (Engine_Api::_()->ynprofilestyler()->isUserLayoutAllowedApply($userId))
			{
				$layout = Engine_Api::_()->ynprofilestyler()->getLayoutFromUser($userId);

				if (is_object($layout))
				{
					$db = Engine_Db_Table::getDefaultAdapter();
					$db->beginTransaction();
					try
					{
						$viewerLayout = Engine_Api::_()->ynprofilestyler()->getViewerLayout();
						$viewerLayout->saveRulesFromLayout($layout);
						$rules = array();
						foreach ($layout->getRules() as $rule)
						{
							if (!empty($rule->value))
							{
								$data = array(
										'rule_id' => $rule->getIdentity(),
										'value'   => $rule->value
								);
								array_push($rules, $data);
							}
						}

						// Get current row
						$this->_saveStylesToProfile($rules);

						$db->commit();
					}
					catch (Exception $e)
					{
						$db->rollBack();
						throw $e;
					}
				}

				$data = array(
						'result'  => 1,
						'message' => Zend_Registry::get('Zend_Translate')->_('Save successfully.')
				);

				return $this->_helper->json($data);
			}
		}
	}

	public function insertAction()
	{
		$rules = new Ynprofilestyler_Model_DbTable_Rules;
		$ruleOptions = new Ynprofilestyler_Model_DbTable_Ruleoptions;

		foreach ($rules->fetchAll() as $rule)
		{
			if ($rule->control_type == 'select')
			{
				switch ($rule->name)
				{
					case 'font-size':
						$options = Ynprofilestyler_Plugin_Constants::getFontSizeMultiOptions();
						break;
					case 'border-style':
						$options = Ynprofilestyler_Plugin_Constants::getBorderStyleMultiOptions();
						break;
					case 'font-family':
						$options = Ynprofilestyler_Plugin_Constants::getFontFamilyMultiOptions();
						break;
					case 'border-width':
						$options = Ynprofilestyler_Plugin_Constants::getBorderWidthMultiOptions();
						break;
					case 'font-weight':
						$options = Ynprofilestyler_Plugin_Constants::getFontWeightMultiOptions();
						break;
					case 'font-style':
						$options = Ynprofilestyler_Plugin_Constants::getFontStyleMultiOptions();
						break;
					case 'text-decoration':
						$options = Ynprofilestyler_Plugin_Constants::getTextDecorationMultiOptions();
						break;
					case 'background-repeat':
						$options = Ynprofilestyler_Plugin_Constants::getBackgroundRepeatMultiOptions();
						break;
					case 'background-position':
						$options = Ynprofilestyler_Plugin_Constants::getBackgroundPositionMultiOptions();
						break;
					case 'background-attachment':
						$options = Ynprofilestyler_Plugin_Constants::getBackgroundAttachmentMultiOptions();
						break;
				}

				if (isset($options) && is_array($options))
				{
					foreach ($options as $key => $value)
					{
						$option = $ruleOptions->createRow(array(
								'rule_id'      => $rule->rule_id,
								'option_label' => $value,
								'option_value' => $key
						));
						$option->save();
					}
				}

			}
		}
	}

	public function uploadSlideAction() {
		$viewer = Engine_Api::_()->user()->getViewer();

		$this->view->form = $form = new Ynprofilestyler_Form_Custom_Upload(
				$this->view->url(array(
						'module' => 'ynprofilestyler',
						'controller' => 'index',
						'action' => 'upload-slide'), 'default'));

		if (!$this->getRequest()->isPost())
		{
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost()))
		{
			return;
		}

		$values = $form->getValues();

		if (!empty($values['background_file']))
		{
			$file = $form->background_file->getFileName();
			$name = basename($file);
			$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
			$params = array(
					'parent_id'   => $viewer->getIdentity(),
					'parent_type' => $viewer->getType()
			);
			// Save
			$storage = Engine_Api::_()->storage();

			// Resize image (main)
			$image = Engine_Image::factory();
			$image->open($file)->write($path . '/m_' . $name)->destroy();

			// Store
			$iMain = $storage->create($path . '/m_' . $name, $params);

			// Remove temp files
			@unlink($path . '/m_' . $name);

			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Save successfully.');
				
			$this->view->url = $url = $iMain->storage_path;
				
			Engine_Api::_()->ynprofilestyler()->addSlide($url);
		}
	}

	public function deleteSlidesAction() {
		$slideIds = $this->_getParam('ids', NULL);
		if (!empty($slideIds)) {
			$slideshow = Engine_Api::_()->ynprofilestyler()->getViewerSlideshow();
			$result = ($slideshow->deleteSlides($slideIds) > 0);
				
			$this->view->result = $result;
		}
	}

	public function publishSlidesAction() {
		$slideIds = $this->_getParam('ids', NULL);
		$published = $this->_getParam('published', NULL);
		if (!empty($slideIds) && $published != NULL) {
			$slideshow = Engine_Api::_()->ynprofilestyler()->getViewerSlideshow();
			$result = ($slideshow->publishSlides($slideIds, $published) > 0);

			$this->view->result = $result;
		}
	}

	private function _getUploadedFile() {
		$fullFilePath = '';
		$upload = new Zend_File_Transfer_Adapter_Http();
		if ($upload->getFileName('thumbnail'))
		{
			$destination = "/public/ynprofilestyler";
			$fullPathDestination = APPLICATION_PATH . $destination;
			if (!is_dir($fullPathDestination))
			{
				mkdir($fullPathDestination);
			}
			$upload->setDestination($fullPathDestination);
			$fullFilePath = $destination . '/' . time() . '_' . $upload->getFileName('thumbnail', false);

			$image = Engine_Image::factory();
			$image->open($_FILES['thumbnail']['tmp_name'])->resize(256, 384)->write(APPLICATION_PATH . $fullFilePath);
		}
		return $fullFilePath;
	}

	private function _saveStylesToProfile($rules)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_()->getDbtable('styles', 'core');
		$select = $table->select()->where('type = ?', $viewer->getType())->where('id = ?', $viewer->getIdentity())->limit();

		$row = $table->fetchRow($select);
		if (null == $row)
		{
			$row = $table->createRow();
			$row->type = $viewer->getType();
			$row->id = $viewer->getIdentity();
		}
		$row->style = Engine_Api::_()->ynprofilestyler()->getStyleStringFromRules($rules);

		$row->save();
	}
}