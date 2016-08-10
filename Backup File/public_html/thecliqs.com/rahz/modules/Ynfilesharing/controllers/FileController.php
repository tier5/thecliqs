<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
require_once ('Scribd/scribd.php');

class Ynfilesharing_FileController extends Core_Controller_Action_Standard
{
	protected static $_roles_for_group = array(
		'owner',
		'parent_member',
		'registered',
		'everyone'
	);

	protected static $_roles_for_user = array(
		'owner',
		'owner_member',
		'owner_member_member',
		'owner_network',
		'registered',
		'everyone'
	);

	protected static $_baseUrl;
	public static function getBaseUrl()
	{
		if (self::$_baseUrl == NULL)
		{
			$request = Zend_Controller_Front::getInstance() -> getRequest();
			self::$_baseUrl = sprintf('%s://%s', $request -> getScheme(), $request -> getHttpHost());
		}
		return self::$_baseUrl;
	}

	/**
	 *
	 * @param string $type
	 * @return string
	 */
	public function selfURL()
	{
		return self::getBaseUrl();
	}

	public function viewAction($oFile = null)
	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (is_null($oFile))
			$fileId = $this -> _getParam('file_id', 0);
		if ($fileId != 0)
		{
			if (is_null($oFile))
				$file = Engine_Api::_() -> getItem('ynfilesharing_file', $fileId);
			else
				$file = $oFile;
			$folder = Engine_Api::_() -> getItem('folder', $file -> folder_id);
		}

		if ($file)
		{
			Engine_Api::_() -> core() -> setSubject($file);
		}

		$is_success = 1;
		$status = 'PROCESSING';

		// get settings
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$api_viewer = $settings -> getSetting('ynfilesharing.apiviewer', 1);
		$scribd_api_key = $settings -> getSetting('ynfilesharing.apikey');
		$scribd_secret = $settings -> getSetting('ynfilesharing.apisecret');
		$this -> view -> mode = $settings -> getSetting('ynfilesharing.mode', 'list');
		$this -> view -> width = $settings -> getSetting('ynfilesharing.width', 'auto');
		$this -> view -> height = $settings -> getSetting('ynfilesharing.height', 'auto');
		$this -> view -> is_embed = 1;
		$this -> view -> is_support = 1;
		$this -> view -> is_image = 0;
		$this -> view -> status = $status;
		$this -> view -> is_success = $is_success;
		$this -> view -> file = $file;
		$this -> view -> folder = $folder;
		$this -> view -> api_viewer = $api_viewer;

		$file_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $folder -> path . $file -> name;
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		if (!$folder -> isAllowed($viewer, 'view'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}

		// Increase view count
		$file -> view_count += 1;
		$file -> save();

		$arr_ext = array(
			'doc',
			'docx',
			'pdf',
			'xls',
			'xlsx',
			'txt',
			'rtf',
			'ps',
			'pps',
			'ppt',
			'pptx',
			'odt',
			'sxw',
			'odp',
			'sxi',
			'ods',
			'sxc',
			'fodt',
			'fods',
			'fodp',
			'odb',
			'odg',
			'fodg',
			'odf',
			'odt',
			'ods',
			'odp'
		);
		if (!in_array($file -> ext, $arr_ext))
		{
			$this -> view -> is_support = 0;
			$arr_img = array(
				'tif',
				'jpg',
				'png',
				'bmp'
			);
			if (in_array($file -> ext, $arr_img))
			{
				$this -> view -> is_image = 1;
				$path = str_replace(DIRECTORY_SEPARATOR, '/', $folder -> path);
				$this -> view -> image = $this -> view -> baseUrl() . '/' . $path . $file -> name;
			}
		}
		else
		{
			if($api_viewer)
			{
				if ($scribd_api_key == null || $scribd_secret == null)
				{
					$this -> view -> is_embed = 0;
					return;
				}
				$doc_type = null;
				$access = 'private';
				$rev_id = null;
				$scribd = new Scribd($scribd_api_key, $scribd_secret);
	
				try
				{
					$db = Engine_Api::_() -> getDbtable('documents', 'ynfilesharing') -> getAdapter();
					$is_uploaded = Engine_Api::_() -> getDbtable('documents', 'ynfilesharing') -> checkFileUploaded($file -> getIdentity());
	
					if (!$is_uploaded)
					{
						$data = $scribd -> upload($file_path, $doc_type, $access, $rev_id);
						if (is_array($data))
						{
							$tbl_documents = Engine_Api::_() -> getDbtable('documents', 'ynfilesharing');
							$row = $tbl_documents -> createRow();
							$row -> document_id = $file -> getIdentity();
							$row -> doc_id = $data['doc_id'];
							$row -> access_key = $data['access_key'];
							if ($data['secret_password'])
							{
								$row -> secret_password = $data['secret_password'];
							}
							$row -> save();
							$is_success = 1;
						}
						else
						{
							$is_success = 0;
						}
					}
	
					// file is existed in database
					if ($is_uploaded)
					{
						// check if file is existed on Scribd
						$document = Engine_Api::_() -> getItem('ynfilesharing_document', $file -> getIdentity());
						if (is_object($document))
						{
							$data = $scribd -> getSettings($document -> doc_id);
							if (!is_array($data))
							{
								$data = $scribd -> upload($file_path, $doc_type, $access, $rev_id);
								if (is_array($data))
								{
									$document -> doc_id = $data['doc_id'];
									$document -> access_key = $data['access_key'];
									if ($data['secret_password'])
									{
										$document -> secret_password = $data['secret_password'];
									}
									$document -> save();
									$is_success = 1;
								}
								else
								{
									$is_success = 0;
								}
							}
							$status = $scribd -> getConversionStatus($document -> doc_id);
						}
					}
	
					if ($is_success == 1)
					{
						$this -> view -> data = Engine_Api::_() -> getItem('ynfilesharing_document', $file -> getIdentity()) -> toArray();
					}
	
				}
				catch ( exception $ex )
				{
					$is_success = 0;
				}
				$this -> view -> status = $status;
				$this -> view -> is_success = $is_success;
			}
			else 
			{
				$this -> view -> status = 'DONE';
				$this -> view -> is_success = true;
				$path = str_replace(DIRECTORY_SEPARATOR, '/', $folder -> path);
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$this -> view -> file_path = $protocol . $_SERVER['SERVER_NAME'] . $this -> view -> baseUrl() . '/' . $path . $file -> name; 
			}
		}
		// will create landing page later
		if (is_null($oFile))
			$this -> _helper -> content -> setEnabled();
	}

