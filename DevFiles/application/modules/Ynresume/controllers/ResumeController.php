<?php
class Ynresume_ResumeController extends Core_Controller_Action_Standard
{
	public function init() {
		
		if (0 !== ($resume_id = (int)$this -> _getParam('resume_id')) && null !== ($resume = Engine_Api::_() -> getItem('ynresume_resume', $resume_id)))
		{
			Engine_Api::_() -> core() -> setSubject($resume);
		}
		$this -> _helper -> requireSubject('ynresume_resume');
	}
	
	public function viewAction() {
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
        
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$resume->isViewable()) {
            return $this->_helper->requireAuth()->forward();
        }
        
		//check login before trackview
		if($viewer -> getIdentity()  && !$viewer -> isSelf($resume -> getOwner()))
		{
			$resume -> view_count += 1;
			$resume -> save();
			$tableView = Engine_Api::_() -> getDbTable('views', 'ynresume') -> trackingView($viewer -> getIdentity(), $resume -> getIdentity());
		}
	}
	
	public function editAction()
	{
	}
	
	public function deleteAction() {
	    $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$resume->isDeletable()) {
            return $this->_helper->requireAuth()->forward();
        }
	}
	
	public function photoAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		
	    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$params = $this ->_getAllParams();
		
	    // Get form
	    $this->view->form = $form = new Ynresume_Form_Resume_Photo();
		$this -> view -> isPost = false;
	    if( empty($resume->photo_id) ) {
	      $form->removeElement('remove');
	    }
	
	    if( !$this->getRequest()->isPost() ) {
	      return;
	    }
		
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	      return;
	    }
		$this -> view -> isPost = true;
	    // Uploading a new photo
	    if( $form->Filedata->getValue() !== null ) {
	      $db = $resume->getTable()->getAdapter();
	      $db->beginTransaction();
	      
	      try {
	        $fileElement = $form->Filedata;
	        $resume->setPhoto($fileElement);
	        $iMain = Engine_Api::_()->getItem('storage_file', $resume->photo_id);
	        $db->commit();
	      }
	
	      // If an exception occurred within the image adapter, it's probably an invalid image
	      catch( Engine_Image_Adapter_Exception $e )
	      {
	        $db->rollBack();
	        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
	      }
	
	      // Otherwise it's probably a problem with the database or the storage system (just throw it)
	      catch( Exception $e )
	      {
	        $db->rollBack();
	        throw $e;
	      }
	    }
	
	    // Resizing a photo
	    else if( $form->getValue('coordinates') !== '' ) {
	      $storage = Engine_Api::_()->storage();
	
	      $iProfile = $storage->get($resume->photo_id, 'thumb.profile');
	      $iSquare = $storage->get($resume->photo_id, 'thumb.icon');
	
	      // Read into tmp file
	      $pName = $iProfile->getStorageService()->temporary($iProfile);
	      $iName = dirname($pName) . '/nis_' . basename($pName);
	
	      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
	
	      $image = Engine_Image::factory();
	      $image->open($pName)
	        ->resample($x+.1, $y+.1, $w-.1, $h-.1, 48, 48)
	        ->write($iName)
	        ->destroy();
	
	      $iSquare->store($iName);
	
	      // Remove temp files
	      @unlink($iName);
	    }
		
	  }

	  public function removePhotoAction() {
		
	    $resume = Engine_Api::_()->core()->getSubject();
	    $resume->photo_id = 0;
	    $resume->save();
		
	  }
      
    public function editPrivacyAction() {
        $this->_helper->content->setEnabled();
        $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$resume->isEditable()) {
            return $this->_helper->requireAuth()->forward();
        }
        
        $this->view->form = $form = new Ynresume_Form_EditPrivacy();
        
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
        $sections = Engine_Api::_()->ynresume()->getAllSections();
        $auth_arr = array_keys($sections);
        if (isset($auth_arr['photo'])) unset($auth_arr['photo']);
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($resume, $role, $elem)) {
                    if ($form->$elem)
                        $form->$elem->setValue($role);
                }
            }    
        }
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $posts = $this -> getRequest() -> getPost();
        if (!$form -> isValid($posts)) {
            return;
        }
        $values = $form->getValues();
        
        foreach ($auth_arr as $elem) {
            $auth_role = $values[$elem];
            if ($auth_role) {
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i=>$role) {
                   $auth->setAllowed($resume, $role, $elem, ($i <= $roleMax));
                }
            }    
        }
        
        $form->addNotice($this->view->translate('Your changes have been saved.'));
    }

    public function selectThemeAction() {
        $this -> _helper -> layout -> setLayout('default-simple');
        $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$resume->isEditable()) {
            return $this->_helper->requireAuth()->forward();
        }
        
        $this->view->resume = $resume;
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        
        $values = $this -> getRequest() -> getPost();
        $theme = $values['theme'];
        $resume->theme = $theme;
        $resume->save();
        
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRefresh' => false,
            'smoothboxClose' => true,
            'messages' => Zend_Registry::get('Zend_Translate') -> _('Change theme successful.')
        ));
    }	  
	
	public function featureAction() {
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$settings = Engine_Api::_()->getApi('settings', 'core');
		$fee_feature = $settings->getSetting('ynresume_fee_feature', 0);
		
        $resume = Engine_Api::_()->getItem('ynresume_resume', $this->_getParam('resume_id'));
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
		
	  	// Get form
		$this -> view -> form = $form = new Ynresume_Form_Resume_Feature(array(
			'fee' => $fee_feature,
		));
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
		
		if ($resume->featured && $resume->feature_expiration_date) {
			$form->setDescription($this->view->translate('Your resume is featuring and valid until <span style="color:red;">%s</span>. It costs %s to feature resume in 1 day.', 
			$this->view ->locale() -> toDate($resume->getFeatureExpirationDate()), 
			$this->view -> locale()->toCurrency($fee_feature, $currency)));
			$form->getDecorator('Description')->setOption('escape', false);
		}
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
			'action' => 'place-order',
			'id' => $resume -> getIdentity(),
			'feature_day_number' => $this->_getParam('day'),
		), 'ynresume_general', true);
							
		$this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRedirect' => $redirect_url,
            'format' => 'smoothbox',
            'messages' => array($this->view->translate("Please wait..."))
        ));
    }
	
	public function serviceAction() {
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$settings = Engine_Api::_()->getApi('settings', 'core');
		$fee_service = $settings->getSetting('ynresume_fee_service', 0);
		
        $resume = Engine_Api::_()->getItem('ynresume_resume', $this->_getParam('resume_id'));
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
		
	  	// Get form
		$this -> view -> form = $form = new Ynresume_Form_Resume_Service(array(
			'fee' => $fee_service,
		));
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
			'action' => 'place-order',
			'id' => $resume -> getIdentity(),
			'service_day_number' => $this->_getParam('day'),
		), 'ynresume_general', true);
							
		$this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRedirect' => $redirect_url,
            'format' => 'smoothbox',
            'messages' => array($this->view->translate("Please wait..."))
        ));
    }

    public function sortAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$resume->isEditable()) {
            return $this->_helper->requireAuth()->forward();
        }
        $resumeorder = $resume->getOrder();
        if (!$resumeorder) {
            $table = Engine_Api::_()->getDbTable('resumeorder', 'ynresume');
            $resumeorder = $table->createRow();
            $resumeorder->resume_id = $resume->getIdentity();
        } 
        $order = explode(',', $this->getRequest()->getParam('order'));
        $order_arr = array();
        foreach($order as $i => $item) {
            $section_key = substr($item, strpos($item, '_') + 1);
            if ($section_key !== false) array_push($order_arr, $section_key);
        }
        $resumeorder->order = $order_arr;
        $resumeorder->save();
    }

    public function renderSectionAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $resume = Engine_Api::_()->core()->getSubject();
        
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        $reload = $this->_getParam('reload');
		$type = $this->_getParam('type');
        $params = $this->_getParam('params');
		if (isset($params['reload'])) {
			$reload = $params['reload'];
		}
		
        if (!$reload) {
	        if (!$resume->isEditable()) {
	            return $this->_helper->requireAuth()->forward();
	        }	
        }
		else {
			if (!$resume->isViewable()) {
	            return $this->_helper->requireAuth()->forward();
	        }
		}
		
        echo Engine_Api::_()->ynresume()->renderSection($type, $resume, $params);
    }
    
    public function uploadPhotosAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }
        if (!$resume->isEditable()) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('You don\'t have permission to do this.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }
        
        if (!$this -> getRequest() -> isPost()) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }

        if (empty($_FILES['files'])) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('No file');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
        }
        $name = $_FILES['files']['name'][0];
        $type = explode('/', $_FILES['files']['type'][0]);
        if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload File');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        
        if($_FILES['files']['size'][0] > 1000*1024) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Exceeded filesize limit.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        $temp_file = array(
            'type' => $_FILES['files']['type'][0],
            'tmp_name' => $_FILES['files']['tmp_name'][0],
            'name' => $_FILES['files']['name'][0]
        );
        $photo_id = Engine_Api::_() -> ynresume() -> setPhoto($temp_file, array(
            'parent_type' => 'ynresume_resume',
            'parent_id' => $resume->getIdentity(),
        ));
        
        $table = Engine_Api::_()->getItemTable('ynresume_photo');
        $photo = $table->createRow();
        $photo->parent_type = 'ynresume_resume';
        $photo->parent_id = $resume->getIdentity();
        $photo->file_id = $photo_id;
        $photo->title = $_FILES['files']['name'][0];
        $photo->creation_date = $photo->modified_date = date('Y-m-d H:i:s');
        $photo->save();

        $status = true;
        $name = $_FILES['files']['name'][0];
        
        return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $name, 'photo_id' => $photo->getIdentity())))));
    }

    public function deletePhotoAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume || !$resume->isEditable()) {
            return false;
        }
        
        if (!$this -> getRequest() -> isPost()) {
            return false;
        }

        $photo_id = $this->_getParam('photo_id', 0);
        if (!$photo_id) {
            return false;
        }
        return Engine_Api::_()->ynresume()->deletePhoto($photo_id);
    }
    
	public function exportPdfAction()
    {
    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return $this->_helper->requireAuth()->forward();
		}
		$this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$resume->isViewable()) {
            return $this->_helper->requireAuth()->forward();
        }
        $resumeOwner = $resume -> getOwner();
	    $filename = str_replace(' ', '_', $resumeOwner->getTitle());
        $content = $resume -> renderText();
	    $content = <<<EOF
	    <html>
	    	<head>
	    		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	    		<meta http-equiv="Content-Language" content="en_US">
	    	</head>
		    <body style="font-family: freeserif">
		    	{$content}
		    </body>
	    </html>
