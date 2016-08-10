<?php

class Socialgames_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('GET')
      ;
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Games',
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
		'most_recent' => 'Most Recent',
        'most_played' => 'Most Played',
        'view_count' => 'Most Viewed',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => array(
        '0' => 'All Categories',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));
  }
}