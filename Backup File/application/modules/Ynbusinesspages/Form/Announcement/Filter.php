<?php
class Ynbusinesspages_Form_Announcement_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
		
      ));

    $this->addElement('Hidden', 'orderby', array(
      'order' => 1
    ));

    $this->addElement('Hidden', 'orderby_direction', array(
      'order' => 2
    ));
  }
}