	public function editAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynfilesharing_Form_EditFile();
		if ($this -> getRequest() -> isPost() 
		&& $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			
			$title = $values['name'];
			$bad = array("<", ">", ":", '"', "/", "\\", "|", "?", "*");
			$result = str_replace($bad, "", $title);
		    if($result != $title)
			{
	       		$form->getElement('name')->addError(Zend_Registry::get('Zend_Translate')->_('A file name can\'t contain any of the following character: /\:*?"<>|'));
		        return false;
		    }
			
			$nameSystemWin = array("CON", "PRN", "AUX", "NUL", "COM1", "COM2", "COM3", "COM4", "COM5", "COM6", "COM7", "COM8", "COM9", "LPT1", "LPT2", "LPT3", "LPT4", "LPT5", "LPT6", "LPT7", "LPT8", "LPT9");
			if(in_array(strtoupper($title), $nameSystemWin))
			{
				$form->getElement('name')->addError(Zend_Registry::get('Zend_Translate')->_('A file name can\'t have any of the following names: CON, PRN, AUX, NUL, COM1, COM2, COM3, COM4, COM5, COM6, COM7, COM8, COM9, LPT1, LPT2, LPT3, LPT4, LPT5, LPT6, LPT7, LPT8, and LPT9'));
		        return false;
			}
			$file = Engine_Api::_() -> getItem('ynfilesharing_file', $values['file_id']);
			if ($file)
			{
				$db = Engine_Db_Table::getDefaultAdapter();
				$db -> beginTransaction();
				try
				{
					$folder = $file -> getParentFolder();
					if (!empty($file -> ext))
					{
						rename($folder -> path . $file -> name, $folder -> path . $values['name'] . '.' . $file -> ext);
						$file -> name = $values['name'] . '.' . $file -> ext;
					}
					else
					{
						rename($folder -> path . $file -> name, $folder -> path . $values['name']);
						$file -> name = $values['name'];
					}

					$file -> save();
					$db -> commit();

					$this -> _forward('success', 'utility', 'core', array(
						'smoothboxClose' => true,
						'parentRefresh' => true,
						'format' => 'smoothbox',
						'messages' => array('Your changes have been saved.')
					));
				}
				catch ( Exception $e )
				{
					$db -> rollback();
					throw $e;
				}
			}
		}
	}

	function deleteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		// In smoothbox
		$fileId = $this -> _getParam('file_id', 0);
		$this -> view -> file_id = $fileId;

		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$file = Engine_Api::_() -> getItem('ynfilesharing_file', $fileId);
				$parentFolder = $file -> getParentFolder();
				if ($parentFolder && $parentFolder -> isAllowed($this -> _viewer, 'view'))
				{
					$file -> delete();
					$document = Engine_Api::_() -> getItem('ynfilesharing_document', $fileId);
					if (is_object($document))
					{
						$document -> delete();
					}
				}

				$db -> commit();
			}
			catch ( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 8,
				'parentRefresh' => 8,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> translate('Deleted file successfully.'))
			));
		}
	}

	function downloadAction()
	{
		$fileId = $this -> _getParam('file_id', 0);
		if ($fileId)
		{
			// Increase download count
			$file = Engine_Api::_() -> getItem('ynfilesharing_file', $fileId);
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$file -> download_count += 1;
			$file -> save();

			// Download Feature
			$file = Engine_Api::_() -> getItem('ynfilesharing_file', $fileId);
			$folder = $file -> getParentFolder();
			$filePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . $folder -> path . $file -> name;
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $file -> name . '"');
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: max-age=0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			
			while (ob_get_level() > 0) 
			{
				ob_end_clean();
			}
			ob_flush();
			readfile($filePath);
			exit();
		}
	}

}