EOF;
        require_once APPLICATION_PATH . '/application/modules/Ynresume/Libs/html2pdf/html2pdf.class.php';
	    $html2pdf = new YNRESUME_HTML2PDF('P','A4','en');
	    $html2pdf->pdf->SetDisplayMode('real');
    	$html2pdf->WriteHTML($content);
    	$html2pdf->Output("{$filename}.pdf");
    }
    
	public function exportWordAction()
    {
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        ini_set('error_reporting', -1);

    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return $this->_helper->requireAuth()->forward();
		}
		$this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
        if (!$resume) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$resume->isViewable()) {
            return $this->_helper->requireAuth()->forward();
        }	
	    $resumeOwner = $resume -> getOwner();
	    $filename = str_replace(' ', '_', $resumeOwner->getTitle());
	    
	    // Load the files we need:
		require_once APPLICATION_PATH . '/application/modules/Ynresume/Libs/htmltodocx/phpword/PHPWord.php';
		require_once APPLICATION_PATH . '/application/modules/Ynresume/Libs/htmltodocx/simplehtmldom/simple_html_dom.php';
		require_once APPLICATION_PATH . '/application/modules/Ynresume/Libs/htmltodocx/htmltodocx_converter/h2d_htmlconverter.php';
		require_once APPLICATION_PATH . '/application/modules/Ynresume/Libs/htmltodocx/example_files/styles.inc';
		
		// Functions to support this example.
		require_once APPLICATION_PATH . '/application/modules/Ynresume/Libs/htmltodocx/documentation/support_functions.inc';
		
		// HTML fragment we want to parse:
		//$html = file_get_contents('example_files/example_html.html');
		$html = $resume -> renderText(true);
		// $html = file_get_contents('test/table.html');
		 
		// New Word Document:
		$phpword_object = new PHPWord();
		$section = $phpword_object->createSection();

        // Add table
        $table = $section->addTable(array('borderColor' => '000000', 'borderSize' => 10));
        $table->addRow();
        $image = Engine_Api::_()->storage()->get($resume->photo_id);
        $imagePath = $image -> temporary();
        $table->addCell(3500, array('borderColor' => '000000', 'borderSize' => 10))->addImage($imagePath, array('width'=>230, 'height'=>300, 'align'=>'left'));
        $cell = $table -> addCell(6500,array('borderColor' => '000000', 'borderSize' => 10));
        $profileData = $resume -> renderProfileInfoForWord();
        foreach ($profileData as $key => $data)
        {
            if (trim($data) != '')
            {
                if ($key == 0)
                    $cell ->addText($data, array('bold'=>true, 'size'=>16));
                else
                    $cell ->addText($data, array());
            }
        }
        $section -> addTextBreak(1);

		// HTML Dom object:
		$html_dom = new simple_html_dom();
		$html_dom->load('<html><body>' . $html . '</body></html>');
        //echo $html; exit;
		// Note, we needed to nest the html in a couple of dummy elements.
		
		// Create the dom array of elements which we are going to work on:
		$html_dom_array = $html_dom->find('html',0)->children();
		
		// We need this for setting base_root and base_path in the initial_state array
		// (below). We are using a function here (derived from Drupal) to create these
		// paths automatically - you may want to do something different in your
		// implementation. This function is in the included file 
		// documentation/support_functions.inc.
		$paths = htmltodocx_paths();
		
		// Provide some initial settings:
		$initial_state = array(
		  // Required parameters:
		  'phpword_object' => &$phpword_object, // Must be passed by reference.
		  // 'base_root' => 'http://test.local', // Required for link elements - change it to your domain.
		  // 'base_path' => '/htmltodocx/documentation/', // Path from base_root to whatever url your links are relative to.
		  'base_root' => $paths['base_root'],
		  'base_path' => $paths['base_path'],
		  // Optional parameters - showing the defaults if you don't set anything:
		  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
		  'parents' => array(0 => 'body'), // Our parent is body.
		  'list_depth' => 0, // This is the current depth of any current list.
		  'context' => 'section', // Possible values - section, footer or header.
		  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
		  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
		  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
		  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
		  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
		  'treat_div_as_paragraph' => TRUE, // If set to TRUE, each new div will trigger a new line in the Word document.
		      
		  // Optional - no default:    
		  'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by htmltodocx_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
		  );    
		
		// Convert the HTML and put it into the PHPWord object
		htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
		
		// Clear the HTML dom object:
		$html_dom->clear(); 
		unset($html_dom);
		
		// Save File
		$h2d_file_uri = tempnam('', 'htd');
		$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
		$objWriter->save($h2d_file_uri);
		
		// Download the file:
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$filename.'.docx');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($h2d_file_uri));
		ob_clean();
		flush();
		$status = readfile($h2d_file_uri);
		unlink($h2d_file_uri);
        @unlink($imagePath);
		exit;
    }
}
