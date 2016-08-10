<?php
class Ynbusinesspages_Form_Wiki_Search extends Engine_Form
{
  public function init()
  {
   //Form Attribute and Method
    $this->setAttribs(array('id' => 'filter_form',
                            'class' => 'global_form f1',))
         ->setMethod('GET')
         ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)));


    $this->addElement('Text', 'name', array(
      'label' => 'Page Name',
      'onchange' => 'this.form.submit();',
    ));
    $this->addElement('Text', 'owner', array(
      'label' => 'Page Creator',
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
        'follow_count' => 'Most Followed',
        'favourite_count' => 'Most Favourite',
        'like_count' => 'Most Liked',
        'comment_count' => 'Most Commented',
        'rate_ave' => 'Most Rated',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}