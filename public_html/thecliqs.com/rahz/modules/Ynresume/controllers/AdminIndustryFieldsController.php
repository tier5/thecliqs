<?php class Ynresume_AdminIndustryFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'ynresume_resume';

  protected $_requireProfileType = true;
  
  public function setPhoto($photo)
  {
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Ynresume_Model_Exception('invalid argument passed to setPhoto');
		}
	
		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynresume_industry',
			'parent_id' => 1
		);
	
		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if(function_exists('exif_read_data'))
		{
			$exif = exif_read_data($file);
			if (!empty($exif['Orientation']))
			{
				switch($exif['Orientation'])
				{
					case 8 :
						$angle = 90;
						break;
					case 3 :
						$angle = 180;
						break;
					case 6 :
						$angle = -90;
						break;
				}
			}
		}	
		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();
	
		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();
	
		// Resize image (normal)
		$image = Engine_Image::factory();
		@$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(140, 105) -> write($path . '/in_' . $name) -> destroy();
	
		// Resize image (icon)
	   $image = Engine_Image::factory();
	   $image->open($file);
	   
	   $size = min($image->height, $image->width);
	   $x = ($image->width - $size) / 2;
	   $y = ($image->height - $size) / 2;
	
	   $image->resample($x, $y, $size, $size, 48, 48)
	     ->write($path.'/is_'.$name)
	     ->destroy();
	
		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage->create($path.'/is_'.$name, $params);
	
		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');
		
		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/is_' . $name);
	
		return $iMain -> file_id;
  }
  
  public function indexAction()
  {
    // Make navigation
    $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresume_admin_main', array(), 'ynresume_admin_main_fields');
	
	try{
		$option = Engine_Api::_() -> fields() -> getOption(1, 'ynresume_resume');
	}
	catch(exception $e)
	{
		//keep silent
	}
	if(empty($option))
	{
		$option_id = Engine_Api::_()->ynresume()->typeCreate('Default field');
	}
	else 
	{
		$option_id =  $option -> option_id;
	}
	
    $tableIndustry = Engine_Api::_()->getItemTable('ynresume_industry');
    parent::indexAction();
  }
  
  public function headingCreateAction()
  {
  	 $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
	
    // Create form
    $this->view->form = $form = new Fields_Form_Admin_Heading();
	
	//Name
	$form -> label -> setLabel('*Group Name');
	$form -> label -> addValidator('StringLength', false, array(1, 20));
	$form -> label -> setDescription($this -> view -> translate('Maximum 20 characters'));
	$form -> label -> getDecorator("Description")->setOption("placement", "append");
	
	 //Description
	$form->addElement('Textarea', 'description', array(
        'label' => '*Group Description',
        'description' => $this -> view ->translate('Maximum 50 characters'),
        'allowEmpty' => false,
      	'required' => true,
      	'order' => '1',
        'filters' => array(
            'StripTags',
        ),
        'validators' => array(
	        array('NotEmpty', true),
	        array('StringLength', false, array(1, 50)),
		),
    ));
	$form -> description -> getDecorator("Description")->setOption("placement", "append");
	
	 // Logo
	 $form->addElement('File', 'photo', array(
      	'label' => '*Group Image',
      	'description' => $this -> view ->translate('Recommended dimensions'),
      	'allowEmpty' => false,
      	'required' => true,
      	'order' => '2',
     ));
	 $form -> photo -> getDecorator("Description")->setOption("placement", "append");
     $form->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	 
	 //Color
	 $form->addElement('Heading', 'color', array(
        'label' => '*Background Color',
        'value' => '<input type="color" id="color" name="color"/>',
        'allowEmpty' => false,
		'required' => true,
		'order' => '3',
    ));
	 
	 // Submit
     $form -> submit -> setLabel('Add Group');
	 
	 if($form){
	 	$form -> removeElement('show');
		 $display = $form->getElement('display');
	     $display->setLabel('Show on resume page?');
	     $display->setOptions(array('multiOptions' => array(
	          1 => 'Show on resume page',
	          0 => 'Hide on resume page'
	        )));
	 }
	
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $values = $form -> getValues();
      $form -> color -> setValue('<input value="'. $values['color'] .'" type="color" id="color" name="color"/>');
      return;
    }
	
	$values = $form -> getValues();
	
	// Set photo
	$photo_id = null;
	if (!empty($values['photo'])) {
		$photo_id = $this -> setPhoto($form -> photo);
	}
	
    // Process
    $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
      'option_id' => $option->option_id,
      'photo_id' => $photo_id,
      'description' => $values['description'],
      'type' => 'heading',
      'display' => $values['display'],
      'color' => $values['color'],
    ), $form->getValues()));

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->option = $option->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
	
    $this->view->htmlArr = $html;
	
  }
  
  public function headingEditAction()
  {
  	$field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    // Create form
    $this->view->form = $form = new Fields_Form_Admin_Heading();
    $form->submit->setLabel('Edit Heading');

	
	//Name
	$form -> label -> setLabel('*Group Name');
	$form -> label -> addValidator('StringLength', false, array(1, 20));
	$form -> label -> setDescription($this -> view -> translate('Maximum 20 characters'));
	$form -> label -> getDecorator("Description")->setOption("placement", "append");
	
	 //Description
	$form->addElement('Textarea', 'description', array(
        'label' => '*Group Description',
        'description' => $this -> view ->translate('Maximum 50 characters'),
        'allowEmpty' => false,
      	'required' => true,
      	'order' => '1',
        'filters' => array(
            'StripTags',
        ),
        'validators' => array(
	        array('NotEmpty', true),
	        array('StringLength', false, array(1, 50)),
		),
    ));
	$form -> description -> getDecorator("Description")->setOption("placement", "append");
	
	 // Logo
	 $form->addElement('File', 'photo', array(
      	'label' => '*Group Image',
      	'description' => $this -> view ->translate('Recommended dimensions'),
      	'order' => '2',
     ));
	 $form -> photo -> getDecorator("Description")->setOption("placement", "append");
     $form->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	 
	 
	  //Color
	 $form->addElement('Heading', 'color', array(
        'label' => '*Background Color',
        'value' => '<input type="color" id="color" name="color"/>',
        'allowEmpty' => false,
		'required' => true,
		'order' => '3',
    ));	
	 
	 // Submit
     $form -> submit -> setLabel('Edit Group');
	 
	 if($form){
	 	$form -> removeElement('show');
		 $display = $form->getElement('display');
	     $display->setLabel('Show on resume page?');
	     $display->setOptions(array('multiOptions' => array(
	          1 => 'Show on resume page',
	          0 => 'Hide on resume page'
	        )));
	 }
	
    // Get sync notice
    $linkCount = count(Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)
        ->getRowsMatching('child_id', $field->field_id));
    if( $linkCount >= 2 ) {
      $form->addNotice($this->view->translate(array(
        'This question is synced. Changes you make here will be applied in %1$s other place.',
        'This question is synced. Changes you make here will be applied in %1$s other places.',
        $linkCount - 1), $this->view->locale()->toNumber($linkCount - 1)));
    }

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $form->populate($field->toArray());
	  $form -> color -> setValue('<input value="'. $field -> color .'" type="color" id="color" name="color"/>');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $values = $form -> getValues();
      $form -> color -> setValue('<input value="'. $values['color'] .'" type="color" id="color" name="color"/>');
      return;
    }
	
	$values = $form -> getValues();
    // Set photo
	$photo_id = $field -> photo_id;
	if (!empty($values['photo'])) {
		$photo_id = $this -> setPhoto($form -> photo);
	}
    // Process
    Engine_Api::_()->fields()->editField($this->_fieldType, $field, array_merge(array(
      'option_id' => $option->option_id,
      'photo_id' => $photo_id,
      'description' => $values['description'],
      'type' => 'heading',
      'display' => $values['display'],
    ), $form->getValues()));
	
    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
	 
  }	
  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form -> removeElement('show');
	  $search = $form->getElement('search');
	  $search->setLabel('Show on search listings?');
      $search->setOptions(array('multiOptions' => array(
          1 => 'Show on search listings',
          0 => 'Hide on search listings'
       )));
      $display = $form->getElement('display');
      $display->setLabel('Show on resume page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on resume page',
          0 => 'Hide on resume page'
        )));
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form -> removeElement('show');	
	  $search = $form->getElement('search');
	  $search->setLabel('Show on search listings?');
      $search->setOptions(array('multiOptions' => array(
          1 => 'Show on search listings',
          0 => 'Hide on search listings'
       )));
      $display = $form->getElement('display');
      $display->setLabel('Show on resume page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on resume page',
          0 => 'Hide on resume page'
        )));
    }
  }
}
?>
