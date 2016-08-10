<?php

class Ynultimatevideo_Form_Admin_Category extends Engine_Form
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
    }

    public function setField($category, $isSub = true)
    {
        $this->_field = $category;

        // Set up elements
        $this->label->setValue($category->title);
        $this->id->setValue($category->category_id);
        $this->submit->setLabel('Edit Category');

        // @todo add the rest of the parameters
    }
}