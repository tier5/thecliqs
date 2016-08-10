<?php
class Ynbusinesspages_Form_Admin_Category extends Engine_Form
{
  protected $_field;
  
  protected $_category;
	
  public function getCategory()
 {
     return $this -> _category;
 }
 public function setCategory($category)
 {
     $this -> _category = $category;
 } 

  public function init()
  {
    $this->setMethod('post');
  
   $this->addElement('Hidden','id');
   
     //Location Name - Required
   $this->addElement('Text','label',array(
      'label'     => 'Category Name',
      'required'  => true,
      'allowEmpty'=> false,
    ));
	
	$this -> addElement('File', 'photo', array('label' => 'Icon'));
	$this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
   $this->addElement('Textarea','description',array(
      'label'     => 'Description',
      'required'  => true,
      'allowEmpty'=> false,
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Category',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function setField($category)
  {
    $this->_field = $category;

    // Set up elements
    //$this->removeElement('type');
    $this->label->setValue($category->getTitle());
    $this->id->setValue($category->category_id);
	if($category -> level == 1)
	{
		$this->description->setValue($category->description);
	}
    $this->submit->setLabel('Edit Category');

    // @todo add the rest of the parameters
  }
}